-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 12, 2011 at 10:17 PM
-- Server version: 5.1.44
-- PHP Version: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `framework`
--

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
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

INSERT INTO `profiles` VALUES(3, 'matej', 'kramny', 'mkram0', 'matejkramny@gmail.com', '63c7727ac872c8e3', '6kBQL1VzVnJQOfEmozDEByJFZv557oJwF6TAKXHQqNY5VSITHESmTp6zJcaZR9mzmA4DhZBiNjUB.qZdmEPlv0', 'en', 1, 1323521498, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `sys_bans`
--

CREATE TABLE `sys_bans` (
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

CREATE TABLE `sys_languages` (
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

CREATE TABLE `sys_settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `value` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `sys_settings`
--

INSERT INTO `sys_settings` VALUES(1, 'lang', 'en');
INSERT INTO `sys_settings` VALUES(2, 'template', '1');
INSERT INTO `sys_settings` VALUES(3, 'template_path', 'default');

-- --------------------------------------------------------

--
-- Table structure for table `sys_templates`
--

CREATE TABLE `sys_templates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `path` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `sys_templates`
--

INSERT INTO `sys_templates` VALUES(1, 'Default look', 'default');
