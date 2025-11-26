-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2025 at 01:35 AM
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
-- Database: `neustmrdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `common_health_issues`
--

CREATE TABLE `common_health_issues` (
  `issue_id` int(11) NOT NULL,
  `issue_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `common_health_issues`
--

INSERT INTO `common_health_issues` (`issue_id`, `issue_name`, `description`, `created_at`) VALUES
(1, 'Fever', 'A temporary increase in body temperature, often due to infection.', '2025-03-21 06:54:26'),
(2, 'Common Cold', 'A viral infection causing runny nose, sneezing, and sore throat.', '2025-03-21 06:54:26'),
(3, 'Cough', 'A reflex action to clear the airways, often caused by infection or allergies.', '2025-03-21 06:54:26'),
(4, 'Flu (Influenza)', 'A contagious respiratory illness caused by influenza viruses.', '2025-03-21 06:54:26'),
(5, 'Sore Throat', 'Pain or irritation in the throat, often caused by viral infections.', '2025-03-21 06:54:26'),
(6, 'Headache', 'A common pain in the head, often due to stress, dehydration, or illness.', '2025-03-21 06:54:26'),
(7, 'Migraine', 'A neurological condition causing severe headaches and nausea.', '2025-03-21 06:54:26'),
(8, 'Asthma', 'A condition that causes difficulty in breathing due to inflamed airways.', '2025-03-21 06:54:26'),
(9, 'Allergic Rhinitis', 'An allergic reaction causing sneezing, congestion, and itchy nose.', '2025-03-21 06:54:26'),
(10, 'Skin Allergies', 'Rashes, redness, or irritation due to allergic reactions.', '2025-03-21 06:54:26'),
(11, 'Chickenpox', 'A contagious viral infection causing itchy red spots and fever.', '2025-03-21 06:54:26'),
(12, 'Measles', 'A viral infection causing rash, fever, and cough, often preventable by vaccine.', '2025-03-21 06:54:26'),
(13, 'Dengue Fever', 'A mosquito-borne illness that causes fever, rash, and muscle pain.', '2025-03-21 06:54:26'),
(14, 'Hypertension', 'High blood pressure that may lead to severe health problems.', '2025-03-21 06:54:26'),
(15, 'Anemia', 'A condition where the body lacks enough healthy red blood cells.', '2025-03-21 06:54:26'),
(18, 'Scoliosis', 'An abnormal curvature of the spine that can cause posture problems.', '2025-03-21 06:54:26'),
(19, 'Vision Problems', 'Common issues such as nearsightedness or farsightedness.', '2025-03-21 06:54:26'),
(20, 'Hearing Impairment', 'Partial or total inability to hear properly.', '2025-03-21 06:54:26'),
(21, 'Dental Cavities', 'Tooth decay that requires treatment to prevent further damage.', '2025-03-21 06:54:26'),
(22, 'Back Pain', 'Pain in the lower or upper back, common among students.', '2025-03-21 06:54:26'),
(23, 'Sports Injuries', 'Physical injuries sustained during physical activities or sports.', '2025-03-21 06:54:26'),
(24, 'Stomach Pain', 'Abdominal discomfort, which could be due to gastritis, indigestion, or infection.', '2025-03-21 06:54:26'),
(25, 'Diarrhea', 'Frequent, loose, or watery bowel movements, often caused by infection or food.', '2025-03-21 06:54:26'),
(26, 'Constipation', 'Difficulty in passing stool, often due to diet or dehydration.', '2025-03-21 06:54:26'),
(27, 'Dehydration', 'A lack of enough fluids in the body, leading to dizziness and fatigue.', '2025-03-21 06:54:26');

-- --------------------------------------------------------

--
-- Table structure for table `dental_logs`
--

CREATE TABLE `dental_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dental_records`
--

