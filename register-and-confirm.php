<?php
require './config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['set_password'])) {
    // Sanitize input
    $name = htmlspecialchars($_POST['name']);
    $phone = htmlspecialchars($_POST['phone']);
    $email = htmlspecialchars($_POST['email']);
    $property_id = intval($_POST['property_id']);
    $check_in_date = htmlspecialchars($_POST['check_in_date']);
    $check_out_date = htmlspecialchars($_POST['check_out_date']);
    $package_id = intval($_POST['package_id']);
    $extra_persons = intval($_POST['extra_persons']);
    $total_price = $_POST['totalPrice'];
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Confirm Details & Register</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-gray-100">
    <div class="container mt-5">
        <h2 class="mb-4">Confirm Your Details</h2>
        <form method="POST" action="register-and-book.php">
            <input type="hidden" name="name" value="<?= $name ?>">
            <input type="hidden" name="phone" value="<?= $phone ?>">
            <input type="hidden" name="email" value="<?= $email ?>">
            <input type="hidden" name="property_id" value="<?= $property_id ?>">
            <input type="hidden" name="check_in_date" value="<?= $check_in_date ?>">
            <input type="hidden" name="check_out_date" value="<?= $check_out_date ?>">
            <input type="hidden" name="package_id" value="<?= $package_id ?>">
            <input type="hidden" name="extra_persons" value="<?= $extra_persons ?>">
            <input type="hidden" name="totalPrice" value="<?= $total_price ?>">
            <input type="hidden" name="set_password" value="1">
            <div class="mb-3">
                <label>Name:</label>
                <input type="text" class="form-control" value="<?= $name ?>" readonly>
            </div>
            <div class="mb-3">
                <label>Phone:</label>
                <input type="text" class="form-control" value="<?= $phone ?>" readonly>
            </div>
            <div class="mb-3">
                <label>Email:</label>
                <input type="text" class="form-control" value="<?= $email ?>" readonly>
            </div>
            <div class="mb-3">
                <label>Create Password *</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Register & Continue Booking</button>
        </form>
    </div>
    </body>
    </html>
    <?php
    exit;
}
?>