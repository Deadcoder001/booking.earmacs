<?php
session_start();
include '../config/db.php';

// Check if the admin is logged in, else redirect to login page
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Fetch all properties from the database
$stmt = $pdo->query("SELECT id, name, location, description FROM properties ORDER BY name");
$properties = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>View Properties</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <h2>Properties List</h2>
    <p><a href="add_property.php">Add New Property</a></p>
    <h2>Add Features</h2>
    <p><a href="add_features.php">Add New Features</a></p>

    <?php
    // Display properties in a table
    if (count($properties) > 0) {
        echo "<table border='1'>
                <thead>
                    <tr>
                        <th>Property Name</th>
                        <th>Location</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($properties as $property) {
            echo "<tr>
                    <td>{$property['name']}</td>
                    <td>{$property['location']}</td>
                    <td>{$property['description']}</td>
                    <td>
                        <a href='add_room.php?property_id={$property['id']}'>Add Rooms</a> | 
                        <a href='view_rooms.php?property_id={$property['id']}'>View Rooms</a> | 
                        <a href='edit_property.php?id={$property['id']}'>Edit</a> | 
                        <a href='delete_property.php?id={$property['id']}'>Delete</a>
                    </td>
                </tr>";
        }

        echo "</tbody>
            </table>";
    } else {
        echo "<p>No properties found. <a href='add_property.php'>Add a property</a></p>";
    }
    ?>
</body>

</html>