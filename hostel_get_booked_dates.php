<?php
require './config/db.php';

if (isset($_GET['bed_id'])) {
    $bed_id = intval($_GET['bed_id']);
    $stmt = $pdo->prepare("SELECT check_in_date, check_out_date FROM hostel_booking WHERE bed_id = ? AND status = 'Booked'");
    $stmt->execute([$bed_id]);

    $bookedRanges = [];

    while ($row = $stmt->fetch()) {
        $bookedRanges[] = [
            "from" => $row['check_in_date'],
            "to" => $row['check_out_date']
        ];
    }

    echo json_encode($bookedRanges);
}
