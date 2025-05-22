<?php
// Database connection settings
$host = 'localhost'; // Your host (usually localhost)
$dbname = 'earmacs_BP'; // Your database name
$username = 'earmacs'; // Your database username
$password = 'Earmac@21#'; // Your database password


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}

$property_id = $_GET['property_id'] ?? null;

if ($property_id) {
    $stmt = $pdo->prepare("SELECT checkin_date, checkout_date FROM bookings WHERE property_id = :property_id");
    $stmt->execute(['property_id' => $property_id]);
    $bookedDates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $dates = [];
    foreach ($bookedDates as $date) {
        $startDate = new DateTime($date['checkin_date']);
        $endDate = new DateTime($date['checkout_date']);
        while ($startDate <= $endDate) {
            $dates[] = $startDate->format('Y-m-d');
            $startDate->modify('+1 day');
        }
    }

    echo json_encode($dates);
} else {
    echo json_encode([]);
}
?>
