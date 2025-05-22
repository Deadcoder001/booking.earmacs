<?php
session_start();
include '../config/db.php';

// Check if the admin is logged in, else redirect to login page
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Fetch property ID from URL
$property_id = isset($_GET['property_id']) ? (int)$_GET['property_id'] : 0;

// Fetch rooms for the selected property
$stmt = $pdo->prepare("SELECT id, name, room_type, base_price, description, status FROM rooms WHERE property_id = ?");
$stmt->execute([$property_id]);
$rooms = $stmt->fetchAll();

// Fetch property details
$stmt_property = $pdo->prepare("SELECT name FROM properties WHERE id = ?");
$stmt_property->execute([$property_id]);
$property = $stmt_property->fetch();
?>

<!DOCTYPE html>
<html>

<head>
    <title>View Rooms for <?php echo htmlspecialchars($property['name']); ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <h2>Rooms for Property: <?php echo htmlspecialchars($property['name']); ?></h2>
    <p><a href="view_properties.php">Back to Properties List</a></p>
    <p><a href="add_room.php">Add New Room</a></p>

    <?php
    if (count($rooms) > 0) {
        echo "<table border='1'>
                <thead>
                    <tr>
                        <th>Room Name</th>
                        <th>Room Type</th>
                        <th>Base Price</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($rooms as $room) {
            echo "<tr>
                    <td>{$room['name']}</td>
                    <td>{$room['room_type']}</td>
                    <td>{$room['base_price']}</td>
                    <td>{$room['description']}</td>
                    <td>{$room['status']}</td>
                    <td>
                        <a href='edit_room.php?id={$room['id']}'>Edit</a> | 
                        <a href='delete_room.php?id={$room['id']}'>Delete</a>
                    </td>
                </tr>";
        }

        echo "</tbody>
            </table>";
    } else {
        echo "<p>No rooms found for this property. <a href='add_room.php'>Add a room</a></p>";
    }
    ?>
</body>

</html>