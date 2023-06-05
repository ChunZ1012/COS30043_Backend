-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 04, 2023 at 04:22 AM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cos30043`
--
CREATE DATABASE IF NOT EXISTS `cos30043` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `cos30043`;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `product_variant_id` int(11) NOT NULL,
  `product_variant_qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `carts`
--

TRUNCATE TABLE `carts`;
--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `cart_id`, `user_id`, `product_variant_id`, `product_variant_qty`) VALUES
(91, 91, 1, 7, 5),
(92, 92, 1, 11, 1),
(94, 93, 1, 9, 1),
(96, 96, 1, 24, 1),
(99, 97, 1, 22, 2),
(100, 100, 1, 17, 1);

--
-- Triggers `carts`
--
DROP TRIGGER IF EXISTS `TRIGGER_INSERT_CART_ID`;
DELIMITER $$
CREATE TRIGGER `TRIGGER_INSERT_CART_ID` BEFORE INSERT ON `carts` FOR EACH ROW SET New.cart_id = (SELECT IFNULL(MAX(id), 0) + 1 FROM carts)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `order_guid` varchar(36) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `order_delivery_name` varchar(64) NOT NULL,
  `order_delivery_contact` varchar(12) NOT NULL,
  `order_delivery_email` varchar(80) NOT NULL,
  `order_delivery_address` mediumtext NOT NULL,
  `order_delivery_address_2` mediumtext NOT NULL,
  `order_created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_status` int(1) NOT NULL DEFAULT 0 COMMENT '0: Pending,\r\n1: Paid\r\n2: Picked\r\n3: Shipped',
  `order_is_cancelled` tinyint(1) NOT NULL DEFAULT 0,
  `order_cancelled_reason` text DEFAULT NULL,
  `order_cancelled_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `orders`
--

TRUNCATE TABLE `orders`;
--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_id`, `order_guid`, `user_id`, `order_delivery_name`, `order_delivery_contact`, `order_delivery_email`, `order_delivery_address`, `order_delivery_address_2`, `order_created_at`, `order_status`, `order_is_cancelled`, `order_cancelled_reason`, `order_cancelled_at`) VALUES
(2, 1, 'ba7b569e-f388-11ed-9fa5-7efa6c2d9e92', 1, 'Jone Doe', '0123365987', 'example@example.com', 'Block A Unit 920 9th Floor 1 Jalan SS 20/27 Damansara Intan, Petaling Jaya, 47400, Selangor, Malaysia', '', '2023-05-14 16:00:00', 3, 0, NULL, '2023-05-29 03:20:42'),
(50, 3, '1a97b314-f6de-11ed-9d53-9c4ba558caed', 1, '', '', '', '', '', '2023-05-20 07:15:30', 1, 0, NULL, NULL),
(63, 52, '8a67de60-f717-11ed-baf6-cc52371ff367', 1, 'John Kawn', '016-99271562', 'john@example.t.com', 'Block C, King\'s Centre, Jalan Simpang 3', '93300 Kuching, Sarawak', '2023-05-17 07:23:54', 2, 0, NULL, '2023-05-29 03:20:46'),
(65, 65, '2c31a2e8-f71b-11ed-baf6-cc52371ff367', 1, 'afdas', '123123', '2@2.com', 'adasd', 'asdasd', '2023-05-20 14:32:39', 3, 0, NULL, '2023-05-29 03:20:44'),
(66, 66, '40e69ef7-f71b-11ed-baf6-cc52371ff367', 1, 'Joe Dohn', '012-99927167', 'example@example.com', 'Lot 12, Jalan Batu Kawa', '93250 Kuching, Sarawak', '2023-05-20 14:33:14', 2, 0, NULL, NULL),
(68, 68, '0d280a7c-faca-11ed-8f7a-e2e6d98f3d86', 1, '123', '1234568', 'q@q.com', '123', '123', '2023-05-25 07:02:02', 2, 0, NULL, '2023-05-25 07:02:36'),
(71, 71, '26a5e06e-ff60-11ed-8a1e-d2a3b7aa32f9', 1, 'John Doe', '018-9924561', 'example@example.com', '4 Luh Satu Kaw Indust, Bdr Sultan Suleiman Pelabuhan Pelabuhan', '42000 Klang, Selangor', '2023-05-31 03:06:34', 2, 0, NULL, '2023-05-31 03:33:14'),
(82, 72, '42ff3c14-ff92-11ed-9e24-e7313fa991da', 1, 'Johanna', '012-9928264', 't@v.c', 'No. 5 Jalan SS 21/39, Damansara Uptown', '47400 Petaling Jaya, Selangor', '2023-05-31 09:05:17', 1, 0, NULL, '2023-05-31 09:12:00'),
(83, 83, '6a4665f9-ff94-11ed-9e24-e7313fa991da', 1, 'Johanna', '012-9928264', 't@v.c', 'No. 5 Jalan SS 21/39, Damansara Uptown', '47400 Petaling Jaya, Selangor', '2023-05-31 09:20:42', 1, 0, NULL, NULL),
(85, 85, 'cc721b59-0145-11ee-9d01-526c267bf45a', 1, 'John Doe', '018-9924561', 'example@example.com', '4 Luh Satu Kaw Indust, Bdr Sultan Suleiman Pelabuhan Pelabuhan', '42000 Klang, Selangor', '2023-06-02 13:02:58', 1, 0, NULL, NULL),
(86, 86, '64e4f3a9-0148-11ee-9d01-526c267bf45a', 1, '', '', '', '', '', '2023-06-02 13:21:33', 0, 0, NULL, NULL);

