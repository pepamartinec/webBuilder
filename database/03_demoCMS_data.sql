-- Adminer 3.3.4 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

INSERT INTO `block_sets` (`ID`, `name`, `parent_ID`, `pregenerated_structure`, `builder_type`, `created_on`, `created_by`, `edited_on`, `edited_by`) VALUES
(1,	'Základní stránka',	NULL,	NULL,	NULL,	'2012-05-08 13:33:55',	NULL,	'2012-05-08 20:47:23',	NULL),
(2,	'[Titulní stránka]',	1,	NULL,	NULL,	'2012-05-08 13:35:41',	NULL,	'2012-05-09 23:30:23',	NULL),
(44,	'[Produkty]',	1,	NULL,	NULL,	'2012-05-09 23:31:11',	NULL,	NULL,	NULL),
(45,	'[Produkt 1]',	46,	NULL,	NULL,	'2012-05-09 23:31:42',	NULL,	'2012-05-09 23:33:58',	NULL),
(46,	'Detail produktu',	1,	NULL,	NULL,	'2012-05-09 23:33:47',	NULL,	NULL,	NULL),
(47,	'[Produkt 2]',	46,	NULL,	NULL,	'2012-05-09 23:34:26',	NULL,	'2012-05-09 23:35:36',	NULL),
(48,	'[Reference 1]',	1,	NULL,	NULL,	'2012-05-09 23:36:54',	NULL,	NULL,	NULL),
(49,	'[Kontakt]',	1,	NULL,	NULL,	'2012-05-09 23:37:52',	NULL,	NULL,	NULL);

INSERT INTO `blocks` (`ID`, `code_name`, `category_ID`, `title`) VALUES
(20,	'\\WebBuilder\\Blocks\\Core\\ItemsList',	NULL,	NULL),
(21,	'\\WebBuilder\\Blocks\\Core\\WebPage',	NULL,	NULL),
(22,	'\\DemoCMS\\BuilderBlocks\\Other\\TextBlock',	4,	'Textový blok'),
(23,	'\\DemoCMS\\BuilderBlocks\\Other\\ContactForm',	4,	'Kontaktní formulář'),
(24,	'\\DemoCMS\\BuilderBlocks\\Layouts\\LandingPage',	1,	'Titulní stránka'),
(25,	'\\DemoCMS\\BuilderBlocks\\Layouts\\TwoColumnOverOne',	1,	'2 sloupce na 1'),
(26,	'\\DemoCMS\\BuilderBlocks\\Layouts\\Simple',	1,	'Základní'),
(27,	'\\DemoCMS\\BuilderBlocks\\Layouts\\RightColumn',	1,	'Pravý sloupec'),
(28,	'\\DemoCMS\\BuilderBlocks\\Layouts\\TwoColumn',	1,	'2 sloupce'),
(29,	'\\DemoCMS\\BuilderBlocks\\Layouts\\OneColumnOverTwo',	1,	'1 sloupce nad 2'),
(30,	'\\DemoCMS\\BuilderBlocks\\Layouts\\LeftColumn',	1,	'Levý sloupec'),
(31,	'\\DemoCMS\\BuilderBlocks\\Navigation\\Menu',	3,	'Menu'),
(32,	'\\DemoCMS\\BuilderBlocks\\Navigation\\Breadcrumbs',	3,	'Drobečková navigace'),
(33,	'\\DemoCMS\\BuilderBlocks\\SimplePages\\ImageGallery',	2,	'Galerie obrázků'),
(34,	'\\DemoCMS\\BuilderBlocks\\SimplePages\\PageDetail',	2,	'Obsah stránky'),
(35,	'\\DemoCMS\\BuilderBlocks\\SimplePages\\Discussion',	2,	'Diskuze'),
(36,	'\\DemoCMS\\BuilderBlocks\\SimplePages\\PageList',	2,	'Seznam podstránek'),
(37,	'\\DemoCMS\\BuilderBlocks\\SimplePages\\LikeButtons',	2,	'Like tlačitka');

