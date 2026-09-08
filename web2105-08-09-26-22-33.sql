# ************************************************************
# Sequel Ace SQL dump
# Version 20104
#
# https://sequel-ace.com/
# https://github.com/Sequel-Ace/Sequel-Ace
#
# Host: localhost (MySQL 5.5.68-MariaDB)
# Datenbank: web2105
# Verarbeitungszeit: 2026-09-08 20:33:43 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE='NO_AUTO_VALUE_ON_ZERO', SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Tabellen-Dump active_item_availability_notification
# ------------------------------------------------------------

DROP TABLE IF EXISTS `active_item_availability_notification`;

CREATE TABLE `active_item_availability_notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `site_code` varchar(30) NOT NULL,
  `site_language_code` varchar(30) NOT NULL,
  `target_shop_code` varchar(30) NOT NULL,
  `item_shop_code` varchar(30) NOT NULL,
  `customer_shop_code` varchar(30) NOT NULL,
  `shop_language_code` varchar(30) NOT NULL,
  `item_no` varchar(30) NOT NULL,
  `variant_code` varchar(30) NOT NULL,
  `customer_no` varchar(30) NOT NULL,
  `user_name` varchar(128) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump amazon_categories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `amazon_categories`;

CREATE TABLE `amazon_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `browsePathById` varchar(255) NOT NULL,
  `modified_date` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump amazon_feed
# ------------------------------------------------------------

DROP TABLE IF EXISTS `amazon_feed`;

CREATE TABLE `amazon_feed` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `feedSubmissionId` varchar(255) NOT NULL,
  `modified_date` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump amazon_reports
# ------------------------------------------------------------

DROP TABLE IF EXISTS `amazon_reports`;

