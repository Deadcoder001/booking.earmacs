<?php
require 'db.php'; // DB connection

// 1. Get POST data safely
$name = $_POST['name'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? null;
$checkin = $_POST['check_in_date'] ?? '';
$checkout = $_POST['check_out_date'] ?? '';
$property_id = $_POST['property_id'] ?? '';
$base_price = $_POST['base_price'] ?? 0;

// Basic validation
if (empty($name) || empty($phone) || empty($checkin) || empty($checkout) || empty($property_id)) {
    die("Required fields are missing.");
}

// 2. Convert dates and calculate nights
$checkin_date = new DateTime($checkin);
$checkout_date = new DateTime($checkout);
$nights = $checkin_date->diff($checkout_date)->days;

if ($nights < 1) {
    die("Check-out date must be after check-in date.");
}

$total_price = $nights * $base_price;

// 3. Find an available room in this property for the selected dates
$stmt = $pdo->prepare("
    SELECT r.id FROM rooms r
    LEFT JOIN bookings b ON r.id = b.room_id 
        AND NOT (
            b.check_out_date <= :checkin OR 
            b.check_in_date >= :checkout
        )
    WHERE r.property_id = :property_id 
        AND b.id IS NULL
    LIMIT 1
");

$stmt->execute([
    ':checkin' => $checkin,
    ':checkout' => $checkout,
    ':property_id' => $property_id
]);

$room = $stmt->fetch();

if (!$room) {
    die("No available room for the selected dates.");
}

$room_id = $room['id'];

// 4. Insert guest info (optional: use existing guest if phone matches)
$guestStmt = $pdo->prepare("INSERT INTO guests (name, email, phone) VALUES (?, ?, ?)");
$guestStmt->execute([$name, $email, $phone]);
$guest_id = $pdo->lastInsertId();

// 5. Insert booking
$bookingStmt = $pdo->prepare("
    INSERT INTO bookings (guest_id, room_id, check_in_date, check_out_date, total_price, status, created_at)
    VALUES (?, ?, ?, ?, ?, 'pending', NOW())
");

$bookingStmt->execute([
    $guest_id, $room_id, $checkin, $checkout, $total_price
]);

echo "✅ Booking successful! Room ID: $room_id";
