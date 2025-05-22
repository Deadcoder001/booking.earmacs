<?php
require '../config/db.php';
session_start();

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'agent') {
    header('Location: agent_login.php');
    exit();
}

if (!isset($_GET['guest_id'])) {
    echo "Guest not specified.";
    exit();
}

$guest_id = $_GET['guest_id'];

// Fetch guest to verify existence
$guestStmt = $pdo->prepare("SELECT * FROM guests WHERE id = ? AND user_id = ?");
$guestStmt->execute([$guest_id, $_SESSION['user_id']]);
$guest = $guestStmt->fetch();

if (!$guest) {
    echo "Invalid guest.";
    exit();
}

// Fetch all properties
$propStmt = $pdo->prepare("SELECT * FROM properties ORDER BY created_at DESC");
$propStmt->execute();
$properties = $propStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Property for Guest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h3>Select Property for: <?= htmlspecialchars($guest['name']) ?></h3>
    <p>Email: <?= htmlspecialchars($guest['email']) ?> | Phone: <?= htmlspecialchars($guest['phone']) ?></p>

    <div class="row">
        <?php foreach ($properties as $property): ?>
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title"><?= htmlspecialchars($property['name']) ?></h4>
                        <p><strong>Location:</strong> <?= htmlspecialchars($property['location']) ?></p>
                        <p><?= nl2br(htmlspecialchars($property['description'])) ?></p>
                        <a href="book_form.php?property_id=<?= $property['id'] ?>&guest_id=<?= $guest_id ?>" class="btn btn-primary">Select Property</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
