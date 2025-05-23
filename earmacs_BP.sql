-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 22, 2025 at 12:36 AM
-- Server version: 10.6.21-MariaDB-cll-lve
-- PHP Version: 8.3.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `earmacs_BP`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `guest_id` bigint(20) DEFAULT NULL,
  `room_id` bigint(20) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `discounted_price` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `guest_id`, `room_id`, `check_in_date`, `check_out_date`, `total_price`, `discounted_price`, `status`, `created_at`) VALUES
(1, NULL, 1, 3, '2025-05-12', '2025-05-15', 9000.00, NULL, 'confirmed', '2025-05-12 08:05:44'),
(2, NULL, 2, 29, '2025-05-13', '2025-05-14', 0.00, NULL, 'pending', '2025-05-12 10:04:02'),
(3, NULL, 3, 4, '2025-05-12', '2025-05-13', 0.00, NULL, 'pending', '2025-05-12 10:51:27'),
(4, NULL, 4, 21, '2025-05-14', '2025-05-16', 0.00, NULL, 'pending', '2025-05-12 14:21:18'),
(5, NULL, 5, 22, '2025-05-15', '2025-05-24', 0.00, NULL, 'pending', '2025-05-13 03:01:56'),
(6, NULL, 6, 21, '2025-05-22', '2025-05-30', 0.00, NULL, 'pending', '2025-05-13 08:01:44'),
(7, NULL, 7, 3, '2025-05-20', '2025-05-22', 0.00, NULL, 'pending', '2025-05-13 08:28:08'),
(8, NULL, 8, 4, '2025-05-13', '2025-05-15', 0.00, NULL, 'pending', '2025-05-13 09:45:33'),
(11, 4, 10, 5, '2025-05-14', '2025-05-15', 3000.00, 2700.00, 'pending', '2025-05-13 19:30:42'),
(12, NULL, 11, 22, '2025-05-14', '2025-05-15', 0.00, NULL, 'pending', '2025-05-14 05:40:36'),
(13, 5, 12, 6, '2025-05-14', '2025-05-15', 3000.00, 2700.00, 'pending', '2025-05-14 06:28:50'),
(14, 6, 13, 7, '2025-05-14', '2025-05-16', 6000.00, 5400.00, 'pending', '2025-05-14 07:12:41'),
(15, NULL, 14, 23, '2025-05-19', '2025-05-27', 0.00, NULL, 'pending', '2025-05-16 05:32:44'),
(16, NULL, NULL, 3, '2025-05-16', '2025-05-17', 500.00, NULL, 'pending', '2025-05-16 10:13:04'),
(17, NULL, NULL, 3, '2025-05-16', '2025-05-17', 500.00, NULL, 'pending', '2025-05-16 10:13:04'),
(18, NULL, 15, 24, '2025-05-21', '2025-05-24', 0.00, NULL, 'pending', '2025-05-17 06:18:40'),
(23, NULL, 27, 21, '2025-05-19', '2025-05-22', 0.00, NULL, 'pending', '2025-05-17 16:57:16'),
(24, NULL, 28, 25, '2025-05-21', '2025-05-29', 0.00, NULL, 'pending', '2025-05-19 06:36:48'),
(25, NULL, 29, 26, '2025-05-23', '2025-05-26', 0.00, NULL, 'pending', '2025-05-22 06:07:51');

-- --------------------------------------------------------

--
-- Table structure for table `booking_features`
--

