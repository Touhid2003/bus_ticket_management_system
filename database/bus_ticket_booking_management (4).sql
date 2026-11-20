-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2026 at 05:46 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bus_ticket_booking_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `booking_date` datetime NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `booking_status` enum('pending','confirmed','cancelled') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `schedule_id`, `booking_date`, `total_amount`, `booking_status`) VALUES
(1, 4, 11, '2026-11-19 01:41:56', 600.00, 'pending'),
(2, 4, 11, '2026-11-19 01:52:34', 1200.00, 'pending'),
(3, 4, 11, '2026-11-19 01:57:07', 600.00, 'pending'),
(4, 4, 11, '2026-11-19 02:02:49', 600.00, 'pending'),
(5, 4, 12, '2026-11-19 02:03:35', 700.00, 'pending'),
(6, 4, 11, '2026-11-19 02:06:27', 600.00, 'confirmed'),
(7, 4, 11, '2026-11-19 02:11:43', 600.00, 'confirmed'),
(8, 4, 12, '2026-11-19 02:13:48', 700.00, 'confirmed'),
(9, 4, 11, '2026-11-19 02:31:13', 600.00, 'pending'),
(10, 4, 11, '2026-11-19 02:31:59', 600.00, 'pending'),
(11, 4, 11, '2026-11-19 02:36:11', 600.00, 'pending'),
(12, 4, 12, '2026-11-19 02:38:35', 700.00, 'pending'),
(13, 4, 12, '2026-11-19 02:43:25', 700.00, 'confirmed'),
(14, 4, 11, '2026-11-19 10:52:31', 600.00, 'confirmed'),
(15, 4, 11, '2026-11-19 11:34:14', 600.00, 'confirmed'),
(16, 4, 11, '2026-11-19 11:39:22', 600.00, 'pending'),
(17, 4, 11, '2026-11-19 11:39:33', 600.00, 'confirmed'),
(18, 4, 12, '2026-11-19 11:39:52', 700.00, 'pending'),
(19, 4, 11, '2026-11-19 11:46:21', 600.00, 'pending'),
(20, 4, 11, '2026-11-19 11:49:08', 600.00, 'pending'),
(21, 4, 11, '2026-11-19 11:53:24', 600.00, 'pending'),
(22, 4, 11, '2026-11-19 12:06:42', 600.00, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `booking_seats`
--

CREATE TABLE `booking_seats` (
  `booking_seats` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `seat_id` int(11) NOT NULL,
  `passenger_name` varchar(100) NOT NULL,
  `passenger_phone` int(20) NOT NULL,
  `seat_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_seats`
--

INSERT INTO `booking_seats` (`booking_seats`, `booking_id`, `seat_id`, `passenger_name`, `passenger_phone`, `seat_price`) VALUES
(1, 1, 5, 'touhid', 2147483647, 600.00),
(2, 2, 10, 'jarif', 359340594, 600.00),
(3, 2, 11, 'jarif', 359340594, 600.00),
(4, 3, 17, 'kfskjf', 2147483647, 600.00),
(5, 4, 23, 'dfjs', 1607955623, 600.00),
(6, 5, 54, 'sjfk', 2147483647, 700.00),
(7, 6, 34, 'fs', 3243242, 600.00),
(8, 7, 20, 'sfsf', 2147483647, 600.00),
(9, 8, 55, 'touhid', 1607955623, 700.00),
(10, 9, 15, 'sds', 3453453, 600.00),
(11, 10, 28, 'dfdkfj', 1607955623, 600.00),
(12, 11, 16, 'dasd', 2147483647, 600.00),
(13, 12, 61, 'touhid', 834783748, 700.00),
(14, 13, 57, 'dfkdsfj', 94328432, 700.00),
(15, 14, 22, 'fahim', 2147483647, 600.00),
(16, 15, 21, 'touhid', 234832984, 600.00),
(17, 16, 6, '', 0, 600.00),
(18, 17, 14, '', 0, 600.00),
(19, 18, 64, '', 0, 700.00),
(20, 19, 19, 'ksjfksj', 2147483647, 600.00),
(21, 20, 18, 'touhid', 2147483647, 600.00),
(22, 21, 26, 'fjskd', 483248373, 600.00),
(23, 22, 25, 'sdifh', 329483298, 600.00);

-- --------------------------------------------------------

--
-- Table structure for table `buses`
--

CREATE TABLE `buses` (
  `bus_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `bus_name` varchar(100) NOT NULL,
  `bus_type` enum('AC','Non-AC','Sleeper','Business Class') NOT NULL,
  `total_seats` int(11) NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  `bus_number` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buses`
--

INSERT INTO `buses` (`bus_id`, `company_id`, `bus_name`, `bus_type`, `total_seats`, `status`, `bus_number`) VALUES
(1, 1, 'Hanif', 'Non-AC', 52, 'active', '101'),
(2, 1, 'Hanif', 'Non-AC', 45, 'inactive', '102'),
(3, 1, 'Hanif', 'Sleeper', 45, 'active', '103'),
(4, 1, 'Hanif', 'Sleeper', 45, 'active', '104');

-- --------------------------------------------------------

--
-- Table structure for table `bus_companies`
--

CREATE TABLE `bus_companies` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus_companies`
--

INSERT INTO `bus_companies` (`company_id`, `company_name`, `email`, `phone`, `address`, `status`, `created_at`) VALUES
(1, 'Hanif', 'rafat@gmail.com', '019678737873', 'Dhaka, Bangladesh\r\n\r\n', 'active', '2026-11-19 16:07:54'),
(3, 'Shohagh Paribahan', 'shohagh@gmail.com', '01812345678', 'Chaীবৃতৃttogram, Bangladesh', 'active', '2026-11-19 16:06:31');

-- --------------------------------------------------------

--
-- Table structure for table `cancellations`
--

CREATE TABLE `cancellations` (
  `cancellation_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `cancellation_charge` decimal(10,2) NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `cancellation_status` enum('pending','approved','rejected') NOT NULL,
  `requested_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid','failed') NOT NULL,
  `payment_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `payment_method`, `transaction_id`, `amount`, `payment_status`, `payment_date`) VALUES
(1, 6, 'bKash Demo', 'DEMO-36ECDF2F1CA37679', 600.00, 'paid', '2026-11-19 02:06:30'),
(2, 7, 'bKash Demo', 'DEMO-BCE769B77B8E4EB5', 600.00, 'paid', '2026-11-19 02:11:45'),
(3, 8, 'bKash Demo', 'DEMO-28BA9D5B6F3C6892', 700.00, 'paid', '2026-11-19 02:13:55'),
(4, 13, 'bKash Demo', 'DEMO-90EECB8491A008AD', 700.00, 'paid', '2026-11-19 02:44:39'),
(5, 14, 'bKash Demo', 'DEMO-71438116FE52224A', 600.00, 'paid', '2026-11-19 10:52:34'),
(6, 15, 'bKash Demo', 'DEMO-43D9C9BA623E3F27', 600.00, 'paid', '2026-11-19 11:34:16'),
(7, 17, 'bKash Demo', 'DEMO-8413AFC2A302091C', 600.00, 'paid', '2026-11-19 11:39:34');

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `refund_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `cancellation_id` int(11) NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `refund_method` varchar(50) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `refund_status` enum('pending','processing','completed','failed') NOT NULL,
  `refund_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `route_id` int(11) NOT NULL,
  `source` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `distance` decimal(10,2) NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  `is_popular` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`route_id`, `source`, `destination`, `distance`, `status`, `is_popular`) VALUES
(1, 'Dhaka', 'Bogura', 195.00, 'active', 1),
(2, 'Bogura', 'Dhaka', 195.00, 'active', 1),
(3, 'Dhaka', 'Chattogram', 250.00, 'active', 1),
(5, 'Dhaka', 'Rangpur', 305.00, 'active', 1);

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `schedule_id` int(11) NOT NULL,
  `bus_id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `departure_date` date NOT NULL,
  `departure_time` time NOT NULL,
  `arrival_time` time NOT NULL,
  `ticket_price` decimal(10,2) NOT NULL,
  `status` enum('scheduled','completed','cancelled') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `bus_id`, `route_id`, `departure_date`, `departure_time`, `arrival_time`, `ticket_price`, `status`) VALUES
(1, 1, 1, '2026-11-16', '08:30:00', '15:00:00', 700.00, 'scheduled'),
(2, 3, 3, '2026-11-17', '08:00:00', '15:59:00', 500.00, 'scheduled'),
(11, 3, 1, '2026-11-19', '02:01:00', '20:01:00', 600.00, 'scheduled'),
(12, 1, 1, '2026-11-19', '12:29:00', '16:29:00', 700.00, 'scheduled'),
(13, 3, 1, '2026-11-20', '13:36:00', '18:36:00', 800.00, 'scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

CREATE TABLE `seats` (
  `seat_id` int(11) NOT NULL,
  `bus_id` int(11) NOT NULL,
  `seat_number` varchar(10) NOT NULL,
  `seat_type` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seats`
--

INSERT INTO `seats` (`seat_id`, `bus_id`, `seat_number`, `seat_type`) VALUES
(1, 3, 'A1', 'Standard'),
(2, 3, 'A2', 'Standard'),
(3, 3, 'A3', 'Standard'),
(4, 3, 'A4', 'Standard'),
(5, 3, 'B1', 'Standard'),
(6, 3, 'B2', 'Standard'),
(7, 3, 'B3', 'Standard'),
(8, 3, 'B4', 'Standard'),
(9, 3, 'C1', 'Standard'),
(10, 3, 'C2', 'Standard'),
(11, 3, 'C3', 'Standard'),
(12, 3, 'C4', 'Standard'),
(13, 3, 'D1', 'Standard'),
(14, 3, 'D2', 'Standard'),
(15, 3, 'D3', 'Standard'),
(16, 3, 'D4', 'Standard'),
(17, 3, 'E1', 'Standard'),
(18, 3, 'E2', 'Standard'),
(19, 3, 'E3', 'Standard'),
(20, 3, 'E4', 'Standard'),
(21, 3, 'F1', 'Standard'),
(22, 3, 'F2', 'Standard'),
(23, 3, 'F3', 'Standard'),
(24, 3, 'F4', 'Standard'),
(25, 3, 'G1', 'Standard'),
(26, 3, 'G2', 'Standard'),
(27, 3, 'G3', 'Standard'),
(28, 3, 'G4', 'Standard'),
(29, 3, 'H1', 'Standard'),
(30, 3, 'H2', 'Standard'),
(31, 3, 'H3', 'Standard'),
(32, 3, 'H4', 'Standard'),
(33, 3, 'I1', 'Standard'),
(34, 3, 'I2', 'Standard'),
(35, 3, 'I3', 'Standard'),
(36, 3, 'I4', 'Standard'),
(37, 3, 'J1', 'Standard'),
(38, 3, 'J2', 'Standard'),
(39, 3, 'J3', 'Standard'),
(40, 3, 'J4', 'Standard'),
(41, 3, 'K1', 'Standard'),
(42, 3, 'K2', 'Standard'),
(43, 3, 'K3', 'Standard'),
(44, 3, 'K4', 'Standard'),
(45, 3, 'L1', 'Standard'),
(46, 1, 'A1', 'Standard'),
(47, 1, 'A2', 'Standard'),
(48, 1, 'A3', 'Standard'),
(49, 1, 'A4', 'Standard'),
(50, 1, 'B1', 'Standard'),
(51, 1, 'B2', 'Standard'),
(52, 1, 'B3', 'Standard'),
(53, 1, 'B4', 'Standard'),
(54, 1, 'C1', 'Standard'),
(55, 1, 'C2', 'Standard'),
(56, 1, 'C3', 'Standard'),
(57, 1, 'C4', 'Standard'),
(58, 1, 'D1', 'Standard'),
(59, 1, 'D2', 'Standard'),
(60, 1, 'D3', 'Standard'),
(61, 1, 'D4', 'Standard'),
(62, 1, 'E1', 'Standard'),
(63, 1, 'E2', 'Standard'),
(64, 1, 'E3', 'Standard'),
(65, 1, 'E4', 'Standard'),
(66, 1, 'F1', 'Standard'),
(67, 1, 'F2', 'Standard'),
(68, 1, 'F3', 'Standard'),
(69, 1, 'F4', 'Standard'),
(70, 1, 'G1', 'Standard'),
(71, 1, 'G2', 'Standard'),
(72, 1, 'G3', 'Standard'),
(73, 1, 'G4', 'Standard'),
(74, 1, 'H1', 'Standard'),
(75, 1, 'H2', 'Standard'),
(76, 1, 'H3', 'Standard'),
(77, 1, 'H4', 'Standard'),
(78, 1, 'I1', 'Standard'),
(79, 1, 'I2', 'Standard'),
(80, 1, 'I3', 'Standard'),
(81, 1, 'I4', 'Standard'),
(82, 1, 'J1', 'Standard'),
(83, 1, 'J2', 'Standard'),
(84, 1, 'J3', 'Standard'),
(85, 1, 'J4', 'Standard'),
(86, 1, 'K1', 'Standard'),
(87, 1, 'K2', 'Standard'),
(88, 1, 'K3', 'Standard'),
(89, 1, 'K4', 'Standard'),
(90, 1, 'L1', 'Standard'),
(91, 1, 'L2', 'Standard'),
(92, 1, 'L3', 'Standard'),
(93, 1, 'L4', 'Standard'),
(94, 1, 'M1', 'Standard'),
(95, 1, 'M2', 'Standard'),
(96, 1, 'M3', 'Standard'),
(97, 1, 'M4', 'Standard');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','company_admin','customer') NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `phone`, `password`, `role`, `company_id`, `created_at`) VALUES
(1, 'jahin', 'jahin@gmail.com', '017000003487', '0f5c0cbcf0e77547a9d942a9c1ed68ec629569a6', 'super_admin', NULL, '2026-11-04 19:30:47'),
(2, 'rafat', 'rafat@gmail.com', '', 'c4ee6474f8fd46c343795bf9fbcd1bb3f9a6b147', 'company_admin', 1, '2026-11-12 19:26:49'),
(4, 'touhid', 'touhid@gmail.com', '', 'cc4ed6830efc118d6b97e74bbc639637c411eafb', 'customer', NULL, '2026-11-06 10:43:26'),
(12, 'moheuzzaman', 'mohe@gmail.com', '', '8cc8fc2d79549dc285b96f7e5a85c11735458faa', 'customer', NULL, '2026-11-10 12:32:32'),
(13, 'korim', 'korim@gmail.com', '0193483', '478153ed370153e39ff185065c02d1fc5cfc536b', 'customer', NULL, '2026-11-10 12:41:39'),
(17, 'safwan', 'safwan@gmail.com', '320483294892', 'da39a3ee5e6b4b0d3255bfef95601890afd80709', '', 0, '2026-11-14 12:08:01'),
(21, 'shoyaib', 'shoyaib@gmail.com', '647890', '71f74ac897254bdedc2bd6a8f72074bebc133691', 'company_admin', 1, '2026-11-14 12:18:25'),
(24, 'shoyaib', 'sho@gmail.com', '647890', '71f74ac897254bdedc2bd6a8f72074bebc133691', 'company_admin', 1, '2026-11-14 12:21:17'),
(25, 'shohage', 'shohag@gmail.com', '4673892', '7977d8c57350d3d9b5e946e81355ad0c27278c60', 'company_admin', 3, '2026-11-14 12:22:15'),
(26, '', '', '', 'da39a3ee5e6b4b0d3255bfef95601890afd80709', '', 0, '2026-11-15 11:16:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`);

--
-- Indexes for table `booking_seats`
--
ALTER TABLE `booking_seats`
  ADD PRIMARY KEY (`booking_seats`);

--
-- Indexes for table `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`bus_id`),
  ADD UNIQUE KEY `bus_number` (`bus_number`);

--
-- Indexes for table `bus_companies`
--
ALTER TABLE `bus_companies`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `cancellations`
--
ALTER TABLE `cancellations`
  ADD PRIMARY KEY (`cancellation_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`refund_id`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`route_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`schedule_id`);

--
-- Indexes for table `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`seat_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `company_id_2` (`company_id`),
  ADD KEY `company_id_3` (`company_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `booking_seats`
--
ALTER TABLE `booking_seats`
  MODIFY `booking_seats` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `buses`
--
ALTER TABLE `buses`
  MODIFY `bus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bus_companies`
--
ALTER TABLE `bus_companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cancellations`
--
ALTER TABLE `cancellations`
  MODIFY `cancellation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `refund_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `route_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `seat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
