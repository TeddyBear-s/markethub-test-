-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 07, 2026 at 01:40 PM
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
-- Database: `markethub`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Komputer'),
(2, 'Handphone'),
(3, 'Dapur'),
(4, 'Fashion'),
(5, 'Hobi');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `shipping_name` varchar(200) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `image`) VALUES
(1, 1, 'Laptop Gaming X1', 'Laptop untuk gaming dan pekerjaan berat', 15000000.00, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8'),
(2, 1, 'Monitor 24 inch', 'Monitor FHD 24\" untuk produktivitas', 1500000.00, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8'),
(3, 1, 'Keyboard Mechanical', 'Keyboard mekanik RGB', 350000.00, 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4'),
(4, 1, 'Mouse Wireless', 'Mouse ergonomis nirkabel', 200000.00, 'https://images.unsplash.com/photo-1512496015851-a90fb38ba796'),
(5, 1, 'RAM 16GB', 'Memory DDR4 3200MHz', 800000.00, 'https://images.unsplash.com/photo-1587202372775-8b9b4a0eeb3b'),
(6, 1, 'SSD 500GB', 'SSD NVMe untuk performa', 900000.00, 'https://images.unsplash.com/photo-1585079540664-6b6d8a5d3a3b'),
(7, 1, 'CPU Cooler', 'Pendingin CPU udara', 250000.00, 'https://images.unsplash.com/photo-1602524819543-8f0b6b2d5fef'),
(8, 1, 'Motherboard ATX', 'Motherboard untuk gaming', 1200000.00, 'https://images.unsplash.com/photo-1518770660439-4636190af475'),
(9, 1, 'GPU RTX 3060', 'Kartu grafis untuk gaming', 5000000.00, 'https://images.unsplash.com/photo-1587202372775-8b9b4a0eeb3b'),
(10, 1, 'Headset Gaming', 'Headset dengan mic', 300000.00, 'https://images.unsplash.com/photo-1499270442801-7a3b7d0b4b08'),
(11, 2, 'Smartphone S20', 'Smartphone dengan kamera unggul', 7000000.00, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9'),
(12, 2, 'Earbuds Wireless', 'Earbuds nirkabel dengan noise cancelling', 500000.00, 'https://images.unsplash.com/photo-1585386959984-a4155223f3c7'),
(13, 2, 'Powerbank 10000mAh', 'Powerbank cepat', 200000.00, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8'),
(14, 2, 'Phone Case', 'Case pelindung silikon', 50000.00, 'https://images.unsplash.com/photo-1536305030016-9a6c2f3f0a99'),
(15, 2, 'Charger Fast', 'Charger USB-C fast charging', 120000.00, 'https://images.unsplash.com/photo-1585386959984-a4155223f3c7'),
(16, 2, 'Screen Protector', 'Tempered glass', 50000.00, 'https://images.unsplash.com/photo-1519183071298-a2962be90b4b'),
(17, 2, 'Smartwatch A1', 'Jam pintar multifungsi', 800000.00, 'https://images.unsplash.com/photo-1517430816045-df4b7de11d1a'),
(18, 2, 'Bluetooth Speaker', 'Speaker portable', 250000.00, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e'),
(19, 2, 'SIM Card Adapter', 'Adapter berbagai ukuran', 25000.00, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8'),
(20, 2, 'Gimbal Phone', 'Stabilizer untuk ponsel', 900000.00, 'https://images.unsplash.com/photo-1518770660439-4636190af475'),
(21, 3, 'Set Pisau Dapur', 'Set pisau stainless untuk kebutuhan dapur', 250000.00, 'https://images.unsplash.com/photo-1511689660979-1b0d5c6a7fbc'),
(22, 3, 'Panci Stainless', 'Panci serbaguna untuk memasak', 350000.00, 'https://images.unsplash.com/photo-1528712306091-ed0763094c98'),
(23, 3, 'Talenan Kayu', 'Talenan bahan kayu', 70000.00, 'https://images.unsplash.com/photo-1504674900247-0877df9cc836'),
(24, 3, 'Mixer Portable', 'Mixer tangan untuk kue', 220000.00, 'https://images.unsplash.com/photo-1532634896-26909d0d64a6'),
(25, 3, 'Rice Cooker', 'Rice cooker 1.8L', 300000.00, 'https://images.unsplash.com/photo-1586201375761-83865001e7b1'),
(26, 3, 'Coffee Maker', 'Pembuat kopi drip', 450000.00, 'https://images.unsplash.com/photo-1509042239860-f550ce710b93'),
(27, 3, 'Set Gelas', 'Set gelas kaca 6pcs', 120000.00, 'https://images.unsplash.com/photo-1504754524776-8f4f37790ca0'),
(28, 3, 'Wajan Teflon', 'Wajan anti lengket', 180000.00, 'https://images.unsplash.com/photo-1547483238-8c6b7efb2b2e'),
(29, 3, 'Blender', 'Blender multifungsi', 320000.00, 'https://images.unsplash.com/photo-1542444459-db0b2a1f0a8d'),
(30, 3, 'Thermos', 'Botol termos 500ml', 90000.00, 'https://images.unsplash.com/photo-1505575967452-1b3d7f8a7e3b'),
(31, 4, 'Kaos Casual', 'Kaos katun nyaman untuk sehari-hari', 120000.00, 'https://images.unsplash.com/photo-1520975912491-8e2d6f0a7d2e'),
(32, 4, 'Jaket Denim', 'Jaket denim stylish', 300000.00, 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f'),
(33, 4, 'Sneakers', 'Sepatu sneakers casual', 350000.00, 'https://images.unsplash.com/photo-1519741491226-4f6f7d6b8b5a'),
(34, 4, 'Topi Baseball', 'Topi gaya kasual', 80000.00, 'https://images.unsplash.com/photo-1503342217505-b0a15d1c1a22'),
(35, 4, 'Kemeja Formal', 'Kemeja kerja slim fit', 220000.00, 'https://images.unsplash.com/photo-1520975912491-8e2d6f0a7d2e'),
(36, 4, 'Dress Casual', 'Dress santai wanita', 250000.00, 'https://images.unsplash.com/photo-1495121605193-b116b5b09f16'),
(37, 4, 'Sabuk Kulit', 'Sabuk kulit asli', 90000.00, 'https://images.unsplash.com/photo-1503341455253-b2e723bb3dbb'),
(38, 4, 'Kacamata', 'Kacamata fashion', 110000.00, 'https://images.unsplash.com/photo-1519741491226-4f6f7d6b8b5a'),
(39, 4, 'Tas Selempang', 'Tas selempang untuk sehari-hari', 180000.00, 'https://images.unsplash.com/photo-1520975912491-8e2d6f0a7d2e'),
(40, 4, 'Sock Premium', 'Kaos kaki nyaman', 30000.00, 'https://images.unsplash.com/photo-1520975912491-8e2d6f0a7d2e'),
(41, 5, 'Drone Mini', 'Drone untuk pemula dengan kamera', 900000.00, 'https://images.unsplash.com/photo-1518779578993-ec3579fee39f'),
(42, 5, 'Kamera Analog', 'Kamera film klasik untuk kolektor', 450000.00, 'https://images.unsplash.com/photo-1519183071298-a2962be90b4b'),
(43, 5, 'Set Cat Air', 'Cat air untuk seni', 120000.00, 'https://images.unsplash.com/photo-1496317899792-9d7dbcd928a1'),
(44, 5, 'Gitar Akustik', 'Gitar untuk pemula', 700000.00, 'https://images.unsplash.com/photo-1506152983158-1f9a6d2b3f2b'),
(45, 5, 'Papan Skate', 'Skateboard deck', 350000.00, 'https://images.unsplash.com/photo-1518600506278-4e8ef466b810'),
(46, 5, 'Kamera Action', 'Action cam 4K', 650000.00, 'https://images.unsplash.com/photo-1518770660439-4636190af475'),
(47, 5, 'Set Lego', 'Mainan susun kreatif', 250000.00, 'https://images.unsplash.com/photo-1541534401786-1b56b2d3d1a7'),
(48, 5, 'Mic Podcast', 'Mikrofon USB untuk podcast', 420000.00, 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4'),
(49, 5, 'Teleskop Mini', 'Teleskop pemula', 500000.00, 'https://images.unsplash.com/photo-1473447197780-8f7e64f6b4d6'),
(50, 5, 'Peralatan Memancing', 'Set pancing lengkap', 200000.00, 'https://images.unsplash.com/photo-1506773090266-9b4f0f2f0c7b');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `birth_date` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
