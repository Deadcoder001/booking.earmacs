<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require './config/db.php';
require 'vendor/autoload.php';// Path to Razorpay SDK

use Razorpay\Api\Api;

$api_key = 'rzp_test_0Y1F8erafToHmY';
$api_secret = 'yF5YGj09hTprP040e3kmDa0s'; // Replace with your Razorpay secret key

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_password'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $property_id = $_POST['property_id'];
    $check_in_date = $_POST['check_in_date'];
    $check_out_date = $_POST['check_out_date'];
    $package_id = $_POST['package_id'];
    $extra_persons = $_POST['extra_persons'];
    $total_price = preg_replace('/[^\d.]/', '', $_POST['totalPrice']);
    $password = $_POST['password'];

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, 'user')");
        $stmt->execute([$name, $email, $phone, $hashed]);
        $user_id = $pdo->lastInsertId();
    } else {
        $user_id = $user['id'];
    }

    // Always create guest record (for tracking)
    $stmt = $pdo->prepare("INSERT INTO guests (name, email, phone, user_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $phone, $user_id]);
    $guest_id = $pdo->lastInsertId();

    // Find available room
    $stmt = $pdo->prepare("SELECT id FROM rooms WHERE property_id = ? AND status = 'available' LIMIT 1");
    $stmt->execute([$property_id]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$room) {
        die("No available rooms for booking.");
    }

    // Insert booking
    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, guest_id, room_id, check_in_date, check_out_date, total_price, discounted_price, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NULL, 'pending', NOW())");
    $stmt->execute([$user_id, $guest_id, $room['id'], $check_in_date, $check_out_date, $total_price]);
    $booking_id = $pdo->lastInsertId();

    // Fetch property name using property_id
    $stmt = $pdo->prepare("SELECT name FROM properties WHERE id = ?");
    $stmt->execute([$property_id]);
    $property = $stmt->fetch(PDO::FETCH_ASSOC);
    $property_name = $property ? $property['name'] : 'EARMACS Booking';

    // === Razorpay Order Creation ===
    $api = new Api($api_key, $api_secret);
    $razorpay_order = $api->order->create([
        'receipt' => 'order_rcptid_' . $booking_id,
        'amount' => (int)($total_price * 100), // in paise
        'currency' => 'INR',
        'payment_capture' => 1
    ]);
    $razorpay_order_id = $razorpay_order['id'];
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Proceed to Payment</title>
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
    <div class="container mt-5">
        <div class="alert alert-info">
            Click the button below to complete your payment.
        </div>
        <button id="payBtn" class="btn btn-success">Pay Now</button>
    </div>
    <script>
        var options = {
            "key": "<?= $api_key ?>",
            "amount": <?= (int)($total_price * 100) ?>,
            "currency": "INR",
            "name": "<?= htmlspecialchars($property_name) ?>",
            "description": "Booking Payment",
            "order_id": "<?= $razorpay_order_id ?>",
            "prefill": {
                "name": "<?= htmlspecialchars($name) ?>",
                "contact": "<?= htmlspecialchars($phone) ?>",
                "email": "<?= htmlspecialchars($email) ?>"
            },
            "handler": function (response){
                window.location.href = "payment-success.php?booking_id=<?= $booking_id ?>
                    &razorpay_payment_id=" + response.razorpay_payment_id +
                    "&razorpay_order_id=" + response.razorpay_order_id +
                    "&razorpay_signature=" + response.razorpay_signature;
            },
            "theme": {
                "color": "#3399cc"
            }
        };
        var rzp1 = new Razorpay(options);
        document.getElementById('payBtn').onclick = function(e) {
            e.preventDefault();
            rzp1.open();
        };
    </script>
    </body>
    </html>
    <?php
    exit;
}
?>