-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2026 at 08:56 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `h360_op`
--

-- --------------------------------------------------------

--
-- Table structure for table `advise_template`
--

CREATE TABLE `advise_template` (
  `at_id` int(11) NOT NULL,
  `template_name` varchar(225) DEFAULT NULL,
  `template_data` varchar(500) DEFAULT NULL,
  `status` enum('0','1') DEFAULT '1',
  `org_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `advise_template`
--

INSERT INTO `advise_template` (`at_id`, `template_name`, `template_data`, `status`, `org_id`) VALUES
(1, 'care', 'need to take \nmuch care', '1', 1);

-- --------------------------------------------------------

--
-- Table structure for table `appointment_existing`
--

CREATE TABLE `appointment_existing` (
  `atmt_id` int(11) NOT NULL,
  `appoint_id` int(11) NOT NULL,
  `bill_id` varchar(30) NOT NULL,
  `bill_date` date DEFAULT NULL,
  `appoint_register_id` varchar(100) NOT NULL,
  `appoint_unicode` varchar(100) NOT NULL,
  `patient_name` varchar(255) NOT NULL,
  `gender` enum('Male','Female','Others') NOT NULL,
  `systolic` varchar(30) NOT NULL,
  `diastolic` varchar(30) NOT NULL,
  `temperature` varchar(100) NOT NULL,
  `glucose_level` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `patient_email` varchar(50) NOT NULL,
  `appoint_date` date NOT NULL,
  `doctor_name` int(15) NOT NULL,
  `start_time` varchar(30) NOT NULL,
  `end_time` varchar(30) NOT NULL,
  `doctor_fee` int(11) NOT NULL,
  `appoint_status` enum('1','0') NOT NULL DEFAULT '1',
  `visitor_status` enum('1','2','0') NOT NULL DEFAULT '1',
  `org_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `create_date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `amount_method` varchar(15) NOT NULL,
  `amount` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointment_online`
--

CREATE TABLE `appointment_online` (
  `appoint_id` int(11) NOT NULL,
  `bill_id` varchar(30) NOT NULL,
  `bill_date` date DEFAULT NULL,
  `appoint_register_id` varchar(100) NOT NULL,
  `appoint_unicode` varchar(255) NOT NULL,
  `patient_name` varchar(255) NOT NULL,
  `gender` enum('Male','Female','Others') NOT NULL,
  `systolic` varchar(30) NOT NULL,
  `diastolic` varchar(30) NOT NULL,
  `temperature` varchar(100) NOT NULL,
  `glucose_level` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `dob` date DEFAULT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `patient_email` varchar(50) NOT NULL,
  `appoint_date` date NOT NULL,
  `doctor_name` int(30) NOT NULL,
  `start_time` varchar(30) NOT NULL,
  `end_time` varchar(30) NOT NULL,
  `check_in` datetime DEFAULT NULL,
  `check_out` datetime DEFAULT NULL,
  `invoice_payment` enum('1','0') DEFAULT '0',
  `doctor_fee` int(11) NOT NULL,
  `appoint_status` enum('1','0') NOT NULL DEFAULT '1',
  `visitor_status` enum('0','1','2','3') NOT NULL DEFAULT '1',
  `org_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `create_date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `amount_method` varchar(50) DEFAULT NULL,
  `cash_amount` decimal(10,2) DEFAULT NULL,
  `amount` varchar(15) NOT NULL,
  `bpSit_systolic` varchar(30) DEFAULT '',
  `bpSit_diastolic` varchar(30) DEFAULT '',
  `bpStand_systolic` varchar(30) DEFAULT '',
  `bpStand_diastolic` varchar(30) DEFAULT '',
  `weight` varchar(30) DEFAULT '',
  `height` varchar(30) DEFAULT '',
  `bmi` varchar(30) DEFAULT '',
  `heart_rate` varchar(30) DEFAULT '',
  `grbs` varchar(100) DEFAULT '',
  `spO2` varchar(30) DEFAULT '',
  `patient_overview` text DEFAULT NULL,
  `transaction_number` varchar(50) NOT NULL,
  `transaction_amount` decimal(10,2) DEFAULT NULL,
  `concession_name` varchar(100) NOT NULL,
  `concession_type` enum('amount','percentage') NOT NULL,
  `concession_value` varchar(125) NOT NULL,
  `final_amount` int(11) NOT NULL,
  `respiration_rate` varchar(30) DEFAULT '',
  `valid_from` date DEFAULT NULL,
  `valid_to` date DEFAULT NULL,
  `appointment_status` enum('0','1') NOT NULL DEFAULT '1',
  `patient_history` longtext NOT NULL,
  `queue_order` int(11) DEFAULT NULL,
  `referred_by` varchar(255) DEFAULT NULL,
  `referral_hospital` varchar(255) DEFAULT NULL,
  `referral_notes` text DEFAULT NULL,
  `referral_type` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment_online`
--

INSERT INTO `appointment_online` (`appoint_id`, `bill_id`, `bill_date`, `appoint_register_id`, `appoint_unicode`, `patient_name`, `gender`, `systolic`, `diastolic`, `temperature`, `glucose_level`, `age`, `dob`, `mobile_number`, `patient_email`, `appoint_date`, `doctor_name`, `start_time`, `end_time`, `check_in`, `check_out`, `invoice_payment`, `doctor_fee`, `appoint_status`, `visitor_status`, `org_id`, `created_by`, `modified_by`, `create_date_time`, `amount_method`, `cash_amount`, `amount`, `bpSit_systolic`, `bpSit_diastolic`, `bpStand_systolic`, `bpStand_diastolic`, `weight`, `height`, `bmi`, `heart_rate`, `grbs`, `spO2`, `patient_overview`, `transaction_number`, `transaction_amount`, `concession_name`, `concession_type`, `concession_value`, `final_amount`, `respiration_rate`, `valid_from`, `valid_to`, `appointment_status`, `patient_history`, `queue_order`, `referred_by`, `referral_hospital`, `referral_notes`, `referral_type`) VALUES
(1, 'BID000001', '2025-09-05', 'A202502010001', 'PAT0001', 'Y.BHIMA RAJU', 'Male', '', '', '', '', 74, NULL, '949224802', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(2, 'BID000002', NULL, 'A202502010002', 'PAT0002', 'J.V.L. NARASIMHA RAJU', 'Male', '', '', '', '', 54, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(3, 'BID000003', NULL, 'A202502010003', 'PAT0003', 'KHANDIA BENIA', 'Female', '', '', '', '', 43, NULL, '9438781762', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(4, 'BID000004', NULL, 'A202502010004', 'PAT0004', 'K.BHASKAR RAO', 'Male', '', '', '', '', 66, NULL, '9550575634', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(5, 'BID000005', NULL, 'A202502010005', 'PAT0005', 'CH JAYA LAXMI', 'Female', '', '', '', '', 78, NULL, '7981501223', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(6, 'BID000006', NULL, 'A202502010006', 'PAT0006', 'V YERRAMMA', 'Female', '', '', '', '', 51, NULL, '9966188420', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(7, 'BID000007', NULL, 'A202502010007', 'PAT0007', 'V VARA LAXMI', 'Female', '', '', '', '', 47, NULL, '994834059', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(8, 'BID000008', NULL, 'A202502010008', 'PAT0008', 'G V NARAYANA RAJU', 'Male', '', '', '', '', 55, NULL, '8500112976', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(9, 'BID000009', NULL, 'A202502010009', 'PAT0009', 'K DURGA DAS', 'Male', '', '', '', '', 78, NULL, '9966551514', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(10, 'BID000010', NULL, 'A202502010010', 'PAT0010', 'G KALAYANI', 'Female', '', '', '', '', 49, NULL, '7995908643', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(11, 'BID000011', NULL, 'A202502010011', 'PAT0011', 'G LAXMI', 'Female', '', '', '', '', 63, NULL, '8699494819', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(12, 'BID000012', NULL, 'A202502010012', 'PAT0012', 'G NARAYANA', 'Male', '', '', '', '', 47, NULL, '7077538378', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(13, 'BID000013', NULL, 'A202502010013', 'PAT0013', 'CH PADMA', 'Female', '', '', '', '', 44, NULL, '8885991222', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(14, 'BID000014', NULL, 'A202502010014', 'PAT0014', 'B RAVI', 'Male', '', '', '', '', 37, NULL, '9703456878', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(15, 'BID000015', NULL, 'A202502010015', 'PAT0015', 'ASWHIN KUMAR PATRO', 'Male', '', '', '', '', 31, NULL, '9437455423', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(16, 'BID000016', NULL, 'A202502010016', 'PAT0016', 'K VIJAYA', 'Female', '', '', '', '', 58, NULL, '9440733837', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(17, 'BID000017', NULL, 'A202502010017', 'PAT0017', 'J RAMA VHANDRA RAJU', 'Male', '', '', '', '', 68, NULL, '9700955059', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(18, 'BID000018', NULL, 'A202502010018', 'PAT0018', 'DHARPANA RAO', 'Female', '', '', '', '', 25, NULL, '8341088335', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(19, 'BID000019', NULL, 'A202502010019', 'PAT0019', 'K JANAKI', 'Female', '', '', '', '', 0, NULL, '1111111111', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(20, 'BID000020', NULL, 'A202502010020', 'PAT0020', 'S VENKATA RAO', 'Male', '', '', '', '', 59, NULL, '9866298153', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(21, 'BID000021', NULL, 'A202502010021', 'PAT0021', 'SVKB NARAYANAMMA', 'Female', '', '', '', '', 53, NULL, '9866298153', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(22, 'BID000022', NULL, 'A202502010022', 'PAT0022', 'S D SAIKUMAR', 'Male', '', '', '', '', 23, NULL, '9866298153', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(23, 'BID000023', NULL, 'A202502010023', 'PAT0023', 'GOVINDA RAO', 'Male', '', '', '', '', 59, NULL, '8985955366', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(24, 'BID000024', NULL, 'A202502010024', 'PAT0024', 'CH CHANDRAKALA', 'Female', '', '', '', '', 42, NULL, '9542427999', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(25, 'BID000025', NULL, 'A202502010025', 'PAT0025', 'DRAANI', 'Female', '', '', '', '', 22, NULL, '9391514536', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(26, 'BID000026', NULL, 'A202502010026', 'PAT0026', 'P JAYA', 'Female', '', '', '', '', 32, NULL, '9989291386', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(27, 'BID000027', NULL, 'A202502010027', 'PAT0027', 'MRK RAJU', 'Male', '', '', '', '', 76, NULL, '9247273489', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(28, 'BID000028', NULL, 'A202502010028', 'PAT0028', 'D DHANUNJAYALU', 'Male', '', '', '', '', 70, NULL, '9963264797', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(29, 'BID000029', NULL, 'A202502010029', 'PAT0029', 'I G SRINIVASA RAO', 'Male', '', '', '', '', 33, NULL, '9849329724', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(30, 'BID000030', NULL, 'A202502010030', 'PAT0030', 'RAMA KRISHNA', 'Male', '', '', '', '', 65, NULL, '9246630644', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(31, 'BID000031', NULL, 'A202502010031', 'PAT0031', 'T SATYAVATHI', 'Female', '', '', '', '', 70, NULL, '8885214029', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(32, 'BID000032', NULL, 'A202502010032', 'PAT0032', 'M JASWANTH', 'Male', '', '', '', '', 14, NULL, '9949576554', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(33, 'BID000033', NULL, 'A202502010033', 'PAT0033', 'M DEENA MANI', 'Female', '', '', '', '', 68, NULL, '9959484488', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(34, 'BID000034', NULL, 'A202502010034', 'PAT0034', 'S MOHAN KUMAR', 'Male', '', '', '', '', 45, NULL, '9885441628', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(35, 'BID000035', NULL, 'A202502010035', 'PAT0035', 'MVA KARISHNA', 'Male', '', '', '', '', 54, NULL, '9989566121', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(36, 'BID000036', NULL, 'A202502010036', 'PAT0036', 'Y CHANDAR RAO', 'Male', '', '', '', '', 47, NULL, '9985422889', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(37, 'BID000037', NULL, 'A202502010037', 'PAT0037', 'FATHIMA BEEGI', 'Female', '', '', '', '', 65, NULL, '9848062838', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(38, 'BID000038', NULL, 'A202502010038', 'PAT0038', 'G SARINIVAS', 'Male', '', '', '', '', 53, NULL, '9885197959', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(39, 'BID000039', NULL, 'A202502010039', 'PAT0039', 'CH SHARON', 'Female', '', '', '', '', 22, NULL, '9959484488', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(40, 'BID000040', NULL, 'A202502010040', 'PAT0040', 'T SATYA NARAYANA RAJU', 'Male', '', '', '', '', 63, NULL, '9676275668', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(41, 'BID000041', NULL, 'A202502010041', 'PAT0041', 'VIJAYA LAKSHMI', 'Female', '', '', '', '', 67, NULL, '9434293267', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(42, 'BID000042', NULL, 'A202502010042', 'PAT0042', 'GOPAL KRISHNA', 'Male', '', '', '', '', 55, NULL, '3333333333', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(43, 'BID000043', NULL, 'A202502010043', 'PAT0043', 'V KISHORE', 'Male', '', '', '', '', 35, NULL, '9701668122', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(44, 'BID000044', NULL, 'A202502010044', 'PAT0044', 'K SURYA NARAYANALU', 'Male', '', '', '', '', 64, NULL, '9502394292', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(45, 'BID000045', NULL, 'A202502010045', 'PAT0045', 'ROHITH PANIGRAHI', 'Male', '', '', '', '', 29, NULL, '7008018041', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(46, 'BID000046', NULL, 'A202502010046', 'PAT0046', 'SUNDARI', 'Female', '', '', '', '', 87, NULL, '9686481900', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(47, 'BID000047', NULL, 'A202502010047', 'PAT0047', 'K MURALI KRISHNA REDDY', 'Male', '', '', '', '', 47, NULL, '9949267877', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(48, 'BID000048', NULL, 'A202502010048', 'PAT0048', 'RAMA CHANDRA RAJU', 'Male', '', '', '', '', 68, NULL, '9700955959', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(49, 'BID000049', NULL, 'A202502010049', 'PAT0049', 'KNV LAXMI', 'Female', '', '', '', '', 74, NULL, '4444444444', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(50, 'BID000050', NULL, 'A202502010050', 'PAT0050', 'Y BHEEMA RAJU', 'Male', '', '', '', '', 78, NULL, '9000665715', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(51, 'BID000051', NULL, 'A202502010051', 'PAT0051', 'M SUNDARI', 'Female', '', '', '', '', 87, NULL, '5555555555', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(52, 'BID000052', NULL, 'A202502010052', 'PAT0052', 'CH ERWARAMMA', 'Female', '', '', '', '', 56, NULL, '9704048595', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(53, 'BID000053', NULL, 'A202502010053', 'PAT0053', 'CH PADMA', 'Female', '', '', '', '', 44, NULL, '6666666666', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(54, 'BID000054', NULL, 'A202502010054', 'PAT0054', 'JAMILA SWARWALA', 'Female', '', '', '', '', 37, NULL, '9949217718', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(55, 'BID000055', NULL, 'A202502010055', 'PAT0055', 'DANAJALU', 'Male', '', '', '', '', 70, NULL, '7777777777', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(56, 'BID000056', NULL, 'A202502010056', 'PAT0056', 'PRADEEP', 'Male', '', '', '', '', 40, NULL, '8971234343', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(57, 'BID000057', NULL, 'A202502010057', 'PAT0057', 'PUNDARI KANKSHA', 'Male', '', '', '', '', 41, NULL, '8919340812', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(58, 'BID000058', NULL, 'A202502010058', 'PAT0058', 'SAMPATH KUMAR', 'Male', '', '', '', '', 50, NULL, '8888888888', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(59, 'BID000059', NULL, 'A202502010059', 'PAT0059', 'DEENA MANI', 'Female', '', '', '', '', 64, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(60, 'BID000060', NULL, 'A202502010060', 'PAT0060', 'K SRINU', 'Male', '', '', '', '', 52, NULL, '6301726180', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(61, 'BID000061', NULL, 'A202502010061', 'PAT0061', 'JATHENDRA', 'Male', '', '', '', '', 23, NULL, '6281251015', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(62, 'BID000062', NULL, 'A202502010062', 'PAT0062', 'CHANDRA SHEKAR NAYAK', 'Male', '', '', '', '', 56, NULL, '7077578416', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(63, 'BID000063', NULL, 'A202502010063', 'PAT0063', 'BB MALLI', 'Male', '', '', '', '', 59, NULL, '7008058013', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(64, 'BID000064', NULL, 'A202502010064', 'PAT0064', 'D SUDHA', 'Female', '', '', '', '', 46, NULL, '9985422225', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(65, 'BID000065', NULL, 'A202502010065', 'PAT0065', 'THIRUPATHI RAO', 'Male', '', '', '', '', 60, NULL, '8886239462', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(66, 'BID000066', NULL, 'A202502010066', 'PAT0066', 'D LALITHA', 'Female', '', '', '', '', 60, NULL, '9703025130', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(67, 'BID000067', NULL, 'A202502010067', 'PAT0067', 'ALISHA', 'Female', '', '', '', '', 23, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(68, 'BID000068', NULL, 'A202502010068', 'PAT0068', 'IRMAIL', 'Male', '', '', '', '', 38, NULL, '9392183113', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(69, 'BID000069', NULL, 'A202502010069', 'PAT0069', 'SATYA NARAYANA RAJU', 'Male', '', '', '', '', 63, NULL, '9440387414', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(70, 'BID000070', NULL, 'A202502010070', 'PAT0070', 'ALISHA', 'Female', '', '', '', '', 23, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(71, 'BID000071', NULL, 'A202502010071', 'PAT0071', 'SUNDARI', 'Female', '', '', '', '', 84, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(72, 'BID000072', NULL, 'A202502010072', 'PAT0072', 'K VIJAYA', 'Female', '', '', '', '', 58, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(73, 'BID000073', NULL, 'A202502010073', 'PAT0073', 'D DANUJAYALLU', 'Male', '', '', '', '', 70, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(74, 'BID000074', NULL, 'A202502010074', 'PAT0074', 'NVG PRATAP RAO', 'Male', '', '', '', '', 77, NULL, '9440675852', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(75, 'BID000075', NULL, 'A202502010075', 'PAT0075', 'INDIRA RANI', 'Female', '', '', '', '', 84, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(76, 'BID000076', NULL, 'A202502010076', 'PAT0076', 'K KALAYANI', 'Female', '', '', '', '', 59, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(77, 'BID000077', NULL, 'A202502010077', 'PAT0077', 'M DONA REDDY', 'Female', '', '', '', '', 20, NULL, '8374371454', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(78, 'BID000078', NULL, 'A202502010078', 'PAT0078', 'P SRINIVASA', 'Male', '', '', '', '', 0, NULL, '9885989922', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(79, 'BID000079', NULL, 'A202502010079', 'PAT0079', 'SD SAIKUMAR', 'Male', '', '', '', '', 23, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(80, 'BID000080', NULL, 'A202502010080', 'PAT0080', 'NITISHA', 'Male', '', '', '', '', 20, NULL, '9440650063', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(81, 'BID000081', NULL, 'A202502010081', 'PAT0081', 'PUSPHA JYOTHI', 'Female', '', '', '', '', 47, NULL, '9963150550', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(82, 'BID000082', NULL, 'A202502010082', 'PAT0082', 'K SURYANARAYANA', 'Male', '', '', '', '', 64, NULL, '9912016239', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(83, 'BID000083', NULL, 'A202502010083', 'PAT0083', 'VIMALA KUMARI', 'Female', '', '', '', '', 47, NULL, '8555910677', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(84, 'BID000084', NULL, 'A202502010084', 'PAT0084', 'MOHAN KUMAR', 'Male', '', '', '', '', 59, NULL, '9908850645', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(85, 'BID000085', NULL, 'A202502010085', 'PAT0085', 'K USHA', 'Female', '', '', '', '', 39, NULL, '8712299952', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(86, 'BID000086', NULL, 'A202502010086', 'PAT0086', 'B SURYA KUMARI', 'Female', '', '', '', '', 56, NULL, '9642558038', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(87, 'BID000087', NULL, 'A202502010087', 'PAT0087', 'D KAMALA', 'Female', '', '', '', '', 63, NULL, '8897621279', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(88, 'BID000088', NULL, 'A202502010088', 'PAT0088', 'RAMESH', 'Male', '', '', '', '', 49, NULL, '9908831059', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(89, 'BID000089', NULL, 'A202502010089', 'PAT0089', 'S RAMANAMMA', 'Female', '', '', '', '', 96, NULL, '8247515935', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(90, 'BID000090', NULL, 'A202502010090', 'PAT0090', 'KAMALA', 'Female', '', '', '', '', 53, NULL, '9966962713', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(91, 'BID000091', NULL, 'A202502010091', 'PAT0091', 'RAMA CHANDRA RAJU', 'Male', '', '', '', '', 68, NULL, '9441247751', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(92, 'BID000092', NULL, 'A202502010092', 'PAT0092', 'DANIEL EVANG', 'Male', '', '', '', '', 72, NULL, '9849154959', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(93, 'BID000093', NULL, 'A202502010093', 'PAT0093', 'KRISHN GOPAL DWIVEDI', 'Male', '', '', '', '', 64, NULL, '8002030359', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(94, 'BID000094', NULL, 'A202502010094', 'PAT0094', 'SAMESHWAR RAO', 'Male', '', '', '', '', 49, NULL, '9866481591', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(95, 'BID000095', NULL, 'A202502010095', 'PAT0095', 'JAYA LAXMI', 'Female', '', '', '', '', 76, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(96, 'BID000096', NULL, 'A202502010096', 'PAT0096', 'AJAY KUMAR', 'Female', '', '', '', '', 20, NULL, '9542427999', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(97, 'BID000097', NULL, 'A202502010097', 'PAT0097', 'INDIRA RANI', 'Female', '', '', '', '', 82, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(98, 'BID000098', NULL, 'A202502010098', 'PAT0098', 'G BHAVANI SHANKAR', 'Male', '', '', '', '', 67, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(99, 'BID000099', NULL, 'A202502010099', 'PAT0099', 'K RAVI', 'Male', '', '', '', '', 48, NULL, '9972743554', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(100, 'BID000100', NULL, 'A202502010100', 'PAT0100', 'B MASENU', 'Male', '', '', '', '', 54, NULL, '8143730371', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(101, 'BID000101', NULL, 'A202502010101', 'PAT0101', 'K VIJAYA', 'Female', '', '', '', '', 58, NULL, '9440733837', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(102, 'BID000102', NULL, 'A202502010102', 'PAT0102', 'P SHANMUKA RAO', 'Male', '', '', '', '', 53, NULL, '9502606136', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(103, 'BID000103', NULL, 'A202502010103', 'PAT0103', 'N MADHAVI', 'Female', '', '', '', '', 55, NULL, '9292301352', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(104, 'BID000104', NULL, 'A202502010104', 'PAT0104', 'K DEEPAK', 'Male', '', '', '', '', 31, NULL, '9440295616', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(105, 'BID000105', NULL, 'A202502010105', 'PAT0105', 'SATYA NARAYANA RAJU', 'Male', '', '', '', '', 63, NULL, '9440387414', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(106, 'BID000106', NULL, 'A202502010106', 'PAT0106', 'D KAMALAMMA', 'Female', '', '', '', '', 73, NULL, '9393926291', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(107, 'BID000107', NULL, 'A202502010107', 'PAT0107', 'B ROJA', 'Female', '', '', '', '', 48, NULL, '9531912925', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(108, 'BID000108', NULL, 'A202502010108', 'PAT0108', 'I GOPI', 'Male', '', '', '', '', 62, NULL, '9849252390', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(109, 'BID000109', NULL, 'A202502010109', 'PAT0109', 'VICHITRA PANIGRAHI', 'Female', '', '', '', '', 52, NULL, '6370181834', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(110, 'BID000110', NULL, 'A202502010110', 'PAT0110', 'SUCHITRA PANIGRAHI', 'Female', '', '', '', '', 54, NULL, '6370181834', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(111, 'BID000111', NULL, 'A202502010111', 'PAT0111', 'VARADARAJAN', 'Male', '', '', '', '', 0, NULL, '8978770600', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(112, 'BID000112', NULL, 'A202502010112', 'PAT0112', 'SHANUMUKA RAO', 'Male', '', '', '', '', 53, NULL, '9502606136', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(113, 'BID000113', NULL, 'A202502010113', 'PAT0113', 'M KRISHNA', 'Male', '', '', '', '', 53, NULL, '9989566121', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(114, 'BID000114', NULL, 'A202502010114', 'PAT0114', 'I GOPI NANDH RAO', 'Male', '', '', '', '', 63, NULL, '9849252390', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(115, 'BID000115', NULL, 'A202502010115', 'PAT0115', 'CH PADMA', 'Female', '', '', '', '', 44, NULL, '9949696700', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(116, 'BID000116', NULL, 'A202502010116', 'PAT0116', 'P NARAYANA RAO', 'Male', '', '', '', '', 31, NULL, '9398380191', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(117, 'BID000117', NULL, 'A202502010117', 'PAT0117', 'CH SHANKAR RAO', 'Male', '', '', '', '', 58, NULL, '8523012246', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(118, 'BID000118', NULL, 'A202502010118', 'PAT0118', 'SATYAVATHI', 'Female', '', '', '', '', 70, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(119, 'BID000119', NULL, 'A202502010119', 'PAT0119', 'SAIKUMAR', 'Male', '', '', '', '', 23, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(120, 'BID000120', NULL, 'A202502010120', 'PAT0120', 'S RAMANAMMA', 'Female', '', '', '', '', 96, NULL, '8247515935', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(121, 'BID000121', NULL, 'A202502010121', 'PAT0121', 'R KAMALA', 'Female', '', '', '', '', 53, NULL, '9966962718', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(122, 'BID000122', NULL, 'A202502010122', 'PAT0122', 'M BALA', 'Male', '', '', '', '', 0, NULL, '9160014041', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(123, 'BID000123', NULL, 'A202502010123', 'PAT0123', 'ALISHA', 'Female', '', '', '', '', 23, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(124, 'BID000124', NULL, 'A202502010124', 'PAT0124', 'KNV LAXMI', 'Female', '', '', '', '', 74, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(125, 'BID000125', NULL, 'A202502010125', 'PAT0125', 'SATYANARAYANA RAJU', 'Male', '', '', '', '', 36, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(126, 'BID000126', NULL, 'A202502010126', 'PAT0126', 'K LAXMI', 'Female', '', '', '', '', 60, NULL, '8367614733', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(127, 'BID000127', NULL, 'A202502010127', 'PAT0127', 'MANIVALAYYA', 'Male', '', '', '', '', 64, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(128, 'BID000128', NULL, 'A202502010128', 'PAT0128', 'SHANUMUKA RAO', 'Male', '', '', '', '', 53, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(129, 'BID000129', NULL, 'A202502010129', 'PAT0129', 'M BALA', 'Male', '', '', '', '', 57, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(130, 'BID000130', NULL, 'A202502010130', 'PAT0130', 'RAMA GOPAL', 'Male', '', '', '', '', 63, NULL, '8978073926', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(131, 'BID000131', NULL, 'A202502010131', 'PAT0131', 'CHAKRAPANI K', 'Male', '', '', '', '', 82, NULL, '8790484622', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(132, 'BID000132', NULL, 'A202502010132', 'PAT0132', 'KALAYANI', 'Female', '', '', '', '', 59, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(133, 'BID000133', NULL, 'A202502010133', 'PAT0133', 'K HYMA', 'Female', '', '', '', '', 44, NULL, '6304950742', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(134, 'BID000134', NULL, 'A202502010134', 'PAT0134', 'SUBHA RAO', 'Male', '', '', '', '', 56, NULL, '9666283553', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(135, 'BID000135', NULL, 'A202502010135', 'PAT0135', 'GOVINDA RAO', 'Male', '', '', '', '', 59, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(136, 'BID000136', NULL, 'A202502010136', 'PAT0136', 'P SANIBABU', 'Male', '', '', '', '', 60, NULL, '9906040889', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(137, 'BID000137', NULL, 'A202502010137', 'PAT0137', 'INDIRA RANI', 'Female', '', '', '', '', 62, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `appointment_online` (`appoint_id`, `bill_id`, `bill_date`, `appoint_register_id`, `appoint_unicode`, `patient_name`, `gender`, `systolic`, `diastolic`, `temperature`, `glucose_level`, `age`, `dob`, `mobile_number`, `patient_email`, `appoint_date`, `doctor_name`, `start_time`, `end_time`, `check_in`, `check_out`, `invoice_payment`, `doctor_fee`, `appoint_status`, `visitor_status`, `org_id`, `created_by`, `modified_by`, `create_date_time`, `amount_method`, `cash_amount`, `amount`, `bpSit_systolic`, `bpSit_diastolic`, `bpStand_systolic`, `bpStand_diastolic`, `weight`, `height`, `bmi`, `heart_rate`, `grbs`, `spO2`, `patient_overview`, `transaction_number`, `transaction_amount`, `concession_name`, `concession_type`, `concession_value`, `final_amount`, `respiration_rate`, `valid_from`, `valid_to`, `appointment_status`, `patient_history`, `queue_order`, `referred_by`, `referral_hospital`, `referral_notes`, `referral_type`) VALUES
(138, 'BID000138', NULL, 'A202502010138', 'PAT0138', 'K MOUNIKA', 'Female', '', '', '', '', 35, NULL, '8106016777', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(139, 'BID000139', NULL, 'A202502010139', 'PAT0139', 'PALUGUNA', 'Male', '', '', '', '', 67, NULL, '9966682462', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(140, 'BID000140', NULL, 'A202502010140', 'PAT0140', 'A LAXMI', 'Female', '', '', '', '', 57, NULL, '9652488396', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(141, 'BID000141', NULL, 'A202502010141', 'PAT0141', 'A USHA RAO', 'Female', '', '', '', '', 50, NULL, '9849517593', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(142, 'BID000142', NULL, 'A202502010142', 'PAT0142', 'SUBHA RAO', 'Female', '', '', '', '', 53, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(143, 'BID000143', NULL, 'A202502010143', 'PAT0143', 'RAMADEVI', 'Female', '', '', '', '', 23, NULL, '9492217597', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(144, 'BID000144', NULL, 'A202502010144', 'PAT0144', 'DEENAMANI', 'Female', '', '', '', '', 64, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(145, 'BID000145', NULL, 'A202502010145', 'PAT0145', 'ASWHINI', 'Female', '', '', '', '', 24, NULL, '9866513212', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(146, 'BID000146', NULL, 'A202502010146', 'PAT0146', 'SHIVA PRASAD', 'Male', '', '', '', '', 45, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(147, 'BID000147', NULL, 'A202502010147', 'PAT0147', 'SIVA PRASAD RAMA KRISHNA', 'Male', '', '', '', '', 62, NULL, '9000455301', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(148, 'BID000148', NULL, 'A202502010148', 'PAT0148', 'K APPANNA', 'Male', '', '', '', '', 32, NULL, '8978537065', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(149, 'BID000149', NULL, 'A202502010149', 'PAT0149', 'M RAJESWARI', 'Female', '', '', '', '', 33, NULL, '7842763977', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(150, 'BID000150', NULL, 'A202502010150', 'PAT0150', 'PRATHUSHA', 'Female', '', '', '', '', 33, NULL, '9985438438', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(151, 'BID000151', NULL, 'A202502010151', 'PAT0151', 'V GANGA RAJU', 'Male', '', '', '', '', 74, NULL, '9963817989', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(152, 'BID000152', NULL, 'A202502010152', 'PAT0152', 'M BALA', 'Female', '', '', '', '', 57, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(153, 'BID000153', NULL, 'A202502010153', 'PAT0153', 'SHIVA RAMA', 'Male', '', '', '', '', 59, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(154, 'BID000154', NULL, 'A202502010154', 'PAT0154', 'N RAVI CHANDRA', 'Male', '', '', '', '', 35, NULL, '7989238651', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(155, 'BID000155', NULL, 'A202502010155', 'PAT0155', 'MAHESH', 'Male', '', '', '', '', 29, NULL, '9618009385', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(156, 'BID000156', NULL, 'A202502010156', 'PAT0156', 'RAMA HASEMI', 'Female', '', '', '', '', 69, NULL, '9985902112', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(157, 'BID000157', NULL, 'A202502010157', 'PAT0157', 'G NARAYANA', 'Male', '', '', '', '', 48, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(158, 'BID000158', NULL, 'A202502010158', 'PAT0158', 'RAMANI', 'Female', '', '', '', '', 57, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(159, 'BID000159', NULL, 'A202502010159', 'PAT0159', 'NARASIMHA RAO', 'Male', '', '', '', '', 56, NULL, '8897131879', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(160, 'BID000160', NULL, 'A202502010160', 'PAT0160', 'RAMA CHANDRA RAO', 'Male', '', '', '', '', 68, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(161, 'BID000161', NULL, 'A202502010161', 'PAT0161', 'SHANUMUKA RAO', 'Male', '', '', '', '', 0, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(162, 'BID000162', NULL, 'A202502010162', 'PAT0162', 'DHARMA RAO', 'Male', '', '', '', '', 54, NULL, '9440621626', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(163, 'BID000163', NULL, 'A202502010163', 'PAT0163', 'M.BALA', 'Female', '', '', '', '', 41, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(164, 'BID000164', NULL, 'A202502010164', 'PAT0164', 'B.SANTHAMMA', 'Female', '', '', '', '', 0, NULL, '9550055940', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(165, 'BID000165', NULL, 'A202502010165', 'PAT0165', 'SRINIVAS', 'Male', '', '', '', '', 33, NULL, '9703027769', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(166, 'BID000166', NULL, 'A202502010166', 'PAT0166', 'RAVI CHANDRA', 'Male', '', '', '', '', 35, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(167, 'BID000167', NULL, 'A202502010167', 'PAT0167', 'B.VENKATA RAO', 'Male', '', '', '', '', 79, NULL, '7382692479', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(168, 'BID000168', NULL, 'A202502010168', 'PAT0168', 'K. KANTHAMMA', 'Female', '', '', '', '', 70, NULL, '9951990972', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(169, 'BID000169', NULL, 'A202502010169', 'PAT0169', 'BHEEMA RAJU', 'Male', '', '', '', '', 74, NULL, '9492248402', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(170, 'BID000170', NULL, 'A202502010170', 'PAT0170', 'SEETHARATNAM', 'Female', '', '', '', '', 83, NULL, '7871171630', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(171, 'BID000171', NULL, 'A202502010171', 'PAT0171', 'MASENU', 'Male', '', '', '', '', 54, NULL, '8143730371', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(172, 'BID000172', NULL, 'A202502010172', 'PAT0172', 'SIMHACHALAM', 'Female', '', '', '', '', 45, NULL, '8143730371', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(173, 'BID000173', NULL, 'A202502010173', 'PAT0173', 'PAVANI', 'Female', '', '', '', '', 31, NULL, '7569901379', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(174, 'BID000174', NULL, 'A202502010174', 'PAT0174', 'MADHURI PRIYA', 'Female', '', '', '', '', 24, NULL, '9032281928', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(175, 'BID000175', NULL, 'A202502010175', 'PAT0175', 'SUNDARI', 'Female', '', '', '', '', 84, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(176, 'BID000176', NULL, 'A202502010176', 'PAT0176', 'JOGA RAO', 'Female', '', '', '', '', 63, NULL, '9393282896', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(177, 'BID000177', NULL, 'A202502010177', 'PAT0177', 'ALISHA', 'Female', '', '', '', '', 23, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(178, 'BID000178', NULL, 'A202502010178', 'PAT0178', 'RAMANI', 'Female', '', '', '', '', 57, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(179, 'BID000179', NULL, 'A202502010179', 'PAT0179', 'D.RAMA RAO', 'Male', '', '', '', '', 69, NULL, '9437215487', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(180, 'BID000180', NULL, 'A202502010180', 'PAT0180', 'G.RAMA RAO', 'Male', '', '', '', '', 49, NULL, '9290144782', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(181, 'BID000181', NULL, 'A202502010181', 'PAT0181', 'TRIPATY SAPAN', 'Female', '', '', '', '', 37, NULL, '898592247', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(182, 'BID000182', NULL, 'A202502010182', 'PAT0182', 'BHEEMA RAJU', 'Male', '', '', '', '', 0, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(183, 'BID000183', NULL, 'A202502010183', 'PAT0183', 'ABHILASH', 'Male', '', '', '', '', 32, NULL, '9337667307', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(184, 'BID000184', NULL, 'A202502010184', 'PAT0184', 'K VARALAKSHMI', 'Female', '', '', '', '', 51, NULL, '9951990972', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(185, 'BID000185', NULL, 'A202502010185', 'PAT0185', 'ISSAC', 'Male', '', '', '', '', 72, NULL, '9440191004', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(186, 'BID000186', NULL, 'A202502010186', 'PAT0186', 'M.ADVITH', 'Male', '', '', '', '', 5, NULL, '9133566044', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(187, 'BID000187', NULL, 'A202502010187', 'PAT0187', 'P.NAGAMANI', 'Female', '', '', '', '', 68, NULL, '9866006247', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(188, 'BID000188', NULL, 'A202502010188', 'PAT0188', 'J.SHANTHI KUMARI', 'Female', '', '', '', '', 62, NULL, '9179359903', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(189, 'BID000189', NULL, 'A202502010189', 'PAT0189', 'C.PARU', 'Female', '', '', '', '', 15, NULL, '9553860099', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(190, 'BID000190', NULL, 'A202502010190', 'PAT0190', 'K.VIJAYA', 'Female', '', '', '', '', 58, NULL, '9440733837', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(191, 'BID000191', NULL, 'A202502010191', 'PAT0191', 'G.TEJASWYANI', 'Female', '', '', '', '', 32, NULL, '6301003148', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(192, 'BID000192', NULL, 'A202502010192', 'PAT0192', 'M.J.BHARATHI', 'Female', '', '', '', '', 67, NULL, '73372226306', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(193, 'BID000193', NULL, 'A202502010193', 'PAT0193', 'VENKATA RAO', 'Male', '', '', '', '', 79, NULL, '7382692471', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(194, 'BID000194', NULL, 'A202502010194', 'PAT0194', 'KASULAMMA', 'Female', '', '', '', '', 56, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(195, 'BID000195', NULL, 'A202502010195', 'PAT0195', 'KAMESHWARI', 'Female', '', '', '', '', 48, NULL, '6304950742', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(196, 'BID000196', NULL, 'A202502010196', 'PAT0196', 'MENAKAMBA', 'Female', '', '', '', '', 62, NULL, '9668279218', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(197, 'BID000197', NULL, 'A202502010197', 'PAT0197', 'RAMESH', 'Male', '', '', '', '', 32, NULL, '7207811796', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(198, 'BID000198', NULL, 'A202502010198', 'PAT0198', 'BHEEMA RAJU', 'Male', '', '', '', '', 74, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(199, 'BID000199', NULL, 'A202502010199', 'PAT0199', 'LAKSHMI NARASAMMA', 'Female', '', '', '', '', 60, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(200, 'BID000200', NULL, 'A202502010200', 'PAT0200', 'PHANINDRA', 'Male', '', '', '', '', 30, NULL, '8008481254', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(201, 'BID000201', NULL, 'A202502010201', 'PAT0201', 'SRI KRISHNA RAJ', 'Male', '', '', '', '', 46, NULL, '8658216002', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(202, 'BID000202', NULL, 'A202502010202', 'PAT0202', 'CHANDANA ROY', 'Female', '', '', '', '', 38, NULL, '8658216002', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(203, 'BID000203', NULL, 'A202502010203', 'PAT0203', 'PYDITHALLAMMA', 'Female', '', '', '', '', 60, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(204, 'BID000204', NULL, 'A202502010204', 'PAT0204', 'KAMALAMMA', 'Female', '', '', '', '', 73, NULL, '9393926291', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(205, 'BID000205', NULL, 'A202502010205', 'PAT0205', 'INDIRA RANI', 'Female', '', '', '', '', 54, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(206, 'BID000206', NULL, 'A202502010206', 'PAT0206', 'MAHESHWARI', 'Female', '', '', '', '', 64, NULL, '9705311995', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(207, 'BID000207', NULL, 'A202502010207', 'PAT0207', 'RADHA KRISHNA', 'Male', '', '', '', '', 61, NULL, '9849490321', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(208, 'BID000208', NULL, 'A202502010208', 'PAT0208', 'LAKSHMI', 'Female', '', '', '', '', 70, NULL, '9966049729', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(209, 'BID000209', NULL, 'A202502010209', 'PAT0209', 'K.SARWESHWARA RAO', 'Male', '', '', '', '', 47, NULL, '9704263217', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(210, 'BID000210', NULL, 'A202502010210', 'PAT0210', 'MADHURI SRIYAN', 'Female', '', '', '', '', 24, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(211, 'BID000211', NULL, 'A202502010211', 'PAT0211', 'S.SATYAVATHI', 'Female', '', '', '', '', 34, NULL, '9290803240', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(212, 'BID000212', NULL, 'A202502010212', 'PAT0212', 'MAHALAKSHMI', 'Female', '', '', '', '', 50, NULL, '9985446565', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(213, 'BID000213', NULL, 'A202502010213', 'PAT0213', 'SATYANARAYANA', 'Male', '', '', '', '', 54, NULL, '9985446565', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(214, 'BID000214', NULL, 'A202502010214', 'PAT0214', 'N SURYA KANTHAM', 'Female', '', '', '', '', 65, NULL, '9059518051', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(215, 'BID000215', NULL, 'A202502010215', 'PAT0215', 'KN LAXMI', 'Female', '', '', '', '', 74, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(216, 'BID000216', NULL, 'A202502010216', 'PAT0216', 'PARAMESWARA RAO', 'Male', '', '', '', '', 59, NULL, '8121662665', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(217, 'BID000217', NULL, 'A202502010217', 'PAT0217', 'A RAJESWARI', 'Female', '', '', '', '', 68, NULL, '7416763569', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(218, 'BID000218', NULL, 'A202502010218', 'PAT0218', 'MAHA LAXMI', 'Female', '', '', '', '', 50, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(219, 'BID000219', NULL, 'A202502010219', 'PAT0219', 'SATYA NARAYANA', 'Male', '', '', '', '', 54, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(220, 'BID000220', NULL, 'A202502010220', 'PAT0220', 'S SATYAVATHI', 'Female', '', '', '', '', 34, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(221, 'BID000221', NULL, 'A202502010221', 'PAT0221', 'SARWESARA RAO', 'Male', '', '', '', '', 47, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(222, 'BID000222', NULL, 'A202502010222', 'PAT0222', 'JAADESWARI', 'Female', '', '', '', '', 43, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(223, 'BID000223', NULL, 'A202502010223', 'PAT0223', 'PARVATHI', 'Female', '', '', '', '', 68, NULL, '8142629266', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(224, 'BID000224', NULL, 'A202502010224', 'PAT0224', 'NARAYANA YOGI', 'Female', '', '', '', '', 24, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(225, 'BID000225', NULL, 'A202502010225', 'PAT0225', 'SUNDARI', 'Female', '', '', '', '', 84, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(226, 'BID000226', NULL, 'A202502010226', 'PAT0226', 'SYAM SUNDARI', 'Female', '', '', '', '', 74, NULL, '9313881618', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(227, 'BID000227', NULL, 'A202502010227', 'PAT0227', 'I KISHORE', 'Male', '', '', '', '', 48, NULL, '9440543999', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(228, 'BID000228', NULL, 'A202502010228', 'PAT0228', 'M BHANUMATHI', 'Female', '', '', '', '', 75, NULL, '9000666920', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(229, 'BID000229', NULL, 'A202502010229', 'PAT0229', 'CH ANANDHRAO', 'Male', '', '', '', '', 46, NULL, '9866291268', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(230, 'BID000230', NULL, 'A202502010230', 'PAT0230', 'MALLESH', 'Male', '', '', '', '', 56, NULL, '9052890992', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(231, 'BID000231', NULL, 'A202502010231', 'PAT0231', 'KRISHNA MURTHY', 'Male', '', '', '', '', 85, NULL, '7032717001', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(232, 'BID000232', NULL, 'A202502010232', 'PAT0232', 'VVS RAMADEVI', 'Female', '', '', '', '', 62, NULL, '8885515366', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(233, 'BID000233', NULL, 'A202502010233', 'PAT0233', 'V VEERABADRA RAO', 'Male', '', '', '', '', 42, NULL, '9396688355', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(234, 'BID000234', NULL, 'A202502010234', 'PAT0234', 'SUNDARI', 'Female', '', '', '', '', 84, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(235, 'BID000235', NULL, 'A202502010235', 'PAT0235', 'BARADHWAJJI', 'Male', '', '', '', '', 51, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(236, 'BID000236', NULL, 'A202502010236', 'PAT0236', 'KRISHNA RAO', 'Male', '', '', '', '', 59, NULL, '8885869805', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(237, 'BID000237', NULL, 'A202502010237', 'PAT0237', 'MALLESH', 'Male', '', '', '', '', 56, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(238, 'BID000238', NULL, 'A202502010238', 'PAT0238', 'SADAN NAYAK', 'Male', '', '', '', '', 54, NULL, '8249758561', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(239, 'BID000239', NULL, 'A202502010239', 'PAT0239', 'T APPAJI', 'Male', '', '', '', '', 0, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(240, 'BID000240', NULL, 'A202502010240', 'PAT0240', 'SATYANARAYANA RAJU', 'Male', '', '', '', '', 63, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(241, 'BID000241', NULL, 'A202502010241', 'PAT0241', 'M BALA', '', '', '', '', '', 57, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(242, 'BID000242', NULL, 'A202502010242', 'PAT0242', 'SANDEEP', '', '', '', '', '', 36, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(243, 'BID000243', NULL, 'A202502010243', 'PAT0243', 'CHABDRA SHEKAR', '', '', '', '', '', 546, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(244, 'BID000244', NULL, 'A202502010244', 'PAT0244', 'PRAVEEN', '', '', '', '', '', 35, NULL, '9966436165', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(245, 'BID000245', NULL, 'A202502010245', 'PAT0245', 'M PANENDRA', '', '', '', '', '', 0, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(246, 'BID000246', NULL, 'A202502010246', 'PAT0246', 'SHIVA RAMA KRISHNA', '', '', '', '', '', 62, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(247, 'BID000247', NULL, 'A202502010247', 'PAT0247', 'PAPA RAO', '', '', '', '', '', 76, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(248, 'BID000248', NULL, 'A202502010248', 'PAT0248', 'JVL NARASIMHA RAO', '', '', '', '', '', 55, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(249, 'BID000249', NULL, 'A202502010249', 'PAT0249', 'T SAIDAIAH', '', '', '', '', '', 76, NULL, '9848722288', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(250, 'BID000250', NULL, 'A202502010250', 'PAT0250', 'M SUNDARI', '', '', '', '', '', 84, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(251, 'BID000251', NULL, 'A202502010251', 'PAT0251', 'R JOGA RAO', '', '', '', '', '', 68, NULL, '9393282898', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(252, 'BID000252', NULL, 'A202502010252', 'PAT0252', 'V KRISHNA RAO', '', '', '', '', '', 54, NULL, '9440764012', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(253, 'BID000253', NULL, 'A202502010253', 'PAT0253', 'SIMHACHALAM', '', '', '', '', '', 49, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(254, 'BID000254', NULL, 'A202502010254', 'PAT0254', 'ASHA LATHA', '', '', '', '', '', 55, NULL, '9494939000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(255, 'BID000255', NULL, 'A202502010255', 'PAT0255', 'BHARATHI', '', '', '', '', '', 70, NULL, '9701971743', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(256, 'BID000256', NULL, 'A202502010256', 'PAT0256', 'VENKATA RAMANAMA', '', '', '', '', '', 54, NULL, '7674047868', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(257, 'BID000257', NULL, 'A202502010257', 'PAT0257', 'MALLESH', '', '', '', '', '', 56, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(258, 'BID000258', NULL, 'A202502010258', 'PAT0258', 'T APPAJI', '', '', '', '', '', 65, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(259, 'BID000259', NULL, 'A202502010259', 'PAT0259', 'PKV RAO', '', '', '', '', '', 65, NULL, '8978947444', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(260, 'BID000260', NULL, 'A202502010260', 'PAT0260', 'GSRK GOVINDA', '', '', '', '', '', 53, NULL, '9440360378', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(261, 'BID000261', NULL, 'A202502010261', 'PAT0261', 'PRABADH BAI', '', '', '', '', '', 40, NULL, '7305626206', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(262, 'BID000262', NULL, 'A202502010262', 'PAT0262', 'MADHAS DAS', '', '', '', '', '', 46, NULL, '7682072241', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(263, 'BID000263', NULL, 'A202502010263', 'PAT0263', 'T DHANUJAI RAO', '', '', '', '', '', 40, NULL, '8328438070', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(264, 'BID000264', NULL, 'A202502010264', 'PAT0264', 'SANGITHA', '', '', '', '', '', 23, NULL, '9668458312', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(265, 'BID000265', NULL, 'A202502010265', 'PAT0265', 'KOWSHALYA', '', '', '', '', '', 45, NULL, '9337667307', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(266, 'BID000266', NULL, 'A202502010266', 'PAT0266', 'G.NARAYANA', '', '', '', '', '', 47, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(267, 'BID000267', NULL, 'A202502010267', 'PAT0267', 'SHANUMUKA RAO', '', '', '', '', '', 53, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(268, 'BID000268', NULL, 'A202502010268', 'PAT0268', 'V.DURGA', '', '', '', '', '', 68, NULL, '7673981888', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(269, 'BID000269', NULL, 'A202502010269', 'PAT0269', 'ASHA LATHA', '', '', '', '', '', 58, NULL, '9494939000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(270, 'BID000270', NULL, 'A202502010270', 'PAT0270', 'SURYA KANTHAM', '', '', '', '', '', 65, NULL, '9059518051', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(271, 'BID000271', NULL, 'A202502010271', 'PAT0271', 'SUNDARI PB', '', '', '', '', '', 84, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(272, 'BID000272', NULL, 'A202502010272', 'PAT0272', 'K VIJAYA', '', '', '', '', '', 37, NULL, '9652666302', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(273, 'BID000273', NULL, 'A202502010273', 'PAT0273', 'Y PRATHAP KUMAR', '', '', '', '', '', 44, NULL, '9949353919', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(274, 'BID000274', NULL, 'A202502010274', 'PAT0274', 'RAMA CHANDRA RAJU', '', '', '', '', '', 63, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(275, 'BID000275', NULL, 'A202502010275', 'PAT0275', 'PADMA', '', '', '', '', '', 47, NULL, '8327740784', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `appointment_online` (`appoint_id`, `bill_id`, `bill_date`, `appoint_register_id`, `appoint_unicode`, `patient_name`, `gender`, `systolic`, `diastolic`, `temperature`, `glucose_level`, `age`, `dob`, `mobile_number`, `patient_email`, `appoint_date`, `doctor_name`, `start_time`, `end_time`, `check_in`, `check_out`, `invoice_payment`, `doctor_fee`, `appoint_status`, `visitor_status`, `org_id`, `created_by`, `modified_by`, `create_date_time`, `amount_method`, `cash_amount`, `amount`, `bpSit_systolic`, `bpSit_diastolic`, `bpStand_systolic`, `bpStand_diastolic`, `weight`, `height`, `bmi`, `heart_rate`, `grbs`, `spO2`, `patient_overview`, `transaction_number`, `transaction_amount`, `concession_name`, `concession_type`, `concession_value`, `final_amount`, `respiration_rate`, `valid_from`, `valid_to`, `appointment_status`, `patient_history`, `queue_order`, `referred_by`, `referral_hospital`, `referral_notes`, `referral_type`) VALUES
(276, 'BID000276', NULL, 'A202502010276', 'PAT0276', 'CHANDRA KALA', '', '', '', '', '', 28, NULL, '9121425254', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(277, 'BID000277', NULL, 'A202502010277', 'PAT0277', 'B.ESWAR REDDY', '', '', '', '', '', 64, NULL, '9642558038', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(278, 'BID000278', NULL, 'A202502010278', 'PAT0278', 'T.DHANUNJAY RAO', '', '', '', '', '', 40, NULL, '8328438070', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(279, 'BID000279', NULL, 'A202502010279', 'PAT0279', 'G.RAJESH', '', '', '', '', '', 39, NULL, '9642785172', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(280, 'BID000280', NULL, 'A202502010280', 'PAT0280', 'BASHEERUDDIN', '', '', '', '', '', 35, NULL, '7036373364', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(281, 'BID000281', NULL, 'A202502010281', 'PAT0281', 'J UMA MAHESHWARI', '', '', '', '', '', 39, NULL, '8317624818', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(282, 'BID000282', NULL, 'A202502010282', 'PAT0282', 'RAVI CHANDRA', '', '', '', '', '', 35, NULL, '7989238651', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(283, 'BID000283', NULL, 'A202502010283', 'PAT0283', 'SHIVA RAMA KRISHNA', '', '', '', '', '', 62, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(284, 'BID000284', NULL, 'A202502010284', 'PAT0284', 'VENKATESH', '', '', '', '', '', 25, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(285, 'BID000285', NULL, 'A202502010285', 'PAT0285', 'KARUNAKAR DAS', '', '', '', '', '', 58, NULL, '8763840056', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(286, 'BID000286', NULL, 'A202502010286', 'PAT0286', 'K SURYANARAYANA RAO', '', '', '', '', '', 64, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(287, 'BID000287', NULL, 'A202502010287', 'PAT0287', 'SAMARENDRA SEKHAR RAO', '', '', '', '', '', 42, NULL, '9776924554', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(288, 'BID000288', NULL, 'A202502010288', 'PAT0288', 'CH JAYA LAXMI', '', '', '', '', '', 77, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(289, 'BID000289', NULL, 'A202502010289', 'PAT0289', 'KVB ARUN KUMAR', '', '', '', '', '', 61, NULL, '7760946427', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(290, 'BID000290', NULL, 'A202502010290', 'PAT0290', 'GOVINDA RAO', '', '', '', '', '', 59, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(291, 'BID000291', NULL, 'A202502010291', 'PAT0291', 'UMA MAHESHWARI', '', '', '', '', '', 39, NULL, '8317624818', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(292, 'BID000292', NULL, 'A202502010292', 'PAT0292', 'MAHENDRA KUMAR', '', '', '', '', '', 44, NULL, '8978359033', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(293, 'BID000293', NULL, 'A202502010293', 'PAT0293', 'JOGA RAO', '', '', '', '', '', 62, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(294, 'BID000294', NULL, 'A202502010294', 'PAT0294', 'R.SRINIVAS RAO', '', '', '', '', '', 60, NULL, '9441343837', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(295, 'BID000295', NULL, 'A202502010295', 'PAT0295', 'SAMPATH KUMAR', '', '', '', '', '', 65, NULL, '8500669646', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(296, 'BID000296', NULL, 'A202502010296', 'PAT0296', 'VENKAT', '', '', '', '', '', 50, NULL, '9866094069', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(297, 'BID000297', NULL, 'A202502010297', 'PAT0297', 'ACCHAYAMMA', '', '', '', '', '', 64, NULL, '7013973720', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(298, 'BID000298', NULL, 'A202502010298', 'PAT0298', 'PKV RAO', '', '', '', '', '', 65, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(299, 'BID000299', NULL, 'A202502010299', 'PAT0299', 'PRATHAP KUMAR', '', '', '', '', '', 44, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(300, 'BID000300', NULL, 'A202502010300', 'PAT0300', 'K.GANGAM', '', '', '', '', '', 72, NULL, '9949333380', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(301, 'BID000301', NULL, 'A202502010301', 'PAT0301', 'VAIKUNTAM', 'Male', '', '', '', '', 74, NULL, '9000267056', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(302, 'BID000302', NULL, 'A202502010302', 'PAT0302', 'SOMESH', 'Male', '', '', '', '', 51, NULL, '9866481591', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(303, 'BID000303', NULL, 'A202502010303', 'PAT0303', 'K.DHANALAKSHMI', 'Female', '', '', '', '', 75, NULL, '9440996481', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(304, 'BID000304', NULL, 'A202502010304', 'PAT0304', 'PATNAIK', 'Male', '', '', '', '', 58, NULL, '9437206844', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(305, 'BID000305', NULL, 'A202502010305', 'PAT0305', 'ABHISHEK PATNAIK', 'Male', '', '', '', '', 22, NULL, '9437206844', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(306, 'BID000306', NULL, 'A202502010306', 'PAT0306', 'MAHESWAR RAO', 'Male', '', '', '', '', 72, NULL, '8897148124', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(307, 'BID000307', NULL, 'A202502010307', 'PAT0307', 'K.S.RAJU', 'Male', '', '', '', '', 44, NULL, '9111216999', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(308, 'BID000308', NULL, 'A202502010308', 'PAT0308', 'RAMA LAKSHMI', 'Female', '', '', '', '', 51, NULL, '7893196223', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(309, 'BID000309', NULL, 'A202502010309', 'PAT0309', 'PRABHAKAR', 'Male', '', '', '', '', 54, NULL, '9849684229', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(310, 'BID000310', NULL, 'A202502010310', 'PAT0310', 'SHANKAR RAO', 'Male', '', '', '', '', 49, NULL, '9866510214', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(311, 'BID000311', NULL, 'A202502010311', 'PAT0311', 'APPAJI', 'Male', '', '', '', '', 65, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(312, 'BID000312', NULL, 'A202502010312', 'PAT0312', 'G.NARASIMHACHARI', 'Male', '', '', '', '', 60, NULL, '7389041375', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(313, 'BID000313', NULL, 'A202502010313', 'PAT0313', 'P.V.MURALI KRISHNA', 'Male', '', '', '', '', 58, NULL, '7675813836', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(314, 'BID000314', NULL, 'A202502010314', 'PAT0314', 'APPA RAO', 'Male', '', '', '', '', 67, NULL, '9247875498', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(315, 'BID000315', NULL, 'A202502010315', 'PAT0315', 'VASANTH', 'Male', '', '', '', '', 40, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(316, 'BID000316', NULL, 'A202502010316', 'PAT0316', 'RENUKA HIRAI', 'Female', '', '', '', '', 46, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(317, 'BID000317', NULL, 'A202502010317', 'PAT0317', 'ASHOK RAJU', 'Male', '', '', '', '', 43, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(318, 'BID000318', NULL, 'A202502010318', 'PAT0318', '', '', '', '', '', '', 0, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(319, 'BID000319', NULL, 'A202502010319', 'PAT0319', 'V MADHURI', 'Female', '', '', '', '', 36, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(320, 'BID000320', NULL, 'A202502010320', 'PAT0320', 'T ELAVARARI', 'Female', '', '', '', '', 36, NULL, '9666557611', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(321, 'BID000321', NULL, 'A202502010321', 'PAT0321', 'A BHARATHI', 'Female', '', '', '', '', 70, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(322, 'BID000322', NULL, 'A202502010322', 'PAT0322', 'A LOLAKSHAMMA', 'Female', '', '', '', '', 50, NULL, '8583880091', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(323, 'BID000323', NULL, 'A202502010323', 'PAT0323', 'ANANTHA SATYANARAYANA', 'Male', '', '', '', '', 70, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(324, 'BID000324', NULL, 'A202502010324', 'PAT0324', 'VENKATA RAMANA', 'Female', '', '', '', '', 62, NULL, '9052711256', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(325, 'BID000325', NULL, 'A202502010325', 'PAT0325', 'INDIRA RANI', 'Female', '', '', '', '', 82, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(326, 'BID000326', NULL, 'A202502010326', 'PAT0326', 'G RAJESH', 'Male', '', '', '', '', 39, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(327, 'BID000327', NULL, 'A202502010327', 'PAT0327', 'LAXMI KNV', 'Female', '', '', '', '', 74, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(328, 'BID000328', NULL, 'A202502010328', 'PAT0328', 'SUJATHA NAGAR', 'Female', '', '', '', '', 45, NULL, '9849911055', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(329, 'BID000329', NULL, 'A202502010329', 'PAT0329', 'H VATIBAR', 'Male', '', '', '', '', 47, NULL, '9848443322', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(330, 'BID000330', NULL, 'A202502010330', 'PAT0330', 'PYDITHALLIAMMA', 'Female', '', '', '', '', 60, NULL, '9052908070', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(331, 'BID000331', NULL, 'A202502010331', 'PAT0331', 'RAVI CHANDRA', 'Male', '', '', '', '', 35, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(332, 'BID000332', NULL, 'A202502010332', 'PAT0332', 'S NARAYANA MURTHY', 'Male', '', '', '', '', 42, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(333, 'BID000333', NULL, 'A202502010333', 'PAT0333', 'M ESWAR RAO', 'Male', '', '', '', '', 45, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(334, 'BID000334', NULL, 'A202502010334', 'PAT0334', 'NIRMALA', 'Female', '', '', '', '', 60, NULL, '7989674078', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(335, 'BID000335', NULL, 'A202502010335', 'PAT0335', 'VASANTHA', 'Female', '', '', '', '', 55, NULL, '8797591189', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(336, 'BID000336', NULL, 'A202502010336', 'PAT0336', 'ASHA LATHA', 'Female', '', '', '', '', 55, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(337, 'BID000337', NULL, 'A202502010337', 'PAT0337', 'TE ELAVARIES', 'Female', '', '', '', '', 36, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(338, 'BID000338', NULL, 'A202502010338', 'PAT0338', 'T PANIGRAHI', 'Male', '', '', '', '', 76, NULL, '7205293190', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(339, 'BID000339', NULL, 'A202502010339', 'PAT0339', 'PKV RAO', 'Male', '', '', '', '', 65, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(340, 'BID000340', NULL, 'A202502010340', 'PAT0340', 'N PALLAVI', 'Female', '', '', '', '', 42, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(341, 'BID000341', NULL, 'A202502010341', 'PAT0341', 'SOMESHWARA RAO', 'Male', '', '', '', '', 51, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(342, 'BID000342', NULL, 'A202502010342', 'PAT0342', 'APPAJI', 'Male', '', '', '', '', 65, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(343, 'BID000343', NULL, 'A202502010343', 'PAT0343', 'JASHMITHA', 'Female', '', '', '', '', 17, NULL, '9989721149', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(344, 'BID000344', NULL, 'A202502010344', 'PAT0344', 'JV LAXMI', 'Female', '', '', '', '', 54, NULL, '9849478722', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(345, 'BID000345', NULL, 'A202502010345', 'PAT0345', 'J RAMACHANDRA RAJU', 'Male', '', '', '', '', 69, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(346, 'BID000346', NULL, 'A202502010346', 'PAT0346', 'SATYA NARAYANA', 'Male', '', '', '', '', 54, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(347, 'BID000347', NULL, 'A202502010347', 'PAT0347', 'SAMBHA MURTHY', 'Male', '', '', '', '', 75, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(348, 'BID000348', NULL, 'A202502010348', 'PAT0348', 'SATYAVATHI', 'Female', '', '', '', '', 50, NULL, '9573647135', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(349, 'BID000349', NULL, 'A202502010349', 'PAT0349', 'SRINU', 'Male', '', '', '', '', 43, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(350, 'BID000350', NULL, 'A202502010350', 'PAT0350', 'K SANGAM', 'Male', '', '', '', '', 74, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(351, 'BID000351', NULL, 'A202502010351', 'PAT0351', 'SHANKAR RAO', 'Male', '', '', '', '', 49, NULL, '7569940517', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(352, 'BID000352', NULL, 'A202502010352', 'PAT0352', 'K KASIVISWERA RAO', 'Male', '', '', '', '', 77, NULL, '9440669284', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(353, 'BID000353', NULL, 'A202502010353', 'PAT0353', 'JAYA LAXMI', 'Female', '', '', '', '', 70, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(354, 'BID000354', NULL, 'A202502010354', 'PAT0354', 'SANJEE PATANAK', 'Male', '', '', '', '', 57, NULL, '7989571293', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(355, 'BID000355', NULL, 'A202502010355', 'PAT0355', 'BHARATHI RAMANI', 'Female', '', '', '', '', 70, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(356, 'BID000356', NULL, 'A202502010356', 'PAT0356', 'KALAYAN', 'Female', '', '', '', '', 38, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(357, 'BID000357', NULL, 'A202502010357', 'PAT0357', 'PS PRAKESH', 'Female', '', '', '', '', 64, NULL, '9441579929', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(358, 'BID000358', NULL, 'A202502010358', 'PAT0358', 'G RAJESH', 'Male', '', '', '', '', 39, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(359, 'BID000359', NULL, 'A202502010359', 'PAT0359', 'ARUN KUMARSWAIK', 'Male', '', '', '', '', 54, NULL, '9090319093', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(360, 'BID000360', NULL, 'A202502010360', 'PAT0360', 'SATYAVATHI', 'Female', '', '', '', '', 70, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(361, 'BID000361', NULL, 'A202502010361', 'PAT0361', 'SURESH', 'Male', '', '', '', '', 40, NULL, '7008737270', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(362, 'BID000362', NULL, 'A202502010362', 'PAT0362', 'K RAJU', 'Male', '', '', '', '', 44, NULL, '8985494112', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(363, 'BID000363', NULL, 'A202502010363', 'PAT0363', 'FRANCIS', 'Male', '', '', '', '', 60, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(364, 'BID000364', NULL, 'A202502010364', 'PAT0364', 'G RAMYA', 'Female', '', '', '', '', 40, NULL, '9776204044', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(365, 'BID000365', NULL, 'A202502010365', 'PAT0365', 'G RAMAKRISHNA', 'Male', '', '', '', '', 49, NULL, '9776204044', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(366, 'BID000366', NULL, 'A202502010366', 'PAT0366', 'MAHENDRA', 'Male', '', '', '', '', 33, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(367, 'BID000367', NULL, 'A202502010367', 'PAT0367', 'THAPAR PANDA', 'Male', '', '', '', '', 15, NULL, '8018449023', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(368, 'BID000368', NULL, 'A202502010368', 'PAT0368', 'N AVANTHI', 'Female', '', '', '', '', 38, NULL, '8328198572', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(369, 'BID000369', NULL, 'A202502010369', 'PAT0369', 'V SATYA NARAYANA', 'Male', '', '', '', '', 70, NULL, '9966177938', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(370, 'BID000370', NULL, 'A202502010370', 'PAT0370', 'ALISHA', 'Female', '', '', '', '', 23, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(371, 'BID000371', NULL, 'A202502010371', 'PAT0371', 'RANJITH', 'Female', '', '', '', '', 42, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(372, 'BID000372', NULL, 'A202502010372', 'PAT0372', 'DHRUV', 'Male', '', '', '', '', 15, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(373, 'BID000373', NULL, 'A202502010373', 'PAT0373', 'VV RAVI PRAKESH', 'Male', '', '', '', '', 57, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(374, 'BID000374', NULL, 'A202502010374', 'PAT0374', 'P SHANTHI', 'Female', '', '', '', '', 42, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(375, 'BID000375', NULL, 'A202502010375', 'PAT0375', 'SHIVA RAMAKRISHNA', 'Male', '', '', '', '', 62, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(376, 'BID000376', NULL, 'A202502010376', 'PAT0376', 'NARASIMHACHARI', 'Male', '', '', '', '', 60, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(377, 'BID000377', NULL, 'A202502010377', 'PAT0377', 'M LAXMI', 'Female', '', '', '', '', 43, NULL, '9949149529', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(378, 'BID000378', NULL, 'A202502010378', 'PAT0378', 'LITHIKHARI', 'Female', '', '', '', '', 15, NULL, '9337118137', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(379, 'BID000379', NULL, 'A202502010379', 'PAT0379', 'V NIRMALA', 'Female', '', '', '', '', 75, NULL, '9440123397', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(380, 'BID000380', NULL, 'A202502010380', 'PAT0380', 'SATYANARAYANA', 'Male', '', '', '', '', 63, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(381, 'BID000381', NULL, 'A202502010381', 'PAT0381', 'RANJITHA', 'Female', '', '', '', '', 42, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(382, 'BID000382', NULL, 'A202502010382', 'PAT0382', 'MALLESU', 'Male', '', '', '', '', 57, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(383, 'BID000383', NULL, 'A202502010383', 'PAT0383', 'M.MANGAMMA', 'Female', '', '', '', '', 47, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(384, 'BID000384', NULL, 'A202502010384', 'PAT0384', 'SIVA SANKAR', 'Male', '', '', '', '', 41, NULL, '9989547507', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(385, 'BID000385', NULL, 'A202502010385', 'PAT0385', 'INDIRA RANI', 'Female', '', '', '', '', 84, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(386, 'BID000386', NULL, 'A202502010386', 'PAT0386', 'BHEEMA RAJU', 'Male', '', '', '', '', 0, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(387, 'BID000387', NULL, 'A202502010387', 'PAT0387', 'RANJITHA', 'Female', '', '', '', '', 42, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(388, 'BID000388', NULL, 'A202502010388', 'PAT0388', 'HASENU', 'Male', '', '', '', '', 52, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(389, 'BID000389', NULL, 'A202502010389', 'PAT0389', 'SRINIVAS', 'Male', '', '', '', '', 42, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(390, 'BID000390', NULL, 'A202502010390', 'PAT0390', 'M LAKSHMI', 'Female', '', '', '', '', 43, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(391, 'BID000391', NULL, 'A202502010391', 'PAT0391', 'DEENA MANI', 'Female', '', '', '', '', 64, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(392, 'BID000392', NULL, 'A202502010392', 'PAT0392', 'KANTHAMMA', 'Female', '', '', '', '', 71, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(393, 'BID000393', NULL, 'A202502010393', 'PAT0393', 'T.ADHI LAKSHMI', 'Female', '', '', '', '', 56, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(394, 'BID000394', NULL, 'A202502010394', 'PAT0394', 'VALSI', 'Female', '', '', '', '', 69, NULL, '9177353602', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(395, 'BID000395', NULL, 'A202502010395', 'PAT0395', 'RAMACHANDRA RAJU', 'Male', '', '', '', '', 68, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(396, 'BID000396', NULL, 'A202502010396', 'PAT0396', 'D.SIVA SHANKARAN', 'Male', '', '', '', '', 76, NULL, '9246614633', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(397, 'BID000397', NULL, 'A202502010397', 'PAT0397', 'NEERAJ', 'Male', '', '', '', '', 32, NULL, '9877818206', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(398, 'BID000398', NULL, 'A202502010398', 'PAT0398', 'SHIVA PRASODH', 'Male', '', '', '', '', 46, NULL, '9885829808', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(399, 'BID000399', NULL, 'A202502010399', 'PAT0399', 'MALLESHU', 'Male', '', '', '', '', 56, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(400, 'BID000400', NULL, 'A202502010400', 'PAT0400', 'DILEEP', 'Male', '', '', '', '', 47, NULL, '9668410294', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(401, 'BID000401', NULL, 'A202502010401', 'PAT0401', 'GOBINDAR GOSH', 'Male', '', '', '', '', 40, NULL, '7077653065', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(402, 'BID000402', NULL, 'A202502010402', 'PAT0402', 'PRAFULLA RAO MANDA', 'Male', '', '', '', '', 42, NULL, '9861558202', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(403, 'BID000403', NULL, 'A202502010403', 'PAT0403', 'KRISHNAPADA BISWAS', 'Male', '', '', '', '', 56, NULL, '9861991744', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(404, 'BID000404', NULL, 'A202502010404', 'PAT0404', 'CH.PADMA', 'Female', '', '', '', '', 44, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(405, 'BID000405', NULL, 'A202502010405', 'PAT0405', 'CH.JAYALAKSHMI', 'Female', '', '', '', '', 77, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(406, 'BID000406', NULL, 'A202502010406', 'PAT0406', 'SUJATHA BANARZEA', 'Female', '', '', '', '', 40, NULL, '8260003606', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(407, 'BID000407', NULL, 'A202502010407', 'PAT0407', 'GOPAL PRASAD', 'Male', '', '', '', '', 55, NULL, '7749812595', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(408, 'BID000408', NULL, 'A202502010408', 'PAT0408', 'M.SRIRAMULU', 'Male', '', '', '', '', 65, NULL, '9866999686', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(409, 'BID000409', NULL, 'A202502010409', 'PAT0409', 'VENKATA LAKSHMI', 'Female', '', '', '', '', 55, NULL, '9177764776', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(410, 'BID000410', NULL, 'A202502010410', 'PAT0410', 'VASANTH', 'Male', '', '', '', '', 42, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(411, 'BID000411', NULL, 'A202502010411', 'PAT0411', 'MOHAN RAO', 'Male', '', '', '', '', 85, NULL, '7989123906', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(412, 'BID000412', NULL, 'A202502010412', 'PAT0412', 'SAMADAS', 'Female', '', '', '', '', 44, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `appointment_online` (`appoint_id`, `bill_id`, `bill_date`, `appoint_register_id`, `appoint_unicode`, `patient_name`, `gender`, `systolic`, `diastolic`, `temperature`, `glucose_level`, `age`, `dob`, `mobile_number`, `patient_email`, `appoint_date`, `doctor_name`, `start_time`, `end_time`, `check_in`, `check_out`, `invoice_payment`, `doctor_fee`, `appoint_status`, `visitor_status`, `org_id`, `created_by`, `modified_by`, `create_date_time`, `amount_method`, `cash_amount`, `amount`, `bpSit_systolic`, `bpSit_diastolic`, `bpStand_systolic`, `bpStand_diastolic`, `weight`, `height`, `bmi`, `heart_rate`, `grbs`, `spO2`, `patient_overview`, `transaction_number`, `transaction_amount`, `concession_name`, `concession_type`, `concession_value`, `final_amount`, `respiration_rate`, `valid_from`, `valid_to`, `appointment_status`, `patient_history`, `queue_order`, `referred_by`, `referral_hospital`, `referral_notes`, `referral_type`) VALUES
(413, 'BID000413', NULL, 'A202502010413', 'PAT0413', 'PARIMAL BHAIRAGI', 'Male', '', '', '', '', 56, NULL, '9938859392', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(414, 'BID000414', NULL, 'A202502010414', 'PAT0414', 'BAIKUNTA DHARA', 'Male', '', '', '', '', 39, NULL, '9556918907', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(415, 'BID000415', NULL, 'A202502010415', 'PAT0415', 'BHASKAR DAKWER', 'Male', '', '', '', '', 66, NULL, '9178010305', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(416, 'BID000416', NULL, 'A202502010416', 'PAT0416', 'PRASADH', 'Male', '', '', '', '', 61, NULL, '9492725353', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(417, 'BID000417', NULL, 'A202502010417', 'PAT0417', 'SRI RAMULU', 'Male', '', '', '', '', 65, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(418, 'BID000418', NULL, 'A202502010418', 'PAT0418', 'D. KAMALA', 'Female', '', '', '', '', 73, NULL, '9393926291', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(419, 'BID000419', NULL, 'A202502010419', 'PAT0419', 'YELLAMMA', 'Female', '', '', '', '', 62, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(420, 'BID000420', NULL, 'A202502010420', 'PAT0420', 'YV SUBBHA RAO', 'Male', '', '', '', '', 59, NULL, '9848534158', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(421, 'BID000421', NULL, 'A202502010421', 'PAT0421', 'HYMAVATHI', 'Female', '', '', '', '', 44, NULL, '9398824210', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(422, 'BID000422', NULL, 'A202502010422', 'PAT0422', 'RAMACHANDRA RAJU', 'Male', '', '', '', '', 64, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(423, 'BID000423', NULL, 'A202502010423', 'PAT0423', 'SUSEELA', 'Female', '', '', '', '', 55, NULL, '9700955959', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(424, 'BID000424', NULL, 'A202502010424', 'PAT0424', 'VENKATA RAMAYYA', 'Male', '', '', '', '', 92, NULL, '7661017244', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(425, 'BID000425', NULL, 'A202502010425', 'PAT0425', 'L.DURGA PRASAD', 'Male', '', '', '', '', 54, NULL, '9398824210', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(426, 'BID000426', NULL, 'A202502010426', 'PAT0426', 'S.BHARATHI DEVI', 'Female', '', '', '', '', 85, NULL, '7661017244', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(427, 'BID000427', NULL, 'A202502010427', 'PAT0427', 'SURYA NARAYANA', 'Male', '', '', '', '', 53, NULL, '9502855346', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(428, 'BID000428', NULL, 'A202502010428', 'PAT0428', 'WKA VARDA RAJU', 'Male', '', '', '', '', 61, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(429, 'BID000429', NULL, 'A202502010429', 'PAT0429', 'M SRINDTH', 'Male', '', '', '', '', 54, NULL, '9320406363', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(430, 'BID000430', NULL, 'A202502010430', 'PAT0430', 'SRIRAMULLU', 'Male', '', '', '', '', 65, NULL, '9866999686', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(431, 'BID000431', NULL, 'A202502010431', 'PAT0431', 'SRINADH', 'Male', '', '', '', '', 54, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(432, 'BID000432', NULL, 'A202502010432', 'PAT0432', 'SANGAM', 'Male', '', '', '', '', 74, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(433, 'BID000433', NULL, 'A202502010433', 'PAT0433', 'VENKATALAXMI', 'Female', '', '', '', '', 55, NULL, '9177764776', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(434, 'BID000434', NULL, 'A202502010434', 'PAT0434', 'SUBHA RAO', 'Male', '', '', '', '', 59, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(435, 'BID000435', NULL, 'A202502010435', 'PAT0435', 'RAMANI', 'Male', '', '', '', '', 54, NULL, '8093256320', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(436, 'BID000436', NULL, 'A202502010436', 'PAT0436', 'PSV GANESH', 'Male', '', '', '', '', 65, NULL, '9490662741', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(437, 'BID000437', NULL, 'A202502010437', 'PAT0437', 'P SAVITHRI', 'Female', '', '', '', '', 60, NULL, '9490662741', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(438, 'BID000438', NULL, 'A202502010438', 'PAT0438', 'SOMADAR', 'Female', '', '', '', '', 44, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(439, 'BID000439', NULL, 'A202502010439', 'PAT0439', 'NS RAJU', '', '', '', '', '', 0, NULL, '8319274077', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(440, 'BID000440', NULL, 'A202502010440', 'PAT0440', 'PREMIKHURI', '', '', '', '', '', 0, NULL, '9437775780', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(441, 'BID000441', NULL, 'A202502010441', 'PAT0441', 'PRASANTH MANOHAR', '', '', '', '', '', 0, NULL, '8169545985', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(442, 'BID000442', NULL, 'A202502010442', 'PAT0442', 'T SRINIVAS RAP', '', '', '', '', '', 0, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(443, 'BID000443', NULL, 'A202502010443', 'PAT0443', 'SURIDEVVUDU', '', '', '', '', '', 0, NULL, '9866971298', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(444, 'BID000444', NULL, 'A202502010444', 'PAT0444', 'M RAJA KUMAR', '', '', '', '', '', 0, NULL, '8186991286', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(445, 'BID000445', NULL, 'A202502010445', 'PAT0445', 'D SIVA SANKHAR', '', '', '', '', '', 0, NULL, '9246614633', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(446, 'BID000446', NULL, 'A202502010446', 'PAT0446', 'G RAJESH', '', '', '', '', '', 0, NULL, '9642785172', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(447, 'BID000447', NULL, 'A202502010447', 'PAT0447', 'PKV RAO', '', '', '', '', '', 0, NULL, '8978947444', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(448, 'BID000448', NULL, 'A202502010448', 'PAT0448', 'D VEKATA LAXMI', '', '', '', '', '', 0, NULL, '9177764776', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(449, 'BID000449', NULL, 'A202502010449', 'PAT0449', 'T SRINIVAS A RAO', 'Male', '', '', '', '', 65, NULL, '9949466685', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(450, 'BID000450', NULL, 'A202502010450', 'PAT0450', 'G KALAYANI', '', '', '', '', '', 0, NULL, '7815894236', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(451, 'BID000451', NULL, 'A202502010451', 'PAT0451', 'SANTHOSH KUMAR', '', '', '', '', '', 0, NULL, '7032328623', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(452, 'BID000452', NULL, 'A202502010452', 'PAT0452', 'R APPALA NAIDU', '', '', '', '', '', 0, NULL, '9951152507', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(453, 'BID000453', NULL, 'A202502010453', 'PAT0453', 'TVP PRATHUSHA', '', '', '', '', '', 0, NULL, '9985438438', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(454, 'BID000454', NULL, 'A202502010454', 'PAT0454', 'T SRAVANI', '', '', '', '', '', 0, NULL, '7893256713', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(455, 'BID000455', NULL, 'A202502010455', 'PAT0455', 'UMA MAHESWARI', '', '', '', '', '', 0, NULL, '6302681323', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(456, 'BID000456', NULL, 'A202502010456', 'PAT0456', 'KNV LAXMI', '', '', '', '', '', 0, NULL, '9871878942', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(457, 'BID000457', NULL, 'A202502010457', 'PAT0457', 'A KISHORE KUMAR', 'Male', '', '', '', '', 44, NULL, '8374965643', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(458, 'BID000458', NULL, 'A202502010458', 'PAT0458', 'B VENKATESWARA RAO', 'Male', '', '', '', '', 70, NULL, '8328198572', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(459, 'BID000459', NULL, 'A202502010459', 'PAT0459', 'B VENKATARAMANA', 'Female', '', '', '', '', 65, NULL, '8328198572', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(460, 'BID000460', NULL, 'A202502010460', 'PAT0460', 'K RAMA KRISHNA', '', '', '', '', '', 0, NULL, '9246630644', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(461, 'BID000461', NULL, 'A202502010461', 'PAT0461', 'SURYANARAYANA', '', '', '', '', '', 0, NULL, '9505219978', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(462, 'BID000462', NULL, 'A202502010462', 'PAT0462', 'SD SAIKUMAR', 'Male', '', '', '', '', 24, NULL, '7093671721', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(463, 'BID000463', NULL, 'A202502010463', 'PAT0463', 'CH SUBHA KUMAR', 'Male', '', '', '', '', 47, NULL, '9966611543', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(464, 'BID000464', NULL, 'A202502010464', 'PAT0464', 'M APPALA NAIDU', 'Male', '', '', '', '', 87, NULL, '7981235552', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(465, 'BID000465', NULL, 'A202502010465', 'PAT0465', 'M ESWAR RAO', 'Male', '', '', '', '', 45, NULL, '7981235552', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(466, 'BID000466', NULL, 'A202502010466', 'PAT0466', 'R LAXMI', '', '', '', '', '', 0, NULL, '9160387803', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(467, 'BID000467', NULL, 'A202502010467', 'PAT0467', 'KOWSHALYA', 'Female', '', '', '', '', 52, NULL, '9337666737', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(468, 'BID000468', NULL, 'A202502010468', 'PAT0468', 'MALLESH', 'Male', '', '', '', '', 56, NULL, '9491698854', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(469, 'BID000469', NULL, 'A202502010469', 'PAT0469', 'V PAVANI', 'Female', '', '', '', '', 33, NULL, '7569901379', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(470, 'BID000470', NULL, 'A202502010470', 'PAT0470', 'SHABEENA', 'Female', '', '', '', '', 36, NULL, '6309898959', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(471, 'BID000471', NULL, 'A202502010471', 'PAT0471', 'J JAYA LAXMI', 'Female', '', '', '', '', 69, NULL, '7981014394', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(472, 'BID000472', NULL, 'A202502010472', 'PAT0472', 'ALISHA', '', '', '', '', '', 0, NULL, '8971605842', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(473, 'BID000473', NULL, 'A202502010473', 'PAT0473', 'M RAJESH', '', '', '', '', '', 0, NULL, '6301537635', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(474, 'BID000474', NULL, 'A202502010474', 'PAT0474', 'R LAXMI', '', '', '', '', '', 0, NULL, '9160387803', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(475, 'BID000475', NULL, 'A202502010475', 'PAT0475', 'ISAA', 'Male', '', '', '', '', 34, NULL, '9701971743', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(476, 'BID000476', NULL, 'A202502010476', 'PAT0476', 'K SURYANARAYANA', 'Male', '', '', '', '', 64, NULL, '0000000000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(477, 'BID000477', NULL, 'A202502010477', 'PAT0477', 'KVG ARUN', 'Male', '', '', '', '', 61, NULL, '9618624054', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(478, 'BID000478', NULL, 'A202502010478', 'PAT0478', 'DVS DHEERAJ', 'Male', '', '', '', '', 26, NULL, '9177368999', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(479, 'BID000479', NULL, 'A202502010479', 'PAT0479', 'G LAXMI NARASAMMA', 'Female', '', '', '', '', 60, NULL, '9885535862', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(480, 'BID000480', NULL, 'A202502010480', 'PAT0480', 'M VEERABABU', 'Male', '', '', '', '', 49, NULL, '7093933448', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(481, 'BID000481', NULL, 'A202502010481', 'PAT0481', 'M AVATHARAM', 'Male', '', '', '', '', 38, NULL, '9505246903', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(482, 'BID000482', NULL, 'A202502010482', 'PAT0482', 'T RAMU', 'Male', '', '', '', '', 33, NULL, '9573277499', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(483, 'BID000483', NULL, 'A202502010483', 'PAT0483', 'MADHU SUDHAKAR RAO', 'Male', '', '', '', '', 66, NULL, '9440090361', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(484, 'BID000484', NULL, 'A202502010484', 'PAT0484', 'V LAKSHMI', '', '', '', '', '', 0, NULL, '9886767967', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(485, 'BID000485', NULL, 'A202502010485', 'PAT0485', 'JV NARASIMHARAO', 'Male', '', '', '', '', 55, NULL, '8885144474', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(486, 'BID000486', NULL, 'A202502010486', 'PAT0486', 'INDRA RANI', 'Male', '', '', '', '', 35, NULL, '9390074703', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(487, 'BID000487', NULL, 'A202502010487', 'PAT0487', 'ASHA LATHA', 'Male', '', '', '', '', 56, NULL, '9494939000', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(488, 'BID000488', NULL, 'A202502010488', 'PAT0488', 'V SRINIVASA RAO', 'Male', '', '', '', '', 0, NULL, '9443884679', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(489, 'BID000489', NULL, 'A202502010489', 'PAT0489', 'A PARVATHI', 'Female', '', '', '', '', 65, NULL, '8247681724', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(490, 'BID000490', NULL, 'A202502010490', 'PAT0490', 'SANTHOSI', 'Female', '', '', '', '', 0, NULL, '9704499136', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(491, 'BID000491', NULL, 'A202502010491', 'PAT0491', 'MATTA LAKSHMAN RAO', 'Male', '', '', '', '', 0, NULL, '9866403430', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(492, 'BID000492', NULL, 'A202502010492', 'PAT0492', 'GURU MURTHY', 'Male', '', '', '', '', 0, NULL, '7010840550', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(493, 'BID000493', NULL, 'A202502010493', 'PAT0493', 'SURYA NARAYANA', 'Male', '', '', '', '', 0, NULL, '9940570110', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(494, 'BID000494', NULL, 'A202502010494', 'PAT0494', 'APPALA NADUDU', 'Male', '', '', '', '', 0, NULL, '9989566121', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(495, 'BID000495', NULL, 'A202502010495', 'PAT0495', 'ESWARA RAO', 'Male', '', '', '', '', 0, NULL, '9676703349', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(496, 'BID000496', NULL, 'A202502010496', 'PAT0496', 'KRISHNA', 'Male', '', '', '', '', 0, NULL, '9989566121', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(497, 'BID000497', NULL, 'A202502010497', 'PAT0497', 'JAYA LAKSHMI', 'Female', '', '', '', '', 0, NULL, '7386676580', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(498, 'BID000498', NULL, 'A202502010498', 'PAT0498', 'VISWANADHYAM', 'Male', '', '', '', '', 0, NULL, '8790299827', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(499, 'BID000499', NULL, 'A202502010499', 'PAT0499', 'KUMAR BHARAT', 'Male', '', '', '', '', 0, NULL, '8895310895', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(500, 'BID000500', NULL, 'A202502010500', 'PAT0500', 'SATYA', 'Male', '', '', '', '', 0, NULL, '8106256695', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(501, 'BID000501', NULL, 'A202502010501', 'PAT0501', 'NARASIMHARAO', 'Male', '', '', '', '', 0, NULL, '9849805458', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(502, 'BID000502', NULL, 'A202502010502', 'PAT0502', 'NARASIMHA RAO', 'Male', '', '', '', '', 0, NULL, '9963526555', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(503, 'BID000503', NULL, 'A202502010503', 'PAT0503', 'APPARAO', 'Male', '', '', '', '', 0, NULL, '9399928353', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(504, 'BID000504', NULL, 'A202502010504', 'PAT0504', 'SATYAVATHI', 'Female', '', '', '', '', 0, NULL, '8688523669', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(505, 'BID000505', NULL, 'A202502010505', 'PAT0505', 'RAMESH NAIDU', 'Male', '', '', '', '', 0, NULL, '9493878877', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(506, 'BID000506', NULL, 'A202502010506', 'PAT0506', 'RAVISHANKAR', 'Male', '', '', '', '', 0, NULL, '8309313793', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(507, 'BID000507', NULL, 'A202502010507', 'PAT0507', 'LAKSHMI NAVEEN', '', '', '', '', '', 0, NULL, '9347329910', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(508, 'BID000508', NULL, 'A202502010508', 'PAT0508', 'HARI', 'Male', '', '', '', '', 0, NULL, '9966280480', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(509, 'BID000509', NULL, 'A202502010509', 'PAT0509', 'VARALAKSHMI', 'Female', '', '', '', '', 0, NULL, '8341296866', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(510, 'BID000510', NULL, 'A202502010510', 'PAT0510', 'TEJESH', 'Male', '', '', '', '', 0, NULL, '8186942354', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(511, 'BID000511', '2025-09-09', 'A202502010511', 'PAT0511', 'SRINIVAS', 'Male', '', '', '', '', 0, NULL, '8801335564', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(512, 'BID000512', NULL, 'A202502010512', 'PAT0512', 'MALLESH', 'Male', '', '', '', '', 0, NULL, '9491698854', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(513, 'BID000513', NULL, 'A202502010513', 'PAT0513', 'MEENA', 'Female', '', '', '', '', 0, NULL, '9014006515', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(514, 'BID000514', NULL, 'A202502010514', 'PAT0514', 'TIRUMALA REDDY', 'Male', '', '', '', '', 0, NULL, '9052227995', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(515, 'BID000515', NULL, 'A202502010515', 'PAT0515', 'MURTHY', 'Male', '', '', '', '', 0, NULL, '6300594539', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(516, 'BID000516', NULL, 'A202502010516', 'PAT0516', 'LALITHA', 'Female', '', '', '', '', 0, NULL, '6281213878', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(517, 'BID000517', NULL, 'A202502010517', 'PAT0517', 'RAMPRASAD', 'Male', '', '', '', '', 0, NULL, '8125524999', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(518, 'BID000518', NULL, 'A202502010518', 'PAT0518', 'DHANA LAKSHMI', '', '', '', '', '', 0, NULL, '9440996481', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(519, 'BID000519', NULL, 'A202502010519', 'PAT0519', 'KUSUMA', 'Female', '', '', '', '', 0, NULL, '7993180388', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(520, 'BID000520', NULL, 'A202502010520', 'PAT0520', 'ANANTH SATYA NARAYANA', 'Male', '', '', '', '', 0, NULL, '9885592837', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(521, 'BID000521', NULL, 'A202502010521', 'PAT0521', 'SARADHA LAKSHM', '', '', '', '', '', 0, NULL, '9885592837', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(522, 'BID000522', NULL, 'A202502010522', 'PAT0522', 'ESWARI', 'Female', '', '', '', '', 0, NULL, '7396416800', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(523, 'BID000523', NULL, 'A202502010523', 'PAT0523', 'UDHY KUMAR', 'Male', '', '', '', '', 0, NULL, '9133316800', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(524, 'BID000524', NULL, 'A202502010524', 'PAT0524', 'BHARATH', 'Male', '', '', '', '', 0, NULL, '9704959579', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(525, 'BID000525', NULL, 'A202502010525', 'PAT0525', 'NAGALAKSHMI', 'Female', '', '', '', '', 0, NULL, '9505794841', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(526, 'BID000526', NULL, 'A202502010526', 'PAT0526', 'APPARAO', 'Male', '', '', '', '', 0, NULL, '9949187509', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(527, 'BID000527', NULL, 'A202502010527', 'PAT0527', 'LAXMIKANTH', 'Male', '', '', '', '', 0, NULL, '7337056260', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(528, 'BID000528', NULL, 'A202502010528', 'PAT0528', 'JAGADESWARI', 'Female', '', '', '', '', 0, NULL, '7995813169', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(529, 'BID000529', NULL, 'A202502010529', 'PAT0529', 'PADMAVATHI', 'Female', '', '', '', '', 0, NULL, '9703194053', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(530, 'BID000530', NULL, 'A202502010530', 'PAT0530', 'SRINIVAS', 'Male', '', '', '', '', 0, NULL, '7989123906', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(531, 'BID000531', '2025-05-07', 'A202502010531', 'PAT0531', 'VIJAY KUMAR', 'Male', '', '', '', '', 0, NULL, '9492561599', '', '2025-03-01', 1, '', '', NULL, NULL, '0', 0, '1', '1', 1, 0, 0, '2025-09-29 09:53:07', '', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, '', NULL, '', 'amount', '0.00', 0, '', NULL, NULL, '1', '', NULL, NULL, NULL, NULL, NULL),
(644, 'BID000532', '2025-09-29', 'A202509290001', 'PAT0434', 'SUBHA RAO', 'Male', '', '', '', '', 59, NULL, '0000000000', '', '2025-09-29', 1, '10:26', '10:41', NULL, NULL, '1', 0, '1', '0', 1, 2, 2, '2025-09-29 12:53:52', '', NULL, '500', '120', '80', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '', 500, '', '2025-09-29', '2025-10-12', '1', '', NULL, NULL, NULL, NULL, NULL),
(645, 'BID000533', NULL, 'A202509290002', 'PAT0096', 'AJAY KUMAR', 'Female', '', '', '', '', 20, NULL, '9542427999', '', '2025-09-29', 1, '13:41', '13:56', NULL, NULL, '0', 0, '1', '2', 1, 2, 2, '2025-09-29 12:53:42', '', NULL, '500', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '', 500, '', '2025-09-29', '2025-10-12', '0', '', NULL, NULL, NULL, NULL, NULL),
(646, 'BID000534', NULL, 'A202509290003', 'PAT0094', 'SAMESHWAR RAO', 'Male', '', '', '', '', 49, NULL, '9866481591', '', '2025-09-29', 5, '16:45', '16:57', NULL, NULL, '0', 0, '1', '1', 1, 2, 2, '2025-09-29 09:53:07', '', NULL, '300', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '', 300, '', '2025-09-29', '2025-10-12', '0', '', NULL, NULL, NULL, NULL, NULL),
(647, 'BID000535', NULL, 'A202509290004', 'PAT0168', 'K. KANTHAMMA', 'Female', '', '', '', '', 70, NULL, '9951990972', '', '2025-09-29', 5, '17:21', '17:33', NULL, NULL, '0', 0, '1', '1', 1, 12, 12, '2025-09-29 09:53:07', '', NULL, '300', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '', 300, '', '2025-09-29', '2025-10-12', '0', '', NULL, NULL, NULL, NULL, NULL),
(648, 'BID000536', NULL, 'A202509290005', 'PAT0012', 'G NARAYANA', 'Male', '', '', '', '', 47, NULL, '7077538378', '', '2025-09-29', 5, '17:57', '18:09', NULL, NULL, '0', 0, '1', '1', 1, 12, 12, '2025-09-29 09:53:07', '', NULL, '300', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '', 300, '', '2025-09-29', '2025-10-12', '0', '', NULL, NULL, NULL, NULL, NULL),
(649, 'BID000537', NULL, 'A202509290006', 'PAT0004', 'K.BHASKAR RAO', 'Male', '', '', '', '', 66, NULL, '9550575634', '', '2025-09-29', 1, '14:56', '15:11', NULL, NULL, '0', 0, '1', '1', 1, 2, 2, '2025-09-29 09:53:07', '', NULL, '500', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '', 500, '', '2025-09-29', '2025-10-12', '0', '', NULL, NULL, NULL, NULL, NULL),
(650, 'BID000538', NULL, 'A202509290007', 'PAT0102', 'P SHANMUKA RAO', 'Male', '', '', '', '', 53, NULL, '9502606136', '', '2025-09-29', 5, '16:57', '17:09', NULL, NULL, '0', 0, '1', '1', 1, 2, 2, '2025-09-29 09:53:07', '', NULL, '300', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '', 300, '', '2025-09-29', '2025-10-12', '0', '', NULL, NULL, NULL, NULL, NULL),
(651, 'BID000539', NULL, 'A202509300001', 'PAT0005', 'CH JAYA LAXMI', 'Female', '', '', '', '', 78, NULL, '7981501223', '', '2025-09-30', 1, '14:00', '14:15', NULL, NULL, '1', 0, '1', '3', 1, 2, 2, '2025-09-30 06:06:17', 'Cash', NULL, '500', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '0', 500, '', '2025-09-30', '2025-10-13', '0', '', NULL, NULL, NULL, NULL, NULL),
(652, 'BID000540', NULL, 'A202510010001', 'PAT0006', 'V YERRAMMA', 'Female', '', '', '', '', 51, NULL, '9966188420', '', '2025-10-01', 1, '10:40', '10:55', NULL, NULL, '1', 0, '1', '1', 1, 2, 2, '2025-10-01 04:55:29', 'Cash', NULL, '500', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '0', 500, '', '2025-10-01', '2025-10-14', '0', '', NULL, NULL, NULL, NULL, NULL),
(653, 'BID000541', NULL, 'A202510010002', 'PAT0010', 'G KALAYANI', 'Female', '', '', '', '', 49, NULL, '7995908643', '', '2025-10-01', 1, '10:55', '11:10', NULL, NULL, '0', 0, '1', '1', 1, 2, 2, '2025-10-01 04:55:48', '', NULL, '500', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '', 500, '', '2025-10-01', '2025-10-14', '0', '', NULL, NULL, NULL, NULL, NULL),
(654, 'BID000542', NULL, 'A202510060001', 'PAT0046', 'SUNDARI', 'Female', '', '', '', '', 87, NULL, '9686481900', '', '2025-10-06', 1, '17:39', '17:54', NULL, NULL, '1', 0, '1', '0', 1, 2, 2, '2025-10-06 11:51:35', 'Cash', NULL, '500', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '1', 'percentage', '50', 250, '', '2025-10-06', '2025-10-19', '1', '', NULL, NULL, NULL, NULL, NULL),
(655, 'BID000543', '2025-10-07', 'A202510060002', 'PAT0050', 'Y BHEEMA RAJU', 'Male', '', '', '', '', 78, NULL, '9000665715', '', '2025-10-06', 1, '18:09', '18:24', NULL, NULL, '0', 0, '1', '0', 1, 2, 2, '2025-10-07 06:51:58', '', NULL, '500', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '', 500, '', '2025-10-06', '2025-10-19', '0', '', NULL, NULL, NULL, NULL, NULL),
(656, 'BID000544', NULL, 'A202604080001', 'PAT0012', 'G NARAYANA', 'Male', '', '', '', '', 47, NULL, '7077538378', '', '2026-04-08', 1, '13:58', '14:13', NULL, NULL, '0', 0, '1', '1', 1, 2, 2, '2026-04-08 08:16:31', '', NULL, '500', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '', 500, '', '2026-04-08', '2026-04-21', '1', '', NULL, NULL, NULL, NULL, NULL),
(657, 'BID000545', '2026-04-08', 'A202604080002', 'PAT0006', 'V YERRAMMA', 'Female', '', '', '', '', 51, NULL, '9966188420', '', '2026-04-08', 1, '14:13', '14:28', NULL, NULL, '0', 0, '1', '1', 1, 2, 2, '2026-04-08 08:13:55', '', NULL, '500', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '', 500, '', '2026-04-08', '2026-04-21', '0', '', NULL, NULL, NULL, NULL, NULL),
(658, 'BID000546', '2026-04-15', 'A202604100001', 'PAT0013', 'CH PADMA', 'Female', '', '', '', '', 44, NULL, '8885991222', '', '2026-04-10', 1, '17:26', '17:41', NULL, NULL, '0', 0, '1', '1', 1, 2, 2, '2026-04-15 04:20:33', '', NULL, '500', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '', 500, '', '2026-04-10', '2026-04-23', '1', '', NULL, NULL, NULL, NULL, NULL),
(659, 'BID000547', '2026-04-15', 'A202604150001', 'PAT0532', 'durga lakshmi', 'Female', '', '', '', '', 34, '1991-06-18', '7032760271', '', '2026-04-15', 1, '10:40', '10:55', NULL, NULL, '1', 0, '1', '1', 1, 2, 2, '2026-04-15 11:38:45', '', 100.00, '500', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '', 500, '', '2026-04-15', '2026-04-28', '1', '', NULL, NULL, NULL, NULL, NULL),
(660, 'BID000548', '2026-04-15', 'A202604150002', 'PAT0010', 'G KALAYANI', 'Female', '', '', '', '', 55, '1970-04-29', '7995908643', '', '2026-04-15', 1, '16:25', '16:40', NULL, NULL, '1', 0, '1', '1', 1, 2, 2, '2026-04-29 07:10:51', 'Both (Cash + UPI)', 300.00, '500', '', '', '', '', '', '', '', '', '', '', '', '12345', 200.00, '', '', '0', 500, '', '2026-04-15', '2026-04-28', '0', '', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `appointment_online` (`appoint_id`, `bill_id`, `bill_date`, `appoint_register_id`, `appoint_unicode`, `patient_name`, `gender`, `systolic`, `diastolic`, `temperature`, `glucose_level`, `age`, `dob`, `mobile_number`, `patient_email`, `appoint_date`, `doctor_name`, `start_time`, `end_time`, `check_in`, `check_out`, `invoice_payment`, `doctor_fee`, `appoint_status`, `visitor_status`, `org_id`, `created_by`, `modified_by`, `create_date_time`, `amount_method`, `cash_amount`, `amount`, `bpSit_systolic`, `bpSit_diastolic`, `bpStand_systolic`, `bpStand_diastolic`, `weight`, `height`, `bmi`, `heart_rate`, `grbs`, `spO2`, `patient_overview`, `transaction_number`, `transaction_amount`, `concession_name`, `concession_type`, `concession_value`, `final_amount`, `respiration_rate`, `valid_from`, `valid_to`, `appointment_status`, `patient_history`, `queue_order`, `referred_by`, `referral_hospital`, `referral_notes`, `referral_type`) VALUES
(661, 'BID000549', NULL, 'A202604160001', 'PAT0533', 'durga prasad', 'Male', '', '', '', '', 30, '1995-04-29', '8787878778', '', '2026-04-16', 1, '16:30', '16:45', NULL, NULL, '0', 0, '1', '1', 1, 2, 2, '2026-04-29 07:09:54', '', NULL, '500', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '', 500, '', '2026-04-16', '2026-04-29', '1', '', NULL, NULL, NULL, NULL, NULL),
(662, 'BID000550', '2026-05-06', 'A202605060001', 'PAT0532', 'durga lakshmi', 'Female', '', '', '', '', 34, '0000-00-00', '7032760271', '', '2026-05-06', 1, '14:17', '14:32', NULL, NULL, '1', 0, '1', '1', 1, 2, 2, '2026-05-06 15:52:54', '', 300.00, '500', '', '', '', '', '', '', '', '', '', '', '', '', 200.00, '', '', '', 500, '', '2026-05-06', '2026-05-19', '0', '', 2, 'Dr.ravi', 'ABC hospital', 'family friend', 'External'),
(663, 'BID000551', '2026-05-06', 'A202605060002', 'PAT0012', 'G NARAYANA', 'Male', '', '', '', '', 54, '1971-05-07', '7077538378', '', '2026-05-06', 1, '14:47', '15:02', NULL, NULL, '1', 0, '1', '1', 1, 2, 2, '2026-05-06 10:57:43', 'Cash', 0.00, '500', '', '', '', '', '', '', '', '', '', '', '', '', 0.00, '', '', '0', 500, '', '2026-05-06', '2026-05-19', '0', '', 1, NULL, NULL, NULL, NULL),
(664, 'BID000552', '2026-05-07', 'A202605070001', 'PAT0534', 'Ji won', 'Female', '', '', '', '', 49, '1976-05-08', '8676867589', '', '2026-05-07', 1, '14:58', '15:13', NULL, NULL, '1', 0, '1', '0', 1, 2, 2, '2026-05-07 16:15:12', 'Both (Cash + UPI)', 200.00, '500', '', '', '', '', '', '', '', '', '', '', '', '876y56', 300.00, '', '', '0', 500, '', '2026-05-07', '2026-05-20', '1', '', NULL, 'Dr. Aswin', 'medicover', 'take much care', 'Internal'),
(665, 'BID000553', NULL, 'A202605070002', 'PAT0011', 'G LAXMI', 'Female', '', '', '', '', 49, '1976-05-08', '8699494819', '', '2026-05-07', 1, '15:43', '15:58', NULL, NULL, '0', 0, '1', '1', 1, 2, 2, '2026-05-07 07:28:20', '', NULL, '500', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '', 500, '', '2026-05-07', '2026-05-20', '0', '', NULL, '', '', '', ''),
(666, 'BID000554', NULL, 'A202605070003', 'PAT0010', 'G KALAYANI', 'Female', '', '', '', '', 55, '1970-04-29', '7995908643', '', '2026-05-07', 5, '18:39', '18:51', NULL, NULL, '0', 0, '1', '1', 1, 2, 2, '2026-05-07 11:10:51', '', NULL, '300', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '', 300, '', '2026-05-07', '2026-05-20', '0', '', NULL, 'Dr. Aswin', 'medicover', '', 'Internal'),
(667, 'BID000555', '2026-05-08', 'A202605080001', 'PAT0116', 'P NARAYANA RAO', 'Male', '', '', '', '', 31, '1994-05-09', '9398380191', '', '2026-05-08', 1, '6:38', '6:53', NULL, NULL, '0', 0, '1', '1', 1, 2, 2, '2026-05-08 04:45:16', '', NULL, '500', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '', 500, '', '2026-05-08', '2026-05-21', '0', '', NULL, 'Dr. Aswin', 'medicover', '', 'External'),
(668, 'BID000556', '2026-05-20', 'A202605200001', 'PAT0535', 'Tarun', 'Male', '', '', '', '', 25, '2001-05-20', '9897567778', '', '2026-05-20', 1, '15:47', '16:02', NULL, NULL, '1', 0, '1', '3', 1, 2, 2, '2026-05-20 11:49:59', 'Cash', NULL, '500', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '', 500, '', '2026-05-20', '2026-06-02', '0', '', NULL, 'Dr.Venkatesh', 'ABC hospital', 'family friend', 'External'),
(669, 'BID000557', '2026-05-20', 'A202605200002', 'PAT0010', 'G KALAYANI', 'Female', '', '', '', '', 55, '1970-04-29', '7995908643', '', '2026-05-20', 1, '16:32', '16:47', NULL, NULL, '1', 0, '1', '2', 1, 2, 2, '2026-05-20 11:30:28', 'Both (Cash + UPI)', 200.00, '500', '', '', '', '', '', '', '', '', '', '', '', '687979', 300.00, '', '', '0', 500, '', '2026-05-20', '2026-06-02', '1', '', NULL, '', '', '', ''),
(670, 'BID000558', NULL, 'A202605200003', 'PAT0011', 'G LAXMI', 'Female', '', '', '', '', 49, '1976-05-08', '8699494819', '', '2026-05-20', 1, '17:17', '17:32', NULL, NULL, '1', 0, '1', '3', 1, 2, 2, '2026-05-20 11:48:00', 'Cash', 0.00, '500', '', '', '', '', '', '', '', '', '', '', '', '', 0.00, '', '', '0', 500, '', '2026-05-20', '2026-05-20', '0', '', NULL, '', '', '', ''),
(671, 'BID000559', NULL, 'A202605200004', 'PAT0006', 'V YERRAMMA', 'Female', '', '', '', '', 51, NULL, '9966188420', '', '2026-05-20', 1, '18:32', '18:47', NULL, NULL, '0', 0, '1', '1', 1, 2, 2, '2026-05-20 11:33:07', '', NULL, '500', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', '', '', 500, '', '2026-05-20', '2026-06-02', '0', '', NULL, '', '', '', ''),
(672, 'BID000001', '2026-05-21', 'A202605210001', 'PAT0001', 'Pavan Kumar', 'Male', '', '', '102', '', 34, '1992-05-21', '4455667788', '', '2026-05-21', 7, '13:46', '14:01', NULL, NULL, '0', 0, '1', '3', 9, 15, 15, '2026-05-25 05:57:31', 'Both (Cash + UPI)', 300.00, '600', '110', '88', '110', '88', '75', '165', '27.55', '65', '', '', '', '894567', 400.00, '', '', '0', 600, '', '2026-05-21', '2026-06-03', '1', '', NULL, 'Dr. Aswin', 'medicover', '', 'External'),
(673, 'BID000002', '2026-05-21', 'A202605210002', 'PAT0002', 'keerthi', 'Female', '', '', '', '', 28, '1997-05-22', '7787868688', '', '2026-05-21', 8, '19:21', '19:41', NULL, NULL, '1', 0, '1', '3', 9, 15, 15, '2026-05-21 13:50:46', 'Cash', 0.00, '700', '', '', '', '', '', '', '', '', '', '', '', '', 0.00, '6', 'percentage', '20', 560, '', '2026-05-21', '2026-06-03', '1', '', NULL, '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `org_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `module` varchar(64) NOT NULL,
  `action` enum('create','update','delete','login','logout') NOT NULL,
  `entity` varchar(64) NOT NULL,
  `entity_id` int(10) UNSIGNED DEFAULT NULL,
  `ts` datetime NOT NULL DEFAULT current_timestamp(),
  `ip` varchar(45) DEFAULT NULL,
  `before_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`before_json`)),
  `after_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`after_json`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id`, `org_id`, `user_id`, `module`, `action`, `entity`, `entity_id`, `ts`, `ip`, `before_json`, `after_json`) VALUES
(1, 1, 2, 'Doctor Timeslot', 'create', 'doctors_time_slot', 1, '2025-09-29 09:26:09', '::1', NULL, '{\"doctors_time_id\":\"1\",\"doctorName_registrationNumber\":\"1\",\"available_date\":\"2025-09-29\",\"doctortime_type\":\"Daily\",\"selectedDays\":\"\",\"modify_by\":\"2\",\"created_by\":\"2\",\"org_id\":\"1\",\"c_d_t\":\"2025-09-29 09:26:09\",\"status\":\"1\",\"multi_id\":\"0\"}'),
(2, 1, 2, 'Appointments', 'create', 'appointment_online', 644, '2025-09-29 09:26:44', '::1', NULL, '{\"appoint_id\":\"644\",\"bill_id\":\"BID000532\",\"bill_date\":null,\"appoint_register_id\":\"A202509290001\",\"appoint_unicode\":\"PAT0434\",\"patient_name\":\"SUBHA RAO\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"59\",\"mobile_number\":\"0000000000\",\"patient_email\":\"\",\"appoint_date\":\"2025-09-29\",\"doctor_name\":\"1\",\"start_time\":\"10:26\",\"end_time\":\"10:41\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2025-09-29 09:26:44\",\"amount_method\":\"\",\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2025-09-29\",\"valid_to\":\"2025-10-12\",\"appointment_status\":\"0\",\"patient_history\":\"\"}'),
(3, 1, 2, 'Appointments', 'update', 'appointment_online', 644, '2025-09-29 09:27:31', '::1', '{\"appoint_id\":\"644\",\"bill_id\":\"BID000532\",\"bill_date\":null,\"appoint_register_id\":\"A202509290001\",\"appoint_unicode\":\"PAT0434\",\"patient_name\":\"SUBHA RAO\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"59\",\"mobile_number\":\"0000000000\",\"patient_email\":\"\",\"appoint_date\":\"2025-09-29\",\"doctor_name\":\"1\",\"start_time\":\"10:26\",\"end_time\":\"10:41\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"1\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2025-09-29 09:26:59\",\"amount_method\":\"Cash\",\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"concession_name\":\"1\",\"concession_type\":\"percentage\",\"concession_value\":\"50\",\"final_amount\":\"250\",\"respiration_rate\":\"\",\"valid_from\":\"2025-09-29\",\"valid_to\":\"2025-10-12\",\"appointment_status\":\"0\",\"patient_history\":\"\"}', '{\"appoint_id\":\"644\",\"bill_id\":\"BID000532\",\"bill_date\":null,\"appoint_register_id\":\"A202509290001\",\"appoint_unicode\":\"PAT0434\",\"patient_name\":\"SUBHA RAO\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"59\",\"mobile_number\":\"0000000000\",\"patient_email\":\"\",\"appoint_date\":\"2025-09-29\",\"doctor_name\":\"1\",\"start_time\":\"10:26\",\"end_time\":\"10:41\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"1\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2025-09-29 09:27:31\",\"amount_method\":\"\",\"amount\":\"500\",\"bpSit_systolic\":\"120\",\"bpSit_diastolic\":\"80\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2025-09-29\",\"valid_to\":\"2025-10-12\",\"appointment_status\":\"1\",\"patient_history\":\"\"}'),
(4, 1, 2, 'Prescriptions', 'create', 'prescripition', 1, '2025-09-29 09:41:46', '::1', NULL, '\"{\\\"prescription_id\\\":\\\"1\\\",\\\"patient_name\\\":\\\"SUBHA RAO\\\",\\\"appoint_register_id\\\":\\\"A202509290001\\\",\\\"patient_uid\\\":\\\"PAT0434\\\",\\\"age\\\":\\\"59\\\",\\\"gender\\\":\\\"Male\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[{\\\\\\\"test_id\\\\\\\":\\\\\\\"149\\\\\\\",\\\\\\\"test_name\\\\\\\":\\\\\\\"BLOOD TEST\\\\\\\",\\\\\\\"instruction\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concession\\\\\\\":\\\\\\\"Family (50%)\\\\\\\",\\\\\\\"concessionName\\\\\\\":\\\\\\\"Family\\\\\\\",\\\\\\\"concessionValue\\\\\\\":50,\\\\\\\"concessionType\\\\\\\":\\\\\\\"percentage\\\\\\\",\\\\\\\"doctor_price\\\\\\\":505,\\\\\\\"standard_price\\\\\\\":1010,\\\\\\\"test_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"test_group_id\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_name\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_price\\\\\\\":\\\\\\\"\\\\\\\"},{\\\\\\\"test_id\\\\\\\":\\\\\\\"11\\\\\\\",\\\\\\\"test_name\\\\\\\":\\\\\\\"GLUCOSE-RANDOM PLASMA\\\\\\\",\\\\\\\"instruction\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concession\\\\\\\":\\\\\\\"new (\\\\u20b917)\\\\\\\",\\\\\\\"concessionName\\\\\\\":\\\\\\\"new\\\\\\\",\\\\\\\"concessionValue\\\\\\\":17,\\\\\\\"concessionType\\\\\\\":\\\\\\\"amount\\\\\\\",\\\\\\\"doctor_price\\\\\\\":63,\\\\\\\"standard_price\\\\\\\":80,\\\\\\\"test_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"test_group_id\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_name\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_price\\\\\\\":\\\\\\\"\\\\\\\"}]\\\",\\\"medicine_id\\\":\\\"[{\\\\\\\"medicine_id\\\\\\\":90,\\\\\\\"medicine_name\\\\\\\":\\\\\\\"DOLO 50 - (PARACETAMOL IP)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"12\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"500MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"2\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"0-2PM-0\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"0-1-0\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"},{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"12\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"7\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"75\\\\\\/20MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"9AM-0-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"1-0-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"}]\\\",\\\"prescriptiondate\\\":\\\"2025-09-29\\\",\\\"patient_vitals\\\":\\\"A202509290001\\\",\\\"finalDiagnosis\\\":\\\"Fever\\\",\\\"chiefcomplaint\\\":\\\"\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"\\\",\\\"advise\\\":\\\"\\\",\\\"personal_note\\\":\\\"\\\",\\\"reviewafter\\\":\\\"5 Days\\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"2025-10-04\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2025-09-29 09:41:46\\\",\\\"create_by\\\":\\\"2\\\",\\\"modify_by\\\":\\\"2\\\",\\\"org_id\\\":\\\"1\\\",\\\"create_date\\\":\\\"2025-09-29 09:41:46\\\"}\"'),
(5, 1, 2, 'TestBill', 'create', 'patienttestbilling', 2, '2025-09-29 10:22:55', '::1', NULL, '{\"test_details\":[{\"test_id\":\"149\",\"test_name\":\"BLOOD TEST\",\"instruction\":\"\",\"doctor_price\":505,\"standard_price\":1010},{\"test_id\":\"11\",\"test_name\":\"GLUCOSE-RANDOM PLASMA\",\"instruction\":\"\",\"doctor_price\":63,\"standard_price\":80}],\"total_amount\":1090,\"discount\":522,\"net_amount\":568,\"payment_method\":\"Cash\"}'),
(6, 1, 2, 'Appointments', 'create', 'appointment_online', 645, '2025-09-29 13:07:36', '::1', NULL, '{\"appoint_id\":\"645\",\"bill_id\":\"BID000533\",\"bill_date\":null,\"appoint_register_id\":\"A202509290002\",\"appoint_unicode\":\"PAT0096\",\"patient_name\":\"AJAY KUMAR\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"20\",\"mobile_number\":\"9542427999\",\"patient_email\":\"\",\"appoint_date\":\"2025-09-29\",\"doctor_name\":\"1\",\"start_time\":\"13:41\",\"end_time\":\"13:56\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2025-09-29 13:07:36\",\"amount_method\":\"\",\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2025-09-29\",\"valid_to\":\"2025-10-12\",\"appointment_status\":\"0\",\"patient_history\":\"\"}'),
(7, 1, 2, 'Doctor Timeslot', 'create', 'doctors_time_slot', 2, '2025-09-29 13:33:52', '::1', NULL, '{\"doctors_time_id\":\"2\",\"doctorName_registrationNumber\":\"5\",\"available_date\":\"2025-09-29\",\"doctortime_type\":\"Daily\",\"selectedDays\":\"\",\"modify_by\":\"2\",\"created_by\":\"2\",\"org_id\":\"1\",\"c_d_t\":\"2025-09-29 13:33:52\",\"status\":\"1\",\"multi_id\":\"0\"}'),
(8, 1, 2, 'Appointments', 'create', 'appointment_online', 646, '2025-09-29 13:34:10', '::1', NULL, '{\"appoint_id\":\"646\",\"bill_id\":\"BID000534\",\"bill_date\":null,\"appoint_register_id\":\"A202509290003\",\"appoint_unicode\":\"PAT0094\",\"patient_name\":\"SAMESHWAR RAO\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"49\",\"mobile_number\":\"9866481591\",\"patient_email\":\"\",\"appoint_date\":\"2025-09-29\",\"doctor_name\":\"5\",\"start_time\":\"16:45\",\"end_time\":\"16:57\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2025-09-29 13:34:10\",\"amount_method\":\"\",\"amount\":\"300\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"300\",\"respiration_rate\":\"\",\"valid_from\":\"2025-09-29\",\"valid_to\":\"2025-10-12\",\"appointment_status\":\"0\",\"patient_history\":\"\"}'),
(9, 1, 12, 'Appointments', 'create', 'appointment_online', 647, '2025-09-29 13:37:29', '::1', NULL, '{\"appoint_id\":\"647\",\"bill_id\":\"BID000535\",\"bill_date\":null,\"appoint_register_id\":\"A202509290004\",\"appoint_unicode\":\"PAT0168\",\"patient_name\":\"K. KANTHAMMA\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"70\",\"mobile_number\":\"9951990972\",\"patient_email\":\"\",\"appoint_date\":\"2025-09-29\",\"doctor_name\":\"5\",\"start_time\":\"17:21\",\"end_time\":\"17:33\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"12\",\"modified_by\":\"12\",\"create_date_time\":\"2025-09-29 13:37:29\",\"amount_method\":\"\",\"amount\":\"300\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"300\",\"respiration_rate\":\"\",\"valid_from\":\"2025-09-29\",\"valid_to\":\"2025-10-12\",\"appointment_status\":\"0\",\"patient_history\":\"\"}'),
(10, 1, 12, 'Doctor Timeslot', 'create', 'doctors_time_slot', 3, '2025-09-29 13:39:28', '::1', NULL, '{\"doctors_time_id\":\"3\",\"doctorName_registrationNumber\":\"1\",\"available_date\":\"2025-09-30\",\"doctortime_type\":\"Daily\",\"selectedDays\":\"\",\"modify_by\":\"12\",\"created_by\":\"12\",\"org_id\":\"1\",\"c_d_t\":\"2025-09-29 13:39:28\",\"status\":\"1\",\"multi_id\":\"0\"}'),
(11, 1, 12, 'Appointments', 'create', 'appointment_online', 648, '2025-09-29 14:24:43', '::1', NULL, '{\"appoint_id\":\"648\",\"bill_id\":\"BID000536\",\"bill_date\":null,\"appoint_register_id\":\"A202509290005\",\"appoint_unicode\":\"PAT0012\",\"patient_name\":\"G NARAYANA\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"47\",\"mobile_number\":\"7077538378\",\"patient_email\":\"\",\"appoint_date\":\"2025-09-29\",\"doctor_name\":\"5\",\"start_time\":\"17:57\",\"end_time\":\"18:09\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"12\",\"modified_by\":\"12\",\"create_date_time\":\"2025-09-29 14:24:43\",\"amount_method\":\"\",\"amount\":\"300\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"300\",\"respiration_rate\":\"\",\"valid_from\":\"2025-09-29\",\"valid_to\":\"2025-10-12\",\"appointment_status\":\"0\",\"patient_history\":\"\"}'),
(12, 1, 2, 'Appointments', 'create', 'appointment_online', 649, '2025-09-29 14:29:30', '::1', NULL, '{\"appoint_id\":\"649\",\"bill_id\":\"BID000537\",\"bill_date\":null,\"appoint_register_id\":\"A202509290006\",\"appoint_unicode\":\"PAT0004\",\"patient_name\":\"K.BHASKAR RAO\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"66\",\"mobile_number\":\"9550575634\",\"patient_email\":\"\",\"appoint_date\":\"2025-09-29\",\"doctor_name\":\"1\",\"start_time\":\"14:56\",\"end_time\":\"15:11\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2025-09-29 14:29:30\",\"amount_method\":\"\",\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2025-09-29\",\"valid_to\":\"2025-10-12\",\"appointment_status\":\"0\",\"patient_history\":\"\"}'),
(13, 1, 2, 'Appointments', 'create', 'appointment_online', 650, '2025-09-29 14:30:22', '::1', NULL, '{\"appoint_id\":\"650\",\"bill_id\":\"BID000538\",\"bill_date\":null,\"appoint_register_id\":\"A202509290007\",\"appoint_unicode\":\"PAT0102\",\"patient_name\":\"P SHANMUKA RAO\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"53\",\"mobile_number\":\"9502606136\",\"patient_email\":\"\",\"appoint_date\":\"2025-09-29\",\"doctor_name\":\"5\",\"start_time\":\"16:57\",\"end_time\":\"17:09\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2025-09-29 14:30:22\",\"amount_method\":\"\",\"amount\":\"300\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"300\",\"respiration_rate\":\"\",\"valid_from\":\"2025-09-29\",\"valid_to\":\"2025-10-12\",\"appointment_status\":\"0\",\"patient_history\":\"\"}'),
(14, 1, 2, 'Rx Groups', 'create', 'rx_groups_names', 1, '2025-09-30 09:31:18', '::1', NULL, '{\"rx_group_id\":\"1\",\"rx_group_name\":\"Cough\",\"status\":\"1\",\"create_by\":\"2\",\"modify_by\":\"2\",\"create_date_time\":\"0000-00-00 00:00:00\",\"org_id\":\"1\"}'),
(15, 1, 2, 'Rx Group Medicines', 'create', 'rx_groups', 1, '2025-09-30 09:31:18', '::1', NULL, '[{\"rx_id\":\"1\",\"rx_group_id\":\"1\",\"rx_group_name\":\"Cough\",\"medicine_name\":\"JBTOR - (TORSEMIDE)\",\"medicine_type\":\"Tab\",\"dosage\":\"6\",\"unit\":\"25MG\",\"timing\":\"10\",\"in_time_period\":\"8\",\"frequency\":\"\",\"duration\":\"3 Days\",\"quantity\":\"\",\"notes\":\"testing\",\"status\":\"1\",\"created_by\":\"2\",\"modify_by\":\"2\",\"org_id\":\"1\",\"create_date_time\":\"0000-00-00 00:00:00\",\"modify_date_time\":\"2025-09-30 09:31:18\"},{\"rx_id\":\"2\",\"rx_group_id\":\"1\",\"rx_group_name\":\"Cough\",\"medicine_name\":\"GABACOX - M - (GABAPENTIN + METHYLCOBALAMIN)\",\"medicine_type\":\"Syp\",\"dosage\":\"5\",\"unit\":\"50MG\",\"timing\":\"1\",\"in_time_period\":\"8\",\"frequency\":\"\",\"duration\":\"3 Days\",\"quantity\":\"\",\"notes\":\"\",\"status\":\"1\",\"created_by\":\"2\",\"modify_by\":\"2\",\"org_id\":\"1\",\"create_date_time\":\"0000-00-00 00:00:00\",\"modify_date_time\":\"2025-09-30 09:31:18\"}]'),
(16, 1, 2, 'Rx Groups', 'update', 'rx_groups_names', 1, '2025-09-30 09:36:28', '::1', '{\"rx_group_id\":\"1\",\"rx_group_name\":\"Cough\",\"status\":\"1\",\"create_by\":\"2\",\"modify_by\":\"2\",\"create_date_time\":\"0000-00-00 00:00:00\",\"org_id\":\"1\"}', '{\"rx_group_id\":\"1\",\"rx_group_name\":\"Cough\",\"status\":\"1\",\"create_by\":\"2\",\"modify_by\":\"2\",\"create_date_time\":\"0000-00-00 00:00:00\",\"org_id\":\"1\"}'),
(17, 1, 2, 'Rx Group Medicines', 'update', 'rx_groups', 1, '2025-09-30 09:36:28', '::1', '[{\"rx_id\":\"1\",\"rx_group_id\":\"1\",\"rx_group_name\":\"Cough\",\"medicine_name\":\"JBTOR - (TORSEMIDE)\",\"medicine_type\":\"Tab\",\"dosage\":\"6\",\"unit\":\"25MG\",\"timing\":\"10\",\"in_time_period\":\"8\",\"frequency\":\"\",\"duration\":\"3 Days\",\"quantity\":\"\",\"notes\":\"testing\",\"status\":\"1\",\"created_by\":\"2\",\"modify_by\":\"2\",\"org_id\":\"1\",\"create_date_time\":\"0000-00-00 00:00:00\",\"modify_date_time\":\"2025-09-30 09:31:18\"},{\"rx_id\":\"2\",\"rx_group_id\":\"1\",\"rx_group_name\":\"Cough\",\"medicine_name\":\"GABACOX - M - (GABAPENTIN + METHYLCOBALAMIN)\",\"medicine_type\":\"Syp\",\"dosage\":\"5\",\"unit\":\"50MG\",\"timing\":\"1\",\"in_time_period\":\"8\",\"frequency\":\"\",\"duration\":\"3 Days\",\"quantity\":\"\",\"notes\":\"\",\"status\":\"1\",\"created_by\":\"2\",\"modify_by\":\"2\",\"org_id\":\"1\",\"create_date_time\":\"0000-00-00 00:00:00\",\"modify_date_time\":\"2025-09-30 09:31:18\"}]', '[{\"rx_id\":\"3\",\"rx_group_id\":\"1\",\"rx_group_name\":\"Cough\",\"medicine_name\":\"JBTOR - (TORSEMIDE)\",\"medicine_type\":\"Tab\",\"dosage\":\"6\",\"unit\":\"25MG\",\"timing\":\"10\",\"in_time_period\":\"8\",\"frequency\":\"\",\"duration\":\"3 Days\",\"quantity\":\"\",\"notes\":\"testing\",\"status\":\"1\",\"created_by\":\"2\",\"modify_by\":\"2\",\"org_id\":\"1\",\"create_date_time\":\"0000-00-00 00:00:00\",\"modify_date_time\":\"2025-09-30 09:36:28\"},{\"rx_id\":\"4\",\"rx_group_id\":\"1\",\"rx_group_name\":\"Cough\",\"medicine_name\":\"GABACOX - M - (GABAPENTIN + METHYLCOBALAMIN)\",\"medicine_type\":\"Syp\",\"dosage\":\"5\",\"unit\":\"50MG\",\"timing\":\"1\",\"in_time_period\":\"8\",\"frequency\":\"\",\"duration\":\"3 Days\",\"quantity\":\"\",\"notes\":\"\",\"status\":\"1\",\"created_by\":\"2\",\"modify_by\":\"2\",\"org_id\":\"1\",\"create_date_time\":\"0000-00-00 00:00:00\",\"modify_date_time\":\"2025-09-30 09:36:28\"}]'),
(18, 1, 2, 'Appointments', 'create', 'appointment_online', 651, '2025-09-30 11:14:00', '::1', NULL, '{\"appoint_id\":\"651\",\"bill_id\":\"BID000539\",\"bill_date\":null,\"appoint_register_id\":\"A202509300001\",\"appoint_unicode\":\"PAT0005\",\"patient_name\":\"CH JAYA LAXMI\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"78\",\"mobile_number\":\"7981501223\",\"patient_email\":\"\",\"appoint_date\":\"2025-09-30\",\"doctor_name\":\"1\",\"start_time\":\"14:00\",\"end_time\":\"14:15\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2025-09-30 11:14:00\",\"amount_method\":\"\",\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2025-09-30\",\"valid_to\":\"2025-10-13\",\"appointment_status\":\"0\",\"patient_history\":\"\"}'),
(19, 1, 2, 'Rx Groups', 'create', 'rx_groups_names', 2, '2025-09-30 17:06:51', '::1', NULL, '{\"rx_group_id\":\"2\",\"rx_group_name\":\"RXGroupnew\",\"medicine_detailes\":\"[{\\\"medicine_id\\\":\\\"1\\\",\\\"medicine_name\\\":\\\"ECOSPRIN - (ASPIRIN)\\\",\\\"type_id\\\":\\\"Tab\\\",\\\"type_text\\\":\\\"Tab\\\",\\\"unit_id\\\":\\\"10\\\\\\/50MG\\\",\\\"unit_text\\\":\\\"10\\\\\\/50MG\\\",\\\"dosage_id\\\":\\\"5\\\",\\\"when_id\\\":\\\"8\\\",\\\"time_id\\\":\\\"1\\\",\\\"duration_value\\\":\\\"12\\\",\\\"duration\\\":\\\"Days\\\",\\\"notes\\\":\\\"\\\",\\\"med_status\\\":\\\"1\\\",\\\"timeText\\\":\\\"9AM-0-9PM\\\",\\\"dosageText\\\":\\\"1-0-1\\\",\\\"whenText\\\":\\\"After Food\\\"},{\\\"medicine_id\\\":\\\"2\\\",\\\"medicine_name\\\":\\\"ECOSPRIN - (ASPIRIN)\\\",\\\"type_id\\\":\\\"Tab\\\",\\\"type_text\\\":\\\"Tab\\\",\\\"unit_id\\\":\\\"40\\\\\\/25MG\\\",\\\"unit_text\\\":\\\"40\\\\\\/25MG\\\",\\\"dosage_id\\\":\\\"2\\\",\\\"when_id\\\":\\\"7\\\",\\\"time_id\\\":\\\"4\\\",\\\"duration_value\\\":\\\"12\\\",\\\"duration\\\":\\\"Days\\\",\\\"notes\\\":\\\"\\\",\\\"med_status\\\":\\\"1\\\",\\\"timeText\\\":\\\"0-2PM-0\\\",\\\"dosageText\\\":\\\"0-1-0\\\",\\\"whenText\\\":\\\"Before Food\\\"}]\",\"status\":\"1\",\"create_by\":\"2\",\"modify_by\":\"2\",\"create_date_time\":\"0000-00-00 00:00:00\",\"org_id\":\"1\"}'),
(20, 1, 2, 'Rx Groups', 'update', 'rx_groups_names', 2, '2025-10-01 09:47:45', '::1', '{\"rx_group_id\":\"2\",\"rx_group_name\":\"RXGroupnew\",\"medicine_detailes\":\"[{\\\"medicine_id\\\":\\\"1\\\",\\\"medicine_name\\\":\\\"ECOSPRIN - (ASPIRIN)\\\",\\\"type_id\\\":\\\"Tab\\\",\\\"type_text\\\":\\\"Tab\\\",\\\"unit_id\\\":\\\"10\\\\\\/50MG\\\",\\\"unit_text\\\":\\\"10\\\\\\/50MG\\\",\\\"dosage_id\\\":\\\"5\\\",\\\"when_id\\\":\\\"8\\\",\\\"time_id\\\":\\\"1\\\",\\\"duration_value\\\":\\\"12\\\",\\\"duration\\\":\\\"Days\\\",\\\"notes\\\":\\\"\\\",\\\"med_status\\\":\\\"1\\\",\\\"timeText\\\":\\\"9AM-0-9PM\\\",\\\"dosageText\\\":\\\"1-0-1\\\",\\\"whenText\\\":\\\"After Food\\\"},{\\\"medicine_id\\\":\\\"2\\\",\\\"medicine_name\\\":\\\"ECOSPRIN - (ASPIRIN)\\\",\\\"type_id\\\":\\\"Tab\\\",\\\"type_text\\\":\\\"Tab\\\",\\\"unit_id\\\":\\\"40\\\\\\/25MG\\\",\\\"unit_text\\\":\\\"40\\\\\\/25MG\\\",\\\"dosage_id\\\":\\\"2\\\",\\\"when_id\\\":\\\"7\\\",\\\"time_id\\\":\\\"4\\\",\\\"duration_value\\\":\\\"12\\\",\\\"duration\\\":\\\"Days\\\",\\\"notes\\\":\\\"\\\",\\\"med_status\\\":\\\"1\\\",\\\"timeText\\\":\\\"0-2PM-0\\\",\\\"dosageText\\\":\\\"0-1-0\\\",\\\"whenText\\\":\\\"Before Food\\\"}]\",\"status\":\"1\",\"create_by\":\"2\",\"modify_by\":\"2\",\"create_date_time\":\"0000-00-00 00:00:00\",\"org_id\":\"1\"}', '{\"rx_group_id\":\"2\",\"rx_group_name\":\"RXGroupnew\",\"medicine_detailes\":\"[{\\\"medicine_id\\\":\\\"1\\\",\\\"medicine_name\\\":\\\"ECOSPRIN - (ASPIRIN)\\\",\\\"type_id\\\":\\\"Tab\\\",\\\"type_text\\\":\\\"Tab\\\",\\\"unit_id\\\":\\\"10\\\\\\/50MG\\\",\\\"unit_text\\\":\\\"10\\\\\\/50MG\\\",\\\"dosage_id\\\":\\\"5\\\",\\\"when_id\\\":\\\"8\\\",\\\"time_id\\\":\\\"1\\\",\\\"duration_value\\\":\\\"12\\\",\\\"duration\\\":\\\"\\\",\\\"notes\\\":\\\"\\\",\\\"med_status\\\":\\\"1\\\",\\\"timeText\\\":\\\"9AM-0-9PM\\\",\\\"dosageText\\\":\\\"1-0-1\\\",\\\"whenText\\\":\\\"After Food\\\"},{\\\"medicine_id\\\":\\\"2\\\",\\\"medicine_name\\\":\\\"ECOSPRIN - (ASPIRIN)\\\",\\\"type_id\\\":\\\"Tab\\\",\\\"type_text\\\":\\\"Tab\\\",\\\"unit_id\\\":\\\"40\\\\\\/25MG\\\",\\\"unit_text\\\":\\\"40\\\\\\/25MG\\\",\\\"dosage_id\\\":\\\"2\\\",\\\"when_id\\\":\\\"7\\\",\\\"time_id\\\":\\\"4\\\",\\\"duration_value\\\":\\\"12\\\",\\\"duration\\\":\\\"\\\",\\\"notes\\\":\\\"\\\",\\\"med_status\\\":\\\"1\\\",\\\"timeText\\\":\\\"0-2PM-0\\\",\\\"dosageText\\\":\\\"0-1-0\\\",\\\"whenText\\\":\\\"Before Food\\\"}]\",\"status\":\"1\",\"create_by\":\"2\",\"modify_by\":\"2\",\"create_date_time\":\"0000-00-00 00:00:00\",\"org_id\":\"1\"}'),
(21, 1, 2, 'Doctor Timeslot', 'create', 'doctors_time_slot', 4, '2025-10-01 10:25:09', '::1', NULL, '{\"doctors_time_id\":\"4\",\"doctorName_registrationNumber\":\"1\",\"available_date\":\"2025-10-01\",\"doctortime_type\":\"Daily\",\"selectedDays\":\"\",\"modify_by\":\"2\",\"created_by\":\"2\",\"org_id\":\"1\",\"c_d_t\":\"2025-10-01 10:25:09\",\"status\":\"1\",\"multi_id\":\"0\"}'),
(22, 1, 2, 'Appointments', 'create', 'appointment_online', 652, '2025-10-01 10:25:25', '::1', NULL, '{\"appoint_id\":\"652\",\"bill_id\":\"BID000540\",\"bill_date\":null,\"appoint_register_id\":\"A202510010001\",\"appoint_unicode\":\"PAT0006\",\"patient_name\":\"V YERRAMMA\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"51\",\"mobile_number\":\"9966188420\",\"patient_email\":\"\",\"appoint_date\":\"2025-10-01\",\"doctor_name\":\"1\",\"start_time\":\"10:40\",\"end_time\":\"10:55\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2025-10-01 10:25:25\",\"amount_method\":\"\",\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2025-10-01\",\"valid_to\":\"2025-10-14\",\"appointment_status\":\"0\",\"patient_history\":\"\"}'),
(23, 1, 2, 'Appointments', 'create', 'appointment_online', 653, '2025-10-01 10:25:48', '::1', NULL, '{\"appoint_id\":\"653\",\"bill_id\":\"BID000541\",\"bill_date\":null,\"appoint_register_id\":\"A202510010002\",\"appoint_unicode\":\"PAT0010\",\"patient_name\":\"G KALAYANI\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"49\",\"mobile_number\":\"7995908643\",\"patient_email\":\"\",\"appoint_date\":\"2025-10-01\",\"doctor_name\":\"1\",\"start_time\":\"10:55\",\"end_time\":\"11:10\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2025-10-01 10:25:48\",\"amount_method\":\"\",\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2025-10-01\",\"valid_to\":\"2025-10-14\",\"appointment_status\":\"0\",\"patient_history\":\"\"}'),
(24, 1, 2, 'Appointments', 'create', 'appointment_online', 654, '2025-10-06 16:40:00', '::1', NULL, '{\"appoint_id\":\"654\",\"bill_id\":\"BID000542\",\"bill_date\":null,\"appoint_register_id\":\"A202510060001\",\"appoint_unicode\":\"PAT0046\",\"patient_name\":\"SUNDARI\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"87\",\"mobile_number\":\"9686481900\",\"patient_email\":\"\",\"appoint_date\":\"2025-10-06\",\"doctor_name\":\"1\",\"start_time\":\"17:39\",\"end_time\":\"17:54\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2025-10-06 16:40:00\",\"amount_method\":\"\",\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2025-10-06\",\"valid_to\":\"2025-10-19\",\"appointment_status\":\"0\",\"patient_history\":\"\"}'),
(25, 1, 2, 'Appointments', 'create', 'appointment_online', 655, '2025-10-06 16:47:32', '::1', NULL, '{\"appoint_id\":\"655\",\"bill_id\":\"BID000543\",\"bill_date\":null,\"appoint_register_id\":\"A202510060002\",\"appoint_unicode\":\"PAT0050\",\"patient_name\":\"Y BHEEMA RAJU\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"78\",\"mobile_number\":\"9000665715\",\"patient_email\":\"\",\"appoint_date\":\"2025-10-06\",\"doctor_name\":\"1\",\"start_time\":\"18:09\",\"end_time\":\"18:24\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2025-10-06 16:47:32\",\"amount_method\":\"\",\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2025-10-06\",\"valid_to\":\"2025-10-19\",\"appointment_status\":\"0\",\"patient_history\":\"\"}'),
(26, 1, 2, 'Prescriptions', 'create', 'prescripition', 2, '2025-10-06 17:21:35', '::1', NULL, '\"{\\\"prescription_id\\\":\\\"2\\\",\\\"patient_name\\\":\\\"SUNDARI\\\",\\\"appoint_register_id\\\":\\\"A202510060001\\\",\\\"patient_uid\\\":\\\"PAT0046\\\",\\\"age\\\":\\\"87\\\",\\\"gender\\\":\\\"Female\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[{\\\\\\\"test_id\\\\\\\":\\\\\\\"93\\\\\\\",\\\\\\\"test_name\\\\\\\":\\\\\\\"HIV PROVIRAL DNA QUALITATIVE#\\\\\\\",\\\\\\\"instruction\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concession\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionName\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionValue\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionType\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"doctor_price\\\\\\\":7000,\\\\\\\"standard_price\\\\\\\":7000,\\\\\\\"test_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"test_group_id\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_name\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_price\\\\\\\":\\\\\\\"\\\\\\\"}]\\\",\\\"medicine_id\\\":\\\"[{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"9\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"ECOSPRIN AV - (ASPIRIN + ATORVASTATIN)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"10\\\\\\/25MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"9AM-0-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"1-0-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"}]\\\",\\\"prescriptiondate\\\":\\\"2025-10-06\\\",\\\"patient_vitals\\\":\\\"A202510060001\\\",\\\"finalDiagnosis\\\":\\\"test\\\",\\\"chiefcomplaint\\\":\\\"\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"\\\",\\\"advise\\\":\\\"\\\",\\\"personal_note\\\":\\\"\\\",\\\"reviewafter\\\":\\\"3 Days\\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"2025-10-09\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2025-10-06 17:21:35\\\",\\\"create_by\\\":\\\"2\\\",\\\"modify_by\\\":\\\"2\\\",\\\"org_id\\\":\\\"1\\\",\\\"create_date\\\":\\\"2025-10-06 17:21:35\\\"}\"'),
(27, 1, 2, 'Doctor Timeslot', 'create', 'doctors_time_slot', 44, '2026-04-08 13:13:13', '::1', NULL, '{\"doctors_time_id\":\"44\",\"doctorName_registrationNumber\":\"1\",\"available_date\":\"2026-04-08\",\"doctortime_type\":\"Daily\",\"selectedDays\":\"\",\"modify_by\":\"2\",\"created_by\":\"2\",\"org_id\":\"1\",\"c_d_t\":\"2026-04-08 13:13:13\",\"status\":\"1\",\"multi_id\":\"0\"}'),
(28, 1, 2, 'Appointments', 'create', 'appointment_online', 656, '2026-04-08 13:13:36', '::1', NULL, '{\"appoint_id\":\"656\",\"bill_id\":\"BID000544\",\"bill_date\":null,\"appoint_register_id\":\"A202604080001\",\"appoint_unicode\":\"PAT0012\",\"patient_name\":\"G NARAYANA\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"47\",\"mobile_number\":\"7077538378\",\"patient_email\":\"\",\"appoint_date\":\"2026-04-08\",\"doctor_name\":\"1\",\"start_time\":\"13:58\",\"end_time\":\"14:13\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-04-08 13:13:36\",\"amount_method\":\"\",\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-04-08\",\"valid_to\":\"2026-04-21\",\"appointment_status\":\"0\",\"patient_history\":\"\"}'),
(29, 1, 2, 'Appointments', 'create', 'appointment_online', 657, '2026-04-08 13:25:56', '::1', NULL, '{\"appoint_id\":\"657\",\"bill_id\":\"BID000545\",\"bill_date\":null,\"appoint_register_id\":\"A202604080002\",\"appoint_unicode\":\"PAT0006\",\"patient_name\":\"V YERRAMMA\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"51\",\"mobile_number\":\"9966188420\",\"patient_email\":\"\",\"appoint_date\":\"2026-04-08\",\"doctor_name\":\"1\",\"start_time\":\"14:13\",\"end_time\":\"14:28\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-04-08 13:25:56\",\"amount_method\":\"\",\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-04-08\",\"valid_to\":\"2026-04-21\",\"appointment_status\":\"0\",\"patient_history\":\"\"}'),
(30, 1, 2, 'Prescriptions', 'create', 'prescripition', 3, '2026-04-08 13:46:31', '::1', NULL, '\"{\\\"prescription_id\\\":\\\"3\\\",\\\"patient_name\\\":\\\"G NARAYANA\\\",\\\"appoint_register_id\\\":\\\"A202604080001\\\",\\\"patient_uid\\\":\\\"PAT0012\\\",\\\"age\\\":\\\"47\\\",\\\"gender\\\":\\\"Male\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[]\\\",\\\"medicine_id\\\":\\\"[]\\\",\\\"prescriptiondate\\\":\\\"2026-04-08\\\",\\\"patient_vitals\\\":\\\"A202604080001\\\",\\\"finalDiagnosis\\\":\\\"\\\",\\\"chiefcomplaint\\\":\\\"\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"\\\",\\\"advise\\\":\\\"\\\",\\\"personal_note\\\":\\\"\\\",\\\"reviewafter\\\":\\\" Days\\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2026-04-08 13:46:31\\\",\\\"create_by\\\":\\\"2\\\",\\\"modify_by\\\":\\\"2\\\",\\\"org_id\\\":\\\"1\\\",\\\"create_date\\\":\\\"2026-04-08 13:46:31\\\"}\"'),
(31, 1, 2, 'Prescriptions', 'update', 'prescripition', 3, '2026-04-08 13:47:02', '::1', '\"{\\\"prescription_id\\\":\\\"3\\\",\\\"patient_name\\\":\\\"G NARAYANA\\\",\\\"appoint_register_id\\\":\\\"A202604080001\\\",\\\"patient_uid\\\":\\\"PAT0012\\\",\\\"age\\\":\\\"47\\\",\\\"gender\\\":\\\"Male\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[]\\\",\\\"medicine_id\\\":\\\"[]\\\",\\\"prescriptiondate\\\":\\\"2026-04-08\\\",\\\"patient_vitals\\\":\\\"A202604080001\\\",\\\"finalDiagnosis\\\":\\\"\\\",\\\"chiefcomplaint\\\":\\\"\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"\\\",\\\"advise\\\":\\\"\\\",\\\"personal_note\\\":\\\"\\\",\\\"reviewafter\\\":\\\" Days\\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2026-04-08 13:46:31\\\",\\\"create_by\\\":\\\"2\\\",\\\"modify_by\\\":\\\"2\\\",\\\"org_id\\\":\\\"1\\\",\\\"create_date\\\":\\\"2026-04-08 13:46:31\\\"}\"', '\"{\\\"prescription_id\\\":\\\"3\\\",\\\"patient_name\\\":\\\"G NARAYANA\\\",\\\"appoint_register_id\\\":\\\"A202604080001\\\",\\\"patient_uid\\\":\\\"PAT0012\\\",\\\"age\\\":\\\"47\\\",\\\"gender\\\":\\\"Male\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[{\\\\\\\"test_id\\\\\\\":\\\\\\\"90\\\\\\\",\\\\\\\"test_name\\\\\\\":\\\\\\\"HISTOPATHOLOGY BIOPSY MEDIUM SPECIMEN\\\\\\\",\\\\\\\"instruction\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concession\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionName\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionValue\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionType\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"doctor_price\\\\\\\":800,\\\\\\\"standard_price\\\\\\\":800,\\\\\\\"test_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"test_group_id\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_name\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_price\\\\\\\":\\\\\\\"\\\\\\\"},{\\\\\\\"test_id\\\\\\\":\\\\\\\"95\\\\\\\",\\\\\\\"test_name\\\\\\\":\\\\\\\"HLA - B27 FLOWCYTOMETRY\\\\\\\",\\\\\\\"instruction\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concession\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionName\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionValue\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionType\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"doctor_price\\\\\\\":2500,\\\\\\\"standard_price\\\\\\\":2500,\\\\\\\"test_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"test_group_id\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_name\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_price\\\\\\\":\\\\\\\"\\\\\\\"}]\\\",\\\"medicine_id\\\":\\\"[]\\\",\\\"prescriptiondate\\\":\\\"2026-04-08\\\",\\\"patient_vitals\\\":\\\"A202604080001\\\",\\\"finalDiagnosis\\\":\\\"\\\",\\\"chiefcomplaint\\\":\\\"\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"\\\",\\\"advise\\\":\\\"\\\",\\\"personal_note\\\":\\\"\\\",\\\"reviewafter\\\":\\\" Days\\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2026-04-08 13:46:31\\\",\\\"create_by\\\":\\\"2\\\",\\\"modify_by\\\":\\\"2\\\",\\\"org_id\\\":\\\"1\\\",\\\"create_date\\\":\\\"2026-04-08 13:46:31\\\"}\"'),
(32, 1, 2, 'Doctor Timeslot', 'create', 'doctors_time_slot', 45, '2026-04-10 14:11:09', '::1', NULL, '{\"doctors_time_id\":\"45\",\"doctorName_registrationNumber\":\"1\",\"available_date\":\"2026-04-10\",\"doctortime_type\":\"Daily\",\"selectedDays\":\"\",\"modify_by\":\"2\",\"created_by\":\"2\",\"org_id\":\"1\",\"c_d_t\":\"2026-04-10 14:11:09\",\"status\":\"1\",\"multi_id\":\"0\"}'),
(33, 1, 2, 'Appointments', 'create', 'appointment_online', 658, '2026-04-10 14:11:28', '::1', NULL, '{\"appoint_id\":\"658\",\"bill_id\":\"BID000546\",\"bill_date\":null,\"appoint_register_id\":\"A202604100001\",\"appoint_unicode\":\"PAT0013\",\"patient_name\":\"CH PADMA\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"44\",\"mobile_number\":\"8885991222\",\"patient_email\":\"\",\"appoint_date\":\"2026-04-10\",\"doctor_name\":\"1\",\"start_time\":\"17:26\",\"end_time\":\"17:41\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-04-10 14:11:28\",\"amount_method\":\"\",\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-04-10\",\"valid_to\":\"2026-04-23\",\"appointment_status\":\"0\",\"patient_history\":\"\"}'),
(34, 1, 2, 'Prescriptions', 'create', 'prescripition', 4, '2026-04-10 14:12:09', '::1', NULL, '\"{\\\"prescription_id\\\":\\\"4\\\",\\\"patient_name\\\":\\\"CH PADMA\\\",\\\"appoint_register_id\\\":\\\"A202604100001\\\",\\\"patient_uid\\\":\\\"PAT0013\\\",\\\"age\\\":\\\"44\\\",\\\"gender\\\":\\\"Female\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[]\\\",\\\"medicine_id\\\":\\\"[{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"ECOSPRIN - (ASPIRIN)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"10\\\\\\/25MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"2\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"3\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"9AM-2PM-0\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"1-1-0\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"}]\\\",\\\"prescriptiondate\\\":\\\"2026-04-10\\\",\\\"patient_vitals\\\":\\\"A202604100001\\\",\\\"finalDiagnosis\\\":\\\"\\\",\\\"chiefcomplaint\\\":\\\"\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"\\\",\\\"advise\\\":\\\"\\\",\\\"personal_note\\\":\\\"testing\\\",\\\"reviewafter\\\":\\\" Days\\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2026-04-10 14:12:09\\\",\\\"create_by\\\":\\\"2\\\",\\\"modify_by\\\":\\\"2\\\",\\\"org_id\\\":\\\"1\\\",\\\"create_date\\\":\\\"2026-04-10 14:12:09\\\"}\"'),
(35, 1, 2, 'Doctor Timeslot', 'create', 'doctors_time_slot', 46, '2026-04-15 09:41:04', '::1', NULL, '{\"doctors_time_id\":\"46\",\"doctorName_registrationNumber\":\"1\",\"available_date\":\"2026-04-15\",\"doctortime_type\":\"Daily\",\"selectedDays\":\"\",\"modify_by\":\"2\",\"created_by\":\"2\",\"org_id\":\"1\",\"c_d_t\":\"2026-04-15 09:41:04\",\"status\":\"1\",\"multi_id\":\"0\"}'),
(36, 1, 2, 'Appointments', 'create', 'appointment_online', 659, '2026-04-15 09:41:59', '::1', NULL, '{\"appoint_id\":\"659\",\"bill_id\":\"BID000547\",\"bill_date\":null,\"appoint_register_id\":\"A202604150001\",\"appoint_unicode\":\"PAT0532\",\"patient_name\":\"durga lakshmi\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"34\",\"mobile_number\":\"7032760271\",\"patient_email\":\"\",\"appoint_date\":\"2026-04-15\",\"doctor_name\":\"1\",\"start_time\":\"10:40\",\"end_time\":\"10:55\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-04-15 09:41:59\",\"amount_method\":\"\",\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-04-15\",\"valid_to\":\"2026-04-28\",\"appointment_status\":\"0\",\"patient_history\":\"\"}'),
(37, 1, 2, 'Prescriptions', 'create', 'prescripition', 5, '2026-04-15 10:53:28', '::1', NULL, '\"{\\\"prescription_id\\\":\\\"5\\\",\\\"patient_name\\\":\\\"durga lakshmi\\\",\\\"appoint_register_id\\\":\\\"A202604150001\\\",\\\"patient_uid\\\":\\\"PAT0532\\\",\\\"age\\\":\\\"34\\\",\\\"gender\\\":\\\"Female\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[]\\\",\\\"medicine_id\\\":\\\"[{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"9\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"ECOSPRIN AV - (ASPIRIN + ATORVASTATIN)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"10\\\\\\/25MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"6\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"0-2PM-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"0-1-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"},{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"12\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"10\\\\\\/25MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"6\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"9\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"0-2PM-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"0-1-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"Not Applicable\\\\\\\"}]\\\",\\\"prescriptiondate\\\":\\\"2026-04-15\\\",\\\"patient_vitals\\\":\\\"A202604150001\\\",\\\"finalDiagnosis\\\":\\\"\\\",\\\"chiefcomplaint\\\":\\\"\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"\\\",\\\"advise\\\":\\\"\\\",\\\"personal_note\\\":\\\"\\\",\\\"reviewafter\\\":\\\" Days\\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2026-04-15 10:53:28\\\",\\\"create_by\\\":\\\"2\\\",\\\"modify_by\\\":\\\"2\\\",\\\"org_id\\\":\\\"1\\\",\\\"create_date\\\":\\\"2026-04-15 10:53:28\\\"}\"');
INSERT INTO `audit_log` (`id`, `org_id`, `user_id`, `module`, `action`, `entity`, `entity_id`, `ts`, `ip`, `before_json`, `after_json`) VALUES
(38, 1, 2, 'Appointments', 'update', 'appointment_online', 659, '2026-04-15 11:20:09', '::1', '{\"appoint_id\":\"659\",\"bill_id\":\"BID000547\",\"bill_date\":\"2026-04-15\",\"appoint_register_id\":\"A202604150001\",\"appoint_unicode\":\"PAT0532\",\"patient_name\":\"durga lakshmi\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"34\",\"dob\":null,\"mobile_number\":\"7032760271\",\"patient_email\":\"\",\"appoint_date\":\"2026-04-15\",\"doctor_name\":\"1\",\"start_time\":\"10:40\",\"end_time\":\"10:55\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"1\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-04-15 10:53:28\",\"amount_method\":\"Both (Cash + UPI)\",\"cash_amount\":\"100.00\",\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"12345\",\"transaction_amount\":null,\"concession_name\":\"1\",\"concession_type\":\"percentage\",\"concession_value\":\"50\",\"final_amount\":\"250\",\"respiration_rate\":\"\",\"valid_from\":\"2026-04-15\",\"valid_to\":\"2026-04-28\",\"appointment_status\":\"1\",\"patient_history\":\"\"}', '{\"appoint_id\":\"659\",\"bill_id\":\"BID000547\",\"bill_date\":\"2026-04-15\",\"appoint_register_id\":\"A202604150001\",\"appoint_unicode\":\"PAT0532\",\"patient_name\":\"durga lakshmi\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"34\",\"dob\":\"1991-06-18\",\"mobile_number\":\"7032760271\",\"patient_email\":\"\",\"appoint_date\":\"2026-04-15\",\"doctor_name\":\"1\",\"start_time\":\"10:40\",\"end_time\":\"10:55\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"1\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-04-15 11:20:09\",\"amount_method\":\"\",\"cash_amount\":\"100.00\",\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-04-15\",\"valid_to\":\"2026-04-28\",\"appointment_status\":\"0\",\"patient_history\":\"\"}'),
(39, 1, 2, 'Appointments', 'create', 'appointment_online', 660, '2026-04-15 11:41:44', '::1', NULL, '{\"appoint_id\":\"660\",\"bill_id\":\"BID000548\",\"bill_date\":null,\"appoint_register_id\":\"A202604150002\",\"appoint_unicode\":\"PAT0010\",\"patient_name\":\"G KALAYANI\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"55\",\"dob\":\"1970-10-21\",\"mobile_number\":\"7995908643\",\"patient_email\":\"\",\"appoint_date\":\"2026-04-15\",\"doctor_name\":\"1\",\"start_time\":\"16:25\",\"end_time\":\"16:40\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-04-15 11:41:44\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-04-15\",\"valid_to\":\"2026-04-28\",\"appointment_status\":\"0\",\"patient_history\":\"\"}'),
(40, 1, 2, 'Roles', 'create', 'roles', 11, '2026-04-15 11:53:35', '::1', NULL, '{\"role_id\":\"11\",\"role_name\":\"Receptionist\",\"created_by\":\"2\",\"created_date_time\":\"2026-04-15 11:53:35\",\"status\":\"1\",\"modified_by\":\"2\",\"modified_date_time\":\"2026-04-15 11:53:35\",\"org_id\":\"1\"}'),
(41, 1, 2, 'Security', 'delete', 'security', 12, '2026-04-15 11:54:38', '::1', '{\"security_id\":\"12\",\"admin_name\":\"Dr. Venkatesh\",\"email\":\"ven@gmail.com\",\"contact\":\"6302669664\",\"security_password\":\"827ccb0eea8a706c4c34a16891f84e7b\",\"image_url\":\"\",\"signature_url\":\"\",\"role_id\":\"2\",\"security_type\":\"U\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"status\":\"1\",\"create_date_time\":\"2025-09-24 11:43:09\"}', '{\"security_id\":\"12\",\"admin_name\":\"Dr. Venkatesh\",\"email\":\"ven@gmail.com\",\"contact\":\"6302669664\",\"security_password\":\"827ccb0eea8a706c4c34a16891f84e7b\",\"image_url\":\"\",\"signature_url\":\"\",\"role_id\":\"2\",\"security_type\":\"U\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"status\":\"0\",\"create_date_time\":\"2026-04-15 11:54:38\"}'),
(42, 1, 2, 'Security', 'create', 'security', 13, '2026-04-15 11:55:04', '::1', NULL, '{\"security_id\":\"13\",\"admin_name\":\"ravi\",\"email\":\"ravi@gmail.com\",\"contact\":\"7095678679\",\"security_password\":\"827ccb0eea8a706c4c34a16891f84e7b\",\"image_url\":\"\",\"signature_url\":\"\",\"role_id\":\"11\",\"security_type\":\"U\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"status\":\"1\",\"create_date_time\":\"2026-04-15 11:55:04\"}'),
(43, 1, 2, 'Doctors', 'update', 'doctor', 1, '2026-04-15 11:58:22', '::1', '{\"doc_id\":\"1\",\"doc_registration_number\":\"D202509180001\",\"doctor_name\":\"Dr.Ashwin Kumar Panda\",\"doctor_type\":\"Out\",\"gender\":\"Male\",\"phone_number\":\"8897355655\",\"email\":\"pandas@gmail.com\",\"doctor_specialization\":\"18\",\"departments\":\"1\",\"doctor_services\":\"1\",\"doctor_fee\":\"500\",\"doctor_charge\":\"2000\",\"doctor_visit_charge\":\"\",\"time_slot_duration\":\"15\",\"details\":\"test\",\"doc_img\":\"doc_1758696935.jpeg\",\"org_id\":\"1\",\"security_id\":\"2\",\"status\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"c_d_t\":\"2025-09-18 10:02:14\"}', '{\"doc_id\":\"1\",\"doc_registration_number\":\"D202509180001\",\"doctor_name\":\"Dr.Ashwin Kumar Panda\",\"doctor_type\":\"Out\",\"gender\":\"Male\",\"phone_number\":\"8897355655\",\"email\":\"pandas@gmail.com\",\"doctor_specialization\":\"18\",\"departments\":\"1\",\"doctor_services\":\"1\",\"doctor_fee\":\"500\",\"doctor_charge\":\"2000\",\"doctor_visit_charge\":\"\",\"time_slot_duration\":\"15\",\"details\":\"test\",\"doc_img\":\"doc_1776234502.png\",\"org_id\":\"1\",\"security_id\":\"2\",\"status\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"c_d_t\":\"2025-09-18 10:02:14\"}'),
(44, 1, 2, 'Receptionnist', 'update', 'receptionnist', 1, '2026-04-15 11:58:22', '::1', '[{\"rep_id\":\"2\",\"doc_id\":\"1\",\"security_id\":\"12\",\"user_name\":\"Dr. Venkatesh\",\"org_id\":\"1\",\"status\":\"1\",\"created_by\":\"2\",\"created_at\":\"2025-09-24 12:25:35\",\"modified_by\":null,\"modified_at\":\"2025-09-24 12:25:35\"}]', '[{\"rep_id\":\"2\",\"doc_id\":\"1\",\"security_id\":\"12\",\"user_name\":\"Dr. Venkatesh\",\"org_id\":\"1\",\"status\":\"1\",\"created_by\":\"2\",\"created_at\":\"2025-09-24 12:25:35\",\"modified_by\":\"2\",\"modified_at\":\"2026-04-15 11:58:22\"},{\"rep_id\":\"4\",\"doc_id\":\"1\",\"security_id\":\"13\",\"user_name\":\"ravi\",\"org_id\":\"1\",\"status\":\"1\",\"created_by\":\"2\",\"created_at\":\"2026-04-15 11:58:22\",\"modified_by\":\"2\",\"modified_at\":\"2026-04-15 11:58:22\"}]'),
(45, 1, 2, 'MedicineBill', 'create', 'patient_medicine_billing', 0, '2026-04-15 15:39:30', '::1', NULL, '{\"patient_id\":\"PAT0532\",\"appointment_id\":\"A202604150001\",\"prescription_id\":5,\"medicine_details\":[{\"medicine_id\":9,\"medicine_name\":\"ECOSPRIN AV - (ASPIRIN + ATORVASTATIN)\",\"type_text\":\"Tab\",\"unit_text\":\"10\\/25MG\",\"dosage_text\":\"0-1-1\",\"when_text\":\"After Food\",\"time_text\":\"0-2PM-9PM\",\"duration_value\":4,\"duration\":\"Days\",\"notes\":\"\",\"price\":20,\"discount\":5,\"final_amount\":15},{\"medicine_id\":12,\"medicine_name\":\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\",\"type_text\":\"Tab\",\"unit_text\":\"10\\/25MG\",\"dosage_text\":\"0-1-1\",\"when_text\":\"Not Applicable\",\"time_text\":\"0-2PM-9PM\",\"duration_value\":4,\"duration\":\"Days\",\"notes\":\"\",\"price\":30,\"discount\":12,\"final_amount\":18}],\"advice\":\"\",\"personal_note\":\"\",\"total_amount\":50,\"discount\":17,\"net_amount\":33,\"purchase_source\":\"Hospital Pharmacy\",\"payment_method\":\"Cash\"}'),
(46, 1, 2, 'MedicineBill', 'create', 'patient_medicine_billing', 0, '2026-04-15 15:39:39', '::1', NULL, '{\"patient_id\":\"PAT0532\",\"appointment_id\":\"A202604150001\",\"prescription_id\":5,\"medicine_details\":[{\"medicine_id\":9,\"medicine_name\":\"ECOSPRIN AV - (ASPIRIN + ATORVASTATIN)\",\"type_text\":\"Tab\",\"unit_text\":\"10\\/25MG\",\"dosage_text\":\"0-1-1\",\"when_text\":\"After Food\",\"time_text\":\"0-2PM-9PM\",\"duration_value\":4,\"duration\":\"Days\",\"notes\":\"\",\"price\":20,\"discount\":5,\"final_amount\":15},{\"medicine_id\":12,\"medicine_name\":\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\",\"type_text\":\"Tab\",\"unit_text\":\"10\\/25MG\",\"dosage_text\":\"0-1-1\",\"when_text\":\"Not Applicable\",\"time_text\":\"0-2PM-9PM\",\"duration_value\":4,\"duration\":\"Days\",\"notes\":\"\",\"price\":30,\"discount\":12,\"final_amount\":18}],\"advice\":\"\",\"personal_note\":\"\",\"total_amount\":50,\"discount\":17,\"net_amount\":33,\"purchase_source\":\"Hospital Pharmacy\",\"payment_method\":\"Cash\"}'),
(47, 1, 2, 'MedicineBill', 'create', 'patient_medicine_billing', 0, '2026-04-15 15:58:22', '::1', NULL, '{\"patient_id\":\"PAT0532\",\"appointment_id\":\"A202604150001\",\"prescription_id\":5,\"medicine_details\":[{\"medicine_id\":9,\"medicine_name\":\"ECOSPRIN AV - (ASPIRIN + ATORVASTATIN)\",\"type_text\":\"Tab\",\"unit_text\":\"10\\/25MG\",\"dosage_text\":\"0-1-1\",\"when_text\":\"After Food\",\"time_text\":\"0-2PM-9PM\",\"duration_value\":4,\"duration\":\"Days\",\"notes\":\"\",\"price\":30,\"discount\":0,\"final_amount\":30,\"purchase_source\":\"Outside Pharmacy\"},{\"medicine_id\":12,\"medicine_name\":\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\",\"type_text\":\"Tab\",\"unit_text\":\"10\\/25MG\",\"dosage_text\":\"0-1-1\",\"when_text\":\"Not Applicable\",\"time_text\":\"0-2PM-9PM\",\"duration_value\":4,\"duration\":\"Days\",\"notes\":\"\",\"price\":50,\"discount\":0,\"final_amount\":50,\"purchase_source\":\"Hospital Pharmacy\"}],\"advice\":\"\",\"personal_note\":\"\",\"total_amount\":80,\"discount\":0,\"net_amount\":80,\"hospital_gross\":50,\"hospital_discount\":0,\"hospital_total\":50,\"outside_total\":30,\"purchase_source\":\"Mixed\",\"payment_method\":\"UPI\"}'),
(48, 1, 2, 'Prescriptions', 'update', 'prescripition', 5, '2026-04-15 17:08:45', '::1', '\"{\\\"prescription_id\\\":\\\"5\\\",\\\"patient_name\\\":\\\"durga lakshmi\\\",\\\"appoint_register_id\\\":\\\"A202604150001\\\",\\\"patient_uid\\\":\\\"PAT0532\\\",\\\"age\\\":\\\"34\\\",\\\"gender\\\":\\\"Female\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[]\\\",\\\"medicine_id\\\":\\\"[{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"9\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"ECOSPRIN AV - (ASPIRIN + ATORVASTATIN)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"10\\\\\\/25MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"6\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"0-2PM-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"0-1-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"},{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"12\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"10\\\\\\/25MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"6\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"9\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"0-2PM-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"0-1-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"Not Applicable\\\\\\\"}]\\\",\\\"prescriptiondate\\\":\\\"2026-04-15\\\",\\\"patient_vitals\\\":\\\"A202604150001\\\",\\\"finalDiagnosis\\\":\\\"\\\",\\\"chiefcomplaint\\\":\\\"\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"\\\",\\\"advise\\\":\\\"\\\",\\\"personal_note\\\":\\\"\\\",\\\"reviewafter\\\":\\\" Days\\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2026-04-15 10:53:28\\\",\\\"create_by\\\":\\\"2\\\",\\\"modify_by\\\":\\\"2\\\",\\\"org_id\\\":\\\"1\\\",\\\"create_date\\\":\\\"2026-04-15 10:53:28\\\"}\"', '\"{\\\"prescription_id\\\":\\\"5\\\",\\\"patient_name\\\":\\\"durga lakshmi\\\",\\\"appoint_register_id\\\":\\\"A202604150001\\\",\\\"patient_uid\\\":\\\"PAT0532\\\",\\\"age\\\":\\\"34\\\",\\\"gender\\\":\\\"Female\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[{\\\\\\\"test_id\\\\\\\":\\\\\\\"91\\\\\\\",\\\\\\\"test_name\\\\\\\":\\\\\\\"HISTOPATHOLOGY BIOPSY SMALL SPECIMEN\\\\\\\",\\\\\\\"instruction\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concession\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionName\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionValue\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionType\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"doctor_price\\\\\\\":800,\\\\\\\"standard_price\\\\\\\":800,\\\\\\\"test_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"test_group_id\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_name\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_price\\\\\\\":\\\\\\\"\\\\\\\"}]\\\",\\\"medicine_id\\\":\\\"[{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"9\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"ECOSPRIN AV - (ASPIRIN + ATORVASTATIN)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"10\\\\\\/25MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"6\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"0-2PM-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"0-1-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"},{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"12\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"10\\\\\\/25MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"6\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"9\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"0-2PM-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"0-1-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"Not Applicable\\\\\\\"}]\\\",\\\"prescriptiondate\\\":\\\"2026-04-15\\\",\\\"patient_vitals\\\":\\\"A202604150001\\\",\\\"finalDiagnosis\\\":\\\"\\\",\\\"chiefcomplaint\\\":\\\"\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"\\\",\\\"advise\\\":\\\"\\\",\\\"personal_note\\\":\\\"\\\",\\\"reviewafter\\\":\\\" Days\\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2026-04-15 10:53:28\\\",\\\"create_by\\\":\\\"2\\\",\\\"modify_by\\\":\\\"2\\\",\\\"org_id\\\":\\\"1\\\",\\\"create_date\\\":\\\"2026-04-15 10:53:28\\\"}\"'),
(49, 1, 2, 'TestBill', 'create', 'patienttestbilling', 11, '2026-04-15 17:09:15', '::1', NULL, '{\"test_details\":[{\"test_id\":\"91\",\"test_name\":\"HISTOPATHOLOGY BIOPSY SMALL SPECIMEN\",\"instruction\":\"\",\"doctor_price\":800,\"standard_price\":800}],\"total_amount\":800,\"discount\":0,\"net_amount\":800,\"payment_method\":\"Cash\"}'),
(50, 1, 2, 'Prescriptions', 'update', 'prescripition', 5, '2026-04-15 17:45:02', '::1', '\"{\\\"prescription_id\\\":\\\"5\\\",\\\"patient_name\\\":\\\"durga lakshmi\\\",\\\"appoint_register_id\\\":\\\"A202604150001\\\",\\\"patient_uid\\\":\\\"PAT0532\\\",\\\"age\\\":\\\"34\\\",\\\"gender\\\":\\\"Female\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[{\\\\\\\"test_id\\\\\\\":\\\\\\\"91\\\\\\\",\\\\\\\"test_name\\\\\\\":\\\\\\\"HISTOPATHOLOGY BIOPSY SMALL SPECIMEN\\\\\\\",\\\\\\\"instruction\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concession\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionName\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionValue\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionType\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"doctor_price\\\\\\\":800,\\\\\\\"standard_price\\\\\\\":800,\\\\\\\"test_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"test_group_id\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_name\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_price\\\\\\\":\\\\\\\"\\\\\\\"}]\\\",\\\"medicine_id\\\":\\\"[{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"9\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"ECOSPRIN AV - (ASPIRIN + ATORVASTATIN)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"10\\\\\\/25MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"6\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"0-2PM-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"0-1-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"},{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"12\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"10\\\\\\/25MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"6\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"9\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"0-2PM-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"0-1-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"Not Applicable\\\\\\\"}]\\\",\\\"prescriptiondate\\\":\\\"2026-04-15\\\",\\\"patient_vitals\\\":\\\"A202604150001\\\",\\\"finalDiagnosis\\\":\\\"\\\",\\\"chiefcomplaint\\\":\\\"\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"\\\",\\\"advise\\\":\\\"\\\",\\\"personal_note\\\":\\\"\\\",\\\"reviewafter\\\":\\\" Days\\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2026-04-15 10:53:28\\\",\\\"create_by\\\":\\\"2\\\",\\\"modify_by\\\":\\\"2\\\",\\\"org_id\\\":\\\"1\\\",\\\"create_date\\\":\\\"2026-04-15 10:53:28\\\"}\"', '\"{\\\"prescription_id\\\":\\\"5\\\",\\\"patient_name\\\":\\\"durga lakshmi\\\",\\\"appoint_register_id\\\":\\\"A202604150001\\\",\\\"patient_uid\\\":\\\"PAT0532\\\",\\\"age\\\":\\\"34\\\",\\\"gender\\\":\\\"Female\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[{\\\\\\\"test_id\\\\\\\":\\\\\\\"91\\\\\\\",\\\\\\\"test_name\\\\\\\":\\\\\\\"HISTOPATHOLOGY BIOPSY SMALL SPECIMEN\\\\\\\",\\\\\\\"instruction\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concession\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionName\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionValue\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionType\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"doctor_price\\\\\\\":800,\\\\\\\"standard_price\\\\\\\":800,\\\\\\\"test_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"test_group_id\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_name\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_price\\\\\\\":\\\\\\\"\\\\\\\"}]\\\",\\\"medicine_id\\\":\\\"[{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"9\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"ECOSPRIN AV - (ASPIRIN + ATORVASTATIN)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"10\\\\\\/25MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"6\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"0-2PM-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"0-1-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"},{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"12\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"10\\\\\\/25MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"6\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"9\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"0-2PM-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"0-1-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"Not Applicable\\\\\\\"}]\\\",\\\"prescriptiondate\\\":\\\"2026-04-15\\\",\\\"patient_vitals\\\":\\\"A202604150001\\\",\\\"finalDiagnosis\\\":\\\"\\\",\\\"chiefcomplaint\\\":\\\"\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"\\\",\\\"advise\\\":\\\"\\\",\\\"personal_note\\\":\\\"testing purpose\\\",\\\"reviewafter\\\":\\\" Days\\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2026-04-15 10:53:28\\\",\\\"create_by\\\":\\\"2\\\",\\\"modify_by\\\":\\\"2\\\",\\\"org_id\\\":\\\"1\\\",\\\"create_date\\\":\\\"2026-04-15 10:53:28\\\"}\"'),
(51, 1, 2, 'EchoReport', 'create', 'echo_reports', 0, '2026-04-16 09:06:13', '::1', NULL, '{\"patient_name\":\"Y.BHIMA RAJU\"}'),
(52, 1, 2, 'Doctor Timeslot', 'create', 'doctors_time_slot', 47, '2026-04-16 14:45:45', '::1', NULL, '{\"doctors_time_id\":\"47\",\"doctorName_registrationNumber\":\"1\",\"available_date\":\"2026-04-16\",\"doctortime_type\":\"Daily\",\"selectedDays\":\"\",\"modify_by\":\"2\",\"created_by\":\"2\",\"org_id\":\"1\",\"c_d_t\":\"2026-04-16 14:45:45\",\"status\":\"1\",\"multi_id\":\"0\"}'),
(53, 1, 2, 'Appointments', 'create', 'appointment_online', 661, '2026-04-16 14:46:40', '::1', NULL, '{\"appoint_id\":\"661\",\"bill_id\":\"BID000549\",\"bill_date\":null,\"appoint_register_id\":\"A202604160001\",\"appoint_unicode\":\"PAT0533\",\"patient_name\":\"durga prasad\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"30\",\"dob\":\"1995-12-17\",\"mobile_number\":\"8787878778\",\"patient_email\":\"\",\"appoint_date\":\"2026-04-16\",\"doctor_name\":\"1\",\"start_time\":\"16:30\",\"end_time\":\"16:45\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-04-16 14:46:40\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-04-16\",\"valid_to\":\"2026-04-29\",\"appointment_status\":\"0\",\"patient_history\":\"\"}'),
(54, 1, 2, 'GynaecRx', 'create', 'gynaec_prescriptions', 1, '2026-04-16 15:22:38', '::1', NULL, '{\"patient_name\":\"durga prasad\"}'),
(55, 1, 2, 'Prescriptions', 'create', 'prescripition', 6, '2026-04-16 15:41:54', '::1', NULL, '\"{\\\"prescription_id\\\":\\\"6\\\",\\\"patient_name\\\":\\\"durga prasad\\\",\\\"appoint_register_id\\\":\\\"A202604160001\\\",\\\"patient_uid\\\":\\\"PAT0533\\\",\\\"age\\\":\\\"30\\\",\\\"gender\\\":\\\"Male\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[]\\\",\\\"medicine_id\\\":\\\"[]\\\",\\\"prescriptiondate\\\":\\\"2026-04-16\\\",\\\"patient_vitals\\\":\\\"A202604160001\\\",\\\"finalDiagnosis\\\":\\\"\\\",\\\"chiefcomplaint\\\":\\\"\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"\\\",\\\"advise\\\":\\\"\\\",\\\"personal_note\\\":\\\"\\\",\\\"reviewafter\\\":\\\" \\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2026-04-16 15:41:54\\\",\\\"create_by\\\":\\\"2\\\",\\\"modify_by\\\":\\\"2\\\",\\\"org_id\\\":\\\"1\\\",\\\"create_date\\\":\\\"2026-04-16 15:41:54\\\"}\"'),
(56, 1, 2, 'GynaecRx', 'update', 'gynaec_prescriptions', 1, '2026-04-16 19:57:01', '::1', NULL, '{\"patient_name\":\"durga prasad\"}'),
(57, 1, 2, 'GynaecRx', 'update', 'gynaec_prescriptions', 1, '2026-04-16 20:13:59', '::1', NULL, '{\"patient_name\":\"durga prasad\"}'),
(58, 1, 2, 'Doctor Timeslot', 'create', 'doctors_time_slot', 48, '2026-05-06 12:32:15', '::1', NULL, '{\"doctors_time_id\":\"48\",\"doctorName_registrationNumber\":\"1\",\"available_date\":\"2026-05-06\",\"doctortime_type\":\"Daily\",\"selectedDays\":\"\",\"modify_by\":\"2\",\"created_by\":\"2\",\"org_id\":\"1\",\"c_d_t\":\"2026-05-06 12:32:15\",\"status\":\"1\",\"multi_id\":\"0\"}'),
(59, 1, 2, 'Appointments', 'create', 'appointment_online', 662, '2026-05-06 12:33:09', '::1', NULL, '{\"appoint_id\":\"662\",\"bill_id\":\"BID000550\",\"bill_date\":null,\"appoint_register_id\":\"A202605060001\",\"appoint_unicode\":\"PAT0532\",\"patient_name\":\"durga lakshmi\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"34\",\"dob\":\"1991-06-18\",\"mobile_number\":\"7032760271\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-06\",\"doctor_name\":\"1\",\"start_time\":\"14:17\",\"end_time\":\"14:32\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-06 12:33:09\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-06\",\"valid_to\":\"2026-05-19\",\"appointment_status\":\"0\",\"patient_history\":\"\"}'),
(60, 1, 2, 'Prescriptions', 'create', 'prescripition', 7, '2026-05-06 12:35:37', '::1', NULL, '\"{\\\"prescription_id\\\":\\\"7\\\",\\\"patient_name\\\":\\\"durga lakshmi\\\",\\\"appoint_register_id\\\":\\\"A202605060001\\\",\\\"patient_uid\\\":\\\"PAT0532\\\",\\\"age\\\":\\\"34\\\",\\\"gender\\\":\\\"Female\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[{\\\\\\\"test_id\\\\\\\":\\\\\\\"102\\\\\\\",\\\\\\\"test_name\\\\\\\":\\\\\\\"LEPTOSPIRA IGM SERUM\\\\\\\",\\\\\\\"instruction\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concession\\\\\\\":\\\\\\\"Family (50%)\\\\\\\",\\\\\\\"concessionName\\\\\\\":\\\\\\\"Family\\\\\\\",\\\\\\\"concessionValue\\\\\\\":50,\\\\\\\"concessionType\\\\\\\":\\\\\\\"percentage\\\\\\\",\\\\\\\"doctor_price\\\\\\\":700,\\\\\\\"standard_price\\\\\\\":1400,\\\\\\\"test_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"test_group_id\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_name\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_price\\\\\\\":\\\\\\\"\\\\\\\"}]\\\",\\\"medicine_id\\\":\\\"[{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"11\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"GABACOX - M - (GABAPENTIN + METHYLCOBALAMIN)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"10\\\\\\/25MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"3\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"3\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"0-0-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"0-0-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"},{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"EMBETA TM - (METAPROLOL + TELMISARTAN)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"14\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"50MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"6\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"0-2PM-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"0-1-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"}]\\\",\\\"prescriptiondate\\\":\\\"2026-05-06\\\",\\\"patient_vitals\\\":\\\"A202605060001\\\",\\\"finalDiagnosis\\\":\\\"testing\\\\npurpose\\\\n123@\\\",\\\"chiefcomplaint\\\":\\\"\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"\\\",\\\"advise\\\":\\\"\\\",\\\"personal_note\\\":\\\"\\\",\\\"reviewafter\\\":\\\"5 \\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"2026-05-06\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2026-05-06 12:35:37\\\",\\\"create_by\\\":\\\"2\\\",\\\"modify_by\\\":\\\"2\\\",\\\"org_id\\\":\\\"1\\\",\\\"create_date\\\":\\\"2026-05-06 12:35:37\\\"}\"'),
(61, 1, 2, 'TestBill', 'create', 'patienttestbilling', 13, '2026-05-06 12:36:24', '::1', NULL, '{\"test_details\":[{\"test_id\":\"102\",\"test_name\":\"LEPTOSPIRA IGM SERUM\",\"instruction\":\"\",\"doctor_price\":700,\"standard_price\":1400}],\"total_amount\":1400,\"discount\":700,\"net_amount\":700,\"payment_method\":\"Cash\"}'),
(62, 1, 2, 'Security', 'delete', 'security', 13, '2026-05-06 12:41:55', '::1', '{\"security_id\":\"13\",\"admin_name\":\"ravi\",\"email\":\"ravi@gmail.com\",\"contact\":\"7095678679\",\"security_password\":\"827ccb0eea8a706c4c34a16891f84e7b\",\"image_url\":\"\",\"signature_url\":\"\",\"role_id\":\"11\",\"security_type\":\"U\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"status\":\"1\",\"create_date_time\":\"2026-04-15 11:55:04\"}', '{\"security_id\":\"13\",\"admin_name\":\"ravi\",\"email\":\"ravi@gmail.com\",\"contact\":\"7095678679\",\"security_password\":\"827ccb0eea8a706c4c34a16891f84e7b\",\"image_url\":\"\",\"signature_url\":\"\",\"role_id\":\"11\",\"security_type\":\"U\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"status\":\"0\",\"create_date_time\":\"2026-05-06 12:41:55\"}'),
(63, 1, 2, 'Security', 'create', 'security', 14, '2026-05-06 12:42:31', '::1', NULL, '{\"security_id\":\"14\",\"admin_name\":\"Dr. Rohith\",\"email\":\"rohith@gmail.com\",\"contact\":\"7032760275\",\"security_password\":\"827ccb0eea8a706c4c34a16891f84e7b\",\"image_url\":\"\",\"signature_url\":\"\",\"role_id\":\"2\",\"security_type\":\"U\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"status\":\"1\",\"create_date_time\":\"2026-05-06 12:42:31\"}'),
(64, 1, 2, 'Appointments', 'create', 'appointment_online', 663, '2026-05-06 14:13:33', '::1', NULL, '{\"appoint_id\":\"663\",\"bill_id\":\"BID000551\",\"bill_date\":null,\"appoint_register_id\":\"A202605060002\",\"appoint_unicode\":\"PAT0012\",\"patient_name\":\"G NARAYANA\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"54\",\"dob\":\"1971-05-07\",\"mobile_number\":\"7077538378\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-06\",\"doctor_name\":\"1\",\"start_time\":\"14:47\",\"end_time\":\"15:02\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-06 14:13:33\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-06\",\"valid_to\":\"2026-05-19\",\"appointment_status\":\"0\",\"patient_history\":\"\",\"queue_order\":null}'),
(65, 1, 2, 'TestBill', 'create', 'patienttestbilling', 14, '2026-05-06 14:31:50', '::1', NULL, '{\"test_details\":[{\"test_id\":\"102\",\"test_name\":\"LEPTOSPIRA IGM SERUM\",\"instruction\":\"\",\"doctor_price\":700,\"standard_price\":1400}],\"total_amount\":1400,\"discount\":700,\"net_amount\":700,\"payment_method\":\"Both (Cash + UPI)\"}'),
(66, 1, 2, 'MedicineBill', 'create', 'patient_medicine_billing', 4, '2026-05-06 14:40:51', '::1', NULL, '{\"patient_id\":\"PAT0532\",\"appointment_id\":\"A202605060001\",\"prescription_id\":7,\"medicine_details\":[{\"medicine_id\":11,\"medicine_name\":\"GABACOX - M - (GABAPENTIN + METHYLCOBALAMIN)\",\"type_text\":\"Tab\",\"unit_text\":\"10\\/25MG\",\"dosage_text\":\"0-0-1\",\"when_text\":\"After Food\",\"time_text\":\"0-0-9PM\",\"duration_value\":4,\"duration\":\"Days\",\"notes\":\"\",\"price\":50,\"discount\":0,\"final_amount\":50,\"purchase_source\":\"Hospital Pharmacy\"},{\"medicine_id\":10,\"medicine_name\":\"EMBETA TM - (METAPROLOL + TELMISARTAN)\",\"type_text\":\"Tab\",\"unit_text\":\"50MG\",\"dosage_text\":\"0-1-1\",\"when_text\":\"After Food\",\"time_text\":\"0-2PM-9PM\",\"duration_value\":4,\"duration\":\"Days\",\"notes\":\"\",\"price\":60,\"discount\":0,\"final_amount\":60,\"purchase_source\":\"Hospital Pharmacy\"}],\"advice\":\"\",\"personal_note\":\"\",\"total_amount\":110,\"discount\":0,\"net_amount\":110,\"hospital_gross\":110,\"hospital_discount\":0,\"hospital_total\":110,\"outside_total\":0,\"purchase_source\":\"Hospital Pharmacy\",\"payment_method\":\"Both (Cash + UPI)\"}'),
(67, 1, 2, 'Appointments', 'update', 'appointment_online', 662, '2026-05-06 21:22:54', '::1', '{\"appoint_id\":\"662\",\"bill_id\":\"BID000550\",\"bill_date\":\"2026-05-06\",\"appoint_register_id\":\"A202605060001\",\"appoint_unicode\":\"PAT0532\",\"patient_name\":\"durga lakshmi\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"34\",\"dob\":\"1991-06-18\",\"mobile_number\":\"7032760271\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-06\",\"doctor_name\":\"1\",\"start_time\":\"14:17\",\"end_time\":\"14:32\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"1\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-06 14:14:45\",\"amount_method\":\"Both (Cash + UPI)\",\"cash_amount\":\"300.00\",\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"12345\",\"transaction_amount\":\"200.00\",\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"0\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-06\",\"valid_to\":\"2026-05-19\",\"appointment_status\":\"1\",\"patient_history\":\"\",\"queue_order\":\"2\",\"referred_by\":null,\"referral_hospital\":null,\"referral_notes\":null,\"referral_type\":null}', '{\"appoint_id\":\"662\",\"bill_id\":\"BID000550\",\"bill_date\":\"2026-05-06\",\"appoint_register_id\":\"A202605060001\",\"appoint_unicode\":\"PAT0532\",\"patient_name\":\"durga lakshmi\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"34\",\"dob\":\"0000-00-00\",\"mobile_number\":\"7032760271\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-06\",\"doctor_name\":\"1\",\"start_time\":\"14:17\",\"end_time\":\"14:32\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"1\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-06 21:22:54\",\"amount_method\":\"\",\"cash_amount\":\"300.00\",\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":\"200.00\",\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-06\",\"valid_to\":\"2026-05-19\",\"appointment_status\":\"0\",\"patient_history\":\"\",\"queue_order\":\"2\",\"referred_by\":\"Dr.ravi\",\"referral_hospital\":\"ABC hospital\",\"referral_notes\":\"family friend\",\"referral_type\":\"External\"}'),
(68, 1, 2, 'Doctor Timeslot', 'create', 'doctors_time_slot', 49, '2026-05-07 09:43:12', '::1', NULL, '{\"doctors_time_id\":\"49\",\"doctorName_registrationNumber\":\"1\",\"available_date\":\"2026-05-07\",\"doctortime_type\":\"Daily\",\"selectedDays\":\"\",\"modify_by\":\"2\",\"created_by\":\"2\",\"org_id\":\"1\",\"c_d_t\":\"2026-05-07 09:43:12\",\"status\":\"1\",\"multi_id\":\"0\"}'),
(69, 1, 2, 'Appointments', 'create', 'appointment_online', 664, '2026-05-07 09:44:27', '::1', NULL, '{\"appoint_id\":\"664\",\"bill_id\":\"BID000552\",\"bill_date\":null,\"appoint_register_id\":\"A202605070001\",\"appoint_unicode\":\"PAT0534\",\"patient_name\":\"Ji won\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"49\",\"dob\":\"1976-05-08\",\"mobile_number\":\"8676867589\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-07\",\"doctor_name\":\"1\",\"start_time\":\"14:58\",\"end_time\":\"15:13\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-07 09:44:27\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-07\",\"valid_to\":\"2026-05-20\",\"appointment_status\":\"0\",\"patient_history\":\"\",\"queue_order\":null,\"referred_by\":\"Dr. Aswin\",\"referral_hospital\":\"medicover\",\"referral_notes\":\"take much care\",\"referral_type\":\"Internal\"}'),
(70, 1, 2, 'Security', 'update', 'security', 2, '2026-05-07 10:28:51', '::1', '{\"security_id\":\"2\",\"admin_name\":\"Dr.Ashwin Kumar Panda\",\"user_code\":\"A001\",\"email\":\"pandas@gmail.com\",\"contact\":\"8897355655\",\"security_password\":\"827ccb0eea8a706c4c34a16891f84e7b\",\"image_url\":\" - 2025.09.19 - 12.34.59pm.png\",\"signature_url\":\"\",\"role_id\":\"2\",\"security_type\":\"A\",\"org_id\":\"1\",\"created_by\":\"1\",\"modified_by\":\"1\",\"status\":\"1\",\"create_date_time\":\"2026-05-07 10:25:51\"}', '{\"security_id\":\"2\",\"admin_name\":\"Dr.Ashwin Kumar Panda\",\"user_code\":\"A001\",\"email\":\"pandas@gmail.com\",\"contact\":\"8897355655\",\"security_password\":\"827ccb0eea8a706c4c34a16891f84e7b\",\"image_url\":\" - 2025.09.19 - 12.34.59pm.png\",\"signature_url\":\"\",\"role_id\":\"2\",\"security_type\":\"A\",\"org_id\":\"1\",\"created_by\":\"1\",\"modified_by\":\"2\",\"status\":\"1\",\"create_date_time\":\"2026-05-07 10:28:51\"}'),
(71, 1, 2, 'Doctors', 'update', 'doctor', 6, '2026-05-07 11:04:24', '::1', '{\"doc_id\":\"6\",\"doc_registration_number\":\"D202509240003\",\"doctor_name\":\"Administrator\",\"doctor_type\":\"In\",\"gender\":\"Male\",\"phone_number\":\"6302669660\",\"email\":\"durgalaxmi417@gmail.com\",\"doctor_specialization\":\"18\",\"departments\":\"10\",\"doctor_services\":\"1\",\"doctor_fee\":\"200\",\"doctor_charge\":\"0\",\"doctor_visit_charge\":\"0\",\"time_slot_duration\":\"18\",\"details\":\"\",\"doc_img\":\"doc_1758860860.jpg\",\"org_id\":\"1\",\"security_id\":\"10\",\"status\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"c_d_t\":\"2025-09-24 16:57:06\"}', '{\"doc_id\":\"6\",\"doc_registration_number\":\"D202509240003\",\"doctor_name\":\"Administrator\",\"doctor_type\":\"In\",\"gender\":\"Male\",\"phone_number\":\"6302669660\",\"email\":\"durgalaxmi417@gmail.com\",\"doctor_specialization\":\"18\",\"departments\":\"10\",\"doctor_services\":\"1\",\"doctor_fee\":\"200\",\"doctor_charge\":\"0\",\"doctor_visit_charge\":\"0\",\"time_slot_duration\":\"18\",\"details\":\"\",\"doc_img\":\"doc_1778132064.jpg\",\"org_id\":\"1\",\"security_id\":\"10\",\"status\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"c_d_t\":\"2025-09-24 16:57:06\"}'),
(72, 1, 2, 'Receptionnist', 'update', 'receptionnist', 6, '2026-05-07 11:04:24', '::1', NULL, '[{\"rep_id\":\"3\",\"doc_id\":\"6\",\"security_id\":\"12\",\"user_name\":\"Dr. Venkatesh\",\"org_id\":\"1\",\"status\":\"1\",\"created_by\":\"2\",\"created_at\":\"2025-09-24 16:57:06\",\"modified_by\":\"2\",\"modified_at\":\"2026-05-07 11:04:24\"}]'),
(73, 1, 2, 'Doctors', 'update', 'doctor', 1, '2026-05-07 11:06:06', '::1', '{\"doc_id\":\"1\",\"doc_registration_number\":\"D202509180001\",\"doctor_name\":\"Dr.Ashwin Kumar Panda\",\"doctor_type\":\"Out\",\"gender\":\"Male\",\"phone_number\":\"8897355655\",\"email\":\"pandas@gmail.com\",\"doctor_specialization\":\"18\",\"departments\":\"1\",\"doctor_services\":\"1\",\"doctor_fee\":\"500\",\"doctor_charge\":\"2000\",\"doctor_visit_charge\":\"\",\"time_slot_duration\":\"15\",\"details\":\"test\",\"doc_img\":\"doc_1776234502.png\",\"org_id\":\"1\",\"security_id\":\"2\",\"status\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"c_d_t\":\"2025-09-18 10:02:14\"}', '{\"doc_id\":\"1\",\"doc_registration_number\":\"D202509180001\",\"doctor_name\":\"Dr.Ashwin Kumar Panda\",\"doctor_type\":\"Out\",\"gender\":\"Male\",\"phone_number\":\"8897355655\",\"email\":\"pandas@gmail.com\",\"doctor_specialization\":\"18\",\"departments\":\"1\",\"doctor_services\":\"1\",\"doctor_fee\":\"500\",\"doctor_charge\":\"2000\",\"doctor_visit_charge\":\"\",\"time_slot_duration\":\"15\",\"details\":\"test\",\"doc_img\":\"doc_1778132166.jpg\",\"org_id\":\"1\",\"security_id\":\"2\",\"status\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"c_d_t\":\"2025-09-18 10:02:14\"}'),
(74, 1, 2, 'Receptionnist', 'update', 'receptionnist', 1, '2026-05-07 11:06:06', '::1', '[{\"rep_id\":\"2\",\"doc_id\":\"1\",\"security_id\":\"12\",\"user_name\":\"Dr. Venkatesh\",\"org_id\":\"1\",\"status\":\"1\",\"created_by\":\"2\",\"created_at\":\"2025-09-24 12:25:35\",\"modified_by\":\"2\",\"modified_at\":\"2026-04-15 11:58:22\"},{\"rep_id\":\"4\",\"doc_id\":\"1\",\"security_id\":\"13\",\"user_name\":\"ravi\",\"org_id\":\"1\",\"status\":\"1\",\"created_by\":\"2\",\"created_at\":\"2026-04-15 11:58:22\",\"modified_by\":\"2\",\"modified_at\":\"2026-04-15 11:58:22\"}]', '[{\"rep_id\":\"2\",\"doc_id\":\"1\",\"security_id\":\"12\",\"user_name\":\"Dr. Venkatesh\",\"org_id\":\"1\",\"status\":\"1\",\"created_by\":\"2\",\"created_at\":\"2025-09-24 12:25:35\",\"modified_by\":\"2\",\"modified_at\":\"2026-05-07 11:06:06\"},{\"rep_id\":\"5\",\"doc_id\":\"1\",\"security_id\":\"14\",\"user_name\":\"Dr. Rohith\",\"org_id\":\"1\",\"status\":\"1\",\"created_by\":\"2\",\"created_at\":\"2026-05-07 11:06:06\",\"modified_by\":\"2\",\"modified_at\":\"2026-05-07 11:06:06\"}]'),
(75, 1, 2, 'Security', 'update', 'security', 10, '2026-05-07 11:14:51', '::1', '{\"security_id\":\"10\",\"admin_name\":\"Administrator\",\"user_code\":\"D001\",\"email\":\"durgalaxmi417@gmail.com\",\"contact\":\"6302669660\",\"security_password\":\"827ccb0eea8a706c4c34a16891f84e7b\",\"image_url\":\"\",\"signature_url\":\"\",\"role_id\":\"2\",\"security_type\":\"U\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"status\":\"1\",\"create_date_time\":\"2026-05-07 11:04:24\"}', '{\"security_id\":\"10\",\"admin_name\":\"Administrator\",\"user_code\":\"D001\",\"email\":\"durgalaxmi417@gmail.com\",\"contact\":\"6302669660\",\"security_password\":\"827ccb0eea8a706c4c34a16891f84e7b\",\"image_url\":\"\",\"signature_url\":\"\",\"role_id\":\"12\",\"security_type\":\"U\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"status\":\"1\",\"create_date_time\":\"2026-05-07 11:14:51\"}'),
(76, 1, 2, 'Roles', 'update', 'roles', 12, '2026-05-07 11:18:02', '::1', '{\"role_id\":\"12\",\"role_name\":\"Pharmacist\",\"created_by\":\"1\",\"created_date_time\":\"2026-04-15 15:37:14\",\"status\":\"1\",\"modified_by\":\"1\",\"modified_date_time\":\"2026-04-15 15:37:14\",\"org_id\":\"1\"}', '{\"role_id\":\"12\",\"role_name\":\"Pharmacist\",\"created_by\":\"1\",\"created_date_time\":\"2026-04-15 15:37:14\",\"status\":\"1\",\"modified_by\":\"2\",\"modified_date_time\":\"2026-05-07 11:18:02\",\"org_id\":\"1\"}'),
(77, 1, 10, 'MedicineBill', 'create', 'patient_medicine_billing', 5, '2026-05-07 11:24:06', '::1', NULL, '{\"patient_id\":\"PAT0532\",\"appointment_id\":\"A202605060001\",\"prescription_id\":7,\"medicine_details\":[{\"medicine_id\":11,\"medicine_name\":\"GABACOX - M - (GABAPENTIN + METHYLCOBALAMIN)\",\"type_text\":\"Tab\",\"unit_text\":\"10\\/25MG\",\"dosage_text\":\"0-0-1\",\"when_text\":\"After Food\",\"time_text\":\"0-0-9PM\",\"duration_value\":4,\"duration\":\"Days\",\"notes\":\"\",\"price\":50,\"discount\":0,\"final_amount\":50,\"purchase_source\":\"Hospital Pharmacy\"},{\"medicine_id\":10,\"medicine_name\":\"EMBETA TM - (METAPROLOL + TELMISARTAN)\",\"type_text\":\"Tab\",\"unit_text\":\"50MG\",\"dosage_text\":\"0-1-1\",\"when_text\":\"After Food\",\"time_text\":\"0-2PM-9PM\",\"duration_value\":4,\"duration\":\"Days\",\"notes\":\"\",\"price\":65,\"discount\":0,\"final_amount\":65,\"purchase_source\":\"Hospital Pharmacy\"}],\"advice\":\"\",\"personal_note\":\"\",\"total_amount\":115,\"discount\":0,\"net_amount\":115,\"hospital_gross\":115,\"hospital_discount\":0,\"hospital_total\":115,\"outside_total\":0,\"purchase_source\":\"Hospital Pharmacy\",\"payment_method\":\"Both (Cash + UPI)\"}'),
(78, 1, 2, 'Invoice', '', 'invoice', 17, '2026-05-07 12:04:20', '::1', '{\"status\":\"1\"}', '{\"status\":\"0\",\"refund_type\":\"refund\",\"refund_amount\":100,\"refund_reason\":\"friend\",\"refunded_by\":\"2\",\"refunded_at\":\"2026-05-07 12:04:20\"}');
INSERT INTO `audit_log` (`id`, `org_id`, `user_id`, `module`, `action`, `entity`, `entity_id`, `ts`, `ip`, `before_json`, `after_json`) VALUES
(79, 1, 2, 'Prescriptions', 'create', 'prescripition', 8, '2026-05-07 12:40:26', '::1', NULL, '\"{\\\"prescription_id\\\":\\\"8\\\",\\\"patient_name\\\":\\\"Ji won\\\",\\\"appoint_register_id\\\":\\\"A202605070001\\\",\\\"patient_uid\\\":\\\"PAT0534\\\",\\\"age\\\":\\\"49\\\",\\\"gender\\\":\\\"Female\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[{\\\\\\\"test_id\\\\\\\":\\\\\\\"82\\\\\\\",\\\\\\\"test_name\\\\\\\":\\\\\\\"CYTOLOGY (PAP SMEAR) (LBC)\\\\\\\",\\\\\\\"instruction\\\\\\\":\\\\\\\"test\\\\\\\",\\\\\\\"concession\\\\\\\":\\\\\\\"Family (50%)\\\\\\\",\\\\\\\"concessionName\\\\\\\":\\\\\\\"Family\\\\\\\",\\\\\\\"concessionValue\\\\\\\":50,\\\\\\\"concessionType\\\\\\\":\\\\\\\"percentage\\\\\\\",\\\\\\\"doctor_price\\\\\\\":600,\\\\\\\"standard_price\\\\\\\":1200,\\\\\\\"test_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"test_group_id\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_name\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_price\\\\\\\":\\\\\\\"\\\\\\\"}]\\\",\\\"medicine_id\\\":\\\"[{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"13\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"IVABRATCO - (IVABRADINE)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"10\\\\\\/25MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"2\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"9AM-2PM-0\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"1-1-0\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"},{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"15\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"JBTOR PLUS LS - (TORSEMIDE + ALDACTONE)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"14\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"50MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"9AM-0-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"1-0-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"}]\\\",\\\"prescriptiondate\\\":\\\"2026-05-07\\\",\\\"patient_vitals\\\":\\\"A202605070001\\\",\\\"finalDiagnosis\\\":\\\"test\\\",\\\"chiefcomplaint\\\":\\\"mhgnh\\\\nkjymjyh\\\\njhmjhm\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"\\\",\\\"advise\\\":\\\"need to take \\\\nmuch care\\\",\\\"personal_note\\\":\\\"future care\\\",\\\"reviewafter\\\":\\\"5 Days\\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"2026-05-12\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2026-05-07 12:40:26\\\",\\\"create_by\\\":\\\"2\\\",\\\"modify_by\\\":\\\"2\\\",\\\"org_id\\\":\\\"1\\\",\\\"create_date\\\":\\\"2026-05-07 12:40:26\\\"}\"'),
(80, 1, 2, 'Appointments', 'create', 'appointment_online', 665, '2026-05-07 12:58:20', '::1', NULL, '{\"appoint_id\":\"665\",\"bill_id\":\"BID000553\",\"bill_date\":null,\"appoint_register_id\":\"A202605070002\",\"appoint_unicode\":\"PAT0011\",\"patient_name\":\"G LAXMI\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"49\",\"dob\":\"1976-05-08\",\"mobile_number\":\"8699494819\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-07\",\"doctor_name\":\"1\",\"start_time\":\"15:43\",\"end_time\":\"15:58\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-07 12:58:20\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-07\",\"valid_to\":\"2026-05-20\",\"appointment_status\":\"0\",\"patient_history\":\"\",\"queue_order\":null,\"referred_by\":\"\",\"referral_hospital\":\"\",\"referral_notes\":\"\",\"referral_type\":\"\"}'),
(81, 1, 2, 'Roles', 'update', 'roles', 12, '2026-05-07 15:00:34', '::1', '{\"role_id\":\"12\",\"role_name\":\"Pharmacist\",\"created_by\":\"1\",\"created_date_time\":\"2026-04-15 15:37:14\",\"status\":\"1\",\"modified_by\":\"2\",\"modified_date_time\":\"2026-05-07 11:18:02\",\"org_id\":\"1\"}', '{\"role_id\":\"12\",\"role_name\":\"Pharmacist\",\"created_by\":\"1\",\"created_date_time\":\"2026-04-15 15:37:14\",\"status\":\"1\",\"modified_by\":\"2\",\"modified_date_time\":\"2026-05-07 11:18:02\",\"org_id\":\"1\"}'),
(82, 1, 2, 'Roles', 'update', 'roles', 12, '2026-05-07 15:20:08', '::1', '{\"role_id\":\"12\",\"role_name\":\"Pharmacist\",\"created_by\":\"1\",\"created_date_time\":\"2026-04-15 15:37:14\",\"status\":\"1\",\"modified_by\":\"2\",\"modified_date_time\":\"2026-05-07 11:18:02\",\"org_id\":\"1\"}', '{\"role_id\":\"12\",\"role_name\":\"Pharmacist\",\"created_by\":\"1\",\"created_date_time\":\"2026-04-15 15:37:14\",\"status\":\"1\",\"modified_by\":\"2\",\"modified_date_time\":\"2026-05-07 11:18:02\",\"org_id\":\"1\"}'),
(83, 1, 2, 'Prescriptions', 'update', 'prescripition', 8, '2026-05-07 15:22:44', '::1', '\"{\\\"prescription_id\\\":\\\"8\\\",\\\"patient_name\\\":\\\"Ji won\\\",\\\"appoint_register_id\\\":\\\"A202605070001\\\",\\\"patient_uid\\\":\\\"PAT0534\\\",\\\"age\\\":\\\"49\\\",\\\"gender\\\":\\\"Female\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[{\\\\\\\"test_id\\\\\\\":\\\\\\\"82\\\\\\\",\\\\\\\"test_name\\\\\\\":\\\\\\\"CYTOLOGY (PAP SMEAR) (LBC)\\\\\\\",\\\\\\\"instruction\\\\\\\":\\\\\\\"test\\\\\\\",\\\\\\\"concession\\\\\\\":\\\\\\\"Family (50%)\\\\\\\",\\\\\\\"concessionName\\\\\\\":\\\\\\\"Family\\\\\\\",\\\\\\\"concessionValue\\\\\\\":50,\\\\\\\"concessionType\\\\\\\":\\\\\\\"percentage\\\\\\\",\\\\\\\"doctor_price\\\\\\\":600,\\\\\\\"standard_price\\\\\\\":1200,\\\\\\\"test_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"test_group_id\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_name\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_price\\\\\\\":\\\\\\\"\\\\\\\"}]\\\",\\\"medicine_id\\\":\\\"[{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"13\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"IVABRATCO - (IVABRADINE)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"10\\\\\\/25MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"2\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"9AM-2PM-0\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"1-1-0\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"},{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"15\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"JBTOR PLUS LS - (TORSEMIDE + ALDACTONE)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"14\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"50MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"9AM-0-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"1-0-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"}]\\\",\\\"prescriptiondate\\\":\\\"2026-05-07\\\",\\\"patient_vitals\\\":\\\"A202605070001\\\",\\\"finalDiagnosis\\\":\\\"test\\\",\\\"chiefcomplaint\\\":\\\"mhgnh\\\\nkjymjyh\\\\njhmjhm\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"\\\",\\\"advise\\\":\\\"need to take \\\\nmuch care\\\",\\\"personal_note\\\":\\\"future care\\\",\\\"reviewafter\\\":\\\"5 Days\\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"2026-05-12\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2026-05-07 12:40:26\\\",\\\"create_by\\\":\\\"2\\\",\\\"modify_by\\\":\\\"2\\\",\\\"org_id\\\":\\\"1\\\",\\\"create_date\\\":\\\"2026-05-07 12:40:26\\\"}\"', '\"{\\\"prescription_id\\\":\\\"8\\\",\\\"patient_name\\\":\\\"Ji won\\\",\\\"appoint_register_id\\\":\\\"A202605070001\\\",\\\"patient_uid\\\":\\\"PAT0534\\\",\\\"age\\\":\\\"49\\\",\\\"gender\\\":\\\"Female\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[{\\\\\\\"test_id\\\\\\\":\\\\\\\"82\\\\\\\",\\\\\\\"test_name\\\\\\\":\\\\\\\"CYTOLOGY (PAP SMEAR) (LBC)\\\\\\\",\\\\\\\"instruction\\\\\\\":\\\\\\\"test\\\\\\\",\\\\\\\"concession\\\\\\\":\\\\\\\"Family (50%)\\\\\\\",\\\\\\\"concessionName\\\\\\\":\\\\\\\"Family\\\\\\\",\\\\\\\"concessionValue\\\\\\\":50,\\\\\\\"concessionType\\\\\\\":\\\\\\\"percentage\\\\\\\",\\\\\\\"doctor_price\\\\\\\":600,\\\\\\\"standard_price\\\\\\\":1200,\\\\\\\"test_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"test_group_id\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_name\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_price\\\\\\\":\\\\\\\"\\\\\\\"}]\\\",\\\"medicine_id\\\":\\\"[{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"13\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"IVABRATCO - (IVABRADINE)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"10\\\\\\/25MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"2\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"9AM-2PM-0\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"1-1-0\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"},{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"15\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"JBTOR PLUS LS - (TORSEMIDE + ALDACTONE)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"14\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"50MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"9AM-0-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"1-0-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"}]\\\",\\\"prescriptiondate\\\":\\\"2026-05-07\\\",\\\"patient_vitals\\\":\\\"A202605070001\\\",\\\"finalDiagnosis\\\":\\\"test\\\",\\\"chiefcomplaint\\\":\\\"mhgnh\\\\nkjymjyh\\\\njhmjhm\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"testing purpose new\\\",\\\"advise\\\":\\\"need to take \\\\nmuch care\\\",\\\"personal_note\\\":\\\"future care\\\",\\\"reviewafter\\\":\\\"5 Days\\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"2026-05-12\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2026-05-07 12:40:26\\\",\\\"create_by\\\":\\\"2\\\",\\\"modify_by\\\":\\\"2\\\",\\\"org_id\\\":\\\"1\\\",\\\"create_date\\\":\\\"2026-05-07 12:40:26\\\"}\"'),
(84, 1, 2, 'Prescriptions', 'update', 'prescripition', 8, '2026-05-07 15:53:47', '::1', '\"{\\\"prescription_id\\\":\\\"8\\\",\\\"patient_name\\\":\\\"Ji won\\\",\\\"appoint_register_id\\\":\\\"A202605070001\\\",\\\"patient_uid\\\":\\\"PAT0534\\\",\\\"age\\\":\\\"49\\\",\\\"gender\\\":\\\"Female\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[{\\\\\\\"test_id\\\\\\\":\\\\\\\"82\\\\\\\",\\\\\\\"test_name\\\\\\\":\\\\\\\"CYTOLOGY (PAP SMEAR) (LBC)\\\\\\\",\\\\\\\"instruction\\\\\\\":\\\\\\\"test\\\\\\\",\\\\\\\"concession\\\\\\\":\\\\\\\"Family (50%)\\\\\\\",\\\\\\\"concessionName\\\\\\\":\\\\\\\"Family\\\\\\\",\\\\\\\"concessionValue\\\\\\\":50,\\\\\\\"concessionType\\\\\\\":\\\\\\\"percentage\\\\\\\",\\\\\\\"doctor_price\\\\\\\":600,\\\\\\\"standard_price\\\\\\\":1200,\\\\\\\"test_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"test_group_id\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_name\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_price\\\\\\\":\\\\\\\"\\\\\\\"}]\\\",\\\"medicine_id\\\":\\\"[{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"13\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"IVABRATCO - (IVABRADINE)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"10\\\\\\/25MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"2\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"9AM-2PM-0\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"1-1-0\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"},{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"15\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"JBTOR PLUS LS - (TORSEMIDE + ALDACTONE)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"14\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"50MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"9AM-0-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"1-0-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"}]\\\",\\\"prescriptiondate\\\":\\\"2026-05-07\\\",\\\"patient_vitals\\\":\\\"A202605070001\\\",\\\"finalDiagnosis\\\":\\\"test\\\",\\\"chiefcomplaint\\\":\\\"mhgnh\\\\nkjymjyh\\\\njhmjhm\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"testing purpose new\\\",\\\"advise\\\":\\\"need to take \\\\nmuch care\\\",\\\"personal_note\\\":\\\"future care\\\",\\\"reviewafter\\\":\\\"5 Days\\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"2026-05-12\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2026-05-07 12:40:26\\\",\\\"create_by\\\":\\\"2\\\",\\\"modify_by\\\":\\\"2\\\",\\\"org_id\\\":\\\"1\\\",\\\"create_date\\\":\\\"2026-05-07 12:40:26\\\"}\"', '\"{\\\"prescription_id\\\":\\\"8\\\",\\\"patient_name\\\":\\\"Ji won\\\",\\\"appoint_register_id\\\":\\\"A202605070001\\\",\\\"patient_uid\\\":\\\"PAT0534\\\",\\\"age\\\":\\\"49\\\",\\\"gender\\\":\\\"Female\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[{\\\\\\\"test_id\\\\\\\":\\\\\\\"82\\\\\\\",\\\\\\\"test_name\\\\\\\":\\\\\\\"CYTOLOGY (PAP SMEAR) (LBC)\\\\\\\",\\\\\\\"instruction\\\\\\\":\\\\\\\"test\\\\\\\",\\\\\\\"concession\\\\\\\":\\\\\\\"Family (50%)\\\\\\\",\\\\\\\"concessionName\\\\\\\":\\\\\\\"Family\\\\\\\",\\\\\\\"concessionValue\\\\\\\":50,\\\\\\\"concessionType\\\\\\\":\\\\\\\"percentage\\\\\\\",\\\\\\\"doctor_price\\\\\\\":600,\\\\\\\"standard_price\\\\\\\":1200,\\\\\\\"test_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"test_group_id\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_name\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_price\\\\\\\":\\\\\\\"\\\\\\\"}]\\\",\\\"medicine_id\\\":\\\"[{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"13\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"IVABRATCO - (IVABRADINE)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"10\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"10\\\\\\/25MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"2\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"9AM-2PM-0\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"1-1-0\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"},{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"15\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"JBTOR PLUS LS - (TORSEMIDE + ALDACTONE)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"14\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"50MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"Days\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"9AM-0-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"1-0-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"}]\\\",\\\"prescriptiondate\\\":\\\"2026-05-07\\\",\\\"patient_vitals\\\":\\\"A202605070001\\\",\\\"finalDiagnosis\\\":\\\"test\\\",\\\"chiefcomplaint\\\":\\\"mhgnh\\\\nkjymjyh\\\\njhmjhm\\\",\\\"pasthistory\\\":\\\"testing\\\",\\\"patient_data\\\":\\\"testing purpose new\\\",\\\"advise\\\":\\\"need to take \\\\nmuch care\\\",\\\"personal_note\\\":\\\"future care\\\",\\\"reviewafter\\\":\\\"5 Days\\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"2026-05-12\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2026-05-07 12:40:26\\\",\\\"create_by\\\":\\\"2\\\",\\\"modify_by\\\":\\\"2\\\",\\\"org_id\\\":\\\"1\\\",\\\"create_date\\\":\\\"2026-05-07 12:40:26\\\"}\"'),
(85, 1, 2, 'Doctor Timeslot', 'create', 'doctors_time_slot', 50, '2026-05-07 16:39:57', '::1', NULL, '{\"doctors_time_id\":\"50\",\"doctorName_registrationNumber\":\"5\",\"available_date\":\"2026-05-07\",\"doctortime_type\":\"Daily\",\"selectedDays\":\"\",\"modify_by\":\"2\",\"created_by\":\"2\",\"org_id\":\"1\",\"c_d_t\":\"2026-05-07 16:39:57\",\"status\":\"1\",\"multi_id\":\"0\"}'),
(86, 1, 2, 'Appointments', 'create', 'appointment_online', 666, '2026-05-07 16:40:51', '::1', NULL, '{\"appoint_id\":\"666\",\"bill_id\":\"BID000554\",\"bill_date\":null,\"appoint_register_id\":\"A202605070003\",\"appoint_unicode\":\"PAT0010\",\"patient_name\":\"G KALAYANI\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"55\",\"dob\":\"1970-04-29\",\"mobile_number\":\"7995908643\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-07\",\"doctor_name\":\"5\",\"start_time\":\"18:39\",\"end_time\":\"18:51\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-07 16:40:51\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"300\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"300\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-07\",\"valid_to\":\"2026-05-20\",\"appointment_status\":\"0\",\"patient_history\":\"\",\"queue_order\":null,\"referred_by\":\"Dr. Aswin\",\"referral_hospital\":\"medicover\",\"referral_notes\":\"\",\"referral_type\":\"Internal\"}'),
(87, 1, 2, 'GynaecRx', 'create', 'gynaec_prescriptions', 2, '2026-05-07 16:44:22', '::1', NULL, '{\"patient_name\":\"G KALAYANI\"}'),
(88, 1, 2, 'Doctors', 'update', 'doctor', 5, '2026-05-07 18:19:16', '::1', '{\"doc_id\":\"5\",\"doc_registration_number\":\"D202509240002\",\"doctor_name\":\"Pravallika\",\"doctor_type\":\"In\",\"gender\":\"Female\",\"phone_number\":\"7032760271\",\"email\":\"test0@gmail.com\",\"doctor_specialization\":\"18\",\"departments\":\"10\",\"doctor_services\":\"1\",\"doctor_fee\":\"300\",\"doctor_charge\":\"0\",\"doctor_visit_charge\":\"0\",\"time_slot_duration\":\"12\",\"details\":\"test\",\"doc_img\":\"doc_1758716857.jpg\",\"org_id\":\"1\",\"security_id\":\"9\",\"status\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"c_d_t\":\"2025-09-24 12:21:50\"}', '{\"doc_id\":\"5\",\"doc_registration_number\":\"D202509240002\",\"doctor_name\":\"Pravallika\",\"doctor_type\":\"In\",\"gender\":\"Female\",\"phone_number\":\"7032760271\",\"email\":\"test0@gmail.com\",\"doctor_specialization\":\"18\",\"departments\":\"10\",\"doctor_services\":\"1\",\"doctor_fee\":\"300\",\"doctor_charge\":\"0\",\"doctor_visit_charge\":\"0\",\"time_slot_duration\":\"12\",\"details\":\"test\",\"doc_img\":\"doc_1778158156.jpg\",\"org_id\":\"1\",\"security_id\":\"9\",\"status\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"c_d_t\":\"2025-09-24 12:21:50\"}'),
(89, 1, 2, 'Receptionnist', 'update', 'receptionnist', 5, '2026-05-07 18:19:16', '::1', '[{\"rep_id\":\"1\",\"doc_id\":\"5\",\"security_id\":\"12\",\"user_name\":\"Dr. Venkatesh\",\"org_id\":\"1\",\"status\":\"1\",\"created_by\":\"2\",\"created_at\":\"2025-09-24 12:21:50\",\"modified_by\":\"2\",\"modified_at\":\"2025-09-24 17:56:18\"}]', '[{\"rep_id\":\"1\",\"doc_id\":\"5\",\"security_id\":\"12\",\"user_name\":\"Dr. Venkatesh\",\"org_id\":\"1\",\"status\":\"1\",\"created_by\":\"2\",\"created_at\":\"2025-09-24 12:21:50\",\"modified_by\":\"2\",\"modified_at\":\"2026-05-07 18:19:16\"}]'),
(90, 1, 2, 'Doctor Timeslot', 'create', 'doctors_time_slot', 51, '2026-05-08 05:38:26', '::1', NULL, '{\"doctors_time_id\":\"51\",\"doctorName_registrationNumber\":\"1\",\"available_date\":\"2026-05-08\",\"doctortime_type\":\"Daily\",\"selectedDays\":\"\",\"modify_by\":\"2\",\"created_by\":\"2\",\"org_id\":\"1\",\"c_d_t\":\"2026-05-08 05:38:26\",\"status\":\"1\",\"multi_id\":\"0\"}'),
(91, 1, 2, 'Appointments', 'create', 'appointment_online', 667, '2026-05-08 05:39:12', '::1', NULL, '{\"appoint_id\":\"667\",\"bill_id\":\"BID000555\",\"bill_date\":null,\"appoint_register_id\":\"A202605080001\",\"appoint_unicode\":\"PAT0116\",\"patient_name\":\"P NARAYANA RAO\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"32\",\"dob\":null,\"mobile_number\":\"9398380191\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-08\",\"doctor_name\":\"1\",\"start_time\":\"6:38\",\"end_time\":\"6:53\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-08 05:39:12\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-08\",\"valid_to\":\"2026-05-21\",\"appointment_status\":\"0\",\"patient_history\":\"\",\"queue_order\":null,\"referred_by\":\"\",\"referral_hospital\":\"\",\"referral_notes\":\"\",\"referral_type\":\"\"}'),
(92, 1, 2, 'Invoice', '', 'invoice', 20, '2026-05-08 07:40:01', '::1', '{\"status\":\"1\"}', '{\"status\":\"0\",\"refund_type\":\"refund\",\"refund_amount\":200,\"refund_reason\":\"family friend\",\"refunded_by\":\"2\",\"refunded_at\":\"2026-05-08 07:40:01\"}'),
(93, 1, 2, 'Appointments', 'update', 'appointment_online', 667, '2026-05-08 09:52:33', '::1', '{\"appoint_id\":\"667\",\"bill_id\":\"BID000555\",\"bill_date\":\"2026-05-08\",\"appoint_register_id\":\"A202605080001\",\"appoint_unicode\":\"PAT0116\",\"patient_name\":\"P NARAYANA RAO\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"32\",\"dob\":null,\"mobile_number\":\"9398380191\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-08\",\"doctor_name\":\"1\",\"start_time\":\"6:38\",\"end_time\":\"6:53\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-08 07:40:01\",\"amount_method\":\"UPI\",\"cash_amount\":null,\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"6789\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"0\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-08\",\"valid_to\":\"2026-05-21\",\"appointment_status\":\"0\",\"patient_history\":\"\",\"queue_order\":null,\"referred_by\":\"\",\"referral_hospital\":\"\",\"referral_notes\":\"\",\"referral_type\":\"\"}', '{\"appoint_id\":\"667\",\"bill_id\":\"BID000555\",\"bill_date\":\"2026-05-08\",\"appoint_register_id\":\"A202605080001\",\"appoint_unicode\":\"PAT0116\",\"patient_name\":\"P NARAYANA RAO\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"32\",\"dob\":null,\"mobile_number\":\"9398380191\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-08\",\"doctor_name\":\"1\",\"start_time\":\"6:38\",\"end_time\":\"6:53\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-08 09:52:33\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-08\",\"valid_to\":\"2026-05-21\",\"appointment_status\":\"0\",\"patient_history\":\"\",\"queue_order\":null,\"referred_by\":\"\",\"referral_hospital\":\"\",\"referral_notes\":\"\",\"referral_type\":\"\"}'),
(94, 1, 2, 'Appointments', 'update', 'appointment_online', 667, '2026-05-08 10:06:06', '::1', '{\"appoint_id\":\"667\",\"bill_id\":\"BID000555\",\"bill_date\":\"2026-05-08\",\"appoint_register_id\":\"A202605080001\",\"appoint_unicode\":\"PAT0116\",\"patient_name\":\"P NARAYANA RAO\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"32\",\"dob\":null,\"mobile_number\":\"9398380191\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-08\",\"doctor_name\":\"1\",\"start_time\":\"6:38\",\"end_time\":\"6:53\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-08 09:52:33\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-08\",\"valid_to\":\"2026-05-21\",\"appointment_status\":\"0\",\"patient_history\":\"\",\"queue_order\":null,\"referred_by\":\"\",\"referral_hospital\":\"\",\"referral_notes\":\"\",\"referral_type\":\"\"}', '{\"appoint_id\":\"667\",\"bill_id\":\"BID000555\",\"bill_date\":\"2026-05-08\",\"appoint_register_id\":\"A202605080001\",\"appoint_unicode\":\"PAT0116\",\"patient_name\":\"P NARAYANA RAO\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"31\",\"dob\":\"1994-05-09\",\"mobile_number\":\"9398380191\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-08\",\"doctor_name\":\"1\",\"start_time\":\"6:38\",\"end_time\":\"6:53\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-08 10:06:06\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-08\",\"valid_to\":\"2026-05-21\",\"appointment_status\":\"0\",\"patient_history\":\"\",\"queue_order\":null,\"referred_by\":\"\",\"referral_hospital\":\"\",\"referral_notes\":\"\",\"referral_type\":\"\"}'),
(95, 1, 2, 'Appointments', 'update', 'appointment_online', 667, '2026-05-08 10:15:16', '::1', '{\"appoint_id\":\"667\",\"bill_id\":\"BID000555\",\"bill_date\":\"2026-05-08\",\"appoint_register_id\":\"A202605080001\",\"appoint_unicode\":\"PAT0116\",\"patient_name\":\"P NARAYANA RAO\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"31\",\"dob\":\"1994-05-09\",\"mobile_number\":\"9398380191\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-08\",\"doctor_name\":\"1\",\"start_time\":\"6:38\",\"end_time\":\"6:53\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-08 10:06:06\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-08\",\"valid_to\":\"2026-05-21\",\"appointment_status\":\"0\",\"patient_history\":\"\",\"queue_order\":null,\"referred_by\":\"\",\"referral_hospital\":\"\",\"referral_notes\":\"\",\"referral_type\":\"\"}', '{\"appoint_id\":\"667\",\"bill_id\":\"BID000555\",\"bill_date\":\"2026-05-08\",\"appoint_register_id\":\"A202605080001\",\"appoint_unicode\":\"PAT0116\",\"patient_name\":\"P NARAYANA RAO\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"31\",\"dob\":\"1994-05-09\",\"mobile_number\":\"9398380191\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-08\",\"doctor_name\":\"1\",\"start_time\":\"6:38\",\"end_time\":\"6:53\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-08 10:15:16\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-08\",\"valid_to\":\"2026-05-21\",\"appointment_status\":\"0\",\"patient_history\":\"\",\"queue_order\":null,\"referred_by\":\"Dr. Aswin\",\"referral_hospital\":\"medicover\",\"referral_notes\":\"\",\"referral_type\":\"External\"}'),
(96, 1, 2, 'Department', 'create', 'department', 13, '2026-05-20 14:46:18', '::1', NULL, '{\"dept_id\":\"13\",\"departmentName\":\"Dentist\",\"description\":\"teeth related department\",\"departmentStatus\":\"In\",\"type\":\"\",\"create_date_time\":\"2026-05-20 14:46:18\",\"created_by\":\"2\",\"modified_by\":\"2\",\"org_id\":\"1\",\"status\":\"1\"}'),
(97, 1, 2, 'Department', 'update', 'department', 13, '2026-05-20 14:46:32', '::1', '{\"dept_id\":\"13\",\"departmentName\":\"Dentist\",\"description\":\"teeth related department\",\"departmentStatus\":\"In\",\"type\":\"\",\"create_date_time\":\"2026-05-20 14:46:18\",\"created_by\":\"2\",\"modified_by\":\"2\",\"org_id\":\"1\",\"status\":\"1\"}', '{\"dept_id\":\"13\",\"departmentName\":\"Dentist\",\"description\":\"teeth related department.\",\"departmentStatus\":\"In\",\"type\":\"\",\"create_date_time\":\"2026-05-20 14:46:32\",\"created_by\":\"2\",\"modified_by\":\"2\",\"org_id\":\"1\",\"status\":\"1\"}'),
(98, 1, 2, 'Department', 'delete', 'department', 13, '2026-05-20 14:46:45', '::1', '{\"dept_id\":\"13\",\"departmentName\":\"Dentist\",\"description\":\"teeth related department.\",\"departmentStatus\":\"In\",\"type\":\"\",\"create_date_time\":\"2026-05-20 14:46:32\",\"created_by\":\"2\",\"modified_by\":\"2\",\"org_id\":\"1\",\"status\":\"1\"}', '{\"dept_id\":\"13\",\"departmentName\":\"Dentist\",\"description\":\"teeth related department.\",\"departmentStatus\":\"In\",\"type\":\"\",\"create_date_time\":\"2026-05-20 14:46:45\",\"created_by\":\"2\",\"modified_by\":\"2\",\"org_id\":\"1\",\"status\":\"0\"}'),
(99, 1, 2, 'Doctor Timeslot', 'create', 'doctors_time_slot', 52, '2026-05-20 14:47:20', '::1', NULL, '{\"doctors_time_id\":\"52\",\"doctorName_registrationNumber\":\"1\",\"available_date\":\"2026-05-20\",\"doctortime_type\":\"Daily\",\"selectedDays\":\"\",\"modify_by\":\"2\",\"created_by\":\"2\",\"org_id\":\"1\",\"c_d_t\":\"2026-05-20 14:47:20\",\"status\":\"1\",\"multi_id\":\"0\"}'),
(100, 1, 2, 'Appointments', 'create', 'appointment_online', 668, '2026-05-20 14:48:47', '::1', NULL, '{\"appoint_id\":\"668\",\"bill_id\":\"BID000556\",\"bill_date\":null,\"appoint_register_id\":\"A202605200001\",\"appoint_unicode\":\"PAT0535\",\"patient_name\":\"Tarun\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"25\",\"dob\":\"2001-05-20\",\"mobile_number\":\"9897567778\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-20\",\"doctor_name\":\"1\",\"start_time\":\"15:47\",\"end_time\":\"16:02\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-20 14:48:47\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-20\",\"valid_to\":\"2026-06-02\",\"appointment_status\":\"0\",\"patient_history\":\"\",\"queue_order\":null,\"referred_by\":\"\",\"referral_hospital\":\"\",\"referral_notes\":\"\",\"referral_type\":\"\"}'),
(101, 1, 2, 'Appointments', 'update', 'appointment_online', 668, '2026-05-20 14:50:07', '::1', '{\"appoint_id\":\"668\",\"bill_id\":\"BID000556\",\"bill_date\":null,\"appoint_register_id\":\"A202605200001\",\"appoint_unicode\":\"PAT0535\",\"patient_name\":\"Tarun\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"25\",\"dob\":\"2001-05-20\",\"mobile_number\":\"9897567778\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-20\",\"doctor_name\":\"1\",\"start_time\":\"15:47\",\"end_time\":\"16:02\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-20 14:48:47\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-20\",\"valid_to\":\"2026-06-02\",\"appointment_status\":\"0\",\"patient_history\":\"\",\"queue_order\":null,\"referred_by\":\"\",\"referral_hospital\":\"\",\"referral_notes\":\"\",\"referral_type\":\"\"}', '{\"appoint_id\":\"668\",\"bill_id\":\"BID000556\",\"bill_date\":null,\"appoint_register_id\":\"A202605200001\",\"appoint_unicode\":\"PAT0535\",\"patient_name\":\"Tarun\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"25\",\"dob\":\"2001-05-20\",\"mobile_number\":\"9897567778\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-20\",\"doctor_name\":\"1\",\"start_time\":\"15:47\",\"end_time\":\"16:02\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-20 14:50:07\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-20\",\"valid_to\":\"2026-06-02\",\"appointment_status\":\"0\",\"patient_history\":\"\",\"queue_order\":null,\"referred_by\":\"Dr.Venkatesh\",\"referral_hospital\":\"ABC hospital\",\"referral_notes\":\"family friend\",\"referral_type\":\"External\"}'),
(102, 1, 2, 'Prescriptions', 'create', 'prescripition', 9, '2026-05-20 14:52:33', '::1', NULL, '\"{\\\"prescription_id\\\":\\\"9\\\",\\\"patient_name\\\":\\\"Tarun\\\",\\\"appoint_register_id\\\":\\\"A202605200001\\\",\\\"patient_uid\\\":\\\"PAT0535\\\",\\\"age\\\":\\\"25\\\",\\\"gender\\\":\\\"Male\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[{\\\\\\\"test_id\\\\\\\":\\\\\\\"149\\\\\\\",\\\\\\\"test_name\\\\\\\":\\\\\\\"BLOOD TEST\\\\\\\",\\\\\\\"instruction\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concession\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionName\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionValue\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionType\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"doctor_price\\\\\\\":505,\\\\\\\"standard_price\\\\\\\":1010,\\\\\\\"test_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"test_group_id\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_name\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_price\\\\\\\":\\\\\\\"\\\\\\\"},{\\\\\\\"test_id\\\\\\\":\\\\\\\"11\\\\\\\",\\\\\\\"test_name\\\\\\\":\\\\\\\"GLUCOSE-RANDOM PLASMA\\\\\\\",\\\\\\\"instruction\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concession\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionName\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionValue\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionType\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"doctor_price\\\\\\\":63,\\\\\\\"standard_price\\\\\\\":80,\\\\\\\"test_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"test_group_id\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_name\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_price\\\\\\\":\\\\\\\"\\\\\\\"}]\\\",\\\"medicine_id\\\":\\\"[{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"90\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"DOLO 50 - (PARACETAMOL IP)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"12\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"500MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"2\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"0-2PM-0\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"0-1-0\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"After Food\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"0-2PM-0\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"0-1-0\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"},{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"12\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"7\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"75\\\\\\/20MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"9AM-0-9PM\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"1-0-1\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"After Food\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"9AM-0-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"1-0-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"}]\\\",\\\"prescriptiondate\\\":\\\"2026-05-20\\\",\\\"patient_vitals\\\":\\\"A202605200001\\\",\\\"finalDiagnosis\\\":\\\"Fever\\\",\\\"chiefcomplaint\\\":\\\"\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"\\\",\\\"advise\\\":\\\"\\\",\\\"personal_note\\\":\\\"\\\",\\\"reviewafter\\\":\\\"5 Days\\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"2026-05-25\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2026-05-20 14:52:33\\\",\\\"create_by\\\":\\\"2\\\",\\\"modify_by\\\":\\\"2\\\",\\\"org_id\\\":\\\"1\\\",\\\"create_date\\\":\\\"2026-05-20 14:52:33\\\"}\"'),
(103, 1, 2, 'TestBill', 'create', 'patienttestbilling', 22, '2026-05-20 14:56:40', '::1', NULL, '{\"test_details\":[{\"test_id\":\"149\",\"test_name\":\"BLOOD TEST\",\"instruction\":\"\",\"doctor_price\":505,\"standard_price\":1010}],\"total_amount\":1010,\"discount\":505,\"net_amount\":505,\"payment_method\":\"Cash\"}'),
(104, 1, 2, 'TestBill', 'create', 'patienttestbilling', 23, '2026-05-20 14:56:54', '::1', NULL, '{\"test_details\":[{\"test_id\":\"11\",\"test_name\":\"GLUCOSE-RANDOM PLASMA\",\"instruction\":\"\",\"doctor_price\":63,\"standard_price\":80}],\"total_amount\":80,\"discount\":17,\"net_amount\":63,\"payment_method\":\"Cash\"}'),
(105, 1, 2, 'MedicineBill', 'create', 'patient_medicine_billing', 6, '2026-05-20 14:59:25', '::1', NULL, '{\"patient_id\":\"PAT0535\",\"appointment_id\":\"A202605200001\",\"prescription_id\":9,\"medicine_details\":[{\"medicine_id\":90,\"medicine_name\":\"DOLO 50 - (PARACETAMOL IP)\",\"type_text\":\"Tab\",\"unit_text\":\"500MG\",\"dosage_text\":\"0-1-0\",\"when_text\":\"After Food\",\"time_text\":\"0-2PM-0\",\"duration_value\":5,\"duration\":\"0-2PM-0\",\"notes\":\"0-1-0\",\"price\":10,\"discount\":0,\"final_amount\":10,\"purchase_source\":\"Hospital Pharmacy\"},{\"medicine_id\":12,\"medicine_name\":\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\",\"type_text\":\"Tab\",\"unit_text\":\"75\\/20MG\",\"dosage_text\":\"1-0-1\",\"when_text\":\"After Food\",\"time_text\":\"9AM-0-9PM\",\"duration_value\":5,\"duration\":\"9AM-0-9PM\",\"notes\":\"1-0-1\",\"price\":30,\"discount\":0,\"final_amount\":30,\"purchase_source\":\"Hospital Pharmacy\"}],\"advice\":\"\",\"personal_note\":\"\",\"total_amount\":40,\"discount\":0,\"net_amount\":40,\"hospital_gross\":40,\"hospital_discount\":0,\"hospital_total\":40,\"outside_total\":0,\"purchase_source\":\"Hospital Pharmacy\",\"payment_method\":\"Cash\"}'),
(106, 1, 2, 'Appointments', 'update', 'appointment_online', 668, '2026-05-20 15:35:00', '::1', '{\"appoint_id\":\"668\",\"bill_id\":\"BID000556\",\"bill_date\":\"2026-05-20\",\"appoint_register_id\":\"A202605200001\",\"appoint_unicode\":\"PAT0535\",\"patient_name\":\"Tarun\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"25\",\"dob\":\"2001-05-20\",\"mobile_number\":\"9897567778\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-20\",\"doctor_name\":\"1\",\"start_time\":\"15:47\",\"end_time\":\"16:02\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"1\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-20 14:56:29\",\"amount_method\":\"Other\",\"cash_amount\":null,\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"67890\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"0\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-20\",\"valid_to\":\"2026-06-02\",\"appointment_status\":\"1\",\"patient_history\":\"\",\"queue_order\":null,\"referred_by\":\"Dr.Venkatesh\",\"referral_hospital\":\"ABC hospital\",\"referral_notes\":\"family friend\",\"referral_type\":\"External\"}', '{\"appoint_id\":\"668\",\"bill_id\":\"BID000556\",\"bill_date\":\"2026-05-20\",\"appoint_register_id\":\"A202605200001\",\"appoint_unicode\":\"PAT0535\",\"patient_name\":\"Tarun\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"25\",\"dob\":\"2001-05-20\",\"mobile_number\":\"9897567778\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-20\",\"doctor_name\":\"1\",\"start_time\":\"15:47\",\"end_time\":\"16:02\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"1\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-20 15:35:00\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-20\",\"valid_to\":\"2026-06-02\",\"appointment_status\":\"0\",\"patient_history\":\"\",\"queue_order\":null,\"referred_by\":\"Dr.Venkatesh\",\"referral_hospital\":\"ABC hospital\",\"referral_notes\":\"family friend\",\"referral_type\":\"External\"}');
INSERT INTO `audit_log` (`id`, `org_id`, `user_id`, `module`, `action`, `entity`, `entity_id`, `ts`, `ip`, `before_json`, `after_json`) VALUES
(107, 1, 2, 'Appointments', 'create', 'appointment_online', 669, '2026-05-20 15:41:42', '::1', NULL, '{\"appoint_id\":\"669\",\"bill_id\":\"BID000557\",\"bill_date\":null,\"appoint_register_id\":\"A202605200002\",\"appoint_unicode\":\"PAT0010\",\"patient_name\":\"G KALAYANI\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"55\",\"dob\":\"1970-04-29\",\"mobile_number\":\"7995908643\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-20\",\"doctor_name\":\"1\",\"start_time\":\"16:32\",\"end_time\":\"16:47\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-20 15:41:42\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-20\",\"valid_to\":\"2026-06-02\",\"appointment_status\":\"0\",\"patient_history\":\"\",\"queue_order\":null,\"referred_by\":\"\",\"referral_hospital\":\"\",\"referral_notes\":\"\",\"referral_type\":\"\"}'),
(108, 1, 2, 'Prescriptions', 'create', 'prescripition', 10, '2026-05-20 15:44:34', '::1', NULL, '\"{\\\"prescription_id\\\":\\\"10\\\",\\\"patient_name\\\":\\\"G KALAYANI\\\",\\\"appoint_register_id\\\":\\\"A202605200002\\\",\\\"patient_uid\\\":\\\"PAT0010\\\",\\\"age\\\":\\\"49\\\",\\\"gender\\\":\\\"Female\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[{\\\\\\\"test_id\\\\\\\":\\\\\\\"149\\\\\\\",\\\\\\\"test_name\\\\\\\":\\\\\\\"BLOOD TEST\\\\\\\",\\\\\\\"instruction\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concession\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionName\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionValue\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionType\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"doctor_price\\\\\\\":505,\\\\\\\"standard_price\\\\\\\":1010,\\\\\\\"test_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"test_group_id\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_name\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_price\\\\\\\":\\\\\\\"\\\\\\\"},{\\\\\\\"test_id\\\\\\\":\\\\\\\"11\\\\\\\",\\\\\\\"test_name\\\\\\\":\\\\\\\"GLUCOSE-RANDOM PLASMA\\\\\\\",\\\\\\\"instruction\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concession\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionName\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionValue\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concessionType\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"doctor_price\\\\\\\":63,\\\\\\\"standard_price\\\\\\\":80,\\\\\\\"test_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"test_group_id\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_name\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_price\\\\\\\":\\\\\\\"\\\\\\\"}]\\\",\\\"medicine_id\\\":\\\"[{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"90\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"DOLO 50 - (PARACETAMOL IP)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"12\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"500MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"2\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"0-2PM-0\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"0-1-0\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"After Food\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"0-2PM-0\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"0-1-0\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"},{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"12\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"7\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"75\\\\\\/20MG\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"9AM-0-9PM\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"1-0-1\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"After Food\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"9AM-0-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"1-0-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"}]\\\",\\\"prescriptiondate\\\":\\\"2026-05-20\\\",\\\"patient_vitals\\\":\\\"A202605200002\\\",\\\"finalDiagnosis\\\":\\\"Fever\\\",\\\"chiefcomplaint\\\":\\\"\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"\\\",\\\"advise\\\":\\\"\\\",\\\"personal_note\\\":\\\"\\\",\\\"reviewafter\\\":\\\"5 Days\\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"2026-05-25\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2026-05-20 15:44:34\\\",\\\"create_by\\\":\\\"2\\\",\\\"modify_by\\\":\\\"2\\\",\\\"org_id\\\":\\\"1\\\",\\\"create_date\\\":\\\"2026-05-20 15:44:34\\\"}\"'),
(109, 1, 2, 'TestBill', 'create', 'patienttestbilling', 28, '2026-05-20 15:45:23', '::1', NULL, '{\"test_details\":[{\"test_id\":\"149\",\"test_name\":\"BLOOD TEST\",\"instruction\":\"\",\"doctor_price\":505,\"standard_price\":1010},{\"test_id\":\"11\",\"test_name\":\"GLUCOSE-RANDOM PLASMA\",\"instruction\":\"\",\"doctor_price\":63,\"standard_price\":80}],\"total_amount\":1090,\"discount\":522,\"net_amount\":568,\"payment_method\":\"Both (Cash + UPI)\"}'),
(110, 1, 2, 'MedicineBill', 'create', 'patient_medicine_billing', 7, '2026-05-20 15:46:53', '::1', NULL, '{\"patient_id\":\"PAT0010\",\"appointment_id\":\"A202605200002\",\"prescription_id\":10,\"medicine_details\":[{\"medicine_id\":90,\"medicine_name\":\"DOLO 50 - (PARACETAMOL IP)\",\"type_text\":\"Tab\",\"unit_text\":\"500MG\",\"dosage_text\":\"0-1-0\",\"when_text\":\"After Food\",\"time_text\":\"0-2PM-0\",\"duration_value\":5,\"duration\":\"0-2PM-0\",\"notes\":\"0-1-0\",\"price\":30,\"discount\":0,\"final_amount\":30,\"purchase_source\":\"Hospital Pharmacy\"},{\"medicine_id\":12,\"medicine_name\":\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\",\"type_text\":\"Tab\",\"unit_text\":\"75\\/20MG\",\"dosage_text\":\"1-0-1\",\"when_text\":\"After Food\",\"time_text\":\"9AM-0-9PM\",\"duration_value\":5,\"duration\":\"9AM-0-9PM\",\"notes\":\"1-0-1\",\"price\":50,\"discount\":0,\"final_amount\":50,\"purchase_source\":\"Hospital Pharmacy\"}],\"advice\":\"\",\"personal_note\":\"\",\"total_amount\":80,\"discount\":0,\"net_amount\":80,\"hospital_gross\":80,\"hospital_discount\":0,\"hospital_total\":80,\"outside_total\":0,\"purchase_source\":\"Hospital Pharmacy\",\"payment_method\":\"Both (Cash + UPI)\"}'),
(111, 1, 2, 'Appointments', 'create', 'appointment_online', 670, '2026-05-20 16:54:15', '::1', NULL, '{\"appoint_id\":\"670\",\"bill_id\":\"BID000558\",\"bill_date\":null,\"appoint_register_id\":\"A202605200003\",\"appoint_unicode\":\"PAT0011\",\"patient_name\":\"G LAXMI\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"49\",\"dob\":\"1976-05-08\",\"mobile_number\":\"8699494819\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-20\",\"doctor_name\":\"1\",\"start_time\":\"17:17\",\"end_time\":\"17:32\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-20 16:54:15\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-20\",\"valid_to\":\"2026-05-20\",\"appointment_status\":\"0\",\"patient_history\":\"\",\"queue_order\":null,\"referred_by\":\"\",\"referral_hospital\":\"\",\"referral_notes\":\"\",\"referral_type\":\"\"}'),
(112, 1, 2, 'Appointments', 'create', 'appointment_online', 671, '2026-05-20 17:03:07', '::1', NULL, '{\"appoint_id\":\"671\",\"bill_id\":\"BID000559\",\"bill_date\":null,\"appoint_register_id\":\"A202605200004\",\"appoint_unicode\":\"PAT0006\",\"patient_name\":\"V YERRAMMA\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"51\",\"dob\":null,\"mobile_number\":\"9966188420\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-20\",\"doctor_name\":\"1\",\"start_time\":\"18:32\",\"end_time\":\"18:47\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"1\",\"created_by\":\"2\",\"modified_by\":\"2\",\"create_date_time\":\"2026-05-20 17:03:07\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"500\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"500\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-20\",\"valid_to\":\"2026-06-02\",\"appointment_status\":\"0\",\"patient_history\":\"\",\"queue_order\":null,\"referred_by\":\"\",\"referral_hospital\":\"\",\"referral_notes\":\"\",\"referral_type\":\"\"}'),
(113, 0, 1, 'Security', 'create', 'security', 15, '2026-05-21 10:49:05', '::1', NULL, '{\"security_id\":\"15\",\"admin_name\":\"Dr. Ravi Teja\",\"user_code\":null,\"email\":\"raviteja@gmail.com\",\"contact\":\"7032760279\",\"security_password\":\"827ccb0eea8a706c4c34a16891f84e7b\",\"image_url\":\"\",\"signature_url\":\"\",\"role_id\":\"2\",\"can_switch_doctor\":\"0\",\"security_type\":\"A\",\"org_id\":\"9\",\"created_by\":\"1\",\"modified_by\":\"1\",\"status\":\"1\",\"create_date_time\":\"2026-05-21 10:49:05\"}'),
(114, 9, 15, 'Roles', 'create', 'roles', 13, '2026-05-21 10:52:38', '::1', NULL, '{\"role_id\":\"13\",\"role_name\":\"Doctor\",\"created_by\":\"15\",\"created_date_time\":\"2026-05-21 10:52:38\",\"status\":\"1\",\"modified_by\":\"15\",\"modified_date_time\":\"2026-05-21 10:52:38\",\"org_id\":\"9\"}'),
(115, 9, 15, 'Roles', 'create', 'roles', 14, '2026-05-21 11:01:36', '::1', NULL, '{\"role_id\":\"14\",\"role_name\":\"Receptionist\",\"created_by\":\"15\",\"created_date_time\":\"2026-05-21 11:01:36\",\"status\":\"1\",\"modified_by\":\"15\",\"modified_date_time\":\"2026-05-21 11:01:36\",\"org_id\":\"9\"}'),
(116, 9, 15, 'Roles', 'create', 'roles', 15, '2026-05-21 11:03:13', '::1', NULL, '{\"role_id\":\"15\",\"role_name\":\"Pharmacist\",\"created_by\":\"15\",\"created_date_time\":\"2026-05-21 11:03:13\",\"status\":\"1\",\"modified_by\":\"15\",\"modified_date_time\":\"2026-05-21 11:03:13\",\"org_id\":\"9\"}'),
(117, 9, 15, 'Roles', 'create', 'roles', 16, '2026-05-21 11:04:23', '::1', NULL, '{\"role_id\":\"16\",\"role_name\":\"Admin\",\"created_by\":\"15\",\"created_date_time\":\"2026-05-21 11:04:23\",\"status\":\"1\",\"modified_by\":\"15\",\"modified_date_time\":\"2026-05-21 11:04:23\",\"org_id\":\"9\"}'),
(118, 9, 15, 'Security', 'create', 'security', 16, '2026-05-21 11:06:13', '::1', NULL, '{\"security_id\":\"16\",\"admin_name\":\"Dr. Kishore\",\"user_code\":null,\"email\":\"kishore@gmail.com\",\"contact\":\"7095678670\",\"security_password\":\"827ccb0eea8a706c4c34a16891f84e7b\",\"image_url\":\"\",\"signature_url\":\"\",\"role_id\":\"13\",\"can_switch_doctor\":\"0\",\"security_type\":\"U\",\"org_id\":\"9\",\"created_by\":\"15\",\"modified_by\":\"15\",\"status\":\"1\",\"create_date_time\":\"2026-05-21 11:06:13\"}'),
(119, 9, 15, 'Security', 'create', 'security', 17, '2026-05-21 11:07:56', '::1', NULL, '{\"security_id\":\"17\",\"admin_name\":\"Dr. Durga Lakshmi\",\"user_code\":null,\"email\":\"durga123@gmail.com\",\"contact\":\"7032760277\",\"security_password\":\"827ccb0eea8a706c4c34a16891f84e7b\",\"image_url\":\"\",\"signature_url\":\"\",\"role_id\":\"14\",\"can_switch_doctor\":\"0\",\"security_type\":\"U\",\"org_id\":\"9\",\"created_by\":\"15\",\"modified_by\":\"15\",\"status\":\"1\",\"create_date_time\":\"2026-05-21 11:07:56\"}'),
(120, 9, 15, 'Security', 'update', 'security', 17, '2026-05-21 11:08:17', '::1', '{\"security_id\":\"17\",\"admin_name\":\"Dr. Durga Lakshmi\",\"user_code\":null,\"email\":\"durga123@gmail.com\",\"contact\":\"7032760277\",\"security_password\":\"827ccb0eea8a706c4c34a16891f84e7b\",\"image_url\":\"\",\"signature_url\":\"\",\"role_id\":\"14\",\"can_switch_doctor\":\"0\",\"security_type\":\"U\",\"org_id\":\"9\",\"created_by\":\"15\",\"modified_by\":\"15\",\"status\":\"1\",\"create_date_time\":\"2026-05-21 11:07:56\"}', '{\"security_id\":\"17\",\"admin_name\":\"Durga Lakshmi\",\"user_code\":null,\"email\":\"durga123@gmail.com\",\"contact\":\"7032760277\",\"security_password\":\"827ccb0eea8a706c4c34a16891f84e7b\",\"image_url\":\"\",\"signature_url\":\"\",\"role_id\":\"14\",\"can_switch_doctor\":\"0\",\"security_type\":\"U\",\"org_id\":\"9\",\"created_by\":\"15\",\"modified_by\":\"15\",\"status\":\"1\",\"create_date_time\":\"2026-05-21 11:08:17\"}'),
(121, 9, 15, 'Security', 'create', 'security', 18, '2026-05-21 11:17:53', '::1', NULL, '{\"security_id\":\"18\",\"admin_name\":\"Likhith\",\"user_code\":null,\"email\":\"likhith@gmail.com\",\"contact\":\"9000786945\",\"security_password\":\"827ccb0eea8a706c4c34a16891f84e7b\",\"image_url\":\"\",\"signature_url\":\"\",\"role_id\":\"15\",\"can_switch_doctor\":\"0\",\"security_type\":\"U\",\"org_id\":\"9\",\"created_by\":\"15\",\"modified_by\":\"15\",\"status\":\"1\",\"create_date_time\":\"2026-05-21 11:17:53\"}'),
(122, 9, 15, 'Department', 'create', 'department', 14, '2026-05-21 11:18:54', '::1', NULL, '{\"dept_id\":\"14\",\"departmentName\":\"Cardiologist\",\"description\":\"heart related issues\",\"departmentStatus\":\"In\",\"type\":\"\",\"create_date_time\":\"2026-05-21 11:18:54\",\"created_by\":\"15\",\"modified_by\":\"15\",\"org_id\":\"9\",\"status\":\"1\"}'),
(123, 9, 15, 'Department', 'create', 'department', 15, '2026-05-21 11:23:24', '::1', NULL, '{\"dept_id\":\"15\",\"departmentName\":\"Gynecologist\",\"description\":\"gynec related issues\",\"departmentStatus\":\"In\",\"type\":\"\",\"create_date_time\":\"2026-05-21 11:23:24\",\"created_by\":\"15\",\"modified_by\":\"15\",\"org_id\":\"9\",\"status\":\"1\"}'),
(124, 9, 15, 'Specialization', 'create', 'specialtis', 20, '2026-05-21 11:24:04', '::1', NULL, '{\"specialtis_id\":\"20\",\"specialtisname\":\"Heart disceases\",\"status\":\"1\",\"created_by\":\"15\",\"modified_by\":\"15\",\"org_id\":\"9\",\"create_date_time\":\"2026-05-21 11:24:04\"}'),
(125, 9, 15, 'Specialization', 'create', 'specialtis', 21, '2026-05-21 11:24:50', '::1', NULL, '{\"specialtis_id\":\"21\",\"specialtisname\":\"gynec disceases\",\"status\":\"1\",\"created_by\":\"15\",\"modified_by\":\"15\",\"org_id\":\"9\",\"create_date_time\":\"2026-05-21 11:24:50\"}'),
(126, 9, 15, 'Concessions', 'create', 'concessions', 5, '2026-05-21 11:25:17', '::1', NULL, '{\"concession_id\":\"5\",\"concession_name\":\"Family\",\"concession_type\":\"percentage\",\"concession_value\":\"50\",\"org_id\":\"9\",\"status\":\"1\",\"created_by\":\"15\",\"modified_by\":\"15\",\"created_date_time\":\"2026-05-21 11:25:17\",\"updated_date_time\":\"2026-05-21 11:25:17\"}'),
(127, 9, 15, 'Concessions', 'create', 'concessions', 6, '2026-05-21 11:25:40', '::1', NULL, '{\"concession_id\":\"6\",\"concession_name\":\"friend\",\"concession_type\":\"percentage\",\"concession_value\":\"20\",\"org_id\":\"9\",\"status\":\"1\",\"created_by\":\"15\",\"modified_by\":\"15\",\"created_date_time\":\"2026-05-21 11:25:40\",\"updated_date_time\":\"2026-05-21 11:25:40\"}'),
(128, 9, 15, 'Concessions', 'create', 'concessions', 7, '2026-05-21 11:26:27', '::1', NULL, '{\"concession_id\":\"7\",\"concession_name\":\"Own family\",\"concession_type\":\"percentage\",\"concession_value\":\"100\",\"org_id\":\"9\",\"status\":\"1\",\"created_by\":\"15\",\"modified_by\":\"15\",\"created_date_time\":\"2026-05-21 11:26:27\",\"updated_date_time\":\"2026-05-21 11:26:27\"}'),
(129, 9, 15, 'Services', 'create', 'services', 2, '2026-05-21 11:27:36', '::1', NULL, '{\"service_id\":\"2\",\"service_name\":\"Consultation\",\"price\":\"600\",\"service_GST\":\"0\",\"org_id\":\"9\",\"status\":\"1\",\"created_by\":\"15\",\"modified_by\":\"15\",\"c_d_t\":\"2026-05-21 11:27:36\"}'),
(130, 9, 15, 'Tests', 'create', 'tests', 165, '2026-05-21 11:35:29', '::1', NULL, '{\"test_id\":\"165\",\"test_name\":\"test\",\"test_price\":\"51\",\"test_gst\":\"02\",\"normal_range\":\"23-50\",\"status\":\"1\",\"created_by\":\"15\",\"modified_by\":\"15\",\"create_date_time\":\"2026-05-21 11:35:29\",\"org_id\":\"9\"}'),
(131, 9, 15, 'Tests', 'update', 'tests', 165, '2026-05-21 11:35:43', '::1', '{\"test_id\":\"165\",\"test_name\":\"test\",\"test_price\":\"51\",\"test_gst\":\"02\",\"normal_range\":\"23-50\",\"status\":\"1\",\"created_by\":\"15\",\"modified_by\":\"15\",\"create_date_time\":\"2026-05-21 11:35:29\",\"org_id\":\"9\"}', '{\"test_id\":\"165\",\"test_name\":\"test\",\"test_price\":\"85\",\"test_gst\":\"70\",\"normal_range\":\"23-50\",\"status\":\"1\",\"created_by\":\"15\",\"modified_by\":\"15\",\"create_date_time\":\"2026-05-21 11:35:29\",\"org_id\":\"9\"}'),
(132, 9, 15, 'Tests', 'delete', 'tests', 165, '2026-05-21 11:35:48', '::1', '{\"test_id\":\"165\",\"test_name\":\"test\",\"test_price\":\"85\",\"test_gst\":\"70\",\"normal_range\":\"23-50\",\"status\":\"1\",\"created_by\":\"15\",\"modified_by\":\"15\",\"create_date_time\":\"2026-05-21 11:35:29\",\"org_id\":\"9\"}', '{\"test_id\":\"165\",\"test_name\":\"test\",\"test_price\":\"85\",\"test_gst\":\"70\",\"normal_range\":\"23-50\",\"status\":\"0\",\"created_by\":\"15\",\"modified_by\":\"15\",\"create_date_time\":\"2026-05-21 11:35:29\",\"org_id\":\"9\"}'),
(133, 9, 15, 'Medicines', 'create', 'medicines', 101, '2026-05-21 11:36:19', '::1', NULL, '{\"medicine_id\":\"101\",\"org_id\":\"9\",\"medicine_type\":\"1\",\"medicine_name\":\"Dolo 650\",\"scientific_name\":\"Paracetamol IP\",\"dosage\":\"500MG\",\"gst\":\"\",\"price\":\"59\",\"notes\":\"\",\"status\":\"1\",\"created_by\":\"15\",\"modifeid_by\":\"15\",\"c_d_t\":\"2026-05-21 11:36:19\"}'),
(134, 9, 15, 'Medicines', 'update', 'medicines', 101, '2026-05-21 11:36:47', '::1', NULL, '{\"medicine_id\":\"101\",\"org_id\":\"9\",\"medicine_type\":\"1\",\"medicine_name\":\"Dolo 650\",\"scientific_name\":\"Paracetamol IP\",\"dosage\":\"500MG\",\"gst\":\"\",\"price\":\"59\",\"notes\":\"\",\"status\":\"1\",\"created_by\":\"15\",\"modifeid_by\":\"15\",\"c_d_t\":\"2026-05-21 11:36:19\"}'),
(135, 9, 15, 'Medicines', 'update', 'medicines', 101, '2026-05-21 11:50:45', '::1', NULL, '{\"medicine_id\":\"101\",\"org_id\":\"9\",\"medicine_type\":\"1\",\"medicine_name\":\"Dolo 650\",\"scientific_name\":\"Paracetamol IP\",\"dosage\":\"500MG\",\"gst\":\"\",\"price\":\"60\",\"notes\":\"\",\"status\":\"1\",\"created_by\":\"15\",\"modifeid_by\":\"15\",\"c_d_t\":\"2026-05-21 11:50:45\"}'),
(136, 9, 15, 'Doctors', 'create', 'doctor', 7, '2026-05-21 11:59:52', '::1', NULL, '{\"doc_id\":\"7\",\"doc_registration_number\":\"D202605210001\",\"doctor_name\":\"Dr. Ravi Teja\",\"doctor_type\":\"In\",\"gender\":\"Male\",\"phone_number\":\"7032760279\",\"email\":\"raviteja@gmail.com\",\"doctor_specialization\":\"20\",\"departments\":\"14\",\"doctor_services\":\"2\",\"doctor_fee\":\"600\",\"doctor_charge\":\"0\",\"doctor_visit_charge\":\"0\",\"time_slot_duration\":\"15\",\"details\":\"\",\"doc_img\":\"doc_1779344992.jpg\",\"org_id\":\"9\",\"security_id\":\"15\",\"status\":\"1\",\"created_by\":\"15\",\"modified_by\":\"15\",\"c_d_t\":\"2026-05-21 11:59:52\"}'),
(137, 9, 15, 'Receptionnist', 'create', 'receptionnist', 7, '2026-05-21 11:59:52', '::1', NULL, '[{\"rep_id\":\"6\",\"doc_id\":\"7\",\"security_id\":\"17\",\"user_name\":\"Durga Lakshmi\",\"org_id\":\"9\",\"status\":\"1\",\"created_by\":\"15\",\"created_at\":\"2026-05-21 11:59:52\",\"modified_by\":\"15\",\"modified_at\":\"2026-05-21 11:59:52\"}]'),
(138, 9, 15, 'Doctors', 'create', 'doctor', 8, '2026-05-21 12:00:42', '::1', NULL, '{\"doc_id\":\"8\",\"doc_registration_number\":\"D202605210002\",\"doctor_name\":\"Dr. Kishore\",\"doctor_type\":\"In\",\"gender\":\"Male\",\"phone_number\":\"7095678670\",\"email\":\"kishore@gmail.com\",\"doctor_specialization\":\"21\",\"departments\":\"15\",\"doctor_services\":\"2\",\"doctor_fee\":\"700\",\"doctor_charge\":\"0\",\"doctor_visit_charge\":\"0\",\"time_slot_duration\":\"20\",\"details\":\"\",\"doc_img\":\"doc_1779345041.jpeg\",\"org_id\":\"9\",\"security_id\":\"16\",\"status\":\"1\",\"created_by\":\"15\",\"modified_by\":\"15\",\"c_d_t\":\"2026-05-21 12:00:42\"}'),
(139, 9, 15, 'Receptionnist', 'create', 'receptionnist', 8, '2026-05-21 12:00:42', '::1', NULL, '[{\"rep_id\":\"7\",\"doc_id\":\"8\",\"security_id\":\"17\",\"user_name\":\"Durga Lakshmi\",\"org_id\":\"9\",\"status\":\"1\",\"created_by\":\"15\",\"created_at\":\"2026-05-21 12:00:42\",\"modified_by\":\"15\",\"modified_at\":\"2026-05-21 12:00:42\"}]'),
(140, 9, 15, 'Doctor Timeslot', 'create', 'doctors_time_slot', 53, '2026-05-21 12:01:56', '::1', NULL, '{\"doctors_time_id\":\"53\",\"doctorName_registrationNumber\":\"7\",\"available_date\":\"2026-05-21\",\"doctortime_type\":\"Daily\",\"selectedDays\":\"\",\"modify_by\":\"15\",\"created_by\":\"15\",\"org_id\":\"9\",\"c_d_t\":\"2026-05-21 12:01:56\",\"status\":\"1\",\"multi_id\":\"0\"}'),
(141, 9, 15, 'Appointments', 'create', 'appointment_online', 672, '2026-05-21 12:02:45', '::1', NULL, '{\"appoint_id\":\"672\",\"bill_id\":\"BID000001\",\"bill_date\":null,\"appoint_register_id\":\"A202605210001\",\"appoint_unicode\":\"PAT0001\",\"patient_name\":\"Pavan Kumar\",\"gender\":\"Male\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"34\",\"dob\":\"1992-05-21\",\"mobile_number\":\"4455667788\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-21\",\"doctor_name\":\"7\",\"start_time\":\"13:46\",\"end_time\":\"14:01\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"9\",\"created_by\":\"15\",\"modified_by\":\"15\",\"create_date_time\":\"2026-05-21 12:02:45\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"600\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"600\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-21\",\"valid_to\":\"2026-06-03\",\"appointment_status\":\"0\",\"patient_history\":\"\",\"queue_order\":null,\"referred_by\":\"Dr. Aswin\",\"referral_hospital\":\"medicover\",\"referral_notes\":\"\",\"referral_type\":\"External\"}'),
(142, 9, 15, 'Prescriptions', 'create', 'prescripition', 11, '2026-05-21 14:46:03', '::1', NULL, '\"{\\\"prescription_id\\\":\\\"11\\\",\\\"patient_name\\\":\\\"Pavan Kumar\\\",\\\"appoint_register_id\\\":\\\"A202605210001\\\",\\\"patient_uid\\\":\\\"PAT0001\\\",\\\"age\\\":\\\"34\\\",\\\"gender\\\":\\\"Male\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[]\\\",\\\"medicine_id\\\":\\\"[{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"96\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"FOLIC ACID 5MG - (PTEROYLMONOGLUTAMIC ACID)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"2\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"5mg\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"3\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"3\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"0-0-9PM\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"0-0-1\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"After Food\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"0-0-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"0-0-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"},{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"97\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"PROGESTERONE 200MG - (MICRONIZED PROGESTERONE)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":31,\\\\\\\"unit_text\\\\\\\":\\\\\\\"200mg\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"2\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"9AM-2PM-0\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"1-1-0\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"After Food\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"9AM-2PM-0\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"1-1-0\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"}]\\\",\\\"prescriptiondate\\\":\\\"2026-05-21\\\",\\\"patient_vitals\\\":\\\"A202605210001\\\",\\\"finalDiagnosis\\\":\\\"Heart Attack\\\",\\\"chiefcomplaint\\\":\\\"\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"\\\",\\\"advise\\\":\\\"\\\",\\\"personal_note\\\":\\\"\\\",\\\"reviewafter\\\":\\\"5 Days\\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"2026-05-26\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2026-05-21 14:46:03\\\",\\\"create_by\\\":\\\"15\\\",\\\"modify_by\\\":\\\"15\\\",\\\"org_id\\\":\\\"9\\\",\\\"create_date\\\":\\\"2026-05-21 14:46:03\\\"}\"'),
(143, 9, 15, 'Tests', 'create', 'tests', 166, '2026-05-21 17:03:43', '::1', NULL, '\"{\\\"test_name\\\":\\\"HISTOPATHOLOGY BIOPSY MEDIUM SPECIMEN\\\",\\\"test_price\\\":\\\"0\\\"}\"'),
(144, 9, 15, 'Prescriptions', 'update', 'prescripition', 11, '2026-05-21 17:03:43', '::1', '\"{\\\"prescription_id\\\":\\\"11\\\",\\\"patient_name\\\":\\\"Pavan Kumar\\\",\\\"appoint_register_id\\\":\\\"A202605210001\\\",\\\"patient_uid\\\":\\\"PAT0001\\\",\\\"age\\\":\\\"34\\\",\\\"gender\\\":\\\"Male\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[]\\\",\\\"medicine_id\\\":\\\"[{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"96\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"FOLIC ACID 5MG - (PTEROYLMONOGLUTAMIC ACID)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"2\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"5mg\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"3\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"3\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"0-0-9PM\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"0-0-1\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"After Food\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"0-0-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"0-0-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"},{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"97\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"PROGESTERONE 200MG - (MICRONIZED PROGESTERONE)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":31,\\\\\\\"unit_text\\\\\\\":\\\\\\\"200mg\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"2\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"9AM-2PM-0\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"1-1-0\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"After Food\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"9AM-2PM-0\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"1-1-0\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"}]\\\",\\\"prescriptiondate\\\":\\\"2026-05-21\\\",\\\"patient_vitals\\\":\\\"A202605210001\\\",\\\"finalDiagnosis\\\":\\\"Heart Attack\\\",\\\"chiefcomplaint\\\":\\\"\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"\\\",\\\"advise\\\":\\\"\\\",\\\"personal_note\\\":\\\"\\\",\\\"reviewafter\\\":\\\"5 Days\\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"2026-05-26\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2026-05-21 14:46:03\\\",\\\"create_by\\\":\\\"15\\\",\\\"modify_by\\\":\\\"15\\\",\\\"org_id\\\":\\\"9\\\",\\\"create_date\\\":\\\"2026-05-21 14:46:03\\\"}\"', '\"{\\\"prescription_id\\\":\\\"11\\\",\\\"patient_name\\\":\\\"Pavan Kumar\\\",\\\"appoint_register_id\\\":\\\"A202605210001\\\",\\\"patient_uid\\\":\\\"PAT0001\\\",\\\"age\\\":\\\"34\\\",\\\"gender\\\":\\\"Male\\\",\\\"rx_id\\\":\\\"0\\\",\\\"test_group_id\\\":\\\"0\\\",\\\"test_id\\\":\\\"[{\\\\\\\"test_id\\\\\\\":\\\\\\\"156\\\\\\\",\\\\\\\"test_name\\\\\\\":\\\\\\\"ECHOCARDIOGRAM\\\\\\\",\\\\\\\"instruction\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concession\\\\\\\":\\\\\\\"Family (50%)\\\\\\\",\\\\\\\"concessionName\\\\\\\":\\\\\\\"Family\\\\\\\",\\\\\\\"concessionValue\\\\\\\":50,\\\\\\\"concessionType\\\\\\\":\\\\\\\"percentage\\\\\\\",\\\\\\\"doctor_price\\\\\\\":750,\\\\\\\"standard_price\\\\\\\":1500,\\\\\\\"test_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"test_group_id\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_name\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_price\\\\\\\":\\\\\\\"\\\\\\\"},{\\\\\\\"test_id\\\\\\\":166,\\\\\\\"test_name\\\\\\\":\\\\\\\"HISTOPATHOLOGY BIOPSY MEDIUM SPECIMEN\\\\\\\",\\\\\\\"instruction\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"concession\\\\\\\":\\\\\\\"Own family (100%)\\\\\\\",\\\\\\\"concessionName\\\\\\\":\\\\\\\"Own family\\\\\\\",\\\\\\\"concessionValue\\\\\\\":100,\\\\\\\"concessionType\\\\\\\":\\\\\\\"percentage\\\\\\\",\\\\\\\"doctor_price\\\\\\\":0,\\\\\\\"standard_price\\\\\\\":0,\\\\\\\"test_status\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"test_group_id\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_name\\\\\\\":\\\\\\\"\\\\\\\",\\\\\\\"test_group_price\\\\\\\":\\\\\\\"\\\\\\\"}]\\\",\\\"medicine_id\\\":\\\"[{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"96\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"FOLIC ACID 5MG - (PTEROYLMONOGLUTAMIC ACID)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"2\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"5mg\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"3\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"3\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"5\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"0-0-9PM\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"0-0-1\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"After Food\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"0-0-9PM\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"0-0-1\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"},{\\\\\\\"medicine_id\\\\\\\":\\\\\\\"97\\\\\\\",\\\\\\\"medicine_name\\\\\\\":\\\\\\\"PROGESTERONE 200MG - (MICRONIZED PROGESTERONE)\\\\\\\",\\\\\\\"type_id\\\\\\\":\\\\\\\"1\\\\\\\",\\\\\\\"type_text\\\\\\\":\\\\\\\"Tab\\\\\\\",\\\\\\\"unit_id\\\\\\\":\\\\\\\"31\\\\\\\",\\\\\\\"unit_text\\\\\\\":\\\\\\\"200mg\\\\\\\",\\\\\\\"dosage_id\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"when_id\\\\\\\":\\\\\\\"8\\\\\\\",\\\\\\\"time_id\\\\\\\":\\\\\\\"2\\\\\\\",\\\\\\\"duration_value\\\\\\\":\\\\\\\"4\\\\\\\",\\\\\\\"duration\\\\\\\":\\\\\\\"9AM-2PM-0\\\\\\\",\\\\\\\"notes\\\\\\\":\\\\\\\"1-1-0\\\\\\\",\\\\\\\"med_status\\\\\\\":\\\\\\\"After Food\\\\\\\",\\\\\\\"timeText\\\\\\\":\\\\\\\"9AM-2PM-0\\\\\\\",\\\\\\\"dosageText\\\\\\\":\\\\\\\"1-1-0\\\\\\\",\\\\\\\"whenText\\\\\\\":\\\\\\\"After Food\\\\\\\"}]\\\",\\\"prescriptiondate\\\":\\\"2026-05-21\\\",\\\"patient_vitals\\\":\\\"A202605210001\\\",\\\"finalDiagnosis\\\":\\\"Heart Attack\\\",\\\"chiefcomplaint\\\":\\\"\\\",\\\"pasthistory\\\":\\\"\\\",\\\"patient_data\\\":\\\"\\\",\\\"advise\\\":\\\"\\\",\\\"personal_note\\\":\\\"\\\",\\\"reviewafter\\\":\\\"5 Days\\\",\\\"images\\\":\\\"\\\",\\\"reviewafterdate\\\":\\\"2026-05-26\\\",\\\"status\\\":\\\"1\\\",\\\"prescription_status\\\":\\\"N\\\",\\\"create_date_time\\\":\\\"2026-05-21 14:46:03\\\",\\\"create_by\\\":\\\"15\\\",\\\"modify_by\\\":\\\"15\\\",\\\"org_id\\\":\\\"9\\\",\\\"create_date\\\":\\\"2026-05-21 14:46:03\\\"}\"'),
(145, 9, 15, 'TestBill', 'create', 'patienttestbilling', 35, '2026-05-21 17:04:30', '::1', NULL, '{\"test_details\":[{\"test_id\":\"156\",\"test_name\":\"ECHOCARDIOGRAM\",\"instruction\":\"\",\"doctor_price\":750,\"standard_price\":1500},{\"test_id\":\"166\",\"test_name\":\"HISTOPATHOLOGY BIOPSY MEDIUM SPECIMEN\",\"instruction\":\"\",\"doctor_price\":0,\"standard_price\":0}],\"total_amount\":1500,\"discount\":750,\"net_amount\":750,\"payment_method\":\"Both (Cash + UPI)\"}'),
(146, 9, 15, 'TestBill', 'create', 'patienttestbilling', 37, '2026-05-21 17:27:19', '::1', NULL, '{\"test_details\":[{\"test_id\":\"156\",\"test_name\":\"ECHOCARDIOGRAM\",\"instruction\":\"\",\"doctor_price\":750,\"standard_price\":1500},{\"test_id\":\"166\",\"test_name\":\"HISTOPATHOLOGY BIOPSY MEDIUM SPECIMEN\",\"instruction\":\"\",\"doctor_price\":0,\"standard_price\":0}],\"total_amount\":1500,\"discount\":750,\"net_amount\":750,\"payment_method\":\"Both (Cash + UPI)\"}'),
(147, 9, 15, 'MedicineBill', 'create', 'patient_medicine_billing', 8, '2026-05-21 17:55:24', '::1', NULL, '{\"patient_id\":\"PAT0001\",\"appointment_id\":\"A202605210001\",\"prescription_id\":11,\"medicine_details\":[{\"medicine_id\":96,\"medicine_name\":\"FOLIC ACID 5MG - (PTEROYLMONOGLUTAMIC ACID)\",\"type_text\":\"Tab\",\"unit_text\":\"5mg\",\"dosage_text\":\"0-0-1\",\"when_text\":\"After Food\",\"time_text\":\"0-0-9PM\",\"duration_value\":5,\"duration\":\"0-0-9PM\",\"notes\":\"0-0-1\",\"price\":50,\"discount\":0,\"final_amount\":50,\"purchase_source\":\"Hospital Pharmacy\"},{\"medicine_id\":97,\"medicine_name\":\"PROGESTERONE 200MG - (MICRONIZED PROGESTERONE)\",\"type_text\":\"Tab\",\"unit_text\":\"200mg\",\"dosage_text\":\"1-1-0\",\"when_text\":\"After Food\",\"time_text\":\"9AM-2PM-0\",\"duration_value\":4,\"duration\":\"9AM-2PM-0\",\"notes\":\"1-1-0\",\"price\":310,\"discount\":0,\"final_amount\":310,\"purchase_source\":\"Hospital Pharmacy\"}],\"advice\":\"\",\"personal_note\":\"\",\"total_amount\":360,\"discount\":0,\"net_amount\":360,\"hospital_gross\":360,\"hospital_discount\":0,\"hospital_total\":360,\"outside_total\":0,\"purchase_source\":\"Hospital Pharmacy\",\"payment_method\":\"UPI\"}'),
(148, 9, 15, 'Doctor Timeslot', 'create', 'doctors_time_slot', 54, '2026-05-21 18:01:31', '::1', NULL, '{\"doctors_time_id\":\"54\",\"doctorName_registrationNumber\":\"8\",\"available_date\":\"2026-05-21\",\"doctortime_type\":\"Daily\",\"selectedDays\":\"\",\"modify_by\":\"15\",\"created_by\":\"15\",\"org_id\":\"9\",\"c_d_t\":\"2026-05-21 18:01:31\",\"status\":\"1\",\"multi_id\":\"0\"}'),
(149, 9, 15, 'Appointments', 'create', 'appointment_online', 673, '2026-05-21 18:04:11', '::1', NULL, '{\"appoint_id\":\"673\",\"bill_id\":\"BID000002\",\"bill_date\":null,\"appoint_register_id\":\"A202605210002\",\"appoint_unicode\":\"PAT0002\",\"patient_name\":\"keerthi\",\"gender\":\"Female\",\"systolic\":\"\",\"diastolic\":\"\",\"temperature\":\"\",\"glucose_level\":\"\",\"age\":\"28\",\"dob\":\"1997-05-22\",\"mobile_number\":\"7787868688\",\"patient_email\":\"\",\"appoint_date\":\"2026-05-21\",\"doctor_name\":\"8\",\"start_time\":\"19:21\",\"end_time\":\"19:41\",\"check_in\":null,\"check_out\":null,\"invoice_payment\":\"0\",\"doctor_fee\":\"0\",\"appoint_status\":\"1\",\"visitor_status\":\"1\",\"org_id\":\"9\",\"created_by\":\"15\",\"modified_by\":\"15\",\"create_date_time\":\"2026-05-21 18:04:11\",\"amount_method\":\"\",\"cash_amount\":null,\"amount\":\"700\",\"bpSit_systolic\":\"\",\"bpSit_diastolic\":\"\",\"bpStand_systolic\":\"\",\"bpStand_diastolic\":\"\",\"weight\":\"\",\"height\":\"\",\"bmi\":\"\",\"heart_rate\":\"\",\"grbs\":\"\",\"spO2\":\"\",\"patient_overview\":\"\",\"transaction_number\":\"\",\"transaction_amount\":null,\"concession_name\":\"\",\"concession_type\":\"\",\"concession_value\":\"\",\"final_amount\":\"700\",\"respiration_rate\":\"\",\"valid_from\":\"2026-05-21\",\"valid_to\":\"2026-06-03\",\"appointment_status\":\"0\",\"patient_history\":\"\",\"queue_order\":null,\"referred_by\":\"\",\"referral_hospital\":\"\",\"referral_notes\":\"\",\"referral_type\":\"\"}'),
(150, 9, 15, 'Security', 'update', 'security', 16, '2026-05-21 18:09:53', '::1', '{\"security_id\":\"16\",\"admin_name\":\"Dr. Kishore\",\"user_code\":\"D005\",\"email\":\"kishore@gmail.com\",\"contact\":\"7095678670\",\"security_password\":\"827ccb0eea8a706c4c34a16891f84e7b\",\"image_url\":\"\",\"signature_url\":\"\",\"role_id\":\"13\",\"can_switch_doctor\":\"0\",\"security_type\":\"U\",\"org_id\":\"9\",\"created_by\":\"15\",\"modified_by\":\"15\",\"status\":\"1\",\"create_date_time\":\"2026-05-21 15:13:19\"}', '{\"security_id\":\"16\",\"admin_name\":\"Dr. Kishore\",\"user_code\":\"D005\",\"email\":\"kishore@gmail.com\",\"contact\":\"7095678670\",\"security_password\":\"827ccb0eea8a706c4c34a16891f84e7b\",\"image_url\":\"\",\"signature_url\":\"\",\"role_id\":\"16\",\"can_switch_doctor\":\"0\",\"security_type\":\"U\",\"org_id\":\"9\",\"created_by\":\"15\",\"modified_by\":\"15\",\"status\":\"1\",\"create_date_time\":\"2026-05-21 18:09:53\"}'),
(151, 9, 15, 'Security', 'update', 'security', 16, '2026-05-21 18:11:27', '::1', '{\"security_id\":\"16\",\"admin_name\":\"Dr. Kishore\",\"user_code\":\"D005\",\"email\":\"kishore@gmail.com\",\"contact\":\"7095678670\",\"security_password\":\"827ccb0eea8a706c4c34a16891f84e7b\",\"image_url\":\"\",\"signature_url\":\"\",\"role_id\":\"16\",\"can_switch_doctor\":\"0\",\"security_type\":\"U\",\"org_id\":\"9\",\"created_by\":\"15\",\"modified_by\":\"15\",\"status\":\"1\",\"create_date_time\":\"2026-05-21 18:09:53\"}', '{\"security_id\":\"16\",\"admin_name\":\"Dr. Kishore\",\"user_code\":\"D005\",\"email\":\"kishore@gmail.com\",\"contact\":\"7095678670\",\"security_password\":\"827ccb0eea8a706c4c34a16891f84e7b\",\"image_url\":\"\",\"signature_url\":\"\",\"role_id\":\"13\",\"can_switch_doctor\":\"0\",\"security_type\":\"U\",\"org_id\":\"9\",\"created_by\":\"15\",\"modified_by\":\"15\",\"status\":\"1\",\"create_date_time\":\"2026-05-21 18:11:27\"}'),
(152, 9, 16, 'GynaecRx', 'create', 'gynaec_prescriptions', 3, '2026-05-21 19:19:58', '::1', NULL, '{\"patient_name\":\"keerthi\"}'),
(153, 9, 15, 'Invoice', 'delete', 'invoice', 32, '2026-05-25 11:27:31', '::1', '{\"status\":\"1\"}', '{\"status\":\"0\",\"refund_type\":\"refund\",\"refund_amount\":100,\"refund_reason\":\"requested to reduce\",\"refunded_by\":\"15\",\"refunded_at\":\"2026-05-25 11:27:31\"}'),
(154, 9, 15, 'Roles', 'update', 'roles', 13, '2026-05-25 12:20:02', '::1', '{\"role_id\":\"13\",\"role_name\":\"Doctor\",\"created_by\":\"15\",\"created_date_time\":\"2026-05-21 10:52:38\",\"status\":\"1\",\"modified_by\":\"15\",\"modified_date_time\":\"2026-05-21 10:52:38\",\"org_id\":\"9\"}', '{\"role_id\":\"13\",\"role_name\":\"Doctor\",\"created_by\":\"15\",\"created_date_time\":\"2026-05-21 10:52:38\",\"status\":\"1\",\"modified_by\":\"15\",\"modified_date_time\":\"2026-05-21 10:52:38\",\"org_id\":\"9\"}'),
(155, 9, 15, 'Roles', 'update', 'roles', 16, '2026-05-25 12:20:21', '::1', '{\"role_id\":\"16\",\"role_name\":\"Admin\",\"created_by\":\"15\",\"created_date_time\":\"2026-05-21 11:04:23\",\"status\":\"1\",\"modified_by\":\"15\",\"modified_date_time\":\"2026-05-21 11:04:23\",\"org_id\":\"9\"}', '{\"role_id\":\"16\",\"role_name\":\"Admin\",\"created_by\":\"15\",\"created_date_time\":\"2026-05-21 11:04:23\",\"status\":\"1\",\"modified_by\":\"15\",\"modified_date_time\":\"2026-05-21 11:04:23\",\"org_id\":\"9\"}'),
(156, 9, 15, 'Department', 'create', 'department', 16, '2026-05-25 12:21:00', '::1', NULL, '{\"dept_id\":\"16\",\"departmentName\":\"Dentisu\",\"description\":\"hntguj\",\"departmentStatus\":\"In\",\"type\":\"\",\"create_date_time\":\"2026-05-25 12:21:00\",\"created_by\":\"15\",\"modified_by\":\"15\",\"org_id\":\"9\",\"status\":\"1\"}'),
(157, 9, 15, 'Department', 'update', 'department', 16, '2026-05-25 12:21:08', '::1', '{\"dept_id\":\"16\",\"departmentName\":\"Dentisu\",\"description\":\"hntguj\",\"departmentStatus\":\"In\",\"type\":\"\",\"create_date_time\":\"2026-05-25 12:21:00\",\"created_by\":\"15\",\"modified_by\":\"15\",\"org_id\":\"9\",\"status\":\"1\"}', '{\"dept_id\":\"16\",\"departmentName\":\"Dentisu\",\"description\":\"hntguj\",\"departmentStatus\":\"In\",\"type\":\"\",\"create_date_time\":\"2026-05-25 12:21:00\",\"created_by\":\"15\",\"modified_by\":\"15\",\"org_id\":\"9\",\"status\":\"1\"}'),
(158, 9, 15, 'Department', 'update', 'department', 16, '2026-05-25 12:21:37', '::1', '{\"dept_id\":\"16\",\"departmentName\":\"Dentisu\",\"description\":\"hntguj\",\"departmentStatus\":\"In\",\"type\":\"\",\"create_date_time\":\"2026-05-25 12:21:00\",\"created_by\":\"15\",\"modified_by\":\"15\",\"org_id\":\"9\",\"status\":\"1\"}', '{\"dept_id\":\"16\",\"departmentName\":\"Dentist\",\"description\":\"teeth related department\",\"departmentStatus\":\"In\",\"type\":\"\",\"create_date_time\":\"2026-05-25 12:21:37\",\"created_by\":\"15\",\"modified_by\":\"15\",\"org_id\":\"9\",\"status\":\"1\"}'),
(159, 9, 15, 'Department', 'create', 'department', 17, '2026-05-25 12:21:57', '::1', NULL, '{\"dept_id\":\"17\",\"departmentName\":\"testhhjh\",\"description\":\"hgjm\",\"departmentStatus\":\"In\",\"type\":\"\",\"create_date_time\":\"2026-05-25 12:21:57\",\"created_by\":\"15\",\"modified_by\":\"15\",\"org_id\":\"9\",\"status\":\"1\"}'),
(160, 9, 15, 'Roles', 'update', 'roles', 13, '2026-05-25 12:23:23', '::1', '{\"role_id\":\"13\",\"role_name\":\"Doctor\",\"created_by\":\"15\",\"created_date_time\":\"2026-05-21 10:52:38\",\"status\":\"1\",\"modified_by\":\"15\",\"modified_date_time\":\"2026-05-21 10:52:38\",\"org_id\":\"9\"}', '{\"role_id\":\"13\",\"role_name\":\"Doctor\",\"created_by\":\"15\",\"created_date_time\":\"2026-05-21 10:52:38\",\"status\":\"1\",\"modified_by\":\"15\",\"modified_date_time\":\"2026-05-21 10:52:38\",\"org_id\":\"9\"}'),
(161, 9, 16, 'Department', 'delete', 'department', 17, '2026-05-25 12:23:51', '::1', '{\"dept_id\":\"17\",\"departmentName\":\"testhhjh\",\"description\":\"hgjm\",\"departmentStatus\":\"In\",\"type\":\"\",\"create_date_time\":\"2026-05-25 12:21:57\",\"created_by\":\"15\",\"modified_by\":\"15\",\"org_id\":\"9\",\"status\":\"1\"}', '{\"dept_id\":\"17\",\"departmentName\":\"testhhjh\",\"description\":\"hgjm\",\"departmentStatus\":\"In\",\"type\":\"\",\"create_date_time\":\"2026-05-25 12:23:51\",\"created_by\":\"15\",\"modified_by\":\"15\",\"org_id\":\"9\",\"status\":\"0\"}');

-- --------------------------------------------------------

--
-- Table structure for table `bill_pages`
--

CREATE TABLE `bill_pages` (
  `pagetype_id` int(11) NOT NULL,
  `bills` varchar(30) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `crate_date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bill_pages`
--

INSERT INTO `bill_pages` (`pagetype_id`, `bills`, `status`, `crate_date_time`) VALUES
(1, 'Consultation Fee', '1', '2023-10-25 11:41:15'),
(2, 'Prescription', '1', '2023-10-25 11:41:15'),
(3, 'Test Bill', '1', '2023-10-25 11:41:15');

-- --------------------------------------------------------

--
-- Table structure for table `bill_sizes`
--

CREATE TABLE `bill_sizes` (
  `bill_size_id` int(11) NOT NULL,
  `sizes` varchar(225) NOT NULL,
  `note` varchar(225) NOT NULL,
  `org_id` int(11) NOT NULL,
  `create_date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `modify_by` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `pagetype` int(11) NOT NULL,
  `top` varchar(30) NOT NULL,
  `bottom` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cheifcomplaint_template`
--

CREATE TABLE `cheifcomplaint_template` (
  `cc_id` int(11) NOT NULL,
  `template_name` varchar(225) NOT NULL,
  `template_data` varchar(225) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `org_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cheifcomplaint_template`
--

INSERT INTO `cheifcomplaint_template` (`cc_id`, `template_name`, `template_data`, `status`, `org_id`) VALUES
(1, 'test', 'mhgnh\nkjymjyh\njhmjhm', '1', 1);

-- --------------------------------------------------------

--
-- Table structure for table `concessions`
--

CREATE TABLE `concessions` (
  `concession_id` int(11) NOT NULL,
  `concession_name` varchar(255) NOT NULL,
  `concession_type` enum('percentage','amount') NOT NULL,
  `concession_value` varchar(125) NOT NULL,
  `org_id` int(11) DEFAULT NULL,
  `status` enum('0','1') DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_date_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_date_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `concessions`
--

INSERT INTO `concessions` (`concession_id`, `concession_name`, `concession_type`, `concession_value`, `org_id`, `status`, `created_by`, `modified_by`, `created_date_time`, `updated_date_time`) VALUES
(1, 'Family', 'percentage', '50', 1, '1', 2, 2, '2025-09-05 04:25:53', '2025-09-05 04:26:08'),
(2, 'Sister', 'percentage', '10', 1, '0', 2, 2, '2025-09-17 05:38:58', '2025-09-17 05:48:11'),
(3, 'test', 'percentage', '10', 1, '1', 2, 2, '2025-09-17 05:48:41', '2025-09-17 05:48:41'),
(4, 'new', 'amount', '17', 1, '1', 2, 2, '2025-09-17 05:51:29', '2025-09-17 05:51:29'),
(5, 'Family', 'percentage', '50', 9, '1', 15, 15, '2026-05-21 05:55:17', '2026-05-21 05:55:17'),
(6, 'friend', 'percentage', '20', 9, '1', 15, 15, '2026-05-21 05:55:40', '2026-05-21 05:55:40'),
(7, 'Own family', 'percentage', '100', 9, '1', 15, 15, '2026-05-21 05:56:27', '2026-05-21 05:56:27');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `dept_id` int(11) NOT NULL,
  `departmentName` varchar(225) NOT NULL,
  `description` varchar(225) NOT NULL,
  `departmentStatus` varchar(30) NOT NULL DEFAULT 'In',
  `type` varchar(30) NOT NULL,
  `create_date_time` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`dept_id`, `departmentName`, `description`, `departmentStatus`, `type`, `create_date_time`, `created_by`, `modified_by`, `org_id`, `status`) VALUES
(1, 'Cardiology', 'Interventional Cardiologist ', 'In', 'Outside Patient', '2023-11-10 20:32:55', 2, 2, 1, '1'),
(6, 'Diabetics', 'diabetic related issues', 'In', '', '2025-06-16 11:03:30', 2, 2, 1, '1'),
(7, 'ENT', 'eye,nose,tongue treatment testing', 'In', 'Outside Patient', '2025-09-15 09:50:17', 2, 1, 1, '1'),
(8, 'Diabetics new', 'A dedicated service provider to the poor and needy who are in need of medical  help', 'In', '', '2025-09-17 09:42:05', 2, 2, 1, '1'),
(9, 'test', 'test new', 'In', '', '2025-09-17 09:51:44', 2, 2, 1, '1'),
(10, 'Blood', 'blood testing new', 'In', '', '2025-09-17 10:01:03', 2, 2, 1, '1'),
(11, 'test recent', 'description recent', 'In', '', '2025-09-17 10:11:49', 2, 2, 1, '0'),
(12, 'ECG', 'refers to testing', 'In', '', '2025-09-24 12:30:28', 12, 12, 1, '1'),
(13, 'Dentist', 'teeth related department.', 'In', '', '2026-05-20 14:46:45', 2, 2, 1, '0'),
(14, 'Cardiologist', 'heart related issues', 'In', '', '2026-05-21 11:18:54', 15, 15, 9, '1'),
(15, 'Gynecologist', 'gynec related issues', 'In', '', '2026-05-21 11:23:24', 15, 15, 9, '1'),
(16, 'Dentist', 'teeth related department', 'In', '', '2026-05-25 12:21:37', 15, 15, 9, '1'),
(17, 'testhhjh', 'hgjm', 'In', '', '2026-05-25 12:23:51', 15, 15, 9, '0');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doc_id` int(11) NOT NULL,
  `doc_registration_number` varchar(255) NOT NULL,
  `doctor_name` varchar(225) NOT NULL,
  `doctor_type` varchar(100) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `doctor_specialization` varchar(225) NOT NULL,
  `departments` varchar(255) NOT NULL,
  `doctor_services` varchar(225) NOT NULL,
  `doctor_fee` int(11) NOT NULL,
  `doctor_charge` varchar(100) NOT NULL,
  `doctor_visit_charge` varchar(250) DEFAULT NULL,
  `time_slot_duration` int(11) DEFAULT NULL,
  `details` varchar(225) NOT NULL,
  `doc_img` varchar(225) NOT NULL,
  `org_id` int(11) NOT NULL,
  `security_id` varchar(125) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `c_d_t` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doc_id`, `doc_registration_number`, `doctor_name`, `doctor_type`, `gender`, `phone_number`, `email`, `doctor_specialization`, `departments`, `doctor_services`, `doctor_fee`, `doctor_charge`, `doctor_visit_charge`, `time_slot_duration`, `details`, `doc_img`, `org_id`, `security_id`, `status`, `created_by`, `modified_by`, `c_d_t`) VALUES
(1, 'D202509180001', 'Dr.Ashwin Kumar Panda', 'Out', 'Male', '8897355655', 'pandas@gmail.com', '18', '1', '1', 500, '2000', '', 15, 'test', 'doc_1778132166.jpg', 1, '2', '1', 2, 2, '2025-09-18 04:32:14'),
(5, 'D202509240002', 'Pravallika', 'In', 'Female', '7032760271', 'test0@gmail.com', '18', '10', '1', 300, '0', '0', 12, 'test', 'doc_1778158156.jpg', 1, '9', '1', 2, 2, '2025-09-24 06:51:50'),
(6, 'D202509240003', 'Administrator', 'In', 'Male', '6302669660', 'durgalaxmi417@gmail.com', '18', '10', '1', 200, '0', '0', 18, '', 'doc_1778132064.jpg', 1, '10', '1', 2, 2, '2025-09-24 11:27:06'),
(7, 'D202605210001', 'Dr. Ravi Teja', 'In', 'Male', '7032760279', 'raviteja@gmail.com', '20', '14', '2', 600, '0', '0', 15, '', 'doc_1779344992.jpg', 9, '15', '1', 15, 15, '2026-05-21 06:29:52'),
(8, 'D202605210002', 'Dr. Kishore', 'In', 'Male', '7095678670', 'kishore@gmail.com', '21', '15', '2', 700, '0', '0', 20, '', 'doc_1779345041.jpeg', 9, '16', '1', 15, 15, '2026-05-21 06:30:42');

-- --------------------------------------------------------

--
-- Table structure for table `doctors_time_slot`
--

CREATE TABLE `doctors_time_slot` (
  `doctors_time_id` int(11) NOT NULL,
  `doctorName_registrationNumber` varchar(255) NOT NULL,
  `available_date` date NOT NULL,
  `doctortime_type` varchar(255) NOT NULL,
  `selectedDays` varchar(30) NOT NULL,
  `modify_by` varchar(255) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `org_id` int(11) NOT NULL,
  `c_d_t` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `multi_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors_time_slot`
--

INSERT INTO `doctors_time_slot` (`doctors_time_id`, `doctorName_registrationNumber`, `available_date`, `doctortime_type`, `selectedDays`, `modify_by`, `created_by`, `org_id`, `c_d_t`, `status`, `multi_id`) VALUES
(1, '1', '2025-09-29', 'Daily', '', '2', '2', 1, '2025-09-29 03:56:09', '1', 0),
(2, '5', '2025-09-29', 'Daily', '', '2', '2', 1, '2025-09-29 08:03:52', '1', 0),
(3, '1', '2025-09-30', 'Daily', '', '12', '12', 1, '2025-09-29 08:09:28', '1', 0),
(4, '1', '2025-10-01', 'Daily', '', '2', '2', 1, '2025-10-01 04:55:09', '1', 0),
(5, '1', '2025-10-06', 'Range', '1', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(6, '1', '2025-10-07', 'Range', '2', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(7, '1', '2025-10-08', 'Range', '3', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(8, '1', '2025-10-09', 'Range', '4', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(9, '1', '2025-10-10', 'Range', '5', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(10, '1', '2025-10-13', 'Range', '1', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(11, '1', '2025-10-14', 'Range', '2', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(12, '1', '2025-10-15', 'Range', '3', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(13, '1', '2025-10-16', 'Range', '4', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(14, '1', '2025-10-17', 'Range', '5', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(15, '1', '2025-10-20', 'Range', '1', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(16, '1', '2025-10-21', 'Range', '2', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(17, '1', '2025-10-22', 'Range', '3', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(18, '1', '2025-10-23', 'Range', '4', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(19, '1', '2025-10-24', 'Range', '5', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(20, '1', '2025-10-27', 'Range', '1', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(21, '1', '2025-10-28', 'Range', '2', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(22, '1', '2025-10-29', 'Range', '3', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(23, '1', '2025-10-30', 'Range', '4', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(24, '1', '2025-10-31', 'Range', '5', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(25, '1', '2025-11-03', 'Range', '1', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(26, '1', '2025-11-04', 'Range', '2', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(27, '1', '2025-11-05', 'Range', '3', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(28, '1', '2025-11-06', 'Range', '4', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(29, '1', '2025-11-07', 'Range', '5', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(30, '1', '2025-11-10', 'Range', '1', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(31, '1', '2025-11-11', 'Range', '2', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(32, '1', '2025-11-12', 'Range', '3', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(33, '1', '2025-11-13', 'Range', '4', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(34, '1', '2025-11-14', 'Range', '5', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(35, '1', '2025-11-17', 'Range', '1', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(36, '1', '2025-11-18', 'Range', '2', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(37, '1', '2025-11-19', 'Range', '3', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(38, '1', '2025-11-20', 'Range', '4', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(39, '1', '2025-11-21', 'Range', '5', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(40, '1', '2025-11-24', 'Range', '1', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(41, '1', '2025-11-25', 'Range', '2', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(42, '1', '2025-11-26', 'Range', '3', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(43, '1', '2025-11-27', 'Range', '4', '2', '2', 1, '2025-10-06 11:09:35', '1', 3),
(44, '1', '2026-04-08', 'Daily', '', '2', '2', 1, '2026-04-08 07:43:13', '1', 0),
(45, '1', '2026-04-10', 'Daily', '', '2', '2', 1, '2026-04-10 08:41:09', '1', 0),
(46, '1', '2026-04-15', 'Daily', '', '2', '2', 1, '2026-04-15 04:11:04', '1', 0),
(47, '1', '2026-04-16', 'Daily', '', '2', '2', 1, '2026-04-16 09:15:45', '1', 0),
(48, '1', '2026-05-06', 'Daily', '', '2', '2', 1, '2026-05-06 07:02:15', '1', 0),
(49, '1', '2026-05-07', 'Daily', '', '2', '2', 1, '2026-05-07 04:13:12', '1', 0),
(50, '5', '2026-05-07', 'Daily', '', '2', '2', 1, '2026-05-07 11:09:57', '1', 0),
(51, '1', '2026-05-08', 'Daily', '', '2', '2', 1, '2026-05-08 00:08:26', '1', 0),
(52, '1', '2026-05-20', 'Daily', '', '2', '2', 1, '2026-05-20 09:17:20', '1', 0),
(53, '7', '2026-05-21', 'Daily', '', '15', '15', 9, '2026-05-21 06:31:56', '1', 0),
(54, '8', '2026-05-21', 'Daily', '', '15', '15', 9, '2026-05-21 12:31:31', '1', 0);

-- --------------------------------------------------------

--
-- Table structure for table `doctors_time_slot2`
--

CREATE TABLE `doctors_time_slot2` (
  `doctor_another_Time_Slot_id` int(11) NOT NULL,
  `doctors_time_id` int(11) NOT NULL,
  `starting_Time` varchar(100) NOT NULL,
  `ending_Time` varchar(100) NOT NULL,
  `created_by` int(11) NOT NULL,
  `modify_by` int(11) NOT NULL,
  `status` enum('1','0','') NOT NULL DEFAULT '1',
  `create_date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `org_id` int(11) NOT NULL,
  `multi_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors_time_slot2`
--

INSERT INTO `doctors_time_slot2` (`doctor_another_Time_Slot_id`, `doctors_time_id`, `starting_Time`, `ending_Time`, `created_by`, `modify_by`, `status`, `create_date_time`, `org_id`, `multi_id`) VALUES
(1, 1, '09:26', '15:26', 2, 2, '1', '2025-09-29 03:56:09', 1, 0),
(2, 2, '13:33', '19:33', 2, 2, '1', '2025-09-29 08:03:52', 1, 0),
(3, 3, '14:00', '15:00', 12, 12, '1', '2025-09-29 08:09:28', 1, 0),
(4, 4, '10:25', '16:25', 2, 2, '1', '2025-10-01 04:55:09', 1, 0),
(5, 5, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(6, 6, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(7, 7, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(8, 8, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(9, 9, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(10, 10, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(11, 11, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(12, 12, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(13, 13, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(14, 14, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(15, 15, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(16, 16, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(17, 17, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(18, 18, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(19, 19, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(20, 20, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(21, 21, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(22, 22, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(23, 23, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(24, 24, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(25, 25, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(26, 26, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(27, 27, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(28, 28, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(29, 29, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(30, 30, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(31, 31, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(32, 32, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(33, 33, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(34, 34, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(35, 35, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(36, 36, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(37, 37, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(38, 38, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(39, 39, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(40, 40, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(41, 41, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(42, 42, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(43, 43, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 3),
(44, 44, '13:13', '19:13', 2, 2, '1', '2026-04-08 07:43:13', 1, 0),
(45, 45, '14:11', '20:11', 2, 2, '1', '2026-04-10 08:41:09', 1, 0),
(46, 46, '09:40', '18:40', 2, 2, '1', '2026-04-15 04:11:04', 1, 0),
(47, 47, '14:45', '20:45', 2, 2, '1', '2026-04-16 09:15:45', 1, 0),
(48, 48, '12:32', '18:32', 2, 2, '1', '2026-05-06 07:02:15', 1, 0),
(49, 49, '09:43', '19:43', 2, 2, '1', '2026-05-07 04:13:12', 1, 0),
(50, 50, '17:39', '21:39', 2, 2, '1', '2026-05-07 11:09:57', 1, 0),
(51, 51, '05:38', '11:38', 2, 2, '1', '2026-05-08 00:08:26', 1, 0),
(52, 52, '14:47', '20:47', 2, 2, '1', '2026-05-20 09:17:20', 1, 0),
(53, 53, '12:01', '18:01', 15, 15, '1', '2026-05-21 06:31:56', 9, 0),
(54, 54, '18:01', '23:01', 15, 15, '1', '2026-05-21 12:31:31', 9, 0);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_patient_duration`
--

CREATE TABLE `doctor_patient_duration` (
  `id` int(11) NOT NULL,
  `appointment_id` varchar(100) NOT NULL,
  `check_in` datetime NOT NULL,
  `check_out` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_patient_duration`
--

INSERT INTO `doctor_patient_duration` (`id`, `appointment_id`, `check_in`, `check_out`) VALUES
(1, '0', '2025-09-29 16:51:15', NULL),
(2, '0', '2025-09-29 16:59:53', NULL),
(3, '0', '2025-09-29 17:01:31', NULL),
(4, '0', '2025-09-29 17:04:26', NULL),
(5, '0', '2025-09-29 17:05:04', NULL),
(6, '0', '2025-09-29 17:05:07', NULL),
(7, 'A202509290001', '2025-09-29 17:14:49', NULL),
(8, 'A202509290001', '2025-09-29 17:16:23', '2025-09-29 17:22:34'),
(9, 'A202509290002', '2025-09-29 17:18:26', NULL),
(10, 'A202509290002', '2025-09-29 17:18:26', NULL),
(11, 'A202509290001', '2025-09-29 17:40:28', NULL),
(12, 'A202509290001', '2025-09-29 17:46:29', NULL),
(13, 'A202509290001', '2025-09-29 17:46:58', NULL),
(14, 'A202509290001', '2025-09-29 17:46:58', NULL),
(15, 'A202509290002', '2025-09-29 18:08:35', NULL),
(16, 'A202509290002', '2025-09-29 18:08:35', NULL),
(17, 'A202509290001', '2025-09-29 18:14:14', '2025-09-29 18:23:52'),
(18, 'A202509290002', '2025-09-29 18:21:45', NULL),
(19, 'A202509290002', '2025-09-29 18:23:42', NULL),
(20, 'A202510060001', '2025-10-06 16:41:45', '2025-10-06 16:41:54'),
(21, 'A202510060002', '2025-10-06 18:31:31', '2025-10-06 18:31:38'),
(22, 'A202605070001', '2026-05-07 15:14:42', NULL),
(23, 'A202605070001', '2026-05-07 15:14:42', '2026-05-07 15:14:56'),
(24, 'A202605200001', '2026-05-20 16:27:58', '2026-05-20 16:28:03'),
(25, 'A202605200002', '2026-05-20 16:29:03', '2026-05-20 16:29:07'),
(26, 'A202605200001', '2026-05-20 16:34:59', '2026-05-20 16:35:20'),
(27, 'A202605200001', '2026-05-20 16:34:59', '2026-05-20 16:35:20'),
(28, 'A202605200001', '2026-05-20 16:35:58', NULL),
(29, 'A202605200001', '2026-05-20 16:35:58', '2026-05-20 16:36:23'),
(30, 'A202605200002', '2026-05-20 16:43:01', '2026-05-20 16:43:08'),
(31, 'A202605200001', '2026-05-20 16:43:31', '2026-05-20 16:43:35'),
(32, 'A202605200001', '2026-05-20 16:48:31', NULL),
(33, 'A202605200001', '2026-05-20 16:48:31', '2026-05-20 16:49:01'),
(34, 'A202605200002', '2026-05-20 16:49:07', NULL),
(35, 'A202605200002', '2026-05-20 16:49:07', '2026-05-20 16:49:15'),
(36, 'A202605200003', '2026-05-20 16:54:33', '2026-05-20 16:54:50'),
(37, 'A202605200003', '2026-05-20 16:54:33', '2026-05-20 16:54:50'),
(38, 'A202605200003', '2026-05-20 16:54:55', NULL),
(39, 'A202605200003', '2026-05-20 16:54:55', '2026-05-20 16:55:00'),
(40, 'A202605200002', '2026-05-20 16:55:20', '2026-05-20 17:00:22'),
(41, 'A202605200002', '2026-05-20 16:55:20', '2026-05-20 17:00:22'),
(42, 'A202605200002', '2026-05-20 17:00:28', NULL),
(43, 'A202605200002', '2026-05-20 17:00:28', NULL),
(44, 'A202605210001', '2026-05-21 14:38:04', '2026-05-21 14:46:49'),
(45, 'A202605210002', '2026-05-21 19:16:09', '2026-05-21 19:20:07');

-- --------------------------------------------------------

--
-- Table structure for table `dosage`
--

CREATE TABLE `dosage` (
  `dosage_id` int(11) NOT NULL,
  `dosages` varchar(50) NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `create_by` int(11) NOT NULL,
  `modify_by` int(11) NOT NULL,
  `create_date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `org_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dosage`
--

INSERT INTO `dosage` (`dosage_id`, `dosages`, `status`, `create_by`, `modify_by`, `create_date_time`, `org_id`) VALUES
(1, '1-0-0', '1', 0, 0, '2023-09-09 05:13:02', 0),
(2, '0-1-0', '1', 0, 0, '2023-09-09 05:13:13', 0),
(3, '0-0-1', '1', 0, 0, '2023-09-09 05:13:21', 0),
(4, '1-1-0', '1', 0, 0, '2023-09-09 05:13:28', 0),
(5, '1-0-1', '1', 0, 0, '2023-09-09 05:13:34', 0),
(6, '0-1-1', '1', 0, 0, '2023-09-09 05:13:43', 0),
(7, '1-1-1', '1', 0, 0, '2023-09-09 05:14:09', 0),
(8, 'S-O-S', '1', 0, 0, '2025-07-01 07:12:32', 0);

-- --------------------------------------------------------

--
-- Table structure for table `dosageandtime`
--

CREATE TABLE `dosageandtime` (
  `doseandtime_id` int(11) NOT NULL,
  `dose_id` varchar(50) NOT NULL,
  `intake_time_id` varchar(50) NOT NULL,
  `dose_schedule` varchar(255) NOT NULL,
  `frequency` varchar(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `created_date_time` datetime DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `modified_date_time` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dosageandtime`
--

INSERT INTO `dosageandtime` (`doseandtime_id`, `dose_id`, `intake_time_id`, `dose_schedule`, `frequency`, `org_id`, `status`, `created_date_time`, `created_by`, `modified_date_time`, `modified_by`) VALUES
(1, '6', '15', '1 - 1 - 0 | Before Breakfast - Before Lunch | 6:00 AM;7:00 AM - 12:30 PM', '2', 1, '1', '2025-02-03 10:58:46', '2', '2025-03-15 11:36:50', '2'),
(2, '6', '17', '1 - 1 - 0 | After Breakfast - Before Lunch | 9:00 AM - 12:30 PM', '2', 1, '1', '2025-02-03 11:17:42', '2', '2025-03-15 11:36:50', '2'),
(3, '4', '10', '1 - 0 - 0 | After Breakfast | 9:00 AM', '1', 1, '0', '2025-02-03 11:27:32', '2', '2025-03-15 11:37:43', '2'),
(4, '3', '7', '0 - 1 - 1 | After Lunch - Before Dinner | 2:00 PM - 7:00 PM', '2', 1, '1', '2025-02-03 11:31:04', '2', '2025-03-15 11:36:50', '2'),
(5, '5', '12', '1 - 0 - 1 | Before Breakfast - After Dinner | 6:00 AM;7:00 AM - 9:00 PM', '2', 1, '1', '2025-02-08 09:53:09', '2', '2025-03-15 11:36:50', '2'),
(6, '1', '1', '0 - 0 - 1 | Before Dinner | 7:00 PM', '1', 1, '1', '2025-02-08 10:37:27', '2', '2025-03-15 11:36:50', '2'),
(7, '1', '2', '0 - 0 - 1 | After Dinner | 9:00 PM', '1', 1, '1', '2025-02-08 10:37:37', '2', '2025-03-15 11:36:50', '2'),
(8, '2', '3', '0 - 1 - 0 | Before Lunch | 12:30 PM', '1', 1, '1', '2025-02-08 10:38:46', '2', '2025-03-15 11:36:50', '2'),
(9, '2', '4', '0 - 1 - 0 | After Lunch | 2:00 PM', '1', 1, '1', '2025-02-08 10:38:54', '2', '2025-03-15 11:36:50', '2'),
(10, '4', '9', '1 - 0 - 0 | Before Breakfast | 6:00 AM;7:00 AM', '1', 1, '1', '2025-02-08 10:39:05', '2', '2025-03-15 11:36:50', '2'),
(11, '5', '11', '1 - 0 - 1 | Before Breakfast - Before Dinner | 6:00 AM;7:00 AM - 7:00 PM', '2', 1, '1', '2025-02-08 10:54:09', '2', '2025-03-15 11:36:50', '2'),
(12, '5', '13', '1 - 0 - 1 | After Breakfast - Before Dinner | 9:00 AM - 7:00 PM', '2', 1, '1', '2025-02-08 10:54:51', '2', '2025-03-15 11:36:50', '2'),
(13, '5', '14', '1 - 0 - 1 | After Breakfast - After Dinner | 9:00 AM - 9:00 PM', '2', 1, '1', '2025-02-08 10:55:07', '2', '2025-03-15 11:36:50', '2'),
(14, '4', '10', '1 - 0 - 0 | After Breakfast | 9:00 AM', '1', 1, '1', '2025-02-08 11:00:12', '2', '2025-03-15 11:36:50', '2'),
(15, '6', '16', '1 - 1 - 0 | Before Breakfast - After Lunch | 6:00 AM;7:00 AM - 2:00 PM', '2', 1, '1', '2025-02-08 11:06:38', '2', '2025-03-15 11:36:50', '2'),
(16, '6', '18', '1 - 1 - 0 | After Breakfast - After Lunch | 9:00 AM - 2:00 PM', '2', 1, '1', '2025-02-08 11:07:08', '2', '2025-03-15 11:36:50', '2'),
(17, '3', '5', '0 - 1 - 1 | Before Lunch - Before Dinner | 12:30 PM - 7:00 PM', '2', 1, '1', '2025-02-08 11:07:21', '2', '2025-03-15 11:36:50', '2'),
(18, '3', '6', '0 - 1 - 1 | Before Lunch - After Dinner | 12:30 PM - 9:00 PM', '2', 1, '1', '2025-02-08 11:07:30', '2', '2025-03-15 11:36:50', '2'),
(19, '3', '8', '0 - 1 - 1 | After Lunch - After Dinner | 2:00 PM - 9:00 PM', '2', 1, '1', '2025-02-08 11:07:51', '2', '2025-03-15 11:36:50', '2'),
(20, '7', '19', '1 - 1 - 1 | Before Breakfast - Before Lunch - Before Dinner | 6:00 AM;7:00 AM - 12:30 PM - 7:00 PM', '3', 1, '1', '2025-02-08 11:08:42', '2', '2025-03-15 11:36:50', '2'),
(21, '7', '20', '1 - 1 - 1 | Before Breakfast - Before Lunch - After Dinner | 6:00 AM;7:00 AM - 12:30 PM - 9:00 PM', '3', 1, '1', '2025-02-08 11:08:51', '2', '2025-03-15 11:36:50', '2'),
(22, '7', '21', '1 - 1 - 1 | Before Breakfast - After Lunch - Before Dinner | 6:00 AM;7:00 AM - 2:00 PM - 7:00 PM', '3', 1, '1', '2025-02-08 11:09:00', '2', '2025-03-15 11:36:50', '2'),
(23, '7', '22', '1 - 1 - 1 | Before Breakfast - After Lunch - After Dinner | 6:00 AM;7:00 AM - 2:00 PM - 9:00 PM', '3', 1, '1', '2025-02-08 11:09:13', '2', '2025-03-15 11:36:50', '2'),
(24, '7', '23', '1 - 1 - 1 | After Breakfast - Before Lunch - Before Dinner | 9:00 AM - 12:30 PM - 7:00 PM', '3', 1, '1', '2025-02-08 11:09:22', '2', '2025-03-15 11:36:50', '2'),
(25, '7', '24', '1 - 1 - 1 | After Breakfast - Before Lunch - After Dinner | 9:00 AM - 12:30 PM - 9:00 PM', '3', 1, '1', '2025-02-08 11:09:31', '2', '2025-03-15 11:36:50', '2'),
(26, '7', '25', '1 - 1 - 1 | After Breakfast - After Lunch - Before Dinner | 9:00 AM - 2:00 PM - 7:00 PM', '3', 1, '1', '2025-02-08 11:09:40', '2', '2025-03-15 11:36:50', '2'),
(27, '7', '26', '1 - 1 - 1 | After Breakfast - After Lunch - After Dinner | 9:00 AM - 2:00 PM - 9:00 PM', '3', 1, '0', '2025-02-08 11:09:48', '2', '2025-03-15 11:55:49', '2'),
(28, '7', '26', '1 - 1 - 1 | After Breakfast - After Lunch - After Dinner | 9:00 AM - 2:00 PM - 9:00 PM', '3', 1, '1', '2025-03-15 11:56:02', '1', '2025-03-15 11:56:02', '1');

-- --------------------------------------------------------

--
-- Table structure for table `dose`
--

CREATE TABLE `dose` (
  `dose_id` int(11) NOT NULL,
  `morning` tinyint(4) NOT NULL,
  `afternoon` tinyint(4) NOT NULL,
  `evening` tinyint(4) NOT NULL,
  `Status` enum('1','0') DEFAULT '1',
  `created_date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dose`
--

INSERT INTO `dose` (`dose_id`, `morning`, `afternoon`, `evening`, `Status`, `created_date_time`) VALUES
(1, 0, 0, 1, '1', NULL),
(2, 0, 1, 0, '1', NULL),
(3, 0, 1, 1, '1', NULL),
(4, 1, 0, 0, '1', NULL),
(5, 1, 0, 1, '1', NULL),
(6, 1, 1, 0, '1', NULL),
(7, 1, 1, 1, '1', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `echo_reports`
--

CREATE TABLE `echo_reports` (
  `echo_report_id` int(11) NOT NULL,
  `appointment_id` varchar(50) DEFAULT NULL,
  `patient_id` varchar(50) DEFAULT NULL,
  `patient_name` varchar(200) NOT NULL,
  `report_date` date DEFAULT NULL,
  `age` varchar(20) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `ref_by` varchar(200) DEFAULT NULL,
  `indication` varchar(500) DEFAULT NULL,
  `mitral_valve` varchar(200) DEFAULT 'Normal',
  `aortic_valve` varchar(200) DEFAULT 'Normal',
  `tricuspid_valve` varchar(200) DEFAULT 'Normal',
  `pulmonary_valve` varchar(200) DEFAULT 'Normal',
  `left_atrium` varchar(50) DEFAULT NULL,
  `lvid_d` varchar(50) DEFAULT NULL,
  `lvid_s` varchar(50) DEFAULT NULL,
  `ef` varchar(50) DEFAULT NULL,
  `ivs_thickness` varchar(50) DEFAULT NULL,
  `pwd` varchar(50) DEFAULT NULL,
  `lv_rwma` varchar(200) DEFAULT 'NO RWMA',
  `right_atrium` varchar(200) DEFAULT 'Normal',
  `right_ventricle` varchar(200) DEFAULT 'Normal',
  `tapse` varchar(50) DEFAULT NULL,
  `aorta` varchar(50) DEFAULT NULL,
  `ajv` varchar(50) DEFAULT NULL,
  `pulmonary_artery` varchar(200) DEFAULT 'Normal',
  `pjv` varchar(50) DEFAULT NULL,
  `ivs_status` varchar(100) DEFAULT 'Intact',
  `ias_status` varchar(100) DEFAULT 'Intact',
  `ivc_svc_cs` varchar(100) DEFAULT 'Normal',
  `pericardium` varchar(100) DEFAULT 'No PE',
  `mitral_flow` varchar(100) DEFAULT NULL,
  `doppler_mr` varchar(50) DEFAULT 'NO',
  `doppler_ar` varchar(50) DEFAULT 'NO',
  `doppler_tr` varchar(50) DEFAULT 'NO',
  `doppler_pr` varchar(50) DEFAULT 'NO',
  `conclusion` text DEFAULT NULL,
  `doctor_name` varchar(200) DEFAULT NULL,
  `doctor_credentials` varchar(500) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `org_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `echo_reports`
--

INSERT INTO `echo_reports` (`echo_report_id`, `appointment_id`, `patient_id`, `patient_name`, `report_date`, `age`, `gender`, `ref_by`, `indication`, `mitral_valve`, `aortic_valve`, `tricuspid_valve`, `pulmonary_valve`, `left_atrium`, `lvid_d`, `lvid_s`, `ef`, `ivs_thickness`, `pwd`, `lv_rwma`, `right_atrium`, `right_ventricle`, `tapse`, `aorta`, `ajv`, `pulmonary_artery`, `pjv`, `ivs_status`, `ias_status`, `ivc_svc_cs`, `pericardium`, `mitral_flow`, `doppler_mr`, `doppler_ar`, `doppler_tr`, `doppler_pr`, `conclusion`, `doctor_name`, `doctor_credentials`, `status`, `org_id`, `created_by`, `created_at`) VALUES
(1, 'A202502010001', 'PAT0001', 'Y.BHIMA RAJU', '2025-03-01', '74', 'Male', 'Dr.Ashwin Kumar Panda', 'Clinical indication', 'Normal', 'Normal', 'Normal', 'Normal', '3.2', '4.0', '2.6', '64', '1.1', '1.1', 'NO RWMA', 'Normal', 'Normal', '1.9', '1.9', '1.1', 'Normal', '0.9', 'Intact', 'Intact', 'Normal', 'No PE', 'E>F>g', 'NO', 'TRIVIAL', 'NO', 'NO', 'testing purpose ignore this time', 'Dr.Aswin Kumar', 'Cardiologist', '1', 1, 2, '2026-04-16 09:06:13');

-- --------------------------------------------------------

--
-- Table structure for table `finaldiagnosis_template`
--

CREATE TABLE `finaldiagnosis_template` (
  `fd_id` int(11) NOT NULL,
  `template_name` varchar(225) NOT NULL,
  `template_data` varchar(225) NOT NULL,
  `status` enum('0','1') NOT NULL,
  `org_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `finaldiagnosis_template`
--

INSERT INTO `finaldiagnosis_template` (`fd_id`, `template_name`, `template_data`, `status`, `org_id`) VALUES
(1, 'First Diagnosis', 'test', '1', 1),
(2, 'First Diagnosis', 'hgnhnks\nsjhmfhn\nsjhmdhmn', '1', 1),
(3, 'newtemp', 'testing\npurpose\n123@', '1', 1),
(4, 'test1', 'jgjj\nbnbjm\nn bn', '1', 1),
(5, 'Heart attack', 'Heart Attack', '1', 9);

-- --------------------------------------------------------

--
-- Table structure for table `frequency`
--

CREATE TABLE `frequency` (
  `freq_id` int(11) NOT NULL,
  `freq_name` varchar(100) NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `create_by` int(11) NOT NULL,
  `create_date_time` datetime NOT NULL,
  `org_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `frequency`
--

INSERT INTO `frequency` (`freq_id`, `freq_name`, `status`, `create_by`, `create_date_time`, `org_id`) VALUES
(1, 'Once Daily', '1', 0, '2023-09-09 07:15:16', 0),
(2, 'Twice Daily', '1', 0, '2023-09-09 07:15:16', 0),
(3, 'Thrice Daily', '1', 0, '2023-09-09 07:15:16', 0);

-- --------------------------------------------------------

--
-- Table structure for table `gynaec_prescriptions`
--

CREATE TABLE `gynaec_prescriptions` (
  `gynaec_rx_id` int(11) NOT NULL,
  `appointment_id` varchar(50) DEFAULT NULL,
  `patient_id` varchar(50) DEFAULT NULL,
  `patient_name` varchar(200) NOT NULL,
  `age` varchar(20) DEFAULT NULL,
  `gender` varchar(20) DEFAULT 'Female',
  `mobile` varchar(20) DEFAULT NULL,
  `rx_date` date DEFAULT NULL,
  `ref_by` varchar(200) DEFAULT NULL,
  `doctor_name` varchar(200) DEFAULT NULL,
  `doctor_credentials` varchar(500) DEFAULT NULL,
  `menstrual_history` text DEFAULT NULL,
  `lmp` date DEFAULT NULL,
  `pmc` varchar(100) DEFAULT NULL,
  `edd` date DEFAULT NULL,
  `risk_factors` text DEFAULT NULL,
  `scan_type` varchar(200) DEFAULT NULL,
  `scan_date` date DEFAULT NULL,
  `scan_findings` text DEFAULT NULL,
  `scan_remarks` text DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `final_diagnosis` text DEFAULT NULL,
  `chief_complaints` text DEFAULT NULL,
  `gynaec_history` text DEFAULT NULL,
  `obstetric_history` text DEFAULT NULL,
  `family_history` text DEFAULT NULL,
  `personal_history` text DEFAULT NULL,
  `general_examination` text DEFAULT NULL,
  `previous_investigations` text DEFAULT NULL,
  `plan` text DEFAULT NULL,
  `advice` text DEFAULT NULL,
  `review_after` varchar(100) DEFAULT NULL,
  `reviewafterdate` varchar(30) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `org_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `medicines_json` longtext DEFAULT NULL,
  `investigations_json` longtext DEFAULT NULL,
  `patient_data` longtext DEFAULT NULL,
  `bpSit_systolic` varchar(10) DEFAULT NULL,
  `bpSit_diastolic` varchar(10) DEFAULT NULL,
  `bpStand_systolic` varchar(10) DEFAULT NULL,
  `bpStand_diastolic` varchar(10) DEFAULT NULL,
  `weight` varchar(10) DEFAULT NULL,
  `height` varchar(10) DEFAULT NULL,
  `bmi` varchar(10) DEFAULT NULL,
  `grbs` varchar(10) DEFAULT NULL,
  `heart_rate` varchar(10) DEFAULT NULL,
  `temperature` varchar(10) DEFAULT NULL,
  `respiration_rate` varchar(10) DEFAULT NULL,
  `spO2` varchar(10) DEFAULT NULL,
  `patient_overview` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gynaec_prescriptions`
--

INSERT INTO `gynaec_prescriptions` (`gynaec_rx_id`, `appointment_id`, `patient_id`, `patient_name`, `age`, `gender`, `mobile`, `rx_date`, `ref_by`, `doctor_name`, `doctor_credentials`, `menstrual_history`, `lmp`, `pmc`, `edd`, `risk_factors`, `scan_type`, `scan_date`, `scan_findings`, `scan_remarks`, `review_notes`, `final_diagnosis`, `chief_complaints`, `gynaec_history`, `obstetric_history`, `family_history`, `personal_history`, `general_examination`, `previous_investigations`, `plan`, `advice`, `review_after`, `reviewafterdate`, `status`, `org_id`, `created_by`, `created_at`, `medicines_json`, `investigations_json`, `patient_data`, `bpSit_systolic`, `bpSit_diastolic`, `bpStand_systolic`, `bpStand_diastolic`, `weight`, `height`, `bmi`, `grbs`, `heart_rate`, `temperature`, `respiration_rate`, `spO2`, `patient_overview`) VALUES
(1, 'A202604160001', 'PAT0533', 'durga prasad', '30', 'Male', '8787878778', '2026-04-16', '', '', '', 'testing', '2026-04-15', 'regular', '2026-04-24', 'nothing', 'tvs', '2026-04-15', 'testing', 'testing', 'test note', 'test', 'test1', 'test2', 'test3', 'test4', 'test5', 'test6', 'test7', 'test plan', 'test advise', 'By 2026-05-21', '2026-05-21', '1', 1, 2, '2026-04-16 15:22:38', '[{\"drugName\":\"GABACOX - M - (GABAPENTIN + METHYLCOBALAMIN)\",\"typeText\":\"Tab\",\"unitText\":\"10/25MG\",\"dosageId\":\"5\",\"dosageText\":\"1-0-1\",\"whenId\":\"9\",\"whenText\":\"Not Applicable\",\"timeId\":\"1\",\"timeText\":\"9AM-0-9PM\",\"duration_value\":\"3\",\"duration\":\"Days\",\"route\":\"Intravenous (IV)\",\"notes\":\"\"},{\"drugName\":\"ECOSPRIN AV - (ASPIRIN + ATORVASTATIN)\",\"typeText\":\"Tab\",\"unitText\":\"40/50MG\",\"dosageId\":\"2\",\"dosageText\":\"0-1-0\",\"whenId\":\"8\",\"whenText\":\"After Food\",\"timeId\":\"4\",\"timeText\":\"0-2PM-0\",\"duration_value\":\"3\",\"duration\":\"Days\",\"route\":\"Oral\",\"notes\":\"\"}]', '[{\"investigation_name\":\"HISTOPATHOLOGY BIOPSY MEDIUM SPECIMEN\",\"instructions\":\"\",\"price\":\"800.00\",\"concession\":\"\"}]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'A202605070003', 'PAT0010', 'G KALAYANI', '55', 'Female', '7995908643', '2026-05-07', '', '', '', '35 days', '2026-04-08', 'irregular', '2027-01-07', 'minor risk', 'TVS', '2026-05-07', 'normal', '', '', 'final', 'chief', 'gynaec', 'G', 'nothing', 'irregular', '', '', '', '', '10 Days', '2026-05-17', '1', 1, 2, '2026-05-07 16:44:22', '[{\"drugName\":\"DILNIP - (CILINDIPINE)\",\"typeText\":\"Tab\",\"unitText\":\"10/25MG\",\"dosageId\":\"6\",\"whenId\":\"8\",\"timeId\":\"10\",\"duration_value\":\"5\",\"duration\":\"Days\",\"route\":\"Intravenous (IV)\",\"notes\":\"\",\"dosageText\":\"0-1-1\",\"whenText\":\"After Food\",\"timeText\":\"0-2PM-9PM\",\"medConcessionId\":\"1\",\"medConcessionName\":\"Family\",\"medConcessionType\":\"percentage\",\"medConcessionVal\":50}]', '[{\"investigation_name\":\"HISTOPATHOLOGY BIOPSY LARGE COMPLEX\",\"instructions\":\"\",\"price\":\"3250.00\",\"concession\":\"Family (50%)\"}]', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(3, 'A202605210002', 'PAT0002', 'keerthi', '28', 'Female', '7787868688', '2026-05-21', '', '', '', '', '2026-04-01', 'irregular', '2027-03-16', '', 'TVS', '2026-05-20', '', '', '', 'pregnancy check up', '', '', '', '', '', '', '', '', '', '5 Days', '2026-05-26', '1', 9, 16, '2026-05-21 19:19:58', '[{\"drugName\":\"PROGESTERONE 200MG - (MICRONIZED PROGESTERONE)\",\"typeText\":\"Tab\",\"unitText\":\"200mg\",\"dosageId\":\"5\",\"whenId\":\"8\",\"timeId\":\"1\",\"duration_value\":\"5\",\"duration\":\"Days\",\"route\":\"\",\"notes\":\"\",\"dosageText\":\"1-0-1\",\"whenText\":\"After Food\",\"timeText\":\"9AM-0-9PM\",\"medConcessionId\":\"\",\"medConcessionName\":\"No Discount\",\"medConcessionType\":\"\",\"medConcessionVal\":\"\"},{\"drugName\":\"DOLO 650 - (PARACETAMOL IP)\",\"typeText\":\"Tab\",\"unitText\":\"500MG\",\"dosageId\":\"5\",\"whenId\":\"8\",\"timeId\":\"1\",\"duration_value\":\"5\",\"duration\":\"Days\",\"route\":\"\",\"notes\":\"\",\"dosageText\":\"1-0-1\",\"whenText\":\"After Food\",\"timeText\":\"9AM-0-9PM\",\"medConcessionId\":\"\",\"medConcessionName\":\"No Discount\",\"medConcessionType\":\"\",\"medConcessionVal\":\"\"}]', '[{\"investigation_name\":\"LIPID PROFILE\",\"instructions\":\"\",\"price\":\"500.00\",\"concession\":\"\"}]', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `instruction_template`
--

CREATE TABLE `instruction_template` (
  `it_id` int(11) NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `template_data` longtext NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'medicine',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `org_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instruction_template`
--

INSERT INTO `instruction_template` (`it_id`, `template_name`, `template_data`, `type`, `status`, `org_id`) VALUES
(1, 'test', 'test', 'investigation', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `intake_time`
--

CREATE TABLE `intake_time` (
  `intake_time_id` int(11) NOT NULL,
  `dose_id` int(11) NOT NULL,
  `morning` varchar(20) NOT NULL DEFAULT '',
  `morning_time` varchar(50) NOT NULL DEFAULT '',
  `afternoon` varchar(20) NOT NULL DEFAULT '',
  `afternoon_time` varchar(50) NOT NULL DEFAULT '',
  `evening` varchar(20) NOT NULL DEFAULT '',
  `evening_time` varchar(50) NOT NULL DEFAULT '',
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `created_date_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `intake_time`
--

INSERT INTO `intake_time` (`intake_time_id`, `dose_id`, `morning`, `morning_time`, `afternoon`, `afternoon_time`, `evening`, `evening_time`, `status`, `created_date_time`) VALUES
(1, 1, '', '', '', '', 'Before Dinner', '7:00 PM', '1', '2025-02-01 16:41:05'),
(2, 1, '', '', '', '', 'After Dinner', '9:00 PM', '1', '2025-02-01 16:41:05'),
(3, 2, '', '', 'Before Lunch', '12:30 PM', '', '', '1', '2025-02-01 16:41:05'),
(4, 2, '', '', 'After Lunch', '2:00 PM', '', '', '1', '2025-02-01 16:41:05'),
(5, 3, '', '', 'Before Lunch', '12:30 PM', 'Before Dinner', '7:00 PM', '1', '2025-02-01 16:41:05'),
(6, 3, '', '', 'Before Lunch', '12:30 PM', 'After Dinner', '9:00 PM', '1', '2025-02-01 16:41:05'),
(7, 3, '', '', 'After Lunch', '2:00 PM', 'Before Dinner', '7:00 PM', '1', '2025-02-01 16:41:05'),
(8, 3, '', '', 'After Lunch', '2:00 PM', 'After Dinner', '9:00 PM', '1', '2025-02-01 16:41:05'),
(9, 4, 'Before Breakfast', '6:00 AM;7:00 AM', '', '', '', '', '1', '2025-02-01 16:41:05'),
(10, 4, 'After Breakfast', '9:00 AM', '', '', '', '', '1', '2025-02-01 16:41:05'),
(11, 5, 'Before Breakfast', '6:00 AM;7:00 AM', '', '', 'Before Dinner', '7:00 PM', '1', '2025-02-01 16:41:05'),
(12, 5, 'Before Breakfast', '6:00 AM;7:00 AM', '', '', 'After Dinner', '9:00 PM', '1', '2025-02-01 16:41:05'),
(13, 5, 'After Breakfast', '9:00 AM', '', '', 'Before Dinner', '7:00 PM', '1', '2025-02-01 16:41:05'),
(14, 5, 'After Breakfast', '9:00 AM', '', '', 'After Dinner', '9:00 PM', '1', '2025-02-01 16:41:05'),
(15, 6, 'Before Breakfast', '6:00 AM;7:00 AM', 'Before Lunch', '12:30 PM', '', '', '1', '2025-02-01 16:41:05'),
(16, 6, 'Before Breakfast', '6:00 AM;7:00 AM', 'After Lunch', '2:00 PM', '', '', '1', '2025-02-01 16:41:05'),
(17, 6, 'After Breakfast', '9:00 AM', 'Before Lunch', '12:30 PM', '', '', '1', '2025-02-01 16:41:05'),
(18, 6, 'After Breakfast', '9:00 AM', 'After Lunch', '2:00 PM', '', '', '1', '2025-02-01 16:41:05'),
(19, 7, 'Before Breakfast', '6:00 AM;7:00 AM', 'Before Lunch', '12:30 PM', 'Before Dinner', '7:00 PM', '1', '2025-02-01 16:41:05'),
(20, 7, 'Before Breakfast', '6:00 AM;7:00 AM', 'Before Lunch', '12:30 PM', 'After Dinner', '9:00 PM', '1', '2025-02-01 16:41:05'),
(21, 7, 'Before Breakfast', '6:00 AM;7:00 AM', 'After Lunch', '2:00 PM', 'Before Dinner', '7:00 PM', '1', '2025-02-01 16:41:05'),
(22, 7, 'Before Breakfast', '6:00 AM;7:00 AM', 'After Lunch', '2:00 PM', 'After Dinner', '9:00 PM', '1', '2025-02-01 16:41:05'),
(23, 7, 'After Breakfast', '9:00 AM', 'Before Lunch', '12:30 PM', 'Before Dinner', '7:00 PM', '1', '2025-02-01 16:41:05'),
(24, 7, 'After Breakfast', '9:00 AM', 'Before Lunch', '12:30 PM', 'After Dinner', '9:00 PM', '1', '2025-02-01 16:41:05'),
(25, 7, 'After Breakfast', '9:00 AM', 'After Lunch', '2:00 PM', 'Before Dinner', '7:00 PM', '1', '2025-02-01 16:41:05'),
(26, 7, 'After Breakfast', '9:00 AM', 'After Lunch', '2:00 PM', 'After Dinner', '9:00 PM', '1', '2025-02-01 16:41:05');

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoice_id` int(11) NOT NULL,
  `patient_id` varchar(50) NOT NULL,
  `appointment_id` varchar(50) NOT NULL,
  `bill_type` varchar(50) NOT NULL,
  `category_type` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `concession_type` varchar(50) NOT NULL,
  `concession_percent` decimal(5,2) DEFAULT 0.00,
  `concession_value` decimal(10,2) DEFAULT 0.00,
  `net_amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `balance_amount` decimal(10,2) DEFAULT 0.00,
  `tests` varchar(100) DEFAULT NULL,
  `payment_method` enum('Cash','Card','UPI','Cheque','Online','Other') DEFAULT 'Cash',
  `status` tinyint(1) DEFAULT 1,
  `org_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `refund_reason` varchar(500) DEFAULT NULL,
  `refunded_by` int(11) DEFAULT NULL,
  `refunded_at` datetime DEFAULT NULL,
  `refund_amount` decimal(10,2) DEFAULT NULL,
  `refund_type` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`invoice_id`, `patient_id`, `appointment_id`, `bill_type`, `category_type`, `amount`, `concession_type`, `concession_percent`, `concession_value`, `net_amount`, `paid_amount`, `balance_amount`, `tests`, `payment_method`, `status`, `org_id`, `created_by`, `modified_by`, `created_at`, `modified_at`, `refund_reason`, `refunded_by`, `refunded_at`, `refund_amount`, `refund_type`) VALUES
(1, 'PAT0434', 'A202509290001', 'Consultation', 'Doctor Fee', 500.00, 'percentage', 50.00, 0.00, 250.00, 250.00, 0.00, '', 'Cash', 1, 1, 2, NULL, '2025-09-29 03:56:59', '2025-09-29 03:56:59', NULL, NULL, NULL, NULL, NULL),
(2, 'PAT0434', 'A202509290001', 'Test', 'Test fee', 1090.00, '', 0.00, 522.00, 568.00, 0.00, 0.00, NULL, 'Cash', 1, 1, 2, NULL, '2025-09-29 04:52:55', '2025-09-29 04:52:55', NULL, NULL, NULL, NULL, NULL),
(3, 'PAT0005', 'A202509300001', 'Consultation', 'Doctor Fee', 500.00, '', 0.00, 0.00, 500.00, 500.00, 0.00, '', 'Cash', 1, 1, 2, NULL, '2025-09-30 05:44:04', '2025-09-30 05:44:04', NULL, NULL, NULL, NULL, NULL),
(4, 'PAT0006', 'A202510010001', 'Consultation', 'Doctor Fee', 500.00, '', 0.00, 0.00, 500.00, 500.00, 0.00, '', 'Cash', 1, 1, 2, NULL, '2025-10-01 04:55:29', '2025-10-01 04:55:29', NULL, NULL, NULL, NULL, NULL),
(5, 'PAT0046', 'A202510060001', 'Consultation', 'Doctor Fee', 500.00, 'percentage', 50.00, 0.00, 250.00, 250.00, 0.00, '', 'Cash', 1, 1, 2, NULL, '2025-10-06 11:10:42', '2025-10-06 11:10:42', NULL, NULL, NULL, NULL, NULL),
(6, 'PAT0532', 'A202604150001', 'Consultation', 'Doctor Fee', 500.00, 'percentage', 50.00, 0.00, 250.00, 250.00, 0.00, '', '', 1, 1, 2, NULL, '2026-04-15 04:41:37', '2026-04-15 04:41:37', NULL, NULL, NULL, NULL, NULL),
(7, 'PAT0010', 'A202604150002', 'Consultation', 'Doctor Fee', 500.00, '', 0.00, 0.00, 500.00, 500.00, 0.00, '', '', 1, 1, 2, NULL, '2026-04-15 06:12:05', '2026-04-15 06:12:05', NULL, NULL, NULL, NULL, NULL),
(8, 'PAT0532', 'A202604150001', 'Medicine', 'Medicine fee', 50.00, '', 0.00, 17.00, 33.00, 0.00, 0.00, NULL, 'Cash', 1, 1, 2, NULL, '2026-04-15 10:09:30', '2026-04-15 10:09:30', NULL, NULL, NULL, NULL, NULL),
(9, 'PAT0532', 'A202604150001', 'Medicine', 'Medicine fee', 50.00, '', 0.00, 17.00, 33.00, 0.00, 0.00, NULL, 'Cash', 1, 1, 2, NULL, '2026-04-15 10:09:39', '2026-04-15 10:09:39', NULL, NULL, NULL, NULL, NULL),
(10, 'PAT0532', 'A202604150001', 'Medicine', 'Medicine fee', 50.00, '', 0.00, 0.00, 50.00, 0.00, 0.00, NULL, 'UPI', 1, 1, 2, NULL, '2026-04-15 10:28:22', '2026-04-15 10:28:22', NULL, NULL, NULL, NULL, NULL),
(11, 'PAT0532', 'A202604150001', 'Test', 'Test fee', 800.00, '', 0.00, 0.00, 800.00, 0.00, 0.00, NULL, 'Cash', 1, 1, 2, NULL, '2026-04-15 11:39:15', '2026-04-15 11:39:15', NULL, NULL, NULL, NULL, NULL),
(12, 'PAT0532', 'A202605060001', 'Consultation', 'Doctor Fee', 500.00, '', 0.00, 0.00, 500.00, 500.00, 0.00, '', '', 1, 1, 2, NULL, '2026-05-06 07:03:28', '2026-05-06 07:03:28', NULL, NULL, NULL, NULL, NULL),
(13, 'PAT0532', 'A202605060001', 'Test', 'Test fee', 1400.00, '', 0.00, 700.00, 700.00, 0.00, 0.00, NULL, 'Cash', 1, 1, 2, NULL, '2026-05-06 07:06:24', '2026-05-06 07:06:24', NULL, NULL, NULL, NULL, NULL),
(14, 'PAT0532', 'A202605060001', 'Test', 'Test fee', 1400.00, '', 0.00, 700.00, 700.00, 0.00, 0.00, NULL, '', 1, 1, 2, NULL, '2026-05-06 09:01:50', '2026-05-06 09:01:50', NULL, NULL, NULL, NULL, NULL),
(15, 'PAT0532', 'A202605060001', 'Medicine', 'Medicine fee', 110.00, '', 0.00, 0.00, 110.00, 0.00, 0.00, NULL, '', 1, 1, 2, NULL, '2026-05-06 09:10:51', '2026-05-06 09:10:51', NULL, NULL, NULL, NULL, NULL),
(16, 'PAT0012', 'A202605060002', 'Consultation', 'Doctor Fee', 500.00, '', 0.00, 0.00, 500.00, 500.00, 0.00, '', 'Cash', 1, 1, 2, NULL, '2026-05-06 10:57:43', '2026-05-06 10:57:43', NULL, NULL, NULL, NULL, NULL),
(17, 'PAT0534', 'A202605070001', 'Consultation', 'Doctor Fee', 500.00, '', 0.00, 0.00, 500.00, 500.00, 0.00, '', 'UPI', 0, 1, 2, 2, '2026-05-07 04:49:48', '2026-05-07 06:34:20', 'friend', 2, '2026-05-07 12:04:20', 100.00, 'refund'),
(18, 'PAT0532', 'A202605060001', 'Medicine', 'Medicine fee', 115.00, '', 0.00, 0.00, 115.00, 0.00, 0.00, NULL, '', 1, 1, 10, NULL, '2026-05-07 05:54:06', '2026-05-07 05:54:06', NULL, NULL, NULL, NULL, NULL),
(19, 'PAT0534', 'A202605070001', 'Consultation', 'Doctor Fee', 500.00, '', 0.00, 0.00, 500.00, 500.00, 0.00, '', '', 1, 1, 2, NULL, '2026-05-07 09:44:13', '2026-05-07 09:44:13', NULL, NULL, NULL, NULL, NULL),
(20, 'PAT0116', 'A202605080001', 'Consultation', 'Doctor Fee', 500.00, '', 0.00, 0.00, 500.00, 500.00, 0.00, '', 'UPI', 0, 1, 2, 2, '2026-05-08 00:21:53', '2026-05-08 02:10:01', 'family friend', 2, '2026-05-08 07:40:01', 200.00, 'refund'),
(22, 'PAT0535', 'A202605200001', 'Test', 'Test fee', 1010.00, '', 0.00, 505.00, 505.00, 0.00, 0.00, NULL, 'Cash', 1, 1, 2, NULL, '2026-05-20 09:26:40', '2026-05-20 09:26:40', NULL, NULL, NULL, NULL, NULL),
(23, 'PAT0535', 'A202605200001', 'Test', 'Test fee', 80.00, '', 0.00, 17.00, 63.00, 0.00, 0.00, NULL, 'Cash', 1, 1, 2, NULL, '2026-05-20 09:26:54', '2026-05-20 09:26:54', NULL, NULL, NULL, NULL, NULL),
(24, 'PAT0535', 'A202605200001', 'Medicine', 'Medicine fee', 40.00, 'None', 0.00, 0.00, 40.00, 0.00, 0.00, NULL, 'Cash', 1, 1, 2, NULL, '2026-05-20 09:29:25', '2026-05-20 09:29:25', NULL, NULL, NULL, NULL, NULL),
(25, 'PAT0010', 'A202605200002', 'Consultation', 'Doctor Fee', 200.00, '', 0.00, 0.00, 200.00, 200.00, 0.00, '', 'Cash', 1, 1, 2, NULL, '2026-05-20 10:12:00', '2026-05-20 10:12:00', NULL, NULL, NULL, NULL, NULL),
(26, 'PAT0010', 'A202605200002', 'Consultation', 'Doctor Fee', 300.00, '', 0.00, 0.00, 300.00, 300.00, 0.00, '', 'UPI', 1, 1, 2, NULL, '2026-05-20 10:12:00', '2026-05-20 10:12:00', NULL, NULL, NULL, NULL, NULL),
(27, 'PAT0010', 'A202605200002', 'Test', 'Test fee', 500.00, '', 0.00, 0.00, 500.00, 0.00, 0.00, NULL, 'Cash', 1, 1, 2, NULL, '2026-05-20 10:15:23', '2026-05-20 10:15:23', NULL, NULL, NULL, NULL, NULL),
(28, 'PAT0010', 'A202605200002', 'Test', 'Test fee', 68.00, '', 0.00, 0.00, 68.00, 0.00, 0.00, NULL, 'UPI', 1, 1, 2, NULL, '2026-05-20 10:15:23', '2026-05-20 10:15:23', NULL, NULL, NULL, NULL, NULL),
(29, 'PAT0010', 'A202605200002', 'Medicine', 'Medicine fee', 30.00, 'None', 0.00, 0.00, 30.00, 0.00, 0.00, NULL, 'Cash', 1, 1, 2, NULL, '2026-05-20 10:16:53', '2026-05-20 10:16:53', NULL, NULL, NULL, NULL, NULL),
(30, 'PAT0010', 'A202605200002', 'Medicine', 'Medicine fee', 50.00, 'None', 0.00, 0.00, 50.00, 0.00, 0.00, NULL, 'UPI', 1, 1, 2, NULL, '2026-05-20 10:16:53', '2026-05-20 10:16:53', NULL, NULL, NULL, NULL, NULL),
(31, 'PAT0011', 'A202605200003', 'Consultation', 'Doctor Fee', 500.00, '', 0.00, 0.00, 500.00, 500.00, 0.00, '', 'Cash', 1, 1, 2, NULL, '2026-05-20 11:48:00', '2026-05-20 11:48:00', NULL, NULL, NULL, NULL, NULL),
(32, 'PAT0001', 'A202605210001', 'Consultation', 'Doctor Fee', 300.00, '', 0.00, 0.00, 300.00, 300.00, 0.00, '', 'Cash', 0, 9, 2, 15, '2026-05-21 09:17:31', '2026-05-25 05:57:31', 'requested to reduce', 15, '2026-05-25 11:27:31', 100.00, 'refund'),
(33, 'PAT0001', 'A202605210001', 'Consultation', 'Doctor Fee', 400.00, '', 0.00, 0.00, 400.00, 400.00, 0.00, '', 'UPI', 1, 9, 2, NULL, '2026-05-21 09:17:31', '2026-05-21 09:17:31', NULL, NULL, NULL, NULL, NULL),
(34, 'PAT0001', 'A202605210001', 'Test', 'Test fee', 250.00, '', 0.00, 0.00, 250.00, 0.00, 0.00, NULL, 'Cash', 1, 9, 15, NULL, '2026-05-21 11:34:30', '2026-05-21 11:59:38', NULL, NULL, NULL, NULL, NULL),
(35, 'PAT0001', 'A202605210001', 'Test', 'Test fee', 500.00, '', 0.00, 0.00, 500.00, 0.00, 0.00, NULL, 'UPI', 1, 9, 15, NULL, '2026-05-21 11:34:30', '2026-05-21 11:34:30', NULL, NULL, NULL, NULL, NULL),
(38, 'PAT0001', 'A202605210001', 'Medicine', 'Medicine fee', 360.00, 'None', 0.00, 0.00, 360.00, 0.00, 0.00, NULL, 'UPI', 1, 9, 15, NULL, '2026-05-21 12:25:24', '2026-05-21 12:25:24', NULL, NULL, NULL, NULL, NULL),
(39, 'PAT0002', 'A202605210002', 'Consultation', 'Doctor Fee', 700.00, 'percentage', 20.00, 0.00, 560.00, 560.00, 0.00, '', 'Cash', 1, 9, 13, NULL, '2026-05-21 13:50:35', '2026-05-21 13:50:35', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `in_take_period`
--

CREATE TABLE `in_take_period` (
  `intake_id` int(11) NOT NULL,
  `intake_name` varchar(100) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `created_by` int(11) NOT NULL,
  `create_date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `org_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `in_take_period`
--

INSERT INTO `in_take_period` (`intake_id`, `intake_name`, `status`, `created_by`, `create_date_time`, `org_id`) VALUES
(1, 'After Breakfast', '0', 1, '2025-04-14 07:07:01', 1),
(2, 'After Lunch', '0', 1, '2025-04-14 07:07:01', 1),
(3, 'After Dinner', '0', 1, '2025-04-14 07:07:01', 1),
(4, 'Before Breakfast', '0', 1, '2025-04-14 07:07:01', 1),
(5, 'Before Lunch', '0', 1, '2025-04-14 07:07:01', 1),
(6, 'Before Dinner', '0', 1, '2025-04-14 07:07:01', 1),
(7, 'Before Food', '1', 1, '2025-04-14 07:02:34', 1),
(8, 'After Food', '1', 1, '2025-04-14 07:02:34', 1),
(9, 'Not Applicable', '1', 1, '2025-04-14 07:05:55', 1);

-- --------------------------------------------------------

--
-- Table structure for table `madicine_type`
--

CREATE TABLE `madicine_type` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(255) NOT NULL,
  `c_d_t` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `madicine_type`
--

INSERT INTO `madicine_type` (`type_id`, `type_name`, `c_d_t`, `status`) VALUES
(1, 'Tab', '2023-09-02 10:08:42', '1'),
(2, 'Drug', '2023-09-02 10:08:53', '1'),
(3, 'Syp', '2023-09-02 10:09:30', '1'),
(4, 'Cap', '2023-09-02 10:09:30', '1'),
(5, 'Inj', '2023-09-02 10:09:30', '1');

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `medicine_id` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `medicine_type` varchar(225) NOT NULL,
  `medicine_name` varchar(225) NOT NULL,
  `scientific_name` varchar(225) NOT NULL,
  `dosage` varchar(11) NOT NULL,
  `gst` varchar(225) NOT NULL,
  `price` int(11) NOT NULL,
  `notes` varchar(225) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `created_by` int(11) NOT NULL,
  `modifeid_by` int(11) NOT NULL,
  `c_d_t` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`medicine_id`, `org_id`, `medicine_type`, `medicine_name`, `scientific_name`, `dosage`, `gst`, `price`, `notes`, `status`, `created_by`, `modifeid_by`, `c_d_t`) VALUES
(1, 1, '1', 'APIGAT', 'APIXABAN', '0', '0', 20, 'APIXABAN', '1', 2, 2, '2025-06-16 05:03:21'),
(2, 1, '3', 'ASCORIL +', 'TERBUTALINE + BROMHEXINE', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:50:13'),
(3, 1, '1', 'AUGMENTIN', 'AMOXICILLIN + CLAVULANATE', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:49:55'),
(4, 1, '4', 'BARIVIT', 'MULTIVITAMIN', '0', '0', 0, 'APIXAN', '1', 2, 2, '2023-09-09 06:49:54'),
(5, 1, '1', 'DAPANORM TRIO', 'DAPAGLIFLOZIN + SITAGLIPTIN + METFORMIN', '0', '0', 0, 'APIABA', '1', 2, 2, '2023-09-09 06:49:51'),
(6, 1, '1', 'DAPARYL', 'DAPAGLIFLOZIN', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:49:50'),
(7, 1, '1', 'DILNIP', 'CILINDIPINE', '0', '0', 0, 'APIXABANAPIXABANAPXABAN', '1', 2, 2, '2023-09-09 06:49:48'),
(8, 1, '1', 'ECOSPRIN', 'ASPIRIN', '0', '0', 0, 'APIABAN', '1', 2, 2, '2023-09-09 06:49:46'),
(9, 1, '1', 'ECOSPRIN AV', 'ASPIRIN + ATORVASTATIN', '0', '0', 0, 'APXABAN', '1', 2, 2, '2023-09-09 06:49:43'),
(10, 1, '1', 'EMBETA TM', 'METAPROLOL + TELMISARTAN', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:49:41'),
(11, 1, '1', 'GABACOX - M', 'GABAPENTIN + METHYLCOBALAMIN', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:49:40'),
(12, 1, '1', 'GLOBIRED', 'FERROUS ASCORBATE + FOLIC ACID', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:49:39'),
(13, 1, '1', 'IVABRATCO', 'IVABRADINE', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:46:50'),
(14, 1, '1', 'JBTOR', 'TORSEMIDE', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:46:53'),
(15, 1, '1', 'JBTOR PLUS LS', 'TORSEMIDE + ALDACTONE', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:46:55'),
(16, 1, '1', 'JBTOR PLUS', 'TORSEMIDE + ALDACTONE', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:46:59'),
(17, 1, '1', 'KATZVIT', 'MULTIVITAMIN', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:47:03'),
(18, 1, '1', 'LEVOFLOX', 'LEVOFLOXACIN', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:47:05'),
(19, 1, '1', 'LIPITAS', 'ROSUVASTATIN', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:47:08'),
(20, 1, '4', 'METHYLCOX', 'METHYLCOBALAMIN', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:47:11'),
(21, 1, '1', 'METPURE XL', 'METAPROLOL', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:47:14'),
(22, 1, '1', 'MONIT GTN', 'NITROGLYCERIN', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:47:17'),
(23, 1, '1', 'MONTAIR LC', 'MONTELUKAST + LEVOCETRIZINE', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:47:21'),
(24, 1, '1', 'PROLOMET AM', 'METAPROLOL + AMLODIPINE', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:47:23'),
(25, 1, '1', 'PROLOMET AM', 'METAPROLOL + AMLODIPINE', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:47:26'),
(26, 1, '1', 'PROLOMET - T', 'METAPROLOL + TELMISARTAN', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:47:29'),
(27, 1, '1', 'ROSLAREN - AC', 'ROSUVASTATIN + CLOPIDOGREL + ASPIRIN', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:47:33'),
(28, 1, '1', 'ROSLAREN - F', 'ROSUVASTATIN + FENOFIBRATE', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:47:37'),
(29, 1, '1', 'ROSULIFE - CV', 'ROSUVASTATIN + CLOPIDOGREL', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:47:40'),
(30, 1, '1', 'SACUVAL', 'SACUBITRIL + VALSARTAN', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:47:43'),
(31, 1, '1', 'SUNNY CAL D3', 'CHOLECALCIFEROL', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:47:46'),
(32, 1, '1', 'TELMA', 'TELMISARTAN', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:47:49'),
(33, 1, '1', 'TELMA - H', 'TELMISARTAN', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:47:51'),
(34, 1, '1', 'TELMA - ACT', 'TELMISARTAN + AMLODIPINE + CHLORTHALIDONE', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:47:55'),
(35, 1, '1', 'TICAVIC', 'TICAGRELOR', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:47:58'),
(36, 1, '1', 'VILDARAY - M', 'VILDAGLIPTIN + METFORMIN', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:48:00'),
(37, 1, '1', 'ZORYL - M1', 'GLIMIPERIDE + METFORMIN', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:48:03'),
(38, 1, '1', 'TAB. ZORYL - MV1', 'GLIMIPERIDE + METFORMIN + VOGLIBOSE', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:48:06'),
(39, 1, '1', 'ZORYL - M2', 'GLIMIPERIDE + METFORMIN', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:48:09'),
(40, 1, '1', 'ZORYL - MV2', 'GLIMIPERIDE + METFORMIN + VOGLIBOSE', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:48:12'),
(41, 1, '1', 'TELZOX AM', 'TELMISARTAN + AMLODIPINE', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:48:14'),
(42, 1, '1', 'AMLONG 5', 'AMLODIPINE', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:48:18'),
(43, 1, '1', 'BYLENTA', 'TELMISARTAN', '0', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:48:24'),
(44, 1, '1', 'CLOPILET - A', 'CLOPIDOGREL + ASPIRIN', '2.5MG', '0', 0, 'APIXABAN', '1', 2, 2, '2023-09-09 06:51:46'),
(47, 1, '1', 'ABOTT', 'THYROXINE', '150MG', '', 0, 'FOR THYROID ', '1', 1, 1, '2023-10-09 11:08:40'),
(48, 1, '1', 'ABOTT', 'ACETOMINE', '5MG', '', 0, '', '0', 1, 1, '2023-10-09 11:09:07'),
(49, 2, '1', 'Dolo650', 'Antibiotic', '2.5MG', '', 0, '', '1', 2, 2, '2023-10-16 13:03:11'),
(50, 1, '1', 'Dolo650', 'Antibiotic', '5MG', '', 0, '', '0', 2, 2, '2023-10-17 13:20:46'),
(51, 1, '1', '111', '66', '2.5MG', '', 0, '', '0', 2, 2, '2023-10-17 13:20:39'),
(52, 1, '2', '111', '67', '5MG', '', 0, '', '0', 2, 2, '2023-10-17 13:20:32'),
(53, 1, '1', 'ZORYL - M2', 'GLIMIPERIDE', '2.5MG', '', 0, '', '0', 2, 2, '2023-10-18 06:22:31'),
(54, 1, '2', 'CLOPILET - A', 'CLOPIDOGREL', '2.5MG', '', 0, '', '0', 2, 2, '2023-10-18 06:22:39'),
(55, 1, '1', 'ZORYL - M2', 'GLIMIPERIDE', '5MG', '', 0, '', '0', 2, 2, '2023-10-18 06:24:02'),
(56, 1, '1', 'ZORYL - M2', 'CLOPIDOGREL', '625MG', '', 0, '', '0', 2, 2, '2023-10-18 09:35:39'),
(57, 1, '1', 'Dolo650', 'Paracetamol', '10MG', '', 0, '', '0', 2, 2, '2023-10-18 09:35:35'),
(58, 1, '1', 'agythro mycin', 'Sor throat', '5MG', '', 0, '', '0', 2, 2, '2023-10-18 09:35:31'),
(59, 1, '2', 'CLOPILET - A', 'GLIMIPERIDE', '2.5MG', '', 0, '', '0', 2, 2, '2023-10-18 09:54:08'),
(60, 1, '2', 'CLOPILET - A', 'TELMISARTA', '5MG', '', 0, '', '0', 2, 2, '2023-10-18 09:54:03'),
(61, 2, '1', 'ABOTT', 'THYROXINE', '150MG', '', 0, '', '1', 1, 1, '2023-10-18 11:17:11'),
(62, 2, '2', 'DR REDDY', 'DICLOCIT', '10MG', '', 0, '', '1', 1, 1, '2023-10-18 11:17:11'),
(63, 1, '1', 'CLOPILET - A', 'CLOPIDOGR', '2.5MG', '', 0, '', '1', 2, 2, '2023-10-19 09:09:01'),
(64, 1, '1', 'CLOPILET - A', 'CLOPIDOGREL', '2.5MG', '', 0, '', '1', 1, 1, '2023-10-20 06:22:28'),
(65, 1, '1', 'CLOPILET - A', 'CLOPIDOGREL', '2.5MG', '', 0, '', '1', 1, 1, '2023-10-20 06:23:21'),
(66, 1, '1', 'CLOPILET - A', 'CLOPIDOGREL + ASPIRIN', '2.5MG', '', 0, '', '1', 1, 1, '2023-10-20 06:24:11'),
(67, 1, '1', 'CLOPILET - A', 'CLOPIDOGREL + ASPIRIN', '2.5MG', '', 0, '', '1', 1, 1, '2023-10-20 06:24:11'),
(68, 4, '1', 'CLOPILET - A', 'CLOPIDOGREL + ASPIRIN', '2.5MG', '', 0, '', '1', 6, 6, '2023-10-20 10:30:52'),
(69, 4, '1', 'CLOPILET - B', 'CLOPIDOGREL', '2.5MG', '', 0, '', '1', 6, 6, '2023-10-20 10:31:56'),
(82, 1, '2', 'Paracetamol', 'Acetaminophen', '75/20MG', '', 18, 'test', '0', 2, 2, '2025-09-17 07:47:24'),
(83, 1, 'Tab', 'Paracetamol', 'Paracetamol 500mg', '500 MG', '', 25, '', '1', 2, 2, '2025-09-25 08:52:39'),
(84, 1, 'Cap', 'Amoxicillin', 'Amoxicillin 250mg', '250 MG', '', 50, '', '1', 2, 2, '2025-09-25 08:52:39'),
(85, 1, 'Syp', 'Cough Syrup', 'Dextromethorphan', '100 ML', '', 80, '', '1', 2, 2, '2025-09-25 08:52:39'),
(86, 1, 'Inj', 'Insulin', 'Insulin Regular', '10 ML', '', 120, '', '1', 2, 2, '2025-09-25 08:52:39'),
(87, 1, '1', 'Ibuprofen', 'Ibuprofen 400mg', '400 MG', '', 30, '', '1', 2, 2, '2025-09-25 09:43:39'),
(88, 1, 'Tab', 'Paracetamol', 'Paracetamol 500mg', '600MG', '', 50, '', '0', 2, 2, '2025-09-25 09:43:11'),
(89, 1, '1', 'Paracetamol', 'Paracetamol 500mg', '600MG', '', 50, '', '1', 2, 2, '2025-09-25 09:43:24'),
(90, 1, '1', 'DOLO 50 - (PARACETAMOL IP)', 'DOLO 50 - (PARACETAMOL IP)', '500MG', '', 0, '', '1', 2, 2, '2025-09-29 04:11:46'),
(91, 9, '1', 'Atorvastatin 20mg', 'Atorvastatin Calcium', '20mg', '12', 150, 'Taken at bedtime for cholesterol management', '1', 1, 1, '2026-05-21 06:04:48'),
(92, 9, '1', 'Metoprolol 50mg', 'Metoprolol Succinate', '50mg', '12', 80, 'Beta-blocker for heart rate and BP control', '1', 1, 1, '2026-05-21 06:04:48'),
(93, 9, '1', 'Amlodipine 5mg', 'Amlodipine Besylate', '5mg', '12', 60, 'Calcium channel blocker for hypertension', '1', 1, 1, '2026-05-21 06:04:48'),
(94, 9, '1', 'Aspirin 75mg', 'Acetylsalicylic Acid', '75mg', '12', 30, 'Antiplatelet for cardiac event prevention', '1', 1, 1, '2026-05-21 06:04:48'),
(95, 9, '1', 'Nitroglycerin 0.5mg', 'Glyceryl Trinitrate', '0.5mg', '12', 220, 'Sublingual tablet for acute angina relief', '1', 1, 1, '2026-05-21 06:04:48'),
(96, 9, '1', 'Folic Acid 5mg', 'Pteroylmonoglutamic Acid', '5mg', '5', 50, 'Prenatal supplement to prevent neural defects', '1', 1, 1, '2026-05-21 06:04:48'),
(97, 9, '1', 'Progesterone 200mg', 'Micronized Progesterone', '200mg', '12', 310, 'Hormonal support in luteal phase deficiency', '1', 1, 1, '2026-05-21 06:04:48'),
(98, 9, '1', 'Clomiphene 50mg', 'Clomiphene Citrate', '50mg', '12', 520, 'Ovulation induction for infertility', '1', 1, 1, '2026-05-21 06:04:48'),
(99, 9, '1', 'Metformin 500mg', 'Metformin Hydrochloride', '500mg', '12', 95, 'Insulin sensitizer for PCOS management', '1', 1, 1, '2026-05-21 06:04:48'),
(100, 9, '5', 'Iron Sucrose 100mg', 'Ferric Hydroxide Sucrose', '100mg', '12', 410, 'IV iron therapy for anaemia in pregnancy', '1', 1, 1, '2026-05-21 06:04:48'),
(101, 9, '1', 'Dolo 650', 'Paracetamol IP', '500MG', '', 60, '', '1', 15, 15, '2026-05-21 06:20:45');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `menu_id` int(11) NOT NULL,
  `menu_name` varchar(50) NOT NULL,
  `menu_type` enum('p','s') NOT NULL,
  `menu_order` int(11) NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `menu_web_url` varchar(100) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `create_date_time` datetime NOT NULL,
  `web_class_name` varchar(50) DEFAULT NULL,
  `web_icon` varchar(100) NOT NULL,
  `restricted_to_specializations` varchar(255) DEFAULT NULL,
  `excluded_specializations` varchar(255) DEFAULT NULL,
  `menu_access` enum('1','0') DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `org_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`menu_id`, `menu_name`, `menu_type`, `menu_order`, `status`, `menu_web_url`, `parent_id`, `create_date_time`, `web_class_name`, `web_icon`, `restricted_to_specializations`, `excluded_specializations`, `menu_access`, `created_by`, `modified_by`, `modified_date_time`, `org_id`) VALUES
(1, 'Dashboard', 'p', 1, '1', 'dashboard.php', 0, '2025-04-17 15:00:02', '', 'home', NULL, NULL, '0', 1, 1, '2025-04-17 06:03:06', 0),
(2, 'Administration', 'p', 2, '1', '', 0, '2025-04-17 15:06:56', 'menu-toggle has-dropdown', 'command', NULL, NULL, '0', 1, 6, '2025-04-18 00:43:13', 0),
(3, 'Roles', 's', 1, '1', 'roles.php', 2, '2025-04-17 15:09:55', '', '', NULL, NULL, '0', 1, 1, '2025-04-17 04:09:55', 0),
(4, 'User Registration', 's', 2, '1', 'registration.php', 2, '2025-04-17 15:20:26', '', '', NULL, NULL, '0', 1, 6, '2025-04-17 06:10:35', 0),
(5, 'Profile', 's', 3, '1', 'profile.php', 2, '2025-04-17 15:21:03', '', '', NULL, NULL, '0', 1, 6, '2025-04-17 06:10:35', 0),
(6, 'Master Data ', 'p', 3, '1', '', 0, '2025-04-17 15:23:03', 'menu-toggle has-dropdown', 'grid', NULL, NULL, '0', 1, 6, '2025-04-17 06:10:35', 0),
(7, 'Department', 's', 1, '1', 'department.php', 6, '2025-04-17 15:26:21', '', '', NULL, NULL, '0', 1, 6, '2025-04-17 06:10:35', 0),
(8, 'Specialization', 's', 2, '1', 'Specialization.php', 6, '2025-04-17 15:27:17', '', '', NULL, NULL, '0', 1, 6, '2025-04-17 06:10:35', 0),
(9, 'Doctors', 's', 3, '1', 'doctor.php', 6, '2025-04-17 15:27:56', '', '', NULL, NULL, '0', 1, 6, '2025-04-17 06:10:35', 0),
(10, 'Taxes', 's', 4, '1', 'taxes.php', 6, '2025-04-17 15:29:13', '', '', NULL, NULL, '0', 1, 6, '2025-04-17 06:10:35', 0),
(11, 'Service Management', 'p', 4, '1', '', 0, '2025-04-17 15:30:42', 'menu-toggle has-dropdown', 'plus-square', NULL, NULL, '0', 1, 6, '2025-04-17 06:10:35', 0),
(12, 'Add Services', 's', 1, '1', 'services.php', 11, '2025-04-17 15:31:52', '', '', NULL, NULL, '0', 1, 6, '2025-04-17 06:10:35', 0),
(13, 'Add Tests', 's', 2, '1', 'test.php', 11, '2025-04-17 15:32:43', '', '', NULL, NULL, '0', 1, 6, '2025-04-17 06:10:35', 0),
(14, 'Test Groups', 's', 3, '1', 'testGroup.php', 11, '2025-04-17 15:33:33', '', '', NULL, NULL, '0', 6, 6, '2025-04-17 23:54:42', 0),
(15, 'Medicines', 's', 4, '1', 'medicines.php', 11, '2025-04-17 15:34:20', '', '', NULL, NULL, '0', 6, 6, '2025-04-17 04:34:20', 0),
(16, 'RX Groups', 's', 5, '1', 'rxgroup.php', 11, '2025-04-17 15:35:07', '', '', NULL, NULL, '0', 6, 6, '2025-04-17 04:35:07', 0),
(17, 'Dosage  and Time ', 's', 6, '0', 'dosageandtime', 11, '2025-04-17 15:36:23', '', '', NULL, NULL, '0', 6, 6, '2025-04-22 04:32:59', 0),
(18, 'Appointments and Billing', 'p', 5, '1', '', 0, '2025-04-17 15:38:37', 'menu-toggle has-dropdown', 'clipboard', NULL, NULL, '0', 6, 6, '2025-04-17 04:56:08', 0),
(19, 'Appointments', 's', 1, '1', 'AppointmentOnline.php', 18, '2025-04-17 15:57:20', '', '', NULL, NULL, '0', 6, 6, '2025-04-17 04:57:20', 0),
(20, 'Test BIlling', 's', 3, '1', 'bill.php', 18, '2025-04-17 15:58:47', '', '', NULL, NULL, '0', 6, 1, '2025-09-05 05:30:05', 0),
(21, 'Doctor Portal', 'p', 6, '1', '', 0, '2025-04-17 16:10:09', 'menu-toggle has-dropdown', 'briefcase', NULL, NULL, '0', 6, 6, '2025-04-17 05:19:35', 0),
(22, 'Doctors Time Slots', 's', 1, '1', 'doctorstimeslot.php', 21, '2025-04-17 16:12:16', '', '', NULL, NULL, '0', 6, 6, '2025-04-17 05:12:16', 0),
(23, 'Prescription', 's', 2, '1', 'prescription.php', 21, '2025-04-17 16:13:01', '', '', NULL, NULL, '0', 6, 6, '2025-04-17 05:13:01', 0),
(24, 'Visitor Doctors', 's', 3, '1', 'visitors_doctor.php', 21, '2025-04-17 16:14:11', '', '', NULL, NULL, '0', 6, 6, '2025-04-17 05:14:11', 0),
(25, 'Visitor Doctors Display', 's', 4, '1', 'visitors_doctor_display.php', 21, '2025-04-17 16:15:09', '', '', NULL, NULL, '0', 6, 6, '2025-04-17 05:15:09', 0),
(26, 'Reports and Analytics ', 'p', 7, '1', '', 0, '2025-04-17 16:21:06', 'menu-toggle has-dropdown', 'briefcase', NULL, NULL, '0', 6, 6, '2025-04-17 05:21:06', 0),
(27, 'Feedback  Report', 's', 1, '0', 'feedbackrepport.php', 26, '2025-04-17 16:21:47', '', '', NULL, NULL, '0', 6, 6, '2025-05-02 04:47:02', 0),
(28, 'Prescription Report', 's', 2, '1', 'prescriptionreports.php', 26, '2025-04-17 16:22:52', '', '', NULL, NULL, '0', 6, 6, '2025-04-17 05:22:52', 0),
(29, 'Test Reports', 's', 3, '1', 'TestReport.php', 26, '2025-04-17 16:23:27', '', '', NULL, NULL, '0', 6, 6, '2025-04-17 05:23:27', 0),
(30, 'All Patients', 's', 4, '1', 'AllPatients.php', 26, '2025-04-17 16:24:07', '', '', NULL, NULL, '0', 6, 6, '2025-04-17 05:24:07', 0),
(31, 'Settings', 'p', 8, '1', '', 0, '2025-04-17 16:24:50', 'menu-toggle has-dropdown', 'briefcase', NULL, NULL, '0', 6, 6, '2025-04-17 05:24:50', 0),
(32, 'Print Sizes', 's', 1, '1', 'billsizes.php', 31, '2025-04-17 16:25:24', '', '', NULL, NULL, '0', 6, 6, '2025-04-17 05:25:24', 0),
(33, 'Menus', 's', 4, '1', 'menus.php', 2, '2025-04-25 12:30:10', '', '', NULL, NULL, '0', 1, 1, '2025-04-25 07:00:10', 0),
(34, 'Organization', 's', 5, '1', 'organization.php', 2, '2025-04-25 12:31:32', '', '', NULL, NULL, '0', 1, 1, '2025-04-25 07:01:32', 0),
(35, 'Appointment Reports', 's', 1, '1', 'appointmentreports.php', 26, '2025-05-02 10:16:45', '', '', NULL, NULL, '0', 1, 1, '2025-05-02 04:46:45', 0),
(36, 'IP Test Reports', 's', 10, '1', 'inptestreports.php', 45, '2025-05-29 09:48:04', '', '', NULL, NULL, '0', 1, 1, '2025-06-25 11:28:32', 0),
(37, 'Patient Registration', 's', 3, '1', 'inPatientRegistration.php', 44, '2025-05-29 09:50:20', '', '', NULL, NULL, '0', 1, 1, '2025-06-03 09:56:09', 0),
(38, 'Vitals Entry', 's', 1, '1', 'inpvitals.php', 45, '2025-05-29 09:51:20', '', '', NULL, NULL, '0', 1, 1, '2025-07-07 10:36:22', 0),
(40, 'Procedures', 's', 4, '1', 'inpprocedures.php', 45, '2025-06-02 10:40:00', '', '', NULL, NULL, '0', 1, 1, '2025-07-07 10:37:55', 0),
(42, 'IP Master Data', 'p', 11, '1', 'ipmasterdata.php', 0, '2025-06-03 11:13:38', 'menu-toggle has-dropdown', 'grid', NULL, NULL, '0', 1, 1, '2025-07-17 10:35:10', 0),
(43, 'IP Service Management', 'p', 12, '1', 'ipservices.php', 0, '2025-06-03 11:16:55', 'menu-toggle has-dropdown', 'plus-square', NULL, NULL, '0', 1, 1, '2025-07-17 10:35:14', 0),
(44, 'Admission & Registration', 'p', 13, '1', 'ipregistration.php', 0, '2025-06-03 11:20:19', 'menu-toggle has-dropdown', 'clipboard', NULL, NULL, '0', 1, 1, '2025-07-17 10:35:29', 0),
(45, 'Clinical Management', 'p', 15, '1', 'ipdoctorportal.php', 0, '2025-06-03 11:21:49', 'menu-toggle has-dropdown', '', NULL, NULL, '0', 1, 1, '2025-07-17 10:35:54', 0),
(46, 'Reports & Analytics', 'p', 19, '1', 'inpreports.php', 0, '2025-06-03 11:22:23', 'menu-toggle has-dropdown', '', NULL, NULL, '0', 1, 1, '2025-07-17 10:36:20', 0),
(47, 'Add Medical Services', 's', 1, '1', 'medical_services.php', 43, '2025-06-03 11:46:06', '', '', NULL, NULL, '0', 1, 1, '2025-06-03 06:17:07', 0),
(48, 'Add Procedures', 's', 4, '1', 'procedure.php', 43, '2025-06-03 11:46:55', '', '', NULL, NULL, '0', 1, 1, '2025-06-16 07:01:49', 0),
(49, 'Assign Beds', 's', 2, '1', 'InpCreateBeds.php', 43, '2025-06-03 11:51:28', '', '', NULL, NULL, '0', 1, 1, '2025-06-16 07:12:51', 0),
(50, 'Add Basic IP Data', 's', 2, '1', 'inp_basicIPMenus.php', 42, '2025-06-03 11:52:46', '', '', NULL, NULL, '0', 1, 1, '2025-06-13 06:30:35', 0),
(51, 'Add Schemes', 's', 5, '1', 'schememanagement.php', 43, '2025-06-03 12:11:39', '', '', NULL, NULL, '0', 1, 1, '2025-06-16 07:02:11', 0),
(52, 'Add Packages', 's', 3, '1', 'packages.php', 43, '2025-06-03 12:13:15', '', '', NULL, NULL, '0', 1, 1, '2025-06-16 07:01:25', 0),
(53, 'Discharge & Summary', 'p', 17, '1', 'test.php', 0, '2025-06-03 16:47:01', 'menu-toggle has-dropdown', '', NULL, NULL, '0', 1, 1, '2025-07-17 10:36:07', 0),
(54, 'Final Prescription', 's', 7, '1', 'inp_prescription_entry.php', 45, '2025-06-03 16:47:51', '', '', NULL, NULL, '0', 1, 1, '2025-07-07 10:38:48', 0),
(55, 'Prescription', 's', 6, '1', 'inp_medicineentry.php', 45, '2025-06-03 16:48:42', '', '', NULL, NULL, '0', 1, 1, '2025-07-07 10:38:27', 0),
(56, 'Doctor Allocation', 's', 2, '1', 'inp_doctorallocation.php', 82, '2025-06-03 16:49:23', '', '', NULL, NULL, '0', 1, 1, '2025-07-07 10:27:48', 0),
(57, 'Room & Bed Assignment', 's', 1, '1', 'inp_roomallocation.php', 82, '2025-06-03 16:53:57', '', '', NULL, NULL, '0', 1, 1, '2025-07-07 10:27:14', 0),
(58, 'Investigations', 's', 3, '1', 'inp_investigation.php', 45, '2025-06-03 16:54:45', '', '', NULL, NULL, '0', 1, 1, '2025-07-07 10:37:30', 0),
(59, 'Clinical Supervision', 's', 5, '1', 'inp_clinicalSupervision.php', 45, '2025-06-03 16:59:16', '', '', NULL, NULL, '0', 1, 1, '2025-07-07 10:38:03', 0),
(60, 'Add Staff and Shifts', 's', 6, '1', 'inp_staffandshift_details.php', 43, '2025-06-03 17:02:15', '', '', NULL, NULL, '0', 1, 1, '2025-06-16 07:02:17', 0),
(61, 'Doctors Notes', 's', 10, '1', 'IPDoctorsNote.php', 45, '2025-06-03 17:03:57', '', '', NULL, NULL, '0', 1, 1, '2025-07-15 09:14:41', 0),
(62, 'Billing Management', 's', 1, '1', 'IPBillingManagement.php', 83, '2025-06-03 17:04:37', '', '', NULL, NULL, '0', 1, 1, '2025-07-07 10:46:33', 0),
(63, 'Doctor & Staff Logs', 's', 3, '1', 'inp_clinicalLogs.php', 82, '2025-06-03 16:47:51', '', '', NULL, NULL, '0', 1, 1, '2025-07-07 10:28:22', 0),
(64, 'IP Investigations Reports', 's', 11, '1', 'inp_investigationreport.php', 46, '2025-06-05 13:13:34', '', '', NULL, NULL, '0', 1, 1, '2025-06-27 04:32:12', 0),
(65, 'IP Final Prescription Reports', 's', 10, '1', 'inp_prescriptionreports.php', 46, '2025-06-05 13:15:35', '', '', NULL, NULL, '0', 1, 1, '2025-06-27 04:31:54', 0),
(66, 'IP Billing Reports', 's', 12, '1', 'patientinvoice.php', 46, '2025-06-06 13:01:11', '', '', NULL, NULL, '0', 1, 1, '2025-06-27 04:32:20', 0),
(67, 'IP Discharge Summary', 's', 14, '1', 'inp_discharge_summary.php', 46, '2025-06-06 17:11:34', '', '', NULL, NULL, '0', 1, 1, '2025-06-27 04:33:18', 0),
(68, 'In-Patient Census Dashboard ', 'p', 9, '1', 'ipdashboard.php', 0, '2025-04-17 15:00:02', '', 'home', NULL, NULL, '0', 1, 1, '2025-07-17 10:30:38', 0),
(69, 'Add Rooms', 's', 1, '1', 'addroomsandbeds.php', 43, '2025-06-12 11:26:18', '', '', NULL, NULL, '0', 1, 1, '2025-06-16 07:00:58', 0),
(70, 'Add Room Management Data', 's', 1, '1', 'inp_roomManagementMenus.php', 42, '2025-06-13 12:02:32', '', '', NULL, NULL, '0', 1, 1, '2025-06-13 06:32:32', 0),
(71, 'IP Clinical Supervision Reports', 's', 13, '1', 'inp_clinical_supervision_reports.php', 46, '2025-06-18 10:39:45', '', '', NULL, NULL, '0', 1, 1, '2025-06-27 04:32:40', 0),
(72, 'In-Patient Flow Reports', 's', 15, '1', 'inp_patient_overall_info.php', 46, '2025-06-26 10:14:20', '', '', NULL, NULL, '0', 1, 1, '2025-07-15 09:30:39', 0),
(73, 'Registration Reports', 's', 1, '1', 'inpregistrationreports.php', 46, '2025-06-27 09:49:57', '', '', NULL, NULL, '0', 1, 1, '2025-06-27 04:44:35', 0),
(74, 'Vitals Reports', 's', 2, '1', 'inpvitalsreports.php', 46, '2025-06-27 09:50:50', '', '', NULL, NULL, '0', 1, 1, '2025-06-27 04:44:27', 0),
(75, 'Doctor Allocation Reports', 's', 3, '1', 'inpdoctorallocationreports.php', 46, '2025-06-27 09:51:31', '', '', NULL, NULL, '0', 1, 1, '2025-06-27 04:43:18', 0),
(76, 'Schemes Reports', 's', 4, '1', 'inpschemesreports.php', 46, '2025-06-27 09:52:07', '', '', NULL, NULL, '0', 1, 1, '2025-06-27 04:44:21', 0),
(77, 'Room Allocation Reports', 's', 5, '1', 'inproomallocationreports.php', 46, '2025-06-27 09:53:57', '', '', NULL, NULL, '0', 1, 1, '2025-06-27 04:43:31', 0),
(78, 'Prescription Reports', 's', 6, '1', 'inpprescriptionreports.php', 46, '2025-06-27 09:55:08', '', '', NULL, NULL, '0', 1, 1, '2025-06-27 04:43:42', 0),
(79, 'Procedures Reports', 's', 7, '1', 'inpproceduresreports.php', 46, '2025-06-27 09:56:10', '', '', NULL, NULL, '0', 1, 1, '2025-06-27 04:43:55', 0),
(80, 'Doctors Note Reports', 's', 8, '1', 'inpdoctorsnotereports.php', 46, '2025-06-27 09:57:18', '', '', NULL, NULL, '0', 1, 1, '2025-06-27 04:44:03', 0),
(81, 'Doctor Logs and Staff logs Reports', 's', 9, '1', 'inpdoctorandstafflogsreports.php', 46, '2025-06-27 09:59:25', '', '', NULL, NULL, '0', 1, 1, '2025-06-27 09:22:18', 0),
(82, 'Ward & Room Management', 'p', 14, '1', 'test.php', 0, '2025-07-07 15:55:03', 'menu-toggle has-dropdown', '', NULL, NULL, '0', 1, 1, '2025-07-17 10:35:50', 0),
(83, 'Billing & Insurance', 'p', 16, '1', 'test.php', 0, '2025-07-07 16:11:10', 'menu-toggle has-dropdown', '', NULL, NULL, '0', 1, 1, '2025-07-17 10:36:03', 0),
(84, 'Patient Discharge Process', 's', 1, '1', 'inpdischarge.php', 53, '2025-07-07 16:43:36', '', '', NULL, NULL, '0', 1, 1, '2025-07-07 11:13:36', 0),
(85, 'Hospital Resources Utilization', 'p', 18, '1', 'test.php', 0, '2025-07-08 13:12:02', 'menu-toggle has-dropdown', '', NULL, NULL, '0', 1, 1, '2025-07-17 10:36:14', 0),
(86, 'Equipment Utilizations', 's', 1, '1', 'inp_equipment_utilization.php', 85, '2025-07-08 13:13:23', '', '', NULL, NULL, '0', 1, 1, '2025-07-08 07:43:23', 0),
(87, 'Equipment Overall Utilization  ', 's', 17, '1', 'equipmentoverallusagereport.php', 46, '2025-07-08 17:18:44', '', '', NULL, NULL, '0', 1, 1, '2025-07-11 08:55:27', 0),
(88, 'Incident Reporting', 's', 9, '1', 'incident_report_form.php', 45, '2025-07-10 15:51:38', '', '', NULL, NULL, '0', 1, 1, '2025-07-10 10:21:38', 0),
(89, 'Incident Reporting Data', 's', 18, '1', 'incident_report_datareport.php', 46, '2025-07-10 15:53:34', '', '', NULL, NULL, '0', 1, 1, '2025-07-11 08:55:13', 0),
(91, 'Consumables Usage Report', 's', 19, '1', 'consumable_usage_reports.php', 46, '2025-07-11 16:31:23', '', '', NULL, NULL, '0', 1, 1, '2025-07-11 11:03:06', 0),
(92, 'Progress Notes', 's', 2, '1', 'inp_progress_notes_page.php', 45, '2025-07-14 09:59:20', '', '', NULL, NULL, '0', 1, 1, '2025-07-15 09:14:35', 0),
(93, 'Progress Notes Report', 's', 19, '1', 'inp_progressnotereports.php', 46, '2025-07-14 10:00:46', '', '', NULL, NULL, '0', 1, 1, '2025-07-14 04:30:46', 0),
(94, 'Staff Attendance Reports', 's', 20, '1', 'inp_staffreportspage.php', 46, '2025-07-14 10:01:59', '', '', NULL, NULL, '0', 1, 1, '2025-07-14 04:31:59', 0),
(95, 'Insurance Schemes', 's', 2, '1', 'inp_schemes.php', 83, '2025-07-14 14:51:13', '', '', NULL, NULL, '1', 1, 2, '2025-09-12 09:17:56', 0),
(96, 'Add Consumables Stock', 's', 7, '1', 'inp_consumable.php', 43, '2025-07-14 15:55:17', '', '', NULL, NULL, '0', 1, 1, '2025-07-14 10:25:17', 0),
(97, 'Consumables Usage', 's', 2, '1', 'consumable.php', 85, '2025-07-14 15:56:14', '', '', NULL, NULL, '0', 1, 1, '2025-07-14 10:27:45', 0),
(98, 'Over-all Inpatients Report', 's', 21, '1', 'overall_inpatients_reports.php', 46, '2025-07-17 09:52:45', '', '', NULL, NULL, '0', 1, 1, '2025-07-17 04:22:45', 0),
(99, 'KPI & Executive Dashboard', 'p', 10, '1', 'kpiexe_dashboard.php', 0, '2025-07-17 10:51:41', '', '', NULL, NULL, '0', 1, 1, '2025-07-17 10:35:01', 0),
(100, 'Add Concession', 's', 5, '1', 'concession.php', 6, '2025-09-05 10:55:28', '', '', NULL, NULL, '0', 1, 1, '2025-09-05 05:25:28', 0),
(101, 'Receptionist Display', 's', 2, '1', 'receptionist.php', 18, '2025-09-05 10:59:25', '', '', NULL, NULL, '0', 1, 1, '2025-09-05 05:29:25', 0),
(102, 'Audit Logs', 's', 5, '0', 'audit_log', 26, '2025-09-15 17:30:38', '', '', NULL, NULL, '0', 1, 1, '2025-09-16 00:06:11', 0),
(103, 'Audit Logs', 's', 6, '1', 'audit_log.php', 26, '2025-09-16 05:26:36', '', '', NULL, NULL, '0', 1, 1, '2025-09-15 23:56:36', 0),
(104, 'Lab Test Reports', 's', 7, '1', 'op_lab_util.php', 26, '2025-09-16 05:29:09', '', '', NULL, NULL, '0', 1, 1, '2025-09-16 00:23:13', 0),
(105, 'Medicines Report', 's', 8, '1', 'op_rx_patterns.php', 26, '2025-09-16 05:33:24', '', '', NULL, NULL, '0', 1, 1, '2025-09-16 00:03:24', 0),
(106, 'OP Revenue Report', 's', 9, '1', 'RevenueReport.php', 26, '2025-09-16 05:34:26', '', '', NULL, NULL, '0', 1, 1, '2025-09-16 13:44:46', 0),
(107, 'Patients Outcome Report', 's', 10, '1', 'op_patient_outcome.php', 26, '2025-09-16 06:01:15', '', '', NULL, NULL, '0', 1, 1, '2025-09-16 00:31:30', 0),
(108, 'Appointment Drop Report', 's', 11, '1', 'NoShowCancellationReport.php', 26, '2025-09-16 13:13:29', '', '', NULL, NULL, '0', 1, 1, '2025-09-16 09:39:46', 0),
(109, 'OP Appointments Report', 's', 12, '1', 'OPAppointmentsReport.php', 26, '2025-09-16 13:14:10', '', '', NULL, NULL, '0', 1, 1, '2025-09-16 07:44:10', 0),
(110, 'Patient Waiting Report', 's', 13, '1', 'PatientWaitingReport.php', 26, '2025-09-16 13:15:14', '', '', NULL, NULL, '0', 1, 1, '2025-09-16 07:45:14', 0),
(111, 'Upload Patient Files', 's', 4, '1', 'patienthistory.php', 18, '2025-09-18 17:02:39', '', '', NULL, NULL, '0', 1, 1, '2025-09-18 11:32:39', 0),
(112, 'Daily Reports', 's', 14, '1', 'dailyreports.php', 26, '2025-09-19 15:04:26', '', '', NULL, NULL, '0', 1, 1, '2025-09-19 09:34:26', 0),
(113, 'Medicine Billing', 's', 5, '1', 'medicine_bill.php', 18, '2026-04-15 15:37:14', '', '', NULL, NULL, '0', 1, 1, '2026-04-15 10:07:14', 0),
(114, '2D Echo Report', 's', 1, '1', 'echo_report.php', 115, '2026-04-16 10:00:00', '', '', NULL, NULL, '0', 1, 1, '2026-04-16 03:21:15', 0),
(115, 'Echo Report', 'p', 20, '1', '', 0, '2026-04-16 10:00:00', 'menu-toggle has-dropdown', 'activity', NULL, NULL, '0', 1, 1, '2026-04-16 04:30:00', 0),
(116, 'Gynaec Prescription', 's', 5, '1', 'gynaec_prescription.php', 21, '2026-04-16 10:00:00', '', '', NULL, NULL, '0', 1, 1, '2026-04-16 04:04:22', 0),
(117, 'Referral Report', 's', 15, '1', 'referrals.php', 26, '0000-00-00 00:00:00', 'nav-link', 'share-2', NULL, NULL, '0', 0, 0, '2026-05-06 15:49:54', 0),
(118, 'Refunds & Cancellations', 's', 6, '1', 'refunds.php', 18, '0000-00-00 00:00:00', NULL, '', NULL, NULL, '0', 0, 0, '2026-05-07 06:19:35', 0),
(119, 'Billing Report', 's', 10, '1', 'billing_report.php', 26, '0000-00-00 00:00:00', NULL, '', NULL, NULL, '0', 0, 0, '2026-05-07 06:19:35', 0);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `channel` varchar(64) NOT NULL,
  `username` varchar(64) NOT NULL,
  `body` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `multi_doctortimeslots`
--

CREATE TABLE `multi_doctortimeslots` (
  `multi_id` int(11) NOT NULL,
  `doctorName_registrationNumber` int(11) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `selectedDays` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `modify_by` int(11) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `create_date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `org_id` int(11) NOT NULL,
  `doctors_time_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `multi_doctortimeslots`
--

INSERT INTO `multi_doctortimeslots` (`multi_id`, `doctorName_registrationNumber`, `from_date`, `to_date`, `selectedDays`, `created_by`, `modify_by`, `status`, `create_date_time`, `org_id`, `doctors_time_id`) VALUES
(1, 5, '2025-09-25', '2025-09-30', '1,2,3,4,5', 10, 10, '1', '2025-09-25 11:41:01', 1, 0),
(2, 1, '2025-09-26', '2025-09-29', '1,2,3,4', 12, 12, '1', '2025-09-25 11:42:50', 1, 0),
(3, 1, '2025-10-06', '2025-11-27', '1,2,3,4,5', 2, 2, '1', '2025-10-06 11:09:35', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `multi_doctortimeslots2`
--

CREATE TABLE `multi_doctortimeslots2` (
  `multi_time_id` int(11) NOT NULL,
  `multi_id` int(11) NOT NULL,
  `start_time` varchar(255) NOT NULL,
  `end_time` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `modify_by` int(11) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `create_date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `org_id` int(11) NOT NULL,
  `doctors_time_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `multi_doctortimeslots2`
--

INSERT INTO `multi_doctortimeslots2` (`multi_time_id`, `multi_id`, `start_time`, `end_time`, `created_by`, `modify_by`, `status`, `create_date_time`, `org_id`, `doctors_time_id`) VALUES
(1, 1, '17:10', '23:10', 10, 10, '1', '2025-09-25 11:41:01', 1, 0),
(2, 2, '18:12', '22:12', 12, 12, '1', '2025-09-25 11:42:50', 1, 0),
(3, 3, '16:39', '23:39', 2, 2, '1', '2025-10-06 11:09:35', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `organization`
--

CREATE TABLE `organization` (
  `org_id` int(11) NOT NULL,
  `organization_name` varchar(255) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `gstNumber` varchar(20) NOT NULL,
  `tanNumber` varchar(13) NOT NULL,
  `longitude` double NOT NULL,
  `latitude` double NOT NULL,
  `address` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `logo_without_text` varchar(250) NOT NULL,
  `org_stamp` varchar(255) NOT NULL DEFAULT '',
  `user_limit` varchar(125) NOT NULL,
  `opipaccess` varchar(100) NOT NULL,
  `status` enum('1','0','2') NOT NULL DEFAULT '1',
  `created_date_time` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organization`
--

INSERT INTO `organization` (`org_id`, `organization_name`, `contact`, `email`, `description`, `gstNumber`, `tanNumber`, `longitude`, `latitude`, `address`, `logo`, `logo_without_text`, `org_stamp`, `user_limit`, `opipaccess`, `status`, `created_date_time`, `created_by`, `modified_by`) VALUES
(1, 'Dr Pandas Heart Care', '8897355655', 'pandas@gmail.com', 'A single stop for complete heart care under a specialist, who has a skillset of cardiologist and a soul of physician. All your queries, problems and issues are dealt with utmost care, giving you the time you deserve.', '0SJEGFWJEKDHTYU', 'GSANDSFQGH', 83.3041052, 17.7324576, 'Chambers 1 & 5, Sr Krishna Multispecialty Clinic, opposite Task Force Office, near A S Raja Circle, Sector 10, MVP Colony, Visakhapatnam, Andhra Pradesh 530017', '1_logoFile1.jpg', '1_logoFile2.jpg', '', '5', 'OP', '1', '2023-10-16 19:13:52', 1, 1),
(2, 'SV HEALTH CARE', '6302669660', 'sv@gmail.com', 'A dedicated service provider to the poor and needy who are in need of medical  help', 'SV12345678874563', 'SV12345677', 48.3, 47.3, 'GOPALAPATNAM\nVISAKHAPATNAM', '2.jpeg', '', '', '2', '', '1', '2023-10-18 14:25:59', 1, 6),
(3, 'Dr Jayas EXULT Aesthetic Clinic', '7095678678', 'drjayaderma@gmail.com', 'Exult Clinics were established in 2015, by Dr. M Srinivas Rao MS, M Ch -Plastic & Cosmetic Surgery, and Dr. Jayalakshmi MBBS DDVL- Dermatologist. In addition to M Ch, Dr. Srinivas has attained Diploma in AAAM (American Academy of Aesthetic Medicine) Cours', '37AAHCD4602R1Z8', 'VPND02136B', 83.3176291400227, 17.7239855337674, 'Dr Jaya’s Exult Aesthetic Clinic\n10-2-B2 Siri Puram Opp Dutt Island building lane besides Eleven Restaurant, ', '3.jpg', '', '', '1', '', '1', '2023-10-20 12:42:48', 1, 6),
(8, 'Vulcantechs', '7032760273', 'durga@gmail.com', 'employee', '12345678912345E', 'JZCPK4491E', 83.3176291400227, 17.7239855337674, 'testing', '4.png', '', '', '3', 'OP', '1', '2025-09-23 10:25:51', 1, 1),
(9, 'ABC Hospitals', '6302669661', 'abc@gmail.com', 'multi specialty hospital ', '12345678912345K', 'JZCPK4491H', 83.3176291400228, 17.7239855337678, 'near beach road', '9_logoFile1.jpg', '9_logoFile2.jpg', '9_stamp.png', '5', 'OP', '1', '2026-05-21 10:39:43', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `pagessize`
--

CREATE TABLE `pagessize` (
  `size_id` int(11) NOT NULL,
  `size_name` varchar(225) NOT NULL,
  `w_size` varchar(225) NOT NULL,
  `h_size` varchar(225) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `create_date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pagessize`
--

INSERT INTO `pagessize` (`size_id`, `size_name`, `w_size`, `h_size`, `status`, `create_date_time`) VALUES
(2, 'Letter', '215.9mm', '279.4mm', '1', '2023-10-21 11:28:25'),
(3, 'Tabloid', '279.4mm', '431.8mm', '1', '2023-10-21 11:29:04'),
(4, 'Legal', '216mm', '356mm', '1', '2023-10-21 11:27:57'),
(5, 'Statement', '139.7mm', '215.9mm', '1', '2023-10-21 11:30:34'),
(6, 'Executive', '6in', '9in', '1', '2023-10-21 11:30:54'),
(7, 'A0', '841mm', '1189mm', '1', '2023-10-21 11:25:46'),
(8, 'A1', '594mm', '841mm', '1', '2023-10-21 11:27:17'),
(9, 'A2', '420mm', '594mm', '1', '2023-10-21 11:27:29'),
(10, 'A3', '297mm', '420mm', '1', '2023-10-21 11:27:43'),
(11, 'A4', '210mm', '297mm', '1', '2023-10-21 11:25:28'),
(12, 'A5', '148mm', '210mm', '1', '2023-10-21 11:29:21'),
(13, 'B4(JIS)', '257mm', '364mm', '1', '2023-10-21 11:31:33'),
(14, 'B5(JIS)', '182mm', '257mm', '1', '2023-10-21 11:31:53');

-- --------------------------------------------------------

--
-- Table structure for table `pasthistory_template`
--

CREATE TABLE `pasthistory_template` (
  `ph_id` int(11) NOT NULL,
  `template_name` varchar(225) NOT NULL,
  `template_data` varchar(225) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `org_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_medicine_billing`
--

CREATE TABLE `patient_medicine_billing` (
  `medicine_billing_id` int(11) NOT NULL,
  `patient_id` varchar(250) NOT NULL,
  `appointment_id` varchar(250) NOT NULL,
  `prescription_id` int(11) DEFAULT NULL,
  `medicine_details` longtext NOT NULL,
  `advice` longtext DEFAULT NULL,
  `personal_note` longtext DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `purchase_source` varchar(50) NOT NULL DEFAULT 'Hospital Pharmacy',
  `payment_method` varchar(200) DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  `org_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `transaction_number` varchar(100) DEFAULT NULL,
  `transaction_amount` decimal(10,2) DEFAULT NULL,
  `cash_amount` decimal(10,2) DEFAULT NULL,
  `refund_reason` varchar(500) DEFAULT NULL,
  `refunded_by` int(11) DEFAULT NULL,
  `refunded_at` datetime DEFAULT NULL,
  `refund_amount` decimal(10,2) DEFAULT NULL,
  `refund_type` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_medicine_billing`
--

INSERT INTO `patient_medicine_billing` (`medicine_billing_id`, `patient_id`, `appointment_id`, `prescription_id`, `medicine_details`, `advice`, `personal_note`, `total_amount`, `discount`, `net_amount`, `purchase_source`, `payment_method`, `status`, `org_id`, `created_by`, `created_at`, `transaction_number`, `transaction_amount`, `cash_amount`, `refund_reason`, `refunded_by`, `refunded_at`, `refund_amount`, `refund_type`) VALUES
(1, 'PAT0532', 'A202604150001', 5, '[{\"medicine_id\":9,\"medicine_name\":\"ECOSPRIN AV - (ASPIRIN + ATORVASTATIN)\",\"type_text\":\"Tab\",\"unit_text\":\"10\\/25MG\",\"dosage_text\":\"0-1-1\",\"when_text\":\"After Food\",\"time_text\":\"0-2PM-9PM\",\"duration_value\":4,\"duration\":\"Days\",\"notes\":\"\",\"price\":20,\"discount\":5,\"final_amount\":15},{\"medicine_id\":12,\"medicine_name\":\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\",\"type_text\":\"Tab\",\"unit_text\":\"10\\/25MG\",\"dosage_text\":\"0-1-1\",\"when_text\":\"Not Applicable\",\"time_text\":\"0-2PM-9PM\",\"duration_value\":4,\"duration\":\"Days\",\"notes\":\"\",\"price\":30,\"discount\":12,\"final_amount\":18}]', '', '', 50.00, 17.00, 33.00, 'Hospital Pharmacy', 'Cash', 1, 1, 2, '2026-04-15 10:09:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'PAT0532', 'A202604150001', 5, '[{\"medicine_id\":9,\"medicine_name\":\"ECOSPRIN AV - (ASPIRIN + ATORVASTATIN)\",\"type_text\":\"Tab\",\"unit_text\":\"10\\/25MG\",\"dosage_text\":\"0-1-1\",\"when_text\":\"After Food\",\"time_text\":\"0-2PM-9PM\",\"duration_value\":4,\"duration\":\"Days\",\"notes\":\"\",\"price\":20,\"discount\":5,\"final_amount\":15},{\"medicine_id\":12,\"medicine_name\":\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\",\"type_text\":\"Tab\",\"unit_text\":\"10\\/25MG\",\"dosage_text\":\"0-1-1\",\"when_text\":\"Not Applicable\",\"time_text\":\"0-2PM-9PM\",\"duration_value\":4,\"duration\":\"Days\",\"notes\":\"\",\"price\":30,\"discount\":12,\"final_amount\":18}]', '', '', 50.00, 17.00, 33.00, 'Hospital Pharmacy', 'Cash', 1, 1, 2, '2026-04-15 10:09:39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'PAT0532', 'A202604150001', 5, '[{\"medicine_id\":9,\"medicine_name\":\"ECOSPRIN AV - (ASPIRIN + ATORVASTATIN)\",\"type_text\":\"Tab\",\"unit_text\":\"10\\/25MG\",\"dosage_text\":\"0-1-1\",\"when_text\":\"After Food\",\"time_text\":\"0-2PM-9PM\",\"duration_value\":4,\"duration\":\"Days\",\"notes\":\"\",\"price\":30,\"discount\":0,\"final_amount\":30,\"purchase_source\":\"Outside Pharmacy\"},{\"medicine_id\":12,\"medicine_name\":\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\",\"type_text\":\"Tab\",\"unit_text\":\"10\\/25MG\",\"dosage_text\":\"0-1-1\",\"when_text\":\"Not Applicable\",\"time_text\":\"0-2PM-9PM\",\"duration_value\":4,\"duration\":\"Days\",\"notes\":\"\",\"price\":50,\"discount\":0,\"final_amount\":50,\"purchase_source\":\"Hospital Pharmacy\"}]', '', '', 80.00, 0.00, 80.00, 'Mixed', 'UPI', 1, 1, 2, '2026-04-15 10:28:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'PAT0532', 'A202605060001', 7, '[{\"medicine_id\":11,\"medicine_name\":\"GABACOX - M - (GABAPENTIN + METHYLCOBALAMIN)\",\"type_text\":\"Tab\",\"unit_text\":\"10\\/25MG\",\"dosage_text\":\"0-0-1\",\"when_text\":\"After Food\",\"time_text\":\"0-0-9PM\",\"duration_value\":4,\"duration\":\"Days\",\"notes\":\"\",\"price\":50,\"discount\":0,\"final_amount\":50,\"purchase_source\":\"Hospital Pharmacy\"},{\"medicine_id\":10,\"medicine_name\":\"EMBETA TM - (METAPROLOL + TELMISARTAN)\",\"type_text\":\"Tab\",\"unit_text\":\"50MG\",\"dosage_text\":\"0-1-1\",\"when_text\":\"After Food\",\"time_text\":\"0-2PM-9PM\",\"duration_value\":4,\"duration\":\"Days\",\"notes\":\"\",\"price\":60,\"discount\":0,\"final_amount\":60,\"purchase_source\":\"Hospital Pharmacy\"}]', '', '', 110.00, 0.00, 110.00, 'Hospital Pharmacy', 'Both (Cash + UPI)', 1, 1, 2, '2026-05-06 09:10:51', '34567', 60.00, 50.00, NULL, NULL, NULL, NULL, NULL),
(5, 'PAT0532', 'A202605060001', 7, '[{\"medicine_id\":11,\"medicine_name\":\"GABACOX - M - (GABAPENTIN + METHYLCOBALAMIN)\",\"type_text\":\"Tab\",\"unit_text\":\"10\\/25MG\",\"dosage_text\":\"0-0-1\",\"when_text\":\"After Food\",\"time_text\":\"0-0-9PM\",\"duration_value\":4,\"duration\":\"Days\",\"notes\":\"\",\"price\":50,\"discount\":0,\"final_amount\":50,\"purchase_source\":\"Hospital Pharmacy\"},{\"medicine_id\":10,\"medicine_name\":\"EMBETA TM - (METAPROLOL + TELMISARTAN)\",\"type_text\":\"Tab\",\"unit_text\":\"50MG\",\"dosage_text\":\"0-1-1\",\"when_text\":\"After Food\",\"time_text\":\"0-2PM-9PM\",\"duration_value\":4,\"duration\":\"Days\",\"notes\":\"\",\"price\":65,\"discount\":0,\"final_amount\":65,\"purchase_source\":\"Hospital Pharmacy\"}]', '', '', 115.00, 0.00, 115.00, 'Hospital Pharmacy', 'Both (Cash + UPI)', 1, 1, 10, '2026-05-07 05:54:06', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'PAT0535', 'A202605200001', 9, '[{\"medicine_id\":90,\"medicine_name\":\"DOLO 50 - (PARACETAMOL IP)\",\"type_text\":\"Tab\",\"unit_text\":\"500MG\",\"dosage_text\":\"0-1-0\",\"when_text\":\"After Food\",\"time_text\":\"0-2PM-0\",\"duration_value\":5,\"duration\":\"0-2PM-0\",\"notes\":\"0-1-0\",\"price\":10,\"discount\":0,\"final_amount\":10,\"purchase_source\":\"Hospital Pharmacy\"},{\"medicine_id\":12,\"medicine_name\":\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\",\"type_text\":\"Tab\",\"unit_text\":\"75\\/20MG\",\"dosage_text\":\"1-0-1\",\"when_text\":\"After Food\",\"time_text\":\"9AM-0-9PM\",\"duration_value\":5,\"duration\":\"9AM-0-9PM\",\"notes\":\"1-0-1\",\"price\":30,\"discount\":0,\"final_amount\":30,\"purchase_source\":\"Hospital Pharmacy\"}]', '', '', 40.00, 0.00, 40.00, 'Hospital Pharmacy', 'Cash', 1, 1, 2, '2026-05-20 09:29:25', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'PAT0010', 'A202605200002', 10, '[{\"medicine_id\":90,\"medicine_name\":\"DOLO 50 - (PARACETAMOL IP)\",\"type_text\":\"Tab\",\"unit_text\":\"500MG\",\"dosage_text\":\"0-1-0\",\"when_text\":\"After Food\",\"time_text\":\"0-2PM-0\",\"duration_value\":5,\"duration\":\"0-2PM-0\",\"notes\":\"0-1-0\",\"price\":30,\"discount\":0,\"final_amount\":30,\"purchase_source\":\"Hospital Pharmacy\"},{\"medicine_id\":12,\"medicine_name\":\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\",\"type_text\":\"Tab\",\"unit_text\":\"75\\/20MG\",\"dosage_text\":\"1-0-1\",\"when_text\":\"After Food\",\"time_text\":\"9AM-0-9PM\",\"duration_value\":5,\"duration\":\"9AM-0-9PM\",\"notes\":\"1-0-1\",\"price\":50,\"discount\":0,\"final_amount\":50,\"purchase_source\":\"Hospital Pharmacy\"}]', '', '', 80.00, 0.00, 80.00, 'Hospital Pharmacy', 'Both (Cash + UPI)', 1, 1, 2, '2026-05-20 10:16:53', '65768', 50.00, 30.00, NULL, NULL, NULL, NULL, NULL),
(8, 'PAT0001', 'A202605210001', 11, '[{\"medicine_id\":96,\"medicine_name\":\"FOLIC ACID 5MG - (PTEROYLMONOGLUTAMIC ACID)\",\"type_text\":\"Tab\",\"unit_text\":\"5mg\",\"dosage_text\":\"0-0-1\",\"when_text\":\"After Food\",\"time_text\":\"0-0-9PM\",\"duration_value\":5,\"duration\":\"0-0-9PM\",\"notes\":\"0-0-1\",\"price\":50,\"discount\":0,\"final_amount\":50,\"purchase_source\":\"Hospital Pharmacy\"},{\"medicine_id\":97,\"medicine_name\":\"PROGESTERONE 200MG - (MICRONIZED PROGESTERONE)\",\"type_text\":\"Tab\",\"unit_text\":\"200mg\",\"dosage_text\":\"1-1-0\",\"when_text\":\"After Food\",\"time_text\":\"9AM-2PM-0\",\"duration_value\":4,\"duration\":\"9AM-2PM-0\",\"notes\":\"1-1-0\",\"price\":310,\"discount\":0,\"final_amount\":310,\"purchase_source\":\"Hospital Pharmacy\"}]', '', '', 360.00, 0.00, 360.00, 'Hospital Pharmacy', 'UPI', 1, 9, 15, '2026-05-21 12:25:24', '566867678', 360.00, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `patient_medicine_billing_items`
--

CREATE TABLE `patient_medicine_billing_items` (
  `medicine_billing_item_id` int(11) NOT NULL,
  `medicine_billing_id` int(11) NOT NULL,
  `medicine_id` int(11) DEFAULT NULL,
  `medicine_name` varchar(255) NOT NULL,
  `type_text` varchar(100) DEFAULT NULL,
  `unit_text` varchar(100) DEFAULT NULL,
  `dosage_text` varchar(100) DEFAULT NULL,
  `when_text` varchar(100) DEFAULT NULL,
  `time_text` varchar(100) DEFAULT NULL,
  `duration_value` varchar(50) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `notes` longtext DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `final_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `purchase_source` varchar(50) NOT NULL DEFAULT 'Hospital Pharmacy',
  `org_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_medicine_billing_items`
--

INSERT INTO `patient_medicine_billing_items` (`medicine_billing_item_id`, `medicine_billing_id`, `medicine_id`, `medicine_name`, `type_text`, `unit_text`, `dosage_text`, `when_text`, `time_text`, `duration_value`, `duration`, `notes`, `price`, `discount`, `final_amount`, `purchase_source`, `org_id`, `created_by`, `created_at`) VALUES
(1, 0, 9, 'ECOSPRIN AV - (ASPIRIN + ATORVASTATIN)', 'Tab', '10/25MG', '0-1-1', 'After Food', '0-2PM-9PM', '4', 'Days', '', 30.00, 0.00, 30.00, 'Outside Pharmacy', 1, 2, '2026-04-15 10:28:22'),
(2, 0, 12, 'GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)', 'Tab', '10/25MG', '0-1-1', 'Not Applicable', '0-2PM-9PM', '4', 'Days', '', 50.00, 0.00, 50.00, 'Hospital Pharmacy', 1, 2, '2026-04-15 10:28:22'),
(3, 4, 11, 'GABACOX - M - (GABAPENTIN + METHYLCOBALAMIN)', 'Tab', '10/25MG', '0-0-1', 'After Food', '0-0-9PM', '4', 'Days', '', 50.00, 0.00, 50.00, 'Hospital Pharmacy', 1, 2, '2026-05-06 09:10:51'),
(4, 4, 10, 'EMBETA TM - (METAPROLOL + TELMISARTAN)', 'Tab', '50MG', '0-1-1', 'After Food', '0-2PM-9PM', '4', 'Days', '', 60.00, 0.00, 60.00, 'Hospital Pharmacy', 1, 2, '2026-05-06 09:10:51'),
(5, 5, 11, 'GABACOX - M - (GABAPENTIN + METHYLCOBALAMIN)', 'Tab', '10/25MG', '0-0-1', 'After Food', '0-0-9PM', '4', 'Days', '', 50.00, 0.00, 50.00, 'Hospital Pharmacy', 1, 10, '2026-05-07 05:54:06'),
(6, 5, 10, 'EMBETA TM - (METAPROLOL + TELMISARTAN)', 'Tab', '50MG', '0-1-1', 'After Food', '0-2PM-9PM', '4', 'Days', '', 65.00, 0.00, 65.00, 'Hospital Pharmacy', 1, 10, '2026-05-07 05:54:06'),
(7, 6, 90, 'DOLO 50 - (PARACETAMOL IP)', 'Tab', '500MG', '0-1-0', 'After Food', '0-2PM-0', '5', '0-2PM-0', '0-1-0', 10.00, 0.00, 10.00, 'Hospital Pharmacy', 1, 2, '2026-05-20 09:29:25'),
(8, 6, 12, 'GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)', 'Tab', '75/20MG', '1-0-1', 'After Food', '9AM-0-9PM', '5', '9AM-0-9PM', '1-0-1', 30.00, 0.00, 30.00, 'Hospital Pharmacy', 1, 2, '2026-05-20 09:29:25'),
(9, 7, 90, 'DOLO 50 - (PARACETAMOL IP)', 'Tab', '500MG', '0-1-0', 'After Food', '0-2PM-0', '5', '0-2PM-0', '0-1-0', 30.00, 0.00, 30.00, 'Hospital Pharmacy', 1, 2, '2026-05-20 10:16:53'),
(10, 7, 12, 'GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)', 'Tab', '75/20MG', '1-0-1', 'After Food', '9AM-0-9PM', '5', '9AM-0-9PM', '1-0-1', 50.00, 0.00, 50.00, 'Hospital Pharmacy', 1, 2, '2026-05-20 10:16:53'),
(11, 8, 96, 'FOLIC ACID 5MG - (PTEROYLMONOGLUTAMIC ACID)', 'Tab', '5mg', '0-0-1', 'After Food', '0-0-9PM', '5', '0-0-9PM', '0-0-1', 50.00, 0.00, 50.00, 'Hospital Pharmacy', 9, 15, '2026-05-21 12:25:24'),
(12, 8, 97, 'PROGESTERONE 200MG - (MICRONIZED PROGESTERONE)', 'Tab', '200mg', '1-1-0', 'After Food', '9AM-2PM-0', '4', '9AM-2PM-0', '1-1-0', 310.00, 0.00, 310.00, 'Hospital Pharmacy', 9, 15, '2026-05-21 12:25:24');

-- --------------------------------------------------------

--
-- Table structure for table `patient_tests_history`
--

CREATE TABLE `patient_tests_history` (
  `patient_history_id` int(11) NOT NULL,
  `patient_id` varchar(255) DEFAULT NULL,
  `appointment_id` varchar(255) DEFAULT NULL,
  `doctor_name` varchar(150) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `performed_at` varchar(255) DEFAULT NULL,
  `file_type` varchar(255) DEFAULT NULL,
  `test_name` varchar(255) NOT NULL,
  `uploaded_date` date NOT NULL,
  `file_url` varchar(500) DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `status` enum('0','1') DEFAULT '1',
  `org_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_tests_history`
--

INSERT INTO `patient_tests_history` (`patient_history_id`, `patient_id`, `appointment_id`, `doctor_name`, `specialization`, `performed_at`, `file_type`, `test_name`, `uploaded_date`, `file_url`, `observations`, `status`, `org_id`, `created_at`, `created_by`) VALUES
(1, 'PAT0046', 'A202510060001', 'Dr. Akhil', 'cardiologist', 'Outside the Hospital', 'Test', 'histopathology biopsy medium specimen', '2025-10-01', 'Testimages/PAT0046_1759755829_0.png', '', '1', 1, '2025-10-06 13:03:49', 2),
(2, 'PAT0012', 'A202604080001', '', '', 'Within the Hospital', 'Test', 'hla - b27 flowcytometry', '2026-04-08', 'Testimages/PAT0012_1775636405_0.png', '', '1', 1, '2026-04-08 08:20:05', 2),
(3, 'PAT0012', 'A202604080001', '', '', 'Within the Hospital', 'Test', 'anti hbs serum', '2026-04-08', 'Testimages/PAT0012_1775636453_0.png', '', '1', 1, '2026-04-08 08:20:53', 2),
(4, 'PAT0532', 'A202605060001', '', '', 'Within the Hospital', 'Test', 'anti cardiolipin igg serum', '2026-05-08', 'Testimages/PAT0532_1778220577_0.jpeg', '', '1', 1, '2026-05-08 06:09:37', 2),
(5, 'PAT0535', 'A202605200001', '', '', 'Within the Hospital', 'Test', 'direct coombs test', '2026-05-20', 'Testimages/PAT0535_1779269302_0.jpeg', '', '1', 1, '2026-05-20 09:28:22', 2),
(6, 'PAT0001', 'A202605210001', '', '', 'Within the Hospital', 'Test', 'colposcopy', '2026-05-21', 'Testimages/PAT0001_1779362743_1.png', '', '1', 9, '2026-05-21 11:25:43', 15);

-- --------------------------------------------------------

--
-- Table structure for table `patient_test_billing`
--

CREATE TABLE `patient_test_billing` (
  `test_billing_id` int(11) NOT NULL,
  `patient_id` varchar(250) NOT NULL,
  `appointment_id` varchar(250) NOT NULL,
  `test_details` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) DEFAULT 0.00,
  `net_amount` decimal(10,2) NOT NULL,
  `status` int(11) DEFAULT 1,
  `org_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(200) DEFAULT NULL,
  `transaction_number` varchar(100) DEFAULT NULL,
  `transaction_amount` decimal(10,2) DEFAULT NULL,
  `cash_amount` decimal(10,2) DEFAULT NULL,
  `refund_reason` varchar(500) DEFAULT NULL,
  `refunded_by` int(11) DEFAULT NULL,
  `refunded_at` datetime DEFAULT NULL,
  `refund_amount` decimal(10,2) DEFAULT NULL,
  `refund_type` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_test_billing`
--

INSERT INTO `patient_test_billing` (`test_billing_id`, `patient_id`, `appointment_id`, `test_details`, `total_amount`, `discount`, `net_amount`, `status`, `org_id`, `created_by`, `created_at`, `payment_method`, `transaction_number`, `transaction_amount`, `cash_amount`, `refund_reason`, `refunded_by`, `refunded_at`, `refund_amount`, `refund_type`) VALUES
(1, 'PAT0434', 'A202509290001', '[{\"test_id\":\"149\",\"test_name\":\"BLOOD TEST\",\"instruction\":\"\",\"doctor_price\":505,\"standard_price\":1010},{\"test_id\":\"11\",\"test_name\":\"GLUCOSE-RANDOM PLASMA\",\"instruction\":\"\",\"doctor_price\":63,\"standard_price\":80}]', 1090.00, 522.00, 568.00, 1, 1, 2, '2025-09-29 04:52:55', 'Cash', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'PAT0532', 'A202604150001', '[{\"test_id\":\"91\",\"test_name\":\"HISTOPATHOLOGY BIOPSY SMALL SPECIMEN\",\"instruction\":\"\",\"doctor_price\":800,\"standard_price\":800}]', 800.00, 0.00, 800.00, 1, 1, 2, '2026-04-15 11:39:15', 'Cash', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'PAT0532', 'A202605060001', '[{\"test_id\":\"102\",\"test_name\":\"LEPTOSPIRA IGM SERUM\",\"instruction\":\"\",\"doctor_price\":700,\"standard_price\":1400}]', 1400.00, 700.00, 700.00, 1, 1, 2, '2026-05-06 07:06:24', 'Cash', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'PAT0532', 'A202605060001', '[{\"test_id\":\"102\",\"test_name\":\"LEPTOSPIRA IGM SERUM\",\"instruction\":\"\",\"doctor_price\":700,\"standard_price\":1400}]', 1400.00, 700.00, 700.00, 1, 1, 2, '2026-05-06 09:01:50', 'Both (Cash + UPI)', '12345', 500.00, 200.00, NULL, NULL, NULL, NULL, NULL),
(5, 'PAT0535', 'A202605200001', '[{\"test_id\":\"149\",\"test_name\":\"BLOOD TEST\",\"instruction\":\"\",\"doctor_price\":505,\"standard_price\":1010}]', 1010.00, 505.00, 505.00, 1, 1, 2, '2026-05-20 09:26:40', 'Cash', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'PAT0535', 'A202605200001', '[{\"test_id\":\"11\",\"test_name\":\"GLUCOSE-RANDOM PLASMA\",\"instruction\":\"\",\"doctor_price\":63,\"standard_price\":80}]', 80.00, 17.00, 63.00, 1, 1, 2, '2026-05-20 09:26:54', 'Cash', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'PAT0010', 'A202605200002', '[{\"test_id\":\"149\",\"test_name\":\"BLOOD TEST\",\"instruction\":\"\",\"doctor_price\":505,\"standard_price\":1010},{\"test_id\":\"11\",\"test_name\":\"GLUCOSE-RANDOM PLASMA\",\"instruction\":\"\",\"doctor_price\":63,\"standard_price\":80}]', 1090.00, 522.00, 568.00, 1, 1, 2, '2026-05-20 10:15:23', 'Both (Cash + UPI)', '658i69o89', 68.00, 500.00, NULL, NULL, NULL, NULL, NULL),
(8, 'PAT0001', 'A202605210001', '[{\"test_id\":\"156\",\"test_name\":\"ECHOCARDIOGRAM\",\"instruction\":\"\",\"doctor_price\":750,\"standard_price\":1500},{\"test_id\":\"166\",\"test_name\":\"HISTOPATHOLOGY BIOPSY MEDIUM SPECIMEN\",\"instruction\":\"\",\"doctor_price\":0,\"standard_price\":0}]', 1500.00, 750.00, 750.00, 1, 9, 15, '2026-05-21 11:34:30', 'Both (Cash + UPI)', '5876768', 500.00, 250.00, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_method`
--

CREATE TABLE `payment_method` (
  `payment_method_id` int(11) NOT NULL,
  `payment_method` varchar(30) NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `created_date_time` datetime NOT NULL,
  `org_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_method`
--

INSERT INTO `payment_method` (`payment_method_id`, `payment_method`, `status`, `created_date_time`, `org_id`) VALUES
(1, 'Cash', '1', '2023-09-06 14:35:00', 1),
(2, 'Card', '0', '2023-09-06 14:35:00', 1),
(3, 'UPI', '1', '2023-09-06 14:35:00', 1),
(4, 'Net Banking', '0', '2023-09-06 14:35:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `prescripition`
--

CREATE TABLE `prescripition` (
  `prescription_id` int(11) NOT NULL,
  `patient_name` varchar(100) NOT NULL,
  `appoint_register_id` varchar(100) NOT NULL,
  `patient_uid` varchar(255) NOT NULL,
  `age` varchar(30) NOT NULL,
  `gender` enum('Male','Female','Others') NOT NULL,
  `rx_id` int(11) NOT NULL,
  `test_group_id` int(11) NOT NULL,
  `test_id` longtext NOT NULL,
  `medicine_id` longtext NOT NULL,
  `prescriptiondate` date DEFAULT NULL,
  `patient_vitals` varchar(255) DEFAULT NULL,
  `finalDiagnosis` longtext NOT NULL,
  `chiefcomplaint` longtext NOT NULL,
  `pasthistory` longtext NOT NULL,
  `patient_data` longtext DEFAULT NULL,
  `advise` longtext DEFAULT NULL,
  `personal_note` longtext NOT NULL,
  `reviewafter` varchar(250) NOT NULL,
  `images` longtext NOT NULL,
  `reviewafterdate` varchar(30) NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `prescription_status` enum('N','R') NOT NULL DEFAULT 'N',
  `create_date_time` datetime NOT NULL DEFAULT current_timestamp(),
  `create_by` int(11) NOT NULL,
  `modify_by` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `gynaec_mirror` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescripition`
--

INSERT INTO `prescripition` (`prescription_id`, `patient_name`, `appoint_register_id`, `patient_uid`, `age`, `gender`, `rx_id`, `test_group_id`, `test_id`, `medicine_id`, `prescriptiondate`, `patient_vitals`, `finalDiagnosis`, `chiefcomplaint`, `pasthistory`, `patient_data`, `advise`, `personal_note`, `reviewafter`, `images`, `reviewafterdate`, `status`, `prescription_status`, `create_date_time`, `create_by`, `modify_by`, `org_id`, `create_date`, `gynaec_mirror`) VALUES
(1, 'SUBHA RAO', 'A202509290001', 'PAT0434', '59', 'Male', 0, 0, '[{\"test_id\":\"149\",\"test_name\":\"BLOOD TEST\",\"instruction\":\"\",\"concession\":\"Family (50%)\",\"concessionName\":\"Family\",\"concessionValue\":50,\"concessionType\":\"percentage\",\"doctor_price\":505,\"standard_price\":1010,\"test_status\":\"1\",\"test_group_id\":\"\",\"test_group_name\":\"\",\"test_group_price\":\"\"},{\"test_id\":\"11\",\"test_name\":\"GLUCOSE-RANDOM PLASMA\",\"instruction\":\"\",\"concession\":\"new (₹17)\",\"concessionName\":\"new\",\"concessionValue\":17,\"concessionType\":\"amount\",\"doctor_price\":63,\"standard_price\":80,\"test_status\":\"1\",\"test_group_id\":\"\",\"test_group_name\":\"\",\"test_group_price\":\"\"}]', '[{\"medicine_id\":90,\"medicine_name\":\"DOLO 50 - (PARACETAMOL IP)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"12\",\"unit_text\":\"500MG\",\"dosage_id\":\"2\",\"when_id\":\"8\",\"time_id\":\"4\",\"duration_value\":\"5\",\"duration\":\"Days\",\"notes\":\"\",\"med_status\":\"1\",\"timeText\":\"0-2PM-0\",\"dosageText\":\"0-1-0\",\"whenText\":\"After Food\"},{\"medicine_id\":\"12\",\"medicine_name\":\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"7\",\"unit_text\":\"75/20MG\",\"dosage_id\":\"5\",\"when_id\":\"8\",\"time_id\":\"1\",\"duration_value\":\"5\",\"duration\":\"Days\",\"notes\":\"\",\"med_status\":\"1\",\"timeText\":\"9AM-0-9PM\",\"dosageText\":\"1-0-1\",\"whenText\":\"After Food\"}]', '2025-09-29', 'A202509290001', 'Fever', '', '', '', '', '', '5 Days', '', '2026-10-04', '1', 'N', '2025-09-29 09:41:46', 2, 2, 1, '2025-09-29 04:11:46', 0),
(2, 'SUNDARI', 'A202510060001', 'PAT0046', '87', 'Female', 0, 0, '[{\"test_id\":\"93\",\"test_name\":\"HIV PROVIRAL DNA QUALITATIVE#\",\"instruction\":\"\",\"concession\":\"\",\"concessionName\":\"\",\"concessionValue\":\"\",\"concessionType\":\"\",\"doctor_price\":7000,\"standard_price\":7000,\"test_status\":\"1\",\"test_group_id\":\"\",\"test_group_name\":\"\",\"test_group_price\":\"\"}]', '[{\"medicine_id\":\"9\",\"medicine_name\":\"ECOSPRIN AV - (ASPIRIN + ATORVASTATIN)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"10\",\"unit_text\":\"10/25MG\",\"dosage_id\":\"5\",\"when_id\":\"8\",\"time_id\":\"1\",\"duration_value\":\"5\",\"duration\":\"Days\",\"notes\":\"\",\"med_status\":\"1\",\"timeText\":\"9AM-0-9PM\",\"dosageText\":\"1-0-1\",\"whenText\":\"After Food\"}]', '2025-10-06', 'A202510060001', 'test', '', '', '', '', '', '3 Days', '', '2025-10-09', '1', 'N', '2025-10-06 17:21:35', 2, 2, 1, '2025-10-06 11:51:35', 0),
(3, 'G NARAYANA', 'A202604080001', 'PAT0012', '47', 'Male', 0, 0, '[{\"test_id\":\"90\",\"test_name\":\"HISTOPATHOLOGY BIOPSY MEDIUM SPECIMEN\",\"instruction\":\"\",\"concession\":\"\",\"concessionName\":\"\",\"concessionValue\":\"\",\"concessionType\":\"\",\"doctor_price\":800,\"standard_price\":800,\"test_status\":\"1\",\"test_group_id\":\"\",\"test_group_name\":\"\",\"test_group_price\":\"\"},{\"test_id\":\"95\",\"test_name\":\"HLA - B27 FLOWCYTOMETRY\",\"instruction\":\"\",\"concession\":\"\",\"concessionName\":\"\",\"concessionValue\":\"\",\"concessionType\":\"\",\"doctor_price\":2500,\"standard_price\":2500,\"test_status\":\"1\",\"test_group_id\":\"\",\"test_group_name\":\"\",\"test_group_price\":\"\"}]', '[]', '2026-04-08', 'A202604080001', '', '', '', '', '', '', ' Days', '', '', '1', 'N', '2026-04-08 13:46:31', 2, 2, 1, '2026-04-08 08:16:31', 0),
(4, 'CH PADMA', 'A202604100001', 'PAT0013', '44', 'Female', 0, 0, '[]', '[{\"medicine_id\":\"8\",\"medicine_name\":\"ECOSPRIN - (ASPIRIN)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"10\",\"unit_text\":\"10/25MG\",\"dosage_id\":\"4\",\"when_id\":\"8\",\"time_id\":\"2\",\"duration_value\":\"3\",\"duration\":\"Days\",\"notes\":\"\",\"med_status\":\"1\",\"timeText\":\"9AM-2PM-0\",\"dosageText\":\"1-1-0\",\"whenText\":\"After Food\"}]', '2026-04-10', 'A202604100001', '', '', '', '', '', 'testing', ' Days', '', '', '1', 'N', '2026-04-10 14:12:09', 2, 2, 1, '2026-04-10 08:42:09', 0),
(5, 'durga lakshmi', 'A202604150001', 'PAT0532', '34', 'Female', 0, 0, '[{\"test_id\":\"91\",\"test_name\":\"HISTOPATHOLOGY BIOPSY SMALL SPECIMEN\",\"instruction\":\"\",\"concession\":\"\",\"concessionName\":\"\",\"concessionValue\":\"\",\"concessionType\":\"\",\"doctor_price\":800,\"standard_price\":800,\"test_status\":\"1\",\"test_group_id\":\"\",\"test_group_name\":\"\",\"test_group_price\":\"\"}]', '[{\"medicine_id\":\"9\",\"medicine_name\":\"ECOSPRIN AV - (ASPIRIN + ATORVASTATIN)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"10\",\"unit_text\":\"10/25MG\",\"dosage_id\":\"6\",\"when_id\":\"8\",\"time_id\":\"10\",\"duration_value\":\"4\",\"duration\":\"Days\",\"notes\":\"\",\"med_status\":\"1\",\"timeText\":\"0-2PM-9PM\",\"dosageText\":\"0-1-1\",\"whenText\":\"After Food\"},{\"medicine_id\":\"12\",\"medicine_name\":\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"10\",\"unit_text\":\"10/25MG\",\"dosage_id\":\"6\",\"when_id\":\"9\",\"time_id\":\"10\",\"duration_value\":\"4\",\"duration\":\"Days\",\"notes\":\"\",\"med_status\":\"1\",\"timeText\":\"0-2PM-9PM\",\"dosageText\":\"0-1-1\",\"whenText\":\"Not Applicable\"}]', '2026-04-15', 'A202604150001', '', '', '', '', '', 'testing purpose', ' Days', '', '', '1', 'N', '2026-04-15 10:53:28', 2, 2, 1, '2026-04-15 05:23:28', 0),
(6, 'durga prasad', 'A202604160001', 'PAT0533', '30', 'Male', 0, 0, '[]', '[]', '2026-04-16', 'A202604160001', '', '', '', '', '', '', ' ', '', '', '0', 'N', '2026-04-16 15:41:54', 2, 2, 1, '2026-04-16 10:11:54', 1),
(7, 'durga lakshmi', 'A202605060001', 'PAT0532', '34', 'Female', 0, 0, '[{\"test_id\":\"102\",\"test_name\":\"LEPTOSPIRA IGM SERUM\",\"instruction\":\"\",\"concession\":\"Family (50%)\",\"concessionName\":\"Family\",\"concessionValue\":50,\"concessionType\":\"percentage\",\"doctor_price\":700,\"standard_price\":1400,\"test_status\":\"1\",\"test_group_id\":\"\",\"test_group_name\":\"\",\"test_group_price\":\"\"}]', '[{\"medicine_id\":\"11\",\"medicine_name\":\"GABACOX - M - (GABAPENTIN + METHYLCOBALAMIN)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"10\",\"unit_text\":\"10/25MG\",\"dosage_id\":\"3\",\"when_id\":\"8\",\"time_id\":\"3\",\"duration_value\":\"4\",\"duration\":\"Days\",\"notes\":\"\",\"med_status\":\"1\",\"timeText\":\"0-0-9PM\",\"dosageText\":\"0-0-1\",\"whenText\":\"After Food\"},{\"medicine_id\":\"10\",\"medicine_name\":\"EMBETA TM - (METAPROLOL + TELMISARTAN)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"14\",\"unit_text\":\"50MG\",\"dosage_id\":\"6\",\"when_id\":\"8\",\"time_id\":\"10\",\"duration_value\":\"4\",\"duration\":\"Days\",\"notes\":\"\",\"med_status\":\"1\",\"timeText\":\"0-2PM-9PM\",\"dosageText\":\"0-1-1\",\"whenText\":\"After Food\"}]', '2026-05-06', 'A202605060001', 'testing\npurpose\n123@', '', '', '', '', '', '5 ', '', '2026-05-06', '1', 'N', '2026-05-06 12:35:37', 2, 2, 1, '2026-05-06 07:05:37', 0),
(8, 'Ji won', 'A202605070001', 'PAT0534', '49', 'Female', 0, 0, '[{\"test_id\":\"82\",\"test_name\":\"CYTOLOGY (PAP SMEAR) (LBC)\",\"instruction\":\"test\",\"concession\":\"Family (50%)\",\"concessionName\":\"Family\",\"concessionValue\":50,\"concessionType\":\"percentage\",\"doctor_price\":600,\"standard_price\":1200,\"test_status\":\"1\",\"test_group_id\":\"\",\"test_group_name\":\"\",\"test_group_price\":\"\"}]', '[{\"medicine_id\":\"13\",\"medicine_name\":\"IVABRATCO - (IVABRADINE)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"10\",\"unit_text\":\"10/25MG\",\"dosage_id\":\"4\",\"when_id\":\"8\",\"time_id\":\"2\",\"duration_value\":\"5\",\"duration\":\"Days\",\"notes\":\"\",\"med_status\":\"1\",\"timeText\":\"9AM-2PM-0\",\"dosageText\":\"1-1-0\",\"whenText\":\"After Food\"},{\"medicine_id\":\"15\",\"medicine_name\":\"JBTOR PLUS LS - (TORSEMIDE + ALDACTONE)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"14\",\"unit_text\":\"50MG\",\"dosage_id\":\"5\",\"when_id\":\"8\",\"time_id\":\"1\",\"duration_value\":\"5\",\"duration\":\"Days\",\"notes\":\"\",\"med_status\":\"1\",\"timeText\":\"9AM-0-9PM\",\"dosageText\":\"1-0-1\",\"whenText\":\"After Food\"}]', '2026-05-07', 'A202605070001', 'test', 'mhgnh\nkjymjyh\njhmjhm', 'testing', 'testing purpose new', 'need to take \nmuch care', 'future care', '5 Days', '', '2026-05-12', '1', 'N', '2026-05-07 12:40:26', 2, 2, 1, '2026-05-07 07:10:26', 0),
(9, 'Tarun', 'A202605200001', 'PAT0535', '25', 'Male', 0, 0, '[{\"test_id\":\"149\",\"test_name\":\"BLOOD TEST\",\"instruction\":\"\",\"concession\":\"\",\"concessionName\":\"\",\"concessionValue\":\"\",\"concessionType\":\"\",\"doctor_price\":505,\"standard_price\":1010,\"test_status\":\"1\",\"test_group_id\":\"\",\"test_group_name\":\"\",\"test_group_price\":\"\"},{\"test_id\":\"11\",\"test_name\":\"GLUCOSE-RANDOM PLASMA\",\"instruction\":\"\",\"concession\":\"\",\"concessionName\":\"\",\"concessionValue\":\"\",\"concessionType\":\"\",\"doctor_price\":63,\"standard_price\":80,\"test_status\":\"1\",\"test_group_id\":\"\",\"test_group_name\":\"\",\"test_group_price\":\"\"}]', '[{\"medicine_id\":\"90\",\"medicine_name\":\"DOLO 50 - (PARACETAMOL IP)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"12\",\"unit_text\":\"500MG\",\"dosage_id\":\"2\",\"when_id\":\"8\",\"time_id\":\"4\",\"duration_value\":\"5\",\"duration\":\"0-2PM-0\",\"notes\":\"0-1-0\",\"med_status\":\"After Food\",\"timeText\":\"0-2PM-0\",\"dosageText\":\"0-1-0\",\"whenText\":\"After Food\"},{\"medicine_id\":\"12\",\"medicine_name\":\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"7\",\"unit_text\":\"75/20MG\",\"dosage_id\":\"5\",\"when_id\":\"8\",\"time_id\":\"1\",\"duration_value\":\"5\",\"duration\":\"9AM-0-9PM\",\"notes\":\"1-0-1\",\"med_status\":\"After Food\",\"timeText\":\"9AM-0-9PM\",\"dosageText\":\"1-0-1\",\"whenText\":\"After Food\"}]', '2026-05-20', 'A202605200001', 'Fever', '', '', '', '', '', '5 Days', '', '2026-05-25', '1', 'N', '2026-05-20 14:52:33', 2, 2, 1, '2026-05-20 09:22:33', 0),
(10, 'G KALAYANI', 'A202605200002', 'PAT0010', '49', 'Female', 0, 0, '[{\"test_id\":\"149\",\"test_name\":\"BLOOD TEST\",\"instruction\":\"\",\"concession\":\"\",\"concessionName\":\"\",\"concessionValue\":\"\",\"concessionType\":\"\",\"doctor_price\":505,\"standard_price\":1010,\"test_status\":\"1\",\"test_group_id\":\"\",\"test_group_name\":\"\",\"test_group_price\":\"\"},{\"test_id\":\"11\",\"test_name\":\"GLUCOSE-RANDOM PLASMA\",\"instruction\":\"\",\"concession\":\"\",\"concessionName\":\"\",\"concessionValue\":\"\",\"concessionType\":\"\",\"doctor_price\":63,\"standard_price\":80,\"test_status\":\"1\",\"test_group_id\":\"\",\"test_group_name\":\"\",\"test_group_price\":\"\"}]', '[{\"medicine_id\":\"90\",\"medicine_name\":\"DOLO 50 - (PARACETAMOL IP)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"12\",\"unit_text\":\"500MG\",\"dosage_id\":\"2\",\"when_id\":\"8\",\"time_id\":\"4\",\"duration_value\":\"5\",\"duration\":\"0-2PM-0\",\"notes\":\"0-1-0\",\"med_status\":\"After Food\",\"timeText\":\"0-2PM-0\",\"dosageText\":\"0-1-0\",\"whenText\":\"After Food\"},{\"medicine_id\":\"12\",\"medicine_name\":\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"7\",\"unit_text\":\"75/20MG\",\"dosage_id\":\"5\",\"when_id\":\"8\",\"time_id\":\"1\",\"duration_value\":\"5\",\"duration\":\"9AM-0-9PM\",\"notes\":\"1-0-1\",\"med_status\":\"After Food\",\"timeText\":\"9AM-0-9PM\",\"dosageText\":\"1-0-1\",\"whenText\":\"After Food\"}]', '2026-05-20', 'A202605200002', 'Fever', '', '', '', '', '', '5 Days', '', '2026-05-25', '1', 'N', '2026-05-20 15:44:34', 2, 2, 1, '2026-05-20 10:14:34', 0),
(11, 'Pavan Kumar', 'A202605210001', 'PAT0001', '34', 'Male', 0, 0, '[{\"test_id\":\"156\",\"test_name\":\"ECHOCARDIOGRAM\",\"instruction\":\"\",\"concession\":\"Family (50%)\",\"concessionName\":\"Family\",\"concessionValue\":50,\"concessionType\":\"percentage\",\"doctor_price\":750,\"standard_price\":1500,\"test_status\":\"1\",\"test_group_id\":\"\",\"test_group_name\":\"\",\"test_group_price\":\"\"},{\"test_id\":166,\"test_name\":\"HISTOPATHOLOGY BIOPSY MEDIUM SPECIMEN\",\"instruction\":\"\",\"concession\":\"Own family (100%)\",\"concessionName\":\"Own family\",\"concessionValue\":100,\"concessionType\":\"percentage\",\"doctor_price\":0,\"standard_price\":0,\"test_status\":\"1\",\"test_group_id\":\"\",\"test_group_name\":\"\",\"test_group_price\":\"\"}]', '[{\"medicine_id\":\"96\",\"medicine_name\":\"FOLIC ACID 5MG - (PTEROYLMONOGLUTAMIC ACID)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"2\",\"unit_text\":\"5mg\",\"dosage_id\":\"3\",\"when_id\":\"8\",\"time_id\":\"3\",\"duration_value\":\"5\",\"duration\":\"0-0-9PM\",\"notes\":\"0-0-1\",\"med_status\":\"After Food\",\"timeText\":\"0-0-9PM\",\"dosageText\":\"0-0-1\",\"whenText\":\"After Food\"},{\"medicine_id\":\"97\",\"medicine_name\":\"PROGESTERONE 200MG - (MICRONIZED PROGESTERONE)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"31\",\"unit_text\":\"200mg\",\"dosage_id\":\"4\",\"when_id\":\"8\",\"time_id\":\"2\",\"duration_value\":\"4\",\"duration\":\"9AM-2PM-0\",\"notes\":\"1-1-0\",\"med_status\":\"After Food\",\"timeText\":\"9AM-2PM-0\",\"dosageText\":\"1-1-0\",\"whenText\":\"After Food\"}]', '2026-05-21', 'A202605210001', 'Heart Attack', '', '', '', '', '', '5 Days', '', '2026-05-26', '1', 'N', '2026-05-21 14:46:03', 15, 15, 9, '2026-05-21 09:16:03', 0),
(12, 'keerthi', 'A202605210002', 'PAT0002', '28', 'Female', 0, 0, '[{\"investigation_name\":\"LIPID PROFILE\",\"instructions\":\"\",\"price\":\"500.00\",\"concession\":\"\"}]', '[{\"drugName\":\"PROGESTERONE 200MG - (MICRONIZED PROGESTERONE)\",\"typeText\":\"Tab\",\"unitText\":\"200mg\",\"dosageId\":\"5\",\"whenId\":\"8\",\"timeId\":\"1\",\"duration_value\":\"5\",\"duration\":\"Days\",\"route\":\"\",\"notes\":\"\",\"dosageText\":\"1-0-1\",\"whenText\":\"After Food\",\"timeText\":\"9AM-0-9PM\",\"medConcessionId\":\"\",\"medConcessionName\":\"No Discount\",\"medConcessionType\":\"\",\"medConcessionVal\":\"\"},{\"drugName\":\"DOLO 650 - (PARACETAMOL IP)\",\"typeText\":\"Tab\",\"unitText\":\"500MG\",\"dosageId\":\"5\",\"whenId\":\"8\",\"timeId\":\"1\",\"duration_value\":\"5\",\"duration\":\"Days\",\"route\":\"\",\"notes\":\"\",\"dosageText\":\"1-0-1\",\"whenText\":\"After Food\",\"timeText\":\"9AM-0-9PM\",\"medConcessionId\":\"\",\"medConcessionName\":\"No Discount\",\"medConcessionType\":\"\",\"medConcessionVal\":\"\"}]', '2026-05-21', 'A202605210002', 'pregnancy check up', '', '', '', '', '', '5 Days', '', '2026-05-26', '1', 'N', '2026-05-21 19:19:58', 16, 16, 9, '2026-05-21 13:49:58', 1);

-- --------------------------------------------------------

--
-- Table structure for table `prescription_medicines`
--

CREATE TABLE `prescription_medicines` (
  `pm_id` int(11) NOT NULL,
  `prescription_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `dosage_id` int(11) NOT NULL,
  `intake_id` varchar(100) NOT NULL,
  `time_id` int(11) NOT NULL,
  `frequency_ids` varchar(100) NOT NULL,
  `test_id` int(11) NOT NULL,
  `duration` longtext NOT NULL,
  `quantity` varchar(100) NOT NULL,
  `note` varchar(255) NOT NULL,
  `Additional_Note` varchar(250) NOT NULL,
  `patient_vitals` varchar(250) NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `created_by` int(11) NOT NULL,
  `create_date_time` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `org_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receptionnist`
--

CREATE TABLE `receptionnist` (
  `rep_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `security_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `org_id` int(11) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receptionnist`
--

INSERT INTO `receptionnist` (`rep_id`, `doc_id`, `security_id`, `user_name`, `org_id`, `status`, `created_by`, `created_at`, `modified_by`, `modified_at`) VALUES
(1, 5, 12, 'Dr. Venkatesh', 1, '1', 2, '2025-09-24 06:51:50', 2, '2026-05-07 12:49:16'),
(2, 1, 12, 'Dr. Venkatesh', 1, '1', 2, '2025-09-24 06:55:35', 2, '2026-05-07 05:36:06'),
(3, 6, 12, 'Dr. Venkatesh', 1, '1', 2, '2025-09-24 11:27:06', 2, '2026-05-07 05:34:24'),
(4, 1, 13, 'ravi', 1, '0', 2, '2026-04-15 06:28:22', 2, '2026-05-07 05:36:06'),
(5, 1, 14, 'Dr. Rohith', 1, '1', 2, '2026-05-07 05:36:06', 2, '2026-05-07 05:36:06'),
(6, 7, 17, 'Durga Lakshmi', 9, '1', 15, '2026-05-21 06:29:52', 15, '2026-05-21 06:29:52'),
(7, 8, 17, 'Durga Lakshmi', 9, '1', 15, '2026-05-21 06:30:42', 15, '2026-05-21 06:30:42');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_date_time` datetime NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `modified_by` int(11) NOT NULL,
  `modified_date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `org_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `created_by`, `created_date_time`, `status`, `modified_by`, `modified_date_time`, `org_id`) VALUES
(1, 'Super Admin', 1, '2023-09-06 12:39:14', '1', 1, '2023-10-16 13:31:11', 0),
(2, 'Admin', 1, '2023-09-06 12:46:01', '1', 2, '2025-09-12 09:43:40', 1),
(9, 'test', 2, '2025-09-17 11:27:04', '0', 2, '2025-09-17 05:57:24', 1),
(10, 'test', 2, '2025-09-17 11:30:32', '0', 2, '2025-09-17 06:00:37', 1),
(11, 'Receptionist', 2, '2026-04-15 11:53:35', '1', 2, '2026-04-15 06:23:35', 1),
(12, 'Pharmacist', 1, '2026-04-15 15:37:14', '1', 2, '2026-05-07 05:48:02', 1),
(13, 'Doctor', 15, '2026-05-21 10:52:38', '1', 15, '2026-05-21 05:22:38', 9),
(14, 'Receptionist', 15, '2026-05-21 11:01:36', '1', 15, '2026-05-21 05:31:36', 9),
(15, 'Pharmacist', 15, '2026-05-21 11:03:13', '1', 15, '2026-05-21 05:33:13', 9),
(16, 'Admin', 15, '2026-05-21 11:04:23', '1', 15, '2026-05-21 05:34:23', 9);

-- --------------------------------------------------------

--
-- Table structure for table `role_menus`
--

CREATE TABLE `role_menus` (
  `role_menus_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `menu_access` enum('1','0') DEFAULT '0',
  `permissions` varchar(100) NOT NULL DEFAULT 'view,add,edit,delete',
  `created_by` int(11) NOT NULL,
  `created_date_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `org_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `role_menus`
--

INSERT INTO `role_menus` (`role_menus_id`, `role_id`, `menu_id`, `menu_access`, `permissions`, `created_by`, `created_date_time`, `org_id`) VALUES
(393, 8, 1, '0', 'view,add,edit,delete', 1, '2025-05-13 05:51:05', 0),
(394, 8, 2, '0', 'view,add,edit,delete', 1, '2025-05-13 05:51:05', 0),
(395, 8, 3, '0', 'view,add,edit,delete', 1, '2025-05-13 05:51:05', 0),
(396, 8, 4, '0', 'view,add,edit,delete', 1, '2025-05-13 05:51:05', 0),
(397, 8, 5, '0', 'view,add,edit,delete', 1, '2025-05-13 05:51:05', 0),
(398, 8, 18, '0', 'view,add,edit,delete', 1, '2025-05-13 05:51:05', 0),
(399, 8, 19, '0', 'view,add,edit,delete', 1, '2025-05-13 05:51:05', 0),
(400, 8, 20, '0', 'view,add,edit,delete', 1, '2025-05-13 05:51:05', 0),
(401, 8, 21, '0', 'view,add,edit,delete', 1, '2025-05-13 05:51:05', 0),
(402, 8, 22, '0', 'view,add,edit,delete', 1, '2025-05-13 05:51:05', 0),
(403, 8, 26, '0', 'view,add,edit,delete', 1, '2025-05-13 05:51:05', 0),
(404, 8, 35, '0', 'view,add,edit,delete', 1, '2025-05-13 05:51:05', 0),
(405, 8, 28, '0', 'view,add,edit,delete', 1, '2025-05-13 05:51:05', 0),
(406, 8, 29, '0', 'view,add,edit,delete', 1, '2025-05-13 05:51:05', 0),
(407, 8, 30, '0', 'view,add,edit,delete', 1, '2025-05-13 05:51:05', 0),
(5464, 2, 1, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5465, 2, 2, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5466, 2, 3, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5467, 2, 4, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5468, 2, 5, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5469, 2, 6, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5470, 2, 7, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5471, 2, 8, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5472, 2, 9, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5473, 2, 10, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5474, 2, 100, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5475, 2, 11, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5476, 2, 12, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5477, 2, 13, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5478, 2, 14, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5479, 2, 15, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5480, 2, 16, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5481, 2, 18, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5482, 2, 19, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5483, 2, 101, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5484, 2, 20, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5485, 2, 111, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5486, 2, 21, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5487, 2, 22, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5488, 2, 23, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5489, 2, 25, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5490, 2, 26, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5491, 2, 35, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5492, 2, 28, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5493, 2, 29, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5494, 2, 30, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5495, 2, 103, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5496, 2, 104, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5497, 2, 105, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5498, 2, 106, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5499, 2, 107, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5500, 2, 108, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5501, 2, 109, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5502, 2, 110, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5503, 2, 112, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5504, 2, 31, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5505, 2, 32, '0', 'view,add,edit,delete', 1, '2025-09-19 09:37:35', 0),
(5524, 1, 2, '0', 'view,add,edit,delete', 1, '2025-09-24 05:11:05', 0),
(5525, 1, 3, '0', 'view,add,edit,delete', 1, '2025-09-24 05:11:05', 0),
(5526, 1, 4, '0', 'view,add,edit,delete', 1, '2025-09-24 05:11:05', 0),
(5527, 1, 5, '0', 'view,add,edit,delete', 1, '2025-09-24 05:11:05', 0),
(5528, 1, 33, '0', 'view,add,edit,delete', 1, '2025-09-24 05:11:05', 0),
(5529, 1, 34, '0', 'view,add,edit,delete', 1, '2025-09-24 05:11:05', 0),
(5531, 11, 6, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5533, 11, 8, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5534, 11, 9, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5535, 11, 10, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5536, 11, 100, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5537, 11, 11, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5538, 11, 12, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5539, 11, 13, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5540, 11, 14, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5541, 11, 15, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5542, 11, 16, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5543, 11, 18, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5544, 11, 19, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5545, 11, 101, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5546, 11, 20, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5547, 11, 111, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5548, 11, 21, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5549, 11, 22, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5550, 11, 23, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5551, 11, 25, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5552, 11, 26, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5553, 11, 35, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5554, 11, 28, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5555, 11, 29, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5556, 11, 30, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5557, 11, 103, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5558, 11, 104, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5559, 11, 105, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5560, 11, 106, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5561, 11, 107, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5562, 11, 108, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5563, 11, 109, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5564, 11, 110, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5565, 11, 112, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5566, 11, 31, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5567, 11, 32, '0', 'view,add,edit,delete', 2, '2026-04-15 06:23:35', 1),
(5568, 1, 113, '0', 'view,add,edit,delete', 1, '2026-04-15 10:07:14', 0),
(5569, 2, 113, '0', 'view,add,edit,delete', 1, '2026-04-15 10:07:14', 0),
(5570, 8, 113, '0', 'view,add,edit,delete', 1, '2026-04-15 10:07:14', 0),
(5572, 1, 115, '0', 'view,add,edit,delete', 1, '2026-04-16 04:30:00', 0),
(5573, 1, 114, '0', 'view,add,edit,delete', 1, '2026-04-16 04:30:00', 0),
(5574, 2, 115, '0', 'view,add,edit,delete', 1, '2026-04-16 04:30:00', 0),
(5575, 2, 114, '0', 'view,add,edit,delete', 1, '2026-04-16 04:30:00', 0),
(5576, 1, 116, '0', 'view,add,edit,delete', 1, '2026-04-16 04:30:00', 0),
(5577, 2, 116, '0', 'view,add,edit,delete', 1, '2026-04-16 04:30:00', 0),
(5578, 1, 117, '0', 'view,add,edit,delete', 0, '2026-05-06 15:49:54', 0),
(5579, 2, 117, '0', 'view,add,edit,delete', 0, '2026-05-06 15:49:54', 0),
(5580, 11, 117, '0', 'view,add,edit,delete', 0, '2026-05-06 15:49:54', 0),
(5583, 1, 118, '0', 'view,add,edit,delete', 0, '2026-05-07 06:09:12', 0),
(5584, 1, 119, '0', 'view,add,edit,delete', 0, '2026-05-07 06:09:12', 0),
(5585, 2, 118, '0', 'view,add,edit,delete', 0, '2026-05-07 06:09:12', 0),
(5586, 2, 119, '0', 'view,add,edit,delete', 0, '2026-05-07 06:09:12', 0),
(5591, 12, 18, '0', 'view,add,edit,delete', 2, '2026-05-07 09:50:08', 1),
(5592, 12, 113, '0', 'view,add,edit,delete', 2, '2026-05-07 09:50:08', 1),
(5638, 14, 1, '0', 'view', 15, '2026-05-21 05:31:36', 9),
(5639, 14, 6, '0', 'view', 15, '2026-05-21 05:31:36', 9),
(5640, 14, 7, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5641, 14, 8, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5642, 14, 9, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5643, 14, 10, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5644, 14, 100, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5645, 14, 11, '0', 'view', 15, '2026-05-21 05:31:36', 9),
(5646, 14, 12, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5647, 14, 13, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5648, 14, 14, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5649, 14, 15, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5650, 14, 16, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5651, 14, 18, '0', 'view', 15, '2026-05-21 05:31:36', 9),
(5652, 14, 19, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5653, 14, 101, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5654, 14, 20, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5655, 14, 111, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5656, 14, 113, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5657, 14, 118, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5658, 14, 26, '0', 'view', 15, '2026-05-21 05:31:36', 9),
(5659, 14, 35, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5660, 14, 28, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5661, 14, 29, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5662, 14, 30, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5663, 14, 103, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5664, 14, 104, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5665, 14, 105, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5666, 14, 106, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5667, 14, 107, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5668, 14, 119, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5669, 14, 108, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5670, 14, 109, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5671, 14, 110, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5672, 14, 112, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5673, 14, 117, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5674, 14, 115, '0', 'view', 15, '2026-05-21 05:31:36', 9),
(5675, 14, 114, '1', 'view,add,edit,delete', 15, '2026-05-21 05:31:36', 9),
(5676, 15, 18, '0', 'view', 15, '2026-05-21 05:33:13', 9),
(5677, 15, 113, '1', 'view,add,edit,delete', 15, '2026-05-21 05:33:13', 9),
(5772, 16, 1, '1', 'view', 15, '2026-05-25 06:50:21', 9),
(5773, 16, 2, '1', 'view', 15, '2026-05-25 06:50:21', 9),
(5774, 16, 3, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5775, 16, 4, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5776, 16, 5, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5777, 16, 6, '1', 'view', 15, '2026-05-25 06:50:21', 9),
(5778, 16, 7, '1', 'view', 15, '2026-05-25 06:50:21', 9),
(5779, 16, 8, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5780, 16, 9, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5781, 16, 10, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5782, 16, 100, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5783, 16, 11, '1', 'view', 15, '2026-05-25 06:50:21', 9),
(5784, 16, 12, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5785, 16, 13, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5786, 16, 14, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5787, 16, 15, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5788, 16, 16, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5789, 16, 18, '1', 'view', 15, '2026-05-25 06:50:21', 9),
(5790, 16, 19, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5791, 16, 101, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5792, 16, 20, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5793, 16, 111, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5794, 16, 113, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5795, 16, 118, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5796, 16, 21, '1', 'view', 15, '2026-05-25 06:50:21', 9),
(5797, 16, 22, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5798, 16, 23, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5799, 16, 25, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5800, 16, 116, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5801, 16, 26, '1', 'view', 15, '2026-05-25 06:50:21', 9),
(5802, 16, 35, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5803, 16, 28, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5804, 16, 29, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5805, 16, 30, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5806, 16, 103, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5807, 16, 104, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5808, 16, 105, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5809, 16, 106, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5810, 16, 107, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5811, 16, 119, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5812, 16, 108, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5813, 16, 109, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5814, 16, 110, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5815, 16, 112, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5816, 16, 117, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5817, 16, 31, '1', 'view', 15, '2026-05-25 06:50:21', 9),
(5818, 16, 32, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5819, 16, 115, '1', 'view', 15, '2026-05-25 06:50:21', 9),
(5820, 16, 114, '1', 'view,add,edit,delete', 15, '2026-05-25 06:50:21', 9),
(5821, 13, 1, '1', 'view', 15, '2026-05-25 06:53:23', 9),
(5822, 13, 6, '1', 'view', 15, '2026-05-25 06:53:23', 9),
(5823, 13, 7, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5824, 13, 8, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5825, 13, 9, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5826, 13, 10, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5827, 13, 100, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5828, 13, 11, '1', 'view', 15, '2026-05-25 06:53:23', 9),
(5829, 13, 12, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5830, 13, 13, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5831, 13, 14, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5832, 13, 15, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5833, 13, 16, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5834, 13, 18, '1', 'view', 15, '2026-05-25 06:53:23', 9),
(5835, 13, 19, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5836, 13, 101, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5837, 13, 20, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5838, 13, 111, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5839, 13, 113, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5840, 13, 118, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5841, 13, 21, '1', 'view', 15, '2026-05-25 06:53:23', 9),
(5842, 13, 22, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5843, 13, 23, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5844, 13, 25, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5845, 13, 116, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5846, 13, 26, '1', 'view', 15, '2026-05-25 06:53:23', 9),
(5847, 13, 35, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5848, 13, 28, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5849, 13, 29, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5850, 13, 30, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5851, 13, 103, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5852, 13, 104, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5853, 13, 105, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5854, 13, 106, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5855, 13, 107, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5856, 13, 119, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5857, 13, 108, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5858, 13, 109, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5859, 13, 110, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5860, 13, 112, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5861, 13, 117, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5862, 13, 31, '1', 'view', 15, '2026-05-25 06:53:23', 9),
(5863, 13, 32, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9),
(5864, 13, 115, '1', 'view', 15, '2026-05-25 06:53:23', 9),
(5865, 13, 114, '1', 'view,add,edit,delete', 15, '2026-05-25 06:53:23', 9);

-- --------------------------------------------------------

--
-- Table structure for table `route`
--

CREATE TABLE `route` (
  `route_id` int(11) NOT NULL,
  `routes` varchar(100) NOT NULL,
  `status` enum('0','1') DEFAULT '1',
  `create_by` varchar(50) DEFAULT NULL,
  `modifiy_by` varchar(50) DEFAULT NULL,
  `create_date_time` datetime DEFAULT current_timestamp(),
  `org_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `route`
--

INSERT INTO `route` (`route_id`, `routes`, `status`, `create_by`, `modifiy_by`, `create_date_time`, `org_id`) VALUES
(1, 'Oral', '1', '0', '0', '2025-06-02 10:03:03', 0),
(2, 'Intravenous (IV)', '1', '0', '0', '2025-06-02 10:03:03', 0),
(3, 'Topical', '1', '0', '0', '2025-06-02 10:03:03', 0),
(4, 'Inhalation', '1', '0', '0', '2025-06-02 10:03:03', 0),
(9, '', '1', '2', NULL, '2025-06-02 16:21:27', 1),
(10, '', '1', '2', NULL, '2025-06-02 17:54:10', 1);

-- --------------------------------------------------------

--
-- Table structure for table `rx_groups`
--

CREATE TABLE `rx_groups` (
  `rx_id` int(11) NOT NULL,
  `rx_group_id` int(11) NOT NULL,
  `rx_group_name` varchar(100) NOT NULL,
  `medicine_name` varchar(225) NOT NULL,
  `medicine_type` varchar(225) NOT NULL,
  `dosage` longtext NOT NULL,
  `unit` varchar(100) NOT NULL,
  `timing` varchar(100) NOT NULL,
  `in_time_period` varchar(100) NOT NULL,
  `frequency` varchar(50) NOT NULL,
  `duration` varchar(50) NOT NULL,
  `quantity` varchar(100) NOT NULL,
  `notes` longtext NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `created_by` int(11) NOT NULL,
  `modify_by` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `create_date_time` datetime NOT NULL,
  `modify_date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rx_groups`
--

INSERT INTO `rx_groups` (`rx_id`, `rx_group_id`, `rx_group_name`, `medicine_name`, `medicine_type`, `dosage`, `unit`, `timing`, `in_time_period`, `frequency`, `duration`, `quantity`, `notes`, `status`, `created_by`, `modify_by`, `org_id`, `create_date_time`, `modify_date_time`) VALUES
(3, 1, 'Cough', 'JBTOR - (TORSEMIDE)', 'Tab', '6', '25MG', '10', '8', '', '3 Days', '', 'testing', '1', 2, 2, 1, '0000-00-00 00:00:00', '2025-09-30 04:06:28'),
(4, 1, 'Cough', 'GABACOX - M - (GABAPENTIN + METHYLCOBALAMIN)', 'Syp', '5', '50MG', '1', '8', '', '3 Days', '', '', '1', 2, 2, 1, '0000-00-00 00:00:00', '2025-09-30 04:06:28');

-- --------------------------------------------------------

--
-- Table structure for table `rx_groups_names`
--

CREATE TABLE `rx_groups_names` (
  `rx_group_id` int(11) NOT NULL,
  `rx_group_name` varchar(50) NOT NULL,
  `medicine_detailes` longtext DEFAULT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `create_by` int(11) NOT NULL,
  `modify_by` int(11) NOT NULL,
  `create_date_time` datetime NOT NULL,
  `org_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rx_groups_names`
--

INSERT INTO `rx_groups_names` (`rx_group_id`, `rx_group_name`, `medicine_detailes`, `status`, `create_by`, `modify_by`, `create_date_time`, `org_id`) VALUES
(1, 'Cough', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(2, 'RXGroupnew', '[{\"medicine_id\":\"1\",\"medicine_name\":\"ECOSPRIN - (ASPIRIN)\",\"type_id\":\"Tab\",\"type_text\":\"Tab\",\"unit_id\":\"10\\/50MG\",\"unit_text\":\"10\\/50MG\",\"dosage_id\":\"5\",\"when_id\":\"8\",\"time_id\":\"1\",\"duration_value\":\"12\",\"duration\":\"\",\"notes\":\"\",\"med_status\":\"1\",\"timeText\":\"9AM-0-9PM\",\"dosageText\":\"1-0-1\",\"whenText\":\"After Food\"},{\"medicine_id\":\"2\",\"medicine_name\":\"ECOSPRIN - (ASPIRIN)\",\"type_id\":\"Tab\",\"type_text\":\"Tab\",\"unit_id\":\"40\\/25MG\",\"unit_text\":\"40\\/25MG\",\"dosage_id\":\"2\",\"when_id\":\"7\",\"time_id\":\"4\",\"duration_value\":\"12\",\"duration\":\"\",\"notes\":\"\",\"med_status\":\"1\",\"timeText\":\"0-2PM-0\",\"dosageText\":\"0-1-0\",\"whenText\":\"Before Food\"}]', '1', 2, 2, '0000-00-00 00:00:00', 1),
(3, 'test', '[{\"medicine_id\":\"9\",\"medicine_name\":\"ECOSPRIN AV - (ASPIRIN + ATORVASTATIN)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"10\",\"unit_text\":\"10/25MG\",\"dosage_id\":\"5\",\"when_id\":\"8\",\"time_id\":\"1\",\"duration_value\":\"5\",\"duration\":\"Days\",\"notes\":\"\",\"med_status\":\"1\",\"timeText\":\"9AM-0-9PM\",\"dosageText\":\"1-0-1\",\"whenText\":\"After Food\"}]', '1', 2, 2, '2025-10-06 17:21:35', 1),
(4, 'testing\npurpose\n123@', '[{\"medicine_id\":\"11\",\"medicine_name\":\"GABACOX - M - (GABAPENTIN + METHYLCOBALAMIN)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"10\",\"unit_text\":\"10/25MG\",\"dosage_id\":\"3\",\"when_id\":\"8\",\"time_id\":\"3\",\"duration_value\":\"4\",\"duration\":\"Days\",\"notes\":\"\",\"med_status\":\"1\",\"timeText\":\"0-0-9PM\",\"dosageText\":\"0-0-1\",\"whenText\":\"After Food\"},{\"medicine_id\":\"10\",\"medicine_name\":\"EMBETA TM - (METAPROLOL + TELMISARTAN)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"14\",\"unit_text\":\"50MG\",\"dosage_id\":\"6\",\"when_id\":\"8\",\"time_id\":\"10\",\"duration_value\":\"4\",\"duration\":\"Days\",\"notes\":\"\",\"med_status\":\"1\",\"timeText\":\"0-2PM-9PM\",\"dosageText\":\"0-1-1\",\"whenText\":\"After Food\"}]', '1', 2, 2, '2026-05-06 12:35:37', 1),
(5, 'Fever', '[{\"medicine_id\":\"90\",\"medicine_name\":\"DOLO 50 - (PARACETAMOL IP)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"12\",\"unit_text\":\"500MG\",\"dosage_id\":\"2\",\"when_id\":\"8\",\"time_id\":\"4\",\"duration_value\":\"5\",\"duration\":\"0-2PM-0\",\"notes\":\"0-1-0\",\"med_status\":\"After Food\",\"timeText\":\"0-2PM-0\",\"dosageText\":\"0-1-0\",\"whenText\":\"After Food\"},{\"medicine_id\":\"12\",\"medicine_name\":\"GLOBIRED - (FERROUS ASCORBATE + FOLIC ACID)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"7\",\"unit_text\":\"75/20MG\",\"dosage_id\":\"5\",\"when_id\":\"8\",\"time_id\":\"1\",\"duration_value\":\"5\",\"duration\":\"9AM-0-9PM\",\"notes\":\"1-0-1\",\"med_status\":\"After Food\",\"timeText\":\"9AM-0-9PM\",\"dosageText\":\"1-0-1\",\"whenText\":\"After Food\"}]', '1', 2, 2, '2026-05-20 14:52:33', 1),
(6, 'Heart Attack', '[{\"medicine_id\":\"96\",\"medicine_name\":\"FOLIC ACID 5MG - (PTEROYLMONOGLUTAMIC ACID)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":\"2\",\"unit_text\":\"5mg\",\"dosage_id\":\"3\",\"when_id\":\"8\",\"time_id\":\"3\",\"duration_value\":\"5\",\"duration\":\"0-0-9PM\",\"notes\":\"0-0-1\",\"med_status\":\"After Food\",\"timeText\":\"0-0-9PM\",\"dosageText\":\"0-0-1\",\"whenText\":\"After Food\"},{\"medicine_id\":\"97\",\"medicine_name\":\"PROGESTERONE 200MG - (MICRONIZED PROGESTERONE)\",\"type_id\":\"1\",\"type_text\":\"Tab\",\"unit_id\":31,\"unit_text\":\"200mg\",\"dosage_id\":\"4\",\"when_id\":\"8\",\"time_id\":\"2\",\"duration_value\":\"4\",\"duration\":\"9AM-2PM-0\",\"notes\":\"1-1-0\",\"med_status\":\"After Food\",\"timeText\":\"9AM-2PM-0\",\"dosageText\":\"1-1-0\",\"whenText\":\"After Food\"}]', '1', 15, 15, '2026-05-21 14:46:03', 9);

-- --------------------------------------------------------

--
-- Table structure for table `security`
--

CREATE TABLE `security` (
  `security_id` int(11) NOT NULL,
  `admin_name` varchar(30) NOT NULL,
  `user_code` varchar(20) DEFAULT NULL,
  `email` varchar(30) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `security_password` varchar(100) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `signature_url` varchar(225) NOT NULL,
  `role_id` int(11) NOT NULL,
  `can_switch_doctor` tinyint(1) NOT NULL DEFAULT 0,
  `security_type` enum('SA','A','U') DEFAULT NULL,
  `org_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `create_date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `security`
--

INSERT INTO `security` (`security_id`, `admin_name`, `user_code`, `email`, `contact`, `security_password`, `image_url`, `signature_url`, `role_id`, `can_switch_doctor`, `security_type`, `org_id`, `created_by`, `modified_by`, `status`, `create_date_time`) VALUES
(1, 'superadmin', NULL, 'superadmin@gmail.com', '9985460049', '827ccb0eea8a706c4c34a16891f84e7b', ' - 2025.04.22 - 05.05.26pm.png', '', 1, 0, 'SA', 0, 0, 0, '1', '2025-09-24 04:44:55'),
(2, 'Dr.Ashwin Kumar Panda', 'D001', 'pandas@gmail.com', '8897355655', '827ccb0eea8a706c4c34a16891f84e7b', ' - 2025.09.19 - 12.34.59pm.png', '', 2, 0, 'A', 1, 1, 2, '1', '2026-05-07 12:50:18'),
(3, 'Dr Jayas', NULL, 'drjayaderma@gmail.com', '7095678678', '827ccb0eea8a706c4c34a16891f84e7b', ' - 2023.10.20 - 12.45.53pm.png', '', 2, 0, NULL, 3, 1, 1, '1', '2023-11-03 16:28:09'),
(4, 'Usha Mylapalli', NULL, 'ushasravani9121@gmail.com', '7569974256', '1136800ee86f59bf852cf15a32eae2c5', '', '', 6, 0, NULL, 1, 2, 2, '0', '2025-09-18 04:20:56'),
(9, 'Pravallika', 'D002', 'test0@gmail.com', '7032760271', '827ccb0eea8a706c4c34a16891f84e7b', '', '', 2, 0, 'U', 1, 2, 2, '1', '2026-05-07 12:50:24'),
(10, 'Administrator', 'D003', 'durgalaxmi417@gmail.com', '6302669660', '827ccb0eea8a706c4c34a16891f84e7b', '', '', 12, 0, 'U', 1, 2, 2, '1', '2026-05-21 09:43:19'),
(11, 'test', NULL, 'test@gmail.com', '6354675768', '827ccb0eea8a706c4c34a16891f84e7b', '', '', 2, 0, NULL, 1, 2, 2, '0', '2025-09-18 04:21:03'),
(12, 'Dr. Venkatesh', 'R001', 'ven@gmail.com', '6302669664', '827ccb0eea8a706c4c34a16891f84e7b', '', '', 2, 0, 'U', 1, 2, 2, '1', '2026-05-07 05:34:24'),
(13, 'ravi', NULL, 'ravi@gmail.com', '7095678679', '827ccb0eea8a706c4c34a16891f84e7b', '', '', 11, 0, 'U', 1, 2, 2, '0', '2026-05-06 07:11:55'),
(14, 'Dr. Rohith', 'R002', 'rohith@gmail.com', '7032760275', '827ccb0eea8a706c4c34a16891f84e7b', '', '', 2, 0, 'U', 1, 2, 2, '1', '2026-05-07 05:36:06'),
(15, 'Dr. Ravi Teja', 'D004', 'raviteja@gmail.com', '7032760279', '827ccb0eea8a706c4c34a16891f84e7b', '', '', 2, 0, 'A', 9, 1, 1, '1', '2026-05-21 09:43:19'),
(16, 'Dr. Kishore', 'D005', 'kishore@gmail.com', '7095678670', '827ccb0eea8a706c4c34a16891f84e7b', '', '', 13, 0, 'U', 9, 15, 15, '1', '2026-05-21 12:41:27'),
(17, 'Durga Lakshmi', 'R003', 'durga123@gmail.com', '7032760277', '827ccb0eea8a706c4c34a16891f84e7b', '', '', 14, 0, 'U', 9, 15, 15, '1', '2026-05-21 09:43:19'),
(18, 'Likhith', 'P002', 'likhith@gmail.com', '9000786945', '827ccb0eea8a706c4c34a16891f84e7b', '', '', 15, 0, 'U', 9, 15, 15, '1', '2026-05-21 05:47:53');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `price` int(255) NOT NULL,
  `service_GST` varchar(255) NOT NULL,
  `org_id` int(11) NOT NULL,
  `status` enum('0','1','','') NOT NULL DEFAULT '1',
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `c_d_t` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `service_name`, `price`, `service_GST`, `org_id`, `status`, `created_by`, `modified_by`, `c_d_t`) VALUES
(1, 'Consultation', 500, '0', 1, '1', 2, 2, '2025-09-18 04:33:56'),
(2, 'Consultation', 600, '0', 9, '1', 15, 15, '2026-05-21 05:57:36');

-- --------------------------------------------------------

--
-- Table structure for table `specialtis`
--

CREATE TABLE `specialtis` (
  `specialtis_id` int(11) NOT NULL,
  `specialtisname` varchar(255) NOT NULL,
  `status` enum('0','1','','') NOT NULL DEFAULT '1',
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `org_id` int(11) NOT NULL,
  `create_date_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `specialtis`
--

INSERT INTO `specialtis` (`specialtis_id`, `specialtisname`, `status`, `created_by`, `modified_by`, `org_id`, `create_date_time`) VALUES
(1, 'osteopaths', '0', 2, 2, 1, '2023-10-17 07:29:06'),
(2, 'General surgeons', '0', 2, 2, 1, '2023-10-17 07:29:28'),
(3, 'Ophthalmology', '0', 2, 2, 1, '2023-10-17 13:10:33'),
(5, 'CARDIOLOGIST', '1', 1, 1, 2, '2023-10-18 09:02:39'),
(6, 'GYNAECOLOGIST', '1', 1, 1, 2, '2023-10-18 09:02:46'),
(7, 'ENT SPECIALIST', '1', 1, 1, 2, '2023-10-18 09:02:56'),
(8, 'PATHOLOGIST', '1', 1, 1, 2, '2023-10-18 10:24:22'),
(15, 'Dental specialist', '1', 6, 6, 5, '2025-04-28 05:44:47'),
(16, 'Cardiology', '1', 7, 7, 6, '2025-04-28 08:59:13'),
(17, 'Dental Public Health', '1', 6, 6, 7, '2025-05-07 05:59:13'),
(18, 'Dentist', '1', 2, 2, 1, '2025-06-04 06:44:48'),
(19, 'cardilogy', '0', 2, 2, 1, '2025-09-17 04:53:28'),
(20, 'Heart disceases', '1', 15, 15, 9, '2026-05-21 05:54:04'),
(21, 'gynec disceases', '1', 15, 15, 9, '2026-05-21 05:54:50');

-- --------------------------------------------------------

--
-- Table structure for table `taxes`
--

CREATE TABLE `taxes` (
  `tax_id` int(11) NOT NULL,
  `cgstNumber` varchar(255) NOT NULL,
  `sgstNumber` varchar(255) NOT NULL,
  `percentage` varchar(25) NOT NULL,
  `org_id` int(11) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `create_date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL,
  `modify_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `test_id` int(11) NOT NULL,
  `test_name` varchar(50) NOT NULL,
  `test_price` int(11) NOT NULL,
  `test_gst` varchar(255) NOT NULL,
  `normal_range` varchar(250) DEFAULT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `create_date_time` datetime NOT NULL,
  `org_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`test_id`, `test_name`, `test_price`, `test_gst`, `normal_range`, `status`, `created_by`, `modified_by`, `create_date_time`, `org_id`) VALUES
(1, 'Blood Group & Rh Type', 110, '0', '250 -500', '1', 2, 2, '0000-00-00 00:00:00', 1),
(2, 'Blood Urea Nitrogen Serum', 140, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(3, 'Complete Blood Counts', 350, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(4, 'C-Reactive Protein Serum', 400, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(5, 'Creatinine Serum', 200, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(6, 'D-Dimer', 1500, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(7, 'Electrolytes Serum', 400, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(8, 'ESR 1 Hour', 120, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(9, 'Glucose Post Prandial - 2hrs Plasma', 80, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(10, 'Glucose-Fasting Plasma', 80, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(11, 'Glucose-Random Plasma', 80, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(12, 'Complete Haemogram', 600, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(13, 'HbA1c', 400, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(14, 'Hemoglobin', 120, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(15, 'High Sensitivity CRP', 800, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(16, 'Homocystiene Serum', 1500, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(17, 'Serum Iron Studies', 520, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(18, 'Lipid profile', 500, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(19, 'Liver Function Test', 680, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(20, 'Thyroid profile', 500, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(21, 'TSH (Ultrasensitive) Serum', 370, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(22, 'Urea Serum', 120, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(23, 'Uric Acid Serum', 200, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(24, 'Urine Routine Examination', 150, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(25, 'Vitamin B12 Serum', 1350, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(26, 'VITAMIN D 1 25- DIHYDROXY#', 4000, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(27, 'Vitamin D total - 25 hydroxy (Serum)', 1500, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(28, 'Ferritin Serum', 900, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(29, 'Thyroid profile - II (Free T3 and T4)', 650, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(30, 'ECG', 400, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(31, '2D ECHO', 1500, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(32, 'TREADMILL TEST', 1500, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(33, 'Activated Partial Thrombo- plastin Time', 600, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(34, 'Calcium Serum', 225, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(35, 'Culture Aerobic Blood', 1100, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(36, 'Culture Aerobic PUS', 1000, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(37, 'Culture Aerobic', 1000, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(38, 'Culture Aerobic Sputum', 1000, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(39, 'Culture Aerobic Throat swab', 1200, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(40, 'Culture Aerobic Urine', 750, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(41, 'Dengue IgG Serum', 900, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(42, 'Dengue IgM Serum', 900, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(43, 'Dengue NS1Ag Serum', 1100, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(44, 'Ferritin Serum', 900, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(45, 'Free T3 Serum', 400, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(46, 'Free T4 Serum', 400, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(47, 'Lipase Serum', 600, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(48, 'Malarial parasites by QBC', 200, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(49, 'Prostate Specific Antigen Serum', 1100, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(50, 'Rheumatoid Factor Serum', 600, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(51, 'Smear for Malarial parasites', 250, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(52, 'WIDAL Serum', 300, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(53, 'Anti Cardiolipin IgG Serum', 800, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(54, 'Anti Cardiolipin IgM Serum', 800, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(55, 'Anti Phospholipid IgG#', 840, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(56, 'Anti Phospholipid IgM#', 840, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(57, 'CARDIOLIPIN ANTIBODIES PANEL IgG IgA & IgM#', 4000, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(58, 'Fibrinogen level', 1100, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(59, 'PROTEIN C FUNCTIONAL #', 5000, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(60, 'PROTEIN S FUNCTIONAL #', 4000, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(61, 'LUPUS ANTICOAGULANT#', 1000, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(62, 'FACTOR V LEIDEN MUTATION', 3000, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(63, '24 hrs Urine Protein', 300, '0', '23-50', '1', 2, 2, '0000-00-00 00:00:00', 1),
(64, 'Adenosine Deaminase', 700, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(65, 'Amylase Serum', 530, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(66, 'Amylase Fluid', 530, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(67, 'ANA PROFILE (Auto antibody profile)', 4500, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(68, 'Angiotensin Converting Enzyme Serum#', 1100, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(69, 'Anti DS DNA by IFA', 1200, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(70, 'Anti HAV IgM Serum', 1190, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(71, 'Anti HBc IgM Serum', 1100, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(72, 'Anti HBc Total Serum', 1080, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(73, 'Anti HBe Serum', 1100, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(74, 'Anti HBs Serum', 1170, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(75, 'Anti HCV Serum', 1200, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(76, 'Anti Nuclear Antibody by ELISA Serum', 700, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(77, 'Anti Nuclear Antibody by IFA', 1000, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(78, 'ANTI THYROID ANTIBODIES PANEL', 2400, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(79, 'Anti-CCP Serum', 2000, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(80, 'ASO Titre Serum', 600, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(81, 'c-ANCA(PR3) Serum', 1250, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(82, 'CYTOLOGY (PAP SMEAR) (LBC)', 1200, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(83, 'Direct Coombs Test', 475, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(84, 'ELECTROLYTES RANDOM URINE', 500, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(85, 'Folic Acid Serum', 1500, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(86, 'GAD 65 Antibody', 6500, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(87, 'HBeAg', 1200, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(88, 'HCV Genotyping#', 6500, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(89, 'HISTOPATHOLOGY BIOPSY LARGE COMPLEX', 6500, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(90, 'HISTOPATHOLOGY BIOPSY MEDIUM SPECIMEN', 800, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(91, 'HISTOPATHOLOGY BIOPSY SMALL SPECIMEN', 800, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(92, 'HIV -1&2 WESTERN BLOT Serum', 3500, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(93, 'HIV PROVIRAL DNA QUALITATIVE#', 7000, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(94, 'HIV-1 RNA Detection by Real Time PCR', 7000, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(95, 'HLA - B27 Flowcytometry', 2500, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(96, 'HLA - B27 Flowcytometry', 2500, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(97, 'Immunoglobulin E Serum', 950, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(98, 'Indirect Coombs Test', 600, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(99, 'Insulin Fasting Serum', 900, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(100, 'Insulin-Random Serum', 1000, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(101, 'Leptospira IgG Serum#', 1400, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(102, 'Leptospira IgM Serum', 1400, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(103, 'Osmolality Serum#', 690, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(104, 'Osmolality Urine#', 690, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(105, 'p-ANCA(MPO) Serum', 1500, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(106, 'PSA PROFILE', 1500, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(107, 'Thyroglobulin Antibody Serum', 1300, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(108, 'Thyroglobulin Serum', 1650, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(109, 'Thyroid Peroxidase Antibody Serum', 1250, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(110, 'HEPATITIS B SURFACE ANTIGEN (HBsAg) QUANTITATIVE#', 1400, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(111, 'HEPATITIS B VIRUS (HBV) DNA QUANTITATIVE TEST- REA', 6000, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(112, 'HEPATITIS C VIRUS (HCV) RNA QUANTIFICATION', 8000, '0', NULL, '1', 2, 2, '0000-00-00 00:00:00', 1),
(113, 'Complete Blood Picture', 350, '0', NULL, '1', 2, 2, '2023-09-14 11:42:13', 1),
(114, 'Kidney Function Test', 600, '0', NULL, '1', 2, 2, '2023-09-14 12:28:50', 1),
(115, 'Iron Studies', 700, '0', NULL, '1', 2, 2, '2023-09-14 12:29:06', 1),
(116, 'Calcium', 100, '0', NULL, '1', 2, 2, '2023-09-14 12:49:44', 1),
(117, 'Phosphorus', 100, '0', NULL, '1', 2, 2, '2023-09-14 12:50:01', 1),
(118, 'Vitamin D', 1000, '0', NULL, '1', 2, 2, '2023-09-14 12:50:19', 1),
(119, 'CUE', 100, '0', NULL, '1', 2, 2, '2023-09-14 12:50:34', 1),
(120, 'ESR', 100, '0', NULL, '1', 2, 2, '2023-09-14 12:50:46', 1),
(121, 'Uric Acid', 200, '0', NULL, '1', 2, 2, '2023-09-14 12:51:01', 1),
(122, 'RBS', 80, '0', NULL, '1', 2, 2, '2023-09-14 12:51:12', 1),
(123, 'Folic Acid', 450, '0', NULL, '1', 2, 2, '2023-09-14 12:51:30', 1),
(124, 'Vitamin B12', 450, '0', NULL, '1', 2, 2, '2023-09-14 12:52:28', 1),
(125, 'eGFR', 50, '0', NULL, '1', 2, 2, '2023-09-14 12:52:45', 1),
(126, 'hs CRP', 800, '0', NULL, '0', 2, 1, '2023-09-14 12:58:06', 1),
(127, 'widal', 650, '12', NULL, '1', 2, 2, '2023-10-16 18:34:16', 2),
(128, 'hs CRP', 100, '1%', NULL, '0', 2, 2, '2023-10-18 11:47:21', 1),
(129, 'CBC', 350, '3', NULL, '1', 1, 1, '2023-10-18 16:02:10', 2),
(130, 'THYROID PROFILE', 900, '5', NULL, '1', 1, 1, '2023-10-18 16:02:30', 2),
(131, 'ECG', 150, '5', NULL, '1', 1, 1, '2023-10-18 16:02:42', 2),
(132, 'SERUM CREATINE', 300, '3', NULL, '0', 1, 1, '2023-10-18 16:03:20', 2),
(133, 'SV HEALTH CARE', 50, '3', NULL, '0', 1, 1, '2023-10-20 11:44:58', 1),
(134, 'SV HEALTH CARE', 50, '3', NULL, '0', 1, 1, '2023-10-20 11:45:34', 1),
(147, 'HISTOPATHOLOGY BIOPSY SMALL SPECIMEN', 200, '0', NULL, '1', 1, 1, '2025-06-03 16:38:46', 0),
(148, 'HIV PROVIRAL DNA QUALITATIVE#', 6000, '0', NULL, '1', 1, 1, '2025-06-03 16:38:46', 0),
(149, 'blood test', 1010, '3', '10-20 units', '1', 2, 2, '2025-09-12 09:55:46', 1),
(150, 'test name recent', 1575, '5', '50-150', '0', 2, 2, '2025-09-17 12:09:37', 1),
(151, 't1', 100, '18', '10-20 units', '1', 2, 2, '2025-09-25 13:04:03', 1),
(152, 't2', 200, '18', '20-40 units', '1', 2, 2, '2025-09-25 13:04:03', 1),
(153, 't3', 0, '18', '10-20 units', '1', 2, 2, '2025-09-25 13:08:42', 1),
(154, 'gf', 30, '0', '23-50', '1', 12, 12, '2025-09-25 16:34:50', 1),
(155, 'ECG (Electrocardiogram)', 300, '0', 'Normal sinus rhythm 60-100 bpm', '1', 1, 1, '2026-05-21 11:32:12', 9),
(156, 'Echocardiogram', 1500, '12', 'EF > 55%, No wall motion abnormality', '1', 1, 1, '2026-05-21 11:32:12', 9),
(157, 'Lipid Profile', 500, '0', 'Total Chol < 200, LDL < 100 mg/dL', '1', 1, 1, '2026-05-21 11:32:12', 9),
(158, 'Troponin T Test', 800, '0', 'Troponin T < 0.01 ng/mL', '1', 1, 1, '2026-05-21 11:32:12', 9),
(159, 'Holter Monitor (24hr)', 2000, '12', 'No significant arrhythmia', '1', 1, 1, '2026-05-21 11:32:12', 9),
(160, 'Pap Smear', 600, '0', 'Negative for intraepithelial lesion', '1', 1, 1, '2026-05-21 11:32:12', 9),
(161, 'Pelvic Ultrasound', 1200, '12', 'Normal uterus and ovaries', '1', 1, 1, '2026-05-21 11:32:12', 9),
(162, 'Beta HCG (Pregnancy Test)', 400, '0', 'Non-pregnant: < 5 mIU/mL', '1', 1, 1, '2026-05-21 11:32:12', 9),
(163, 'FSH / LH Hormones', 700, '0', 'FSH: 3-10 mIU/mL; LH: 2-15 mIU/mL', '1', 1, 1, '2026-05-21 11:32:12', 9),
(164, 'Colposcopy', 2500, '12', 'No abnormal cervical findings', '1', 1, 1, '2026-05-21 11:32:12', 9),
(165, 'test', 85, '70', '23-50', '0', 15, 15, '2026-05-21 11:35:29', 9),
(166, 'HISTOPATHOLOGY BIOPSY MEDIUM SPECIMEN', 0, '0', NULL, '1', 15, 15, '2026-05-21 17:03:43', 9);

-- --------------------------------------------------------

--
-- Table structure for table `test_group`
--

CREATE TABLE `test_group` (
  `test_group_id` int(11) NOT NULL,
  `test_group_name` varchar(255) NOT NULL,
  `test_id` longtext NOT NULL,
  `test_group_price` int(11) NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `create_date_time` datetime NOT NULL,
  `org_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `test_group`
--

INSERT INTO `test_group` (`test_group_id`, `test_group_name`, `test_id`, `test_group_price`, `status`, `created_by`, `modified_by`, `create_date_time`, `org_id`) VALUES
(1, 'Med pack', '[{\"investigation\":\"2D ECHO\",\"instruction\":\"\",\"price\":\"7500\",\"test_group_id\":\"\",\"test_group_name\":\"Med pack\",\"test_group_price\":\"\"},{\"investigation\":\"Adenosine Deaminase\",\"instruction\":\"\",\"price\":\"7500\",\"test_group_id\":\"\",\"test_group_name\":\"Med pack\",\"test_group_price\":\"\"},{\"investigation\":\"Amylase Serum\",\"instruction\":\"\",\"price\":\"7500\",\"test_group_id\":\"\",\"test_group_name\":\"Med pack\",\"test_group_price\":\"\"}]', 7500, '0', 2, 2, '2025-09-17 12:35:12', 1);

-- --------------------------------------------------------

--
-- Table structure for table `times`
--

CREATE TABLE `times` (
  `time_id` int(11) NOT NULL,
  `time` varchar(255) NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `create_by` int(11) NOT NULL,
  `create_date_time` datetime NOT NULL,
  `org_id` int(11) NOT NULL,
  `dose_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `times`
--

INSERT INTO `times` (`time_id`, `time`, `status`, `create_by`, `create_date_time`, `org_id`, `dose_id`) VALUES
(1, '9AM-0-9PM', '1', 1, '2023-06-27 05:56:58', 1, 5),
(2, '9AM-2PM-0', '1', 1, '2023-06-27 05:56:58', 1, 4),
(3, '0-0-9PM', '1', 1, '2023-06-27 05:56:58', 1, 3),
(4, '0-2PM-0', '1', 1, '2023-06-27 05:56:58', 1, 2),
(5, '0-4PM-0', '1', 1, '2023-06-27 05:56:58', 1, 2),
(6, '9AM-0-0', '1', 1, '2023-06-27 05:56:58', 1, 1),
(7, '9AM-4PM-0', '1', 1, '2023-06-27 05:56:58', 1, 4),
(8, '9AM-2PM-9PM', '1', 1, '2025-04-10 06:48:00', 1, 7),
(9, '9AM-4PM-9PM', '1', 1, '2025-04-10 06:49:18', 1, 7),
(10, '0-2PM-9PM', '1', 1, '2025-04-10 08:06:36', 1, 6),
(11, '0-4PM-9PM', '1', 1, '2025-04-10 08:07:25', 1, 6),
(12, '6AM-0-0', '1', 1, '2025-07-01 09:12:49', 1, 1),
(13, '6AM-0-6PM', '1', 1, '2025-07-01 09:13:29', 1, 5),
(14, '--', '1', 1, '2025-07-01 09:15:52', 1, 8);

-- --------------------------------------------------------

--
-- Table structure for table `truncate_logs`
--

CREATE TABLE `truncate_logs` (
  `truncate_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `org_id` int(11) DEFAULT NULL,
  `action` text NOT NULL,
  `log_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `truncate_logs`
--

INSERT INTO `truncate_logs` (`truncate_id`, `user_id`, `org_id`, `action`, `log_time`) VALUES
(1, 1, 0, 'Table \'taxes\' truncated by User ID 1 (Type: SA, Org ID: 0)', '2025-10-07 17:04:45');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `unit_id` int(11) NOT NULL,
  `unit_name` varchar(100) NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `create_by` int(11) NOT NULL,
  `create_date_time` datetime NOT NULL,
  `org_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`unit_id`, `unit_name`, `status`, `create_by`, `create_date_time`, `org_id`) VALUES
(1, '2.5MG', '1', 1, '2023-06-27 05:53:10', 1),
(2, '5MG', '1', 1, '2023-06-27 05:53:10', 1),
(3, '625MG', '1', 1, '2023-06-27 05:53:10', 1),
(4, '10MG', '1', 1, '2023-06-27 05:53:10', 1),
(5, '150MG', '1', 1, '2023-06-27 05:54:11', 1),
(6, '75MG', '1', 1, '2023-06-27 05:54:11', 1),
(7, '75/20MG', '1', 1, '2023-06-27 05:54:11', 1),
(8, '40/25MG', '1', 1, '2023-06-27 05:54:11', 1),
(9, '40/50MG', '1', 1, '2023-09-09 06:51:46', 1),
(10, '10/25MG', '1', 1, '2023-09-09 06:51:46', 1),
(11, '10/50MG', '1', 1, '2023-09-09 06:51:46', 1),
(12, '500MG', '1', 1, '2023-09-09 06:51:46', 1),
(13, '25MG', '1', 1, '2023-09-09 06:51:46', 1),
(14, '50MG', '1', 1, '2023-09-09 06:51:46', 1),
(15, '2.6MG', '1', 1, '2023-09-09 06:51:46', 1),
(16, '25/5MG', '1', 1, '2023-09-09 06:51:46', 1),
(17, '50/5MG', '1', 1, '2023-09-09 06:51:46', 1),
(18, '25/40MG', '1', 1, '2023-09-09 06:51:46', 1),
(19, '50/40MG', '1', 1, '2023-09-09 06:51:46', 1),
(20, '20MG', '1', 1, '2023-09-09 06:51:46', 1),
(21, '75/20MG', '1', 1, '2023-09-09 06:51:46', 1),
(22, '40MG', '1', 1, '2023-09-09 06:51:46', 1),
(23, '90MG', '1', 1, '2023-09-09 06:51:46', 1),
(24, '40/5MG', '1', 1, '2023-09-09 06:51:46', 1),
(31, '200mg', '1', 15, '2026-05-21 14:46:03', 9);

-- --------------------------------------------------------

--
-- Table structure for table `vitals`
--

CREATE TABLE `vitals` (
  `vital_id` int(11) NOT NULL,
  `BPsit` varchar(225) DEFAULT NULL,
  `BPstand` varchar(225) DEFAULT NULL,
  `weight` varchar(225) DEFAULT NULL,
  `height` varchar(225) DEFAULT NULL,
  `GRBS` varchar(225) DEFAULT NULL,
  `heartrate` varchar(225) DEFAULT NULL,
  `temperature` varchar(225) DEFAULT NULL,
  `resp` varchar(225) DEFAULT NULL,
  `sp02percent` varchar(225) DEFAULT NULL,
  `bloodgroup` varchar(225) DEFAULT NULL,
  `CPAP` varchar(225) DEFAULT NULL,
  `HFNC` varchar(225) DEFAULT NULL,
  `VO2` varchar(225) DEFAULT NULL,
  `BMIvalue` varchar(225) DEFAULT NULL,
  `Overviewofpatient` varchar(225) DEFAULT NULL,
  `appointment_id` varchar(225) DEFAULT NULL,
  `org_id` int(11) NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `createdatetime` timestamp NULL DEFAULT NULL,
  `modifydatetime` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advise_template`
--
ALTER TABLE `advise_template`
  ADD PRIMARY KEY (`at_id`);

--
-- Indexes for table `appointment_existing`
--
ALTER TABLE `appointment_existing`
  ADD PRIMARY KEY (`atmt_id`);

--
-- Indexes for table `appointment_online`
--
ALTER TABLE `appointment_online`
  ADD PRIMARY KEY (`appoint_id`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `org_id` (`org_id`,`ts`),
  ADD KEY `module` (`module`),
  ADD KEY `action` (`action`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `bill_pages`
--
ALTER TABLE `bill_pages`
  ADD PRIMARY KEY (`pagetype_id`);

--
-- Indexes for table `bill_sizes`
--
ALTER TABLE `bill_sizes`
  ADD PRIMARY KEY (`bill_size_id`);

--
-- Indexes for table `cheifcomplaint_template`
--
ALTER TABLE `cheifcomplaint_template`
  ADD PRIMARY KEY (`cc_id`);

--
-- Indexes for table `concessions`
--
ALTER TABLE `concessions`
  ADD PRIMARY KEY (`concession_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`dept_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doc_id`);

--
-- Indexes for table `doctors_time_slot`
--
ALTER TABLE `doctors_time_slot`
  ADD PRIMARY KEY (`doctors_time_id`);

--
-- Indexes for table `doctors_time_slot2`
--
ALTER TABLE `doctors_time_slot2`
  ADD PRIMARY KEY (`doctor_another_Time_Slot_id`);

--
-- Indexes for table `doctor_patient_duration`
--
ALTER TABLE `doctor_patient_duration`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dosage`
--
ALTER TABLE `dosage`
  ADD PRIMARY KEY (`dosage_id`);

--
-- Indexes for table `dosageandtime`
--
ALTER TABLE `dosageandtime`
  ADD PRIMARY KEY (`doseandtime_id`);

--
-- Indexes for table `dose`
--
ALTER TABLE `dose`
  ADD PRIMARY KEY (`dose_id`);

--
-- Indexes for table `echo_reports`
--
ALTER TABLE `echo_reports`
  ADD PRIMARY KEY (`echo_report_id`);

--
-- Indexes for table `finaldiagnosis_template`
--
ALTER TABLE `finaldiagnosis_template`
  ADD PRIMARY KEY (`fd_id`);

--
-- Indexes for table `frequency`
--
ALTER TABLE `frequency`
  ADD PRIMARY KEY (`freq_id`);

--
-- Indexes for table `gynaec_prescriptions`
--
ALTER TABLE `gynaec_prescriptions`
  ADD PRIMARY KEY (`gynaec_rx_id`);

--
-- Indexes for table `instruction_template`
--
ALTER TABLE `instruction_template`
  ADD PRIMARY KEY (`it_id`);

--
-- Indexes for table `intake_time`
--
ALTER TABLE `intake_time`
  ADD PRIMARY KEY (`intake_time_id`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoice_id`);

--
-- Indexes for table `in_take_period`
--
ALTER TABLE `in_take_period`
  ADD PRIMARY KEY (`intake_id`);

--
-- Indexes for table `madicine_type`
--
ALTER TABLE `madicine_type`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`medicine_id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `channel_created` (`channel`,`created_at`),
  ADD KEY `id_idx` (`id`);

--
-- Indexes for table `multi_doctortimeslots`
--
ALTER TABLE `multi_doctortimeslots`
  ADD PRIMARY KEY (`multi_id`);

--
-- Indexes for table `multi_doctortimeslots2`
--
ALTER TABLE `multi_doctortimeslots2`
  ADD PRIMARY KEY (`multi_time_id`);

--
-- Indexes for table `organization`
--
ALTER TABLE `organization`
  ADD PRIMARY KEY (`org_id`);

--
-- Indexes for table `pagessize`
--
ALTER TABLE `pagessize`
  ADD PRIMARY KEY (`size_id`);

--
-- Indexes for table `pasthistory_template`
--
ALTER TABLE `pasthistory_template`
  ADD PRIMARY KEY (`ph_id`);

--
-- Indexes for table `patient_medicine_billing`
--
ALTER TABLE `patient_medicine_billing`
  ADD PRIMARY KEY (`medicine_billing_id`);

--
-- Indexes for table `patient_medicine_billing_items`
--
ALTER TABLE `patient_medicine_billing_items`
  ADD PRIMARY KEY (`medicine_billing_item_id`);

--
-- Indexes for table `patient_tests_history`
--
ALTER TABLE `patient_tests_history`
  ADD PRIMARY KEY (`patient_history_id`);

--
-- Indexes for table `patient_test_billing`
--
ALTER TABLE `patient_test_billing`
  ADD PRIMARY KEY (`test_billing_id`);

--
-- Indexes for table `payment_method`
--
ALTER TABLE `payment_method`
  ADD PRIMARY KEY (`payment_method_id`);

--
-- Indexes for table `prescripition`
--
ALTER TABLE `prescripition`
  ADD PRIMARY KEY (`prescription_id`);

--
-- Indexes for table `prescription_medicines`
--
ALTER TABLE `prescription_medicines`
  ADD PRIMARY KEY (`pm_id`);

--
-- Indexes for table `receptionnist`
--
ALTER TABLE `receptionnist`
  ADD PRIMARY KEY (`rep_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `role_menus`
--
ALTER TABLE `role_menus`
  ADD PRIMARY KEY (`role_menus_id`);

--
-- Indexes for table `route`
--
ALTER TABLE `route`
  ADD PRIMARY KEY (`route_id`);

--
-- Indexes for table `rx_groups`
--
ALTER TABLE `rx_groups`
  ADD PRIMARY KEY (`rx_id`);

--
-- Indexes for table `rx_groups_names`
--
ALTER TABLE `rx_groups_names`
  ADD PRIMARY KEY (`rx_group_id`);

--
-- Indexes for table `security`
--
ALTER TABLE `security`
  ADD PRIMARY KEY (`security_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `specialtis`
--
ALTER TABLE `specialtis`
  ADD PRIMARY KEY (`specialtis_id`);

--
-- Indexes for table `taxes`
--
ALTER TABLE `taxes`
  ADD PRIMARY KEY (`tax_id`);

--
-- Indexes for table `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`test_id`);

--
-- Indexes for table `test_group`
--
ALTER TABLE `test_group`
  ADD PRIMARY KEY (`test_group_id`);

--
-- Indexes for table `times`
--
ALTER TABLE `times`
  ADD PRIMARY KEY (`time_id`);

--
-- Indexes for table `truncate_logs`
--
ALTER TABLE `truncate_logs`
  ADD PRIMARY KEY (`truncate_id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`unit_id`);

--
-- Indexes for table `vitals`
--
ALTER TABLE `vitals`
  ADD PRIMARY KEY (`vital_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `advise_template`
--
ALTER TABLE `advise_template`
  MODIFY `at_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointment_existing`
--
ALTER TABLE `appointment_existing`
  MODIFY `atmt_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appointment_online`
--
ALTER TABLE `appointment_online`
  MODIFY `appoint_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=674;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT for table `bill_pages`
--
ALTER TABLE `bill_pages`
  MODIFY `pagetype_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bill_sizes`
--
ALTER TABLE `bill_sizes`
  MODIFY `bill_size_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cheifcomplaint_template`
--
ALTER TABLE `cheifcomplaint_template`
  MODIFY `cc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `concessions`
--
ALTER TABLE `concessions`
  MODIFY `concession_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `dept_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `doctors_time_slot`
--
ALTER TABLE `doctors_time_slot`
  MODIFY `doctors_time_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `doctors_time_slot2`
--
ALTER TABLE `doctors_time_slot2`
  MODIFY `doctor_another_Time_Slot_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `doctor_patient_duration`
--
ALTER TABLE `doctor_patient_duration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `dosage`
--
ALTER TABLE `dosage`
  MODIFY `dosage_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `dosageandtime`
--
ALTER TABLE `dosageandtime`
  MODIFY `doseandtime_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `echo_reports`
--
ALTER TABLE `echo_reports`
  MODIFY `echo_report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `finaldiagnosis_template`
--
ALTER TABLE `finaldiagnosis_template`
  MODIFY `fd_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `frequency`
--
ALTER TABLE `frequency`
  MODIFY `freq_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `gynaec_prescriptions`
--
ALTER TABLE `gynaec_prescriptions`
  MODIFY `gynaec_rx_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `instruction_template`
--
ALTER TABLE `instruction_template`
  MODIFY `it_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `in_take_period`
--
ALTER TABLE `in_take_period`
  MODIFY `intake_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `madicine_type`
--
ALTER TABLE `madicine_type`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `medicine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `multi_doctortimeslots`
--
ALTER TABLE `multi_doctortimeslots`
  MODIFY `multi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `multi_doctortimeslots2`
--
ALTER TABLE `multi_doctortimeslots2`
  MODIFY `multi_time_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `organization`
--
ALTER TABLE `organization`
  MODIFY `org_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pagessize`
--
ALTER TABLE `pagessize`
  MODIFY `size_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `pasthistory_template`
--
ALTER TABLE `pasthistory_template`
  MODIFY `ph_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_medicine_billing`
--
ALTER TABLE `patient_medicine_billing`
  MODIFY `medicine_billing_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `patient_medicine_billing_items`
--
ALTER TABLE `patient_medicine_billing_items`
  MODIFY `medicine_billing_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `patient_tests_history`
--
ALTER TABLE `patient_tests_history`
  MODIFY `patient_history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `patient_test_billing`
--
ALTER TABLE `patient_test_billing`
  MODIFY `test_billing_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payment_method`
--
ALTER TABLE `payment_method`
  MODIFY `payment_method_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `prescripition`
--
ALTER TABLE `prescripition`
  MODIFY `prescription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `prescription_medicines`
--
ALTER TABLE `prescription_medicines`
  MODIFY `pm_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receptionnist`
--
ALTER TABLE `receptionnist`
  MODIFY `rep_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `role_menus`
--
ALTER TABLE `role_menus`
  MODIFY `role_menus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5866;

--
-- AUTO_INCREMENT for table `route`
--
ALTER TABLE `route`
  MODIFY `route_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `rx_groups`
--
ALTER TABLE `rx_groups`
  MODIFY `rx_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rx_groups_names`
--
ALTER TABLE `rx_groups_names`
  MODIFY `rx_group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `security`
--
ALTER TABLE `security`
  MODIFY `security_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `specialtis`
--
ALTER TABLE `specialtis`
  MODIFY `specialtis_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `taxes`
--
ALTER TABLE `taxes`
  MODIFY `tax_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `test_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;

--
-- AUTO_INCREMENT for table `test_group`
--
ALTER TABLE `test_group`
  MODIFY `test_group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `times`
--
ALTER TABLE `times`
  MODIFY `time_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `truncate_logs`
--
ALTER TABLE `truncate_logs`
  MODIFY `truncate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `vitals`
--
ALTER TABLE `vitals`
  MODIFY `vital_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
