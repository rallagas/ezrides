-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 12, 2025 at 08:24 AM
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
-- Table structure for table `angkas_bookings`
--

CREATE TABLE `angkas_bookings` (
  `angkas_booking_id` int(11) NOT NULL,
  `angkas_booking_reference` varchar(20) DEFAULT NULL,
  `shop_order_reference_number` varchar(25) DEFAULT NULL,
  `shop_cost` decimal(12,2) DEFAULT NULL,
  `transaction_category_id` int(2) NOT NULL DEFAULT 2,
  `user_id` int(11) NOT NULL COMMENT '(FK) to user as customer',
  `angkas_rider_user_id` int(11) DEFAULT NULL COMMENT '(FK) to user as rider',
  `form_from_dest_name` varchar(255) NOT NULL,
  `user_currentLoc_lat` varchar(55) NOT NULL,
  `user_currentLoc_long` varchar(55) NOT NULL,
  `form_to_dest_name` varchar(255) NOT NULL,
  `formToDest_long` varchar(55) NOT NULL,
  `formToDest_lat` varchar(55) NOT NULL,
  `form_ETA_duration` decimal(6,2) DEFAULT NULL,
  `form_TotalDistance` decimal(6,2) DEFAULT NULL,
  `form_Est_Cost` decimal(12,2) DEFAULT NULL,
  `date_booked` timestamp NOT NULL DEFAULT current_timestamp(),
  `booking_status` varchar(1) NOT NULL DEFAULT 'P' COMMENT 'case when ab.booking_status = ''P'' THEN ''Waiting for Driver''\r\n                                         when ab.booking_status = ''A'' THEN ''Driver Found''\r\n                                         when ab.booking_status = ''R'' THEN ''Driver Arrived in Your Location''\r\n                                         when ab.booking_status = ''I'' THEN ''In Transit''\r\n                                         when ab.booking_status = ''C'' THEN ''Completed''\r\n                                    end as booking_status\r\n',
  `payment_status` char(1) NOT NULL DEFAULT 'P' COMMENT 'P = Pending, D=Declined, C=Completed Payment',
  `payment_id` int(11) DEFAULT NULL,
  `rating` varchar(20) NOT NULL DEFAULT '3'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `angkas_rider_queue`
--

CREATE TABLE `angkas_rider_queue` (
  `angkas_rider_queue_id` int(11) NOT NULL,
  `angkas_rider_id` int(11) NOT NULL COMMENT '(FK) to user_id for riders',
  `queue_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `queue_status` varchar(1) NOT NULL DEFAULT 'A' COMMENT 'A - available\r\nI - In Transit\r\nD - Done'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `angkas_vehicle_model`
--

CREATE TABLE `angkas_vehicle_model` (
  `vehicle_model_id` int(11) NOT NULL,
  `vehicle_model` varchar(100) NOT NULL,
  `model_body_type` varchar(20) NOT NULL,
  `number_of_seats` int(11) NOT NULL,
  `allowed_ind` varchar(1) NOT NULL DEFAULT 'Y'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `angkas_vehicle_model`
--

INSERT INTO `angkas_vehicle_model` (`vehicle_model_id`, `vehicle_model`, `model_body_type`, `number_of_seats`, `allowed_ind`) VALUES
(1, 'Toyota Vios', 'Sedan', 5, 'Y'),
(2, 'Toyota Avanza', 'MPV', 7, 'Y'),
(3, 'Toyota Innova', 'MPV', 8, 'Y'),
(4, 'Toyota Wigo', 'Hatchback', 5, 'Y'),
(5, 'Toyota Altis', 'Sedan', 5, 'Y'),
(6, 'Nissan Almera', 'Sedan', 5, 'Y'),
(7, 'Nissan NV350 Urvan', 'Van', 15, 'Y'),
(8, 'Nissan Sylphy', 'Sedan', 5, 'Y'),
(9, 'Hyundai Accent', 'Sedan', 5, 'Y'),
(10, 'Hyundai Reina', 'Sedan', 5, 'Y'),
(11, 'Hyundai H-100', 'Truck', 3, 'Y'),
(12, 'Hyundai Eon', 'Hatchback', 5, 'Y'),
(13, 'Mitsubishi Mirage', 'Hatchback', 5, 'Y'),
(14, 'Mitsubishi Mirage G4', 'Sedan', 5, 'Y'),
(15, 'Mitsubishi Adventure', 'MPV', 7, 'Y'),
(16, 'Mitsubishi L300', 'Truck', 3, 'Y'),
(17, 'Suzuki Celerio', 'Hatchback', 5, 'Y'),
(18, 'Suzuki Dzire', 'Sedan', 5, 'Y'),
(19, 'Suzuki Alto', 'Hatchback', 4, 'Y'),
(20, 'Suzuki Ertiga', 'MPV', 7, 'Y'),
(21, 'Suzuki APV', 'MPV', 8, 'Y'),
(22, 'Kia Soluto', 'Sedan', 5, 'Y'),
(23, 'Kia Picanto', 'Hatchback', 5, 'Y'),
(24, 'Kia K2700', 'Truck', 3, 'Y'),
(25, 'Isuzu Crosswind', 'MPV', 7, 'Y'),
(26, 'Isuzu Traviz', 'Truck', 3, 'Y'),
(27, 'Isuzu D-Max', 'Pickup', 5, 'Y'),
(28, 'Chevrolet Spark', 'Hatchback', 5, 'Y'),
(29, 'Chevrolet Sail', 'Sedan', 5, 'Y'),
(30, 'Ford Fiesta', 'Hatchback', 5, 'Y'),
(31, 'Ford EcoSport', 'SUV', 5, 'Y'),
(32, 'Honda City', 'Sedan', 5, 'Y'),
(33, 'Honda Brio', 'Hatchback', 5, 'Y'),
(34, 'Honda Jazz', 'Hatchback', 5, 'Y'),
(35, 'Honda Mobilio', 'MPV', 7, 'Y'),
(36, 'Geely Coolray', 'SUV', 5, 'Y'),
(37, 'Chery Tiggo 2', 'SUV', 5, 'Y'),
(38, 'Foton Gratour', 'Van', 11, 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `app_transactions`
--

CREATE TABLE `app_transactions` (
  `app_txn_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `txn_category_id` int(11) NOT NULL,
  `txn_status` varchar(1) NOT NULL DEFAULT 'P',
  `txn_tm_ts` timestamp NOT NULL DEFAULT current_timestamp(),
  `book_start_dte` date DEFAULT NULL COMMENT 'for booking type transactions',
  `book_end_dte` date DEFAULT NULL COMMENT 'for booking type transactions',
  `book_location_id` varchar(50) NOT NULL COMMENT 'for booking type transactions',
  `book_item_inventory_id` int(11) NOT NULL COMMENT 'connects to items_inventory_id in items_inventory table',
  `amount_to_pay` decimal(7,2) DEFAULT NULL,
  `payment_status` char(1) NOT NULL DEFAULT 'P' COMMENT 'P - Pending\r\nD - Done Payment',
  `rental_reference` varchar(55) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `customerSuggestions`
--

CREATE TABLE `customerSuggestions` (
  `cs_id` int(11) NOT NULL,
  `emailadd` varchar(255) DEFAULT NULL,
  `cus_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `rate` int(11) NOT NULL DEFAULT 5,
  `photo` varchar(255) DEFAULT NULL,
  `approved` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `items_inventory`
--

CREATE TABLE `items_inventory` (
  `items_inventory_id` int(11) NOT NULL,
  `item_reference_id` int(11) DEFAULT NULL,
  `item_description` text NOT NULL,
  `vendor_id` int(11) NOT NULL COMMENT 'associated with vendors inside the app',
  `txn_category_id` int(11) NOT NULL,
  `item_price` double NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `txn_status` varchar(1) NOT NULL DEFAULT 'P'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `items_inventory`
--

INSERT INTO `items_inventory` (`items_inventory_id`, `item_reference_id`, `item_description`, `vendor_id`, `txn_category_id`, `item_price`, `date_added`, `txn_status`) VALUES
(9, 2, 'RENTAL:2:Toyota:2000.00:XXX 124', 8, 1, 2000, '2024-10-02 19:12:56', 'P'),
(10, 1, 'RENTAL:1:Volvo:0.00:XXX 123', 8, 1, 2500, '2024-10-02 19:23:12', 'P'),
(14, 15, 'RENTAL:15:Isuzu Elf:600.00:GHI-3456', 8, 1, 600, '2024-10-04 13:07:49', 'P'),
(15, 12, 'RENTAL:12:Toyota Corolla:200.00:ABC-1234', 8, 1, 200, '2024-10-04 13:08:33', 'P'),
(16, 13, 'RENTAL:13:Honda CBR1000RR:100.00:XYZ-5678', 8, 1, 100, '2024-10-04 13:08:35', 'P'),
(17, 14, 'RENTAL:14:Ford Transit:400.00:DEF-9012', 8, 1, 400, '2024-10-04 13:08:37', 'P'),
(18, 16, 'RENTAL:16:Ford Explorer:300.00:JKL-7890', 8, 1, 300, '2024-10-04 13:08:38', 'P'),
(19, 17, 'RENTAL:17:Toyota Hilux:500.00:MNO-5678', 8, 1, 500, '2024-10-04 13:08:39', 'P'),
(20, 18, 'RENTAL:18:Tesla Model 3:250.00:PQR-1234', 8, 1, 250, '2024-10-04 13:08:40', 'P'),
(21, 19, 'RENTAL:19:Toyota Prius:200.00:STU-5678', 8, 1, 200, '2024-10-04 13:08:43', 'P'),
(22, 20, 'RENTAL:20:Honda Scoopy:60.00:VWX-1234', 8, 1, 60, '2024-10-04 13:10:03', 'P'),
(23, 21, 'RENTAL:21:Giant Escape:20.00:YZX-5678', 8, 1, 20, '2024-10-04 13:10:05', 'P');

-- --------------------------------------------------------

--
-- Table structure for table `lu_cars`
--

CREATE TABLE `lu_cars` (
  `car_id` int(11) NOT NULL,
  `car_brand` varchar(55) NOT NULL,
  `car_rent_price` decimal(6,2) NOT NULL,
  `car_plate_no` varchar(15) NOT NULL,
  `car_owner_id` int(11) NOT NULL,
  `car_color_id` int(11) NOT NULL,
  `car_model_id` int(11) NOT NULL,
  `car_body_type_id` int(11) NOT NULL,
  `car_img` varchar(255) NOT NULL,
  `car_year_model` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `refcitymun`
--

CREATE TABLE `refcitymun` (
  `id` int(255) NOT NULL,
  `psgcCode` varchar(255) DEFAULT NULL,
  `citymunDesc` text DEFAULT NULL,
  `regDesc` varchar(255) DEFAULT NULL,
  `provCode` varchar(255) DEFAULT NULL,
  `citymunCode` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='lu_city_mun';

--
-- Dumping data for table `refcitymun`
--

INSERT INTO `refcitymun` (`id`, `psgcCode`, `citymunDesc`, `regDesc`, `provCode`, `citymunCode`) VALUES
(564, '050501000', 'BACACAY', '05', '0505', '050501'),
(565, '050502000', 'CAMALIG', '05', '0505', '050502'),
(566, '050503000', 'DARAGA (LOCSIN)', '05', '0505', '050503'),
(567, '050504000', 'GUINOBATAN', '05', '0505', '050504'),
(568, '050505000', 'JOVELLAR', '05', '0505', '050505'),
(569, '050506000', 'LEGAZPI CITY (Capital)', '05', '0505', '050506'),
(570, '050507000', 'LIBON', '05', '0505', '050507'),
(571, '050508000', 'CITY OF LIGAO', '05', '0505', '050508'),
(572, '050509000', 'MALILIPOT', '05', '0505', '050509'),
(573, '050510000', 'MALINAO', '05', '0505', '050510'),
(574, '050511000', 'MANITO', '05', '0505', '050511'),
(575, '050512000', 'OAS', '05', '0505', '050512'),
(576, '050513000', 'PIO DURAN', '05', '0505', '050513'),
(577, '050514000', 'POLANGUI', '05', '0505', '050514'),
(578, '050515000', 'RAPU-RAPU', '05', '0505', '050515'),
(579, '050516000', 'SANTO DOMINGO (LIBOG)', '05', '0505', '050516'),
(580, '050517000', 'CITY OF TABACO', '05', '0505', '050517'),
(581, '050518000', 'TIWI', '05', '0505', '050518'),
(582, '051601000', 'BASUD', '05', '0516', '051601'),
(583, '051602000', 'CAPALONGA', '05', '0516', '051602'),
(584, '051603000', 'DAET (Capital)', '05', '0516', '051603'),
(585, '051604000', 'SAN LORENZO RUIZ (IMELDA)', '05', '0516', '051604'),
(586, '051605000', 'JOSE PANGANIBAN', '05', '0516', '051605'),
(587, '051606000', 'LABO', '05', '0516', '051606'),
(588, '051607000', 'MERCEDES', '05', '0516', '051607'),
(589, '051608000', 'PARACALE', '05', '0516', '051608'),
(590, '051609000', 'SAN VICENTE', '05', '0516', '051609'),
(591, '051610000', 'SANTA ELENA', '05', '0516', '051610'),
(592, '051611000', 'TALISAY', '05', '0516', '051611'),
(593, '051612000', 'VINZONS', '05', '0516', '051612'),
(594, '051701000', 'BAAO', '05', '0517', '051701'),
(595, '051702000', 'BALATAN', '05', '0517', '051702'),
(596, '051703000', 'BATO', '05', '0517', '051703'),
(597, '051704000', 'BOMBON', '05', '0517', '051704'),
(598, '051705000', 'BUHI', '05', '0517', '051705'),
(599, '051706000', 'BULA', '05', '0517', '051706'),
(600, '051707000', 'CABUSAO', '05', '0517', '051707'),
(601, '051708000', 'CALABANGA', '05', '0517', '051708'),
(602, '051709000', 'CAMALIGAN', '05', '0517', '051709'),
(603, '051710000', 'CANAMAN', '05', '0517', '051710'),
(604, '051711000', 'CARAMOAN', '05', '0517', '051711'),
(605, '051712000', 'DEL GALLEGO', '05', '0517', '051712'),
(606, '051713000', 'GAINZA', '05', '0517', '051713'),
(607, '051714000', 'GARCHITORENA', '05', '0517', '051714'),
(608, '051715000', 'GOA', '05', '0517', '051715'),
(609, '051716000', 'IRIGA CITY', '05', '0517', '051716'),
(610, '051717000', 'LAGONOY', '05', '0517', '051717'),
(611, '051718000', 'LIBMANAN', '05', '0517', '051718'),
(612, '051719000', 'LUPI', '05', '0517', '051719'),
(613, '051720000', 'MAGARAO', '05', '0517', '051720'),
(614, '051721000', 'MILAOR', '05', '0517', '051721'),
(615, '051722000', 'MINALABAC', '05', '0517', '051722'),
(616, '051723000', 'NABUA', '05', '0517', '051723'),
(617, '051724000', 'NAGA CITY', '05', '0517', '051724'),
(618, '051725000', 'OCAMPO', '05', '0517', '051725'),
(619, '051726000', 'PAMPLONA', '05', '0517', '051726'),
(620, '051727000', 'PASACAO', '05', '0517', '051727'),
(621, '051728000', 'PILI (Capital)', '05', '0517', '051728'),
(622, '051729000', 'PRESENTACION (PARUBCAN)', '05', '0517', '051729'),
(623, '051730000', 'RAGAY', '05', '0517', '051730'),
(624, '051731000', 'SAGÑAY', '05', '0517', '051731'),
(625, '051732000', 'SAN FERNANDO', '05', '0517', '051732'),
(626, '051733000', 'SAN JOSE', '05', '0517', '051733'),
(627, '051734000', 'SIPOCOT', '05', '0517', '051734'),
(628, '051735000', 'SIRUMA', '05', '0517', '051735'),
(629, '051736000', 'TIGAON', '05', '0517', '051736'),
(630, '051737000', 'TINAMBAC', '05', '0517', '051737'),
(631, '052001000', 'BAGAMANOC', '05', '0520', '052001'),
(632, '052002000', 'BARAS', '05', '0520', '052002'),
(633, '052003000', 'BATO', '05', '0520', '052003'),
(634, '052004000', 'CARAMORAN', '05', '0520', '052004'),
(635, '052005000', 'GIGMOTO', '05', '0520', '052005'),
(636, '052006000', 'PANDAN', '05', '0520', '052006'),
(637, '052007000', 'PANGANIBAN (PAYO)', '05', '0520', '052007'),
(638, '052008000', 'SAN ANDRES (CALOLBON)', '05', '0520', '052008'),
(639, '052009000', 'SAN MIGUEL', '05', '0520', '052009'),
(640, '052010000', 'VIGA', '05', '0520', '052010'),
(641, '052011000', 'VIRAC (Capital)', '05', '0520', '052011'),
(642, '054101000', 'AROROY', '05', '0541', '054101'),
(643, '054102000', 'BALENO', '05', '0541', '054102'),
(644, '054103000', 'BALUD', '05', '0541', '054103'),
(645, '054104000', 'BATUAN', '05', '0541', '054104'),
(646, '054105000', 'CATAINGAN', '05', '0541', '054105'),
(647, '054106000', 'CAWAYAN', '05', '0541', '054106'),
(648, '054107000', 'CLAVERIA', '05', '0541', '054107'),
(649, '054108000', 'DIMASALANG', '05', '0541', '054108'),
(650, '054109000', 'ESPERANZA', '05', '0541', '054109'),
(651, '054110000', 'MANDAON', '05', '0541', '054110'),
(652, '054111000', 'CITY OF MASBATE (Capital)', '05', '0541', '054111'),
(653, '054112000', 'MILAGROS', '05', '0541', '054112'),
(654, '054113000', 'MOBO', '05', '0541', '054113'),
(655, '054114000', 'MONREAL', '05', '0541', '054114'),
(656, '054115000', 'PALANAS', '05', '0541', '054115'),
(657, '054116000', 'PIO V. CORPUZ (LIMBUHAN)', '05', '0541', '054116'),
(658, '054117000', 'PLACER', '05', '0541', '054117'),
(659, '054118000', 'SAN FERNANDO', '05', '0541', '054118'),
(660, '054119000', 'SAN JACINTO', '05', '0541', '054119'),
(661, '054120000', 'SAN PASCUAL', '05', '0541', '054120'),
(662, '054121000', 'USON', '05', '0541', '054121'),
(663, '056202000', 'BARCELONA', '05', '0562', '056202'),
(664, '056203000', 'BULAN', '05', '0562', '056203'),
(665, '056204000', 'BULUSAN', '05', '0562', '056204'),
(666, '056205000', 'CASIGURAN', '05', '0562', '056205'),
(667, '056206000', 'CASTILLA', '05', '0562', '056206'),
(668, '056207000', 'DONSOL', '05', '0562', '056207'),
(669, '056208000', 'GUBAT', '05', '0562', '056208'),
(670, '056209000', 'IROSIN', '05', '0562', '056209'),
(671, '056210000', 'JUBAN', '05', '0562', '056210'),
(672, '056211000', 'MAGALLANES', '05', '0562', '056211'),
(673, '056212000', 'MATNOG', '05', '0562', '056212'),
(674, '056213000', 'PILAR', '05', '0562', '056213'),
(675, '056214000', 'PRIETO DIAZ', '05', '0562', '056214'),
(676, '056215000', 'SANTA MAGDALENA', '05', '0562', '056215'),
(677, '056216000', 'CITY OF SORSOGON (Capital)', '05', '0562', '056216');

-- --------------------------------------------------------

--
-- Table structure for table `refprovince`
--

CREATE TABLE `refprovince` (
  `id` int(11) NOT NULL,
  `psgcCode` varchar(255) DEFAULT NULL,
  `provDesc` text DEFAULT NULL,
  `regCode` varchar(255) DEFAULT NULL,
  `provCode` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `refprovince`
--

INSERT INTO `refprovince` (`id`, `psgcCode`, `provDesc`, `regCode`, `provCode`) VALUES
(27, '050500000', 'ALBAY', '05', '0505'),
(32, '056200000', 'SORSOGON', '05', '0562');

-- --------------------------------------------------------

--
-- Table structure for table `refregion`
--

CREATE TABLE `refregion` (
  `id` int(11) NOT NULL,
  `psgcCode` varchar(255) DEFAULT NULL,
  `regDesc` text DEFAULT NULL,
  `regCode` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `refregion`
--

INSERT INTO `refregion` (`id`, `psgcCode`, `regDesc`, `regCode`) VALUES
(6, '050000000', 'REGION V (BICOL REGION)', '05');

-- --------------------------------------------------------

--
-- Table structure for table `shop_category`
--

CREATE TABLE `shop_category` (
  `sc_id` int(11) NOT NULL,
  `shop_category_name` varchar(55) NOT NULL,
  `shop_category_status` varchar(1) NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `shop_category`
--

INSERT INTO `shop_category` (`sc_id`, `shop_category_name`, `shop_category_status`) VALUES
(1, 'grocery', 'A'),
(2, 'pharmacy', 'A'),
(3, 'food-delivery', 'A');

-- --------------------------------------------------------

--
-- Table structure for table `shop_items`
--

CREATE TABLE `shop_items` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `saleDiscount` double DEFAULT NULL,
  `saleIndicator` varchar(1) NOT NULL,
  `merchant_id` int(11) DEFAULT NULL,
  `category` int(11) NOT NULL DEFAULT 1,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `item_img` varchar(255) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `item_status` varchar(1) NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `shop_merchants`
--

CREATE TABLE `shop_merchants` (
  `merchant_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `merchant_loc_coor` varchar(100) DEFAULT NULL,
  `merchant_img` varchar(100) DEFAULT NULL,
  `merchant_type` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `shop_merchants`
--

INSERT INTO `shop_merchants` (`merchant_id`, `name`, `address`, `phone`, `email`, `merchant_loc_coor`, `merchant_img`, `merchant_type`) VALUES
(1, 'LCC Ligao', '6GRQ+7HM Ligao, Albay', '123-456-7890', 'freshmart@example.com', '13.2407034,123.5389159', 'lcc-ligao.jpg', 'Supermarket'),
(11, 'Mercury Drug Corporation@Ligao City McKinley', '6GRR+455, McKinley Street, Ligao, 4504 Albay', '0524851150', NULL, '13.2401825,123.5403961', 'mercury-drug-ligao.jpeg', 'Pharmacy'),
(12, 'McDonald\'s Ligao', '276 National Hwy, Ligao, 4504 Albay', '0288886236', NULL, '13.2377605,123.5423527', 'mcdo-ligao.jpg', 'Food Delivery'),
(13, 'Philippine Statistics Authority Region V', '4540 Daraga - Legazpi City - Tiwi Rd, Legazpi City, 4500 Albay', '0524817479', NULL, '13.1653945,123.7499651', 'psa-daraga.jpg', 'Document Processing');

-- --------------------------------------------------------

--
-- Table structure for table `shop_orders`
--

CREATE TABLE `shop_orders` (
  `order_id` int(11) NOT NULL,
  `shop_order_ref_num` varchar(20) DEFAULT 'ON_CART',
  `voucher_code` int(11) DEFAULT NULL,
  `Shipping_fee` int(11) DEFAULT NULL,
  `shipping_name` varchar(100) DEFAULT NULL,
  `shipping_address` varchar(255) DEFAULT NULL,
  `shipping_address_coor` varchar(255) DEFAULT NULL,
  `shipping_phone` varchar(55) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `rider_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `amount_to_pay` decimal(9,2) DEFAULT NULL,
  `order_date` timestamp NULL DEFAULT current_timestamp(),
  `delivery_status` varchar(1) DEFAULT 'P',
  `payment_status` varchar(1) DEFAULT '',
  `order_state_ind` char(1) NOT NULL DEFAULT 'C' COMMENT 'C - Cart\r\nO - Checkout\r\nP - Payment\r\nD - Delivered\r\nX - Cancelled',
  `order_special_instructions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `txn_category`
--

CREATE TABLE `txn_category` (
  `txn_category_id` int(11) NOT NULL,
  `page_action` varchar(55) DEFAULT NULL,
  `txn_prefix` varchar(3) DEFAULT NULL,
  `txn_link` varchar(55) NOT NULL,
  `page_include_form` varchar(100) NOT NULL,
  `txn_category_name` varchar(55) NOT NULL,
  `txn_category_status` varchar(1) NOT NULL,
  `icon_class` varchar(55) NOT NULL,
  `txn_title` varchar(55) NOT NULL,
  `load_js_file` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `txn_category`
--

INSERT INTO `txn_category` (`txn_category_id`, `page_action`, `txn_prefix`, `txn_link`, `page_include_form`, `txn_category_name`, `txn_category_status`, `icon_class`, `txn_title`, `load_js_file`) VALUES
(1, 'rent', 'RNT', '_car_rental.php', '', 'RENTAL', 'A', 'car-rental-icon.png', 'rent', '_car_rental.js'),
(2, 'angkas', 'ANG', '_angkas.php', '', 'Angkas', 'A', 'ride-hailing-icon.png', 'angkas', ''),
(3, NULL, NULL, '_food_delivery.php', '', 'Food Delivery', 'X', 'hamburger-soda', 'fooddelivery', ''),
(4, NULL, NULL, '_rx.php', '', 'Medicine Pabili', 'X', 'file-prescription', 'rx', ''),
(5, NULL, NULL, '_legal.php', '', 'Document Processing', 'X', 'legal', 'legal', ''),
(6, 'shop', 'GRX', '_shop.php', '', 'SHOP', 'A', 'store.png', 'shop', ''),
(7, 'wallet', NULL, '_wallet.php', '', 'Wallet', 'A', 'wallet.png', 'wallet', ''),
(8, NULL, NULL, '_coupons.php', '', 'Coupons', 'X', 'ticket', 'coupons', ''),
(9, NULL, NULL, '_surprise.php', '', 'Motorcycle Surprises', 'X', 'gift-box-benefits', 'motorcyclesurprises', ''),
(10, 'earnings', NULL, '_my_earnings.php', '', 'My Earnings', 'X', 'peso-sign', 'My Earnings', ''),
(11, 'bookings', NULL, '', '', 'My Bookings', 'X', 'location-alt', 'My Bookings', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `vehicle`
--

CREATE TABLE `vehicle` (
  `vehicle_id` int(11) NOT NULL,
  `vehicle_type` varchar(5) NOT NULL COMMENT '0010 - 10-wheeler truck\r\n0004 - e-bike\r\n0005 - sedan\r\n0006 - suv\r\n0007 - 7-seater\r\n02 - motorcycle\r\n01 - bike',
  `vehicle_plate_no` varchar(10) NOT NULL,
  `vehicle_color` varchar(55) NOT NULL,
  `vehicle_model` varchar(55) DEFAULT NULL,
  `vehicle_img` varchar(100) DEFAULT NULL,
  `vehicle_owner_id` int(4) DEFAULT NULL COMMENT 'connects to vendors.vendor_id',
  `vehicle_owner_name` varchar(255) NOT NULL,
  `vehicle_owner_address` varchar(255) NOT NULL,
  `vehicle_price_rate_per_hr` decimal(6,2) DEFAULT NULL,
  `vehicle_price_rate_per_day` decimal(7,2) DEFAULT NULL,
  `vehicle_price_rate_per_km` decimal(7,2) DEFAULT NULL,
  `vehicle_txn_type` int(11) NOT NULL DEFAULT 2 COMMENT '1 - rent\r\n2 - angkas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `vehicle`
--

INSERT INTO `vehicle` (`vehicle_id`, `vehicle_type`, `vehicle_plate_no`, `vehicle_color`, `vehicle_model`, `vehicle_img`, `vehicle_owner_id`, `vehicle_owner_name`, `vehicle_owner_address`, `vehicle_price_rate_per_hr`, `vehicle_price_rate_per_day`, `vehicle_price_rate_per_km`, `vehicle_txn_type`) VALUES
(1, '0005', 'XXX 123', 'Black', '2000 Toyota Vios', 'toyota_vios.jpg', 8, '', '', '100.00', '2500.00', '2.00', 1),
(12, '0010', 'ABC-1234', 'Red', 'Toyota Corolla', 'toyota_corolla.jpg', 8, '', '', '10.00', '200.00', '0.50', 1),
(13, '0005', 'XYZ-5678', 'Blue', 'Honda CBR1000RR', 'HondaCBR1000RR.jpg', 8, '', '', '5.00', '100.00', '0.25', 1),
(14, '0006', 'DEF-9012', 'White', 'Ford Transit', 'FordTransit.jpg', 8, '', '', '20.00', '400.00', '1.00', 1),
(15, '0010', 'GHI-3456', 'Green', 'Isuzu Elf', 'isuzuelf.jpg', 8, '', '', '30.00', '600.00', '2.00', 1),
(16, '0006', 'JKL-7890', 'Black', 'Ford Explorer', 'fordexplorer.jpg', 8, '', '', '15.00', '300.00', '0.75', 1),
(17, '02', 'MNO-5678', 'Silver', 'Toyota Hilux', 'hilux.jpg', 8, '', '', '25.00', '500.00', '1.25', 1),
(18, '0004', 'PQR-1234', 'White', 'Tesla Model 3', 'tesla.jpg', 8, '', '', '12.00', '250.00', '0.60', 1),
(19, '0005', 'STU-5678', 'Blue', 'Toyota Prius', 'prius.jpg', 8, '', '', '10.00', '200.00', '0.50', 1),
(20, '02', 'VWX-1234', 'Red', 'Honda Scoopy', 'scoopy.jpg', 8, '', '', '3.00', '60.00', '0.15', 1),
(21, '01', 'YZX-5678', 'Black', 'Giant Escape', 'giant.jpg', 8, '', '', '1.00', '20.00', '0.05', 1);

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `vendor_id` int(11) NOT NULL,
  `vendor_name` varchar(55) NOT NULL,
  `vendor_type` int(2) NOT NULL COMMENT '99 - External\r\n1 - Internal',
  `vendor_status` varchar(1) NOT NULL DEFAULT 'A',
  `vendor_contact_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`vendor_id`, `vendor_name`, `vendor_type`, `vendor_status`, `vendor_contact_number`) VALUES
(1, 'Philippine Statistics Office', 99, 'A', ''),
(2, 'Wallet Top Up System', 1, 'A', ''),
(3, 'Mercury Drug', 99, 'A', ''),
(4, 'South Star Drug Store', 99, 'A', ''),
(5, 'Generica Drugstore', 99, 'A', ''),
(6, 'TGP - The Generic Pharmacy', 99, 'A', ''),
(7, 'EZ Rides - Internal Rider', 1, 'A', ''),
(8, 'EZ Rides - Internal Rent a Car', 1, 'A', '');

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `voucher_id` int(11) NOT NULL,
  `voucher_code` varchar(10) NOT NULL,
  `voucher_amt` int(11) NOT NULL,
  `voucher_desc` varchar(255) NOT NULL,
  `voucher_valid_until` timestamp NULL DEFAULT NULL,
  `voucher_avail_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `vouchers`
--

INSERT INTO `vouchers` (`voucher_id`, `voucher_code`, `voucher_amt`, `voucher_desc`, `voucher_valid_until`, `voucher_avail_count`) VALUES
(1, 'TEST', -2000, 'Php 2000.00 OFF', '2024-11-30 03:59:59', 999);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `angkas_bookings`
--
ALTER TABLE `angkas_bookings`
  ADD PRIMARY KEY (`angkas_booking_id`),
  ADD KEY `transaction_category_id` (`transaction_category_id`);

--
-- Indexes for table `angkas_rider_queue`
--
ALTER TABLE `angkas_rider_queue`
  ADD PRIMARY KEY (`angkas_rider_queue_id`);

--
-- Indexes for table `angkas_vehicle_model`
--
ALTER TABLE `angkas_vehicle_model`
  ADD PRIMARY KEY (`vehicle_model_id`);

--
-- Indexes for table `app_transactions`
--
ALTER TABLE `app_transactions`
  ADD PRIMARY KEY (`app_txn_id`);

--
-- Indexes for table `customerSuggestions`
--
ALTER TABLE `customerSuggestions`
  ADD PRIMARY KEY (`cs_id`);

--
-- Indexes for table `items_inventory`
--
ALTER TABLE `items_inventory`
  ADD PRIMARY KEY (`items_inventory_id`),
  ADD KEY `vendor_items` (`vendor_id`),
  ADD KEY `txn_category_items` (`txn_category_id`);

--
-- Indexes for table `lu_cars`
--
ALTER TABLE `lu_cars`
  ADD PRIMARY KEY (`car_id`);

--
-- Indexes for table `refcitymun`
--
ALTER TABLE `refcitymun`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `refprovince`
--
ALTER TABLE `refprovince`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `refregion`
--
ALTER TABLE `refregion`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shop_category`
--
ALTER TABLE `shop_category`
  ADD PRIMARY KEY (`sc_id`);

--
-- Indexes for table `shop_items`
--
ALTER TABLE `shop_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `item-cat` (`category`),
  ADD KEY `item-merchant` (`merchant_id`);

--
-- Indexes for table `shop_merchants`
--
ALTER TABLE `shop_merchants`
  ADD PRIMARY KEY (`merchant_id`);

--
-- Indexes for table `shop_orders`
--
ALTER TABLE `shop_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `unique_user_item` (`user_id`,`item_id`,`shop_order_ref_num`) USING BTREE;

--
-- Indexes for table `txn_category`
--
ALTER TABLE `txn_category`
  ADD PRIMARY KEY (`txn_category_id`);

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
-- Indexes for table `vehicle`
--
ALTER TABLE `vehicle`
  ADD PRIMARY KEY (`vehicle_id`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`vendor_id`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`voucher_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `angkas_bookings`
--
ALTER TABLE `angkas_bookings`
  MODIFY `angkas_booking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `angkas_rider_queue`
--
ALTER TABLE `angkas_rider_queue`
  MODIFY `angkas_rider_queue_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `angkas_vehicle_model`
--
ALTER TABLE `angkas_vehicle_model`
  MODIFY `vehicle_model_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `app_transactions`
--
ALTER TABLE `app_transactions`
  MODIFY `app_txn_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customerSuggestions`
--
ALTER TABLE `customerSuggestions`
  MODIFY `cs_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items_inventory`
--
ALTER TABLE `items_inventory`
  MODIFY `items_inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `lu_cars`
--
ALTER TABLE `lu_cars`
  MODIFY `car_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `refcitymun`
--
ALTER TABLE `refcitymun`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1648;

--
-- AUTO_INCREMENT for table `refprovince`
--
ALTER TABLE `refprovince`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `refregion`
--
ALTER TABLE `refregion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `shop_category`
--
ALTER TABLE `shop_category`
  MODIFY `sc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `shop_items`
--
ALTER TABLE `shop_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shop_merchants`
--
ALTER TABLE `shop_merchants`
  MODIFY `merchant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `shop_orders`
--
ALTER TABLE `shop_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `txn_category`
--
ALTER TABLE `txn_category`
  MODIFY `txn_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
-- AUTO_INCREMENT for table `vehicle`
--
ALTER TABLE `vehicle`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `vendor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `voucher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `angkas_bookings`
--
ALTER TABLE `angkas_bookings`
  ADD CONSTRAINT `angkas_bookings_ibfk_1` FOREIGN KEY (`transaction_category_id`) REFERENCES `txn_category` (`txn_category_id`);

--
-- Constraints for table `items_inventory`
--
ALTER TABLE `items_inventory`
  ADD CONSTRAINT `txn_category_items` FOREIGN KEY (`txn_category_id`) REFERENCES `txn_category` (`txn_category_id`),
  ADD CONSTRAINT `vendor_items` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`);

--
-- Constraints for table `shop_items`
--
ALTER TABLE `shop_items`
  ADD CONSTRAINT `item-cat` FOREIGN KEY (`category`) REFERENCES `shop_category` (`sc_id`),
  ADD CONSTRAINT `item-merchant` FOREIGN KEY (`merchant_id`) REFERENCES `shop_merchants` (`merchant_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD CONSTRAINT `user-userProfile` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
