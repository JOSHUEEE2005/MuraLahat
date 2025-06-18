-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 15, 2025 at 09:34 PM
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
-- Database: `testml2`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `Cart_ID` int(11) NOT NULL,
  `Customer_ID` int(11) NOT NULL,
  `Product_ID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Added_At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`Cart_ID`, `Customer_ID`, `Product_ID`, `Quantity`, `Price`, `Added_At`) VALUES
(16, 2, 3, 2, 500.00, '2025-06-15 16:46:18'),
(19, 4, 3, 1, 500.00, '2025-06-15 17:10:00'),
(20, 2, 4, 1, 544.00, '2025-06-15 17:11:08'),
(21, 2, 8, 1, 180.00, '2025-06-15 17:18:13'),
(23, 3, 1, 1, 30.00, '2025-06-15 17:19:42'),
(51, 7, 5, 5, 1211.00, '2025-06-15 18:40:03'),
(66, 10, 8, 7, 180.00, '2025-06-15 19:14:15'),
(69, 11, 9, 1, 10.00, '2025-06-15 19:17:06'),
(70, 11, 3, 4, 500.00, '2025-06-15 19:23:42'),
(75, 12, 8, 1, 180.00, '2025-06-15 19:29:31');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `Category_ID` int(11) NOT NULL,
  `Category_Name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`Category_ID`, `Category_Name`) VALUES
(1, 'vape'),
(2, 'hirono'),
(3, 'foods'),
(4, 'vape');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `Customer_ID` int(11) NOT NULL,
  `CA_ID` int(11) NOT NULL,
  `Customer_FirstName` varchar(255) DEFAULT NULL,
  `Customer_LastName` varchar(255) DEFAULT NULL,
  `Customer_Phone` varchar(255) DEFAULT NULL,
  `Membership_Status` tinyint(1) DEFAULT 0 COMMENT '0 = Non-member, 1 = Premium Member'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`Customer_ID`, `CA_ID`, `Customer_FirstName`, `Customer_LastName`, `Customer_Phone`, `Membership_Status`) VALUES
(1, 1, 'val', 'de vega', '09086766460', 0),
(2, 2, 'val', 'de vega', '09086866460', 0),
(3, 3, 'Val Anthony', 'De Vega', '09060820723', 0),
(4, 4, 'Josh Andrew', 'Cumpas', '0985082888', 0),
(5, 5, 'Elouie', 'Stewart', '09876784321', 0),
(6, 6, 'pop', 'Stewart', '0988877453', 0),
(7, 7, 'Nina', 'Pum', '098712345673', 0),
(8, 8, 'Peter', 'Griffin', '0985435672', 1),
(9, 9, 'Meg', 'Griffin', '098763456123', 1),
(10, 10, 'Jade', 'Caponpon', '097834551234', 1),
(11, 11, 'Yami', 'Fu', '0987654321', 1),
(12, 12, 'Feng', 'Min', '0987656789', 1);

-- --------------------------------------------------------

--
-- Table structure for table `customer_address`
--

