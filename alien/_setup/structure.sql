-- MySQL dump 10.13  Distrib 5.5.34, for Win32 (x86)
--
-- Host: localhost    Database: test
-- ------------------------------------------------------
-- Server version	5.5.34

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
-- Current Database: `test`
--

USE `test`;

--
-- Table structure for table `forum_posts`
--

DROP TABLE IF EXISTS `forum_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_posts` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `thread` int(8) DEFAULT NULL,
  `reply` int(8) DEFAULT NULL,
  `id_u` int(8) NOT NULL,
  `date` int(10) NOT NULL,
  `date_modified` int(10) NOT NULL,
  `message` text NOT NULL,
  `views` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forum_threads`
--

DROP TABLE IF EXISTS `forum_threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forum_threads` (
  `id` int(8) DEFAULT NULL,
  `parent` int(8) DEFAULT NULL,
  `lft` int(8) DEFAULT NULL,
  `rgt` int(8) DEFAULT NULL,
  `level` int(8) DEFAULT NULL,
  `name` varchar(90) DEFAULT NULL,
  `description` varchar(750) DEFAULT NULL,
  `locked` tinyint(1) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT NULL,
  `read_groups` text,
  `write_groups` text,
  `admin_groups` text,
  `admin_users` text,
  `tmp_lft` int(8) DEFAULT NULL,
  `tmp_rgt` int(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_authorization`
--

DROP TABLE IF EXISTS `test_authorization`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_authorization` (
  `id_auth` int(8) NOT NULL AUTO_INCREMENT,
  `id_u` int(8) NOT NULL,
  `timeout` int(10) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `url` varchar(250) NOT NULL,
  PRIMARY KEY (`id_auth`)
) ENGINE=MyISAM AUTO_INCREMENT=336 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_config`
--

DROP TABLE IF EXISTS `test_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_config` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `param` varchar(10) NOT NULL,
  `data` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_content_containers`
--

DROP TABLE IF EXISTS `test_content_containers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_content_containers` (
  `id_c` int(8) NOT NULL AUTO_INCREMENT,
  `maxlimit` int(4) DEFAULT NULL,
  PRIMARY KEY (`id_c`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_content_folders`
--

