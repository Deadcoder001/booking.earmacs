<?php
require './config/db.php';

$bed_ids = explode(",", $_GET['bed_ids'] ?? "");

$placeholders = implode(",", array_fill(0, count($bed_ids), "?"));
$stmt = $pdo->prepare("SELECT SUM(price) as total_price FROM hostel_beds WHERE id IN ($placeholders)");
$stmt->execute($bed_ids);

$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode(['total_price' => $result['total_price'] ?? 0]);
?>
