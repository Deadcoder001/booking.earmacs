<?php
// agent_requests.php
require '../config/db.php'; // Your PDO DB connection
session_start();

// Optional: Check if admin is logged in
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
//     header("Location: login.php");
//     exit;
// }

// Handle approval
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $stmt = $pdo->prepare("UPDATE users SET status = 'approved' WHERE id = ? AND role = 'agent'");
    $stmt->execute([$id]);
    header("Location: agent_requests.php");
    exit;
}
// Handle rejection
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'agent' AND status = 'pending'");
    $stmt->execute([$id]);
    header("Location: agent_requests.php");
    exit;
}

// Fetch pending agent requests
$stmt = $pdo->prepare("SELECT id, name, email, phone, created_at FROM users WHERE role = 'agent' AND status = 'pending'");
$stmt->execute();
$agents = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Agent Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="flex h-screen">

        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?> 


    <main class="p-4">
    <h2 class="mb-4">Pending Agent Approval Requests</h2>

    <?php if (count($agents) === 0): ?>
        <div class="alert alert-info">No pending agent requests.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Requested On</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($agents as $agent): ?>
                    <tr>
                        <td><?= htmlspecialchars($agent['name']) ?></td>
                        <td><?= htmlspecialchars($agent['email']) ?></td>
                        <td><?= htmlspecialchars($agent['phone']) ?></td>
                        <td><?= htmlspecialchars($agent['created_at']) ?></td>
                        <td>
                            <a href="?approve=<?= $agent['id'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Approve this agent?')">Approve</a>
                            <a href="?reject=<?= $agent['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Reject this agent?')">Reject</a>

                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php endif ?>
</body>
</html>
