<?php
require '../config/db.php';

$property_id = $_GET['property_id'] ?? null;
$room_id = $_GET['room_id'] ?? null;

$sql = "
    SELECT 
        b.id,
        b.check_in_date, 
        b.check_out_date, 
        b.room_id, 
        r.name AS room_name,
        COALESCE(g.name, 'Guest') AS guest_name,
        b.total_price,
        b.discounted_price,
        u.name AS booked_by,
        u.role AS user_role
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    LEFT JOIN guests g ON b.guest_id = g.id
    LEFT JOIN users u ON b.user_id = u.id
    WHERE 1
";

$params = [];

if (!empty($property_id)) {
    $sql .= " AND r.property_id = ?";
    $params[] = $property_id;
}

if (!empty($room_id)) {
    $sql .= " AND b.room_id = ?";
    $params[] = $room_id;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$events = [];

foreach ($bookings as $b) {
    $isAgent = ($b['user_role'] === 'agent');
    $color = $isAgent ? '#4CAF50' : '#2196F3'; // Green for agent, blue for direct

    $events[] = [
        'title' => $b['room_name'] . ' - ' . $b['guest_name'],
        'start' => $b['check_in_date'],
        'end'   => $b['check_out_date'], // FullCalendar handles it as exclusive
        'color' => $color,
        'extendedProps' => [
            'guest_name'       => $b['guest_name'],
            'room_name'        => $b['room_name'],
            'check_in_date'    => $b['check_in_date'],
            'check_out_date'   => $b['check_out_date'],
            'total_price'      => $b['total_price'],
            'discounted_price' => $b['discounted_price'],
            'booked_by_agent'  => $isAgent,
            'agent_name'       => $isAgent ? $b['booked_by'] : null,
        ]
    ];
}

header('Content-Type: application/json');
echo json_encode($events);