CREATE TABLE `customer_address` (
  `CA_ID` int(11) NOT NULL,
  `CA_Street` varchar(255) DEFAULT NULL,
  `CA_Barangay` varchar(255) DEFAULT NULL,
  `CA_City` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_address`
--

INSERT INTO `customer_address` (`CA_ID`, `CA_Street`, `CA_Barangay`, `CA_City`) VALUES
(1, 'brixton', 'malay', 'lipa'),
(2, 'brixton', 'malay', 'lipa'),
(3, 'Kanto', 'Tinyo', 'Batangas'),
(4, 'Kanto', 'Tinyo', 'Batangas'),
(5, 'Kanto', 'Tinyo', 'Batangas'),
(6, 'Kanto', 'Tinyo', 'Batangas'),
(7, 'Kanto', 'Tinyo', 'Batangas'),
(8, 'Kanto', 'Tinyo', 'Batangas'),
(9, 'Kanto', 'Tinyo', 'Batangas'),
(10, 'Kanto', 'Tinyo', 'Batangas'),
(11, 'Kanto', 'Tinyo', 'Batangas'),
(12, 'Kanto', 'Tinyo', 'Batangas');

-- --------------------------------------------------------

--
-- Table structure for table `customer_discount`
--

CREATE TABLE `customer_discount` (
  `CustDisc` int(11) NOT NULL,
  `Discount_ID` int(11) NOT NULL,
  `Customer_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_points`
--

CREATE TABLE `customer_points` (
  `Points_ID` int(11) NOT NULL,
  `Customer_ID` int(11) NOT NULL,
  `Points_Balance` int(11) NOT NULL DEFAULT 0,
  `Points_Earned_Date` datetime DEFAULT NULL,
  `Points_Redeemed_Date` datetime DEFAULT NULL,
  `Transaction_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_points`
--

INSERT INTO `customer_points` (`Points_ID`, `Customer_ID`, `Points_Balance`, `Points_Earned_Date`, `Points_Redeemed_Date`, `Transaction_ID`) VALUES
(10, 6, 40, '2025-06-15 19:50:16', NULL, 30),
(11, 6, 55, '2025-06-15 20:06:11', NULL, 31),
(12, 6, 30, '2025-06-15 20:20:36', NULL, 32),
(13, 6, 5, '2025-06-15 20:21:03', NULL, 33),
(14, 6, -125, NULL, '2025-06-15 20:21:03', 33),
(15, 6, 90, '2025-06-15 20:21:37', NULL, 34),
(16, 7, 135, '2025-06-15 20:31:29', NULL, 35),
(17, 7, -30, NULL, '2025-06-15 20:38:04', 36),
(18, 8, 240, '2025-06-15 20:42:24', NULL, 37),
(19, 8, -180, NULL, '2025-06-15 20:43:45', 38),
(20, 9, 420, '2025-06-15 20:46:51', NULL, 39),
(21, 9, -30, NULL, '2025-06-15 20:47:53', 40),
(22, 9, 10, '2025-06-15 20:48:39', NULL, 41),
(23, 9, -390, NULL, '2025-06-15 20:48:39', 41),
(24, 9, 15, '2025-06-15 20:49:44', NULL, 42),
(25, 9, 110, '2025-06-15 20:50:22', NULL, 43),
(26, 10, 135, '2025-06-15 21:06:05', NULL, 45),
(27, 10, -30, NULL, '2025-06-15 21:06:39', 46),
(28, 10, -105, NULL, '2025-06-15 21:07:42', 47),
(29, 11, 240, '2025-06-15 21:15:06', NULL, 48),
(30, 11, -30, NULL, '2025-06-15 21:16:08', 49),
(31, 12, 75, '2025-06-15 21:24:41', NULL, 50),
(32, 12, -40, NULL, '2025-06-15 21:28:23', 51),
(33, 12, 5, '2025-06-15 21:29:24', NULL, 52);

-- --------------------------------------------------------

--
-- Table structure for table `discount`
--

CREATE TABLE `discount` (
  `Discount_ID` int(11) NOT NULL,
  `Discount_Type` varchar(255) DEFAULT NULL,
  `Discount_Percentage` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discount`
--

INSERT INTO `discount` (`Discount_ID`, `Discount_Type`, `Discount_Percentage`) VALUES
(1, 'Points Redemption', 50);

-- --------------------------------------------------------

--
-- Table structure for table `employee_salary`
--

CREATE TABLE `employee_salary` (
  `EmployeeSa_ID` int(11) NOT NULL,
  `User_Account_ID` int(11) NOT NULL,
  `Salary_Amount` int(11) DEFAULT NULL,
  `Payout_Date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_salary`
--

INSERT INTO `employee_salary` (`EmployeeSa_ID`, `User_Account_ID`, `Salary_Amount`, `Payout_Date`) VALUES
(1, 2, 0, '2025-06-15 12:15:23'),
(2, 3, 7, '2025-06-15 12:15:56'),
(3, 6, 0, '2025-06-15 12:40:31'),
(4, 2, 0, '2025-06-15 12:40:54'),
(5, 6, 3000, '2025-06-15 12:41:16'),
(6, 2, 0, '2025-06-15 12:41:58'),
(7, 6, 3000, '2025-06-15 12:42:23'),
(8, 2, 0, '2025-06-15 12:48:02'),
(9, 7, 30, '2025-06-15 12:49:13'),
(10, 7, 10, '2025-06-15 12:49:45'),
(11, 2, 0, '2025-06-15 12:54:03'),
(12, 6, 95000, '2025-06-15 13:53:04'),
(13, 2, 0, '2025-06-15 13:53:45'),
(14, 8, 10, '2025-06-15 13:55:07'),
(15, 2, 0, '2025-06-15 13:55:13'),
(16, 8, 70, '2025-06-15 13:55:55'),
(17, 2, 0, '2025-06-15 13:57:17'),
(18, 8, 60, '2025-06-15 14:03:16'),
(19, 8, 1250, '2025-06-15 14:14:06'),
(20, 6, 7000, '2025-06-15 14:22:34'),
(21, 6, 41000, '2025-06-15 14:26:50'),
(22, 6, 2000, '2025-06-15 14:30:21'),
(23, 9, 10, '2025-06-15 14:39:10'),
(24, 2, 0, '2025-06-15 14:39:33'),
(25, 9, 20, '2025-06-15 14:39:51'),
(26, 2, 0, '2025-06-15 14:40:29'),
(27, 2, 0, '2025-06-15 14:41:26'),
(28, 9, 1030, '2025-06-15 14:50:13'),
(29, 6, 63000, '2025-06-15 14:55:35'),
(30, 6, 11000, '2025-06-15 14:57:15'),
(31, 2, 0, '2025-06-15 14:57:26'),
(32, 2, 0, '2025-06-15 14:57:41'),
(33, 8, 5330, '2025-06-15 18:08:05'),
(34, 2, 0, '2025-06-15 18:09:17'),
(35, 10, 30, '2025-06-15 18:10:06'),
(36, 2, 0, '2025-06-15 18:10:51'),
(37, 10, 3990, '2025-06-15 18:44:20'),
(38, 8, 850, '2025-06-15 18:52:10'),
(39, 2, 0, '2025-06-15 18:52:54');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `Expenses_ID` int(11) NOT NULL,
  `EmployeeSa_ID` int(11) DEFAULT NULL,
  `Income_ID` int(11) DEFAULT NULL,
  `Supply_Fees` decimal(10,2) DEFAULT NULL,
  `Utilities` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `income`
--

CREATE TABLE `income` (
  `Income_ID` int(11) NOT NULL,
  `Payment_ID` int(11) NOT NULL,
  `Income_Amount` decimal(10,2) DEFAULT NULL,
  `Income_Date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `income`
--

INSERT INTO `income` (`Income_ID`, `Payment_ID`, `Income_Amount`, `Income_Date`) VALUES
(1, 1, 1800.00, '2025-06-14 16:13:44'),
(2, 2, 900.00, '2025-06-14 16:14:06'),
(3, 3, 1500.00, '2025-06-15 10:27:14');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `Payment_ID` int(11) NOT NULL,
  `Transaction_ID` int(11) NOT NULL,
  `Payment_Type` varchar(255) DEFAULT NULL,
  `Payment_Date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Payment_Amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`Payment_ID`, `Transaction_ID`, `Payment_Type`, `Payment_Date`, `Payment_Amount`) VALUES
(1, 1, 'Cash', '2025-06-14 16:13:44', 1800.00),
(2, 2, 'Cash', '2025-06-14 16:14:06', 900.00),
(3, 3, 'Cash', '2025-06-15 10:27:14', 1500.00);

-- --------------------------------------------------------

--
-- Table structure for table `position_details`
--

CREATE TABLE `position_details` (
  `Position_Details_ID` int(11) NOT NULL,
  `Position` varchar(255) DEFAULT NULL,
  `Position_Status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `position_details`
--

INSERT INTO `position_details` (`Position_Details_ID`, `Position`, `Position_Status`) VALUES
(1, 'Admin', '1'),
(2, 'Cashier', '1'),
(3, 'Cashier', 'Active'),
(4, 'Admin', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `Product_ID` int(11) NOT NULL,
  `User_Account_ID` int(11) NOT NULL,
  `Product_Name` varchar(255) DEFAULT NULL,
  `Product_Stock` varchar(255) DEFAULT NULL,
  `Product_Image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`Product_ID`, `User_Account_ID`, `Product_Name`, `Product_Stock`, `Product_Image`) VALUES
(1, 1, 'Kwek Kwek', '32', 'uploads/prod_684edf03b5321_kwek-kwek-13.jpg'),
(2, 1, 'HIRONO', '23', 'images/product_684d9b2d1f9a3_download (9).jpeg'),
(3, 1, 'vape v2 chocolate', '18', 'uploads/prod_684edb8f1a333_mock.jpg'),
(4, 1, 'vape v2 chocolate', '13', 'uploads/prod_684ed6f827fc9_SAM_0340.JPG'),
(5, 1, 'v2', '5', 'images/product_684da8cabac02_v2.jpeg'),
(7, 1, 'Waffle', '7', 'images/product_684ed78b29c4f_SAM_0208.JPG'),
(8, 1, 'Stick 0', '8', 'images/product_684edf4570c7e_Sticko.jpg'),
(9, 1, 'Binky', '33', 'images/product_684f1637a3e9a_PHYSICAL-FINAL1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

CREATE TABLE `product_category` (
  `ProdCat_ID` int(11) NOT NULL,
  `Product_ID` int(11) NOT NULL,
  `Category_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_category`
--

INSERT INTO `product_category` (`ProdCat_ID`, `Product_ID`, `Category_ID`) VALUES
(3, 2, 2),
(7, 5, 1),
(8, 5, 4),
(11, 4, 1),
(12, 4, 2),
(14, 7, 3),
(15, 7, 4),
(16, 3, 1),
(18, 8, 3),
(19, 1, 3),
(20, 9, 3);

-- --------------------------------------------------------

--
-- Table structure for table `product_price`
--

CREATE TABLE `product_price` (
  `Price_ID` int(11) NOT NULL,
  `Product_ID` int(11) NOT NULL,
  `Price` decimal(10,2) DEFAULT NULL,
  `Effective_From` date DEFAULT NULL,
  `Effective_To` date DEFAULT NULL,
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_price`
--

INSERT INTO `product_price` (`Price_ID`, `Product_ID`, `Price`, `Effective_From`, `Effective_To`, `Created_At`) VALUES
(1, 1, 900.00, '2025-06-14', '0000-00-00', '2025-06-14 09:53:54'),
(2, 2, 600.00, '2025-06-14', '0000-00-00', '2025-06-14 09:54:21'),
(3, 3, 500.00, '2025-06-14', '0000-00-00', '2025-06-14 09:54:42'),
(4, 4, 544.00, '2025-06-14', '0000-00-00', '2025-06-14 10:51:28'),
(5, 5, 1211.00, '2025-06-14', '0000-00-00', '2025-06-14 10:52:26'),
(7, 1, 30.00, '2025-06-14', '2025-06-15', '2025-06-15 14:19:48'),
(8, 1, 30.00, '2025-06-14', '2025-06-15', '2025-06-15 14:22:50'),
(9, 4, 544.00, '2025-06-14', NULL, '2025-06-15 14:21:44'),
(10, 1, 30.00, '2025-06-14', '2025-06-15', '2025-06-15 14:56:03'),
(11, 7, 300.00, '2025-06-15', '2025-07-04', '2025-06-15 08:24:11'),
(12, 3, 500.00, '2025-06-14', NULL, '2025-06-15 14:41:19'),
(13, 1, 30.00, '2025-06-14', '2025-06-16', '2025-06-15 18:10:44'),
(14, 8, 180.00, '2025-06-15', '2025-07-03', '2025-06-15 08:57:09'),
(15, 1, 30.00, '2025-06-14', NULL, '2025-06-15 18:10:44'),
(16, 9, 10.00, '2025-06-15', '2025-07-02', '2025-06-15 12:51:35');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_items`
--

CREATE TABLE `transaction_items` (
  `Transaction_Item_ID` int(11) NOT NULL,
  `Transaction_ID` int(11) DEFAULT NULL,
  `Product_ID` int(11) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `Original_Price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_items`
--

INSERT INTO `transaction_items` (`Transaction_Item_ID`, `Transaction_ID`, `Product_ID`, `Quantity`, `Original_Price`) VALUES
(1, 1, 1, 1, 900.00),
(2, 1, 1, 1, 900.00),
(3, 2, 1, 1, 900.00),
(4, 3, 1, 1, 900.00),
(5, 3, 2, 1, 600.00),
(6, 6, 4, 2, NULL),
(7, 6, 8, 6, NULL),
(8, 6, 1, 1, NULL),
(9, 6, 3, 6, NULL),
(10, 7, 3, 1, NULL),
(11, 8, 3, 1, NULL),
(12, 9, 4, 1, NULL),
(13, 10, 4, 1, NULL),
(16, 13, 3, 1, 500.00),
(17, 13, 3, 2, 500.00),
(21, 15, 3, 1, 500.00),
(22, 15, 3, 1, 500.00),
(23, 15, 3, 1, 500.00),
(24, 15, 5, 1, 1211.00),
(25, 16, 5, 1, 1211.00),
(27, 18, 1, 1, 30.00),
(28, 19, 1, 1, 30.00),
(34, 23, 1, 1, 30.00),
(36, 25, 1, 1, 30.00),
(37, 26, 1, 1, 30.00),
(45, 30, 8, 1, 180.00),
(46, 30, 8, 1, 180.00),
(47, 30, 8, 1, 180.00),
(48, 30, 7, 1, 300.00),
(49, 31, 1, 1, 30.00),
(50, 31, 2, 1, 600.00),
(51, 31, 4, 1, 544.00),
(52, 32, 7, 1, 300.00),
(53, 32, 7, 1, 300.00),
(54, 33, 7, 1, 300.00),
(55, 34, 7, 6, 300.00),
(56, 35, 4, 1, 544.00),
(57, 35, 4, 4, 544.00),
(58, 36, 1, 1, 30.00),
(59, 37, 5, 4, 1211.00),
(60, 38, 8, 1, 180.00),
(61, 39, 5, 7, 1211.00),
(62, 40, 1, 1, 30.00),
(63, 41, 7, 2, 300.00),
(64, 42, 1, 1, 30.00),
(65, 42, 7, 1, 300.00),
(66, 43, 1, 1, 30.00),
(67, 43, 4, 4, 544.00),
(68, 44, 1, 1, 30.00),
(69, 45, 4, 1, 544.00),
(70, 45, 4, 4, 544.00),
(71, 46, 1, 1, 30.00),
(72, 47, 8, 1, 180.00),
(73, 48, 5, 4, 1211.00),
(74, 49, 1, 1, 30.00),
(75, 50, 3, 3, 500.00),
(76, 51, 9, 1, 10.00),
(77, 51, 1, 1, 30.00),
(78, 52, 8, 1, 180.00);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_ml`
--

CREATE TABLE `transaction_ml` (
  `Transaction_ID` int(11) NOT NULL,
  `Customer_ID` int(11) DEFAULT NULL,
  `Trans_Total` decimal(10,2) DEFAULT NULL,
  `Points_Redeemed` int(11) DEFAULT 0,
  `Transaction_Date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_ml`
--

INSERT INTO `transaction_ml` (`Transaction_ID`, `Customer_ID`, `Trans_Total`, `Points_Redeemed`, `Transaction_Date`) VALUES
(1, 1, 1800.00, 0, '2025-06-15'),
(2, 1, 900.00, 0, '2025-06-15'),
(3, 1, 1500.00, 0, '2025-06-15'),
(6, NULL, 5198.00, 0, NULL),
(7, NULL, 500.00, 0, NULL),
(8, NULL, 500.00, 0, NULL),
(9, NULL, 544.00, 0, NULL),
(10, NULL, 544.00, 0, NULL),
(13, NULL, 1500.00, 0, '2025-06-16'),
(15, NULL, 2711.00, 0, '2025-06-16'),
(16, NULL, 1211.00, 0, '2025-06-16'),
(18, NULL, 30.00, 0, '2025-06-16'),
(19, 5, 30.00, 0, '2025-06-16'),
(23, 5, 30.00, 0, '2025-06-16'),
(25, 5, 30.00, 0, '2025-06-16'),
(26, 5, 30.00, 0, '2025-06-16'),
(30, 6, 840.00, 0, '2025-06-16'),
(31, 6, 1174.00, 0, '2025-06-16'),
(32, 6, 600.00, 0, '2025-06-16'),
(33, 6, 175.00, 125, '2025-06-16'),
(34, 6, 1800.00, 0, '2025-06-16'),
(35, 7, 2720.00, 0, '2025-06-16'),
(36, 7, 0.00, 30, '2025-06-16'),
(37, 8, 4844.00, 0, '2025-06-16'),
(38, 8, 0.00, 180, '2025-06-16'),
(39, 9, 8477.00, 0, '2025-06-16'),
(40, 9, 0.00, 30, '2025-06-16'),
(41, 9, 210.00, 390, '2025-06-16'),
(42, 9, 330.00, 0, '2025-06-16'),
(43, 9, 2206.00, 0, '2025-06-16'),
(44, 9, 30.00, 0, '2025-06-16'),
(45, 10, 2720.00, 0, '2025-06-16'),
(46, 10, 0.00, 30, '2025-06-16'),
(47, 10, 75.00, 105, '2025-06-16'),
(48, 11, 4844.00, 0, '2025-06-16'),
(49, 11, 0.00, 30, '2025-06-16'),
(50, 12, 1500.00, 0, '2025-06-16'),
(51, 12, 0.00, 40, '2025-06-16'),
(52, 12, 180.00, 0, '2025-06-16');

-- --------------------------------------------------------

--
-- Table structure for table `user_account`
--

CREATE TABLE `user_account` (
  `User_Account_ID` int(11) NOT NULL,
  `Username` varchar(255) DEFAULT NULL,
  `Pass` varchar(255) DEFAULT NULL,
  `User_Photo` varchar(255) DEFAULT NULL,
  `Hourly_Rate` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`User_Account_ID`, `Username`, `Pass`, `User_Photo`, `Hourly_Rate`) VALUES
(1, 'val', '$2y$10$Uhv7pd1qhuAF8DJDdpGOEOTHbrTT4jW5feaz7MhOiNfEgHUGaxqsC', 'profile_picture/489975713_9221659817942496_7009943061107201733_n_1749916367.jpg', 0.00),
(2, 'Maxweller', '$2y$10$q2Efe1ofQbHts6gkC9LLIuDizzKs5zAuo2TfQWzpjKEJ7mmSHCNEu', 'profile_picture/pfp_1749987394.jpg', 0.00),
(3, 'joshueee', '$2y$10$ZQZxHn/D6femldODF899iuLMMrJV.msunA/g40llU6984hYhDXrri', 'profile_picture/pfp_1749987478.jpg', 1000.00),
(4, 'Patricia', '$2y$10$2fR36wWzkuBslNhkr.TOnO/9iJS9BcUDV51a2VGsC7cBjc8/EPe0u', 'profile_picture/pfp_1749987864.jpg', 20.00),
(5, 'patrick', '$2y$10$ZXFG5/bWeApUs/164Ojvi.YdDKIBHBAiY0un2S9iMLp54673ir/WS', 'profile_picture/pfp_1749991143.jpg', 0.00),
(6, 'Star', '$2y$10$5w2bjbAWh.jrcJuKtXQEe.SuKNns4IPQhuJLGOVwIofpUXY.CpH0u', 'profile_picture/pfp_1749991221.jpg', 1000.00),
(7, 'Johannes', '$2y$10$gjzReckWtYYH2Kk7XcuzU.ZF1jL8wacOld9pKH0OeHcBQ3pYxiTym', 'profile_picture/pfp_1749991653.jpg', 10.00),
(8, 'Martin', '$2y$10$xGK6sHtsoznk0yU0K6wDYucjLqJaWuFADEGIonVyy4PKlGu.6DVdm', 'profile_picture/pfp_1749995694.jpg', 10.00),
(9, 'Yumi', '$2y$10$cisz9SqTev..ISw3ejIAIOdsmFvJGqsR2gpldw9td/NjShUJTl8RO', 'profile_picture/SAM_0202_1749998336.jpg', 10.00),
(10, 'party', '$2y$10$INS1kepL3EpmC3r9MJiAA.BBcDbYveqRRGxwurO5S.wi8No2vvxMi', 'profile_picture/mock_1750010985.jpg', 10.00);

-- --------------------------------------------------------

--
-- Table structure for table `user_position`
--

CREATE TABLE `user_position` (
  `Position_ID` int(11) NOT NULL,
  `User_Account_ID` int(11) NOT NULL,
  `Position_Details_ID` int(11) NOT NULL,
  `Start_Date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `End_Date` date DEFAULT NULL,
  `User_Status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_position`
--

INSERT INTO `user_position` (`Position_ID`, `User_Account_ID`, `Position_Details_ID`, `Start_Date`, `End_Date`, `User_Status`) VALUES
(1, 1, 1, '2025-06-15 12:50:58', '2025-06-15', 0),
(2, 2, 4, '2025-06-15 12:50:52', NULL, 1),
(3, 3, 2, '2025-06-15 12:32:31', '2025-06-15', 0),
(4, 4, 2, '2025-06-15 12:32:35', '2025-06-15', 0),
(5, 5, 3, '2025-06-15 12:39:39', NULL, 1),
(6, 6, 2, '2025-06-15 12:40:21', NULL, 1),
(7, 7, 2, '2025-06-15 12:47:33', NULL, 1),
(8, 8, 2, '2025-06-15 13:54:54', NULL, 1),
(9, 9, 3, '2025-06-15 14:38:56', NULL, 1),
(10, 10, 3, '2025-06-15 18:09:45', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `Session_ID` int(11) NOT NULL,
  `User_Account_ID` int(11) NOT NULL,
  `Login_Time` timestamp NOT NULL DEFAULT current_timestamp(),
  `Logout_Time` timestamp NULL DEFAULT NULL,
  `Session_Duration` int(11) GENERATED ALWAYS AS (timestampdiff(SECOND,`Login_Time`,`Logout_Time`)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_sessions`
--

INSERT INTO `user_sessions` (`Session_ID`, `User_Account_ID`, `Login_Time`, `Logout_Time`) VALUES
(1, 2, '2025-06-15 11:58:51', '2025-06-15 12:01:50'),
(2, 3, '2025-06-15 12:01:59', '2025-06-15 12:02:42'),
(3, 2, '2025-06-15 12:02:45', '2025-06-15 12:03:39'),
(4, 4, '2025-06-15 12:03:48', '2025-06-15 12:04:21'),
(5, 4, '2025-06-15 12:04:33', '2025-06-15 12:05:25'),
(6, 2, '2025-06-15 12:05:28', '2025-06-15 12:06:39'),
(7, 3, '2025-06-15 12:06:45', '2025-06-15 12:07:03'),
(8, 2, '2025-06-15 12:07:06', '2025-06-15 12:15:23'),
(9, 3, '2025-06-15 12:15:27', '2025-06-15 12:15:56'),
(10, 2, '2025-06-15 12:15:59', NULL),
(11, 2, '2025-06-15 12:38:36', NULL),
(12, 5, '2025-06-15 12:39:14', NULL),
(13, 2, '2025-06-15 12:39:32', NULL),
(14, 2, '2025-06-15 12:39:49', NULL),
(15, 6, '2025-06-15 12:40:26', '2025-06-15 12:40:31'),
(16, 2, '2025-06-15 12:40:39', '2025-06-15 12:40:54'),
(17, 6, '2025-06-15 12:40:59', '2025-06-15 12:41:16'),
(18, 2, '2025-06-15 12:41:23', '2025-06-15 12:41:58'),
(19, 6, '2025-06-15 12:42:05', '2025-06-15 12:42:23'),
(20, 2, '2025-06-15 12:42:26', NULL),
(21, 2, '2025-06-15 12:47:36', '2025-06-15 12:48:02'),
(22, 7, '2025-06-15 12:48:56', '2025-06-15 12:49:13'),
(23, 2, '2025-06-15 12:49:21', NULL),
(24, 2, '2025-06-15 12:49:32', NULL),
(25, 7, '2025-06-15 12:49:40', '2025-06-15 12:49:45'),
(26, 2, '2025-06-15 12:49:47', '2025-06-15 12:54:03'),
(27, 2, '2025-06-15 12:54:07', NULL),
(28, 6, '2025-06-15 13:45:08', '2025-06-15 13:53:04'),
(29, 2, '2025-06-15 13:53:27', '2025-06-15 13:53:45'),
(30, 2, '2025-06-15 13:53:48', NULL),
(31, 2, '2025-06-15 13:54:18', NULL),
(32, 8, '2025-06-15 13:55:01', '2025-06-15 13:55:07'),
(33, 2, '2025-06-15 13:55:09', '2025-06-15 13:55:13'),
(34, 8, '2025-06-15 13:55:19', '2025-06-15 13:55:55'),
(35, 2, '2025-06-15 13:55:57', '2025-06-15 13:57:17'),
(36, 2, '2025-06-15 13:57:20', NULL),
(37, 8, '2025-06-15 14:02:42', '2025-06-15 14:03:16'),
(38, 2, '2025-06-15 14:03:19', NULL),
(39, 8, '2025-06-15 14:03:41', '2025-06-15 14:14:06'),
(40, 2, '2025-06-15 14:14:09', NULL),
(41, 6, '2025-06-15 14:20:15', NULL),
(42, 6, '2025-06-15 14:21:06', NULL),
(43, 2, '2025-06-15 14:21:23', NULL),
(44, 6, '2025-06-15 14:21:59', '2025-06-15 14:22:34'),
(45, 2, '2025-06-15 14:22:37', NULL),
(46, 2, '2025-06-15 14:23:04', NULL),
(47, 6, '2025-06-15 14:23:21', '2025-06-15 14:26:50'),
(48, 6, '2025-06-15 14:26:55', NULL),
(49, 6, '2025-06-15 14:30:08', '2025-06-15 14:30:21'),
(50, 2, '2025-06-15 14:30:23', NULL),
(51, 9, '2025-06-15 14:39:02', '2025-06-15 14:39:10'),
(52, 2, '2025-06-15 14:39:14', '2025-06-15 14:39:33'),
(53, 9, '2025-06-15 14:39:38', '2025-06-15 14:39:51'),
(54, 2, '2025-06-15 14:39:54', '2025-06-15 14:40:29'),
(55, 2, '2025-06-15 14:40:35', '2025-06-15 14:41:26'),
(56, 2, '2025-06-15 14:41:29', NULL),
(57, 9, '2025-06-15 14:41:38', '2025-06-15 14:50:13'),
(58, 6, '2025-06-15 14:50:20', '2025-06-15 14:55:35'),
(59, 2, '2025-06-15 14:55:37', NULL),
(60, 6, '2025-06-15 14:56:17', '2025-06-15 14:57:15'),
(61, 2, '2025-06-15 14:57:17', '2025-06-15 14:57:26'),
(62, 2, '2025-06-15 14:57:28', '2025-06-15 14:57:41'),
(63, 6, '2025-06-15 14:58:25', NULL),
(64, 8, '2025-06-15 17:23:39', '2025-06-15 18:08:05'),
(65, 2, '2025-06-15 18:08:08', '2025-06-15 18:09:17'),
(66, 10, '2025-06-15 18:09:49', '2025-06-15 18:10:06'),
(67, 2, '2025-06-15 18:10:08', '2025-06-15 18:10:51'),
(68, 10, '2025-06-15 18:11:01', '2025-06-15 18:44:20'),
(69, 8, '2025-06-15 18:45:04', '2025-06-15 18:52:10'),
(70, 2, '2025-06-15 18:52:13', '2025-06-15 18:52:54'),
(71, 6, '2025-06-15 19:03:32', NULL),
(72, 6, '2025-06-15 19:23:08', NULL),
(73, 2, '2025-06-15 19:24:48', NULL),
(74, 6, '2025-06-15 19:25:06', NULL),
(75, 6, '2025-06-15 19:27:50', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `user_total_salary`
-- (See below for the actual view)
--
CREATE TABLE `user_total_salary` (
`User_Account_ID` int(11)
,`Total_Salary` decimal(46,6)
);

-- --------------------------------------------------------

--
-- Structure for view `user_total_salary`
--
DROP TABLE IF EXISTS `user_total_salary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_total_salary`  AS SELECT `us`.`User_Account_ID` AS `User_Account_ID`, sum(`us`.`Session_Duration` / 3600 * `ua`.`Hourly_Rate`) AS `Total_Salary` FROM (`user_sessions` `us` join `user_account` `ua` on(`us`.`User_Account_ID` = `ua`.`User_Account_ID`)) WHERE `us`.`Logout_Time` is not null GROUP BY `us`.`User_Account_ID` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`Cart_ID`),
  ADD KEY `Customer_ID` (`Customer_ID`),
  ADD KEY `Product_ID` (`Product_ID`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`Category_ID`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`Customer_ID`),
  ADD UNIQUE KEY `Customer_Phone` (`Customer_Phone`),
  ADD KEY `CA_ID` (`CA_ID`);

--
-- Indexes for table `customer_address`
--
ALTER TABLE `customer_address`
  ADD PRIMARY KEY (`CA_ID`);

--
-- Indexes for table `customer_discount`
--
ALTER TABLE `customer_discount`
  ADD PRIMARY KEY (`CustDisc`),
  ADD KEY `Discount_ID` (`Discount_ID`),
  ADD KEY `Customer_ID` (`Customer_ID`);

--
-- Indexes for table `customer_points`
--
ALTER TABLE `customer_points`
  ADD PRIMARY KEY (`Points_ID`),
  ADD KEY `customer_points_ibfk_1` (`Customer_ID`),
  ADD KEY `customer_points_ibfk_2` (`Transaction_ID`);

--
-- Indexes for table `discount`
--
ALTER TABLE `discount`
  ADD PRIMARY KEY (`Discount_ID`);

--
-- Indexes for table `employee_salary`
--
ALTER TABLE `employee_salary`
  ADD PRIMARY KEY (`EmployeeSa_ID`),
  ADD KEY `User_Account_ID` (`User_Account_ID`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`Expenses_ID`),
  ADD KEY `EmployeeSa_ID` (`EmployeeSa_ID`),
  ADD KEY `Income_ID` (`Income_ID`);

--
-- Indexes for table `income`
--
ALTER TABLE `income`
  ADD PRIMARY KEY (`Income_ID`),
  ADD KEY `Payment_ID` (`Payment_ID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`Payment_ID`),
  ADD KEY `Transaction_ID` (`Transaction_ID`);

--
-- Indexes for table `position_details`
--
ALTER TABLE `position_details`
  ADD PRIMARY KEY (`Position_Details_ID`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`Product_ID`),
  ADD KEY `User_Account_ID` (`User_Account_ID`);

--
-- Indexes for table `product_category`
--
ALTER TABLE `product_category`
  ADD PRIMARY KEY (`ProdCat_ID`),
  ADD KEY `Product_ID` (`Product_ID`),
  ADD KEY `Category_ID` (`Category_ID`);

--
-- Indexes for table `product_price`
--
ALTER TABLE `product_price`
  ADD PRIMARY KEY (`Price_ID`),
  ADD KEY `Product_ID` (`Product_ID`);

--
-- Indexes for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`Transaction_Item_ID`),
  ADD KEY `Transaction_ID` (`Transaction_ID`),
  ADD KEY `Product_ID` (`Product_ID`);

--
-- Indexes for table `transaction_ml`
--
ALTER TABLE `transaction_ml`
  ADD PRIMARY KEY (`Transaction_ID`),
  ADD KEY `Customer_ID` (`Customer_ID`);

--
-- Indexes for table `user_account`
--
ALTER TABLE `user_account`
  ADD PRIMARY KEY (`User_Account_ID`);

--
-- Indexes for table `user_position`
--
ALTER TABLE `user_position`
  ADD PRIMARY KEY (`Position_ID`),
  ADD KEY `User_Account_ID` (`User_Account_ID`),
  ADD KEY `Position_Details_ID` (`Position_Details_ID`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`Session_ID`),
  ADD KEY `User_Account_ID` (`User_Account_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `Cart_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `Category_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `Customer_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `customer_address`
--
ALTER TABLE `customer_address`
  MODIFY `CA_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `customer_discount`
--
ALTER TABLE `customer_discount`
  MODIFY `CustDisc` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_points`
--
ALTER TABLE `customer_points`
  MODIFY `Points_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `discount`
--
ALTER TABLE `discount`
  MODIFY `Discount_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employee_salary`
--
ALTER TABLE `employee_salary`
  MODIFY `EmployeeSa_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `Expenses_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `income`
--
ALTER TABLE `income`
  MODIFY `Income_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `Payment_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `position_details`
--
ALTER TABLE `position_details`
  MODIFY `Position_Details_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `Product_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product_category`
--
ALTER TABLE `product_category`
  MODIFY `ProdCat_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `product_price`
--
ALTER TABLE `product_price`
  MODIFY `Price_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `Transaction_Item_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `transaction_ml`
--
ALTER TABLE `transaction_ml`
  MODIFY `Transaction_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `user_account`
--
ALTER TABLE `user_account`
  MODIFY `User_Account_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_position`
--
ALTER TABLE `user_position`
  MODIFY `Position_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `Session_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`Customer_ID`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`);

--
-- Constraints for table `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`CA_ID`) REFERENCES `customer_address` (`CA_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `customer_discount`
--
ALTER TABLE `customer_discount`
  ADD CONSTRAINT `customer_discount_ibfk_1` FOREIGN KEY (`Discount_ID`) REFERENCES `discount` (`Discount_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `customer_discount_ibfk_2` FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`Customer_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `customer_points`
--
ALTER TABLE `customer_points`
  ADD CONSTRAINT `customer_points_ibfk_1` FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`Customer_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `customer_points_ibfk_2` FOREIGN KEY (`Transaction_ID`) REFERENCES `transaction_ml` (`Transaction_ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `employee_salary`
--
ALTER TABLE `employee_salary`
  ADD CONSTRAINT `employee_salary_ibfk_1` FOREIGN KEY (`User_Account_ID`) REFERENCES `user_account` (`User_Account_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`EmployeeSa_ID`) REFERENCES `employee_salary` (`EmployeeSa_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`Income_ID`) REFERENCES `income` (`Income_ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `income`
--
ALTER TABLE `income`
  ADD CONSTRAINT `income_ibfk_1` FOREIGN KEY (`Payment_ID`) REFERENCES `payment` (`Payment_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`Transaction_ID`) REFERENCES `transaction_ml` (`Transaction_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`User_Account_ID`) REFERENCES `user_account` (`User_Account_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `product_category`
--
ALTER TABLE `product_category`
  ADD CONSTRAINT `product_category_ibfk_1` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `product_category_ibfk_2` FOREIGN KEY (`Category_ID`) REFERENCES `category` (`Category_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `product_price`
--
ALTER TABLE `product_price`
  ADD CONSTRAINT `product_price_ibfk_1` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`Transaction_ID`) REFERENCES `transaction_ml` (`Transaction_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_items_ibfk_2` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `transaction_ml`
--
ALTER TABLE `transaction_ml`
  ADD CONSTRAINT `transaction_ml_ibfk_1` FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`Customer_ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `user_position`
--
ALTER TABLE `user_position`
  ADD CONSTRAINT `user_position_ibfk_1` FOREIGN KEY (`User_Account_ID`) REFERENCES `user_account` (`User_Account_ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `user_position_ibfk_2` FOREIGN KEY (`Position_Details_ID`) REFERENCES `position_details` (`Position_Details_ID`) ON UPDATE CASCADE;

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`User_Account_ID`) REFERENCES `user_account` (`User_Account_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
