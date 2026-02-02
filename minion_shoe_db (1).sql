-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 02, 2026 at 10:42 AM
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
-- Database: `minion_shoe_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `addusers`
--

CREATE TABLE `addusers` (
  `id` int(11) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `shoe_size` varchar(10) DEFAULT NULL,
  `total_spent` decimal(10,2) DEFAULT 0.00,
  `status` enum('New','VIP','Regular') DEFAULT 'Regular',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addusers`
--

INSERT INTO `addusers` (`id`, `full_name`, `email`, `shoe_size`, `total_spent`, `status`, `created_at`) VALUES
(1, 'Marcus Jordan', 'marcus@example.com', 'US 10.5', 450.00, 'Regular', '2024-01-14 16:00:00'),
(4, 'Chua yun chao', 'chuayc1110@gaha.cvom', 'UK 8', NULL, NULL, '2026-02-01 06:11:41');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(10) NOT NULL,
  `parent_cat` varchar(100) DEFAULT 'Top Level',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `icon`, `parent_cat`, `description`, `created_at`) VALUES
(1, 'Sneakers', '🏃', 'Men\'s Shoes', 'Casual and athletic sneakers', '2026-01-31 07:09:40'),
(3, 'Nike', '🏀', 'Men\'s Shoes', '', '2026-01-31 08:02:46'),
(8, 'Nike', '🏃', 'Women\'s Shoes', '', '2026-02-01 06:28:17');

-- --------------------------------------------------------

--
-- Table structure for table `catelog`
--

CREATE TABLE `catelog` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category_group` enum('men','women','kids') NOT NULL,
  `image_url` text DEFAULT NULL,
  `colors` varchar(255) DEFAULT NULL,
  `sizes` varchar(255) DEFAULT 'US 7,US 8,US 9,US 10,US 11'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `catelog`
--

INSERT INTO `catelog` (`id`, `name`, `description`, `price`, `category_group`, `image_url`, `colors`, `sizes`) VALUES
(1, 'Nike Air Max 270', 'Maximum comfort with the tallest Air unit yet.', 599.00, 'men', 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=500', 'black,white,red', 'US 7,US 8,US 9,US 10,US 11'),
(2, 'Cole Haan Oxford', 'Classic formal style meets modern cushioning.', 549.00, 'men', 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=500', '#8B4513,black', 'US 7,US 8,US 9,US 10,US 11'),
(3, 'Jimmy Choo Heels', 'Sophisticated stilettos for your most special occasions.', 1299.00, 'women', 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=500', 'black,red,silver', 'US 7,US 8,US 9,US 10,US 11');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Shipped','Cancelled') DEFAULT 'Pending',
  `shipping_address` text NOT NULL,
  `payment_method` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_name`, `customer_email`, `order_date`, `total_amount`, `status`, `shipping_address`, `payment_method`) VALUES
(1, 'Kevin', 'guest@example.com', '2026-02-02 09:41:04', 1148.00, 'Pending', '19, JALAN SETIA JAYA, TAMAN SETIA JAYA', 'Online Banking');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price_at_purchase`) VALUES
(1, 1, 1, 1, 599.00),
(2, 1, 2, 1, 549.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `size` varchar(10) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price_at_purchase` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `sold` int(11) DEFAULT 0,
  `category` varchar(100) DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `image_url` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `price`, `stock`, `sold`, `category`, `sku`, `image_url`, `created_at`) VALUES
(7, 'Nike', 77.00, 77, 0, 'Women\'s Sport', 'SB222', 'https://hips.hearstapps.com/vader-prod.s3.amazonaws.com/1696442487-nike-invincible-3-651da82469fb3.jpg?crop=1.00xw:0.564xh;0,0.196xh&resize=980:*', '2026-02-02 04:30:33');

-- --------------------------------------------------------

--
-- Table structure for table `productsdetail`
--

CREATE TABLE `productsdetail` (
  `id` int(11) NOT NULL,
  `group_name` enum('men','women','kids') NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sizes` varchar(255) DEFAULT NULL,
  `colors` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `productsdetail`
--

INSERT INTO `productsdetail` (`id`, `group_name`, `product_name`, `category`, `price`, `image_url`, `description`, `sizes`, `colors`, `created_at`) VALUES
(1, 'men', 'Nike Air Max 270', 'Men\'s Running', 179.00, 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=600', 'Maximum comfort with the tallest Air unit yet.', '40,41,42,43,44', 'black,red,white', '2026-02-01 07:00:38');

-- --------------------------------------------------------

--
-- Table structure for table `registerusers`
--

CREATE TABLE `registerusers` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `shoe_size` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registerusers`
--

INSERT INTO `registerusers` (`id`, `full_name`, `email`, `password`, `shoe_size`, `created_at`) VALUES
(3, 'Kevin', 'chuayc2006@gmail.com', '$2y$10$SFiMWbtkV3bNQNf/5WOrE.r4vtY05aX.xIZ0/ud673cI18T7LJfva', NULL, '2026-02-01 08:50:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `shoe_size` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addusers`
--
ALTER TABLE `addusers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `catelog`
--
ALTER TABLE `catelog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `productsdetail`
--
ALTER TABLE `productsdetail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registerusers`
--
ALTER TABLE `registerusers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addusers`
--
ALTER TABLE `addusers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `catelog`
--
ALTER TABLE `catelog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `productsdetail`
--
ALTER TABLE `productsdetail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `registerusers`
--
ALTER TABLE `registerusers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