--
-- Triggers `orders`
--
DROP TRIGGER IF EXISTS `TRIGGER_INSERT_ORDER_ID_GUID`;
DELIMITER $$
CREATE TRIGGER `TRIGGER_INSERT_ORDER_ID_GUID` BEFORE INSERT ON `orders` FOR EACH ROW SET New.order_id = (SELECT IFNULL(MAX(id), 0) + 1 FROM orders) ,New.order_guid = (SELECT UUID())
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `orders_delivery_log`
--

DROP TABLE IF EXISTS `orders_delivery_log`;
CREATE TABLE `orders_delivery_log` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_log_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `order_remark` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `orders_delivery_log`
--

TRUNCATE TABLE `orders_delivery_log`;
--
-- Dumping data for table `orders_delivery_log`
--

INSERT INTO `orders_delivery_log` (`id`, `order_id`, `order_log_time`, `order_remark`) VALUES
(1, 1, '2023-05-19 02:32:53', 'Picked by XXX Couriers'),
(2, 1, '2023-05-22 01:02:11', 'Shipping'),
(3, 1, '2023-05-24 06:53:31', 'Shipped'),
(4, 52, '2023-05-18 03:51:19', 'Picked by XYZ Couriers');

-- --------------------------------------------------------

--
-- Table structure for table `orders_detail`
--

