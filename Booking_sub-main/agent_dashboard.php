<?php
require '../config/db.php';
include './includes/navbar.php';

session_start();

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'agent') {
    header('Location: agent_login.php');
    exit();
}

// Fetch agent data (logged-in user)
$agent_id = $_SESSION['id'];

// Fetch guests added by the agent
$guests = $pdo->prepare("SELECT id, name, email, phone FROM guests WHERE user_id = ?");
$guests->execute([$agent_id]);
$guests = $guests->fetchAll();

// Fetch bookings made by the agent
$bookings = $pdo->prepare("SELECT b.id, g.name AS guest_name, r.name AS room_name, b.check_in_date, b.check_out_date, b.total_price, b.status 
                           FROM bookings b 
                           JOIN guests g ON b.guest_id = g.id 
                           JOIN rooms r ON b.room_id = r.id 
                           WHERE b.user_id = ?");
$bookings->execute([$agent_id]);
$bookings = $bookings->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
/* From Uiverse.io by Madflows */ 
.button {
  position: relative;
  overflow: hidden;
  height: 2rem;
  padding: 0 2rem;
  border-radius: 1.5rem;
  background: #3d3a4e;
  background-size: 400%;
  color: #fff;
  border: none;
  cursor: pointer;
}

.button:hover::before {
  transform: scaleX(1);
}

.button-content {
  position: relative;
  z-index: 1;
}

.button::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  transform: scaleX(0);
  transform-origin: 0 50%;
  width: 100%;
  height: inherit;
  border-radius: inherit;
  background: linear-gradient(
    82.3deg,
    rgba(150, 93, 233, 1) 10.8%,
    rgba(99, 88, 238, 1) 94.3%
  );
  transition: all 0.475s;
}
/* From Uiverse.io by adamgiebl */ 
.cssbuttons-io-button {
  display: flex;
  align-items: center;
  font-family: inherit;
  cursor: pointer;
  font-weight: 500;
  font-size: 16px;
  padding: 0.7em 1.4em 0.7em 1.1em;
  color: white;
  background: #ad5389;
  background: linear-gradient(
    0deg,
    rgba(20, 167, 62, 1) 0%,
    rgba(102, 247, 113, 1) 100%
  );
  border: none;
  box-shadow: 0 0.7em 1.5em -0.5em #14a73e98;
  letter-spacing: 0.05em;
  border-radius: 20em;
}

.cssbuttons-io-button svg {
  margin-right: 6px;
}

.cssbuttons-io-button:hover {
  box-shadow: 0 0.5em 1.5em -0.5em #14a73e98;
}

.cssbuttons-io-button:active {
  box-shadow: 0 0.3em 1em -0.5em #14a73e98;
}
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>Welcome, Agent Dashboard</h2>

        <!-- Guests Section -->
        <h4 class="mt-4">Guests</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($guests as $guest): ?>
                    <tr>
                        <td><?= htmlspecialchars($guest['name']) ?></td>
                        <td><?= htmlspecialchars($guest['email']) ?></td>
                        <td><?= htmlspecialchars($guest['phone']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

<!-- Bookings Section -->
<h4 class="mt-4">Your Bookings</h4>
<table class="table">
    <thead>
        <tr>
            <th>Guest</th>
            <th>Room</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Total Price</th>
            <th>Status</th>
            <th>Action</th> <!-- New column -->
        </tr>
    </thead>
    <tbody>
        <?php foreach ($bookings as $booking): ?>
            <tr>
                <td><?= htmlspecialchars($booking['guest_name']) ?></td>
                <td><?= htmlspecialchars($booking['room_name']) ?></td>
                <td><?= htmlspecialchars($booking['check_in_date']) ?></td>
                <td><?= htmlspecialchars($booking['check_out_date']) ?></td>
                <td><?= htmlspecialchars($booking['total_price']) ?></td>
                <td><?= htmlspecialchars($booking['status']) ?></td>
                <td>
                    <form action="generate_invoice.php" method="get" style="display:inline;">
                        <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                        <button class="button">
                            <span class="button-content">Download</span>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


        <!-- Add Guest Button -->
<button onclick="window.location.href='agent_add_guest.php'"  class="cssbuttons-io-button">
  <svg
    height="24"
    width="24"
    viewBox="0 0 24 24"
    xmlns="http://www.w3.org/2000/svg"
  >
    <path d="M0 0h24v24H0z" fill="none"></path>
    <path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2z" fill="currentColor"></path>
  </svg>
  <span>Add</span>
</button>
        <a href="agent_properties.php" class="btn btn-success mt-3">Select Properties for your Guest</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
