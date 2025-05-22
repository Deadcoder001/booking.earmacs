<!-- agent_register.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Agent Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Agent Registration</h2>
    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Name:</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Phone:</label>
            <input type="text" name="phone" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" name="register" class="btn btn-primary">Register</button>
    </form>
</div>

<!-- Bootstrap Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-success">
      <div class="modal-header">
        <h5 class="modal-title text-success" id="successModalLabel">Registration Successful</h5>
      </div>
      <div class="modal-body">
        Thank you for registering! We will update you on your phone number once you are approved.<br>
        After approval, you will be able to log in.
      </div>
      <div class="modal-footer">
        <a href="agent_login.php" class="btn btn-success">Go to Login</a>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php
if (isset($_POST['register'])) {
    require '../config/db.php'; // your PDO connection file

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = 'agent';
    $status = 'pending';

    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password_hash, role, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");

    if ($stmt->execute([$name, $email, $phone, $password, $role, $status])) {
        // Trigger modal using JS
        echo "<script>
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
              </script>";
    } else {
        echo "<div class='alert alert-danger mt-3 text-center'>Registration failed. Please try again.</div>";
    }
}
?>

</body>
</html>
