<?php
require '../config/db.php';

header('Content-Type: application/json');

try {
    // Fetch all properties
    $propertyStmt = $pdo->query("SELECT * FROM properties");
    $properties = [];

    while ($property = $propertyStmt->fetch()) {
        $propertyId = $property['id'];

        // Fetch rooms for the property
        $roomStmt = $pdo->prepare("SELECT id, name, base_price FROM rooms WHERE property_id = ?");
        $roomStmt->execute([$propertyId]);
        $rooms = $roomStmt->fetchAll();

        // Calculate property revenue from bookings
        $bookingStmt = $pdo->prepare("
            SELECT b.*, g.name as guest_name, g.phone
            FROM bookings b
            LEFT JOIN guests g ON b.guest_id = g.id
            WHERE b.room_id IN (SELECT id FROM rooms WHERE property_id = ?)
        ");
        $bookingStmt->execute([$propertyId]);
        $bookings = $bookingStmt->fetchAll();

        $totalRevenue = 0;
        $formattedBookings = [];

        foreach ($bookings as $booking) {
            $totalRevenue += $booking['total_price'];
            $formattedBookings[] = [
                'name' => $booking['guest_name'] ?? 'Unknown',
                'phone' => $booking['phone'] ?? 'N/A',
                'payment' => (float)$booking['total_price'],
                'paymentMethod' => 'N/A', // Add payment method field if your schema supports it
                'roomIds' => [$booking['room_id']],
                'bookedDates' => getDatesFromRange($booking['check_in_date'], $booking['check_out_date'])
            ];
        }

        // Format room revenue (mocked with base_price * bookings count for now)
        $roomRevenue = [];
        foreach ($rooms as $room) {
            $roomId = $room['id'];
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE room_id = ?");
            $countStmt->execute([$roomId]);
            $count = $countStmt->fetchColumn();

            $roomRevenue[] = [
                'id' => $roomId,
                'name' => $room['name'],
                'revenue' => $room['base_price'] * $count
            ];
        }

        $properties[] = [
            'id' => $propertyId,
            'name' => $property['name'],
            'rooms' => $roomRevenue,
            'bookings' => $formattedBookings
        ];
    }

    echo json_encode([
        'success' => true,
        'properties' => $properties
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

// Helper function to generate date range
function getDatesFromRange($start, $end, $format = 'Y-m-d')
{
    $array = [];
    $interval = new DateInterval('P1D');
    $realEnd = new DateTime($end);
    $realEnd->modify('-1 day');

    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

    foreach ($period as $date) {
        $array[] = $date->format($format);
    }

    return $array;
}
