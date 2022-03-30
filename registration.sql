-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 30, 2022 at 03:21 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `registration`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(70) NOT NULL,
  `password` varchar(70) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$jhrkll9LqU/2Ue1NEbal2OcGSBYZCxkzvdp2JlAo4ZcFx2oH79Brq');

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `Id` int(11) NOT NULL,
  `Sender` varchar(255) NOT NULL,
  `Recipient` varchar(255) NOT NULL,
  `Message` text NOT NULL,
  `DateSent` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Seen` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `chats`
--

INSERT INTO `chats` (`Id`, `Sender`, `Recipient`, `Message`, `DateSent`, `Seen`) VALUES
(1, 'admin', 'joemar1', 'Hello doy.', '2022-03-17 06:13:42', 0),
(2, 'admin', 'juan001', 'Hoy juan, nasaan si Pedro?', '2022-03-20 05:11:45', 0),
(3, 'admin', 'juan001', 'reply ASAP', '2022-03-20 08:14:10', 0),
(4, 'admin', 'joemar1', 'Very good student, Mr. Closa.', '2022-03-20 08:41:53', 0),
(10, 'joemar1', 'admin', 'Thank you sir!', '2022-03-20 16:02:37', 0),
(11, 'juan001', 'admin', 'slr sir. d ko po alam.', '2022-03-20 16:11:00', 0),
(12, 'admin', 'Niel', 'Niel, tagay.', '2022-03-20 17:07:01', 0),
(13, 'admin', 'juan001', 'Okay. Paki sabi, hulog na sya.', '2022-03-20 17:47:38', 0),
(14, 'admin', 'Stephanie', 'Good morning, Miss Reimer. I want the list of all vaccinated students in your class. Send it to me ASAP. Thank you.', '2022-03-20 17:57:12', 0),
(15, 'Stephanie', 'admin', 'Okay sir. I will send it to you tomorrow. ', '2022-03-20 18:56:45', 0),
(16, 'Stephanie', 'admin', 'Some of the students haven\'t responded yet sir.', '2022-03-20 19:02:07', 0),
(17, 'Stephanie', 'admin', 'Sorry for the delay sir.', '2022-03-20 19:03:46', 0),
(18, 'Stephanie', 'admin', 'Sir. One of the students refuses to give their vaccination details.', '2022-03-20 19:15:11', 0),
(19, 'Stephanie', 'admin', 'It was Mr. de la Cruz sir.', '2022-03-20 19:16:50', 0),
(20, 'admin', 'Stephanie', 'Tell Mr. de la Cruz that I will call his parents if he continues his attitude.', '2022-03-20 19:18:04', 0),
(21, 'admin', 'joemar1', 'No problem, Mr. Closa. You are one of our bright students in your year level.', '2022-03-20 19:19:34', 0),
(22, 'joemar1', 'admin', 'Thank you, thank you sir!', '2022-03-21 06:19:11', 0),
(23, 'joemar1', 'admin', 'THank you again sir!', '2022-03-21 06:21:24', 0);

-- --------------------------------------------------------

--
-- Table structure for table `css`
--

CREATE TABLE `css` (
  `Id` int(11) NOT NULL,
  `Activity_Title` varchar(255) NOT NULL,
  `Instructions` text NOT NULL,
  `ModuleFile` text NOT NULL,
  `MaxScore` int(11) NOT NULL,
  `Deadline` date NOT NULL,
  `Open` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `css`
--

INSERT INTO `css` (`Id`, `Activity_Title`, `Instructions`, `ModuleFile`, `MaxScore`, `Deadline`, `Open`) VALUES
(3, 'Activity 1', '\"{\\\"ops\\\":[{\\\"insert\\\":\\\"Create a robot that can talk and walk.\\\\n\\\"}]}\"', 'robot2.pdf', 5, '2022-04-30', 1);

-- --------------------------------------------------------

--
-- Table structure for table `english`
--

CREATE TABLE `english` (
  `Id` int(11) NOT NULL,
  `Activity_Title` varchar(255) NOT NULL,
  `Instructions` text NOT NULL,
  `ModuleFile` text NOT NULL,
  `MaxScore` int(11) NOT NULL,
  `Deadline` date NOT NULL,
  `Open` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `english`
--

INSERT INTO `english` (`Id`, `Activity_Title`, `Instructions`, `ModuleFile`, `MaxScore`, `Deadline`, `Open`) VALUES
(3, 'Module 1', '\"{\\\"ops\\\":[{\\\"insert\\\":\\\"Create a robot.\\\\n\\\"}]}\"', '', 5, '2022-03-31', 1),
(4, 'Quiz 1', '\"{\\\"ops\\\":[{\\\"insert\\\":\\\"Spell the following words.\\\"},{\\\"attributes\\\":{\\\"header\\\":3},\\\"insert\\\":\\\"\\\\n\\\"},{\\\"insert\\\":\\\"\\\\nbanana\\\"},{\\\"attributes\\\":{\\\"list\\\":\\\"ordered\\\"},\\\"insert\\\":\\\"\\\\n\\\"},{\\\"insert\\\":\\\"apple\\\"},{\\\"attributes\\\":{\\\"list\\\":\\\"ordered\\\"},\\\"insert\\\":\\\"\\\\n\\\"},{\\\"insert\\\":\\\"stardenburdenhardenbart\\\"},{\\\"attributes\\\":{\\\"list\\\":\\\"ordered\\\"},\\\"insert\\\":\\\"\\\\n\\\"},{\\\"insert\\\":\\\"supercalifragilisticexpialidocious\\\"},{\\\"attributes\\\":{\\\"list\\\":\\\"ordered\\\"},\\\"insert\\\":\\\"\\\\n\\\"},{\\\"insert\\\":\\\"Pneumonoultramicroscopicsilicovolcanoconiosis\\\"},{\\\"attributes\\\":{\\\"list\\\":\\\"ordered\\\"},\\\"insert\\\":\\\"\\\\n\\\"},{\\\"insert\\\":\\\"\\\\n\\\"}]}\"', '', 5, '2022-04-09', 0);

-- --------------------------------------------------------

--
-- Table structure for table `filipino`
--

CREATE TABLE `filipino` (
  `Id` int(11) NOT NULL,
  `Activity_Title` varchar(255) NOT NULL,
  `Instructions` text NOT NULL,
  `ModuleFile` text NOT NULL,
  `MaxScore` int(11) NOT NULL,
  `Deadline` date NOT NULL,
  `Open` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `filipino`
--

INSERT INTO `filipino` (`Id`, `Activity_Title`, `Instructions`, `ModuleFile`, `MaxScore`, `Deadline`, `Open`) VALUES
(3, 'Gawain 1', '\"{\\\"ops\\\":[{\\\"insert\\\":\\\"Sumulat ng isang kwento na hindi lalagpas sa dalawampu\'t salita lamang.\\\\n\\\"}]}\"', '', 50, '2022-03-31', 1),
(4, 'Gawain2', '\"{\\\"ops\\\":[{\\\"insert\\\":\\\"Sumulat ng kwento.\\\\n\\\"}]}\"', '', 22, '2022-04-01', 1);

-- --------------------------------------------------------

--
-- Table structure for table `homeroomguidance`
--

CREATE TABLE `homeroomguidance` (
  `Id` int(11) NOT NULL,
  `Activity_Title` varchar(255) NOT NULL,
  `Instructions` text NOT NULL,
  `ModuleFile` text NOT NULL,
  `MaxScore` int(11) NOT NULL,
  `Deadline` date NOT NULL,
  `Open` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pe`
