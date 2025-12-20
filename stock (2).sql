-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 16, 2025 at 02:04 AM
-- Server version: 8.4.3
-- PHP Version: 7.4.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `stock`
--

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE `attributes` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `active` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `attribute_value`
--

CREATE TABLE `attribute_value` (
  `id` int NOT NULL,
  `value` varchar(255) NOT NULL,
  `attribute_parent_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `attribute_value`
--

INSERT INTO `attribute_value` (`id`, `value`, `attribute_parent_id`) VALUES
(5, 'Blue', 2),
(6, 'White', 2),
(7, 'M', 3),
(8, 'L', 3),
(9, 'Green', 2),
(10, 'Black', 2),
(12, 'Grey', 2),
(13, 'S', 3),
(14, 'Red', 1),
(15, 'Blue', 1),
(16, 'Green', 1),
(17, 'S', 2),
(18, 'M', 2),
(19, 'L', 2),
(20, 'Blue', 1),
(21, 'White', 1),
(22, 'Red', 1),
(23, 'Green', 1),
(24, 'S', 2),
(25, 'M', 2),
(26, 'L', 2),
(27, 'Blue', 2),
(28, 'White', 2),
(29, 'M', 3),
(30, 'L', 3),
(31, 'Green', 2),
(32, 'Black', 2),
(33, 'Grey', 2),
(34, 'S', 3),
(35, 'Red', 1),
(36, 'Blue', 1),
(37, 'Green', 1),
(38, 'S', 2),
(39, 'M', 2),
(40, 'L', 2),
(41, 'M', 10),
(42, 'S', 10);

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `active` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `active`) VALUES
(15, 'Channel', 1),
(16, 'Azzaro', 1),
(17, 'Sansilk', 1),
(18, 'Nivea', 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `active` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `active`) VALUES
(15, 'Homme', 1),
(16, 'Femme', 1),
(17, 'Champoo', 1),
(18, 'Gell', 1);

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `id` int NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `service_charge_value` varchar(255) NOT NULL,
  `vat_charge_value` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `currency` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`id`, `company_name`, `service_charge_value`, `vat_charge_value`, `address`, `phone`, `country`, `message`, `currency`) VALUES
(1, 'ABC Inc.', '13', '10', '1234 Main St. Los Angeles, CA 98765 U.S.A.', '(123) 456-7890', 'United States of America', 'Sample message<br>', 'USD'),
(2, 'ABC Inc.', '13', '10', '1234 Main St. Los Angeles, CA 98765 U.S.A.', '(123) 456-7890', 'United States of America', 'Sample message<br>', 'USD'),
(3, 'Tech Solutions Ltd.', '12', '15', '456 Innovation Drive, Silicon Valley, CA', '+1-408-555-0123', 'USA', 'Welcome to Tech Solutions Ltd.', 'USD');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int NOT NULL,
  `customer_code` varchar(50) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_type` enum('super_wholesale','wholesale','retail') NOT NULL DEFAULT 'retail',
  `phone` varchar(255) DEFAULT NULL,
  `address` text,
  `email` varchar(255) DEFAULT NULL,
  `credit_limit` decimal(10,2) DEFAULT '0.00',
  `current_balance` decimal(10,2) DEFAULT '0.00',
  `payment_terms` varchar(100) DEFAULT NULL,
  `tax_number` varchar(100) DEFAULT NULL,
  `notes` text,
  `active` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `customer_code`, `customer_name`, `customer_type`, `phone`, `address`, `email`, `credit_limit`, `current_balance`, `payment_terms`, `tax_number`, `notes`, `active`, `created_at`, `updated_at`) VALUES
(19, 'CUST-0001', 'NAIDJA Zakaria', 'super_wholesale', '0672357753', 'Lotissement N04, Oued Zenati, Guelma, Algeria', 'zakaria.naidja@univ-constantine2.dz', 0.00, 0.00, 'Cash', '', '', 1, '2025-12-16 00:34:41', '2025-12-16 00:39:02'),
(20, 'CUST-0002', 'Chouaib Naidja', 'wholesale', '06723544852', 'Oued zenati', '', 0.00, 11000.00, 'Cash', '', '', 1, '2025-12-16 00:35:08', '2025-12-16 02:03:06'),
(21, 'CUST-0003', 'SAKLOUL SONIA', 'retail', '0672698974', 'Nouvelle ville Constantine', '', 0.00, 0.00, '', '', '', 1, '2025-12-16 00:35:50', '2025-12-16 00:41:43');

-- --------------------------------------------------------

--
-- Table structure for table `customer_transactions`
--