CREATE TABLE `booking_features` (
  `id` bigint(20) NOT NULL,
  `booking_id` bigint(20) NOT NULL,
  `feature_id` bigint(20) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `features`
--

CREATE TABLE `features` (
  `id` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `default_price` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `features`
--

INSERT INTO `features` (`id`, `name`, `description`, `default_price`) VALUES
(1, 'Extra bed', NULL, 500.00);

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `id` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guests`
--

INSERT INTO `guests` (`id`, `name`, `email`, `phone`, `created_at`, `user_id`) VALUES
(1, 'Gitartha neog', 'gitarthajr@gmail.com', '09365354993', '2025-05-12 08:05:44', 0),
(2, 'Gitartha neog', '', '09365354993', '2025-05-12 10:04:02', 0),
(3, 'Gitartha neog', '', '09365354993', '2025-05-12 10:51:27', 0),
(4, 'Beccagrace Rynjah', '', '8731946119', '2025-05-12 14:21:18', 0),
(5, 'Beccagrace Rynjah', '', '8731946119', '2025-05-13 03:01:56', 0),
(6, 'Beccagrace Rynjah', '', '8731046119', '2025-05-13 08:01:44', 0),
(7, 'Beccagrace Rynjah', '', '8731046119', '2025-05-13 08:28:08', 0),
(8, 'Gitartha neog', 'gitarthajr@gmail.com', '09365354993', '2025-05-13 09:45:33', 0),
(10, 'RESHAB GUPTA', 'reshabgupta100@gmail.com', '09706369565', '2025-05-13 19:07:49', 4),
(11, 'Gitartha neog', 'gitarthajr@gmail.com', '09365354993', '2025-05-14 05:40:36', 0),
(12, 'abcd', 'ggg@gmail.com', '1346789422', '2025-05-14 06:28:37', 5),
(13, 'demo guest', 'demo12@gmail.com', '12322111212', '2025-05-14 07:11:28', 6),
(14, 'becca', '', '8731046119', '2025-05-16 05:32:44', 0),
(15, 'becca', '', '8731046119', '2025-05-17 06:18:40', 0),
(16, 'Ashif', '', '123456789', '2025-05-17 06:43:12', 0),
(17, 'Ashif', '', '123456789', '2025-05-17 06:44:34', 0),
(18, 'Ashif', '', '123456789', '2025-05-17 06:47:47', 0),
(19, 'Ashif', '', '123456789', '2025-05-17 06:52:46', 0),
(20, 'Ashif', '', '123456789', '2025-05-17 06:52:56', 0),
(21, 'Ashif', '', '123456789', '2025-05-17 06:55:52', 0),
(22, 'Ashif', '', '123456789', '2025-05-17 06:55:54', 0),
(23, 'Ashif', '', '123456789', '2025-05-17 07:04:03', 0),
(24, 'Ashif', '', '123456789', '2025-05-17 07:04:49', 0),
(25, 'Ashif', '', '123456789', '2025-05-17 07:16:09', 0),
(26, 'Ashif', '', '123456789', '2025-05-17 07:16:35', 0),
(27, 'Beccagrace Rynjah', '', '8731946119', '2025-05-17 16:57:16', 0),
(28, 'becca', '', '8731046119', '2025-05-19 06:36:48', 0),
(29, 'becca', '', '8731046119', '2025-05-22 06:07:51', 0);

-- --------------------------------------------------------

--
-- Table structure for table `hostel_beds`
--

CREATE TABLE `hostel_beds` (
  `id` int(11) NOT NULL,
  `room_id` bigint(20) NOT NULL,
  `bed_number` varchar(20) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `hostel_beds`
--

INSERT INTO `hostel_beds` (`id`, `room_id`, `bed_number`, `price`) VALUES
(1, 1, '01', 500.00),
(2, 1, '02', 500.00),
(3, 2, '01', 500.00);

-- --------------------------------------------------------

--
-- Table structure for table `hostel_booking`
--

CREATE TABLE `hostel_booking` (
  `id` bigint(20) NOT NULL,
  `bed_id` int(11) NOT NULL,
  `guest_name` varchar(100) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `status` varchar(20) DEFAULT 'Booked',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `hostel_booking`
--

INSERT INTO `hostel_booking` (`id`, `bed_id`, `guest_name`, `phone_number`, `email`, `check_in_date`, `check_out_date`, `status`, `created_at`) VALUES
(2, 1, 'demo', '1323123123', 'demo@gmail.com', '2025-05-17', '2025-05-19', 'Booked', '2025-05-17 08:06:33'),
(5, 1, 'Raju', '936513529', 'raju@gmail.com', '2025-05-20', '2025-05-21', 'Booked', '2025-05-18 16:24:11'),
(6, 2, 'babu', '098765456', 'demo@gmail.com', '2025-05-18', '2025-05-19', 'Booked', '2025-05-18 16:27:45');

-- --------------------------------------------------------

--
-- Table structure for table `hostel_rooms`
--

CREATE TABLE `hostel_rooms` (
  `id` bigint(20) NOT NULL,
  `room_number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `hostel_rooms`
--

INSERT INTO `hostel_rooms` (`id`, `room_number`) VALUES
(1, 'H01'),
(2, 'H02'),
(3, 'H03');

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` int(11) NOT NULL,
  `property_id` bigint(20) NOT NULL,
  `occupancy_type` enum('Single','Double','Extra Person') NOT NULL,
  `package_type` enum('CP','MAP') NOT NULL,
  `b2c_rate` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `extra_person_rate` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `property_id`, `occupancy_type`, `package_type`, `b2c_rate`, `created_at`, `extra_person_rate`) VALUES
(3, 3, 'Single', 'MAP', 2530.00, '2025-05-12 09:06:29', 1800.00),
(4, 5, 'Single', 'CP', 2200.00, '2025-05-12 10:48:50', 1200.00),
(5, 5, 'Double', 'CP', 3000.00, '2025-05-12 10:49:18', 1200.00),
(6, 5, 'Single', 'MAP', 2530.00, '2025-05-12 10:49:46', 1800.00),
(7, 5, 'Double', 'MAP', 3620.00, '2025-05-12 10:50:15', 1800.00),
(8, 4, 'Single', 'CP', 2500.00, '2025-05-12 11:15:25', 1200.00),
(9, 4, 'Double', 'CP', 3500.00, '2025-05-12 11:16:06', 0.00),
(10, 4, 'Single', 'MAP', 3190.00, '2025-05-12 11:16:31', 0.00),
(11, 4, 'Double', 'MAP', 4290.00, '2025-05-12 11:16:54', 0.00),
(12, 3, 'Single', 'CP', 2500.00, '2025-05-12 11:18:49', 1200.00),
(13, 3, 'Double', 'CP', 3500.00, '2025-05-12 11:19:06', 1200.00),
(14, 3, 'Double', 'MAP', 4290.00, '2025-05-12 11:19:30', 1800.00);

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` bigint(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `name`, `location`, `description`, `created_at`) VALUES
(3, 'Langkawet', 'Langkawet, pynursla block, East Khasi Hills ', 'Langkawet offers a peaceful atmosphere with\r\nprivate accomodation under the local\r\ntraditional cottages nestled in Pynursla block,\r\nEast Khasi Hills District', '2025-05-12 07:17:18'),
(4, 'Laitumiong', 'Laitumiong Village, East Khasi Hills, Meghalaya. 5km from Mawkdok Dympep Valley', 'Jet off to your dream\r\ndestination with our travel\r\npromotion. Exclusive deals\r\nfor the ultimate getaway', '2025-05-12 07:20:12'),
(5, 'kyiem', 'Kyiem Village, Mawphlang', 'A house with a modern design\r\nthat is perfect for you and your\r\nfamily, now comes at an\r\naffordable price.', '2025-05-12 07:21:07'),
(6, 'Mylliem', 'Mylliem Village', 'Situated in a quiet off-road nook in Mylliem, the Mylliem Zostel under EARMACS offers a tranquil and safe retreat for travelers. This Zostel system caters to men, women, and also features a unisex section, making it ideal for families and friends wishing to stay together.', '2025-05-16 06:43:47');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` bigint(20) NOT NULL,
  `property_id` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('available','booked','maintenance') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `room_type` varchar(50) NOT NULL DEFAULT 'single_bed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `property_id`, `name`, `base_price`, `description`, `status`, `created_at`, `room_type`) VALUES
(3, 5, '01', 3000.00, 'A house with a modern design\r\nthat is perfect for you and your\r\nfamily, now comes at an\r\naffordable price.', 'available', '2025-05-12 07:22:45', 'Double'),
(4, 5, '02', 3000.00, 'A house with a modern design\r\nthat is perfect for you and your\r\nfamily, now comes at an\r\naffordable price.', 'available', '2025-05-12 07:23:11', 'Double'),
(5, 5, '03', 3000.00, 'A house with a modern design\r\nthat is perfect for you and your\r\nfamily, now comes at an\r\naffordable price.', 'available', '2025-05-12 07:23:23', 'Double'),
(6, 5, '04', 3000.00, 'A house with a modern design\r\nthat is perfect for you and your\r\nfamily, now comes at an\r\naffordable price.', 'available', '2025-05-12 07:23:42', 'Double'),
(7, 5, '05', 3000.00, 'A house with a modern design\r\nthat is perfect for you and your\r\nfamily, now comes at an\r\naffordable price.', 'available', '2025-05-12 07:23:54', 'Double'),
(21, 4, '01', 3500.00, 'Laitumiong Village, East Khasi\r\nHills, Meghalaya.\r\n5km from Mawkdok Dympep\r\nValley', 'available', '2025-05-12 08:45:55', 'Double'),
(22, 4, '02', 3500.00, 'Laitumiong Village, East Khasi\r\nHills, Meghalaya.\r\n5km from Mawkdok Dympep\r\nValley', 'available', '2025-05-12 08:46:11', 'Double'),
(23, 4, '03', 3500.00, 'Laitumiong Village, East Khasi\r\nHills, Meghalaya.\r\n5km from Mawkdok Dympep\r\nValley', 'available', '2025-05-12 08:46:28', 'Double'),
(24, 4, '04', 3500.00, 'Laitumiong Village, East Khasi\r\nHills, Meghalaya.\r\n5km from Mawkdok Dympep\r\nValley', 'available', '2025-05-12 08:46:42', 'Double'),
(25, 4, '05', 3500.00, 'Laitumiong Village, East Khasi\r\nHills, Meghalaya.\r\n5km from Mawkdok Dympep\r\nValley', 'available', '2025-05-12 08:46:54', 'Double'),
(26, 4, '06', 3500.00, 'Laitumiong Village, East Khasi\r\nHills, Meghalaya.\r\n5km from Mawkdok Dympep\r\nValley', 'available', '2025-05-12 08:47:21', 'Double'),
(27, 4, '07', 3500.00, 'Laitumiong Village, East Khasi\r\nHills, Meghalaya.\r\n5km from Mawkdok Dympep\r\nValley', 'available', '2025-05-12 08:47:34', 'Double'),
(28, 4, '08', 3500.00, 'Laitumiong Village, East Khasi\r\nHills, Meghalaya.\r\n5km from Mawkdok Dympep\r\nValley', 'available', '2025-05-12 08:47:49', 'Double'),
(29, 3, '01', 3500.00, 'Langkawet offers a peaceful atmosphere with\r\nprivate accomodation under the local\r\ntraditional cottages nestled in Pynursla block,\r\nEast Khasi Hills District', 'available', '2025-05-12 08:48:12', 'Double'),
(30, 3, '02', 3500.00, 'Langkawet offers a peaceful atmosphere with\r\nprivate accomodation under the local\r\ntraditional cottages nestled in Pynursla block,\r\nEast Khasi Hills District', 'available', '2025-05-12 08:48:26', 'Double'),
(31, 3, '03', 3500.00, 'Langkawet offers a peaceful atmosphere with\r\nprivate accomodation under the local\r\ntraditional cottages nestled in Pynursla block,\r\nEast Khasi Hills District', 'available', '2025-05-12 08:48:48', 'Double'),
(32, 3, '04', 3500.00, 'Langkawet offers a peaceful atmosphere with\r\nprivate accomodation under the local\r\ntraditional cottages nestled in Pynursla block,\r\nEast Khasi Hills District', 'available', '2025-05-12 08:49:02', 'Double'),
(33, 3, '05', 3500.00, 'Langkawet offers a peaceful atmosphere with\r\nprivate accomodation under the local\r\ntraditional cottages nestled in Pynursla block,\r\nEast Khasi Hills District', 'available', '2025-05-12 08:49:17', 'Double');

-- --------------------------------------------------------

--
-- Table structure for table `room_features`
--

CREATE TABLE `room_features` (
  `id` bigint(20) NOT NULL,
  `room_id` bigint(20) NOT NULL,
  `feature_id` bigint(20) NOT NULL,
  `extra_price` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','agent') DEFAULT 'agent',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password_hash`, `role`, `created_at`, `status`) VALUES
(3, 'admin', 'admin@gmail.com', NULL, '$2y$10$ZWWgdvhlzE3NeR/QVDIWvOiE8j20ruuCkZP0XnLZ43gTxwfzsPPya', 'admin', '2025-05-09 09:17:44', 'pending'),
(4, 'RESHAB GUPTA', 'reshabgupta100@gmail.com', '09706369565', '$2y$10$/LNpqlYA54MAPmQW1gtzh.qBrP3zEhGRY/NcTN6mDh82xk0wn7unu', 'agent', '2025-05-13 18:50:45', 'approved'),
(5, 'Gitartha neog', 'geetjr2@gmail.com', '09365354993', '$2y$10$NckPBta1kDVMDPX7X0LYMefRrBs.bfr9Eg8tEH6l0yzM2Berq3gf.', 'agent', '2025-05-14 06:26:59', 'approved'),
(6, 'demo', 'demo@gmail.com', '1234567890', '$2y$10$T5as/3QmGSSZZoct45REN..YjoyImMmxViQAblAkL8flMJkrIh7yu', 'agent', '2025-05-14 07:10:19', 'approved'),
(7, 'Angie Kohler', 'tess.eichmann91@ourtimesupport.com', '7893949572', '$2y$10$7uj/s.qMZ2R6lEONxkH2u.evFey2ybpdcMZ58a.KzJJTfHOsREV5S', 'agent', '2025-05-15 20:46:56', 'pending'),
(8, 'Hagen Sauerland', 'jolie61@ourtimesupport.com', '+447893949572', '$2y$10$ipQT7TnXnwAZFCs6uuuMZuoJnBNOmttZi3wGSoLxHxjbVQN5QIF3m', 'agent', '2025-05-15 20:49:32', 'pending');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `guest_id` (`guest_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `booking_features`
--
ALTER TABLE `booking_features`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `feature_id` (`feature_id`);

--
-- Indexes for table `features`
--
ALTER TABLE `features`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hostel_beds`
--
ALTER TABLE `hostel_beds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `hostel_booking`
--
ALTER TABLE `hostel_booking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bed_id` (`bed_id`);

--
-- Indexes for table `hostel_rooms`
--
ALTER TABLE `hostel_rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `room_features`
--
ALTER TABLE `room_features`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `feature_id` (`feature_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `booking_features`
--
ALTER TABLE `booking_features`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `features`
--
ALTER TABLE `features`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `hostel_beds`
--
ALTER TABLE `hostel_beds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hostel_booking`
--
ALTER TABLE `hostel_booking`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `hostel_rooms`
--
ALTER TABLE `hostel_rooms`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `room_features`
--
ALTER TABLE `room_features`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_features`
--
ALTER TABLE `booking_features`
  ADD CONSTRAINT `booking_features_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_features_ibfk_2` FOREIGN KEY (`feature_id`) REFERENCES `features` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hostel_beds`
--
ALTER TABLE `hostel_beds`
  ADD CONSTRAINT `hostel_beds_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `hostel_rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hostel_booking`
--
ALTER TABLE `hostel_booking`
  ADD CONSTRAINT `hostel_booking_ibfk_1` FOREIGN KEY (`bed_id`) REFERENCES `hostel_beds` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_features`
--
ALTER TABLE `room_features`
  ADD CONSTRAINT `room_features_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_features_ibfk_2` FOREIGN KEY (`feature_id`) REFERENCES `features` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
