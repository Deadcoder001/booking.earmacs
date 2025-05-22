<?php
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete' && isset($_POST['booking_id'])) {
        $booking_id = $_POST['booking_id'];
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->execute([$booking_id]);
        echo "<script>alert('Booking deleted successfully');</script>";
    } elseif ($_POST['action'] === 'approve' && isset($_POST['booking_id'])) {
        $booking_id = $_POST['booking_id'];
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'approved' WHERE id = ?");
        $stmt->execute([$booking_id]);
        echo "<script>alert('Booking approved successfully');</script>";
    }
}

// Fetch all bookings
$stmt = $pdo->prepare("SELECT b.id, b.name, b.phone, b.email, b.check_in_date, b.check_out_date, r.room_number, b.status
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id");
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Bookings</title>
    <script src="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.js"></script>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body class="bg-gray-100">

<div class="container mx-auto p-8">
    <h1 class="text-3xl font-semibold text-center mb-6">Manage Guest Bookings</h1>

    <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
        <thead>
            <tr>
                <th class="p-4">Booking ID</th>
                <th class="p-4">Guest Name</th>
                <th class="p-4">Phone</th>
                <th class="p-4">Email</th>
                <th class="p-4">Room Number</th>
                <th class="p-4">Check-in</th>
                <th class="p-4">Check-out</th>
                <th class="p-4">Status</th>
                <th class="p-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td class="p-4"><?= htmlspecialchars($booking['id']) ?></td>
                    <td class="p-4"><?= htmlspecialchars($booking['name']) ?></td>
                    <td class="p-4"><?= htmlspecialchars($booking['phone']) ?></td>
                    <td class="p-4"><?= htmlspecialchars($booking['email']) ?></td>
                    <td class="p-4"><?= htmlspecialchars($booking['room_number']) ?></td>
                    <td class="p-4"><?= htmlspecialchars($booking['check_in_date']) ?></td>
                    <td class="p-4"><?= htmlspecialchars($booking['check_out_date']) ?></td>
                    <td class="p-4"><?= htmlspecialchars($booking['status']) ?></td>
                    <td class="p-4">
                        <?php if ($booking['status'] !== 'approved'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['id']) ?>">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Approve</button>
                            </form>
                        <?php endif; ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['id']) ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
