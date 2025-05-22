<?php
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_number = $_POST['room_number'];

    $stmt = $pdo->prepare("INSERT INTO hostel_rooms (room_number) VALUES (?)");
    $stmt->execute([$room_number]);

    echo "<script>alert('Room added successfully');</script>";
}
?>

<h2>Add Room</h2>
<form method="POST">
    <label>Room Number:</label>
    <input type="text" name="room_number" required class="form-control">
    <button type="submit">Add Room</button>
</form>
