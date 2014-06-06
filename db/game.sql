-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Värd: localhost:3306
-- Tid vid skapande: 06 jun 2014 kl 20:04
-- Serverversion: 5.5.34
-- PHP-version: 5.5.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Databas: `game`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `arc`
--

CREATE TABLE IF NOT EXISTS `arc` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) COLLATE utf8_swedish_ci NOT NULL DEFAULT '0',
  `ip_address` varchar(45) COLLATE utf8_swedish_ci NOT NULL DEFAULT '0',
  `user_agent` varchar(120) COLLATE utf8_swedish_ci NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `hint`
--

CREATE TABLE IF NOT EXISTS `hint` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `quest_id` int(5) NOT NULL,
  `text` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `time` int(5) NOT NULL,
  `point_deduction` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `quest`
--

CREATE TABLE IF NOT EXISTS `quest` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `arc_id` int(5) NOT NULL,
  `main` int(5) NOT NULL,
  `sub` int(5) NOT NULL,
  `html` text COLLATE utf8_swedish_ci NOT NULL,
  `html_is_php` tinyint(1) NOT NULL DEFAULT '1',
  `answer` varchar(50) COLLATE utf8_swedish_ci NOT NULL,
  `first_team_id` int(5) DEFAULT NULL,
  `has_answer_box` tinyint(1) NOT NULL DEFAULT '1',
  `start_time` int(11) DEFAULT NULL,
  `point_standard` int(3) NOT NULL DEFAULT '0',
  `point_first_extra` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Tabellstruktur `team`
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

