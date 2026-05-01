-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 01, 2026 at 07:28 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uiu_foodexpress`
--

-- --------------------------------------------------------

--
-- Table structure for table `canteens`
--

CREATE TABLE `canteens` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(150) DEFAULT NULL,
  `contact_info` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `canteens`
--

INSERT INTO `canteens` (`id`, `name`, `location`, `contact_info`, `created_at`) VALUES
(1, 'Main Canteen', 'Ground Floor', '01700000001', '2026-05-01 14:07:28'),
(2, 'Cafe 101', 'Academic Building', '01700000002', '2026-05-01 14:07:28'),
(3, 'CP 5 star', 'Ground Floor', '01711111119', '2026-05-01 14:54:13'),
(4, 'Main Canteen', 'Ground Floor', '01700000001', '2026-05-01 14:59:10'),
(5, 'Cafe 101', 'Academic Building', '01700000002', '2026-05-01 14:59:10');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_availability`
--

CREATE TABLE `delivery_availability` (
  `id` int(11) NOT NULL,
  `delivery_person_id` int(11) NOT NULL,
  `available` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `food_items`
--

CREATE TABLE `food_items` (
  `id` int(11) NOT NULL,
  `canteen_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_items`
--

INSERT INTO `food_items` (`id`, `canteen_id`, `name`, `description`, `price`, `image`, `created_at`) VALUES
(1, 1, 'Chicken Drumstick', 'Crispy chicken drumstick with roll', 120.00, 'uploads/ChickenDrum.jpeg', '2026-05-01 17:21:34'),
(2, 1, 'Firni', 'Sweet dessert firni', 70.00, 'uploads/Firni.jpeg', '2026-05-01 17:21:34'),
(3, 2, 'Fried Chicken', 'Crispy fried chicken pieces', 120.00, 'uploads/FriedChicken.jpeg', '2026-05-01 17:21:34'),
(4, 2, 'Fried Rice', 'Chicken fried rice', 150.00, 'uploads/Friedrice.jpeg', '2026-05-01 17:21:34'),
(5, 3, 'Grill Chicken', 'Spicy grilled chicken', 220.00, 'uploads/GrillChicken.jpeg', '2026-05-01 17:21:34'),
(6, 1, 'Khichuri', 'Hot khichuri meal', 150.00, 'uploads/Khichuri.jpeg', '2026-05-01 17:21:34'),
(7, 2, 'Onion Chicken', 'Chicken cooked with onion and spices', 180.00, 'uploads/Onionchicken.jpeg', '2026-05-01 17:21:34'),
(8, 3, 'Rice with Fish', 'Plain rice with fish curry', 160.00, 'uploads/Rice+Fish.jpeg', '2026-05-01 17:21:34'),
(9, 1, 'Chicken Roll', 'Chicken roll with sauce', 90.00, 'uploads/Roll.jpeg', '2026-05-01 17:21:34'),
(10, 2, 'Sandwich', 'Chicken sandwich', 100.00, 'uploads/Sandwitch.jpeg', '2026-05-01 17:21:34');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `canteen_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `room_number` varchar(50) DEFAULT NULL,
  `building_block` varchar(100) DEFAULT NULL,
  `phone_number` varchar(30) DEFAULT NULL,
  `order_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`order_details`)),
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `status` varchar(50) DEFAULT 'pending',
  `assigned_delivery_id` int(11) DEFAULT NULL,
  `order_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_commissions`
--

CREATE TABLE `order_commissions` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `canteen_id` int(11) NOT NULL,
  `commission_amount` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_users`
--

CREATE TABLE `staff_users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','canteen_manager','delivery_person','user') NOT NULL,
  `canteen_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_users`
--

INSERT INTO `staff_users` (`id`, `full_name`, `email`, `password`, `role`, `canteen_id`, `created_at`) VALUES
(1, 'sharika', 'sharika@gmail.com', '$2y$10$I0JUMniue/m1Ytbkt6HFqucca3kSNxUVNwKelSnMP8s/GFhAk9HEW', 'canteen_manager', NULL, '2026-05-01 14:05:01'),
(2, 'admin', 'admin@gmail.com', '$2y$10$tTJhNjFCmAgX9s2QvZVEeehzTdphQkLv4XuIQYMw2JpcefTfnsI/m', 'admin', NULL, '2026-05-01 14:51:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `canteens`
--
ALTER TABLE `canteens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_availability`
--
ALTER TABLE `delivery_availability`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `food_items`
--
ALTER TABLE `food_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_commissions`
--
ALTER TABLE `order_commissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff_users`
--
ALTER TABLE `staff_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `canteens`
--
ALTER TABLE `canteens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `delivery_availability`
--
ALTER TABLE `delivery_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `food_items`
--
ALTER TABLE `food_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_commissions`
--
ALTER TABLE `order_commissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_users`
--
ALTER TABLE `staff_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
