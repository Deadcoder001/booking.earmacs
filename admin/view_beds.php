<?php
require '../config/db.php';

// Fetch all rooms with their beds
$stmt = $pdo->query("SELECT * FROM hostel_rooms ORDER BY room_number ASC");
$rooms = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Hostel Beds</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .bed {
            width: 50px;
            height: 50px;
            margin: 5px;
            border-radius: 8px;
            line-height: 50px;
            text-align: center;
            font-weight: bold;
            cursor: pointer;
            user-select: none;
        }
        .bed.free {
            background-color: #28a745;
            color: white;
        }
        .bed.booked {
            background-color: #dc3545;
            color: white;
            cursor: not-allowed;
        }
        .room-container {
            margin-bottom: 40px;
            background: #2c2f33;
            padding: 15px;
            border-radius: 12px;
        }
        .room-title {
            color: #ffc107;
            margin-bottom: 15px;
            font-size: 1.5rem;
            font-weight: 700;
        }
        .beds-row {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }
    </style>
</head>
<body class="bg-dark text-light">
<div class="container mt-5">
    <h2 class="text-center mb-5">Hostel Rooms & Beds</h2>

    <?php foreach ($rooms as $room): ?>
        <div class="room-container">
            <div class="room-title">Room: <?= htmlspecialchars($room['room_number']) ?> (Price per bed: ₹<?= number_format($room['price_per_bed'], 2) ?>)</div>

            <div class="beds-row">
                <?php
                // Fetch beds of this room with booking info
                $beds_stmt = $pdo->prepare("
                    SELECT hb.id, hb.bed_number, hb.price,
                        CASE WHEN b.id IS NULL THEN 0 ELSE 1 END AS booked
                    FROM hostel_beds hb
                    LEFT JOIN booking b ON b.room_id = hb.room_id AND b.status = 'booked' AND
                        (CURDATE() BETWEEN b.check_in_date AND b.check_out_date) AND hb.bed_number = b.guest_id
                    WHERE hb.room_id = ?
                    ORDER BY hb.bed_number ASC
                ");
                $beds_stmt->execute([$room['id']]);
                $beds = $beds_stmt->fetchAll();

                // If booking table structure differs, you might adjust the join and condition accordingly.
                // Here, I assumed guest_id = bed_number for demo purposes, please adapt as needed.
                ?>

                <?php foreach ($beds as $bed): ?>
                    <div
                        class="bed <?= $bed['booked'] ? 'booked' : 'free' ?>"
                        title="Bed <?= $bed['bed_number'] ?> - <?= $bed['booked'] ? 'Booked' : 'Available' ?>"
                    >
                        <?= $bed['bed_number'] ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

</div>
</body>
</html>
