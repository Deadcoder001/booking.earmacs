<?php
require './config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Save guest info
    $stmt = $pdo->prepare("INSERT INTO guests (name, phone, email) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['name'], $_POST['phone'], $_POST['email']]);
    $guest_id = $pdo->lastInsertId();

    // Save guest session or data temporarily
    session_start();
    $_SESSION['guest_id'] = $guest_id;
    $_SESSION['checkin'] = $_POST['checkin'];
    $_SESSION['checkout'] = $_POST['checkout'];
}
?>

<h3>Select Room & Beds</h3>

<form method="POST" action="Hostel_process_booking.php">
    <div class="mb-3">
        <label>Select Room</label>
        <select name="room_id" class="form-control" id="roomSelect" required>
            <option value="">--Select--</option>
            <?php
            $rooms = $pdo->query("SELECT id, room_number FROM hostel_rooms")->fetchAll();
            foreach ($rooms as $room) {
                echo "<option value='{$room['id']}'>Room {$room['room_number']}</option>";
            }
            ?>
        </select>
    </div>

    <div id="bedsContainer" class="mb-3">
        <!-- Beds will load here -->
    </div>

    <button type="submit">Confirm Booking</button>
</form>

<script>
document.getElementById('roomSelect').addEventListener('change', function () {
    const roomId = this.value;

    fetch('fetch_beds.php?room_id=' + roomId)
        .then(res => res.json())
        .then(data => {
            const bedsDiv = document.getElementById('bedsContainer');
            bedsDiv.innerHTML = '';

            if (data.length === 0) {
                bedsDiv.innerHTML = '<p>No beds available.</p>';
                return;
            }

            data.forEach(bed => {
                const cb = document.createElement('input');
                cb.type = 'checkbox';
                cb.name = 'bed_ids[]';
                cb.value = bed.id;

                const label = document.createElement('label');
                label.innerHTML = ` Bed ${bed.bed_number} (₹${bed.price})`;
                const div = document.createElement('div');
                div.appendChild(cb);
                div.appendChild(label);
                bedsDiv.appendChild(div);
            });
        });
});
</script>