--

CREATE TABLE `pe` (
  `Id` int(11) NOT NULL,
  `Activity_Title` varchar(255) NOT NULL,
  `Instructions` text NOT NULL,
  `ModuleFile` text NOT NULL,
  `MaxScore` int(11) NOT NULL,
  `Deadline` date NOT NULL,
  `Open` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `philosophy`
--

CREATE TABLE `philosophy` (
  `Id` int(11) NOT NULL,
  `Activity_Title` varchar(255) NOT NULL,
  `Instructions` text NOT NULL,
  `ModuleFile` text NOT NULL,
  `MaxScore` int(11) NOT NULL,
  `Deadline` date NOT NULL,
  `Open` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `philosophy`
--

INSERT INTO `philosophy` (`Id`, `Activity_Title`, `Instructions`, `ModuleFile`, `MaxScore`, `Deadline`, `Open`) VALUES
(1, 'Activity 1', '\"{\\\"ops\\\":[{\\\"insert\\\":\\\"Activity 1\\\"},{\\\"attributes\\\":{\\\"header\\\":2},\\\"insert\\\":\\\"\\\\n\\\"},{\\\"insert\\\":\\\"1. Who is Hitler?\\\\n2. Why we need Hitler?\\\\n3. Is Nazi a boy band?\\\\n4. Which is better, Kar98 or M1 Garand? Explain.\\\\n5. Will you die for Hitler?\\\\n\\\"}]}\"', 'Sample activity.pdf', 100, '2022-02-12', 1),
(2, 'Sample Activity', '\"{\\\"ops\\\":[{\\\"insert\\\":\\\"Lorem ipsum dolor\\\\n\\\"}]}\"', 'Arduino Learning Kit Manual- Keyrens.pdf', 50, '2022-03-11', 1),
(3, 'Test 1', '\"{\\\"ops\\\":[{\\\"insert\\\":\\\"Do Test 1.\\\\n\\\"}]}\"', '', 100, '2022-03-31', 0);

-- --------------------------------------------------------

--
-- Table structure for table `practicalresearch`
--

CREATE TABLE `practicalresearch` (
  `Id` int(11) NOT NULL,
  `Activity_Title` varchar(255) NOT NULL,
  `Instructions` text NOT NULL,
  `ModuleFile` text NOT NULL,
  `MaxScore` int(11) NOT NULL,
  `Deadline` date NOT NULL,
  `Open` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `Id` smallint(5) UNSIGNED NOT NULL,
  `Subject` varchar(70) NOT NULL,
  `ActivityId` int(11) NOT NULL,
  `StudentUsername` varchar(255) NOT NULL,
  `UploadedFile` text NOT NULL,
  `DateSubmitted` date NOT NULL DEFAULT current_timestamp(),
  `Score` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `submissions`
--

INSERT INTO `submissions` (`Id`, `Subject`, `ActivityId`, `StudentUsername`, `UploadedFile`, `DateSubmitted`, `Score`) VALUES
(1, 'philosophy', 1, 'joemar1', 'Install PHP.pdf', '2022-03-01', 86),
(2, 'philosophy', 1, 'fred101', 'fred.pdf', '2022-03-13', NULL),
(6, 'philosophy', 2, 'joemar1', 'lorem ipsum - CLOSA.pdf', '2022-03-30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `usertable`
--

CREATE TABLE `usertable` (
  `Username` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Firstname` varchar(70) NOT NULL,
  `Lastname` varchar(70) NOT NULL,
  `Email` varchar(128) NOT NULL,
  `DateOfBirth` date NOT NULL,
  `Gender` int(11) NOT NULL,
  `Address` text NOT NULL,
  `PhoneNumber` varchar(11) NOT NULL,
  `Avatar` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `usertable`
--

INSERT INTO `usertable` (`Username`, `Password`, `Firstname`, `Lastname`, `Email`, `DateOfBirth`, `Gender`, `Address`, `PhoneNumber`, `Avatar`) VALUES
('carter123', '$2y$10$Uo.RYmo.Nz4.vtbQIoIH/uliaMz32WWrV9ukEt0dhSZEKXbyQ9Ivq', 'Snich', 'Carter', 'cartersnich@hotmail.com', '2000-04-20', 0, 'Tacloban City', '09158746322', '2017-05-05 12.09.28.jpg'),
('fred101', '$2y$10$L4oYRGwhVLSXwownFdRlvOn8GAK1N0FnjQu0217cnYzDYye4dCy1S', 'Fredrik', 'Schwalsky', 'fred@hotmail.org', '2000-01-01', 0, 'Marinduque', '09132468754', '687px-Fire.JPG'),
('jericho', '$2y$10$zsKn/Os4rr.s11wY/9EDsOUMPAX79HwQkKkr94oIzVn5kKYjWTnPS', 'Jericho', 'Montimor', 'montimor@gmail.com', '2000-02-10', 0, 'Mayorga, Leyte', '09123468792', '3_momo.jpg'),
('joemar1', '$2y$10$zsKn/Os4rr.s11wY/9EDsOUMPAX79HwQkKkr94oIzVn5kKYjWTnPS', 'Joe Mar', 'Closa', 'jmestreraclosa@gmail.com', '2000-03-05', 0, 'MacArthur, Leyte', '09123456789', '18222139_195308050988671_2490619185082802274_n.jpg'),
('juan001', '$2y$10$zsKn/Os4rr.s11wY/9EDsOUMPAX79HwQkKkr94oIzVn5kKYjWTnPS', 'Juan', 'de la Cruz', 'juan001@gmail.com', '2001-02-01', 0, 'Baybay City', '09569233549', 'baymax.png'),
('lipoy', '$2y$10$zsKn/Os4rr.s11wY/9EDsOUMPAX79HwQkKkr94oIzVn5kKYjWTnPS', 'Jepoy', 'Lacambra', 'lacambra@yahoo.com', '2000-02-10', 0, 'Dulag, Leyte', '09586734213', 'Bryan_Fury_T6BR.jpg'),
('Niel', '$2y$10$zsKn/Os4rr.s11wY/9EDsOUMPAX79HwQkKkr94oIzVn5kKYjWTnPS', 'Niel', 'Lumpas', 'lumpasniel@rocketmail.com', '2000-02-10', 0, 'Mayorga, Leyte', '09458763127', '__samsung_sam_samsung_drawn_by_vulcan_ejel2000__0ad2d3410427614da50c5251a1a53d53.jpg'),
('Shahanny', '$2y$10$zsKn/Os4rr.s11wY/9EDsOUMPAX79HwQkKkr94oIzVn5kKYjWTnPS', 'Shannie', 'de la Rama', 'me@shannie.com', '2000-02-10', 1, 'Tolosa, Leyte', '09852637412', '7787a113f4fdb61a493753d436221fb7.jpg'),
('Stephanie', '$2y$10$zsKn/Os4rr.s11wY/9EDsOUMPAX79HwQkKkr94oIzVn5kKYjWTnPS', 'Stephanie', 'Reimer', 'stephanie@reimer.org', '2000-02-10', 1, 'Tacloban City', '09563978412', '1000187_1383764711836337_1978437302_n.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `css`
--
ALTER TABLE `css`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `english`
--
ALTER TABLE `english`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `filipino`
--
ALTER TABLE `filipino`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `homeroomguidance`
--
ALTER TABLE `homeroomguidance`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `pe`
--
ALTER TABLE `pe`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `philosophy`
--
ALTER TABLE `philosophy`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `practicalresearch`
--
ALTER TABLE `practicalresearch`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `fk_student_username` (`StudentUsername`);

--
-- Indexes for table `usertable`
--
ALTER TABLE `usertable`
  ADD PRIMARY KEY (`Username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `css`
--
ALTER TABLE `css`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `english`
--
ALTER TABLE `english`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `filipino`
--
ALTER TABLE `filipino`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `homeroomguidance`
--
ALTER TABLE `homeroomguidance`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pe`
--
ALTER TABLE `pe`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `philosophy`
--
ALTER TABLE `philosophy`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `practicalresearch`
--
ALTER TABLE `practicalresearch`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `Id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `fk_student_username` FOREIGN KEY (`StudentUsername`) REFERENCES `usertable` (`Username`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
