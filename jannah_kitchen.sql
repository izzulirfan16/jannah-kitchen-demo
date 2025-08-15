-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 10, 2025 at 12:56 AM
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
-- Database: `jannah_kitchen`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `menu_item_id`, `quantity`, `added_at`) VALUES
(25, 3, 17, 1, '2025-07-09 17:34:45'),
(26, 3, 15, 1, '2025-07-09 17:34:47');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Makanan', 'Traditional Malaysian and Middle Eastern main courses', '2025-06-24 15:32:47'),
(3, 'Minuman', 'Fresh juices and traditional drinks', '2025-06-24 15:32:47');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `description`, `price`, `category_id`, `image_url`, `is_available`, `created_at`) VALUES
(9, 'Nasi Goreng Daging', '+ Telur Mata & Air', 6.00, 1, 'NGD.jpg', 1, '2025-06-30 02:38:09'),
(10, 'Nasi Goreng Kampung', '+ Telur Mata & Air', 6.00, 1, 'NGK.jpg', 1, '2025-06-30 02:39:03'),
(11, 'Nasi Goreng Keretapi', '+ Telur Mata & Air', 6.00, 1, 'NGKP.jpg', 1, '2025-06-30 02:39:46'),
(12, 'Nasi Goreng Biasa', '+ Telur Mata & Air', 6.00, 1, 'NGB.jpg', 1, '2025-06-30 02:40:11'),
(13, 'Nasi Goreng Udang', '+ Telur Mata & Air', 6.00, 1, 'NGU.jpg', 1, '2025-06-30 02:40:34'),
(14, 'Nasi Goreng Cili Padi', '+ Telur Mata & Air', 6.00, 1, 'NGCP.jpg', 1, '2025-06-30 02:41:05'),
(15, 'Mee Goreng', '+ Telur Mata & Air', 6.00, 1, 'MG.jpg', 1, '2025-06-30 02:41:43'),
(16, 'Bihun Goreng', '+ Telur Mata & Air', 6.00, 1, 'BG.jpg', 1, '2025-06-30 02:46:37'),
(17, 'Keow Teow Goreng', '+ Telur Mata & Air', 6.00, 1, 'KG.jpg', 1, '2025-06-30 02:47:44'),
(18, 'Milo Ais', 'fresh dari truck milo', 2.00, 3, 'milo.jpg', 1, '2025-06-30 02:49:40'),
(19, 'Nescafe Ais', 'tiada hari tanpa kopi', 2.00, 3, 'nescafe.jpg', 1, '2025-06-30 02:53:35'),
(20, 'Horlicks Ais', 'sedap ni 😋', 2.00, 3, 'horlick.jpg', 1, '2025-06-30 02:54:32'),
(21, 'Teh Ais', 'kaw kaw punya 😎', 2.00, 3, 'tehais.jpg', 1, '2025-06-30 02:55:43'),
(22, 'Sirap Bandung Ais', 'betul dari bandung', 2.00, 3, 'bandung.jpg', 1, '2025-06-30 02:58:06'),
(23, 'Green Tea', 'fresh dari cameron 😊', 2.00, 3, 'greentea.jpg', 1, '2025-06-30 02:59:43');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','preparing','on its way','delivered','cancelled') DEFAULT 'pending',
  `delivery_address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `payment_method` varchar(50) NOT NULL DEFAULT 'Cash on Delivery',
  `hide_order` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `delivery_address`, `phone`, `notes`, `created_at`, `updated_at`, `payment_method`, `hide_order`) VALUES
(9, 3, 6.00, 'pending', NULL, NULL, '', '2025-07-09 13:58:04', '2025-07-09 13:58:04', 'Cash on Delivery', 0),
(10, 3, 6.00, 'pending', NULL, NULL, '', '2025-07-09 14:22:12', '2025-07-09 14:22:12', 'Cash on Delivery', 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `price`) VALUES
(16, 9, 14, 1, 6.00),
(17, 10, 16, 1, 6.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `address`, `role`, `created_at`) VALUES
(2, 'izzul', 'izzul@gmail.com', '$2y$10$7nXYTJ2kquQ3q5cCAscP5uOsWeFqJBiNxIxoo4eGyTL.E6Eh.kU/W', 'kasim', '', 'yessir', 'admin', '2025-06-24 15:46:06'),
(3, 'admin', 'admin@jannahkitchen.com', '$2y$10$Y4oXJXR5nqiGxG/X3lm8SeoYCCU02Zwwwa1ZBsZRig8vYKKapepqi', 'admin', '', 'admin', 'admin', '2025-06-24 16:46:00'),
(4, 'amirah', 'aamyramani@gmail.com', '$2y$10$S0151UMZV5.yjv9wJ1H/0u5C.eJqCTJk.4.lrPBOy4rID0FWyoSne', 'amirah', '0102355231', 'uitm', 'customer', '2025-07-01 17:23:56'),
(5, 'kasim', 'kasim@sini.my', '$2y$10$9n2rYocQZzg0Owk4F/EPH.1qNCL0KIdzD4CATus0cbldSJN9Vu0kO', 'Kasim Selamat', '123456789', 'no 19, dekat mana mana, 75121, Texas, Malaysia', 'customer', '2025-07-09 13:37:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
