-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2024 at 11:56 PM
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
-- Database: `emurray46`
--

-- --------------------------------------------------------

--
-- Table structure for table `sleep_tracker`
--

CREATE TABLE `sleep_tracker` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_of_sleep` date NOT NULL,
  `sleep_time` time NOT NULL,
  `wake_time` time NOT NULL,
  `sleep_duration` time NOT NULL,
  `sleep_quality` enum('Very Bad','Bad','Fair','Good','Excellent') NOT NULL,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sleep_tracker`
--

INSERT INTO `sleep_tracker` (`id`, `user_id`, `date_of_sleep`, `sleep_time`, `wake_time`, `sleep_duration`, `sleep_quality`, `comments`) VALUES
(57, 25, '2024-03-10', '23:00:00', '08:00:00', '09:00:00', 'Excellent', 'I had a really great sleep'),
(59, 25, '2024-03-12', '23:00:00', '07:00:00', '08:00:00', 'Fair', 'Slept Ok, but had to get up early'),
(63, 25, '2024-03-13', '23:35:00', '10:20:00', '10:45:00', 'Fair', 'Not too shabby'),
(71, 25, '2024-03-15', '23:00:00', '09:00:00', '10:00:00', 'Excellent', 'Amazing sleep'),
(73, 25, '2024-03-14', '23:00:00', '09:00:00', '10:00:00', 'Excellent', 'great'),
(74, 25, '2024-03-19', '23:30:00', '08:30:00', '09:00:00', 'Good', 'Slept ok, not to bad'),
(76, 25, '2024-03-20', '23:00:00', '07:00:00', '08:00:00', 'Good', 'Slept ok'),
(78, 25, '2024-03-21', '23:00:00', '08:00:00', '09:00:00', 'Good', 'Slept ok dreamt of sheep'),
(79, 25, '2024-03-22', '23:00:00', '08:30:00', '09:30:00', 'Bad', 'Kept waking up because the dog kept barking'),
(80, 25, '2024-03-23', '22:00:00', '09:00:00', '11:00:00', 'Excellent', 'Slept great, very deep sleep'),
(81, 25, '2024-03-24', '23:00:00', '07:00:00', '08:00:00', 'Good', 'Slept ok had to get up early however'),
(82, 39, '2024-03-31', '23:00:00', '08:00:00', '09:00:00', 'Fair', 'Slept great'),
(83, 39, '2024-03-30', '23:30:00', '08:10:00', '07:40:00', 'Bad', 'Kept waking up all night, felt bad'),
(84, 40, '2024-03-21', '21:00:00', '06:15:00', '07:15:00', 'Bad', 'Felt anxious all night, tossing and turning.'),
(85, 40, '2024-03-22', '22:00:00', '06:15:00', '06:15:00', 'Fair', 'Got home late, but felt really tired, got up early for work'),
(86, 40, '2024-03-23', '23:30:00', '09:00:00', '05:30:00', 'Good', 'Had a lie in as it was the weekend, felt relaxed'),
(87, 40, '2024-03-24', '22:45:00', '09:00:00', '06:15:00', 'Excellent', 'Slept great had another lie in'),
(88, 40, '2024-03-25', '01:30:00', '06:15:00', '04:45:00', 'Bad', 'Couldnt get to sleep, had to get up early for work'),
(89, 40, '2024-03-26', '00:30:00', '06:00:00', '06:30:00', 'Very Bad', 'Slept better'),
(90, 41, '2024-03-25', '20:30:00', '06:30:00', '10:00:00', 'Excellent', 'Woke up early feeling refreshed, went for a morning run'),
(91, 41, '2024-03-26', '20:30:00', '06:30:00', '10:00:00', 'Good', 'feeling good sticking to my routine, went for another morning run '),
(92, 41, '2024-03-27', '20:35:00', '06:30:00', '09:55:00', 'Excellent', 'Slept great again, Another morning run'),
(93, 41, '2024-03-28', '20:00:00', '06:45:00', '10:45:00', 'Excellent', 'Felt great this morning, I can see improvements to my physical performance.'),
(94, 41, '2024-03-29', '20:45:00', '06:00:00', '09:15:00', 'Fair', 'Sleep wasn&#039;t as good as it could&#039;ve been'),
(103, 25, '2024-04-11', '21:00:00', '06:00:00', '09:00:00', 'Fair', 'Slept Like a baby'),
(104, 25, '2024-04-12', '23:00:00', '08:00:00', '09:00:00', 'Good', 'Slept great'),
(105, 25, '2024-04-13', '23:45:00', '08:00:00', '08:15:00', 'Bad', 'kept waking up '),
(108, 25, '2024-04-14', '23:00:00', '08:00:00', '09:00:00', 'Good', 'Read a book before going to sleep'),
(109, 25, '2024-04-15', '23:30:00', '05:00:00', '05:30:00', 'Bad', 'Couldnt sleep all night');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`) VALUES
(25, 'emmett', 'emmett@test.com', '12345'),
(38, 'emmett72', 'emmett@emmett.com', 'emmett'),
(39, 'TestUser1', 'test@email.com', 'Test123'),
(40, 'Jane', 'Jane@HealthCare.com', '12345'),
(41, 'Owen', 'Owen@Fitness.com', '12345'),
(43, 'bob', 'bob@gmail.com', 'bob123'),
(45, 'test12345', 'test@test.com', '12345');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sleep_tracker`
--
ALTER TABLE `sleep_tracker`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sleep_tracker`
--
ALTER TABLE `sleep_tracker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sleep_tracker`
--
ALTER TABLE `sleep_tracker`
  ADD CONSTRAINT `sleep_tracker_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
