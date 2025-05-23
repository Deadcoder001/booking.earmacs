<?php
require './config/db.php';

$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
$razorpay_payment_id = isset($_GET['razorpay_payment_id']) ? $_GET['razorpay_payment_id'] : '';

if (!$booking_id || !$razorpay_payment_id) {
    die("Invalid payment or booking reference.");
}

// Update booking status and store payment reference
$stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed', payment_reference = ? WHERE id = ?");
$stmt->execute([$razorpay_payment_id, $booking_id]);

// Fetch booking details
$stmt = $pdo->prepare("SELECT b.id, b.check_in_date, b.check_out_date, b.total_price, p.name AS property_name 
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    JOIN properties p ON r.property_id = p.id
    WHERE b.id = ?");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="container mt-5">
    <div class="alert alert-success">
        <h4>Payment Successful!</h4>
        <p>Your booking (ID: <strong><?= htmlspecialchars($booking_id) ?></strong>) has been confirmed.</p>
        <?php if ($booking): ?>
            <ul>
                <li><strong>Property:</strong> <?= htmlspecialchars($booking['property_name']) ?></li>
                <li><strong>Check-in:</strong> <?= htmlspecialchars($booking['check_in_date']) ?></li>
                <li><strong>Check-out:</strong> <?= htmlspecialchars($booking['check_out_date']) ?></li>
                <li><strong>Total Price:</strong> ₹<?= htmlspecialchars($booking['total_price']) ?></li>
            </ul>
        <?php endif; ?>
        <p>Payment Reference: <strong><?= htmlspecialchars($razorpay_payment_id) ?></strong></p>
    </div>
    <a href="index.php" class="btn btn-primary">Back to Home</a>
</div>
</body>
</html>