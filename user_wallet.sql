-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 05, 2024 at 04:58 PM
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
  `wallet_action` varchar(255) NOT NULL,
  `payment_type` varchar(1) NOT NULL DEFAULT 'R' COMMENT 'R = Rider\r\n(has deduction of 70% to rider)\r\nS = Shop Cost (no deduction from rider , commission to Admin)\r\nA = Admin\r\nC = Cash Out\r\nT = Top Up\r\n',
  `reference_number` varchar(32) DEFAULT NULL,
  `wallet_txn_status` varchar(1) NOT NULL DEFAULT 'P',
  `wallet_txn_start_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  `gcash_account_number` varchar(11) DEFAULT NULL,
  `gcash_reference_number` varchar(50) CHARACTER SET utf16 COLLATE utf16_bin DEFAULT NULL,
  `gcash_amount_sent` decimal(12,2) DEFAULT NULL,
  `gcash_account_name` varchar(100) DEFAULT NULL,
  `gcash_attachment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `user_wallet`
--
ALTER TABLE `user_wallet`
  MODIFY `user_wallet_id` bigint(11) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
