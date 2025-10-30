-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2025 at 07:43 AM
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
-- Database: `ecommerce_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `boys_product`
--

CREATE TABLE `boys_product` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `boys_product`
--

INSERT INTO `boys_product` (`id`, `name`, `price`, `image_path`, `status`, `created_at`) VALUES
(8, 'VERSACE', 1511.00, 'uploads/t shrt 22.jpg', 'inactive', '2025-09-24 04:44:39'),
(19, 't shirt', 1200.00, 'uploads/1.jpeg', 'active', '2025-09-24 04:44:39'),
(21, 'Shirt', 800.00, 'uploads/shrt3.jpeg', 'active', '2025-09-24 04:44:39'),
(22, 'Shirt', 300.00, 'uploads/shrt4.jpeg', 'active', '2025-09-24 04:44:39'),
(23, 'Shirt check', 350.00, 'uploads/shrt5.jpeg', 'active', '2025-09-24 04:44:39'),
(24, 'Shirt', 730.00, 'uploads/shrt6.jpeg', 'active', '2025-09-24 04:44:39'),
(25, 'Shirt ', 600.00, 'uploads/sht2.jpeg', 'active', '2025-09-24 04:44:39'),
(26, 'Shirt', 1349.00, 'uploads/sht3.jpeg', 'active', '2025-09-24 04:44:39'),
(27, 'T Shirt white', 299.00, 'uploads/122.jpg', 'active', '2025-09-24 04:44:39'),
(28, 'T-Shirt', 500.00, 'uploads/t shrt2.jpeg', 'active', '2025-09-24 04:44:39'),
(30, 'T-Shirt', 399.00, 'uploads/t shrt3.jpeg', 'active', '2025-09-24 04:44:39'),
(31, 'T-Shirt', 459.00, 'uploads/t sh1.jpeg', 'active', '2025-09-24 04:44:39'),
(32, 'Combo dress party yer', 1299.00, 'uploads/combo dress.jpeg', 'active', '2025-09-24 04:44:39'),
(33, 'T Shirt skay', 350.00, 'uploads/t.jpeg', 'active', '2025-09-24 04:44:39'),
(34, 'T Shirt blue sports ', 420.00, 'uploads/t shrt4.jpeg', 'active', '2025-09-24 04:44:39'),
(35, 'Billie Eilish T Shirt', 2599.00, 'uploads/1download.webp', 'active', '2025-09-24 04:44:39'),
(36, 'VERSACE T Shirt', 5899.00, 'uploads/s-815052_0-versace-sweaters-long-sleeved-o-neck-for-men.jpg', 'active', '2025-09-24 04:44:39'),
(38, 'Red T-Shirt', 1390.00, 'uploads/2.jpeg', 'active', '2025-09-24 04:44:39'),
(39, 'Formal White Shirt', 899.00, 'uploads/shirt OPAC.jpeg', 'active', '2025-09-24 04:44:39'),
(40, 'Black Hoodie', 1350.00, 'uploads/hoodi bl OPAC.jpeg', 'active', '2025-09-24 04:44:39'),
(41, 'Green Casual Pants', 1249.00, 'uploads/pants th.jpeg', 'active', '2025-09-24 04:44:39'),
(42, 'Blue Sports Shoes', 899.00, 'uploads/shoes th.jpeg', 'active', '2025-09-24 04:44:39'),
(43, 'Party Suit Set With Tie  S 4 Piece ', 2899.00, 'uploads/party suit OPAC.jpeg', 'active', '2025-09-24 04:44:39'),
(44, 'Mens Blazer green', 5199.00, 'uploads/blezer OPAC.jpeg', 'active', '2025-09-24 04:44:39'),
(45, 'Blue Denim Jacket', 1499.00, 'uploads/1.jpeg', 'active', '2025-09-24 04:44:39'),
(55, 'shirt check red', 449.00, 'uploads/1758609051_shirt 1.jpeg', 'active', '2025-09-24 04:44:39');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` varchar(255) NOT NULL,
  `location` varchar(100) NOT NULL,
  `product_id` int(11) NOT NULL,
  `category` varchar(20) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `customer_email`, `customer_name`, `phone`, `address`, `location`, `product_id`, `category`, `price`, `quantity`, `added_on`) VALUES
