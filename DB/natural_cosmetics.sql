-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 06, 2026 at 12:54 AM
-- Server version: 8.4.11-cll-lve
-- PHP Version: 8.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `natural_cosmetics`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `added_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `session_id`, `product_id`, `quantity`, `added_at`) VALUES
(13, NULL, 'drcgf3v0d3493u9rre65t3is1m', 9, 1, '2026-01-08 19:42:36'),
(17, NULL, 'b57vomvuhn0uqd8a40up1vf4jt', 7, 1, '2026-01-08 20:04:00'),
(21, NULL, '697b65d8257c94cefa22a7aa4ca5b2cc', 10, 2, '2026-02-02 18:50:24'),
(22, NULL, '697b65d8257c94cefa22a7aa4ca5b2cc', 9, 1, '2026-02-02 18:50:44'),
(24, NULL, 'de6dd458a76ae6cff912016c3d935e9d', 10, 1, '2026-02-10 16:01:19'),
(26, NULL, '681901f635c7abe3e493c17d747dd374', 8, 1, '2026-02-16 13:28:09'),
(30, NULL, '30f97024a1973c0bf77575a539de4d47', 15, 1, '2026-02-20 10:28:25'),
(33, NULL, '0d19ddf835d6c5e90a9730d05279313d', 25, 2, '2026-07-16 18:29:46'),
(35, NULL, 'aa314888aa3e27c62c51f1d543e806b3', 41, 1, '2026-10-14 19:42:51'),
(36, NULL, '90d1f0500a7dd417432a0d35a714f1a3', 31, 4, '2026-10-24 01:24:28'),
(37, 1, NULL, 37, 1, '2026-11-05 15:51:48'),
(38, 1, NULL, 39, 2, '2026-11-05 15:51:55');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Face Care', 'Natural face care products including cleansers, moisturizers, and serums', '2026-01-07 16:16:14'),
(2, 'Body Care', 'Natural body care products including lotions, scrubs, and oils', '2026-01-07 16:16:14'),
(3, 'Hair Care', 'Natural hair care products including shampoos, conditioners, and treatments', '2026-01-07 16:16:14'),
(4, 'Makeup', 'Natural and organic makeup products', '2026-01-07 16:16:14'),
(5, 'Novel Perfume', 'Novel perfume: elegant  Light, natural fragrance made from botanical extracts.', '2026-04-02 07:34:29'),
(6, 'Baby Products', 'for organic products for baby care.', '2026-04-02 07:41:17'),
(7, 'Moisturizer', 'Moisturizer', '2026-04-09 20:01:06'),
(8, 'Cleanser', 'Cleanser', '2026-04-09 20:01:17'),
(9, 'Lip Balm', 'Lip Balm', '2026-04-09 20:01:25'),
(10, 'Face Serum', 'Face Serum', '2026-04-09 20:01:45'),
(11, 'Whitening Cream', 'Whitening Cream', '2026-04-09 20:05:43'),
(12, 'Ayurvedic oil', 'Ayurvedic oil', '2026-04-09 20:13:28'),
(13, 'Soap', 'soap', '2026-04-09 20:17:12');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `price` decimal(10,2) NOT NULL,
  `category_id` int DEFAULT NULL,
  `image_url` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `stock_quantity` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `category_id`, `image_url`, `stock_quantity`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Organic Face Cleanser', 'Gentle organic face cleanser with natural botanicals', 24.99, 1, 'face_cleanser.jpg', 50, 0, '2026-01-07 16:16:14', '2026-01-07 18:58:41'),
(2, 'Hydrating Face Serum', 'Vitamin C rich face serum for glowing skin', 34.99, 1, 'face_serum.jpg', 29, 0, '2026-01-07 16:16:14', '2026-01-07 20:49:28'),
(3, 'Coconut Body Lotion', 'Deep moisturizing body lotion with organic coconut oil', 19.99, 2, 'body_lotion.jpg', 39, 0, '2026-01-07 16:16:14', '2026-01-07 20:49:29'),
(4, 'Shea Butter Body Scrub', 'Exfoliating body scrub with natural shea butter', 26.99, 2, 'body_scrub.jpg', 35, 0, '2026-01-07 16:16:14', '2026-01-07 20:49:31'),
(5, 'Argan Oil Shampoo', 'Nourishing shampoo with organic argan oil', 22.99, 3, 'shampoo.jpg', 45, 0, '2026-01-07 16:16:14', '2026-01-07 20:49:33'),
(6, 'Natural Lipstick', 'Pigmented natural lipstick with organic ingredients', 18.99, 4, 'lipstick.jpg', 60, 0, '2026-01-07 16:16:14', '2026-01-07 20:49:35'),
(7, 'qwqw', 'SASASAS', 54544.00, 2, '1767819650_hero_image_modern_cosmetics_1767807439554.png', 2, 0, '2026-01-07 21:00:50', '2026-02-16 18:42:23'),
(8, 'eqrwe', '12122121awcw', 12121.00, 1, '1767869945_222111.png', 211, 0, '2026-01-08 10:59:05', '2026-02-16 18:42:22'),
(9, 'wqeqwe', '1212adafdaf', 21.00, 4, '1767869959_444.png', 2122, 0, '2026-01-08 10:59:19', '2026-02-16 18:42:21'),
(10, 'test', 'test', 3221.00, 2, '1769521005_joker-meaningless-life-5k-if.jpg', 10000, 0, '2026-01-27 13:36:46', '2026-02-16 18:42:19'),
(11, 'Natural body lotion', 'Indulge your skin in pure, plant-powered hydration with our Hand & Body Lotion. Crafted to soothe and moisturize without synthetic fragrances or harsh chemicals, this lotion is a must-have for anyone seeking natural skincare. Formulated with an exquisite blend of pure Shea, Cocoa, and Tucuma Butters, it delivers rich hydration and lasting softness. Its delightfully light and velvety consistency absorbs quickly, providing immediate replenishment with no greasy residue. The result? Smooth, soft skin that feels naturally restored and refreshed. Infused with 100% pure, therapeutic-grade essential oils, it offers a gentle aromatherapy experience to elevate your everyday routine. Suitable for all skin types, this lotion works to naturally relieve roughness and dryness, leaving your skin feeling', 1599.99, 2, '1771269475_body lotion.jpg', 10, 1, '2026-02-16 19:17:55', '2026-02-16 19:18:00'),
(12, 'Calming Body Lotion (Lavender & Chamomile)', 'Housed in a soft sage-green bottle with a natural wood-finish pump.\r\n\r\nInfused with the soothing scents of lavender and chamomile to promote relaxation.\r\n\r\nArtfully displayed alongside fresh sprigs of lavender and a warm, glowing candle.', 1499.99, 2, '1771270879_4.png', 5, 1, '2026-02-16 19:40:16', '2026-02-16 19:41:19'),
(13, 'Revitalizing Body Lotion (Citrus & Aloe)', 'Presented in a rich amber bottle designed to protect its botanical ingredients.\r\n\r\nFormulated with citrus and aloe to provide an energizing and refreshing skin boost.\r\n\r\nSurrounded by vibrant orange slices and fresh mint leaves to highlight its zesty profile.', 1200.00, 2, '1771270935_3.png', 12, 1, '2026-02-16 19:42:15', '2026-02-16 19:42:15'),
(14, 'Nourishing Body Lotion (Coconut & Shea)', 'Packaged in a clean, minimalist white bottle that reflects its pure, creamy texture.\r\n\r\nCombines coconut and shea butter for deep hydration and long-lasting smoothness.\r\n\r\nSet against a backdrop of cracked coconuts and shea nuts to emphasize its moisture-rich formula.', 1540.00, 2, '1771270977_2.png', 20, 1, '2026-02-16 19:42:57', '2026-02-16 19:42:57'),
(15, 'Rose Glow Body Scrub', 'The Rose Glow Body Scrub is presented in a clear glass jar topped with a natural bamboo lid, showcasing its delicate pink crystalline texture. This formula is specifically designed to brighten and smooth the skin, using floral botanicals to enhance a natural radiance. It is elegantly staged among soft pink rose petals and a flickering candle, emphasizing a luxurious and romantic self-care experience.', 1540.00, 2, '1771272756_scrb 3.png', 12, 1, '2026-02-16 20:12:13', '2026-02-16 20:12:36'),
(16, 'Hair oil', 'best for growing hair and hair thickness', 759.00, 3, '1774813962_hair oil.jpeg', 25, 0, '2026-03-29 19:52:42', '2026-04-16 14:00:35'),
(17, 'werwr', 'klnsdfjrkrr', 33434.00, 3, '1775114959_WhatsApp Image 2026-03-25 at 9.06.25 PM.jpeg', 34, 0, '2026-04-02 07:29:19', '2026-04-02 07:29:26'),
(18, 'Rabbit Hair Oil', 'Nourishes hair naturally, promotes healthy growth, and strengthens roots.', 1850.00, 3, '1775115018_Rabbit Hair Oil.jpeg', 50, 1, '2026-04-02 07:30:18', '2026-04-16 14:21:17'),
(19, 'Perfume', 'Light, natural fragrance made from botanical extracts', 899.00, 5, '1775115305_WhatsApp Image 2026-03-25 at 9.06.29 PM.jpeg', 20, 1, '2026-04-02 07:35:05', '2026-04-02 07:35:14'),
(20, 'Herbal Hair Oil', 'Blended with herbs to reduce hair fall and improve scalp health.', 500.00, 3, '1775115542_WhatsApp Image 2026-03-25 at 9.06.51 PM.jpeg', 50, 1, '2026-04-02 07:37:16', '2026-04-16 14:20:58'),
(21, 'Aloe Vera Body Lotion', 'Deeply moisturizes and soothes skin with pure aloe vera.', 1400.00, 2, '1775115506_WhatsApp Image 2026-03-25 at 9.06.49 PM.jpeg', 50, 1, '2026-04-02 07:38:26', '2026-04-16 14:20:20'),
(22, 'Hair Growth Oil', 'Nourishes hair naturally, promotes healthy growth, and strengthens roots.', 899.00, 3, '1775115609_WhatsApp Image 2026-03-25 at 9.06.51 PM.jpeg', 20, 0, '2026-04-02 07:40:09', '2026-04-16 14:01:58'),
(23, 'Baby Massage Oil', 'A traditional Ayurvedic formula for baby’s gentle skin, helps strengthen bones and relax muscles.', 2800.00, 6, '1775115741_WhatsApp Image 2026-03-25 at 9.06.53 PM.jpeg', 50, 1, '2026-04-02 07:42:21', '2026-04-16 14:20:02'),
(24, 'NOVEL  DAY MOISTURIZED', 'A lightweight, non-greasy day moisturizer enriched with nourishing ingredients to keep your skin hydrated and soft throughout the day. Ideal for daily use. Helps maintain natural skin moisture balance and provides a smooth base for makeup.', 1800.00, 7, '1775765067_WhatsApp Image 2026-04-09 at 7.51.04 PM.jpeg', 20, 1, '2026-04-09 20:04:27', '2026-04-16 14:19:42'),
(25, 'NOVEL VIP CREAM', 'VIP CREAM is a premium formulation designed to support skin brightening and even tone. Regular use helps reduce the appearance of dark spots and dullness, leaving skin looking radiant and healthy.', 1800.00, 11, '1775765222_WhatsApp Image 2026-04-09 at 7.51.05 PM.jpeg', 50, 1, '2026-04-09 20:07:02', '2026-04-16 14:19:22'),
(26, 'NOVEL CAROTONE BODY CREAM', 'NOVEL CAROTONE Natural Glove Whitening Body Cream helps brighten and even out skin tone on hands and body. Enriched with natural actives, it moisturizes deeply while working to reduce tan and dark patches for a naturally glowing look.', 1950.00, 11, '1775765366_WhatsApp Image 2026-04-09 at 7.51.06 PM.jpeg', 50, 1, '2026-04-09 20:09:26', '2026-04-16 14:18:47'),
(27, 'NOYEL DAY MOISTURIZED CREAM', 'A rich yet fast-absorbing day cream infused with jojoba oil and other skin-friendly ingredients. Provides long-lasting hydration, softens rough patches, and helps protect skin from daily environmental stress.', 2700.00, 7, '1775765423_WhatsApp Image 2026-04-09 at 7.51.07 PM (1).jpeg', 50, 1, '2026-04-09 20:10:23', '2026-04-16 14:18:19'),
(28, 'Ayurvedic & Natural Facial CLEANSERS', 'A gentle, soap-free facial cleanser made with Ayurvedic and natural ingredients. Removes dirt, oil, and impurities without stripping the skin\'s natural moisture. Suitable for all skin types, including sensitive skin.', 750.00, 8, '1775765466_WhatsApp Image 2026-04-09 at 7.51.07 PM.jpeg', 50, 1, '2026-04-09 20:11:06', '2026-04-16 14:17:02'),
(29, 'Novel Natural Rosehip Lip Balm', 'Enriched with rosehip oil and natural emollients, this lip balm deeply nourishes and repairs dry, chapped lips. Regular use leaves lips soft, smooth, and protected. Free from harsh chemicals.', 1200.00, 9, '1775765503_WhatsApp Image 2026-04-09 at 7.51.09 PM.jpeg', 54, 1, '2026-04-09 20:11:43', '2026-04-16 14:16:36'),
(30, 'Kumkumadi Tailam', 'A traditional Ayurvedic oil formulation known for its skin-brightening and healing properties. Kumadi Tail helps reduce scars, blemishes, and pigmentation, while promoting an even, radiant complexion. Best used as a night serum or spot treatment.', 1300.00, 12, '1775765641_WhatsApp Image 2026-04-09 at 7.51.10 PM (1).jpeg', 50, 1, '2026-04-09 20:14:01', '2026-04-16 14:15:43'),
(31, 'NOVEL NIACINAMIDE FACE SERUM', 'A powerful face serum formulated with Niacinamide (Vitamin B3) to control oil, minimize pores, and improve uneven skin tone. Paired with a soothing hydrosol, this set helps calm irritation and boost hydration for a healthy, balanced complexion.', 2100.00, 10, '1775765689_WhatsApp Image 2026-04-09 at 7.51.10 PM.jpeg', 50, 1, '2026-04-09 20:14:49', '2026-04-16 14:15:25'),
(32, 'DARK CIRCLE REMOVAL EYE SERUM', 'Specially formulated under-eye serum to target dark circles, puffiness, and fine lines. Lightweight and fast-absorbing, it helps brighten the under-eye area and restore a refreshed, youthful look. For best results, use twice daily', 1200.00, 1, '1775765724_WhatsApp Image 2026-04-09 at 7.51.11 PM.jpeg', 50, 1, '2026-04-09 20:15:24', '2026-04-16 14:15:04'),
(33, 'NOVEL ROSE HYDROGEL TONER', 'A refreshing hydrogel toner infused with rose extracts. Helps balance skin pH, tighten pores, and provide an instant burst of hydration. Suitable for all skin types, including oily and sensitive skin. Alcohol-free and gentle enough for daily use. (100ml)', 1200.00, 12, '1775765774_WhatsApp Image 2026-04-09 at 7.51.12 PM.jpeg', 48, 1, '2026-04-09 20:16:14', '2026-04-16 14:14:46'),
(34, 'Manjistha SOAP', 'A luxurious bath soap enriched with mangosteen extract, known for its antioxidant and skin-nourishing properties. Gently cleanses, refreshes, and helps maintain healthy-looking skin. Leaves a light, natural fragrance.', 650.00, 13, '1775765860_WhatsApp Image 2026-04-09 at 7.51.13 PM (1).jpeg', 116, 1, '2026-04-09 20:17:40', '2026-04-16 14:14:02'),
(35, 'Baby glow soap', 'A luxurious bath soap enriched with mangosteen extract, known for its antioxidant and skin-nourishing properties. Gently cleanses, refreshes, and helps maintain healthy-looking skin. Leaves a light, natural fragrance.', 1199.96, 13, '1775765913_WhatsApp Image 2026-04-09 at 7.51.13 PM.jpeg', 120, 1, '2026-04-09 20:18:33', '2026-04-16 14:13:33'),
(36, 'VIP Glow Soap', 'A luxurious bath soap enriched with mangosteen extract, known for its antioxidant and skin-nourishing properties. Gently cleanses, refreshes, and helps maintain healthy-looking skin. Leaves a light, natural fragrance.', 900.00, 13, '1775765955_WhatsApp Image 2026-04-09 at 7.51.14 PM.jpeg', 100, 1, '2026-04-09 20:19:15', '2026-04-16 14:13:14'),
(37, 'Aloe Vera Soap', 'Natural soap is a biodegradable, skin-friendly cleanser made from natural fats/oils  typically produced via traditional cold-process methods.', 650.00, 13, '1776348322_WhatsApp Image 2026-04-16 at 7.19.43 PM.jpeg', 30, 1, '2026-04-16 14:05:22', '2026-04-16 14:07:15'),
(38, 'Avarampoo Soap', 'Natural soap is a biodegradable, skin-friendly cleanser made from natural fats/oils and lye, typically produced via traditional cold-process methods.', 600.00, 13, '1776348419_WhatsApp Image 2026-04-16 at 7.19.44 PM.jpeg', 33, 1, '2026-04-16 14:06:59', '2026-04-16 14:06:59'),
(39, 'Charcoal Soap', 'Natural soap is a biodegradable, skin-friendly cleanser made from natural  typically produced via traditional cold-process methods.', 600.00, 13, '1776348497_WhatsApp Image 2026-04-16 at 7.19.46 PM (1).jpeg', 35, 1, '2026-04-16 14:08:17', '2026-04-16 14:08:17'),
(40, 'Neem Oil Soap', 'Natural soap is a biodegradable, skin-friendly cleanser made from natural typically produced via traditional cold-process methods.', 600.00, 13, '1776348579_WhatsApp Image 2026-04-16 at 7.19.47 PM.jpeg', 110, 1, '2026-04-16 14:09:39', '2026-04-16 14:13:49'),
(41, 'Cassia Alata Soap', 'Natural soap is a biodegradable, skin-friendly cleanser made from natural  produced via traditional cold-process methods.', 600.00, 13, '1776348645_WhatsApp Image 2026-04-16 at 7.19.46 PM.jpeg', 100, 1, '2026-04-16 14:10:45', '2026-04-16 14:13:41');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `city` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `state` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `zip_code` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `country` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_type` enum('customer','admin') COLLATE utf8mb4_general_ci DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `first_name`, `last_name`, `phone`, `address`, `city`, `state`, `zip_code`, `country`, `user_type`, `created_at`, `updated_at`) VALUES
(1, 'Rislan', 'rislan', '$2y$10$LQE9JrZpAWzmUiC0Zo0nEe9YNgWj1F0L0.8UJ9oQrfD/RVSED/7H.', 'Rislan', 'mohamed', NULL, NULL, NULL, NULL, NULL, NULL, 'admin', '2026-01-07 16:28:04', '2026-11-05 15:49:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

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
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
