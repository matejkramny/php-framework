SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `framework` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `framework`;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `file_uploads`;
CREATE TABLE IF NOT EXISTS `file_uploads` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `location` varchar(256) NOT NULL COMMENT 'Location of the uploaded file',
  `filename` varchar(256) NOT NULL COMMENT 'Name of the uploaded file',
  `extension` varchar(256) NOT NULL COMMENT 'Extension of the file',
  `timestamp` int(10) NOT NULL COMMENT 'UNIX timestamp of time when the file was uploaded',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- Store data associated with forms created by modules

DROP TABLE IF EXISTS `forms`;
CREATE TABLE IF NOT EXISTS `forms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(256) NOT NULL COMMENT 'Unique ID of the form. if form does not include this id it cannot be accepted and is not valid',
  `timestamp` int(10) unsigned NOT NULL COMMENT 'Unix timestamp',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

DROP TABLE IF EXISTS `profiles`;
CREATE TABLE IF NOT EXISTS `profiles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL COMMENT 'Profile first name',
  `surname` varchar(256) NOT NULL COMMENT 'Surname of the profile',
  `middlename` varchar(256) NOT NULL COMMENT 'Middle name (if any)',
  `username` varchar(256) NOT NULL COMMENT 'unique username',
  `email` varchar(256) NOT NULL COMMENT 'unique email',
  `salt` varchar(16) NOT NULL COMMENT 'Randomly generated salt',
  `password` varchar(256) NOT NULL COMMENT 'Hashed password',
  `lang` varchar(5) NOT NULL COMMENT 'Language name ID',
  `template` varchar(256) NOT NULL DEFAULT '1' COMMENT 'Template name',
  `created` int(10) NOT NULL COMMENT 'UNIX timestamp of time when account was created',
  `last_access` int(10) NOT NULL COMMENT 'UNIX timestamp of time when the account was last accessed',
  `last_ip` int(11) unsigned NOT NULL COMMENT 'Last ID of IP address of this accounts login',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- FIX THIS NOT COMPLETE (TODO)
INSERT INTO `profiles` VALUES(3, 'matej', 'kramny', 'mkram0', 'matejkramny@gmail.com', '63c7727ac872c8e3', '6kBQL1VzVnJQOfEmozDEByJFZv557oJwF6TAKXHQqNY5VSITHESmTp6zJcaZR9mzmA4DhZBiNjUB.qZdmEPlv0', 'en', 1, 1323521498, 0, '');

-- --------------------------------------------------------

DROP TABLE IF EXISTS `bans`;
CREATE TABLE IF NOT EXISTS `sys_bans` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT 'User ID',
  `ip` int(11) unsigned NOT NULL COMMENT 'banned IP address',
  `Expires` int(10) NOT NULL COMMENT 'Time ban expires (if any)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- IP addresses table

DROP TABLE IF EXISTS `ip`;
CREATE TABLE IF NOT EXISTS `ip` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ipv4` varchar(15) NOT NULL COMMENT 'an ipv4 address',
  `ipv6` varchar(256) NOT NULL COMMENT 'an ipv6 address',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT charset=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

-- Key=Value store table

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL COMMENT 'Key',
  `value` varchar(256) NOT NULL COMMENT 'Value',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

INSERT INTO `settings` VALUES(1, 'lang', 'en');
INSERT INTO `settings` VALUES(2, 'default_template', 'default');
INSERT INTO `settings` VALUES(3, 'max_upload_file_size', '4096');
