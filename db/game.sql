-- phpMyAdmin SQL Dump
-- version 3.3.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 04, 2012 at 04:24 PM
-- Server version: 5.1.62
-- PHP Version: 5.3.13-pl0-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `game`
--

-- --------------------------------------------------------

--
-- Table structure for table `quest`
--

CREATE TABLE IF NOT EXISTS `quest` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `main` int(5) NOT NULL,
  `sub` int(5) NOT NULL,
  `html` text COLLATE utf8_swedish_ci NOT NULL,
  `html_is_php` tinyint(1) NOT NULL DEFAULT '1',
  `answer` varchar(50) COLLATE utf8_swedish_ci NOT NULL,
  `first_team_id` int(5) DEFAULT NULL,
  `has_answer_box` tinyint(1) NOT NULL DEFAULT '1',
  `start_time` int(11) DEFAULT NULL,
  `hint_1_text` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `hint_1_time` int(5) DEFAULT NULL,
  `hint_2_text` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `hint_2_time` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `quest`
--

INSERT INTO `quest` (`id`, `main`, `sub`, `html`, `html_is_php`, `answer`, `first_team_id`, `has_answer_box`, `start_time`, `hint_1_text`, `hint_1_time`, `hint_2_text`, `hint_2_time`) VALUES
(1, 1, 1, '1-1', 0, '1-1', NULL, 1, NULL, '', 0, '', 0),
(2, 1, 2, '1-2', 0, '1-2', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(3, 2, 1, '2-1', 0, '2-1', NULL, 1, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

CREATE TABLE IF NOT EXISTS `team` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) COLLATE utf8_swedish_ci NOT NULL,
  `password` varchar(42) COLLATE utf8_swedish_ci NOT NULL,
  `points` int(5) NOT NULL DEFAULT '0',
  `current_quest_id` int(5) DEFAULT NULL,
  `started_quest` int(11) NOT NULL DEFAULT '0',
  `last_answered` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `team`
--

INSERT INTO `team` (`id`, `name`, `password`, `points`, `current_quest_id`, `started_quest`, `last_answered`) VALUES
(1, 'test', '098f6bcd4621d373cade4e832627b4f6', 0, 0, 0, 0);
