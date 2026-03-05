-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 01, 2025 at 05:48 PM
-- Server version: 8.3.0
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zum`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities_log`
--

DROP TABLE IF EXISTS `activities_log`;
CREATE TABLE IF NOT EXISTS `activities_log` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED DEFAULT NULL,
  `activity` text NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `detail` varchar(255) NOT NULL,
  `performed_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
CREATE TABLE IF NOT EXISTS `announcements` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `subject` varchar(90) NOT NULL,
  `announcement` text NOT NULL,
  `updated_at` int UNSIGNED DEFAULT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attempts`
--

DROP TABLE IF EXISTS `attempts`;
CREATE TABLE IF NOT EXISTS `attempts` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `value` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `type` varchar(30) NOT NULL,
  `count` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `is_locked` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `attempted_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `backup_log`
--

DROP TABLE IF EXISTS `backup_log`;
CREATE TABLE IF NOT EXISTS `backup_log` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `backup_file` varchar(50) NOT NULL,
  `backup_option` tinyint UNSIGNED NOT NULL,
  `backup_action` tinyint UNSIGNED NOT NULL,
  `taken_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blocked_ip_addresses`
--

DROP TABLE IF EXISTS `blocked_ip_addresses`;
CREATE TABLE IF NOT EXISTS `blocked_ip_addresses` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `reason` varchar(500) NOT NULL,
  `blocked_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

DROP TABLE IF EXISTS `ci_sessions`;
CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int UNSIGNED NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` varchar(50) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `reply` text NOT NULL,
  `is_read` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `replied_at` int UNSIGNED DEFAULT NULL,
  `received_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
