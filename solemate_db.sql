-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 10, 2025 at 07:57 AM
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
-- Database: `solemate_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `logo_url` varchar(512) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`brand_id`, `name`, `slug`, `logo_url`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Nike', 'nike', 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a6/Logo_NIKE.svg/1200px-Logo_NIKE.svg.png', NULL, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(2, 'Adidas', 'adidas', 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/20/Adidas_Logo.svg/1200px-Adidas_Logo.svg.png', NULL, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(3, 'Timberland', 'timberland', 'https://cdn.worldvectorlogo.com/logos/timberland-2.svg', NULL, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(4, 'Crocs', 'crocs', 'https://cdn.iconscout.com/icon/free/png-256/free-crocs-3421600-2854042.png', NULL, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(5, 'Vans', 'vans', 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/ca/Vans_logo.svg/1024px-Vans_logo.svg.png', NULL, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(512) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `slug`, `description`, `image_url`, `parent_id`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Sneakers', 'sneakers', 'Comfortable and stylish sneakers for everyday wear and sports.', NULL, NULL, 0, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(2, 'Boots', 'boots', 'Durable and fashionable boots for all weather conditions.', NULL, NULL, 0, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(3, 'Sandals', 'sandals', 'Open-toed footwear perfect for warm weather and casual outings.', NULL, NULL, 0, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `order_uid` varchar(36) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `billing_first_name` varchar(100) NOT NULL,
  `billing_last_name` varchar(100) NOT NULL,
  `billing_email` varchar(255) NOT NULL,
  `billing_phone` varchar(20) DEFAULT NULL,
  `billing_address_line1` varchar(255) NOT NULL,
  `billing_address_line2` varchar(255) DEFAULT NULL,
  `billing_city` varchar(100) NOT NULL,
  `billing_zip_code` varchar(20) NOT NULL,
  `billing_country` varchar(100) NOT NULL DEFAULT 'Philippines',
  `shipping_first_name` varchar(100) DEFAULT NULL,
  `shipping_last_name` varchar(100) DEFAULT NULL,
  `shipping_email` varchar(255) DEFAULT NULL,
  `shipping_phone` varchar(20) DEFAULT NULL,
  `shipping_address_line1` varchar(255) DEFAULT NULL,
  `shipping_address_line2` varchar(255) DEFAULT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_zip_code` varchar(20) DEFAULT NULL,
  `shipping_country` varchar(100) DEFAULT NULL,
  `ship_to_billing_address` tinyint(1) NOT NULL DEFAULT 1,
  `order_subtotal` decimal(12,2) NOT NULL,
  `shipping_method_name` varchar(100) DEFAULT NULL,
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `order_total` decimal(12,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_gateway_txn_id` varchar(100) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed','refunded','cancelled') NOT NULL DEFAULT 'pending',
  `order_status` enum('pending_payment','processing','shipped','delivered','completed','cancelled','refunded','on_hold') NOT NULL DEFAULT 'pending_payment',
  `customer_notes` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `paid_at` datetime DEFAULT NULL,
  `shipped_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_sku` varchar(100) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `item_subtotal` decimal(12,2) NOT NULL,
  `item_discount` decimal(10,2) DEFAULT 0.00,
  `item_total` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(280) NOT NULL,
  `description` text DEFAULT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `is_on_sale` tinyint(1) NOT NULL DEFAULT 0,
  `stock_quantity` int(11) DEFAULT 0,
  `manage_stock` tinyint(1) NOT NULL DEFAULT 1,
  `stock_status` enum('in_stock','out_of_stock','on_backorder') NOT NULL DEFAULT 'in_stock',
  `image_url` varchar(512) DEFAULT NULL,
  `hover_image_url` varchar(512) DEFAULT NULL,
  `gallery_images` text DEFAULT NULL,
  `is_new_arrival` tinyint(1) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `average_rating` decimal(3,2) DEFAULT 0.00,
  `rating_count` int(11) DEFAULT 0,
  `weight_kg` decimal(8,3) DEFAULT NULL,
  `dimensions_cm` varchar(100) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `description`, `short_description`, `category_id`, `brand_id`, `sku`, `price`, `sale_price`, `is_on_sale`, `stock_quantity`, `manage_stock`, `stock_status`, `image_url`, `hover_image_url`, `gallery_images`, `is_new_arrival`, `is_featured`, `average_rating`, `rating_count`, `weight_kg`, `dimensions_cm`, `meta_title`, `meta_description`, `meta_keywords`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Nike Air Force 1 \'07', 'nike-air-force-1-07', 'The radiance lives on in the Nike Air Force 1 \'07, the b-ball OG that puts a fresh spin on what you know best: durably stitched overlays, clean finishes and the perfect amount of flash to make you shine.', 'Iconic white sneaker with timeless appeal.', 1, 1, 'CW2288-111', 5495.00, NULL, 0, 25, 1, 'in_stock', 'upload/products/CW2288-111-removebg-preview.png', NULL, NULL, 1, 1, 4.90, 0, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(2, 'Adidas Stan Smith', 'adidas-stan-smith', 'Timeless appeal. Effortless style. Everyday versatility. For over 50 years and counting, adidas Stan Smith Shoes have continued to hold their place as an icon.', 'Classic court style, reinvented for today.', 1, 2, 'FX5502', 4800.00, 4200.00, 1, 18, 1, 'in_stock', 'upload/products/Stan_Smith_Shoes_White_M20324_01_standard-removebg-preview.png', NULL, NULL, 0, 1, 4.80, 0, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(3, 'Timberland 6-Inch Premium Boot', 'timberland-6-inch-boot', 'These waterproof boots are an icon. Crafted in premium, full-grain leather and seam-sealed for complete waterproof protection.', 'The original yellow waterproof boot.', 2, 3, 'TB010061713', 10500.00, NULL, 0, 12, 1, 'in_stock', 'upload/products/17725790_37182215_600-removebg-preview.png', NULL, NULL, 0, 0, 4.90, 0, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(4, 'Crocs Classic Clog - Black', 'crocs-classic-clog-black', 'The irreverent go-to comfort shoe that you’re sure to fall deeper in love with day after day. Crocs Classic Clogs offer lightweight Iconic Crocs Comfort™.', 'Lightweight, comfortable, and versatile clog.', 3, 4, '10001-001', 2750.00, NULL, 0, 30, 1, 'in_stock', 'upload/products/207241-001_7_800x-removebg-preview.png', NULL, NULL, 1, 0, 4.60, 0, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(5, 'Vans Old Skool - Black/White', 'vans-old-skool-black-white', 'The Old Skool, Vans classic skate shoe and first to bare the iconic sidestripe, is a low top lace-up featuring sturdy canvas and suede uppers, re-enforced toecaps to withstand repeated wear, padded collars for support and flexibility, and signature rubber waffle outsoles.', 'The iconic sidestripe skate shoe.', 1, 5, 'VN000D3HY28', 3800.00, NULL, 0, 22, 1, 'in_stock', 'upload/products/VansSkateOldSkool_black_white3-removebg-previewN.png', NULL, NULL, 1, 1, 4.70, 0, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(6, 'Nike Zoom Freak 5', 'nike-zoom-freak-5', 'Designed for the explosive game of Giannis Antetokounmpo, the Zoom Freak 5 offers springy cushioning, secure support, and multidirectional traction.', 'High-performance basketball sneakers made for speed.', 1, 1, 'DX4985-100', 6395.00, 5795.00, 1, 22, 1, 'in_stock', 'upload/products/1_18_afe2373f-07cc-4ab5-9b6c-d63650dbf46c_3024x-removebg-preview.png', NULL, NULL, 1, 1, 4.85, 91, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-02 00:05:00', '2025-06-02 00:05:00'),
(7, 'Adidas Forum Low', 'adidas-forum-low', 'The Adidas Forum Low shoes bring basketball heritage into everyday streetwear with a low-cut profile and retro style.', 'Retro basketball-inspired sneakers with bold flair.', 1, 2, 'FY7756', 4995.00, 4495.00, 1, 20, 1, 'in_stock', 'upload/products/Forum_Low_Shoes_White_FY7756_01_00_standard-removebg-preview.png', NULL, NULL, 1, 0, 4.70, 98, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-02 00:00:00', '2025-06-02 00:00:00'),
(8, 'Vans ComfyCush Era', 'vans-comfycush-era', 'Vans ComfyCush Era combines classic style with upgraded comfort through softer cushioning and a moisture-wicking lining.', 'Classic skater shoe with modern comfort upgrade.', 1, 5, 'VN0A3WM9VNE', 3895.00, NULL, 0, 15, 1, 'in_stock', 'upload/products/17814853_37477564_600-removebg-preview.png', NULL, NULL, 1, 1, 4.60, 47, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-02 00:01:00', '2025-06-02 00:01:00'),
(9, 'Nike Air Max 90', 'nike-air-max-90', 'The Nike Air Max 90 stays true to its OG running roots with the iconic Waffle sole, stitched overlays and classic TPU accents.', 'Timeless runner with visible Max Air cushioning.', 1, 1, 'CN8490-100', 6495.00, NULL, 0, 18, 1, 'in_stock', 'upload/products/15950141_36455204_600-removebg-preview.png', NULL, NULL, 0, 0, 4.80, 122, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-02 00:02:00', '2025-06-02 00:02:00'),
(10, 'Adidas NMD_R1', 'adidas-nmd-r1', 'The Adidas NMD_R1 blends running-inspired design with streetwear style, featuring a snug sock-like fit and Boost cushioning.', 'Urban-style sneakers with responsive Boost comfort.', 1, 2, 'FX4351', 6995.00, 6495.00, 1, 10, 1, 'in_stock', 'upload/products/NMD_R1_Shoes_Black_IE2063_01_standard-removebg-preview.png', NULL, NULL, 1, 1, 4.75, 84, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-02 00:03:00', '2025-06-02 00:03:00'),
(11, 'Nike Canyon Sandal', 'nike-canyon-sandal', 'The Nike Canyon Sandal brings rugged outdoor style and comfort together with a lightweight design and adjustable straps.', 'Outdoor-ready sandals with cushioned comfort.', 1, 3, 'CW9704-300', 4295.00, 3795.00, 1, 25, 1, 'in_stock', 'upload/products/NIKECANYONSANDAL-removebg-preview.png', NULL, NULL, 1, 1, 4.70, 65, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-02 00:05:00', '2025-06-02 00:05:00'),
(12, 'Adidas Adilette Comfort', 'adidas-adilette-comfort', 'Slide into relaxation with the Adidas Adilette Comfort, featuring a contoured footbed and soft cushioning.', 'Classic comfort slides for everyday wear.', 1, 2, 'FZ2863', 2795.00, 2395.00, 1, 30, 1, 'in_stock', 'upload/products/Adilette_Comfort_Slides_Blue_GZ5892_01_standard-removebg-preview.png', NULL, NULL, 1, 0, 4.65, 88, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-02 00:00:00', '2025-06-02 00:00:00'),
(13, 'Vans Trek Slip-On Sandal', 'vans-trek-slip-on-sandal', 'The Vans Trek Slip-On is a water-friendly sandal with a durable design and easy-on construction, perfect for summer days.', 'Durable slip-on sandals for water and sun.', 1, 5, 'VN0A5HF5Y28', 2495.00, NULL, 0, 18, 1, 'in_stock', 'upload/products/s-l1200-removebg-preview.png', NULL, NULL, 1, 1, 4.55, 41, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-02 00:01:00', '2025-06-02 00:01:00'),
(14, 'Nike Victori One Slide', 'nike-victori-one-slide', 'The Nike Victori One Slide offers a versatile design with responsive foam and a contoured grip pattern for all-day wear.', 'Cushioned slides built for comfort and ease.', 1, 3, 'CN9675-003', 3195.00, NULL, 0, 20, 1, 'in_stock', 'upload/products/NKVICTORIONESLIDEPRINT-removebg-preview.png', NULL, NULL, 0, 0, 4.60, 77, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-02 00:02:00', '2025-06-02 00:02:00'),
(15, 'Adidas Comfort Flip-Flops', 'adidas-comfort-flip-flops', 'The Adidas Comfort Flip-Flops combine laid-back style with soft cushioning and a flexible footbed for everyday use.', 'Casual flip-flops with plush foot support.', 1, 2, 'GZ5893', 1895.00, 1595.00, 1, 35, 1, 'in_stock', 'upload/products/Comfort_Flip-Flops_Black_EG2069_01_standard-removebg-preview.png', NULL, NULL, 1, 1, 4.50, 53, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-02 00:03:00', '2025-06-02 00:03:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verification_token` varchar(100) DEFAULT NULL,
  `password_reset_token` varchar(100) DEFAULT NULL,
  `password_reset_expires` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `first_name`, `last_name`, `phone`, `is_admin`, `is_verified`, `verification_token`, `password_reset_token`, `password_reset_expires`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'juandelacruz@hotmail.com', '$2y$10$iBm06jlCLH.tAprzA1g3zO3RWXTQaFR696sPbm4MG6PUwQ7Peixe2', 'Juan', 'Dela Cruz', NULL, 0, 0, NULL, NULL, NULL, '2025-06-06 14:58:40', '2025-06-02 15:14:24', '2025-06-06 06:58:40');

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address_type` enum('shipping','billing') NOT NULL DEFAULT 'shipping',
  `is_default_shipping` tinyint(1) NOT NULL DEFAULT 0,
  `is_default_billing` tinyint(1) NOT NULL DEFAULT 0,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `zip_code` varchar(20) NOT NULL,
  `country` varchar(100) NOT NULL DEFAULT 'Philippines',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`brand_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug_brands` (`slug`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug_categories` (`slug`),
  ADD KEY `idx_parent_id_categories` (`parent_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `order_uid` (`order_uid`),
  ADD KEY `idx_order_uid_orders` (`order_uid`),
  ADD KEY `idx_user_id_orders` (`user_id`),
  ADD KEY `idx_billing_email_orders` (`billing_email`),
  ADD KEY `idx_payment_status_orders` (`payment_status`),
  ADD KEY `idx_order_status_orders` (`order_status`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_order_id_order_items` (`order_id`),
  ADD KEY `idx_product_id_order_items` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `idx_slug_products` (`slug`),
  ADD KEY `idx_category_id_products` (`category_id`),
  ADD KEY `idx_brand_id_products` (`brand_id`),
  ADD KEY `idx_is_new_arrival_products` (`is_new_arrival`),
  ADD KEY `idx_is_featured_products` (`is_featured`),
  ADD KEY `idx_is_active_products` (`is_active`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email_users` (`email`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `idx_user_id_addresses` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_category_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_items_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_product_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `fk_address_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
