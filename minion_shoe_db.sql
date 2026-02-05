-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 05, 2026 at 07:07 AM
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
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `admin_id` varchar(50) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `admin_name` varchar(100) DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `admin_id`, `password`, `admin_name`, `reset_token`, `token_expiry`) VALUES
(1, '242DT2423Z', '$2y$10$ZV1lEAf7VzszeFv7yATWreHrpNHriPpJhCM7vYKYV/gRaHD0SMgXm', 'Captain Gru', NULL, NULL),
(2, '242DT242BZ', '$2y$10$SFiMWbtkV3bNQNf/5WOrE.r4vtY05aX.xIZ0/ud673cI18T7LJfva', 'Staff Kevin', NULL, NULL),
(3, '242DT24334', '$2y$10$SFiMWbtkV3bNQNf/5WOrE.r4vtY05aX.xIZ0/ud673cI18T7LJfva', 'Staff Bob', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `allproducts`
--

CREATE TABLE `allproducts` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image_url` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sizes` varchar(255) DEFAULT NULL,
  `colors` varchar(255) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `sold` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `allproducts`
--

INSERT INTO `allproducts` (`id`, `product_name`, `category`, `price`, `image_url`, `description`, `sizes`, `colors`, `sku`, `stock`, `sold`, `created_at`) VALUES
(1, 'Nike Air Max 270', 'men', 150.00, 'https://i5.walmartimages.com/seo/Nike-Air-Max-270-Men-s-Running-Shoes-White-Black-White-AH8050-100_279e90de-804a-4290-93f6-8cdd6fee0c64.1854df18b8f5442413dc093629f67edf.png', 'Maximum comfort with the tallest Air unit yet.', '40,41,42,43,44', 'black,red,white', 'NK-AM270-M', 50, 12, '2026-02-03 02:00:00'),
(2, 'Adidas Ultraboost 22', 'men', 190.00, 'https://assets.adidas.com/images/w_600,f_auto,q_auto/4b593057a18c47d2844dad9000ecd808_9366/Ultraboost_22_Shoes_Black_GX3062_01_standard.jpg', 'Responsive running shoes with energy-returning Boost.', '40,41,42,43,44,45', 'black,white,blue', 'AD-UB22-M', 45, 8, '2026-02-03 02:05:00'),
(3, 'Timberland 6-Inch Premium', 'men', 198.00, 'https://www.decafjournal.com/content/images/size/w1200/2024/10/IMG_0336.jpg', 'The original waterproof boot that started it all.', '40,41,42,43,44', 'wheat,black,brown', 'TB-6IN-M', 30, 5, '2026-02-03 02:10:00'),
(4, 'Vans Old Skool', 'men', 70.00, 'https://thefactorykl.com/cdn/shop/products/GettyImages_621173942.0.jpg?v=1657079160&width=1445', 'Classic skate shoe and the first to bare the iconic side stripe.', '39,40,41,42,43,44', 'black,white,navy', 'VN-OS-M', 100, 25, '2026-02-03 02:15:00'),
(5, 'New Balance 574', 'men', 85.00, 'https://en.afew-store.com/cdn/shop/collections/new-balance-574_b8f3a43b-66bd-4cbf-aaac-413b6735c35b.jpg?v=1702564008&width=1200', 'The most New Balance shoe ever, versatile and timeless.', '41,42,43,44,45', 'grey,navy,burgundy', 'NB-574-M', 60, 15, '2026-02-03 02:20:00'),
(6, 'Cole Haan Oxford', 'men', 140.00, 'https://www.shoesforcrews.com/shared-imgs/productimages/US_en/42150/0000-1024.webp', 'Classic formal style meets modern cushioning.', '40,41,42,43', 'black,british tan', 'CH-OX-M', 20, 2, '2026-02-03 02:25:00'),
(7, 'Puma Suede Classic', 'men', 75.00, 'https://images.puma.com/image/upload/f_auto,q_auto,b_rgb:fafafa,w_500,h_500/global/399781/07/fnd/SEA/fmt/png/Suede-Classic-Sneakers-Unisex', 'The 1968 legend, still turning heads today.', '40,41,42,44', 'black,white', 'PM-SD-M', 40, 10, '2026-02-03 02:30:00'),
(8, 'Jimmy Choo Heels', 'women', 650.00, 'https://media.jimmychoo.com/image/upload/q_auto:best,f_auto,dpr_2.0/ROWPROD_PRODUCT/images/original/ALIASXU_001721_SIDE_vg507.jpg', 'Sophisticated stilettos for your most special occasions.', '36,37,38,39', 'black,red,silver', 'JC-HL-W', 15, 3, '2026-02-03 02:35:00'),
(9, 'Steve Madden Pump', 'women', 99.00, 'https://vcdn.valiram.com/wp-content/uploads/2020/07/Steve-Madden-VALA-Red-Patent-450x450.jpg', 'The perfect pointed-toe pump for the office or evening.', '35,36,37,38,39', 'nude,black,leopard', 'SM-PM-W', 40, 14, '2026-02-03 02:40:00'),
(10, 'Nike Air Force 1 Shadow', 'women', 110.00, 'https://www.jdsports.my/cdn/shop/files/jd_DZ1847-113_a.jpg?v=1751558300&width=500', 'A playful twist on a classic hoops design.', '36,37,38,39,40', 'pastel,white,pink', 'NK-AF1-W', 55, 20, '2026-02-03 02:45:00'),
(11, 'Adidas Stan Smith', 'women', 95.00, 'https://assets.adidas.com/images/w_600,f_auto,q_auto/68ae7ea7849b43eca70aac1e00f5146d_faec/Stan_Smith_Shoes_White_FX5502_db01_standard.jpg', 'Timeless look with a sustainable makeover.', '36,37,38,39', 'white/green,white/navy', 'AD-SS-W', 65, 30, '2026-02-03 02:50:00'),
(12, 'Converse Chuck Taylor High', 'women', 65.00, 'https://crossoverconceptstore.com/cdn/shop/files/ConverseChuck70Black_WhiteHiNaturalIvory2_1800x1800.jpg?v=1756206661', 'The quintessential sneaker for every wardrobe.', '35,36,37,38,39', 'black,white,red', 'CV-CT-W', 80, 45, '2026-02-03 02:55:00'),
(13, 'Dr. Martens 1460', 'women', 170.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTfMUhQWz1JxOjS4j5Z47a62cC7NQQ3kgqGUA&s', 'The iconic 8-eye boot with yellow stitching.', '36,37,38,39', 'black,cherry red', 'DM-1460-W', 25, 6, '2026-02-03 03:00:00'),
(14, 'Nike Revolution 6', 'kids', 55.00, 'https://static.nike.com/a/images/t_web_pdp_936_v2/f_auto/f16e6c1f-28b9-4da2-a993-87c31b09f9c7/NIKE+REVOLUTION+6+NN+4E.png', 'Lightweight cushioning for all-day play.', '28,30,32,34', 'blue,pink,black', 'NK-REV-K', 90, 40, '2026-02-03 03:05:00'),
(15, 'Adidas Superstar J', 'kids', 70.00, 'https://assets.adidas.com/images/w_600,f_auto,q_auto/b324422839d4414aa851c7266e3630de_faec/Superstar_II_Shoes_White_JI0080_db01_00_standard.tiff.jpg', 'The famous shell-toe for the next generation.', '30,32,33,35', 'white/black', 'AD-SUP-K', 50, 18, '2026-02-03 03:10:00'),
(16, 'Jordan 1 Retro High', 'kids', 120.00, 'https://atmos-kl.com/cdn/shop/files/fd2596-021-01.jpg?v=1765527372', 'Miniature legend for future ballers.', '31,33,35', 'chicago red,black', 'JD-1RT-K', 20, 12, '2026-02-03 03:15:00'),
(17, 'Crocs Classic Clog', 'kids', 35.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTegJmcdrEBZqbi5ssTAFmjCy-yEHU5tmK_oA&s', 'Easy to clean, easy to wear, kid-approved.', '25,27,29,31', 'yellow,blue,lime', 'CR-CL-K', 120, 60, '2026-02-03 03:20:00'),
(18, 'Skechers Light-Ups', 'kids', 45.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR9lMdN14PJtx5pgw9ikOzMQCLzQekG4_zQrA&s', 'Sparkly shoes that light up with every step.', '26,28,30', 'silver/multi', 'SK-LU-K', 45, 22, '2026-02-03 03:25:00'),
(19, 'Vans Slip-On Checkerboard', 'kids', 40.00, 'https://cdn-images.farfetch-contents.com/14/13/35/30/14133530_18797293_600.jpg', 'The classic slip-on for easy on and off.', '28,30,32,34', 'black/white', 'VN-SO-K', 70, 35, '2026-02-03 03:30:00'),
(20, 'Asics Kids Contend', 'kids', 48.00, 'https://images.asics.com/is/image/asics/1014A338_010_SL_LT_GLB?$sfcc-product$', 'Durable running shoes for active children.', '30,32,34', 'navy/green', 'AS-CN-K', 35, 9, '2026-02-03 03:35:00');

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
(3, 'Nike', '🏀', 'Men\'s Shoes', '', '2026-01-31 08:02:46'),
(8, 'Nike', '🏃', 'Women\'s Shoes', '', '2026-02-01 06:28:17');

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
(1, 'Kevin', 'guest@example.com', '2026-02-02 09:41:04', 1148.00, 'Cancelled', '19, JALAN SETIA JAYA, TAMAN SETIA JAYA', 'Online Banking'),
(2, 'Kevin', 'guest@example.com', '2026-02-03 07:17:16', 3950.00, 'Pending', '19, JALAN SETIA JAYA, TAMAN SETIA JAYA', 'Online Banking'),
(3, 'Kevin', 'chuayc2006@gmail.com', '2026-02-04 16:45:11', 152.80, 'Shipped', '19, JALAN SETIA JAYA, TAMAN SETIA JAYA', 'Online Banking'),
(4, 'Chua', 'chuayc2006@gmail.com', '2026-02-04 19:22:59', 216.40, 'Pending', '19, JALAN SETIA JAYA, TAMAN SETIA JAYA, JOHOR, BATU PAHAT, 83000', 'Credit Card'),
(6, 'Chua', 'chuayc2006@gmail.com', '2026-02-04 22:21:29', 57.40, 'Shipped', '19, JALAN SETIA JAYA, TAMAN SETIA JAYA, JOHOR, BATU PAHAT, 83000', 'Online Banking');

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
(2, 1, 2, 1, 549.00),
(3, 2, 2, 1, 549.00),
(4, 2, 1, 1, 179.00),
(5, 2, 1, 1, 179.00),
(6, 2, 1, 1, 179.00),
(7, 2, 1, 1, 179.00),
(8, 2, 1, 1, 179.00),
(9, 2, 1, 1, 179.00),
(10, 2, 1, 1, 179.00),
(11, 2, 1, 1, 179.00),
(12, 2, 1, 1, 179.00),
(13, 2, 1, 1, 179.00),
(14, 2, 1, 1, 179.00),
(15, 2, 1, 1, 179.00),
(16, 2, 1, 1, 179.00),
(17, 2, 1, 1, 179.00),
(18, 2, 1, 1, 179.00),
(19, 2, 1, 1, 179.00),
(20, 2, 1, 1, 179.00),
(21, 2, 1, 1, 179.00),
(22, 2, 1, 1, 179.00),
(23, 3, 19, 1, 40.00),
(24, 3, 18, 2, 45.00);

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(64) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `account_status` varchar(20) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registerusers`
--

INSERT INTO `registerusers` (`id`, `full_name`, `email`, `password`, `shoe_size`, `created_at`, `reset_token`, `token_expiry`, `account_status`) VALUES
(3, 'Chua', 'chuayc2006@gmail.com', '$2y$10$mbfPmxu..TLJteF1B6oUie7TMu2oRNnlx2pHqf21CfEyY40bZc28S', NULL, '2026-02-01 08:50:08', NULL, NULL, 'Active'),
(4, 'Kevin', 'bonddgg@gmail.com', '$2y$10$mbfPmxu..TLJteF1B6oUie7TMu2oRNnlx2pHqf21CfEyY40bZc28S', NULL, '2026-02-05 04:57:57', NULL, NULL, 'Inactive');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_id` (`admin_id`);

--
-- Indexes for table `allproducts`
--
ALTER TABLE `allproducts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
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
-- Indexes for table `registerusers`
--
ALTER TABLE `registerusers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `allproducts`
--
ALTER TABLE `allproducts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `registerusers`
--
ALTER TABLE `registerusers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
