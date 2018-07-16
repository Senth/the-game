-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Host: localhost:8889
-- Generation Time: Jul 16, 2018 at 01:20 PM
-- Server version: 5.5.42
-- PHP Version: 7.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `game_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `arc`
--

CREATE TABLE IF NOT EXISTS `arc` (
  `id` int(9) NOT NULL,
  `name` varchar(32) COLLATE utf8_swedish_ci NOT NULL,
  `start_time` int(11) DEFAULT NULL,
  `length` int(11) NOT NULL DEFAULT '9000'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `id` varchar(40) COLLATE utf8_swedish_ci NOT NULL DEFAULT '0',
  `ip_address` varchar(45) COLLATE utf8_swedish_ci NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned DEFAULT '0',
  `data` text COLLATE utf8_swedish_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guess`
--

CREATE TABLE IF NOT EXISTS `guess` (
  `id` int(9) NOT NULL,
  `team_id` int(9) NOT NULL,
  `quest_id` int(9) NOT NULL,
  `guess` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `hint`
--

CREATE TABLE IF NOT EXISTS `hint` (
  `id` int(9) NOT NULL,
  `quest_id` int(9) DEFAULT NULL,
  `text` varchar(100) COLLATE utf8_swedish_ci NOT NULL DEFAULT 'TEXT',
  `time` int(5) NOT NULL DEFAULT '9999',
  `skippable` tinyint(1) DEFAULT '1',
  `point_deduction` int(3) NOT NULL DEFAULT '0',
  `order` tinyint(10) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quest`
--

CREATE TABLE IF NOT EXISTS `quest` (
  `id` int(9) NOT NULL,
  `arc_id` int(9) DEFAULT NULL,
  `name` varchar(52) COLLATE utf8_swedish_ci NOT NULL DEFAULT 'NAME',
  `main` int(5) NOT NULL DEFAULT '9',
  `sub` int(5) NOT NULL DEFAULT '9',
  `html` text COLLATE utf8_swedish_ci NOT NULL,
  `html_is_php` tinyint(1) NOT NULL DEFAULT '0',
  `answer` varchar(50) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `has_answer_box` tinyint(1) NOT NULL DEFAULT '1',
  `start_time` int(11) DEFAULT NULL,
  `points` int(3) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

CREATE TABLE IF NOT EXISTS `team` (
  `id` int(9) NOT NULL,
  `name` varchar(25) COLLATE utf8_swedish_ci NOT NULL,
  `password` varchar(42) COLLATE utf8_swedish_ci NOT NULL,
  `points` int(5) NOT NULL DEFAULT '0',
  `current_quest_id` int(5) DEFAULT NULL,
  `started_quest` int(11) NOT NULL DEFAULT '0',
  `last_answered` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `current_hint` int(9) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(9) NOT NULL,
  `name` varchar(32) COLLATE utf8_swedish_ci NOT NULL,
  `password` varchar(42) COLLATE utf8_swedish_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci COMMENT='Currently this table only contains admins';

-- --------------------------------------------------------

--
-- Table structure for table `version`
--

CREATE TABLE IF NOT EXISTS `version` (
  `version` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `last_activity_idx` (`timestamp`);

--
-- Indexes for table `guess`
--
ALTER TABLE `guess`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `version`
--
ALTER TABLE `version`
  ADD PRIMARY KEY (`version`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `arc`
--
ALTER TABLE `arc`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `guess`
--
ALTER TABLE `guess`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `hint`
--
ALTER TABLE `hint`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `quest`
--
ALTER TABLE `quest`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `team`
--
ALTER TABLE `team`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(9) NOT NULL AUTO_INCREMENT;

--
-- Default version for table `version`
--
INSERT INTO `version` VALUES (102000);