CREATE TABLE `dental_records` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `tooth_number` varchar(100) DEFAULT NULL,
  `record_details` text DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `date_deleted` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dental_records`
--

INSERT INTO `dental_records` (`id`, `student_id`, `tooth_number`, `record_details`, `date_created`, `date_deleted`) VALUES
(178, '201911015999', '11', 'Extracted', '2025-04-04 10:44:56', NULL),
(179, '201911015999', '21', 'Filling', '2025-04-04 10:50:02', NULL),
(180, '201911015999', '22', 'Healthy', '2025-04-04 10:50:12', NULL),
(181, '201911015999', '22', 'Filling', '2025-04-04 10:50:16', NULL),
(182, '201911015999', '15', 'Filling', '2025-04-04 10:53:15', NULL),
(183, '201911015999', '15', 'Extracted', '2025-04-04 10:53:32', NULL),
(184, '201911015999', '16', 'Extracted', '2025-04-04 10:54:38', NULL),
(185, '201911015999', '23', 'Filling', '2025-04-04 10:57:24', NULL),
(186, '2019555556', '14', 'Cavity', '2025-04-04 11:03:29', NULL),
(187, '201911015999', '17', 'Cavity', '2025-04-04 11:09:16', NULL),
(188, '201911015999', '18', 'Cavity', '2025-04-04 11:09:33', NULL),
(189, '201911015999', '17', 'Cavity', '2025-04-04 11:10:43', NULL),
(190, '201911015999', '17', 'Filling', '2025-04-04 11:11:03', NULL),
(191, '201911015999', '17', 'Extracted', '2025-04-04 11:11:19', NULL),
(192, '201911015999', '17', 'Healthy', '2025-04-04 12:43:01', NULL),
(193, '201911015999', '13', 'Extracted', '2025-04-04 12:52:50', NULL),
(194, '201911015999', '13', 'Healthy', '2025-04-04 12:52:53', NULL),
(195, '201911015999', '13', 'Filling', '2025-04-04 12:57:24', NULL),
(196, '201911015999', '17', 'Cavity', '2025-04-04 13:12:36', NULL),
(197, '201911015999', '17', 'Filling', '2025-04-04 13:12:47', NULL),
(198, '201911015999', '17', 'Filling', '2025-04-04 13:12:55', NULL),
(199, '201911015999', '18', 'Cavity', '2025-04-04 13:12:58', NULL),
(200, '201911015999', '46', 'Extracted', '2025-04-04 13:34:47', NULL),
(201, '201911015999', '45', 'Filling', '2025-04-04 13:38:19', NULL),
(202, '201911015999', '43', 'Filling', '2025-04-04 13:38:38', NULL),
(203, '201911015999', '42', 'Extracted', '2025-04-04 14:14:50', NULL),
(204, '201911015999', '41', 'Cavity', '2025-04-04 14:14:58', NULL),
(205, '201911015999', '14', 'Cavity', '2025-05-02 09:31:22', NULL),
(206, '201911015999', '33', 'Extracted', '2025-05-02 09:31:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `medical_certificates`
--

CREATE TABLE `medical_certificates` (
  `id` int(11) NOT NULL,
  `id_number` varchar(20) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `exam_date` date NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medical_location`
--

