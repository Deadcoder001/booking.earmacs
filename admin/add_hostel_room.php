<?php
require '../config/db.php';
include 'sidebar.php'; // Make sure you have a sidebar.php in your admin folder

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_number = $_POST['room_number'];

    $stmt = $pdo->prepare("INSERT INTO hostel_rooms (room_number) VALUES (?)");
    $stmt->execute([$room_number]);

    echo "<script>alert('Room added successfully'); window.location.href='add_hostel_room.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Hostel Room</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="styles.css">
    <style>
        body { background: #f8fafc; }
        .main-content { margin-left: 220px; padding: 2rem 1rem; }
        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 1rem; }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="container">
            <div class="card shadow-sm mx-auto" style="max-width: 400px;">
                <div class="card-body">
                    <h3 class="card-title mb-4 text-center">Add Hostel Room</h3>
                    <form method="POST" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label">Room Number</label>
                            <input type="text" name="room_number" required class="form-control">
                        </div>
                        <button type="submit" class="btn btn-success w-100">Add Room</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
