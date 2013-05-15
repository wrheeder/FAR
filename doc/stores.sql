-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 15, 2013 at 03:06 PM
-- Server version: 5.5.24-log
-- PHP Version: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `far`
--

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE IF NOT EXISTS `stores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_name` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `store_type_id` int(11) DEFAULT NULL,
  `parent_store_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_stores_store_type` (`store_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=18 ;

--
-- Dumping data for table `stores`
--

INSERT INTO `stores` (`id`, `store_name`, `store_type_id`, `parent_store_id`) VALUES
(1, 'CellC Main Warehouse', 1, NULL),
(2, 'Gauteng Southern Store', 2, NULL),
(3, 'Gauteng Northern Store', 2, NULL),
(4, 'Central Store', 2, NULL),
(5, 'Eastern Cape Store', 2, NULL),
(6, 'Western Cape Store', 2, NULL),
(7, 'Kwazulu Natal Store', 2, NULL),
(9, 'Gauteng Southern Transit Store', 3, 2),
(10, 'Gauteng Northern Transit Store', 3, 3),
(11, 'Central Transit Store', 3, 4),
(12, 'Eastern Cape Transit Store', 3, 5),
(13, 'Western Cape Transit Store', 3, 6),
(14, 'Kwazulu Natal Transit Store', 3, 7);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `stores`
--
ALTER TABLE `stores`
  ADD CONSTRAINT `fk_stores_store_type` FOREIGN KEY (`store_type_id`) REFERENCES `store_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
