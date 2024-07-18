-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 18, 2024 at 01:04 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smart-park`
--

-- --------------------------------------------------------

--
-- Table structure for table `active_subscription`
--

CREATE TABLE `active_subscription` (
  `sub_id` varchar(255) NOT NULL,
  `package_id` int(255) NOT NULL,
  `usr_id` varchar(255) NOT NULL,
  `date_created` varchar(255) NOT NULL,
  `due_date` varchar(255) NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `plate_no` varchar(255) NOT NULL,
  `time_status` varchar(255) NOT NULL,
  `auto_renew` varchar(255) NOT NULL,
  `amount` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `active_subscription`
--

INSERT INTO `active_subscription` (`sub_id`, `package_id`, `usr_id`, `date_created`, `due_date`, `order_id`, `plate_no`, `time_status`, `auto_renew`, `amount`) VALUES
('S-5squjiyfcxaVU6d', 3, 'U-osbornmaja-ut9sbYPiHEqC8DK', '2024-06-12 21:21:00', '2024-07-12 21:21:00', '77807459Y0997062B', 'KBALLLE', 'elapsed', 'yes', '2100'),
('S-MAiKguNDnUx8lWt', 4, 'U-osbornmaja-ut9sbYPiHEqC8DK', '2024-06-28 06:22:38', '2024-06-29 06:22:38', 'nDIyiTs7qX9wYBM', 'KDA808E', 'elapsed', 'yes', '200'),
('S-wroevqbJxhuI1QF', 1, 'U-osbornmaja-ut9sbYPiHEqC8DK', '2024-07-18 12:07:54', '2024-07-19 12:07:54', 'HGmLBiIqMkFWt3O', 'KCA8080E', 'active', 'no', '70');

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `package_id` int(255) NOT NULL,
  `package_fee` varchar(255) NOT NULL,
  `package_title` varchar(255) NOT NULL,
  `package_interval` varchar(255) NOT NULL,
  `cover_image` text NOT NULL,
  `placeholder_img` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`package_id`, `package_fee`, `package_title`, `package_interval`, `cover_image`, `placeholder_img`) VALUES
(1, '70', 'Daily', 'daily', 'daily_parking.jpg', 'placeholder-car.png'),
(2, '3500', 'PSV monthly', 'monthly', 'psv_parking.webp', 'psv_vector.png'),
(3, '2100', 'Private vehicle monthly', 'monthly', 'monthly_parking.jpg', 'placeholder-car.png'),
(4, '200', 'Lorry/Bus daily', 'daily', 'lorry_parking.jpg', 'large_vehicle_vector.png');

-- --------------------------------------------------------

--
-- Table structure for table `payment_history`
--

CREATE TABLE `payment_history` (
  `payment_id` int(255) NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `usr_id` varchar(255) NOT NULL,
  `amount` varchar(255) NOT NULL,
  `payment_type` varchar(255) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `payer_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_history`
--

INSERT INTO `payment_history` (`payment_id`, `order_id`, `usr_id`, `amount`, `payment_type`, `payment_method`, `payer_name`) VALUES
(1, '4UE06086M0063772M', 'U-osbornmaja-ut9sbYPiHEqC8DK', '300', 'deposit', 'Paypal', 'Osborn Mangaro'),
(2, '0B84484151141401V', 'U-osbornmaja-ut9sbYPiHEqC8DK', '300', 'deposit', 'Paypal', 'Osborn Mangaro'),
(3, '2XW47708U11912709', 'U-osbornmaja-ut9sbYPiHEqC8DK', '100', 'deposit', 'Paypal', 'Osborn Mangaro');

-- --------------------------------------------------------

--
-- Table structure for table `smart_park_tickets`
--

CREATE TABLE `smart_park_tickets` (
  `ticket_id` varchar(255) NOT NULL,
  `usr_id` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `ticket_subject` varchar(255) NOT NULL,
  `ticket_status` varchar(255) NOT NULL,
  `date_created` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `smart_park_tickets`
--

INSERT INTO `smart_park_tickets` (`ticket_id`, `usr_id`, `message`, `ticket_subject`, `ticket_status`, `date_created`) VALUES
('fSaRFJ9jrkeHIOq', 'U-osbornmaja-ut9sbYPiHEqC8DK', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s', 'Subscription issues', 'received', '2024-06-13 11:58:38'),
('XEBbiTFwPl57a2j', 'U-osbornmaja-ut9sbYPiHEqC8DK', 'I am experience some issue with my payment options kindly help', 'Payment issues', 'received', '2024-06-13 12:00:28');

-- --------------------------------------------------------

--
-- Table structure for table `smart_park_users`
--

CREATE TABLE `smart_park_users` (
  `usr_id` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `pwd` text NOT NULL,
  `official_names` varchar(255) NOT NULL,
  `national_id` text NOT NULL,
  `region` varchar(255) NOT NULL,
  `balance` int(255) NOT NULL,
  `code_token` varchar(255) NOT NULL,
  `cookie_token_name` text NOT NULL,
  `cookie_token_value` text NOT NULL,
  `reset_token` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `smart_park_users`
--

INSERT INTO `smart_park_users` (`usr_id`, `mail`, `pwd`, `official_names`, `national_id`, `region`, `balance`, `code_token`, `cookie_token_name`, `cookie_token_value`, `reset_token`) VALUES
('U-osbornmaja-ut9sbYPiHEqC8DK', 'osbornmaja@gmail.com', '$2y$12$92.fB.GExz2zNLePzlrWJOzDgJBM2600W7ZnXHDRGdP0cIsPojQFO', 'Osborn Mangaro', '39180543', 'Kiharu Sub-County', 710, '010207', 'e10812cbbe3a9f544e07d2e05e0e49fd', '30c2a6cbecc0cd3b6493554cb38cd3bbebeca52b7e331e4837e12de51603713c', '6677e09f803bcb5e62f80e174800a52461f75e91b570099f8bfd71485b20a67e'),
('U-osbornmangaro-wBjF8KVx1029atR', 'osbornmangaro@gmail.com', '$2y$12$3nu779KFVIP821rDy.TzlumMpKYeZ.DyHYUYhg39QV4ypebtmr1SO', 'Osborn Mangaro', '39180543', 'Kiharu Sub-County', 2200, '729598', '65e4d6ca33398001834d5b9ad47651f7', 'c8cba70d18d6dbad0b173e842d58652bc0f4d6e71a3bafcb6225a7db1d004ab5', '');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `vehicle_id` int(255) NOT NULL,
  `plate_no` varchar(255) NOT NULL,
  `date_registered` varchar(255) NOT NULL,
  `vehicle_desc` text NOT NULL,
  `usr_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`vehicle_id`, `plate_no`, `date_registered`, `vehicle_desc`, `usr_id`) VALUES
(5, 'KCA808E', '2024-06-14 10:42:55', '', 'U-osbornmaja-ut9sbYPiHEqC8DK'),
(6, 'KAB909E', '2024-06-14 10:43:44', '', 'U-osbornmaja-ut9sbYPiHEqC8DK');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `active_subscription`
--
ALTER TABLE `active_subscription`
  ADD PRIMARY KEY (`sub_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`package_id`);

--
-- Indexes for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `smart_park_tickets`
--
ALTER TABLE `smart_park_tickets`
  ADD PRIMARY KEY (`ticket_id`);

--
-- Indexes for table `smart_park_users`
--
ALTER TABLE `smart_park_users`
  ADD PRIMARY KEY (`usr_id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`vehicle_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `package_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payment_history`
--
ALTER TABLE `payment_history`
  MODIFY `payment_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `vehicle_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