CREATE TABLE `amazon_reports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `options` text NOT NULL,
  `merchant` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `report_request_id` varchar(255) NOT NULL,
  `generated_report_id` varchar(255) NOT NULL,
  `modified_date` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump basket_header
# ------------------------------------------------------------

DROP TABLE IF EXISTS `basket_header`;

CREATE TABLE `basket_header` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visitor_id` int(11) NOT NULL,
  `description` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `visitor_id` (`visitor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump contactform_header
# ------------------------------------------------------------

DROP TABLE IF EXISTS `contactform_header`;

CREATE TABLE `contactform_header` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_language_id` int(10) unsigned NOT NULL,
  `description` varchar(255) NOT NULL,
  `modified_date` int(11) NOT NULL,
  `modified_user` int(11) NOT NULL,
  `sender_name` varchar(255) NOT NULL,
  `sender_email` varchar(255) NOT NULL,
  `recipient_name` varchar(255) NOT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `collection_header` tinyint(4) NOT NULL DEFAULT '0',
  `all_languages` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump contactform_line
# ------------------------------------------------------------

DROP TABLE IF EXISTS `contactform_line`;

CREATE TABLE `contactform_line` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `header_id` int(11) NOT NULL,
  `sorting` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `typ` int(11) NOT NULL,
  `mandatory` tinyint(4) NOT NULL,
  `option_string` mediumtext NOT NULL,
  `infield` tinyint(4) NOT NULL,
  `modified_date` int(11) NOT NULL,
  `modified_user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump copy_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `copy_link`;

CREATE TABLE `copy_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `old_id` int(10) unsigned NOT NULL,
  `new_id` int(10) unsigned NOT NULL,
  `source_table` varchar(100) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump facebook_header
# ------------------------------------------------------------

DROP TABLE IF EXISTS `facebook_header`;

CREATE TABLE `facebook_header` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_language_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `show_faces` tinyint(4) NOT NULL,
  `show_posts` tinyint(4) NOT NULL,
  `modified_date` int(11) NOT NULL,
  `modified_user` int(11) NOT NULL,
  `collection_header` tinyint(4) NOT NULL DEFAULT '0',
  `all_languages` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump filegallery_header
# ------------------------------------------------------------

DROP TABLE IF EXISTS `filegallery_header`;

CREATE TABLE `filegallery_header` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_language_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `modified_date` int(11) NOT NULL,
  `modified_user` int(11) NOT NULL,
  `collection_header` tinyint(4) NOT NULL DEFAULT '0',
  `all_languages` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump filegallery_line
# ------------------------------------------------------------

DROP TABLE IF EXISTS `filegallery_line`;

CREATE TABLE `filegallery_line` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `header_id` int(11) NOT NULL,
  `extension` varchar(250) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `description` varchar(250) NOT NULL,
  `sorting` int(11) NOT NULL,
  `modified_date` int(11) NOT NULL,
  `modified_user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump gallery_header
# ------------------------------------------------------------

DROP TABLE IF EXISTS `gallery_header`;

CREATE TABLE `gallery_header` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_language_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `width` int(5) NOT NULL,
  `height` int(5) NOT NULL,
  `thumb_width` int(5) NOT NULL,
  `thumb_height` int(5) NOT NULL,
  `modified_date` int(11) NOT NULL,
  `modified_user` int(11) NOT NULL,
  `collection_header` tinyint(4) NOT NULL DEFAULT '0',
  `all_languages` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump gallery_line
# ------------------------------------------------------------

DROP TABLE IF EXISTS `gallery_line`;

CREATE TABLE `gallery_line` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `header_id` int(11) NOT NULL,
  `preview` varchar(250) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `description` varchar(250) NOT NULL,
  `sorting` int(11) NOT NULL,
  `modified_date` int(11) NOT NULL,
  `modified_user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump general_job_queue
# ------------------------------------------------------------

DROP TABLE IF EXISTS `general_job_queue`;

CREATE TABLE `general_job_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queue_name` varchar(128) NOT NULL,
  `creation_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `time_to_live` int(11) NOT NULL DEFAULT '0',
  `last_action_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(20) NOT NULL,
  `failure_code` int(3) NOT NULL DEFAULT '0',
  `no_of_unsuccessful_attempts` int(3) NOT NULL DEFAULT '0',
  `max_no_of_retries` int(3) NOT NULL DEFAULT '0',
  `payload` mediumtext NOT NULL,
  `failure_data` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump google_maps_header
# ------------------------------------------------------------

DROP TABLE IF EXISTS `google_maps_header`;

CREATE TABLE `google_maps_header` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_language_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `width` int(5) NOT NULL,
  `height` int(5) NOT NULL,
  `icon_location` varchar(255) NOT NULL,
  `modified_date` int(11) NOT NULL,
  `modified_user` int(11) NOT NULL,
  `collection_header` tinyint(4) NOT NULL DEFAULT '0',
  `all_languages` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump google_maps_line
# ------------------------------------------------------------

DROP TABLE IF EXISTS `google_maps_line`;

CREATE TABLE `google_maps_line` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `header_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `address` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `sorting` int(11) NOT NULL,
  `lat` varchar(30) NOT NULL,
  `lng` varchar(30) NOT NULL,
  `modified_date` int(11) NOT NULL,
  `modified_user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump iframe_header
# ------------------------------------------------------------

DROP TABLE IF EXISTS `iframe_header`;

CREATE TABLE `iframe_header` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_language_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `modified_date` int(11) NOT NULL,
  `modified_user` int(11) NOT NULL,
  `collection_header` tinyint(4) NOT NULL DEFAULT '0',
  `all_languages` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump main_admin_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_admin_user`;

CREATE TABLE `main_admin_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `login` varchar(45) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `right_create_user` tinyint(1) DEFAULT '0',
  `edit_mode` tinyint(1) NOT NULL,
  `is_super_user` tinyint(4) DEFAULT '0',
  `main_site_id` int(10) unsigned NOT NULL,
  `main_language` char(10) NOT NULL DEFAULT 'de',
  `api_access` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 PACK_KEYS=0 ROW_FORMAT=COMPACT;



# Tabellen-Dump main_admin_user_shop_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_admin_user_shop_link`;

CREATE TABLE `main_admin_user_shop_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_code` varchar(10) NOT NULL,
  `main_admin_user_id` int(10) unsigned NOT NULL,
  `active` tinyint(1) DEFAULT '0',
  `modified_date` int(10) unsigned NOT NULL,
  `modified_user` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump main_collection
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_collection`;

CREATE TABLE `main_collection` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_language_id` int(11) NOT NULL,
  `main_collection_setup_id` int(10) unsigned NOT NULL,
  `description` varchar(255) NOT NULL,
  `registration` tinyint(4) NOT NULL DEFAULT '0',
  `registration_contactform_id` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_date` int(10) unsigned NOT NULL,
  `modified_user` int(10) unsigned NOT NULL,
  `validity_from` date DEFAULT NULL,
  `validity_to` date DEFAULT NULL,
  `sorting` int(11) NOT NULL DEFAULT '0',
  `subtitle` varchar(255) NOT NULL,
  `meta_description` mediumtext NOT NULL,
  `noindex` tinyint(1) NOT NULL DEFAULT '0',
  `nofollow` tinyint(1) NOT NULL DEFAULT '0',
  `session_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump main_collection_group_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_collection_group_link`;

CREATE TABLE `main_collection_group_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_collection_id` int(10) unsigned NOT NULL,
  `main_collection_setup_group_id` int(10) unsigned NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump main_collection_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_collection_link`;

CREATE TABLE `main_collection_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_collection_id` int(10) unsigned NOT NULL,
  `main_collection_setup_content_id` int(10) unsigned NOT NULL,
  `main_sitepart_header_id` int(10) unsigned NOT NULL,
  `main_sitepart_id` int(10) unsigned NOT NULL,
  `data` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump main_collection_setup
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_collection_setup`;

CREATE TABLE `main_collection_setup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_language_id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `linked` tinyint(4) NOT NULL DEFAULT '0',
  `icon` varchar(255) NOT NULL,
  `modified_user` int(11) NOT NULL,
  `modified_date` int(11) NOT NULL,
  `all_languages` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump main_collection_setup_content
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_collection_setup_content`;

CREATE TABLE `main_collection_setup_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_collection_setup_id` int(10) unsigned NOT NULL,
  `type_id` int(10) unsigned NOT NULL,
  `fieldname` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `fieldtype` varchar(20) NOT NULL,
  `is_teaser` tinyint(4) NOT NULL DEFAULT '0',
  `options` mediumtext NOT NULL,
  `sorting` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump main_collection_setup_group
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_collection_setup_group`;

CREATE TABLE `main_collection_setup_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_collection_setup_id` int(10) unsigned NOT NULL,
  `description` varchar(255) NOT NULL,
  `default_active` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump main_component
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_component`;

CREATE TABLE `main_component` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `code` varchar(20) NOT NULL,
  `main_language_id` int(11) NOT NULL,
  `layout_area_id` int(10) unsigned NOT NULL,
  `active` tinyint(4) NOT NULL,
  `default_active` tinyint(1) NOT NULL DEFAULT '1',
  `modified_date` int(11) NOT NULL,
  `modified_user` int(11) NOT NULL,
  `validity_from` date DEFAULT NULL,
  `validity_to` date DEFAULT NULL,
  `all_languages` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump main_component_collection_group_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_component_collection_group_link`;

CREATE TABLE `main_component_collection_group_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_component_link_id` int(10) unsigned NOT NULL,
  `main_collection_setup_group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump main_component_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_component_link`;

CREATE TABLE `main_component_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_component_id` int(11) NOT NULL,
  `main_sitepart_id` int(11) NOT NULL,
  `main_sitepart_header_id` int(11) NOT NULL,
  `main_collection_list` tinyint(4) NOT NULL DEFAULT '0',
  `main_collection_id` int(10) unsigned NOT NULL DEFAULT '0',
  `main_collection_setup_id` int(10) unsigned NOT NULL DEFAULT '0',
  `main_collection_page_list_id` int(10) unsigned NOT NULL,
  `main_collection_items` int(10) unsigned NOT NULL,
  `main_collection_view_type` tinyint(4) NOT NULL DEFAULT '0',
  `modified_user` int(11) NOT NULL,
  `modified_date` int(11) NOT NULL,
  `validity_from` date DEFAULT NULL,
  `validity_to` date DEFAULT NULL,
  `sorting` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump main_language
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_language`;

CREATE TABLE `main_language` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `main_site_id` int(11) DEFAULT NULL,
  `code` varchar(5) DEFAULT NULL,
  `locale_code` varchar(15) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `site_name` varchar(45) DEFAULT NULL,
  `site_title_name` varchar(45) DEFAULT NULL,
  `main_layout_id` int(11) DEFAULT NULL,
  `meta_description` text,
  `meta_keywords` text,
  `std_main_navigation_id` int(11) DEFAULT NULL,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `shop_language_code` varchar(10) NOT NULL,
  `logout_site_id` int(11) NOT NULL,
  `logout_language_id` int(11) NOT NULL,
  `logout_navigation_id` int(11) NOT NULL,
  `related_language_codes` text NOT NULL,
  `catalog_login` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `main_site_id` (`main_site_id`),
  KEY `code` (`code`),
  KEY `main_layout_id` (`main_layout_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 PACK_KEYS=0 ROW_FORMAT=COMPACT;



# Tabellen-Dump main_layout
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_layout`;

CREATE TABLE `main_layout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `fck_style_include` varchar(100) DEFAULT NULL,
  `fck_css_include` varchar(255) NOT NULL,
  `favicon_include` varchar(100) DEFAULT NULL,
  `frontend_include` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 PACK_KEYS=0 ROW_FORMAT=COMPACT;



# Tabellen-Dump main_layout_area
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_layout_area`;

CREATE TABLE `main_layout_area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `main_layout_id` int(11) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `parameter` varchar(250) DEFAULT NULL,
  `sorting` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 PACK_KEYS=0 ROW_FORMAT=COMPACT;



# Tabellen-Dump main_layout_class
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_layout_class`;

CREATE TABLE `main_layout_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `main_layout_id` int(11) DEFAULT NULL,
  `code` varchar(100) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `sorting` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 PACK_KEYS=0 ROW_FORMAT=COMPACT;



# Tabellen-Dump main_layout_inclusions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_layout_inclusions`;

CREATE TABLE `main_layout_inclusions` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `main_layout_id` int(4) NOT NULL,
  `type` varchar(3) NOT NULL,
  `path` varchar(250) NOT NULL,
  `default_active` tinyint(1) NOT NULL,
  `sorting` int(2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `normal_query` (`sorting`,`main_layout_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump main_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_log`;

CREATE TABLE `main_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) NOT NULL DEFAULT '',
  `identd` varchar(255) NOT NULL DEFAULT '',
  `auth` varchar(255) NOT NULL DEFAULT '',
  `day` int(8) NOT NULL DEFAULT '0',
  `month` varchar(255) NOT NULL DEFAULT '',
  `year` int(8) NOT NULL DEFAULT '0',
  `time` varchar(255) NOT NULL DEFAULT '',
  `request` mediumtext NOT NULL,
  `http_version` varchar(255) NOT NULL DEFAULT '',
  `response_code` int(8) NOT NULL DEFAULT '0',
  `size` int(11) NOT NULL DEFAULT '0',
  `referrer` mediumtext NOT NULL,
  `navigator` mediumtext NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `insertdate` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump main_mail_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_mail_log`;

CREATE TABLE `main_mail_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `send_date` datetime DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `to_adress` varchar(100) DEFAULT NULL,
  `to_name` varchar(100) DEFAULT NULL,
  `from_adress` varchar(100) DEFAULT NULL,
  `from_name` varchar(100) DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `message` text,
  `html_mail` tinyint(1) DEFAULT NULL,
  `attachment` text,
  `attachment_2` varchar(255) NOT NULL,
  `bcc_address` varchar(255) NOT NULL,
  `payment_transaction_id` varchar(100) NOT NULL,
  `delayed_send` tinyint(1) NOT NULL DEFAULT '0',
  `main_site_code` varchar(20) NOT NULL,
  `main_language_code` varchar(5) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `shop_language_code` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump main_navigation
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_navigation`;

CREATE TABLE `main_navigation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `main_site_id` int(11) unsigned DEFAULT NULL,
  `main_language_id` int(11) unsigned DEFAULT NULL,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `sorting` int(11) unsigned DEFAULT NULL,
  `level` int(11) unsigned DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `menu_name` varchar(100) DEFAULT NULL,
  `title_name` varchar(100) DEFAULT NULL,
  `meta_keywords` text NOT NULL,
  `meta_description` text NOT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `validity_from` date DEFAULT NULL,
  `validity_to` date DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_admin_user_id` int(11) DEFAULT NULL,
  `forward_navigation_id` int(11) NOT NULL,
  `forward` int(11) NOT NULL,
  `forward_type` tinyint(4) NOT NULL DEFAULT '1',
  `forward_page_id` int(11) NOT NULL,
  `forward_url` varchar(255) NOT NULL,
  `forward_shop_category` int(11) NOT NULL,
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `is_landing_page` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_main_navigation_main_site` (`main_site_id`),
  KEY `fk_main_navigation_main_language` (`main_language_id`),
  KEY `fk_main_navigation_main_navigation` (`parent_id`),
  KEY `fk_main_navigation_main_admin_user` (`modified_admin_user_id`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 PACK_KEYS=0 ROW_FORMAT=COMPACT;



# Tabellen-Dump main_page
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_page`;

CREATE TABLE `main_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) NOT NULL,
  `meta_keywords` mediumtext NOT NULL,
  `meta_description` mediumtext NOT NULL,
  `main_language_id` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `modified_date` int(11) NOT NULL,
  `modified_user` int(11) NOT NULL,
  `validity_from` date DEFAULT NULL,
  `validity_to` date DEFAULT NULL,
  `noindex` tinyint(1) NOT NULL DEFAULT '0',
  `nofollow` tinyint(1) NOT NULL DEFAULT '0',
  `is_shopping_world` tinyint(1) NOT NULL DEFAULT '0',
  `is_template` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump main_page_collection_group_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_page_collection_group_link`;

CREATE TABLE `main_page_collection_group_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_page_link_id` int(10) unsigned NOT NULL,
  `main_collection_setup_group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump main_page_component_include_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_page_component_include_link`;

CREATE TABLE `main_page_component_include_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_page_id` int(10) unsigned NOT NULL,
  `main_component_id` int(10) unsigned NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump main_page_layout_inclusion_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_page_layout_inclusion_link`;

CREATE TABLE `main_page_layout_inclusion_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_layout_includes_id` int(11) NOT NULL,
  `main_page_id` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump main_page_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_page_link`;

CREATE TABLE `main_page_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_page_id` int(11) NOT NULL,
  `main_sitepart_id` int(11) NOT NULL,
  `main_sitepart_header_id` int(11) NOT NULL,
  `main_collection_list` tinyint(4) NOT NULL DEFAULT '0',
  `main_collection_id` int(10) unsigned NOT NULL DEFAULT '0',
  `main_collection_setup_id` int(10) unsigned NOT NULL DEFAULT '0',
  `main_collection_page_list_id` int(10) unsigned NOT NULL DEFAULT '0',
  `main_collection_items` int(10) unsigned NOT NULL DEFAULT '0',
  `main_collection_view_type` tinyint(4) NOT NULL DEFAULT '0',
  `main_page_group` tinyint(4) NOT NULL DEFAULT '0',
  `main_page_group_code` varchar(200) NOT NULL,
  `main_page_group_name` varchar(255) NOT NULL,
  `main_page_group_link` varchar(255) NOT NULL,
  `main_page_link_parent_id` int(10) unsigned NOT NULL,
  `modified_user` int(11) NOT NULL,
  `modified_date` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `validity_from` date DEFAULT NULL,
  `validity_to` date DEFAULT NULL,
  `layout_area_id` int(11) NOT NULL,
  `layout_class_id` int(10) unsigned NOT NULL DEFAULT '0',
  `background_image_path` varchar(255) NOT NULL,
  `sorting` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump main_page_link_layout_class_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_page_link_layout_class_link`;

CREATE TABLE `main_page_link_layout_class_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_page_link_id` int(10) unsigned NOT NULL,
  `main_layout_class_id` int(10) unsigned NOT NULL,
  `modified_user` int(10) unsigned NOT NULL,
  `modified_date` int(10) unsigned NOT NULL,
  `sorting` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump main_rewrite_rules
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_rewrite_rules`;

CREATE TABLE `main_rewrite_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `old_url` varchar(255) NOT NULL,
  `new_url` varchar(255) NOT NULL,
  `rewrite_code` int(11) NOT NULL DEFAULT '301',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump main_shop_dealer_search
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_shop_dealer_search`;

CREATE TABLE `main_shop_dealer_search` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `main_language_id` int(11) NOT NULL,
  `description` varchar(120) NOT NULL,
  `google_api_key` varchar(120) NOT NULL,
  `modified_date` datetime NOT NULL,
  `modified_user` int(11) NOT NULL,
  `collection_header` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `main_language_id` (`main_language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump main_shop_item_preview
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_shop_item_preview`;

CREATE TABLE `main_shop_item_preview` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `main_language_id` int(11) NOT NULL,
  `description` varchar(120) NOT NULL,
  `no_of_items` int(4) NOT NULL,
  `category_code_string` varchar(255) NOT NULL,
  `item_no_string` mediumtext NOT NULL,
  `modified_date` datetime NOT NULL,
  `modified_user` int(11) NOT NULL,
  `collection_header` int(11) NOT NULL DEFAULT '0',
  `include_sub_categories` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `main_language_id` (`main_language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump main_shop_language_switch
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_shop_language_switch`;

CREATE TABLE `main_shop_language_switch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `main_language_id` int(11) NOT NULL,
  `description` varchar(120) NOT NULL,
  `modified_date` datetime NOT NULL,
  `modified_user` int(11) NOT NULL,
  `collection_header` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `main_language_id` (`main_language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump main_shop_login
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_shop_login`;

CREATE TABLE `main_shop_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `main_language_id` int(11) NOT NULL,
  `description` varchar(120) NOT NULL,
  `target_site_code` varchar(30) NOT NULL,
  `target_language_code` varchar(30) NOT NULL,
  `target_url` varchar(255) NOT NULL,
  `show_labels_in_fields` tinyint(1) NOT NULL DEFAULT '0',
  `modified_date` datetime NOT NULL,
  `modified_user` int(11) NOT NULL,
  `collection_header` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `main_language_id` (`main_language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump main_shop_sitepart
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_shop_sitepart`;

CREATE TABLE `main_shop_sitepart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `main_language_id` int(11) NOT NULL,
  `description` varchar(120) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `modified_date` datetime NOT NULL,
  `modified_user` int(11) NOT NULL,
  `collection_header` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `main_language_id` (`main_language_id`),
  KEY `main_language_id_2` (`main_language_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump main_shop_top_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_shop_top_items`;

CREATE TABLE `main_shop_top_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `main_language_id` int(11) NOT NULL,
  `description` varchar(120) NOT NULL,
  `no_of_items` int(4) NOT NULL,
  `modified_date` datetime NOT NULL,
  `modified_user` int(11) NOT NULL,
  `collection_header` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `main_language_id` (`main_language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump main_site
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_site`;

CREATE TABLE `main_site` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `std_main_language_id` int(11) unsigned DEFAULT NULL,
  `login_required` tinyint(1) NOT NULL DEFAULT '0',
  `login_type` tinyint(1) NOT NULL,
  `google_analytics_id` varchar(45) NOT NULL,
  `use_session_id` tinyint(1) NOT NULL DEFAULT '0',
  `create_xml_sitemap` tinyint(1) NOT NULL DEFAULT '0',
  `site_url` varchar(80) NOT NULL,
  `is_standard_site` tinyint(1) NOT NULL DEFAULT '0',
  `use_ssl` tinyint(1) NOT NULL,
  `use_browser_language_detection` tinyint(1) NOT NULL DEFAULT '0',
  `use_ip_detection` tinyint(1) NOT NULL DEFAULT '0',
  `default_country_codes` mediumtext NOT NULL,
  `facebook_pixel_id` varchar(45) NOT NULL,
  `google_tag_container_id` varchar(45) NOT NULL,
  `is_unique_site` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_main_site_main_language` (`std_main_language_id`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 PACK_KEYS=0 ROW_FORMAT=COMPACT;



# Tabellen-Dump main_site_admin_user_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_site_admin_user_link`;

CREATE TABLE `main_site_admin_user_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_site_id` int(10) unsigned NOT NULL,
  `main_admin_user_id` int(10) unsigned NOT NULL,
  `modified_date` int(10) unsigned NOT NULL,
  `modified_user` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump main_sitepart_changes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_sitepart_changes`;

CREATE TABLE `main_sitepart_changes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_language_id` int(11) NOT NULL,
  `main_sitepart_id` int(11) NOT NULL,
  `main_sitepart_header_id` int(11) NOT NULL,
  `modified_user` int(11) NOT NULL,
  `modified_date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;





# Tabellen-Dump main_visitor
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_visitor`;

CREATE TABLE `main_visitor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(45) DEFAULT NULL,
  `session_date` datetime DEFAULT NULL,
  `valid_until` datetime DEFAULT NULL,
  `frontend_login` tinyint(1) DEFAULT NULL,
  `cookie_only` tinyint(1) NOT NULL DEFAULT '0',
  `main_user_id` int(11) DEFAULT NULL,
  `shop_salesperson_id` int(11) NOT NULL DEFAULT '0',
  `admin_login` tinyint(1) DEFAULT NULL COMMENT '	',
  `main_admin_user_id` int(11) DEFAULT NULL,
  `currency_code` varchar(10) NOT NULL DEFAULT '',
  `data` text NOT NULL,
  `remember_token` varchar(100) NOT NULL DEFAULT '',
  `nav_login` tinyint(1) NOT NULL DEFAULT '0',
  `serialized_objects` mediumblob NOT NULL,
  `last_ipv4_anon` varchar(16) NOT NULL DEFAULT '',
  `last_ipv6_anon` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `index1` (`session_id`),
  KEY `session_date` (`session_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;



# Tabellen-Dump newsletter_sitepart
# ------------------------------------------------------------

DROP TABLE IF EXISTS `newsletter_sitepart`;

CREATE TABLE `newsletter_sitepart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `main_language_id` int(11) NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `group_id` int(11) NOT NULL,
  `account` varchar(45) NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(45) NOT NULL,
  `description` varchar(80) NOT NULL,
  `text` mediumtext NOT NULL,
  `type` tinyint(1) NOT NULL,
  `segment` varchar(30) NOT NULL,
  `salutation_active` tinyint(1) NOT NULL,
  `name_status` tinyint(1) NOT NULL,
  `city_active` tinyint(1) NOT NULL,
  `post_code_active` tinyint(1) NOT NULL,
  `birthday_active` tinyint(1) NOT NULL,
  `label_position` tinyint(1) NOT NULL,
  `modified_date` datetime NOT NULL,
  `modified_user` int(11) NOT NULL,
  `collection_header` int(11) NOT NULL DEFAULT '0',
  `forwarding_url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `main_language_id` (`main_language_id`,`modified_date`,`modified_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump oauth_access_tokens
# ------------------------------------------------------------

DROP TABLE IF EXISTS `oauth_access_tokens`;

CREATE TABLE `oauth_access_tokens` (
  `access_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(80) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(4000) DEFAULT NULL,
  PRIMARY KEY (`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump oauth_authorization_codes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `oauth_authorization_codes`;

CREATE TABLE `oauth_authorization_codes` (
  `authorization_code` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(80) DEFAULT NULL,
  `redirect_uri` varchar(2000) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(4000) DEFAULT NULL,
  `id_token` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`authorization_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump oauth_clients
# ------------------------------------------------------------

DROP TABLE IF EXISTS `oauth_clients`;

CREATE TABLE `oauth_clients` (
  `client_id` varchar(80) NOT NULL,
  `client_secret` varchar(80) DEFAULT NULL,
  `redirect_uri` varchar(2000) DEFAULT NULL,
  `grant_types` varchar(80) DEFAULT NULL,
  `scope` varchar(4000) DEFAULT NULL,
  `user_id` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump oauth_jwt
# ------------------------------------------------------------

DROP TABLE IF EXISTS `oauth_jwt`;

CREATE TABLE `oauth_jwt` (
  `client_id` varchar(80) NOT NULL,
  `subject` varchar(80) DEFAULT NULL,
  `public_key` varchar(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump oauth_refresh_tokens
# ------------------------------------------------------------

DROP TABLE IF EXISTS `oauth_refresh_tokens`;

CREATE TABLE `oauth_refresh_tokens` (
  `refresh_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(80) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(4000) DEFAULT NULL,
  PRIMARY KEY (`refresh_token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump oauth_scopes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `oauth_scopes`;

CREATE TABLE `oauth_scopes` (
  `scope` varchar(80) NOT NULL,
  `is_default` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`scope`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump oauth_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `oauth_users`;

CREATE TABLE `oauth_users` (
  `username` varchar(80) NOT NULL DEFAULT '',
  `password` varchar(80) DEFAULT NULL,
  `first_name` varchar(80) DEFAULT NULL,
  `last_name` varchar(80) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT NULL,
  `scope` varchar(4000) DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump scheduled_jobs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `scheduled_jobs`;

CREATE TABLE `scheduled_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cron_spec` varchar(128) NOT NULL,
  `registered_with_pid` varchar(10) NOT NULL DEFAULT '',
  `queue_name` varchar(128) NOT NULL,
  `time_to_live` int(11) NOT NULL DEFAULT '0',
  `max_no_of_retries` int(3) NOT NULL DEFAULT '0',
  `payload` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`registered_with_pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump scrollbar_header
# ------------------------------------------------------------

DROP TABLE IF EXISTS `scrollbar_header`;

CREATE TABLE `scrollbar_header` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_language_id` int(10) unsigned NOT NULL,
  `description` varchar(255) NOT NULL,
  `item_width` int(4) NOT NULL,
  `item_height` int(4) NOT NULL,
  `width` int(4) NOT NULL,
  `height` int(4) NOT NULL,
  `arrows` varchar(255) NOT NULL,
  `eff_interval` int(5) NOT NULL,
  `effect_duration` int(5) NOT NULL,
  `items` int(5) NOT NULL,
  `step` int(5) NOT NULL,
  `modified_user` int(11) NOT NULL,
  `modified_date` int(11) NOT NULL,
  `collection_header` tinyint(4) NOT NULL DEFAULT '0',
  `all_languages` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump scrollbar_line
# ------------------------------------------------------------

DROP TABLE IF EXISTS `scrollbar_line`;

CREATE TABLE `scrollbar_line` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `header_id` int(11) NOT NULL,
  `preview` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `text` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `sorting` int(11) NOT NULL,
  `modified_date` int(11) NOT NULL,
  `modified_user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shipping_classes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shipping_classes`;

CREATE TABLE `shipping_classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL DEFAULT '',
  `code` varchar(20) NOT NULL DEFAULT '',
  `description` varchar(50) NOT NULL DEFAULT '',
  `priority` int(11) NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nav_primary` (`company`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump shipping_option_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shipping_option_link`;

CREATE TABLE `shipping_option_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL DEFAULT '',
  `shop_code` varchar(10) NOT NULL DEFAULT '',
  `shipping_group_code` varchar(20) NOT NULL DEFAULT '',
  `line_no` int(11) NOT NULL DEFAULT '0',
  `coupon_shipping_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `campaign_code` varchar(20) NOT NULL DEFAULT '',
  `send_order_mail_text` varchar(30) NOT NULL DEFAULT '',
  `coupon_code` varchar(20) NOT NULL DEFAULT '',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nav_primary` (`company`,`shop_code`,`shipping_group_code`,`line_no`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump shipping_option_translation
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shipping_option_translation`;

CREATE TABLE `shipping_option_translation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL DEFAULT '',
  `shipping_group_code` varchar(20) NOT NULL DEFAULT '',
  `line_no` int(11) NOT NULL DEFAULT '0',
  `language_code` varchar(10) NOT NULL DEFAULT '',
  `description` varchar(50) NOT NULL DEFAULT '',
  `logo` varchar(100) NOT NULL DEFAULT '',
  `content` varchar(250) NOT NULL DEFAULT '',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nav_primary` (`company`,`shipping_group_code`,`line_no`,`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump shipping_zone_line
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shipping_zone_line`;

CREATE TABLE `shipping_zone_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL DEFAULT '',
  `shipping_zone_code` varchar(20) NOT NULL DEFAULT '',
  `post_code_code` varchar(20) NOT NULL DEFAULT '',
  `country_region_code` varchar(10) NOT NULL DEFAULT '',
  `type` int(11) NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nav_primary` (`company`,`shipping_zone_code`,`post_code_code`,`country_region_code`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump shipping_zones
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shipping_zones`;

CREATE TABLE `shipping_zones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL DEFAULT '',
  `code` varchar(30) NOT NULL DEFAULT '',
  `description` varchar(50) NOT NULL DEFAULT '',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nav_primary` (`company`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump shop_attribute
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_attribute`;

CREATE TABLE `shop_attribute` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `code` varchar(30) NOT NULL,
  `description` varchar(50) NOT NULL,
  `display_type` int(11) NOT NULL,
  `multiple_choices` tinyint(1) NOT NULL,
  `data_type` tinyint(1) NOT NULL,
  `navision_value` varchar(30) NOT NULL,
  `filter_all` tinyint(1) NOT NULL,
  `filter_category` tinyint(1) NOT NULL,
  `show_on_card` tinyint(1) NOT NULL,
  `use_for_comparison` tinyint(1) NOT NULL,
  `auto_link` tinyint(1) NOT NULL DEFAULT '0',
  `show_in_header` tinyint(1) NOT NULL DEFAULT '0',
  `attribute_type` tinyint(1) NOT NULL DEFAULT '0',
  `marketplace_type` tinyint(4) NOT NULL DEFAULT '0',
  `map_to_attribute` varchar(30) DEFAULT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `code` (`code`),
  KEY `display_type` (`display_type`),
  KEY `data_type` (`data_type`),
  KEY `navision_value` (`navision_value`),
  KEY `filter_all` (`filter_all`),
  KEY `filter_category` (`filter_category`),
  KEY `show_on_card` (`show_on_card`),
  KEY `use_for_comparison` (`use_for_comparison`),
  KEY `auto_link` (`auto_link`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_attribute_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_attribute_link`;

CREATE TABLE `shop_attribute_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(20) NOT NULL,
  `language_code` varchar(20) NOT NULL,
  `no` varchar(30) NOT NULL,
  `attribute_code` varchar(30) NOT NULL,
  `line_no` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `value_decimal` decimal(10,2) NOT NULL,
  `value_integer` int(11) NOT NULL,
  `value_option` varchar(30) NOT NULL,
  `value_bool` tinyint(1) NOT NULL,
  `value_text` varchar(100) NOT NULL,
  `sorting` int(11) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `prim2` (`no`,`attribute_code`,`type`,`line_no`,`value_option`,`language_code`,`shop_code`,`company`),
  KEY `no` (`no`),
  KEY `attribute_code` (`attribute_code`),
  KEY `value_option` (`value_option`),
  KEY `shop_code` (`shop_code`,`language_code`,`line_no`),
  KEY `company_2` (`company`,`no`,`attribute_code`,`type`),
  KEY `company_3` (`company`,`shop_code`,`attribute_code`,`type`),
  KEY `no_2` (`no`,`attribute_code`),
  KEY `cat` (`type`,`line_no`,`language_code`,`shop_code`,`company`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_attribute_option
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_attribute_option`;

CREATE TABLE `shop_attribute_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `code` varchar(30) NOT NULL,
  `attribute_code` varchar(30) NOT NULL,
  `description` varchar(50) NOT NULL,
  `filter` varchar(100) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `link` varchar(250) NOT NULL,
  `sorting` int(11) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `code` (`code`),
  KEY `attribute_code` (`attribute_code`),
  KEY `code_2` (`code`,`attribute_code`),
  KEY `sorting` (`sorting`),
  KEY `company` (`attribute_code`,`code`,`company`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_attribute_translation
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_attribute_translation`;

CREATE TABLE `shop_attribute_translation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `attribute_code` varchar(30) NOT NULL,
  `attribute_link_no` varchar(30) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `description` varchar(100) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_campaign_element
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_campaign_element`;

CREATE TABLE `shop_campaign_element` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) CHARACTER SET latin1 NOT NULL,
  `link_to_header_code` varchar(20) NOT NULL,
  `link_type` int(3) NOT NULL,
  `type` int(3) NOT NULL,
  `include_exclude` int(3) NOT NULL,
  `item_no` varchar(30) DEFAULT NULL,
  `item_var_code` varchar(10) DEFAULT NULL,
  `category_line_no` int(11) DEFAULT NULL,
  `to_delete` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nav_primary` (`link_to_header_code`,`link_type`,`type`,`company`,`item_no`,`item_var_code`,`category_line_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_campaign_header
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_campaign_header`;

CREATE TABLE `shop_campaign_header` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(20) DEFAULT NULL,
  `language_code` varchar(20) DEFAULT NULL,
  `all_shops` tinyint(1) NOT NULL,
  `all_languages` tinyint(1) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` varchar(80) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `active_from_date` date DEFAULT NULL,
  `active_to_date` date DEFAULT NULL,
  `active_from_time` time DEFAULT NULL,
  `active_to_time` time DEFAULT NULL,
  `multiply_applicable` tinyint(1) NOT NULL DEFAULT '0',
  `priority` int(2) NOT NULL,
  `cond_sum_amnt_all_items_gte` decimal(10,2) DEFAULT NULL,
  `cond_sum_amnt_sing_item_gte` decimal(10,2) DEFAULT NULL,
  `cond_no_diff_items_gte` int(3) DEFAULT NULL,
  `cond_total_item_qty_gte` decimal(10,2) DEFAULT NULL,
  `cond_qty_single_item_gte` decimal(10,2) DEFAULT NULL,
  `cond_basket_total_gte` decimal(10,2) DEFAULT NULL,
  `discount_amnt` decimal(10,2) DEFAULT NULL,
  `discount_amnt_applies_to` int(3) DEFAULT NULL,
  `discount_percent` decimal(10,2) DEFAULT NULL,
  `discount_percent_applies_to` int(3) DEFAULT NULL,
  `discount_applies_to_qty_max` decimal(10,4) DEFAULT NULL,
  `qty_free_item` decimal(10,2) DEFAULT NULL,
  `type_free_item` int(3) DEFAULT NULL,
  `alternative_shipping_option` tinyint(1) NOT NULL,
  `icon_condition_items` varchar(80) NOT NULL,
  `banner_condition_items` varchar(80) NOT NULL,
  `text_condition_items` varchar(30) CHARACTER SET latin1 NOT NULL,
  `icon_action_items` varchar(80) NOT NULL,
  `banner_action_items` varchar(80) NOT NULL,
  `text_action_items` varchar(30) NOT NULL,
  `banner_basket` varchar(80) NOT NULL,
  `text_basket` varchar(30) NOT NULL,
  `to_delete` tinyint(1) NOT NULL,
  `cond_sum_amnt_each_item_gte` decimal(10,4) NOT NULL,
  `cond_qty_each_item_gte` decimal(10,4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nav_primary` (`code`,`company`),
  KEY `priority` (`company`,`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_category`;

CREATE TABLE `shop_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `line_no` int(11) NOT NULL,
  `parent_line_no` int(11) NOT NULL,
  `sorting` int(3) NOT NULL,
  `level` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `root_line_no` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `show_random_items` tinyint(1) NOT NULL DEFAULT '0',
  `no_of_random_items` int(3) NOT NULL,
  `show_campain_items` tinyint(1) NOT NULL DEFAULT '0',
  `no_of_campain_items` int(3) NOT NULL,
  `show_all_items` tinyint(1) NOT NULL DEFAULT '0',
  `user_sorting` tinyint(1) NOT NULL DEFAULT '0',
  `sort_items` tinyint(1) NOT NULL DEFAULT '0',
  `show_sub_categorys` tinyint(1) NOT NULL DEFAULT '0',
  `category_picture` varchar(100) NOT NULL,
  `category_icon` varchar(100) NOT NULL,
  `category_description` text NOT NULL,
  `category_description_2` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `validity_from` date DEFAULT NULL,
  `validity_to` date DEFAULT NULL,
  `promotion_active` tinyint(1) NOT NULL DEFAULT '0',
  `promotion_validity_from` date DEFAULT NULL,
  `promotion_validity_to` date DEFAULT NULL,
  `promotion_label` tinyint(1) NOT NULL DEFAULT '0',
  `promotion_description` text NOT NULL,
  `search_query` varchar(250) NOT NULL,
  `meta_keywords` varchar(250) NOT NULL,
  `meta_description` varchar(250) NOT NULL,
  `site_titel` varchar(50) NOT NULL,
  `filter_active` tinyint(1) NOT NULL,
  `max_no_of_filter` int(11) NOT NULL,
  `url` varchar(1024) NOT NULL,
  `marketplace_category_id` varchar(40) NOT NULL,
  `root_code` varchar(20) NOT NULL,
  `parent_code` varchar(20) NOT NULL,
  `category_description_excerpt` text NOT NULL,
  `g_product_category` varchar(250) NOT NULL,
  `in_use_with_page_id` int(10) NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL,
  `hide_category_sub_navigation` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `covering_1` (`line_no`,`parent_line_no`,`level`,`root_line_no`,`active`,`language_code`,`validity_from`,`validity_to`,`shop_code`,`company`,`code`,`sorting`),
  UNIQUE KEY `helper_1` (`line_no`,`language_code`,`active`,`validity_from`,`validity_to`,`shop_code`,`company`),
  KEY `validity_from` (`validity_from`,`validity_to`),
  KEY `promotion_validity_from` (`promotion_validity_from`,`promotion_validity_to`),
  KEY `promotion_active` (`promotion_active`),
  KEY `active` (`active`),
  KEY `to_delete` (`to_delete`),
  KEY `company` (`company`),
  KEY `parent_line_no` (`parent_line_no`),
  KEY `root_line_no` (`root_line_no`),
  KEY `level` (`level`),
  KEY `code` (`code`),
  KEY `nav_primary` (`line_no`,`language_code`,`shop_code`,`company`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_category_old
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_category_old`;

CREATE TABLE `shop_category_old` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `line_no` int(11) NOT NULL,
  `parent_line_no` int(11) NOT NULL,
  `sorting` int(3) NOT NULL,
  `level` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `root_line_no` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `show_random_items` tinyint(1) NOT NULL DEFAULT '0',
  `no_of_random_items` int(3) NOT NULL,
  `show_campain_items` tinyint(1) NOT NULL DEFAULT '0',
  `no_of_campain_items` int(3) NOT NULL,
  `show_all_items` tinyint(1) NOT NULL DEFAULT '0',
  `user_sorting` tinyint(1) NOT NULL DEFAULT '0',
  `sort_items` tinyint(1) NOT NULL DEFAULT '0',
  `show_sub_categorys` tinyint(1) NOT NULL DEFAULT '0',
  `category_picture` varchar(100) NOT NULL,
  `category_icon` varchar(100) NOT NULL,
  `category_description` text NOT NULL,
  `category_description_2` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `validity_from` date DEFAULT NULL,
  `validity_to` date DEFAULT NULL,
  `promotion_active` tinyint(1) NOT NULL DEFAULT '0',
  `promotion_validity_from` date DEFAULT NULL,
  `promotion_validity_to` date DEFAULT NULL,
  `promotion_label` tinyint(1) NOT NULL DEFAULT '0',
  `promotion_description` text NOT NULL,
  `search_query` varchar(250) NOT NULL,
  `meta_keywords` varchar(250) NOT NULL,
  `meta_description` varchar(250) NOT NULL,
  `site_titel` varchar(50) NOT NULL,
  `filter_active` tinyint(1) NOT NULL,
  `max_no_of_filter` int(11) NOT NULL,
  `url` varchar(1024) NOT NULL,
  `marketplace_category_id` varchar(40) NOT NULL,
  `root_code` varchar(20) NOT NULL,
  `parent_code` varchar(20) NOT NULL,
  `category_description_excerpt` text NOT NULL,
  `g_product_category` varchar(250) NOT NULL,
  `in_use_with_page_id` int(10) NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL,
  `hide_category_sub_navigation` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `covering_1` (`line_no`,`parent_line_no`,`level`,`root_line_no`,`active`,`language_code`,`validity_from`,`validity_to`,`shop_code`,`company`,`code`,`sorting`),
  UNIQUE KEY `helper_1` (`line_no`,`language_code`,`active`,`validity_from`,`validity_to`,`shop_code`,`company`),
  KEY `validity_from` (`validity_from`,`validity_to`),
  KEY `promotion_validity_from` (`promotion_validity_from`,`promotion_validity_to`),
  KEY `promotion_active` (`promotion_active`),
  KEY `active` (`active`),
  KEY `to_delete` (`to_delete`),
  KEY `company` (`company`),
  KEY `parent_line_no` (`parent_line_no`),
  KEY `root_line_no` (`root_line_no`),
  KEY `level` (`level`),
  KEY `code` (`code`),
  KEY `nav_primary` (`line_no`,`language_code`,`shop_code`,`company`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_country
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_country`;

CREATE TABLE `shop_country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `country_code` varchar(10) NOT NULL,
  `description` varchar(50) NOT NULL,
  `invoice_to` tinyint(1) NOT NULL DEFAULT '0',
  `ship_to` tinyint(1) NOT NULL DEFAULT '0',
  `reverse_charge` tinyint(1) NOT NULL DEFAULT '0',
  `customer_template_code` varchar(10) NOT NULL DEFAULT '',
  `customer_reminder_terms_code` varchar(10) NOT NULL DEFAULT '',
  `customer_location_code` varchar(10) NOT NULL DEFAULT '',
  `customer_salesperson_code` varchar(10) NOT NULL DEFAULT '',
  `pool_customer_no` varchar(20) NOT NULL DEFAULT '',
  `reverse_charge_customer_template_code` varchar(10) NOT NULL DEFAULT '',
  `reverse_charge_pool_customer_no` varchar(20) NOT NULL DEFAULT '',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `to_delete` (`to_delete`),
  KEY `company` (`company`),
  KEY `nav_primary` (`company`,`shop_code`,`language_code`,`country_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;



# Tabellen-Dump shop_coupon_group_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_coupon_group_link`;

CREATE TABLE `shop_coupon_group_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL DEFAULT '',
  `coupon_group` varchar(20) NOT NULL DEFAULT '',
  `shop_code` varchar(10) NOT NULL DEFAULT '',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `NAV_PRIMARY` (`company`,`coupon_group`,`shop_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump shop_coupon_header
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_coupon_header`;

CREATE TABLE `shop_coupon_header` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` varchar(50) NOT NULL,
  `coupon_type` tinyint(1) NOT NULL,
  `value_type` tinyint(1) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `item_no` varchar(20) NOT NULL,
  `variant_code` varchar(10) NOT NULL,
  `valid_from` date NOT NULL,
  `valid_to` date NOT NULL,
  `amount_from` decimal(10,2) NOT NULL DEFAULT '0.00',
  `value_coupon` tinyint(1) NOT NULL DEFAULT '0',
  `coupon_group` varchar(20) NOT NULL DEFAULT '',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `coupon_type` (`coupon_type`),
  KEY `value_type` (`value_type`),
  KEY `code` (`code`),
  KEY `item_no` (`item_no`),
  KEY `valid_from` (`valid_from`,`valid_to`),
  KEY `amount_from` (`amount_from`),
  KEY `value_coupon` (`value_coupon`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_coupon_line
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_coupon_line`;

CREATE TABLE `shop_coupon_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `code` varchar(20) NOT NULL,
  `coupon_code` varchar(20) NOT NULL,
  `customer_no` varchar(20) NOT NULL,
  `times_used` int(11) NOT NULL,
  `max_no_of_usage` int(11) NOT NULL DEFAULT '0',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount_left` decimal(10,2) NOT NULL,
  `last_date_used` date NOT NULL,
  `value_type` tinyint(1) NOT NULL,
  `percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `item_no` varchar(20) NOT NULL,
  `variant_code` varchar(10) NOT NULL,
  `valid_from` date NOT NULL,
  `valid_to` date NOT NULL,
  `amount_from` decimal(10,2) NOT NULL DEFAULT '0.00',
  `value_coupon` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `coupon_group` varchar(20) NOT NULL DEFAULT '',
  `update_insert` tinyint(1) NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `code` (`code`),
  KEY `main_site_code` (`company`),
  KEY `coupon_code` (`coupon_code`),
  KEY `customer_no` (`customer_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_coupon_line_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_coupon_line_link`;

CREATE TABLE `shop_coupon_line_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL DEFAULT '',
  `code` varchar(20) NOT NULL DEFAULT '',
  `coupon_code` varchar(20) NOT NULL DEFAULT '',
  `shop_code` varchar(10) NOT NULL DEFAULT '',
  `language_code` varchar(10) NOT NULL DEFAULT '',
  `update_insert` tinyint(1) NOT NULL DEFAULT '0',
  `category_coupon` tinyint(1) NOT NULL DEFAULT '0',
  `category_line_no` int(11) NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `NAV_PRIMARY` (`company`,`code`,`coupon_code`,`shop_code`,`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump shop_coupon_shipping_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_coupon_shipping_link`;

CREATE TABLE `shop_coupon_shipping_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL DEFAULT '',
  `code` varchar(20) NOT NULL DEFAULT '',
  `coupon_code` varchar(20) NOT NULL DEFAULT '',
  `shipping_coupon_type` tinyint(1) NOT NULL DEFAULT '0',
  `shipping_coupon_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `NAV_PRIMARY` (`company`,`code`,`coupon_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump shop_cr_memo_header
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_cr_memo_header`;

CREATE TABLE `shop_cr_memo_header` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `no` varchar(20) NOT NULL,
  `posting_date` date DEFAULT NULL,
  `webshop_order_no` int(11) NOT NULL,
  `your_reference` varchar(50) NOT NULL,
  `sell_to_customer_no` varchar(50) NOT NULL,
  `bill_to_customer_no` varchar(20) NOT NULL,
  `sell_to_name` varchar(50) NOT NULL,
  `sell_to_name_2` varchar(50) NOT NULL,
  `sell_to_address` varchar(50) NOT NULL,
  `sell_to_address_2` varchar(50) NOT NULL,
  `sell_to_post_code` varchar(20) NOT NULL,
  `sell_to_city` varchar(50) NOT NULL,
  `sell_to_country` varchar(50) NOT NULL,
  `sell_to_contact` varchar(50) NOT NULL,
  `bill_to_name` varchar(50) NOT NULL,
  `bill_to_name_2` varchar(50) NOT NULL,
  `bill_to_address` varchar(50) NOT NULL,
  `bill_to_address_2` varchar(50) NOT NULL,
  `bill_to_post_code` varchar(20) NOT NULL,
  `bill_to_city` varchar(50) NOT NULL,
  `bill_to_country` varchar(50) NOT NULL,
  `bill_to_contact` varchar(50) NOT NULL,
  `ship_to_name` varchar(50) NOT NULL,
  `ship_to_name_2` varchar(50) NOT NULL,
  `ship_to_address` varchar(50) NOT NULL,
  `ship_to_address_2` varchar(50) NOT NULL,
  `ship_to_post_code` varchar(20) NOT NULL,
  `ship_to_city` varchar(50) NOT NULL,
  `ship_to_country` varchar(50) NOT NULL,
  `ship_to_contact` varchar(50) NOT NULL,
  `amount` decimal(10,4) NOT NULL,
  `amount_including_vat` decimal(10,4) NOT NULL,
  `shipment_method` varchar(80) NOT NULL,
  `payment_terms` varchar(80) NOT NULL,
  `request_mail` varchar(80) NOT NULL,
  `request_shop_code` varchar(10) NOT NULL,
  `request_language_code` varchar(10) NOT NULL,
  `send_request` tinyint(1) NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nav_primary` (`company`,`no`),
  KEY `posting_date` (`posting_date`),
  KEY `webshop_order_no` (`webshop_order_no`),
  KEY `sell_to_customer_no` (`sell_to_customer_no`),
  KEY `bill_to_customer_no` (`bill_to_customer_no`),
  KEY `send_request` (`send_request`),
  KEY `to_delete` (`to_delete`),
  KEY `amount` (`amount`),
  KEY `company` (`company`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_cr_memo_line
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_cr_memo_line`;

CREATE TABLE `shop_cr_memo_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `document_no` varchar(20) NOT NULL,
  `line_no` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `no` varchar(20) NOT NULL,
  `description` varchar(50) NOT NULL,
  `description_2` varchar(50) NOT NULL,
  `unit_of_measure` varchar(10) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_price` decimal(10,4) NOT NULL,
  `line_amount` decimal(10,4) NOT NULL,
  `variant_code` varchar(10) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nav_primary` (`company`,`document_no`,`line_no`),
  KEY `type` (`type`),
  KEY `no` (`no`),
  KEY `quantity` (`quantity`),
  KEY `line_amount` (`line_amount`),
  KEY `to_delete` (`to_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_currency
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_currency`;

CREATE TABLE `shop_currency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `code` varchar(10) NOT NULL,
  `description` varchar(30) NOT NULL,
  `amount_decimal_places` varchar(5) NOT NULL,
  `amount_rounding_precision` decimal(10,4) NOT NULL,
  `unit_amount_decimal_places` varchar(5) NOT NULL,
  `unit_amount_rounding_precision` decimal(10,4) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `to_delete` (`to_delete`),
  KEY `nav_primary` (`company`,`shop_code`,`language_code`,`code`),
  KEY `company` (`company`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_customer
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_customer`;

CREATE TABLE `shop_customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `customer_no` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `name_2` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL,
  `address_2` varchar(50) NOT NULL,
  `address_street` varchar(50) NOT NULL,
  `address_no` varchar(10) NOT NULL,
  `post_code` varchar(30) NOT NULL,
  `city` varchar(50) NOT NULL,
  `country` varchar(20) NOT NULL,
  `phone_no` varchar(50) NOT NULL,
  `fax_no` varchar(50) NOT NULL,
  `email` varchar(80) NOT NULL,
  `homepage` varchar(80) NOT NULL,
  `salesperson_code` varchar(10) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `currency_code` varchar(10) NOT NULL,
  `bill_to_customer_no` varchar(20) NOT NULL,
  `bill_to_name` varchar(50) NOT NULL,
  `bill_to_name_2` varchar(50) NOT NULL,
  `bill_to_address` varchar(50) NOT NULL,
  `bill_to_address_2` varchar(50) NOT NULL,
  `bill_to_post_code` varchar(30) NOT NULL,
  `bill_to_city` varchar(50) NOT NULL,
  `bill_to_country` varchar(20) NOT NULL,
  `customer_price_group` varchar(10) NOT NULL,
  `vat_bus_posting_group` varchar(10) NOT NULL,
  `invoice_disc_code` varchar(10) NOT NULL,
  `customer_disc_group` varchar(20) NOT NULL,
  `payment_terms` varchar(80) NOT NULL,
  `surname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `company_name` varchar(45) NOT NULL,
  `salutation` varchar(4) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nav_primary` (`company`,`customer_no`),
  KEY `company` (`company`),
  KEY `to_delete` (`to_delete`),
  KEY `salesperson_code` (`salesperson_code`),
  KEY `shop_code` (`shop_code`),
  KEY `language_code` (`language_code`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_customer_address
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_customer_address`;

CREATE TABLE `shop_customer_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `customer_no` varchar(20) NOT NULL,
  `code` varchar(10) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL,
  `name_2` varchar(50) NOT NULL DEFAULT '',
  `address` varchar(50) NOT NULL,
  `address_2` varchar(50) NOT NULL DEFAULT '',
  `post_code` varchar(20) NOT NULL,
  `city` varchar(50) NOT NULL,
  `country` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_customer_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_customer_link`;

CREATE TABLE `shop_customer_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `customer_no` varchar(20) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `active_for_dealer_search` tinyint(1) NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `company` (`company`),
  KEY `shop_code` (`shop_code`),
  KEY `active_for_dealer_search` (`active_for_dealer_search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_customer_pseudo_pay_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_customer_pseudo_pay_data`;

CREATE TABLE `shop_customer_pseudo_pay_data` (
  `id` int(11) NOT NULL,
  `customer_no` varchar(30) CHARACTER SET latin1 NOT NULL,
  `line_no` int(11) NOT NULL,
  `type` int(3) NOT NULL,
  `card_type` int(3) DEFAULT NULL,
  `holder_owner_name` varchar(120) CHARACTER SET latin1 NOT NULL,
  `pseudo_card_no` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `card_expire_date` date DEFAULT NULL,
  `bic` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `pseudo_iban` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `bank_name` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `to_delete` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nav_primary` (`line_no`,`customer_no`),
  KEY `customer` (`customer_no`),
  KEY `customer_type` (`customer_no`,`type`),
  KEY `to_delete` (`to_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_customer_template
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_customer_template`;

CREATE TABLE `shop_customer_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL DEFAULT '',
  `code` varchar(10) NOT NULL DEFAULT '',
  `description` varchar(50) NOT NULL DEFAULT '',
  `territory_code` varchar(10) NOT NULL DEFAULT '',
  `global_dimension_1_code` varchar(20) NOT NULL DEFAULT '',
  `global_dimension_2_code` varchar(20) NOT NULL DEFAULT '',
  `customer_posting_group` varchar(20) NOT NULL DEFAULT '',
  `currency_code` varchar(10) NOT NULL DEFAULT '',
  `customer_price_group` varchar(10) NOT NULL DEFAULT '',
  `payment_terms_code` varchar(10) NOT NULL DEFAULT '',
  `shipment_method_code` varchar(10) NOT NULL DEFAULT '',
  `invoice_disc_code` varchar(20) NOT NULL DEFAULT '',
  `customer_disc_group` varchar(20) NOT NULL DEFAULT '',
  `country_region_code` varchar(10) NOT NULL DEFAULT '',
  `payment_method_code` varchar(10) NOT NULL DEFAULT '',
  `prices_including_vat` tinyint(1) NOT NULL DEFAULT '0',
  `gen_bus_posting_group` varchar(20) NOT NULL DEFAULT '',
  `vat_bus_posting_group` varchar(20) NOT NULL DEFAULT '',
  `contact_type` int(11) NOT NULL DEFAULT '0',
  `allow_line_disc` tinyint(1) NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `NAV_PRIMARY` (`company`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump shop_digital_coupon
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_digital_coupon`;

CREATE TABLE `shop_digital_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_header` varchar(20) NOT NULL,
  `shop_coupon_line_id` int(11) NOT NULL,
  `from_name` varchar(100) NOT NULL,
  `from_email` varchar(200) NOT NULL,
  `to_name` varchar(100) NOT NULL,
  `to_email` varchar(200) NOT NULL,
  `amount_id` tinyint(1) NOT NULL DEFAULT '0',
  `amount` decimal(10,2) NOT NULL,
  `background_image` tinyint(1) NOT NULL,
  `message` text NOT NULL,
  `shipping_option` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_invoice_discount
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_invoice_discount`;

CREATE TABLE `shop_invoice_discount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `invoice_discount_code` varchar(10) NOT NULL,
  `currency_code` varchar(20) NOT NULL,
  `minimum_amount` decimal(10,4) NOT NULL,
  `discount` decimal(10,4) NOT NULL,
  `service_charge` decimal(10,4) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nav_primary` (`company`,`invoice_discount_code`,`currency_code`,`minimum_amount`),
  KEY `to_delete` (`to_delete`),
  KEY `company` (`company`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_item
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_item`;

CREATE TABLE `shop_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `item_no` varchar(20) NOT NULL,
  `description` varchar(50) NOT NULL,
  `summary` varchar(250) NOT NULL,
  `base_unit_of_measure` varchar(30) NOT NULL,
  `unit_of_measure_code` varchar(10) NOT NULL,
  `nav_base_unit_code` varchar(10) NOT NULL,
  `multiplier` decimal(10,2) NOT NULL DEFAULT '1.00',
  `variant_type` varchar(50) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `validity_from` date NOT NULL,
  `validity_to` date NOT NULL,
  `main_picture_line_no` int(11) NOT NULL,
  `main_category_line_no` int(11) NOT NULL,
  `retail_price` decimal(10,4) NOT NULL,
  `base_price` decimal(10,4) NOT NULL,
  `price_includes_vat` tinyint(1) NOT NULL DEFAULT '0',
  `inventory` decimal(10,2) NOT NULL,
  `insufficient_inventory_limit` decimal(10,2) NOT NULL,
  `quantity_on_purchase_order` decimal(10,2) NOT NULL,
  `discount_group` varchar(10) NOT NULL,
  `allow_invoice_discount` tinyint(1) NOT NULL DEFAULT '0',
  `search_query` varchar(250) NOT NULL,
  `vendor_no` varchar(20) NOT NULL,
  `vendor_name` varchar(50) NOT NULL,
  `parent_item_no` varchar(45) NOT NULL,
  `order_ranking` decimal(10,2) NOT NULL,
  `weight` decimal(10,2) NOT NULL,
  `net_weight` decimal(10,2) NOT NULL,
  `width` decimal(10,2) NOT NULL,
  `height` decimal(10,2) NOT NULL,
  `length` decimal(10,2) NOT NULL,
  `volume` decimal(10,2) NOT NULL,
  `creation_date` date NOT NULL,
  `meta_keywords` varchar(250) NOT NULL,
  `meta_description` varchar(250) NOT NULL,
  `site_title` varchar(100) NOT NULL,
  `allow_gift_package` tinyint(1) NOT NULL DEFAULT '0',
  `is_gift_package` tinyint(1) NOT NULL DEFAULT '0',
  `minimum_order_quantity` decimal(10,0) NOT NULL,
  `quantity_packing_unit` decimal(10,0) NOT NULL,
  `order_per_packing_unit` tinyint(1) NOT NULL DEFAULT '0',
  `vat_prod_posting_group` varchar(10) NOT NULL,
  `customizable` tinyint(1) NOT NULL DEFAULT '0',
  `customization_price` decimal(10,4) NOT NULL,
  `canonical_url` varchar(1024) NOT NULL,
  `main_preview_image_filename` varchar(250) DEFAULT NULL,
  `shipping_class_code` varchar(20) NOT NULL DEFAULT '',
  `always_available` tinyint(1) NOT NULL DEFAULT '0',
  `is_greeting_card` tinyint(1) NOT NULL DEFAULT '0',
  `marketplace_max_inventory` decimal(10,4) NOT NULL,
  `marketplace_update` tinyint(1) NOT NULL DEFAULT '0',
  `marketplace_update_inventory` tinyint(1) NOT NULL DEFAULT '0',
  `marketplace_update_price` tinyint(1) NOT NULL DEFAULT '0',
  `marketplace_update_images` tinyint(1) NOT NULL DEFAULT '0',
  `marketplace_update_info` tinyint(1) NOT NULL DEFAULT '0',
  `marketplace_error` varchar(255) NOT NULL,
  `marketplace_last_update` datetime NOT NULL,
  `item_condition` int(11) NOT NULL,
  `item_location_zip` int(5) NOT NULL,
  `marketplace_ebay_item_template` varchar(30) NOT NULL,
  `marketplace_existing_item` tinyint(1) NOT NULL DEFAULT '0',
  `marketplace_item_id` varchar(200) NOT NULL,
  `marketplace_standalone_product` tinyint(1) NOT NULL DEFAULT '0',
  `marketplace_start_relationship` tinyint(1) NOT NULL DEFAULT '0',
  `g_product_category` varchar(250) NOT NULL,
  `item_slug` varchar(50) NOT NULL,
  `update_insert` tinyint(1) NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nav_primary` (`item_no`,`language_code`,`shop_code`,`company`),
  UNIQUE KEY `query_main` (`item_no`,`parent_item_no`,`language_code`,`active`,`shop_code`,`validity_from`,`validity_to`,`company`),
  KEY `active` (`active`),
  KEY `validity_from` (`validity_from`),
  KEY `validity_to` (`validity_to`),
  KEY `parent_item_no` (`parent_item_no`),
  KEY `to_delete` (`to_delete`),
  KEY `variant_type` (`variant_type`),
  KEY `creation_date` (`creation_date`),
  KEY `order_ranking` (`order_ranking`),
  KEY `description` (`description`),
  KEY `item_no` (`item_no`),
  KEY `base_price` (`base_price`),
  KEY `retail_price` (`retail_price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;





# Tabellen-Dump shop_item_comments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_item_comments`;

CREATE TABLE `shop_item_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `line_no` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_no` varchar(20) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `comment` text NOT NULL,
  `name` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `header` varchar(45) NOT NULL,
  `email` varchar(200) NOT NULL,
  `creation_date` datetime NOT NULL,
  `released` tinyint(4) NOT NULL,
  `update_insert` tinyint(4) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_item_cross_reference
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_item_cross_reference`;

CREATE TABLE `shop_item_cross_reference` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `item_no` varchar(20) NOT NULL,
  `variant_code` varchar(10) NOT NULL,
  `unit_of_measure_code` varchar(10) NOT NULL,
  `reference_type` tinyint(1) NOT NULL DEFAULT '0',
  `customer_no` varchar(30) NOT NULL,
  `item_reference_no` varchar(20) NOT NULL,
  `description` varchar(80) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nav_primary` (`item_no`,`customer_no`,`reference_type`,`variant_code`,`unit_of_measure_code`,`company`,`item_reference_no`),
  UNIQUE KEY `helper_1` (`item_reference_no`,`reference_type`,`customer_no`,`company`,`item_no`,`variant_code`,`unit_of_measure_code`),
  KEY `to_delete` (`to_delete`),
  KEY `item_reference_no` (`item_reference_no`),
  KEY `helper_2` (`item_no`,`variant_code`,`reference_type`,`company`,`item_reference_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_item_customer_price
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_item_customer_price`;

CREATE TABLE `shop_item_customer_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) DEFAULT NULL,
  `item_no` varchar(20) DEFAULT NULL,
  `variant_code` varchar(10) DEFAULT NULL,
  `customer_no` varchar(20) DEFAULT NULL,
  `shop_code` varchar(10) DEFAULT NULL,
  `customer_price` decimal(10,4) DEFAULT NULL,
  `timestamp_last_calculated` decimal(20,4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump shop_item_customize
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_item_customize`;

CREATE TABLE `shop_item_customize` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `item_no` varchar(20) NOT NULL,
  `field_length` int(10) NOT NULL,
  `field_name` varchar(50) NOT NULL,
  `field_description` varchar(200) NOT NULL,
  `field_type` int(11) NOT NULL,
  `line_no` int(11) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_item_data_management
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_item_data_management`;

CREATE TABLE `shop_item_data_management` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(20) NOT NULL,
  `source_table` varchar(50) NOT NULL,
  `source_primary` int(11) NOT NULL,
  `source_type_description` tinyint(1) NOT NULL,
  `source_type_file` tinyint(1) NOT NULL,
  `item_no` varchar(20) NOT NULL,
  `to_delete` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_item_description
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_item_description`;

CREATE TABLE `shop_item_description` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `item_no` varchar(20) NOT NULL,
  `line_no` int(11) NOT NULL,
  `description` varchar(50) NOT NULL,
  `shop_text_module_code` varchar(30) NOT NULL,
  `content` text NOT NULL,
  `all_language_codes` tinyint(1) NOT NULL DEFAULT '0',
  `show_in_header` tinyint(1) NOT NULL DEFAULT '0',
  `all_shop_codes` tinyint(4) NOT NULL DEFAULT '0',
  `marketplace_title` tinyint(4) NOT NULL DEFAULT '0',
  `nav_primary` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `marketplace_only` tinyint(1) NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `query_1` (`item_no`,`line_no`,`language_code`,`shop_code`,`show_in_header`,`all_language_codes`),
  KEY `nav_primary` (`company`,`shop_code`,`language_code`,`item_no`,`line_no`),
  KEY `to_delete` (`to_delete`),
  KEY `company` (`company`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_item_elasticsearch
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_item_elasticsearch`;

CREATE TABLE `shop_item_elasticsearch` (
  `id` int(11) NOT NULL,
  `last_version_json` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_item_file
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_item_file`;

CREATE TABLE `shop_item_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `item_no` varchar(20) NOT NULL,
  `variant_code` varchar(20) NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `line_no` int(11) NOT NULL,
  `description` varchar(50) NOT NULL,
  `filename` varchar(255) NOT NULL DEFAULT '',
  `youtube_video_id` varchar(20) NOT NULL DEFAULT '',
  `all_language_codes` tinyint(1) NOT NULL DEFAULT '0',
  `mp4` tinyint(1) NOT NULL DEFAULT '0',
  `webm` tinyint(1) NOT NULL DEFAULT '0',
  `ogg` tinyint(1) NOT NULL DEFAULT '0',
  `customization` tinyint(1) NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `to_delete` (`to_delete`),
  KEY `company` (`company`),
  KEY `item_no` (`item_no`),
  KEY `type` (`type`),
  KEY `nav_primary` (`company`,`shop_code`,`language_code`,`item_no`,`variant_code`,`type`,`line_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_item_has_category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_item_has_category`;

CREATE TABLE `shop_item_has_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `item_no` varchar(20) NOT NULL,
  `category_shop_code` varchar(10) NOT NULL,
  `category_language_code` varchar(10) NOT NULL,
  `category_line_no` int(11) NOT NULL,
  `sorting` int(11) NOT NULL,
  `url` varchar(1024) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `to_delete` (`to_delete`),
  KEY `category_line_no` (`category_line_no`),
  KEY `item_no` (`item_no`),
  KEY `sorting_primary` (`company`,`shop_code`,`language_code`,`category_line_no`,`sorting`),
  KEY `sorting` (`sorting`),
  KEY `company` (`company`,`shop_code`,`language_code`,`item_no`),
  KEY `nav_primary` (`item_no`,`category_line_no`,`language_code`,`category_language_code`,`shop_code`,`category_shop_code`,`company`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_item_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_item_link`;

CREATE TABLE `shop_item_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `item_no` varchar(20) NOT NULL,
  `linked_item_no` varchar(20) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `helper` (`linked_item_no`,`type`,`shop_code`,`company`,`item_no`),
  KEY `to_delete` (`to_delete`),
  KEY `company` (`company`),
  KEY `item_no_2` (`item_no`,`linked_item_no`),
  KEY `item_no_3` (`item_no`),
  KEY `linked_item_no_2` (`linked_item_no`),
  KEY `nav_primary` (`item_no`,`type`,`shop_code`,`company`,`linked_item_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_item_variant
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_item_variant`;

CREATE TABLE `shop_item_variant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `item_no` varchar(20) NOT NULL,
  `code` varchar(10) NOT NULL,
  `description` varchar(50) NOT NULL,
  `description_2` varchar(50) NOT NULL,
  `inventory` decimal(10,2) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nav_primary` (`company`,`item_no`,`code`),
  KEY `company` (`company`),
  KEY `to_delete` (`to_delete`),
  KEY `item_no` (`item_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_item_variant_translation
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_item_variant_translation`;

CREATE TABLE `shop_item_variant_translation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `item_no` varchar(20) NOT NULL,
  `variant_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `description` varchar(50) NOT NULL,
  `description_2` varchar(50) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nav_primary` (`company`,`item_no`,`variant_code`,`language_code`),
  KEY `company` (`company`),
  KEY `to_delete` (`to_delete`),
  KEY `language_code` (`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_language
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_language`;

CREATE TABLE `shop_language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `code` varchar(10) NOT NULL,
  `description` varchar(50) NOT NULL,
  `default_currency_code` varchar(10) NOT NULL,
  `default_country_code` varchar(10) NOT NULL,
  `default_payment_option_line_no` int(10) NOT NULL,
  `email_order_1_text_module` varchar(30) NOT NULL,
  `email_order_2_text_module` varchar(30) NOT NULL,
  `email_order_3_text_module` varchar(30) NOT NULL,
  `email_order_4_text_module` varchar(30) NOT NULL,
  `email_login_text_module` varchar(30) NOT NULL,
  `email_password_text_module` varchar(30) NOT NULL,
  `email_invoice_copy_text_module` varchar(30) NOT NULL,
  `email_shipment_copy_text_module` varchar(30) NOT NULL,
  `email_cr_memo_copy_text_module` varchar(30) NOT NULL,
  `email_return_order_text_module` varchar(30) NOT NULL,
  `email_rma_confirm_text_module` varchar(30) NOT NULL,
  `email_availability_notify` varchar(30) NOT NULL,
  `login_welcome_text_module` varchar(30) NOT NULL,
  `shopping_basket_text_module` varchar(30) NOT NULL,
  `empty_basket_text_module` varchar(30) NOT NULL,
  `order_queue_text_module` varchar(30) NOT NULL,
  `order_step_1_text_module` varchar(30) NOT NULL,
  `order_step_2_text_module` varchar(30) NOT NULL,
  `order_step_3_text_module` varchar(30) NOT NULL,
  `order_complete_text_module` varchar(30) NOT NULL,
  `return_order_complete_text_module` varchar(30) NOT NULL,
  `payment_error_text_module` varchar(30) NOT NULL,
  `checkout_confirmation_text_module` varchar(30) NOT NULL,
  `newsletter_registration_text_module` varchar(30) NOT NULL,
  `recommend_mail_text_module` varchar(30) NOT NULL,
  `itemcard_trust_box_text_module` varchar(30) NOT NULL,
  `text_search_results` varchar(30) NOT NULL,
  `item_placeholder_image` varchar(30) NOT NULL,
  `billpay_agb_text_module` varchar(30) NOT NULL,
  `digital_coupon_active` tinyint(1) NOT NULL,
  `coupon_header_digital_coupon` varchar(20) NOT NULL,
  `digital_coupon_background_1` varchar(100) NOT NULL,
  `digital_coupon_background_2` varchar(100) NOT NULL,
  `digital_coupon_background_3` varchar(100) NOT NULL,
  `digital_coupon_background_4` varchar(100) NOT NULL,
  `digital_coupon_background_5` varchar(100) NOT NULL,
  `digital_coupon_amount_1` decimal(10,2) NOT NULL,
  `digital_coupon_amount_2` decimal(10,2) NOT NULL,
  `digital_coupon_amount_3` decimal(10,2) NOT NULL,
  `digital_coupon_amount_4` decimal(10,2) NOT NULL,
  `digital_coupon_amount_5` decimal(10,2) NOT NULL,
  `minimum_coupon_amount` decimal(10,2) NOT NULL,
  `max_coupon_amount` decimal(10,2) NOT NULL,
  `gl_account_coupons` varchar(30) NOT NULL,
  `text_email_digital_coupon` varchar(30) NOT NULL,
  `dc_category_line_no` int(11) NOT NULL,
  `nl_coupon_active` tinyint(1) NOT NULL DEFAULT '0',
  `nl_coupon_header` varchar(50) NOT NULL,
  `nl_coupon_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `share_facebook` tinyint(1) NOT NULL DEFAULT '0',
  `share_google` tinyint(1) NOT NULL DEFAULT '0',
  `share_twitter` tinyint(1) NOT NULL DEFAULT '0',
  `share_pinterest` tinyint(1) NOT NULL DEFAULT '0',
  `share_xing` tinyint(1) NOT NULL DEFAULT '0',
  `share_mail` tinyint(1) NOT NULL DEFAULT '0',
  `marketplace_cancellation_text` varchar(30) DEFAULT NULL,
  `marketplace_ebay_item_template` varchar(30) DEFAULT NULL,
  `itemcard_trust_box_text_module_2` varchar(30) NOT NULL,
  `number_of_items_per_page` int(11) NOT NULL DEFAULT '0',
  `fitting_items_text_module` varchar(100) NOT NULL,
  `alternative_items_text_module` varchar(100) NOT NULL,
  `accessories_items_text_module` varchar(100) NOT NULL,
  `spare_parts_text_module` varchar(100) NOT NULL,
  `check_shipping_address` tinyint(1) NOT NULL,
  `address_doctor_customer_id` varchar(45) NOT NULL,
  `address_doctor_password` varchar(45) DEFAULT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `company` (`company`),
  KEY `to_delete` (`to_delete`),
  KEY `nav_primary` (`company`,`shop_code`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_marketplace_amazon_prices
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_amazon_prices`;

CREATE TABLE `shop_marketplace_amazon_prices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `asin` varchar(200) NOT NULL,
  `sku` varchar(20) NOT NULL,
  `condition` varchar(20) NOT NULL,
  `marketplace_id` varchar(50) NOT NULL,
  `offer_listing_price` decimal(10,4) NOT NULL,
  `offer_shipping_price` decimal(10,4) NOT NULL,
  `offer_ships_from` varchar(10) NOT NULL,
  `offer_is_fullfilled_by_amazon` varchar(5) NOT NULL,
  `offer_is_buybox_winner` varchar(5) NOT NULL,
  `offer_is_featured_merchant` varchar(5) NOT NULL,
  `seller_positive_feedback_rating` decimal(10,1) NOT NULL,
  `seller_feedback_count` int(11) NOT NULL,
  `shipping_time_minimum_hours` int(11) NOT NULL,
  `shipping_time_maximum_hours` int(11) NOT NULL,
  `shipping_availability_type` varchar(100) NOT NULL,
  `my_offer` tinyint(1) NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  `update_insert` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_marketplace_amazon_xsd
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_amazon_xsd`;

CREATE TABLE `shop_marketplace_amazon_xsd` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_code` varchar(255) NOT NULL,
  `element` varchar(255) NOT NULL,
  `element_type_tag` varchar(255) NOT NULL,
  `productType` varchar(255) NOT NULL,
  `xsd` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_marketplace_categories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_categories`;

CREATE TABLE `shop_marketplace_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `marketplace_type` tinyint(1) unsigned NOT NULL,
  `category_id` varchar(45) DEFAULT NULL,
  `category_code` varchar(45) NOT NULL,
  `category_name` varchar(45) DEFAULT NULL,
  `category_level` int(11) unsigned NOT NULL,
  `parent_category_code` varchar(45) DEFAULT NULL,
  `root_category_code` varchar(100) NOT NULL,
  `in_use` tinyint(1) unsigned NOT NULL,
  `variant_available` tinyint(4) NOT NULL DEFAULT '0',
  `children_fetched` tinyint(1) unsigned NOT NULL,
  `timestamp_fetched` int(11) unsigned NOT NULL,
  `timestamp_children_fetched` int(11) unsigned NOT NULL,
  `update_insert` tinyint(1) NOT NULL DEFAULT '1',
  `to_delete` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_marketplace_conditions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_conditions`;

CREATE TABLE `shop_marketplace_conditions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `marketplace_type` tinyint(4) NOT NULL,
  `category_code` varchar(255) NOT NULL,
  `condition_code` varchar(255) NOT NULL,
  `codition_description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_marketplace_config_amazon
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_config_amazon`;

CREATE TABLE `shop_marketplace_config_amazon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `environment` varchar(50) NOT NULL,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `aws_access_key_id` varchar(100) NOT NULL,
  `aws_secret_access_key` varchar(100) NOT NULL,
  `merchant_id` varchar(50) NOT NULL,
  `marketplace_id` varchar(50) NOT NULL,
  `marketplace_uk` varchar(50) NOT NULL,
  `marketplace_es` varchar(50) NOT NULL,
  `marketplace_fr` varchar(50) NOT NULL,
  `marketplace_it` varchar(50) NOT NULL,
  `marketplace_us` varchar(50) NOT NULL,
  `mws_auth_token` varchar(255) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nav_primary` (`shop_code`,`language_code`,`company`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_marketplace_config_ebay
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_config_ebay`;

CREATE TABLE `shop_marketplace_config_ebay` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `environment` varchar(20) NOT NULL,
  `auth_token` text NOT NULL,
  `dev_name` varchar(255) NOT NULL,
  `app_name` varchar(255) NOT NULL,
  `cert_name` varchar(255) NOT NULL,
  `runame` varchar(255) NOT NULL,
  `session_id` varchar(40) NOT NULL,
  `token_url` varchar(255) NOT NULL,
  `final_token_url` varchar(255) NOT NULL,
  `user_auth_token` text NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_marketplace_ebay_jobs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_ebay_jobs`;

CREATE TABLE `shop_marketplace_ebay_jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `request_name` varchar(255) NOT NULL,
  `job_id` varchar(255) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_marketplace_errors
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_errors`;

CREATE TABLE `shop_marketplace_errors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `marketplace_type` tinyint(4) NOT NULL,
  `operation` varchar(255) NOT NULL,
  `parameter` text NOT NULL,
  `errormsg` text NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `update_insert` tinyint(1) DEFAULT '1',
  `to_delete` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_marketplace_item_errors
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_item_errors`;

CREATE TABLE `shop_marketplace_item_errors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `marketplace_type` tinyint(4) NOT NULL,
  `company` varchar(255) NOT NULL,
  `shop_code` varchar(255) NOT NULL,
  `language_code` varchar(255) NOT NULL,
  `item_no` varchar(255) NOT NULL,
  `operation` varchar(255) NOT NULL,
  `errorcode` varchar(255) NOT NULL,
  `errortext` text NOT NULL,
  `update_insert` tinyint(1) NOT NULL DEFAULT '1',
  `to_delete` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_marketplace_item_update
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_item_update`;

CREATE TABLE `shop_marketplace_item_update` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `item_no` varchar(20) NOT NULL,
  `item_shop_code` varchar(10) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `item_language_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `marketplace_update` tinyint(1) NOT NULL DEFAULT '0',
  `marketplace_update_inventory` tinyint(1) NOT NULL DEFAULT '0',
  `marketplace_update_price` tinyint(1) NOT NULL DEFAULT '0',
  `marketplace_update_images` tinyint(1) NOT NULL DEFAULT '0',
  `marketplace_relist_item` tinyint(1) NOT NULL DEFAULT '0',
  `marketplace_update_info` tinyint(1) NOT NULL DEFAULT '0',
  `marketplace_existing_item` tinyint(1) NOT NULL DEFAULT '0',
  `marketplace_last_update` datetime NOT NULL,
  `marketplace_last_nav_update` datetime NOT NULL,
  `marketplace_standalone_product` tinyint(1) NOT NULL DEFAULT '0',
  `marketplace_start_relationship` tinyint(1) NOT NULL DEFAULT '0',
  `marketplace_item_id` varchar(200) NOT NULL,
  `marketplace_error` varchar(250) NOT NULL,
  `last_transfered_inventory` decimal(10,2) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `active_on_marketplace` tinyint(1) NOT NULL DEFAULT '0',
  `delete_on_marketplace` tinyint(1) NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  `update_insert` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump shop_marketplace_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_items`;

CREATE TABLE `shop_marketplace_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `marketplace_type` tinyint(4) NOT NULL,
  `item_no` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `summary` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `marketplace_item_id` varchar(255) NOT NULL,
  `inventory` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `base_price` decimal(10,4) NOT NULL,
  `currency` varchar(20) NOT NULL,
  `condition` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `is_variation_parent` tinyint(1) NOT NULL DEFAULT '0',
  `item_location_zip` int(5) NOT NULL,
  `channel` varchar(255) NOT NULL,
  `ean` varchar(255) NOT NULL,
  `vat_prod_posting_group` varchar(10) NOT NULL,
  `marketplace_custom_category_id_1` varchar(255) NOT NULL,
  `marketplace_custom_category_id_2` varchar(255) NOT NULL,
  `timestamp_modified` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_marketplace_items_attributes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_items_attributes`;

CREATE TABLE `shop_marketplace_items_attributes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `item_no` varchar(20) NOT NULL,
  `is_variation` tinyint(4) NOT NULL DEFAULT '0',
  `attribute_code` varchar(255) NOT NULL,
  `value_text` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_marketplace_items_import
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_items_import`;

CREATE TABLE `shop_marketplace_items_import` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `item_id` varchar(200) NOT NULL,
  `timestamp_modified` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_marketplace_items_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_items_link`;

CREATE TABLE `shop_marketplace_items_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `item_no` varchar(20) NOT NULL,
  `linked_item_no` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_marketplace_payments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_payments`;

CREATE TABLE `shop_marketplace_payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `marketplace_type` tinyint(4) NOT NULL,
  `category_code` varchar(255) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_marketplace_queue
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_queue`;

CREATE TABLE `shop_marketplace_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `marketplace_type` tinyint(4) NOT NULL,
  `operation` varchar(255) NOT NULL,
  `parameter` text NOT NULL,
  `unique_id` varchar(255) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `parent_queue_id` int(10) NOT NULL,
  `finish_parent_queue` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_marketplace_queue2
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_queue2`;

CREATE TABLE `shop_marketplace_queue2` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `marketplace_type` tinyint(4) NOT NULL,
  `operation` varchar(255) NOT NULL,
  `parameter` text NOT NULL,
  `unique_id` varchar(255) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_marketplace_submissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_submissions`;

CREATE TABLE `shop_marketplace_submissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `marketplace_type` tinyint(4) NOT NULL,
  `operation` varchar(255) NOT NULL,
  `parameter` text NOT NULL,
  `submission_id` varchar(255) NOT NULL,
  `unique_id` varchar(255) NOT NULL,
  `queue_id` int(10) unsigned NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `parent_queue_id` int(10) NOT NULL,
  `finish_parent_queue` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_marketplace_submissions_ready
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_submissions_ready`;

CREATE TABLE `shop_marketplace_submissions_ready` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `marketplace_type` tinyint(4) NOT NULL,
  `operation` varchar(255) NOT NULL,
  `parameter` text NOT NULL,
  `generated_id` varchar(255) NOT NULL,
  `unique_id` varchar(255) NOT NULL,
  `queue_id` int(10) unsigned NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `parent_queue_id` int(10) NOT NULL,
  `finish_parent_queue` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_marketplace_submissions_result
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_submissions_result`;

CREATE TABLE `shop_marketplace_submissions_result` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `marketplace_type` tinyint(4) NOT NULL,
  `operation` varchar(255) NOT NULL,
  `parameter` text NOT NULL,
  `unique_id` varchar(255) NOT NULL,
  `queue_id` int(10) unsigned NOT NULL,
  `result_file` varchar(255) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `processed` tinyint(1) NOT NULL DEFAULT '0',
  `parent_queue_id` int(10) NOT NULL,
  `finish_parent_queue` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_marketplace_variant_attribute_values
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_variant_attribute_values`;

CREATE TABLE `shop_marketplace_variant_attribute_values` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `marketplace_type` tinyint(4) NOT NULL,
  `category_code` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `value` varchar(100) DEFAULT NULL,
  `update_insert` tinyint(1) NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_marketplace_variant_attributes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_variant_attributes`;

CREATE TABLE `shop_marketplace_variant_attributes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `marketplace_type` tinyint(4) NOT NULL,
  `category_code` varchar(100) NOT NULL,
  `productType` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `is_variation` tinyint(4) NOT NULL DEFAULT '0',
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `ValueType` varchar(100) NOT NULL,
  `MaxValues` int(11) DEFAULT NULL,
  `SelectionMode` varchar(45) DEFAULT NULL,
  `timestamp_fetched` int(10) unsigned NOT NULL,
  `update_insert` tinyint(1) NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_marketplace_variant_themes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_marketplace_variant_themes`;

CREATE TABLE `shop_marketplace_variant_themes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `marketplace_type` tinyint(4) NOT NULL,
  `category_code` varchar(255) NOT NULL,
  `productType` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `timestamp_fetched` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Tabellen-Dump shop_nav_sales_header
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_nav_sales_header`;

CREATE TABLE `shop_nav_sales_header` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `no` varchar(20) NOT NULL,
  `order_date` date DEFAULT NULL,
  `promised_delivery_date` date DEFAULT NULL,
  `webshop_order_no` int(11) NOT NULL,
  `your_reference` varchar(50) NOT NULL,
  `sell_to_customer_no` varchar(50) NOT NULL,
  `bill_to_customer_no` varchar(20) NOT NULL,
  `sell_to_name` varchar(50) NOT NULL,
  `sell_to_name_2` varchar(50) NOT NULL,
  `sell_to_address` varchar(50) NOT NULL,
  `sell_to_address_2` varchar(50) NOT NULL,
  `sell_to_post_code` varchar(20) NOT NULL,
  `sell_to_city` varchar(50) NOT NULL,
  `sell_to_country` varchar(50) NOT NULL,
  `sell_to_contact` varchar(50) NOT NULL,
  `bill_to_name` varchar(50) NOT NULL,
  `bill_to_name_2` varchar(50) NOT NULL,
  `bill_to_address` varchar(50) NOT NULL,
  `bill_to_address_2` varchar(50) NOT NULL,
  `bill_to_post_code` varchar(20) NOT NULL,
  `bill_to_city` varchar(50) NOT NULL,
  `bill_to_country` varchar(50) NOT NULL,
  `bill_to_contact` varchar(50) NOT NULL,
  `ship_to_name` varchar(50) NOT NULL,
  `ship_to_name_2` varchar(50) NOT NULL,
  `ship_to_address` varchar(50) NOT NULL,
  `ship_to_address_2` varchar(50) NOT NULL,
  `ship_to_post_code` varchar(20) NOT NULL,
  `ship_to_city` varchar(50) NOT NULL,
  `ship_to_country` varchar(50) NOT NULL,
  `ship_to_contact` varchar(50) NOT NULL,
  `amount` decimal(10,4) NOT NULL,
  `amount_including_vat` decimal(10,4) NOT NULL,
  `shipment_method` varchar(80) NOT NULL,
  `payment_terms` varchar(80) NOT NULL,
  `return_receipt_no` varchar(20) NOT NULL,
  `last_return_receipt_no` varchar(20) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `posting_date` (`order_date`),
  KEY `webshop_order_no` (`webshop_order_no`),
  KEY `sell_to_customer_no` (`sell_to_customer_no`),
  KEY `bill_to_customer_no` (`bill_to_customer_no`),
  KEY `to_delete` (`to_delete`),
  KEY `amount` (`amount`),
  KEY `company` (`company`),
  KEY `bill_to_contact` (`bill_to_contact`),
  KEY `nav_primary` (`no`,`type`,`company`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_nav_sales_line
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_nav_sales_line`;

CREATE TABLE `shop_nav_sales_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `document_type` tinyint(1) NOT NULL,
  `document_no` varchar(20) NOT NULL,
  `line_no` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `no` varchar(20) NOT NULL,
  `description` varchar(50) NOT NULL,
  `description_2` varchar(50) NOT NULL,
  `unit_of_measure` varchar(10) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_price` decimal(10,4) NOT NULL,
  `line_amount` decimal(10,4) NOT NULL,
  `return_receipt_no` varchar(20) NOT NULL,
  `return_reason_code` varchar(10) NOT NULL,
  `variant_code` varchar(10) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `no` (`no`),
  KEY `quantity` (`quantity`),
  KEY `line_amount` (`line_amount`),
  KEY `to_delete` (`to_delete`),
  KEY `nav_primary` (`document_no`,`line_no`,`document_type`,`company`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_order_us_sales_tax
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_order_us_sales_tax`;

CREATE TABLE `shop_order_us_sales_tax` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  `basket_header_id` int(11) DEFAULT NULL,
  `webshop_order_no` varchar(35) DEFAULT NULL,
  `line_id` varchar(125) DEFAULT NULL,
  `order_total` decimal(20,4) DEFAULT NULL,
  `shipping` decimal(20,4) DEFAULT NULL,
  `tax_source` varchar(20) DEFAULT NULL,
  `freight_taxable` tinyint(1) DEFAULT NULL,
  `has_nexus` tinyint(1) DEFAULT NULL,
  `taxable_amount` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `combined_tax_rate` decimal(6,4) NOT NULL DEFAULT '0.0000',
  `tax_collectable` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `state_taxable_amount` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `state_tax_rate` decimal(6,4) NOT NULL DEFAULT '0.0000',
  `state_tax_collectable` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `county_taxable_amount` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `county_tax_rate` decimal(6,4) NOT NULL DEFAULT '0.0000',
  `county_tax_collectable` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `city_taxable_amount` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `city_tax_rate` decimal(6,4) NOT NULL DEFAULT '0.0000',
  `city_tax_collectable` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `special_district_taxable_amount` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `special_district_tax_rate` decimal(6,4) NOT NULL DEFAULT '0.0000',
  `special_district_tax_collectable` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `transaction_date` datetime DEFAULT NULL,
  `transaction_id` varchar(45) DEFAULT NULL,
  `transaction_reference_id` varchar(45) DEFAULT NULL,
  `update_insert` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `basket` (`basket_header_id`,`line_id`),
  KEY `visitor_id` (`visitor_id`),
  KEY `basket_id` (`basket_header_id`),
  KEY `webshop_order_no` (`webshop_order_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_payment_option
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_payment_option`;

CREATE TABLE `shop_payment_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `line_no` int(11) NOT NULL,
  `description` varchar(50) NOT NULL,
  `country_code` varchar(10) NOT NULL,
  `checkout` tinyint(1) NOT NULL DEFAULT '0',
  `checkout_state` tinyint(1) NOT NULL DEFAULT '0',
  `payment_cost` decimal(10,2) NOT NULL,
  `credit_check_required` tinyint(1) NOT NULL DEFAULT '0',
  `dc_active` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `recurrent_payment_active` tinyint(1) NOT NULL DEFAULT '0',
  `logo` varchar(100) DEFAULT NULL,
  `content` varchar(250) DEFAULT NULL,
  `text_module_order_conf` varchar(80) NOT NULL DEFAULT '',
  `sorting` int(11) NOT NULL,
  `send_order_mail_text` varchar(30) NOT NULL,
  `send_order_mail_1_text` varchar(30) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_2` (`company`,`shop_code`,`language_code`,`line_no`),
  KEY `to_delete` (`to_delete`),
  KEY `nav_primary` (`company`,`shop_code`,`language_code`,`line_no`),
  KEY `company` (`company`),
  KEY `rec_pay_index` (`shop_code`,`language_code`,`company`,`recurrent_payment_active`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_permissions_group
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_permissions_group`;

CREATE TABLE `shop_permissions_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` varchar(50) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_permissions_group_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_permissions_group_link`;

CREATE TABLE `shop_permissions_group_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `type` int(3) NOT NULL,
  `customer_no` varchar(20) NOT NULL,
  `item_no` varchar(20) NOT NULL,
  `permission_group_code` varchar(20) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `line_no` int(11) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timestamp_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `customer_no` (`customer_no`),
  KEY `item_no` (`item_no`),
  KEY `permission_group_code` (`permission_group_code`),
  KEY `category_shop_code` (`shop_code`,`language_code`,`line_no`),
  KEY `customer_no_2` (`customer_no`,`permission_group_code`),
  KEY `item_no_2` (`item_no`,`permission_group_code`),
  KEY `customer_no_3` (`customer_no`,`item_no`),
  KEY `permission_group_code_2` (`permission_group_code`,`shop_code`,`language_code`,`line_no`),
  KEY `query_1` (`item_no`,`type`,`company`,`permission_group_code`),
  KEY `query_2` (`customer_no`,`type`,`company`,`permission_group_code`),
  KEY `query_3` (`line_no`,`type`,`language_code`,`shop_code`,`company`,`permission_group_code`),
  KEY `query_4` (`item_no`,`customer_no`,`type`,`company`,`permission_group_code`),
  KEY `customer_no_4` (`customer_no`,`line_no`,`language_code`,`shop_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_return_reason
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_return_reason`;

CREATE TABLE `shop_return_reason` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `code` varchar(10) NOT NULL,
  `description` varchar(50) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `company` (`company`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_sales_header
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_sales_header`;

CREATE TABLE `shop_sales_header` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `order_no` int(11) NOT NULL,
  `shop_customer_id` int(11) NOT NULL,
  `shop_user_id` int(11) NOT NULL,
  `customer_no` varchar(20) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `user_email` varchar(80) NOT NULL,
  `user_phone_no` varchar(30) NOT NULL,
  `user_salutation` tinyint(1) NOT NULL DEFAULT '0',
  `salesperson_code` varchar(20) NOT NULL DEFAULT '',
  `order_date` date DEFAULT NULL,
  `requested_delivery_date` date DEFAULT NULL,
  `ship_to_name` varchar(50) NOT NULL,
  `ship_to_name_2` varchar(50) NOT NULL,
  `ship_to_address` varchar(50) NOT NULL,
  `ship_to_address_2` varchar(50) NOT NULL,
  `ship_to_city` varchar(50) NOT NULL,
  `ship_to_post_code` varchar(20) NOT NULL,
  `ship_to_country` varchar(10) NOT NULL,
  `ship_to_contact` varchar(50) NOT NULL,
  `ship_to_telephone_no` varchar(80) NOT NULL,
  `bill_to_customer_no` varchar(20) NOT NULL,
  `bill_to_name` varchar(50) NOT NULL,
  `bill_to_name_2` varchar(50) NOT NULL,
  `bill_to_address` varchar(50) NOT NULL,
  `bill_to_address_2` varchar(50) NOT NULL,
  `bill_to_post_code` varchar(20) NOT NULL,
  `bill_to_city` varchar(50) NOT NULL,
  `bill_to_country` varchar(10) NOT NULL,
  `your_reference` varchar(50) NOT NULL,
  `your_comment` varchar(320) NOT NULL,
  `subtotal` decimal(10,4) NOT NULL,
  `online_discount` decimal(10,2) NOT NULL,
  `online_discount_amount` decimal(10,4) NOT NULL,
  `invoice_discount` decimal(10,2) NOT NULL,
  `invoice_discount_amount` decimal(10,4) NOT NULL,
  `small_quantity_charge_amount` decimal(10,4) NOT NULL,
  `total` decimal(10,4) NOT NULL,
  `shipping_option_line_no` int(11) NOT NULL,
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_option_line_no` int(11) NOT NULL,
  `payment_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_transaction_id` varchar(40) NOT NULL,
  `pay_id` varchar(40) NOT NULL DEFAULT '',
  `bank_account_no` varchar(10) NOT NULL,
  `bank_branch_no` varchar(10) NOT NULL,
  `bank_name` varchar(80) NOT NULL,
  `currency_code` varchar(10) NOT NULL,
  `drop_shipment` tinyint(1) NOT NULL DEFAULT '0',
  `shipping_advice` tinyint(1) NOT NULL DEFAULT '0',
  `process_payment` tinyint(1) NOT NULL DEFAULT '0',
  `payment_processed` datetime DEFAULT NULL,
  `coupon_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `value_coupon` tinyint(1) NOT NULL DEFAULT '0',
  `coupon_code` varchar(20) NOT NULL,
  `coupon_header_code` varchar(20) NOT NULL,
  `shipping_coupon` tinyint(1) NOT NULL DEFAULT '0',
  `dc_order` tinyint(1) NOT NULL,
  `newsletter_registration` tinyint(1) NOT NULL DEFAULT '0',
  `bp_acc_owner` varchar(255) NOT NULL DEFAULT '',
  `bp_acc_nr` varchar(40) NOT NULL DEFAULT '',
  `bp_acc_iban` varchar(16) NOT NULL DEFAULT '',
  `bp_bank` varchar(255) NOT NULL DEFAULT '',
  `bp_invoice_ref` varchar(255) NOT NULL DEFAULT '',
  `bon_check` tinyint(1) NOT NULL,
  `sepa` tinyint(1) NOT NULL DEFAULT '0',
  `birthday` date DEFAULT NULL,
  `sur_name` varchar(30) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `salutation_title` varchar(30) NOT NULL,
  `to_nl_transfer` tinyint(1) NOT NULL,
  `order_error` tinyint(1) NOT NULL DEFAULT '1',
  `update_notify` tinyint(1) NOT NULL DEFAULT '0',
  `successful` tinyint(1) NOT NULL DEFAULT '0',
  `subscription_code` varchar(30) NOT NULL,
  `subscription_cust_line_no` int(11) NOT NULL,
  `update_insert` tinyint(1) NOT NULL DEFAULT '0',
  `marketplace_order` varchar(255) DEFAULT NULL,
  `marketplace_order_status` varchar(255) DEFAULT NULL,
  `marketplace_sales_channel` varchar(200) DEFAULT NULL,
  `marketplace_shipped` tinyint(4) NOT NULL DEFAULT '0',
  `vat_id` varchar(30) NOT NULL DEFAULT '',
  `payment_reference` varchar(255) NOT NULL DEFAULT '',
  `paypal_transaction_id` varchar(19) NOT NULL DEFAULT '',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `company` (`company`,`shop_code`,`language_code`),
  KEY `shop_customer_id` (`shop_customer_id`),
  KEY `customer_no` (`customer_no`),
  KEY `total` (`total`),
  KEY `update_insert` (`update_insert`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_sales_invoice_header
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_sales_invoice_header`;

CREATE TABLE `shop_sales_invoice_header` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `no` varchar(20) NOT NULL,
  `posting_date` date DEFAULT NULL,
  `order_no` varchar(20) NOT NULL,
  `webshop_order_no` int(11) NOT NULL,
  `your_reference` varchar(50) NOT NULL,
  `sell_to_customer_no` varchar(50) NOT NULL,
  `bill_to_customer_no` varchar(20) NOT NULL,
  `sell_to_name` varchar(50) NOT NULL,
  `sell_to_name_2` varchar(50) NOT NULL,
  `sell_to_address` varchar(50) NOT NULL,
  `sell_to_address_2` varchar(50) NOT NULL,
  `sell_to_post_code` varchar(20) NOT NULL,
  `sell_to_city` varchar(50) NOT NULL,
  `sell_to_country` varchar(50) NOT NULL,
  `sell_to_contact` varchar(50) NOT NULL,
  `bill_to_name` varchar(50) NOT NULL,
  `bill_to_name_2` varchar(50) NOT NULL,
  `bill_to_address` varchar(50) NOT NULL,
  `bill_to_address_2` varchar(50) NOT NULL,
  `bill_to_post_code` varchar(20) NOT NULL,
  `bill_to_city` varchar(50) NOT NULL,
  `bill_to_country` varchar(50) NOT NULL,
  `bill_to_contact` varchar(50) NOT NULL,
  `ship_to_name` varchar(50) NOT NULL,
  `ship_to_name_2` varchar(50) NOT NULL,
  `ship_to_address` varchar(50) NOT NULL,
  `ship_to_address_2` varchar(50) NOT NULL,
  `ship_to_post_code` varchar(20) NOT NULL,
  `ship_to_city` varchar(50) NOT NULL,
  `ship_to_country` varchar(50) NOT NULL,
  `ship_to_contact` varchar(50) NOT NULL,
  `amount` decimal(10,4) NOT NULL,
  `amount_including_vat` decimal(10,4) NOT NULL,
  `shipment_method` varchar(80) NOT NULL,
  `payment_terms` varchar(80) NOT NULL,
  `request_mail` varchar(80) NOT NULL,
  `request_shop_code` varchar(10) NOT NULL,
  `request_language_code` varchar(10) NOT NULL,
  `send_request` tinyint(1) NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nav_primary` (`company`,`no`),
  KEY `posting_date` (`posting_date`),
  KEY `webshop_order_no` (`webshop_order_no`),
  KEY `sell_to_customer_no` (`sell_to_customer_no`),
  KEY `bill_to_customer_no` (`bill_to_customer_no`),
  KEY `send_request` (`send_request`),
  KEY `to_delete` (`to_delete`),
  KEY `amount` (`amount`),
  KEY `company` (`company`),
  KEY `order_no` (`order_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_sales_invoice_line
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_sales_invoice_line`;

CREATE TABLE `shop_sales_invoice_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `document_no` varchar(20) NOT NULL,
  `line_no` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `no` varchar(20) NOT NULL,
  `description` varchar(50) NOT NULL,
  `description_2` varchar(50) NOT NULL,
  `unit_of_measure` varchar(10) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_price` decimal(10,4) NOT NULL,
  `line_amount` decimal(10,4) NOT NULL,
  `variant_code` varchar(10) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nav_primary` (`company`,`document_no`,`line_no`),
  KEY `type` (`type`),
  KEY `no` (`no`),
  KEY `quantity` (`quantity`),
  KEY `line_amount` (`line_amount`),
  KEY `to_delete` (`to_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_sales_line
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_sales_line`;

CREATE TABLE `shop_sales_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_sales_header_id` int(11) NOT NULL,
  `shop_item_id` int(11) NOT NULL,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `order_no` int(11) NOT NULL,
  `item_no` varchar(20) NOT NULL,
  `variant_code` varchar(10) NOT NULL,
  `description` varchar(50) NOT NULL,
  `summary` varchar(50) NOT NULL,
  `list_price` decimal(10,4) NOT NULL,
  `unit_price` decimal(10,4) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `line_amount` decimal(10,4) NOT NULL,
  `greeting_card_text` varchar(250) NOT NULL DEFAULT '',
  `package_for_item` varchar(30) NOT NULL DEFAULT '',
  `package_for_variant_code` varchar(10) NOT NULL DEFAULT '',
  `customized` tinyint(1) NOT NULL DEFAULT '0',
  `allow_invoice_disc` tinyint(1) NOT NULL DEFAULT '0',
  `is_coupon_item` tinyint(1) NOT NULL DEFAULT '0',
  `salesperson_discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `marketplace_line_id` varchar(255) DEFAULT NULL,
  `update_insert` tinyint(1) NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `shop_sales_header_id` (`shop_sales_header_id`),
  KEY `shop_item_id` (`shop_item_id`),
  KEY `company` (`company`),
  KEY `shop_code` (`shop_code`),
  KEY `language_code` (`language_code`),
  KEY `order_no` (`order_no`),
  KEY `item_no` (`item_no`),
  KEY `update_insert` (`update_insert`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_sales_line_customize
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_sales_line_customize`;

CREATE TABLE `shop_sales_line_customize` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_line_id` int(11) NOT NULL,
  `field_type` int(11) NOT NULL,
  `field_name` varchar(50) NOT NULL,
  `value` varchar(250) NOT NULL,
  `update_insert` tinyint(1) NOT NULL DEFAULT '1',
  `to_delete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_sales_price
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_sales_price`;

CREATE TABLE `shop_sales_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `item_no` varchar(20) NOT NULL,
  `variant_code` varchar(10) NOT NULL,
  `currency_code` varchar(10) NOT NULL,
  `discount_group` varchar(20) NOT NULL,
  `sales_type` tinyint(1) NOT NULL DEFAULT '0',
  `sales_code` varchar(20) NOT NULL,
  `unit_price` decimal(10,4) NOT NULL,
  `line_discount` decimal(10,4) NOT NULL,
  `minimum_quantity` decimal(10,4) NOT NULL,
  `unit_of_measure_code` varchar(10) NOT NULL,
  `allow_line_disc` tinyint(1) NOT NULL DEFAULT '0',
  `allow_invoice_disc` tinyint(1) NOT NULL DEFAULT '0',
  `starting_date` date DEFAULT NULL,
  `ending_date` date DEFAULT NULL,
  `price_includes_vat` tinyint(1) NOT NULL DEFAULT '0',
  `vat_bus_posting_group` varchar(10) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `to_delete` (`to_delete`),
  KEY `company` (`company`),
  KEY `type` (`type`),
  KEY `item_no` (`item_no`),
  KEY `nav_primary` (`company`,`type`,`item_no`,`sales_type`,`sales_code`,`starting_date`,`currency_code`,`variant_code`,`unit_of_measure_code`,`minimum_quantity`),
  KEY `unit_price` (`unit_price`),
  KEY `line_discount` (`line_discount`),
  KEY `minimum_quantity` (`minimum_quantity`),
  KEY `currency_code` (`currency_code`),
  KEY `sales_code` (`sales_code`),
  KEY `starting_date` (`starting_date`),
  KEY `ending_date` (`ending_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_sales_shipment_header
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_sales_shipment_header`;

CREATE TABLE `shop_sales_shipment_header` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `no` varchar(20) NOT NULL,
  `posting_date` date DEFAULT NULL,
  `order_no` varchar(20) NOT NULL,
  `webshop_order_no` int(11) NOT NULL,
  `your_reference` varchar(50) NOT NULL,
  `sell_to_customer_no` varchar(20) NOT NULL,
  `bill_to_customer_no` varchar(20) NOT NULL,
  `sell_to_name` varchar(50) NOT NULL,
  `sell_to_name_2` varchar(50) NOT NULL,
  `sell_to_address` varchar(50) NOT NULL,
  `sell_to_address_2` varchar(50) NOT NULL,
  `sell_to_post_code` varchar(20) NOT NULL,
  `sell_to_city` varchar(50) NOT NULL,
  `sell_to_country` varchar(50) NOT NULL,
  `sell_to_contact` varchar(50) NOT NULL,
  `bill_to_name` varchar(50) NOT NULL,
  `bill_to_name_2` varchar(50) NOT NULL,
  `bill_to_address` varchar(50) NOT NULL,
  `bill_to_address_2` varchar(50) NOT NULL,
  `bill_to_post_code` varchar(20) NOT NULL,
  `bill_to_city` varchar(50) NOT NULL,
  `bill_to_country` varchar(50) NOT NULL,
  `bill_to_contact` varchar(50) NOT NULL,
  `ship_to_name` varchar(50) NOT NULL,
  `ship_to_name_2` varchar(50) NOT NULL,
  `ship_to_address` varchar(50) NOT NULL,
  `ship_to_address_2` varchar(50) NOT NULL,
  `ship_to_post_code` varchar(20) NOT NULL,
  `ship_to_city` varchar(50) NOT NULL,
  `ship_to_country` varchar(50) NOT NULL,
  `ship_to_contact` varchar(50) NOT NULL,
  `shipment_method` varchar(80) NOT NULL,
  `request_mail` varchar(80) NOT NULL,
  `request_shop_code` varchar(10) NOT NULL,
  `request_language_code` varchar(10) NOT NULL,
  `send_request` tinyint(1) NOT NULL DEFAULT '0',
  `return_order_insert` tinyint(1) NOT NULL DEFAULT '0',
  `return_order` tinyint(1) NOT NULL DEFAULT '0',
  `return_shop_code` varchar(20) NOT NULL,
  `return_language_code` varchar(20) NOT NULL,
  `return_order_reference` varchar(80) NOT NULL,
  `return_order_shop_no` varchar(30) NOT NULL,
  `return_order_token` varchar(255) NOT NULL,
  `marketplace_update` tinyint(1) NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nav_primary` (`company`,`no`),
  KEY `posting_date` (`posting_date`),
  KEY `webshop_order_no` (`webshop_order_no`),
  KEY `sell_to_customer_no` (`sell_to_customer_no`),
  KEY `bill_to_customer_no` (`bill_to_customer_no`),
  KEY `send_request` (`send_request`),
  KEY `to_delete` (`to_delete`),
  KEY `company` (`company`),
  KEY `order_no` (`order_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_sales_shipment_line
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_sales_shipment_line`;

CREATE TABLE `shop_sales_shipment_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `document_no` varchar(20) NOT NULL,
  `line_no` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `no` varchar(20) NOT NULL,
  `description` varchar(50) NOT NULL,
  `description_2` varchar(50) NOT NULL,
  `unit_of_measure` varchar(10) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `quantity_returned` decimal(10,4) NOT NULL,
  `return_order_insert` tinyint(1) NOT NULL DEFAULT '0',
  `return_order` tinyint(1) NOT NULL,
  `return_quantity` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `return_reason_code` varchar(20) NOT NULL,
  `variant_code` varchar(10) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nav_primary` (`company`,`document_no`,`line_no`),
  KEY `company` (`company`),
  KEY `type` (`type`),
  KEY `no` (`no`),
  KEY `to_delete` (`to_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_salesperson
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_salesperson`;

CREATE TABLE `shop_salesperson` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `salesperson_code` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(80) NOT NULL,
  `phone_no` varchar(80) NOT NULL,
  `max_discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `password` varchar(255) NOT NULL,
  `image` varchar(128) NOT NULL,
  `position` varchar(30) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nav_primary` (`company`,`salesperson_code`),
  KEY `to_delete` (`to_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_search_query
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_search_query`;

CREATE TABLE `shop_search_query` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `search_query` varchar(100) NOT NULL,
  `no_of_results` int(11) NOT NULL,
  `search_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company` (`company`,`shop_code`,`search_query`,`search_datetime`),
  KEY `company_2` (`company`),
  KEY `shop_code` (`shop_code`),
  KEY `search_query` (`search_query`),
  KEY `search_datetime` (`search_datetime`),
  KEY `no_of_results` (`no_of_results`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_setup
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_setup`;

CREATE TABLE `shop_setup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `nas_email_text_module` varchar(20) NOT NULL,
  `nas_email_recipient` varchar(80) NOT NULL,
  `nas_email_recipient_2` varchar(80) NOT NULL,
  `nas_email_sender` varchar(80) NOT NULL,
  `default_currency_code` varchar(10) NOT NULL,
  `last_datetime_nav_updated` datetime NOT NULL,
  `last_datetime_solr_updated` datetime NOT NULL,
  `default_shipping_class_priority` int(11) NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `company` (`company`),
  KEY `to_delete` (`to_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_shipment_address
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_shipment_address`;

CREATE TABLE `shop_shipment_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `customer_no` varchar(20) NOT NULL,
  `code` varchar(10) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL,
  `name_2` varchar(50) NOT NULL DEFAULT '',
  `contact` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL,
  `address_2` varchar(50) NOT NULL DEFAULT '',
  `address_street` varchar(50) NOT NULL,
  `address_no` varchar(10) NOT NULL,
  `post_code` varchar(20) NOT NULL,
  `city` varchar(50) NOT NULL,
  `country` varchar(10) NOT NULL,
  `phone_no` varchar(80) NOT NULL DEFAULT '',
  `surname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `company_name` varchar(45) NOT NULL DEFAULT '',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nav_primary` (`company`,`customer_no`,`code`),
  KEY `to_delete` (`to_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_shipping_option
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_shipping_option`;

CREATE TABLE `shop_shipping_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shipping_group_code` varchar(20) NOT NULL,
  `line_no` int(11) NOT NULL,
  `shipping_agent_code` varchar(10) NOT NULL,
  `shipping_agent_service_code` varchar(10) NOT NULL,
  `weight_from` decimal(10,2) NOT NULL,
  `weight_to` decimal(10,2) NOT NULL,
  `amount_from` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount_to` decimal(10,2) NOT NULL DEFAULT '0.00',
  `valid_from` date DEFAULT NULL,
  `valid_to` date DEFAULT NULL,
  `shipping_cost` decimal(10,2) NOT NULL,
  `exemption` decimal(10,2) NOT NULL,
  `description` varchar(50) NOT NULL,
  `logo` varchar(100) NOT NULL DEFAULT '',
  `content` varchar(250) NOT NULL DEFAULT '',
  `sorting` int(11) NOT NULL,
  `shipping_class_code` varchar(20) DEFAULT NULL,
  `shipping_zone_code` varchar(20) NOT NULL DEFAULT '',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `company` (`company`),
  KEY `to_delete` (`to_delete`),
  KEY `weight_from` (`weight_from`),
  KEY `weight_to` (`weight_to`),
  KEY `shipping_cost` (`shipping_cost`),
  KEY `nav_primary` (`company`,`shipping_group_code`,`line_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_shop
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_shop`;

CREATE TABLE `shop_shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `code` varchar(10) NOT NULL,
  `description` varchar(50) NOT NULL,
  `shop_typ` tinyint(1) NOT NULL DEFAULT '0',
  `login_type` tinyint(1) NOT NULL,
  `default_language_code` varchar(10) NOT NULL,
  `use_items_from_shop_code` varchar(10) NOT NULL,
  `use_categorys_from_shop_code` varchar(10) NOT NULL,
  `variant_typ` tinyint(1) NOT NULL DEFAULT '0',
  `use_customer_from_shop_code` varchar(10) NOT NULL,
  `email_sender` varchar(80) NOT NULL,
  `email_order_mail_1_copy` varchar(80) NOT NULL,
  `computop_merchant_id` varchar(30) NOT NULL,
  `computop_password` varchar(40) NOT NULL,
  `computop_hmac_key` varchar(250) NOT NULL,
  `show_vendor_filter` tinyint(1) NOT NULL DEFAULT '0',
  `show_invoice_discount` tinyint(1) NOT NULL DEFAULT '0',
  `small_quantity_charge` decimal(10,2) NOT NULL,
  `small_quantity_charge_limit` decimal(10,2) NOT NULL,
  `online_discount` decimal(10,2) NOT NULL,
  `retail_price_typ` tinyint(1) NOT NULL DEFAULT '0',
  `cust_price_group_retail_price` varchar(10) NOT NULL,
  `cust_disc_group_retail_price` varchar(10) NOT NULL,
  `base_price_typ` tinyint(1) NOT NULL DEFAULT '0',
  `cust_price_group_base_price` varchar(10) NOT NULL,
  `cust_disc_group_base_price` varchar(10) NOT NULL,
  `campain_no` varchar(20) NOT NULL,
  `prices_including_vat` tinyint(1) NOT NULL DEFAULT '0',
  `vat_bus_posting_group` varchar(10) NOT NULL,
  `select_req_delivery_date` tinyint(1) NOT NULL DEFAULT '0',
  `addition_req_delivery_date` int(11) NOT NULL,
  `cross_price_typ` tinyint(1) NOT NULL DEFAULT '0',
  `show_filters` tinyint(1) NOT NULL DEFAULT '0',
  `show_comparison` tinyint(1) NOT NULL DEFAULT '0',
  `show_filter_on_card` tinyint(1) NOT NULL DEFAULT '0',
  `attribute_frige_shipping` varchar(45) NOT NULL,
  `attribute_bio_food` varchar(45) NOT NULL,
  `g_ftp_url` varchar(50) NOT NULL,
  `g_ftp_user` varchar(25) NOT NULL,
  `g_ftp_passwd` varchar(25) NOT NULL,
  `g_channel_title` varchar(100) NOT NULL,
  `g_channel_link` varchar(100) NOT NULL,
  `g_channel_desc` varchar(100) NOT NULL,
  `g_product_category` varchar(250) NOT NULL,
  `max_days_shipment_returnable` int(11) NOT NULL,
  `attribute_brand` varchar(45) NOT NULL,
  `extended_search` tinyint(1) NOT NULL DEFAULT '0',
  `forerun_subscr_order_create` varchar(50) DEFAULT NULL,
  `forerun_payment_capture` varchar(50) DEFAULT NULL,
  `order_options_display` tinyint(1) NOT NULL DEFAULT '0',
  `order_options_sorting` tinyint(1) NOT NULL DEFAULT '0',
  `inventory_display` tinyint(1) NOT NULL DEFAULT '0',
  `paydirekt_api_key` varchar(64) NOT NULL,
  `vat_identifier_1` varchar(10) DEFAULT NULL,
  `vat_identifier_2` varchar(10) DEFAULT NULL,
  `vat_identifier_3` varchar(10) DEFAULT NULL,
  `paypal_email` varchar(255) NOT NULL,
  `shop_location_zip` int(5) NOT NULL,
  `marketplace_type` int(11) NOT NULL,
  `shop_url` varchar(100) NOT NULL,
  `item_availability` tinyint(1) NOT NULL DEFAULT '0',
  `us_sales_tax_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `newsletter_account` varchar(45) NOT NULL,
  `newsletter_login` varchar(45) NOT NULL,
  `newsletter_password` varchar(45) NOT NULL,
  `newsletter_group` varchar(45) NOT NULL,
  `newsletter_segment` varchar(50) NOT NULL,
  `payolution_invoice_copy` varchar(100) NOT NULL DEFAULT '',
  `no_of_items_per_page` int(3) NOT NULL DEFAULT '16',
  `shipping_group_code` varchar(20) NOT NULL DEFAULT '',
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `to_delete` (`to_delete`),
  KEY `company` (`company`),
  KEY `nav_primary` (`company`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_subscr_customer_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_subscr_customer_link`;

CREATE TABLE `shop_subscr_customer_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `subscription_code` varchar(30) NOT NULL,
  `customer_no` varchar(30) NOT NULL,
  `webshop_order_email` varchar(100) NOT NULL,
  `line_no` int(11) NOT NULL,
  `bill_to_name` varchar(50) DEFAULT NULL,
  `bill_to_name_2` varchar(50) DEFAULT NULL,
  `bill_to_address` varchar(50) DEFAULT NULL,
  `bill_to_address_2` varchar(50) DEFAULT NULL,
  `bill_to_city` varchar(30) DEFAULT NULL,
  `ship_to_name` varchar(50) DEFAULT NULL,
  `ship_to_name_2` varchar(50) DEFAULT NULL,
  `ship_to_address` varchar(50) DEFAULT NULL,
  `ship_to_address_2` varchar(50) DEFAULT NULL,
  `ship_to_city` varchar(30) DEFAULT NULL,
  `currency_code` varchar(10) DEFAULT NULL,
  `bill_to_post_code` varchar(20) DEFAULT NULL,
  `bill_to_county` varchar(30) DEFAULT NULL,
  `bill_to_country_region_code` varchar(10) DEFAULT NULL,
  `ship_to_post_code` varchar(20) DEFAULT NULL,
  `ship_to_county` varchar(30) DEFAULT NULL,
  `ship_to_country_region_code` varchar(10) DEFAULT NULL,
  `webshop_shop_code` varchar(10) DEFAULT NULL,
  `webshop_language_code` varchar(10) DEFAULT NULL,
  `webshop_pay_opt_line_no` int(11) DEFAULT NULL,
  `webshop_ship_opt_line_no` int(11) DEFAULT NULL,
  `customer_pseudo_pay_line_no` int(11) DEFAULT NULL,
  `signup_date` date NOT NULL,
  `cancellation_date` date DEFAULT '0000-00-00',
  `next_order_creation_date` date DEFAULT '0000-00-00',
  `next_order_w_payment_date` date DEFAULT '0000-00-00',
  `no_of_turns` int(11) NOT NULL,
  `no_of_turns_processed` int(11) NOT NULL,
  `no_of_turns_left` int(11) NOT NULL,
  `user_name` varchar(50) DEFAULT NULL,
  `subscription_item_no` varchar(30) DEFAULT NULL,
  `next_sequence_step_line_no` int(11) NOT NULL,
  `subscription_qty_per_turn` decimal(12,4) NOT NULL,
  `initial_order_no` varchar(20) NOT NULL,
  `update_insert` tinyint(1) NOT NULL,
  `to_delete` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nav_primary` (`customer_no`,`webshop_order_email`,`company`,`subscription_code`,`line_no`),
  KEY `to_delete` (`to_delete`),
  KEY `update_insert` (`update_insert`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_subscription_header
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_subscription_header`;

CREATE TABLE `shop_subscription_header` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `code` varchar(30) CHARACTER SET latin1 NOT NULL,
  `type` int(1) NOT NULL,
  `description` varchar(80) CHARACTER SET latin1 DEFAULT NULL,
  `item_no_subscription_item` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `payment_type` int(2) NOT NULL,
  `payment_type_description` varchar(50) NOT NULL,
  `start_type` int(1) NOT NULL,
  `start_date_formula` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `turn_interval_formula` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `turn_interval_description` varchar(50) NOT NULL,
  `min_no_of_turns` int(2) DEFAULT NULL,
  `max_no_of_turns` int(2) DEFAULT NULL,
  `subscription_duration` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `no_of_sequence_steps` int(3) DEFAULT NULL,
  `no_of_items` int(11) DEFAULT NULL,
  `no_of_customers` int(11) DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  `valid_from` date NOT NULL,
  `valid_to` date NOT NULL,
  `cancel_period_date_formula` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `cancel_period_description` varchar(50) NOT NULL,
  `price_per_billing_interval` decimal(10,4) DEFAULT NULL,
  `orderable` tinyint(1) NOT NULL,
  `orderable_from` date NOT NULL,
  `orderable_to` date NOT NULL,
  `shop_code` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `language_code` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `all_shops` tinyint(1) DEFAULT NULL,
  `all_languages` tinyint(1) DEFAULT NULL,
  `first_order_email_text` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `shipment_email_text` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `last_ship_email_text` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `shipping_option_line_no` int(11) DEFAULT NULL,
  `ref_pay_date_from_start_date` varchar(45) DEFAULT NULL,
  `allow_late_entry` tinyint(1) DEFAULT '0',
  `to_delete` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nav_primary` (`code`,`company`),
  KEY `shop_orderable` (`all_shops`,`all_languages`,`company`,`shop_code`,`language_code`,`orderable`,`orderable_from`,`orderable_to`),
  KEY `to_delete` (`to_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_subscription_item_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_subscription_item_link`;

CREATE TABLE `shop_subscription_item_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `subscription_code` varchar(30) NOT NULL,
  `link_to` tinyint(1) NOT NULL,
  `item_no` varchar(30) NOT NULL,
  `variant_code` varchar(10) NOT NULL,
  `subscr_seq_step_line_no` int(11) DEFAULT NULL,
  `description` varchar(80) DEFAULT NULL,
  `fix_price` decimal(10,4) DEFAULT NULL,
  `discount_percent` decimal(5,2) DEFAULT NULL,
  `min_quantity` decimal(12,4) DEFAULT NULL,
  `max_quantity` decimal(12,4) DEFAULT NULL,
  `quantity` decimal(12,4) DEFAULT NULL,
  `price_includes_vat` tinyint(1) NOT NULL DEFAULT '0',
  `to_delete` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nav_primary` (`subscription_code`,`company`,`link_to`,`item_no`,`variant_code`,`subscr_seq_step_line_no`),
  KEY `to_delete` (`to_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_subscription_sequence_step
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_subscription_sequence_step`;

CREATE TABLE `shop_subscription_sequence_step` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `subscription_code` varchar(30) NOT NULL,
  `line_no` int(11) NOT NULL,
  `fix_date` date DEFAULT NULL,
  `interval` varchar(45) DEFAULT NULL,
  `no_of_items` int(11) DEFAULT NULL,
  `to_delete` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nav_primary` (`subscription_code`,`company`,`line_no`),
  KEY `to_delete` (`to_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_text_module
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_text_module`;

CREATE TABLE `shop_text_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `code` varchar(30) NOT NULL,
  `description` varchar(80) NOT NULL,
  `content` text NOT NULL,
  `attachment_1` varchar(250) NOT NULL,
  `attachment_2` varchar(250) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nav_primary` (`company`,`code`),
  KEY `to_delete` (`to_delete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_used_ret_shipment_nos
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_used_ret_shipment_nos`;

CREATE TABLE `shop_used_ret_shipment_nos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ret_order_no` varchar(45) NOT NULL,
  `datetime_used` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ret_order_no_UNIQUE` (`ret_order_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_user`;

CREATE TABLE `shop_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `customer_no` varchar(20) NOT NULL,
  `shop_shipment_address_id` int(11) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(80) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `main_user` tinyint(1) NOT NULL DEFAULT '0',
  `right_user_management` tinyint(1) NOT NULL DEFAULT '1',
  `right_order_history` tinyint(1) NOT NULL DEFAULT '1',
  `right_order` tinyint(1) NOT NULL DEFAULT '1',
  `right_return_order` tinyint(1) NOT NULL DEFAULT '0',
  `last_visitor_id` int(11) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `company` (`company`,`customer_no`),
  KEY `company_2` (`company`),
  KEY `customer_no` (`customer_no`),
  KEY `main_user` (`main_user`),
  KEY `login` (`login`),
  KEY `password` (`password`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_user_basket
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_user_basket`;

CREATE TABLE `shop_user_basket` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_user_id` int(11) NOT NULL,
  `shop_visitor_id` int(11) NOT NULL,
  `shop_item_id` int(11) NOT NULL,
  `variant_code` varchar(10) NOT NULL,
  `item_quantity` decimal(10,2) NOT NULL,
  `allow_invoice_disc` tinyint(1) NOT NULL DEFAULT '0',
  `customer_price` decimal(10,4) NOT NULL,
  `customer_price_wo_vat` decimal(10,4) DEFAULT NULL,
  `is_coupon_item` tinyint(1) NOT NULL DEFAULT '0',
  `changed_to_minimum` tinyint(1) NOT NULL DEFAULT '0',
  `changed_to_vpe` tinyint(1) NOT NULL DEFAULT '0',
  `package_for_item` int(11) NOT NULL DEFAULT '0',
  `insert_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `global_query` (`id`,`shop_visitor_id`),
  KEY `shop_user_id` (`shop_user_id`),
  KEY `shop_item_id` (`shop_item_id`),
  KEY `insert_datetime` (`insert_datetime`),
  KEY `line_amt_query` (`shop_visitor_id`,`customer_price`,`item_quantity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_user_basket_customize
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_user_basket_customize`;

CREATE TABLE `shop_user_basket_customize` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_basked_id` int(11) NOT NULL,
  `field_length` int(10) NOT NULL,
  `field_type` int(11) NOT NULL,
  `field_name` varchar(50) NOT NULL,
  `field_description` varchar(200) NOT NULL,
  `value` varchar(250) NOT NULL,
  `customization_hash` varchar(32) NOT NULL,
  `item_customization_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_user_basket_header_new
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_user_basket_header_new`;

CREATE TABLE `shop_user_basket_header_new` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) DEFAULT NULL,
  `shop_code` varchar(10) DEFAULT NULL,
  `shop_language_code` varchar(10) DEFAULT NULL,
  `main_basket` tinyint(1) NOT NULL,
  `shop_user_id` int(11) NOT NULL,
  `shop_visitor_id` int(11) NOT NULL,
  `description` varchar(80) NOT NULL,
  `basket_total` decimal(10,4) NOT NULL,
  `basket_total_gross` decimal(10,4) NOT NULL,
  `basket_total_net` decimal(10,4) NOT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modify_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  `last_hash` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `visitor_main` (`shop_visitor_id`,`main_basket`,`id`),
  UNIQUE KEY `user_main` (`shop_user_id`,`main_basket`,`id`),
  UNIQUE KEY `visitorid_query` (`shop_visitor_id`,`id`),
  UNIQUE KEY `userid_query` (`shop_user_id`,`id`),
  UNIQUE KEY `id_query_userid` (`id`,`shop_user_id`),
  UNIQUE KEY `id_query_visitorid` (`id`,`shop_visitor_id`),
  KEY `shop_user_id` (`shop_user_id`),
  KEY `shop_visitor_id` (`shop_visitor_id`),
  KEY `create_timestamp` (`timestamp_created`),
  KEY `modify_timestamp` (`modify_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_user_basket_line_new
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_user_basket_line_new`;

CREATE TABLE `shop_user_basket_line_new` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `header_id` int(11) NOT NULL,
  `orderable_item_type` tinyint(1) DEFAULT NULL,
  `identifier` varchar(30) NOT NULL,
  `subidentifier` varchar(30) NOT NULL,
  `description` varchar(80) NOT NULL,
  `quantity` decimal(10,4) NOT NULL,
  `unit_price` decimal(10,4) NOT NULL,
  `vat_percent` decimal(10,4) NOT NULL,
  `vat_amount` decimal(10,4) NOT NULL,
  `vat_prod_posting_group` varchar(20) NOT NULL,
  `unit_price_gross` decimal(10,4) NOT NULL,
  `unit_price_net` decimal(10,4) NOT NULL,
  `line_amount` decimal(10,4) NOT NULL,
  `line_amount_gross` decimal(10,4) NOT NULL,
  `line_amount_net` decimal(10,4) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modify_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `to_delete` tinyint(1) NOT NULL,
  `parent_item_id` int(11) NOT NULL DEFAULT '0',
  `line_no` int(11) NOT NULL,
  `qty_protected` tinyint(1) NOT NULL DEFAULT '0',
  `unit_price_protected` tinyint(1) NOT NULL DEFAULT '0',
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `shop_language_code` varchar(10) NOT NULL,
  `customization_hash` varchar(32) NOT NULL,
  `greeting_card_text` varchar(255) NOT NULL,
  `links_to_entity_key` varchar(250) NOT NULL,
  `entity_link_type` int(11) NOT NULL,
  `entity_key` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `header_id_id` (`header_id`,`id`),
  KEY `header_item_3` (`header_id`,`orderable_item_type`,`identifier`,`subidentifier`,`line_no`),
  KEY `line_no` (`header_id`,`line_no`),
  KEY `header_id` (`header_id`),
  KEY `header_id_line_amnt` (`header_id`,`line_amount`),
  KEY `create_timestamp` (`timestamp_created`),
  KEY `header_create` (`header_id`,`timestamp_created`),
  KEY `header_modify` (`header_id`,`modify_timestamp`),
  KEY `header_item_2` (`header_id`,`orderable_item_type`,`item_id`,`subidentifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_user_basket_value_source_new
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_user_basket_value_source_new`;

CREATE TABLE `shop_user_basket_value_source_new` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `applied_to_header_id` int(11) NOT NULL,
  `applied_to_line_id` int(11) DEFAULT NULL,
  `created_by_source_type` varchar(30) DEFAULT NULL,
  `created_by_source_id` int(11) DEFAULT NULL,
  `qty_set_by_source_type` varchar(30) DEFAULT NULL,
  `qty_set_by_source_id` int(11) DEFAULT NULL,
  `qty_before_setting` decimal(10,4) DEFAULT NULL,
  `qty_after_setting` decimal(10,4) DEFAULT NULL,
  `unit_price_set_by_source_type` varchar(30) DEFAULT NULL,
  `unit_price_set_by_source_id` int(11) DEFAULT NULL,
  `unit_price_before_setting` decimal(10,4) DEFAULT NULL,
  `unit_price_after_setting` decimal(10,4) DEFAULT NULL,
  `invoice_discount_source_type` varchar(30) DEFAULT NULL,
  `invoice_discount_source_id` int(11) DEFAULT NULL,
  `invoice_discount_applied` decimal(10,4) DEFAULT NULL,
  `basket_total_before_invoice_discount` decimal(10,4) DEFAULT NULL,
  `basket_total_after_invoice_discount` decimal(10,4) DEFAULT NULL,
  `timestamp_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modify_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `to_delete` tinyint(1) NOT NULL,
  `creation_notification` varchar(120) NOT NULL,
  `price_notification` varchar(120) NOT NULL,
  `qty_notification` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `line_created_by_type` (`created_by_source_type`,`applied_to_header_id`,`applied_to_line_id`,`created_by_source_id`),
  UNIQUE KEY `line_created_by_source` (`created_by_source_type`,`created_by_source_id`,`applied_to_header_id`,`applied_to_line_id`),
  UNIQUE KEY `header_line_creation` (`applied_to_header_id`,`created_by_source_type`,`applied_to_line_id`,`created_by_source_id`),
  KEY `line_qty_set_by_type` (`qty_set_by_source_type`,`applied_to_header_id`,`applied_to_line_id`,`qty_set_by_source_id`),
  KEY `line_qty_set_by_source` (`qty_set_by_source_type`,`qty_set_by_source_id`,`applied_to_header_id`,`applied_to_line_id`),
  KEY `line_unit_price_set_by_type` (`unit_price_set_by_source_type`,`applied_to_header_id`,`applied_to_line_id`,`unit_price_set_by_source_id`,`unit_price_before_setting`,`unit_price_after_setting`),
  KEY `line_unit_price_set_by_source` (`unit_price_set_by_source_type`,`unit_price_set_by_source_id`,`applied_to_header_id`,`applied_to_line_id`,`unit_price_before_setting`,`unit_price_after_setting`),
  KEY `invoice_disc_set_by_type` (`invoice_discount_source_type`,`applied_to_header_id`,`invoice_discount_source_id`,`basket_total_before_invoice_discount`,`basket_total_after_invoice_discount`),
  KEY `invoice_disc_set_by_source` (`invoice_discount_source_type`,`invoice_discount_source_id`,`applied_to_header_id`,`basket_total_before_invoice_discount`,`basket_total_after_invoice_discount`),
  KEY `header_line_qty_setting` (`applied_to_header_id`,`qty_set_by_source_type`,`applied_to_line_id`,`qty_set_by_source_id`),
  KEY `header_line_unit_price_setting` (`applied_to_header_id`,`unit_price_set_by_source_type`,`applied_to_line_id`,`unit_price_set_by_source_id`,`unit_price_before_setting`,`unit_price_after_setting`),
  KEY `header_invoice_disc_setting` (`applied_to_header_id`,`invoice_discount_source_type`,`basket_total_before_invoice_discount`,`basket_total_after_invoice_discount`,`invoice_discount_source_id`),
  KEY `header` (`applied_to_header_id`),
  KEY `line` (`applied_to_header_id`,`applied_to_line_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_user_customer_top_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_user_customer_top_items`;

CREATE TABLE `shop_user_customer_top_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_user_id` int(11) NOT NULL,
  `shop_customer_id` int(11) NOT NULL,
  `top_items_json_data` mediumtext NOT NULL,
  `last_timestamp_updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`shop_user_id`,`shop_customer_id`),
  KEY `shop_user_id` (`shop_user_id`),
  KEY `shop_customer_id` (`shop_customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_user_favorites
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_user_favorites`;

CREATE TABLE `shop_user_favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_user_id` int(11) NOT NULL,
  `shop_visitor_id` int(11) NOT NULL,
  `shop_item_id` varchar(45) NOT NULL,
  `insert_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `global_query` (`id`,`shop_visitor_id`),
  KEY `shop_user_id` (`shop_user_id`),
  KEY `shop_item_id` (`shop_item_id`),
  KEY `insert_datetime` (`insert_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_vat_posting_setup
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_vat_posting_setup`;

CREATE TABLE `shop_vat_posting_setup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `vat_bus_posting_group` varchar(10) NOT NULL,
  `vat_prod_posting_group` varchar(10) NOT NULL,
  `vat_percent` decimal(10,4) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nav_primary` (`company`,`vat_bus_posting_group`,`vat_prod_posting_group`),
  KEY `to_delete` (`to_delete`),
  KEY `company` (`company`),
  KEY `vat_prod_posting_group` (`vat_prod_posting_group`),
  KEY `vat_bus_posting_group` (`vat_bus_posting_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump shop_vendor
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_vendor`;

CREATE TABLE `shop_vendor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `vendor_no` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `to_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nav_primary` (`company`,`vendor_no`),
  KEY `to_delete` (`to_delete`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;





















# Tabellen-Dump slidecontent_header
# ------------------------------------------------------------

DROP TABLE IF EXISTS `slidecontent_header`;

CREATE TABLE `slidecontent_header` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_language_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `modified_date` int(11) NOT NULL,
  `modified_user` int(11) NOT NULL,
  `collection_header` tinyint(4) NOT NULL DEFAULT '0',
  `all_languages` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump slidecontent_line
# ------------------------------------------------------------

DROP TABLE IF EXISTS `slidecontent_line`;

CREATE TABLE `slidecontent_line` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `header_id` int(11) NOT NULL,
  `headline` varchar(250) NOT NULL,
  `content` mediumtext NOT NULL,
  `sorting` int(10) unsigned NOT NULL,
  `modified_date` int(11) NOT NULL,
  `modified_user` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump slideshow_header
# ------------------------------------------------------------

DROP TABLE IF EXISTS `slideshow_header`;

CREATE TABLE `slideshow_header` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_language_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `width` int(5) NOT NULL,
  `height` int(5) NOT NULL,
  `effect` varchar(10) NOT NULL,
  `arrows` varchar(10) NOT NULL,
  `eff_interval` varchar(4) NOT NULL,
  `effect_duration` varchar(3) NOT NULL,
  `text_effect` varchar(10) NOT NULL,
  `text_pos` varchar(15) NOT NULL,
  `modified_date` int(11) NOT NULL,
  `modified_user` int(11) NOT NULL,
  `collection_header` tinyint(4) NOT NULL DEFAULT '0',
  `all_languages` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump slideshow_line
# ------------------------------------------------------------

DROP TABLE IF EXISTS `slideshow_line`;

CREATE TABLE `slideshow_line` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `header_id` int(11) NOT NULL,
  `preview` varchar(250) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `description` varchar(250) NOT NULL,
  `headline` varchar(250) NOT NULL,
  `text` varchar(250) NOT NULL,
  `text2` varchar(250) NOT NULL,
  `link` varchar(250) NOT NULL,
  `info` varchar(250) NOT NULL,
  `bg_position` int(11) DEFAULT NULL,
  `sorting` int(11) NOT NULL,
  `modified_date` int(11) NOT NULL,
  `modified_user` int(11) NOT NULL,
  `validity_from` date DEFAULT NULL,
  `validity_to` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump solr_materialized
# ------------------------------------------------------------

DROP TABLE IF EXISTS `solr_materialized`;

CREATE TABLE `solr_materialized` (
  `id` int(11) NOT NULL DEFAULT '0',
  `company` varchar(30) CHARACTER SET utf8 NOT NULL,
  `shop_code` varchar(10) CHARACTER SET utf8 NOT NULL,
  `category_shop_code` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `language_code` varchar(10) CHARACTER SET utf8 NOT NULL,
  `default_language_code` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `item_no` varchar(20) CHARACTER SET utf8 NOT NULL,
  `parent_item_no` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `is_parent` int(11) NOT NULL DEFAULT '0',
  `description` varchar(50) CHARACTER SET utf8 NOT NULL,
  `summary` varchar(250) CHARACTER SET utf8 NOT NULL,
  `variant_type` varchar(50) CHARACTER SET utf8 NOT NULL,
  `manufacturer` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `retail_price` decimal(10,4) NOT NULL,
  `base_price` decimal(10,4) NOT NULL,
  `inventory` decimal(10,2) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `cat_line_nos` mediumtext CHARACTER SET utf8,
  `cat_names` mediumtext CHARACTER SET utf8,
  `cat_codes` mediumtext CHARACTER SET utf8,
  `cat_search_terms` mediumtext CHARACTER SET utf8,
  `canonical_url` varchar(1024) CHARACTER SET utf8 NOT NULL,
  `order_ranking` decimal(10,2) NOT NULL,
  `main_preview_image_filename` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `linked_item_nos` mediumtext CHARACTER SET utf8,
  `validity_from` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `validity_to` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `reference_nos` mediumtext CHARACTER SET utf8,
  `long_descriptions` mediumtext CHARACTER SET utf8,
  `attribute_pairs` mediumtext CHARACTER SET utf8,
  `attribute_codes` mediumtext CHARACTER SET utf8,
  `attribute_descriptions` mediumtext CHARACTER SET utf8,
  `hersteller_atto` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `loft_atto` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `schaft_atto` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `fitting_atto` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `flex_atto` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `hand_atto` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `typ_atto` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `unisex_attb` bigint(4) DEFAULT NULL,
  `permitted_groups` mediumtext,
  `permitted_customers` mediumtext,
  `nav_variant_descriptions` mediumtext,
  `nav_variant_codes` mediumtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_no` (`item_no`,`shop_code`,`language_code`,`company`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Tabellen-Dump textcontent_header
# ------------------------------------------------------------

DROP TABLE IF EXISTS `textcontent_header`;

CREATE TABLE `textcontent_header` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_language_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `content` mediumtext NOT NULL,
  `modified_date` int(11) NOT NULL,
  `modified_user` int(11) NOT NULL,
  `collection_header` tinyint(4) NOT NULL DEFAULT '0',
  `all_languages` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;







# Tabellen-Dump tokentable
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tokentable`;

CREATE TABLE `tokentable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(100) DEFAULT NULL,
  `document_id` int(11) DEFAULT NULL,
  `importance` decimal(10,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ukey` (`token`,`document_id`,`importance`),
  KEY `token` (`token`),
  KEY `document` (`document_id`),
  KEY `tokenImportance` (`token`,`importance`),
  KEY `tokenDocument` (`token`,`document_id`),
  FULLTEXT KEY `tokenFulltext` (`token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump user_new_password_request
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_new_password_request`;

CREATE TABLE `user_new_password_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(30) NOT NULL,
  `shop_code` varchar(10) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `random_hash` varchar(250) NOT NULL,
  `create_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_index` (`company`,`shop_code`,`user_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Tabellen-Dump youtube_header
# ------------------------------------------------------------

DROP TABLE IF EXISTS `youtube_header`;

CREATE TABLE `youtube_header` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `main_language_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `youtube_id` varchar(255) NOT NULL,
  `modified_date` int(11) NOT NULL,
  `modified_user` int(11) NOT NULL,
  `collection_header` tinyint(4) NOT NULL DEFAULT '0',
  `all_languages` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Export der Ansicht shop_view_cust_sales_stats_base
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_view_cust_sales_stats_base`; DROP VIEW IF EXISTS `shop_view_cust_sales_stats_base`;

CREATE VIEW `shop_view_cust_sales_stats_base`
AS SELECT
   `shop_sales_header`.`customer_no` AS `customer_no`,count(`shop_sales_header`.`order_no`) AS `order_count`,round(sum(`shop_sales_header`.`total`),2) AS `sum_amt`,round(avg(`shop_sales_header`.`total`),2) AS `avg_order_amt`
FROM `shop_sales_header` where (`shop_sales_header`.`customer_no` is not null) group by `shop_sales_header`.`customer_no` order by `shop_sales_header`.`customer_no`;

# Export der Ansicht shop_view_cust_sales_stats_pop_vals
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_view_cust_sales_stats_pop_vals`; DROP VIEW IF EXISTS `shop_view_cust_sales_stats_pop_vals`;

CREATE VIEW `shop_view_cust_sales_stats_pop_vals`
AS SELECT
   round(avg(`shop_view_cust_sales_stats_base`.`order_count`),2) AS `pop_avg_order_count`,round(avg(`shop_view_cust_sales_stats_base`.`sum_amt`),2) AS `pop_avg_sum_amt`,round(avg(`shop_view_cust_sales_stats_base`.`avg_order_amt`),2) AS `pop_avg_cust_avg_order_amt`,round(std(`shop_view_cust_sales_stats_base`.`order_count`),2) AS `stddev_order_count`,round(std(`shop_view_cust_sales_stats_base`.`sum_amt`),2) AS `stddev_sum_amt`,round(std(`shop_view_cust_sales_stats_base`.`avg_order_amt`),2) AS `stddev_avg_order_amt`
FROM `shop_view_cust_sales_stats_base`;

# Export der Ansicht shop_view_active_item
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_view_active_item`; DROP VIEW IF EXISTS `shop_view_active_item`;

CREATE VIEW `shop_view_active_item`
AS SELECT
   `shop_item`.`id` AS `id`,
   `shop_item`.`company` AS `company`,
   `shop_item`.`shop_code` AS `shop_code`,
   `shop_item`.`language_code` AS `language_code`,
   `shop_item`.`item_no` AS `item_no`,
   `shop_item`.`description` AS `description`,
   `shop_item`.`summary` AS `summary`,
   `shop_item`.`base_unit_of_measure` AS `base_unit_of_measure`,
   `shop_item`.`unit_of_measure_code` AS `unit_of_measure_code`,
   `shop_item`.`nav_base_unit_code` AS `nav_base_unit_code`,
   `shop_item`.`multiplier` AS `multiplier`,
   `shop_item`.`variant_type` AS `variant_type`,
   `shop_item`.`active` AS `active`,
   `shop_item`.`validity_from` AS `validity_from`,
   `shop_item`.`validity_to` AS `validity_to`,
   `shop_item`.`main_picture_line_no` AS `main_picture_line_no`,
   `shop_item`.`main_category_line_no` AS `main_category_line_no`,
   `shop_item`.`retail_price` AS `retail_price`,
   `shop_item`.`base_price` AS `base_price`,
   `shop_item`.`price_includes_vat` AS `price_includes_vat`,
   `shop_item`.`inventory` AS `inventory`,
   `shop_item`.`insufficient_inventory_limit` AS `insufficient_inventory_limit`,
   `shop_item`.`quantity_on_purchase_order` AS `quantity_on_purchase_order`,
   `shop_item`.`discount_group` AS `discount_group`,
   `shop_item`.`allow_invoice_discount` AS `allow_invoice_discount`,
   `shop_item`.`search_query` AS `search_query`,
   `shop_item`.`vendor_no` AS `vendor_no`,
   `shop_item`.`vendor_name` AS `vendor_name`,
   `shop_item`.`parent_item_no` AS `parent_item_no`,
   `shop_item`.`order_ranking` AS `order_ranking`,
   `shop_item`.`weight` AS `weight`,
   `shop_item`.`width` AS `width`,
   `shop_item`.`height` AS `height`,
   `shop_item`.`length` AS `length`,
   `shop_item`.`volume` AS `volume`,
   `shop_item`.`always_available` AS `always_available`,
   `shop_item`.`creation_date` AS `creation_date`,
   `shop_item`.`meta_keywords` AS `meta_keywords`,
   `shop_item`.`meta_description` AS `meta_description`,
   `shop_item`.`site_title` AS `site_title`,
   `shop_item`.`allow_gift_package` AS `allow_gift_package`,
   `shop_item`.`is_gift_package` AS `is_gift_package`,
   `shop_item`.`minimum_order_quantity` AS `minimum_order_quantity`,
   `shop_item`.`quantity_packing_unit` AS `quantity_packing_unit`,
   `shop_item`.`order_per_packing_unit` AS `order_per_packing_unit`,
   `shop_item`.`vat_prod_posting_group` AS `vat_prod_posting_group`,
   `shop_item`.`customizable` AS `customizable`,
   `shop_item`.`item_slug` AS `item_slug`,
   `shop_item`.`to_delete` AS `to_delete`
FROM `shop_item` where (((`shop_item`.`active` <> 0) and isnull(`shop_item`.`validity_from`) and isnull(`shop_item`.`validity_to`)) or ((`shop_item`.`active` <> 0) and isnull(`shop_item`.`validity_from`) and (`shop_item`.`validity_to` >= curdate())) or ((`shop_item`.`active` <> 0) and (`shop_item`.`validity_from` <= curdate()) and isnull(`shop_item`.`validity_to`)) or ((`shop_item`.`active` <> 0) and (`shop_item`.`validity_from` <= curdate()) and (`shop_item`.`validity_to` >= curdate())));

# Export der Ansicht shop_view_item_search
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_view_item_search`; DROP VIEW IF EXISTS `shop_view_item_search`;

CREATE VIEW `shop_view_item_search`
AS SELECT
   `si`.`id` AS `id`,
   `si`.`company` AS `company`,
   `si`.`shop_code` AS `shop_code`,
   `si`.`language_code` AS `language_code`,
   `si`.`item_no` AS `item_no`,
   `siv`.`code` AS `variant_code`,
   `si`.`parent_item_no` AS `parent_item_no`,
   `si`.`description` AS `description`,
   `si`.`summary` AS `description_2`,
   `siv`.`description` AS `variant_description`,
   `siv`.`description_2` AS `variant_description_2`,
   `si`.`search_query` AS `item_search_terms`,
   `si`.`active` AS `active`,
   `si`.`inventory` AS `inventory`,
   `siv`.`inventory` AS `variant_inventory`,
   `si`.`retail_price` AS `retail_price`,
   `si`.`base_price` AS `base_price`
FROM (`shop_item` `si` left join `shop_item_variant` `siv` on(((`siv`.`company` = `si`.`company`) and (`siv`.`item_no` = `si`.`item_no`))));

# Export der Ansicht token_documents
# ------------------------------------------------------------

DROP TABLE IF EXISTS `token_documents`; DROP VIEW IF EXISTS `token_documents`;

CREATE VIEW `token_documents`
AS SELECT
   `tokentable`.`token` AS `token`,group_concat(`tokentable`.`document_id` separator ',') AS `documents`
FROM `tokentable` group by `tokentable`.`token`;

# Export der Ansicht main_view_active_navigation
# ------------------------------------------------------------

DROP TABLE IF EXISTS `main_view_active_navigation`; DROP VIEW IF EXISTS `main_view_active_navigation`;

CREATE VIEW `main_view_active_navigation`
AS SELECT
   `main_navigation`.`id` AS `id`,
   `main_navigation`.`main_site_id` AS `main_site_id`,
   `main_navigation`.`main_language_id` AS `main_language_id`,
   `main_navigation`.`parent_id` AS `parent_id`,
   `main_navigation`.`sorting` AS `sorting`,
   `main_navigation`.`level` AS `level`,
   `main_navigation`.`code` AS `code`,
   `main_navigation`.`menu_name` AS `menu_name`,
   `main_navigation`.`title_name` AS `title_name`,
   `main_navigation`.`active` AS `active`,
   `main_navigation`.`validity_from` AS `validity_from`,
   `main_navigation`.`validity_to` AS `validity_to`,
   `main_navigation`.`modified_date` AS `modified_date`,
   `main_navigation`.`modified_admin_user_id` AS `modified_admin_user_id`,
   `main_navigation`.`meta_keywords` AS `meta_keywords`,
   `main_navigation`.`meta_description` AS `meta_description`,
   `main_navigation`.`hidden` AS `hidden`,
   `main_navigation`.`forward_type` AS `forward_type`,
   `main_navigation`.`forward_url` AS `forward_url`
FROM `main_navigation` where (((`main_navigation`.`active` <> 0) and isnull(`main_navigation`.`validity_from`) and isnull(`main_navigation`.`validity_to`)) or ((`main_navigation`.`active` <> 0) and isnull(`main_navigation`.`validity_from`) and (`main_navigation`.`validity_to` >= curdate())) or ((`main_navigation`.`active` <> 0) and (`main_navigation`.`validity_from` <= curdate()) and isnull(`main_navigation`.`validity_to`)) or ((`main_navigation`.`active` <> 0) and (`main_navigation`.`validity_from` <= curdate()) and (`main_navigation`.`validity_to` >= curdate())));

# Export der Ansicht shop_view_user_basket
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_view_user_basket`; DROP VIEW IF EXISTS `shop_view_user_basket`;

CREATE VIEW `shop_view_user_basket`
AS SELECT
   `sub`.`id` AS `id`,
   `sub`.`shop_user_id` AS `shop_user_id`,
   `sub`.`shop_visitor_id` AS `shop_visitor_id`,
   `sub`.`shop_item_id` AS `shop_item_id`,
   `sub`.`variant_code` AS `variant_code`,
   `sub`.`item_quantity` AS `item_quantity`,
   `sub`.`allow_invoice_disc` AS `allow_invoice_disc`,
   `sub`.`customer_price` AS `customer_price`,
   `sub`.`customer_price_wo_vat` AS `customer_price_wo_vat`,
   `sub`.`is_coupon_item` AS `is_coupon_item`,
   `sub`.`changed_to_minimum` AS `changed_to_minimum`,
   `sub`.`changed_to_vpe` AS `changed_to_vpe`,
   `sub`.`package_for_item` AS `package_for_item`,
   `sub`.`insert_datetime` AS `insert_datetime`,
   `si`.`shop_code` AS `shop_code`,
   `si`.`language_code` AS `language_code`,
   `si`.`item_no` AS `item_no`,
   `si`.`parent_item_no` AS `parent_item_no`
FROM (`shop_user_basket` `sub` left join `shop_item` `si` on((`si`.`id` = `sub`.`shop_item_id`)));

# Export der Ansicht shop_view_active_item_full
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_view_active_item_full`; DROP VIEW IF EXISTS `shop_view_active_item_full`;

CREATE VIEW `shop_view_active_item_full`
AS SELECT
   `shop_item`.`id` AS `id`,
   `shop_item`.`company` AS `company`,
   `shop_item`.`shop_code` AS `shop_code`,
   `shop_item`.`language_code` AS `language_code`,
   `shop_item`.`item_no` AS `item_no`,(case when (`shop_item_variant`.`code` is not null) then `shop_item_variant`.`code` when isnull(`shop_item_variant`.`id`) then '' end) AS `variant_code`,(case when (((char_length(trim(`shop_item_variant_translation`.`description`)) <= 0) or isnull(`shop_item_variant_translation`.`id`)) and (char_length(trim(`shop_item_variant`.`description`)) > 0)) then `shop_item_variant`.`description` when (((char_length(trim(`shop_item_variant_translation`.`description`)) <= 0) or isnull(`shop_item_variant_translation`.`id`)) and ((char_length(trim(`shop_item_variant`.`description`)) <= 0) or isnull(`shop_item_variant`.`description`))) then `shop_item`.`description` when (char_length(trim(`shop_item_variant_translation`.`description`)) > 0) then `shop_item_variant_translation`.`description` end) AS `description`,(case when (((char_length(trim(`shop_item_variant_translation`.`description_2`)) <= 0) or isnull(`shop_item_variant_translation`.`description_2`)) and (char_length(trim(`shop_item_variant`.`description_2`)) > 0)) then `shop_item_variant`.`description_2` when (((char_length(trim(`shop_item_variant_translation`.`description_2`)) <= 0) or isnull(`shop_item_variant_translation`.`description_2`)) and ((char_length(trim(`shop_item_variant`.`description_2`)) <= 0) or isnull(`shop_item_variant`.`description_2`))) then `shop_item`.`summary` when (char_length(trim(`shop_item_variant_translation`.`description_2`)) > 0) then `shop_item_variant_translation`.`description_2` end) AS `summary`,
   `shop_item`.`base_unit_of_measure` AS `base_unit_of_measure`,
   `shop_item`.`unit_of_measure_code` AS `unit_of_measure_code`,
   `shop_item`.`nav_base_unit_code` AS `nav_base_unit_code`,
   `shop_item`.`multiplier` AS `multiplier`,
   `shop_item`.`variant_type` AS `variant_type`,
   `shop_item`.`main_picture_line_no` AS `main_picture_line_no`,
   `shop_item`.`main_category_line_no` AS `main_category_line_no`,
   `shop_item`.`retail_price` AS `retail_price`,
   `shop_item`.`base_price` AS `base_price`,
   `shop_item`.`price_includes_vat` AS `price_includes_vat`,(case when (char_length(`shop_item_variant`.`id`) > 0) then `shop_item_variant`.`inventory` else `shop_item`.`inventory` end) AS `inventory`,
   `shop_item`.`insufficient_inventory_limit` AS `insufficient_inventory_limit`,
   `shop_item`.`quantity_on_purchase_order` AS `quantity_on_purchase_order`,
   `shop_item`.`discount_group` AS `discount_group`,
   `shop_item`.`allow_invoice_discount` AS `allow_invoice_discount`,
   `shop_item`.`search_query` AS `search_query`,
   `shop_item`.`vendor_no` AS `vendor_no`,
   `shop_item`.`vendor_name` AS `vendor_name`,
   `shop_item`.`parent_item_no` AS `parent_item_no`,
   `shop_item`.`order_ranking` AS `order_ranking`,
   `shop_item`.`weight` AS `weight`,
   `shop_item`.`width` AS `width`,
   `shop_item`.`height` AS `height`,
   `shop_item`.`length` AS `length`,
   `shop_item`.`volume` AS `volume`,
   `shop_item`.`creation_date` AS `creation_date`,
   `shop_item`.`meta_keywords` AS `meta_keywords`,
   `shop_item`.`meta_description` AS `meta_description`,
   `shop_item`.`site_title` AS `site_title`,
   `shop_item`.`allow_gift_package` AS `allow_gift_package`,
   `shop_item`.`is_gift_package` AS `is_gift_package`,
   `shop_item`.`minimum_order_quantity` AS `minimum_order_quantity`,
   `shop_item`.`quantity_packing_unit` AS `quantity_packing_unit`,
   `shop_item`.`order_per_packing_unit` AS `order_per_packing_unit`,
   `shop_item`.`vat_prod_posting_group` AS `vat_prod_posting_group`
FROM ((`shop_item` left join `shop_item_variant` on(((`shop_item_variant`.`company` = `shop_item`.`company`) and (`shop_item_variant`.`item_no` = `shop_item`.`item_no`) and (`shop_item_variant`.`to_delete` = 0)))) left join `shop_item_variant_translation` on(((`shop_item_variant_translation`.`company` = `shop_item`.`company`) and (`shop_item_variant_translation`.`item_no` = `shop_item`.`item_no`) and (`shop_item_variant_translation`.`language_code` = `shop_item`.`language_code`) and (`shop_item_variant_translation`.`variant_code` = `shop_item_variant`.`code`) and (`shop_item_variant_translation`.`to_delete` = 0)))) where ((`shop_item`.`active` = 1) and (isnull(`shop_item`.`validity_from`) or (`shop_item`.`validity_from` <= curdate())) and ((`shop_item`.`validity_to` = '0000-00-00') or (`shop_item`.`validity_to` > curdate()) or isnull(`shop_item`.`validity_to`)));

# Export der Ansicht token_table_view
# ------------------------------------------------------------

DROP TABLE IF EXISTS `token_table_view`; DROP VIEW IF EXISTS `token_table_view`;

CREATE VIEW `token_table_view`
AS SELECT
   `tokentable`.`id` AS `id`,
   `tokentable`.`token` AS `token`,
   `tokentable`.`document_id` AS `document_id`,
   `tokentable`.`importance` AS `importance`,
   `shop_item`.`company` AS `company`,
   `shop_item`.`shop_code` AS `shop_code`,
   `shop_item`.`language_code` AS `language_code`,
   `shop_item`.`item_no` AS `item_no`,
   `shop_item`.`active` AS `active`
FROM (`tokentable` left join `shop_item` on((`shop_item`.`id` = `tokentable`.`document_id`)));

# Export der Ansicht shop_view_returnable_shipments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_view_returnable_shipments`; DROP VIEW IF EXISTS `shop_view_returnable_shipments`;

CREATE VIEW `shop_view_returnable_shipments`
AS SELECT
   `ssh`.`id` AS `id`,
   `ssh`.`company` AS `company`,
   `ssh`.`no` AS `no`,
   `ssh`.`posting_date` AS `posting_date`,
   `ssh`.`order_no` AS `order_no`,
   `ssh`.`webshop_order_no` AS `webshop_order_no`,
   `ssh`.`your_reference` AS `your_reference`,
   `ssh`.`sell_to_customer_no` AS `sell_to_customer_no`,
   `ssh`.`bill_to_customer_no` AS `bill_to_customer_no`,
   `ssh`.`sell_to_name` AS `sell_to_name`,
   `ssh`.`sell_to_name_2` AS `sell_to_name_2`,
   `ssh`.`sell_to_address` AS `sell_to_address`,
   `ssh`.`sell_to_address_2` AS `sell_to_address_2`,
   `ssh`.`sell_to_post_code` AS `sell_to_post_code`,
   `ssh`.`sell_to_city` AS `sell_to_city`,
   `ssh`.`sell_to_country` AS `sell_to_country`,
   `ssh`.`sell_to_contact` AS `sell_to_contact`,
   `ssh`.`bill_to_name` AS `bill_to_name`,
   `ssh`.`bill_to_name_2` AS `bill_to_name_2`,
   `ssh`.`bill_to_address` AS `bill_to_address`,
   `ssh`.`bill_to_address_2` AS `bill_to_address_2`,
   `ssh`.`bill_to_post_code` AS `bill_to_post_code`,
   `ssh`.`bill_to_city` AS `bill_to_city`,
   `ssh`.`bill_to_country` AS `bill_to_country`,
   `ssh`.`bill_to_contact` AS `bill_to_contact`,
   `ssh`.`ship_to_name` AS `ship_to_name`,
   `ssh`.`ship_to_name_2` AS `ship_to_name_2`,
   `ssh`.`ship_to_address` AS `ship_to_address`,
   `ssh`.`ship_to_address_2` AS `ship_to_address_2`,
   `ssh`.`ship_to_post_code` AS `ship_to_post_code`,
   `ssh`.`ship_to_city` AS `ship_to_city`,
   `ssh`.`ship_to_country` AS `ship_to_country`,
   `ssh`.`ship_to_contact` AS `ship_to_contact`,
   `ssh`.`shipment_method` AS `shipment_method`,
   `ssh`.`request_mail` AS `request_mail`,
   `ssh`.`request_shop_code` AS `request_shop_code`,
   `ssh`.`request_language_code` AS `request_language_code`,
   `ssh`.`send_request` AS `send_request`,
   `ssh`.`return_order_insert` AS `return_order_insert`,
   `ssh`.`return_order` AS `return_order`,
   `ssh`.`return_shop_code` AS `return_shop_code`,
   `ssh`.`return_language_code` AS `return_language_code`,
   `ssh`.`return_order_reference` AS `return_order_reference`,
   `ssh`.`return_order_shop_no` AS `return_order_shop_no`,
   `ssh`.`return_order_token` AS `return_order_token`,
   `ssh`.`to_delete` AS `to_delete`
FROM `shop_sales_shipment_header` `ssh` where ((`ssh`.`return_order_insert` = 0) and exists(select 1 from `shop_sales_shipment_line` where ((`shop_sales_shipment_line`.`document_no` = `ssh`.`no`) and (`shop_sales_shipment_line`.`type` = 2) and (`shop_sales_shipment_line`.`quantity` > 0) and (`shop_sales_shipment_line`.`quantity` > (`shop_sales_shipment_line`.`return_quantity` + `shop_sales_shipment_line`.`quantity_returned`))) limit 1));

# Export der Ansicht shop_item_attribute_links_ext
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_item_attribute_links_ext`; DROP VIEW IF EXISTS `shop_item_attribute_links_ext`;

CREATE VIEW `shop_item_attribute_links_ext`
AS SELECT
   `sal`.`id` AS `id`,
   `sal`.`company` AS `company`,
   `sal`.`shop_code` AS `shop_code`,
   `sal`.`language_code` AS `language_code`,
   `sal`.`no` AS `no`,
   `sal`.`attribute_code` AS `attribute_code`,
   `sa`.`description` AS `attribute_description`,
   `sa`.`data_type` AS `data_type`,
   `sal`.`line_no` AS `line_no`,
   `sal`.`type` AS `type`,
   `sal`.`value_decimal` AS `value_decimal`,
   `sal`.`value_integer` AS `value_integer`,
   `sal`.`value_option` AS `value_option`,
   `sao`.`description` AS `val_opt_desc`,
   `sal`.`value_bool` AS `value_bool`,
   `sal`.`value_text` AS `value_text`,
   `sal`.`to_delete` AS `to_delete`
FROM ((`shop_attribute_link` `sal` left join `shop_attribute` `sa` on(((`sa`.`company` = `sal`.`company`) and (`sa`.`code` = `sal`.`attribute_code`)))) left join `shop_attribute_option` `sao` on(((`sao`.`company` = `sal`.`company`) and (`sao`.`attribute_code` = `sal`.`attribute_code`) and (`sao`.`code` = `sal`.`value_option`))));

# Export der Ansicht shop_view_item_category_info
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_view_item_category_info`; DROP VIEW IF EXISTS `shop_view_item_category_info`;

CREATE VIEW `shop_view_item_category_info`
AS SELECT
   `sihc`.`company` AS `company`,
   `sihc`.`shop_code` AS `shop_code`,
   `sihc`.`language_code` AS `language_code`,
   `sihc`.`category_shop_code` AS `category_shop_code`,
   `sihc`.`item_no` AS `item_no`,group_concat(`sihc`.`category_line_no` separator '~|~') AS `cat_line_nos`,group_concat(distinct `sc`.`name` separator '~|~') AS `cat_names`,group_concat(`sc`.`code` separator '~|~') AS `cat_codes`,group_concat(distinct replace(replace(replace(`sc`.`search_query`,' ','~|~'),',','~|~'),';','~|~') separator '~|~') AS `cat_search_terms`
FROM (`shop_item_has_category` `sihc` left join `shop_category` `sc` on(((`sc`.`company` = `sihc`.`company`) and (`sc`.`shop_code` = `sihc`.`category_shop_code`) and (`sc`.`language_code` = `sihc`.`category_language_code`) and (`sc`.`line_no` = `sihc`.`category_line_no`)))) group by `sihc`.`company`,`sihc`.`shop_code`,`sihc`.`language_code`,`sihc`.`item_no`;

# Export der Ansicht shop_view_returnable_shipments_ext
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shop_view_returnable_shipments_ext`; DROP VIEW IF EXISTS `shop_view_returnable_shipments_ext`;

CREATE VIEW `shop_view_returnable_shipments_ext`
AS SELECT
   `svrs`.`id` AS `id`,
   `svrs`.`company` AS `company`,
   `svrs`.`no` AS `no`,
   `svrs`.`posting_date` AS `posting_date`,
   `svrs`.`order_no` AS `order_no`,
   `svrs`.`webshop_order_no` AS `webshop_order_no`,
   `ssh`.`shop_code` AS `shop_code`,
   `ssh`.`language_code` AS `language_code`,
   `ssh`.`user_email` AS `user_email`,
   `ssh`.`shop_user_id` AS `user_id`,
   `ssh`.`shop_customer_id` AS `customer_id`,
   `svrs`.`your_reference` AS `your_reference`,
   `svrs`.`sell_to_customer_no` AS `sell_to_customer_no`,
   `svrs`.`bill_to_customer_no` AS `bill_to_customer_no`,
   `svrs`.`sell_to_name` AS `sell_to_name`,
   `svrs`.`sell_to_name_2` AS `sell_to_name_2`,
   `svrs`.`sell_to_address` AS `sell_to_address`,
   `svrs`.`sell_to_address_2` AS `sell_to_address_2`,
   `svrs`.`sell_to_post_code` AS `sell_to_post_code`,
   `svrs`.`sell_to_city` AS `sell_to_city`,
   `svrs`.`sell_to_country` AS `sell_to_country`,
   `svrs`.`sell_to_contact` AS `sell_to_contact`,
   `svrs`.`bill_to_name` AS `bill_to_name`,
   `svrs`.`bill_to_name_2` AS `bill_to_name_2`,
   `svrs`.`bill_to_address` AS `bill_to_address`,
   `svrs`.`bill_to_address_2` AS `bill_to_address_2`,
   `svrs`.`bill_to_post_code` AS `bill_to_post_code`,
   `svrs`.`bill_to_city` AS `bill_to_city`,
   `svrs`.`bill_to_country` AS `bill_to_country`,
   `svrs`.`bill_to_contact` AS `bill_to_contact`,
   `svrs`.`ship_to_name` AS `ship_to_name`,
   `svrs`.`ship_to_name_2` AS `ship_to_name_2`,
   `svrs`.`ship_to_address` AS `ship_to_address`,
   `svrs`.`ship_to_address_2` AS `ship_to_address_2`,
   `svrs`.`ship_to_post_code` AS `ship_to_post_code`,
   `svrs`.`ship_to_city` AS `ship_to_city`,
   `svrs`.`ship_to_country` AS `ship_to_country`,
   `svrs`.`ship_to_contact` AS `ship_to_contact`,
   `svrs`.`shipment_method` AS `shipment_method`,
   `svrs`.`request_mail` AS `request_mail`,
   `svrs`.`request_shop_code` AS `request_shop_code`,
   `svrs`.`request_language_code` AS `request_language_code`,
   `svrs`.`send_request` AS `send_request`,
   `svrs`.`return_order_insert` AS `return_order_insert`,
   `svrs`.`return_order` AS `return_order`,
   `svrs`.`return_shop_code` AS `return_shop_code`,
   `svrs`.`return_language_code` AS `return_language_code`,
   `svrs`.`return_order_reference` AS `return_order_reference`,
   `svrs`.`return_order_shop_no` AS `return_order_shop_no`,
   `svrs`.`to_delete` AS `to_delete`,
   `sih`.`no` AS `invoice_no`,
   `nsh`.`no` AS `nav_order_no`,
   `svrs`.`return_order_token` AS `return_order_token`
FROM (((`shop_view_returnable_shipments` `svrs` left join `shop_sales_invoice_header` `sih` on((`sih`.`order_no` = `svrs`.`order_no`))) left join `shop_nav_sales_header` `nsh` on((`nsh`.`no` = `svrs`.`order_no`))) left join `shop_sales_header` `ssh` on(((`ssh`.`order_no` = `svrs`.`webshop_order_no`) and (`ssh`.`order_no` <> 0)))) group by `svrs`.`no`;


/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
