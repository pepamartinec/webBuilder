-- Adminer 3.3.4 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `discussion_posts` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `web_page_ID` int(10) unsigned NOT NULL,
  `author_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `author_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK__discussion_posts__web_page_ID` (`web_page_ID`),
  CONSTRAINT `FK__discussion_posts__web_page_ID` FOREIGN KEY (`web_page_ID`) REFERENCES `web_pages` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `images` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `web_page_ID` int(10) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `filename_full` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `filename_thumb` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK__images__web_page_ID` (`web_page_ID`),
  CONSTRAINT `FK__images__web_page_ID` FOREIGN KEY (`web_page_ID`) REFERENCES `web_pages` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `simple_pages` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `web_page_ID` int(10) unsigned NOT NULL,
  `title_image_ID` int(10) unsigned DEFAULT NULL,
  `perex` text COLLATE utf8_unicode_ci,
  `content` text COLLATE utf8_unicode_ci,
  `created_on` datetime DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `edited_on` datetime DEFAULT NULL,
  `edited_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `UNQ__simple_pages__webPage_ID` (`web_page_ID`),
  KEY `FK__simple_pages__web_page_ID` (`web_page_ID`),
  KEY `FK__simple_pages__title_image_ID` (`title_image_ID`),
  CONSTRAINT `FK__simple_pages__title_image_ID` FOREIGN KEY (`title_image_ID`) REFERENCES `images` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK__simple_pages__web_page_ID` FOREIGN KEY (`web_page_ID`) REFERENCES `web_pages` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `text_blocks` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image_ID` int(10) unsigned DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `edited_on` datetime DEFAULT NULL,
  `edited_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK__text_blocks__image_ID` (`image_ID`),
  CONSTRAINT `FK__text_blocks__image_ID` FOREIGN KEY (`image_ID`) REFERENCES `images` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `web_pages` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_ID` int(10) unsigned DEFAULT NULL,
  `position` int(10) unsigned NOT NULL DEFAULT '0',
  `block_set_ID` int(10) unsigned DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `valid_from` datetime DEFAULT NULL,
  `valid_to` datetime DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `edited_on` datetime DEFAULT NULL,
  `edited_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK__web_pages__parent_ID` (`parent_ID`),
  KEY `FK__web_pages__blocks_set_ID` (`block_set_ID`),
  CONSTRAINT `FK__web_pages__blocks_set_ID` FOREIGN KEY (`block_set_ID`) REFERENCES `block_sets` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `FK__web_pages__parent_ID` FOREIGN KEY (`parent_ID`) REFERENCES `web_pages` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- 2012-05-11 11:09:18

