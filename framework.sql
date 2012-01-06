-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 06, 2012 at 11:46 PM
-- Server version: 5.1.44
-- PHP Version: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `framework`
--
DROP DATABASE `framework`;
CREATE DATABASE `framework` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `framework`;

-- --------------------------------------------------------

--
-- Table structure for table `forms`
--

DROP TABLE IF EXISTS `forms`;
CREATE TABLE IF NOT EXISTS `forms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `forms`
--


-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

DROP TABLE IF EXISTS `profiles`;
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`id`, `name`, `surname`, `username`, `email`, `salt`, `password`, `lang`, `template`, `created`, `last_access`, `last_ip`) VALUES(3, 'matej', 'kramny', 'mkram0', 'matejkramny@gmail.com', '63c7727ac872c8e3', '6kBQL1VzVnJQOfEmozDEByJFZv557oJwF6TAKXHQqNY5VSITHESmTp6zJcaZR9mzmA4DhZBiNjUB.qZdmEPlv0', 'en', 1, 1323521498, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `sys_bans`
--

DROP TABLE IF EXISTS `sys_bans`;
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

DROP TABLE IF EXISTS `sys_languages`;
CREATE TABLE IF NOT EXISTS `sys_languages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lang_code` varchar(5) NOT NULL,
  `lang_full` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `sys_languages`
--

INSERT INTO `sys_languages` (`id`, `lang_code`, `lang_full`) VALUES(1, 'en', 'English');

-- --------------------------------------------------------

--
-- Table structure for table `sys_settings`
--

DROP TABLE IF EXISTS `sys_settings`;
CREATE TABLE IF NOT EXISTS `sys_settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `value` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `sys_settings`
--

INSERT INTO `sys_settings` (`id`, `name`, `value`) VALUES(1, 'lang', 'en');
INSERT INTO `sys_settings` (`id`, `name`, `value`) VALUES(2, 'template', '1');
INSERT INTO `sys_settings` (`id`, `name`, `value`) VALUES(3, 'template_path', 'default');

-- --------------------------------------------------------

--
-- Table structure for table `sys_templates`
--

DROP TABLE IF EXISTS `sys_templates`;
CREATE TABLE IF NOT EXISTS `sys_templates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `path` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `sys_templates`
--

INSERT INTO `sys_templates` (`id`, `name`, `path`) VALUES(1, 'Default look', 'default');
