<?php
session_start();
include '../config/db.php';  // Include your database connection file

// Check if the agent is logged in
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'agent') {
    header('Location: agent_login.php');
    exit();
}

// Fetch agent data (logged-in user)
$agent_id = $_SESSION['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Insert the guest into the database
    $stmt = $pdo->prepare("INSERT INTO guests (user_id, name, email, phone, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$agent_id, $name, $email, $phone]);

    // Get the last inserted guest's ID
    $guest_id = $pdo->lastInsertId();

    // Set a flash success message
    $_SESSION['success'] = "Guest added successfully!";

    // Redirect to dashboard
    header("Location: agent_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Guest</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Add New Guest</h2>
        <form action="agent_add_guest.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Guest Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Guest Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Guest Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Guest</button>
        </form>
    </div>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
