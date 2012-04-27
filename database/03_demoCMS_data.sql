-- Adminer 3.3.4 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

INSERT INTO `blocks` (`ID`, `code_name`, `category_ID`, `title`) VALUES
(1,	'\\WebBuilder\\Blocks\\Core\\ItemsList',	NULL,	NULL),
(2,	'\\WebBuilder\\Blocks\\Core\\WebPage',	NULL,	NULL),
(3,	'\\DemoCMS\\BuilderBlocks\\Other\\TextBlock',	4,	'Textový blok'),
(4,	'\\DemoCMS\\BuilderBlocks\\Other\\ContactForm',	4,	'Kontaktní formulář'),
(5,	'\\DemoCMS\\BuilderBlocks\\Layouts\\TitlePage',	1,	'Titulní stránka'),
(6,	'\\DemoCMS\\BuilderBlocks\\Layouts\\Page',	1,	'Stránka'),
(7,	'\\DemoCMS\\BuilderBlocks\\Navigation\\Menu',	3,	'Menu'),
(8,	'\\DemoCMS\\BuilderBlocks\\Navigation\\Breadcrumbs',	3,	'Drobečková navigace'),
(9,	'\\DemoCMS\\BuilderBlocks\\SimplePages\\ImageGallery',	2,	'Obrázková galerie'),
(10,	'\\DemoCMS\\BuilderBlocks\\SimplePages\\Image',	2,	'Obrázek'),
(11,	'\\DemoCMS\\BuilderBlocks\\SimplePages\\PageDetail',	2,	'Obsah stránky'),
(12,	'\\DemoCMS\\BuilderBlocks\\SimplePages\\Discussion',	2,	'Diskuze'),
(13,	'\\DemoCMS\\BuilderBlocks\\SimplePages\\PageList',	2,	'Seznam podstránek');

INSERT INTO `blocks_categories` (`ID`, `title`) VALUES
(1,	'Layout'),
(2,	'Stránka'),
(3,	'Navigace'),
(4,	'Ostatní');

INSERT INTO `blocks_data_requirements` (`ID`, `block_ID`, `property`, `data_type`) VALUES
(1,	1,	'items',	'array'),
(2,	2,	'webPage',	'WebPageInterface'),
(3,	3,	'textBlockID',	'ID'),
(4,	4,	'webPage',	'cWebPage'),
(5,	5,	'webPage',	'cWebPage'),
(6,	6,	'webPage',	'cWebPage'),
(7,	7,	'webPage',	'cWebPage'),
(8,	8,	'webPage',	'cWebPage'),
(9,	9,	'webPage',	'cWebPage'),
(10,	10,	'imageID',	'int'),
(11,	11,	'page',	'cWebPage'),
(12,	12,	'webPage',	'cWebPage'),
(13,	13,	'parent',	'cWebPage');

INSERT INTO `blocks_data_requirements_providers` (`ID`, `required_property_ID`, `provider_ID`, `provider_property`) VALUES
(1,	4,	2,	'webPage'),
(2,	5,	3,	'webPage'),
(3,	6,	2,	'webPage'),
(4,	7,	2,	'webPage'),
(5,	8,	2,	'webPage'),
(6,	9,	2,	'webPage'),
(7,	11,	2,	'webPage'),
(8,	12,	2,	'webPage'),
(9,	13,	2,	'webPage');





INSERT INTO `blocks_templates` (`ID`, `block_ID`, `filename`, `title`) VALUES
(1,	1,	'src/WebBuilder/Templates/Core/itemsList_table.twig',	NULL),
(2,	1,	'src/WebBuilder/Templates/Core/itemsList_ul.twig',	NULL),
(3,	2,	'src/WebBuilder/Templates/Core/webPage_html5.twig',	NULL),
(4,	3,	'src/DemoCMS/BuilderTemplates/Other/textBlock_title.twig',	'S titulkem'),
(5,	4,	'src/DemoCMS/BuilderTemplates/Other/contactForm.twig',	''),
(6,	3,	'src/DemoCMS/BuilderTemplates/Other/textBlock_noTitle.twig.twig',	'Bez titulku'),
(7,	6,	'src/DemoCMS/BuilderTemplates/Layouts/page_rightColumn.twig',	'S pravým sloupcem'),
(8,	5,	'src/DemoCMS/BuilderTemplates/Layouts/landingPage.twig',	NULL),
(9,	6,	'src/DemoCMS/BuilderTemplates/Layouts/page_simple.twig.twig',	'Jednoduchá'),
(10,	8,	'src/DemoCMS/BuilderTemplates/Navigation/breadcrumbs.twig',	NULL),
(11,	7,	'src/DemoCMS/BuilderTemplates/Navigation/menu_top.twig',	'Vždy nahoře'),
(12,	7,	'src/DemoCMS/BuilderTemplates/Navigation/menu_horizontal.twig',	'Horizotální'),
(13,	12,	'src/DemoCMS/BuilderTemplates/SimplePages/discussion_formBottom.twig',	'Formulář dole'),
(14,	9,	'src/DemoCMS/BuilderTemplates/SimplePages/imageGallery_single.twig',	'Slideshow'),
(15,	13,	'src/DemoCMS/BuilderTemplates/SimplePages/list_noTitleImage.twig',	'Bez titulních obrázků'),
(16,	9,	'src/DemoCMS/BuilderTemplates/SimplePages/imageGallery_standard.twig',	'Běžná'),
(17,	12,	'src/DemoCMS/BuilderTemplates/SimplePages/discussion_formTop.twig',	'Formulář nahoře'),
(18,	13,	'src/DemoCMS/BuilderTemplates/SimplePages/list_standard.twig',	'Běžný'),
(19,	11,	'src/DemoCMS/BuilderTemplates/SimplePages/detail.twig',	''),
(20,	9,	'src/DemoCMS/BuilderTemplates/SimplePages/imageGallery_plain.twig',	'Obyčejná');

INSERT INTO `blocks_templates_slots` (`ID`, `template_ID`, `code_name`, `title`) VALUES
(1,	1,	'item',	NULL),
(2,	3,	'masterContent',	'obsah'),
(3,	7,	'header',	'hlavička'),
(4,	7,	'content',	'obsah'),
(5,	7,	'rightColumn',	'pravý sloupec'),
(6,	7,	'footer',	'patička'),
(7,	8,	'header',	'hlavička'),
(8,	8,	'welcomeText',	'uvítací text'),
(9,	8,	'smallText',	'malé články'),
(10,	8,	'footer',	'patička'),
(11,	9,	'header',	'hlavička'),
(12,	9,	'content',	'obsah'),
(13,	9,	'footer',	'patička');

INSERT INTO `web_pages` (`ID`, `parent_ID`, `position`, `block_set_ID`, `type`, `title`, `url_name`, `published`, `valid_from`, `valid_to`, `created_on`, `created_by`, `edited_on`, `edited_by`) VALUES
(1,	NULL,	1,	NULL,	'simplePage',	'Titulní stránka',	'/',	1,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL);