CREATE TABLE `medical_location` (
  `campus_id` int(11) NOT NULL,
  `campus_name` varchar(100) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_location`
--

INSERT INTO `medical_location` (`campus_id`, `campus_name`, `course_name`, `created_at`, `deleted_at`) VALUES
(1, 'San Isidro Campus', 'CMBT', '2025-02-05 00:46:15', NULL),
(2, 'Main Campus', 'BSIT', '2025-01-30 08:11:04', NULL),
(3, 'Main Campus', 'College of Architecture', '2025-01-30 08:11:04', NULL),
(4, 'Main Campus', 'College of Engineering', '2025-01-30 08:11:04', NULL),
(5, 'Fort Magsaysay Campus', 'Certificate in Professional Teacher Education', '2025-02-05 07:45:51', NULL),
(6, 'Main Campus', 'Bachelor of Science in Architecture', '2025-02-05 07:54:24', NULL),
(7, 'General Tinio Street Campus', 'Bachelor of Science in Electrical Engineering', '2025-02-12 00:24:05', NULL),
(8, 'Sto. Domingo Campus', 'Bachelor of Science in Hotel and Restaurant Management', '2025-02-12 00:41:48', NULL),
(9, 'Fort Magsaysay Campus', 'Bachelor of Science in Entrepreneurship', '2025-02-12 00:53:20', NULL),
(10, 'Main Campus', 'Bachelor of Physical Education', '2025-02-14 01:40:54', NULL),
(11, 'General Tinio Street Campus', 'Bachelor of Science in Civil Engineering', '2025-02-18 02:38:52', NULL),
(12, 'Main Campus', 'Bachelor of Secondary Education', '2025-02-18 02:41:15', NULL),
(13, 'Fort Magsaysay Campus', 'Bachelor of Public Administration', '2025-02-20 03:17:46', NULL),
(14, 'Main Campus', 'Bachelor of Science in Information Technology', '2025-04-04 07:31:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `medical_record`
--

CREATE TABLE `medical_record` (
  `medical_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `school_year` char(9) NOT NULL,
  `allergy` varchar(100) DEFAULT NULL,
  `asthma` tinyint(1) NOT NULL DEFAULT 0,
  `diabetes` varchar(100) NOT NULL DEFAULT '0',
  `heart_disease` varchar(100) DEFAULT NULL,
  `seizure_disorder` tinyint(1) NOT NULL DEFAULT 0,
  `other_HC` varchar(255) DEFAULT NULL,
  `medication` varchar(255) DEFAULT NULL,
  `record_date` date NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `id_number` varchar(50) DEFAULT NULL,
  `campus_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_record`
--

INSERT INTO `medical_record` (`medical_id`, `student_id`, `course_id`, `school_year`, `allergy`, `asthma`, `diabetes`, `heart_disease`, `seizure_disorder`, `other_HC`, `medication`, `record_date`, `date_created`, `deleted_at`, `id_number`, `campus_id`) VALUES
(61, 140, 6, '2024-2025', 'none', 0, 'No', 'No', 0, 'none', 'none', '2025-01-29', '2025-02-17 00:10:33', NULL, NULL, NULL),
(62, 141, 6, '2024-2025', 'none', 0, 'No', 'No', 0, 'none', 'none', '2025-01-27', '2025-02-17 00:11:03', NULL, NULL, NULL),
(63, 143, 6, '2024-2025', 'none', 0, 'No', 'No', 0, 'none', 'none', '2025-01-28', '2025-02-17 00:17:36', '2025-03-07 06:49:23', NULL, NULL),
(64, 144, 11, '2024-2025', 'none', 0, 'None', 'None', 0, 'none', 'none', '2025-01-01', '2025-02-18 02:38:52', '2025-03-07 07:09:17', NULL, NULL),
(65, 145, 12, '2024-2025', 'none', 0, 'None', 'None', 0, 'none', 'none', '2025-02-04', '2025-02-18 02:41:15', '0000-00-00 00:00:00', NULL, NULL),
(66, 146, 6, '2024-2025', 'none', 0, 'None', 'None', 0, 'none', 'none', '2025-01-27', '2025-02-18 02:54:47', NULL, NULL, NULL),
(67, 147, 13, '2024-2025', 'none', 0, 'Type 1 ', 'Irregular HB', 0, 'none', 'none', '2025-02-05', '2025-02-20 03:17:46', NULL, NULL, NULL),
(68, 148, 6, '2024-2025', 'none', 0, 'None', 'None', 0, 'none', 'none', '2025-03-10', '2025-03-12 02:07:13', NULL, NULL, NULL),
(69, 149, 14, '2024-2025', 'fish', 1, 'dsasdasd', 'sdasdas', 1, 'pwd', 'dasasdsda', '0000-00-00', '2025-04-04 07:31:58', '2025-04-08 06:03:43', NULL, NULL),
(70, 151, 6, '2024-2025', 'fish', 0, 'None', 'None', 0, 'pwd', 'dasasdsda', '2025-04-04', '2025-04-04 07:33:54', '2025-04-08 08:16:26', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `medical_treatment_records`
--

CREATE TABLE `medical_treatment_records` (
  `MTR_id` int(100) NOT NULL,
  `date` date NOT NULL,
  `chief_complaint` varchar(100) NOT NULL,
  `treatment` varchar(100) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_deleted` date DEFAULT NULL,
  `student_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_treatment_records`
--

INSERT INTO `medical_treatment_records` (`MTR_id`, `date`, `chief_complaint`, `treatment`, `date_created`, `date_deleted`, `student_id`) VALUES
(26, '2025-04-07', 'Dental Cavities', 'dasd', '2025-04-07 02:56:29', NULL, '201911015999'),
(27, '2025-05-02', 'Vision Problems', 'sdas', '2025-04-07 03:01:34', NULL, '201911015999'),
(28, '2025-04-07', 'Dental Cavities', 'dasda', '2025-04-07 03:21:23', NULL, '201911015999'),
(29, '2025-04-07', 'Dengue Fever', 'sdasd', '2025-04-07 03:22:28', NULL, '201911015999'),
(30, '2025-04-07', 'Dental Cavities', 'sdasd', '2025-04-07 03:45:49', NULL, '201911015999'),
(31, '2025-04-07', 'Dental Cavities', 'das', '2025-04-07 03:47:35', NULL, '201911015999'),
(32, '2025-04-07', 'Dental Cavities', 'dasd', '2025-04-07 03:49:28', NULL, '201911015999'),
(33, '2025-04-07', 'Dental Cavities', 'dasd', '2025-04-07 03:52:32', NULL, '201911015999'),
(34, '2025-05-08', 'Hearing Impairment', 'fasfas', '2025-04-08 05:12:52', NULL, '201911015999'),
(35, '2025-04-08', 'Vision Problems', 'dasda', '2025-04-08 05:13:04', NULL, '201911015999'),
(36, '2025-04-08', 'Scoliosis', 'dasda', '2025-04-08 05:18:34', NULL, '201911015999'),
(37, '2025-04-08', 'Hearing Impairment', 'dasda', '2025-04-08 05:20:38', NULL, '201911015999'),
(38, '2025-04-08', 'Sore Throat', 'sada', '2025-04-08 05:24:23', NULL, '201911015999'),
(39, '2025-04-08', 'Vision Problems', 'dasdasd', '2025-04-08 05:29:29', NULL, '201911015999'),
(40, '2025-04-08', 'Dental Cavities', 'dasd', '2025-04-08 05:32:10', NULL, '201911015999'),
(41, '2025-04-08', 'Vision Problems', 'sdasdas', '2025-04-08 05:32:27', NULL, '2019555556'),
(42, '2025-04-24', 'tooth ache', 'pill', '2025-04-24 06:13:27', NULL, '201911015999');

-- --------------------------------------------------------

--
-- Table structure for table `physical_examination`
--

CREATE TABLE `physical_examination` (
  `exam_id` int(11) NOT NULL,
  `medical_id` int(11) NOT NULL,
  `exam_date` date NOT NULL,
  `height_cm` decimal(5,2) NOT NULL,
  `weight_kg` decimal(5,2) NOT NULL,
  `blood_pressure` varchar(7) NOT NULL,
  `blood_type` char(3) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `smoking` tinyint(1) NOT NULL,
  `liquor_drinking` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `physical_examination`
--

INSERT INTO `physical_examination` (`exam_id`, `medical_id`, `exam_date`, `height_cm`, `weight_kg`, `blood_pressure`, `blood_type`, `date_created`, `deleted_at`, `smoking`, `liquor_drinking`) VALUES
(49, 61, '2025-01-29', 5.00, 70.00, '120/80', 'A+', '2025-02-17 00:10:33', NULL, 0, 0),
(50, 62, '2025-01-27', 5.00, 70.00, '120/80', 'A+', '2025-02-17 00:11:03', NULL, 0, 0),
(51, 63, '2025-01-28', 5.00, 70.00, '120/80', 'A+', '2025-02-17 00:17:36', NULL, 0, 0),
(52, 64, '2025-01-01', 5.00, 70.00, '120/80', 'A+', '2025-02-18 02:38:52', NULL, 1, 1),
(53, 65, '2025-02-04', 5.00, 70.00, '120/80', 'A+', '2025-02-18 02:41:15', NULL, 0, 0),
(54, 66, '2025-01-27', 5.00, 70.00, '120/80', 'A+', '2025-02-18 02:54:47', NULL, 0, 0),
(55, 67, '2025-02-05', 5.00, 70.00, '120/80', 'B-', '2025-02-20 03:17:46', NULL, 1, 1),
(56, 68, '2025-03-10', 5.00, 70.00, '120/80', 'A+', '2025-03-12 02:07:13', NULL, 0, 0),
(57, 67, '2025-03-14', 123.00, 55.00, '120/80', '', '2025-03-21 04:42:16', NULL, 0, 0),
(58, 67, '2025-03-21', 123.00, 55.00, '120/80', '', '2025-03-21 04:42:35', NULL, 0, 0),
(59, 67, '2025-03-21', 123.00, 55.00, '120/80', '', '2025-03-21 04:42:39', NULL, 0, 0),
(60, 67, '2025-03-11', 123.00, 55.00, '120/80', '', '2025-03-21 04:43:13', NULL, 1, 1),
(61, 67, '2025-03-20', 123.00, 55.00, '120/80', '', '2025-03-21 05:10:51', NULL, 0, 0),
(62, 67, '2025-04-11', 120.00, 55.00, '120/80', '', '2025-04-03 01:26:37', NULL, 0, 0),
(63, 67, '2025-04-04', 120.00, 55.00, '120/80', '', '2025-04-04 00:27:57', NULL, 0, 0),
(64, 67, '2025-04-04', 120.00, 60.00, '120/80', '', '2025-04-04 00:28:21', NULL, 1, 1),
(65, 67, '2025-04-04', 120.00, 60.00, '120/80', '', '2025-04-04 00:28:24', NULL, 1, 1),
(66, 69, '0000-00-00', 120.00, 60.00, '120/80', 'AB+', '2025-04-04 07:31:58', NULL, 0, 0),
(67, 70, '2025-04-04', 120.00, 60.00, '120/80', 'A+', '2025-04-04 07:33:54', NULL, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `student_health_issues`
--

CREATE TABLE `student_health_issues` (
  `record_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `issue_id` int(11) DEFAULT NULL,
  `diagnosis_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('admin','staff','super_admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `role`, `created_at`, `deleted_at`) VALUES
(9, 'mrjixel@gmail.com', '$2y$10$d84c6Zg9HCDsMHGF/CNDtOMn/3ATuLyQdRVDpy0W7DIwxiZFD5kau', 'super_admin', '2025-02-03 21:09:06', NULL),
(80, 'patrickherminigildo03@gmail.com', '$2y$10$zrmv9d4ARuA9TUyqst24FOSnI9C7HgwkTxY1FV1IU4K0MRXvQuiom', 'admin', '2025-03-27 06:39:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vaccine`
--

CREATE TABLE `vaccine` (
  `vaccine_id` int(11) NOT NULL,
  `medical_id` int(11) NOT NULL,
  `dose_number` tinyint(1) NOT NULL,
  `vaccination_date` date NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vaccine`
--

INSERT INTO `vaccine` (`vaccine_id`, `medical_id`, `dose_number`, `vaccination_date`, `date_created`, `deleted_at`) VALUES
(53, 61, 2, '2025-02-14', '2025-02-17 00:10:33', NULL),
(54, 61, 2, '2025-02-14', '2025-02-17 00:10:33', NULL),
(55, 62, 2, '2025-02-05', '2025-02-17 00:11:03', NULL),
(56, 62, 2, '2025-02-05', '2025-02-17 00:11:03', NULL),
(57, 63, 1, '2025-02-05', '2025-02-17 00:17:36', NULL),
(58, 63, 1, '2025-02-05', '2025-02-17 00:17:36', NULL),
(59, 64, 3, '2025-02-05', '2025-02-18 02:38:52', NULL),
(60, 64, 3, '2025-02-05', '2025-02-18 02:38:52', NULL),
(61, 65, 0, '2025-02-12', '2025-02-18 02:41:15', NULL),
(62, 65, 0, '2025-02-12', '2025-02-18 02:41:15', NULL),
(63, 66, 1, '2025-02-07', '2025-02-18 02:54:47', NULL),
(64, 66, 1, '2025-02-07', '2025-02-18 02:54:47', NULL),
(65, 67, 0, '2025-02-19', '2025-02-20 03:17:46', NULL),
(66, 67, 0, '2025-02-19', '2025-02-20 03:17:46', NULL),
(67, 68, 2, '2025-03-05', '2025-03-12 02:07:13', NULL),
(68, 68, 2, '2025-03-05', '2025-03-12 02:07:13', NULL),
(69, 69, 0, '2025-05-02', '2025-04-04 07:31:58', NULL),
(70, 70, 3, '2025-04-04', '2025-04-04 07:33:54', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `common_health_issues`
--
ALTER TABLE `common_health_issues`
  ADD PRIMARY KEY (`issue_id`);

--
-- Indexes for table `dental_logs`
--
ALTER TABLE `dental_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `dental_records`
--
ALTER TABLE `dental_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `medical_certificates`
--
ALTER TABLE `medical_certificates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medical_location`
--
ALTER TABLE `medical_location`
  ADD PRIMARY KEY (`campus_id`);

--
-- Indexes for table `medical_record`
--
ALTER TABLE `medical_record`
  ADD PRIMARY KEY (`medical_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `fk_student_id` (`student_id`),
  ADD KEY `fk_id_number` (`id_number`),
  ADD KEY `idx_campus_id` (`campus_id`);

--
-- Indexes for table `medical_treatment_records`
--
ALTER TABLE `medical_treatment_records`
  ADD PRIMARY KEY (`MTR_id`);

--
-- Indexes for table `physical_examination`
--
ALTER TABLE `physical_examination`
  ADD PRIMARY KEY (`exam_id`),
  ADD KEY `medical_id` (`medical_id`);

--
-- Indexes for table `student_health_issues`
--
ALTER TABLE `student_health_issues`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `issue_id` (`issue_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `vaccine`
--
ALTER TABLE `vaccine`
  ADD PRIMARY KEY (`vaccine_id`),
  ADD KEY `medical_id` (`medical_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `common_health_issues`
--
ALTER TABLE `common_health_issues`
  MODIFY `issue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `dental_logs`
--
ALTER TABLE `dental_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dental_records`
--
ALTER TABLE `dental_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=207;

--
-- AUTO_INCREMENT for table `medical_certificates`
--
ALTER TABLE `medical_certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medical_location`
--
ALTER TABLE `medical_location`
  MODIFY `campus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `medical_record`
--
ALTER TABLE `medical_record`
  MODIFY `medical_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `medical_treatment_records`
--
ALTER TABLE `medical_treatment_records`
  MODIFY `MTR_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `physical_examination`
--
ALTER TABLE `physical_examination`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `student_health_issues`
--
ALTER TABLE `student_health_issues`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `vaccine`
--
ALTER TABLE `vaccine`
  MODIFY `vaccine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dental_logs`
--
ALTER TABLE `dental_logs`
  ADD CONSTRAINT `dental_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `dental_records`
--
ALTER TABLE `dental_records`
  ADD CONSTRAINT `dental_records_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `neust_student_details`.`students` (`id_number`);

--
-- Constraints for table `medical_record`
--
ALTER TABLE `medical_record`
  ADD CONSTRAINT `fk_campus_id` FOREIGN KEY (`campus_id`) REFERENCES `medical_location` (`campus_id`),
  ADD CONSTRAINT `fk_id_number` FOREIGN KEY (`id_number`) REFERENCES `neust_student_details`.`students` (`id_number`),
  ADD CONSTRAINT `fk_student_id` FOREIGN KEY (`student_id`) REFERENCES `neust_student_details`.`students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medical_record_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `medical_location` (`campus_id`) ON DELETE CASCADE;

--
-- Constraints for table `physical_examination`
--
ALTER TABLE `physical_examination`
  ADD CONSTRAINT `physical_examination_ibfk_1` FOREIGN KEY (`medical_id`) REFERENCES `medical_record` (`medical_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_health_issues`
--
ALTER TABLE `student_health_issues`
  ADD CONSTRAINT `student_health_issues_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `neust_student_details`.`students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_health_issues_ibfk_2` FOREIGN KEY (`issue_id`) REFERENCES `common_health_issues` (`issue_id`) ON DELETE CASCADE;

--
-- Constraints for table `vaccine`
--
ALTER TABLE `vaccine`
  ADD CONSTRAINT `vaccine_ibfk_1` FOREIGN KEY (`medical_id`) REFERENCES `medical_record` (`medical_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
