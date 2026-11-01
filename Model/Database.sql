-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 01, 2026 at 09:53 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nexora`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password_hash`) VALUES
(1, 'NEXORA Admin', 'admin@nexora.example.test', '$2y$12$NEmpq4qRLxHrj3Q.m/vaXuvVGLLKPgftWO1F0VbNgLIy9kSinv8QS');

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `customer_id`) VALUES
(5, 4),
(4, 5),
(2, 7),
(1, 8),
(3, 9),
(7, 11),
(8, 14),
(6, 15);

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cart_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `stock_reserved_quantity` int(10) UNSIGNED NOT NULL DEFAULT 0
) ;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `quantity`, `stock_reserved_quantity`) VALUES
(1, 1, 40, 2, 0),
(2, 2, 75, 2, 0),
(3, 3, 17, 2, 0),
(4, 3, 12, 2, 0),
(5, 3, 38, 1, 0),
(6, 4, 17, 2, 0),
(7, 5, 65, 2, 0),
(8, 5, 35, 2, 0),
(9, 5, 22, 2, 0),
(10, 6, 44, 1, 0),
(11, 6, 15, 1, 0),
(12, 6, 60, 1, 0),
(13, 7, 93, 2, 0),
(14, 7, 87, 1, 0),
(15, 7, 51, 2, 0),
(16, 8, 34, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `image`, `created_at`) VALUES
(1, 'Processors', 'https://bcdn.askleo.com/wp-content/uploads/2009/01/cpu-600x490.jpg.webp', '2026-10-29 10:18:39'),
(2, 'Graphics Cards', 'https://image.citycenter.jo/cachewebp/catalog/002026/92026/pr2592026-1200x1200.webp', '2026-10-29 10:18:39'),
(3, 'Motherboards', 'https://cdn.britannica.com/64/170564-004-4A0ABE7C/motherboard-for-computer.jpg?s=1500x700&q=85', '2026-10-29 10:18:39'),
(4, 'Memory (RAM)', 'https://i.ebayimg.com/thumbs/images/g/XbkAAOxyfpJSVLrF/s-l500.jpg', '2026-10-29 10:18:39'),
(5, 'Storage', 'https://img-1.kwcdn.com/product/fancy/4189ceb0-ad81-46c0-835c-a09d0b0c69d8.jpg?imageView2/2/w/800/q/70/format/avif', '2026-10-29 10:18:39'),
(6, 'Power Supplies', 'https://m.media-amazon.com/images/I/710LKWKvtAL._AC_SX679_.jpg', '2026-10-29 10:18:39'),
(7, 'PC Cases', 'https://image.citycenter.jo/cachewebp/catalog/002026/22026/CO2822026-1200x1200.webp', '2026-10-29 10:18:39'),
(8, 'Monitors', 'https://image.citycenter.jo/cachewebp/catalog/002026/102026/MM9102026-1200x1200.webp', '2026-10-29 10:18:39'),
(9, 'Peripherals', 'https://mbmnewtech.com/wp-content/uploads/2025/08/hardware-peripherals-1.jpg', '2026-10-29 10:18:39'),
(10, 'Cooling', 'https://image.citycenter.jo/cachewebp/catalog/002026/12026/g1312026-1200x1200.webp', '2026-10-29 10:18:39');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT 0.00,
  `valid_until` date NOT NULL,
  `usage_limit` int(10) UNSIGNED DEFAULT 100,
  `used_count` int(10) UNSIGNED DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `discount_type`, `discount_value`, `min_order_amount`, `valid_until`, `usage_limit`, `used_count`, `is_active`, `created_at`) VALUES
