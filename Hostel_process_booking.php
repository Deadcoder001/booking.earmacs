<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require './config/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $guest_name = trim($_POST['guest_name'] ?? '');
    $phone = trim($_POST['phone_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $check_in = trim($_POST['check_in_date'] ?? '');
    $check_out = trim($_POST['check_out_date'] ?? '');
    $bed_ids_raw = $_POST['bed_ids'] ?? [];

    // Validate required fields except email
    if (empty($guest_name) || empty($phone) || empty($check_in) || empty($check_out) || empty($bed_ids_raw)) {
        echo "❌ All fields except email are required.";
        exit;
    }

    // Make sure bed_ids is an array of integers
    if (!is_array($bed_ids_raw)) {
        $bed_ids_raw = explode(',', $bed_ids_raw);
    }
    $bed_ids = array_map('intval', $bed_ids_raw);

    if (empty($bed_ids)) {
        echo "❌ All fields except email are required.";
        exit;
    }

    // Validate dates format (YYYY-MM-DD)
    $date_pattern = '/^\d{4}-\d{2}-\d{2}$/';
    if (!preg_match($date_pattern, $check_in) || !preg_match($date_pattern, $check_out)) {
        echo "❌ Dates must be in YYYY-MM-DD format.";
        exit;
    }

    // Check that check_out is after check_in
    if (strtotime($check_in) >= strtotime($check_out)) {
        echo "❌ Check-out date must be after check-in date.";
        exit;
    }

    // Proceed with booking...
    $errors = [];

    try {
        $pdo->beginTransaction();

        foreach ($bed_ids as $bed_id) {
            if ($bed_id <= 0) {
                $errors[] = "❌ Invalid bed ID: $bed_id";
                continue;
            }

            // Check for overlapping bookings
            $stmt = $pdo->prepare("
                SELECT 1 FROM hostel_booking 
                WHERE bed_id = :bed_id 
                AND status = 'Booked'
                AND (check_in_date <= :check_out AND check_out_date >= :check_in)
                LIMIT 1
            ");
            $stmt->execute([
                ':bed_id' => $bed_id,
                ':check_in' => $check_in,
                ':check_out' => $check_out
            ]);

            if ($stmt->fetch()) {
                $errors[] = "❌ Bed ID $bed_id is already booked for the selected dates.";
                continue;
            }

            // Insert booking
            $insert = $pdo->prepare("
                INSERT INTO hostel_booking 
                    (bed_id, guest_name, phone_number, email, check_in_date, check_out_date, status, created_at) 
                VALUES 
                    (:bed_id, :guest_name, :phone, :email, :check_in, :check_out, 'Booked', NOW())
            ");
            $insert->execute([
                ':bed_id' => $bed_id,
                ':guest_name' => $guest_name,
                ':phone' => $phone,
                ':email' => $email,
                ':check_in' => $check_in,
                ':check_out' => $check_out
            ]);
        }

        if (!empty($errors)) {
            $pdo->rollBack();
            echo implode("<br>", $errors);
        } else {
            $pdo->commit();
            echo "✅ Booking successful for all selected beds.";
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "❌ Booking failed: " . $e->getMessage();
    }
} else {
    echo "❌ Invalid request.";
}
?>
