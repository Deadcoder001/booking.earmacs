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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hotel Booking Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/icofont@1.0.0/dist/css/icofont.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .image-gallery {
            text-align: center;
        }

        .main-image img {
            width: 600px;
            height: 300px;
            object-fit: cover;
            margin-bottom: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .gallery {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .gallery-image {
            width: 143px;
            height: 100px;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .gallery-image:hover {
            transform: scale(1.1);
        }

        .form-container {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .main-image {
                max-height: 300px;
            }

            .container {
                flex-direction: column;
            }

            .form-container {
                margin-top: 20px;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
<header class="bg-gradient-to-r from-black via-gray-900 to-black shadow-lg shadow-gray-800/30">
  <div class="max-w-7xl mx-auto px-4 py-6 flex justify-between items-center">
    <a href="https://earmacs.com/" class="flex items-center space-x-2">
      <img src="./HTML/photos/Earmacs Tourism.jpg" alt="EARMACS Logo" class="h-20 w-auto">
    </a>
    <nav class="space-x-4">
      <a href="https://earmacs.com/" class="text-gray-300 hover:text-white transition duration-300 ease-in-out">Home</a>
      <a href="https://earmacs.com/contact-us-2" class="text-gray-300 hover:text-white transition duration-300 ease-in-out">Contact</a>
      <a href="#" class="text-gray-300 hover:text-white transition duration-300 ease-in-out">Login</a>
      <a href="#" class="text-gray-300 hover:text-white transition duration-300 ease-in-out">Register</a>
    </nav>
  </div>
</header>

<div class="container mx-auto p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Left Column -->
    <div class="image-gallery">
        <div class="main-image">
            <img id="mainImage" src="./includes/Langkawet/WhatsApp Image 2025-04-26 at 11.25.48 AM (1).jpeg" alt="Main Image">
        </div>
        <div class="gallery">
            <img class="gallery-image" src="./includes/Langkawet/WhatsApp Image 2025-04-26 at 11.25.48 AM (1).jpeg" alt="Image 1" onclick="changeImage('./includes/Langkawet/WhatsApp Image 2025-04-26 at 11.25.48 AM (1).jpeg')">
            <img class="gallery-image" src="./includes/Langkawet/IMG20241218184516 (2) (2).jpg" alt="Image 2" onclick="changeImage('./includes/Langkawet/IMG20241218184516 (2) (2).jpg')">
            <img class="gallery-image" src="./includes/Langkawet/IMG_0807_(1).jpg" alt="Image 3" onclick="changeImage('./includes/Langkawet/IMG_0807_(1).jpg')">
            <img class="gallery-image" src="./includes/Langkawet/IMG_1347.jpg" alt="Image 4" onclick="changeImage('./includes/Langkawet/IMG_1347.jpg')">
        </div>

        <div class="form-container mt-4">
            <h2 class="text-3xl font-bold mb-4">Langkawet</h2>
            <p class="mb-4 text-justify">
                Langkawet – The Retreat, located 48 km from Shillong in East Khasi Hills, offers comfortable private lodging amid a biodiverse landscape. With self-cooking options, local assistance, and clean spring water, it’s a peaceful getaway with essential amenities and a communal dining space.
            </p>
        </div>
   <section class="w-full bg-gray-100 py-6 px-4 sm:px-6 md:px-12">
  <div class="max-w-7xl mx-auto">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Property Amenities</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-lg shadow p-4 hover:shadow-md transition">
        <h3 class="text-base font-semibold text-gray-800 mb-1">5 Cottages</h3>
        <p class="text-sm text-gray-600">Eco-friendly, comfortable cottages for a cozy stay.</p>
      </div>

      <div class="bg-white rounded-lg shadow p-4 hover:shadow-md transition">
        <h3 class="text-base font-semibold text-gray-800 mb-1">Family Room</h3>
        <p class="text-sm text-gray-600">Spacious and comfortable room for the whole family.</p>
      </div>

      <div class="bg-white rounded-lg shadow p-4 hover:shadow-md transition">
        <h3 class="text-base font-semibold text-gray-800 mb-1">Dining Area</h3>
        <p class="text-sm text-gray-600">A dedicated space to enjoy meals together.</p>
      </div>

      <div class="bg-white rounded-lg shadow p-4 hover:shadow-md transition">
        <h3 class="text-base font-semibold text-gray-800 mb-1">Laundry Service</h3>
        <p class="text-sm text-gray-600">Convenient laundry facilities for all guests.</p>
      </div>

      <div class="bg-white rounded-lg shadow p-4 hover:shadow-md transition">
        <h3 class="text-base font-semibold text-gray-800 mb-1">Parking</h3>
        <p class="text-sm text-gray-600">Safe and convenient parking space available.</p>
      </div>

      <div class="bg-white rounded-lg shadow p-4 hover:shadow-md transition">
        <h3 class="text-base font-semibold text-gray-800 mb-1">Driver’s Room</h3>
        <p class="text-sm text-gray-600">Separate, comfortable space for drivers.</p>
      </div>
    </div>
  </div>
</section>

    </div>

    <!-- Right Column: Booking Form -->
    <div class="form-container bg-white p-6 rounded-lg shadow-md mb-0">
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
                <?= htmlspecialchars($pkg['package_type']) ?>, <?= htmlspecialchars($pkg['occupancy_type']) ?>
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
    function changeImage(imageSrc){
        document.getElementById("mainImage").src = imageSrc;
    }
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