(1, 'WELCOME10', 'percentage', 10.00, 50.00, '2026-12-31', 200, 14, 1, '2026-10-31 11:56:01'),
(2, 'SAVE20', 'percentage', 20.00, 150.00, '2026-12-15', 100, 16, 1, '2026-10-31 11:56:01'),
(3, 'FREESHIP', 'fixed', 15.00, 0.00, '2026-11-30', 500, 50, 1, '2026-10-31 11:56:01'),
(4, 'NEXORA50', 'fixed', 50.00, 300.00, '2027-01-31', 50, 10, 1, '2026-10-31 11:56:01');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `google_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `first_name`, `last_name`, `email`, `password_hash`, `phone`, `created_at`, `is_active`, `google_id`) VALUES
(1, 'Omar', 'Khalil', 'toom81138@gmail.com', '$2y$12$NEmpq4qRLxHrj3Q.m/vaXuvVGLLKPgftWO1F0VbNgLIy9kSinv8QS', '0796623525', '2026-10-03 21:48:00', 1, NULL),
(2, 'Yousef', 'Ammar', 'yousef.ammar2@example.test', '$2y$12$NEmpq4qRLxHrj3Q.m/vaXuvVGLLKPgftWO1F0VbNgLIy9kSinv8QS', '0794940441', '2026-10-22 12:14:00', 1, NULL),
(3, 'Ahmad', 'Saleh', 'ahmad.saleh3@example.test', '$2y$12$NEmpq4qRLxHrj3Q.m/vaXuvVGLLKPgftWO1F0VbNgLIy9kSinv8QS', '0794340845', '2026-10-05 08:02:00', 1, NULL),
(4, 'Lina', 'Hamdan', 'lina.hamdan4@example.test', '$2y$12$NEmpq4qRLxHrj3Q.m/vaXuvVGLLKPgftWO1F0VbNgLIy9kSinv8QS', '0795107776', '2026-10-16 17:54:00', 1, NULL),
(5, 'Sara', 'Qasem', 'sara.qasem5@example.test', '$2y$12$NEmpq4qRLxHrj3Q.m/vaXuvVGLLKPgftWO1F0VbNgLIy9kSinv8QS', '0792221854', '2026-10-15 14:56:00', 1, NULL),
(6, 'Mohammad', 'Yaseen', 'mohammad.yaseen6@example.test', '$2y$12$NEmpq4qRLxHrj3Q.m/vaXuvVGLLKPgftWO1F0VbNgLIy9kSinv8QS', '0794262081', '2026-10-23 19:24:00', 1, NULL),
(7, 'Laith', 'Btoush', 'laith.btoush7@example.test', '$2y$12$NEmpq4qRLxHrj3Q.m/vaXuvVGLLKPgftWO1F0VbNgLIy9kSinv8QS', '0799294272', '2026-10-13 11:09:00', 1, NULL),
(8, 'Rana', 'Mansour', 'rana.mansour8@example.test', '$2y$12$NEmpq4qRLxHrj3Q.m/vaXuvVGLLKPgftWO1F0VbNgLIy9kSinv8QS', '0791093026', '2026-10-29 20:55:00', 1, NULL),
(9, 'Hamza', 'Qudah', 'hamza.qudah9@example.test', '$2y$12$NEmpq4qRLxHrj3Q.m/vaXuvVGLLKPgftWO1F0VbNgLIy9kSinv8QS', '0792788238', '2026-10-25 14:14:00', 1, NULL),
(10, 'Dana', 'Nawasrah', 'dana.nawasrah10@example.test', '$2y$12$NEmpq4qRLxHrj3Q.m/vaXuvVGLLKPgftWO1F0VbNgLIy9kSinv8QS', '0793950888', '2026-10-26 19:33:00', 1, NULL),
(11, 'Khaled', 'Obaid', 'khaled.obaid11@example.test', '$2y$12$NEmpq4qRLxHrj3Q.m/vaXuvVGLLKPgftWO1F0VbNgLIy9kSinv8QS', '0798793853', '2026-10-02 16:15:00', 1, NULL),
(12, 'Tala', 'Zoubi', 'tala.zoubi12@example.test', '$2y$12$NEmpq4qRLxHrj3Q.m/vaXuvVGLLKPgftWO1F0VbNgLIy9kSinv8QS', '0793036059', '2026-10-15 10:51:00', 1, NULL),
(13, 'Zaid', 'Kanaan', 'zaid.kanaan13@example.test', '$2y$12$NEmpq4qRLxHrj3Q.m/vaXuvVGLLKPgftWO1F0VbNgLIy9kSinv8QS', '0798795419', '2026-10-22 16:35:00', 1, NULL),
(14, 'Nour', 'Hijazi', 'nour.hijazi14@example.test', '$2y$12$NEmpq4qRLxHrj3Q.m/vaXuvVGLLKPgftWO1F0VbNgLIy9kSinv8QS', '0796323376', '2026-10-25 22:28:00', 1, NULL),
(15, 'Mahmoud', 'Shawabkeh', 'mahmoud.shawabkeh15@example.test', '$2y$12$NEmpq4qRLxHrj3Q.m/vaXuvVGLLKPgftWO1F0VbNgLIy9kSinv8QS', '0799468772', '2026-10-14 21:58:00', 1, NULL),
(16, 'Hiba', 'Freihat', 'hiba.freihat16@example.test', '$2y$12$NEmpq4qRLxHrj3Q.m/vaXuvVGLLKPgftWO1F0VbNgLIy9kSinv8QS', '0798481188', '2026-10-29 10:47:00', 1, NULL),
(17, 'Ali', 'Momani', 'ali.momani17@example.test', '$2y$12$NEmpq4qRLxHrj3Q.m/vaXuvVGLLKPgftWO1F0VbNgLIy9kSinv8QS', '0798963715', '2026-10-15 12:48:00', 1, NULL),
(18, 'Mariam', 'Dabbas', 'mariam.dabbas18@example.test', '$2y$12$NEmpq4qRLxHrj3Q.m/vaXuvVGLLKPgftWO1F0VbNgLIy9kSinv8QS', '0795147994', '2026-10-27 18:17:00', 1, NULL),
(19, 'Faris', 'Rousan', 'faris.rousan19@example.test', '$2y$12$NEmpq4qRLxHrj3Q.m/vaXuvVGLLKPgftWO1F0VbNgLIy9kSinv8QS', '0799745959', '2026-10-16 18:15:00', 1, NULL),
(20, 'Rawan', 'Tarawneh', 'rawan.tarawneh20@example.test', '$2y$12$NEmpq4qRLxHrj3Q.m/vaXuvVGLLKPgftWO1F0VbNgLIy9kSinv8QS', '0795606980', '2026-10-15 09:45:00', 1, NULL),
(21, 'omar', 'altoom', 'omaraltoom899@gmail.com', NULL, '0790022380', '2026-11-01 09:10:11', 1, '114951435559146096984');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `message`, `is_read`, `created_at`) VALUES
(1, 'New customer review received.', 1, '2026-10-29 08:22:00'),
(2, 'Payment received for order.', 1, '2026-10-24 15:28:00'),
(3, 'New order placed successfully.', 1, '2026-10-25 21:18:00'),
(4, 'New order placed successfully.', 1, '2026-10-29 17:32:00'),
(5, 'Order status updated to shipped.', 1, '2026-10-29 16:50:00'),
(6, 'New order placed successfully.', 1, '2026-10-29 18:12:00'),
(7, 'New customer review received.', 1, '2026-10-28 10:03:00'),
(8, 'Order status updated to shipped.', 1, '2026-10-25 19:05:00'),
(9, 'Payment received for order.', 1, '2026-10-20 11:45:00'),
(10, 'Order status updated to shipped.', 1, '2026-10-28 16:39:00'),
(11, 'Low stock alert for a product.', 1, '2026-10-25 22:18:00'),
(12, 'Order status updated to shipped.', 1, '2026-10-25 18:38:00'),
(13, 'New order placed successfully.', 1, '2026-10-21 13:06:00'),
(14, 'Payment received for order.', 1, '2026-10-24 12:46:00'),
(15, 'Payment received for order.', 1, '2026-10-25 09:37:00'),
(16, 'New order #NX-41 placed by customer #1.', 1, '2026-11-01 06:12:44'),
(17, 'New order #NX-42 placed by customer #1.', 1, '2026-11-01 06:15:07'),
(18, 'New order #NX-43 placed by customer #21.', 1, '2026-11-01 08:26:09'),
(19, 'New order #NX-44 placed by customer #21.', 1, '2026-11-01 09:02:48');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `shipping_address` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `order_date`, `total_amount`, `status`, `shipping_address`, `created_at`) VALUES
(1, 1, '2026-09-05 10:14:00', 255.00, 'delivered', 'Zarqa, Street 91, Bldg 28', '2026-09-05 10:14:00'),
(2, 1, '2026-10-25 17:44:00', 764.00, 'delivered', 'Jerash, Street 74, Bldg 49', '2026-10-25 17:44:00'),
(3, 2, '2026-09-16 11:17:00', 858.00, 'delivered', 'Amman, Street 50, Bldg 44', '2026-09-16 11:17:00'),
(4, 3, '2026-09-13 17:36:00', 1224.00, 'delivered', 'Amman, Street 83, Bldg 55', '2026-09-13 17:36:00'),
(5, 3, '2026-10-25 22:24:00', 2523.00, 'delivered', 'Jerash, Street 54, Bldg 33', '2026-10-25 22:24:00'),
(6, 3, '2026-09-25 08:15:00', 1923.00, 'confirmed', 'Amman, Street 80, Bldg 20', '2026-09-25 08:15:00'),
(7, 4, '2026-09-20 17:47:00', 4641.00, 'delivered', 'Jerash, Street 21, Bldg 40', '2026-09-20 17:47:00'),
(8, 5, '2026-10-13 19:12:00', 297.00, 'delivered', 'Madaba, Street 81, Bldg 32', '2026-10-13 19:12:00'),
(9, 5, '2026-10-22 13:04:00', 2224.00, 'delivered', 'Irbid, Street 2, Bldg 109', '2026-10-22 13:04:00'),
(10, 5, '2026-09-24 16:41:00', 1172.00, 'delivered', 'Jerash, Street 69, Bldg 100', '2026-09-24 16:41:00'),
(11, 6, '2026-09-25 15:36:00', 614.00, 'delivered', 'Salt - Balqa, Street 44, Bldg 4', '2026-09-25 15:36:00'),
(12, 6, '2026-10-20 19:56:00', 1364.00, 'delivered', 'Amman, Street 67, Bldg 25', '2026-10-20 19:56:00'),
(13, 7, '2026-10-15 20:01:00', 1944.00, 'delivered', 'Zarqa, Street 52, Bldg 89', '2026-10-15 20:01:00'),
(14, 8, '2026-10-12 19:29:00', 2063.00, 'delivered', 'Irbid, Street 30, Bldg 16', '2026-10-12 19:29:00'),
(15, 8, '2026-09-24 15:17:00', 1825.00, 'delivered', 'Jerash, Street 68, Bldg 77', '2026-09-24 15:17:00'),
(16, 9, '2026-10-01 19:34:00', 557.00, 'confirmed', 'Amman, Street 7, Bldg 71', '2026-10-01 19:34:00'),
(17, 10, '2026-09-19 12:30:00', 257.00, 'delivered', 'Irbid, Street 24, Bldg 7', '2026-09-19 12:30:00'),
(18, 10, '2026-09-05 10:51:00', 2035.00, 'delivered', 'Irbid, Street 11, Bldg 32', '2026-09-05 10:51:00'),
(19, 10, '2026-10-28 17:27:00', 2791.00, 'delivered', 'Aqaba, Street 8, Bldg 79', '2026-10-28 17:27:00'),
(20, 11, '2026-09-06 16:04:00', 348.00, 'confirmed', 'Salt - Balqa, Street 58, Bldg 89', '2026-09-06 16:04:00'),
(21, 12, '2026-10-23 21:29:00', 518.00, 'delivered', 'Zarqa, Street 34, Bldg 101', '2026-10-23 21:29:00'),
(22, 12, '2026-09-06 20:19:00', 1605.00, 'delivered', 'Jerash, Street 73, Bldg 118', '2026-09-06 20:19:00'),
(23, 13, '2026-10-23 14:17:00', 417.00, 'delivered', 'Salt - Balqa, Street 57, Bldg 11', '2026-10-23 14:17:00'),
(24, 13, '2026-09-25 18:52:00', 659.00, 'delivered', 'Amman, Street 98, Bldg 97', '2026-09-25 18:52:00'),
(25, 14, '2026-10-14 13:20:00', 1542.00, 'delivered', 'Jerash, Street 21, Bldg 43', '2026-10-14 13:20:00'),
(26, 14, '2026-10-09 13:07:00', 673.00, 'cancelled', 'Salt - Balqa, Street 66, Bldg 106', '2026-10-09 13:07:00'),
(27, 15, '2026-10-25 08:13:00', 2880.00, 'delivered', 'Zarqa, Street 37, Bldg 57', '2026-10-25 08:13:00'),
(28, 15, '2026-09-23 10:19:00', 1767.00, 'delivered', 'Aqaba, Street 53, Bldg 12', '2026-09-23 10:19:00'),
(29, 15, '2026-09-16 22:45:00', 139.00, 'delivered', 'Madaba, Street 35, Bldg 54', '2026-09-16 22:45:00'),
(30, 16, '2026-09-20 16:47:00', 1457.00, 'delivered', 'Jerash, Street 9, Bldg 36', '2026-09-20 16:47:00'),
(31, 16, '2026-10-28 10:28:00', 1513.00, 'delivered', 'Irbid, Street 43, Bldg 71', '2026-10-28 10:28:00'),
(32, 17, '2026-10-02 13:47:00', 764.00, 'delivered', 'Jerash, Street 49, Bldg 50', '2026-10-02 13:47:00'),
(33, 17, '2026-10-01 19:09:00', 1554.00, 'delivered', 'Madaba, Street 20, Bldg 10', '2026-10-01 19:09:00'),
(34, 18, '2026-10-11 18:45:00', 1724.00, 'delivered', 'Salt - Balqa, Street 70, Bldg 5', '2026-10-11 18:45:00'),
(35, 18, '2026-09-14 09:48:00', 597.00, 'delivered', 'Jerash, Street 13, Bldg 57', '2026-09-14 09:48:00'),
(36, 19, '2026-10-14 10:15:00', 1405.00, 'delivered', 'Aqaba, Street 88, Bldg 102', '2026-10-14 10:15:00'),
(37, 19, '2026-09-16 22:37:00', 3735.00, 'confirmed', 'Salt - Balqa, Street 82, Bldg 33', '2026-09-16 22:37:00'),
(38, 19, '2026-09-03 15:22:00', 2102.00, 'cancelled', 'Irbid, Street 82, Bldg 55', '2026-09-03 15:22:00'),
(39, 20, '2026-10-19 12:44:00', 746.00, 'delivered', 'Jerash, Street 85, Bldg 51', '2026-10-19 12:44:00'),
(40, 20, '2026-09-20 19:31:00', 1797.00, 'delivered', 'Irbid, Street 30, Bldg 78', '2026-09-20 19:31:00'),
(41, 1, '2026-11-01 09:12:44', 90.20, 'delivered', 'Zahran Street, منطقة زهران, Amman, Amman Sub-District, Amman Qasabah District, Amman, 11183, Jordan', '2026-11-01 09:12:44'),
(42, 1, '2026-11-01 09:15:07', 116.45, 'delivered', 'Zahran Street, منطقة زهران, Amman, Amman Sub-District, Amman Qasabah District, Amman, 11183, Jordan', '2026-11-01 09:15:07'),
(43, 21, '2026-11-01 11:26:09', 46.10, 'delivered', 'Zahran Street, منطقة زهران, Amman, Amman Sub-District, Amman Qasabah District, Amman, 11183, Jordan', '2026-11-01 11:26:09'),
(44, 21, '2026-11-01 12:02:48', 735.95, 'delivered', '6, Al-Bonouk Street, Jabal al-Webdeh, منطقة زهران, Amman, Amman Sub-District, Amman Qasabah District, Amman, 11185, Jordan', '2026-11-01 12:02:48');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`) VALUES
(1, 1, 35, 2, 39.00),
(2, 1, 43, 3, 59.00),
(3, 2, 53, 2, 119.00),
(4, 2, 43, 1, 59.00),
(5, 2, 70, 1, 189.00),
(6, 2, 60, 2, 139.00),
(7, 3, 39, 2, 42.00),
(8, 3, 97, 3, 119.00),
(9, 3, 50, 3, 139.00),
(10, 4, 60, 3, 139.00),
(11, 4, 17, 3, 269.00),
(12, 5, 24, 2, 449.00),
(13, 5, 7, 1, 649.00),
(14, 5, 34, 2, 299.00),
(15, 5, 49, 2, 189.00),
(16, 6, 3, 2, 299.00),
(17, 6, 96, 1, 109.00),
(18, 6, 70, 3, 189.00),
(19, 6, 7, 1, 649.00),
(20, 7, 86, 2, 169.00),
(21, 7, 15, 3, 949.00),
(22, 7, 73, 2, 599.00),
(23, 7, 28, 2, 129.00),
(24, 8, 40, 3, 99.00),
(25, 9, 88, 3, 299.00),
(26, 9, 77, 1, 229.00),
(27, 9, 16, 2, 549.00),
(28, 10, 14, 2, 299.00),
(29, 10, 56, 3, 129.00),
(30, 10, 47, 1, 49.00),
(31, 10, 82, 2, 69.00),
(32, 11, 94, 1, 89.00),
(33, 11, 76, 1, 269.00),
(34, 11, 35, 2, 39.00),
(35, 11, 42, 2, 89.00),
(36, 12, 24, 2, 449.00),
(37, 12, 63, 2, 104.00),
(38, 12, 28, 2, 129.00),
(39, 13, 63, 3, 104.00),
(40, 13, 72, 2, 549.00),
(41, 13, 98, 3, 69.00),
(42, 13, 31, 3, 109.00),
(43, 14, 61, 2, 169.00),
(44, 14, 71, 2, 699.00),
(45, 14, 68, 3, 109.00),
(46, 15, 16, 3, 549.00),
(47, 15, 96, 1, 109.00),
(48, 15, 69, 1, 69.00),
(49, 16, 38, 2, 149.00),
(50, 16, 30, 1, 259.00),
(51, 17, 82, 2, 69.00),
(52, 17, 97, 1, 119.00),
(53, 18, 15, 1, 949.00),
(54, 18, 9, 3, 159.00),
(55, 18, 52, 3, 99.00),
(56, 18, 63, 3, 104.00),
(57, 19, 78, 3, 399.00),
(58, 19, 77, 2, 229.00),
(59, 19, 80, 2, 449.00),
(60, 19, 29, 2, 119.00),
(61, 20, 34, 1, 299.00),
(62, 20, 85, 1, 49.00),
(63, 21, 30, 2, 259.00),
(64, 22, 15, 1, 949.00),
(65, 22, 70, 2, 189.00),
(66, 22, 29, 1, 119.00),
(67, 22, 83, 1, 159.00),
(68, 23, 60, 3, 139.00),
(69, 24, 95, 1, 39.00),
(70, 24, 42, 1, 89.00),
(71, 24, 78, 1, 399.00),
(72, 24, 33, 3, 44.00),
(73, 25, 36, 3, 79.00),
(74, 25, 24, 2, 449.00),
(75, 25, 75, 1, 149.00),
(76, 25, 56, 2, 129.00),
(77, 26, 37, 3, 119.00),
(78, 26, 85, 1, 49.00),
(79, 26, 52, 2, 99.00),
(80, 26, 98, 1, 69.00),
(81, 27, 53, 2, 119.00),
(82, 27, 7, 3, 649.00),
(83, 27, 25, 2, 139.00),
(84, 27, 67, 3, 139.00),
(85, 28, 4, 3, 589.00),
(86, 29, 60, 1, 139.00),
(87, 30, 59, 1, 59.00),
(88, 30, 71, 2, 699.00),
(89, 31, 65, 2, 99.00),
(90, 31, 35, 3, 39.00),
(91, 31, 1, 2, 599.00),
(92, 32, 25, 3, 139.00),
(93, 32, 90, 2, 119.00),
(94, 32, 31, 1, 109.00),
(95, 33, 5, 2, 409.00),
(96, 33, 17, 1, 269.00),
(97, 33, 65, 2, 99.00),
(98, 33, 76, 1, 269.00),
(99, 34, 80, 3, 449.00),
(100, 34, 89, 1, 99.00),
(101, 34, 51, 2, 139.00),
(102, 35, 81, 2, 149.00),
(103, 35, 88, 1, 299.00),
(104, 36, 4, 1, 589.00),
(105, 36, 6, 2, 319.00),
(106, 36, 42, 2, 89.00),
(107, 37, 23, 3, 179.00),
(108, 37, 11, 2, 1599.00),
(109, 38, 86, 2, 169.00),
(110, 38, 2, 3, 449.00),
(111, 38, 60, 3, 139.00),
(112, 39, 26, 1, 429.00),
(113, 39, 50, 1, 139.00),
(114, 39, 62, 2, 89.00),
(115, 40, 73, 3, 599.00),
(116, 41, 99, 1, 99.00),
(117, 42, 96, 1, 109.00),
(118, 43, 39, 1, 42.00),
(119, 44, 19, 1, 699.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(50) NOT NULL,
  `status` enum('pending','successful','failed') NOT NULL DEFAULT 'pending'
) ;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `amount`, `method`, `status`) VALUES
(1, 1, 255.00, 'Credit Card', 'successful'),
(2, 2, 764.00, 'Bank Transfer', 'pending'),
(3, 3, 858.00, 'Bank Transfer', 'successful'),
(4, 4, 1224.00, 'Cash on Delivery', 'successful'),
(5, 5, 2523.00, 'Credit Card', 'successful'),
(6, 6, 1923.00, 'Cash on Delivery', 'successful'),
(7, 7, 4641.00, 'Credit Card', 'successful'),
(8, 8, 297.00, 'Credit Card', 'pending'),
(9, 9, 2224.00, 'Bank Transfer', 'successful'),
(10, 10, 1172.00, 'Bank Transfer', 'successful'),
(11, 11, 614.00, 'Bank Transfer', 'successful'),
(12, 12, 1364.00, 'Credit Card', 'successful'),
(13, 13, 1944.00, 'Cash on Delivery', 'pending'),
(14, 14, 2063.00, 'Cash on Delivery', 'successful'),
(15, 15, 1825.00, 'PayPal', 'successful'),
(16, 16, 557.00, 'PayPal', 'successful'),
(17, 17, 257.00, 'PayPal', 'successful'),
(18, 18, 2035.00, 'Credit Card', 'successful'),
(19, 19, 2791.00, 'Credit Card', 'successful'),
(20, 20, 348.00, 'Bank Transfer', 'successful'),
(21, 21, 518.00, 'Cash on Delivery', 'pending'),
(22, 22, 1605.00, 'PayPal', 'successful'),
(23, 23, 417.00, 'Credit Card', 'successful'),
(24, 24, 659.00, 'Cash on Delivery', 'successful'),
(25, 25, 1542.00, 'Bank Transfer', 'successful'),
(26, 26, 673.00, 'Credit Card', 'failed'),
(27, 27, 2880.00, 'Bank Transfer', 'successful'),
(28, 28, 1767.00, 'Cash on Delivery', 'successful'),
(29, 29, 139.00, 'Bank Transfer', 'successful'),
(30, 30, 1457.00, 'Bank Transfer', 'successful'),
(31, 31, 1513.00, 'Bank Transfer', 'successful'),
(32, 32, 764.00, 'Cash on Delivery', 'successful'),
(33, 33, 1554.00, 'Bank Transfer', 'successful'),
(34, 34, 1724.00, 'Credit Card', 'successful'),
(35, 35, 597.00, 'Cash on Delivery', 'successful'),
(36, 36, 1405.00, 'Cash on Delivery', 'successful'),
(37, 37, 3735.00, 'Bank Transfer', 'successful'),
(38, 38, 2102.00, 'PayPal', 'failed'),
(39, 39, 746.00, 'PayPal', 'successful'),
(40, 40, 1797.00, 'PayPal', 'successful');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `stock_quantity`, `created_at`, `is_active`) VALUES
(1, 1, 'AMD Ryzen 9 7950X', 'AMD Ryzen 9 7950X - high performance processors component, brand new and sealed.', 599.00, 86, '2026-10-18 08:47:00', 1),
(2, 1, 'AMD Ryzen 7 7800X3D', 'AMD Ryzen 7 7800X3D - high performance processors component, brand new and sealed.', 449.00, 33, '2026-10-19 19:06:00', 1),
(3, 1, 'AMD Ryzen 5 7600X', 'AMD Ryzen 5 7600X - high performance processors component, brand new and sealed.', 299.00, 16, '2026-10-28 08:01:00', 1),
(4, 1, 'Intel Core i9-14900K', 'Intel Core i9-14900K - high performance processors component, brand new and sealed.', 589.00, 34, '2026-10-15 16:12:00', 1),
(5, 1, 'Intel Core i7-14700K', 'Intel Core i7-14700K - high performance processors component, brand new and sealed.', 409.00, 94, '2026-10-28 11:28:00', 1),
(6, 1, 'Intel Core i5-14600K', 'Intel Core i5-14600K - high performance processors component, brand new and sealed.', 319.00, 108, '2026-10-15 20:51:00', 1),
(7, 1, 'AMD Ryzen 9 9950X', 'AMD Ryzen 9 9950X - high performance processors component, brand new and sealed.', 649.00, 59, '2026-10-25 12:09:00', 1),
(8, 1, 'Intel Core i5-13400F', 'Intel Core i5-13400F - high performance processors component, brand new and sealed.', 199.00, 18, '2026-10-17 14:06:00', 1),
(9, 1, 'AMD Ryzen 5 5600X', 'AMD Ryzen 5 5600X - high performance processors component, brand new and sealed.', 159.00, 38, '2026-10-16 19:29:00', 1),
(10, 1, 'Intel Core i3-13100F', 'Intel Core i3-13100F - high performance processors component, brand new and sealed.', 119.00, 53, '2026-10-17 16:18:00', 1),
(11, 2, 'NVIDIA GeForce RTX 4090', 'NVIDIA GeForce RTX 4090 - high performance graphics cards component, brand new and sealed.', 1599.00, 84, '2026-10-26 17:12:00', 1),
(12, 2, 'NVIDIA GeForce RTX 4080 Super', 'NVIDIA GeForce RTX 4080 Super - high performance graphics cards component, brand new and sealed.', 999.00, 10, '2026-10-22 20:18:00', 1),
(13, 2, 'NVIDIA GeForce RTX 4070 Ti', 'NVIDIA GeForce RTX 4070 Ti - high performance graphics cards component, brand new and sealed.', 799.00, 114, '2026-10-22 09:24:00', 1),
(14, 2, 'NVIDIA GeForce RTX 4060', 'NVIDIA GeForce RTX 4060 - high performance graphics cards component, brand new and sealed.', 299.00, 86, '2026-10-26 10:23:00', 1),
(15, 2, 'AMD Radeon RX 7900 XTX', 'AMD Radeon RX 7900 XTX - high performance graphics cards component, brand new and sealed.', 949.00, 90, '2026-10-23 19:59:00', 1),
(16, 2, 'AMD Radeon RX 7800 XT', 'AMD Radeon RX 7800 XT - high performance graphics cards component, brand new and sealed.', 549.00, 14, '2026-10-20 16:46:00', 1),
(17, 2, 'AMD Radeon RX 7600', 'AMD Radeon RX 7600 - high performance graphics cards component, brand new and sealed.', 269.00, 64, '2026-10-27 12:59:00', 1),
(18, 2, 'NVIDIA GeForce RTX 3060', 'NVIDIA GeForce RTX 3060 - high performance graphics cards component, brand new and sealed.', 329.00, 76, '2026-10-22 18:20:00', 1),
(19, 2, 'ASUS ROG Strix RTX 4070', 'ASUS ROG Strix RTX 4070 - high performance graphics cards component, brand new and sealed.', 699.00, 34, '2026-10-16 20:20:00', 1),
(20, 2, 'MSI Gaming X RX 6700 XT', 'MSI Gaming X RX 6700 XT - high performance graphics cards component, brand new and sealed.', 419.00, 13, '2026-10-21 17:56:00', 1),
(21, 3, 'ASUS ROG Strix Z790-E', 'ASUS ROG Strix Z790-E - high performance motherboards component, brand new and sealed.', 489.00, 32, '2026-10-30 14:56:00', 1),
(22, 3, 'MSI MAG B650 Tomahawk', 'MSI MAG B650 Tomahawk - high performance motherboards component, brand new and sealed.', 219.00, 63, '2026-10-19 12:08:00', 1),
(23, 3, 'Gigabyte B760 Aorus Elite', 'Gigabyte B760 Aorus Elite - high performance motherboards component, brand new and sealed.', 179.00, 38, '2026-10-28 17:25:00', 1),
(24, 3, 'ASRock X670E Taichi', 'ASRock X670E Taichi - high performance motherboards component, brand new and sealed.', 449.00, 22, '2026-10-30 09:48:00', 1),
(25, 3, 'ASUS Prime B660-Plus', 'ASUS Prime B660-Plus - high performance motherboards component, brand new and sealed.', 139.00, 85, '2026-10-20 20:43:00', 1),
(26, 3, 'MSI MPG X670E Carbon', 'MSI MPG X670E Carbon - high performance motherboards component, brand new and sealed.', 429.00, 13, '2026-10-27 14:38:00', 1),
(27, 3, 'Gigabyte Z790 Aorus Master', 'Gigabyte Z790 Aorus Master - high performance motherboards component, brand new and sealed.', 389.00, 72, '2026-10-23 16:55:00', 1),
(28, 3, 'ASRock B550 Steel Legend', 'ASRock B550 Steel Legend - high performance motherboards component, brand new and sealed.', 129.00, 92, '2026-10-18 18:56:00', 1),
(29, 3, 'MSI PRO B760M-A', 'MSI PRO B760M-A - high performance motherboards component, brand new and sealed.', 119.00, 19, '2026-10-24 14:10:00', 1),
(30, 3, 'ASUS TUF Gaming X670-Plus', 'ASUS TUF Gaming X670-Plus - high performance motherboards component, brand new and sealed.', 259.00, 97, '2026-10-23 16:48:00', 1),
(31, 4, 'Corsair Vengeance 32GB DDR5 6000MHz', 'Corsair Vengeance 32GB DDR5 6000MHz - high performance memory (ram) component, brand new and sealed.', 109.00, 18, '2026-10-24 18:32:00', 1),
(32, 4, 'G.Skill Trident Z5 32GB DDR5 6400MHz', 'G.Skill Trident Z5 32GB DDR5 6400MHz - high performance memory (ram) component, brand new and sealed.', 139.00, 24, '2026-10-26 20:10:00', 1),
(33, 4, 'Kingston Fury Beast 16GB DDR4 3200MHz', 'Kingston Fury Beast 16GB DDR4 3200MHz - high performance memory (ram) component, brand new and sealed.', 44.00, 46, '2026-10-30 08:07:00', 1),
(34, 4, 'Corsair Dominator Platinum 64GB DDR5', 'Corsair Dominator Platinum 64GB DDR5 - high performance memory (ram) component, brand new and sealed.', 299.00, 117, '2026-10-24 11:03:00', 1),
(35, 4, 'Crucial 16GB DDR4 3200MHz', 'Crucial 16GB DDR4 3200MHz - high performance memory (ram) component, brand new and sealed.', 39.00, 98, '2026-10-30 09:48:00', 1),
(36, 4, 'G.Skill Ripjaws V 32GB DDR4 3600MHz', 'G.Skill Ripjaws V 32GB DDR4 3600MHz - high performance memory (ram) component, brand new and sealed.', 79.00, 89, '2026-10-30 16:10:00', 1),
(37, 4, 'TeamGroup T-Force Delta RGB 32GB DDR5', 'TeamGroup T-Force Delta RGB 32GB DDR5 - high performance memory (ram) component, brand new and sealed.', 119.00, 116, '2026-10-28 11:59:00', 1),
(38, 4, 'Kingston Fury Renegade 32GB DDR5 6000MHz', 'Kingston Fury Renegade 32GB DDR5 6000MHz - high performance memory (ram) component, brand new and sealed.', 149.00, 30, '2026-10-24 14:42:00', 1),
(39, 4, 'Corsair Vengeance LPX 16GB DDR4 3200MHz', 'Corsair Vengeance LPX 16GB DDR4 3200MHz - high performance memory (ram) component, brand new and sealed.', 42.00, 61, '2026-10-29 09:15:00', 1),
(40, 4, 'ADATA XPG Lancer 32GB DDR5 5600MHz', 'ADATA XPG Lancer 32GB DDR5 5600MHz - high performance memory (ram) component, brand new and sealed.', 99.00, 48, '2026-10-15 17:35:00', 1),
(41, 5, 'Samsung 990 Pro 2TB NVMe SSD', 'Samsung 990 Pro 2TB NVMe SSD - high performance storage component, brand new and sealed.', 169.00, 33, '2026-10-15 09:45:00', 1),
(42, 5, 'WD Black SN850X 1TB NVMe SSD', 'WD Black SN850X 1TB NVMe SSD - high performance storage component, brand new and sealed.', 89.00, 34, '2026-10-17 08:55:00', 1),
(43, 5, 'Crucial MX500 1TB SATA SSD', 'Crucial MX500 1TB SATA SSD - high performance storage component, brand new and sealed.', 59.00, 70, '2026-10-22 12:42:00', 1),
(44, 5, 'Seagate Barracuda 4TB HDD', 'Seagate Barracuda 4TB HDD - high performance storage component, brand new and sealed.', 89.00, 74, '2026-10-19 19:59:00', 1),
(45, 5, 'Samsung 980 500GB NVMe SSD', 'Samsung 980 500GB NVMe SSD - high performance storage component, brand new and sealed.', 44.00, 78, '2026-10-30 11:50:00', 1),
(46, 5, 'WD Blue 2TB HDD', 'WD Blue 2TB HDD - high performance storage component, brand new and sealed.', 54.00, 17, '2026-10-18 18:27:00', 1),
(47, 5, 'Kingston NV2 1TB NVMe SSD', 'Kingston NV2 1TB NVMe SSD - high performance storage component, brand new and sealed.', 49.00, 57, '2026-10-29 19:03:00', 1),
(48, 5, 'Sabrent Rocket 4 Plus 2TB NVMe SSD', 'Sabrent Rocket 4 Plus 2TB NVMe SSD - high performance storage component, brand new and sealed.', 149.00, 87, '2026-10-18 08:25:00', 1),
(49, 5, 'Seagate IronWolf 8TB NAS HDD', 'Seagate IronWolf 8TB NAS HDD - high performance storage component, brand new and sealed.', 189.00, 107, '2026-10-18 11:12:00', 1),
(50, 5, 'Crucial P5 Plus 2TB NVMe SSD', 'Crucial P5 Plus 2TB NVMe SSD - high performance storage component, brand new and sealed.', 139.00, 62, '2026-10-19 14:11:00', 1),
(51, 6, 'Corsair RM850x 850W 80+ Gold', 'Corsair RM850x 850W 80+ Gold - high performance power supplies component, brand new and sealed.', 139.00, 36, '2026-10-17 15:51:00', 1),
(52, 6, 'EVGA SuperNOVA 650 G6', 'EVGA SuperNOVA 650 G6 - high performance power supplies component, brand new and sealed.', 99.00, 11, '2026-10-15 09:59:00', 1),
(53, 6, 'Seasonic Focus GX-750', 'Seasonic Focus GX-750 - high performance power supplies component, brand new and sealed.', 119.00, 57, '2026-10-30 15:13:00', 1),
(54, 6, 'be quiet! Straight Power 11 1000W', 'be quiet! Straight Power 11 1000W - high performance power supplies component, brand new and sealed.', 189.00, 120, '2026-10-16 10:24:00', 1),
(55, 6, 'Cooler Master MWE Gold 650W', 'Cooler Master MWE Gold 650W - high performance power supplies component, brand new and sealed.', 79.00, 105, '2026-10-29 12:27:00', 1),
(56, 6, 'MSI MPG A850G', 'MSI MPG A850G - high performance power supplies component, brand new and sealed.', 129.00, 89, '2026-10-30 10:12:00', 1),
(57, 6, 'Thermaltake Toughpower GF1 750W', 'Thermaltake Toughpower GF1 750W - high performance power supplies component, brand new and sealed.', 109.00, 12, '2026-10-16 19:20:00', 1),
(58, 6, 'ASUS ROG Thor 1200W', 'ASUS ROG Thor 1200W - high performance power supplies component, brand new and sealed.', 299.00, 79, '2026-10-30 16:58:00', 1),
(59, 6, 'Corsair CV650 650W 80+ Bronze', 'Corsair CV650 650W 80+ Bronze - high performance power supplies component, brand new and sealed.', 59.00, 25, '2026-10-16 16:05:00', 1),
(60, 6, 'NZXT C850 850W Gold', 'NZXT C850 850W Gold - high performance power supplies component, brand new and sealed.', 139.00, 13, '2026-10-17 18:55:00', 1),
(61, 7, 'Lian Li O11 Dynamic EVO', 'Lian Li O11 Dynamic EVO - high performance pc cases component, brand new and sealed.', 169.00, 20, '2026-10-22 17:38:00', 1),
(62, 7, 'NZXT H510 Flow', 'NZXT H510 Flow - high performance pc cases component, brand new and sealed.', 89.00, 15, '2026-10-28 18:37:00', 1),
(63, 7, 'Corsair 4000D Airflow', 'Corsair 4000D Airflow - high performance pc cases component, brand new and sealed.', 104.00, 45, '2026-10-23 11:42:00', 1),
(64, 7, 'Fractal Design Meshify 2', 'Fractal Design Meshify 2 - high performance pc cases component, brand new and sealed.', 149.00, 35, '2026-10-23 14:08:00', 1),
(65, 7, 'Cooler Master MasterBox TD500 Mesh', 'Cooler Master MasterBox TD500 Mesh - high performance pc cases component, brand new and sealed.', 99.00, 43, '2026-10-29 13:59:00', 1),
(66, 7, 'Phanteks P400A', 'Phanteks P400A - high performance pc cases component, brand new and sealed.', 89.00, 63, '2026-10-18 09:34:00', 1),
(67, 7, 'ASUS TUF Gaming GT501', 'ASUS TUF Gaming GT501 - high performance pc cases component, brand new and sealed.', 139.00, 38, '2026-10-19 13:56:00', 1),
(68, 7, 'be quiet! Pure Base 500DX', 'be quiet! Pure Base 500DX - high performance pc cases component, brand new and sealed.', 109.00, 41, '2026-10-20 15:53:00', 1),
(69, 7, 'Thermaltake Core V21', 'Thermaltake Core V21 - high performance pc cases component, brand new and sealed.', 69.00, 43, '2026-10-15 18:52:00', 1),
(70, 7, 'Hyte Y60', 'Hyte Y60 - high performance pc cases component, brand new and sealed.', 189.00, 89, '2026-10-18 10:16:00', 1),
(71, 8, 'LG UltraGear 27GP950 4K 144Hz', 'LG UltraGear 27GP950 4K 144Hz - high performance monitors component, brand new and sealed.', 699.00, 39, '2026-10-24 17:13:00', 1),
(72, 8, 'Samsung Odyssey G7 32-inch', 'Samsung Odyssey G7 32-inch - high performance monitors component, brand new and sealed.', 549.00, 31, '2026-10-23 16:31:00', 1),
(73, 8, 'ASUS ROG Swift PG279QM', 'ASUS ROG Swift PG279QM - high performance monitors component, brand new and sealed.', 599.00, 86, '2026-10-28 12:02:00', 1),
(74, 8, 'Dell UltraSharp U2723QE 27-inch 4K', 'Dell UltraSharp U2723QE 27-inch 4K - high performance monitors component, brand new and sealed.', 499.00, 103, '2026-10-19 18:16:00', 1),
(75, 8, 'AOC 24G2 24-inch 144Hz', 'AOC 24G2 24-inch 144Hz - high performance monitors component, brand new and sealed.', 149.00, 95, '2026-10-28 16:00:00', 1),
(76, 8, 'BenQ EX2710Q', 'BenQ EX2710Q - high performance monitors component, brand new and sealed.', 269.00, 118, '2026-10-19 16:02:00', 1),
(77, 8, 'MSI Optix MAG274QRF', 'MSI Optix MAG274QRF - high performance monitors component, brand new and sealed.', 229.00, 79, '2026-10-19 14:08:00', 1),
(78, 8, 'ViewSonic Elite XG270QG', 'ViewSonic Elite XG270QG - high performance monitors component, brand new and sealed.', 399.00, 51, '2026-10-16 13:13:00', 1),
(79, 8, 'Gigabyte M27Q 27-inch QHD', 'Gigabyte M27Q 27-inch QHD - high performance monitors component, brand new and sealed.', 259.00, 90, '2026-10-18 13:49:00', 1),
(80, 8, 'LG 34WN80C UltraWide', 'LG 34WN80C UltraWide - high performance monitors component, brand new and sealed.', 449.00, 84, '2026-10-19 11:55:00', 1),
(81, 9, 'Logitech G Pro X Superlight', 'Logitech G Pro X Superlight - high performance peripherals component, brand new and sealed.', 149.00, 27, '2026-10-25 20:59:00', 1),
(82, 9, 'Razer DeathAdder V3', 'Razer DeathAdder V3 - high performance peripherals component, brand new and sealed.', 69.00, 25, '2026-10-18 14:55:00', 1),
(83, 9, 'Corsair K70 RGB Pro Mechanical Keyboard', 'Corsair K70 RGB Pro Mechanical Keyboard - high performance peripherals component, brand new and sealed.', 159.00, 30, '2026-10-29 13:19:00', 1),
(84, 9, 'SteelSeries Apex Pro', 'SteelSeries Apex Pro - high performance peripherals component, brand new and sealed.', 199.00, 33, '2026-10-15 18:12:00', 1),
(85, 9, 'Logitech G502 Hero', 'Logitech G502 Hero - high performance peripherals component, brand new and sealed.', 49.00, 40, '2026-10-17 20:17:00', 1),
(86, 9, 'Razer BlackWidow V4', 'Razer BlackWidow V4 - high performance peripherals component, brand new and sealed.', 169.00, 70, '2026-10-27 18:53:00', 1),
(87, 9, 'HyperX Cloud II Headset', 'HyperX Cloud II Headset - high performance peripherals component, brand new and sealed.', 79.00, 8, '2026-10-18 12:11:00', 1),
(88, 9, 'SteelSeries Arctis Nova Pro', 'SteelSeries Arctis Nova Pro - high performance peripherals component, brand new and sealed.', 299.00, 9, '2026-10-18 17:27:00', 1),
(89, 9, 'Logitech MX Master 3S', 'Logitech MX Master 3S - high performance peripherals component, brand new and sealed.', 99.00, 105, '2026-10-25 14:38:00', 1),
(90, 9, 'Razer Huntsman Mini', 'Razer Huntsman Mini - high performance peripherals component, brand new and sealed.', 119.00, 19, '2026-10-27 17:12:00', 1),
(91, 10, 'NZXT Kraken X73 360mm AIO', 'NZXT Kraken X73 360mm AIO - high performance cooling component, brand new and sealed.', 189.00, 95, '2026-10-28 08:33:00', 1),
(92, 10, 'Corsair iCUE H150i Elite', 'Corsair iCUE H150i Elite - high performance cooling component, brand new and sealed.', 199.00, 97, '2026-10-21 13:27:00', 1),
(93, 10, 'Noctua NH-D15', 'Noctua NH-D15 - high performance cooling component, brand new and sealed.', 109.00, 45, '2026-10-18 19:57:00', 1),
(94, 10, 'be quiet! Dark Rock Pro 4', 'be quiet! Dark Rock Pro 4 - high performance cooling component, brand new and sealed.', 89.00, 44, '2026-10-28 13:25:00', 1),
(95, 10, 'Cooler Master Hyper 212 Black', 'Cooler Master Hyper 212 Black - high performance cooling component, brand new and sealed.', 39.00, 75, '2026-10-19 11:26:00', 1),
(96, 10, 'Arctic Liquid Freezer II 240', 'Arctic Liquid Freezer II 240 - high performance cooling component, brand new and sealed.', 109.00, 100, '2026-10-20 17:36:00', 1),
(97, 10, 'Lian Li Galahad 240', 'Lian Li Galahad 240 - high performance cooling component, brand new and sealed.', 119.00, 75, '2026-10-15 12:18:00', 1),
(98, 10, 'DeepCool AK620', 'DeepCool AK620 - high performance cooling component, brand new and sealed.', 69.00, 105, '2026-10-25 15:28:00', 1),
(99, 10, 'Thermaltake TOUGHLIQUID 240', 'Thermaltake TOUGHLIQUID 240 - high performance cooling component, brand new and sealed.', 99.00, 32, '2026-10-30 20:57:00', 1),
(100, 10, 'Corsair LL120 RGB Triple Fan Kit', 'Corsair LL120 RGB Triple Fan Kit - high performance cooling component, brand new and sealed.', 109.00, 30, '2026-10-17 12:32:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image`) VALUES
(1, 1, 'https://www.amd.com/content/dam/amd/en/images/products/processors/ryzen/2505503-ryzen-9-7900x.jpg'),
(2, 1, 'https://tpucdn.com/cpu-specs/images/chips/2846-installed.jpg'),
(3, 2, 'https://i.ebayimg.com/images/g/UQYAAOSwwCdksYa9/s-l1600.webp'),
(4, 2, 'https://kotaku.com/app/uploads/2026/10/amd-ryzen-7-1280x853.jpg'),
(5, 3, 'https://os-jo.com/image/cache/catalog/products/processor/RYZEN-5-7600/ezgif.com-webp-to-jpg-1200x1200.jpg'),
(6, 3, 'https://os-jo.com/image/cache/catalog/products/processor/RYZEN-5-7600/AM5-1200x1200.jpg'),
(7, 4, 'https://tpucdn.com/cpu-specs/images/chips/3269-installed.jpg'),
(8, 4, 'https://api.cazasouq.com/storage/v1/render/image/public/media-library/opencart/276e2ca307524597af61af72ce458497d10952fe10d5c5406eb666b68167ecb0.png?width=1024&quality=70&resize=contain'),
(9, 5, 'https://api.cazasouq.com/storage/v1/render/image/public/media-library/opencart/4e3d68579e510f854120875b159be04d679ff7849087d344ce8d30a0004e9387.png?width=1024&quality=70&resize=contain'),
(10, 5, 'https://tpucdn.com/cpu-specs/images/chips/3268-installed.jpg'),
(11, 6, 'https://tpucdn.com/cpu-specs/images/chips/3266-installed.jpg'),
(12, 6, 'https://tpucdn.com/cpu-specs/images/chips/3266-package.jpg'),
(13, 7, 'https://tpucdn.com/cpu-specs/images/chips/3649-package.jpg'),
(14, 7, 'https://tpucdn.com/cpu-specs/images/chips/3649-installed.jpg'),
(15, 8, 'https://tpucdn.com/cpu-specs/images/chips/2995-installed.jpg'),
(16, 8, 'https://tpucdn.com/cpu-specs/images/chips/2995-package.jpg'),
(17, 9, 'https://tpucdn.com/cpu-specs/images/chips/2365-package.jpg'),
(18, 9, 'https://tpucdn.com/cpu-specs/images/chips/2365-installed.jpg'),
(19, 10, 'https://tpucdn.com/cpu-specs/images/chips/2999-package.jpg'),
(20, 10, 'https://tpucdn.com/cpu-specs/images/chips/2999-front.jpg'),
(21, 11, 'https://images.unsplash.com/photo-1520205982192?auto=format&fit=crop&w=800&q=80&graphics-card-gpuhttps://tpucdn.com/gpu-specs/images-new/c/3889-front-large.jpg'),
(22, 11, 'https://tpucdn.com/gpu-specs/images-new/g/1005-ad102-300-a1-large.jpg'),
(23, 12, 'https://tpucdn.com/gpu-specs/images-new/c/4182-front-large.jpg'),
(24, 12, 'https://tpucdn.com/gpu-specs/images-new/g/1012-ad103-400-a1-large.jpg'),
(25, 13, 'https://tpucdn.com/gpu-specs/images-new/c/3950-front-large.jpg'),
(26, 13, 'https://tpucdn.com/gpu-specs/images-new/g/1013-ad104-400-a1-large.jpg'),
(27, 14, 'https://tpucdn.com/gpu-specs/images-new/c/4107-front-large.jpg'),
(28, 14, 'https://tpucdn.com/gpu-specs/images-new/g/1015-ad107-400-a1-large.jpg'),
(29, 15, 'https://tpucdn.com/gpu-specs/images-new/c/3941-front-large.jpg'),
(30, 15, 'https://tpucdn.com/gpu-specs/images-new/g/998-navi-31-xtx-large.jpg'),
(31, 16, 'https://tpucdn.com/gpu-specs/images-new/c/3839-front-large.jpg'),
(32, 16, 'https://tpucdn.com/gpu-specs/images-new/g/1000-navi-32-xt-large.jpg'),
(33, 17, 'https://tpucdn.com/gpu-specs/images-new/c/4153-front-large.jpg'),
(34, 17, 'https://tpucdn.com/gpu-specs/images-new/g/1001-navi-33-xl-large.jpg'),
(35, 18, 'https://tpucdn.com/gpu-specs/images-new/c/3937-front-large.jpg'),
(36, 18, 'https://tpucdn.com/gpu-specs/images-new/g/966-default-large.jpg'),
(37, 19, 'https://tpucdn.com/gpu-specs/images-new/c/4228-front-large.jpg'),
(38, 19, 'https://tpucdn.com/gpu-specs/images-new/g/1013-default-large.jpg'),
(39, 20, 'https://tpucdn.com/gpu-specs/images-new/c/3695-front-large.jpg'),
(40, 20, 'https://tpucdn.com/gpu-specs/images-new/g/951-navi-22-xt-large.jpg'),
(41, 21, 'https://dlcdnwebimgs.asus.com/files/media/9A827026-9AD2-4CE7-9958-DB583A2DB6F8/v1/img/kv/ROG-Strix-Z790-E-Gaming.png'),
(42, 21, 'https://dlcdnwebimgs.asus.com/files/media/9A827026-9AD2-4CE7-9958-DB583A2DB6F8/v1/img/kv/ROG-Strix-Z790-E-Gaming.png'),
(43, 22, 'https://storage-asset.msi.com/global/picture/image/feature/mb/B650/MAG-B650-TOMAHAWK-WIFI/mag-b650-tomahawk-wifi.png'),
(44, 22, 'https://storage-asset.msi.com/global/picture/image/feature/mb/B650/MAG-B650-TOMAHAWK-WIFI/mag-b650-tomahawk-wifi.png'),
(45, 23, 'https://static.gigabyte.com/StaticFile/Image/Global/44b27a46ba52c7f07e2208cc937bf790/ProductRemoveBg/33509/webp/600'),
(46, 23, 'https://static.gigabyte.com/StaticFile/Image/Global/44b27a46ba52c7f07e2208cc937bf790/ProductRemoveBg/33509/webp/600'),
(47, 24, 'https://www.asrock.com/mb/photo/X670E%20Taichi(M1).png'),
(48, 24, 'https://www.asrock.com/mb/photo/X670E%20Taichi(M1).png'),
(49, 25, 'https://dlcdnwebimgs.asus.com/gain/fb95a37b-ee86-42f5-9615-cf9103c5fa0a/w692'),
(50, 25, 'https://dlcdnwebimgs.asus.com/gain/fb95a37b-ee86-42f5-9615-cf9103c5fa0a/w692'),
(51, 26, 'https://storage-asset.msi.com/global/picture/image/feature/mb/X670/mpg/X670E-CARBON-WIFI/mpg_x670e_carbon_wifi-norgb.png'),
(52, 26, 'https://storage-asset.msi.com/global/picture/image/feature/mb/X670/mpg/X670E-CARBON-WIFI/mpg_x670e_carbon_wifi-block02.png'),
(53, 27, 'https://www.gigabyte.com/FileUpload/Global/KeyFeature/2492/innergigabyte/images/product/summary.png'),
(54, 27, 'https://www.gigabyte.com/FileUpload/Global/KeyFeature/2492/innergigabyte/images/product/summary.png'),
(55, 28, 'https://mcc-jo.com/wp-content/uploads/2021/02/ASROCK-AMD-B550-Steel-Legend-RGB-Motherboard-4.jpg'),
(56, 28, 'https://mcc-jo.com/wp-content/uploads/2021/02/ASROCK-AMD-B550-Steel-Legend-RGB-Motherboard-4.jpg'),
(57, 29, 'https://storage-asset.msi.com/global/picture/image/feature/mb/Pro_B760M/PRO_B760M-A-Wifi/msi-pro-b760m-a-wifi-main.png'),
(58, 29, 'https://storage-asset.msi.com/global/picture/image/feature/mb/Pro_B760M/PRO_B760M-A-Wifi/msi-pro-b760m-a-wifi-main.png'),
(59, 30, 'https://dlcdnwebimgs.asus.com/files/media/d75c690e-189d-4c4f-9dd2-7057d045efa6/V1/img/kv-main.png'),
(60, 30, 'https://dlcdnwebimgs.asus.com/files/media/d75c690e-189d-4c4f-9dd2-7057d045efa6/V1/img/kv-main.png'),
(61, 31, 'https://assets.corsair.com/image/upload/c_pad,q_85,h_1100,w_1100,f_auto/products/Memory/vengeance-ddr5-blk-config/Gallery/VENGEANCE_DDR5_BLK_01_2up.webp'),
(62, 31, 'https://assets.corsair.com/image/upload/c_pad,q_85,h_1100,w_1100,f_auto/products/Memory/vengeance-ddr5-blk-config/Gallery/VENGEANCE_DDR5_BLK_01_2up.webp'),
(63, 32, 'https://igeekjo.com/cdn/shop/files/166564450410.png?v=1732727668&width=540'),
(64, 32, 'https://igeekjo.com/cdn/shop/files/166564450410.png?v=1732727668&width=540'),
(65, 33, 'https://images.gts.jo/cache/catalog/A2024/accss/kingston-ram/ddr4-pic/FURY_Beast_RGB_Black_DDR4_1_angle-zm-lg-1200x1200.jpg'),
(66, 33, 'https://images.gts.jo/cache/catalog/A2024/accss/kingston-ram/ddr4-pic/FURY_Beast_RGB_Black_DDR4_1_angle-zm-lg-1200x1200.jpg'),
(67, 34, 'https://cdn.mcc-jo.com/media/corsair-dominator-platinum-64gb-6400mhz-cl32-3pvyw0mutw.webp'),
(68, 34, 'https://cdn.mcc-jo.com/media/corsair-dominator-platinum-64gb-6400mhz-cl32-3pvyw0mutw.webp'),
(69, 35, 'https://m.media-amazon.com/images/I/71oz85Vv6mL._AC_SX679_.jpg'),
(70, 35, 'https://m.media-amazon.com/images/I/71oz85Vv6mL._AC_SX679_.jpg'),
(71, 36, 'https://www.gskill.com/_upload/images/156283353510.png'),
(72, 36, 'https://www.gskill.com/_upload/images/156283353510.png'),
(73, 37, 'https://api.cazasouq.com/storage/v1/render/image/public/media-library/opencart/344bd7af01c9224de455017c057ed234a21b80819cbd0e6ed5a968611d49641c.png?width=1024&quality=70&resize=contain'),
(74, 37, 'https://api.cazasouq.com/storage/v1/render/image/public/media-library/opencart/344bd7af01c9224de455017c057ed234a21b80819cbd0e6ed5a968611d49641c.png?width=1024&quality=70&resize=contain'),
(75, 38, 'https://media.kingston.com/kingston/product/FURY_Renegade_Silver_Black_DDR5_1-zm-lg.jpg'),
(76, 38, 'https://media.kingston.com/kingston/product/FURY_Renegade_Silver_Black_DDR5_1-zm-lg.jpg'),
(77, 39, 'https://assets.corsair.com/image/upload/c_pad,q_85,h_1100,w_1100,f_auto/products/Memory/CMK16GX4M2B3200C16/Gallery/VENG_LPX_BLK_00.webp'),
(78, 39, 'https://assets.corsair.com/image/upload/c_pad,q_85,h_1100,w_1100,f_auto/products/Memory/CMK16GX4M2B3200C16/Gallery/VENG_LPX_BLK_05.webp'),
(79, 40, 'https://api.cazasouq.com/storage/v1/render/image/public/media-library/opencart/311881f83c99daf1f8e64c2e59f1f2496501225266dc648432d98d492a83b63e.png?width=1024&quality=70&resize=contain'),
(80, 40, 'https://api.cazasouq.com/storage/v1/render/image/public/media-library/opencart/311881f83c99daf1f8e64c2e59f1f2496501225266dc648432d98d492a83b63e.png?width=1024&quality=70&resize=contain'),
(81, 41, 'https://api.cazasouq.com/storage/v1/render/image/public/media-library/opencart/c7b0550e854650ed237ee996e46b24aa7923d60016c13c7514b27a119c92e77e.png?width=1024&quality=70&resize=contain'),
(82, 41, 'https://api.cazasouq.com/storage/v1/render/image/public/media-library/opencart/c7b0550e854650ed237ee996e46b24aa7923d60016c13c7514b27a119c92e77e.png?width=1024&quality=70&resize=contain'),
(83, 42, 'https://igeekjo.com/cdn/shop/files/85fdf519af354be63cfb1538d4b4ab8b.png?v=1736593118&width=540'),
(84, 42, 'https://igeekjo.com/cdn/shop/files/850xx5-1200x1200.png?v=1736593118&width=1200'),
(85, 43, 'https://pisces.bbystatic.com/image2/BestBuy_US/images/products/6254/6254400_sd.jpg;maxHeight=1920;maxWidth=900?format=webp'),
(86, 43, 'https://pisces.bbystatic.com/image2/BestBuy_US/images/products/6254/6254400cv1d.jpg;maxHeight=1920;maxWidth=900?format=webp'),
(87, 44, 'https://www.geekay.com/media/catalog/product/cache/207e23213cf636ccdef205098cf3c8a3/2/0/20230621155021.png'),
(88, 44, 'https://www.geekay.com/media/catalog/product/cache/207e23213cf636ccdef205098cf3c8a3/2/0/20230621155043.png'),
(89, 45, 'https://images.samsung.com/is/image/samsung/p6pim/ie/mz-v8v500bw/gallery/ie-980-nvme-m2-ssd-mz-v8v500bw-404057836?$Q90_684_547_JPG$'),
(90, 45, 'https://images.samsung.com/is/image/samsung/p6pim/ie/mz-v8v500bw/gallery/ie-980-nvme-m2-ssd-mz-v8v500bw-399731882?$Q90_684_547_JPG$'),
(91, 46, 'https://www.westerndigital.com/content/dam/store/en-us/assets/products/internal-storage/wd-blue-desktop-sata-hdd/gallery/wd-blue-pc-desktop-hard-drive-2tb.png.wdthumb.1280.1280.webp'),
(92, 46, 'https://kimostore.net/cdn/shop/files/WesternDigitalBlue2TB3.5inchInternalHardDrive_4.png?v=1779621876'),
(93, 47, 'https://mcc-jo.com/wp-content/uploads/2022/10/KINGSTON-NV2-1TB-M.2-PCIe-4.0-NVMe-SSD-.jpg'),
(94, 47, 'https://mcc-jo.com/wp-content/uploads/2022/10/KINGSTON-NV2-1TB-M.2-PCIe-4.0-NVMe-SSD-_1.jpg'),
(95, 48, 'https://sabrent.com/cdn/shop/products/Square3_3001095a-a9a8-4651-9362-fd4b33caf845.jpg?v=1688176207&width=1200'),
(96, 48, 'https://sabrent.com/cdn/shop/products/Square8_8ac0b3c6-1591-4537-876a-429bf3e2b9e8.jpg?v=1688176207&width=1200'),
(97, 49, 'https://www.seagate.com/content/dam/seagate/migrated-assets/www-content/products/nas-drives/ironwolf-hard-drive/_shared/images/ironwolf-12tb-front-product-detail-image.png'),
(98, 49, 'https://www.seagate.com/content/dam/seagate/migrated-assets/www-content/products/nas-drives/ironwolf-hard-drive/_shared/images/ironwolf-12tb-left-product-detail-image.png'),
(99, 50, 'https://tpucdn.com/ssd-specs/images/d/108-front.jpg'),
(100, 50, 'https://tpucdn.com/ssd-specs/images/d/108-back.jpg'),
(101, 51, 'https://pccircle.com/wp-content/uploads/2024/06/1c.jpg'),
(102, 51, 'https://pccircle.com/wp-content/uploads/2024/06/2c.jpg'),
(103, 52, 'https://www.evga.com/filesimages/products/gallery/png/220-P5-0650-X1_LG_1.png'),
(104, 52, 'https://www.evga.com/filesimages/products/gallery/png/220-P5-0650-X1_XL_4.png'),
(105, 53, 'https://mcc-jo.com/wp-content/uploads/2022/07/SEASONIC-FOCUS-GX-750-750W-80-GOLD-FULL-MODULAR-POWER-SUPPLY.jpg'),
(106, 53, 'https://mcc-jo.com/wp-content/uploads/2022/07/SEASONIC-FOCUS-GX-750-750W-80-GOLD-FULL-MODULAR-POWER-SUPPLY-_1.jpg'),
(107, 54, 'https://i.ebayimg.com/images/g/f8wAAeSwHPhp7kVI/s-l1600.webp'),
(108, 54, 'https://i.ebayimg.com/images/g/YFsAAeSwhANp7kVI/s-l1600.webp'),
(109, 55, 'https://www.coolermaster.com/on/demandware.static/-/Sites-cooler-master-main/default/dwe9350642/Assets/mwe-gold-650-v2-full-modular/large/mwe-gold-650-v2-full-modular-gallery-1.png'),
(110, 55, 'https://www.coolermaster.com/on/demandware.static/-/Sites-cooler-master-main/default/dw26834036/Assets/mwe-gold-650-v2-full-modular/large/mwe-gold-650-v2-full-modular-gallery-v3-2.png'),
(111, 56, 'https://asset-us-store.msi.com/image/cache/catalog/Pd_page/PSU/MPGA850PCIE5/A850GPCIE5-1-1024x1024.png'),
(112, 56, 'https://asset-us-store.msi.com/image/cache/catalog/Pd_page/PSU/MPGA850PCIE5/A850GPCIE5-4-1024x1024.png'),
(113, 57, 'https://thermaltake.com/cdn/shop/files/toughpower_gf1_snow_750_1_ed710ab3-d17b-4c97-9835-351679ba3b2e.jpg?v=1786517485'),
(114, 57, 'https://thermaltake.com/cdn/shop/files/toughpower_gf1_snow_750_2_c8b3ba38-a43b-4d3e-8d9c-96de2f6c201c.jpg?v=1786517485'),
(115, 58, 'https://os-jo.com/image/cache/catalog/products/power-supply/ROG-THOR-1200P2-GAMING/81IqY-18ftL._AC_SL1500_-1200x1200.jpg'),
(116, 58, 'https://os-jo.com/image/cache/catalog/products/power-supply/ROG-THOR-1200P2-GAMING/17-320-032-02-1200x1200.jpeg'),
(117, 59, 'https://assets.corsair.com/image/upload/c_pad,q_85,h_1100,w_1100,f_auto/products/Power-Supply-Units/CP-9020211-EU/Gallery/CV650_PSU_01.webp'),
(118, 59, 'https://assets.corsair.com/image/upload/c_pad,q_85,h_1100,w_1100,f_auto/products/Power-Supply-Units/CP-9020211-EU/Gallery/CV650_PSU_11.webp'),
(119, 60, 'https://nzxt.com/cdn/shop/files/UrL870_52c5e38b-8f1b-464a-88a9-6d117b1ce424.png?v=1747103857&width=1000'),
(120, 60, 'https://nzxt.com/cdn/shop/files/c850-gold-psu-side-white.png?v=1747103857&width=1000'),
(121, 61, 'https://lian-li.com/wp-content/uploads/2021/12/evo-600-000.jpg'),
(122, 61, 'https://lian-li.com/wp-content/uploads/2021/12/evo-600-000.jpg'),
(123, 62, 'https://nzxt.com/cdn/shop/files/h5-flow_white_09_in-build.png?v=1789351747&width=1000'),
(124, 62, 'https://nzxt.com/cdn/shop/files/h5-flow_white_10_in-build-side.png?v=1789351747&width=1000'),
(125, 63, 'https://assets.corsair.com/image/upload/c_pad,q_85,h_1100,w_1100,f_auto/products/Cases/base-4000d-airflow-config/Gallery/4000D_AF_BLACK_01.webp'),
(126, 63, 'https://assets.corsair.com/image/upload/c_pad,q_85,h_1100,w_1100,f_auto/products/Cases/base-4000d-airflow-config/Gallery/4000D_AF_BLACK_02.webp'),
(127, 64, 'https://assets.fractal-design.com/images/uxzbxy2o/production/866e70818b5a87b147a2243f9b509a3076eaed7f-2560x2560.jpg?w=1080&q=75&auto=format'),
(128, 64, 'https://assets.fractal-design.com/images/uxzbxy2o/production/6ca0a305db6e4986f91896b63a71132ac0499fa9-2560x2560.jpg?w=1080&q=75&auto=format'),
(129, 65, 'https://www.coolermaster.com/on/demandware.static/-/Sites-cooler-master-main/default/dwfcd0516b/Assets/masterbox-td500-mesh/large/td500-mesh-black-gallery-1.png'),
(130, 65, 'https://www.coolermaster.com/on/demandware.static/-/Sites-cooler-master-main/default/dwedb19cd0/Assets/masterbox-td500-mesh/large/td500-mesh-black-gallery-4.png'),
(131, 66, 'https://c1.neweggimages.com/productimage/nb1280/11-854-087-V01.jpg'),
(132, 66, 'https://c1.neweggimages.com/productimage/nb1280/11-854-087-V05.jpg'),
(133, 67, 'https://www.asus.com/media/global/gallery/dCZvjJ0TViMrrjWl_setting_xxx_0_90_end_800.png'),
(134, 67, 'https://www.asus.com/media/global/gallery/5Ngv4KwlNQLw2vv2_setting_xxx_0_90_end_800.png'),
(135, 68, 'https://www.bequiet.com/admin/ImageServer.php?ID=80c86a30287@be-quiet.net&omitPreview=true&.jpg'),
(136, 68, 'https://www.bequiet.com/admin/ImageServer.php?ID=ad8bba30285@be-quiet.net&omitPreview=true&.jpg'),
(137, 69, 'https://thermaltake.com/cdn/shop/files/1_9_9bd7f90f-a621-4e08-8fe4-241d54316273.jpg?v=1785290632'),
(138, 69, 'https://thermaltake.com/cdn/shop/files/CA-1D5-00S1WN-00_39ddf989f3234b478e0342230539b00b_thum_e877f0f4-5782-4fa5-afb3-e9340467e46d.jpg?v=1785290632'),
(139, 70, 'https://cdn.sanity.io/images/mqc7p4g4/production/391fba567900b3161dee8d62328746d16d087a81-3480x2460.jpg?w=1920&q=100&fit=clip&auto=format'),
(140, 70, 'https://cdn.sanity.io/images/mqc7p4g4/production/86ce673dc6628aef72c55cea8f4ea7dd6eb847ff-3480x2460.jpg?w=1920&q=100&fit=clip&auto=format'),
(141, 71, 'https://www.lg.com/content/dam/channel/wcms/hk_en/images/monitor/27gp950-b_ahk_echk_hk_en_c/gallery/large02.jpg?w=800'),
(142, 71, 'https://www.lg.com/content/dam/channel/wcms/hk_en/images/monitor/27gp950-b_ahk_echk_hk_en_c/gallery/large01.jpg?w=800'),
(143, 72, 'https://cdn.mcc-jo.com/media/g9QeJeyjNA5a5IdbXEYdkqW94RWsYYVRzpXjAuFB.webp'),
(144, 72, 'https://cdn.mcc-jo.com/media/MVAKC4oNl2ro9NG1M9MiFAjl3JpCmzggdNbDr7Jj.webp'),
(145, 73, 'https://dlcdnwebimgs.asus.com/gain/72C16A36-4EE3-4AC4-A58A-35F6B8A2FB6F/w717/h525/fwebp'),
(146, 73, 'https://dlcdnwebimgs.asus.com/gain/72C16A36-4EE3-4AC4-A58A-35F6B8A2FB6F/w717/h525/fwebp'),
(147, 74, 'https://image.citycenter.jo/cachewebp/catalog/002025/32025/27235520252-1200x1200.webp'),
(148, 74, 'https://image.citycenter.jo/cachewebp/catalog/002025/32025/2723552025-1200x1200.webp'),
(149, 75, 'https://cse.jo/image/cache/catalog/products/monitors/aoc-24G2-1200x1200.jpg'),
(150, 75, 'https://cse.jo/image/cache/catalog/products/monitors/aoc-24G21-1200x1200.jpg'),
(151, 76, 'https://www.jarir.com/cdn-cgi/image/fit=contain,width=380,height=380,quality=85,metadata=none,format=auto/https://ak-asset.jarir.com/akeneo-prod/asset/0/6/c/8/06c8f130e9d3e22ae7d7ed6b9bfdc58f1b3714d3_598116_1.jpg'),
(152, 76, 'https://www.jarir.com/cdn-cgi/image/fit=contain,width=380,height=380,quality=85,metadata=none,format=auto/https://ak-asset.jarir.com/akeneo-prod/asset/0/6/c/8/06c8f130e9d3e22ae7d7ed6b9bfdc58f1b3714d3_598116_1.jpg'),
(153, 77, 'https://mcc-jo.com/wp-content/uploads/2023/03/MSI-Optix-MAG274QRF-QD-27-2K-165Hz-1ms-G-Sync-Quantum-DOT-Gaming-Monitor.jpg'),
(154, 77, 'https://mcc-jo.com/wp-content/uploads/2023/03/MSI-Optix-MAG274QRF-QD-27-2K-165Hz-1ms-G-Sync-Quantum-DOT-Gaming-Monitor2.jpg'),
(155, 78, 'https://www.viewsonic.com/media/catalog/product/cache/d979f3c0cdcbdabda114d3b4bd3541f5/x/g/xg270qg_front_hires_4.png'),
(156, 78, 'https://www.viewsonic.com/media/catalog/product/cache/d979f3c0cdcbdabda114d3b4bd3541f5/x/g/xg270qg_front_hires_4.png'),
(157, 79, 'https://cdn.miswag.me/images/thumbs512/a24a3efa-a845-4645-aac7-6395c98e200a.png'),
(158, 79, 'https://cdn.miswag.me/images/thumbs512/a24a3efa-a845-4645-aac7-6395c98e200a.png'),
(159, 80, 'https://media.us.lg.com/transform/ecomm-PDPGallery-1100x730/0105a9c4-0b5f-46bc-99fd-5510364d2fca/md07000511-zoom-01-jpg?io=transform:fill,width:899'),
(160, 80, 'https://media.us.lg.com/transform/ecomm-PDPGallery-1100x730/6996c9a4-9c20-402e-afca-b5f3e6d9f445/md07000511-zoom-02-jpg?io=transform:fill,width:899'),
(161, 81, 'https://resource.logitechg.com/w_544,h_466,ar_7:6,c_pad,q_auto,f_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/products/pro-x-superlight/pro-x-superlight-black-gallery-3.png'),
(162, 81, 'https://resource.logitechg.com/w_544,h_466,ar_7:6,c_pad,q_auto,f_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/products/pro-x-superlight/pro-x-superlight-gallery-2.png'),
(163, 82, 'https://gameakjo.com/cdn/shop/files/razer-deathadder-v3-wired-gaming-mouse-758.png?v=1756925920&width=800'),
(164, 82, 'https://gameakjo.com/cdn/shop/files/razer-deathadder-v3-wired-gaming-mouse-319.jpg?v=1756925922&width=800'),
(165, 83, 'https://assets.corsair.com/image/upload/c_pad,q_85,h_1100,w_1100,f_auto/products/Gaming-Keyboards/CH-9109410-NA/Gallery/K70_RGB_PRO_PBT_01.webp'),
(166, 83, 'https://assets.corsair.com/image/upload/c_pad,q_85,h_1100,w_1100,f_auto/products/Gaming-Keyboards/CH-9109410-NA/Gallery/K70_RGB_PRO_PBT_02.webp'),
(167, 84, 'https://images.ctfassets.net/hmm5mo4qf4mf/4XjPLiGTZpb3NxxuaE0cFI/156c2e64773c0d92d564f4ca855f4c40/wired-buy-4.png?fm=webp&q=90&fit=scale&w=1920'),
(168, 84, 'https://images.ctfassets.net/hmm5mo4qf4mf/7smnHZmIDs4qIpDthoapjl/ff85def760fb4ab1067da3bd46dae132/apex_pro_mini_img_buy_03.png__1920x1080_crop-fit_optimize_subsampling-2-2023.png?fm=webp&q=90&fit=scale&w=1920'),
(169, 85, 'https://resource.logitechg.com/w_544,h_466,ar_7:6,c_pad,q_auto,f_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/non-braid/hyjal-g502-hero/2025/g502-hero-mouse-profile-left-angle-gallery-2.png'),
(170, 85, 'https://resource.logitechg.com/w_544,h_466,ar_7:6,c_pad,q_auto,f_auto,dpr_2.0/d_transparent.gif/content/dam/gaming/en/non-braid/hyjal-g502-hero/2025/g502-hero-mouse-bottom-angle-gallery-3.png'),
(171, 86, 'https://igeekjo.com/cdn/shop/files/1_c0f6add4-ee6d-40aa-a74c-4d932d8d2cfe.jpg?v=1721124348&width=540'),
(172, 86, 'https://igeekjo.com/cdn/shop/files/4_c375a95a-6971-4b38-b5d7-c89c0dfbbd79.jpg?v=1721124348&width=1500'),
(173, 87, 'https://hyperx.com/cdn/shop/files/hyperx_cloud_ii_red_1_main.jpg?v=1763563198'),
(174, 87, 'https://hyperx.com/cdn/shop/files/2-hx-usp-cloud-ii.jpg?v=1763563198'),
(175, 88, 'https://images.ctfassets.net/hmm5mo4qf4mf/1VkqkV7Z3WN1ygbODUmPmH/173e839d023259f2b94b667288d5cf20/arctis_nova_pro_omni_black_pdp_img_buy_01.png?fm=webp&q=90&fit=scale&w=1920'),
(176, 88, 'https://images.ctfassets.net/hmm5mo4qf4mf/5V6ZkJoGW71hn4tNoSwQWq/ab400ae23ebfbaee8ad48f5ba5b67a1b/arctis_nova_pro_omni_black_pdp_img_buy_03.png?fm=webp&q=90&fit=scale&w=1920'),
(177, 89, 'https://resource.logitech.com/w_544,h_544,ar_1,c_fill,q_auto,f_auto,dpr_2.0/d_transparent.gif/content/dam/logitech/en/products/mice/mx-master-3s/2025-update/mx-master-3s-black-sustainability-gallery-4.png'),
(178, 89, 'https://resource.logitech.com/w_544,h_544,ar_1,c_fill,q_auto,f_auto,dpr_2.0/d_transparent.gif/content/dam/logitech/en/products/mice/mx-master-3s/migration-assets-for-delorean-2025/gallery/person-using-mx-master-3s-on-glass-surface-black.jpg'),
(179, 90, 'https://image.citycenter.jo/cachewebp/catalog/12020/82020/HUNB-1200x1200.webp'),
(180, 90, 'https://image.citycenter.jo/cachewebp/catalog/12020/82020/HUNB1-1200x1200.webp'),
(181, 91, 'https://nzxt.com/cdn/shop/files/kcore360_01_hero.png?v=1789344973&width=1000'),
(182, 91, 'https://nzxt.com/cdn/shop/files/kcore360_02_front.png?v=1789344972&width=1000'),
(183, 92, 'https://assets.corsair.com/image/upload/c_pad,q_85,h_1100,w_1100,f_auto/products/Liquid-Cooling/base-icue-elite-cooler-config/Gallery/H150i_ELITE_CAPELLIX_01.webp'),
(184, 92, 'https://assets.corsair.com/image/upload/c_pad,q_85,h_1100,w_1100,f_auto/products/Liquid-Cooling/base-icue-elite-cooler-config/Gallery/H150i_ELITE_CAPELLIX_02.webp'),
(185, 93, 'https://mcc-jo.com/wp-content/uploads/2022/02/Noctua-NH-D15-D-Type-Premium-CPU-Cooler.jpg'),
(186, 93, 'https://mcc-jo.com/wp-content/uploads/2022/02/Noctua-NH-D15-D-Type-Premium-CPU-Cooler-_1.jpg'),
(187, 94, 'https://www.bequiet.com/admin/ImageServer.php?ID=2412aa15698@be-quiet.net&omitPreview=true&width=600'),
(188, 94, 'https://www.bequiet.com/admin/ImageServer.php?ID=2a148a15642@be-quiet.net&omitPreview=true&.jpg'),
(189, 95, 'https://www.coolermaster.com/on/demandware.static/-/Sites-cooler-master-main/default/dw219443ed/Assets/hyper-212-black/large/hyper-212-black-01-gallery-01.png'),
(190, 95, 'https://www.coolermaster.com/on/demandware.static/-/Sites-cooler-master-main/default/dwdc59d2e7/Assets/hyper-212-black/large/hyper-212-black-01-gallery-02.png'),
(191, 96, 'https://www.arctic.de/media/17/22/72/1661421855/Liquid_Freezer_II_240_G00_Award_Badge.png'),
(192, 96, 'https://www.arctic.de/media/c6/fd/90/1583243358/Liquid_Freezer_II_240_G01.jpg'),
(193, 97, 'https://lian-li.com/wp-content/uploads/2020/11/AIO-cover.png'),
(194, 97, 'https://lian-li.com/wp-content/uploads/2020/11/AIO-cover.png'),
(195, 98, 'https://cdn.deepcool.com/public/ProductFile/DEEPCOOL/Cooling/CPUAirCoolers/AK620/Gallery/608X760/01.jpg?auto=format,compress&q=90'),
(196, 98, 'https://cdn.deepcool.com/public/ProductFile/DEEPCOOL/Cooling/CPUAirCoolers/AK620/Gallery/608X760/02.jpg?auto=format,compress&q=90'),
(197, 99, 'https://thermaltake.com/cdn/shop/files/toughliquidargb240_01_301f3fd3-7cc7-48d5-8ed6-fcc3c83a6523.jpg?v=1786612917'),
(198, 99, 'https://thermaltake.com/cdn/shop/files/toughliquidargb240_02_39ad2e6a-53e2-466e-846d-9aa5fe57358b.jpg?v=1786612917'),
(199, 100, 'https://assets.corsair.com/image/upload/c_pad,q_85,h_1100,w_1100,f_auto/products/Fans/CO-9050072-WW/Gallery/LL120_RGB_01.webp'),
(200, 100, 'https://assets.corsair.com/image/upload/c_pad,q_85,h_1100,w_1100,f_auto/products/Fans/CO-9050072-WW/Gallery/LL120_RGB_02.webp');

-- --------------------------------------------------------

--
-- Table structure for table `product_specs`
--

CREATE TABLE `product_specs` (
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `socket_type` varchar(30) DEFAULT NULL,
  `ram_type` varchar(20) DEFAULT NULL,
  `form_factor` varchar(30) DEFAULT NULL,
  `power_watts` int(10) UNSIGNED DEFAULT NULL,
  `tdp_watts` int(10) UNSIGNED DEFAULT NULL,
  `gpu_length_mm` int(10) UNSIGNED DEFAULT NULL,
  `max_gpu_length_mm` int(10) UNSIGNED DEFAULT NULL,
  `cooler_height_mm` int(10) UNSIGNED DEFAULT NULL,
  `max_cooler_height_mm` int(10) UNSIGNED DEFAULT NULL,
  `cooler_sockets` varchar(255) DEFAULT NULL,
  `supported_form_factors` varchar(255) DEFAULT NULL,
  `storage_interface` varchar(30) DEFAULT NULL,
  `data_status` varchar(30) NOT NULL DEFAULT 'needs_verification'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_specs`
--

INSERT INTO `product_specs` (`product_id`, `socket_type`, `ram_type`, `form_factor`, `power_watts`, `tdp_watts`, `gpu_length_mm`, `max_gpu_length_mm`, `cooler_height_mm`, `max_cooler_height_mm`, `cooler_sockets`, `supported_form_factors`, `storage_interface`, `data_status`) VALUES
(1, 'AM5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(2, 'AM5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(3, 'AM5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(4, 'LGA1700', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(5, 'LGA1700', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(6, 'LGA1700', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(7, 'AM5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(8, 'LGA1700', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(9, 'AM4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(10, 'LGA1700', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(15, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(17, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(20, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(21, 'LGA1700', 'DDR5', 'ATX', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(22, 'AM5', 'DDR5', 'ATX', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(23, 'LGA1700', NULL, 'ATX', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(24, 'AM5', 'DDR5', 'E-ATX', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(25, 'LGA1700', 'DDR4', 'ATX', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(26, 'AM5', 'DDR5', 'ATX', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(27, 'LGA1700', 'DDR5', 'E-ATX', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(28, 'AM4', 'DDR4', 'ATX', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(29, 'LGA1700', NULL, 'Micro-ATX', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(30, 'AM5', 'DDR5', 'ATX', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(31, NULL, 'DDR5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(32, NULL, 'DDR5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(33, NULL, 'DDR4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(34, NULL, 'DDR5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(35, NULL, 'DDR4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(36, NULL, 'DDR4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(37, NULL, 'DDR5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(38, NULL, 'DDR5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(39, NULL, 'DDR4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(40, NULL, 'DDR5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(41, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'NVMe', 'needs_verification'),
(42, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'NVMe', 'needs_verification'),
(43, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SATA', 'needs_verification'),
(44, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'NVMe', 'needs_verification'),
(46, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(47, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'NVMe', 'needs_verification'),
(48, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'NVMe', 'needs_verification'),
(49, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(50, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'NVMe', 'needs_verification'),
(51, NULL, NULL, NULL, 850, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(52, NULL, NULL, NULL, 650, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(53, NULL, NULL, NULL, 750, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(54, NULL, NULL, NULL, 1000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(55, NULL, NULL, NULL, 650, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(56, NULL, NULL, NULL, 850, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(57, NULL, NULL, NULL, 750, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(58, NULL, NULL, NULL, 1200, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(59, NULL, NULL, NULL, 650, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(60, NULL, NULL, NULL, 850, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(61, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ATX,Micro-ATX,Mini-ITX', NULL, 'needs_verification'),
(62, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ATX,Micro-ATX,Mini-ITX', NULL, 'needs_verification'),
(63, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ATX,Micro-ATX,Mini-ITX', NULL, 'needs_verification'),
(64, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'E-ATX,ATX,Micro-ATX,Mini-ITX', NULL, 'needs_verification'),
(65, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ATX,Micro-ATX,Mini-ITX', NULL, 'needs_verification'),
(66, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ATX,Micro-ATX,Mini-ITX', NULL, 'needs_verification'),
(67, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'E-ATX,ATX,Micro-ATX,Mini-ITX', NULL, 'needs_verification'),
(68, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ATX,Micro-ATX,Mini-ITX', NULL, 'needs_verification'),
(69, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Micro-ATX,Mini-ITX', NULL, 'needs_verification'),
(70, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ATX,Micro-ATX,Mini-ITX', NULL, 'needs_verification'),
(71, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(72, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(73, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(74, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(75, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(76, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(77, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(78, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(79, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(80, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(81, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(82, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(83, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(84, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(85, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(86, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(87, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(88, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(89, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(90, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(91, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(92, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(93, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(94, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(95, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(96, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(97, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(98, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification'),
(100, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'needs_verification');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `customer_id`, `product_id`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 53, 4, 'Performance is excellent, no issues so far.', '2026-10-27 15:15:00'),
(2, 1, 70, 5, 'Decent product but a bit pricey for what it offers.', '2026-10-28 22:41:00'),
(3, 1, 60, 4, 'Packaging was solid and delivery was quick.', '2026-10-23 09:54:00'),
(4, 2, 97, 3, 'Great quality, works perfectly and arrived fast.', '2026-10-20 20:21:00'),
(5, 2, 50, 3, 'Performance is excellent, no issues so far.', '2026-10-21 21:48:00'),
(6, 3, 7, 3, 'Great quality, works perfectly and arrived fast.', '2026-10-24 21:09:00'),
(7, 3, 96, 3, 'Exactly as described, very satisfied.', '2026-10-20 13:50:00'),
(8, 4, 15, 4, 'Great quality, works perfectly and arrived fast.', '2026-10-22 19:26:00'),
(9, 5, 16, 5, 'Performance is excellent, no issues so far.', '2026-10-29 08:46:00'),
(10, 6, 76, 4, 'Works great, would buy again.', '2026-10-26 18:06:00'),
(11, 6, 24, 3, 'Decent product but a bit pricey for what it offers.', '2026-10-29 10:04:00'),
(12, 6, 63, 5, 'Decent product but a bit pricey for what it offers.', '2026-10-26 17:33:00'),
(13, 6, 28, 5, 'Works great, would buy again.', '2026-10-21 20:44:00'),
(14, 7, 63, 5, 'Performance is excellent, no issues so far.', '2026-10-26 15:56:00'),
(15, 7, 72, 4, 'Had minor issues but support was helpful.', '2026-10-26 14:46:00'),
(16, 7, 98, 4, 'Decent product but a bit pricey for what it offers.', '2026-10-30 12:23:00'),
(17, 8, 68, 3, 'Good value for the price, highly recommended.', '2026-10-26 09:47:00'),
(18, 10, 82, 3, 'Works great, would buy again.', '2026-10-25 21:42:00'),
(19, 10, 52, 4, 'Packaging was solid and delivery was quick.', '2026-10-25 09:36:00'),
(20, 10, 78, 4, 'Performance is excellent, no issues so far.', '2026-10-21 13:54:00'),
(21, 10, 80, 4, 'Performance is excellent, no issues so far.', '2026-10-26 21:35:00'),
(22, 12, 15, 5, 'Packaging was solid and delivery was quick.', '2026-10-20 10:17:00'),
(23, 12, 29, 4, 'Decent product but a bit pricey for what it offers.', '2026-10-20 10:55:00'),
(24, 12, 83, 5, 'Works great, would buy again.', '2026-10-21 10:47:00'),
(25, 13, 95, 5, 'Performance is excellent, no issues so far.', '2026-10-26 14:29:00'),
(26, 13, 42, 4, 'Packaging was solid and delivery was quick.', '2026-10-31 13:49:00'),
(27, 14, 24, 5, 'Great quality, works perfectly and arrived fast.', '2026-10-30 09:17:00'),
(28, 14, 52, 5, 'Good value for the price, highly recommended.', '2026-10-25 14:07:00'),
(29, 14, 98, 5, 'Had minor issues but support was helpful.', '2026-10-28 18:19:00'),
(30, 15, 53, 4, 'Great quality, works perfectly and arrived fast.', '2026-10-20 11:19:00'),
(31, 15, 4, 3, 'Great quality, works perfectly and arrived fast.', '2026-10-27 19:27:00'),
(32, 15, 60, 4, 'Performance is excellent, no issues so far.', '2026-10-28 16:53:00'),
(33, 16, 71, 4, 'Great quality, works perfectly and arrived fast.', '2026-10-26 08:29:00'),
(34, 17, 5, 3, 'Decent product but a bit pricey for what it offers.', '2026-10-22 20:39:00'),
(35, 17, 76, 4, 'Good value for the price, highly recommended.', '2026-10-23 14:37:00'),
(36, 18, 89, 4, 'Decent product but a bit pricey for what it offers.', '2026-10-23 13:49:00'),
(37, 18, 51, 5, 'Good value for the price, highly recommended.', '2026-10-28 16:12:00'),
(38, 18, 88, 5, 'Exactly as described, very satisfied.', '2026-10-23 09:09:00'),
(39, 19, 4, 3, 'Exactly as described, very satisfied.', '2026-10-30 09:11:00'),
(40, 20, 62, 5, 'Decent product but a bit pricey for what it offers.', '2026-10-22 15:04:00'),
(42, 21, 99, 5, 'welcome', '2026-11-01 12:14:43');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wishlists`
--

INSERT INTO `wishlists` (`id`, `customer_id`) VALUES
(10, 1),
(6, 2),
(8, 4),
(9, 8),
(3, 9),
(7, 11),
(2, 12),
(4, 13),
(1, 15),
(5, 19),
(11, 21);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist_items`
--

CREATE TABLE `wishlist_items` (
  `wishlist_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wishlist_items`
--

INSERT INTO `wishlist_items` (`wishlist_id`, `product_id`) VALUES
(1, 29),
(1, 79),
(1, 83),
(2, 82),
(3, 39),
(3, 53),
(3, 84),
(3, 90),
(4, 18),
(5, 5),
(6, 13),
(6, 15),
(6, 64),
(7, 18),
(7, 69),
(8, 48),
(8, 59),
(8, 86),
(8, 96),
(9, 20),
(9, 76),
(9, 94),
(9, 96),
(10, 13),
(10, 63),
(10, 79),
(10, 84);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_carts_customer` (`customer_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cart_product` (`cart_id`,`product_id`),
  ADD KEY `fk_cart_items_product` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `google_id` (`google_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_customer` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_items_order` (`order_id`),
  ADD KEY `fk_order_items_product` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payments_order` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_products_category` (`category_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_images_product` (`product_id`);

--
-- Indexes for table `product_specs`
--
ALTER TABLE `product_specs`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_customer_product_review` (`customer_id`,`product_id`),
  ADD KEY `fk_reviews_product` (`product_id`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_id` (`customer_id`);

--
-- Indexes for table `wishlist_items`
--
ALTER TABLE `wishlist_items`
  ADD PRIMARY KEY (`wishlist_id`,`product_id`),
  ADD KEY `fk_wishlist_items_product` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `fk_carts_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cart_items_cart` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cart_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_specs`
--
ALTER TABLE `product_specs`
  ADD CONSTRAINT `fk_nexora_product_specs_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `fk_wishlists_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wishlist_items`
--
ALTER TABLE `wishlist_items`
  ADD CONSTRAINT `fk_wishlist_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_wishlist_items_wishlist` FOREIGN KEY (`wishlist_id`) REFERENCES `wishlists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