DROP TABLE IF EXISTS `test_content_folders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_content_folders` (
  `id_f` int(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `parent` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_f`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_content_item_types`
--

DROP TABLE IF EXISTS `test_content_item_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_content_item_types` (
  `id_type` int(8) NOT NULL AUTO_INCREMENT,
  `classname` varchar(20) NOT NULL,
  PRIMARY KEY (`id_type`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_content_items`
--

DROP TABLE IF EXISTS `test_content_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_content_items` (
  `id_i` int(8) NOT NULL AUTO_INCREMENT,
  `id_c` int(8) DEFAULT NULL,
  `id_f` int(8) DEFAULT '0',
  `id_type` int(8) NOT NULL,
  `name` varchar(70) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`id_i`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_content_pages`
--

DROP TABLE IF EXISTS `test_content_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_content_pages` (
  `id_p` int(8) NOT NULL AUTO_INCREMENT,
  `id_t` int(8) NOT NULL,
  `id_f` int(8) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `seolink` varchar(50) NOT NULL,
  `description` text,
  `keywords` varchar(50) DEFAULT NULL,
  `groups` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_p`),
  UNIQUE KEY `UNIQUE` (`seolink`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_content_template_blocks`
--

DROP TABLE IF EXISTS `test_content_template_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_content_template_blocks` (
  `id_b` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(30) NOT NULL,
  PRIMARY KEY (`id_b`),
  UNIQUE KEY `UNIQUE` (`label`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_content_templates`
--

DROP TABLE IF EXISTS `test_content_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_content_templates` (
  `id_t` int(8) NOT NULL AUTO_INCREMENT,
  `id_f` int(8) NOT NULL DEFAULT '0',
  `name` varchar(25) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `src` varchar(50) NOT NULL,
  PRIMARY KEY (`id_t`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_content_views`
--

DROP TABLE IF EXISTS `test_content_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_content_views` (
  `id_v` int(8) NOT NULL AUTO_INCREMENT,
  `id_type` int(8) NOT NULL,
  `id_i` int(8) NOT NULL,
  `id_p` int(8) DEFAULT NULL,
  `id_t` int(8) DEFAULT NULL,
  `id_c` int(2) DEFAULT NULL,
  `position` int(5) DEFAULT NULL,
  `visible` tinyint(4) NOT NULL DEFAULT '0',
  `class` varchar(50) DEFAULT NULL,
  `script` varchar(50) DEFAULT NULL,
  `params` text,
  PRIMARY KEY (`id_v`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_folder_group_permissions`
--

DROP TABLE IF EXISTS `test_folder_group_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_folder_group_permissions` (
  `id_f` int(8) NOT NULL,
  `id_g` int(8) NOT NULL,
  `view` tinyint(1) NOT NULL DEFAULT '0',
  `modify` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_f`,`id_g`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_folder_user_permissions`
--

DROP TABLE IF EXISTS `test_folder_user_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_folder_user_permissions` (
  `id_f` int(8) NOT NULL,
  `id_u` int(8) NOT NULL,
  `view` tinyint(1) NOT NULL DEFAULT '0',
  `modify` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_f`,`id_u`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_galleries`
--

DROP TABLE IF EXISTS `test_galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_galleries` (
  `id_g` int(8) NOT NULL AUTO_INCREMENT,
  `id_i` int(8) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text,
  PRIMARY KEY (`id_g`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_group_members`
--

DROP TABLE IF EXISTS `test_group_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_group_members` (
  `id_u` int(8) NOT NULL,
  `id_g` int(8) NOT NULL,
  `since` int(10) NOT NULL,
  PRIMARY KEY (`id_u`,`id_g`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_group_permissions`
--

DROP TABLE IF EXISTS `test_group_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_group_permissions` (
  `id_g` int(5) NOT NULL,
  `id_p` int(5) NOT NULL,
  `since` int(10) NOT NULL,
  PRIMARY KEY (`id_g`,`id_p`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_groups`
--

DROP TABLE IF EXISTS `test_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_groups` (
  `id_g` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `dateCreated` int(10) NOT NULL,
  `description` varchar(200) NOT NULL,
  PRIMARY KEY (`id_g`),
  UNIQUE KEY `groupname` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_images`
--

DROP TABLE IF EXISTS `test_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_images` (
  `id_img` int(8) NOT NULL AUTO_INCREMENT,
  `id_g` int(8) DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `format` varchar(4) DEFAULT NULL,
  `image` mediumblob NOT NULL,
  `url` varchar(100) DEFAULT NULL,
  `position` int(4) DEFAULT NULL,
  PRIMARY KEY (`id_img`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_logs`
--

DROP TABLE IF EXISTS `test_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_logs` (
  `id_l` int(8) NOT NULL AUTO_INCREMENT,
  `errno` int(3) NOT NULL,
  `time` int(13) NOT NULL,
  `data` varchar(500) DEFAULT NULL,
  `id_auth` int(8) NOT NULL,
  `flag` int(1) DEFAULT '0',
  PRIMARY KEY (`id_l`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_messages`
--

DROP TABLE IF EXISTS `test_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_messages` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `author` int(8) NOT NULL,
  `recipient` int(8) NOT NULL,
  `message` text NOT NULL,
  `dateSent` int(10) NOT NULL,
  `dateSeen` int(10) DEFAULT NULL,
  `authorTags` varchar(20) DEFAULT NULL,
  `recipientTags` varchar(20) DEFAULT NULL,
  `deletedByAuthor` int(1) DEFAULT '0',
  `deletedByRecipient` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_press_data`
--

DROP TABLE IF EXISTS `test_press_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_press_data` (
  `id_a` int(8) NOT NULL AUTO_INCREMENT,
  `id_i` int(8) NOT NULL,
  `id_u` int(8) NOT NULL,
  `name` varchar(50) NOT NULL,
  `intro` text,
  `content` text,
  `image_url` varchar(50) DEFAULT NULL,
  `seolink` varchar(50) DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `is_top` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` int(10) NOT NULL,
  `date_modified` int(10) DEFAULT NULL,
  `class` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_a`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_user_permissions`
--

DROP TABLE IF EXISTS `test_user_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_user_permissions` (
  `id_u` int(8) NOT NULL,
  `id_p` int(5) NOT NULL,
  `since` int(10) DEFAULT NULL,
  PRIMARY KEY (`id_u`,`id_p`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test_users`
--

DROP TABLE IF EXISTS `test_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_users` (
  `id_u` int(8) NOT NULL AUTO_INCREMENT,
  `login` varchar(20) NOT NULL,
  `password` varchar(40) NOT NULL,
  `email` varchar(50) NOT NULL,
  `date_registered` int(10) NOT NULL,
  `activated` tinyint(1) NOT NULL DEFAULT '0',
  `last_active` int(10) DEFAULT NULL,
  `ban` int(10) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `firstname` varchar(20) DEFAULT NULL,
  `surname` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_u`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-12-27 23:52:46
