-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2015-11-27 16:02:07
-- 服务器版本： 5.6.20-log
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `newtw`
--

-- --------------------------------------------------------

--
-- 表的结构 `twitter_copy`
--

CREATE TABLE IF NOT EXISTS `twitter_copy` (
`id` int(15) NOT NULL,
  `id_str` varchar(50) DEFAULT NULL,
  `screen_name` varchar(20) DEFAULT NULL,
  `text` varchar(1024) DEFAULT NULL,
  `dtime` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `twitter_copy`
--
ALTER TABLE `twitter_copy`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id_str` (`id_str`), ADD KEY `dtime` (`dtime`), ADD KEY `screen_name` (`screen_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `twitter_copy`
--
ALTER TABLE `twitter_copy`
MODIFY `id` int(15) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
