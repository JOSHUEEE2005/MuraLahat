-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2025 at 05:52 PM
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
-- Database: `help_muralahat`
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
(22, 1, 11, 1, 600.00, '2025-06-11 14:45:52'),
(23, 1, 9, 1, 600.00, '2025-06-11 14:45:55'),
(24, 1, 10, 1, 600.00, '2025-06-11 14:46:01');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `Category_ID` int(11) NOT NULL,
  `Category_Name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `Customer_ID` int(11) NOT NULL,
  `Customer_FirstName` varchar(255) DEFAULT NULL,
  `Customer_LastName` varchar(255) DEFAULT NULL,
  `Customer_Phone` varchar(255) DEFAULT NULL,
  `Membership_Status` tinyint(1) DEFAULT 0 COMMENT '0 = Non-member, 1 = Premium Member'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`Customer_ID`, `Customer_FirstName`, `Customer_LastName`, `Customer_Phone`, `Membership_Status`) VALUES
(1, 'Charisse', 'Begino', '09060820723', 1),
(2, 'Josh Andrew', 'Cumpas', '0985082888', 1),
(3, 'Val Anthony', 'De Vega', '09554615323', 1),
(4, 'Claire', 'Andal', '0988877453', 1),
(5, 'Martha', 'Stewart', '1234567890', 1);

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
-- Table structure for table `discount`
--

CREATE TABLE `discount` (
  `Discount_ID` int(11) NOT NULL,
  `Discount_Type` varchar(255) DEFAULT NULL,
  `Discount_Percentage` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 1, 10200.00, '2025-06-11 14:06:07'),
(2, 2, 3600.00, '2025-06-11 14:36:20'),
(3, 3, 3000.00, '2025-06-11 14:41:11'),
(4, 4, 3000.00, '2025-06-11 14:42:28'),
(5, 5, 600.00, '2025-06-11 14:43:51'),
(6, 6, 1800.00, '2025-06-11 14:45:05');

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
(1, 1, 'Cash', '2025-06-11 14:06:07', 10200.00),
(2, 2, 'Cash', '2025-06-11 14:36:20', 3600.00),
(3, 3, 'Cash', '2025-06-11 14:41:11', 3000.00),
(4, 4, 'Cash', '2025-06-11 14:42:28', 3000.00),
(5, 5, 'Cash', '2025-06-11 14:43:51', 600.00),
(6, 6, 'Cash', '2025-06-11 14:45:05', 1800.00);

-- --------------------------------------------------------

--
-- Table structure for table `position_details`
--

CREATE TABLE `position_details` (
  `Position_Details_ID` int(11) NOT NULL,
  `Position_ID` int(11) NOT NULL,
  `Position` varchar(255) DEFAULT NULL,
  `Position_Status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `position_details`
--

