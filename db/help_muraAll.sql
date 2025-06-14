SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- TABLES --

CREATE TABLE `category` (
  `Category_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Category_Name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Category_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `customer_address` (
  `CA_ID` int(11) NOT NULL AUTO_INCREMENT,
  `CA_Street` varchar(255) DEFAULT NULL,
  `CA_Barangay` varchar(255) DEFAULT NULL,
  `CA_City` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`CA_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `customer` (
  `Customer_ID` int(11) NOT NULL AUTO_INCREMENT,
  `CA_ID` int(11) NOT NULL,
  `Customer_FirstName` varchar(255) DEFAULT NULL,
  `Customer_LastName` varchar(255) DEFAULT NULL,
  `Customer_Phone` varchar(255) DEFAULT NULL,
  `Membership_Status` tinyint(1) DEFAULT 0 COMMENT '0 = Non-member, 1 = Premium Member',
  PRIMARY KEY (`Customer_ID`),
  UNIQUE KEY `Customer_Phone` (`Customer_Phone`),
  KEY `CA_ID` (`CA_ID`),
  CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`CA_ID`) REFERENCES `customer_address` (`CA_ID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `discount` (
  `Discount_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Discount_Type` varchar(255) DEFAULT NULL,
  `Discount_Percentage` float DEFAULT NULL,
  PRIMARY KEY (`Discount_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `customer_discount` (
  `CustDisc` int(11) NOT NULL AUTO_INCREMENT,
  `Discount_ID` int(11) NOT NULL,
  `Customer_ID` int(11) NOT NULL,
  PRIMARY KEY (`CustDisc`),
  KEY `Discount_ID` (`Discount_ID`),
  KEY `Customer_ID` (`Customer_ID`),
  CONSTRAINT `customer_discount_ibfk_1` FOREIGN KEY (`Discount_ID`) REFERENCES `discount` (`Discount_ID`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `customer_discount_ibfk_2` FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`Customer_ID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `user_account` (
  `User_Account_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(255) DEFAULT NULL,
  `Pass` varchar(255) DEFAULT NULL,
  `User_Photo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`User_Account_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `employee_salary` (
  `EmployeeSa_ID` int(11) NOT NULL AUTO_INCREMENT,
  `User_Account_ID` int(11) NOT NULL,
  `Salary_Amount` int(11) DEFAULT NULL,
  `Payout_Date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`EmployeeSa_ID`),
  KEY `User_Account_ID` (`User_Account_ID`),
  CONSTRAINT `employee_salary_ibfk_1` FOREIGN KEY (`User_Account_ID`) REFERENCES `user_account` (`User_Account_ID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `transaction_ml` (
  `Transaction_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Customer_ID` int(11) DEFAULT NULL,
  `Trans_Total` decimal(10,2) DEFAULT NULL,
  `Transaction_Date` date DEFAULT NULL,
  PRIMARY KEY (`Transaction_ID`),
  KEY `Customer_ID` (`Customer_ID`),
  CONSTRAINT `transaction_ml_ibfk_1` FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`Customer_ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `payment` (
  `Payment_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Transaction_ID` int(11) NOT NULL,
  `Payment_Type` varchar(255) DEFAULT NULL,
  `Payment_Date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Payment_Amount` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`Payment_ID`),
  KEY `Transaction_ID` (`Transaction_ID`),
  CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`Transaction_ID`) REFERENCES `transaction_ml` (`Transaction_ID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `income` (
  `Income_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Payment_ID` int(11) NOT NULL,
  `Income_Amount` decimal(10,2) DEFAULT NULL,
  `Income_Date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`Income_ID`),
  KEY `Payment_ID` (`Payment_ID`),
  CONSTRAINT `income_ibfk_1` FOREIGN KEY (`Payment_ID`) REFERENCES `payment` (`Payment_ID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `expenses` (
  `Expenses_ID` int(11) NOT NULL AUTO_INCREMENT,
  `EmployeeSa_ID` int(11) DEFAULT NULL,
  `Income_ID` int(11) DEFAULT NULL,
  `Supply_Fees` decimal(10,2) DEFAULT NULL,
  `Utilities` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`Expenses_ID`),
  KEY `EmployeeSa_ID` (`EmployeeSa_ID`),
  KEY `Income_ID` (`Income_ID`),
  CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`EmployeeSa_ID`) REFERENCES `employee_salary` (`EmployeeSa_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`Income_ID`) REFERENCES `income` (`Income_ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `position_details` (
  `Position_Details_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Position` varchar(255) DEFAULT NULL,
  `Position_Status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Position_Details_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `user_position` (
  `Position_ID` int(11) NOT NULL AUTO_INCREMENT,
  `User_Account_ID` int(11) NOT NULL,
  `Position_Details_ID` int(11) NOT NULL,
  `Start_Date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `End_Date` date DEFAULT NULL,
  `User_Status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`Position_ID`),
  KEY `User_Account_ID` (`User_Account_ID`),
  KEY `Position_Details_ID` (`Position_Details_ID`),
  CONSTRAINT `user_position_ibfk_1` FOREIGN KEY (`User_Account_ID`) REFERENCES `user_account` (`User_Account_ID`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `user_position_ibfk_2` FOREIGN KEY (`Position_Details_ID`) REFERENCES `position_details` (`Position_Details_ID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `product` (
  `Product_ID` int(11) NOT NULL AUTO_INCREMENT,
  `User_Account_ID` int(11) NOT NULL,
  `Product_Name` varchar(255) DEFAULT NULL,
  `Product_Stock` varchar(255) DEFAULT NULL,
  `Product_Image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Product_ID`),
  KEY `User_Account_ID` (`User_Account_ID`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`User_Account_ID`) REFERENCES `user_account` (`User_Account_ID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `product_category` (
  `ProdCat_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Product_ID` int(11) NOT NULL,
  `Category_ID` int(11) NOT NULL,
  PRIMARY KEY (`ProdCat_ID`),
  KEY `Product_ID` (`Product_ID`),
  KEY `Category_ID` (`Category_ID`),
  CONSTRAINT `product_category_ibfk_1` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `product_category_ibfk_2` FOREIGN KEY (`Category_ID`) REFERENCES `category` (`Category_ID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `product_price` (
  `Price_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Product_ID` int(11) NOT NULL,
  `Price` decimal(10,2) DEFAULT NULL,
  `Effective_From` date DEFAULT NULL,
  `Effective_To` date DEFAULT NULL,
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`Price_ID`),
  KEY `Product_ID` (`Product_ID`),
  CONSTRAINT `product_price_ibfk_1` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `transaction_items` (
  `Transaction_Item_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Transaction_ID` int(11) DEFAULT NULL,
  `Product_ID` int(11) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `Original_Price` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`Transaction_Item_ID`),
  KEY `Transaction_ID` (`Transaction_ID`),
  KEY `Product_ID` (`Product_ID`),
  CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`Transaction_ID`) REFERENCES `transaction_ml` (`Transaction_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `transaction_items_ibfk_2` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- INSERT DATA --
INSERT INTO `position_details` (`Position`, `Position_Status`) VALUES ('Admin', '1');

COMMIT;