<?php require './config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hostel Bed Booking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap & Flatpickr CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_green.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%);
            min-height: 100vh;
        }
        .form-card {
            border-radius: 1.5rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            max-width: 500px;
            margin: 2rem auto;
        }
        .bed-box {
            width: 80px;
            height: 80px;
            background: #dff0d8;
            border: 2px solid #4caf50;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            user-select: none;
            
            font-weight: bold;
            margin: 5px;
        }
        .bed-box.selected {
            background: #4caf50;
            color: white;
        }
        .bed-box.disabled {
            background: #eee;
            color: #aaa;
            border: 2px dashed #ccc;
            cursor: not-allowed;
            opacity: 0.6;
        }
        @media (max-width: 576px) {
            .form-card { padding: 1rem !important; }
            .bed-box { width: 60px; height: 60px; font-size: 0.9rem; }
        }
    </style>
</head>
<body>
<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="form-card bg-white p-4 p-md-5 w-100">
        <h2 class="fw-bold mb-3 text-center">Book a Hostel Bed</h2>
        <form id="bookingForm" action="Hostel_process_booking.php" method="post" autocomplete="off">
            <div class="mb-3">
                <label class="form-label">Guest Name *</label>
                <input type="text" name="guest_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone Number *</label>
                <input type="text" name="phone_number" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control">
            </div>
            <div class="row mb-3">
                <div class="col-6">
                    <label class="form-label">Check-in Date *</label>
                    <input type="text" id="check_in_date" name="check_in_date" class="form-control" required autocomplete="off">
                </div>
                <div class="col-6">
                    <label class="form-label">Check-out Date *</label>
                    <input type="text" id="check_out_date" name="check_out_date" class="form-control" required autocomplete="off">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Select Room *</label>
                <select name="room_id" id="room_select" class="form-select" required>
                    <option value="">Select Room</option>
                    <?php
                    $stmt = $pdo->query("SELECT id, room_number FROM hostel_rooms");
                    while ($row = $stmt->fetch()) {
                        echo "<option value='{$row['id']}'>Room {$row['room_number']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Select Beds *</label>
                <div id="bed_container" class="d-flex flex-wrap"></div>
                <div id="selected_beds_inputs"></div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Select Extra Features (for admin reference):</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="features[]" value="breakfast" id="breakfast" data-label="Breakfast" data-price="100">
                    <label class="form-check-label" for="breakfast">
                        Breakfast (₹100)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="features[]" value="veg_lunch_dinner" id="veg_lunch_dinner" data-label="Veg Lunch/Dinner" data-price="100">
                    <label class="form-check-label" for="veg_lunch_dinner">
                        Veg Lunch/Dinner (₹100)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="features[]" value="nonveg_lunch_dinner" id="nonveg_lunch_dinner" data-label="Non-Veg Lunch/Dinner" data-price="250">
                    <label class="form-check-label" for="nonveg_lunch_dinner">
                        Non-Veg Lunch/Dinner (₹250)
                    </label>
                </div>
                <div id="featuresInfo" class="alert alert-info mt-2 d-none"></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Total Price:</label>
                <input type="text" id="total_price" class="form-control" readonly>
            </div>
            <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">Book & Pay</button>
        </form>
    </div>
</div>

<!-- Flatpickr & Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
let selectedBeds = [];
let availableBeds = [];

const checkIn = flatpickr("#check_in_date", {
    dateFormat: "Y-m-d",
    minDate: "today",
    onChange: function(selectedDates, dateStr) {
        checkOut.set("minDate", dateStr);
        clearBedsAndPrice();
        fetchAvailableRooms();
    }
});
const checkOut = flatpickr("#check_out_date", {
    dateFormat: "Y-m-d",
    minDate: "today",
    onChange: function() {
        clearBedsAndPrice();
        fetchAvailableRooms();
    }
});

function fetchAvailableRooms() {
    const checkInDate = document.getElementById("check_in_date").value;
    const checkOutDate = document.getElementById("check_out_date").value;
    const roomSelect = document.getElementById("room_select");

    if (!checkInDate || !checkOutDate) {
        clearBedsAndPrice();
        return;
    }

    fetch(`get_available_rooms.php?check_in=${checkInDate}&check_out=${checkOutDate}`)
        .then(res => res.json())
        .then(rooms => {
            roomSelect.innerHTML = `<option value="">Select Room</option>`;
            rooms.forEach(room => {
                const opt = document.createElement("option");
                opt.value = room.id;
                opt.textContent = `Room ${room.room_number}`;
                roomSelect.appendChild(opt);
            });
            clearBedsAndPrice();
        });
}

function clearBedsAndPrice() {
    document.getElementById("bed_container").innerHTML = "";
    selectedBeds = [];
    availableBeds = [];
    document.getElementById("selected_beds_inputs").innerHTML = "";
    document.getElementById("total_price").value = "";
}

document.getElementById("room_select").addEventListener("change", function () {
    loadBeds();
});

function loadBeds() {
    const roomId = document.getElementById("room_select").value;
    const bedContainer = document.getElementById("bed_container");
    bedContainer.innerHTML = "";
    selectedBeds = [];
    availableBeds = [];
    document.getElementById("selected_beds_inputs").innerHTML = "";
    document.getElementById("total_price").value = "";

    const checkInDate = document.getElementById("check_in_date").value;
    const checkOutDate = document.getElementById("check_out_date").value;

    if (!roomId || !checkInDate || !checkOutDate) {
        return;
    }

    fetch(`get_beds_by_room.php?room_id=${roomId}&check_in=${checkInDate}&check_out=${checkOutDate}`)
        .then(res => res.json())
        .then(beds => {
            if (beds.length === 0) {
                bedContainer.innerHTML = "<p>No available beds for selected dates.</p>";
                return;
            }
            availableBeds = beds.map(bed => bed.id.toString());
            beds.forEach(bed => {
                const bedBox = document.createElement("div");
                bedBox.classList.add("bed-box");
                bedBox.textContent = "Bed " + bed.bed_number;
                bedBox.dataset.bedId = bed.id;

                bedBox.addEventListener("click", () => {
                    if (bedBox.classList.contains("disabled")) return;

                    bedBox.classList.toggle("selected");
                    const bedId = bed.id.toString();

                    if (selectedBeds.includes(bedId)) {
                        selectedBeds = selectedBeds.filter(id => id !== bedId);
                    } else {
                        selectedBeds.push(bedId);
                    }

                    updateSelectedBedsInputs();
                    fetchBedPricesAndUpdateTotal();
                });

                bedContainer.appendChild(bedBox);
            });
        })
        .catch(err => {
            bedContainer.innerHTML = "<p>Error fetching beds. Try again later.</p>";
            console.error(err);
        });
}

// Create multiple hidden inputs for PHP array input
function updateSelectedBedsInputs() {
    const container = document.getElementById("selected_beds_inputs");
    container.innerHTML = "";
    selectedBeds.forEach(id => {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "bed_ids[]";
        input.value = id;
        container.appendChild(input);
    });
}

function calculateTotalPrice() {
    if (selectedBeds.length === 0) {
        document.getElementById("total_price").value = "";
        return;
    }

    const checkInDate = new Date(document.getElementById("check_in_date").value);
    const checkOutDate = new Date(document.getElementById("check_out_date").value);

    if (isNaN(checkInDate) || isNaN(checkOutDate)) {
        document.getElementById("total_price").value = "";
        return;
    }

    const days = (checkOutDate - checkInDate) / (1000 * 60 * 60 * 24);
    if (days <= 0) {
        document.getElementById("total_price").value = "";
        return;
    }

    fetch("get_multiple_beds_price.php?bed_ids=" + selectedBeds.join(","))
        .then(res => res.json())
        .then(data => {
            let total = data.total_price * days;
            document.getElementById("total_price").value = total.toFixed(2) + " ₹";
        });
}

function fetchBedPricesAndUpdateTotal() {
    calculateTotalPrice();
}

// Razorpay integration
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const name = form.querySelector('[name="guest_name"]').value;
    const email = form.querySelector('[name="email"]').value || "guest@example.com";
    const phone = form.querySelector('[name="phone_number"]').value;
    const totalPrice = parseFloat(document.getElementById('total_price').value) || 0;

    if (selectedBeds.length === 0) {
        alert("Please select at least one bed.");
        return;
    }

    if (isNaN(totalPrice) || totalPrice <= 0) {
        alert("Total price must be greater than 0.");
        return;
    }

    const options = {
        key: "rzp_test_Z9EJkJjfUBwbUn", // Replace with your Razorpay Key ID
        amount: totalPrice * 100,
        currency: "INR",
        name: "Hostel Booking",
        description: "Booking Payment",
        handler: function (response) {
            // Optionally, you can add the payment ID to the form here
            // let paymentInput = document.createElement('input');
            // paymentInput.type = 'hidden';
            // paymentInput.name = 'razorpay_payment_id';
            // paymentInput.value = response.razorpay_payment_id;
            // form.appendChild(paymentInput);
            form.submit();
        },
        prefill: {
            name: name,
            email: email,
            contact: phone
        },
        theme: {
            color: "#28a745"
        }
    };

    const rzp = new Razorpay(options);
    rzp.open();
});

// Show info about selected features and their prices
const featureCheckboxes = document.querySelectorAll('input[name="features[]"]');
const featuresInfo = document.getElementById('featuresInfo');

featureCheckboxes.forEach(cb => {
    cb.addEventListener('change', () => {
        const selected = Array.from(featureCheckboxes)
            .filter(c => c.checked)
            .map(c => `${c.dataset.label} (₹${c.dataset.price})`);
        if (selected.length > 0) {
            featuresInfo.innerHTML = `You have to pay for these at the time of checkout:<br><strong>${selected.join(', ')}</strong>`;
            featuresInfo.classList.remove('d-none');
        } else {
            featuresInfo.innerHTML = '';
            featuresInfo.classList.add('d-none');
        }
    });
});
</script>
</body>
</html>
