-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 28, 2025 at 04:57 PM
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
-- Database: `study_resources_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `Comment_ID` int(11) NOT NULL,
  `Comment_Text` text DEFAULT NULL,
  `Comment_Date` datetime DEFAULT current_timestamp(),
  `Commenter_ID` int(11) DEFAULT NULL,
  `ResourceID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`Comment_ID`, `Comment_Text`, `Comment_Date`, `Commenter_ID`, `ResourceID`) VALUES
(2, 'good', '2025-12-20 21:48:21', 1, 4),
(4, 'gd\r\n', '2025-12-20 22:01:10', 1, 5),
(5, 'gg', '2025-12-20 22:13:38', 1, 7);

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `Course_Code` varchar(10) NOT NULL,
  `Course_Name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`Course_Code`, `Course_Name`) VALUES
('CSE110', 'Programming Language 1'),
('CSE111', 'Programmimg Language 2'),
('CSE220', 'Data Structure'),
('CSE221', 'Algorithm Analysis & Design'),
('CSE230', 'Discrete Mathematics'),
('CSE250', 'Circuits and Electronics'),
('CSE251', 'Electronic Devices and Circuits'),
('CSE260', 'Digital Logic Design'),
('CSE320', 'Data Communications'),
('CSE321', 'Operating Systems'),
('CSE330', 'Numerical Methods'),
('CSE331', 'Automata and Computability'),
('CSE340', 'Computer Architecture'),
('CSE341', 'MICROPROCESSORS'),
('CSE350', 'Digital Electronics and Pulse Techniques'),
('CSE360', 'Computer Interfacing'),
('CSE370', 'Database'),
('CSE400', 'Final Year Design Project'),
('CSE420', 'Compiler Design'),
('CSE421', 'Computer Networks'),
('CSE422', 'Artificial Intelligence'),
('CSE423', 'Computer Graphics'),
('CSE460', 'VLSI Design'),
('CSE461', 'Introduction to Robotics'),
('CSE470', 'Software Engineering'),
('CSE471', 'System Analysis and Design');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `Initial` varchar(25) NOT NULL,
  `Full_Name` varchar(100) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculty_course_evaluation`
--

