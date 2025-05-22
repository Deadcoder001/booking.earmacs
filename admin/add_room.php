<?php
require '../config/db.php';

// Fetch all properties
$propertyStmt = $pdo->prepare("SELECT id, name FROM properties ORDER BY name ASC");
$propertyStmt->execute();
$properties = $propertyStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle room addition
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = $_POST['property_id'];
    $name = $_POST['name'];
    $base_price = $_POST['base_price'];
    $room_type = $_POST['room_type'];
    $status = $_POST['status'];
    $description = $_POST['description'];

    $insert = $pdo->prepare("INSERT INTO rooms (property_id, name, base_price, room_type, status, description, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $insert->execute([$property_id, $name, $base_price, $room_type, $status, $description]);

    header("Location: add_room.php");
    exit;
}

// Filters
$filterProperty = $_GET['property_id'] ?? '';
$filterStatus = $_GET['status'] ?? '';

// Build query with filters
$query = "SELECT rooms.*, properties.name AS property_name FROM rooms JOIN properties ON rooms.property_id = properties.id WHERE 1";
$params = [];

if ($filterProperty) {
    $query .= " AND rooms.property_id = ?";
    $params[] = $filterProperty;
}

if ($filterStatus) {
    $query .= " AND rooms.status = ?";
    $params[] = $filterStatus;
}

$query .= " ORDER BY rooms.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rooms Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="flex h-screen">

        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

    <main class="container py-4">
    <h2 class="mb-4">All Rooms</h2>

    <!-- Filters -->
    <form class="row mb-4" method="GET">
        <div class="col-md-4">
            <label>Filter by Property</label>
            <select name="property_id" class="form-select">
                <option value="">All Properties</option>
                <?php foreach ($properties as $property): ?>
                    <option value="<?= $property['id'] ?>" <?= ($filterProperty == $property['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($property['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label>Filter by Status</label>
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="available" <?= ($filterStatus == 'available') ? 'selected' : '' ?>>Available</option>
                <option value="booked" <?= ($filterStatus == 'booked') ? 'selected' : '' ?>>Booked</option>
                <option value="maintenance" <?= ($filterStatus == 'maintenance') ? 'selected' : '' ?>>Maintenance</option>
            </select>
        </div>
        <div class="col-md-4 align-self-end">
            <button class="btn btn-primary">Apply Filters</button>
        </div>
    </form>

    <!-- Room Cards -->
    <div class="row">
        <?php foreach ($rooms as $room): ?>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5><?= htmlspecialchars($room['name']) ?> <small class="text-muted">[<?= htmlspecialchars($room['room_type']) ?>]</small></h5>
                    <p><strong>Property:</strong> <?= htmlspecialchars($room['property_name']) ?></p>
                    <p><strong>Per Night:</strong> ₹<?= number_format($room['base_price'], 2) ?></p>
                    <p><strong>Status:</strong> <span class="badge bg-<?= $room['status'] == 'booked' ? 'danger' : ($room['status'] == 'available' ? 'success' : 'warning') ?>">
                        <?= ucfirst($room['status']) ?>
                    </span></p>
                    <p><?= nl2br(htmlspecialchars($room['description'])) ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Add Room Button -->
    <button class="btn btn-success mt-4" onclick="document.getElementById('addForm').classList.toggle('d-none')">+ Add Room</button>

    <!-- Add Room Form -->
    <div id="addForm" class="card p-4 mt-3 d-none">
        <form method="POST">
            <div class="mb-2">
                <label>Room No</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-2">
                <label>Room Type</label>
                <input type="text" name="room_type" class="form-control" required placeholder="e.g. Single, Double, Suite">
            </div>
            <div class="mb-2">
                <label>Base Price (per night)</label>
                <input type="number" step="0.01" name="base_price" class="form-control" required>
            </div>
            <div class="mb-2">
                <label>Status</label>
                <select name="status" class="form-select" required>
                    <option value="available">Available</option>
                    <option value="booked">Booked</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div class="mb-2">
                <label>Property</label>
                <select name="property_id" class="form-select" required>
                    <option value="">Select Property</option>
                    <?php foreach ($properties as $property): ?>
                        <option value="<?= $property['id'] ?>"><?= htmlspecialchars($property['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-2">
                <label>Description / Features</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Room</button>
        </form>
    </div>
</body>
</html>
