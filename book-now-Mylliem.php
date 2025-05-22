<!DOCTYPE html>
<html>
<head>
    <title>Hostel Bed Booking</title>
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_green.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }

        form {
            background: white;
            padding: 30px;
            max-width: 500px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        label {
            display: block;
            margin-top: 15px;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background: green;
            color: white;
            border: none;
            margin-top: 20px;
            cursor: pointer;
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
        }
        .bed-box.selected {
            background: #4caf50;
            color: white;
        }
    </style>
</head>
<body>

<form action="Hostel_process_booking.php" method="post">
    <h2>Book a Bed</h2>

    <label>Guest Name:</label>
    <input type="text" name="guest_name" required>

    <label>Phone Number:</label>
    <input type="text" name="phone_number" required>

    <label>Email:</label>
    <input type="email" name="email">

    <label>Check-in Date:</label>
    <input type="text" id="check_in_date" name="check_in_date" required>

    <label>Check-out Date:</label>
    <input type="text" id="check_out_date" name="check_out_date" required>

    <label>Select Room:</label>
    <select name="room_id" id="room_select" required>
        <option value="">Select Room</option>
        <?php
        require './config/db.php';
        $stmt = $pdo->query("SELECT id, room_number FROM hostel_rooms");
        while ($row = $stmt->fetch()) {
            echo "<option value='{$row['id']}'>Room {$row['room_number']}</option>";
        }
        ?>
    </select>

    <label>Select Beds:</label>
    <div id="bed_container" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>

    <!-- Instead of one hidden input, dynamically add multiple hidden inputs -->
    <div id="selected_beds_inputs"></div>

    <label>Total Price:</label>
    <input type="text" id="total_price" readonly>

    <button type="submit">Book Now</button>
</form>

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    let selectedBeds = [];

    let checkIn = flatpickr("#check_in_date", {
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
        minDate: "today",
        onChange: function (selectedDates, dateStr) {
            checkOut.set("minDate", dateStr);
            fetchAvailableRooms();
            fetchBedPricesAndUpdateTotal(); // update price on date change
        }
    });

    let checkOut = flatpickr("#check_out_date", {
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
        minDate: "today",
        onChange: function () {
            fetchAvailableRooms();
            fetchBedPricesAndUpdateTotal(); // update price on date change
        }
    });

    function fetchAvailableRooms() {
        const checkInDate = document.getElementById("check_in_date").value;
        const checkOutDate = document.getElementById("check_out_date").value;
        const roomSelect = document.getElementById("room_select");

        if (!checkInDate || !checkOutDate) return;

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

                // Clear beds when room list is reloaded
                clearBedsAndPrice();
            });
    }

    function clearBedsAndPrice() {
        document.getElementById("bed_container").innerHTML = "";
        selectedBeds = [];
        document.getElementById("selected_beds_inputs").innerHTML = "";
        document.getElementById("total_price").value = "";
    }

    document.getElementById("room_select").addEventListener("change", function () {
        const roomId = this.value;
        const bedContainer = document.getElementById("bed_container");
        bedContainer.innerHTML = "";
        selectedBeds = [];
        document.getElementById("selected_beds_inputs").innerHTML = "";
        document.getElementById("total_price").value = "";

        if (!roomId) return;

        const checkInDate = document.getElementById("check_in_date").value;
        const checkOutDate = document.getElementById("check_out_date").value;

        if (!checkInDate || !checkOutDate) {
            alert("Please select check-in and check-out dates first.");
            return;
        }

        fetch(`get_beds_by_room.php?room_id=${roomId}&check_in=${checkInDate}&check_out=${checkOutDate}`)
            .then(res => res.json())
            .then(beds => {
                if (beds.length === 0) {
                    bedContainer.innerHTML = "<p>No available beds for selected dates.</p>";
                    return;
                }
                beds.forEach(bed => {
                    const bedBox = document.createElement("div");
                    bedBox.classList.add("bed-box");
                    bedBox.textContent = "Bed " + bed.bed_number;
                    bedBox.dataset.bedId = bed.id;

                    bedBox.addEventListener("click", () => {
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
    });

    // Instead of one hidden input with comma-separated, create multiple hidden inputs for PHP array input
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
</script>

</body>
</html>