INSERT INTO `position_details` (`Position_Details_ID`, `Position_ID`, `Position`, `Position_Status`) VALUES
(1, 1, 'Manager', 'Active'),
(2, 2, 'Admin', 'Active'),
(3, 3, 'Admin', 'Active'),
(4, 4, 'Cashier', 'Active'),
(5, 5, 'Manager', 'Active'),
(7, 7, 'Cashier', 'Active');

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
(2, 1, 'Kids Study Chair ', '50', NULL),
(5, 1, 'Puzzle mat ', '30', NULL),
(6, 1, 'Hirono The Other One', '8', 'images/product_68496f2a138fb_TOO.jpg'),
(7, 1, 'Hirono echo', '5', 'images/product_68496fa90723c_Echo.jpg'),
(8, 1, 'Hirono Mime', '2', 'images/product_68496fcd26a28_mime.jpg'),
(9, 1, 'Hirono Clot', '6', 'images/product_68496fe8f10ad_Clot.jpg'),
(10, 1, 'Hirono shelter', '80', 'images/product_684970374f0f8_shelter.jpg'),
(11, 1, 'Hirono LPP', '30', 'images/product_684970623dfea_LPP.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

CREATE TABLE `product_category` (
  `ProdCat_ID` int(11) NOT NULL,
  `Product_ID` int(11) NOT NULL,
  `Category_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(2, 2, 200.00, '2025-06-06', '2025-06-06', '2025-06-05 16:00:00'),
(5, 5, 200.00, '2025-06-19', '2025-06-25', '2025-06-05 16:00:00'),
(6, 6, 600.00, '2025-06-11', '2025-06-27', '2025-06-11 05:57:30'),
(7, 7, 600.00, '2025-06-11', '2025-06-26', '2025-06-11 05:59:37'),
(8, 8, 600.00, '2025-06-11', '2025-06-19', '2025-06-11 06:00:13'),
(9, 9, 600.00, '2025-06-11', '2025-06-24', '2025-06-11 06:00:40'),
(10, 10, 600.00, '2025-06-11', '2025-06-30', '2025-06-11 06:01:59'),
(11, 11, 600.00, '2025-06-11', '2025-06-26', '2025-06-11 06:02:42'),
(12, 6, 600.00, '2025-06-11', '2025-06-27', '2025-06-11 15:04:42'),
(13, 11, 600.00, '2025-06-11', '2025-06-26', '2025-06-11 15:06:04'),
(14, 10, 600.00, '2025-06-11', '2025-06-30', '2025-06-11 15:41:45'),
(15, 10, 700.00, '2025-06-11', '2025-06-30', '2025-06-11 15:41:55');

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
(1, 1, 8, 3, 600.00),
(2, 1, 7, 1, 600.00),
(3, 1, 6, 1, 600.00),
(4, 1, 8, 3, 600.00),
(5, 1, 7, 2, 600.00),
(6, 1, 6, 1, 600.00),
(7, 1, 8, 3, 600.00),
(8, 1, 7, 2, 600.00),
(9, 1, 6, 1, 600.00),
(10, 2, 11, 1, 600.00),
(11, 2, 10, 2, 600.00),
(12, 2, 8, 1, 600.00),
(13, 2, 7, 2, 600.00),
(14, 3, 11, 2, 600.00),
(15, 3, 9, 3, 600.00),
(16, 4, 6, 2, 600.00),
(17, 4, 10, 2, 600.00),
(18, 4, 9, 1, 600.00),
(19, 5, 10, 1, 600.00),
(20, 6, 6, 1, 600.00),
(21, 6, 9, 2, 600.00);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_ml`
--

CREATE TABLE `transaction_ml` (
  `Transaction_ID` int(11) NOT NULL,
  `Customer_ID` int(11) DEFAULT NULL,
  `Trans_Total` decimal(10,2) DEFAULT NULL,
  `Transaction_Date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_ml`
--

INSERT INTO `transaction_ml` (`Transaction_ID`, `Customer_ID`, `Trans_Total`, `Transaction_Date`) VALUES
(1, 1, 10200.00, '2025-06-11'),
(2, 1, 3600.00, '2025-06-11'),
(3, 1, 3000.00, '2025-06-11'),
(4, 1, 3000.00, '2025-06-11'),
(5, 1, 600.00, '2025-06-11'),
(6, 1, 1800.00, '2025-06-11');

-- --------------------------------------------------------

--
-- Table structure for table `user_account`
--

CREATE TABLE `user_account` (
  `User_Account_ID` int(11) NOT NULL,
  `Username` varchar(255) DEFAULT NULL,
  `POSITION` varchar(255) DEFAULT NULL,
  `Pass` varchar(255) DEFAULT NULL,
  `User_Photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`User_Account_ID`, `Username`, `POSITION`, `Pass`, `User_Photo`) VALUES
(1, 'Maxweller', 'Manager', '$2y$10$7aTaSNNfl5CxVvoFtDQA5.Z4yV9Gqflw2UKnWRTHkcdxJvnEZFTV.', 'profile_picture/PHYSICAL-FINAL1_1749142892.jpg'),
(2, 'Admin321', 'Admin', '$2y$10$9Xz4X7Q8Z5Y3W2V1U0T9SeJ8K9L0M1N2O3P4Q5R6S7T8U9V0W1X2Y', NULL),
(3, 'Joshueee', 'Admin', '$2y$10$oHy7E7/hvxI4WaySk9cqkeS5.ri.vcyiOUkqOmtJITWzPK3Y9zBie', 'profile_picture/mime_1749643408.jpg'),
(4, 'Patricia', 'Cashier', '$2y$10$IN6u6ulG/IQvmATxUYLfP.tZwIT1HeoCWp5DRpshw4aOeAK.IoarK', 'profile_picture/TOO_1749643720.png'),
(5, 'Martin', 'Manager', '$2y$10$e663cRnnbXVXkWO3r4FBiePira.nM0DJmDNA/XR2FV1vJFPopkTxy', 'profile_picture/TOO_1749643844.png'),
(7, 'Percy', 'Cashier', '$2y$10$DAYP4jtPdF17umwsu984YedgPANR1D7K6v2zr3tZlS9rYeaN/6aY6', 'profile_picture/images_1749645335.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `user_position`
--

CREATE TABLE `user_position` (
  `Position_ID` int(11) NOT NULL,
  `User_Account_ID` int(11) NOT NULL,
  `Start_Date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `End_Date` date DEFAULT NULL,
  `User_Status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_position`
--

INSERT INTO `user_position` (`Position_ID`, `User_Account_ID`, `Start_Date`, `End_Date`, `User_Status`) VALUES
(1, 1, '2025-06-05 17:01:32', NULL, 1),
(2, 2, '2025-06-06 20:29:00', NULL, 1),
(3, 3, '2025-06-11 12:03:28', NULL, 1),
(4, 4, '2025-06-11 12:08:40', NULL, 1),
(5, 5, '2025-06-11 12:10:44', NULL, 1),
(7, 7, '2025-06-11 12:35:35', NULL, 1);

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
  ADD UNIQUE KEY `Customer_Phone` (`Customer_Phone`);

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
  ADD PRIMARY KEY (`Position_Details_ID`),
  ADD KEY `Position_ID` (`Position_ID`);

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
  ADD KEY `User_Account_ID` (`User_Account_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `Cart_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `Category_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `Customer_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `customer_address`
--
ALTER TABLE `customer_address`
  MODIFY `CA_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_discount`
--
ALTER TABLE `customer_discount`
  MODIFY `CustDisc` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discount`
--
ALTER TABLE `discount`
  MODIFY `Discount_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_salary`
--
ALTER TABLE `employee_salary`
  MODIFY `EmployeeSa_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `Expenses_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `income`
--
ALTER TABLE `income`
  MODIFY `Income_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `Payment_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `position_details`
--
ALTER TABLE `position_details`
  MODIFY `Position_Details_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `Product_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `product_category`
--
ALTER TABLE `product_category`
  MODIFY `ProdCat_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_price`
--
ALTER TABLE `product_price`
  MODIFY `Price_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `Transaction_Item_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `transaction_ml`
--
ALTER TABLE `transaction_ml`
  MODIFY `Transaction_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_account`
--
ALTER TABLE `user_account`
  MODIFY `User_Account_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_position`
--
ALTER TABLE `user_position`
  MODIFY `Position_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- Constraints for table `customer_discount`
--
ALTER TABLE `customer_discount`
  ADD CONSTRAINT `customer_discount_ibfk_1` FOREIGN KEY (`Discount_ID`) REFERENCES `discount` (`Discount_ID`),
  ADD CONSTRAINT `customer_discount_ibfk_2` FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`Customer_ID`);

--
-- Constraints for table `employee_salary`
--
ALTER TABLE `employee_salary`
  ADD CONSTRAINT `employee_salary_ibfk_1` FOREIGN KEY (`User_Account_ID`) REFERENCES `user_account` (`User_Account_ID`);

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`EmployeeSa_ID`) REFERENCES `employee_salary` (`EmployeeSa_ID`),
  ADD CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`Income_ID`) REFERENCES `income` (`Income_ID`);

--
-- Constraints for table `income`
--
ALTER TABLE `income`
  ADD CONSTRAINT `income_ibfk_1` FOREIGN KEY (`Payment_ID`) REFERENCES `payment` (`Payment_ID`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`Transaction_ID`) REFERENCES `transaction_ml` (`Transaction_ID`);

--
-- Constraints for table `position_details`
--
ALTER TABLE `position_details`
  ADD CONSTRAINT `position_details_ibfk_1` FOREIGN KEY (`Position_ID`) REFERENCES `user_position` (`Position_ID`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`User_Account_ID`) REFERENCES `user_account` (`User_Account_ID`);

--
-- Constraints for table `product_category`
--
ALTER TABLE `product_category`
  ADD CONSTRAINT `product_category_ibfk_1` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`),
  ADD CONSTRAINT `product_category_ibfk_2` FOREIGN KEY (`Category_ID`) REFERENCES `category` (`Category_ID`);

--
-- Constraints for table `product_price`
--
ALTER TABLE `product_price`
  ADD CONSTRAINT `product_price_ibfk_1` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`);

--
-- Constraints for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`Transaction_ID`) REFERENCES `transaction_ml` (`Transaction_ID`),
  ADD CONSTRAINT `transaction_items_ibfk_2` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`);

--
-- Constraints for table `transaction_ml`
--
ALTER TABLE `transaction_ml`
  ADD CONSTRAINT `transaction_ml_ibfk_1` FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`Customer_ID`);

--
-- Constraints for table `user_position`
--
ALTER TABLE `user_position`
  ADD CONSTRAINT `user_position_ibfk_1` FOREIGN KEY (`User_Account_ID`) REFERENCES `user_account` (`User_Account_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
