-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 13, 2011 at 10:52 PM
-- Server version: 5.1.44
-- PHP Version: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `framework`
--

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE IF NOT EXISTS `profiles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `surname` varchar(256) NOT NULL,
  `username` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `salt` varchar(16) NOT NULL,
  `password` varchar(256) NOT NULL,
  `lang` varchar(5) NOT NULL,
  `template` int(11) NOT NULL DEFAULT '1',
  `created` int(10) NOT NULL,
  `last_access` int(10) NOT NULL,
  `last_ip` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` VALUES(1, 'matej', 'kramny', 'mkram0', 'matejkramny@gmail.com', '', '', 'en', 0, 0, 0, '::1');

-- --------------------------------------------------------

--
-- Table structure for table `sys_bans`
--

CREATE TABLE IF NOT EXISTS `sys_bans` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `ip` varchar(15) NOT NULL,
  `Expires` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `sys_bans`
--


-- --------------------------------------------------------

--
-- Table structure for table `sys_languages`
--

CREATE TABLE IF NOT EXISTS `sys_languages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lang_code` varchar(5) NOT NULL,
  `lang_full` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `sys_languages`
--

INSERT INTO `sys_languages` VALUES(1, 'en', 'English');

-- --------------------------------------------------------

--
-- Table structure for table `sys_settings`
--

CREATE TABLE IF NOT EXISTS `sys_settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `value` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `sys_settings`
--

INSERT INTO `sys_settings` VALUES(1, 'lang', 'en');

-- --------------------------------------------------------

--
-- Table structure for table `sys_templates`
--

CREATE TABLE IF NOT EXISTS `sys_templates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `path` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `sys_templates`
--

INSERT INTO `sys_templates` VALUES(1, 'Default look', 'default');