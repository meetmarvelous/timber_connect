-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2025 at 02:47 PM
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
-- Database: `timber_connect`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','moderator') DEFAULT 'admin',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `email`, `password_hash`, `full_name`, `role`, `is_active`, `last_login`, `created_at`) VALUES
(1, 'admin', 'admin@woodconnect.com.ng', '$2y$10$4Gxnd777ol/UnmW5r5iJEuYeXB9VSajmc1bd3Qr616w6x1dY2GvBa', 'System Administrator', 'super_admin', 1, '2025-10-31 04:06:49', '2025-10-30 15:53:30');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `buyer_name` varchar(255) NOT NULL,
  `buyer_phone` varchar(20) NOT NULL,
  `buyer_email` varchar(255) DEFAULT NULL,
  `marketer_id` int(11) NOT NULL,
  `species_id` int(11) NOT NULL,
  `dimensions` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','contacted','completed','cancelled') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `marketer_id` int(11) NOT NULL,
  `species_id` int(11) NOT NULL,
  `dimensions` varchar(50) NOT NULL,
  `price_per_unit` decimal(10,2) NOT NULL,
  `quantity_available` int(11) NOT NULL DEFAULT 0,
  `unit_type` enum('length','piece','bundle') DEFAULT 'length',
  `quality_grade` enum('premium','standard','economy') DEFAULT 'standard',
  `description` text DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `marketer_id`, `species_id`, `dimensions`, `price_per_unit`, `quantity_available`, `unit_type`, `quality_grade`, `description`, `image_path`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 1, 3, '2x6', 3000.00, 400, 'length', 'standard', 'Quality Ayunre wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(2, 1, 15, '2x12', 7000.00, 800, 'length', 'standard', 'Quality Itara wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(3, 1, 1, '1x6', 2000.00, 500, 'length', 'standard', 'Quality Iroko wood available in 1x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(4, 1, 4, '1x12', 4500.00, 400, 'length', 'standard', 'Quality Sonu wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(5, 1, 5, '3x4', 8000.00, 400, 'length', 'standard', 'Quality Ire wood available in 3x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(6, 1, 2, '3x6', 12000.00, 400, 'length', 'standard', 'Quality Mahogany wood available in 3x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(7, 1, 14, '2x4', 2000.00, 400, 'length', 'standard', 'Quality Ogbabi wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(8, 1, 13, '2x3', 1500.00, 400, 'length', 'standard', 'Quality Koko igbo wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(9, 2, 1, '2x3', 1400.00, 500, 'length', 'standard', 'Quality Iroko wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(10, 2, 11, '2x2', 1200.00, 1200, 'length', 'standard', 'Quality Opoporopo wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(11, 2, 12, '2x2', 7000.00, 700, 'length', 'standard', 'Quality Abora wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(12, 2, 3, '2x6', 3200.00, 1000, 'length', 'standard', 'Quality Ayunre wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(13, 2, 7, '2x6', 3000.00, 800, 'length', 'standard', 'Quality Somi wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(14, 2, 6, '3x4', 2300.00, 1000, 'length', 'standard', 'Quality Opepe wood available in 3x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(15, 2, 36, '2x8', 1000.00, 1000, 'length', 'standard', 'Quality Ekki wood available in 2x8 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(16, 3, 18, '1x12', 2600.00, 150, 'length', 'standard', 'Quality Payapayaba wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(17, 3, 18, '3x4', 2700.00, 100, 'length', 'standard', 'Quality Payapayaba wood available in 3x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(18, 3, 18, '2x4', 1500.00, 200, 'length', 'standard', 'Quality Payapayaba wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(19, 3, 18, '2x2', 1200.00, 500, 'length', 'standard', 'Quality Payapayaba wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(20, 3, 13, '2x4', 1500.00, 500, 'length', 'standard', 'Quality Kokogbo wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(21, 3, 7, '1x12', 2600.00, 500, 'length', 'standard', 'Quality Araba wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(22, 4, 6, '1x12', 2600.00, 500, 'length', 'standard', 'Quality Opepe wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(23, 4, 4, '1x12', 4000.00, 500, 'length', 'standard', 'Quality Sonu wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(24, 4, 1, '2x2', 1000.00, 500, 'length', 'standard', 'Quality Iroko wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(25, 4, 1, '2x3', 1400.00, 500, 'length', 'standard', 'Quality Iroko wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(26, 4, 1, '2x4', 1500.00, 500, 'length', 'standard', 'Quality Iroko wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(27, 4, 1, '3x4', 2700.00, 500, 'length', 'standard', 'Quality Iroko wood available in 3x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(28, 5, 1, '2x3', 1400.00, 500, 'length', 'standard', 'Quality Iroko wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(29, 5, 1, '2x12', 6000.00, 1000, 'length', 'standard', 'Quality Iroko wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(30, 5, 11, '2x2', 1200.00, 1200, 'length', 'standard', 'Quality Opoporopo wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(31, 5, 3, '2x6', 3500.00, 1000, 'length', 'standard', 'Quality Ayunre wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(32, 5, 7, '3x4', 3000.00, 500, 'length', 'standard', 'Quality Somi wood available in 3x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(33, 5, 7, '3x8', 6000.00, 400, 'length', 'standard', 'Quality Somi wood available in 3x8 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(34, 5, 6, '1x12', 2600.00, 500, 'length', 'standard', 'Quality Opepe wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(35, 5, 18, '2x4', 1500.00, 1000, 'length', 'standard', 'Quality Payapayaba wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(36, 6, 1, '2x12', 5000.00, 300, 'length', 'standard', 'Quality Iroko wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(37, 6, 2, '1x6', 2500.00, 300, 'length', 'standard', 'Quality Mahogany wood available in 1x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(38, 6, 23, '3x4', 2000.00, 300, 'length', 'standard', 'Quality Obobo wood available in 3x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(39, 6, 17, '2x4', 3000.00, 300, 'length', 'standard', 'Quality Gmelina wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(40, 6, 16, '1x12', 4000.00, 300, 'length', 'standard', 'Quality Teak wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(41, 6, 4, '3x8', 3000.00, 300, 'length', 'standard', 'Quality Opoporopo wood available in 3x8 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(42, 6, 1, '2x6', 1500.00, 300, 'length', 'standard', 'Quality Iroko wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(43, 7, 1, '2x2', 4000.00, 300, 'length', 'standard', 'Quality Iroko wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(44, 7, 21, '1x12', 3000.00, 300, 'length', 'standard', 'Quality Mansonia wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(45, 7, 10, '1x6', 1500.00, 300, 'length', 'standard', 'Quality Obeche wood available in 1x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(46, 7, 2, '3x8', 4000.00, 300, 'length', 'standard', 'Quality Mahogany wood available in 3x8 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(47, 7, 22, '3x4', 2000.00, 300, 'length', 'standard', 'Quality Orunmodun wood available in 3x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(48, 7, 19, '2x4', 1100.00, 300, 'length', 'standard', 'Quality Ita wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(49, 7, 9, '2x3', 1000.00, 300, 'length', 'standard', 'Quality Afara wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(50, 7, 23, '2x2', 800.00, 300, 'length', 'standard', 'Quality Obobo wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(51, 7, 4, '1x6', 1500.00, 300, 'length', 'standard', 'Quality Opoporopo wood available in 1x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(52, 7, 20, '2x6', 2000.00, 300, 'length', 'standard', 'Quality Omo wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(53, 8, 1, '2x12', 4000.00, 300, 'length', 'standard', 'Quality Iroko wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(54, 8, 1, '3x4', 2500.00, 300, 'length', 'standard', 'Quality Iroko wood available in 3x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(55, 8, 1, '1x12', 3500.00, 300, 'length', 'standard', 'Quality Iroko wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(56, 8, 1, '2x6', 2500.00, 300, 'length', 'standard', 'Quality Iroko wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(57, 8, 17, '1x9', 3000.00, 300, 'length', 'standard', 'Quality Gmelina wood available in 1x9 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(58, 9, 1, '2x12', 4000.00, 200, 'length', 'standard', 'Quality Iroko wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(59, 9, 2, '1x12', 2500.00, 200, 'length', 'standard', 'Quality Mahogany wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(60, 9, 3, '2x6', 2000.00, 200, 'length', 'standard', 'Quality Ayunre wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(61, 9, 20, '3x4', 3000.00, 200, 'length', 'standard', 'Quality Omo wood available in 3x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(62, 9, 5, '2x8', 3500.00, 200, 'length', 'standard', 'Quality Ire wood available in 2x8 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(63, 9, 8, '3x6', 4000.00, 200, 'length', 'standard', 'Quality Idigbo wood available in 3x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(64, 9, 4, '2x6', 2500.00, 200, 'length', 'standard', 'Quality Opoporopo wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(65, 10, 1, '2x12', 5000.00, 1000, 'length', 'standard', 'Quality Iroko wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(66, 10, 2, '1x12', 5000.00, 1000, 'length', 'standard', 'Quality Mahogany wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(67, 10, 8, '1x12', 4000.00, 1000, 'length', 'standard', 'Quality Idigbo wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(68, 10, 9, '2x6', 2500.00, 1000, 'length', 'standard', 'Quality Afara wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(69, 10, 10, '2x4', 1200.00, 1000, 'length', 'standard', 'Quality Obeche wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(70, 10, 23, '2x3', 1100.00, 1000, 'length', 'standard', 'Quality Obobo wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(71, 10, 5, '1x12', 4000.00, 1000, 'length', 'standard', 'Quality Rubber wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(72, 10, 3, '2x6', 2500.00, 1000, 'length', 'standard', 'Quality Ayunre wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(73, 11, 1, '2x4', 1200.00, 500, 'length', 'standard', 'Quality Iroko wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(74, 11, 2, '2x3', 1100.00, 500, 'length', 'standard', 'Quality Mahogany wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(75, 11, 19, '3x4', 1200.00, 500, 'length', 'standard', 'Quality Ita wood available in 3x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(76, 11, 10, '2x6', 2500.00, 500, 'length', 'standard', 'Quality Ayo wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(77, 11, 23, '1x12', 3500.00, 500, 'length', 'standard', 'Quality Obobo wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(78, 11, 25, '2x2', 900.00, 500, 'length', 'standard', 'Quality Ipin wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(79, 11, 10, '2x6', 2000.00, 500, 'length', 'standard', 'Quality White wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(80, 11, 20, '2x4', 1500.00, 500, 'length', 'standard', 'Quality Omo wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(81, 12, 10, '2x2', 800.00, 800, 'length', 'standard', 'Quality Awa wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(82, 12, 19, '2x3', 1000.00, 800, 'length', 'standard', 'Quality Ita wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(83, 12, 7, '2x4', 1200.00, 800, 'length', 'standard', 'Quality Ogungun wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(84, 12, 3, '2x6', 2400.00, 800, 'length', 'standard', 'Quality Ayunre wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(85, 12, 8, '2x12', 15000.00, 800, 'length', 'standard', 'Quality Idigbo wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(86, 12, 10, '2x8', 5000.00, 800, 'length', 'standard', 'Quality White wood available in 2x8 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(87, 12, 35, '1x12', 4800.00, 800, 'length', 'standard', 'Quality Okogbo wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(88, 12, 16, '2x12', 15000.00, 300, 'length', 'standard', 'Quality Teak wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(89, 12, 20, '2x4', 1200.00, 500, 'length', 'standard', 'Quality Omo wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(90, 12, 20, '2x6', 2400.00, 500, 'length', 'standard', 'Quality Omo wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(91, 12, 20, '2x8', 5000.00, 400, 'length', 'standard', 'Quality Omo wood available in 2x8 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(92, 12, 20, '2x12', 15000.00, 300, 'length', 'standard', 'Quality Omo wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(93, 13, 7, '1x12', 2400.00, 700, 'length', 'standard', 'Quality Araba wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(94, 13, 2, '2x12', 9500.00, 350, 'length', 'standard', 'Quality Mahogany wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(95, 13, 3, '1x12', 4000.00, 400, 'length', 'standard', 'Quality Ayunre wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(96, 13, 3, '2x6', 1700.00, 500, 'length', 'standard', 'Quality Ayunre wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(97, 13, 31, '2x6', 2200.00, 500, 'length', 'standard', 'Quality Obi wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(98, 13, 10, '2x6', 1200.00, 600, 'length', 'standard', 'Quality White wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(99, 13, 24, '2x6', 2200.00, 500, 'length', 'standard', 'Quality Ayin wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(100, 13, 23, '2x6', 2000.00, 600, 'length', 'standard', 'Quality Obobo wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(101, 13, 5, '2x6', 2200.00, 500, 'length', 'standard', 'Quality Ire wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(102, 13, 21, '2x6', 2500.00, 700, 'length', 'standard', 'Quality Mansonia wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(103, 14, 9, '2x2', 850.00, 500, 'length', 'standard', 'Quality Afara wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(104, 14, 9, '2x6', 2450.00, 500, 'length', 'standard', 'Quality Afara wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(105, 14, 17, '2x3', 750.00, 500, 'length', 'standard', 'Quality Gmelina wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(106, 14, 26, '2x4', 1200.00, 500, 'length', 'standard', 'Quality African locust bean wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(107, 14, 20, '2x2', 800.00, 800, 'length', 'standard', 'Quality Omo wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(108, 14, 20, '2x8', 5000.00, 500, 'length', 'standard', 'Quality Omo wood available in 2x8 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(109, 14, 20, '2x12', 15500.00, 500, 'length', 'standard', 'Quality Omo wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(110, 14, 20, '1x12', 5000.00, 500, 'length', 'standard', 'Quality Omo wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(111, 14, 3, '2x6', 2400.00, 800, 'length', 'standard', 'Quality Ayunre wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(112, 14, 16, '2x12', 15000.00, 800, 'length', 'standard', 'Quality Teak wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(113, 14, 8, '2x12', 15000.00, 800, 'length', 'standard', 'Quality Idigbo wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(114, 14, 7, '2x4', 1200.00, 800, 'length', 'standard', 'Quality Ogungun wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(115, 14, 19, '2x3', 1000.00, 800, 'length', 'standard', 'Quality Ita wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(116, 14, 10, '2x2', 800.00, 800, 'length', 'standard', 'Quality Awa wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(117, 15, 2, '2x2', 850.00, 350, 'length', 'standard', 'Quality Mahogany wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(118, 15, 2, '2x3', 1000.00, 350, 'length', 'standard', 'Quality Mahogany wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(119, 15, 2, '2x4', 1100.00, 350, 'length', 'standard', 'Quality Mahogany wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(120, 15, 2, '2x6', 2300.00, 350, 'length', 'standard', 'Quality Mahogany wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(121, 15, 17, '2x8', 5000.00, 350, 'length', 'standard', 'Quality Gmelina wood available in 2x8 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(122, 15, 17, '2x12', 15000.00, 350, 'length', 'standard', 'Quality Gmelina wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(123, 15, 16, '1x12', 4800.00, 350, 'length', 'standard', 'Quality Teak wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(124, 15, 10, '2x6', 1700.00, 350, 'length', 'standard', 'Quality Arere wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(125, 15, 28, '2x6', 1200.00, 350, 'length', 'standard', 'Quality Eku wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(126, 15, 27, '2x6', 2100.00, 350, 'length', 'standard', 'Quality Oro wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(127, 15, 1, '2x6', 3000.00, 350, 'length', 'standard', 'Quality Iroko wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(128, 15, 1, '2x12', 6000.00, 350, 'length', 'standard', 'Quality Iroko wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(129, 15, 20, '2x6', 2000.00, 350, 'length', 'standard', 'Quality Omo wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(130, 15, 20, '2x12', 4000.00, 350, 'length', 'standard', 'Quality Omo wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(131, 15, 6, '2x6', 2000.00, 350, 'length', 'standard', 'Quality Opepe wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(132, 15, 6, '2x12', 4000.00, 350, 'length', 'standard', 'Quality Opepe wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(133, 15, 15, '2x6', 2000.00, 350, 'length', 'standard', 'Quality Itara wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(134, 16, 2, '2x6', 1500.00, 300, 'length', 'standard', 'Quality Mahogany wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(135, 16, 2, '2x12', 5000.00, 300, 'length', 'standard', 'Quality Mahogany wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(136, 16, 2, '1x12', 1500.00, 300, 'length', 'standard', 'Quality Mahogany wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(137, 16, 1, '2x6', 1500.00, 300, 'length', 'standard', 'Quality Iroko wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(138, 16, 1, '2x12', 3000.00, 300, 'length', 'standard', 'Quality Iroko wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(139, 16, 1, '1x12', 2000.00, 300, 'length', 'standard', 'Quality Iroko wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(140, 16, 10, '2x6', 800.00, 300, 'length', 'standard', 'Quality Arere wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(141, 16, 10, '2x12', 1600.00, 300, 'length', 'standard', 'Quality Arere wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(142, 16, 10, '1x12', 1000.00, 300, 'length', 'standard', 'Quality Arere wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(143, 17, 3, '2x12', 7000.00, 300, 'length', 'standard', 'Quality Ayunre wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(144, 17, 3, '2x6', 3500.00, 300, 'length', 'standard', 'Quality Ayunre wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(145, 17, 3, '2x3', 1200.00, 300, 'length', 'standard', 'Quality Ayunre wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(146, 17, 3, '2x4', 1500.00, 300, 'length', 'standard', 'Quality Ayunre wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(147, 17, 3, '2x2', 800.00, 300, 'length', 'standard', 'Quality Ayunre wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(148, 17, 1, '2x12', 4000.00, 300, 'length', 'standard', 'Quality Iroko wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(149, 17, 1, '2x6', 3500.00, 300, 'length', 'standard', 'Quality Iroko wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(150, 17, 1, '2x3', 1200.00, 300, 'length', 'standard', 'Quality Iroko wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(151, 17, 1, '2x4', 1500.00, 300, 'length', 'standard', 'Quality Iroko wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(152, 17, 1, '2x2', 800.00, 300, 'length', 'standard', 'Quality Iroko wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(153, 17, 1, '3x8', 4000.00, 300, 'length', 'standard', 'Quality Iroko wood available in 3x8 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(154, 17, 1, '3x4', 2000.00, 300, 'length', 'standard', 'Quality Iroko wood available in 3x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(155, 17, 19, '2x3', 1200.00, 300, 'length', 'standard', 'Quality Ika wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(156, 17, 23, '2x4', 1500.00, 300, 'length', 'standard', 'Quality Obobo wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(157, 17, 20, '2x12', 4000.00, 300, 'length', 'standard', 'Quality Odundun wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(158, 17, 20, '2x6', 3500.00, 300, 'length', 'standard', 'Quality Odundun wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(159, 17, 20, '2x3', 1200.00, 300, 'length', 'standard', 'Quality Odundun wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(160, 17, 20, '2x4', 1500.00, 300, 'length', 'standard', 'Quality Odundun wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(161, 17, 20, '2x2', 800.00, 300, 'length', 'standard', 'Quality Odundun wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(162, 17, 20, '3x8', 4000.00, 300, 'length', 'standard', 'Quality Odundun wood available in 3x8 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(163, 17, 20, '3x4', 2000.00, 300, 'length', 'standard', 'Quality Odundun wood available in 3x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(164, 17, 5, '3x4', 2000.00, 350, 'length', 'standard', 'Quality Ire wood available in 3x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(165, 17, 21, '3x8', 4000.00, 300, 'length', 'standard', 'Quality Mansonia wood available in 3x8 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(166, 18, 3, '1x12', 1000.00, 1000, 'length', 'standard', 'Quality Ayunre wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(167, 18, 12, '2x12', 2000.00, 1000, 'length', 'standard', 'Quality Abura wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(168, 18, 2, '2x6', 3000.00, 500, 'length', 'standard', 'Quality Mahogany wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(169, 18, 9, '2x6', 2500.00, 500, 'length', 'standard', 'Quality Afara wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(170, 18, 1, '2x6', 2800.00, 500, 'length', 'standard', 'Quality Iroko wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(171, 18, 15, '2x6', 3000.00, 500, 'length', 'standard', 'Quality Itara wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(172, 19, 12, '2x6', 3000.00, 1000, 'length', 'standard', 'Quality Abora wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(173, 19, 1, '2x12', 10000.00, 1000, 'length', 'standard', 'Quality Iroko wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(174, 19, 3, '2x6', 3000.00, 1000, 'length', 'standard', 'Quality Ayunre wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(175, 19, 8, '1x12', 7000.00, 1000, 'length', 'standard', 'Quality Black afara wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(176, 19, 25, '2x6', 3000.00, 1000, 'length', 'standard', 'Quality Ipin wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(177, 19, 15, '2x6', 3000.00, 1000, 'length', 'standard', 'Quality Itara wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(178, 19, 5, '2x6', 3000.00, 1000, 'length', 'standard', 'Quality Ire wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(179, 19, 24, '2x6', 3000.00, 1000, 'length', 'standard', 'Quality Ayin wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(180, 19, 28, '2x12', 3000.00, 1000, 'length', 'standard', 'Quality Eku wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(181, 20, 3, '2x6', 3000.00, 1200, 'length', 'standard', 'Quality Ayunre wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(182, 20, 15, '2x12', 6000.00, 8000, 'length', 'standard', 'Quality Itara wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(183, 20, 1, '1x6', 7000.00, 1000, 'length', 'standard', 'Quality Iroko wood available in 1x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(184, 20, 7, '1x12', 4500.00, 900, 'length', 'standard', 'Quality Somi wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(185, 20, 5, '3x4', 8000.00, 1000, 'length', 'standard', 'Quality Ire wood available in 3x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(186, 20, 5, '1x12', 12000.00, 600, 'length', 'standard', 'Quality Ire wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(187, 20, 2, '3x6', 12000.00, 800, 'length', 'standard', 'Quality Mahogany wood available in 3x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(188, 20, 2, '1x12', 13000.00, 500, 'length', 'standard', 'Quality Mahogany wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(189, 20, 35, '2x3', 2000.00, 800, 'length', 'standard', 'Quality Koko igbo wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(190, 20, 10, '2x4', 2000.00, 1500, 'length', 'standard', 'Quality Ogbabi wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(191, 20, 10, '2x12', 9500.00, 1000, 'length', 'standard', 'Quality Arere wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(192, 20, 9, '1x12', 12000.00, 300, 'length', 'standard', 'Quality Afara wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(193, 20, 29, '2x6', 3500.00, 1500, 'length', 'standard', 'Quality Ogbus wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(194, 20, 31, '2x12', 8000.00, 750, 'length', 'standard', 'Quality Obi wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(195, 20, 20, '1x12', 13500.00, 300, 'length', 'standard', 'Quality Omo wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(196, 20, 10, '2x12', 9500.00, 250, 'length', 'standard', 'Quality Ayo wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(197, 21, 15, '2x4', 1700.00, 2000, 'length', 'standard', 'Quality Itara wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(198, 21, 15, '2x6', 3000.00, 1000, 'length', 'standard', 'Quality Itara wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(199, 21, 32, '2x6', 3400.00, 500, 'length', 'standard', 'Quality Akun wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(200, 21, 3, '1x12', 7000.00, 500, 'length', 'standard', 'Quality Ayunre wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(201, 21, 3, '2x6', 3000.00, 300, 'length', 'standard', 'Quality Ayunre wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(202, 21, 7, '1x12', 3200.00, 1200, 'length', 'standard', 'Quality Somi wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(203, 21, 7, '2x6', 3000.00, 300, 'length', 'standard', 'Quality Araba wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(204, 21, 28, '2x3', 1500.00, 800, 'length', 'standard', 'Quality Eku wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(205, 21, 28, '1x12', 7000.00, 500, 'length', 'standard', 'Quality Eku wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(206, 21, 9, '1x12', 7000.00, 500, 'length', 'standard', 'Quality Afara wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(207, 21, 24, '3x4', 3700.00, 1000, 'length', 'standard', 'Quality Orin dudu wood available in 3x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(208, 21, 29, '2x12', 5500.00, 400, 'length', 'standard', 'Quality Ogbus wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(209, 21, 1, '2x6', 2800.00, 500, 'length', 'standard', 'Quality Irokogbo wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(210, 21, 12, '2x6', 3000.00, 500, 'length', 'standard', 'Quality Abora wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(211, 21, 5, '2x6', 3000.00, 500, 'length', 'standard', 'Quality Ire wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(212, 21, 1, '2x6', 3000.00, 500, 'length', 'standard', 'Quality Iroko wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(213, 21, 24, '2x6', 3000.00, 500, 'length', 'standard', 'Quality Ayin wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(214, 22, 2, '2x12', 6000.00, 250, 'length', 'standard', 'Quality Mahogany wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(215, 22, 3, '1x12', 4500.00, 350, 'length', 'standard', 'Quality Ayunre wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(216, 22, 1, '2x4', 2000.00, 350, 'length', 'standard', 'Quality Iroko wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(217, 22, 1, '2x6', 2450.00, 200, 'length', 'standard', 'Quality Iroko wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(218, 22, 17, '2x3', 1500.00, 750, 'length', 'standard', 'Quality Gmelina wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(219, 22, 24, '4x6', 4000.00, 550, 'length', 'standard', 'Quality Ayin wood available in 4x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(220, 22, 37, '3x4', 2450.00, 400, 'length', 'standard', 'Quality Casia wood available in 3x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(221, 22, 16, '2x4', 1750.00, 200, 'length', 'standard', 'Quality Teak wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(222, 23, 1, '2x12', 7000.00, 300, 'length', 'standard', 'Quality Iroko wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(223, 23, 9, '3x8', 4000.00, 300, 'length', 'standard', 'Quality Afara wood available in 3x8 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(224, 23, 8, '2x6', 4000.00, 300, 'length', 'standard', 'Quality Idigbo wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(225, 23, 17, '2x3', 900.00, 300, 'length', 'standard', 'Quality Gmelina wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(226, 23, 3, '2x4', 1000.00, 300, 'length', 'standard', 'Quality Ayunre wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(227, 23, 7, '1x12', 2000.00, 300, 'length', 'standard', 'Quality Araba wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(228, 23, 5, '2x2', 800.00, 300, 'length', 'standard', 'Quality Ire wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(229, 23, 10, 'Tie rod', 250.00, 500, 'length', 'standard', 'Quality Tie rod available', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(230, 23, 19, '2x6', 1500.00, 300, 'length', 'standard', 'Quality Ita wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(231, 23, 2, '2x12', 6000.00, 300, 'length', 'standard', 'Quality Mahogany wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(232, 24, 2, '3x4', 2400.00, 200, 'length', 'standard', 'Quality Mahogany wood available in 3x4 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(233, 24, 1, '2x12', 4800.00, 200, 'length', 'standard', 'Quality Iroko wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(234, 24, 16, '3x8', 2400.00, 200, 'length', 'standard', 'Quality Teak wood available in 3x8 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(235, 24, 17, '2x3', 1000.00, 200, 'length', 'standard', 'Quality Gmelina wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(236, 24, 20, '2x2', 900.00, 255, 'length', 'standard', 'Quality Omo wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(237, 24, 8, '1x12', 1500.00, 200, 'length', 'standard', 'Quality Idigbo wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(238, 24, 7, '2x4', 1400.00, 200, 'length', 'standard', 'Quality Araba wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(239, 24, 8, '1x12', 1500.00, 200, 'length', 'standard', 'Quality Idigbo wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(240, 24, 3, '2x6', 2000.00, 200, 'length', 'standard', 'Quality Ayunre wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(241, 24, 33, '2x4', 1500.00, 200, 'length', 'standard', 'Quality Iyeye wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(242, 25, 1, '2x12', 5000.00, 100, 'length', 'standard', 'Quality Iroko wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(243, 25, 2, '2x4', 2500.00, 1000, 'length', 'standard', 'Quality Mahogany wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(244, 25, 10, '2x6', 2500.00, 700, 'length', 'standard', 'Quality Obeche wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(245, 25, 3, '1x12', 3500.00, 200, 'length', 'standard', 'Quality Ayunre wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(246, 25, 3, '2x4', 1500.00, 300, 'length', 'standard', 'Quality Ayunre wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(247, 25, 7, '2x3', 1500.00, 200, 'length', 'standard', 'Quality Araba wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(248, 25, 5, '2x2', 1000.00, 150, 'length', 'standard', 'Quality Ire wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(249, 25, 9, '2x4', 5000.00, 300, 'length', 'standard', 'Quality Afara wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(250, 25, 21, '2x6', 3000.00, 200, 'length', 'standard', 'Quality Mansonia wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(251, 26, 1, '2x12', 4800.00, 500, 'length', 'standard', 'Quality Iroko wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(252, 26, 1, '2x12', 8000.00, 500, 'length', 'standard', 'Quality Iroko wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(253, 26, 2, '3x4', 4800.00, 500, 'length', 'standard', 'Quality Mahogany wood available in 3x4 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(254, 26, 2, '2x12', 6000.00, 500, 'length', 'standard', 'Quality Mahogany wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(255, 26, 17, '2x3', 1000.00, 500, 'length', 'standard', 'Quality Gmelina wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(256, 26, 20, '2x2', 900.00, 500, 'length', 'standard', 'Quality Omo wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(257, 26, 4, '2x12', 4500.00, 500, 'length', 'standard', 'Quality Ole wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(258, 26, 8, '1x12', 1500.00, 500, 'length', 'standard', 'Quality Idigbo wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(259, 26, 7, '2x6', 2000.00, 500, 'length', 'standard', 'Quality Araba wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(260, 26, 16, '3x8', 2400.00, 500, 'length', 'standard', 'Quality Teak wood available in 3x8 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(261, 26, 10, '1x12', 2400.00, 500, 'length', 'standard', 'Quality Obeche wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(262, 26, 19, '2x12', 4200.00, 500, 'length', 'standard', 'Quality Ita wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(263, 26, 7, '2x4', 1500.00, 500, 'length', 'standard', 'Quality Araba wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(264, 26, 3, '2x6', 2000.00, 500, 'length', 'standard', 'Quality Ayunre wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(265, 26, 33, '2x4', 1500.00, 500, 'length', 'standard', 'Quality Iyeye wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(266, 27, 2, '3x8', 4800.00, 500, 'length', 'standard', 'Quality Mahogany wood available in 3x8 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(267, 27, 2, '2x12', 6000.00, 500, 'length', 'standard', 'Quality Mahogany wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(268, 27, 20, '2x3', 1000.00, 500, 'length', 'standard', 'Quality Omo wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(269, 27, 20, '2x2', 900.00, 500, 'length', 'standard', 'Quality Omo wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(270, 27, 3, '2x12', 5000.00, 500, 'length', 'standard', 'Quality Ayunre wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(271, 27, 1, '2x12', 4800.00, 500, 'length', 'standard', 'Quality Iroko wood available in 2x12 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(272, 27, 16, '2x4', 1750.00, 500, 'length', 'standard', 'Quality Teak wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(273, 27, 16, '3x4', 2400.00, 500, 'length', 'standard', 'Quality Teak wood available in 3x4 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(274, 27, 8, '2x4', 1500.00, 500, 'length', 'standard', 'Quality Idigbo wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(275, 27, 8, '2x6', 2000.00, 500, 'length', 'standard', 'Quality Idigbo wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(276, 27, 31, '2x4', 1000.00, 500, 'length', 'standard', 'Quality Obi wood available in 2x4 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(277, 27, 5, '2x3', 900.00, 500, 'length', 'standard', 'Quality Ire wood available in 2x3 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(278, 27, 9, '2x2', 700.00, 500, 'length', 'standard', 'Quality Afara wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(279, 27, 7, '3x4', 2000.00, 500, 'length', 'standard', 'Quality Araba wood available in 3x4 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(280, 27, 34, '3x8', 4000.00, 500, 'length', 'standard', 'Quality Iya wood available in 3x8 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(281, 27, 19, '2x6', 1500.00, 500, 'length', 'standard', 'Quality Ita wood available in 2x6 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(282, 27, 17, '2x2', 900.00, 500, 'length', 'standard', 'Quality Gmelina wood available in 2x2 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
(283, 27, 4, '1x12', 4500.00, 500, 'length', 'standard', 'Quality Ole wood available in 1x12 dimensions', NULL, 1, '2025-10-30 15:53:30', '2025-10-30 15:53:30');

-- --------------------------------------------------------

--
-- Table structure for table `marketers`
--

CREATE TABLE `marketers` (
  `id` int(11) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` enum('Ondo','Ekiti','Osun','Oyo','Ogun','Lagos') NOT NULL,
  `local_government` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `business_description` text DEFAULT NULL,
  `verification_status` enum('pending','verified','rejected') DEFAULT 'verified',
  `verification_notes` text DEFAULT NULL,
  `profile_image` varchar(500) DEFAULT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_expires` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `marketers`
--

INSERT INTO `marketers` (`id`, `business_name`, `owner_name`, `address`, `city`, `state`, `local_government`, `phone`, `email`, `password_hash`, `business_description`, `verification_status`, `verification_notes`, `profile_image`, `registration_date`, `last_login`, `is_active`, `reset_token`, `reset_expires`) VALUES
(1, 'Orisun Ayo Planks Shield', 'Proprietor', 'Adjacent Okiki Jesu filling Station, Oda-road, Akure Ondo State', 'Akure', 'Ondo', 'Akure South', '08034227144', 'orisunayo@woodconnect.com.ng', '$2y$10$VW6ovsbOg4qmzKd9ESy10eY9eNq01OA7RzBJpIrya.ofHMg.6g.ui', NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', '2025-10-31 04:12:50', 1, NULL, NULL),
(2, 'Asejere (Adelanke) Plank Seller Shield', 'Proprietor', 'Road block Ibadan-road, Akure South local Government, Ondo State', 'Akure', 'Ondo', 'Akure South', '07025685033', 'asejere@woodconnect.com.ng', '$2y$10$VW6ovsbOg4qmzKd9ESy10eY9eNq01OA7RzBJpIrya.ofHMg.6g.ui', NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(3, 'Love and Peace Plank Market', 'Proprietor', 'Ayeni 2, custom Ondo-road, Akure South local Government Ondo State', 'Akure', 'Ondo', 'Akure South', '08101133721', 'lovepeace@woodconnect.com.ng', '$2y$10$VW6ovsbOg4qmzKd9ESy10eY9eNq01OA7RzBJpIrya.ofHMg.6g.ui', NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(4, 'WeliWeli / Anu Oluwapo Plank Marketer', 'Proprietor', 'Oda road Power line, Akure South local Government, Akure Ondo State', 'Akure', 'Ondo', 'Akure South', '08107215335', 'weliweli@woodconnect.com.ng', '$2y$10$VW6ovsbOg4qmzKd9ESy10eY9eNq01OA7RzBJpIrya.ofHMg.6g.ui', NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(5, 'God\'s Grace Plank Seller', 'Proprietor', 'St Luke planks market, Akure South local Government, Ondo State', 'Akure', 'Ondo', 'Akure South', '08167170106', 'godsgrace@woodconnect.com.ng', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(6, 'Mama Aduke Oladele Planks Market', 'Mama Aduke Oladele', 'Ewenla Planks Market, Ado Local Government', 'Ado Ekiti', 'Ekiti', 'Ado Ekiti', '08103123420', 'mamaaduke@woodconnect.com.ng', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(7, 'Egbewa Planks Market Association', 'Association Chairman', 'Egbewa Planks Market, Ado Local Government', 'Ado Ekiti', 'Ekiti', 'Ado Ekiti', '08012345671', 'egbewa@woodconnect.com.ng', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(8, 'Egbewa/Idiagba Planks Association', 'Association Secretary', 'Egbewa Planks Market, Ado Local Government', 'Ado Ekiti', 'Ekiti', 'Ado Ekiti', '08012345672', 'idiagba@woodconnect.com.ng', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(9, 'Egbewa/Idiagba-ese osi Planks Association', 'Association President', 'Egbewa plank Market, Ado Local Government', 'Ado Ekiti', 'Ekiti', 'Ado Ekiti', '08012345673', 'idiagbaosi@woodconnect.com.ng', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(10, 'Egbewa Planks Market Main', 'Market Leader', 'Egbewa Planks market, Ado-ekiti', 'Ado Ekiti', 'Ekiti', 'Ado Ekiti', '08012345674', 'egbewamain@woodconnect.com.ng', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(11, 'Egbewa Planks Market Additional', 'Market Leader', 'Egbewa Planks Market, Ado Local Government', 'Ado Ekiti', 'Ekiti', 'Ado Ekiti', '08012345675', 'egbewaadditional@woodconnect.com.ng', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(12, 'Ademola Oluwaseun Planks Seller', 'Ademola Oluwaseun', 'Orisumbare Planks Market, Bodija Isopako Ibadan North LG Oyo State', 'Ibadan', 'Oyo', 'Ibadan North', '08030569398', 'ademola@woodconnect.com.ng', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(13, 'Orisumbare Planks Market Zone A', 'Market Leader', 'Orisumbare Planks Market, Bodija Isopako Ibadan North LG Oyo State', 'Ibadan', 'Oyo', 'Ibadan North', '08012345676', 'zonea@woodconnect.com.ng', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(14, 'Orisumbare Planks Market Zone B', 'Market Leader', 'Orisumbare Planks Market, Bodija Isopako Ibadan North LG Oyo State', 'Ibadan', 'Oyo', 'Ibadan North', '08012345677', 'zoneb@woodconnect.com.ng', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(15, 'Orisumbare Planks Market Zone C', 'Market Leader', 'Orisumbare Planks Market, Bodija Isopako Ibadan North LG Oyo State', 'Ibadan', 'Oyo', 'Ibadan North', '08012345678', 'zonec@woodconnect.com.ng', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(16, 'Orisumbare Planks Market Zone D', 'Market Leader', 'Orisumbare Planks Market, Bodija Isopako Ibadan North LG Oyo State', 'Ibadan', 'Oyo', 'Ibadan North', '08012345679', 'zoned@woodconnect.com.ng', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(17, 'Orisumbare Planks Market Zone 5', 'Market Leader', 'Zone 5, Orisumbare Planks Market, Bodija Isopako Ibadan North LG Oyo State', 'Ibadan', 'Oyo', 'Ibadan North', '08012345680', 'zone5@woodconnect.com.ng', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(18, 'Alhaji Hon. Tajudeen Karem Timber Hub', 'Alhaji Hon. Tajudeen Karem', 'Akin Ogungbile market, Alimosho Local Government, Lagos State', 'Lagos', 'Lagos', 'Alimosho', '08056568406', 'tajudeen@woodconnect.com.ng', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(19, 'Alaga Planks Market', 'Proprietor', 'Orisumbare plank market Idimu, Alimosho Local Government, Lagos', 'Lagos', 'Lagos', 'Alimosho', '08024234320', 'alaga@woodconnect.com.ng', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(20, 'Orisumbare Planks Market Lagos A', 'Market Leader', 'Orisumbare plank market Alimosho Local Government, Lagos', 'Lagos', 'Lagos', 'Alimosho', '08012345681', 'lagosa@woodconnect.com.ng', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(21, 'Orisumbare Planks Market Lagos B', 'Market Leader', 'Orisumbare plank market Idimu, Alimosho Local Government, Lagos', 'Lagos', 'Lagos', 'Alimosho', '08012345682', 'lagosb@woodconnect.com.ng', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(22, 'Alaga Eyenkorin Planks Seller', 'Proprietor', 'Akorede Central Planks Market, Egbedore local Government, Osun State', 'Osogbo', 'Osun', 'Egbedore', '08081897595', 'eyenkorin@woodconnect.com.ng', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(23, 'Alhaji Semiu Sawmill and Planks Seller', 'Alhaji Semiu', 'Akorede Market, Ido Osun, Egbedore local Government, Osun State', 'Osogbo', 'Osun', 'Egbedore', '08063033622', 'semiu@woodconnect.com.ng', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(24, 'Oga Owokunle Jomaicha Planks Seller', 'Oga Owokunle', 'Ibuaje Plank Market, Osogbo, Osun State', 'Osogbo', 'Osun', 'Osogbo', '08069477877', 'owokunle@woodconnect.com.ng', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(25, 'Alaga Jomaicha Planks Seller', 'Proprietor', 'Ibuaje Plank Market, Oke ayepe, Osogbo, Osun State', 'Osogbo', 'Osun', 'Osogbo', '08065385021', 'jomaicha@woodconnect.com.ng', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(26, 'Baba Adeyemo Alekuwodo Planks Market', 'Baba Adeyemo', 'Akorede Plank Market, Egbedore local Government, Osogbo, Osun State', 'Osogbo', 'Osun', 'Egbedore', '08034960040', 'adeyemo@timber.com', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL),
(27, 'Ogbeni Oke Planks Seller', 'Ogbeni Oke', 'Ibuaje Sawnswery Association Plank Market, Osogbo Local Govt, Osogbo Osun State', 'Osogbo', 'Osun', 'Osogbo', '08038205470', 'ogbeni@timber.com', NULL, NULL, 'verified', NULL, NULL, '2025-10-30 15:53:29', NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `platform_stats`
--

CREATE TABLE `platform_stats` (
  `id` int(11) NOT NULL,
  `total_marketers` int(11) DEFAULT 0,
  `verified_marketers` int(11) DEFAULT 0,
  `total_species` int(11) DEFAULT 0,
  `active_listings` int(11) DEFAULT 0,
  `total_inquiries` int(11) DEFAULT 0,
  `completed_inquiries` int(11) DEFAULT 0,
  `stat_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `platform_stats`
--

INSERT INTO `platform_stats` (`id`, `total_marketers`, `verified_marketers`, `total_species`, `active_listings`, `total_inquiries`, `completed_inquiries`, `stat_date`, `created_at`) VALUES
(1, 27, 27, 37, 283, 0, 0, '2025-10-30', '2025-10-30 15:53:30');

-- --------------------------------------------------------

--
-- Table structure for table `species`
--

CREATE TABLE `species` (
  `id` int(11) NOT NULL,
  `scientific_name` varchar(255) NOT NULL,
  `common_names` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`common_names`)),
  `family` varchar(100) DEFAULT NULL,
  `density_range` varchar(100) DEFAULT NULL,
  `durability` varchar(50) DEFAULT NULL,
  `timber_value_rank` int(11) DEFAULT 1,
  `common_uses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`common_uses`)),
  `description` text DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `species`
--

INSERT INTO `species` (`id`, `scientific_name`, `common_names`, `family`, `density_range`, `durability`, `timber_value_rank`, `common_uses`, `description`, `image_path`, `created_at`, `updated_at`) VALUES
(1, 'Milicia excelsa', '[\"Iroko\", \"Efo pupa\"]', 'Moraceae', '640-720 kg/m³', 'Very Durable', 1, '[\"Furniture\", \"Flooring\", \"Construction\", \"Boats\"]', 'Iroko is a large hardwood tree from the west coast of tropical Africa. It is one of the woods sometimes referred to as African Teak.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(2, 'Khaya ivorensis', '[\"Mahogany\", \"African Mahogany\"]', 'Meliaceae', '540-670 kg/m³', 'Durable', 2, '[\"Furniture\", \"Cabinetry\", \"Boat Building\", \"Veneer\"]', 'African Mahogany is a medium-sized tree native to tropical Africa. The wood is pinkish-brown and known for its workability.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(3, 'Albizia zygia', '[\"Ayunre\", \"Ayinre\"]', 'Fabaceae', '560-640 kg/m³', 'Moderately Durable', 3, '[\"Furniture\", \"Construction\", \"Plywood\"]', 'Commonly used for furniture and construction in West Africa.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(4, 'Pterocarpus osun', '[\"Sonu\", \"Opoporopo\", \"Ole\"]', 'Fabaceae', '700-800 kg/m³', 'Durable', 2, '[\"Furniture\", \"Flooring\", \"Carving\"]', 'Known for its durability and attractive grain pattern.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(5, 'Funtumia elastica', '[\"Ire\", \"Rubber tree\"]', 'Apocynaceae', '450-550 kg/m³', 'Moderately Durable', 3, '[\"Furniture\", \"Plywood\", \"Packaging\"]', 'Lightweight timber used for various wood products.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(6, 'Nauclea diderrichii', '[\"Opepe\", \"Bilinga\"]', 'Rubiaceae', '750-850 kg/m³', 'Very Durable', 1, '[\"Heavy Construction\", \"Bridges\", \"Docks\"]', 'Known for its strength and durability in heavy construction.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(7, 'Ceiba pentandra', '[\"Araba\", \"Somi\", \"Ogungun\", \"White wood\"]', 'Malvaceae', '300-400 kg/m³', 'Perishable', 4, '[\"Plywood\", \"Packaging\", \"Light Construction\"]', 'Very lightweight wood, easy to work with but not durable.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(8, 'Terminalia ivorensis', '[\"Idigbo\", \"Black Afara\"]', 'Combretaceae', '560-640 kg/m³', 'Durable', 2, '[\"Furniture\", \"Flooring\", \"Construction\"]', 'Medium-weight timber with good durability.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(9, 'Terminalia superba', '[\"Afara\", \"White Afara\", \"Limba\"]', 'Combretaceae', '500-580 kg/m³', 'Moderately Durable', 3, '[\"Veneer\", \"Plywood\", \"Furniture\"]', 'Light-colored wood used for veneer and furniture.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(10, 'Triplochiton scleroxylon', '[\"Obeche\", \"Arere\", \"Awa\", \"Ogbogbo\"]', 'Malvaceae', '380-450 kg/m³', 'Perishable', 4, '[\"Plywood\", \"Model Making\", \"Packaging\"]', 'Very lightweight and easy to carve.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(11, 'Cordia platythyrsa', '[\"Opoporopo\"]', 'Boraginaceae', '520-600 kg/m³', 'Moderately Durable', 3, '[\"Furniture\", \"Cabinetry\"]', 'Attractive wood with good workability.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(12, 'Hallea ciliata', '[\"Abora\", \"Abura\"]', 'Rubiaceae', '550-650 kg/m³', 'Moderately Durable', 3, '[\"Furniture\", \"Construction\"]', 'Medium-weight timber for general construction.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(13, 'Afzelia africana', '[\"Koko igbo\", \"Kokogbo\", \"Apa\"]', 'Fabaceae', '800-900 kg/m³', 'Very Durable', 1, '[\"Heavy Construction\", \"Bridges\", \"Flooring\"]', 'Extremely durable and heavy wood.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(14, 'Piptadeniastrum africanum', '[\"Ogbabi\", \"Dahoma\"]', 'Fabaceae', '680-780 kg/m³', 'Durable', 2, '[\"Construction\", \"Flooring\"]', 'Strong and durable construction timber.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(15, 'Sacoglottis gabonensis', '[\"Itara\"]', 'Humiriaceae', '850-950 kg/m³', 'Very Durable', 1, '[\"Heavy Construction\", \"Marine Work\"]', 'Extremely dense and durable wood.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(16, 'Tectona grandis', '[\"Teak\"]', 'Lamiaceae', '630-720 kg/m³', 'Extremely Durable', 1, '[\"Outdoor Furniture\", \"Decking\", \"Shipbuilding\"]', 'Premium timber known for exceptional durability.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(17, 'Gmelina arborea', '[\"Gmelina\", \"Beechwood\"]', 'Lamiaceae', '430-510 kg/m³', 'Moderately Durable', 3, '[\"Plywood\", \"Furniture\", \"Pulpwood\"]', 'Fast-growing timber for various uses.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(18, 'Balanites aegyptiaca', '[\"Payapayaba\"]', 'Zygophyllaceae', '850-950 kg/m³', 'Very Durable', 1, '[\"Construction\", \"Tool Handles\"]', 'Very hard and durable wood.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(19, 'Celtis mildbraedii', '[\"Ita\"]', 'Cannabaceae', '620-720 kg/m³', 'Durable', 2, '[\"Furniture\", \"Construction\"]', 'Good quality timber for furniture.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(20, 'Cordia millenii', '[\"Omo\"]', 'Boraginaceae', '520-600 kg/m³', 'Moderately Durable', 3, '[\"Furniture\", \"Cabinetry\"]', 'Attractive wood for fine furniture.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(21, 'Mansonia altissima', '[\"Mansonia\"]', 'Malvaceae', '560-640 kg/m³', 'Durable', 2, '[\"Furniture\", \"Flooring\"]', 'Medium-weight timber with good properties.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(22, 'Alstonia boonei', '[\"Orunmodun\", \"Pattern wood\"]', 'Apocynaceae', '420-480 kg/m³', 'Perishable', 4, '[\"Plywood\", \"Matchsticks\"]', 'Lightweight wood for specific uses.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(23, 'Guarea thompsonii', '[\"Obobo\", \"Black Guarea\"]', 'Meliaceae', '580-680 kg/m³', 'Durable', 2, '[\"Furniture\", \"Cabinetry\"]', 'Good quality timber for interior work.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(24, 'Anogeissus leiocarpus', '[\"Ayin\", \"Orin dudu\"]', 'Combretaceae', '900-1000 kg/m³', 'Very Durable', 1, '[\"Heavy Construction\", \"Tool Handles\"]', 'Extremely hard and durable wood.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(25, 'Ficus exasperata', '[\"Ipin\"]', 'Moraceae', '450-550 kg/m³', 'Perishable', 4, '[\"Plywood\", \"Packaging\"]', 'Lightweight wood for temporary uses.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(26, 'Parkia biglobosa', '[\"African locust bean\"]', 'Fabaceae', '700-800 kg/m³', 'Durable', 2, '[\"Construction\", \"Furniture\"]', 'Durable timber from the locust bean tree.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(27, 'Irvingia gabonensis', '[\"Oro\", \"Bush mango\"]', 'Irvingiaceae', '750-850 kg/m³', 'Durable', 2, '[\"Construction\", \"Furniture\"]', 'Hardwood from the bush mango tree.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(28, 'Brachystegia spp', '[\"Eku\"]', 'Fabaceae', '680-780 kg/m³', 'Durable', 2, '[\"Construction\", \"Flooring\"]', 'Durable African hardwood.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(29, 'Cola gigantea', '[\"Ogbus\"]', 'Malvaceae', '600-700 kg/m³', 'Moderately Durable', 3, '[\"Furniture\", \"Construction\"]', 'Medium-weight timber.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(30, 'Cola nitida', '[\"Obi\", \"Kola\"]', 'Malvaceae', '580-680 kg/m³', 'Moderately Durable', 3, '[\"Furniture\", \"Construction\"]', 'Timber from kola nut tree.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(31, 'Uapaca guineensis', '[\"Akun\"]', 'Phyllanthaceae', '750-850 kg/m³', 'Durable', 2, '[\"Construction\", \"Tool Handles\"]', 'Durable African hardwood.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(32, 'Spondias mombin', '[\"Iyeye\", \"Yellow mombin\"]', 'Anacardiaceae', '520-620 kg/m³', 'Moderately Durable', 3, '[\"Furniture\", \"Construction\"]', 'Medium-weight fruit tree timber.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(33, 'Daniellia oliveri', '[\"Iya\", \"African copaiba\"]', 'Fabaceae', '480-580 kg/m³', 'Moderately Durable', 3, '[\"Furniture\", \"Plywood\"]', 'Light to medium weight timber.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(34, 'Entandrophragma cylindricum', '[\"Okogbo\", \"Sapele\"]', 'Meliaceae', '620-720 kg/m³', 'Durable', 2, '[\"Furniture\", \"Veneer\"]', 'High-quality furniture timber.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(35, 'Lovoa trichilioides', '[\"Koko igbo\", \"African walnut\"]', 'Meliaceae', '520-620 kg/m³', 'Durable', 2, '[\"Furniture\", \"Cabinetry\"]', 'Beautiful wood for fine furniture.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(36, 'Lophira alata', '[\"Ekki\"]', 'Ochnaceae', '1050-1150 kg/m³', 'Extremely Durable', 1, '[\"Heavy Construction\", \"Marine Work\", \"Bridges\"]', 'Extremely dense and durable wood, resistant to termites.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
(37, 'Casia fistula', '[\"Casia\"]', 'Fabaceae', '700-800 kg/m³', 'Durable', 2, '[\"Furniture\", \"Construction\"]', 'Durable timber with good workability.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `species_id` (`species_id`),
  ADD KEY `idx_marketer_status` (`marketer_id`,`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `species_id` (`species_id`),
  ADD KEY `idx_marketer_species` (`marketer_id`,`species_id`),
  ADD KEY `idx_dimensions` (`dimensions`),
  ADD KEY `idx_price` (`price_per_unit`),
  ADD KEY `idx_availability` (`is_available`);

--
-- Indexes for table `marketers`
--
ALTER TABLE `marketers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_phone` (`phone`),
  ADD KEY `idx_state_city` (`state`,`city`),
  ADD KEY `idx_verification_status` (`verification_status`),
  ADD KEY `idx_business_name` (`business_name`);

--
-- Indexes for table `platform_stats`
--
ALTER TABLE `platform_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stat_date` (`stat_date`);

--
-- Indexes for table `species`
--
ALTER TABLE `species`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `scientific_name` (`scientific_name`),
  ADD KEY `idx_scientific_name` (`scientific_name`),
  ADD KEY `idx_durability` (`durability`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=284;

--
-- AUTO_INCREMENT for table `marketers`
--
ALTER TABLE `marketers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `platform_stats`
--
ALTER TABLE `platform_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `species`
--
ALTER TABLE `species`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`marketer_id`) REFERENCES `marketers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inquiries_ibfk_2` FOREIGN KEY (`species_id`) REFERENCES `species` (`id`);

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`marketer_id`) REFERENCES `marketers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`species_id`) REFERENCES `species` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
