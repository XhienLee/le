-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 24, 2025 at 10:54 AM
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
-- Database: `cybersense`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `adminId` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`adminId`, `full_name`, `email`, `date_of_birth`, `password`, `created_at`, `updated_at`, `created_by`) VALUES
('ADM001', 'Segn Lee Buslon', 'admin@usep.edu.ph', '2004-10-16', '$2y$10$i8FaIuxIb/hrQU6RWQXOmeSW2t4w2Hbl070Mnh0XZdYajGlsht6zS', '2025-04-08 18:42:48', '2025-05-28 16:17:16', NULL),
('ADM002', 'System Admin', 'sysadmin@usep.edu.ph', '2005-10-15', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-08 18:42:48', '2025-05-21 14:16:59', 'ADM001'),
('buslon2004-10-10', 'Cyrel James Birot Buslon', 'cjbbuslon101004@cybersense.com', '2004-10-10', '$2y$10$soFp9yhGAovGWQI2StvNuudXHHJctFWimUOZPyejtGKCMoMnzcjFW', '2025-05-21 14:18:16', '2025-05-21 14:18:16', 'ADM001'),
('estsetses2025-05-09', 'testestest estsetset estsetses', 'teestsetses050925@cybersense.com', '2025-05-09', '$2y$10$29Q2fbRZN1yO.XulIZWFSeOO2RozDTE.EFmYA.4xI.Xq8TsToahk6', '2025-05-21 14:25:14', '2025-05-21 14:25:14', 'ADM001'),
('test2025-05-23', 'testte test test', 'tttest052325@cybersense.com', '2025-05-23', '$2y$10$.QBYRI0m4p5qkKgRjgVbq.w55B8MRl4PQMB9JZqKDooRBTEK1wEA6', '2025-05-23 15:13:47', '2025-05-23 15:13:47', 'ADM001');

-- --------------------------------------------------------

--
-- Table structure for table `authentication_logs`
--

CREATE TABLE `authentication_logs` (
  `logId` varchar(20) NOT NULL,
  `user_type` enum('student','instructor','admin') NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `action` enum('login','logout','failed_attempt','password_reset') NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `authentication_logs`
--

INSERT INTO `authentication_logs` (`logId`, `user_type`, `user_id`, `action`, `ip_address`, `user_agent`, `created_at`) VALUES
('LOG000812', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 05:30:05'),
('LOG005268', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 02:59:10'),
('LOG006050', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 08:45:10'),
('LOG007293', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 02:31:00'),
('LOG012787', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 11:18:42'),
('LOG015284', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 18:29:44'),
('LOG015825', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 05:07:31'),
('LOG018216', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 20:43:06'),
('LOG023582', 'student', 'STU001', 'login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 14:56:10'),
('LOG024979', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 16:50:09'),
('LOG028575', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 20:21:17'),
('LOG029333', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 18:51:36'),
('LOG032228', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 07:39:12'),
('LOG033667', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 18:07:53'),
('LOG035333', 'student', 'STU002', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 05:05:42'),
('LOG039519', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 11:03:34'),
('LOG041143', 'student', 'STU002', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 21:03:44'),
('LOG047100', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 17:58:54'),
('LOG063365', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 03:45:15'),
('LOG074653', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 17:24:36'),
('LOG077852', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 16:17:08'),
('LOG091098', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 18:52:13'),
('LOG099356', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 13:15:20'),
('LOG101009', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 19:04:42'),
('LOG105504', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 18:51:29'),
('LOG126267', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-28 16:17:04'),
('LOG131306', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 09:02:01'),
('LOG134238', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 17:52:14'),
('LOG141151', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 03:48:29'),
('LOG141578', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Linux; Android 11; RMX3171 Build/RP1A.200720.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/136.0.7103.61 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/506.0.0.61.109;]', '2025-05-24 17:51:34'),
('LOG142839', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 05:04:16'),
('LOG142965', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 16:15:44'),
('LOG148664', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 18:53:31'),
('LOG152357', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 21:23:36'),
('LOG152463', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 03:21:23'),
('LOG165870', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 18:24:26'),
('LOG167413', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 17:26:29'),
('LOG170962', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 19:08:44'),
('LOG181143', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 03:06:33'),
('LOG181852', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 15:12:53'),
('LOG182849', 'student', 'STU001', 'login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 14:54:57'),
('LOG187698', 'student', 'STU001', 'login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-26 00:38:42'),
('LOG188450', 'admin', 'ADM001', 'login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-26 00:47:26'),
('LOG191805', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 18:32:13'),
('LOG195470', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 17:23:04'),
('LOG202071', 'admin', 'ADM001', 'login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 19:02:46'),
('LOG204514', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 15:24:09'),
('LOG207214', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 03:18:03'),
('LOG208912', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 11:12:40'),
('LOG209570', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 14:27:24'),
('LOG214822', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-28 14:24:59'),
('LOG221392', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 08:10:52'),
('LOG222273', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 05:39:02'),
('LOG228013', 'student', 'STU001', 'login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 15:15:20'),
('LOG240232', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 05:32:52'),
('LOG242548', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 18:10:56'),
('LOG244555', 'student', 'STU001', 'login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 14:57:47'),
('LOG244810', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 20:17:42'),
('LOG249792', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 19:45:45'),
('LOG264211', 'student', 'buslon2005-10-16', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 15:57:27'),
('LOG264537', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 20:31:28'),
('LOG271302', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 18:51:15'),
('LOG276779', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 17:22:35'),
('LOG284660', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 06:18:15'),
('LOG299296', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 10:56:08'),
('LOG306831', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Linux; Android 11; RMX3171 Build/RP1A.200720.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/136.0.7103.61 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/506.0.0.61.109;]', '2025-05-24 17:51:07'),
('LOG310033', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 06:14:58'),
('LOG310413', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 18:20:43'),
('LOG318633', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 02:06:34'),
('LOG320874', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 06:10:59'),
('LOG321343', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 18:15:02'),
('LOG324019', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 06:14:43'),
('LOG337556', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 06:16:17'),
('LOG347257', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 15:50:56'),
('LOG350787', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-28 15:08:29'),
('LOG357404', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 02:49:11'),
('LOG358959', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 06:36:19'),
('LOG368228', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 06:20:57'),
('LOG373053', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 18:08:54'),
('LOG374999', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 02:30:29'),
('LOG376432', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 11:00:46'),
('LOG394650', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 05:09:11'),
('LOG406711', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 06:12:51'),
('LOG421204', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 09:08:46'),
('LOG427058', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 02:10:15'),
('LOG430685', 'admin', 'ADM001', 'login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 14:23:33'),
('LOG432377', 'student', 'STU001', 'login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 15:31:37'),
('LOG440111', 'student', 'STU001', 'login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 15:18:27'),
('LOG448377', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 18:51:51'),
('LOG458914', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 18:06:58'),
('LOG461809', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-05-26 04:56:40'),
('LOG463276', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 21:42:32'),
('LOG464280', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 03:46:43'),
('LOG464321', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 20:22:39'),
('LOG469728', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 18:52:24'),
('LOG471652', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 06:29:59'),
('LOG473954', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 18:23:26'),
('LOG474269', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 11:13:48'),
('LOG479757', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 21:24:22'),
('LOG481446', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 04:09:07'),
('LOG491587', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 15:50:09'),
('LOG499265', 'student', 'STU001', 'login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 18:39:25'),
('LOG499884', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 05:26:50'),
('LOG502935', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-28 14:29:53'),
('LOG505671', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 17:48:41'),
('LOG508669', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 03:20:41'),
('LOG510121', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 03:50:22'),
('LOG512268', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-28 15:06:43'),
('LOG514099', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 22:10:55'),
('LOG515238', 'instructor', 'INS001', 'login', '192.168.8.45', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-05-24 22:13:27'),
('LOG532551', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 20:18:07'),
('LOG546735', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 17:59:48'),
('LOG547601', 'student', 'STU001', 'login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 14:50:10'),
('LOG555077', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 18:10:30'),
('LOG560323', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 20:17:37'),
('LOG562763', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 16:50:26'),
('LOG565588', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 22:09:40'),
('LOG573087', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 19:05:52'),
('LOG579646', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 19:21:54'),
('LOG583437', 'student', 'STU001', 'login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 15:00:35'),
('LOG610614', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 22:54:20'),
('LOG611960', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-28 15:06:23'),
('LOG618004', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 03:43:47'),
('LOG624542', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 11:17:52'),
('LOG636139', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 22:14:23'),
('LOG640553', 'student', 'STU002', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 21:05:27'),
('LOG645759', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 02:49:44'),
('LOG645979', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 07:40:55'),
('LOG646498', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 10:59:17'),
('LOG647817', 'student', 'STU001', 'login', '192.168.8.33', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-05-23 18:24:08'),
('LOG668022', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 03:04:40'),
('LOG677777', 'student', 'STU001', 'login', '192.168.152.168', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-05-24 02:44:06'),
('LOG683892', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 03:52:12'),
('LOG692816', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 16:11:11'),
('LOG695744', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 11:17:43'),
('LOG696746', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 17:13:15'),
('LOG704498', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Linux; Android 11; RMX3171 Build/RP1A.200720.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/136.0.7103.61 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/506.0.0.61.109;]', '2025-05-24 17:50:40'),
('LOG728736', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 15:12:45'),
('LOG734347', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 20:58:05'),
('LOG739453', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 11:18:01'),
('LOG746994', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 21:59:57'),
('LOG750619', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 22:29:14'),
('LOG758534', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 17:52:46'),
('LOG759900', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 04:09:42'),
('LOG765107', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 05:05:09'),
('LOG766122', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 16:11:09'),
('LOG771433', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 11:03:07'),
('LOG777799', 'admin', 'ADM001', 'login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 14:20:15'),
('LOG777812', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 18:48:05'),
('LOG779701', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 12:21:10'),
('LOG780064', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 22:01:47'),
('LOG786047', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 09:36:39'),
('LOG793077', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 15:56:25'),
('LOG796253', 'admin', 'ADM001', 'login', '192.168.141.141', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-05-24 02:57:05'),
('LOG797601', 'admin', 'ADM001', 'login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0', '2025-05-26 00:45:14'),
('LOG801520', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 19:08:21'),
('LOG808131', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-26 05:34:38'),
('LOG811324', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 03:53:07'),
('LOG811454', 'student', 'STU002', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 06:26:33'),
('LOG833848', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 15:22:26'),
('LOG836203', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 02:52:18'),
('LOG839476', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 09:41:28'),
('LOG844116', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 07:41:47'),
('LOG864500', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 09:41:09'),
('LOG870105', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 03:45:44'),
('LOG870539', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 16:52:22'),
('LOG870959', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 18:48:36'),
('LOG873958', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 07:38:17'),
('LOG886586', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 18:15:47'),
('LOG893403', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 23:17:02'),
('LOG894507', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 23:01:16'),
('LOG897925', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-28 16:17:27'),
('LOG912797', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 18:25:28'),
('LOG915079', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 23:22:40'),
('LOG925572', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-28 16:16:46'),
('LOG927341', 'student', 'STU001', 'login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 14:55:31'),
('LOG928376', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-28 16:06:59'),
('LOG934514', 'instructor', 'INS001', 'login', '192.168.141.141', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-05-24 02:57:42'),
('LOG939317', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-23 17:44:20'),
('LOG945811', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 15:59:50'),
('LOG956783', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 18:48:59'),
('LOG963115', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 03:19:35'),
('LOG966287', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-25 11:11:15'),
('LOG972030', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 17:15:03'),
('LOG972990', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-21 17:36:56'),
('LOG985168', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 20:01:32'),
('LOG986754', 'instructor', 'INS001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-24 22:03:14'),
('LOG992028', 'admin', 'ADM001', 'login', '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-05-26 05:13:32'),
('LOG998833', 'student', 'STU001', 'login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-22 20:36:04');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `gradeId` varchar(50) NOT NULL,
  `studentId` varchar(50) NOT NULL,
  `moduleId` varchar(50) NOT NULL,
  `quizId` varchar(50) NOT NULL,
  `recordId` varchar(50) NOT NULL DEFAULT '0',
  `grades` double NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `feedback` varchar(250) DEFAULT 'Quiz not yet taken.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`gradeId`, `studentId`, `moduleId`, `quizId`, `recordId`, `grades`, `created_at`, `updated_at`, `feedback`) VALUES
('grade_6837214730cf99.97455734', 'STU001', 'COD002', 'popup', 'rec_27648a472b6cd753', 5, '2025-05-28 14:44:23', '2025-08-08 12:22:56', 'Congratulations! You passed the quiz.');

-- --------------------------------------------------------

--
-- Table structure for table `instructors`
--

CREATE TABLE `instructors` (
  `instructorId` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instructors`
--

INSERT INTO `instructors` (`instructorId`, `full_name`, `email`, `date_of_birth`, `password`, `created_at`, `updated_at`, `created_by`) VALUES
('buslon2004-10-10', 'Cyrel James Birot Buslon', 'cjbbuslon101004@cybersense.com', '2004-10-04', '$2y$10$M5hprmxuE.eNR.4nAEaTEOEP92mApDG/kqmbNKAek4m3qQDCPXZy6', '2025-05-21 14:20:23', '2025-05-21 19:06:09', 'ADM001'),
('dekarabaw2004-01-01', 'Kervy How dekarabaw', 'khdekarabaw010104@cybersense.com', '2004-01-01', '$2y$10$n7/34DVHm1qM8X6RopYUMev/cE7ywsh46JNGyjTNbALQUMaNgejRq', '2025-05-21 14:20:23', '2025-05-21 14:20:23', 'ADM001'),
('est2025-05-14', 'test etst est', 'teest051425@cybersense.com', '2025-05-14', '$2y$10$EbY/xBEPtRWo2oZkcPY5rOSxPiBT7JOcMxQURPZLXYSy8DzqCcFQy', '2025-05-21 14:24:02', '2025-05-21 14:24:02', 'ADM001'),
('est2025-05-23', 'test test est', 'ttest052325@cybersense.com', '2025-05-23', '$2y$10$rCfisaeO6cFn1ZQYpz9/yeEAEtAUOTL/e0xAyKF/UrRUHKRTuwIDW', '2025-05-23 15:13:30', '2025-05-23 15:13:30', 'ADM001'),
('estset2025-05-02', 'testestes estset estset', 'teestset050225@cybersense.com', '2025-05-02', '$2y$10$EjP6HUGvncsXvAP6hyTlVe1Ux8MwrUj3xsJzn5Av1F90bmojrM5AK', '2025-05-21 14:25:00', '2025-05-21 14:25:00', 'ADM001'),
('INS001', 'James Wilson', 'jwilson@usep.edu.ph', '2005-10-15', '$2y$10$OkRkpE34o0Anha4DqYcHw.PJ8EsH8VyRQC2qr1plJEOBSvF.USA/G', '2025-04-08 18:42:48', '2025-05-28 15:06:33', 'ADM001'),
('INS002', 'Maria Santos', 'msantos@usep.edu.ph', '2005-10-15', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-08 18:42:48', '2025-05-21 14:17:05', 'ADM001'),
('INS003', 'Robert Cruz', 'rcruz@usep.edu.ph', '2005-10-15', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-08 18:42:48', '2025-05-21 14:17:07', 'ADM002');

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

CREATE TABLE `module` (
  `moduleId` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `content_text` text DEFAULT NULL,
  `content_video_url` varchar(255) DEFAULT NULL,
  `content_pdf_path` varchar(255) DEFAULT NULL,
  `instructorId` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','deleted') DEFAULT 'active',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `module_image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `module`
--

INSERT INTO `module` (`moduleId`, `title`, `description`, `content_text`, `content_video_url`, `content_pdf_path`, `instructorId`, `created_at`, `updated_at`, `status`, `deleted_at`, `module_image_path`) VALUES
('COD002', 'CyberSecurity - Beginners', 'Beginners Level', 'Remedial Module', 'https://example.com/video', '../assets/pdf/COD002_1748124695.pdf', 'INS001', '2025-05-03 19:55:31', '2025-05-24 22:11:35', 'active', '2025-05-22 19:44:03', '../assets/images/module/COD002.jpeg'),
('COD003', 'CyberSecurity- Intermediate', 'Intermediate Level', 'Voluntary Module', 'https://example.com/video', 'https://example.com/pdf', 'INS001', '2025-05-15 10:34:55', '2025-05-24 03:15:20', 'active', NULL, '../assets/images/module/COD003.jpeg'),
('COD004', 'Data Privacy - Master', 'Master Level', 'Master Student', 'https://example.com/video', 'https://example.com/pdf', 'INS001', '2025-05-15 10:34:55', '2025-05-22 20:29:28', 'active', NULL, '../assets/images/module/COD004.jpeg'),
('popup', 'Pop up', 'Basic Test For Students', 'Basic Test For New Student', 'https://iamaurl.com/', 'https://iamaurl.com/', 'INS001', '2025-05-21 14:53:17', '2025-05-24 06:34:27', 'active', '2025-05-24 03:13:43', '../assets/images/module/COD004.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `module_enrollments`
--

CREATE TABLE `module_enrollments` (
  `enrollmentId` int(11) NOT NULL,
  `moduleId` varchar(50) NOT NULL,
  `studentId` varchar(50) NOT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('in-progress','completed','dropped') DEFAULT 'in-progress',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `module_enrollments`
--

INSERT INTO `module_enrollments` (`enrollmentId`, `moduleId`, `studentId`, `enrollment_date`, `status`, `created_at`, `updated_at`) VALUES
(31, 'COD002', 'STU001', '2025-05-28 14:38:32', 'in-progress', '2025-05-28 14:38:32', '2025-05-28 14:38:32'),
(32, 'COD003', 'STU001', '2025-05-28 14:38:32', 'in-progress', '2025-05-28 14:38:32', '2025-05-28 14:38:32'),
(33, 'COD004', 'STU001', '2025-05-28 14:38:32', 'in-progress', '2025-05-28 14:38:32', '2025-05-28 14:38:32'),
(34, 'popup', 'STU001', '2025-05-28 14:38:32', 'in-progress', '2025-05-28 14:38:32', '2025-05-28 14:38:32');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `questionId` varchar(50) NOT NULL,
  `quizId` varchar(50) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('multiple_choice','true_false') NOT NULL,
  `correct_answer` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `answer_option` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`answer_option`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`questionId`, `quizId`, `question_text`, `question_type`, `correct_answer`, `created_at`, `updated_at`, `answer_option`) VALUES
('Q001', 'popup', 'Malware, short for malicious software, refers to any software designed to harm a computer or networt', 'multiple_choice', 'B', '2025-05-21 14:49:36', '2025-05-24 21:53:47', '{\"A\": \"true\", \"B\": \"false\"}'),
('Q002', 'popup', 'Which of the following is an example of phishing?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Receiving an email asking for your password\", \"B\": \"Updating your software regularly\", \"C\": \"Using a VPN\", \"D\": \"Backing up your files\"}'),
('Q003', 'popup', 'What is ransomware?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Software that encrypts your files and demands payment\", \"B\": \"A virus that deletes files\", \"C\": \"A firewall type\", \"D\": \"Anti-virus software\"}'),
('Q004', 'popup', 'What does VPN stand for?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Virtual Private Network\", \"B\": \"Verified Personal Number\", \"C\": \"Virtual Public Network\", \"D\": \"Virus Protection Network\"}'),
('Q005', 'popup', 'What is a DDoS attack?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"A distributed denial of service attack that floods a system with traffic\", \"B\": \"A password cracking technique\", \"C\": \"A data encryption method\", \"D\": \"A type of firewall\"}'),
('Q006', 'popup', 'How can you protect yourself from phishing emails?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Never click on suspicious links\", \"B\": \"Always share your password with trusted people\", \"C\": \"Open all email attachments\", \"D\": \"Use the same password for all accounts\"}'),
('Q007', 'popup', 'What is social engineering?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Manipulating people into giving away confidential information\", \"B\": \"Writing secure code\", \"C\": \"Encrypting files\", \"D\": \"Using two-factor authentication\"}'),
('Q008', 'popup', 'Which of the following is a strong password?', 'multiple_choice', 'C', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"password123\", \"B\": \"123456\", \"C\": \"P@ssw0rd!2025\", \"D\": \"yourname\"}'),
('Q009', 'popup', 'What is a firewall?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"A tool that blocks unauthorized access to your network\", \"B\": \"A type of virus\", \"C\": \"A software update\", \"D\": \"A backup system\"}'),
('Q010', 'popup', 'What does two-factor authentication (2FA) do?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Adds an extra layer of security by requiring two verification methods\", \"B\": \"Deletes old passwords\", \"C\": \"Makes passwords visible\", \"D\": \"Slows down your device\"}'),
('Q011', 'popup', 'What is a botnet used for?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Sending spam and launching attacks through a network of infected computers\", \"B\": \"Increasing internet speed\", \"C\": \"Protecting against malware\", \"D\": \"Backing up data\"}'),
('Q012', 'popup', 'Which of these is NOT a common sign of a compromised account?', 'multiple_choice', 'C', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Unexpected password changes\", \"B\": \"Receiving alerts for login attempts from unknown locations\", \"C\": \"Receiving your usual emails\", \"D\": \"Unknown transactions on your account\"}'),
('Q013', 'popup', 'What is spear phishing?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"A targeted phishing attack on a specific individual or organization\", \"B\": \"General spam email\", \"C\": \"Virus infection\", \"D\": \"Firewall configuration\"}'),
('Q014', 'popup', 'Which is safest for online transactions?', 'multiple_choice', 'B', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Public Wi-Fi\", \"B\": \"Using HTTPS websites\", \"C\": \"Clicking on unknown links\", \"D\": \"Sharing card info via email\"}'),
('Q015', 'popup', 'What is spyware?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Software that secretly monitors and collects user information\", \"B\": \"A security patch\", \"C\": \"A type of firewall\", \"D\": \"An antivirus program\"}'),
('Q016', 'popup', 'What does it mean to encrypt data?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"To convert it into a coded form to prevent unauthorized access\", \"B\": \"To delete data permanently\", \"C\": \"To back up data\", \"D\": \"To scan for viruses\"}'),
('Q017', 'popup', 'What is a brute-force attack?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Trying all possible passwords to gain access\", \"B\": \"Sending spam emails\", \"C\": \"Encrypting files\", \"D\": \"Installing antivirus software\"}'),
('Q018', 'popup', 'What is a zero-day exploit?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"A vulnerability unknown to the software vendor and unpatched\", \"B\": \"A software update\", \"C\": \"An expired password\", \"D\": \"A firewall rule\"}'),
('Q019', 'popup', 'How can you tell if a website is secure?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"It has a padlock icon, and the URL begins with HTTPS\", \"B\": \"It loads very fast\", \"C\": \"It has colorful graphics\", \"D\": \"It has many ads\"}'),
('Q020', 'popup', 'What is a keylogger?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Malware that records your keystrokes to steal sensitive information\", \"B\": \"A password manager\", \"C\": \"A type of encryption\", \"D\": \"A firewall feature\"}'),
('Q021', 'popup', 'What is the main purpose of antivirus software?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Detect and remove malicious software\", \"B\": \"Speed up the computer\", \"C\": \"Backup files\", \"D\": \"Manage passwords\"}'),
('Q022', 'popup', 'What is \"pharming\"?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Redirecting users from legitimate sites to fake ones to steal data\", \"B\": \"Farming crops online\", \"C\": \"Sending spam emails\", \"D\": \"Updating software\"}'),
('Q023', 'popup', 'What kind of attack involves overwhelming a website with traffic to make it unavailable?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"DDoS attack\", \"B\": \"Phishing attack\", \"C\": \"Malware injection\", \"D\": \"Man-in-the-middle attack\"}'),
('Q024', 'popup', 'What is a common way attackers spread malware?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Through email attachments\", \"B\": \"Using antivirus software\", \"C\": \"Software updates\", \"D\": \"Secure websites\"}'),
('Q025', 'popup', 'What should you do if your password is compromised?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Change it immediately\", \"B\": \"Ignore it\", \"C\": \"Share it with friends\", \"D\": \"Use the same password for all sites\"}'),
('Q026', 'popup', 'What is the difference between a virus and a worm?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Worms can spread without user action; viruses need user intervention\", \"B\": \"Viruses are harmless\", \"C\": \"Worms only spread through emails\", \"D\": \"Both are the same\"}'),
('Q027', 'popup', 'What is a VPN primarily used for?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Encrypting internet traffic and hiding the IP address\", \"B\": \"Deleting files\", \"C\": \"Speeding up your computer\", \"D\": \"Sharing passwords\"}'),
('Q028', 'popup', 'Which of these is NOT a good cybersecurity practice?', 'multiple_choice', 'C', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Regularly updating software\", \"B\": \"Using strong, unique passwords\", \"C\": \"Clicking on all email links\", \"D\": \"Enabling two-factor authentication\"}'),
('Q029', 'popup', 'What is SQL injection?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"A code injection technique used to attack databases\", \"B\": \"A network protocol\", \"C\": \"A type of encryption\", \"D\": \"An email scam\"}'),
('Q030', 'popup', 'What is ransomware\'s primary goal?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"To encrypt user files and demand payment to restore access\", \"B\": \"To speed up your computer\", \"C\": \"To backup files\", \"D\": \"To install security patches\"}'),
('Q031', 'popup', 'What is cross-site scripting (XSS)?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Injecting malicious scripts into trusted websites\", \"B\": \"A firewall rule\", \"C\": \"An encryption method\", \"D\": \"Email spam\"}'),
('Q032', 'popup', 'What is a man-in-the-middle (MITM) attack?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Intercepting communication between two parties without their knowledge\", \"B\": \"A virus type\", \"C\": \"A firewall\", \"D\": \"Password manager\"}'),
('Q033', 'popup', 'Which of these is a sign of a phishing email?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Urgent request for personal information\", \"B\": \"Personalized greeting with the correct name\", \"C\": \"From a verified contact\", \"D\": \"No spelling errors\"}'),
('Q034', 'popup', 'What is social media oversharing?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Posting too much personal info online, which can be exploited\", \"B\": \"Using social media often\", \"C\": \"Liking many posts\", \"D\": \"Sharing memes\"}'),
('Q035', 'popup', 'What is a honeypot?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"A decoy system to attract and analyze attackers\", \"B\": \"A type of malware\", \"C\": \"A firewall setting\", \"D\": \"Password manager\"}'),
('Q036', 'popup', 'What is data exfiltration?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Unauthorized transfer of data from a system\", \"B\": \"Encrypting data\", \"C\": \"Backing up files\", \"D\": \"Deleting data\"}'),
('Q037', 'popup', 'What is spear phishing different from phishing?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Spear phishing targets specific individuals or organizations\", \"B\": \"Spear phishing is harmless\", \"C\": \"Phishing targets one person only\", \"D\": \"They are the same\"}'),
('Q038', 'popup', 'What is a rogue access point?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"An unauthorized wireless access point on a network\", \"B\": \"A secure Wi-Fi router\", \"C\": \"Firewall software\", \"D\": \"Antivirus program\"}'),
('Q039', 'popup', 'Why is it risky to use public Wi-Fi without protection?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Your data can be intercepted easily\", \"B\": \"It is slower\", \"C\": \"It costs money\", \"D\": \"It deletes files\"}'),
('Q040', 'popup', 'What is the best way to secure your online accounts?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Use strong passwords and enable multi-factor authentication\", \"B\": \"Share passwords with friends\", \"C\": \"Use the same password everywhere\", \"D\": \"Never update passwords\"}'),
('Q041', 'popup', 'What does \"patch management\" mean?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Keeping software up-to-date to fix security flaws\", \"B\": \"Installing antivirus\", \"C\": \"Creating passwords\", \"D\": \"Deleting old files\"}'),
('Q042', 'popup', 'What is the primary purpose of a Security Operations Center (SOC)?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Monitor and respond to security incidents\", \"B\": \"Manage user accounts\", \"C\": \"Backup data\", \"D\": \"Update software\"}'),
('Q043', 'popup', 'What is a logic bomb?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Malicious code triggered by specific conditions\", \"B\": \"Antivirus software\", \"C\": \"Backup tool\", \"D\": \"Firewall\"}'),
('Q044', 'popup', 'Which of the following is NOT a type of malware?', 'multiple_choice', 'C', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Trojan\", \"B\": \"Worm\", \"C\": \"Spam email\", \"D\": \"Ransomware\"}'),
('Q045', 'popup', 'What is \"credential stuffing\"?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Using stolen usernames and passwords to access multiple accounts\", \"B\": \"Filling out forms online\", \"C\": \"Using password managers\", \"D\": \"Installing antivirus software\"}'),
('Q046', 'popup', 'Why should you avoid clicking on shortened URLs from unknown sources?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"They can hide malicious destinations\", \"B\": \"They are always safe\", \"C\": \"They improve speed\", \"D\": \"They encrypt data\"}'),
('Q047', 'popup', 'What is the function of a certificate authority (CA)?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Issue digital certificates verifying identities\", \"B\": \"Create malware\", \"C\": \"Block spam emails\", \"D\": \"Backup data\"}'),
('Q048', 'popup', 'What does the CIA triad stand for in cybersecurity?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Confidentiality, Integrity, Availability\", \"B\": \"Control, Inspect, Audit\", \"C\": \"Cybersecurity, Internet, Access\", \"D\": \"Create, Implement, Assess\"}'),
('Q049', 'popup', 'What is cross-site request forgery (CSRF)?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"An attack forcing a user to execute unwanted actions on a website\", \"B\": \"A type of firewall\", \"C\": \"Email spam\", \"D\": \"Password cracking\"}'),
('Q050', 'popup', 'What is fuzz testing?', 'multiple_choice', 'A', '2025-05-21 14:49:36', '2025-05-21 14:49:36', '{\"A\": \"Sending random data to software to find bugs or vulnerabilities\", \"B\": \"Updating software\", \"C\": \"Encrypting emails\", \"D\": \"Installing antivirus\"}');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `quizId` varchar(50) NOT NULL,
  `moduleId` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `total_questions` int(11) NOT NULL DEFAULT 0,
  `passing_score` int(11) NOT NULL DEFAULT 70,
  `duration_minutes` double DEFAULT 60,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `description` varchar(250) NOT NULL DEFAULT 'Test your ability.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`quizId`, `moduleId`, `title`, `total_questions`, `passing_score`, `duration_minutes`, `created_at`, `updated_at`, `description`) VALUES
('popup', 'COD002', 'Basic Quiz', 5, 3, 0.5, '2025-04-08 18:42:48', '2025-05-26 05:06:15', 'Basic Test For New Student');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempt`
--

CREATE TABLE `quiz_attempt` (
  `recordId` varchar(50) NOT NULL,
  `quizId` varchar(50) NOT NULL,
  `studentId` varchar(50) NOT NULL,
  `student_answer` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`student_answer`)),
  `attempt_score` decimal(5,2) NOT NULL DEFAULT 0.00,
  `attempt_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `completion_status` enum('not_started','in_progress','completed','abandoned') DEFAULT 'not_started',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('passed','failed','retake') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_attempt`
--

INSERT INTO `quiz_attempt` (`recordId`, `quizId`, `studentId`, `student_answer`, `attempt_score`, `attempt_date`, `completion_status`, `created_at`, `updated_at`, `status`) VALUES
('rec_09f2392b7a127c9e', 'popup', 'STU001', '[\"{\\\"questionId\\\":\\\"Q016\\\",\\\"studentAnswer\\\":\\\"To convert it into a coded form to prevent unauthorized access\\\",\\\"correctAnswer\\\":\\\"A\\\",\\\"isCorrect\\\":true}\",\"{\\\"questionId\\\":\\\"Q039\\\",\\\"studentAnswer\\\":\\\"It costs money\\\",\\\"correctAnswer\\\":\\\"A\\\",\\\"isCorrect\\\":false}\",\"{\\\"questionId\\\":\\\"Q049\\\",\\\"studentAnswer\\\":\\\"Password cracking\\\",\\\"correctAnswer\\\":\\\"A\\\",\\\"isCorrect\\\":false}\",\"{\\\"questionId\\\":\\\"Q032\\\",\\\"studentAnswer\\\":\\\"Intercepting communication between two parties without their knowledge\\\",\\\"correctAnswer\\\":\\\"A\\\",\\\"isCorrect\\\":true}\",\"{\\\"questionId\\\":\\\"Q028\\\",\\\"studentAnswer\\\":\\\"Using strong, unique passwords\\\",\\\"correctAnswer\\\":\\\"C\\\",\\\"isCorrect\\\":false}\"]', 2.00, '2025-08-08 12:21:40', 'completed', '2025-08-08 12:21:40', '2025-08-08 12:21:40', 'failed'),
('rec_26aa336fd1db7992', 'popup', 'STU001', '[\"{\\\"questionId\\\":\\\"Q020\\\",\\\"studentAnswer\\\":\\\"A type of encryption\\\",\\\"correctAnswer\\\":\\\"A\\\",\\\"isCorrect\\\":false}\",\"{\\\"questionId\\\":\\\"Q037\\\",\\\"studentAnswer\\\":\\\"Phishing targets one person only\\\",\\\"correctAnswer\\\":\\\"A\\\",\\\"isCorrect\\\":false}\",\"{\\\"questionId\\\":\\\"Q019\\\",\\\"studentAnswer\\\":\\\"It has a padlock icon, and the URL begins with HTTPS\\\",\\\"correctAnswer\\\":\\\"A\\\",\\\"isCorrect\\\":true}\",\"{\\\"questionId\\\":\\\"Q032\\\",\\\"studentAnswer\\\":\\\"Intercepting communication between two parties without their knowledge\\\",\\\"correctAnswer\\\":\\\"A\\\",\\\"isCorrect\\\":true}\",\"{\\\"questionId\\\":\\\"Q027\\\",\\\"studentAnswer\\\":\\\"Deleting files\\\",\\\"correctAnswer\\\":\\\"A\\\",\\\"isCorrect\\\":false}\"]', 2.00, '2025-05-28 14:44:23', 'completed', '2025-05-28 14:44:23', '2025-05-28 14:44:23', 'failed'),
('rec_27648a472b6cd753', 'popup', 'STU001', '[\"{\\\"questionId\\\":\\\"Q012\\\",\\\"studentAnswer\\\":\\\"Receiving your usual emails\\\",\\\"correctAnswer\\\":\\\"C\\\",\\\"isCorrect\\\":true}\",\"{\\\"questionId\\\":\\\"Q010\\\",\\\"studentAnswer\\\":\\\"Adds an extra layer of security by requiring two verification methods\\\",\\\"correctAnswer\\\":\\\"A\\\",\\\"isCorrect\\\":true}\",\"{\\\"questionId\\\":\\\"Q045\\\",\\\"studentAnswer\\\":\\\"Using stolen usernames and passwords to access multiple accounts\\\",\\\"correctAnswer\\\":\\\"A\\\",\\\"isCorrect\\\":true}\",\"{\\\"questionId\\\":\\\"Q013\\\",\\\"studentAnswer\\\":\\\"A targeted phishing attack on a specific individual or organization\\\",\\\"correctAnswer\\\":\\\"A\\\",\\\"isCorrect\\\":true}\",\"{\\\"questionId\\\":\\\"Q036\\\",\\\"studentAnswer\\\":\\\"Unauthorized transfer of data from a system\\\",\\\"correctAnswer\\\":\\\"A\\\",\\\"isCorrect\\\":true}\"]', 5.00, '2025-08-08 12:22:56', 'completed', '2025-08-08 12:22:56', '2025-08-08 12:22:56', 'passed');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `studentId` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `enrollment_status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`studentId`, `full_name`, `date_of_birth`, `email`, `password`, `enrollment_status`, `created_at`, `updated_at`, `created_by`) VALUES
('barza2005-10-11', 'bat terna barza', '2005-10-11', 'btbarza101105@cybersense.com', '$2y$10$/VN705sr7InVciRUKfM6HOl.phfOc4mjTgEInrAChg0JFQFwhIozm', 'active', '2025-05-26 06:16:47', '2025-05-26 06:16:47', 'ADM001'),
('buslon2005-10-16', 'test test test', '2004-06-08', 'slbbuslon101605@cybersense.com', '$2y$10$fGaKkz.0qWNAMCoIUyMkkeqws.6gkjm/i2ojamU4SMzQV8KdhSdd6', 'inactive', '2025-05-25 15:56:43', '2025-05-26 06:18:13', 'ADM001'),
('buslon2005-11-16', 'Segn Lee Birot Buslon', '2005-11-16', 'slbbuslon111605@cybersense.com', '$2y$10$fj3mKOtnGvZdHApjQ4T.EutzGc32SVLunsdyZNw3excTAGjtqXfdy', 'active', '2025-05-25 17:09:24', '2025-05-25 17:09:24', 'ADM001'),
('STU001', 'Jhon Wester', '2001-02-28', 'jdelacruz@usep.edu.ph', '$2y$10$GuoaZUYdFNAffO1AwwPxqeKwhbk5ooDT8/hL8Vv4kd6HrlqVPwuwq', 'inactive', '2025-04-08 18:42:48', '2025-05-26 02:09:14', 'ADM001'),
('STU002', 'Maria Garcia', '2025-05-19', 'mgarcia@usep.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active', '2025-04-08 18:42:48', '2025-05-19 13:58:26', 'ADM001'),
('STU003', 'Pedro Reyes', '2025-05-19', 'preyes@usep.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active', '2025-04-08 18:42:48', '2025-05-19 13:58:28', 'ADM002'),
('STU004', 'Ana Macapagal', '2025-05-19', 'amacapagal@usep.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active', '2025-04-08 18:42:48', '2025-05-19 13:58:29', 'ADM002'),
('vince2005-10-15', 'Kurt Suhid Vince', '2005-10-15', 'ksvince101505@cybersense.com', '$2y$10$849CiEJRWM41y8h6FciGUOewNxrKLiJKO/OVvUDbm.xPTOXfSMoD.', 'active', '2025-05-26 06:16:47', '2025-05-26 06:16:47', 'ADM001');

-- --------------------------------------------------------

--
-- Table structure for table `user_activity_logs`
--

CREATE TABLE `user_activity_logs` (
  `activityLogId` varchar(50) NOT NULL,
  `user_type` enum('student','instructor','admin') NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `activity_details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_activity_logs`
--

INSERT INTO `user_activity_logs` (`activityLogId`, `user_type`, `user_id`, `activity_type`, `activity_details`, `created_at`) VALUES
('act_68313c8f9e11b8.3', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":3,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 03:27:11'),
('act_68313dbaf04252.4', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":3,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 03:32:10'),
('act_68313ea4628868.0', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":8,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 03:36:04'),
('act_683147b00a8ae9.2', 'admins', 'ADM001', 'delete_user', '{\"reason\":\"la lang dais core\",\"deleted_user\":{\"id\":\"buslon2025-05-30\",\"type\":\"students\",\"name\":\"BUSLOn Segmn Buslon ahhh\",\"email\":\"bsbuslon053025@cybersense.com\"},\"performed_by\":\"ADM001\"}', '2025-05-24 04:14:40'),
('act_683164313413c1.2', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":10,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 06:16:17'),
('act_683165a0d3ba51.0', 'admins', 'ADM001', 'delete_user', '{\"reason\":\"wala lan\",\"deleted_user\":{\"id\":\"buslon2005-10-16\",\"type\":\"students\",\"name\":\"Segn Lee Birot Buslon\",\"email\":\"slbbuslon101605@cybersense.com\"},\"performed_by\":\"ADM001\"}', '2025-05-24 06:22:24'),
('act_683166d1224a14.3', 'students', 'STU002', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":10,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 06:27:29'),
('act_68322cdc1551b6.9', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":1,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 20:32:28'),
('act_68322d34350e15.6', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":10,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 20:33:56'),
('act_68322dd3d680d8.1', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":1,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 20:36:35'),
('act_6832305ed1bcf8.5', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":5,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 20:47:26'),
('act_683230689309b8.5', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":5,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 20:47:36'),
('act_6832307e2a6e11.1', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":3,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 20:47:58'),
('act_6832308da748d5.2', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":3,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 20:48:13'),
('act_6832309b3ccae4.9', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":3,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 20:48:27'),
('act_68323170930cb3.1', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":2,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 20:52:00'),
('act_68323209f34326.8', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":0,\"totalQuestions\":10,\"passed\":false}', '2025-05-24 20:54:34'),
('act_6832321fbac4a2.2', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":0,\"totalQuestions\":10,\"passed\":false}', '2025-05-24 20:54:55'),
('act_68323257224401.0', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":2,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 20:55:51'),
('act_6832326f7a48c4.0', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":4,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 20:56:15'),
('act_683232cf7f8ed4.4', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":4,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 20:57:51'),
('act_683232e7021fc5.7', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":0,\"totalQuestions\":10,\"passed\":false}', '2025-05-24 20:58:15'),
('act_6832332f710cc8.1', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":1,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 20:59:27'),
('act_6832335ed22a09.9', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":5,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 21:00:14'),
('act_683233b28af3a2.1', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":10,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 21:01:38'),
('act_6832343db0adb4.9', 'students', 'STU002', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":4,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 21:03:57'),
('act_6832345c4ef481.4', 'students', 'STU002', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":1,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 21:04:28'),
('act_68323483f18c97.1', 'students', 'STU002', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":2,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 21:05:08'),
('act_683234a90baf24.3', 'students', 'STU002', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":5,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 21:05:45'),
('act_683234da5869d5.7', 'students', 'STU002', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":7,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 21:06:34'),
('act_683234faca7ec2.3', 'students', 'STU002', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":3,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 21:07:06'),
('act_68323aac377652.9', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":0,\"totalQuestions\":10,\"passed\":false}', '2025-05-24 21:31:24'),
('act_68323c4a23c447.1', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":0,\"totalQuestions\":10,\"passed\":false}', '2025-05-24 21:38:18'),
('act_68323d383bcac8.2', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":3,\"totalQuestions\":10,\"passed\":true}', '2025-05-24 21:42:16'),
('act_68323fb4873501.1', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":0,\"totalQuestions\":10,\"passed\":false}', '2025-05-24 21:52:52'),
('act_68323ffe8929d9.8', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":0,\"totalQuestions\":50,\"passed\":false}', '2025-05-24 21:54:06'),
('act_6832407cefb2c4.7', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":5,\"totalQuestions\":5,\"passed\":true}', '2025-05-24 21:56:12'),
('act_6833cd27180579.5', 'admins', 'ADM001', 'delete_user', '{\"reason\":\"test\",\"deleted_user\":{\"id\":\"buslon2005-10-11\",\"type\":\"students\",\"name\":\"Segn Birot Buslon\",\"email\":\"sbbuslon101105@cybersense.com\"},\"performed_by\":\"ADM001\"}', '2025-05-26 02:08:39'),
('act_6833ce257bcdd4.6', 'admins', 'ADM001', 'delete_user', '{\"reason\":\"test\",\"deleted_user\":{\"id\":\"buslon2005-10-15\",\"type\":\"students\",\"name\":\"Segn Birot Buslon\",\"email\":\"sbbuslon101505@cybersense.com\"},\"performed_by\":\"ADM001\"}', '2025-05-26 02:12:53'),
('act_6833f4a6d35614.2', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":5,\"totalQuestions\":5,\"passed\":true}', '2025-05-26 04:57:10'),
('act_6833f675e62561.2', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":0,\"totalQuestions\":5,\"passed\":false}', '2025-05-26 05:04:53'),
('act_6833f6ba231028.6', 'students', 'STU002', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":3,\"totalQuestions\":5,\"passed\":true}', '2025-05-26 05:06:02'),
('act_6833f7524f4d84.7', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":2,\"totalQuestions\":5,\"passed\":true}', '2025-05-26 05:08:34'),
('act_6833f9473717d4.2', 'admins', 'ADM001', 'delete_user', '{\"reason\":\"test\",\"deleted_user\":{\"id\":\"blue2005-02-08\",\"type\":\"students\",\"name\":\"John West test Blue\",\"email\":\"jwtblue020805@cybersense.com\"},\"performed_by\":\"ADM001\"}', '2025-05-26 05:16:55'),
('act_6833f96b01b250.0', 'admins', 'ADM001', 'delete_user', '{\"reason\":\"test\",\"deleted_user\":{\"id\":\"dekarabaw2004-01-01\",\"type\":\"admins\",\"name\":\"Kervy How dekarabaw\",\"email\":\"khdekarabaw010104@cybersense.com\"},\"performed_by\":\"ADM001\"}', '2025-05-26 05:17:31'),
('act_68340611700941.5', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":5,\"totalQuestions\":5,\"passed\":true}', '2025-05-26 06:11:29'),
('act_68340637781a94.4', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":2,\"totalQuestions\":5,\"passed\":true}', '2025-05-26 06:12:07'),
('act_68372147328309.8', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":2,\"totalQuestions\":5,\"passed\":true}', '2025-05-28 14:44:23'),
('act_6895ebd41b2830.7', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":2,\"totalQuestions\":5,\"passed\":true}', '2025-08-08 12:21:40'),
('act_6895ec20d4b001.5', 'students', 'STU001', 'quiz_completion', '{\"quizId\":\"popup\",\"score\":5,\"totalQuestions\":5,\"passed\":true}', '2025-08-08 12:22:56'),
('LOG_20250524_2b89393', 'instructors', 'INS001', 'module_delete', 'Deleted module: \'hhahhahah\' (ID: MODC0A82C2F)', '2025-05-24 03:53:28'),
('LOG_20250524_8dce0d0', 'instructors', 'INS001', 'module_delete', 'Deleted module: \'test 2\' (ID: MOD41C07B58)', '2025-05-24 21:23:40'),
('LOG_20250524_8f5e94d', 'instructors', 'INS001', 'module_delete', 'Deleted module: \'test 3\' (ID: MOD1876D60D)', '2025-05-24 21:24:05'),
('LOG_20250524_8fcb27d', 'instructors', 'INS001', 'module_delete', 'Deleted module: \'test\' (ID: MOD001)', '2025-05-24 21:24:12'),
('LOG_20250524_967d788', 'instructors', 'INS001', 'module_delete', 'Deleted module: \'Pop up\' (ID: popup)', '2025-05-24 03:13:43'),
('log_68313635bbe93', 'instructors', 'INS001', 'create_module', 'Instructor INS001 created Module \'test 6\' with ID: MODB24BFB34 at 2025-05-24 05:00:05', '2025-05-24 03:00:05'),
('log_68313704a6e11', 'instructors', 'INS001', 'create_module', 'Instructor INS001 created Module \'tes 8\' with ID: MODFB32EC92 at 2025-05-24 05:03:32', '2025-05-24 03:03:32'),
('log_6831422a32b27', 'instructors', 'INS001', 'create_module', 'Instructor INS001 created Module \'hhahhahah\' with ID: MODC0A82C2F at 2025-05-24 05:51:06', '2025-05-24 03:51:06'),
('log_6833f7a07ef75', 'instructors', 'INS001', 'create_module', 'Instructor INS001 created Module \'test\' with ID: MOD65E3AFD1 at 2025-05-26 07:09:52', '2025-05-26 05:09:52'),
('log_6833fd37e9f8e', 'instructors', 'INS001', 'create_module', 'Instructor INS001 created Module \'This is a test\' with ID: MODD5C7D741 at 2025-05-26 07:33:43', '2025-05-26 05:33:43'),
('log_6834068671af8', 'instructors', 'INS001', 'create_module', 'Instructor INS001 created Module \'test\' with ID: MODC9303E10 at 2025-05-26 08:13:26', '2025-05-26 06:13:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`adminId`),
  ADD UNIQUE KEY `admin_email_unique` (`email`),
  ADD KEY `created_by_admin` (`created_by`);

--
-- Indexes for table `authentication_logs`
--
ALTER TABLE `authentication_logs`
  ADD PRIMARY KEY (`logId`),
  ADD KEY `auth_user_id_index` (`user_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`gradeId`),
  ADD KEY `progress_student_foreign` (`studentId`),
  ADD KEY `progress_module_foreign` (`moduleId`),
  ADD KEY `progress_quizzes_foreign` (`quizId`),
  ADD KEY `progress_answer_foreign` (`recordId`);

--
-- Indexes for table `instructors`
--
ALTER TABLE `instructors`
  ADD PRIMARY KEY (`instructorId`),
  ADD UNIQUE KEY `instructor_email_unique` (`email`),
  ADD KEY `instructor_created_by_foreign` (`created_by`);

--
-- Indexes for table `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`moduleId`),
  ADD KEY `module_instructor_foreign` (`instructorId`);

--
-- Indexes for table `module_enrollments`
--
ALTER TABLE `module_enrollments`
  ADD PRIMARY KEY (`enrollmentId`),
  ADD KEY `enrollment_module_foreign` (`moduleId`),
  ADD KEY `enrollment_student_foreign` (`studentId`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`questionId`),
  ADD KEY `question_quiz_foreign` (`quizId`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`quizId`),
  ADD KEY `quiz_module_foreign` (`moduleId`);

--
-- Indexes for table `quiz_attempt`
--
ALTER TABLE `quiz_attempt`
  ADD PRIMARY KEY (`recordId`),
  ADD KEY `quiz_attempt_quiz_foreign` (`quizId`),
  ADD KEY `quiz_attempt_question_foreign` (`studentId`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`studentId`),
  ADD UNIQUE KEY `student_email_unique` (`email`),
  ADD KEY `student_created_by_foreign` (`created_by`);

--
-- Indexes for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  ADD PRIMARY KEY (`activityLogId`),
  ADD KEY `activity_user_id_index` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `module_enrollments`
--
ALTER TABLE `module_enrollments`
  MODIFY `enrollmentId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `created_by_admin` FOREIGN KEY (`created_by`) REFERENCES `admins` (`adminId`) ON UPDATE CASCADE;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `progress_answer_foreign` FOREIGN KEY (`recordId`) REFERENCES `quiz_attempt` (`recordId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `progress_module_foreign` FOREIGN KEY (`moduleId`) REFERENCES `module` (`moduleId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `progress_quizzes_foreign` FOREIGN KEY (`quizId`) REFERENCES `quizzes` (`quizId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `progress_student_foreign` FOREIGN KEY (`studentId`) REFERENCES `students` (`studentId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `instructors`
--
ALTER TABLE `instructors`
  ADD CONSTRAINT `instructor_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`adminId`) ON UPDATE CASCADE;

--
-- Constraints for table `module`
--
ALTER TABLE `module`
  ADD CONSTRAINT `module_instructor_foreign` FOREIGN KEY (`instructorId`) REFERENCES `instructors` (`instructorId`) ON UPDATE CASCADE;

--
-- Constraints for table `module_enrollments`
--
ALTER TABLE `module_enrollments`
  ADD CONSTRAINT `enrollment_student_foreign` FOREIGN KEY (`studentId`) REFERENCES `students` (`studentId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `module_foreign` FOREIGN KEY (`moduleId`) REFERENCES `module` (`moduleId`) ON UPDATE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `question_quiz_foreign` FOREIGN KEY (`quizId`) REFERENCES `quizzes` (`quizId`) ON UPDATE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quiz_module_foreign` FOREIGN KEY (`moduleId`) REFERENCES `module` (`moduleId`) ON UPDATE CASCADE;

--
-- Constraints for table `quiz_attempt`
--
ALTER TABLE `quiz_attempt`
  ADD CONSTRAINT `quiz_attempt_quiz_foreign` FOREIGN KEY (`quizId`) REFERENCES `quizzes` (`quizId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `quiz_attempt_student_foreign` FOREIGN KEY (`studentId`) REFERENCES `students` (`studentId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `student_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`adminId`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
