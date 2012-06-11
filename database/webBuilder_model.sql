-- Adminer 3.3.4 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `block_sets` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parent_ID` int(10) unsigned DEFAULT NULL,
  `pregenerated_structure` text COLLATE utf8_unicode_ci,
  `builder_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `edited_on` datetime DEFAULT NULL,
  `edited_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK__blocks_sets__parent_ID` (`parent_ID`),
  CONSTRAINT `FK__blocks_sets__parent_ID` FOREIGN KEY (`parent_ID`) REFERENCES `block_sets` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `blocks` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category_ID` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK__blocks__category_ID` (`category_ID`),
  CONSTRAINT `FK__blocks__category_ID` FOREIGN KEY (`category_ID`) REFERENCES `blocks_categories` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `blocks_categories` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `blocks_data_requirements` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `block_ID` int(10) unsigned NOT NULL,
  `property` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `data_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK__blocks_data_requirements__block_ID` (`block_ID`),
  CONSTRAINT `FK__blocks_data_dependencies__block_ID` FOREIGN KEY (`block_ID`) REFERENCES `blocks` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `blocks_data_requirements_providers` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `required_property_ID` int(10) unsigned NOT NULL,
  `provider_ID` int(10) unsigned NOT NULL,
  `provider_property` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK__blocks_data_dependencies_providers__required_property_ID` (`required_property_ID`),
  KEY `FK__blocks_data_dependencies_providers__provider_ID` (`provider_ID`),
  CONSTRAINT `FK__blocks_data_dependencies_providers__provider_ID` FOREIGN KEY (`provider_ID`) REFERENCES `blocks` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__blocks_data_dependencies_providers__required_property_ID` FOREIGN KEY (`required_property_ID`) REFERENCES `blocks_data_requirements` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `blocks_instances` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `block_set_ID` int(10) unsigned NOT NULL,
  `template_ID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK__blocks_instances__template_ID` (`template_ID`),
  KEY `FK__blocks_instances__blocks_set_ID` (`block_set_ID`),
  CONSTRAINT `FK__blocks_instances__blocks_set_ID` FOREIGN KEY (`block_set_ID`) REFERENCES `block_sets` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__blocks_instances__tempate_ID` FOREIGN KEY (`template_ID`) REFERENCES `blocks_templates` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `blocks_instances_data_constant` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `instance_ID` int(10) unsigned NOT NULL,
  `property_ID` int(10) unsigned NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK__blocks_instances_data_constant__instance_ID` (`instance_ID`),
  KEY `FK__blocks_instances_data_constant__property_ID` (`property_ID`),
  CONSTRAINT `FK__blocks_instances_data_constant__instance_ID` FOREIGN KEY (`instance_ID`) REFERENCES `blocks_instances` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__blocks_instances_data_constant__property_ID` FOREIGN KEY (`property_ID`) REFERENCES `blocks_data_requirements` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `blocks_instances_data_inherited` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `instance_ID` int(10) unsigned NOT NULL,
  `provider_instance_ID` int(10) unsigned NOT NULL,
  `provider_property_ID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK__blocks_instances_data_inherited__provider_instance_ID` (`provider_instance_ID`),
  KEY `FK__blocks_instances_data_inherited__instance_ID` (`instance_ID`),
  KEY `FK__blocks_instances_data_inherited__provider_property_ID` (`provider_property_ID`),
  CONSTRAINT `FK__blocks_instances_data_inherited__instance_ID` FOREIGN KEY (`instance_ID`) REFERENCES `blocks_instances` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__blocks_instances_data_inherited__provider_instance_ID` FOREIGN KEY (`provider_instance_ID`) REFERENCES `blocks_instances` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `FK__blocks_instances_data_inherited__provider_property_ID` FOREIGN KEY (`provider_property_ID`) REFERENCES `blocks_data_requirements_providers` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `blocks_instances_subblocks` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_instance_ID` int(10) unsigned NOT NULL,
  `parent_slot_ID` int(10) unsigned NOT NULL,
  `position` int(10) unsigned NOT NULL,
  `inserted_instance_ID` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK__blocks_instances_subblocks__inserted_instance_ID` (`inserted_instance_ID`),
  KEY `FK__blocks_instances_subblocks__parent_instance_ID` (`parent_instance_ID`),
  KEY `FK__blocks_instances_subblocks__parent_slot_ID` (`parent_slot_ID`),
  KEY `UNQ__blocks_instances_subblocks__slot_position` (`parent_instance_ID`,`parent_slot_ID`,`position`),
  CONSTRAINT `FK__blocks_instances_subblocks__inserted_instance_ID` FOREIGN KEY (`inserted_instance_ID`) REFERENCES `blocks_instances` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__blocks_instances_subblocks__parent_instance_ID` FOREIGN KEY (`parent_instance_ID`) REFERENCES `blocks_instances` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__blocks_instances_subblocks__parent_slot_ID` FOREIGN KEY (`parent_slot_ID`) REFERENCES `blocks_templates_slots` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `blocks_templates` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `block_ID` int(10) unsigned NOT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK__blocks_templates__block_ID` (`block_ID`),
  CONSTRAINT `FK__blocks_templates__block_ID` FOREIGN KEY (`block_ID`) REFERENCES `blocks` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `blocks_templates_slots` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `template_ID` int(10) unsigned NOT NULL,
  `code_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `UNQ__blocks_slots__name` (`template_ID`,`code_name`),
  KEY `FK__blocks_slots__block_ID` (`template_ID`),
  CONSTRAINT `FK__blocks_slots__block_ID` FOREIGN KEY (`template_ID`) REFERENCES `blocks_templates` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- 2012-05-11 11:08:45