INSERT INTO `blocks_categories` (`ID`, `title`) VALUES
(1,	'Layout'),
(2,	'Stránky'),
(3,	'Navigace'),
(4,	'Ostatní');

INSERT INTO `blocks_data_requirements` (`ID`, `block_ID`, `property`, `data_type`) VALUES
(17,	20,	'items',	'array'),
(18,	21,	'webPage',	'WebPageInterface'),
(19,	22,	'textBlockID',	'ID'),
(20,	23,	'webPage',	'cWebPage'),
(21,	24,	'webPage',	'cWebPage'),
(22,	25,	'webPage',	'cWebPage'),
(23,	26,	'webPage',	'cWebPage'),
(24,	27,	'webPage',	'cWebPage'),
(25,	28,	'webPage',	'cWebPage'),
(26,	29,	'webPage',	'cWebPage'),
(27,	30,	'webPage',	'cWebPage'),
(28,	31,	'webPage',	'cWebPage'),
(29,	32,	'webPage',	'cWebPage'),
(30,	33,	'webPage',	'cWebPage'),
(31,	34,	'page',	'cWebPage'),
(32,	35,	'webPage',	'cWebPage'),
(33,	36,	'parent',	'cWebPage'),
(34,	37,	'webPage',	'cWebPage');

INSERT INTO `blocks_data_requirements_providers` (`ID`, `required_property_ID`, `provider_ID`, `provider_property`) VALUES
(2,	20,	21,	'webPage'),
(3,	20,	21,	'webPage'),
(4,	21,	21,	'webPage'),
(5,	22,	21,	'webPage'),
(6,	23,	21,	'webPage'),
(7,	24,	21,	'webPage'),
(8,	25,	21,	'webPage'),
(9,	26,	21,	'webPage'),
(10,	27,	21,	'webPage'),
(11,	28,	21,	'webPage'),
(12,	29,	21,	'webPage'),
(13,	30,	21,	'webPage'),
(14,	31,	21,	'webPage'),
(15,	32,	21,	'webPage'),
(16,	33,	21,	'webPage'),
(17,	34,	21,	'webPage');

INSERT INTO `blocks_instances` (`ID`, `block_set_ID`, `template_ID`) VALUES
(1,	1,	3),
(2,	1,	16),
(4,	1,	6),
(33,	1,	14),
(69,	2,	12),
(70,	2,	23),
(71,	44,	8),
(72,	44,	23),
(73,	44,	19),
(78,	46,	8),
(79,	46,	23),
(80,	46,	25),
(81,	46,	17),
(82,	48,	8),
(83,	48,	23),
(84,	49,	13),
(85,	49,	23),
(86,	49,	5);

INSERT INTO `blocks_instances_data_constant` (`ID`, `instance_ID`, `property_ID`, `value`) VALUES
(7,	4,	19,	'1');

INSERT INTO `blocks_instances_data_inherited` (`ID`, `instance_ID`, `provider_instance_ID`, `provider_property_ID`) VALUES
(54,	2,	1,	11),
(55,	33,	1,	12),
(119,	69,	1,	4),
(120,	70,	1,	14),
(121,	71,	1,	6),
(122,	72,	1,	14),
(123,	73,	1,	16),
(128,	78,	1,	6),
(129,	79,	1,	14),
(130,	80,	1,	17),
(131,	81,	1,	15),
(132,	82,	1,	6),
(133,	83,	1,	14),
(134,	84,	1,	7),
(135,	85,	1,	14),
(136,	86,	1,	2),
(137,	86,	1,	3);

INSERT INTO `blocks_instances_subblocks` (`ID`, `parent_instance_ID`, `parent_slot_ID`, `position`, `inserted_instance_ID`) VALUES
(69,	1,	2,	0,	2),
(70,	1,	2,	1,	33),
(71,	1,	4,	0,	4),
(138,	1,	3,	0,	69),
(139,	69,	16,	0,	70),
(140,	1,	3,	0,	71),
(141,	71,	7,	0,	72),
(142,	71,	7,	1,	73),
(147,	1,	3,	0,	78),
(148,	78,	7,	0,	79),
(149,	78,	7,	1,	80),
(150,	78,	7,	2,	81),
(151,	1,	3,	0,	82),
(152,	82,	7,	0,	83),
(153,	1,	3,	0,	84),
(154,	84,	18,	0,	85),
(155,	84,	19,	0,	86);

