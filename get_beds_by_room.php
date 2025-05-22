<?php
require './config/db.php';

$room_id = $_GET['room_id'] ?? 0;
$check_in = $_GET['check_in'] ?? null;
$check_out = $_GET['check_out'] ?? null;

if (!$room_id || !$check_in || !$check_out) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT id, bed_number 
        FROM hostel_beds 
        WHERE room_id = ? 
          AND id NOT IN (
            SELECT bed_id FROM hostel_booking
            WHERE 
                (check_in_date < ? AND check_out_date > ?) -- booking overlaps with requested dates
                OR
                (check_in_date >= ? AND check_in_date < ?)
                OR
                (check_out_date > ? AND check_out_date <= ?)
          )";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $room_id, 
    $check_out, $check_in,
    $check_in, $check_out,
    $check_in, $check_out
]);
$beds = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($beds);
