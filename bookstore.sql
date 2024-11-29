-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2024 at 03:45 PM
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
-- Database: `bookstore`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminuser`
--

CREATE TABLE `adminuser` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adminuser`
--

INSERT INTO `adminuser` (`id`, `full_name`, `email`, `password`) VALUES
(0, 'Renzo Zamora Luna', 'lunarenzo@e.com', '$2y$10$4lflKNW.lxsuDceW4wqbs.P0yWtUQWlZZc1bsyJ3Y0BcPaPjWmq2a'),
(0, 'John Vince Decena', 'vince@e.com', '$2y$10$D1y6OdQv/oy2YKarYzQtmuPuXRTyX1H1i6NMX8s1iRzbTotcZQrji'),
(0, 'John Doe', 'jdoe@e.com', '$2y$10$AcnBCo4Hg3TAy5OTwV1UIulAE1e9W8N6qN/V4jx6qFSArmp6iYLYW');

-- --------------------------------------------------------

--
-- Table structure for table `bookstore`
--

CREATE TABLE `bookstore` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `isbn` varchar(13) NOT NULL,
  `author` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `genre` varchar(255) DEFAULT NULL,
  `date_published` date NOT NULL,
  `book_cover` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `stock_available` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookstore`
--

INSERT INTO `bookstore` (`id`, `title`, `isbn`, `author`, `price`, `genre`, `date_published`, `book_cover`, `created_at`, `stock_available`) VALUES
(29, 'Harry Potter and the Philosopher\'s Stone', '0747549559', 'J. K. Rowling', 8.00, 'Fantasy', '1997-06-26', '673dfd3c94ffd.jpg', '2024-11-19 13:16:11', 11),
(37, '1984', '546546456', 'George Orwell', 10.00, 'Dystopian', '2024-11-20', '673dfef3b5be2.jpg', '2024-11-20 14:37:53', 12),
(39, 'test', '123123', 'test', 12.00, 'Fantasy', '2024-11-29', '6749d1b22c68a.png', '2024-11-29 14:37:38', 0);

-- --------------------------------------------------------

--
-- Table structure for table `shopuser`
--

CREATE TABLE `shopuser` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shopuser`
--

INSERT INTO `shopuser` (`id`, `email`, `password`) VALUES
(0, 'john@e.com', '$2y$10$MDrWACPMryM6gXDBhX4zcuVYEc7X1IgodN9bvsrrMn7Qob1xbFEU2'),
(0, 'test@e.com', '$2y$10$zb5jwe/ETLiHxfAAAJdswu8dkWpTfcQ.JIAdbBtVg0ISsrw99J7tO'),
(0, 'renzo@e.com', '$2y$10$vUhJRzxkxZ8WsT9/zun9YeSXX6vca4k8LUtWzfX3yXjU/dBMvd1ui');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookstore`
--
ALTER TABLE `bookstore`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookstore`
--
ALTER TABLE `bookstore`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