INSERT INTO `blocks_templates` (`ID`, `block_ID`, `filename`, `title`) VALUES
(1,	20,	'src/WebBuilder/Templates/Core/itemsList_table.twig',	NULL),
(2,	20,	'src/WebBuilder/Templates/Core/itemsList_ul.twig',	NULL),
(3,	21,	'src/WebBuilder/Templates/Core/webPage_html5.twig',	'Webová stránka'),
(4,	22,	'src/DemoCMS/BuilderTemplates/Other/textBlock_title.twig',	'S titulkem'),
(5,	23,	'src/DemoCMS/BuilderTemplates/Other/contactForm.twig',	NULL),
(6,	22,	'src/DemoCMS/BuilderTemplates/Other/textBlock_noTitle.twig.twig',	'Bez titulku'),
(7,	28,	'src/DemoCMS/BuilderTemplates/Layouts/twoColumn.twig',	NULL),
(8,	26,	'src/DemoCMS/BuilderTemplates/Layouts/simple.twig',	NULL),
(9,	29,	'src/DemoCMS/BuilderTemplates/Layouts/oneColumnOverTwo.twig',	NULL),
(10,	30,	'src/DemoCMS/BuilderTemplates/Layouts/leftColumn.twig',	NULL),
(11,	25,	'src/DemoCMS/BuilderTemplates/Layouts/twoColumnOverOne.twig',	NULL),
(12,	24,	'src/DemoCMS/BuilderTemplates/Layouts/landingPage.twig',	NULL),
(13,	27,	'src/DemoCMS/BuilderTemplates/Layouts/rightColumn.twig',	NULL),
(14,	32,	'src/DemoCMS/BuilderTemplates/Navigation/breadcrumbs.twig',	NULL),
(15,	31,	'src/DemoCMS/BuilderTemplates/Navigation/menu_top.twig',	'Vždy nahoře'),
(16,	31,	'src/DemoCMS/BuilderTemplates/Navigation/menu_horizontal.twig',	'Horizontální'),
(17,	35,	'src/DemoCMS/BuilderTemplates/SimplePages/discussion_formBottom.twig',	'Formulář pod příspěvky'),
(18,	33,	'src/DemoCMS/BuilderTemplates/SimplePages/imageGallery_single.twig',	'Slideshow'),
(19,	36,	'src/DemoCMS/BuilderTemplates/SimplePages/list_noTitleImage.twig',	'Bez titulních obrázků'),
(20,	33,	'src/DemoCMS/BuilderTemplates/SimplePages/imageGallery_standard.twig',	'Interktivní'),
(21,	35,	'src/DemoCMS/BuilderTemplates/SimplePages/discussion_formTop.twig',	'Formulář nad příspěvky'),
(22,	36,	'src/DemoCMS/BuilderTemplates/SimplePages/list_standard.twig',	'S titlními obrázky'),
(23,	34,	'src/DemoCMS/BuilderTemplates/SimplePages/detail.twig',	NULL),
(24,	33,	'src/DemoCMS/BuilderTemplates/SimplePages/imageGallery_plain.twig',	'Jednoduchá'),
(25,	37,	'src/DemoCMS/BuilderTemplates/SimplePages/likeButtons.twig',	NULL);

INSERT INTO `blocks_templates_slots` (`ID`, `template_ID`, `code_name`, `title`) VALUES
(1,	1,	'item',	NULL),
(2,	3,	'header',	'hlavička'),
(3,	3,	'content',	'obsah'),
(4,	3,	'footer',	'patička'),
(5,	7,	'leftContent',	'levý obsah'),
(6,	7,	'rightContent',	'pravý obsah'),
(7,	8,	'content',	'obsah'),
(8,	9,	'content',	'obsah'),
(9,	9,	'leftColumn',	'levý sloupec'),
(10,	9,	'rightColumn',	'pravý sloupec'),
(11,	10,	'leftColumn',	'levý sloupec'),
(12,	10,	'content',	'obsah'),
(13,	11,	'leftColumn',	'levý sloupec'),
(14,	11,	'rightColumn',	'pravý sloupec'),
(15,	11,	'content',	'obsah'),
(16,	12,	'welcomeText',	'uvítací text'),
(17,	12,	'smallText',	'dodatkové texty'),
(18,	13,	'content',	'obsah'),
(19,	13,	'rightColumn',	'pravý sloupec');



