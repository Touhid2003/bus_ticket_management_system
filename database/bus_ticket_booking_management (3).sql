-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 15, 2026 at 09:10 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `booking_seats`
--

CREATE TABLE `booking_seats` (
  `booking_seats` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `seat_id` int(11) NOT NULL,
  `passenger_name` varchar(100) NOT NULL,
  `passenger_phone` varchar(20) NOT NULL,
  `seat_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'Hanif', 'rafat@gmail.com', '019678737873', 'Dhaka,Bangladesh', 'active', '2026-11-12 19:15:52'),
(3, 'Shohagh Paribahan', 'shohagh@gmail.com', '01812345678', 'Chattogram, Bangladesh', 'active', '2026-11-08 17:52:00');

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
  `status` enum('active','inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`route_id`, `source`, `destination`, `distance`, `status`) VALUES
(1, 'Dhaka', 'Bogura', 195.00, 'active'),
(2, 'Bogura', 'Dhaka', 195.00, 'active'),
(3, 'Dhaka', 'Chattogram', 250.00, 'active'),
(5, 'Dhaka', 'Rangpur', 305.00, 'active');

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
(11, 3, 1, '2026-11-19', '02:01:00', '20:01:00', 600.00, 'scheduled');

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
(16, 'touhid1', 'touhid1@gmail.com', '02839888', 'cc4ed6830efc118d6b97e74bbc639637c411eafb', 'customer', NULL, '2026-11-10 12:47:11'),
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
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `booking_seats`
--
ALTER TABLE `booking_seats`
  MODIFY `booking_seats` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `buses`
--
ALTER TABLE `buses`
  MODIFY `bus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bus_companies`
--
ALTER TABLE `bus_companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cancellations`
--
ALTER TABLE `cancellations`
  MODIFY `cancellation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `refund_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `route_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `seat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
