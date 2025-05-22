<?php
require '../config/db.php';

$property_id = $_GET['property_id'] ?? null;

if (!$property_id) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, name FROM rooms WHERE property_id = ?");
$stmt->execute([$property_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
