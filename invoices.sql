-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 05, 2023 at 04:00 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `invoices`
--

-- --------------------------------------------------------

--
-- Table structure for table `table_invoice`
--

CREATE TABLE `table_invoice` (
  `invoice_id` int(11) NOT NULL,
  `invoice_no` varchar(50) NOT NULL,
  `customer_name` varchar(250) NOT NULL,
  `customer_contact` varchar(15) NOT NULL,
  `customer_address` text NOT NULL,
  `service_date` date NOT NULL,
  `service` varchar(100) NOT NULL,
  `area_covered` varchar(20) NOT NULL,
  `service_cost` varchar(10) NOT NULL,
  `total_payment` varchar(10) NOT NULL,
  `remaining_payment` varchar(10) NOT NULL,
  `billing_status` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_invoice`
--

INSERT INTO `table_invoice` (`invoice_id`, `invoice_no`, `customer_name`, `customer_contact`, `customer_address`, `service_date`, `service`, `area_covered`, `service_cost`, `total_payment`, `remaining_payment`, `billing_status`) VALUES
(184, '644651', 'Peeyoosh Suryawanshi', '9922277835', 'Ballur, Degloor', '2023-06-22', 'Cultivation', '5', '4000', '2000', '2000', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `table_invoice_records`
--

CREATE TABLE `table_invoice_records` (
  `invoice_record_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `invoice_date` date NOT NULL,
  `invoice_description` varchar(250) NOT NULL,
  `invoice_paid_amount` varchar(10) NOT NULL,
  `invoice_remaining_amount` varchar(10) NOT NULL,
  `final_total_paid` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_invoice_records`
--

INSERT INTO `table_invoice_records` (`invoice_record_id`, `invoice_id`, `invoice_date`, `invoice_description`, `invoice_paid_amount`, `invoice_remaining_amount`, `final_total_paid`) VALUES
(54706, 184, '2023-06-20', 'Diesel', '2000', '2000', '2000');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `table_invoice`
--
ALTER TABLE `table_invoice`
  ADD PRIMARY KEY (`invoice_id`);

--
-- Indexes for table `table_invoice_records`
--
ALTER TABLE `table_invoice_records`
  ADD PRIMARY KEY (`invoice_record_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `table_invoice`
--
ALTER TABLE `table_invoice`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=188;

--
-- AUTO_INCREMENT for table `table_invoice_records`
--
ALTER TABLE `table_invoice_records`
  MODIFY `invoice_record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54712;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
