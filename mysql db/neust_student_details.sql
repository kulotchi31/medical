-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 09, 2025 at 10:44 AM
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
-- Database: `neust_student_details`
--

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `id_number` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `campus` varchar(100) NOT NULL,
  `guardian_name` varchar(255) NOT NULL,
  `province` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `barangay` varchar(100) NOT NULL,
  `emergency_contact` varchar(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `student_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `id_number`, `first_name`, `middle_name`, `last_name`, `campus`, `guardian_name`, `province`, `city`, `barangay`, `emergency_contact`, `created_at`, `updated_at`, `student_photo`) VALUES
(140, '201944122222', 'john', 'Ssdasd', 'doe', 'Main Campus', 'smith ', 'nueva ecija', 'jaen', 'malabon', '09454991544', '2025-02-17 00:10:33', '2025-02-17 00:10:33', 'uploads/1358039.jpeg'),
(141, '20194412333', 'johns', 'Ssdasd', 'doe', 'Main Campus', 'smith ', 'nueva ecija', 'jaen', 'malabon', '09454991544', '2025-02-17 00:11:03', '2025-04-04 00:31:40', 'uploads/443563.jpg'),
(143, '2019441241145', 'john', 'Ssdasd', 'doe', 'Main Campus', 'smith ', 'nueva ecija', 'jaen', 'malabon', '09454991544', '2025-02-17 00:17:36', '2025-02-17 00:17:36', 'uploads/1358039.jpeg'),
(144, '201944125555', 'Mike', 'Ssdasd', 'doe', 'General Tinio Street Campus', 'smith ', 'nueva ecija', 'jaen', 'malabon', '09454991544', '2025-02-18 02:38:52', '2025-02-18 02:38:52', 'uploads/IMG_8495[1].JPG'),
(145, '2019555556', 'Mikesss', 'Ssdasd', 'doe', 'Main Campus', 'smith ', 'nueva ecija', 'jaen', 'malabon', '09454991544', '2025-02-18 02:41:15', '2025-04-08 08:16:44', '1358039.jpeg'),
(146, '2021064251', 'Anne', 'Cruz', 'Reyes', 'Main Campus', 'smith ', 'nueva ecija', 'jaen', 'malabon', '09454991544', '2025-02-18 02:54:47', '2025-04-04 00:25:40', 'uploads/sample.jpg'),
(147, '201911015999', 'john', 'smith', 'doe', 'Fort Magsaysay Campus', 'smith ', 'nueva ecija', 'jaen', 'jaen', '09454991544', '2025-02-20 03:17:46', '2025-04-04 07:59:13', 'uploads/443563.jpg'),
(148, '2019110159121', 'popo', 'smith', 'doe', 'Main Campus', 'smith ', 'nueva ecija', 'jaen', 'jaen', '09454991544', '2025-03-12 02:07:13', '2025-04-04 07:59:27', 'uploads1359276.jpeg'),
(149, '101010', 'PATRICK', 'M.', 'HERMINIGILDO', 'Main Campus', 'smith ', 'Nueva Ecija', 'Cabanatuan City', 'jaen', '09454991544', '2025-04-04 07:31:58', '2025-04-04 07:59:17', 'uploads⚝• Luffy G5 𖥣𖡡 ᴬᴵ.jpg'),
(151, '101011', 'PATRICK', 'M', 'HERMINIGILDO', 'Main Campus', 'smith ', 'Nueva Ecija', 'Cabanatuan City', 'jaen', '09454991544', '2025-04-04 07:33:54', '2025-04-08 07:15:39', 'uploads/⚝• Luffy G5 𖥣𖡡 ᴬᴵ.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `id_number` (`id_number`),
  ADD UNIQUE KEY `id_number_2` (`id_number`),
  ADD KEY `idx_id_number` (`id_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
