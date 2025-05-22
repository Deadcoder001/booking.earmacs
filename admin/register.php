<?php
session_start();
include '../config/db.php';

// Process registration form
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists in the database
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $email_count = $stmt->fetchColumn();

    if ($email_count > 0) {
        $error_message = "Email is already registered. Please use another email.";
    } else {
        // Insert new admin user into the database
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute([$name, $email, $hashed_password]);

        // Redirect to the login page after successful registration
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Registration</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <h2>Admin Registration</h2>

    <?php
    if (isset($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
    }
    ?>

    <form method="post" action="">
        <label for="name">Name:</label>
        <input type="text" name="name" required><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>

        <button type="submit" name="submit">Register</button>
    </form>
</body>

</html>