CREATE TABLE `faculty_course_evaluation` (
  `Review_ID` int(11) NOT NULL,
  `Course_Code` varchar(10) NOT NULL,
  `Faculty_Initial` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pending_reviews`
--

CREATE TABLE `pending_reviews` (
  `PendingID` int(11) NOT NULL,
  `review_type` enum('faculty','course') NOT NULL,
  `target_name` varchar(255) NOT NULL,
  `course_code` varchar(50) DEFAULT NULL,
  `rating_score` int(11) NOT NULL,
  `review_text` text NOT NULL,
  `submitted_by` int(11) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rating`
--

CREATE TABLE `rating` (
  `RatingID` int(11) NOT NULL,
  `Score` int(11) DEFAULT NULL CHECK (`Score` between 1 and 5),
  `Rating_Date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rating`
--

INSERT INTO `rating` (`RatingID`, `Score`, `Rating_Date`) VALUES
(1, 1, '2025-12-20 21:28:42'),
(2, 2, '2025-12-20 21:28:42'),
(3, 3, '2025-12-20 21:28:42'),
(4, 4, '2025-12-20 21:28:42'),
(5, 5, '2025-12-20 21:28:42'),
(6, 4, '2025-12-22 00:23:30'),
(7, 2, '2025-12-22 02:29:38');

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `ResourceID` int(11) NOT NULL,
  `Title` varchar(255) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Topic` varchar(255) DEFAULT NULL,
  `File_Path` varchar(255) DEFAULT NULL,
  `Upload_Date` datetime DEFAULT current_timestamp(),
  `Average_Rating` float DEFAULT NULL,
  `Uploader_ID` int(11) DEFAULT NULL,
  `Course_Code` varchar(20) DEFAULT NULL,
  `Resource_Type` varchar(20) NOT NULL DEFAULT 'video'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`ResourceID`, `Title`, `Description`, `Topic`, `File_Path`, `Upload_Date`, `Average_Rating`, `Uploader_ID`, `Course_Code`, `Resource_Type`) VALUES
(2, 'CSE110', 'Introduction to Programming Language (Java)', 'Java for Beginners', 'https://youtu.be/hBg3njn56Z0?si=zIqIC5Ni3erdWaGc', '2025-12-14 18:35:33', 5, NULL, 'CSE110', 'video'),
(3, 'CSE110', 'Introduction to Programming Language (Python)', 'Python for Beginners', 'https://youtu.be/cb5coX1jAYE?si=fOMzSeXapmN7Z0SB', '2025-12-14 21:16:13', 5, NULL, 'CSE110', 'video'),
(4, 'CSE220', 'Class Recording of MTY from Fall 21', 'Linear Array', 'https://youtu.be/aQyNAl0ip2c?si=ekU7DjtaznGASepz', '2025-12-15 00:07:03', 3.7, NULL, 'CSE220', 'video'),
(5, 'CSE221', 'Single Source Shortest Path-Greedy Method', 'Dijkstra Algorithm', 'https://youtu.be/8gYBHjtjWBI?si=YhYZmxKoifYOD0nr', '2025-12-15 00:07:03', 2, NULL, 'CSE221', 'video'),
(6, 'CSE250', 'Summer 23 recorded lecture by PDS', 'Basics of Electricity', 'https://youtu.be/QWSFu_zZ4kg?si=TUI7OgnsPfyoCjam', '2025-12-15 00:07:03', 5, NULL, 'CSE250', 'video'),
(7, 'CSE260', 'Part 1 of Lecture Recording', 'Combinational Circuits', 'https://youtu.be/Ax5zgo6Mcmc?si=7RRgQ0WlmrUvPV9K', '2025-12-15 00:33:50', 5, NULL, 'CSE260', 'video'),
(9, 'CSE320', 'Midterm notes for CSE320', 'Notes', 'uploads/note/1766344187_CSE320 MID NOTES by Minhazul Islam Royel.pdf', '2025-12-22 01:09:47', NULL, NULL, 'CSE320', 'note'),
(10, 'CSE110', 'Practice Problems', 'All topics of CSE110', 'uploads/note/1766345291_Practice Problems for Python.docx', '2025-12-22 01:28:11', NULL, NULL, 'CSE110', 'note'),
(11, 'CSE110', 'List practice problems by MSI', 'Python List', 'uploads/video/1766351621_Class Number 16 Python List Practice Problems.mp4', '2025-12-22 03:13:41', NULL, NULL, 'CSE110', 'video');

-- --------------------------------------------------------

--
-- Table structure for table `resource_download`
--

CREATE TABLE `resource_download` (
  `UserID` int(11) NOT NULL,
  `ResourceID` int(11) NOT NULL,
  `Download_Date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resource_download`
--

INSERT INTO `resource_download` (`UserID`, `ResourceID`, `Download_Date`) VALUES
(1, 9, '2025-12-22 01:54:20'),
(1, 10, '2025-12-22 01:54:50'),
(1, 11, '2025-12-22 03:13:56'),
(2, 10, '2025-12-28 19:14:28');

-- --------------------------------------------------------

--
-- Table structure for table `resource_rating`
--

CREATE TABLE `resource_rating` (
  `ResourceID` int(11) NOT NULL,
  `RatingID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resource_rating`
--

INSERT INTO `resource_rating` (`ResourceID`, `RatingID`) VALUES
(4, 3),
(4, 4),
(4, 6),
(5, 7);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `ReviewID` int(11) NOT NULL,
  `review_type` enum('faculty','course') NOT NULL DEFAULT 'faculty',
  `target_name` varchar(255) DEFAULT NULL,
  `course_code` varchar(50) DEFAULT NULL,
  `Review_Text` text DEFAULT NULL,
  `Rating_Score` int(11) DEFAULT NULL CHECK (`Rating_Score` between 1 and 5),
  `Review_Date` datetime DEFAULT current_timestamp(),
  `Reviewer_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`ReviewID`, `review_type`, `target_name`, `course_code`, `Review_Text`, `Rating_Score`, `Review_Date`, `Reviewer_ID`) VALUES
(1, 'faculty', NULL, NULL, 'ajhfhefhefehfufhe', 4, '2025-12-25 03:08:15', 3),
(2, 'faculty', NULL, NULL, 'shadyaeeh', 4, '2025-12-25 03:24:09', 2),
(3, 'faculty', NULL, NULL, 'hehhhhhhhh', 3, '2025-12-25 22:03:23', 3),
(4, 'faculty', NULL, NULL, 'sfseufen', 5, '2025-12-25 22:04:54', NULL),
(5, 'course', 'data', 'cse110', 'cfcerhrhdrhryjyjtydjdtjjy', 4, '2025-12-26 18:14:34', 3),
(6, 'faculty', NULL, NULL, 'hehhhh', 4, '2025-12-28 18:59:22', NULL),
(7, 'faculty', NULL, NULL, 'yoooo', 5, '2025-12-28 19:40:54', 2),
(8, 'faculty', NULL, NULL, 'yesss', 5, '2025-12-28 19:43:00', 3),
(9, 'faculty', NULL, NULL, 'wffesa', 3, '2025-12-28 20:12:02', 2),
(10, 'faculty', NULL, NULL, 'yeswh', 5, '2025-12-28 20:26:45', 2),
(11, 'course', 'data', 'cse111', 'dfeeff', 4, '2025-12-28 20:35:02', 2),
(12, 'faculty', 'shhh', 'cse111', 'noope', 4, '2025-12-28 20:36:35', 2),
(13, 'faculty', 'zyh', '3xse321', 'hehh nouuu', 4, '2025-12-28 20:52:31', 2),
(14, 'course', 'data h', 'cse220', 'nusaaaaaaaaaa', 5, '2025-12-28 20:53:10', 2);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `User_ID` int(11) NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `join_date` datetime DEFAULT current_timestamp(),
  `Role` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`User_ID`, `Name`, `Email`, `Password`, `join_date`, `Role`) VALUES
