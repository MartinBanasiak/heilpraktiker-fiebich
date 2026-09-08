-- MySQL dump 10.13  Distrib 5.7.12, for Win64 (x86_64)
--
-- Host: localhost    Database: tracking
-- ------------------------------------------------------
-- Server version	5.5.5-10.1.21-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Tabellenstruktur für Tabelle `auth_tokens`
--

DROP TABLE IF EXISTS `auth_tokens`;
CREATE TABLE IF NOT EXISTS `auth_tokens` (
  `token` varchar(128) NOT NULL,
  `creation_time` timestamp(2) NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `code` varchar(20) NOT NULL,
  `category_description` text NOT NULL,
  `category_description_2` text NOT NULL,
  `meta_keywords` varchar(250) NOT NULL,
  `meta_description` varchar(250) NOT NULL,
  `view_duration_seconds` bigint(20) unsigned NOT NULL DEFAULT '0',
  `view_counter` int(11) unsigned NOT NULL DEFAULT '0',
  `item_add_to_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_add_to_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_add_to_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `item_rm_from_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_rm_from_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_rm_from_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `item_order_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_order_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_order_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `customer`
--

DROP TABLE IF EXISTS `customer`;
CREATE TABLE IF NOT EXISTS `customer` (
  `id` int(11) NOT NULL,
  `customer_no` varchar(20) NOT NULL,
  `post_code_anon` varchar(30) NOT NULL,
  `city` varchar(50) NOT NULL,
  `country` varchar(20) NOT NULL,
  `category_view_duration_seconds` bigint(20) unsigned NOT NULL DEFAULT '0',
  `category_view_counter` bigint(20) unsigned NOT NULL DEFAULT '0',
  `item_preview_duration_seconds` bigint(20) unsigned NOT NULL DEFAULT '0',
  `item_preview_counter` int(11) unsigned NOT NULL DEFAULT '0',
  `item_details_view_duration_seconds` bigint(20) unsigned NOT NULL DEFAULT '0',
  `item_details_view_counter` int(11) unsigned NOT NULL DEFAULT '0',
  `item_add_to_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_add_to_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_add_to_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `item_rm_from_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_rm_from_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_rm_from_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `item_order_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_order_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_order_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `customer_category`
--

DROP TABLE IF EXISTS `customer_category`;
CREATE TABLE IF NOT EXISTS `customer_category` (
  `customer_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `view_duration_seconds` bigint(20) unsigned NOT NULL DEFAULT '0',
  `view_counter` int(11) unsigned NOT NULL DEFAULT '0',
  `item_add_to_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_add_to_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_add_to_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `item_rm_from_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_rm_from_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_rm_from_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `item_order_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_order_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_order_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `customer_item`
--

DROP TABLE IF EXISTS `customer_item`;
CREATE TABLE IF NOT EXISTS `customer_item` (
  `customer_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `preview_duration_seconds` bigint(20) unsigned NOT NULL DEFAULT '0',
  `preview_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `details_view_duration_seconds` bigint(20) unsigned NOT NULL DEFAULT '0',
  `details_view_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `add_to_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `total_add_to_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `total_add_to_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `rm_from_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `total_rm_from_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `total_rm_from_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `order_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `total_order_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `total_order_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `item`
--

DROP TABLE IF EXISTS `item`;
CREATE TABLE IF NOT EXISTS `item` (
  `id` int(11) NOT NULL,
  `item_no` varchar(20) NOT NULL,
  `description` varchar(50) NOT NULL,
  `summary` varchar(250) NOT NULL,
  `variant_type` varchar(50) NOT NULL,
  `retail_price` decimal(10,4) NOT NULL,
  `base_price` decimal(10,4) NOT NULL,
  `meta_keywords` varchar(250) DEFAULT NULL,
  `meta_description` varchar(250) DEFAULT NULL,
  `preview_duration_seconds` bigint(20) unsigned NOT NULL DEFAULT '0',
  `preview_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `details_view_duration_seconds` bigint(20) unsigned NOT NULL DEFAULT '0',
  `details_view_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `add_to_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `total_add_to_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `total_add_to_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `rm_from_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `total_rm_from_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `total_rm_from_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `order_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `total_order_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `total_order_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `attributes` longtext,
  `descriptions` longtext,
  `reviews` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tracking_events`
--

DROP TABLE IF EXISTS `tracking_events`;
CREATE TABLE IF NOT EXISTS `tracking_events` (
  `uuid` varchar(32) NOT NULL,
  `session_id` varchar(45) NOT NULL,
  `event_type` varchar(30) NOT NULL,
  `creation_timestamp` varchar(30) NOT NULL,
  `last_modified_timestamp` varchar(30) NOT NULL,
  `event_data` longtext,
  `visitor_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `last_handler_hash` varchar(32) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL,
  `customer_no` varchar(20) NOT NULL,
  `category_view_duration_seconds` bigint(20) unsigned NOT NULL DEFAULT '0',
  `category_view_counter` bigint(20) unsigned NOT NULL DEFAULT '0',
  `item_preview_duration_seconds` bigint(20) unsigned NOT NULL DEFAULT '0',
  `item_preview_counter` int(11) unsigned NOT NULL DEFAULT '0',
  `item_details_view_duration_seconds` bigint(20) unsigned NOT NULL DEFAULT '0',
  `item_details_view_counter` int(11) unsigned NOT NULL DEFAULT '0',
  `item_add_to_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_add_to_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_add_to_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `item_rm_from_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_rm_from_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_rm_from_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `item_order_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_order_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_order_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_category`
--

DROP TABLE IF EXISTS `user_category`;
CREATE TABLE IF NOT EXISTS `user_category` (
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `view_duration_seconds` bigint(20) unsigned NOT NULL DEFAULT '0',
  `view_counter` int(11) unsigned NOT NULL DEFAULT '0',
  `item_add_to_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_add_to_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_add_to_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `item_rm_from_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_rm_from_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_rm_from_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `item_order_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_order_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_order_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_item`
--

DROP TABLE IF EXISTS `user_item`;
CREATE TABLE IF NOT EXISTS `user_item` (
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `preview_duration_seconds` bigint(20) unsigned NOT NULL DEFAULT '0',
  `preview_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `details_view_duration_seconds` bigint(20) unsigned NOT NULL DEFAULT '0',
  `details_view_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `add_to_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `total_add_to_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `total_add_to_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `rm_from_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `total_rm_from_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `total_rm_from_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `order_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `total_order_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `total_order_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `visitor`
--

DROP TABLE IF EXISTS `visitor`;
CREATE TABLE IF NOT EXISTS `visitor` (
  `id` int(11) NOT NULL,
  `session_id` varchar(64) NOT NULL,
  `session_date` datetime NOT NULL,
  `last_ipv4_anon` varchar(16) NOT NULL,
  `last_ipv6_anon` varchar(128) NOT NULL,
  `category_view_duration_seconds` bigint(20) unsigned NOT NULL DEFAULT '0',
  `category_view_counter` bigint(20) unsigned NOT NULL DEFAULT '0',
  `item_preview_duration_seconds` bigint(20) unsigned NOT NULL DEFAULT '0',
  `item_preview_counter` int(11) unsigned NOT NULL DEFAULT '0',
  `item_details_view_duration_seconds` bigint(20) unsigned NOT NULL DEFAULT '0',
  `item_details_view_counter` int(11) unsigned NOT NULL DEFAULT '0',
  `item_add_to_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_add_to_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_add_to_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `item_rm_from_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_rm_from_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_rm_from_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `item_order_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_order_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_order_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `visitor_category`
--

DROP TABLE IF EXISTS `visitor_category`;
CREATE TABLE IF NOT EXISTS `visitor_category` (
  `visitor_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `view_duration_seconds` bigint(20) unsigned NOT NULL DEFAULT '0',
  `view_counter` int(11) unsigned NOT NULL,
  `item_add_to_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_add_to_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_add_to_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `item_rm_from_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_rm_from_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_rm_from_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `item_order_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_order_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `item_total_order_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `visitor_item`
--

DROP TABLE IF EXISTS `visitor_item`;
CREATE TABLE IF NOT EXISTS `visitor_item` (
  `visitor_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `preview_duration_seconds` bigint(20) unsigned NOT NULL DEFAULT '0',
  `preview_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `details_view_duration_seconds` bigint(20) unsigned NOT NULL DEFAULT '0',
  `details_view_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `add_to_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `total_add_to_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `total_add_to_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `rm_from_basket_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `total_rm_from_basket_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `total_rm_from_basket_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000',
  `order_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `total_order_qty` int(10) unsigned NOT NULL DEFAULT '0',
  `total_order_value` decimal(22,4) unsigned NOT NULL DEFAULT '0.0000'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD PRIMARY KEY (`token`);

--
-- Indizes für die Tabelle `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `code` (`code`);

--
-- Indizes für die Tabelle `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `customer_category`
--
ALTER TABLE `customer_category`
  ADD PRIMARY KEY (`customer_id`,`category_id`);

--
-- Indizes für die Tabelle `customer_item`
--
ALTER TABLE `customer_item`
  ADD PRIMARY KEY (`customer_id`,`item_id`);

--
-- Indizes für die Tabelle `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variant_type` (`variant_type`),
  ADD KEY `description` (`description`),
  ADD KEY `item_no` (`item_no`),
  ADD KEY `base_price` (`base_price`),
  ADD KEY `retail_price` (`retail_price`);

--
-- Indizes für die Tabelle `tracking_events`
--
ALTER TABLE `tracking_events`
  ADD PRIMARY KEY (`uuid`),
  ADD UNIQUE KEY `uuid_creation` (`uuid`,`creation_timestamp`),
  ADD KEY `creation_timestamp` (`creation_timestamp`),
  ADD KEY `visitor_id` (`visitor_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `session_id_index` (`session_id`),
  ADD KEY `by_type_timestamp` (`event_type`,`creation_timestamp`),
  ADD KEY `by_type_timestamp_visitor_item` (`event_type`,`creation_timestamp`,`visitor_id`,`item_id`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_no` (`customer_no`);

--
-- Indizes für die Tabelle `user_category`
--
ALTER TABLE `user_category`
  ADD PRIMARY KEY (`user_id`,`category_id`);

--
-- Indizes für die Tabelle `user_item`
--
ALTER TABLE `user_item`
  ADD PRIMARY KEY (`user_id`,`item_id`);

--
-- Indizes für die Tabelle `visitor`
--
ALTER TABLE `visitor`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `visitor_category`
--
ALTER TABLE `visitor_category`
  ADD PRIMARY KEY (`visitor_id`,`category_id`);

--
-- Indizes für die Tabelle `visitor_item`
--
ALTER TABLE `visitor_item`
  ADD PRIMARY KEY (`visitor_id`,`item_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
