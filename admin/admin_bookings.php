<?php
require '../config/db.php';
session_start();

// // Check if the user is an admin
// if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
//     header('Location: login.php');
//     exit();
// }

// Fetch all agent bookings
$stmt = $pdo->prepare("
    SELECT b.id, g.name AS guest_name, r.name AS room_name, b.check_in_date, b.check_out_date, b.total_price, b.discounted_price, b.status, u.name AS agent_name
    FROM bookings b
    JOIN guests g ON b.guest_id = g.id
    JOIN rooms r ON b.room_id = r.id
    JOIN users u ON b.user_id = u.id
    WHERE u.role = 'agent'
");
$stmt->execute();
$bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Agent Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Agent Bookings</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Agent Name</th>
                    <th>Guest Name</th>
                    <th>Room</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Total Price</th>
                    <th>Discounted Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['agent_name']) ?></td>
                        <td><?= htmlspecialchars($booking['guest_name']) ?></td>
                        <td><?= htmlspecialchars($booking['room_name']) ?></td>
                        <td><?= htmlspecialchars($booking['check_in_date']) ?></td>
                        <td><?= htmlspecialchars($booking['check_out_date']) ?></td>
                        <td>₹<?= htmlspecialchars($booking['total_price']) ?></td>
                        <td>₹<?= htmlspecialchars($booking['discounted_price']) ?></td>
                        <td><?= htmlspecialchars($booking['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
