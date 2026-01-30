-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 30, 2026 at 10:50 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `car_rental_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `car_id` int NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `year` int NOT NULL,
  `fuel_type` varchar(50) NOT NULL,
  `drive_type` varchar(20) NOT NULL,
  `transmission` varchar(20) NOT NULL,
  `engine_cylinders` int NOT NULL,
  `displacement` float NOT NULL,
  `plate_number` varchar(20) NOT NULL,
  `daily_rate` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL,
  `category_name` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`car_id`, `brand`, `model`, `year`, `fuel_type`, `drive_type`, `transmission`, `engine_cylinders`, `displacement`, `plate_number`, `daily_rate`, `status`, `created_at`, `category_name`, `description`, `modified`) VALUES
(23, 'AUDI', 'TT', 2021, 'Petrol', 'FWD', 'Automatic', 4, 2, 'WLF 4321', '550.00', 'Available', '2026-01-14 23:38:54', 'General', 'Good Condition', '2026-01-14 23:38:54'),
(25, 'PORSCHE', '911', 2025, 'Petrol', 'FWD', 'Automatic', 4, 2, 'JWG 775', '2500.00', 'Available', '2026-01-14 23:42:52', 'Sedan', '', '2026-01-14 23:42:52'),
(26, 'FERRARI', 'La Ferrari', 2015, 'Petrol', 'FWD', 'Automatic', 4, 2, 'WWW 10', '10000.00', 'Available', '2026-01-14 23:44:01', 'Sedan', 'HIGH-END LUXURY', '2026-01-27 09:57:52'),
(27, 'BMW', '530i', 2020, 'Petrol', 'FWD', 'Automatic', 4, 2, 'BMW 530', '1500.00', 'Available', '2026-01-14 23:46:10', 'Sedan', '', '2026-01-14 23:46:10'),
(28, 'TOYOTA', 'Corolla', 2018, 'Petrol', 'FWD', 'Automatic', 4, 2, 'WLF 1234', '350.00', 'Available', '2026-01-14 23:48:32', 'Unknown', '', '2026-01-14 23:48:32'),
(29, 'AAS', 'Sport Edition', 2023, 'Petrol', 'FWD', 'Automatic', 4, 2, 'JB 4', '1000.00', 'Rented', '2026-01-14 23:49:00', 'Unknown', '', '2026-01-30 17:17:17'),
(31, 'ASTON MARTIN', 'Vanquish', 2015, 'Petrol', 'FWD', 'Automatic', 4, 2, 'J 12', '7550.00', 'Available', '2026-01-14 23:51:43', 'Sedan', '', '2026-01-27 09:57:52'),
(32, 'HONDA', 'Accord', 2025, 'Petrol', 'FWD', 'Automatic', 4, 2, 'AAA 7657', '449.00', 'Available', '2026-01-14 23:53:54', 'Sedan', '', '2026-01-14 23:53:54'),
(33, 'TOYOTA', 'GR Corolla', 2015, 'Petrol', 'FWD', 'Automatic', 4, 2, 'WEA 7676', '150.00', 'Available', '2026-01-15 01:39:06', 'Sedan', '', '2026-01-15 01:39:06'),
(34, 'MERCEDES-BENZ', 'GLA-Class', 2020, 'Petrol', 'FWD', 'Automatic', 4, 2, 'KLA 8765', '700.00', 'Available', '2026-01-15 01:40:20', 'Sedan', '', '2026-01-15 01:40:20'),
(36, 'ASTON MARTIN', 'Vantage', 2023, 'Petrol', 'FWD', 'Automatic', 4, 2, 'JB 11', '5500.00', 'Available', '2026-01-15 01:44:30', 'Luxury', '', '2026-01-15 01:44:30'),
(37, 'NISSAN', 'GT-R', 2016, 'Petrol', 'FWD', 'Automatic', 4, 2, 'GD 25', '1500.00', 'Available', '2026-01-15 01:47:04', 'Sports Car', 'Twin-Turbo', '2026-01-15 01:47:04'),
(38, 'HYUNDAI', 'Elantra GT', 2020, 'Petrol', 'FWD', 'Automatic', 4, 2, 'LOL 22', '250.00', 'Available', '2026-01-15 01:54:12', 'Sedan', '', '2026-01-15 01:54:12'),
(39, 'FORD', 'Mustang', 2016, 'Petrol', 'FWD', 'Automatic', 4, 2, 'M 4386', '500.00', 'Available', '2026-01-15 02:00:45', 'Coupe', '', '2026-01-27 09:57:52'),
(40, 'KOENIGSEGG', 'Agera', 2020, 'Petrol', 'FWD', 'Automatic', 4, 2, 'JD 444', '10000.00', 'Available', '2026-01-17 19:10:01', 'Luxury', '', '2026-01-27 09:57:52'),
(42, 'AUDI', 'RS 7', 2024, 'Petrol', 'FWD', 'Automatic', 4, 2, 'LDY 14', '5600.00', 'Available', '2026-01-28 23:29:34', 'Hatchback', '', '2026-01-28 23:29:34'),
(43, 'AUDI', 'A4', 2023, 'Petrol', 'FWD', 'Automatic', 4, 2, 'JTF 4457', '1000.00', 'Available', '2026-01-28 23:30:40', 'Sedan', '', '2026-01-28 23:30:40');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int NOT NULL,
  `rental_id` int NOT NULL,
  `user_id` int NOT NULL,
  `payment_methods` varchar(30) NOT NULL,
  `payment_date` datetime NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_status` varchar(20) NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `rental_id`, `user_id`, `payment_methods`, `payment_date`, `amount`, `payment_status`, `status`, `created_at`, `modified`) VALUES
