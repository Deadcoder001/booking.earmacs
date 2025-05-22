<?php
session_start();
include '../config/db.php';

// Process login form
if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch admin from database
    $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // Set session variable if login is successful
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['id'];
        header("Location: view_properties.php"); // Redirect to the admin dashboard
        exit;
    } else {
        $error_message = "Invalid email or password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <h2>Admin Login</h2>

    <?php
    if (isset($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
    }
    ?>

    <form method="post" action="">
        <label for="email">Email:</label>
        <input type="email" name="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>

        <button type="submit" name="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
</body>

</html>