<?php
require('libs/fpdf.php');
require('../config/db.php');
session_start();

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'agent') {
    die("Unauthorized access.");
}

// Booking ID must be passed as a GET parameter
$booking_id = $_GET['booking_id'] ?? null;

if (!$booking_id) {
    die("Booking ID missing.");
}

// Fetch booking details
$stmt = $pdo->prepare("
    SELECT b.id AS booking_id, b.check_in_date, b.check_out_date, b.total_price,
           g.name AS guest_name, g.email AS guest_email, g.phone AS guest_phone,
           r.id AS room_id, p.name AS property_name
    FROM bookings b
    JOIN guests g ON b.guest_id = g.id
    JOIN rooms r ON b.room_id = r.id
    JOIN properties p ON r.property_id = p.id
    WHERE b.id = ? AND b.user_id = ?
");
$stmt->execute([$booking_id, $_SESSION['id']]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    die("Booking not found or access denied.");
}

// Generate invoice with FPDF
$pdf = new FPDF();
$pdf->AddPage();

$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Booking Invoice',0,1,'C');

$pdf->SetFont('Arial','',12);
$pdf->Ln(10);

$pdf->Cell(100,10,'Invoice Number: '.$booking['booking_id'],0,1);
$pdf->Cell(100,10,'Guest Name: '.$booking['guest_name'],0,1);
$pdf->Cell(100,10,'Email: '.$booking['guest_email'],0,1);
$pdf->Cell(100,10,'Phone: '.$booking['guest_phone'],0,1);
$pdf->Ln(5);

$pdf->Cell(100,10,'Property: '.$booking['property_name'],0,1);
$pdf->Cell(100,10,'Room ID: '.$booking['room_id'],0,1);
$pdf->Cell(100,10,'Check-in: '.$booking['check_in_date'],0,1);
$pdf->Cell(100,10,'Check-out: '.$booking['check_out_date'],0,1);
$pdf->Ln(5);

$pdf->SetFont('Arial','B',14);
$pdf->Cell(100,10,'Total Price: ₹'.$booking['total_price'],0,1);

$pdf->Output('D', 'invoice_'.$booking['booking_id'].'.pdf');  // Force download
exit;
