<?php
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'];
    $bed_number = $_POST['bed_number'];
    $price = $_POST['price'];

    $stmt = $pdo->prepare("INSERT INTO hostel_beds (room_id, bed_number, price) VALUES (?, ?, ?)");
    $stmt->execute([$room_id, $bed_number, $price]);

    echo "<script>alert('Bed added successfully');</script>";
}
?>

<h2>Add Bed to Room</h2>
<form method="POST">
    <label>Select Room:</label>
    <select name="room_id" class="form-control" required>
        <option value="">--Select Room--</option>
        <?php
        $rooms = $pdo->query("SELECT * FROM hostel_rooms")->fetchAll();
        foreach ($rooms as $room) {
            echo "<option value='{$room['id']}'>Room {$room['room_number']}</option>";
        }
        ?>
    </select>

    <label>Bed Number:</label>
    <input type="text" name="bed_number" required class="form-control">

    <label>Price Per Night:</label>
    <input type="number" name="price" step="0.01" required class="form-control">

    <button type="submit">Add Bed</button>
</form>
