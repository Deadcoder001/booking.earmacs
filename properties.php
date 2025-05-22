<?php
require 'db.php';

// Fetch all properties
$stmt = $pdo->prepare("SELECT * FROM properties ORDER BY created_at DESC");
$stmt->execute();
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Properties</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h2 class="mb-4">Available Properties</h2>

    <div class="row">
        <?php foreach ($properties as $property): ?>
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title"><?= htmlspecialchars($property['name']) ?></h4>
                        <p><strong>Location:</strong> <?= htmlspecialchars($property['location']) ?></p>
                        <p><?= nl2br(htmlspecialchars($property['description'])) ?></p>

                        <a href="book_form.php?property_id=<?= $property['id'] ?>" class="btn btn-primary">Book Now</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