CREATE TABLE `customer_transactions` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `order_id` int DEFAULT NULL,
  `transaction_type` enum('sale','payment','credit','debit','adjustment') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `balance_before` decimal(10,2) NOT NULL,
  `balance_after` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `notes` text,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `permission` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `group_name`, `permission`) VALUES
(1, 'Administrator', 'a:40:{i:0;s:10:\"createUser\";i:1;s:10:\"updateUser\";i:2;s:8:\"viewUser\";i:3;s:10:\"deleteUser\";i:4;s:11:\"createGroup\";i:5;s:11:\"updateGroup\";i:6;s:9:\"viewGroup\";i:7;s:11:\"deleteGroup\";i:8;s:11:\"createBrand\";i:9;s:11:\"updateBrand\";i:10;s:9:\"viewBrand\";i:11;s:11:\"deleteBrand\";i:12;s:14:\"createCategory\";i:13;s:14:\"updateCategory\";i:14;s:12:\"viewCategory\";i:15;s:14:\"deleteCategory\";i:16;s:11:\"createStore\";i:17;s:11:\"updateStore\";i:18;s:9:\"viewStore\";i:19;s:11:\"deleteStore\";i:20;s:15:\"createAttribute\";i:21;s:15:\"updateAttribute\";i:22;s:13:\"viewAttribute\";i:23;s:15:\"deleteAttribute\";i:24;s:13:\"createProduct\";i:25;s:13:\"updateProduct\";i:26;s:11:\"viewProduct\";i:27;s:13:\"deleteProduct\";i:28;s:11:\"createOrder\";i:29;s:11:\"updateOrder\";i:30;s:9:\"viewOrder\";i:31;s:11:\"deleteOrder\";i:32;s:11:\"viewReports\";i:33;s:13:\"updateCompany\";i:34;s:11:\"viewProfile\";i:35;s:13:\"updateSetting\";i:36;s:14:\"createCustomer\";i:37;s:14:\"updateCustomer\";i:38;s:12:\"viewCustomer\";i:39;s:14:\"deleteCustomer\";}');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `customer_id` int DEFAULT NULL,
  `customer_type` enum('super_wholesale','wholesale','retail') DEFAULT 'retail',
  `bill_no` varchar(255) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_address` varchar(255) NOT NULL,
  `customer_phone` varchar(255) NOT NULL,
  `date_time` varchar(255) NOT NULL,
  `gross_amount` varchar(255) NOT NULL,
  `service_charge_rate` varchar(255) NOT NULL,
  `service_charge` varchar(255) NOT NULL,
  `vat_charge_rate` varchar(255) NOT NULL,
  `vat_charge` varchar(255) NOT NULL,
  `net_amount` varchar(255) NOT NULL,
  `paid_amount` decimal(10,2) DEFAULT '0.00',
  `due_amount` decimal(10,2) DEFAULT '0.00',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_notes` text,
  `discount` varchar(255) NOT NULL,
  `paid_status` tinyint(1) DEFAULT '2' COMMENT '1=Paid, 2=Unpaid, 3=Partial',
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `customer_type`, `bill_no`, `customer_name`, `customer_address`, `customer_phone`, `date_time`, `gross_amount`, `service_charge_rate`, `service_charge`, `vat_charge_rate`, `vat_charge`, `net_amount`, `paid_amount`, `due_amount`, `payment_method`, `payment_notes`, `discount`, `paid_status`, `user_id`) VALUES
(27, 20, 'wholesale', 'BILPR-CAA9', 'Chouaib Naidja', 'Oued zenati', '06723544852', '1765850586', '11000.00', '0', '0', '0', '0', '11000.00', 0.00, 0.00, NULL, NULL, '', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders_item`
--

CREATE TABLE `orders_item` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `qty` varchar(255) NOT NULL,
  `rate` varchar(255) NOT NULL,
  `amount` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `orders_item`
--

INSERT INTO `orders_item` (`id`, `order_id`, `product_id`, `qty`, `rate`, `amount`) VALUES
(3, 1, 3, '2', '500', '1000'),
(4, 1, 4, '1', '40', '40'),
(39, 27, 15, '50', '220', '11000.00');

-- --------------------------------------------------------

--
-- Table structure for table `order_payments`
--

CREATE TABLE `order_payments` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `payment_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `notes` text,
  `received_by` int NOT NULL COMMENT 'User ID who received payment'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_date` datetime NOT NULL,
  `payment_method` varchar(100) NOT NULL DEFAULT 'Cash',
  `notes` text,
  `receipt_number` varchar(100) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `price_default` varchar(255) NOT NULL,
  `price_super_wholesale` varchar(255) DEFAULT NULL,
  `price_wholesale` varchar(255) DEFAULT NULL,
  `price_retail` varchar(255) DEFAULT NULL,
  `qty` varchar(255) NOT NULL,
  `image` text NOT NULL,
  `description` text NOT NULL,
  `attribute_value_id` text,
  `brand_id` text NOT NULL,
  `category_id` text NOT NULL,
  `store_id` int NOT NULL,
  `availability` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `sku`, `price_default`, `price_super_wholesale`, `price_wholesale`, `price_retail`, `qty`, `image`, `description`, `attribute_value_id`, `brand_id`, `category_id`, `store_id`, `availability`) VALUES
(11, 'Azzaro Wanted', '1', '2000', '2100', '2200', '2500', '200', '<p>You did not select a file to upload.</p>', '', 'null', '[\"16\"]', '[\"15\"]', 7, 1),
(12, 'Azzaro Home', '2', '1900', '2000', '2100', '2800', '70', '<p>You did not select a file to upload.</p>', '', 'null', '[\"16\"]', '[\"15\"]', 7, 1),
(13, 'Channel Orios', '3', '1200', '1500', '1600', '2000', '49', '<p>You did not select a file to upload.</p>', '', 'null', '[\"15\"]', '[\"16\"]', 7, 1),
(14, 'Channel Jija', '33', '2000', '2100', '2200', '3000', '149', '<p>You did not select a file to upload.</p>', '', 'null', '[\"15\"]', '[\"16\"]', 7, 1),
(15, 'Sansilk Gold', '5', '200', '210', '220', '300', '220', '<p>You did not select a file to upload.</p>', '', 'null', '[\"17\"]', '[\"17\"]', 8, 1),
(16, 'Nivea okko', '9', '100', '150', '160', '500', '190', '<p>You did not select a file to upload.</p>', '', 'null', '[\"18\"]', '[\"18\"]', 8, 1);

-- --------------------------------------------------------

--
-- Table structure for table `stock_history`
--

CREATE TABLE `stock_history` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `order_id` int DEFAULT NULL,
  `movement_type` enum('sale','purchase','adjustment','return') NOT NULL,
  `quantity` int NOT NULL,
  `quantity_before` int NOT NULL,
  `quantity_after` int NOT NULL,
  `user_id` int NOT NULL,
  `date_time` int NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE `stores` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `active` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `stores`
--

INSERT INTO `stores` (`id`, `name`, `active`) VALUES
(7, 'Parfume', 1),
(8, 'Cosmetique', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `gender` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `firstname`, `lastname`, `phone`, `gender`) VALUES
(1, 'admin', '$2y$10$ZrBk2zWOLhPAaOhncDBJv.pKAfhFYywahFQXY4NXDmhOcaRtLdAfS', 'admin@admin.com', 'admin', 'a', '12345678910', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_group`
--

CREATE TABLE `user_group` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `group_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `user_group`
--

INSERT INTO `user_group` (`id`, `user_id`, `group_id`) VALUES
(1, 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attributes`
--
ALTER TABLE `attributes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attribute_value`
--
ALTER TABLE `attribute_value`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_code` (`customer_code`);

--
-- Indexes for table `customer_transactions`
--
ALTER TABLE `customer_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_transaction_date` (`created_at`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customer_id` (`customer_id`),
  ADD KEY `idx_paid_status` (`paid_status`),
  ADD KEY `idx_customer_order` (`customer_id`);

--
-- Indexes for table `orders_item`
--
ALTER TABLE `orders_item`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_payments`
--
ALTER TABLE `order_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_payment_date` (`payment_date`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `receipt_number` (`receipt_number`),
  ADD KEY `fk_order_id` (`order_id`),
  ADD KEY `fk_created_by` (`created_by`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_history`
--
ALTER TABLE `stock_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_group`
--
ALTER TABLE `user_group`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attributes`
--
ALTER TABLE `attributes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `attribute_value`
--
ALTER TABLE `attribute_value`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `customer_transactions`
--
ALTER TABLE `customer_transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `orders_item`
--
ALTER TABLE `orders_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `order_payments`
--
ALTER TABLE `order_payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `stock_history`
--
ALTER TABLE `stock_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stores`
--
ALTER TABLE `stores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_group`
--
ALTER TABLE `user_group`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer_transactions`
--
ALTER TABLE `customer_transactions`
  ADD CONSTRAINT `customer_transactions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_payments`
--
ALTER TABLE `order_payments`
  ADD CONSTRAINT `order_payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stock_history`
--
ALTER TABLE `stock_history`
  ADD CONSTRAINT `stock_history_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
