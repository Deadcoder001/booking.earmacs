<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: agent_book_form.php');
    exit;
}
$bookingData = $_POST;
$amount = isset($bookingData['discounted_price']) ? floatval(str_replace(['₹', ','], '', $bookingData['discounted_price'])) : 0;
$razorpayAmount = intval($amount * 100); // in paise
?>
<!DOCTYPE html>
<html>
<head>
    <title>Agent Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%);
            min-height: 100vh;
        }
        .payment-card {
            border-radius: 1.5rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
            max-width: 430px;
            margin: auto;
        }
        .summary-label {
            font-weight: 500;
            color: #374151;
        }
        .summary-value {
            color: #2563eb;
            font-weight: 600;
        }
        .amount-pay {
            font-size: 1.3rem;
            font-weight: 700;
            color: #16a34a;
        }
        @media (max-width: 576px) {
            .payment-card {
                padding: 1rem !important;
            }
        }
    </style>
</head>
<body>
<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="payment-card bg-white p-4 p-md-5 w-100">
        <div class="text-center mb-4">
            <i class="bi bi-credit-card-2-front-fill text-success" style="font-size:2.5rem;"></i>
            <h2 class="fw-bold mt-2 mb-0">Confirm & Pay</h2>
            <p class="text-muted mb-0">Review your booking and pay securely</p>
        </div>
        <div class="mb-4">
            <h5 class="mb-3">Booking Summary</h5>
            <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item d-flex justify-content-between">
                    <span class="summary-label">Name:</span>
                    <span class="summary-value"><?= htmlspecialchars($bookingData['name']) ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="summary-label">Phone:</span>
                    <span class="summary-value"><?= htmlspecialchars($bookingData['phone']) ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="summary-label">Email:</span>
                    <span class="summary-value"><?= htmlspecialchars($bookingData['email']) ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="summary-label">Check-in:</span>
                    <span class="summary-value"><?= htmlspecialchars($bookingData['check_in_date']) ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="summary-label">Check-out:</span>
                    <span class="summary-value"><?= htmlspecialchars($bookingData['check_out_date']) ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="summary-label">Amount to Pay:</span>
                    <span class="amount-pay">₹<?= number_format($amount, 2) ?></span>
                </li>
            </ul>
        </div>
        <button id="rzp-button" class="btn btn-success w-100 py-2 fw-semibold">
            <i class="bi bi-currency-rupee"></i> Pay with Razorpay
        </button>
        <form id="finalizeBooking" method="POST" action="process_booking_agent.php" style="display:none;">
            <?php foreach ($bookingData as $key => $value): ?>
                <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
            <?php endforeach; ?>
            <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
            <input type="hidden" name="discounted_price" value="<?= htmlspecialchars($amount) ?>">
        </form>
    </div>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<script>
    document.getElementById('rzp-button').onclick = function(e){
        e.preventDefault();
        var options = {
            "key": "rzp_test_Uf88y3rnsJK0DG", // Replace with your Razorpay key
            "amount": "<?= $razorpayAmount ?>",
            "currency": "INR",
            "name": "EARMACS Booking",
            "description": "Room Booking Payment",
            "handler": function (response){
                document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                document.getElementById('finalizeBooking').submit();
            },
            "prefill": {
                "name": "<?= htmlspecialchars($bookingData['name']) ?>",
                "email": "<?= htmlspecialchars($bookingData['email']) ?>",
                "contact": "<?= htmlspecialchars($bookingData['phone']) ?>"
            },
            "theme": {
                "color": "#28a745"
            }
        };
        var rzp = new Razorpay(options);
        rzp.open();
    }
</script>
</body>
</html>