DROP TABLE IF EXISTS `orders_detail`;
CREATE TABLE `orders_detail` (
  `id` int(11) NOT NULL,
  `order_detail_id` int(11) DEFAULT NULL,
  `order_id` int(11) NOT NULL,
  `order_product_variant_id` int(11) NOT NULL,
  `order_qty` int(11) NOT NULL,
  `order_price` decimal(10,2) NOT NULL,
  `order_discount` tinyint(1) DEFAULT 0,
  `order_discount_amt` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `orders_detail`
--

TRUNCATE TABLE `orders_detail`;
--
-- Dumping data for table `orders_detail`
--

INSERT INTO `orders_detail` (`id`, `order_detail_id`, `order_id`, `order_product_variant_id`, `order_qty`, `order_price`, `order_discount`, `order_discount_amt`) VALUES
(3, 1, 1, 21, 1, '64.00', 1, '5.00'),
(4, 4, 1, 6, 1, '169.00', 1, '33.80'),
(5, 5, 1, 14, 1, '79.00', 1, '5.00'),
(6, 6, 1, 11, 1, '1799.00', 0, '400.00'),
(42, 7, 3, 22, 1, '39.00', 0, '0.00'),
(55, 44, 52, 11, 1, '1799.00', 1, '400.00'),
(57, 57, 65, 9, 1, '110.00', 0, '0.00'),
(58, 58, 66, 22, 1, '39.00', 0, '0.00'),
(60, 60, 68, 24, 1, '200.00', 0, '0.00'),
(64, 64, 71, 22, 1, '39.00', 0, '0.00'),
(68, 65, 72, 9, 1, '110.00', 0, '0.00'),
(69, 69, 83, 23, 2, '69.90', 0, '0.00'),
(71, 71, 85, 17, 1, '239.99', 1, '9.99'),
(72, 72, 86, 18, 1, '64.00', 1, '5.00');

--
-- Triggers `orders_detail`
--
DROP TRIGGER IF EXISTS `TRIGGER_INSERT_ORDER_DETAIL_ID`;
DELIMITER $$
CREATE TRIGGER `TRIGGER_INSERT_ORDER_DETAIL_ID` BEFORE INSERT ON `orders_detail` FOR EACH ROW SET New.order_detail_id = (SELECT IFNULL(MAX(id), 0) + 1 FROM orders_detail)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(128) NOT NULL,
  `product_desc` longtext DEFAULT NULL,
  `product_type` varchar(16) NOT NULL,
  `product_catg` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `products`
--

TRUNCATE TABLE `products`;
--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_id`, `product_name`, `product_desc`, `product_type`, `product_catg`) VALUES
(4, 4, 'Galaxy S23 Ultra Silicone Grip Case', '<h4>Easy to hold, hard to drop.</h4>\n<p>The sturdy strap on the back of the case will give you an easy hold of your phone, while allowing you to do more with less chance of dropping it.</p>\n<h4>Smooth grip for a comfortable hold.</h4>\n<p>Along with the trendy strap, the silicone case has a smooth, silky texture that not only is soft to the touch but also provides a comfortable grip for hours.</p>\n<h4>Customize your strap to your taste.</h4>\n<p>There is a variety of designs to choose from. Have fun switching the strap to give the case a different look.</p>\n<h4>We turn waste into valuable resources.</h4>\n<p>While protecting your phone, you are also saving the environment. You can help preserve the Earth\'s precious resources by purchasing our product made with UL-certified recycled materials.</p>', 'promotion', 'pc'),
(5, 5, 'MagSafe Charger', '<p>The MagSafe Charger makes wireless charging a snap.</p>\n<p>The perfectly aligned magnets attach to your iPhone 14, iPhone 14 Pro, iPhone 13, iPhone 13 Pro, iPhone 12, and iPhone 12 Pro and provide faster wireless charging up to 15W.</p>\n<p>The MagSafe Charger maintains compatibility with Qi charging, so it can be used to wirelessly charge your iPhone 8 or later, as well as AirPods models with a wireless charging case, as you would with any Qi-certified charger.</p>\n<p>The magnetic alignment experience only applies to iPhone 14, iPhone 14 Pro, iPhone 13, iPhone 13 Pro, iPhone 12 and iPhone 12 Pro models.</p>', 'new-arrivals', 'ac'),
(6, 6, 'PIXEL FOLD TEMPERED GLASS SCREEN PROTECTOR', '<p>Picture this: you&rsquo;re the world&rsquo;s most incompetent jewelry thief. After somehow managing to convince the clerk to &ldquo;cough &lsquo;em up,&rdquo; it hits you like a sack of diamonds: you left your duffel bag at home. As you haphazardly stuff the jewels into your pocket, you realize your second mistake: that&rsquo;s where your phone is. Congratulations. Not only are you going to prison, but the world&rsquo;s hardest mineral just turned your phone screen into a crime scene: scratched glass, shattered dreams, and immeasurable regret. How could you possibly have prevented this unavoidable tragedy? Our lawyers advised us not to sell you a crash course in thievery, so we\'ll sell you a flawless Pixel Fold screen protector instead.</p>', 'new-arrivals', 'sp'),
(7, 7, 'AirPods (3rd generation)', '<h4>Updated design</h4>\r\n<div>\r\n<p>AirPods are lightweight and offer a contoured design. They sit at just the right angle for comfort and to better direct audio to your ear. The stem is 33 percent shorter than AirPods (2nd generation) and includes a force sensor to easily control music and calls.</p>\r\n</div>\r\n<h4>Personalized Spatial Audio with dynamic head tracking</h4>\r\n<div>\r\n<p>Sound is placed all around you to create an immersive, three-dimensional listening experience for music, TV shows, and movies. Gyroscopes and accelerometers in AirPods work together to track your head movements &mdash; so it sounds like you&rsquo;re in the center of songs and scenes.</p>\r\n</div>\r\n<h4>Adaptive EQ</h4>\r\n<div>\r\n<p>Music is automatically tuned to suit the shape of your ear. Inward-facing microphones detect what you&rsquo;re hearing, then adjust low- and mid-range frequencies to deliver the rich details in every song.</p>\r\n</div>\r\n<h4>Longer battery life</h4>\r\n<div>\r\n<p>AirPods have an extra hour of battery life compared with AirPods (2nd generation) for up to 6 hours of listening time&sup2; and up to 4 hours of talk time. With just 5 minutes of charge, you&rsquo;ll get around an hour of listening⁶ or talk time.⁷ And with the Lightning Charging Case, you can enjoy up to 30 hours of total listening time.</p>\r\n</div>\r\n<h4>Sweat and water resistant</h4>\r\n<div>\r\n<p>Both AirPods and the Lightning Charging Case are rated IPX4 water resistant &mdash; so they&rsquo;ll withstand anything from rain to heavy workouts.</p>\r\n</div>\r\n<h4>Magical in every way</h4>\r\n<div>\r\n<p>Setup is effortless &mdash; pull them out of the case and they&rsquo;re ready to use. Automatically switch between your Apple devices. In-ear detection knows the difference between your ear and other surfaces. Announce Notifications with Siri gives you the option to have Siri read your notifications through your AirPods. And with Audio Sharing, you and a friend can easily share a song or show between any two sets of AirPods.</p>\r\n</div>', 'best-seller', 'ep'),
(8, 8, 'WH-1000XM5 Wireless Noise Cancelling Headphones', '<p>SONY WH-1000XM5 Wireless Bluetooth Noise Cancelling Headphone</p>\n<h4><br />YOUR WORLD. NOTHING ELSE.</h4>\n<p>From airplane noise to people&rsquo;s voices, our WH-1000XM5 wireless headphones with multiple microphone noise cancelling keep out more high and mid frequency sounds than ever. And with Auto NC Optimizer, noise cancelling is automatically optimised based on your wearing conditions and environment.</p>\n<h4>Multi Noise Sensor technology</h4>\n<p>With four microphones on each earcup, this is our biggest ever step forward in noise cancelling. Ambient sound is captured even more accurately for a dramatic reduction in high frequency noise. Thanks to Auto NC Optimizer, noise cancelling performance is always and automatically optimised based on wearing conditions and external environmental factors such as atmospheric pressure.</p>\n<h4>Incomparable noise cancelling</h4>\n<p>Specially developed by Sony, the new Integrated Processor V1 unlocks the full potential of our HD Noise Cancelling Processor QN1. This unique combination of technology controls eight microphones to deliver unprecedented noise cancelling quality.</p>\n<h4>Superlative sound, engineered to perfection</h4>\n<p>The specially designed 30mm driver unit with light and rigid dome using carbon fiber composite material improves high frequency sensitivity for more natural sound quality. Sony unique technologies include a premium lead-free solder containing gold for excellent conductivity, Fine Sound Resistor for even power distribution, and optimised circuitry for an improved signal-to-noise ratio to ensure clear, consistent sound.</p>\n<h4>1000X series best call quality ever</h4>\n<p>Our Precise Voice Pickup Technology uses four beamforming microphones and an AI-based noise reduction algorithm to isolate your voice precisely. A newly developed wind noise reduction structure minimises wind noise during calls. Wherever you are, you&rsquo;ll always hear and be heard clearly.</p>\n<h4>Multiple microphones, focused on your voice</h4>\n<p>Equipped with four beamforming microphones, these headphones are calibrated to only pick up your voice. An improved signal-to-noise ratio enables them to catch every single word, even when there&rsquo;s a lot of noise around.</p>\n<h4>All day comfort with a noiseless design</h4>\n<p>These lightweight headphones are beautifully finished in newly developed soft fit leather. This material fits snugly around the head with less pressure on the ears while keeping out external sounds. Our noiseless design with stepless slider, seamless swivel and hanger, and silent joints, makes WH-1000XM5 a pleasure to wear.</p>', 'recommended', 'hp'),
(9, 9, '25W PD Adapter (USB-C) (Without Cable)', '<h4>Super Fast Charging to stay ready</h4>\n<p>Give your mobile devices the powerful and safe charging support they deserve. This Wall Charger provides Super Fast Charging with USB-C PD 3.0 PPS at up to max 25W for capable devices. So when you do run low, it\'s not for long.</p>\n<h4>Compatible with various devices</h4>\n<p>Wall Charger does the job for Android devices as well as devices that run different operating systems. For this Wall Charger, the size of your devices isn\'t a problem; from earbuds to laptops, take advantage of an ideal charging time for your devices at speeds they can manage to handle.</p>\n<h4>Enjoy the flexibility of the USB Type-C</h4>\n<p>Enjoy the flexibility enabled by USB-C compatible cables. You can alternate the types of cables to charge a variety of mobile devices that you own. Just plug in a cable &ndash; there\'s no need to change the adapter plugged into your wall outlet</p>', 'promotion', 'ac'),
(10, 10, 'AirPods Pro (2nd generation)', '<p>AirPods Pro feature up to two times more Active Noise Cancellation, plus Adaptive Transparency and Personalised Spatial Audio with dynamic head tracking for immersive sound. Now with multiple ear tips (XS, S, M, L) and up to 6 hours of listening time.</p>\n<p>Key feature<br />&bull; Active Noise Cancellation reduces unwanted background noise<br />&bull; Adaptive Transparency lets outside sounds in while reducing loud environmental noise<br />&bull; Personalised Spatial Audio with dynamic head tracking places sound all around you<br />&bull; Multiple ear tips (XS, S, M, L)<br />&bull; Touch control lets you swipe to adjust volume, press to direct media playback, answer or end calls, and press and hold to switch between listening modes<br />&bull; Sweat and water resistant for AirPods Pro and charging case<br />&bull; MagSafe Charging Case with speaker and lanyard loop<br />&bull; Up to 6 hours of listening time with Active Noise Cancellation on<br />&bull; Up to 30 hours of total listening time with the MagSafe Charging Case and Active Noise Cancellation on<br />&bull; Easy setup, in-ear detection and automatic switching between devices<br />&bull; Audio Sharing between two sets of AirPods on your iPhone, iPad, iPod touch or Apple TV<br />&bull; Find My with proximity view for AirPods Pro and Precision Finding for charging case</p>', 'best-seller', 'ep'),
(11, 11, 'Xiaomi Redmi Buds 3 Pro', '<h4>Up to 35dB Hybrid ANC</h4>\n<p>The active noise cancellation can reduce up to 35dB of surrounding noise.</p>\n<h4>Intelligent Dynamic ANC with 3 modes</h4>\n<p>Recognizing the ambient sounds intelligently, Redmi Buds 3 Pro switches the noise cancellation mode accordingly for a more suitable hearing experience.</p>\n<h4>Up to 28 hours battery life</h4>\n<p>A single charge brings you up to 6 hours listening time with noise cancellation turned-off, and up to 28 hours when coupled with the charging case.</p>\n<h4>Dual Device Connection</h4>\n<p>Redmi Buds 3 Pro can be connected with two devices simultaneously. The devices include smartphones, tablets, PCs, and other smart devices.</p>\n<h4>10 minutes Charge for 3 hours usage</h4>\n<p>Redmi Buds 3 Pro supports fast charging, only 10 minutes charge you can use 3 hours. Never be afraid of battery off.</p>\n<h4>Comfortable Fit</h4>\n<p>The ergonomic design ensures a comfortable and secure fit when you wear the earbuds while doing yoga or cycling.</p>\n<h4>Supports Wireless Charging</h4>\n<p>Redmi Buds 3 Pro is compatible with Qi-certified chargers.</p>\n<h4>In-ear Detection</h4>\n<p>The music stops when you take out the earphone, and the music resumes when you put on the earphones.</p>\n<h4>Find your earphone</h4>\n<p>Don&rsquo;t worry about losing earphones, as long as your earphone is connected to Bluetooth, you can find your earphone through the MIUI system.</p>\n<p><em>Dimensions</em><br /><em>Earbuds: 25.4mm x 20.3mm x 21.3mm</em><br /><em>Box: 65mm x 48mm x 26mm</em><br /><em>Weight: 55g (with charging case)</em><br /><em>Color: Graphite Black, Glacier Gray</em></p>\n<p><em>Audio Codec: SBC/AAC</em><br /><em>ANC Depth: up to 35dB1</em><br /><em>ANC Modes: Adaptive, Light, Balanced, Deep2</em><br /><em>Transparency Modes: Enhanced Voice, Transparency2</em><br /><em>Control Type: Long press, double tap, triple tap</em></p>', 'best-seller', 'ep'),
(12, 12, 'Anker A2633/A2149/A2637 PowerPort III Nano', '<h4>Designed for iPhone:</h4>\n<p>Anker Nanos 20W output is designed to provide the maximum charge to iPhone 12.</p>\n<h4>Unrivaled Speed:</h4>\n<p>Charge iPhone 12 and previous iPhone models up to 3&times; faster than with an original 5W Charger.</p>\n<h4>Space-Saving Design:</h4>\n<p>At 50% smaller than a standard 18W iPhone charger, Anker Nano provides more power while saving space in your bag or while plugged into a wall outlet.</p>\n<h4>Works with Most Handheld Devices:</h4>\n<p>Provide up to 20W charging to most flagship phones including iPhone and Samsung Galaxy S20 as well as smartwatches and earbuds.</p>\n<h4>What You Get:</h4>\n<p><em>PowerPort III Nano, welcome guide, our worry-free 18-month warranty, and friendly customer service (cable not included).</em></p>\n<h4>Specs</h4>\n<p><em>Input: 200-240V 0.6A 50-60Hz</em><br /><em>Output: 5V=3A/9V=2.22A (Max 20W)</em><br /><em>Size: 45 &times; 27 &times; 27mm</em><br /><em>Weight: 30g</em></p>', 'promotion', 'ac'),
(13, 13, 'Redmi Airdots S BT 5.0', '<h4>Easy Connection</h4>\n<p>This headphone automatically connects when removed from the charging case.</p>\n<h4>BT 5.0</h4>\n<p>Adopt the latest BT 5.0 chip, the data transmission speed is doubled compared with the previous generation, and the connection is faster and more stable.</p>\n<h4>Clear Sound Quality</h4>\n<p>7.2mm sound unit, DSP digital noise reduction technology, can filter environmental noise, bring clear sound quality.</p>\n<h4>Long Endurance</h4>\n<p>Built-in rechargeable lithium battery, the headphone ensures 12 hours of endurance time with the charging case, no need to worry about power shortage.</p>\n<h4>Game Mode</h4>\n<p>Monaural mode: &lsquo;Left with phone&rsquo; or &lsquo;Right with phone&rsquo;, no more need to release binaural mode, triple tap into &lsquo;lower lag mode&rsquo;.</p>', 'best-value', 'ep'),
(14, 14, 'Mi 20W Wireless Charging Stand', '<h4>A New Wireless Charging Experience</h4>\n<p>Watch a movie while charging.</p>\n<h4>Vertical Design</h4>\n<p>Quick induction instant charging.</p>\n<h4>20W Max</h4>\n<p>Powerful 20W super fast wireless charging</p>\n<h4>Universal Fast Charge</h4>\n<p>Compatible with most devices supporting wireless charging</p>\n<h4>Dual Coils</h4>\n<p>Charges a phone placed vertically or horizontally</p>', 'best-value', 'ac'),
(15, 15, 'Clone of the Kingdom', '<h4>The Wisdom of Dead Scientists</h4>\n<p>There&rsquo;s a thin line between a cheap knockoff and a masterful clone. As it turns out, that line is made of gold. To recreate the dock\'s metallic sheen, we assembled the nation\'s leading gold scientists and brought them to an enormous smelter. Unfortunately, their job titles had misled us: the bodies of the scientists didn\'t contain any extra gold content. Their ashes did, however, prove invaluable when perfecting our metallic finish. Now, if you&rsquo;ll excuse us, we&rsquo;ve got some more scientists to incinerate.</p>\n<h4>The Courage to Innovate</h4>\n<p>We\'ve got good news: during Clone of the Kingdom&rsquo;s mass production run, an intern managed to severely botch the green ink pigmentation, producing a deep black. After taking one look at his accidental masterpiece, we immediately gave him a promotion. As a direct result of this mishap, each Clone of the Kingdom order contains an additional color option for the left Joy-Con, free of charge. As for the intern, his new job title is \"Hazardous Materials Taste Tester\". Or rather, it was. We\'ll have to hire a replacement.</p>', 'new-arrivals', 'ac');

--
-- Triggers `products`
--
DROP TRIGGER IF EXISTS `TRIGGER_INSERT_PRODUCT_ID`;
DELIMITER $$
CREATE TRIGGER `TRIGGER_INSERT_PRODUCT_ID` BEFORE INSERT ON `products` FOR EACH ROW SET New.product_id = (SELECT IFNULL(MAX(product_id), 0) + 1 FROM products)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

DROP TABLE IF EXISTS `product_reviews`;
CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_display_name` varchar(32) NOT NULL,
  `user_rating` decimal(2,1) NOT NULL,
  `user_comment` longtext NOT NULL,
  `user_comment_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `product_reviews`
--

TRUNCATE TABLE `product_reviews`;
--
-- Dumping data for table `product_reviews`
--

INSERT INTO `product_reviews` (`id`, `product_id`, `user_display_name`, `user_rating`, `user_comment`, `user_comment_time`) VALUES
(1, 4, 'Sundiep', '3.0', 'Great but requires a bit more.\r\nGot this cover as there is not much Samsung S23 Ultra cover available, although the launch showed all these great covers. The cover itself is good and durable, but the rear hand grip makes it annoying when laying phone flat ', '2023-05-11 14:31:23'),
(2, 4, 'trebolt', '5.0', 'Great case!\r\nStill using it and it\'s a good piece of accessory for my S23 Ultra.', '2023-04-21 06:12:22'),
(3, 4, 'Gunzta', '1.0', 'No where near as good as last years ribbon case.No where near as good as last years ribbon strap case. This case uses elastic which will stretch over time to make grip less secure. Last year\'s case used ribbon strap which could not stretch. A significant downgrade from S22 Ultra strap case.', '2023-05-12 14:34:01'),
(4, 4, 'Waqy27', '1.0', 'Do not buy.\r\nThe worst case I\'ve had. Dropped phone in case once on road, and a tiny stone ripped right through. It is easily damaged and offers very little protection.', '2023-03-16 03:00:25'),
(5, 4, 'Kader', '4.0', 'Nice cover and good look I liked þttttttttþtttttyyyt', '2023-03-18 09:32:12'),
(6, 9, 'Gmac', '1.0', 'Poor longevity\r\nFailed 2 days under 12 months after received, but 2 days after order was placed. Failure bu design I would say.', '2023-01-04 03:38:48'),
(7, 9, 'MrNaib', '5.0', 'Superfast charging is REAL!!\r\n\r\nI have been using it for about a year and can\'t recommend it enough.', '2023-01-30 09:49:29'),
(8, 9, 'mbh297', '2.0', 'Okay wired but poor for Samsung wireless\n\nIt charges the S22 at the full 25 watts (super fast charging) but will not power the Samsung EP-P1100 for fast wireless charging (5v, 2A i.e. 10W), only normal (5W) wireless charging. Disappointing as the old 15W chargers (EP-TA200) do allow for fast wireless charging (at 10W). This puts me off investing in a 15W Samsung wireless charger.', '2022-11-15 06:00:25'),
(9, 9, 'pot8o', '3.0', 'Great while it lasted\r\n\r\nFaulty after 19 months. Maybe I expect too much from products these days but I would have hoped for longer than this. Kind of mocks the reducing ewaste argument for not supplying in the first place. Lets see if the replacement fares better', '2022-09-09 07:25:00'),
(10, 9, 'Maitaa', '2.0', 'Not really fast at all\r\n\r\nNeeded a new charger for my S22 Ultra as I was still using my 3.5 year old Huawei charger. Samsung hasn\'t had the 45w charger in for ages so thought I\'d buy the 25w one. It literally arrived 10 mins ago.y phone says 76% battery left. I plugged my old Huawei charger in and it said fully charged within 35 minutes....then I plugged this one in and it said 27 minutes! 27 minutes to charge just 24%?? That\'s 2 hours for a full charge which was pretty much what my 4 year old charger was giving me anyway....wish I\'d saved my money now until a 45w eventually comes available and at a reasonable price!', '2022-06-17 02:29:30'),
(11, 9, 'Tman1', '3.0', 'samsung plug\r\n\r\nthis would be great if it would be in the box. you are wasting more EWaste buying it separate', '2022-05-03 06:16:16'),
(12, 9, 'MJ0317', '5.0', 'Nicest packing I ever received!\n\nFast delivery & good handling by courier no dent!!!', '2023-05-14 23:57:38'),
(13, 9, 'DOM TAN', '2.0', '25W Travel Adapter (Super Fast Charging)\r\n\r\n- Initiated a purchase on this item on 8 Aug 2021 - Item was packaged in a very compact manner without the Super Fast Charging cable - Item charges phone quite fast with the Super Fast Charging cable although phone was not SFC compatible - Not value for money as Super Fast Charging was not included in the package', '2022-08-08 04:03:25'),
(14, 10, 'ajimafiq', '5.0', 'This airpod have the best noise cancelling compare to Sony wf1000 mx4 in my opinion, not to mention the spartial sound is also superb to.', '2022-12-23 10:18:18'),
(15, 10, 'englandson_lim', '5.0', 'Superb quality as expected from Apple product: \r\n\r\nMulti-device switching👍👍👍👍\r\nNoise-cancelling👍👍\r\nTransparency 👍👍👍👍👍\r\nVolume slider built in 👍👍👍👍👍\r\nExtra eartips 👍👍👍\r\nCharge using Apple Watch charger 👍👍👍👍', '2023-01-15 01:12:31'),
(16, 10, ' mardhiahsha', '5.0', 'My second purchased with this seller for apple product. Love this Airpods Pro', '2022-12-02 04:24:34'),
(17, 10, 'hewjerry', '5.0', 'Very Nice sounds for wireless headphones.', '2022-12-30 16:00:00'),
(18, 10, 'cuuyszy', '5.0', 'Received in 5days. Box a little bit dent but doesn’t effect performance so ok lar.', '2022-10-14 16:00:00'),
(19, 13, 'najmi203', '5.0', 'Good quality with affordable price. Thank you seller. Will repeat', '2023-03-29 00:29:59'),
(20, 13, 'pynk9697', '5.0', 'Received in good and well packing. Not yet try and pray hard can function well. Will feed back once got any problem.', '2023-05-16 00:32:01'),
(21, 13, 'pavitrrhraj', '5.0', 'Bought for my two bros.. thanks seller good product quality fast delivery good value for money', '2022-11-05 00:31:10'),
(22, 13, ' angelinangbl', '4.5', 'slight dent on the box but the quality IS SUPERB ! does not compete with airpods pro but this one is the better buy, does the same job except no noise cancelling. pairing is easy. even with an iphone. fast shipping too', '2022-06-13 00:32:04'),
(23, 14, 'miracey', '5.0', 'fast charging, really good product but al lower price nice nicr', '2022-05-30 00:40:49'),
(24, 14, 'faiqhaziq', '5.0', 'Good product function as it stated. Very good job and delivery is acceptable.', '2022-12-19 00:41:38'),
(25, 14, 'mju7sa2', '4.0', 'Function as intended. Wireless fast charging. However dont expect it to be as fast as wired charging obviously', '2022-09-30 00:42:00');

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
CREATE TABLE `product_variants` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `variant_image_urls` longtext DEFAULT NULL,
  `variant_type` int(11) NOT NULL,
  `variant_value` varchar(32) NOT NULL,
  `variant_avail_qty` int(8) NOT NULL,
  `variant_price` decimal(8,2) NOT NULL,
  `variant_discount` tinyint(1) DEFAULT 0,
  `variant_discount_amt` decimal(8,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `product_variants`
--

TRUNCATE TABLE `product_variants`;
--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `variant_id`, `variant_image_urls`, `variant_type`, `variant_value`, `variant_avail_qty`, `variant_price`, `variant_discount`, `variant_discount_amt`) VALUES
(11, 4, 6, 'galaxy-s23-ultra-out.png,galaxy-s23-ultra-side.png,galaxy-s23-ultra-in.png', 1, 'Black', 6, '169.00', 1, '33.80'),
(12, 4, 7, 'galaxy-s23-ultra-white-in.png,galaxy-s23-ultra-white-out.png', 1, 'White', 7, '169.00', 1, '33.80'),
(13, 5, 8, 'magsafe-front.jpg,magsafe-back.jpg,magsafe-cable.jpg,magsafe-iphone.jpg', 1, 'White', 9, '179.00', 0, '0.00'),
(14, 6, 9, 'pixel_fold_back.jpg,pixel_fold_front.jpg', 4, 'Clear', 2, '110.00', 0, '0.00'),
(15, 7, 10, 'airpods3.jpg,airpods3_pods.jpg,airpods3_case.jpg,airpods3_case_top.jpg', 1, 'White', 27, '829.00', 1, '15.00'),
(16, 8, 11, 'WH-1000XM5_black.png,WH-1000XM5_black_back.png,WH-1000XM5_black_front.png', 1, 'Black', 33, '1799.00', 1, '400.00'),
(17, 8, 12, 'WH-1000XM5_white.png,WH-1000XM5_white_back.png,WH-1000XM5_white_front.png', 1, 'White', 34, '1799.00', 1, '400.00'),
(19, 9, 13, '25w-pd-adapter.png,25w-pd-adapter-front.png,25w-pd-adapter-back.png,25w-pd-adapter-side.png', 1, 'Black', 32, '79.00', 1, '5.00'),
(20, 9, 14, '25w-pd-adapter-white.png,25w-pd-adapter-white-front.png,25w-pd-adapter-white-back.png,25w-pd-adapter-side.png', 1, 'White', 27, '79.00', 1, '5.00'),
(21, 10, 15, 'airpods-pro-2.jpg,airpods-pro-2-pods.jpg,airpods-pro-2-case-open.jpg,airpods-pro-2-side.jpg', 1, 'White', 47, '1099.00', 0, '0.00'),
(22, 11, 16, 'rm-buds-3-pro.jpg,rm-buds-3-pro-side.jpg,rm-buds-3-pro-top.jpg', 1, 'Black', 54, '239.99', 1, '9.99'),
(23, 11, 17, 'rm-buds-3-pro-white.jpg,rm-buds-3-pro-white-side.jpg', 1, 'White', 53, '239.99', 1, '9.99'),
(24, 12, 18, 'anker-25w-white.jpg', 1, 'White', 8, '64.00', 1, '5.00'),
(25, 12, 19, 'anker-25w-purple.jpg', 1, 'Purple', 8, '64.00', 1, '5.00'),
(26, 12, 20, 'anker-25w-blue.jpg', 1, 'Blue', 8, '64.00', 1, '5.00'),
(27, 12, 21, 'anker-25w-black.jpg', 1, 'Black', 8, '64.00', 1, '5.00'),
(28, 13, 22, 'redmi-airdots-s.jpg', 1, 'Black', 8, '39.00', 0, '0.00'),
(29, 14, 23, 'mi-20w-wireless-charging-stand.png,mi-20w-wireless-charging-stand-back.png,mi-20w-wireless-charging-stand-bottom.png', 1, 'Black', 21, '69.90', 0, '0.00'),
(30, 15, 24, 'cotkd-dock.jpg,cotkd-joycon.jpg,cotkd-back.jpg', 4, 'Switch', 4, '200.00', 0, '0.00');

--
-- Triggers `product_variants`
--
DROP TRIGGER IF EXISTS `TRIGGER_INSERT_VARIANT_ID`;
DELIMITER $$
CREATE TRIGGER `TRIGGER_INSERT_VARIANT_ID` BEFORE INSERT ON `product_variants` FOR EACH ROW SET New.variant_id = (SELECT IFNULL(MAX(variant_id), 0) + 1 FROM product_variants)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `product_variants_type`
--

DROP TABLE IF EXISTS `product_variants_type`;
CREATE TABLE `product_variants_type` (
  `id` int(11) NOT NULL,
  `variant_type_id` int(11) DEFAULT NULL,
  `variant_type_text` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `product_variants_type`
--

TRUNCATE TABLE `product_variants_type`;
--
-- Dumping data for table `product_variants_type`
--

INSERT INTO `product_variants_type` (`id`, `variant_type_id`, `variant_type_text`) VALUES
(1, 1, 'Color'),
(2, 2, 'Size'),
(3, 3, 'Length'),
(4, 4, 'Type');

--
-- Triggers `product_variants_type`
--
DROP TRIGGER IF EXISTS `TRIGGER_INSERT_PRODUCT_VARIANTS_ID`;
DELIMITER $$
CREATE TRIGGER `TRIGGER_INSERT_PRODUCT_VARIANTS_ID` BEFORE INSERT ON `product_variants_type` FOR EACH ROW SET New.variant_type_id = (SELECT IFNULL(MAX(variant_type_id), 0) + 1 FROM product_variants_type)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_display_name` varchar(40) NOT NULL,
  `user_email` varchar(64) NOT NULL,
  `user_password` varchar(256) NOT NULL,
  `user_age` int(2) NOT NULL,
  `user_contact` varchar(12) NOT NULL,
  `user_avatar` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `users`
--

TRUNCATE TABLE `users`;
--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_id`, `user_display_name`, `user_email`, `user_password`, `user_age`, `user_contact`, `user_avatar`) VALUES
(1, 1, 'User 1', 'user@user.com', '$2y$10$jFqy2U42hHgH/BVdhVxgke8hCtUozcODwx.wb/AtmToRbxCdnG4yy', 24, '012-11293671', '6476b916cbb5b.jpg'),
(5, 2, 'User 21', 'user21@user.com', '$2y$10$PV163eq13LenKy1XsRr5QOVV1Po1xGwOZiRwJQLvdXrr2xIQsntGC', 22, '012-2345678', NULL),
(6, 3, 'user 2', 'user2@user2.com', '$2y$10$0hkgQClIFfr5YhqHc6tmn.C4Qs/sJqiLZJ7USFsl0kXTWUQJlpTf2', 24, '012-9921019', NULL),
(7, 4, 'user 3', 'user3@user3.com', '$2y$10$wuhGoKhminh/O8GMlRFbquiY7OYH421f9TZ0iXjGW10mDpdCSfcVq', 25, '012-2291281', NULL);

--
-- Triggers `users`
--
DROP TRIGGER IF EXISTS `TRIGGER_BEFORE_INSERT`;
DELIMITER $$
CREATE TRIGGER `TRIGGER_BEFORE_INSERT` BEFORE INSERT ON `users` FOR EACH ROW SET New.user_id = (SELECT IFNULL(MAX(user_id), 0) + 1 FROM `users`)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users_delivery_information`
--

DROP TABLE IF EXISTS `users_delivery_information`;
CREATE TABLE `users_delivery_information` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `delivery_name` varchar(80) NOT NULL,
  `delivery_address_1` mediumtext NOT NULL,
  `delivery_address_2` mediumtext NOT NULL,
  `delivery_contact` varchar(12) NOT NULL,
  `delivery_email` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `users_delivery_information`
--

TRUNCATE TABLE `users_delivery_information`;
--
-- Dumping data for table `users_delivery_information`
--

INSERT INTO `users_delivery_information` (`id`, `user_id`, `delivery_name`, `delivery_address_1`, `delivery_address_2`, `delivery_contact`, `delivery_email`) VALUES
(1, 1, 'John Doe', '4 Luh Satu Kaw Indust, Bdr Sultan Suleiman Pelabuhan Pelabuhan', '42000 Klang, Selangor', '018-9924561', 'example@example.com'),
(6, 1, 'Johanna', 'No. 5 Jalan SS 21/39, Damansara Uptown', '47400 Petaling Jaya, Selangor', '012-9928264', 't@v.c');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_carts_users_user_id` (`user_id`),
  ADD KEY `fk_carts_product_variants_variant_id` (`product_variant_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_guid`),
  ADD UNIQUE KEY `order_id_2` (`order_id`),
  ADD KEY `fk_orders_users_user_id` (`user_id`);

--
-- Indexes for table `orders_delivery_log`
--
ALTER TABLE `orders_delivery_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_delivery_log_orders_order_id` (`order_id`);

--
-- Indexes for table `orders_detail`
--
ALTER TABLE `orders_detail`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_detail_id` (`order_detail_id`),
  ADD KEY `fk_orders_detail_orders_order_id` (`order_id`),
  ADD KEY `fk_orders_detail_product_variants_variant_id` (`order_product_variant_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`product_id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_reviews_orders_product_id` (`product_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `variant_id` (`variant_id`),
  ADD KEY `fk_product_variants_products_product_id` (`product_id`),
  ADD KEY `fk_product_variants_products_variants_type_variants_id` (`variant_type`);

--
-- Indexes for table `product_variants_type`
--
ALTER TABLE `product_variants_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `variant_type_id` (`variant_type_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `user_email` (`user_email`);

--
-- Indexes for table `users_delivery_information`
--
ALTER TABLE `users_delivery_information`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_users_delivery)information_users_user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `orders_delivery_log`
--
ALTER TABLE `orders_delivery_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders_detail`
--
ALTER TABLE `orders_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `product_variants_type`
--
ALTER TABLE `product_variants_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users_delivery_information`
--
ALTER TABLE `users_delivery_information`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `fk_carts_product_variants_variant_id` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_carts_users_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_users_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `orders_delivery_log`
--
ALTER TABLE `orders_delivery_log`
  ADD CONSTRAINT `fk_orders_delivery_log_orders_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders_detail`
--
ALTER TABLE `orders_detail`
  ADD CONSTRAINT `fk_orders_detail_orders_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orders_detail_product_variants_variant_id` FOREIGN KEY (`order_product_variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `fk_product_reviews_orders_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `fk_product_variants_products_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_product_variants_products_variants_type_variants_id` FOREIGN KEY (`variant_type`) REFERENCES `product_variants_type` (`variant_type_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `users_delivery_information`
--
ALTER TABLE `users_delivery_information`
  ADD CONSTRAINT `fk_users_delivery)information_users_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