INSERT INTO `simple_pages` (`ID`, `web_page_ID`, `title_image_ID`, `perex`, `content`, `created_on`, `created_by`, `edited_on`, `edited_by`) VALUES
(1,	1,	NULL,	'<font color=\"#ff00ff\" face=\"\'times new roman\'\" size=\"4\"><b><br></b></font>',	'<div style=\"text-align: left;\"><span style=\"font-family: Arial, Helvetica, sans; font-size: 11px; line-height: 14px; text-align: justify; \">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi faucibus tellus eget elit gravida mollis. Phasellus tincidunt volutpat vehicula. Sed volutpat dolor ut massa lobortis dictum. Ut egestas tempor dui eget auctor. Nam sodales consectetur est eget tempor. Donec luctus rutrum velit, at tempus mauris facilisis aliquet. Integer dictum orci at urna vulputate vehicula. Nunc vitae augue a nibh porttitor convallis eget a mi. Etiam hendrerit nisi eu est ullamcorper vitae luctus quam dapibus. Donec consequat laoreet vehicula. Aliquam metus lorem, mollis sed aliquam et, dictum nec sapien. Duis ut augue dolor. Morbi tincidunt iaculis facilisis. Nunc luctus semper ipsum ac faucibus.</span></div>',	'2012-05-08 13:35:41',	NULL,	'2012-05-09 23:30:23',	NULL),
(41,	45,	NULL,	'',	'<span style=\"font-family: Arial, Helvetica, sans; font-size: 11px; line-height: 14px; text-align: justify; \">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi faucibus tellus eget elit gravida mollis. Phasellus tincidunt volutpat vehicula. Sed volutpat dolor ut massa lobortis dictum. Ut egestas tempor dui eget auctor. Nam sodales consectetur est eget tempor. Donec luctus rutrum velit, at tempus mauris facilisis aliquet. Integer dictum orci at urna vulputate vehicula. Nunc vitae augue a nibh porttitor convallis eget a mi. Etiam hendrerit nisi eu est ullamcorper vitae luctus quam dapibus. Donec consequat laoreet vehicula. Aliquam metus lorem, mollis sed aliquam et, dictum nec sapien. Duis ut augue dolor. Morbi tincidunt iaculis facilisis. Nunc luctus semper ipsum ac faucibus.</span>',	'2012-05-09 23:31:11',	NULL,	NULL,	NULL),
(42,	46,	NULL,	'<span style=\"font-family: Arial, Helvetica, sans; font-size: 11px; line-height: 14px; text-align: justify; \">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi faucibus tellus eget elit gravida mollis. Phasellus tincidunt volutpat vehicula. Sed volutpat dolor ut massa lobortis dictum. Ut egestas tempor dui eget auctor. Nam sodales consectetur est eget tempor. Donec luctus rutrum velit, at tempus mauris facilisis aliquet. Integer dictum orci at urna vulputate vehicula. Nunc vitae augue a nibh porttitor convallis eget a mi. Etiam hendrerit nisi eu est ullamcorper vitae luctus quam dapibus. Donec consequat laoreet vehicula. Aliquam metus lorem, mollis sed aliquam et, dictum nec sapien. Duis ut augue dolor. Morbi tincidunt iaculis facilisis. Nunc luctus semper ipsum ac faucibus.</span>',	'<span style=\"font-family: Arial, Helvetica, sans; font-size: 11px; line-height: 14px; text-align: justify; \">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi faucibus tellus eget elit gravida mollis. Phasellus tincidunt volutpat vehicula. Sed volutpat dolor ut massa lobortis dictum. Ut egestas tempor dui eget auctor. Nam sodales consectetur est eget tempor. Donec luctus rutrum velit, at tempus mauris facilisis aliquet. Integer dictum orci at urna vulputate vehicula. Nunc vitae augue a nibh porttitor convallis eget a mi. Etiam hendrerit nisi eu est ullamcorper vitae luctus quam dapibus. Donec consequat laoreet vehicula. Aliquam metus lorem, mollis sed aliquam et, dictum nec sapien. Duis ut augue dolor. Morbi tincidunt iaculis facilisis. Nunc luctus semper ipsum ac faucibus.</span><p style=\"text-align: justify; font-size: 11px; line-height: 14px; margin-top: 0px; margin-right: 0px; margin-bottom: 14px; margin-left: 0px; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; font-family: Arial, Helvetica, sans; \">Proin at lectus dolor, nec gravida justo. Phasellus porttitor purus at tortor pellentesque consectetur. Curabitur ac sapien et est aliquet accumsan a vitae lectus. Pellentesque viverra leo eu lorem posuere ornare. Sed ut quam risus, a aliquam lorem. Aliquam vitae est eu risus volutpat condimentum vel sed ante. Duis pretium interdum dictum. Sed interdum mattis elit, eu rutrum odio ullamcorper condimentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Curabitur odio elit, rutrum quis eleifend et, facilisis in enim.</p><p style=\"text-align: justify; font-size: 11px; line-height: 14px; margin-top: 0px; margin-right: 0px; margin-bottom: 14px; margin-left: 0px; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; font-family: Arial, Helvetica, sans; \">Nulla id nibh massa, ut imperdiet libero. Aliquam tincidunt tempus purus a imperdiet. Donec a ante metus. Etiam scelerisque porta eros sit amet tempor. Phasellus adipiscing turpis id erat placerat facilisis vehicula erat congue. Nullam varius iaculis quam sed bibendum. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vestibulum imperdiet neque tincidunt risus suscipit ut sollicitudin felis sagittis. Proin libero nibh, blandit bibendum tristique ut, tincidunt at nisi.</p><p style=\"text-align: justify; font-size: 11px; line-height: 14px; margin-top: 0px; margin-right: 0px; margin-bottom: 14px; margin-left: 0px; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; font-family: Arial, Helvetica, sans; \">Donec sed turpis nec arcu molestie adipiscing. Etiam laoreet, eros nec fringilla commodo, urna velit porta nulla, placerat commodo urna arcu vitae elit. Ut in dui ipsum, non porta est. Morbi facilisis, odio ac accumsan sagittis, felis quam pretium metus, nec egestas quam metus dapibus magna. Suspendisse in eros ut urna gravida mattis vel et mi. Vivamus sit amet lorem at nulla vehicula congue. Quisque varius hendrerit quam at ornare. Cras ut mi vel dui iaculis accumsan eu tincidunt orci. Sed condimentum purus aliquam augue pulvinar nec venenatis nisi dignissim.</p>',	'2012-05-09 23:31:42',	NULL,	'2012-05-09 23:33:58',	NULL),
(43,	47,	NULL,	'<span style=\"font-family: Arial, Helvetica, sans; font-size: 11px; line-height: 14px; text-align: justify; \">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi faucibus tellus eget elit gravida mollis. Phasellus tincidunt volutpat vehicula. Sed volutpat dolor ut massa lobortis dictum. Ut egestas tempor dui eget auctor. Nam sodales consectetur est eget tempor. Donec luctus rutrum velit, at tempus mauris facilisis aliquet. Integer dictum orci at urna vulputate vehicula. Nunc vitae augue a nibh porttitor convallis eget a mi. Etiam hendrerit nisi eu est ullamcorper vitae luctus quam dapibus. Donec consequat laoreet vehicula. Aliquam metus lorem, mollis sed aliquam et, dictum nec sapien. Duis ut augue dolor. Morbi tincidunt iaculis facilisis. Nunc luctus semper ipsum ac faucibus.</span>',	'<span style=\"font-family: Arial, Helvetica, sans; font-size: 11px; line-height: 14px; text-align: justify; \">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi faucibus tellus eget elit gravida mollis. Phasellus tincidunt volutpat vehicula. Sed volutpat dolor ut massa lobortis dictum. Ut egestas tempor dui eget auctor. Nam sodales consectetur est eget tempor. Donec luctus rutrum velit, at tempus mauris facilisis aliquet. Integer dictum orci at urna vulputate vehicula. Nunc vitae augue a nibh porttitor convallis eget a mi. Etiam hendrerit nisi eu est ullamcorper vitae luctus quam dapibus. Donec consequat laoreet vehicula. Aliquam metus lorem, mollis sed aliquam et, dictum nec sapien. Duis ut augue dolor. Morbi tincidunt iaculis facilisis. Nunc luctus semper ipsum ac faucibus.</span><br><br><p style=\"text-align: justify; font-size: 11px; line-height: 14px; margin-top: 0px; margin-right: 0px; margin-bottom: 14px; margin-left: 0px; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; font-family: Arial, Helvetica, sans; \">Proin at lectus dolor, nec gravida justo. Phasellus porttitor purus at tortor pellentesque consectetur. Curabitur ac sapien et est aliquet accumsan a vitae lectus. Pellentesque viverra leo eu lorem posuere ornare. Sed ut quam risus, a aliquam lorem. Aliquam vitae est eu risus volutpat condimentum vel sed ante. Duis pretium interdum dictum. Sed interdum mattis elit, eu rutrum odio ullamcorper condimentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Curabitur odio elit, rutrum quis eleifend et, facilisis in enim.</p><p style=\"text-align: justify; font-size: 11px; line-height: 14px; margin-top: 0px; margin-right: 0px; margin-bottom: 14px; margin-left: 0px; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; font-family: Arial, Helvetica, sans; \">Nulla id nibh massa, ut imperdiet libero. Aliquam tincidunt tempus purus a imperdiet. Donec a ante metus. Etiam scelerisque porta eros sit amet tempor. Phasellus adipiscing turpis id erat placerat facilisis vehicula erat congue. Nullam varius iaculis quam sed bibendum. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vestibulum imperdiet neque tincidunt risus suscipit ut sollicitudin felis sagittis. Proin libero nibh, blandit bibendum tristique ut, tincidunt at nisi.</p><p style=\"text-align: justify; font-size: 11px; line-height: 14px; margin-top: 0px; margin-right: 0px; margin-bottom: 14px; margin-left: 0px; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; font-family: Arial, Helvetica, sans; \">Donec sed turpis nec arcu molestie adipiscing. Etiam laoreet, eros nec fringilla commodo, urna velit porta nulla, placerat commodo urna arcu vitae elit. Ut in dui ipsum, non porta est. Morbi facilisis, odio ac accumsan sagittis, felis quam pretium metus, nec egestas quam metus dapibus magna. Suspendisse in eros ut urna gravida mattis vel et mi. Vivamus sit amet lorem at nulla vehicula congue. Quisque varius hendrerit quam at ornare. Cras ut mi vel dui iaculis accumsan eu tincidunt orci. Sed condimentum purus aliquam augue pulvinar nec venenatis nisi dignissim.</p>',	'2012-05-09 23:34:26',	NULL,	'2012-05-09 23:35:36',	NULL),
(44,	49,	NULL,	'',	'<span style=\"font-family: Arial, Helvetica, sans; font-size: 11px; line-height: 14px; text-align: justify; \">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi faucibus tellus eget elit gravida mollis. Phasellus tincidunt volutpat vehicula. Sed volutpat dolor ut massa lobortis dictum. Ut egestas tempor dui eget auctor. Nam sodales consectetur est eget tempor. Donec luctus rutrum velit, at tempus mauris facilisis aliquet. Integer dictum orci at urna vulputate vehicula. Nunc vitae augue a nibh porttitor convallis eget a mi. Etiam hendrerit nisi eu est ullamcorper vitae luctus quam dapibus. Donec consequat laoreet vehicula. Aliquam metus lorem, mollis sed aliquam et, dictum nec sapien. Duis ut augue dolor. Morbi tincidunt iaculis facilisis. Nunc luctus semper ipsum ac faucibus.</span><p style=\"text-align: justify; font-size: 11px; line-height: 14px; margin-top: 0px; margin-right: 0px; margin-bottom: 14px; margin-left: 0px; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; font-family: Arial, Helvetica, sans; \">Proin at lectus dolor, nec gravida justo. Phasellus porttitor purus at tortor pellentesque consectetur. Curabitur ac sapien et est aliquet accumsan a vitae lectus. Pellentesque viverra leo eu lorem posuere ornare. Sed ut quam risus, a aliquam lorem. Aliquam vitae est eu risus volutpat condimentum vel sed ante. Duis pretium interdum dictum. Sed interdum mattis elit, eu rutrum odio ullamcorper condimentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Curabitur odio elit, rutrum quis eleifend et, facilisis in enim.</p><p style=\"text-align: justify; font-size: 11px; line-height: 14px; margin-top: 0px; margin-right: 0px; margin-bottom: 14px; margin-left: 0px; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; font-family: Arial, Helvetica, sans; \">Nulla id nibh massa, ut imperdiet libero. Aliquam tincidunt tempus purus a imperdiet. Donec a ante metus. Etiam scelerisque porta eros sit amet tempor. Phasellus adipiscing turpis id erat placerat facilisis vehicula erat congue. Nullam varius iaculis quam sed bibendum. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vestibulum imperdiet neque tincidunt risus suscipit ut sollicitudin felis sagittis. Proin libero nibh, blandit bibendum tristique ut, tincidunt at nisi.</p><p style=\"text-align: justify; font-size: 11px; line-height: 14px; margin-top: 0px; margin-right: 0px; margin-bottom: 14px; margin-left: 0px; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; font-family: Arial, Helvetica, sans; \">Donec sed turpis nec arcu molestie adipiscing. Etiam laoreet, eros nec fringilla commodo, urna velit porta nulla, placerat commodo urna arcu vitae elit. Ut in dui ipsum, non porta est. Morbi facilisis, odio ac accumsan sagittis, felis quam pretium metus, nec egestas quam metus dapibus magna. Suspendisse in eros ut urna gravida mattis vel et mi. Vivamus sit amet lorem at nulla vehicula congue. Quisque varius hendrerit quam at ornare. Cras ut mi vel dui iaculis accumsan eu tincidunt orci. Sed condimentum purus aliquam augue pulvinar nec venenatis nisi dignissim.</p>',	'2012-05-09 23:36:54',	NULL,	NULL,	NULL),
(45,	50,	NULL,	'',	'<span style=\"font-family: Arial, Helvetica, sans; font-size: 11px; line-height: 14px; text-align: justify; \">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi faucibus tellus eget elit gravida mollis. Phasellus tincidunt volutpat vehicula. Sed volutpat dolor ut massa lobortis dictum. Ut egestas tempor dui eget auctor. Nam sodales consectetur est eget tempor. Donec luctus rutrum velit, at tempus mauris facilisis aliquet. Integer dictum orci at urna vulputate vehicula. Nunc vitae augue a nibh porttitor convallis eget a mi. Etiam hendrerit nisi eu est ullamcorper vitae luctus quam dapibus. Donec consequat laoreet vehicula. Aliquam metus lorem, mollis sed aliquam et, dictum nec sapien. Duis ut augue dolor. Morbi tincidunt iaculis facilisis. Nunc luctus semper ipsum ac faucibus.</span><p style=\"text-align: justify; font-size: 11px; line-height: 14px; margin-top: 0px; margin-right: 0px; margin-bottom: 14px; margin-left: 0px; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; font-family: Arial, Helvetica, sans; \">Proin at lectus dolor, nec gravida justo. Phasellus porttitor purus at tortor pellentesque consectetur. Curabitur ac sapien et est aliquet accumsan a vitae lectus. Pellentesque viverra leo eu lorem posuere ornare. Sed ut quam risus, a aliquam lorem. Aliquam vitae est eu risus volutpat condimentum vel sed ante. Duis pretium interdum dictum. Sed interdum mattis elit, eu rutrum odio ullamcorper condimentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Curabitur odio elit, rutrum quis eleifend et, facilisis in enim.</p><p style=\"text-align: justify; font-size: 11px; line-height: 14px; margin-top: 0px; margin-right: 0px; margin-bottom: 14px; margin-left: 0px; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; font-family: Arial, Helvetica, sans; \">Nulla id nibh massa, ut imperdiet libero. Aliquam tincidunt tempus purus a imperdiet. Donec a ante metus. Etiam scelerisque porta eros sit amet tempor. Phasellus adipiscing turpis id erat placerat facilisis vehicula erat congue. Nullam varius iaculis quam sed bibendum. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vestibulum imperdiet neque tincidunt risus suscipit ut sollicitudin felis sagittis. Proin libero nibh, blandit bibendum tristique ut, tincidunt at nisi.</p><p style=\"text-align: justify; font-size: 11px; line-height: 14px; margin-top: 0px; margin-right: 0px; margin-bottom: 14px; margin-left: 0px; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; font-family: Arial, Helvetica, sans; \">Donec sed turpis nec arcu molestie adipiscing. Etiam laoreet, eros nec fringilla commodo, urna velit porta nulla, placerat commodo urna arcu vitae elit. Ut in dui ipsum, non porta est. Morbi facilisis, odio ac accumsan sagittis, felis quam pretium metus, nec egestas quam metus dapibus magna. Suspendisse in eros ut urna gravida mattis vel et mi. Vivamus sit amet lorem at nulla vehicula congue. Quisque varius hendrerit quam at ornare. Cras ut mi vel dui iaculis accumsan eu tincidunt orci. Sed condimentum purus aliquam augue pulvinar nec venenatis nisi dignissim.</p>',	'2012-05-09 23:37:52',	NULL,	NULL,	NULL);

