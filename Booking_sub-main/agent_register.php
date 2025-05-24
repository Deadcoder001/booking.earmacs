<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$showModal = false;
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    require '../config/db.php';

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = 'agent';
    $status = 'pending';

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errorMsg = "This email is already registered. Please use another email or login.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password_hash, role, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        if ($stmt->execute([$name, $email, $phone, $password, $role, $status])) {
            $showModal = true;
        } else {
            $errorMsg = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Agent Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%);
            min-height: 100vh;
        }
        .card {
            border-radius: 1.5rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            max-width: 420px;
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
            .card {
                padding: 1rem !important;
            }
        }
    </style>
</head>
<body>
<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="card p-4 p-md-5 w-100">
        <div class="text-center mb-4">
            <i class="bi bi-person-badge-fill text-primary" style="font-size:2.5rem;"></i>
            <h2 class="fw-bold mt-2 mb-0">Agent Registration</h2>
            <p class="text-muted mb-0">Join as an EARMACS agent</p>
        </div>
        <?php if ($errorMsg): ?>
            <div class='alert alert-danger text-center py-2 mb-3'><?= $errorMsg ?></div>
        <?php endif; ?>
        <form method="POST" action="" autocomplete="off">
            <div class="mb-3 icon-input">
                <label class="form-label">Name</label>
                <i class="bi bi-person"></i>
                <input type="text" name="name" class="form-control" required maxlength="100">
            </div>
            <div class="mb-3 icon-input">
                <label class="form-label">Email</label>
                <i class="bi bi-envelope"></i>
                <input type="email" name="email" class="form-control" required maxlength="100">
            </div>
            <div class="mb-3 icon-input">
                <label class="form-label">Phone</label>
                <i class="bi bi-telephone"></i>
                <input type="text" name="phone" class="form-control" required maxlength="20" pattern="[0-9+ ]+">
            </div>
            <div class="mb-3 icon-input">
                <label class="form-label">Password</label>
                <i class="bi bi-lock"></i>
                <input type="password" name="password" class="form-control" required minlength="6" maxlength="100">
            </div>
            <button type="submit" name="register" class="btn btn-primary w-100 py-2 mt-2 fw-semibold">
                <i class="bi bi-person-plus"></i> Register
            </button>
        </form>
        <div class="text-center mt-3">
            <small>Already registered? <a href="agent_login.php">Login here</a></small>
        </div>
    </div>
</div>

<!-- Registration Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-success">
      <div class="modal-header">
        <h5 class="modal-title text-success" id="successModalLabel"><i class="bi bi-check-circle-fill"></i> Registration Successful</h5>
      </div>
      <div class="modal-body">
        Thank you for registering! We will update you on your phone number once you are approved.<br>
        After approval, you will be able to log in.
      </div>
      <div class="modal-footer">
        <a href="agent_login.php" class="btn btn-success w-100">Go to Login</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php if ($showModal): ?>
<script>
    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
    successModal.show();
</script>
<?php endif; ?>
</body>
</html>
