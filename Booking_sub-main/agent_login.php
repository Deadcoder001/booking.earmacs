<?php
session_start();
$loginMsg = '';
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
                header("Location: agent_dashboard.php");
                exit;
            } else {
                $loginMsg = "<div class='alert alert-warning text-center'>You're not approved yet. Please wait for admin approval.</div>";
            }
        } else {
            $loginMsg = "<div class='alert alert-danger text-center'>Incorrect password.</div>";
        }
    } else {
        $loginMsg = "<div class='alert alert-danger text-center'>Phone number not found.</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Agent Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%);
            min-height: 100vh;
        }
        .login-card {
            border-radius: 1.5rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            max-width: 400px;
            margin: auto;
        }
        .form-control, .btn {
            border-radius: 0.75rem;
            font-size: 1.1rem;
        }
        .form-label {
            font-weight: 500;
        }
        .icon-input {
            position: relative;
        }
        .icon-input i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 1.2rem;
        }
        .icon-input input {
            padding-left: 2.2rem;
        }
        @media (max-width: 576px) {
            .login-card {
                padding: 1rem !important;
            }
        }
    </style>
</head>
<body>
<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="login-card bg-white p-4 p-md-5 w-100">
        <div class="text-center mb-4">
            <i class="bi bi-person-circle text-primary" style="font-size:2.5rem;"></i>
            <h2 class="fw-bold mt-2 mb-0">Agent Login</h2>
            <p class="text-muted mb-0">Login to your agent account</p>
        </div>
        <?= $loginMsg ?>
        <form method="POST" action="" autocomplete="off">
            <div class="mb-3 icon-input">
                <label class="form-label">Phone Number</label>
                <i class="bi bi-telephone"></i>
                <input type="text" name="phone" class="form-control" required maxlength="20" pattern="[0-9+ ]+">
            </div>
            <div class="mb-3 icon-input">
                <label class="form-label">Password</label>
                <i class="bi bi-lock"></i>
                <input type="password" name="password" class="form-control" required minlength="6" maxlength="100">
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 mt-2 fw-semibold">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </button>
        </form>
        <div class="text-center mt-3">
            <small>Don't have an account? <a href="agent_register.php">Register here</a></small>
        </div>
    </div>
</div>
</body>
</html>