INSERT INTO `text_blocks` (`ID`, `title`, `image_ID`, `content`, `created_on`, `created_by`, `edited_on`, `edited_by`) VALUES
(2,	'Úvod',	NULL,	'Nazdar!',	'2012-05-08 19:21:20',	NULL,	NULL,	NULL),
(3,	'DEMO',	NULL,	'DEMO CCCC',	'2012-05-08 21:18:41',	NULL,	'2012-05-08 21:19:05',	NULL);

INSERT INTO `web_pages` (`ID`, `parent_ID`, `position`, `block_set_ID`, `type`, `title`, `url_name`, `published`, `valid_from`, `valid_to`, `created_on`, `created_by`, `edited_on`, `edited_by`) VALUES
(1,	NULL,	2,	2,	'simplePage',	'Titulní stránka',	'/',	1,	NULL,	NULL,	NULL,	NULL,	'2012-05-09 23:30:23',	NULL),
(45,	1,	1,	44,	'simplePage',	'Produkty',	'/produkty',	1,	NULL,	NULL,	'2012-05-09 23:31:11',	NULL,	'2012-05-09 23:38:11',	NULL),
(46,	45,	1,	45,	'simplePage',	'Produkt 1',	'/produkt-1',	1,	NULL,	NULL,	'2012-05-09 23:31:42',	NULL,	'2012-05-09 23:36:08',	NULL),
(47,	45,	3,	47,	'simplePage',	'Produkt 2',	'/produkt-2',	1,	NULL,	NULL,	'2012-05-09 23:34:26',	NULL,	'2012-05-09 23:36:09',	NULL),
(48,	1,	2,	NULL,	'menuItem',	'Napsali o nás',	'',	1,	NULL,	NULL,	'2012-05-09 23:36:05',	NULL,	'2012-05-09 23:38:11',	NULL),
(49,	48,	0,	48,	'simplePage',	'Reference 1',	'/reference-1',	1,	NULL,	NULL,	'2012-05-09 23:36:54',	NULL,	NULL,	NULL),
(50,	1,	3,	49,	'simplePage',	'Kontakt',	'/kontakt',	1,	NULL,	NULL,	'2012-05-09 23:37:52',	NULL,	'2012-05-09 23:38:11',	NULL);

-- 2012-05-11 11:09:50

