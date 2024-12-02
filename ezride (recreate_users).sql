-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 02, 2024 at 05:33 PM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ezride`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `t_username` varchar(55) NOT NULL,
  `t_password` varchar(255) NOT NULL,
  `t_status` varchar(1) NOT NULL DEFAULT 'A',
  `date_joined` timestamp NOT NULL DEFAULT current_timestamp(),
  `t_user_type` varchar(1) NOT NULL DEFAULT 'C',
  `t_rider_status` varchar(1) DEFAULT NULL,
  `t_online_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Online / Offline',
  `t_last_online_ts` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

DROP TABLE IF EXISTS `user_profile`;
CREATE TABLE `user_profile` (
  `user_profile_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `gcash_account_number` varchar(11) DEFAULT NULL,
  `gcash_account_name` varchar(100) DEFAULT NULL,
  `user_firstname` varchar(55) DEFAULT NULL,
  `user_lastname` varchar(55) DEFAULT NULL,
  `user_mi` varchar(55) DEFAULT NULL,
  `user_contact_no` varchar(255) DEFAULT NULL,
  `user_gender` varchar(1) DEFAULT NULL,
  `user_email_address` varchar(255) DEFAULT NULL,
  `user_profile_image` varchar(255) DEFAULT 'female_person1.jpg',
  `rider_plate_no` varchar(10) DEFAULT NULL,
  `rider_license_no` varchar(55) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user_wallet`
--

DROP TABLE IF EXISTS `user_wallet`;
CREATE TABLE `user_wallet` (
  `user_wallet_id` bigint(11) UNSIGNED ZEROFILL NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `payTo` int(11) DEFAULT NULL,
  `payFrom` int(11) DEFAULT NULL,
  `wallet_txn_amt` decimal(12,2) NOT NULL DEFAULT 0.00,
  `txn_type_id` int(11) NOT NULL,
  `wallet_action` varchar(55) NOT NULL,
  `payment_type` varchar(1) NOT NULL DEFAULT 'R' COMMENT 'R = Rider\r\n(has deduction of 70% to rider)\r\nS = Shop Cost (no deduction from rider , commission to Admin)\r\nA = Admin\r\nC = Cash Out\r\nT = Top Up\r\n',
  `reference_number` varchar(32) DEFAULT NULL,
  `wallet_txn_status` varchar(1) NOT NULL DEFAULT 'P',
  `wallet_txn_start_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  `gcash_account_number` varchar(11) DEFAULT NULL,
  `gcash_reference_number` varchar(50) CHARACTER SET utf16 COLLATE utf16_bin DEFAULT NULL,
  `gcash_amount_sent` decimal(12,2) DEFAULT NULL,
  `gcash_account_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`user_profile_id`),
  ADD KEY `user-userProfile` (`user_id`);

--
-- Indexes for table `user_wallet`
--
ALTER TABLE `user_wallet`
  ADD PRIMARY KEY (`user_wallet_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`payment_type`,`txn_type_id`,`reference_number`,`wallet_txn_amt`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `user_profile_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_wallet`
--
ALTER TABLE `user_wallet`
  MODIFY `user_wallet_id` bigint(11) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD CONSTRAINT `user-userProfile` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
