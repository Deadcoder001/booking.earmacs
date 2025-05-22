<?php

include '../config/db.php';
session_start();

// Check if agent is logged in
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'agent') {
    die("Unauthorized access.");
}

$agent_id = $_SESSION['id']; // Corrected session variable
$property_id = $_POST['property_id'] ?? null;
$guest_id = $_POST['guest_id'] ?? null;
$check_in = $_POST['check_in_date'] ?? null;
$check_out = $_POST['check_out_date'] ?? null;
$base_price = $_POST['base_price'] ?? null;

// Basic validation
if (!$property_id || !$guest_id || !$check_in || !$check_out || !$base_price) {
    die("Missing required fields.");
}

// Convert and validate dates
$start = new DateTime($check_in);
$end = new DateTime($check_out);
if ($end <= $start) {
    die("Invalid check-in/check-out dates.");
}

$days = $end->diff($start)->days;
$total_price = $days * $base_price;

// Apply 10% discount
$discounted_price = $total_price * 0.90;

// Fetch available room
$stmt = $pdo->prepare("
    SELECT id FROM rooms 
    WHERE property_id = ? AND status = 'available' 
    AND id NOT IN (
        SELECT room_id FROM bookings 
        WHERE (check_in_date < ? AND check_out_date > ?)
    ) 
    LIMIT 1
");
$stmt->execute([$property_id, $check_out, $check_in]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    die("No available room found for the selected dates.");
}

$room_id = $room['id'];

// Insert booking
$stmt = $pdo->prepare("
    INSERT INTO bookings 
    (user_id, guest_id, room_id, check_in_date, check_out_date, total_price, discounted_price, status, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
");

$stmt->execute([
    $agent_id,
    $guest_id,
    $room_id,
    $check_in,
    $check_out,
    $total_price,
    $discounted_price
]);

// Optional: You might want to mark the room as 'booked' instead
// $stmt = $pdo->prepare("UPDATE rooms SET status = 'booked' WHERE id = ?");
// $stmt->execute([$room_id]);

// Set success message
echo "<div style='padding: 15px; margin: 20px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px;'>
    Booking successful! Room ID: {$room_id}, Discounted Total: ₹" . number_format($discounted_price, 2) . "
</div>";

// Optional delay before redirecting
header("refresh:3;url=agent_dashboard.php");
exit;
