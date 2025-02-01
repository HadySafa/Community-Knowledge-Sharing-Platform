-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 01, 2025 at 11:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sharingplatform`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `Id` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`Id`, `Name`) VALUES
(1, 'Backend'),
(5, 'Database'),
(22, 'Entity Framework'),
(2, 'Frontend'),
(4, 'Problem/Solution'),
(3, 'Tips');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `Id` int(11) NOT NULL,
  `Comment` text NOT NULL,
  `PostId` int(11) NOT NULL,
  `UserId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`Id`, `Comment`, `PostId`, `UserId`) VALUES
(1, 'Good job hady!', 6, 5),
(2, 'Will try it!', 5, 5),
(3, 'It\'s good not bad.', 1, 5),
(4, 'Hahaha that\'s true.', 3, 6),
(7, 'Hello', 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `Id` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `Link` varchar(255) DEFAULT NULL,
  `CodeSnippet` text DEFAULT NULL,
  `CategoryId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`Id`, `UserId`, `Title`, `Description`, `Link`, `CodeSnippet`, `CategoryId`) VALUES
(1, 1, 'Frontend Journey Update', 'I would like to announce that I have finished the frontend part of the project succesfully! Excited to share this milestone with you.', 'https://github.com/hadysafa', '', 2),
(3, 1, 'Backend Journey Update!', 'I would like to announce that I have successfully finished the backend part of my project! Happy to share this milestone with you all.', 'https://github.com/hadysafa', '', 1),
(4, 2, 'Postman!', 'I would advice you to use Postman for testing your APIs in your next project. Make sure to try it!', 'https://www.postman.com/', '', 3),
(5, 2, 'FlexFroggy', 'FlexFroggy is a great game that will help you master flexbox! Make sure to try it.', 'https://flexboxfroggy.com/', '', 2),
(6, 4, 'Debugging Tricks!', 'As developers, we are ready to spend about 3 hours debuggingg, instead of reading documentation. Make sure to read the docs well, it will save your time!', '', '', 4),
(7, 6, 'PHP', 'PHP is a powerful langauge that\'s mainly used for handling backend processes of a web application, moreover it is easy to be learned and integrated into your project.', 'https://www.php.com/', 'echo \"Hello world!\";', 1),
(8, 1, 'React-Icons', 'When using ReactJS, make sure to use react icons which allows you to add icons to your website easily.\nCheck the link below for icons, and run the below command in your terminal to install the required libraries.', 'https://react-icons.github.io/react-icons/', 'npm install react-icons', 2);

-- --------------------------------------------------------

--
-- Table structure for table `reactions`
--

CREATE TABLE `reactions` (
  `Id` int(11) NOT NULL,
  `Reaction` varchar(255) NOT NULL,
  `PostId` int(11) NOT NULL,
  `UserId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reactions`
--

INSERT INTO `reactions` (`Id`, `Reaction`, `PostId`, `UserId`) VALUES
(1, 'Like', 3, 4),
(2, 'Like', 4, 4),
(3, 'Like', 1, 4),
(4, 'Dislike', 5, 4),
(5, 'Like', 6, 5),
(6, 'Like', 5, 5),
(7, 'Dislike', 1, 5),
(8, 'Like', 3, 5),
(9, 'Like', 4, 6),
(10, 'Like', 3, 6),
(11, 'Like', 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `Id` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `PostId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`Id`, `Name`, `PostId`) VALUES
(1, 'Frontend', 1),
(2, 'Coding', 1),
(3, 'Coding', 3),
(4, 'Tips', 4),
(5, 'Postman', 4),
(6, 'Frontend', 5),
(7, 'CSS', 5),
(8, 'FlexFroggy', 5),
(9, 'Tips', 6),
(10, 'Debugging', 6),
(11, 'Solutions', 6),
(12, 'PHP', 7),
(13, 'Backend', 7);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `Id` int(11) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `PhoneNumber` varchar(20) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `Role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`Id`, `FullName`, `PhoneNumber`, `Username`, `Password`, `Role`) VALUES
(1, 'Hady Safa', '70860860', 'hady__', '$2y$10$CfHTN3bAMGgbbOaEV/NGGuxFA5NPzLfjoakhffezQrDliLLaqZF1y', 'User'),
(2, 'Walid', '70111111', 'walid__', '$2y$10$pM9TFUhDjYD2rKptwYB3O.Qy0S1CdJ229ZPZmC.WARTll8CqLe6A.', 'User'),
(3, 'Suha', '70222222', 'suha__', '$2y$10$cmnNKh1OBCxABt6PSp59fOWfnya6/Q95FZel7qLrYVqa.pQ1Q8tiy', 'Manager'),
(4, 'Lana', '70333333', 'lana__', '$2y$10$fnot1vldvjfP094M9xqUOOmVN7kV2/wpdDXWZs4cgjCYadgialHsC', 'User'),
(5, 'Joe', '70444444', 'joe__', '$2y$10$E3Bt2gVD1Ft2aKG7MykkKebr/h6q3hd20kwW4iDXbJhxmAfYlqoAK', 'User'),
(6, 'Adam', '70555555', 'adam__', '$2y$10$L2jaKUYg4crRiG3aPS/WPejq94mpjW.rd5/UL5nIX4oTdkWf7Bkii', 'User');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Name` (`Name`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `PostId` (`PostId`),
  ADD KEY `UserId` (`UserId`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `CategoryId` (`CategoryId`);

--
-- Indexes for table `reactions`
--
ALTER TABLE `reactions`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `PostId` (`PostId`),
  ADD KEY `UserId` (`UserId`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `PostId` (`PostId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `PhoneNumber` (`PhoneNumber`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reactions`
--
ALTER TABLE `reactions`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`PostId`) REFERENCES `posts` (`Id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`UserId`) REFERENCES `users` (`Id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`Id`) ON DELETE CASCADE,
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`CategoryId`) REFERENCES `categories` (`Id`) ON DELETE CASCADE;

--
-- Constraints for table `reactions`
--
ALTER TABLE `reactions`
  ADD CONSTRAINT `reactions_ibfk_1` FOREIGN KEY (`PostId`) REFERENCES `posts` (`Id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reactions_ibfk_2` FOREIGN KEY (`UserId`) REFERENCES `users` (`Id`) ON DELETE CASCADE;

--
-- Constraints for table `tags`
--
ALTER TABLE `tags`
  ADD CONSTRAINT `tags_ibfk_1` FOREIGN KEY (`PostId`) REFERENCES `posts` (`Id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
