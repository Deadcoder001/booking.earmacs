<?php
require '../config/db.php';
session_start();

// Assuming user is logged in, agent's user_id is stored in session
$agent_id = $_SESSION['user_id'] ?? 0;

// Fetch all guests for selection
$guests = $pdo->query("SELECT id, name FROM guests")->fetchAll();

// Fetch all properties for selection
$properties = $pdo->query("SELECT id, name FROM properties")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guest_id = $_POST['guest_id'];
    $property_id = $_POST['property_id'];
    $room_id = $_POST['room_id'];
    $check_in_date = $_POST['check_in_date'];
    $check_out_date = $_POST['check_out_date'];

    // Fetch room base price
    $stmt = $pdo->prepare("SELECT base_price FROM rooms WHERE id = ?");
    $stmt->execute([$room_id]);
    $room = $stmt->fetch();
    $base_price = $room['base_price'];

    // Calculate total price
    $check_in = new DateTime($check_in_date);
    $check_out = new DateTime($check_out_date);
    $interval = $check_in->diff($check_out);
    $total_days = $interval->days;
    $total_price = $base_price * $total_days;

    // Insert booking into the database
    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, guest_id, room_id, check_in_date, check_out_date, total_price, status, created_at) 
                           VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->execute([$agent_id, $guest_id, $room_id, $check_in_date, $check_out_date, $total_price]);

    echo "<script>alert('Booking successful!'); window.location.href='agent_dashboard.php';</script>";
}
?>

<form method="post" class="p-4">
    <h3>Book Property for Guest</h3>

    <label>Choose Guest</label>
    <select name="guest_id" class="form-control mb-2" required>
        <?php foreach ($guests as $guest): ?>
            <option value="<?= $guest['id'] ?>"><?= htmlspecialchars($guest['name']) ?></option>
        <?php endforeach ?>
    </select>

    <label>Choose Property</label>
    <select name="property_id" class="form-control mb-2" id="property_select" required>
        <?php foreach ($properties as $property): ?>
            <option value="<?= $property['id'] ?>"><?= htmlspecialchars($property['name']) ?></option>
        <?php endforeach ?>
    </select>

    <label>Choose Room</label>
    <select name="room_id" class="form-control mb-2" id="room_select" required>
        <option value="">Select Property First</option>
    </select>

    <label>Check-in Date</label>
    <input type="date" name="check_in_date" class="form-control mb-2" required>

    <label>Check-out Date</label>
    <input type="date" name="check_out_date" class="form-control mb-2" required>

    <button type="submit" class="btn btn-success">Book Property</button>
</form>

<script>
// Fetch rooms when a property is selected
document.getElementById('property_select').addEventListener('change', function() {
    var property_id = this.value;
    var room_select = document.getElementById('room_select');
    
    // Clear previous room options
    room_select.innerHTML = '<option value="">Loading rooms...</option>';

    fetch('get_rooms.php?property_id=' + property_id)
        .then(response => response.json())
        .then(data => {
            room_select.innerHTML = '<option value="">Select Room</option>';
            data.rooms.forEach(room => {
                var option = document.createElement('option');
                option.value = room.id;
                option.textContent = room.name + " - " + room.room_type + " ($" + room.base_price + ")";
                room_select.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching rooms:', error));
});
</script>
