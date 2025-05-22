<?php
require '../config/db.php';

// Fetch all properties
$stmt = $pdo->prepare("SELECT * FROM properties ORDER BY created_at ASC");
$stmt->execute();
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total properties
$totalProperties = count($properties);

// Get room and booking info for each property
$roomInfo = [];
foreach ($properties as $property) {
    $property_id = $property['id'];

    // Total rooms
    $stmtRooms = $pdo->prepare("SELECT COUNT(*) FROM rooms WHERE property_id = ?");
    $stmtRooms->execute([$property_id]);
    $totalRooms = $stmtRooms->fetchColumn();

    // Booked rooms
    $stmtBooked = $pdo->prepare("SELECT COUNT(*) FROM rooms WHERE property_id = ? AND status = 'booked'");
    $stmtBooked->execute([$property_id]);
    $bookedRooms = $stmtBooked->fetchColumn();

    $roomInfo[$property_id] = [
        'total' => $totalRooms,
        'booked' => $bookedRooms
    ];
}

// Handle new property creation
if ($_SERVER["REQUEST_METHOD"] === "POST" && $totalProperties < 4) {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $description = $_POST['description'];

    $insert = $pdo->prepare("INSERT INTO properties (name, location, description, created_at) VALUES (?, ?, ?, NOW())");
    $insert->execute([$name, $location, $description]);

    header("Location: add_property.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Properties</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="flex h-screen">

        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

    <main class="container py-4">
    <h2 class="mb-4">My Properties (<?= $totalProperties ?>/4)</h2>

    <div class="row">
        <?php foreach ($properties as $property): 
            $id = $property['id'];
            $booked = $roomInfo[$id]['booked'];
            $total = $roomInfo[$id]['total'];
            $percentage = ($total > 0) ? ($booked / $total) * 100 : 0;
        ?>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5><?= htmlspecialchars($property['name']) ?></h5>
                    <p><strong>Location:</strong> <?= htmlspecialchars($property['location']) ?></p>
                    <p><?= nl2br(htmlspecialchars($property['description'])) ?></p>

                    <!-- Progress bar -->
                    <p class="mb-1"><strong>Bookings:</strong> <?= $booked ?>/<?= $total ?></p>
                    <div class="progress mb-2" style="height: 20px;">
                        <div class="progress-bar bg-success" role="progressbar"
                             style="width: <?= $percentage ?>%;" 
                             aria-valuenow="<?= $booked ?>" aria-valuemin="0" aria-valuemax="<?= $total ?>">
                             <?= $booked ?>/<?= $total ?>
                        </div>
                    </div>

                    <a href="add_room.php?property_id=<?= $id ?>" class="btn btn-sm btn-outline-primary">+ Add Room</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Add Property Form -->
    <?php if ($totalProperties < 4): ?>
        <button class="btn btn-success mt-4" onclick="document.getElementById('addForm').classList.toggle('d-none')">+ Add Property</button>
        <div id="addForm" class="card p-4 mt-3 d-none">
            <form method="POST">
                <div class="mb-2">
                    <label>Property Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Location</label>
                    <input type="text" name="location" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Property</button>
            </form>
        </div>
    <?php else: ?>
        <div class="alert alert-warning mt-4">You have reached the maximum number of 4 properties.</div>
    <?php endif; ?>
</body>
</html>