(3, 'jswebdev29@gmail.com', 'jaswinder singh', '8728906013', 'fatehgarh panjtoor', 'moga', 55, 'boys', 1397.00, 3, '2025-10-11 06:08:02'),
(4, 'jswebdev29@gmail.com', 'jaswinder singh', '8728906013', 'fatehgarh panjtoor', 'moga', 45, 'boys', 1499.00, 2, '2025-10-11 06:08:11'),
(5, 'jswebdev29@gmail.com', 'jaswinder singh', '8728906013', 'fatehgarh panjtoor', 'moga', 2, 'girls', 212.00, 1, '2025-10-11 06:24:19');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` varchar(255) NOT NULL,
  `location` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `address`, `location`, `email`, `password`, `created_at`) VALUES
(1, 'jaswinder singh', '8728906013', 'fatehgarh panjtoor', 'moga', 'jswebdev29@gmail.com', '$2y$10$BQHyO5a6mTTVz9ufnHG1zuT.0szOCGx0Kuiod1LKWFXGVR1.NDkWK', '2025-10-11 05:59:58');

-- --------------------------------------------------------

--
-- Table structure for table `girls_product`
--

CREATE TABLE `girls_product` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `girls_product`
--

INSERT INTO `girls_product` (`id`, `name`, `price`, `image_path`, `status`, `created_at`) VALUES
(3, 'Green Embroidered Viscose Kurta Set', 1999.00, 'uploads/1760715523_1OPAC.jpeg', 'active', '2025-10-17 15:38:43'),
(4, '3Biba Girls Floral Printed Kurta and Palazzo  Dupatta Suit Set', 2932.00, 'uploads/1760716172_3Biba Girls Floral Printed Kurta and Palazzo & Dupatta Suit Set.webp', 'active', '2025-10-17 15:49:32'),
(5, '1 Bitiya by Bhama Girls Ready to Wear Lehenga  Choli by Myntra', 1250.00, 'uploads/1760716377_1 Bitiya by Bhama Girls Ready to Wear Lehenga & Choli by Myntra.webp', 'active', '2025-10-17 15:52:57'),
(6, 'Lil Drama Bollywood Tadka Orangza Ghagra with Mokesh work and Embroidery', 1426.00, 'uploads/1760716589_Lil Drama Bollywood Tadka Orangza Ghagra with Mokesh work and Embroidery.webp', 'active', '2025-10-17 15:56:29'),
(7, 'Navratri Frock', 1100.00, 'uploads/1760716620_Navratri Frock.webp', 'active', '2025-10-17 15:57:00'),
(8, 'shoppinJBN Creation Yell Three Fourth Sleeve Threadwork Embroidered Kurta', 1489.00, 'uploads/1760716654_shoppinJBN Creation Yell Three Fourth Sleeve Threadwork Embroidered Kurta.webp', 'active', '2025-10-17 15:57:34');

-- --------------------------------------------------------

--
-- Table structure for table `login_owner`
--

CREATE TABLE `login_owner` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `dob` date DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` int(11) DEFAULT NULL,
  `security_answer` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_owner`
--

INSERT INTO `login_owner` (`id`, `username`, `password`, `dob`, `phone`, `email`, `reset_token`, `token_expiry`, `security_answer`) VALUES
(1, 'jaswinder', '123', '2002-02-24', '8728906012', 'jaswindersingh295111@gmail.com', 'a9686178a7cd07739f735e0c36af434f', 1761391549, 'moga');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `boys_product`
--
ALTER TABLE `boys_product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `girls_product`
--
ALTER TABLE `girls_product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_owner`
--
ALTER TABLE `login_owner`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `boys_product`
--
ALTER TABLE `boys_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `girls_product`
--
ALTER TABLE `girls_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `login_owner`
--
ALTER TABLE `login_owner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
