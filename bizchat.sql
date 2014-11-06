-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 06, 2014 at 06:22 PM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bizchat`
--
CREATE DATABASE IF NOT EXISTS `bizchat` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `bizchat`;

-- --------------------------------------------------------

--
-- Table structure for table `bz_category`
--

CREATE TABLE IF NOT EXISTS `bz_category` (
  `categoryId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`categoryId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `bz_category`
--

INSERT INTO `bz_category` (`categoryId`, `name`, `active`) VALUES
(1, 'Test', 1),
(2, 'Test 2', 1);

-- --------------------------------------------------------

--
-- Table structure for table `bz_cookie_tracker`
--

CREATE TABLE IF NOT EXISTS `bz_cookie_tracker` (
  `cookieId` int(11) NOT NULL AUTO_INCREMENT,
  `loginId` int(11) DEFAULT NULL,
  `cookie` varchar(50) DEFAULT NULL,
  `lastIp` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`cookieId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `bz_cookie_tracker`
--

INSERT INTO `bz_cookie_tracker` (`cookieId`, `loginId`, `cookie`, `lastIp`) VALUES
(1, 1, '17AKUim1NgVIA0ttJ9ewF', '::1'),
(2, 1, '1K12Oz1tXtzMUKBqRxGgn', '192.168.0.105');

-- --------------------------------------------------------

--
-- Table structure for table `bz_login`
--

CREATE TABLE IF NOT EXISTS `bz_login` (
  `loginId` int(11) NOT NULL AUTO_INCREMENT,
  `userType` int(11) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`loginId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `bz_login`
--

INSERT INTO `bz_login` (`loginId`, `userType`, `email`, `password`) VALUES
(1, 1, 'danny@dannynochumsohn.com', 'b714337aa8007c433329ef43c7b8252c');

-- --------------------------------------------------------

--
-- Table structure for table `bz_user_details`
--

CREATE TABLE IF NOT EXISTS `bz_user_details` (
  `detailsId` int(11) NOT NULL AUTO_INCREMENT,
  `loginId` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `value` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`detailsId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `bz_user_details`
--

INSERT INTO `bz_user_details` (`detailsId`, `loginId`, `name`, `value`) VALUES
(1, 1, 'dateRegistered', '2014-11-04'),
(2, 1, 'name', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `bz_user_type`
--

CREATE TABLE IF NOT EXISTS `bz_user_type` (
  `userType` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`userType`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `bz_user_type`
--

INSERT INTO `bz_user_type` (`userType`, `name`) VALUES
(1, 'Admin'),
(2, 'User');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
