-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 26, 2025 at 05:27 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `banking_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '123'),
(3, 'newadmin', 'newpassword123');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `accno` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `type`, `amount`, `timestamp`, `accno`) VALUES
(1, 1, 'debit', '100.00', '2024-12-12 06:16:36', 0),
(2, 1, 'credit', '100.00', '2024-12-12 06:16:36', 0),
(3, 1, 'debit', '100.00', '2024-12-12 06:18:10', 0),
(4, 1, 'credit', '100.00', '2024-12-12 06:18:10', 0),
(5, 1, 'debit', '100.00', '2024-12-12 06:20:53', 0),
(6, 1, 'credit', '100.00', '2024-12-12 06:20:53', 0),
(7, 1, 'debit', '100.00', '2024-12-12 06:22:02', 0),
(8, 2, 'credit', '100.00', '2024-12-12 06:22:02', 0),
(9, 1, 'debit', '1000.00', '2024-12-12 06:22:31', 0),
(10, 2, 'credit', '1000.00', '2024-12-12 06:22:31', 0),
(11, 1, 'debit', '100.00', '2024-12-12 06:48:16', 0),
(12, 1, 'credit', '100.00', '2024-12-12 06:48:16', 0),
(13, 1, 'debit', '1000.00', '2024-12-12 06:49:31', 0),
(14, 6, 'credit', '1000.00', '2024-12-12 06:49:31', 0),
(15, 3, 'credit', '100000.00', '2024-12-12 09:55:21', 0),
(16, 0, 'credit', '10000.00', '2024-12-12 09:57:47', 0),
(17, 9, 'credit', '100000.00', '2024-12-12 09:59:23', 0),
(18, 9, 'debit', '10000.00', '2025-02-04 15:31:44', 0),
(19, 3, 'credit', '10000.00', '2025-02-04 15:31:44', 0),
(20, 0, 'credit', '100000.00', '2025-02-04 15:35:25', 0),
(21, 9, 'credit', '10000000.00', '2025-02-04 15:38:50', 0),
(22, 9, 'debit', '500000.00', '2025-02-04 15:40:07', 0),
(23, 3, 'credit', '500000.00', '2025-02-04 15:40:07', 0),
(24, 9, 'debit', '123000.00', '2025-02-04 15:42:10', 0),
(25, 3, 'credit', '123000.00', '2025-02-04 15:42:10', 0),
(26, 9, 'debit', '100000.00', '2025-02-04 15:49:26', 0),
(27, 3, 'credit', '100000.00', '2025-02-04 15:49:26', 0),
(28, 9, 'debit', '10000.00', '2025-02-04 15:50:43', 0),
(29, 3, 'credit', '10000.00', '2025-02-04 15:50:43', 0),
(30, 9, 'debit', '10000.00', '2025-02-04 15:54:21', 0),
(31, 3, 'credit', '10000.00', '2025-02-04 15:54:21', 0),
(32, 0, 'credit', '100000.00', '2025-02-04 15:55:59', 0),
(33, 11, 'credit', '33000.00', '2025-02-04 16:23:07', 0),
(34, 9, 'debit', '1000.00', '2025-02-05 08:28:20', 0),
(35, 12, 'credit', '1000.00', '2025-02-05 08:28:20', 0),
(36, 9, 'debit', '1000.00', '2025-02-05 08:31:06', 0),
(37, 15, 'credit', '1000.00', '2025-02-05 08:31:06', 0),
(38, 9, 'debit', '1000.00', '2025-02-05 08:31:52', 0),
(39, 15, 'credit', '1000.00', '2025-02-05 08:31:52', 0),
(40, 11, 'debit', '1000.00', '2025-02-25 14:16:45', 0),
(41, 12, 'credit', '1000.00', '2025-02-25 14:16:45', 0),
(42, 11, 'debit', '2000.00', '2025-02-25 14:19:13', 0),
(43, 15, 'credit', '2000.00', '2025-02-25 14:19:13', 0),
(44, 11, 'credit', '1000.00', '2025-02-25 18:52:20', 0),
(45, 11, 'credit', '1000.00', '2025-02-25 18:55:58', 0),
(46, 12, 'credit', '20000.00', '2025-02-25 20:38:57', 0),
(47, 11, 'credit', '200000.00', '2025-02-25 21:40:34', 0),
(48, 11, 'debit', '1000.00', '2025-02-25 21:45:37', 0),
(49, 12, 'credit', '1000.00', '2025-02-25 21:45:37', 0),
(50, 11, 'credit', '10000.00', '2025-02-26 07:45:53', 0),
(51, 11, 'debit', '2000.00', '2025-02-26 07:50:35', 0),
(52, 12, 'credit', '2000.00', '2025-02-26 07:50:35', 0),
(53, 11, 'debit', '20000.00', '2025-02-26 11:04:30', 0),
(54, 15, 'credit', '20000.00', '2025-02-26 11:04:30', 0),
(55, 11, 'credit', '200000.00', '2025-02-26 12:03:08', 0),
(56, 11, 'debit', '12000.00', '2025-02-26 12:15:30', 0),
(57, 15, 'credit', '12000.00', '2025-02-26 12:15:30', 0),
(58, 11, 'credit', '20000.00', '2025-02-26 13:08:55', 0),
(59, 11, 'credit', '50000.00', '2025-02-26 13:32:49', 0),
(60, 11, 'credit', '5000.00', '2025-02-26 13:37:13', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(20) NOT NULL,
  `balance` decimal(10,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `admin_id` int(11) DEFAULT NULL,
  `accno` int(15) NOT NULL,
  `address` varchar(100) NOT NULL,
  `nic` int(12) NOT NULL,
  `phone` int(10) NOT NULL,
  `date_of_birth` int(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `balance`, `status`, `admin_id`, `accno`, `address`, `nic`, `phone`, `date_of_birth`) VALUES
(3, 'tharu', 'kkk@gmail.com', 'admin123', '10000.00', 'inactive', NULL, 0, '', 0, 0, 0),
(9, 'dami', 'dami@gmail.com', '123', '9344000.00', 'inactive', NULL, 0, '', 0, 0, 0),
(11, 'sandunika', 'sanduherath0905@gmail.com', '123', '450000.00', 'active', NULL, 40501050, 'no 41 dippitigoda menikdiwela', 2147483647, 752735746, 2025),
(12, 'Tharushi ekanayake', 'tharu@gmail.com', '123', '20000.00', 'active', NULL, 40501051, 'colombo', 2147483647, 2147483647, 2025),
(15, 'hashi', 'hashi@gmail.com', '123', '154200.00', 'inactive', NULL, 0, '', 0, 0, 0),
(16, 'Dhananjalie wijerathne', 'dami1@gmail.com', '123', '5000400.00', 'active', NULL, 40501057, 'theldeniya', 2147483647, 764587956, 2025),
(17, 'Hasini Ekanayake', 'hasini@gmail.com', '123', '1000000.00', 'active', NULL, 40501053, 'mathale ', 2147483647, 2147483647, 2025),
(18, 'Ishani Ransika', 'ishani@gmail.com', '123', '5000000.00', 'active', NULL, 40501054, 'bokkawala', 2147483647, 756649823, 2025),
(19, 'Sandunika Herath', 'sandu@gmail.com', '123', '99999999.99', 'active', NULL, 40501055, 'poththapitiya', 2147483647, 752735746, 2025),
(20, 'Prabath Manikpura', 'prabath@gmail.com', '123', '45201230.00', 'active', NULL, 40501058, 'theldeniya', 2147483647, 2147483647, 2025),
(21, 'Venura Sandaruwan', 'venura@gmail.com', '123', '4452100.00', 'active', NULL, 40501060, 'peradeniya', 2147483647, 754899732, 2025),
(22, 'Nilawala', 'nilwala@gmail.com', '123', '0.00', 'active', NULL, 40501062, 'kandy', 200214566, 754879555, 2025),
(23, 'Hashini', 'hasini123@gmail.com', '123', '0.00', 'active', NULL, 40501063, 'theldeniya', 2147483647, 2147483647, 2025),
(24, 'Dhananjalie wijerathne', 'sanduherath01905@gmail.com', '123', '0.00', 'inactive', NULL, 40000, 'theldeniya', 2001563215, 45633366, 2025),
(25, 'Dhananjalie wijerathne', 'damitha@gmail.com', '123', '0.00', 'inactive', NULL, 40501067, 'theldeniya', 2147483647, 758498744, 2025),
(26, 'damitha herath', 'damitha12@gmail.com', '123', '0.00', 'inactive', NULL, 40501067, 'theldenia', 2000045679, 778956423, 2025);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_admin` (`admin_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
