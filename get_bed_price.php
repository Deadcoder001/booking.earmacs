<?php
require './config/db.php';

if (isset($_GET['bed_id'])) {
    $bed_id = intval($_GET['bed_id']);
    $stmt = $pdo->prepare("SELECT price FROM hostel_beds WHERE id = ?");
    $stmt->execute([$bed_id]);
    $row = $stmt->fetch();
    echo json_encode($row);
}