(3, 33, 13, 'Debit Card', '2026-01-18 03:46:43', '3500.00', 'Completed', 1, '2026-01-18 03:46:43', '2026-01-18 03:46:43'),
(4, 34, 13, 'Cash', '2026-01-18 03:47:30', '10000.00', 'Completed', 1, '2026-01-18 03:47:30', '2026-01-18 03:47:30'),
(6, 36, 1, 'Credit Card', '2026-01-18 04:31:37', '15100.00', 'Completed', 1, '2026-01-18 04:31:37', '2026-01-18 04:31:37'),
(7, 37, 1, 'Debit Card', '2026-01-30 17:17:17', '1000.00', 'Completed', 1, '2026-01-30 17:17:17', '2026-01-30 17:17:17');

-- --------------------------------------------------------

--
-- Table structure for table `rentals`
--

CREATE TABLE `rentals` (
  `rental_id` int NOT NULL,
  `user_id` int NOT NULL,
  `car_id` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` int NOT NULL,
  `total_price` decimal(10,0) NOT NULL,
  `rental_status` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `status` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rentals`
--

INSERT INTO `rentals` (`rental_id`, `user_id`, `car_id`, `start_date`, `end_date`, `total_days`, `total_price`, `rental_status`, `created_at`, `modified`, `status`) VALUES
(22, 1, 31, '2026-01-14', '2026-01-15', 1, '7550', 'Completed', '2026-01-14 23:52:03', '2026-01-14 23:52:03', 1),
(23, 1, 40, '2026-01-17', '2026-01-18', 1, '10000', 'Completed', '2026-01-17 19:10:53', '2026-01-17 19:10:53', 1),
(32, 13, 39, '2026-01-19', '2026-01-26', 7, '3500', 'Completed', '2026-01-18 03:44:46', '2026-01-18 03:44:46', 1),
(33, 13, 39, '2026-01-19', '2026-01-26', 7, '3500', 'Completed', '2026-01-18 03:46:43', '2026-01-18 03:46:43', 1),
(34, 13, 26, '2026-01-19', '2026-01-20', 1, '10000', 'Completed', '2026-01-18 03:47:30', '2026-01-18 03:47:30', 1),
(36, 1, 31, '2026-01-18', '2026-01-20', 2, '15100', 'Completed', '2026-01-18 04:31:37', '2026-01-18 04:31:37', 1),
(37, 1, 29, '2026-01-31', '2026-02-01', 1, '1000', 'Active', '2026-01-30 17:17:17', '2026-01-30 17:17:17', 1);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `status` tinyint NOT NULL,
  `modified` datetime NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `status`, `modified`, `created_at`) VALUES
(1, 'admin', 1, '2026-01-12 18:20:57', '2026-01-12 18:20:57');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `role_id` int NOT NULL DEFAULT '2',
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `phone_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `role_id`, `full_name`, `email`, `password`, `phone_number`, `created_at`, `status`, `modified`) VALUES
(1, 1, 'adam', NULL, '$2y$10$wUngOGgq52e3LVMQJdnTaOILr8EXdFyRt9Zy342C6m4rMRJZJ5yDG', '', '2026-01-12 02:12:20', NULL, '2026-01-12 02:12:20'),
(8, 2, 'dam', 'dam@gmail.com', '$2y$10$K7m8BKORtpRNyzFb8OaYVO.f1l4648lPHPAAE7SKYyWK65ayWeFvq', '0132456978', '2026-01-13 02:49:20', NULL, '2026-01-13 02:49:20'),
(9, 2, 'kip', 'kip@gmail.com', '$2y$10$cfLGKqd6LcpuVa1ueyv8bODFK.62BYfZTA.OFipiYPx/W3iSn/vB.', '011236985', '2026-01-13 03:33:05', NULL, '2026-01-13 03:33:05'),
(10, 2, 'pik', 'pik@gmail.com', '$2y$10$rHg8LfLXwM9OMhIEAdAKduVLXCyFGb8kOdnxDOWdC2V4bhhv9wNJe', '014253698', '2026-01-13 05:09:45', NULL, '2026-01-13 05:09:45'),
(11, 2, 'ammar', 'ammar@gmail.com', '$2y$10$e9zp8lAC6dewfgwkuXCXOeis7IDOrmVlulqGhO2fSffGvS7OiYNhm', '01164508304', '2026-01-13 14:07:43', NULL, '2026-01-13 14:07:43'),
(12, 2, 'hakim', 'kimkim@gmail.com', '$2y$10$mXFoEoarn88LGnhdhmiKR.mdSozGahJOC7cppk4RBMO4v2Ca6YdT.', '0165478542', '2026-01-18 01:39:51', NULL, '2026-01-18 01:39:51'),
(13, 2, 'man', 'man@gmail.com', '$2y$10$VEFNGOmb16en4ZakQgwBUORABOwcUt4/5K8rL9GU/jlM8qIp/6nTO', '011111345', '2026-01-18 01:47:35', NULL, '2026-01-18 01:47:35'),
(14, 1, 'admin', 'admin@gmail.com', '$2y$10$N0J3eiadD/2nOHvFfxF/TOn221s2240gfkIdV/XL2mimJ/iF1YUQ2', '012559874', '2026-01-28 23:35:20', NULL, '2026-01-28 23:35:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`car_id`),
  ADD UNIQUE KEY `plate_number` (`plate_number`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `rental_id` (`rental_id`);

--
-- Indexes for table `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`rental_id`),
  ADD KEY `car_id` (`car_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `car_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rentals`
--
ALTER TABLE `rentals`
  MODIFY `rental_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`rental_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rentals`
--
ALTER TABLE `rentals`
  ADD CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`car_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
