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
-- Dumping data for table `forum_posts`
--

LOCK TABLES `forum_posts` WRITE;
/*!40000 ALTER TABLE `forum_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `forum_posts` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `forum_threads`
--

LOCK TABLES `forum_threads` WRITE;
/*!40000 ALTER TABLE `forum_threads` DISABLE KEYS */;
INSERT INTO `forum_threads` VALUES (1,0,1,40,0,'ROOT','ROOT',1,1,NULL,NULL,NULL,NULL,NULL,NULL),(2,1,2,25,1,'Vedenie tímu','Určené len pre členov vedenia tímu.',1,1,'[6]','[6]',NULL,NULL,NULL,NULL),(14,2,3,4,2,'Členovia tímu','Fórum určené pre diskusiu ohľadom členov a budúcich členov tímu.',0,1,'','',NULL,NULL,NULL,NULL),(13,1,26,37,1,'Tímové fórum','Prístupné iba pre členov tímu.',0,1,'[1][2][3]','[4][5][6]',NULL,NULL,NULL,NULL),(15,13,27,30,2,'Závody','Diskusia ohľadom závodov a ich výsledkov.',0,1,'','',NULL,NULL,NULL,NULL),(16,15,28,29,3,'Žiadosti o prihlasovanie','Žiadosti o prihlasovanie a odhlasovanie do závodov.',0,1,'','',NULL,NULL,NULL,NULL),(17,13,33,34,2,'Protesty','Diskusia ohľadom prípadných protestov.',0,1,'','',NULL,NULL,NULL,NULL),(18,13,35,36,2,'Setupy','Diskusia určená nastaveniam áut.',0,1,'','',NULL,NULL,NULL,NULL),(19,1,38,39,1,'Public','Voľná debata o všetkom.',0,1,'','',NULL,NULL,NULL,NULL),(20,13,31,32,2,'Dôležité oznámenia','Dôležité oznámenia pre všetkých členov tímu od vedenia.',0,1,'','',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `forum_threads` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=MyISAM AUTO_INCREMENT=461 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_authorization`
--

LOCK TABLES `test_authorization` WRITE;
/*!40000 ALTER TABLE `test_authorization` DISABLE KEYS */;
INSERT INTO `test_authorization` VALUES (1,1,1376177337,'127.0.0.1','/alien/index.php?alien=logout'),(2,1,1376177619,'127.0.0.1','/alien/index.php?users=viewList'),(3,1,1376222143,'127.0.0.1','/alien/index.php?content=editTemplate&id=1'),(4,1,1376244585,'127.0.0.1','/alien/index.php?content=editTemplate&id=1'),(5,1,1376249367,'127.0.0.1','/alien/index.php?users=edit&id=1'),(6,1,1376598350,'127.0.0.1','/alien/index.php?users=edit&id=0'),(7,1,1376601751,'127.0.0.1','/alien/index.php?alien=logout'),(8,1,1376604738,'127.0.0.1','/alien/index.php?alien=logout'),(9,1,1376606008,'127.0.0.1','/alien/index.php?alien=logout'),(10,0,1376606008,'127.0.0.1','/alien/index.php'),(11,1,1376736369,'127.0.0.1','/alien/index.php?users=viewList'),(12,1,1376758805,'127.0.0.1','/alien/index.php?users=viewList'),(13,1,1376763485,'127.0.0.1','/alien/index.php?users=viewList'),(14,1,1376776306,'127.0.0.1','/alien/index.php?alien=logout'),(15,1,1376777070,'127.0.0.1','/alien/index.php?alien=logout'),(16,1,1376777131,'127.0.0.1','/alien/index.php?alien=logout'),(17,0,1376777131,'127.0.0.1','/alien/index.php'),(18,1,1376833711,'127.0.0.1','/alien/index.php?content=browser&folder=0'),(19,1,1376836575,'127.0.0.1','/alien/index.php?users=viewList'),(20,1,1376839077,'127.0.0.1','/alien/index.php?content=browser'),(21,1,1376850725,'127.0.0.1','/alien/index.php?alien=logout'),(22,1,1376850729,'127.0.0.1','/alien/index.php?users=viewList'),(23,1,1376870978,'127.0.0.1','/alien/index.php?alien=logout'),(24,0,1376870978,'127.0.0.1','/alien/index.php'),(25,1,1376936692,'127.0.0.1','/alien/index.php?users=viewList'),(26,1,1376938579,'127.0.0.1','/alien/index.php?alien=logout'),(27,1,1376952537,'127.0.0.1','/alien/content/images/icons/favicon.ico'),(28,1,1377019260,'127.0.0.1','/alien/index.php?alien=logout'),(29,1,1377020949,'127.0.0.1','/favicon.ico'),(30,1,1377116965,'127.0.0.1','/alien//logout'),(31,1,1377118254,'127.0.0.1','/alien/images/icons/favicon.ico'),(32,1,1377121287,'127.0.0.1','/alien/base/logout'),(33,1,1377121307,'127.0.0.1','/alien//logout'),(34,1,1377121319,'127.0.0.1','/alien//logout'),(35,1,1377121477,'127.0.0.1','/alien//logout'),(36,1,1377124148,'127.0.0.1','/alien/images/icons/favicon.ico'),(37,1,1377193875,'127.0.0.1','/alien/images/icons/favicon.ico'),(38,1,1377198169,'127.0.0.1','/alien/content/editTemplate/id/ajax.php?action=templateShowFilePreview&file=templates/uvod.php'),(39,1,1377210623,'127.0.0.1','/alien/images/icons/favicon.ico'),(40,1,1377290508,'127.0.0.1','/alien/images/icons/favicon.ico'),(41,1,1377292396,'127.0.0.1','/alien//logout'),(42,1,1377335067,'127.0.0.1','/alien//index.php'),(43,1,1377335873,'127.0.0.1','/alien/images/icons/favicon.ico'),(44,1,1377341117,'127.0.0.1','/alien//logout'),(45,1,1377341130,'127.0.0.1','/alien//logout'),(46,1,1377341921,'127.0.0.1','/alien/images/icons/favicon.ico'),(47,1,1377361852,'127.0.0.1','/alien/images/icons/favicon.ico'),(48,1,1377363879,'127.0.0.1','/alien/images/icons/favicon.ico'),(49,1,1377367963,'127.0.0.1','/alien/images/icons/favicon.ico'),(50,1,1377372642,'127.0.0.1','/alien/images/icons/favicon.ico'),(51,1,1377376212,'127.0.0.1','/favicon.ico'),(52,0,1377378027,'127.0.0.1','/alien/content/browser/folder/images/icons/favicon.ico'),(53,1,1377947223,'127.0.0.1','/alien/content/editTemplate/id/images/icons/less.png'),(54,1,1378235723,'127.0.0.1','/alien//logout'),(55,0,1378235724,'127.0.0.1','/alien//images/icons/favicon.ico'),(56,1,1379155326,'127.0.0.1','/alien//logout'),(57,1,1379155335,'127.0.0.1','/alien/images/icons/favicon.ico'),(58,1,1379157491,'127.0.0.1','/alien//logout'),(59,1,1379157492,'127.0.0.1','/alien/images/icons/favicon.ico'),(60,1,1379162307,'127.0.0.1','/alien/images/icons/favicon.ico'),(61,0,1379164860,'127.0.0.1','/alien/users/images/icons/favicon.ico'),(62,1,1379173527,'127.0.0.1','/alien/images/icons/favicon.ico'),(63,1,1379177476,'127.0.0.1','/alien/ajax.php?action=templateShowFilePreview&file=templates/uvod.php'),(64,1,1379180484,'127.0.0.1','/alien/images/icons/favicon.ico'),(65,1,1379186400,'127.0.0.1','/alien/images/icons/favicon.ico'),(66,1,1379191785,'127.0.0.1','/alien//logout'),(67,1,1379193267,'127.0.0.1','/alien/images/icons/favicon.ico'),(68,1,1379248566,'127.0.0.1','/alien/images/icons/favicon.ico'),(69,1,1379275510,'127.0.0.1','/alien//logout'),(70,1,1379276360,'127.0.0.1','/alien/images/icons/favicon.ico'),(71,1,1379283896,'127.0.0.1','/alien/images/icons/favicon.ico'),(72,1,1379453227,'127.0.0.1','/alien/images/icons/favicon.ico'),(73,1,1379458277,'127.0.0.1','/alien/images/icons/favicon.ico'),(74,0,1379458684,'127.0.0.1','/sites/default/files/js/js_4688e33a253b7b108ef60e24de0912c0.js'),(75,1,1379459986,'127.0.0.1','/alien//logout'),(76,0,1379459987,'127.0.0.1','/alien//images/icons/favicon.ico'),(77,1,1379617146,'127.0.0.1','/alien/images/icons/favicon.ico'),(78,1,1379622896,'127.0.0.1','/favicon.ico'),(79,1,1379625553,'127.0.0.1','/favicon.ico'),(80,0,1379630149,'127.0.0.1','/alien/users/images/icons/favicon.ico'),(81,1,1379762462,'127.0.0.1','/alien/images/icons/favicon.ico'),(82,1,1379784919,'127.0.0.1','/alien/images/icons/favicon.ico'),(83,1,1379790425,'127.0.0.1','/favicon.ico'),(84,1,1379862411,'127.0.0.1','/alien/images/icons/favicon.ico'),(85,1,1379868037,'127.0.0.1','/alien/images/icons/favicon.ico'),(86,0,1379875169,'127.0.0.1','/alien/users/images/icons/favicon.ico'),(87,0,1380198054,'127.0.0.1','/alien/content/editTemplate/images/icons/favicon.ico'),(88,0,1380207832,'127.0.0.1','/favicon.ico'),(89,1,1380212671,'127.0.0.1','/alien//logout'),(90,1,1380213343,'127.0.0.1','/alien/images/icons/favicon.ico'),(91,0,1380216038,'127.0.0.1','/favicon.ico'),(92,1,1380217333,'127.0.0.1','/alien/images/icons/favicon.ico'),(93,0,1380220231,'127.0.0.1','/favicon.ico'),(94,0,1380362761,'127.0.0.1','/'),(95,1,1380364050,'127.0.0.1','/favicon.ico'),(96,1,1380366221,'127.0.0.1','/alien/images/icons/favicon.ico'),(97,1,1380367750,'127.0.0.1','/alien/images/icons/favicon.ico'),(98,1,1380374428,'127.0.0.1','/alien/images/icons/favicon.ico'),(99,1,1380376574,'127.0.0.1','/alien/images/icons/favicon.ico'),(100,1,1380626506,'127.0.0.1','/alien/images/icons/favicon.ico'),(101,1,1380629387,'127.0.0.1','/alien/ajax.php?action=templateShowFileBrowser&type=ini'),(102,1,1380998619,'127.0.0.1','/alien/images/icons/favicon.ico'),(103,1,1381000389,'127.0.0.1','/alien/images/icons/favicon.ico'),(104,0,1381006188,'127.0.0.1','/alien/content/images/icons/favicon.ico'),(105,1,1381594553,'127.0.0.1','/alien/images/icons/favicon.ico'),(106,1,1381597582,'127.0.0.1','/alien/images/icons/favicon.ico'),(107,1,1381674774,'127.0.0.1','/alien/images/icons/favicon.ico'),(108,1,1382885743,'127.0.0.1','/alien//logout'),(109,0,1382885683,'127.0.0.1','/'),(110,1,1382886262,'127.0.0.1','/alien/images/icons/favicon.ico'),(111,1,1382888109,'127.0.0.1','/alien/images/icons/favicon.ico'),(112,1,1382893289,'127.0.0.1','/alien/images/icons/favicon.ico'),(113,1,1382897117,'127.0.0.1','/alien/images/icons/favicon.ico'),(114,1,1383162923,'127.0.0.1','/alien/images/icons/favicon.ico'),(115,1,1383168349,'127.0.0.1','/alien//logout'),(116,1,1383168360,'127.0.0.1','/favicon.ico'),(117,1,1383219718,'127.0.0.1','/favicon.ico'),(118,1,1383221337,'127.0.0.1','/alien//logout'),(119,1,1383221341,'127.0.0.1','/alien/images/icons/favicon.ico'),(120,1,1383223721,'127.0.0.1','/alien//logout'),(121,1,1383227523,'127.0.0.1','/alien//logout'),(122,1,1383228166,'127.0.0.1','/alien/images/icons/favicon.ico'),(123,1,1383232242,'127.0.0.1','/alien//logout'),(124,1,1383233792,'127.0.0.1','/alien//logout'),(125,1,1383233752,'127.0.0.1','/alien//logout'),(126,0,1383233734,'127.0.0.1','/alien/users/images/icons/favicon.ico'),(128,0,1383233752,'127.0.0.1','/alien//index.php'),(127,0,1383233741,'127.0.0.1','/alien/images/icons/favicon.ico'),(129,0,1383233752,'127.0.0.1','/alien//images/icons/favicon.ico'),(130,1,1383233787,'127.0.0.1','/alien//logout'),(132,0,1383233787,'127.0.0.1','/alien//index.php'),(131,0,1383233787,'127.0.0.1','/alien//images/icons/favicon.ico'),(133,1,1383233930,'127.0.0.1','/alien//logout'),(134,1,1383240816,'127.0.0.1','/alien//logout'),(135,0,1383240816,'127.0.0.1','/alien//images/icons/favicon.ico'),(136,1,1383259064,'127.0.0.1','/alien/images/icons/favicon.ico'),(137,1,1383276949,'127.0.0.1','/alien/images/icons/favicon.ico'),(138,1,1383314418,'127.0.0.1','/alien//logout'),(139,1,1383324190,'127.0.0.1','/alien/images/icons/favicon.ico'),(140,1,1383355234,'127.0.0.1','/alien//logout'),(141,1,1383356347,'127.0.0.1','/alien/images/icons/favicon.ico'),(142,1,1383392808,'127.0.0.1','/alien//logout'),(143,1,1383401294,'127.0.0.1','/alien/images/icons/favicon.ico'),(144,1,1383609447,'127.0.0.1','/alien//logout'),(145,0,1383609447,'127.0.0.1','/alien//images/icons/favicon.ico'),(146,1,1383686536,'127.0.0.1','/alien//logout'),(147,1,1383686954,'127.0.0.1','/alien//logout'),(148,1,1383701203,'127.0.0.1','/alien/images/icons/NOP'),(149,1,1383783633,'127.0.0.1','/alien//logout'),(150,1,1383784884,'127.0.0.1','/alien//logout'),(151,1,1383785171,'127.0.0.1','/alien/base/logout'),(152,1,1383785174,'127.0.0.1','/alien/base/logout'),(153,1,1383785176,'127.0.0.1','/alien/base/logout'),(154,1,1383788685,'127.0.0.1','/alien/base/logout'),(155,1,1383789026,'127.0.0.1','/alien/images/icons/NOP'),(156,1,1383839129,'127.0.0.1','/alien/base/logout'),(157,1,1383860926,'127.0.0.1','/alien/base/logout'),(158,1,1383866319,'127.0.0.1','/alien/base/logout'),(159,1,1383868877,'127.0.0.1','/alien/base/logout'),(160,1,1383871933,'127.0.0.1','/alien/base/logout'),(161,2,1383871955,'127.0.0.1','/alien/base/logout'),(162,1,1383871970,'127.0.0.1','/alien/base/logout'),(163,1,1383872211,'127.0.0.1','/alien/base/logout'),(164,0,1383872211,'127.0.0.1','/alien/base/images/icons/favicon.ico'),(165,1,1383958208,'127.0.0.1','/alien/base/logout'),(166,2,1383952029,'127.0.0.1','/alien/base/logout'),(167,0,1383949726,'127.0.0.1','/alien/images/icons/favicon.ico'),(168,0,1383949741,'127.0.0.1','/alien/images/icons/favicon.ico'),(169,0,1383949743,'127.0.0.1','/alien/images/icons/favicon.ico'),(170,0,1383950358,'127.0.0.1','/alien/images/icons/favicon.ico'),(171,0,1383950365,'127.0.0.1','/alien/images/icons/favicon.ico'),(172,0,1383950415,'127.0.0.1','/alien/images/icons/favicon.ico'),(173,0,1383950423,'127.0.0.1','/alien/images/icons/favicon.ico'),(174,0,1383950429,'127.0.0.1','/alien/images/icons/favicon.ico'),(175,0,1383950429,'127.0.0.1','/alien/images/icons/favicon.ico'),(176,0,1383950670,'127.0.0.1','/alien/images/icons/favicon.ico'),(177,0,1383950677,'127.0.0.1','/alien/images/icons/favicon.ico'),(178,0,1383951230,'127.0.0.1','/alien/images/icons/favicon.ico'),(179,0,1383951491,'127.0.0.1','/alien/images/icons/favicon.ico'),(180,0,1383951492,'127.0.0.1','/alien/images/icons/favicon.ico'),(181,0,1383951494,'127.0.0.1','/alien/images/icons/favicon.ico'),(182,0,1383951496,'127.0.0.1','/alien/images/icons/favicon.ico'),(183,0,1383951506,'127.0.0.1','/alien/images/icons/favicon.ico'),(184,0,1383951530,'127.0.0.1','/alien/images/icons/favicon.ico'),(185,0,1383951728,'127.0.0.1','/alien/images/icons/favicon.ico'),(186,0,1383951783,'127.0.0.1','/alien/images/icons/favicon.ico'),(187,0,1383951784,'127.0.0.1','/alien/images/icons/favicon.ico'),(188,0,1383952029,'127.0.0.1','/alien/base/display/img/key.png'),(189,0,1383952029,'127.0.0.1','/alien/base/images/icons/favicon.ico'),(190,2,1383956643,'127.0.0.1','/alien/users/viewList'),(191,0,1383956636,'127.0.0.1','/alien/users/images/icons/favicon.ico'),(192,0,1383956643,'127.0.0.1','/alien/images/icons/favicon.ico'),(193,2,1383956686,'127.0.0.1','/alien/base/logout'),(194,0,1383956687,'127.0.0.1','/alien/base/images/icons/favicon.ico'),(197,1,1384004317,'127.0.0.1','/alien/'),(195,0,1383956687,'127.0.0.1','/alien/base/display/img/user.png'),(196,0,1383958208,'127.0.0.1','/alien/base/images/icons/favicon.ico'),(198,1,1384117976,'127.0.0.1','/alien/base/logout'),(199,0,1384117976,'127.0.0.1','/alien/base/images/icons/favicon.ico'),(200,1,1384296000,'127.0.0.1','/alien/base/logout'),(201,0,1384296000,'127.0.0.1','/alien/base/images/icons/favicon.ico'),(202,1,1384477707,'127.0.0.1','/alien/users/edit/formErrorOutput.php?request=read&_=1384470507109'),(203,1,1384567819,'127.0.0.1','/alien/users/edit/formErrorOutput.php?request=read&_=1384560619929'),(204,0,1384609474,'127.0.0.1','/xampp/php.php'),(205,0,1384609474,'127.0.0.1','/xampp/mysql.php'),(206,0,1384609474,'127.0.0.1','/xampp/ssi.shtml'),(207,1,1384609791,'127.0.0.1','/alien/dashboard/messages/3'),(208,1,1384743343,'127.0.0.1','/alien/users/edit/formErrorOutput.php?request=read&_=1384736143461'),(209,1,1385821225,'127.0.0.1','/alien/base/logout'),(210,1,1385839755,'127.0.0.1','/alien/base/logout'),(211,0,1385839755,'127.0.0.1','/alien/base/images/icons/favicon.ico'),(212,1,1385916345,'127.0.0.1','/alien/users/edit/1'),(213,1,1385924090,'127.0.0.1','/alien/dashboard/home'),(214,1,1385943361,'127.0.0.1','/alien/base/logout'),(215,1,1385943493,'127.0.0.1','/alien/base/logout'),(216,1,1385945674,'127.0.0.1','/alien/base/logout'),(217,1,1385946098,'127.0.0.1','/alien/base/logout'),(218,1,1385946114,'127.0.0.1','/alien/base/logout'),(219,1,1385946132,'127.0.0.1','/alien/base/logout'),(220,1,1385946562,'127.0.0.1','/alien/base/logout'),(221,1,1385946571,'127.0.0.1','/alien/base/logout'),(222,1,1385946591,'127.0.0.1','/alien/base/logout'),(223,1,1385946597,'127.0.0.1','/alien/base/logout'),(224,1,1385946619,'127.0.0.1','/alien/base/logout'),(225,1,1385946795,'127.0.0.1','/alien/base/logout'),(226,1,1385946864,'127.0.0.1','/alien/base/logout'),(227,1,1385946884,'127.0.0.1','/alien/base/logout'),(228,1,1385946945,'127.0.0.1','/alien/base/logout'),(229,1,1385947002,'127.0.0.1','/alien/base/logout'),(230,1,1385947007,'127.0.0.1','/alien/base/logout'),(231,1,1385947072,'127.0.0.1','/alien/base/logout'),(232,1,1385947763,'127.0.0.1','/alien/base/logout'),(233,1,1385947789,'127.0.0.1','/alien/base/logout'),(234,0,1385947789,'127.0.0.1','/alien/images/icons/favicon.ico'),(235,1,1386028099,'127.0.0.1','/alien/base/logout'),(236,1,1386028196,'127.0.0.1','/alien/base/logout'),(237,0,1386028196,'127.0.0.1','/alien/images/icons/favicon.ico'),(238,1,1386100021,'127.0.0.1','/alien/base/logout'),(239,1,1386101794,'127.0.0.1','/alien/base/logout'),(240,0,1386101794,'127.0.0.1','/alien/images/icons/favicon.ico'),(241,1,1386119228,'127.0.0.1','/alien/base/logout'),(242,1,1386119285,'127.0.0.1','/alien/base/logout'),(243,2,1386119307,'127.0.0.1','/alien/base/logout'),(244,1,1386119645,'127.0.0.1','/alien/base/logout'),(245,1,1386119647,'127.0.0.1','/alien/dashboard/messages'),(246,1,1386250389,'127.0.0.1','/alien/base/logout'),(247,1,1386252015,'127.0.0.1','/alien/base/logout'),(248,0,1386252015,'127.0.0.1','/alien/images/icons/favicon.ico'),(249,1,1386284081,'127.0.0.1','/alien/dashboard/home'),(250,1,1386446516,'127.0.0.1','/alien/base/logout'),(251,0,1386446517,'127.0.0.1','/alien/images/icons/favicon.ico'),(252,1,1386507875,'127.0.0.1','/alien/base/logout'),(253,1,1386507903,'127.0.0.1','/alien/base/logout'),(254,1,1386515497,'127.0.0.1','/alien/base/logout'),(272,1,1386515514,'127.0.0.1','/alien/base/logout'),(255,2,1386514776,'127.0.0.1','/alien/dashboard/home'),(256,0,1386511072,'127.0.0.1','/alien/images/icons/favicon.ico'),(257,0,1386511399,'127.0.0.1','/favicon.ico'),(258,0,1386511400,'127.0.0.1','/favicon.ico'),(259,0,1386512213,'127.0.0.1','/favicon.ico'),(260,0,1386512214,'127.0.0.1','/favicon.ico'),(261,0,1386512214,'127.0.0.1','/favicon.ico'),(262,0,1386514369,'127.0.0.1','/favicon.ico'),(263,0,1386514369,'127.0.0.1','/favicon.ico'),(264,0,1386514441,'127.0.0.1','/favicon.ico'),(265,0,1386514461,'127.0.0.1','/favicon.ico'),(266,0,1386514487,'127.0.0.1','/favicon.ico'),(267,0,1386514487,'127.0.0.1','/favicon.ico'),(268,0,1386514505,'127.0.0.1','/favicon.ico'),(269,2,1386515736,'127.0.0.1','/alien/base/logout'),(270,0,1386514858,'127.0.0.1','/alien/images/icons/favicon.ico'),(271,0,1386514866,'127.0.0.1','/favicon.ico'),(273,1,1386515537,'127.0.0.1','/alien/base/logout'),(274,1,1386515614,'127.0.0.1','/alien/base/logout'),(275,1,1386515624,'127.0.0.1','/alien/base/logout'),(276,1,1386515641,'127.0.0.1','/alien/base/logout'),(277,1,1386515655,'127.0.0.1','/alien/base/logout'),(278,1,1386515665,'127.0.0.1','/alien/base/logout'),(279,1,1386516208,'127.0.0.1','/alien/base/logout'),(280,2,1386515742,'127.0.0.1','/alien/base/logout'),(281,2,1386515789,'127.0.0.1','/alien/base/logout'),(282,0,1386515761,'127.0.0.1','/alien/images/icons/favicon.ico'),(283,0,1386515762,'127.0.0.1','/alien/images/icons/favicon.ico'),(284,2,1386515834,'127.0.0.1','/alien/base/logout'),(285,0,1386515834,'127.0.0.1','/alien/'),(286,1,1386536422,'127.0.0.1','/alien/users/viewList'),(287,2,1386517400,'127.0.0.1','/alien/base/logout'),(288,0,1386517318,'127.0.0.1','/alien/images/icons/favicon.ico'),(298,0,1386517413,'127.0.0.1','/alien/'),(289,0,1386517331,'127.0.0.1','/favicon.ico'),(290,0,1386517346,'127.0.0.1','/favicon.ico'),(291,0,1386517347,'127.0.0.1','/favicon.ico'),(292,0,1386517352,'127.0.0.1','/favicon.ico'),(293,0,1386517353,'127.0.0.1','/favicon.ico'),(294,0,1386517365,'127.0.0.1','/favicon.ico'),(295,0,1386517365,'127.0.0.1','/favicon.ico'),(296,0,1386517365,'127.0.0.1','/favicon.ico'),(297,0,1386517365,'127.0.0.1','/favicon.ico'),(299,0,1386517413,'127.0.0.1','/alien/images/icons/favicon.ico'),(300,1,1386802925,'127.0.0.1','/alien/base/logout'),(301,1,1386802938,'127.0.0.1','/alien/dashboard/home'),(302,1,1386885271,'127.0.0.1','/alien/base/logout'),(303,0,1386885271,'127.0.0.1','/alien/images/icons/favicon.ico'),(304,1,1386892656,'127.0.0.1','/alien/dashboard/home'),(305,1,1386897687,'127.0.0.1','/alien/base/logout'),(306,0,1386897687,'127.0.0.1','/alien/images/icons/favicon.ico'),(307,1,1387052974,'127.0.0.1','/alien/base/logout'),(308,1,1387056664,'127.0.0.1','/alien/base/logout'),(309,0,1387056664,'127.0.0.1','/alien/images/icons/favicon.ico'),(310,0,1387146509,'127.0.0.1','/favicon.ico'),(311,1,1387327437,'127.0.0.1','/alien/base/logout'),(312,1,1387328005,'127.0.0.1','/alien/base/logout'),(313,1,1387328013,'127.0.0.1','/alien/base/logout'),(314,1,1387328680,'127.0.0.1','/alien/base/logout'),(315,0,1387328680,'127.0.0.1','/alien/images/icons/favicon.ico'),(316,1,1387463344,'127.0.0.1','/alien/content/editTemplate/1'),(317,1,1387489661,'127.0.0.1','/alien/base/logout'),(318,0,1387489661,'127.0.0.1','/alien/images/icons/favicon.ico'),(319,1,1387641016,'127.0.0.1','/alien/content/editTemplate/1'),(320,1,1387673173,'127.0.0.1','/alien/base/logout'),(321,1,1387674455,'127.0.0.1','/alien/dashboard/messages'),(322,1,1387928637,'127.0.0.1','/alien/groups/viewList'),(323,0,1387930082,'127.0.0.1','/favicon.ico'),(324,1,1388091705,'127.0.0.1','/alien/base/logout'),(325,0,1388081517,'127.0.0.1','/xampp/php.php'),(326,0,1388081517,'127.0.0.1','/xampp/mysql.php'),(327,0,1388081517,'127.0.0.1','/xampp/ssi.shtml'),(328,0,1388081537,'127.0.0.1','/xampp/php.php'),(329,0,1388081537,'127.0.0.1','/xampp/mysql.php'),(330,0,1388081537,'127.0.0.1','/xampp/ssi.shtml'),(331,1,1388101017,'127.0.0.1','/alien/base/logout'),(332,1,1388093367,'127.0.0.1','/alien/users/edit/1'),(333,0,1388101017,'127.0.0.1','/alien/images/icons/favicon.ico'),(334,1,1388180125,'127.0.0.1','/alien/content/editTemplate/1'),(335,1,1388198271,'127.0.0.1','/favicon.ico'),(336,1,1388198662,'127.0.0.1','/alien/base/logout'),(337,0,1388198662,'127.0.0.1','/alien/images/icons/favicon.ico'),(338,1,1388255925,'127.0.0.1','/alien/display/NOP'),(339,1,1388271348,'127.0.0.1','/alien/display/NOP'),(340,1,1388283506,'127.0.0.1','/alien/base/logout'),(341,1,1388283545,'127.0.0.1','/alien/display/NOP'),(342,0,1388283523,'127.0.0.1','/browserconfig.xml'),(343,1,1388285813,'127.0.0.1','/alien/base/logout'),(344,1,1388283610,'127.0.0.1','/alien/base/logout'),(345,0,1388283610,'127.0.0.1','/alien/'),(346,0,1388285814,'127.0.0.1','/alien/images/icons/favicon.ico'),(347,1,1388352445,'127.0.0.1','/alien/display/NOP'),(348,1,1388365584,'127.0.0.1','/alien/display/NOP'),(349,1,1388437833,'127.0.0.1','/alien/display/NOP'),(350,1,1388443584,'127.0.0.1','/alien/base/logout'),(351,1,1388457256,'127.0.0.1','/alien/base/logout'),(352,1,1388457260,'127.0.0.1','/alien/base/logout'),(353,1,1388468656,'127.0.0.1','/alien/base/logout'),(354,0,1388468656,'127.0.0.1','/alien/images/icons/favicon.ico'),(355,1,1388533307,'127.0.0.1','/alien/base/logout'),(356,1,1388539009,'127.0.0.1','/alien/base/logout'),(357,1,1388539155,'127.0.0.1','/alien/base/logout'),(358,0,1388539155,'127.0.0.1','/alien/images/icons/favicon.ico'),(359,1,1388541148,'127.0.0.1','/alien/base/logout'),(360,1,1388541679,'127.0.0.1','/alien/base/logout'),(361,0,1388541679,'127.0.0.1','/alien/images/icons/favicon.ico'),(362,1,1388589002,'127.0.0.1','/alien/base/logout'),(363,1,1388593578,'127.0.0.1','/alien/base/logout'),(364,1,1388609173,'127.0.0.1','/alien/base/logout'),(365,0,1388609173,'127.0.0.1','/alien/images/icons/favicon.ico'),(366,1,1388622582,'127.0.0.1','/alien/base/logout'),(367,1,1388622951,'127.0.0.1','/alien/base/logout'),(368,1,1388626227,'127.0.0.1','/alien/base/logout'),(369,1,1388626117,'127.0.0.1','/alien/base/logout'),(370,0,1388626108,'127.0.0.1','/favicon.ico'),(371,1,1388626156,'127.0.0.1','/alien/display/NOP'),(372,1,1388626192,'127.0.0.1','/alien/base/logout'),(373,1,1388626223,'127.0.0.1','/alien/base/logout'),(374,0,1388626223,'127.0.0.1','/alien/'),(375,1,1388626727,'127.0.0.1','/alien/display/NOP'),(376,1,1388694494,'127.0.0.1','/alien/base/logout'),(377,1,1388712723,'127.0.0.1','/alien/base/logout'),(378,1,1388712739,'127.0.0.1','/alien/base/logout'),(379,1,1388713086,'127.0.0.1','/alien/base/logout'),(380,1,1388715210,'127.0.0.1','/alien/display/NOP'),(381,1,1388784867,'127.0.0.1','/alien/display/NOP'),(382,1,1388804572,'127.0.0.1','/alien/dashboard/messages'),(383,1,1388849397,'127.0.0.1','/alien/base/logout'),(384,1,1388850628,'127.0.0.1','/alien/base/logout'),(385,1,1388853528,'127.0.0.1','/alien/display/img/NOP'),(386,1,1388869919,'127.0.0.1','/alien/base/logout'),(387,1,1388880665,'127.0.0.1','/alien/base/logout'),(388,1,1388881556,'127.0.0.1','/alien/base/logout'),(389,1,1388880820,'127.0.0.1','/alien/dashboard/home'),(390,1,1388881641,'127.0.0.1','/alien/groups/viewList'),(391,1,1388942146,'127.0.0.1','/alien/dashboard/home'),(392,0,1389210699,'127.0.0.1','/kp/zadanie.html'),(393,1,1389533542,'127.0.0.1','/alien/base/logout'),(394,0,1389533542,'127.0.0.1','/alien/images/icons/favicon.ico'),(395,1,1389545846,'127.0.0.1','/alien/base/logout'),(396,1,1389545858,'127.0.0.1','/alien/base/logout'),(397,1,1389550846,'127.0.0.1','/alien/users/edit/1'),(398,1,1389570311,'127.0.0.1','/alien/dashboard/home'),(399,1,1389617273,'127.0.0.1','/alien/groups/viewList'),(400,1,1389785524,'127.0.0.1','/alien/content/editTemplate/1'),(401,1,1389871265,'127.0.0.1','/alien/base/logout'),(402,1,1389873119,'127.0.0.1','/alien/base/logout'),(403,1,1389873120,'127.0.0.1','/alien/dashboard/home'),(404,1,1389984075,'127.0.0.1','/alien/base/logout'),(405,1,1389984280,'127.0.0.1','/alien/base/logout'),(406,1,1389984557,'127.0.0.1','/alien/base/logout'),(407,1,1389985769,'127.0.0.1','/alien/users/edit/1'),(408,1,1390067131,'127.0.0.1','/alien/base/logout'),(409,0,1390067131,'127.0.0.1','/alien/images/icons/favicon.ico'),(410,1,1390141591,'127.0.0.1','/alien/users/edit/1'),(411,1,1390244232,'127.0.0.1','/alien/users/viewList'),(412,1,1390406812,'127.0.0.1','/alien/dashboard/NOP'),(413,1,1390600548,'127.0.0.1','/alien/dashboard/home'),(414,1,1390600572,'127.0.0.1','/alien/users/viewList'),(415,0,1390600563,'127.0.0.1','/alien/images/icons/favicon.ico'),(416,1,1390749091,'127.0.0.1','/alien/base/logout'),(417,1,1390755115,'127.0.0.1','/alien/base/logout'),(418,1,1390761436,'127.0.0.1','/alien/base/logout'),(419,1,1390761943,'127.0.0.1','/alien/base/logout'),(420,1,1390767115,'127.0.0.1','/alien/base/logout'),(421,1,1390769385,'127.0.0.1','/alien/users/edit/1'),(422,1,1390866937,'127.0.0.1','/alien/dashboard/profil'),(423,1,1390944868,'127.0.0.1','/alien/base/logout'),(424,1,1390944877,'127.0.0.1','/alien/base/logout'),(425,1,1390944900,'127.0.0.1','/alien/base/logout'),(426,1,1390944927,'127.0.0.1','/alien/base/logout'),(427,2,1390944939,'127.0.0.1','/alien/base/logout'),(428,1,1390945128,'127.0.0.1','/alien/base/logout'),(429,2,1390945212,'127.0.0.1','/alien/base/logout'),(430,1,1390945326,'127.0.0.1','/alien/base/logout'),(431,1,1390945345,'127.0.0.1','/alien/base/logout'),(432,1,1390951865,'127.0.0.1','/alien/dashboard/home'),(433,1,1391034694,'127.0.0.1','/alien/base/logout'),(434,1,1391034787,'127.0.0.1','/alien/base/logout'),(435,1,1391034816,'127.0.0.1','/alien/base/logout'),(436,1,1391034820,'127.0.0.1','/alien/base/logout'),(437,1,1391034832,'127.0.0.1','/alien/base/logout'),(438,1,1391035497,'127.0.0.1','/alien/base/logout'),(439,1,1391036268,'127.0.0.1','/alien/base/logout'),(440,1,1391041140,'127.0.0.1','/alien/base/logout'),(441,1,1391041940,'127.0.0.1','/alien/base/logout'),(442,1,1391041953,'127.0.0.1','/alien/base/logout'),(443,1,1391042487,'127.0.0.1','/alien/base/logout'),(444,1,1391042516,'127.0.0.1','/alien/base/logout'),(445,1,1391042554,'127.0.0.1','/alien/base/logout'),(446,1,1391042563,'127.0.0.1','/alien/base/logout'),(447,1,1391042588,'127.0.0.1','/alien/base/logout'),(448,1,1391042602,'127.0.0.1','/alien/base/logout'),(449,1,1391044890,'127.0.0.1','/alien/base/logout'),(450,0,1391044891,'127.0.0.1','/alien/images/icons/favicon.ico'),(451,1,1391112401,'127.0.0.1','/alien/base/logout'),(452,1,1391121256,'127.0.0.1','/alien/base/logout'),(453,1,1391125671,'127.0.0.1','/alien/base/logout'),(454,0,1391125671,'127.0.0.1','/alien/images/icons/favicon.ico'),(455,1,1391210294,'127.0.0.1','/alien/base/logout'),(456,1,1391221810,'127.0.0.1','/alien/template/edit/1'),(457,1,1391287646,'127.0.0.1','/alien/system/NOP'),(458,0,1391283196,'127.0.0.1','/alien_v2/index.php'),(459,1,1391295124,'127.0.0.1','/alien/dashboard/profil'),(460,1,1391356922,'127.0.0.1','/alien/system/NOP');
/*!40000 ALTER TABLE `test_authorization` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `test_config`
--

LOCK TABLES `test_config` WRITE;
/*!40000 ALTER TABLE `test_config` DISABLE KEYS */;
INSERT INTO `test_config` VALUES (1,'config','a:3:{s:8:\"pageHome\";i:1;s:7:\"page404\";i:-1;s:7:\"page500\";i:-1;}');
/*!40000 ALTER TABLE `test_config` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `test_content_containers`
--

LOCK TABLES `test_content_containers` WRITE;
/*!40000 ALTER TABLE `test_content_containers` DISABLE KEYS */;
INSERT INTO `test_content_containers` VALUES (1,1),(2,10);
/*!40000 ALTER TABLE `test_content_containers` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `test_content_folders`
--

LOCK TABLES `test_content_folders` WRITE;
/*!40000 ALTER TABLE `test_content_folders` DISABLE KEYS */;
INSERT INTO `test_content_folders` VALUES (1,'system',0),(2,'obsah',0),(3,'stranky',0),(4,'fotogaleria',2);
/*!40000 ALTER TABLE `test_content_folders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_content_items`
--

DROP TABLE IF EXISTS `test_content_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_content_items` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `container` int(8) DEFAULT NULL,
  `folder` int(8) DEFAULT '0',
  `type` varchar(30) NOT NULL,
  `name` varchar(70) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_content_items`
--

LOCK TABLES `test_content_items` WRITE;
/*!40000 ALTER TABLE `test_content_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_content_items` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `test_content_pages`
--

LOCK TABLES `test_content_pages` WRITE;
/*!40000 ALTER TABLE `test_content_pages` DISABLE KEYS */;
INSERT INTO `test_content_pages` VALUES (1,1,3,'uvod','uvod','','',NULL);
/*!40000 ALTER TABLE `test_content_pages` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_content_template_blocks`
--

LOCK TABLES `test_content_template_blocks` WRITE;
/*!40000 ALTER TABLE `test_content_template_blocks` DISABLE KEYS */;
INSERT INTO `test_content_template_blocks` VALUES (1,'topmenu'),(2,'slider'),(3,'main'),(4,'footer');
/*!40000 ALTER TABLE `test_content_template_blocks` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `test_content_templates`
--

LOCK TABLES `test_content_templates` WRITE;
/*!40000 ALTER TABLE `test_content_templates` DISABLE KEYS */;
INSERT INTO `test_content_templates` VALUES (1,1,'uvod','uvodna stranka','templates/uvod.php'),(2,1,'stranka','sablona pre podstranky','templates/stranka.php');
/*!40000 ALTER TABLE `test_content_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_content_widgets`
--

DROP TABLE IF EXISTS `test_content_widgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_content_widgets` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `item` int(8) NOT NULL,
  `type` varchar(30) NOT NULL,
  `page` int(8) DEFAULT NULL,
  `template` int(8) DEFAULT NULL,
  `container` int(2) DEFAULT NULL,
  `position` int(5) DEFAULT NULL,
  `visible` tinyint(4) NOT NULL DEFAULT '0',
  `class` varchar(50) DEFAULT NULL,
  `script` varchar(50) DEFAULT NULL,
  `params` text,
  PRIMARY KEY (`id`),
  KEY `PAGE` (`page`,`container`),
  KEY `TEMPLATE` (`template`,`container`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_content_widgets`
--

LOCK TABLES `test_content_widgets` WRITE;
/*!40000 ALTER TABLE `test_content_widgets` DISABLE KEYS */;
INSERT INTO `test_content_widgets` VALUES (1,1,'CodeItemWidget',NULL,1,3,1,1,NULL,NULL,NULL),(2,1,'CodeItemWidget',NULL,1,2,1,1,NULL,NULL,NULL),(3,1,'CodeItemWidget',NULL,1,2,1,1,NULL,NULL,NULL),(4,0,'CodeItemWidget',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL);
/*!40000 ALTER TABLE `test_content_widgets` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `test_folder_group_permissions`
--

LOCK TABLES `test_folder_group_permissions` WRITE;
/*!40000 ALTER TABLE `test_folder_group_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_folder_group_permissions` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `test_folder_user_permissions`
--

LOCK TABLES `test_folder_user_permissions` WRITE;
/*!40000 ALTER TABLE `test_folder_user_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_folder_user_permissions` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `test_galleries`
--

LOCK TABLES `test_galleries` WRITE;
/*!40000 ALTER TABLE `test_galleries` DISABLE KEYS */;
INSERT INTO `test_galleries` VALUES (4,19,'Formula 1 2011',NULL),(5,20,'GP2 2011/2012',NULL),(6,26,'Shakedown: Radical 2011/2012',NULL);
/*!40000 ALTER TABLE `test_galleries` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `test_group_members`
--

LOCK TABLES `test_group_members` WRITE;
/*!40000 ALTER TABLE `test_group_members` DISABLE KEYS */;
INSERT INTO `test_group_members` VALUES (1,4,1383691172),(1,6,1388085633),(1,1,1383691169),(2,3,1386876107);
/*!40000 ALTER TABLE `test_group_members` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `test_group_permissions`
--

LOCK TABLES `test_group_permissions` WRITE;
/*!40000 ALTER TABLE `test_group_permissions` DISABLE KEYS */;
INSERT INTO `test_group_permissions` VALUES (1,1,1),(1,2,1),(2,2,1),(2,3,1),(2,4,1),(2,5,1),(3,2,1),(3,3,1),(3,4,1),(4,6,1),(4,7,1),(4,8,1),(4,9,1);
/*!40000 ALTER TABLE `test_group_permissions` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `test_groups`
--

LOCK TABLES `test_groups` WRITE;
/*!40000 ALTER TABLE `test_groups` DISABLE KEYS */;
INSERT INTO `test_groups` VALUES (1,'admins',1366560049,'can change almost everything'),(4,'user managers',1366560049,'can change/add user groups'),(2,'content managers',1366560049,'full access to page content and templates'),(3,'moderator',1366560049,'can only change content of items'),(6,'team members',1366560049,'Dudo Aliens Racing Team');
/*!40000 ALTER TABLE `test_groups` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `test_logs`
--

LOCK TABLES `test_logs` WRITE;
/*!40000 ALTER TABLE `test_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_logs` ENABLE KEYS */;
UNLOCK TABLES;

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
  PRIMARY KEY (`id`),
  KEY `AUTHOR` (`author`),
  KEY `RECIPIENT` (`recipient`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_messages`
--

LOCK TABLES `test_messages` WRITE;
/*!40000 ALTER TABLE `test_messages` DISABLE KEYS */;
INSERT INTO `test_messages` VALUES (1,2,1,'Lorem ipsum dolor sit amet, eum nostrud assueverit ex. At tantas aperiam pro, qui ad zril sensibus. Id dolore omnium per, utinam admodum erroribus qui ne. Suas labore putant at pri, ornatus recusabo signiferumque cu sea.\r\n\r\nPrima placerat consulatu ad vix, agam quodsi ut sed. Usu accusam erroribus efficiendi in, eu mei alii nonumy eleifend, at eum erat sonet quaestio. Pri te magna ubique, ius dicat utamur cu. His et illum appellantur suscipiantur, mea detracto mentitum at, vidisse nominavi instructior per et. Esse nibh mel no, ne aeque appetere definiebas sed.',1383692217,NULL,'','',0,0),(2,1,2,'qweqwe',1383864635,NULL,'','',0,0),(3,1,2,'test odosielania správy',1383944523,NULL,'','',0,0),(4,2,1,'secko fachci... treba ptm pridat filter sprav podla prijemcu, tagy....',1383944636,1389563099,'','',0,0),(5,2,1,'a samozrejme este osetrit vstupy a vsetko pekne oeascapovat proti xss',1383944677,NULL,'','',0,0),(6,2,1,'a ani to skracovanie v nahlade teraz nieje prave najlepsie :)\r\nnajlepsie by bolo to prerobit cez akoze view helper alebo take daco tam si predrobit podobne metody',1383944743,NULL,'','',0,0),(7,2,2,'test sam sebe',1383944761,NULL,'','',0,0),(8,1,2,'test notifikacii pri spravach',1386508557,NULL,'','',0,0),(9,0,1,'test sam sebe',1390939090,1390939093,'','',0,0),(10,0,1,'Tvoje heslo bolo zmenené.',1390939524,1390939531,'','',0,1),(11,0,1,'Tvoje heslo bolo s okamžitou platnosťou zmenené.',1391029098,1391033934,'','',0,1),(12,0,1,'Tvoje heslo bolo s okamžitou platnosťou zmenené.',1391029111,1391033932,'','',0,1),(13,0,1,'Tvoje heslo bolo s okamžitou platnosťou zmenené.',1391029404,1391033932,'','',0,1),(14,0,1,'Tvoje heslo bolo s okamžitou platnosťou zmenené.',1391033249,1391033931,'','',0,1),(15,0,1,'Tvoje heslo bolo s okamžitou platnosťou zmenené.',1391033910,1391033930,'','',0,1),(16,0,1,'Tvoje heslo bolo s okamžitou platnosťou zmenené.',1391033923,1391033929,'','',0,1);
/*!40000 ALTER TABLE `test_messages` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `test_press_data`
--

LOCK TABLES `test_press_data` WRITE;
/*!40000 ALTER TABLE `test_press_data` DISABLE KEYS */;
INSERT INTO `test_press_data` VALUES (1,27,1,'test','test','<p>test</p>\r\n',NULL,NULL,1,0,1366737343,NULL,NULL);
/*!40000 ALTER TABLE `test_press_data` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `test_user_permissions`
--

LOCK TABLES `test_user_permissions` WRITE;
/*!40000 ALTER TABLE `test_user_permissions` DISABLE KEYS */;
INSERT INTO `test_user_permissions` VALUES (1,1,1383692358),(1,2,1388085626),(1,7,1388615539);
/*!40000 ALTER TABLE `test_user_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test_users`
--

DROP TABLE IF EXISTS `test_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test_users` (
  `id_u` int(8) NOT NULL AUTO_INCREMENT,
  `login` varchar(20) NOT NULL,
  `password` varchar(128) NOT NULL,
  `email` varchar(50) NOT NULL,
  `dateRegistered` int(10) NOT NULL,
  `activated` tinyint(1) NOT NULL DEFAULT '0',
  `lastActive` int(10) DEFAULT NULL,
  `ban` int(10) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `firstname` varchar(20) DEFAULT NULL,
  `surname` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_u`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test_users`
--

LOCK TABLES `test_users` WRITE;
/*!40000 ALTER TABLE `test_users` DISABLE KEYS */;
INSERT INTO `test_users` VALUES (1,'admin','$2y$11$itwycSJskXcvKdOYnStsCuwVlwOY2D4w1JNjdB5JqJ7XVLeOElBBK','admin@alien.com',1366410325,1,1391349722,0,0,'Dominik','Geršák'),(2,'moderator','$2y$11$6xfw.ZNzsyfBSD4Fs9cTnu/dCWZgDB8ZP9p0fn9OA9OYRTI9IUNSO','moderator@alien.com',1366410325,1,1386510200,0,0,'ferko','mkrvicka'),(4,'test','f91f3eed5bbf0eec3e4737e13c867a12d6ac1d47','',1383599330,0,NULL,0,1,'meno','priezvisko');
/*!40000 ALTER TABLE `test_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-02-02 15:13:08
