-- phpMyAdmin SQL Dump
-- version 4.2.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 18, 2016 at 02:38 PM
-- Server version: 5.6.28-log
-- PHP Version: 5.6.14-pl0-gentoo

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `game`
--
CREATE DATABASE IF NOT EXISTS `game` DEFAULT CHARACTER SET utf8 COLLATE utf8_swedish_ci;
USE `game`;

-- --------------------------------------------------------

--
-- Table structure for table `arc`
--

CREATE TABLE IF NOT EXISTS `arc` (
`id` int(5) NOT NULL,
  `name` varchar(32) COLLATE utf8_swedish_ci NOT NULL,
  `start_time` int(11) DEFAULT NULL,
  `length` int(11) NOT NULL DEFAULT '10800'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) COLLATE utf8_swedish_ci NOT NULL DEFAULT '0',
  `ip_address` varchar(45) COLLATE utf8_swedish_ci NOT NULL DEFAULT '0',
  `user_agent` varchar(120) COLLATE utf8_swedish_ci NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text COLLATE utf8_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hint`
--

CREATE TABLE IF NOT EXISTS `hint` (
`id` int(5) NOT NULL,
  `quest_id` int(5) NOT NULL,
  `text` varchar(100) COLLATE utf8_swedish_ci NOT NULL DEFAULT 'TEXT',
  `time` int(5) NOT NULL DEFAULT '9999',
  `point_deduction` int(3) NOT NULL DEFAULT '0'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=107 ;

-- --------------------------------------------------------

--
-- Table structure for table `quest`
--

CREATE TABLE IF NOT EXISTS `quest` (
`id` int(5) NOT NULL,
  `arc_id` int(5) NOT NULL,
  `name` varchar(52) COLLATE utf8_swedish_ci NOT NULL DEFAULT 'NAME',
  `main` int(5) NOT NULL DEFAULT '9',
  `sub` int(5) NOT NULL DEFAULT '9',
  `html` text COLLATE utf8_swedish_ci NOT NULL,
  `html_is_php` tinyint(1) NOT NULL DEFAULT '0',
  `answer` varchar(50) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `first_team_id` int(5) DEFAULT NULL,
  `has_answer_box` tinyint(1) NOT NULL DEFAULT '1',
  `start_time` int(11) DEFAULT NULL,
  `point_standard` int(3) NOT NULL DEFAULT '0',
  `point_first_extra` int(3) NOT NULL DEFAULT '0'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=39 ;

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

CREATE TABLE IF NOT EXISTS `team` (
`id` int(5) unsigned NOT NULL,
  `name` varchar(25) COLLATE utf8_swedish_ci NOT NULL,
  `password` varchar(42) COLLATE utf8_swedish_ci NOT NULL,
  `points` int(5) NOT NULL DEFAULT '0',
  `current_quest_id` int(5) DEFAULT NULL,
  `started_quest` int(11) NOT NULL DEFAULT '0',
  `last_answered` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
`id` int(5) unsigned NOT NULL,
  `name` varchar(32) COLLATE utf8_swedish_ci NOT NULL,
  `password` varchar(42) COLLATE utf8_swedish_ci NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci COMMENT='Currently this table only contains admins' AUTO_INCREMENT=3 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `arc`
--
ALTER TABLE `arc`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ci_sessions`
--
ALTER TABLE `ci_sessions`
 ADD PRIMARY KEY (`session_id`), ADD KEY `last_activity_idx` (`last_activity`);

--
-- Indexes for table `hint`
--
ALTER TABLE `hint`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quest`
--
ALTER TABLE `quest`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `team`
--
ALTER TABLE `team`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `arc`
--
ALTER TABLE `arc`
MODIFY `id` int(5) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `hint`
--
ALTER TABLE `hint`
MODIFY `id` int(5) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=107;
--
-- AUTO_INCREMENT for table `quest`
--
ALTER TABLE `quest`
MODIFY `id` int(5) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=39;
--
-- AUTO_INCREMENT for table `team`
--
ALTER TABLE `team`
MODIFY `id` int(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
MODIFY `id` int(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