(1, 'Sadia Siddiqa', 'sadia.siddiqa3022@gmail.com', '$2y$10$s8pd5jJWbcqZ3N6HcI9NZezNiiDO.ABWFto09yjwSY.Z/qk4eCwXq', '2025-12-20 21:46:30', 'student'),
(2, 'nusaop', 'nusaop@gmail.com', '$2y$10$9x7XeFnllgC9Fh2wRqEy4.yEHqUoWzIOem9wpd33lY9l4Ca6qvZYm', '2025-12-25 02:27:52', 'admin'),
(3, 'nusu', 'nusu@gmail.com', '$2y$10$5qjCJswBdBrzoGOBGi1GCOjWquv0XFv6mBQ79dVqTZSx/H36YcvU2', '2025-12-25 03:06:56', 'student');

-- --------------------------------------------------------

--
-- Table structure for table `user_rating`
--

CREATE TABLE `user_rating` (
  `User_ID` int(11) NOT NULL,
  `RatingID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_rating`
--

INSERT INTO `user_rating` (`User_ID`, `RatingID`) VALUES
(1, 6),
(1, 7);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`Comment_ID`),
  ADD KEY `Commenter_ID` (`Commenter_ID`),
  ADD KEY `ResourceID` (`ResourceID`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`Course_Code`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`Initial`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `faculty_course_evaluation`
--
ALTER TABLE `faculty_course_evaluation`
  ADD PRIMARY KEY (`Review_ID`,`Course_Code`,`Faculty_Initial`),
  ADD KEY `Course_Code` (`Course_Code`),
  ADD KEY `Faculty_Initial` (`Faculty_Initial`);

--
-- Indexes for table `pending_reviews`
--
ALTER TABLE `pending_reviews`
  ADD PRIMARY KEY (`PendingID`);

--
-- Indexes for table `rating`
--
ALTER TABLE `rating`
  ADD PRIMARY KEY (`RatingID`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`ResourceID`),
  ADD KEY `fk_resources_course` (`Course_Code`),
  ADD KEY `fk_resources_user` (`Uploader_ID`);

--
-- Indexes for table `resource_download`
--
ALTER TABLE `resource_download`
  ADD PRIMARY KEY (`UserID`,`ResourceID`),
  ADD KEY `ResourceID` (`ResourceID`);

--
-- Indexes for table `resource_rating`
--
ALTER TABLE `resource_rating`
  ADD PRIMARY KEY (`ResourceID`,`RatingID`),
  ADD KEY `RatingID` (`RatingID`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`ReviewID`),
  ADD KEY `fk_review_user` (`Reviewer_ID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`User_ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `user_rating`
--
ALTER TABLE `user_rating`
  ADD PRIMARY KEY (`User_ID`,`RatingID`),
  ADD KEY `RatingID` (`RatingID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `Comment_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pending_reviews`
--
ALTER TABLE `pending_reviews`
  MODIFY `PendingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `rating`
--
ALTER TABLE `rating`
  MODIFY `RatingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `ResourceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `User_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`Commenter_ID`) REFERENCES `user` (`User_ID`),
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`ResourceID`) REFERENCES `resources` (`ResourceID`);

--
-- Constraints for table `faculty_course_evaluation`
--
ALTER TABLE `faculty_course_evaluation`
  ADD CONSTRAINT `faculty_course_evaluation_ibfk_1` FOREIGN KEY (`Review_ID`) REFERENCES `review` (`ReviewID`),
  ADD CONSTRAINT `faculty_course_evaluation_ibfk_2` FOREIGN KEY (`Course_Code`) REFERENCES `course` (`Course_Code`),
  ADD CONSTRAINT `faculty_course_evaluation_ibfk_3` FOREIGN KEY (`Faculty_Initial`) REFERENCES `faculty` (`Initial`);

--
-- Constraints for table `resources`
--
ALTER TABLE `resources`
  ADD CONSTRAINT `fk_resources_course` FOREIGN KEY (`Course_Code`) REFERENCES `course` (`Course_Code`),
  ADD CONSTRAINT `fk_resources_user` FOREIGN KEY (`Uploader_ID`) REFERENCES `user` (`User_ID`);

--
-- Constraints for table `resource_download`
--
ALTER TABLE `resource_download`
  ADD CONSTRAINT `resource_download_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`User_ID`),
  ADD CONSTRAINT `resource_download_ibfk_2` FOREIGN KEY (`ResourceID`) REFERENCES `resources` (`ResourceID`);

--
-- Constraints for table `resource_rating`
--
ALTER TABLE `resource_rating`
  ADD CONSTRAINT `resource_rating_ibfk_1` FOREIGN KEY (`ResourceID`) REFERENCES `resources` (`ResourceID`),
  ADD CONSTRAINT `resource_rating_ibfk_2` FOREIGN KEY (`RatingID`) REFERENCES `rating` (`RatingID`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `fk_review_user` FOREIGN KEY (`Reviewer_ID`) REFERENCES `user` (`User_ID`);

--
-- Constraints for table `user_rating`
--
ALTER TABLE `user_rating`
  ADD CONSTRAINT `user_rating_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `user` (`User_ID`),
  ADD CONSTRAINT `user_rating_ibfk_2` FOREIGN KEY (`RatingID`) REFERENCES `rating` (`RatingID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