CREATE TABLE IF NOT EXISTS `countries` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `short_name` varchar(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=251 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `short_name`) VALUES
(1, 'Afghanistan', 'AF'),
(2, 'Åland Islands', 'AX'),
(3, 'Albania', 'AL'),
(4, 'Algeria', 'DZ'),
(5, 'American Samoa', 'AS'),
(6, 'Andorra', 'AD'),
(7, 'Angola', 'AO'),
(8, 'Anguilla', 'AI'),
(9, 'Antarctica', 'AQ'),
(10, 'Antigua & Barbuda', 'AG'),
(11, 'Argentina', 'AR'),
(12, 'Armenia', 'AM'),
(13, 'Aruba', 'AW'),
(14, 'Australia', 'AU'),
(15, 'Austria', 'AT'),
(16, 'Azerbaijan', 'AZ'),
(17, 'Bahamas', 'BS'),
(18, 'Bahrain', 'BH'),
(19, 'Bangladesh', 'BD'),
(20, 'Barbados', 'BB'),
(21, 'Belarus', 'BY'),
(22, 'Belgium', 'BE'),
(23, 'Belize', 'BZ'),
(24, 'Benin', 'BJ'),
(25, 'Bermuda', 'BM'),
(26, 'Bhutan', 'BT'),
(27, 'Bolivia', 'BO'),
(28, 'Bosnia & Herzegovina', 'BA'),
(29, 'Botswana', 'BW'),
(30, 'Bouvet Island', 'BV'),
(31, 'Brazil', 'BR'),
(32, 'British Indian Ocean Territory', 'IO'),
(33, 'British Virgin Islands', 'VG'),
(34, 'Brunei', 'BN'),
(35, 'Bulgaria', 'BG'),
(36, 'Burkina Faso', 'BF'),
(37, 'Burundi', 'BI'),
(38, 'Cambodia', 'KH'),
(39, 'Cameroon', 'CM'),
(40, 'Canada', 'CA'),
(41, 'Cape Verde', 'CV'),
(42, 'Caribbean Netherlands', 'BQ'),
(43, 'Cayman Islands', 'KY'),
(44, 'Central African Republic', 'CF'),
(45, 'Chad', 'TD'),
(46, 'Chile', 'CL'),
(47, 'China', 'CN'),
(48, 'Christmas Island', 'CX'),
(49, 'Cocos (Keeling) Islands', 'CC'),
(50, 'Colombia', 'CO'),
(51, 'Comoros', 'KM'),
(52, 'Congo - Brazzaville', 'CG'),
(53, 'Congo - Kinshasa', 'CD'),
(54, 'Cook Islands', 'CK'),
(55, 'Costa Rica', 'CR'),
(56, 'Côte d’Ivoire', 'CI'),
(57, 'Croatia', 'HR'),
(58, 'Cuba', 'CU'),
(59, 'Curaçao', 'CW'),
(60, 'Cyprus', 'CY'),
(61, 'Czechia', 'CZ'),
(62, 'Denmark', 'DK'),
(63, 'Djibouti', 'DJ'),
(64, 'Dominica', 'DM'),
(65, 'Dominican Republic', 'DO'),
(66, 'Ecuador', 'EC'),
(67, 'Egypt', 'EG'),
(68, 'El Salvador', 'SV'),
(69, 'Equatorial Guinea', 'GQ'),
(70, 'Eritrea', 'ER'),
(71, 'Estonia', 'EE'),
(72, 'Eswatini', 'SZ'),
(73, 'Ethiopia', 'ET'),
(74, 'Falkland Islands', 'FK'),
(75, 'Faroe Islands', 'FO'),
(76, 'Fiji', 'FJ'),
(77, 'Finland', 'FI'),
(78, 'France', 'FR'),
(79, 'French Guiana', 'GF'),
(80, 'French Polynesia', 'PF'),
(81, 'French Southern Territories', 'TF'),
(82, 'Gabon', 'GA'),
(83, 'Gambia', 'GM'),
(84, 'Georgia', 'GE'),
(85, 'Germany', 'DE'),
(86, 'Ghana', 'GH'),
(87, 'Gibraltar', 'GI'),
(88, 'Greece', 'GR'),
(89, 'Greenland', 'GL'),
(90, 'Grenada', 'GD'),
(91, 'Guadeloupe', 'GP'),
(92, 'Guam', 'GU'),
(93, 'Guatemala', 'GT'),
(94, 'Guernsey', 'GG'),
(95, 'Guinea', 'GN'),
(96, 'Guinea-Bissau', 'GW'),
(97, 'Guyana', 'GY'),
(98, 'Haiti', 'HT'),
(99, 'Heard & McDonald Islands', 'HM'),
(100, 'Honduras', 'HN'),
(101, 'Hong Kong SAR China', 'HK'),
(102, 'Hungary', 'HU'),
(103, 'Iceland', 'IS'),
(104, 'India', 'IN'),
(105, 'Indonesia', 'ID'),
(106, 'Iran', 'IR'),
(107, 'Iraq', 'IQ'),
(108, 'Ireland', 'IE'),
(109, 'Isle of Man', 'IM'),
(110, 'Israel', 'IL'),
(111, 'Italy', 'IT'),
(112, 'Jamaica', 'JM'),
(113, 'Japan', 'JP'),
(114, 'Jersey', 'JE'),
(115, 'Jordan', 'JO'),
(116, 'Kazakhstan', 'KZ'),
(117, 'Kenya', 'KE'),
(118, 'Kiribati', 'KI'),
(119, 'Kuwait', 'KW'),
(120, 'Kyrgyzstan', 'KG'),
(121, 'Laos', 'LA'),
(122, 'Latvia', 'LV'),
(123, 'Lebanon', 'LB'),
(124, 'Lesotho', 'LS'),
(125, 'Liberia', 'LR'),
(126, 'Libya', 'LY'),
(127, 'Liechtenstein', 'LI'),
(128, 'Lithuania', 'LT'),
(129, 'Luxembourg', 'LU'),
(130, 'Macao SAR China', 'MO'),
(131, 'Madagascar', 'MG'),
(132, 'Malawi', 'MW'),
(133, 'Malaysia', 'MY'),
(134, 'Maldives', 'MV'),
(135, 'Mali', 'ML'),
(136, 'Malta', 'MT'),
(137, 'Marshall Islands', 'MH'),
(138, 'Martinique', 'MQ'),
(139, 'Mauritania', 'MR'),
(140, 'Mauritius', 'MU'),
(141, 'Mayotte', 'YT'),
(142, 'Mexico', 'MX'),
(143, 'Micronesia', 'FM'),
(144, 'Micronesia', 'FM'),
(145, 'Moldova', 'MD'),
(146, 'Monaco', 'MC'),
(147, 'Mongolia', 'MN'),
(148, 'Montenegro', 'ME'),
(149, 'Montserrat', 'MS'),
(150, 'Morocco', 'MA'),
(151, 'Mozambique', 'MZ'),
(152, 'Myanmar (Burma)', 'MM'),
(153, 'Namibia', 'NA'),
(154, 'Nauru', 'NR'),
(155, 'Nepal', 'NP'),
(156, 'Netherlands', 'NL'),
(157, 'New Caledonia', 'NC'),
(158, 'New Zealand', 'NZ'),
(159, 'Nicaragua', 'NI'),
(160, 'Niger', 'NE'),
(161, 'Nigeria', 'NG'),
(162, 'Niue', 'NU'),
(163, 'Norfolk Island', 'NF'),
(164, 'North Korea', 'KP'),
(165, 'North Macedonia', 'MK'),
(166, 'Northern Mariana Islands', 'MP'),
(167, 'Norway', 'NO'),
(168, 'Oman', 'OM'),
(169, 'Pakistan', 'PK'),
(170, 'Palau', 'PW'),
(171, 'Palestinian Territories', 'PS'),
(172, 'Panama', 'PA'),
(173, 'Papua New Guinea', 'PG'),
(174, 'Paraguay', 'PY'),
(175, 'Peru', 'PE'),
(176, 'Philippines', 'PH'),
(177, 'Pitcairn Islands', 'PN'),
(178, 'Poland', 'PL'),
(179, 'Portugal', 'PT'),
(180, 'Puerto Rico', 'PR'),
(181, 'Qatar', 'QA'),
(182, 'Réunion', 'RE'),
(183, 'Romania', 'RO'),
(184, 'Russia', 'RU'),
(185, 'Rwanda', 'RW'),
(186, 'Samoa', 'WS'),
(187, 'San Marino', 'SM'),
(188, 'São Tomé & Príncipe', 'ST'),
(189, 'Saudi Arabia', 'SA'),
(190, 'Senegal', 'SN'),
(191, 'Serbia', 'RS'),
(192, 'Seychelles', 'SC'),
(193, 'Sierra Leone', 'SL'),
(194, 'Singapore', 'SG'),
(195, 'Sint Maarten', 'SX'),
(196, 'Slovakia', 'SK'),
(197, 'Slovenia', 'SI'),
(198, 'Solomon Islands', 'SB'),
(199, 'Somalia', 'SO'),
(200, 'South Africa', 'ZA'),
(201, 'South Georgia & South Sandwich Islands', 'GS'),
(202, 'South Korea', 'KR'),
(203, 'South Sudan', 'SS'),
(204, 'Spain', 'ES'),
(205, 'Sri Lanka', 'LK'),
(206, 'St. Barthélemy', 'BL'),
(207, 'St. Helena', 'SH'),
(208, 'St. Kitts & Nevis', 'KN'),
(209, 'St. Lucia', 'LC'),
(210, 'St. Martin', 'MF'),
(211, 'St. Pierre & Miquelon', 'PM'),
(212, 'St. Vincent & Grenadines', 'VC'),
(213, 'Sudan', 'SD'),
(214, 'Suriname', 'SR'),
(215, 'Svalbard & Jan Mayen', 'SJ'),
(216, 'Sweden', 'SE'),
(217, 'Switzerland', 'CH'),
(218, 'Syria', 'SY'),
(219, 'Taiwan', 'TW'),
(220, 'Tajikistan', 'TJ'),
(221, 'Tanzania', 'TZ'),
(222, 'Thailand', 'TH'),
(223, 'Timor-Leste', 'TL'),
(224, 'Togo', 'TG'),
(225, 'Tokelau', 'TK'),
(226, 'Tonga', 'TO'),
(227, 'Trinidad & Tobago', 'TT'),
(228, 'Tunisia', 'TN'),
(229, 'Turkey', 'TR'),
(230, 'Turkmenistan', 'TM'),
(231, 'Turks & Caicos Islands', 'TC'),
(232, 'Tuvalu', 'TV'),
(233, 'U.S. Outlying Islands', 'UM'),
(234, 'U.S. Virgin Islands', 'VI'),
(235, 'Uganda', 'UG'),
(236, 'Ukraine', 'UA'),
(237, 'United Arab Emirates', 'AE'),
(238, 'United Kingdom', 'GB'),
(239, 'United States', 'US'),
(240, 'Uruguay', 'UY'),
(241, 'Uzbekistan', 'UZ'),
(242, 'Vanuatu', 'VU'),
(243, 'Vatican City', 'VA'),
(244, 'Venezuela', 'VE'),
(245, 'Vietnam', 'VN'),
(246, 'Wallis & Futuna', 'WF'),
(247, 'Western Sahara', 'EH'),
(248, 'Yemen', 'YE'),
(249, 'Zambia', 'ZM'),
(250, 'Zimbabwe', 'ZW');

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
CREATE TABLE IF NOT EXISTS `currencies` (
  `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `code` varchar(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `name`, `code`) VALUES
(1, 'US Dollar', 'USD'),
(2, 'UAE Dirham', 'AED'),
(3, 'Afghanistan Afghani', 'AFN'),
(4, 'Albanian Lek', 'ALL'),
(5, 'Armenian Dram', 'AMD'),
(6, 'Netherlands Antillian Guilder', 'ANG'),
(7, 'Kwanza', 'AOA'),
(8, 'Argentine Peso', 'ARS'),
(9, 'Australian Dollar', 'AUD'),
(10, 'Aruban Guilder', 'AWG'),
(11, 'Azerbaijan Manat', 'AZN'),
(12, 'Convertible Marks', 'BAM'),
(13, 'Barbados Dollar', 'BBD'),
(14, 'Bangladeshi Taka', 'BDT'),
(15, 'Lev, Bulgarian Lev', 'BGN'),
(16, 'Burundi Franc', 'BIF'),
(17, 'Bermuda Dollar', 'BMD'),
(18, 'Brunei Dolla', 'BND'),
(19, 'Boliviano, Mvdol', 'BOB'),
(20, 'Brazil Real', 'BRL'),
(21, 'Bahamian Dollar', 'BSD'),
(22, 'Pula', 'BWP'),
(23, 'Belize Dollar', 'BZD'),
(24, 'Canadian Dollar', 'CAD'),
(25, 'Franc Congolais', 'CDF'),
(26, 'Swiss Franc', 'CHF'),
(27, 'Chilean Peso', 'CLP'),
(28, 'Yuan Renminbi', 'CNY'),
(29, 'Colombian Peso', 'COP'),
(30, 'Costa Rican Colon', 'CRC'),
(31, 'Cape Verde Escudo', 'CVE'),
(32, 'Czech Koruna', 'CZK'),
(33, 'Djibouti Franc', 'DJF'),
(34, 'Danish Krone', 'DKK'),
(35, 'Dominican Peso', 'DOP'),
(36, 'Algerian Dinar', 'DZD'),
(37, 'Egyptian Pound', 'EGP'),
(38, 'Ethiopian Birr', 'ETB'),
(39, 'European Currency Unit', 'EUR'),
(40, 'Fiji Dollar', 'FJD'),
(41, 'Falkland Islands Pound', 'FKP'),
(42, 'Pound Sterling', 'GBP'),
(43, 'Lari', 'GEL'),
(44, 'Gibraltar Pound', 'GIP'),
(45, 'Dalasi', 'GMD'),
(46, 'Guinea Franc', 'GNF'),
(47, 'Guatemalan Quetza', 'GTQ'),
(48, 'Guyana Dollar', 'GYD'),
(49, 'Hong Kong Dollar', 'HKD'),
(50, 'Honduran Lempira', 'HNL'),
(51, 'Croatian Kuna', 'HRK'),
(52, 'Haiti Gourde', 'HTG'),
(53, 'Forint', 'HUF'),
(54, 'Indonesian Rupiah', 'IDR'),
(55, 'New Israeli Sheqel', 'ILS'),
(56, 'Indian Rupee, Ngultrum', 'INR'),
(57, 'Iceland Krona', 'ISK'),
(58, 'Jamaican Dollar', 'JMD'),
(59, 'Yen', 'JPY'),
(60, 'Kenyan Shilling', 'KES'),
(61, 'Kyrgyzstan Som', 'KGS'),
(62, 'Cambodian Riel', 'KHR'),
(63, 'CFA Franc (BEAC)', 'KMF'),
(64, 'South Korean Won', 'KRW'),
(65, 'Kuwaiti Dinar', 'KWD'),
(66, 'Kazakhstan Tenge', 'KZT'),
(67, 'Laos Kip', 'LAK'),
(68, 'Lebanese Pound', 'LBP'),
(69, 'Sri Lanka Rupee', 'LKR'),
(70, 'Liberian Dollar', 'LRD'),
(71, 'Rand, Loti', 'LSL'),
(72, 'Moroccan Dirham', 'MAD'),
(73, 'Moldovan Leu', 'MDL'),
(74, 'Madagascar Ariary', 'MGA'),
(75, 'Macedonian Denar', 'MKD'),
(76, 'Myanmar Kyat', 'MMK'),
(77, 'Mongolian Tugrik', 'MNT'),
(78, 'Pataca', 'MOP'),
(79, 'Mauritanian Ouguiya', 'MRO'),
(80, 'Mauritius Rupee', 'MUR'),
(81, 'Maldives Rufiyaa', 'MVR'),
(82, 'Kwacha', 'MWK'),
(83, 'Mexican Peso, Mexican Unidad de Inversion (UDI)', 'MXN'),
(84, 'Malaysian Ringgit', 'MYR'),
(85, 'Mozambique Metical', 'MZN'),
(86, 'Rand, Namibia Dollar', 'NAD'),
(87, 'Nigerian Naira', 'NGN'),
(88, 'Nicaraguan Cordoba Oro', 'NIO'),
(89, 'Norwegian Krone', 'NOK'),
(90, 'Nepalese Rupee', 'NPR'),
(91, 'New Zealand', 'NZD'),
(92, 'Balboa', 'PAB'),
(93, 'Peru Nuevo Sol', 'PEN'),
(94, 'Papua New Guinea Kina', 'PGK'),
(95, 'Philippine Peso', 'PHP'),
(96, 'Pakistan Rupee', 'PKR'),
(97, 'Poland Zloty', 'PLN'),
(98, 'Paraguay Guarani', 'PYG'),
(99, 'Qatari Rial', 'QAR'),
(100, 'Romania Leu', 'RON'),
(101, 'Serbia Dinar', 'RSD'),
(102, 'Russian Ruble, Russian Ruble', 'RUB'),
(103, 'Rwanda Franc', 'RWF'),
(104, 'Saudi Riyal', 'SAR'),
(105, 'Solomon Islands Dollar', 'SBD'),
(106, 'Seychelles Rupee', 'SCR'),
(107, 'Swedish Krona', 'SEK'),
(108, 'Singapore Dollar', 'SGD'),
(109, 'St. Helena Pound', 'SHP'),
(110, 'Sierra Leone Leone', 'SLL'),
(111, 'Somalia Shilling', 'SOS'),
(112, 'Suriname Dollar', 'SRD'),
(113, 'Sao Tome and Principe Dobra', 'STD'),
(114, 'El Salvador Colon', 'SVC'),
(115, 'Swaziland Lilangeni', 'SZL'),
(116, 'Thai Baht', 'THB'),
(117, 'Tajikistan Somoni', 'TJS'),
(118, 'Tonga Pa\'anga', 'TOP'),
(119, 'Turkey Lira', 'TRY'),
(120, 'Trinidad and Tobago Dollar', 'TTD'),
(121, 'New Taiwan Dollar', 'TWD'),
(122, 'Tanzanian Shilling', 'TZS'),
(123, 'Hryvnia', 'UAH'),
(124, 'Ugandan Shilling', 'UGX'),
(125, 'Peso Uruguayo', 'UYU'),
(126, 'Uzbekistan Sum', 'UZS'),
(127, 'Viet Nam Dong', 'VND'),
(128, 'Vanuatu Vatu', 'VUV'),
(129, 'Tala', 'WST'),
(130, 'CFA Franc (BEAC)', 'XAF'),
(131, 'East Caribbean Dollar', 'XCD'),
(132, 'Guinea-Bissau Peso, CFA Franc (BCEAO)', 'XOF'),
(133, 'CFP Franc', 'XPF'),
(134, 'Yemeni Rial', 'YER'),
(135, 'South Africa Rand', 'ZAR'),
(136, 'Zambian kwacha', 'ZMW');

-- --------------------------------------------------------

--
-- Table structure for table `custom_fields`
--

DROP TABLE IF EXISTS `custom_fields`;
CREATE TABLE IF NOT EXISTS `custom_fields` (
  `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(90) NOT NULL,
  `type` varchar(30) NOT NULL,
  `options` varchar(1500) NOT NULL,
  `on_registeration` tinyint UNSIGNED NOT NULL,
  `is_required` tinyint UNSIGNED NOT NULL,
  `guide_text` varchar(255) NOT NULL,
  `updated_at` int UNSIGNED DEFAULT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `custom_pages`
--

DROP TABLE IF EXISTS `custom_pages`;
CREATE TABLE IF NOT EXISTS `custom_pages` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `slug` varchar(30) NOT NULL,
  `content` text NOT NULL,
  `meta_description` varchar(255) NOT NULL,
  `meta_keywords` varchar(255) NOT NULL,
  `visibility` tinyint UNSIGNED NOT NULL,
  `created_by` int UNSIGNED NOT NULL,
  `updated_at` int UNSIGNED DEFAULT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dashboard`
--

DROP TABLE IF EXISTS `dashboard`;
CREATE TABLE IF NOT EXISTS `dashboard` (
  `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  `access_key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dashboard`
--

INSERT INTO `dashboard` (`id`, `access_key`, `value`) VALUES
(1, 'recent_users_stats', '{\"September\":0,\"October\":0,\"November\":0,\"December\":0,\"January\":0,\"February\":0,\"March\":0}'),
(2, 'social_users', '0'),
(3, 'new_within_24hrs', '0'),
(4, 'online_today', '0'),
(5, 'total_users', '1');

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

DROP TABLE IF EXISTS `email_templates`;
CREATE TABLE IF NOT EXISTS `email_templates` (
  `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(90) NOT NULL,
  `subject` varchar(90) NOT NULL,
  `hook` varchar(50) NOT NULL,
  `language` varchar(255) NOT NULL,
  `template` text NOT NULL,
  `is_built_in` tinyint UNSIGNED NOT NULL,
  `updated_at` int UNSIGNED DEFAULT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`id`, `title`, `subject`, `hook`, `language`, `template`, `is_built_in`, `updated_at`, `created_at`) VALUES
(1, 'Email Verification', 'Please Verify your Account', 'email_verification', 'english', '<p>Hi {USER_NAME},<br><br>Thank you so much for joining the {SITE_NAME}.<br><br>Please verify your account by clicking on the following link:<br><a href=\"{EMAIL_LINK}\" target=\"_blank\">Click here</a><br><br>If you did not register, please kindly ignore this email.<br><br>Thanks,<br>{SITE_NAME}<br></p>', 1, NULL, 1611059287),
(2, 'Two Factor Authentication', 'Two Factor Authentication', '2f_authentication', 'english', '<p>Hi {USER_NAME},<br><br>Your two-factor authentication code is {2F_CODE}.<br><br>Thanks,<br>{SITE_NAME}<br></p>', 1, NULL, 1611075739),
(3, 'Forgot Password', 'Password Reset', 'forgot_password', 'english', '<p>Hi {USER_NAME},<br><br>We\'ve received a password reset request. Please click on the following link to proceed:<br><a href=\"{EMAIL_LINK}\" target=\"_blank\">Reset Password</a><br><br>The link will expire after a limited time. If you didn\'t request to reset your password, please kindly ignore this message.<br><br>Thanks,<br>{SITE_NAME}<br></p>', 1, NULL, 1611135272),
(4, 'Ticket Reply Notification', 'Your Ticket Has Been Replied', 'ticket_reply_notification', 'english', '<p>Dear {USER_NAME},<br><br>Your ticket has been replied to by our agent. Please login to your account to see.<br><br>Thanks,<br>{SITE_NAME} Support<br></p>', 1, NULL, 1611135470),
(5, 'Member Invite', 'Sign Up Invitation', 'member_invite', 'english', '<p>Hi there,<br><br>You have been invited to sign up as a member of {SITE_NAME}.<br><br>Please click on the following link to proceed:<br><a href=\"{EMAIL_LINK}\" target=\"_blank\">Register now</a><br><br>Thanks,<br>{SITE_NAME}<br></p>', 1, NULL, 1611136036),
(6, 'Newsletter Subscribe', 'Please Confirm the Subscription', 'subscribe', 'english', '<p>Hi there,<br><br>Thanks for the request of subscribing to our newsletter, to receive messages from us, please confirm the subscription:<br><a href=\"{SUB_LINK}\" target=\"_blank\">Confirm now</a><br><br>If you did not request, please kindly ignore this message and click on the link below:<br><a href=\"{UNSUB_LINK}\" target=\"_blank\">Unsubscribe</a><br><br>Thanks,<br>{SITE_NAME}<br></p>', 1, NULL, 1611254752),
(7, 'Change Email', 'Please Verify your Email Address', 'change_email', 'english', '<p>Hi there,<br><br>We\'ve received a request to change your email address. Please click on the following link to proceed:<br><a href=\"{EMAIL_LINK}\" target=\"_blank\">Click here</a><br><br>If you didn\'t request, please kindly ignore this email.<br><br>Thanks,<br>{SITE_NAME}</p>', 1, NULL, 1614102803),
(8, 'Changed Password', 'Password Changed', 'changed_password', 'english', '<p>Hi {USER_NAME},<br><br>Your account password is successfully changed. If you didn\'t request the change, please send us a message through the contact form.<br><br>Thanks,<br>{SITE_NAME} Support</p>', 1, NULL, 1616355883),
(9, 'Welcome User', 'Your Account is Successfully Registered', 'welcome_user', 'english', '<p>Hi {USER_NAME},<br><br>You\'re welcome to {SITE_NAME}.<br><br>You can login to your account with username: {LOGIN_USERNAME} and the password that you created when registering.<br><br>You can go to the login page by clicking on the following link:<br><a href=\"{EMAIL_LINK}\" target=\"_blank\">Click here</a><br><br>Thanks,<br>{SITE_NAME}</p>', 1, NULL, 1625991429),
(10, 'Newsletter Email', '{SUBJECT}', 'newsletter_email', 'english', '<p>{MESSAGE} You are receiving this message because you have subscribed to our newsletter. If you want to unsubscribe, please click on the following link:<br><a href=\"{UNSUB_LINK}\" target=\"_blank\">Unsubscribe</a></p>', 1, NULL, 1658576859);

-- --------------------------------------------------------

--
-- Table structure for table `email_tokens`
--

DROP TABLE IF EXISTS `email_tokens`;
CREATE TABLE IF NOT EXISTS `email_tokens` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `token` varchar(32) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `requested_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
CREATE TABLE IF NOT EXISTS `pages` (
  `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `meta_description` varchar(255) NOT NULL,
  `meta_keywords` varchar(255) NOT NULL,
  `updated_at` int UNSIGNED DEFAULT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `content`, `meta_description`, `meta_keywords`, `updated_at`, `created_at`) VALUES
(1, '<p>The WEBSITE NAME website located at http://yourwebsite.com is a copyrighted work belonging to WEBSITE NAME. Certain features of the Site may be subject to additional guidelines, terms, or rules, which will be posted on the Site in connection with such features.<br><br>All such additional terms, guidelines, and rules are incorporated by reference into these Terms.<br><br>These Terms of Use described the legally binding terms and conditions that oversee your use of the Site. BY LOGGING INTO THE SITE, YOU ARE BEING COMPLIANT THAT THESE TERMS and you represent that you have the authority and capacity to enter into these Terms. YOU SHOULD BE AT LEAST 18 YEARS OF AGE TO ACCESS THE SITE. IF YOU DISAGREE WITH ALL OF THE PROVISION OF THESE TERMS, DO NOT LOG INTO AND/OR USE THE SITE.<br><br>These terms require the use of arbitration Section 10.2 on an individual basis to resolve disputes and also limit the remedies available to you in the event of a dispute.</p><h3>Accounts</h3><b>Account Creation.</b> For you to use the Site, you have to start an account and provide information about yourself. You warrant that: (a) all required registration information you submit is truthful, up-to-date and accurate; (b) you will maintain the accuracy of such information. You may delete your Account at any time by following the instructions on the Site. Company may suspend or terminate your Account in accordance with Section.<br><br><b>Account Responsibilities.</b> You are responsible for maintaining the confidentiality of your Account login information and are fully responsible for all activities that occur under your Account. You approve to immediately notify the Company of any unauthorized use, or suspected unauthorized use of your Account.  Company cannot and will not be liable for any loss or damage arising from your failure to comply with the above requirements.<br><br><h3>Access to the Site</h3><b>Subject to these Terms.</b> Company grants you a non-transferable, non-exclusive, revocable, limited license to access the Site solely for your own personal, noncommercial use.<br><br><b>Certain Restrictions.</b> The rights approved to you in these Terms are subject to the following restrictions: (a) you shall not sell, rent, lease, transfer, assign, distribute, host, or otherwise commercially exploit the Site; (b) you shall not change, make derivative works of, disassemble, reverse compile or reverse engineer any part of the Site; (c) you shall not access the Site in order to build a similar or competitive website; and (d) except as expressly stated herein, no part of the Site may be copied, reproduced, distributed, republished, downloaded, displayed, posted or transmitted in any form or by any means unless otherwise indicated, any future release, update, or other addition to functionality of the Site shall be subject to these Terms.  All copyright and other proprietary notices on the Site must be retained on all copies thereof.<br><br>Company reserves the right to change, suspend, or cease the Site with or without notice to you.  You approved that Company will not be held liable to you or any third-party for any change, interruption, or termination of the Site or any part.<br><br><b>No Support or Maintenance.</b> You agree that Company will have no obligation to provide you with any support in connection with the Site.<br><br>Excluding any User Content that you may provide, you are aware that all the intellectual property rights, including copyrights, patents, trademarks, and trade secrets, in the Site and its content are owned by Company or Company\'s suppliers. Note that these Terms and access to the Site do not give you any rights, title or interest in or to any intellectual property rights, except for the limited access rights expressed in Section 2.1. Company and its suppliers reserve all rights not granted in these Terms.<br><br><h3>User Content</h3><b>User Content.</b> “User Content” means any and all information and content that a user submits to the Site. You are exclusively responsible for your User Content. You bear all risks associated with use of your User Content.  You hereby certify that your User Content does not violate our Acceptable Use Policy.  You may not represent or imply to others that your User Content is in any way provided, sponsored or endorsed by Company. Because you alone are responsible for your User Content, you may expose yourself to liability. Company is not obliged to backup any User Content that you post; also, your User Content may be deleted at any time without prior notice to you. You are solely responsible for making your own backup copies of your User Content if you desire.<br><br>You hereby grant to Company an irreversible, nonexclusive, royalty-free and fully paid, worldwide license to reproduce, distribute, publicly display and perform, prepare derivative works of, incorporate into other works, and otherwise use and exploit your User Content, and to grant sublicenses of the foregoing rights, solely for the purposes of including your User Content in the Site.  You hereby irreversibly waive any claims and assertions of moral rights or attribution with respect to your User Content.<br><br><b>Acceptable Use Policy.</b> The following terms constitute our “Acceptable Use Policy”: You agree not to use the Site to collect, upload, transmit, display, or distribute any User Content (i) that violates any third-party right or any intellectual property or proprietary right; (ii) that is unlawful, harassing, abusive, tortious, threatening, harmful, invasive of another\'s privacy, vulgar, defamatory, false, intentionally misleading, trade libelous, pornographic, obscene, patently offensive, promotes racism, bigotry, hatred, or physical harm of any kind against any group or individual; (iii) that is harmful to minors in any way; or (iv) that is in violation of any law, regulation, or obligations or restrictions imposed by any third party.<br><br>In addition, you agree not to: (i) upload, transmit, or distribute to or through the Site any software intended to damage or alter a computer system or data; (ii) send through the Site unsolicited or unauthorized advertising, promotional materials, junk mail, spam, chain letters, pyramid schemes, or any other form of duplicative or unsolicited messages; (iii) use the Site to harvest, collect, gather or assemble information or data regarding other users without their consent; (iv) interfere with, disrupt, or create an undue burden on servers or networks connected to the Site, or violate the regulations, policies or procedures of such networks; (v) attempt to gain unauthorized access to the Site, whether through password mining or any other means; (vi) harass or interfere with any other user\'s use and enjoyment of the Site; or (vi) use software or automated agents or scripts to produce multiple accounts on the Site, or to generate automated searches, requests, or queries to the Site.<br><br>We reserve the right to review any User Content, and to investigate and/or take appropriate action against you in our sole discretion if you violate the Acceptable Use Policy or any other provision of these Terms or otherwise create liability for us or any other person. Such action may include removing or modifying your User Content, terminating your Account in accordance with Section 8, and/or reporting you to law enforcement authorities.<br><br>If you provide Company with any feedback or suggestions regarding the Site, you hereby assign to Company all rights in such Feedback and agree that Company shall have the right to use and fully exploit such Feedback and related information in any manner it believes appropriate.  Company will treat any Feedback you provide to Company as non-confidential and non-proprietary.<br><br>You agree to indemnify and hold Company and its officers, employees, and agents harmless, including costs and attorneys\' fees, from any claim or demand made by any third-party due to or arising out of (a) your use of the Site, (b) your violation of these Terms, (c) your violation of applicable laws or regulations or (d) your User Content.  Company reserves the right to assume the exclusive defense and control of any matter for which you are required to indemnify us, and you agree to cooperate with our defense of these claims.  You agree not to settle any matter without the prior written consent of Company.  Company will use reasonable efforts to notify you of any such claim, action or proceeding upon becoming aware of it.<br><br><h3>Third-Party Links & Ads; Other Users</h3><b>Third-Party Links & Ads.</b> The Site may contain links to third-party websites and services, and/or display advertisements for third-parties.  Such Third-Party Links & Ads are not under the control of Company, and Company is not responsible for any Third-Party Links & Ads.  Company provides access to these Third-Party Links & Ads only as a convenience to you, and does not review, approve, monitor, endorse, warrant, or make any representations with respect to Third-Party Links & Ads.  You use all Third-Party Links & Ads at your own risk, and should apply a suitable level of caution and discretion in doing so. When you click on any of the Third-Party Links & Ads, the applicable third party\'s terms and policies apply, including the third party\'s privacy and data gathering practices.<br><br><b>Other Users.</b> Each Site user is solely responsible for any and all of its own User Content.  Because we do not control User Content, you acknowledge and agree that we are not responsible for any User Content, whether provided by you or by others.  You agree that Company will not be responsible for any loss or damage incurred as the result of any such interactions.  If there is a dispute between you and any Site user, we are under no obligation to become involved.<br><br>You hereby release and forever discharge the Company and our officers, employees, agents, successors, and assigns from, and hereby waive and relinquish, each and every past, present and future dispute, claim, controversy, demand, right, obligation, liability, action and cause of action of every kind and nature, that has arisen or arises directly or indirectly out of, or that relates directly or indirectly to, the Site. If you are a California resident, you hereby waive California civil code section 1542 in connection with the foregoing, which states: “a general release does not extend to claims which the creditor does not know or suspect to exist in his or her favor at the time of executing the release, which if known by him or her must have materially affected his or her settlement with the debtor.”<br><br><b>Cookies and Web Beacons.</b> Like any other website, WEBSITE NAME uses ‘cookies\'. These cookies are used to store information including visitors\' preferences, and the pages on the website that the visitor accessed or visited. The information is used to optimize the users\' experience by customizing our web page content based on visitors\' browser type and/or other information.<br><br><h3>Disclaimers</h3>The site is provided on an “as-is” and “as available” basis, and company and our suppliers expressly disclaim any and all warranties and conditions of any kind, whether express, implied, or statutory, including all warranties or conditions of merchantability, fitness for a particular purpose, title, quiet enjoyment, accuracy, or non-infringement.  We and our suppliers make not guarantee that the site will meet your requirements, will be available on an uninterrupted, timely, secure, or error-free basis, or will be accurate, reliable, free of viruses or other harmful code, complete, legal, or safe.  If applicable law requires any warranties with respect to the site, all such warranties are limited in duration to ninety (90) days from the date of first use.<br><br>Some jurisdictions do not allow the exclusion of implied warranties, so the above exclusion may not apply to you.  Some jurisdictions do not allow limitations on how long an implied warranty lasts, so the above limitation may not apply to you.<br><br><b>Limitation on Liability</b><br>To the maximum extent permitted by law, in no event shall company or our suppliers be liable to you or any third-party for any lost profits, lost data, costs of procurement of substitute products, or any indirect, consequential, exemplary, incidental, special or punitive damages arising from or relating to these terms or your use of, or incapability to use the site even if company has been advised of the possibility of such damages.  Access to and use of the site is at your own discretion and risk, and you will be solely responsible for any damage to your device or computer system, or loss of data resulting therefrom.<br><br>To the maximum extent permitted by law, notwithstanding anything to the contrary contained herein, our liability to you for any damages arising from or related to this agreement, will at all times be limited to a maximum of fifty U.S. dollars (u.s. $50). The existence of more than one claim will not enlarge this limit.  You agree that our suppliers will have no liability of any kind arising from or relating to this agreement.<br><br>Some jurisdictions do not allow the limitation or exclusion of liability for incidental or consequential damages, so the above limitation or exclusion may not apply to you.<br><br><b>Term and Termination.</b> Subject to this Section, these Terms will remain in full force and effect while you use the Site.  We may suspend or terminate your rights to use the Site at any time for any reason at our sole discretion, including for any use of the Site in violation of these Terms.  Upon termination of your rights under these Terms, your Account and right to access and use the Site will terminate immediately.  You understand that any termination of your Account may involve deletion of your User Content associated with your Account from our live databases.  Company will not have any liability whatsoever to you for any termination of your rights under these Terms.  Even after your rights under these Terms are terminated, the following provisions of these Terms will remain in effect: Sections 2 through 2.5, Section 3 and Sections 4 through 10.<br><br><h3>Copyright Policy.</h3>Company respects the intellectual property of others and asks that users of our Site do the same.  In connection with our Site, we have adopted and implemented a policy respecting copyright law that provides for the removal of any infringing materials and for the termination of users of our online Site who are repeated infringers of intellectual property rights, including copyrights.  If you believe that one of our users is, through the use of our Site, unlawfully infringing the copyright(s) in a work, and wish to have the allegedly infringing material removed, the following information in the form of a written notification (pursuant to 17 U.S.C. § 512(c)) must be provided to our designated Copyright Agent:<p></p><ul><li>your physical or electronic signature;</li><li>identification of the copyrighted work(s) that you claim to have been infringed;</li><li>identification of the material on our services that you claim is infringing and that you request us to remove;</li><li>sufficient information to permit us to locate such material;</li><li>your address, telephone number, and e-mail address;</li><li>a statement that you have a good faith belief that use of the objectionable material is not authorized by the copyright owner, its agent, or under the law; and</li><li>a statement that the information in the notification is accurate, and under penalty of perjury, that you are either the owner of the copyright that has allegedly been infringed or that you are authorized to act on behalf of the copyright owner.</li><li>Please note that, pursuant to 17 U.S.C. § 512(f), any misrepresentation of material fact in a written notification automatically subjects the complaining party to liability for any damages, costs and attorney\'s fees incurred by us in connection with the written notification and allegation of copyright infringement.</li></ul><p></p><h3>General</h3>These Terms are subject to occasional revision, and if we make any substantial changes, we may notify you by sending you an e-mail to the last e-mail address you provided to us and/or by prominently posting notice of the changes on our Site. You are responsible for providing us with your most current e-mail address. In the event that the last e-mail address that you have provided us is not valid our dispatch of the e-mail containing such notice will nonetheless constitute effective notice of the changes described in the notice. Any changes to these Terms will be effective upon the earliest of thirty (30) calendar days following our dispatch of an e-mail notice to you or thirty (30) calendar days following our posting of notice of the changes on our Site. These changes will be effective immediately for new users of our Site. Continued use of our Site following notice of such changes shall indicate your acknowledgement of such changes and agreement to be bound by the terms and conditions of such changes. Dispute Resolution. Please read this Arbitration Agreement carefully. It is part of your contract with Company and affects your rights. It contains procedures for MANDATORY BINDING ARBITRATION AND A CLASS ACTION WAIVER.<br><br><b>Applicability of Arbitration Agreement.</b> All claims and disputes in connection with the Terms or the use of any product or service provided by the Company that cannot be resolved informally or in small claims court shall be resolved by binding arbitration on an individual basis under the terms of this Arbitration Agreement. Unless otherwise agreed to, all arbitration proceedings shall be held in English. This Arbitration Agreement applies to you and the Company, and to any subsidiaries, affiliates, agents, employees, predecessors in interest, successors, and assigns, as well as all authorized or unauthorized users or beneficiaries of services or goods provided under the Terms.<br><br><b>Notice Requirement and Informal Dispute Resolution.</b> Before either party may seek arbitration, the party must first send to the other party a written Notice of Dispute describing the nature and basis of the claim or dispute, and the requested relief. A Notice to the Company should be sent to: YOUR COMPANY ADDRESS. After the Notice is received, you and the Company may attempt to resolve the claim or dispute informally. If you and the Company do not resolve the claim or dispute within thirty (30) days after the Notice is received, either party may begin an arbitration proceeding. The amount of any settlement offer made by any party may not be disclosed to the arbitrator until after the arbitrator has determined the amount of the award to which either party is entitled.<br><br><b>Arbitration Rules.</b> Arbitration shall be initiated through the American Arbitration Association, an established alternative dispute resolution provider that offers arbitration as set forth in this section. If AAA is not available to arbitrate, the parties shall agree to select an alternative ADR Provider. The rules of the ADR Provider shall govern all aspects of the arbitration except to the extent such rules are in conflict with the Terms. The AAA Consumer Arbitration Rules governing the arbitration are available online at adr.org or by calling the AAA at 1-800-778-7879. The arbitration shall be conducted by a single, neutral arbitrator. Any claims or disputes where the total amount of the award sought is less than Ten Thousand U.S. Dollars (US $10,000.00) may be resolved through binding non-appearance-based arbitration, at the option of the party seeking relief. For claims or disputes where the total amount of the award sought is Ten Thousand U.S. Dollars (US $10,000.00) or more, the right to a hearing will be determined by the Arbitration Rules. Any hearing will be held in a location within 100 miles of your residence, unless you reside outside of the United States, and unless the parties agree otherwise. If you reside outside of the U.S., the arbitrator shall give the parties reasonable notice of the date, time and place of any oral hearings. Any judgment on the award rendered by the arbitrator may be entered in any court of competent jurisdiction. If the arbitrator grants you an award that is greater than the last settlement offer that the Company made to you prior to the initiation of arbitration, the Company will pay you the greater of the award or $2,500.00. Each party shall bear its own costs and disbursements arising out of the arbitration and shall pay an equal share of the fees and costs of the ADR Provider.<br><br><b>Additional Rules for Non-Appearance Based Arbitration. </b>If non-appearance based arbitration is elected, the arbitration shall be conducted by telephone, online and/or based solely on written submissions; the specific manner shall be chosen by the party initiating the arbitration. The arbitration shall not involve any personal appearance by the parties or witnesses unless otherwise agreed by the parties.<br><br><b>Time Limits.</b> If you or the Company pursues arbitration, the arbitration action must be initiated and/or demanded within the statute of limitations and within any deadline imposed under the AAA Rules for the pertinent claim.<br><br><b>Authority of Arbitrator.</b> If arbitration is initiated, the arbitrator will decide the rights and liabilities of you and the Company, and the dispute will not be consolidated with any other matters or joined with any other cases or parties. The arbitrator shall have the authority to grant motions dispositive of all or part of any claim. The arbitrator shall have the authority to award monetary damages, and to grant any non-monetary remedy or relief available to an individual under applicable law, the AAA Rules, and the Terms. The arbitrator shall issue a written award and statement of decision describing the essential findings and conclusions on which the award is based. The arbitrator has the same authority to award relief on an individual basis that a judge in a court of law would have. The award of the arbitrator is final and binding upon you and the Company.<br><br><b>Waiver of Jury Trial.</b> THE PARTIES HEREBY WAIVE THEIR CONSTITUTIONAL AND STATUTORY RIGHTS TO GO TO COURT AND HAVE A TRIAL IN FRONT OF A JUDGE OR A JURY, instead electing that all claims and disputes shall be resolved by arbitration under this Arbitration Agreement. Arbitration procedures are typically more limited, more efficient and less expensive than rules applicable in a court and are subject to very limited review by a court. In the event any litigation should arise between you and the Company in any state or federal court in a suit to vacate or enforce an arbitration award or otherwise, YOU AND THE COMPANY WAIVE ALL RIGHTS TO A JURY TRIAL, instead electing that the dispute be resolved by a judge.<br><br><b>Waiver of Class or Consolidated Actions.</b> All claims and disputes within the scope of this arbitration agreement must be arbitrated or litigated on an individual basis and not on a class basis, and claims of more than one customer or user cannot be arbitrated or litigated jointly or consolidated with those of any other customer or user.<br><br><b>Confidentiality.</b> All aspects of the arbitration proceeding shall be strictly confidential. The parties agree to maintain confidentiality unless otherwise required by law. This paragraph shall not prevent a party from submitting to a court of law any information necessary to enforce this Agreement, to enforce an arbitration award, or to seek injunctive or equitable relief.<br><br><b>Severability.</b> If any part or parts of this Arbitration Agreement are found under the law to be invalid or unenforceable by a court of competent jurisdiction, then such specific part or parts shall be of no force and effect and shall be severed and the remainder of the Agreement shall continue in full force and effect.<br><br><b>Right to Waive.</b> Any or all of the rights and limitations set forth in this Arbitration Agreement may be waived by the party against whom the claim is asserted. Such waiver shall not waive or affect any other portion of this Arbitration Agreement.<br><br><b>Survival of Agreement.</b> This Arbitration Agreement will survive the termination of your relationship with Company.<br><br><b>Small Claims Court.</b> Nonetheless the foregoing, either you or the Company may bring an individual action in small claims court.<br><br><b>Emergency Equitable Relief.</b> Anyhow the foregoing, either party may seek emergency equitable relief before a state or federal court in order to maintain the status quo pending arbitration. A request for interim measures shall not be deemed a waiver of any other rights or obligations under this Arbitration Agreement.<br><br><b>Claims Not Subject to Arbitration.</b> Notwithstanding the foregoing, claims of defamation, violation of the Computer Fraud and Abuse Act, and infringement or misappropriation of the other party\'s patent, copyright, trademark or trade secrets shall not be subject to this Arbitration Agreement.<br><br>In any circumstances where the foregoing Arbitration Agreement permits the parties to litigate in court, the parties hereby agree to submit to the personal jurisdiction of the courts located within Netherlands County, California, for such purposes.<br><br>The Site may be subject to U.S. export control laws and may be subject to export or import regulations in other countries. You agree not to export, re-export, or transfer, directly or indirectly, any U.S. technical data acquired from Company, or any products utilizing such data, in violation of the United States export laws or regulations.<br><br>Company is located at the address in Section 10.8. If you are a California resident, you may report complaints to the Complaint Assistance Unit of the Division of Consumer Product of the California Department of Consumer Affairs by contacting them in writing at 400 R Street, Sacramento, CA 95814, or by telephone at (800) 952-5210.<br><br><b>Electronic Communications.</b> The communications between you and Company use electronic means, whether you use the Site or send us emails, or whether Company posts notices on the Site or communicates with you via email. For contractual purposes, you (a) consent to receive communications from Company in an electronic form; and (b) agree that all terms and conditions, agreements, notices, disclosures, and other communications that Company provides to you electronically satisfy any legal obligation that such communications would satisfy if it were be in a hard copy writing.<br><br><b>Entire Terms.</b> These Terms constitute the entire agreement between you and us regarding the use of the Site. Our failure to exercise or enforce any right or provision of these Terms shall not operate as a waiver of such right or provision. The section titles in these Terms are for convenience only and have no legal or contractual effect. The word “including” means “including without limitation”. If any provision of these Terms is held to be invalid or unenforceable, the other provisions of these Terms will be unimpaired and the invalid or unenforceable provision will be deemed modified so that it is valid and enforceable to the maximum extent permitted by law. Your relationship to Company is that of an independent contractor, and neither party is an agent or partner of the other. These Terms, and your rights and obligations herein, may not be assigned, subcontracted, delegated, or otherwise transferred by you without Company\'s prior written consent, and any attempted assignment, subcontract, delegation, or transfer in violation of the foregoing will be null and void. Company may freely assign these Terms. The terms and conditions set forth in these Terms shall be binding upon assignees.<br><br><b>Copyright/Trademark Information.</b> Copyright ©. All rights reserved. All trademarks, logos and service marks displayed on the Site are our property or the property of other third-parties. You are not permitted to use these Marks without our prior written consent or the consent of such third party which may own the Marks.<br><br><h3>Contact Information</h3><b>Address:</b> YOUR COMPANY ADDRESS<br><b>Email:</b> email@yourwebsite.com', 'The meta description goes here...', 'terms of use, terms', NULL, 1611744608),
(2, '<p>At WEBSITE NAME, accessible at http://yourwebsite.com, one of our main priorities is the privacy of our visitors. This Privacy Policy document contains types of information that is collected and recorded by WEBSITE NAME and how we use it.<br><br>If you have additional questions or require more information about our Privacy Policy, do not hesitate to contact us.<br><br>This privacy policy applies only to our online activities and is valid for visitors to our website with regards to the information that they shared and/or collect in WEBSITE NAME. This policy is not applicable to any information collected offline or via channels other than this website.<br><br><b>Consent</b><br>By using our website, you hereby consent to our Privacy Policy and agree to its terms.<br><br><b>Information we collect</b><br>The personal information that you are asked to provide, and the reasons why you are asked to provide it, will be made clear to you at the point we ask you to provide your personal information.<br><br>If you contact us directly, we may receive additional information about you such as your name, email address, the contents of the message and/or attachments you may send us, and any other information you may choose to provide.<br><br>When you register for an Account, we may ask for your contact information, including items such as name, and email address.<br><br><b>How we use your information</b><br>We use the information we collect in various ways, including to:</p><ul><li>Provide, operate, and maintain our website</li><li>Improve, personalize, and expand our website</li><li>Understand and analyze how you use our website</li><li>Develop new products, services, features, and functionality</li><li>Communicate with you, either directly or through one of our partners, including for customer service, to provide you with updates and other information relating to the website, and for marketing and promotional purposes</li><li>Send you emails</li><li>Find and prevent fraud</li></ul><p><b>Log Files<br></b>WEBSITE NAME follows a standard procedure of using log files. These files log visitors when they visit websites. All hosting companies do this and a part of hosting services\' analytics. The information collected by log files include internet protocol (IP) addresses, browser type, Internet Service Provider (ISP), date and time stamp, referring/exit pages, and possibly the number of clicks. These are not linked to any information that is personally identifiable. The purpose of the information is for analyzing trends, administering the site, tracking users\' movement on the website, and gathering demographic information.<br><br><b>Cookies and Web Beacons</b><br>Like any other website, WEBSITE NAME uses ‘cookies\'. These cookies are used to store information including visitors\' preferences, and the pages on the website that the visitor accessed or visited. The information is used to optimize the users\' experience by customizing our web page content based on visitors\' browser type and/or other information.<br><br><b>Advertising Partners Privacy Policies</b><br>You may consult this list to find the Privacy Policy for each of the advertising partners of WEBSITE NAME.<br><br>Third-party ad servers or ad networks uses technologies like cookies, JavaScript, or Web Beacons that are used in their respective advertisements and links that appear on WEBSITE NAME, which are sent directly to users\' browser. They automatically receive your IP address when this occurs. These technologies are used to measure the effectiveness of their advertising campaigns and/or to personalize the advertising content that you see on websites that you visit.<br><br>Note that WEBSITE NAME has no access to or control over these cookies that are used by third-party advertisers.<br><br><b>Third-Party Privacy Policies</b><br>WEBSITE NAME\'s Privacy Policy does not apply to other advertisers or websites. Thus, we are advising you to consult the respective Privacy Policies of these third-party ad servers for more detailed information. It may include their practices and instructions about how to opt-out of certain options.<br><br>You can choose to disable cookies through your individual browser options. To know more detailed information about cookie management with specific web browsers, it can be found at the browsers\' respective websites.<br><br><b>CCPA Privacy Policy (Do Not Sell My Personal Information)</b><br>Under the CCPA, among other rights, California consumers have the right to:</p><ul><li>Request that a business that collects a consumer\'s personal data disclose the categories and specific pieces of personal data that a business has collected about consumers.</li><li>Request that a business delete any personal data about the consumer that a business has collected.</li><li>Request that a business that sells a consumer\'s personal data, not sell the consumer\'s personal data.</li><li>If you make a request, we have one month to respond to you. If you would like to exercise any of these rights, please contact us.</li></ul><p><b>GDPR Privacy Policy (Data Protection Rights)</b><br>We would like to make sure you are fully aware of all of your data protection rights. Every user is entitled to the following:</p><ul><li>The right to access – You have the right to request copies of your personal data. We may charge you a small fee for this service.</li><li>The right to rectification – You have the right to request that we correct any information you believe is inaccurate. You also have the right to request that we complete the information you believe is incomplete.</li><li>The right to erasure – You have the right to request that we erase your personal data, under certain conditions.</li><li>The right to restrict processing – You have the right to request that we restrict the processing of your personal data, under certain conditions.</li><li>The right to object to processing – You have the right to object to our processing of your personal data, under certain conditions.</li><li>The right to data portability – You have the right to request that we transfer the data that we have collected to another organization, or directly to you, under certain conditions.</li><li>If you make a request, we have one month to respond to you. If you would like to exercise any of these rights, please contact us.</li></ul><p><b>Children\'s Information</b><br>Another part of our priority is adding protection for children while using the internet. We encourage parents and guardians to observe, participate in, and/or monitor and guide their online activity.<br><br>WEBSITE NAME does not knowingly collect any Personal Identifiable Information from children under the age of 13. If you think that your child provided this kind of information on our website, we strongly encourage you to contact us immediately and we will do our best efforts to promptly remove such information from our records.</p>', 'The meta description goes here...', 'privacy policy, visitor\'s privacy', NULL, 1611744759);

-- --------------------------------------------------------

--
-- Table structure for table `payments_log`
--

DROP TABLE IF EXISTS `payments_log`;
CREATE TABLE IF NOT EXISTS `payments_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED DEFAULT NULL,
  `currency_id` tinyint UNSIGNED NOT NULL,
  `item_id` tinyint UNSIGNED DEFAULT NULL,
  `item_name` varchar(90) NOT NULL,
  `gateway` varchar(30) NOT NULL,
  `transaction_id` varchar(50) NOT NULL,
  `hash` varchar(32) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `quantity` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `visible_to_user` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `create_invoice` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `status` varchar(30) NOT NULL,
  `performed_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_items`
--

DROP TABLE IF EXISTS `payment_items`;
CREATE TABLE IF NOT EXISTS `payment_items` (
  `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(90) NOT NULL,
  `description` varchar(255) NOT NULL,
  `type` varchar(30) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `currency_id` tinyint UNSIGNED NOT NULL,
  `days` int UNSIGNED NOT NULL,
  `sales` int UNSIGNED NOT NULL DEFAULT '0',
  `status` tinyint UNSIGNED NOT NULL,
  `updated_at` int UNSIGNED DEFAULT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `access_key` varchar(50) NOT NULL,
  `is_built_in` tinyint UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `access_key`, `is_built_in`) VALUES
(1, 'Support', 'support', 1),
(2, 'Roles & Permissions', 'roles_and_permissions', 1),
(3, 'Impersonate', 'impersonate', 1),
(4, 'Backup', 'backup', 1),
(5, 'Subscribers', 'subscribers', 1),
(6, 'Users', 'users', 1),
(7, 'Pages', 'pages', 1),
(8, 'Settings', 'settings', 1),
(9, 'Tools', 'tools', 1),
(10, 'Payment', 'payment', 1);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `access_key` varchar(50) NOT NULL,
  `is_built_in` tinyint UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `access_key`, `is_built_in`) VALUES
(1, 'Super Admin', 'super_admin', 1),
(2, 'Admin', 'admin', 1),
(3, 'User', 'user', 1);

-- --------------------------------------------------------

--
-- Table structure for table `roles_permissions`
--

DROP TABLE IF EXISTS `roles_permissions`;
CREATE TABLE IF NOT EXISTS `roles_permissions` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` tinyint UNSIGNED NOT NULL,
  `permission_id` tinyint UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles_permissions`
--

INSERT INTO `roles_permissions` (`id`, `role_id`, `permission_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7),
(8, 1, 8),
(9, 1, 9),
(10, 1, 10),
(11, 2, 6),
(12, 2, 9),
(13, 2, 7);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  `access_key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `access_key` (`access_key`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `access_key`, `value`) VALUES
(1, 'site_name', 'ZUM'),
(2, 'site_description', 'The description goes here...'),
(3, 'site_keywords', 'keyword1, keyword2'),
(4, 'site_logo', 'ba3e20bd31a3ebf9230cbe2ebd979faf.png'),
(5, 'site_favicon', '270a7c5fc534816289d443254f59cae9.png'),
(6, 'site_timezone', ''),
(7, 'site_tagline', 'CI Advanced User Management System'),
(8, 'site_about', 'A handy script for applications that are required a user-based authentication system.'),
(9, 'site_show_cookie_popup', '1'),
(10, 'enable_newsletter', '1'),
(11, 'maintenance_mode', '0'),
(12, 'mm_message', 'Thanks for the patience.'),
(13, 'mm_allowed_ips', ''),
(14, 'u_max_avator_size', '500x500'),
(15, 'u_track_activities', '1'),
(16, 'u_password_requirement', 'low'),
(17, 'u_temporary_lockout', 'medium'),
(18, 'u_lockout_unlock_time', '1'),
(19, 'u_reset_password', '1'),
(20, 'u_2f_authentication', '0'),
(21, 'u_enable_registration', '1'),
(22, 'u_default_user_role', '3'),
(23, 'u_can_remove_them', '0'),
(24, 'fb_enable_login', '0'),
(25, 'fb_app_id', ''),
(26, 'fb_app_secret', ''),
(27, 'gl_enable', '0'),
(28, 'gl_client_key', ''),
(29, 'gl_secret_key', ''),
(30, 'tw_enable_login', '0'),
(31, 'tw_consumer_key', ''),
(32, 'tw_consumer_secret', ''),
(33, 'google_analytics_id', ''),
(34, 'gr_enable', '0'),
(35, 'gr_public_key', ''),
(36, 'gr_secret_key', ''),
(37, 'iv_company_name', ''),
(38, 'iv_phone_number', ''),
(39, 'iv_address_1', ''),
(40, 'iv_address_2', ''),
(41, 'cu_enable_form', '1'),
(42, 'cu_email_address', ''),
(43, 'sp_enable_tickets', '1'),
(44, 'sp_notify_replies', '0'),
(45, 'e_protocol', 'mail'),
(46, 'e_host', ''),
(47, 'e_port', ''),
(48, 'e_encryption', ''),
(49, 'e_username', ''),
(50, 'e_password', ''),
(51, 'e_sender', ''),
(52, 'e_sender_name', ''),
(53, 'sp_publishable_key', ''),
(54, 'sp_secret_key', ''),
(55, 'sp_enable', '0'),
(56, 'site_theme', 'default'),
(57, 'u_req_ev_onchange', '1'),
(58, 'u_allow_username_change', '1'),
(59, 'dashboard_cache_time', '3600'),
(60, 'dc_last_updated', '1615738417'),
(61, 'u_notify_pass_changed', '1'),
(62, 'credit_pay_enable', '1'),
(63, 'facebook_link', ''),
(64, 'twitter_link', ''),
(65, 'linkedin_link', ''),
(66, 'youtube_link', ''),
(67, 'ipinfo_token', ''),
(68, 'enable_restful_api', '0'),
(69, 'u_send_welcome_email', '1'),
(70, 'u_allow_email_change', '0'),
(71, 'custom_css', ''),
(72, 'custom_js', ''),
(73, 'vkontakte_app_id', ''),
(74, 'vkontakte_secret_key', ''),
(75, 'vkontakte_enable', '0'),
(76, 'cloudflare_turnstile_site_key', ''),
(77, 'cloudflare_turnstile_secret_key', ''),
(78, 'cloudflare_turnstile_enable', '0'),
(79, 'captcha_plugin', 'google_recaptcha'),
(80, 'hcaptcha_site_key', ''),
(81, 'hcaptcha_secret_key', ''),
(82, 'hcaptcha_enable', '0');

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

DROP TABLE IF EXISTS `subscribers`;
CREATE TABLE IF NOT EXISTS `subscribers` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `email_address` varchar(255) NOT NULL,
  `authentication_token` varchar(32) NOT NULL,
  `confirmed_at` int UNSIGNED DEFAULT NULL,
  `subscribed_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_address` (`email_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `subject` varchar(90) NOT NULL,
  `message` text NOT NULL,
  `attachment` varchar(255) NOT NULL,
  `attachment_name` varchar(255) NOT NULL,
  `priority` varchar(10) NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `category_id` tinyint UNSIGNED NOT NULL,
  `is_read` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `last_message_area` varchar(10) NOT NULL DEFAULT 'user',
  `status` tinyint UNSIGNED NOT NULL DEFAULT '2',
  `updated_at` int UNSIGNED DEFAULT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets_categories`
--

DROP TABLE IF EXISTS `tickets_categories`;
CREATE TABLE IF NOT EXISTS `tickets_categories` (
  `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `updated_at` int UNSIGNED DEFAULT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets_categories`
--

INSERT INTO `tickets_categories` (`id`, `name`, `updated_at`, `created_at`) VALUES
(1, 'General Inquiries', NULL, 1611213421);

-- --------------------------------------------------------

--
-- Table structure for table `tickets_replies`
--

DROP TABLE IF EXISTS `tickets_replies`;
CREATE TABLE IF NOT EXISTS `tickets_replies` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `attachment` varchar(255) NOT NULL,
  `attachment_name` varchar(255) NOT NULL,
  `replied_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` varchar(25) NOT NULL,
  `last_name` varchar(25) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `pending_email_address` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `restful_api_key` varchar(32) NOT NULL,
  `picture` varchar(255) NOT NULL DEFAULT 'default.png',
  `gender` varchar(6) NOT NULL,
  `about` varchar(500) NOT NULL,
  `phone_number` varchar(50) NOT NULL,
  `company` varchar(255) NOT NULL,
  `date_format` varchar(10) NOT NULL DEFAULT 'Y-m-d',
  `time_format` varchar(10) NOT NULL DEFAULT 'H:i:s',
  `timezone` varchar(32) NOT NULL,
  `language` varchar(255) NOT NULL,
  `currency_id` tinyint UNSIGNED DEFAULT NULL,
  `country_id` tinyint UNSIGNED DEFAULT NULL,
  `state` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `address_1` varchar(255) NOT NULL,
  `address_2` varchar(255) NOT NULL,
  `zip_code` varchar(16) NOT NULL,
  `role` tinyint UNSIGNED NOT NULL,
  `facebook` varchar(255) NOT NULL,
  `twitter` varchar(255) NOT NULL,
  `linkedin` varchar(255) NOT NULL,
  `youtube` varchar(255) NOT NULL,
  `two_factor_authentication` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `premium_time` int DEFAULT NULL,
  `premium_item_id` tinyint UNSIGNED DEFAULT NULL,
  `is_online` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `online_time` int UNSIGNED DEFAULT NULL,
  `online_date` varchar(10) NOT NULL,
  `status` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `reason` varchar(255) NOT NULL,
  `is_verified` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `announcements_last_read_at` int UNSIGNED DEFAULT NULL,
  `last_activity` int UNSIGNED DEFAULT NULL,
  `last_login` int UNSIGNED DEFAULT NULL,
  `last_login_interface` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `updated_at` int UNSIGNED DEFAULT NULL,
  `registration_source` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `oauth_identifier` varchar(255) NOT NULL,
  `registered_month_year` varchar(7) NOT NULL,
  `registered_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_address` (`email_address`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `email_address`, `pending_email_address`, `password`, `restful_api_key`, `picture`, `gender`, `about`, `phone_number`, `company`, `date_format`, `time_format`, `timezone`, `language`, `currency_id`, `country_id`, `state`, `city`, `address_1`, `address_2`, `zip_code`, `role`, `facebook`, `twitter`, `linkedin`, `youtube`, `two_factor_authentication`, `premium_time`, `premium_item_id`, `is_online`, `online_time`, `online_date`, `status`, `reason`, `is_verified`, `announcements_last_read_at`, `last_activity`, `last_login`, `last_login_interface`, `updated_at`, `registration_source`, `oauth_identifier`, `registered_month_year`, `registered_at`) VALUES
(1, 'Super', 'Admin', 'superadmin', '', '', '', '', 'default.png', '', '', '', '', 'Y-m-d', 'h:i:s A', '', '', 0, 0, '', '', '', '', '', 1, '', '', '', '', 0, NULL, NULL, 0, NULL, '', 1, '', 1, NULL, NULL, NULL, 1, NULL, 1, '', '2-2021', 1612125637);

-- --------------------------------------------------------

--
-- Table structure for table `users_credits`
--

DROP TABLE IF EXISTS `users_credits`;
CREATE TABLE IF NOT EXISTS `users_credits` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `currency_id` tinyint UNSIGNED NOT NULL,
  `credit` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_custom_fields`
--

DROP TABLE IF EXISTS `users_custom_fields`;
CREATE TABLE IF NOT EXISTS `users_custom_fields` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `custom_field_id` tinyint UNSIGNED NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_invites`
--

DROP TABLE IF EXISTS `users_invites`;
CREATE TABLE IF NOT EXISTS `users_invites` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `email_address` varchar(255) NOT NULL,
  `invitation_code` varchar(32) NOT NULL,
  `expires_in` tinyint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `bypass_registration` tinyint UNSIGNED NOT NULL,
  `status` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `updated_at` int UNSIGNED DEFAULT NULL,
  `invited_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_rememberings`
--

DROP TABLE IF EXISTS `users_rememberings`;
CREATE TABLE IF NOT EXISTS `users_rememberings` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `token` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_sent_emails`
--

DROP TABLE IF EXISTS `users_sent_emails`;
CREATE TABLE IF NOT EXISTS `users_sent_emails` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `sent_to` int UNSIGNED NOT NULL,
  `sent_by` int UNSIGNED NOT NULL,
  `sent_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_sessions`
--

DROP TABLE IF EXISTS `users_sessions`;
CREATE TABLE IF NOT EXISTS `users_sessions` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `platform` varchar(255) NOT NULL,
  `browser` varchar(255) NOT NULL,
  `interface` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `last_activity` int UNSIGNED DEFAULT NULL,
  `last_location` varchar(255) NOT NULL,
  `logged_in_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
