-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2024 at 04:32 PM
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
(0, 'John Doe', 'jdoe@e.com', '$2y$10$AcnBCo4Hg3TAy5OTwV1UIulAE1e9W8N6qN/V4jx6qFSArmp6iYLYW'),
(0, 'admin', 'admin@e.com', '$2y$10$/w.GnOk29xZt4EsOV6L5Aea04nANHgMURPpZeNobeAAjsAcjmQbom');

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
(29, 'Harry Potter and the Philosopher\'s Stone', '0747549559', 'J. K. Rowling', 8.00, 'Fantasy', '1997-06-26', '673dfd3c94ffd.jpg', '2024-11-19 13:16:11', 5),
(37, '1984', '546546456', 'George Orwell', 10.00, 'Dystopian', '2024-11-20', '673dfef3b5be2.jpg', '2024-11-20 14:37:53', 4),
(40, 'Oz the Great and Powerful', '504751833', 'Mitchell Kapner', 12.00, 'Fantasy', '2024-12-01', '674c78ed26515.jpg', '2024-12-01 14:55:41', 10),
(41, 'Mein Kampf', '736348456', 'Adolf Hitler', 15.00, 'Historical', '2024-12-01', '674c79971b716.jpg', '2024-12-01 14:58:31', 10),
(42, '9/11', '7547896', 'Lauren Scott', 11.00, 'Historical', '2001-09-11', '674c7a0079a5a.jpg', '2024-12-01 15:00:16', 10),
(43, 'Naria', '6853663', 'Scott Davis', 14.00, 'Fantasy', '2024-12-01', '674c7a539c449.jpg', '2024-12-01 15:01:39', 10),
(44, 'A Man Called Ove', '9573453467', 'Ove', 20.00, 'Contemporary', '2024-12-01', '674c7aadbfe2f.jpg', '2024-12-01 15:03:09', 10),
(45, 'Dune', '9674563', 'Frank Herbert', 16.00, 'Sci-Fi', '2024-12-01', '674c7b12c2e5b.jpg', '2024-12-01 15:04:50', 18),
(46, 'Life of Pi', '35638835', 'Yann Martel', 27.00, 'Adventure', '2024-12-01', '674c7b6fab2d0.jpg', '2024-12-01 15:06:23', 65),
(47, 'The Love Hypothesis', '94735623', 'Ali Hazelwood', 18.00, 'Romance', '2024-12-01', '674c7be09bc74.jpg', '2024-12-01 15:08:16', 43),
(48, 'The Hound of the Baskervilles', '23452627', 'Arthur Conan', 34.00, 'Mystery', '2024-12-01', '674c7c7781310.jpg', '2024-12-01 15:10:47', 13),
(49, 'It', '34268234', 'Stephen King', 12.00, 'Horror', '2024-12-01', '674c7cd7dd3ec.jpg', '2024-12-01 15:12:23', 19),
(50, 'Never Lie', '3526732', 'Freida McFadden', 14.00, 'Thriller', '2024-12-01', '674c7d4707cf1.jpg', '2024-12-01 15:14:15', 10),
(51, 'Three Body Problem', '3256278', 'Cixin Liu', 13.00, 'Sci-Fi', '2024-12-01', '674c7dd72523d.jpg', '2024-12-01 15:16:39', 12),
(52, 'Journey to the Center of the Earth', '34275375', 'Jules Verne', 11.00, 'Adventure', '2024-12-01', '674c7e5f059a9.jpg', '2024-12-01 15:18:55', 10);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Processing','Shipped','Delivered') NOT NULL DEFAULT 'Pending',
  `transaction_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `orders`
--
DELIMITER $$
CREATE TRIGGER `after_order_insert` AFTER INSERT ON `orders` FOR EACH ROW BEGIN
    INSERT INTO sales_history (order_id, total_amount, transaction_date)
    VALUES (NEW.id, NEW.total_amount, NEW.created_at);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_unit` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_history`
--

CREATE TABLE `sales_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_history`
--

INSERT INTO `sales_history` (`id`, `order_id`, `total_amount`, `transaction_date`) VALUES
(1, 55, 8.00, '2024-12-01 10:56:41'),
(2, 56, 18.00, '2024-12-01 11:00:41'),
(3, 57, 8.00, '2024-12-01 11:02:01'),
(4, 58, 60.00, '2024-12-01 11:06:03'),
(5, 59, 40.00, '2024-12-01 12:41:15'),
(6, 60, 40.00, '2024-12-01 12:41:23'),
(7, 61, 10.00, '2024-12-01 12:50:20'),
(8, 62, 10.00, '2024-12-01 12:52:19'),
(9, 63, 10.00, '2024-12-01 12:53:05'),
(10, 64, 10.00, '2024-12-01 12:53:07'),
(11, 65, 10.00, '2024-12-01 12:53:07'),
(12, 66, 10.00, '2024-12-01 12:53:08'),
(13, 67, 10.00, '2024-12-01 12:53:18'),
(14, 68, 10.00, '2024-12-01 12:53:34'),
(15, 69, 10.00, '2024-12-01 12:54:09'),
(16, 70, 10.00, '2024-12-01 12:54:10'),
(17, 71, 10.00, '2024-12-01 12:54:10'),
(18, 72, 10.00, '2024-12-01 12:54:10'),
(19, 73, 10.00, '2024-12-01 12:54:10'),
(20, 74, 10.00, '2024-12-01 12:54:11'),
(21, 75, 10.00, '2024-12-01 12:54:11'),
(22, 76, 10.00, '2024-12-01 12:54:11'),
(23, 77, 10.00, '2024-12-01 12:54:11'),
(24, 78, 10.00, '2024-12-01 12:54:11'),
(25, 79, 10.00, '2024-12-01 12:54:11'),
(26, 80, 10.00, '2024-12-01 12:54:12'),
(27, 81, 10.00, '2024-12-01 12:54:13'),
(28, 82, 10.00, '2024-12-01 12:54:13'),
(29, 83, 10.00, '2024-12-01 12:54:14'),
(30, 84, 10.00, '2024-12-01 12:54:14'),
(31, 85, 10.00, '2024-12-01 12:54:15'),
(32, 86, 10.00, '2024-12-01 12:54:20'),
(33, 87, 10.00, '2024-12-01 12:55:16'),
(34, 88, 10.00, '2024-12-01 12:55:28'),
(35, 89, 10.00, '2024-12-01 12:55:28'),
(36, 90, 10.00, '2024-12-01 12:55:35'),
(37, 91, 10.00, '2024-12-01 12:56:22'),
(38, 92, 10.00, '2024-12-01 12:56:23'),
(39, 93, 10.00, '2024-12-01 12:56:23'),
(40, 94, 10.00, '2024-12-01 12:56:23'),
(41, 95, 10.00, '2024-12-01 12:56:23'),
(42, 96, 10.00, '2024-12-01 12:56:24'),
(43, 97, 10.00, '2024-12-01 12:56:53'),
(44, 98, 10.00, '2024-12-01 12:57:01'),
(45, 99, 18.00, '2024-12-01 13:36:13'),
(46, 100, 18.00, '2024-12-01 13:37:01'),
(47, 101, 18.00, '2024-12-01 13:37:06'),
(48, 102, 18.00, '2024-12-01 13:37:12'),
(49, 103, 26.00, '2024-12-01 13:39:57'),
(50, 104, 26.00, '2024-12-01 13:40:55'),
(51, 105, 26.00, '2024-12-01 13:40:58'),
(52, 106, 26.00, '2024-12-01 13:40:59'),
(53, 107, 10.00, '2024-12-01 13:47:58'),
(54, 108, 10.00, '2024-12-01 13:48:50'),
(55, 109, 10.00, '2024-12-01 13:51:03'),
(56, 110, 10.00, '2024-12-01 13:51:47'),
(57, 111, 18.00, '2024-12-01 14:16:26'),
(58, 112, 10.00, '2024-12-01 14:17:12'),
(59, 113, 10.00, '2024-12-01 14:18:39'),
(60, 114, 26.00, '2024-12-01 14:30:46'),
(61, 115, 16.00, '2024-12-01 14:40:07'),
(62, 116, 235.00, '2024-12-01 15:20:29'),
(63, 117, 11.00, '2024-12-01 15:30:50');

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
(13, 'johnd@e.com', '$2y$10$PezR3ioj66huP.CARKGU.O79bDmRLkV/CwF.9q5K1sAcEHjCHTW8C'),
(15, 'testaccount@e.com', '$2y$10$5W05VqLVBov0fKM1uWRqV.T0PULbVtYTW0/EAUhPT/UIe2Xx2wbtW'),
(18, 'deeznuts@e.com', '$2y$10$cpkVHp6h25oyoO6RxTfsPOprS7QLgxaAucU8bh255kmjpxBOdKMkO'),
(19, 'skibidisigma@e.com', '$2y$10$WODucDN4aC4JORaepmzqQ.q.vt8qYBncWpxmsWpEeood8Ti/wnSbK');

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`id`, `user_id`, `full_name`, `phone`, `address`, `created_at`, `updated_at`) VALUES
(5, 13, 'John Doe', '09612526735', 'test', '2024-12-01 08:36:24', '2024-12-01 08:36:24'),
(7, 15, 'nigger', '0912356787', 'niggerhood', '2024-12-01 12:41:46', '2024-12-01 12:41:46'),
(8, 18, 'deeznuts', '12345678900', 'test 1234', '2024-12-01 13:34:07', '2024-12-01 14:43:07'),
(10, 19, 'skibidi', '12345678900', 'street wood 123', '2024-12-01 14:27:03', '2024-12-01 14:27:03');

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
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `sales_history`
--
ALTER TABLE `sales_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `shopuser`
--
ALTER TABLE `shopuser`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookstore`
--
ALTER TABLE `bookstore`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;

--
-- AUTO_INCREMENT for table `sales_history`
--
ALTER TABLE `sales_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `shopuser`
--
ALTER TABLE `shopuser`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `bookstore` (`id`);

--
-- Constraints for table `user_details`
--
ALTER TABLE `user_details`
  ADD CONSTRAINT `user_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `shopuser` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
