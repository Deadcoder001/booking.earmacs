<!DOCTYPE html>
<html>
<head>
    <title>Agent Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Agent Login</h2>

    <?php
    session_start();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require '../config/db.php'; // PDO connection file

        $phone = $_POST['phone'];
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ? AND role = 'agent'");
        $stmt->execute([$phone]);
        $user = $stmt->fetch();

        if ($user) {
            if (password_verify($password, $user['password_hash'])) {
                if ($user['status'] === 'approved') {
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['name'] = $user['name'];

                    echo "<div class='alert alert-success'>Login successful. Welcome, " . htmlspecialchars($user['name']) . "!</div>";
                    // Redirect to agent dashboard
                     header("Location: agent_dashboard.php");
                    // exit;
                } else {
                    echo "<div class='alert alert-warning'>You're not approved yet. Please wait for admin approval.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Incorrect password.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Phone number not found.</div>";
        }
    }
    ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Phone Number:</label>
            <input type="text" name="phone" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>

</body>
</html>
