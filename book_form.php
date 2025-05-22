<?php
require './config/db.php';

if (!isset($_GET['property_id']) || !is_numeric($_GET['property_id'])) {
    die("Invalid property ID");
}

$property_id = $_GET['property_id'];

// Get property details
$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->execute([$property_id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    die("Property not found");
}

// Fetch one available room for base price estimation
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE property_id = ? AND status = 'available' LIMIT 1");
$stmt->execute([$property_id]);
$exampleRoom = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$exampleRoom) {
    die("No available rooms in this property.");
}

// Count total rooms
$stmt = $pdo->prepare("SELECT COUNT(*) FROM rooms WHERE property_id = ?");
$stmt->execute([$property_id]);
$totalRooms = $stmt->fetchColumn();

// Fetch bookings for this property
$stmt = $pdo->prepare("SELECT check_in_date, check_out_date FROM bookings 
    JOIN rooms ON bookings.room_id = rooms.id 
    WHERE rooms.property_id = ?");
$stmt->execute([$property_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count bookings per day
$dateCounts = [];
foreach ($bookings as $booking) {
    $start = new DateTime($booking['check_in_date']);
    $end = new DateTime($booking['check_out_date']);
    $range = new DatePeriod($start, new DateInterval('P1D'), $end); // exclude checkout
    foreach ($range as $date) {
        $d = $date->format('Y-m-d');
        $dateCounts[$d] = ($dateCounts[$d] ?? 0) + 1;
    }
}

// Fully booked dates
$fullyBookedDates = [];
foreach ($dateCounts as $date => $count) {
    if ($count >= $totalRooms) {
        $fullyBookedDates[] = $date;
    }
}

// Fetch available packages
$pkgStmt = $pdo->prepare("SELECT id, occupancy_type, package_type, b2c_rate, extra_person_rate FROM packages WHERE property_id = ?");
$pkgStmt->execute([$property_id]);
$packages = $pkgStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Book Room - <?= htmlspecialchars($property['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body class="container py-4">
    <h2>Book a Room at <?= htmlspecialchars($property['name']) ?></h2>

    <form method="POST" action="process_booking.php">
        <input type="hidden" name="property_id" value="<?= $property_id ?>">

        <div class="mb-3">
            <label>Your Name *</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Phone Number *</label>
            <input type="text" name="phone" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email (optional)</label>
            <input type="email" name="email" class="form-control">
        </div>

        <div class="mb-3">
            <label>Check-in Date *</label>
            <input type="text" name="check_in_date" id="checkIn" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Check-out Date *</label>
            <input type="text" name="check_out_date" id="checkOut" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Select Package *</label>
            <select class="form-select" id="package_id" name="package_id" required>
                <option value="">-- Select --</option>
                <?php foreach ($packages as $pkg): ?>
                    <option value="<?= $pkg['id'] ?>"
                        data-b2c="<?= $pkg['b2c_rate'] ?>"
                        data-extra="<?= $pkg['extra_person_rate'] ?>">
                        <?= htmlspecialchars($pkg['package_type']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Extra Persons</label>
            <input type="number" class="form-control" id="extra_persons" name="extra_persons" min="0" value="0">
        </div>

        <div class="mb-3">
            <label>Total Price:</label>
            <input type="text" id="totalPrice" class="form-control" readonly>
        </div>

        <button type="submit" class="btn btn-success">Confirm Booking</button>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        const fullyBooked = <?= json_encode($fullyBookedDates) ?>;
        const packages = <?= json_encode($packages) ?>;
        const today = new Date().toISOString().split('T')[0];

        const checkIn = flatpickr("#checkIn", {
            dateFormat: "Y-m-d",
            disable: fullyBooked,
            minDate: today,
            onChange: function(selectedDates, dateStr) {
                checkOut.set("minDate", dateStr);
                calculatePrice();
            }
        });

        const checkOut = flatpickr("#checkOut", {
            dateFormat: "Y-m-d",
            disable: fullyBooked,
            minDate: today,
            onChange: calculatePrice
        });

        document.getElementById("package_id").addEventListener("change", calculatePrice);
        document.getElementById("extra_persons").addEventListener("input", calculatePrice);

        function calculatePrice() {
            const checkInDate = new Date(document.getElementById("checkIn").value);
            const checkOutDate = new Date(document.getElementById("checkOut").value);
            const packageId = document.getElementById("package_id").value;
            const extraPersons = parseInt(document.getElementById("extra_persons").value) || 0;

            // Validate date range
            if (checkInDate && checkOutDate && checkOutDate > checkInDate && packageId) {
                const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
                const selectedOption = document.querySelector(`#package_id option[value="${packageId}"]`);

                const basePrice = parseFloat(selectedOption.dataset.b2c);
                const extraPrice = parseFloat(selectedOption.dataset.extra);

                // Total price calculation
                const total = (basePrice + (extraPersons * extraPrice)) * nights;
                document.getElementById("totalPrice").value = `₹${total.toFixed(2)}`;
            } else {
                document.getElementById("totalPrice").value = 'Invalid dates or package selection';
            }
        }
    </script>
</body>

</html>