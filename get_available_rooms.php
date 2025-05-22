<?php
require_once "./config/db.php"; // Your mysqli connection as $conn

header('Content-Type: application/json');

$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';

// Validate date format YYYY-MM-DD
$date_pattern = '/^\d{4}-\d{2}-\d{2}$/';
if (!preg_match($date_pattern, $check_in) || !preg_match($date_pattern, $check_out)) {
    echo json_encode([]);
    exit;
}

if (!$check_in || !$check_out) {
    echo json_encode([]);
    exit;
}

// Query to find hostel_rooms with at least one available bed in date range
$sql = "
    SELECT DISTINCT hr.id, hr.room_number
    FROM hostel_rooms hr
    JOIN hostel_beds hb ON hr.id = hb.room_id
    WHERE hb.id NOT IN (
        SELECT bed_id FROM hostel_booking
        WHERE NOT (
            check_out_date <= ? OR
            check_in_date >= ?
        )
    )
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode([]);
    exit;
}

$stmt->bind_param("ss", $check_in, $check_out);
$stmt->execute();
$result = $stmt->get_result();

$available_rooms = [];
while ($row = $result->fetch_assoc()) {
    $available_rooms[] = $row;
}

echo json_encode($available_rooms);
