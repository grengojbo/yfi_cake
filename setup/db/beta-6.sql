-- MySQL dump 10.13  Distrib 5.1.41, for debian-linux-gnu (i486)
--
-- Host: localhost    Database: yfi
-- ------------------------------------------------------
-- Server version	5.1.41-3ubuntu12.10

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
-- Table structure for table `actions`
--

DROP TABLE IF EXISTS `actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actions` (
  `id` char(36) NOT NULL,
  `na_id` char(36) NOT NULL,
  `action` enum('execute') DEFAULT 'execute',
  `command` varchar(500) DEFAULT '',
  `status` enum('awaiting','fetched','replied') DEFAULT 'awaiting',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actions`
--

LOCK TABLES `actions` WRITE;
/*!40000 ALTER TABLE `actions` DISABLE KEYS */;
/*!40000 ALTER TABLE `actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auto_contacts`
--

DROP TABLE IF EXISTS `auto_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auto_contacts` (
  `id` char(36) NOT NULL,
  `auto_mac_id` char(36) NOT NULL DEFAULT '',
  `ip_address` varchar(15) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auto_contacts`
--

LOCK TABLES `auto_contacts` WRITE;
/*!40000 ALTER TABLE `auto_contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `auto_contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auto_groups`
--

DROP TABLE IF EXISTS `auto_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auto_groups` (
  `id` char(36) NOT NULL,
  `name` varchar(80) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auto_groups`
--

LOCK TABLES `auto_groups` WRITE;
/*!40000 ALTER TABLE `auto_groups` DISABLE KEYS */;
INSERT INTO `auto_groups` VALUES ('4b41de4a-6048-407c-b2a0-19dda509ff00','Network','2010-01-04 14:25:46','2010-01-04 14:25:46'),('4b42e302-a4e8-4d9d-8d35-3b96a509ff00','OpenVPN','2010-01-05 08:58:10','2010-01-05 08:58:10'),('4b444e2a-e32c-4f34-90b7-2252a509ff00','Wireless','2010-01-06 10:47:38','2010-01-06 10:47:38');
/*!40000 ALTER TABLE `auto_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auto_macs`
--

DROP TABLE IF EXISTS `auto_macs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auto_macs` (
  `id` char(36) NOT NULL,
  `name` varchar(17) NOT NULL,
  `contact_ip` varchar(17) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auto_macs`
--

LOCK TABLES `auto_macs` WRITE;
/*!40000 ALTER TABLE `auto_macs` DISABLE KEYS */;
/*!40000 ALTER TABLE `auto_macs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auto_setups`
--

DROP TABLE IF EXISTS `auto_setups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auto_setups` (
  `id` char(36) NOT NULL,
  `auto_group_id` char(36) NOT NULL DEFAULT '',
  `auto_mac_id` char(36) NOT NULL DEFAULT '',
  `description` varchar(80) NOT NULL,
  `value` varchar(2000) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auto_setups`
--

LOCK TABLES `auto_setups` WRITE;
/*!40000 ALTER TABLE `auto_setups` DISABLE KEYS */;
/*!40000 ALTER TABLE `auto_setups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `batches`
--

DROP TABLE IF EXISTS `batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `batches` (
  `id` char(36) NOT NULL,
  `name` varchar(40) NOT NULL,
  `realm_id` char(36) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `batches`
--

LOCK TABLES `batches` WRITE;
/*!40000 ALTER TABLE `batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `batches_vouchers`
--

DROP TABLE IF EXISTS `batches_vouchers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `batches_vouchers` (
  `batch_id` char(36) NOT NULL,
  `voucher_id` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `batches_vouchers`
--

LOCK TABLES `batches_vouchers` WRITE;
/*!40000 ALTER TABLE `batches_vouchers` DISABLE KEYS */;
/*!40000 ALTER TABLE `batches_vouchers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `billing_plan_realms`
--

DROP TABLE IF EXISTS `billing_plan_realms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billing_plan_realms` (
  `id` char(36) NOT NULL,
  `billing_plan_id` char(36) NOT NULL,
  `realm_id` char(36) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billing_plan_realms`
--

LOCK TABLES `billing_plan_realms` WRITE;
/*!40000 ALTER TABLE `billing_plan_realms` DISABLE KEYS */;
INSERT INTO `billing_plan_realms` VALUES ('4a50f523-a0d4-4d4b-bb13-4b18a509ff00','4a50b4f8-e4f0-4359-bac0-3d21a509ff00','49d09ec6-5480-45d4-a5ae-2b0ea509ff00','2009-07-05 20:46:59','2009-07-05 20:46:59'),('4a56ab5c-e71c-4b4d-945b-572ba509ff00','4a562c4c-7f50-4a27-b48e-2b8ba509ff00','49d09ec6-5480-45d4-a5ae-2b0ea509ff00','2009-07-10 04:45:48','2009-07-10 04:45:48');
/*!40000 ALTER TABLE `billing_plan_realms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `billing_plans`
--

DROP TABLE IF EXISTS `billing_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billing_plans` (
  `id` char(36) NOT NULL,
  `name` varchar(40) NOT NULL,
  `currency` varchar(20) NOT NULL,
  `subscription` decimal(9,2) DEFAULT '0.00',
  `time_unit` decimal(10,9) DEFAULT '0.000000000',
  `data_unit` decimal(10,9) DEFAULT '0.000000000',
  `free_data` int(12) DEFAULT '0',
  `free_time` int(12) DEFAULT '0',
  `discount` decimal(5,2) DEFAULT '0.00',
  `tax` decimal(5,2) DEFAULT '0.00',
  `extra_time` decimal(3,2) DEFAULT '1.00',
  `extra_data` decimal(3,2) DEFAULT '1.00',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billing_plans`
--

LOCK TABLES `billing_plans` WRITE;
/*!40000 ALTER TABLE `billing_plans` DISABLE KEYS */;
INSERT INTO `billing_plans` VALUES ('4a562c4c-7f50-4a27-b48e-2b8ba509ff00','Basic500','Rand','100.00','0.000000000','0.000300000',0,0,'2.00','1.00','1.00','1.00','2009-07-09 19:43:40','2009-07-10 04:46:35');
/*!40000 ALTER TABLE `billing_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `checks`
--

DROP TABLE IF EXISTS `checks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checks` (
  `id` char(36) NOT NULL,
  `name` varchar(40) NOT NULL,
  `value` varchar(40) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `checks`
--

LOCK TABLES `checks` WRITE;
/*!40000 ALTER TABLE `checks` DISABLE KEYS */;
INSERT INTO `checks` VALUES ('49e494b2-336c-4b46-abfe-4ea8a509ff00','radacct_last_id','107','2009-04-14 15:50:42','2012-04-06 16:20:02'),('49e4e67c-ff94-40cf-83b1-6672a509ff00','radius_restart','1','2009-04-14 21:39:40','2012-04-06 16:05:05'),('4a29f7d7-fa74-462e-b44c-03c8a509ff00','notify_check','1','2009-06-06 07:00:07','2012-04-06 17:25:02');
/*!40000 ALTER TABLE `checks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `credits`
--

DROP TABLE IF EXISTS `credits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `credits` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL DEFAULT '',
  `realm_id` char(36) NOT NULL DEFAULT '',
  `used_by_id` char(36) DEFAULT NULL,
  `expires` datetime DEFAULT NULL,
  `time` bigint(20) DEFAULT NULL,
  `data` bigint(20) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credits`
--

LOCK TABLES `credits` WRITE;
/*!40000 ALTER TABLE `credits` DISABLE KEYS */;
/*!40000 ALTER TABLE `credits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `devices`
--

DROP TABLE IF EXISTS `devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devices` (
  `id` char(36) NOT NULL,
  `name` varchar(17) NOT NULL,
  `description` varchar(40) NOT NULL,
  `user_id` char(36) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devices`
--

LOCK TABLES `devices` WRITE;
/*!40000 ALTER TABLE `devices` DISABLE KEYS */;
/*!40000 ALTER TABLE `devices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `extra_services`
--

DROP TABLE IF EXISTS `extra_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `extra_services` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `title` varchar(50) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `amount` decimal(9,2) DEFAULT '0.00',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `extra_services`
--

LOCK TABLES `extra_services` WRITE;
/*!40000 ALTER TABLE `extra_services` DISABLE KEYS */;
/*!40000 ALTER TABLE `extra_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `extras`
--

DROP TABLE IF EXISTS `extras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `extras` (
  `id` char(36) NOT NULL,
  `type` enum('data','time') DEFAULT 'data',
  `value` varchar(40) NOT NULL DEFAULT '',
  `user_id` char(36) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `extras`
--

LOCK TABLES `extras` WRITE;
/*!40000 ALTER TABLE `extras` DISABLE KEYS */;
/*!40000 ALTER TABLE `extras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group_rights`
--

DROP TABLE IF EXISTS `group_rights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_rights` (
  `id` char(36) NOT NULL,
  `group_id` char(36) NOT NULL,
  `right_id` char(36) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_rights`
--

LOCK TABLES `group_rights` WRITE;
/*!40000 ALTER TABLE `group_rights` DISABLE KEYS */;
INSERT INTO `group_rights` VALUES ('499ef51e-7b80-4819-a85c-2f51a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','499ef500-c76c-4897-9bde-190da509ff00',1,'2009-02-20 20:23:26','2009-02-20 20:23:26'),('499effd9-93c4-49b2-81c8-7acfa509ff00','499ef44e-42e8-4615-8d51-2f51a509ff00','499ef500-c76c-4897-9bde-190da509ff00',1,'2009-02-20 21:09:13','2009-02-20 21:09:30'),('49ab94fc-129c-4d25-94e4-19dba509ff00','499ef44e-42e8-4615-8d51-2f51a509ff00','49ab946c-78cc-4b3f-ad02-2755a509ff00',1,'2009-03-02 10:12:44','2009-03-02 10:12:44'),('49ab9512-75c8-400b-96ec-19dba509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49ab946c-78cc-4b3f-ad02-2755a509ff00',1,'2009-03-02 10:13:06','2009-03-02 10:13:06'),('49ae2c01-2b20-4a0b-ad41-1972a509ff00','499ef44e-42e8-4615-8d51-2f51a509ff00','49ae2bc4-ccd8-4660-81af-2ce7a509ff00',1,'2009-03-04 09:21:37','2009-03-04 09:21:37'),('49ae4ea5-3eb8-460e-905e-7146a509ff00','499ef44e-42e8-4615-8d51-2f51a509ff00','49ae4e7f-73b8-46c7-864c-7146a509ff00',1,'2009-03-04 11:49:25','2009-03-04 11:49:25'),('49ba5393-9e14-4a3d-a266-1609a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49ba51f9-26a0-4365-bf83-6ed1a509ff00',1,'2009-03-13 14:37:39','2009-03-13 14:37:39'),('49ba539f-ad78-4d4f-8ae2-1609a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49ba5218-ddbc-47eb-8711-661ea509ff00',1,'2009-03-13 14:37:51','2009-03-13 14:37:51'),('49ba53b5-23bc-40bd-ad80-6ed1a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49ba522f-0ff8-4ee3-b044-1854a509ff00',1,'2009-03-13 14:38:13','2009-03-13 14:38:13'),('49ba53c7-6a98-4ae3-a236-661ea509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49ba5245-2ba4-49d9-b806-1854a509ff00',0,'2009-03-13 14:38:31','2009-03-13 14:38:31'),('49ba53e8-d9dc-4caa-a32e-1854a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49ba525b-c0ec-43ab-a149-6edea509ff00',1,'2009-03-13 14:39:04','2009-03-13 14:39:04'),('49ba53f5-ee8c-4500-83f9-1854a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49ba527b-1f3c-4490-ab05-6edea509ff00',1,'2009-03-13 14:39:17','2009-03-13 14:39:17'),('49ba5410-8a34-4d75-8555-1854a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49ba5293-ab1c-420e-a5d3-6edea509ff00',1,'2009-03-13 14:39:44','2009-03-13 14:39:44'),('49ba541b-048c-48fc-8357-1854a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49ba52b6-01b8-4905-ac5d-66b6a509ff00',1,'2009-03-13 14:39:55','2009-03-13 14:39:55'),('49be4018-5810-40f2-aa91-3926a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49be4003-6570-4746-a8d3-3926a509ff00',1,'2009-03-16 14:03:36','2009-03-16 14:03:36'),('49bf5fff-1498-4bca-ad0b-285aa509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49bf5fe6-b858-42e6-b6f9-51e1a509ff00',1,'2009-03-17 10:31:59','2009-03-17 10:31:59'),('49bf60ba-d828-41c8-b4e1-3491a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49bf60a9-1c58-49f3-83c3-3491a509ff00',1,'2009-03-17 10:35:06','2009-03-17 10:35:06'),('49bf6288-8250-4f3d-8cf6-2073a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49bf6239-a734-4082-a33b-51e2a509ff00',1,'2009-03-17 10:42:48','2009-03-17 10:42:48'),('49bf6706-0070-48ec-93ba-3a21a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49bf66f9-e324-404f-a9f0-3a21a509ff00',0,'2009-03-17 11:01:58','2009-03-17 11:03:14'),('49bf68d8-0b48-4764-be66-2073a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49bf68a7-60d4-41e8-b2bb-2073a509ff00',1,'2009-03-17 11:09:44','2009-03-17 11:09:44'),('49bf7dc3-fc2c-4d39-b246-51e1a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49bf7d31-f050-42ae-b2fe-339ba509ff00',1,'2009-03-17 12:38:59','2009-03-17 12:38:59'),('49bf7dd2-decc-4491-b16d-51e1a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49bf7d4d-5990-46c6-b22a-3491a509ff00',1,'2009-03-17 12:39:14','2009-03-17 12:39:14'),('49bfafe4-bca4-4f25-8dde-7acca509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49bfafd8-fcb4-4f9b-85c8-7acca509ff00',1,'2009-03-17 16:12:52','2009-03-17 16:12:52'),('49c1012f-79cc-448e-b42a-5a75a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49c10118-d8e0-4483-a2fd-69fba509ff00',1,'2009-03-18 16:11:59','2009-03-18 16:11:59'),('49cfb771-f4bc-49a0-830f-21e2a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49cfb1ab-6bf0-4bc2-9f5a-21e2a509ff00',1,'2009-03-29 20:01:21','2009-03-29 20:01:21'),('49cfb781-1b88-458b-a719-21e2a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49cfb1d1-39ec-4b14-908e-21f0a509ff00',1,'2009-03-29 20:01:37','2009-03-29 20:01:37'),('49cfb8c8-cfd8-478c-b9e0-563ea509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49cfb442-d66c-4861-aeed-150ea509ff00',1,'2009-03-29 20:07:04','2009-03-29 20:07:04'),('49cfbd11-ab24-4cad-b54c-6b04a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49cfb4c0-8418-46c2-9d9b-21e2a509ff00',1,'2009-03-29 20:25:21','2009-03-29 20:25:21'),('49cfbd66-e724-4fd0-88f3-171fa509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49cfb4e2-fa7c-4103-a509-21f0a509ff00',1,'2009-03-29 20:26:46','2009-03-29 20:26:46'),('49cfbd76-e8e4-481f-8613-171fa509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49cfb500-7d84-4d51-adac-21a0a509ff00',1,'2009-03-29 20:27:02','2009-03-29 20:27:02'),('49cfc13a-7d18-45d3-a8c5-5641a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49cfb5ce-01bc-4559-9690-5642a509ff00',1,'2009-03-29 20:43:06','2009-03-29 20:43:06'),('49cfc148-cb64-4572-8447-5641a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49cfb5e5-ce94-4442-a3e3-6b04a509ff00',1,'2009-03-29 20:43:20','2009-03-29 20:43:20'),('49cfc155-262c-4e58-8f81-5641a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49cfb616-0560-477f-b07d-563fa509ff00',1,'2009-03-29 20:43:33','2009-03-29 20:43:33'),('49cfc163-1b94-43d5-bdf9-5641a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49cfb636-b1c4-4f72-b360-171fa509ff00',1,'2009-03-29 20:43:47','2009-03-29 20:43:47'),('49cfc1da-57ac-4f0b-a58e-563ea509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49cfb40a-1144-42de-947d-171fa509ff00',1,'2009-03-29 20:45:46','2009-03-29 20:45:46'),('49e1d90b-a838-48f4-a698-6545a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49e1d876-9718-4ba0-816f-75eba509ff00',1,'2009-04-12 14:05:31','2009-04-12 14:05:31'),('49e1d918-eebc-4c91-9b4e-6545a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49e1d8aa-152c-40c9-8571-35f4a509ff00',1,'2009-04-12 14:05:44','2009-04-12 14:05:44'),('49e29fc2-f4a4-4b3d-a553-75eba509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49e29ed9-d918-4650-ae41-6541a509ff00',1,'2009-04-13 04:13:22','2009-04-13 04:13:22'),('49e29fd3-51f8-443a-be0b-75eba509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49e29f0f-70c0-4c73-bd2b-6541a509ff00',1,'2009-04-13 04:13:39','2009-04-13 04:13:39'),('49e29ff0-df08-46d4-abf6-75eba509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49e29f4c-2e3c-4e2e-ae52-6541a509ff00',1,'2009-04-13 04:14:08','2009-04-13 04:14:08'),('49e29ffd-9dc8-465d-a4ec-75eba509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49e29f69-ecc8-4bc1-92d8-6541a509ff00',1,'2009-04-13 04:14:21','2009-04-13 04:14:21'),('49e2a00b-e334-4b4d-88c4-75eba509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49e29f90-f0b8-411e-ad74-6541a509ff00',1,'2009-04-13 04:14:35','2009-04-13 04:14:35'),('4a1806a8-edec-4de8-a221-3239a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a18066d-d360-402c-8164-2f8aa509ff00',1,'2009-05-23 16:22:32','2009-05-23 16:22:32'),('4a18cf91-64b4-4361-a686-1995a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a18cf79-fa60-44a3-a555-1995a509ff00',1,'2009-05-24 06:39:45','2009-05-24 06:39:45'),('4a18d001-bf9c-4594-8ba2-2f88a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a18cff3-822c-4b78-bca9-2f88a509ff00',1,'2009-05-24 06:41:37','2009-05-24 06:41:37'),('4a18d26a-eb60-496c-a304-1992a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a18d25f-9cfc-427f-b04f-1992a509ff00',1,'2009-05-24 06:51:54','2009-05-24 06:51:54'),('4a18d4f8-77f8-44e9-8b40-3239a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a18d4e9-ada8-4ef5-b3ed-3239a509ff00',1,'2009-05-24 07:02:48','2009-05-24 07:02:48'),('4a18d5a8-2174-4ffa-9bab-1994a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a18d595-e5c4-45cc-9e90-1994a509ff00',1,'2009-05-24 07:05:44','2009-05-24 07:05:44'),('4a18d768-e874-43a9-ba61-1994a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a18d75c-85e8-4bce-80c6-1994a509ff00',1,'2009-05-24 07:13:12','2009-05-24 07:13:12'),('4a18eea4-6198-4fb6-a4fc-26f2a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a18ee98-f6d4-4d93-bcaa-26f2a509ff00',1,'2009-05-24 08:52:20','2009-05-24 08:52:20'),('4a18ef2e-4f68-48a2-b91b-1241a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a18d071-d67c-4079-8d28-1993a509ff00',1,'2009-05-24 08:54:38','2009-05-24 08:54:38'),('4a18f14a-3f4c-4693-b5f8-26f1a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a18f0ba-ff80-40d6-818a-12b1a509ff00',1,'2009-05-24 09:03:38','2009-05-24 09:03:38'),('4a18f2d9-2f14-4110-8739-26f3a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a18f26d-49dc-4553-bf68-26f2a509ff00',1,'2009-05-24 09:10:17','2009-05-24 09:10:17'),('4a18f2e4-8fe0-422a-82a0-26f3a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a18f285-3980-4359-acb9-26f4a509ff00',1,'2009-05-24 09:10:28','2009-05-24 09:10:28'),('4a18f3a1-1bcc-43f1-bedc-12a4a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a18f395-97a8-4d3a-b761-12a4a509ff00',1,'2009-05-24 09:13:37','2009-05-24 09:13:37'),('4a18f422-ee80-4169-af9e-1291a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a18f3cf-60f8-4cec-8a09-26f1a509ff00',1,'2009-05-24 09:15:46','2009-05-24 09:15:46'),('4a18f42c-54cc-43ad-a567-1291a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a18f3f7-0584-40d3-b170-1241a509ff00',1,'2009-05-24 09:15:56','2009-05-24 09:15:56'),('4a18f438-83e4-49a8-a2b0-1291a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a18f415-3eb0-4bb6-85c8-1291a509ff00',1,'2009-05-24 09:16:08','2009-05-24 09:16:08'),('4a1932bc-7ce4-43ef-8663-129aa509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a1932a9-e81c-4058-bb37-129aa509ff00',1,'2009-05-24 13:42:52','2009-05-24 13:42:52'),('4a617cbe-4cb4-4526-9dcb-19caa509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a617c04-a940-4f3e-a020-19c9a509ff00',1,'2009-07-18 09:41:50','2009-07-18 09:41:50'),('4a618bcb-4c38-47de-ba8b-09fda509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a618ae9-3acc-44d5-aecf-4324a509ff00',1,'2009-07-18 10:46:03','2009-07-18 10:46:03'),('4a618bdd-d1bc-4b21-b7eb-09fda509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a618b0e-a9bc-45c5-bb2e-431aa509ff00',1,'2009-07-18 10:46:21','2009-07-18 10:46:21'),('4a618bec-744c-4f82-a70e-09fda509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a618b3e-76b8-4fff-b772-19c7a509ff00',1,'2009-07-18 10:46:36','2009-07-18 10:46:36'),('4a618c0e-9fe0-4f60-a9f4-09fda509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a618b63-c3ac-4140-a4c6-19c9a509ff00',1,'2009-07-18 10:47:10','2009-07-18 10:47:10'),('4a618c23-38f0-4f9d-abaa-09fda509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a618b80-22c4-498a-ac5e-0a07a509ff00',1,'2009-07-18 10:47:31','2009-07-18 10:47:31'),('4a619586-04e8-403f-847a-09fda509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a619486-6aa8-4511-9b27-19caa509ff00',1,'2009-07-18 11:27:34','2009-07-18 11:27:34'),('4a619597-5bd4-4f74-ad29-09fda509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a6194ab-c9cc-45b4-976a-4298a509ff00',1,'2009-07-18 11:27:51','2009-07-18 11:27:51'),('4a6195a5-6114-49ea-845d-09fda509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a6194db-920c-4e1b-9470-4324a509ff00',1,'2009-07-18 11:28:05','2009-07-18 11:28:05'),('4a6195b3-df14-463d-8252-09fda509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a619505-3378-4b35-b642-19c7a509ff00',1,'2009-07-18 11:28:19','2009-07-18 11:28:19'),('4a6195c2-1b9c-429f-845d-09fda509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a61952c-5220-41a0-a2e7-19c9a509ff00',1,'2009-07-18 11:28:34','2009-07-18 11:28:34'),('4a6195ce-3dd4-455f-9f45-09fda509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a61954f-e000-402b-9018-0a07a509ff00',1,'2009-07-18 11:28:46','2009-07-18 11:28:46'),('4a6217d2-dc4c-4a97-9534-431aa509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a62177d-84a8-40d4-94bf-09fda509ff00',1,'2009-07-18 20:43:30','2009-07-18 20:43:30'),('4a6217df-7a1c-4689-be8f-431aa509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a6217a2-2608-484a-be64-0a07a509ff00',1,'2009-07-18 20:43:43','2009-07-18 20:43:43'),('4a6217eb-bd64-4c83-bcef-431aa509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a6217b8-fed8-4ac4-ba3e-4298a509ff00',1,'2009-07-18 20:43:55','2009-07-18 20:43:55'),('4a6f7873-6174-447c-ba1c-60b5a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a6f7860-42ec-4f61-b946-60b5a509ff00',1,'2009-07-29 00:15:15','2009-07-29 00:15:15'),('4b29dd87-8e3c-43cc-bacf-4cb0a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4b29d6aa-9afc-4568-a64a-1a02a509ff00',1,'2009-12-17 09:28:07','2009-12-17 09:28:07'),('4b29ddad-2894-4bc8-b62c-4cb0a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4b29d6cc-41c4-4341-b71d-1a04a509ff00',1,'2009-12-17 09:28:45','2009-12-17 09:28:45'),('4b29ddbf-4300-4cff-a4fb-4cb0a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4b29d71b-eb28-47ca-9bb9-4cb0a509ff00',1,'2009-12-17 09:29:03','2009-12-17 09:29:03'),('4b29dddc-0f1c-4268-a2b4-593ca509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4b29d73e-a938-4d73-8641-593ca509ff00',1,'2009-12-17 09:29:32','2009-12-17 09:29:32'),('4b29ddf3-ca78-4c88-93dc-593ca509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4b29d77b-5c00-4862-a683-1a00a509ff00',1,'2009-12-17 09:29:55','2009-12-17 09:29:55'),('4b29de05-dcb8-4c60-9745-593ca509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4b29d797-9e5c-401b-bffa-1a01a509ff00',1,'2009-12-17 09:30:13','2009-12-17 09:30:13'),('4b29de50-22d8-42d1-a48b-1a00a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4b29d7f4-3600-41b5-a20d-4d55a509ff00',1,'2009-12-17 09:31:28','2009-12-17 09:31:28'),('4b5de52a-cc54-48da-9d26-53f3a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4b5de501-b0b4-4ee2-9b09-53e9a509ff00',1,'2010-01-25 20:38:34','2010-01-25 20:38:34'),('4b91d65b-49c4-47c6-9157-13a4a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49ab942c-f9bc-46ad-b119-19dea509ff00',0,'2010-03-06 06:13:15','2010-03-06 06:13:15'),('4b91d68e-9fb8-4ab6-8fcf-1989a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49ae2bc4-ccd8-4660-81af-2ce7a509ff00',0,'2010-03-06 06:14:06','2010-03-06 06:14:06'),('4b91d6ea-0fcc-4858-b529-15e0a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a193830-1b54-4b9b-8858-542ea509ff00',0,'2010-03-06 06:15:38','2010-03-06 06:15:38'),('4b91d70b-772c-47c0-a263-1986a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49ae4e7f-73b8-46c7-864c-7146a509ff00',0,'2010-03-06 06:16:11','2010-03-06 06:16:11'),('4b91f163-ac3c-47e8-9063-52a9a509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b91e84b-64c4-48fb-b58b-13a4a509ff00',0,'2010-03-06 08:08:35','2010-03-06 08:08:35'),('4b91f173-408c-4652-839d-52a9a509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b91e887-eb50-4858-8dfb-1987a509ff00',0,'2010-03-06 08:08:51','2010-03-06 08:08:51'),('4b96a075-3b64-45d2-8a20-308fa509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b969fb2-29ec-4158-9c9d-1999a509ff00',0,'2010-03-09 21:24:37','2010-03-09 21:24:37'),('4b96a08d-03d0-4332-ad5c-199aa509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b969fd6-c014-4b70-bf18-199ca509ff00',1,'2010-03-09 21:25:01','2010-03-09 21:25:01'),('4b96a09f-a260-4e7f-9bca-199aa509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b969ff8-14c0-40ea-b198-308da509ff00',1,'2010-03-09 21:25:19','2010-03-09 21:25:19'),('4b96a108-1c70-4477-986b-199ba509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b96a01a-d620-423a-95a2-308ea509ff00',1,'2010-03-09 21:27:04','2010-03-09 21:27:04'),('4b96a115-6c68-4cdf-be57-199ba509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b96a036-2900-43e8-9c7b-3090a509ff00',1,'2010-03-09 21:27:17','2010-03-09 21:27:17'),('4b96a121-1b24-4245-96bb-199ba509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b96a052-fe60-4c9a-81cb-308fa509ff00',1,'2010-03-09 21:27:29','2010-03-09 21:27:29'),('4b97e1bb-6d24-41d3-9b2b-198fa509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b97e0ca-1594-4a40-8625-5202a509ff00',1,'2010-03-10 20:15:23','2010-03-10 20:15:23'),('4b97e1cb-74ec-40ca-96f3-198fa509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b97e10c-492c-43f3-8a8b-198ba509ff00',1,'2010-03-10 20:15:39','2010-03-10 20:15:39'),('4b97e1db-7d08-4329-a5be-198fa509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b97e12e-7eb8-432a-98fa-19aca509ff00',1,'2010-03-10 20:15:55','2010-03-10 20:15:55'),('4b97e1ee-bff8-4385-8a84-198fa509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b97e16a-0444-4c5c-9bd0-1943a509ff00',1,'2010-03-10 20:16:14','2010-03-10 20:16:14'),('4b97e1fb-6f28-4656-a5bc-198fa509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b97e1a5-21ec-475e-a013-198fa509ff00',1,'2010-03-10 20:16:27','2010-03-10 20:16:27'),('4b9857e5-fda8-4f99-a755-5202a509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b985797-4e3c-48a8-9faf-5201a509ff00',0,'2010-03-11 04:39:33','2010-03-11 04:41:23'),('4b9857f7-d730-49d1-8963-5202a509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b9857b9-66dc-4dbe-ae4a-5c7da509ff00',0,'2010-03-11 04:39:51','2010-03-11 04:41:35'),('4b994567-e814-436d-810a-6c90a509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b994552-4640-4c95-a747-6c90a509ff00',0,'2010-03-11 21:32:55','2010-03-11 21:32:55'),('4c5863ce-8020-4e51-b810-0545a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4c5863ae-3984-48e3-adde-0544a509ff00',1,'2010-08-03 20:45:34','2010-08-03 20:45:34'),('4e7d96c5-508c-42c5-992b-0ea8a509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4e7d969a-efd8-43e5-bcc8-0eaaa509ff00',0,'2011-09-24 10:37:25','2011-09-24 10:37:25'),('4e7d9f4a-0154-4152-a3a7-03faa509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4e7d9d80-481c-4e70-853b-0fb3a509ff00',1,'2011-09-24 11:13:46','2011-09-24 11:13:46'),('4e7d9f69-4578-47d3-8956-0ea9a509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4e7d9e4a-0c3c-4e87-9e9f-0eaba509ff00',1,'2011-09-24 11:14:17','2011-09-24 11:14:17'),('4e7d9f78-786c-436b-bc54-0ea9a509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4e7d9e5c-f788-4a29-b68b-0eaba509ff00',1,'2011-09-24 11:14:32','2011-09-24 11:14:32');
/*!40000 ALTER TABLE `group_rights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id` char(36) NOT NULL,
  `name` varchar(127) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES ('499ef44e-42e8-4615-8d51-2f51a509ff00','Administrators','2009-02-20 20:19:58','2009-02-20 20:19:58'),('499ef455-acf4-469e-991b-2f51a509ff00','Access Providers','2009-02-20 20:20:05','2009-02-20 20:20:05'),('499ef45a-dc24-42b1-8d99-2f51a509ff00','Users','2009-02-20 20:20:10','2009-02-20 20:20:10');
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `heartbeats`
--

DROP TABLE IF EXISTS `heartbeats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `heartbeats` (
  `id` char(36) NOT NULL,
  `na_id` char(36) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `heartbeats`
--

LOCK TABLES `heartbeats` WRITE;
/*!40000 ALTER TABLE `heartbeats` DISABLE KEYS */;
/*!40000 ALTER TABLE `heartbeats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` char(36) NOT NULL,
  `billing_plan_id` char(36) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=78 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (64,'4a0b0cb6-9718-4221-886b-3706a509ff00','4a562c4c-7f50-4a27-b48e-2b8ba509ff00','2009-03-01 00:00:00','2009-03-31 23:59:59','2009-07-20 21:22:47','2009-07-20 21:22:47'),(51,'4a280b2f-5100-45f4-8bf9-486dc4072569','4a562c4c-7f50-4a27-b48e-2b8ba509ff00','2009-05-01 00:00:00','2009-05-31 23:59:59','2009-07-09 21:04:01','2009-07-09 21:04:01'),(65,'4a0b0cb6-9718-4221-886b-3706a509ff00','4a562c4c-7f50-4a27-b48e-2b8ba509ff00','2009-04-01 00:00:00','2009-04-30 23:59:59','2009-07-21 04:31:10','2009-07-21 04:31:10'),(61,'4a2bb886-89dc-4152-9d3d-49d3c4072569','4a562c4c-7f50-4a27-b48e-2b8ba509ff00','2009-07-01 00:00:00','2009-07-31 23:59:59','2009-07-17 09:31:02','2009-07-17 09:31:02'),(54,'4a280b2f-5100-45f4-8bf9-486dc4072569','4a562c4c-7f50-4a27-b48e-2b8ba509ff00','2009-06-01 00:00:00','2009-06-30 23:59:59','2009-07-10 04:38:23','2009-07-10 04:38:23'),(66,'4a0b0cb6-9718-4221-886b-3706a509ff00','4a562c4c-7f50-4a27-b48e-2b8ba509ff00','2009-05-01 00:00:00','2009-05-31 23:59:59','2009-07-21 04:31:48','2009-07-21 04:31:48'),(63,'4a0b0cb6-9718-4221-886b-3706a509ff00','4a562c4c-7f50-4a27-b48e-2b8ba509ff00','2009-02-01 00:00:00','2009-02-28 23:59:59','2009-07-20 16:06:08','2009-07-20 16:06:08'),(76,'4a6c96f6-2f30-42c9-9d2c-68bca509ff00','4a562c4c-7f50-4a27-b48e-2b8ba509ff00','2009-07-01 00:00:00','2009-07-31 23:59:59','2009-07-26 19:48:58','2009-07-26 19:48:58'),(75,'4a0b0cb6-9718-4221-886b-3706a509ff00','4a562c4c-7f50-4a27-b48e-2b8ba509ff00','2009-06-01 00:00:00','2009-06-30 23:59:59','2009-07-26 11:05:45','2009-07-26 11:05:45');
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `iptable_rules`
--

DROP TABLE IF EXISTS `iptable_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iptable_rules` (
  `id` char(36) NOT NULL,
  `profile_id` char(36) NOT NULL,
  `priority` int(3) NOT NULL DEFAULT '100',
  `action` enum('allow','block') NOT NULL DEFAULT 'allow',
  `destination` varchar(100) NOT NULL,
  `protocol` varchar(10) NOT NULL,
  `port` int(7) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `iptable_rules`
--

LOCK TABLES `iptable_rules` WRITE;
/*!40000 ALTER TABLE `iptable_rules` DISABLE KEYS */;
/*!40000 ALTER TABLE `iptable_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages` (
  `id` char(36) NOT NULL,
  `name` varchar(100) NOT NULL,
  `iso_name` varchar(50) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES ('4a80e849-5300-46b5-9b64-4ba1a509ff00','English','en','2009-08-11 05:40:57','2009-08-11 05:40:57'),('4a80e867-24d4-4824-8270-3e74a509ff00','Afrikaans','af_ZA','2009-08-11 05:41:27','2009-08-11 05:41:27'),('4a84671d-ced8-4417-a836-18a9a509ff00','French','fr_FR','2009-08-13 21:18:53','2009-08-13 21:18:53'),('4ad2fec7-17b8-4aec-aa88-06e0a509ff00','Malay','ms_MY','2009-10-12 12:02:47','2009-10-12 12:02:47'),('4ad31379-b198-4cf8-88af-54b5a509ff00','Indonesian','id_ID','2009-10-12 13:31:05','2009-10-12 13:31:05'),('4ae8063f-c08c-42f4-9adc-19eba509ff00','Nederlands','nl_NL','2009-10-28 10:52:15','2009-10-28 10:52:15'),('4b374060-7c78-4fcf-935e-2322a509ff00','Spanish','es_ES','2009-12-27 13:09:20','2009-12-27 13:09:20'),('4bcffb4b-b3c8-45eb-a578-6f52a509ff00','Thai','th_TH','2010-04-22 09:31:23','2010-04-22 09:31:23'),('4c99e65a-d014-4dd1-984d-1bb3a509ff00','Portugues','pt_BR','2010-09-22 13:19:54','2010-09-22 13:19:54'),('4e3eca1a-8af0-4791-89c8-3ddaa509ff00','German','de_DE','2011-08-07 19:23:38','2011-08-07 19:23:38'),('4e47d171-6a24-4ab7-b491-6582a509ff00','Danish','da_DK','2011-08-14 15:45:21','2011-08-14 15:45:21');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maps`
--

DROP TABLE IF EXISTS `maps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maps` (
  `id` char(36) NOT NULL,
  `name` varchar(40) NOT NULL,
  `value` varchar(40) NOT NULL,
  `user_id` char(36) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maps`
--

LOCK TABLES `maps` WRITE;
/*!40000 ALTER TABLE `maps` DISABLE KEYS */;
INSERT INTO `maps` VALUES ('4b7aa71c-7118-498c-bae4-21e4a509ff00','zoom','15','49d09fb4-f23c-4b30-9a50-2b0ba509ff00','2010-02-16 16:09:32','2010-02-16 16:22:44'),('4b7aa71c-a794-43e1-8498-21e4a509ff00','lon','21.884164810180664','49d09fb4-f23c-4b30-9a50-2b0ba509ff00','2010-02-16 16:09:32','2010-02-16 16:22:44'),('4b7aa71c-a880-498c-b86e-21e4a509ff00','type','G_HYBRID_MAP','49d09fb4-f23c-4b30-9a50-2b0ba509ff00','2010-02-16 16:09:32','2010-02-16 16:22:44'),('4b7aa71c-f9c4-4394-9c85-21e4a509ff00','lat','-29.220650436963687','49d09fb4-f23c-4b30-9a50-2b0ba509ff00','2010-02-16 16:09:32','2010-02-16 16:22:44'),('4b7aaaa7-552c-4e25-8aa0-19d1a509ff00','lon','21.881139278411865','49d09f65-9b48-4c1e-baed-194ea509ff00','2010-02-16 16:24:39','2010-02-16 16:26:04'),('4b7aaaa7-6378-4314-89d5-19d1a509ff00','lat','-29.232148231872852','49d09f65-9b48-4c1e-baed-194ea509ff00','2010-02-16 16:24:39','2010-02-16 16:26:04'),('4b7aaaa7-6750-4484-8515-19d1a509ff00','zoom','16','49d09f65-9b48-4c1e-baed-194ea509ff00','2010-02-16 16:24:39','2010-02-16 16:26:04'),('4b7aaaa7-99e4-47a4-a529-19d1a509ff00','type','G_HYBRID_MAP','49d09f65-9b48-4c1e-baed-194ea509ff00','2010-02-16 16:24:39','2010-02-16 16:26:04');
/*!40000 ALTER TABLE `maps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `na_realms`
--

DROP TABLE IF EXISTS `na_realms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `na_realms` (
  `id` char(36) NOT NULL,
  `na_id` char(36) NOT NULL,
  `realm_id` char(36) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `na_realms`
--

LOCK TABLES `na_realms` WRITE;
/*!40000 ALTER TABLE `na_realms` DISABLE KEYS */;
/*!40000 ALTER TABLE `na_realms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `na_states`
--

DROP TABLE IF EXISTS `na_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `na_states` (
  `id` char(36) NOT NULL,
  `na_id` char(36) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `na_states`
--

LOCK TABLES `na_states` WRITE;
/*!40000 ALTER TABLE `na_states` DISABLE KEYS */;
INSERT INTO `na_states` VALUES ('4b7aa3b6-85f8-4a70-bbdf-23aea509ff00','1',1,'2010-02-16 15:55:02','2010-02-16 15:55:02'),('4b7aa473-96d4-4782-bb49-29a9a509ff00','2',0,'2010-02-16 15:58:11','2010-02-16 15:58:11');
/*!40000 ALTER TABLE `na_states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nas`
--

DROP TABLE IF EXISTS `nas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nas` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nasname` varchar(128) NOT NULL,
  `shortname` varchar(32) DEFAULT NULL,
  `type` varchar(30) DEFAULT 'other',
  `ports` int(5) DEFAULT NULL,
  `secret` varchar(60) NOT NULL DEFAULT 'secret',
  `community` varchar(50) DEFAULT NULL,
  `description` varchar(200) DEFAULT 'RADIUS Client',
  `monitor` tinyint(1) NOT NULL DEFAULT '1',
  `user_id` char(36) NOT NULL,
  `lat` double DEFAULT NULL,
  `lon` double DEFAULT NULL,
  `photo_file_name` varchar(128) NOT NULL DEFAULT 'logo.jpg',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `realm_id` char(36) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `nasname` (`nasname`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nas`
--

LOCK TABLES `nas` WRITE;
/*!40000 ALTER TABLE `nas` DISABLE KEYS */;
INSERT INTO `nas` VALUES (1,'127.0.0.1','localhost','CoovaChilli',3799,'testing123','read','One horse town',1,'49d09fb4-f23c-4b30-9a50-2b0ba509ff00',-29.2306876715279,21.8704748153687,'1.jpeg','2009-02-20 20:20:10','2012-04-06 12:02:28','49d09ec6-5480-45d4-a5ae-2b0ea509ff00'),(2,'192.168.1.11','Dummy AP','CoovaChilli-AP',NULL,'dummy','','One donkey barn',1,'49d09f65-9b48-4c1e-baed-194ea509ff00',-29.2303880668306,21.8869972229004,'2.jpeg','2010-02-16 15:57:53','2012-04-06 15:31:54','49d09ec6-5480-45d4-a5ae-2b0ea509ff00');
/*!40000 ALTER TABLE `nas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notes` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL DEFAULT '',
  `section_id` char(36) NOT NULL DEFAULT '',
  `value` varchar(500) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notes`
--

LOCK TABLES `notes` WRITE;
/*!40000 ALTER TABLE `notes` DISABLE KEYS */;
INSERT INTO `notes` VALUES ('4e7c53bb-825c-441c-9662-0b8f0a010001','4a0b0cb6-9718-4221-886b-3706a509ff00','4b974708-6cb4-48ef-af9e-3e83a509ff00','User Detail update','2011-09-23 11:39:07','2011-09-23 11:39:07');
/*!40000 ALTER TABLE `notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_details`
--

DROP TABLE IF EXISTS `notification_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_details` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` char(36) NOT NULL,
  `type` enum('disabled','email','sms') DEFAULT 'disabled',
  `address1` varchar(128) NOT NULL,
  `address2` varchar(128) NOT NULL,
  `start` int(5) DEFAULT '80',
  `increment` int(5) DEFAULT '10',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_details`
--

LOCK TABLES `notification_details` WRITE;
/*!40000 ALTER TABLE `notification_details` DISABLE KEYS */;
INSERT INTO `notification_details` VALUES (19,'4a0b0cb6-9718-4221-886b-3706a509ff00','email','dirkvanderwalt@gmail.com','',40,10,'2009-05-16 15:30:09','2009-06-02 21:52:27');
/*!40000 ALTER TABLE `notification_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` char(36) NOT NULL,
  `value` int(5) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` char(36) NOT NULL,
  `amount` decimal(9,2) NOT NULL DEFAULT '0.00',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (26,'4a0b0cb6-9718-4221-886b-3706a509ff00','250.30','2009-07-25 02:01:14','2009-07-25 02:01:14'),(28,'4a0b0cb6-9718-4221-886b-3706a509ff00','28700.00','2009-07-29 00:29:07','2009-07-29 00:29:07'),(29,'4a0b0cb6-9718-4221-886b-3706a509ff00','100.00','2009-08-14 14:12:05','2009-08-14 14:12:05');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `photos`
--

DROP TABLE IF EXISTS `photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `photos` (
  `id` char(36) NOT NULL,
  `realm_id` char(36) NOT NULL,
  `title` varchar(128) NOT NULL DEFAULT '',
  `description` varchar(250) NOT NULL DEFAULT '',
  `file_name` varchar(128) NOT NULL DEFAULT 'logo.jpg',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `title` (`title`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `photos`
--

LOCK TABLES `photos` WRITE;
/*!40000 ALTER TABLE `photos` DISABLE KEYS */;
INSERT INTO `photos` VALUES ('4f7ed1a7-7f18-4891-9085-0d860a010001','49d09ec6-5480-45d4-a5ae-2b0ea509ff00','Friendly Staff','Feel at home with friendly staff','4f7ed1a7-7f18-4891-9085-0d860a010001.jpeg','2012-04-06 13:21:11','2012-04-06 13:21:11'),('4f7ed171-4cb0-471d-80a8-0d860a010001','49d09ec6-5480-45d4-a5ae-2b0ea509ff00','Clean Rooms','Our rooms are sparkling clean','4f7ed171-4cb0-471d-80a8-0d860a010001.jpeg','2012-04-06 13:20:17','2012-04-06 13:20:17'),('4f7ed1cc-110c-47e9-8a08-0d860a010001','49d09ec6-5480-45d4-a5ae-2b0ea509ff00','Gym training','Keep fit with our state of the art gym','4f7ed1cc-110c-47e9-8a08-0d860a010001.jpeg','2012-04-06 13:21:48','2012-04-06 13:21:48'),('4f7ed1ff-8874-425d-8a95-0d860a010001','49d09ec6-5480-45d4-a5ae-2b0ea509ff00','Feel at home','Rooms that have a homely feel','4f7ed1ff-8874-425d-8a95-0d860a010001.jpeg','2012-04-06 13:22:39','2012-04-06 13:22:39'),('4f7ed23b-8bd4-4acf-9a3a-0d860a010001','49d09ec6-5480-45d4-a5ae-2b0ea509ff00','Keep it cool','Enjoy a sundowner at the swimming pool','4f7ed23b-8bd4-4acf-9a3a-0d860a010001.jpeg','2012-04-06 13:23:39','2012-04-06 13:23:39');
/*!40000 ALTER TABLE `photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile_realms`
--

DROP TABLE IF EXISTS `profile_realms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profile_realms` (
  `id` char(36) NOT NULL,
  `profile_id` char(36) NOT NULL,
  `realm_id` char(36) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile_realms`
--

LOCK TABLES `profile_realms` WRITE;
/*!40000 ALTER TABLE `profile_realms` DISABLE KEYS */;
/*!40000 ALTER TABLE `profile_realms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profiles`
--

DROP TABLE IF EXISTS `profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profiles` (
  `id` char(36) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `template_id` varchar(128) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profiles`
--

LOCK TABLES `profiles` WRITE;
/*!40000 ALTER TABLE `profiles` DISABLE KEYS */;
INSERT INTO `profiles` VALUES ('4a62fd7a-6068-42fd-aaae-1c72a509ff00','Voucher 10M CAP','4a629d58-7114-4f73-a4db-4298a509ff00','2009-07-19 13:03:22','2009-07-19 13:03:22'),('4a62f9c4-25a8-45bc-ab56-1c84a509ff00','Permanent 250M CAP','4a62984f-d8e4-428b-bb45-39a8a509ff00','2009-07-19 12:47:32','2009-07-19 12:47:32'),('4a6320ad-2f3c-4eb3-9bee-489da509ff00','Permanent 1h/day 7d/month','4a629864-f8fc-475f-b694-39a8a509ff00','2009-07-19 15:33:33','2009-07-19 15:33:33'),('4a62f5ca-1f54-4034-aa59-1c69a509ff00','Permanent 10Days CAP','4a62982e-f070-4332-af98-19c9a509ff00','2009-07-19 12:30:34','2009-07-19 12:30:34');
/*!40000 ALTER TABLE `profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `radacct`
--

DROP TABLE IF EXISTS `radacct`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `radacct` (
  `radacctid` bigint(21) NOT NULL AUTO_INCREMENT,
  `acctsessionid` varchar(64) NOT NULL DEFAULT '',
  `acctuniqueid` varchar(32) NOT NULL DEFAULT '',
  `username` varchar(64) NOT NULL DEFAULT '',
  `groupname` varchar(64) NOT NULL DEFAULT '',
  `realm` varchar(64) DEFAULT '',
  `nasipaddress` varchar(15) NOT NULL DEFAULT '',
  `nasportid` varchar(15) DEFAULT NULL,
  `nasporttype` varchar(32) DEFAULT NULL,
  `acctstarttime` datetime DEFAULT NULL,
  `acctstoptime` datetime DEFAULT NULL,
  `acctsessiontime` int(12) DEFAULT NULL,
  `acctauthentic` varchar(32) DEFAULT NULL,
  `connectinfo_start` varchar(50) DEFAULT NULL,
  `connectinfo_stop` varchar(50) DEFAULT NULL,
  `acctinputoctets` bigint(20) DEFAULT NULL,
  `acctoutputoctets` bigint(20) DEFAULT NULL,
  `calledstationid` varchar(50) NOT NULL DEFAULT '',
  `callingstationid` varchar(50) NOT NULL DEFAULT '',
  `acctterminatecause` varchar(32) NOT NULL DEFAULT '',
  `servicetype` varchar(32) DEFAULT NULL,
  `framedprotocol` varchar(32) DEFAULT NULL,
  `framedipaddress` varchar(15) NOT NULL DEFAULT '',
  `acctstartdelay` int(12) DEFAULT NULL,
  `acctstopdelay` int(12) DEFAULT NULL,
  `xascendsessionsvrkey` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`radacctid`),
  KEY `username` (`username`),
  KEY `framedipaddress` (`framedipaddress`),
  KEY `acctsessionid` (`acctsessionid`),
  KEY `acctsessiontime` (`acctsessiontime`),
  KEY `acctuniqueid` (`acctuniqueid`),
  KEY `acctstarttime` (`acctstarttime`),
  KEY `acctstoptime` (`acctstoptime`),
  KEY `nasipaddress` (`nasipaddress`)
) ENGINE=MyISAM AUTO_INCREMENT=108 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `radacct`
--

LOCK TABLES `radacct` WRITE;
/*!40000 ALTER TABLE `radacct` DISABLE KEYS */;
INSERT INTO `radacct` VALUES (70,'4a0fcccc00000001','275f7e0532ee4672','dvdwalt@ri','','ri','10.20.30.2','1','Wireless-802.11','2009-05-17 11:03:54','2009-05-17 18:36:53',27184,'','','',6695245,2837936,'00-1D-7E-BC-02-AD','00-0C-F1-5F-58-0B','User-Request','','','10.1.0.2',0,0,''),(71,'4a11a0f000000001','8ea2d39c470478a3','dvdwalt@ri','','ri','10.20.30.2','1','Wireless-802.11','2009-05-18 20:20:17','2009-05-18 21:59:02',5926,'','','',3413468,1876695,'00-1D-7E-BC-02-AD','00-0C-F1-5F-58-0B','User-Request','','','10.1.0.2',0,0,''),(72,'4a12edc200000001','92e7ea04d1a7b324','dvdwalt@ri','','ri','10.20.30.2','1','Wireless-802.11','2009-05-19 20:59:23','2009-05-19 21:12:19',777,'','','',1957518,412343,'00-1D-7E-BC-02-AD','00-0C-F1-5F-58-0B','Lost-Carrier','','','10.1.0.3',0,0,''),(73,'4a1adb5300000001','5d2636e5d54c438b','dvdwalt@ri','','ri','10.20.30.2','1','Wireless-802.11','2009-05-25 20:15:36','2009-05-25 10:27:26',7912,'','','',5744974,1251980,'00-1D-7E-BC-02-AD','00-0C-F1-5F-58-0B','Admin-Reset','','','10.1.0.2',0,0,''),(77,'4a295db900000002','caba08557b1923c6','dvdwalt@ri','','ri','10.20.30.2','2','Wireless-802.11','2009-06-05 20:23:12','2009-06-05 20:29:14',63,'','','',270230,87448,'00-1D-7E-BC-02-AD','00-0C-F1-5F-58-0B','User-Request','','','10.1.0.3',0,0,''),(78,'4a295fdc00000002','a4aff42674ff54ca','dvdwalt@ri','','ri','10.20.30.2','2','Wireless-802.11','2009-06-05 21:03:25','2009-06-05 22:05:34',3729,'','','',7670081,1806752,'00-1D-7E-BC-02-AD','00-0C-F1-5F-58-0B','User-Request','','','10.1.0.3',0,0,''),(80,'4a29767100000002','ec0e6d2ce4929e7c','dvdwalt@ri','','ri','10.20.30.2','2','Wireless-802.11','2009-06-05 22:13:44','2009-06-05 22:18:41',297,'','','',816874,162244,'00-1D-7E-BC-02-AD','00-0C-F1-5F-58-0B','User-Request','','','10.1.0.3',0,0,''),(81,'4a29798400000002','cb98153586eb92d5','dvdwalt@ri','','ri','10.20.30.2','2','Wireless-802.11','2009-06-05 22:33:28','2009-06-05 22:36:36',188,'','','',103638,44754,'00-1D-7E-BC-02-AD','00-0C-F1-5F-58-0B','User-Request','','','10.1.0.3',0,0,''),(82,'4a297db700000002','99e8a6349650decf','dvdwalt@ri','','ri','10.20.30.2','2','Wireless-802.11','2009-06-06 05:26:48','2009-06-06 07:01:11',5664,'','','',23629278,3089044,'00-1D-7E-BC-02-AD','00-0C-F1-5F-58-0B','Admin-Reset','','','10.1.0.3',0,0,''),(83,'4a2a969000000002','9ab4364a15c330aa','dvdwalt@ri','','ri','10.20.30.2','2','Wireless-802.11','2009-06-06 19:48:20','2009-06-07 05:56:21',36489,'','','',11312477,3336752,'00-1D-7E-BC-02-AD','00-0C-F1-5F-58-0B','Admin-Reset','','','10.1.0.5',0,0,''),(84,'4a2b46ee00000002','da2778a8b3f8a8b7','dvdwalt@ri','','ri','10.20.30.2','2','Wireless-802.11','2009-06-07 19:44:48','2009-06-07 19:55:16',629,'','','',2475814,788619,'00-1D-7E-BC-02-AD','00-0C-F1-5F-58-0B','Admin-Reset','','','10.1.0.6',0,0,''),(85,'4a2bfb0800000002','c581e2435c730892','dvdwalt@ri','','ri','10.20.30.2','2','Wireless-802.11','2009-06-07 19:57:34','2009-06-07 20:02:49',315,'','','',1081469,188949,'00-1D-7E-BC-02-AD','00-0C-F1-5F-58-0B','Admin-Reset','','','10.1.0.6',0,0,''),(86,'4a2bfccd00000002','d0a37f7d1b371eeb','dvdwalt@ri','','ri','10.20.30.2','2','Wireless-802.11','2009-06-07 20:04:04','2009-06-07 20:20:49',1006,'','','',2726805,792331,'00-1D-7E-BC-02-AD','00-0C-F1-5F-58-0B','Admin-Reset','','','10.1.0.6',0,0,''),(87,'4a2c010500000002','15990f36d3d1eece','dvdwalt@ri','','ri','10.20.30.2','2','Wireless-802.11','2009-06-07 20:49:59','2009-06-08 04:00:22',25828,'','','',4616120,3316749,'00-1D-7E-BC-02-AD','00-0C-F1-5F-58-0B','Admin-Reset','','','10.1.0.6',0,0,''),(88,'4a2c6cc000000002','ddd8c3a3f2d20ecf','dvdwalt@ri','','ri','10.20.30.2','2','Wireless-802.11','2009-06-08 06:28:27','2009-06-08 06:48:26',1200,'','','',487242,413961,'00-1D-7E-BC-02-AD','00-0C-F1-5F-58-0B','Lost-Carrier','','','10.1.0.6',0,0,''),(89,'4a2d4cfd00000002','c646efe04d5b6e2b','dvdwalt@ri','','ri','10.20.30.2','2','Wireless-802.11','2009-06-08 19:58:38','2009-06-10 02:42:59',3591,'','','',1337335,693568,'00-1D-7E-BC-02-AD','00-0C-F1-5F-58-0B','','','','10.1.0.7',0,0,''),(93,'1254161271_test','9da8dd883b7a03ac','dvdwalt@ri','','ri','127.0.0.1','','','2009-09-28 20:07:51','2009-09-28 20:07:52',10,'','','',10,10,'','','User-Request','','','',0,0,''),(94,'4e7c467600000001','5777da7d65ba94cf','dvdwalt@ri','','ri','127.0.0.1','1','Wireless-802.11','2011-09-23 10:42:56','2011-09-23 10:45:07',131,'','','',1792146,172740,'08-00-27-56-22-0B','08-00-27-83-EB-FB','NAS-Reboot','','','10.1.0.2',0,0,''),(95,'4e7c47dc00000001','c975a713ce8b5e78','dvdwalt@ri','','ri','127.0.0.1','1','Wireless-802.11','2011-09-23 10:54:25','2011-09-23 10:55:20',56,'','','',321044,30769,'08-00-27-56-22-0B','08-00-27-83-EB-FB','User-Request','','','10.1.0.2',0,0,''),(96,'4e7c4a3400000001','da8cb26526a2cb4c','dvdwalt@ri','','ri','127.0.0.1','1','Wireless-802.11','2011-09-23 10:59:19','2011-09-23 10:59:41',21,'','','',323821,19069,'08-00-27-56-22-0B','08-00-27-83-EB-FB','Admin-Reset','','','10.1.0.3',0,0,''),(97,'4e7c4a7d00000001','fe73273989436418','dvdwalt@ri','','ri','127.0.0.1','1','Wireless-802.11','2011-09-23 11:08:52','2011-09-23 11:09:17',25,'','','',1121189,55991,'08-00-27-56-22-0B','08-00-27-83-EB-FB','User-Request','','','10.1.0.3',0,0,''),(98,'4e7c4cbd00000001','1fa5a9c82e2f2f63','dvdwalt@ri','','ri','127.0.0.1','1','Wireless-802.11','2011-09-23 11:13:16','2011-09-23 11:17:52',277,'','','',356078,34307,'08-00-27-56-22-0B','08-00-27-83-EB-FB','User-Request','','','10.1.0.3',0,0,''),(99,'4e7c4ec000000001','f36720fbd2bd3b64','dvdwalt@ri','','ri','127.0.0.1','1','Wireless-802.11','2011-09-23 11:27:07','2011-09-23 11:54:43',1656,'','','',4763885,1089804,'08-00-27-56-22-0B','08-00-27-83-EB-FB','Admin-Reset','','','10.1.0.3',0,0,''),(100,'4e7c576300000001','775988eb0200c7ac','dvdwalt@ri','','ri','127.0.0.1','1','Wireless-802.11','2011-09-23 11:57:14','2011-09-23 12:48:09',3055,'','','',148122,225322,'08-00-27-56-22-0B','08-00-27-83-EB-FB','','','','10.1.0.3',0,0,''),(101,'4f7e9c1700000001','e3154a232c382c4b','dvdwalt@ri','','ri','127.0.0.1','1','Wireless-802.11','2012-04-06 09:33:19','2012-04-06 11:43:45',7827,'','','',9566941,3161962,'08-00-27-56-22-0B','08-00-27-3E-84-A7','Idle-Timeout','','','10.1.0.2',0,0,''),(102,'4f7ec48200000001','e26d5e7dbaef51d4','dvdwalt@ri','','ri','127.0.0.1','1','Wireless-802.11','2012-04-06 12:30:12','2012-04-06 12:32:27',135,'','','',1026982,386315,'08-00-27-56-22-0B','08-00-27-3E-84-A7','User-Request','','','10.1.0.2',0,0,''),(103,'4f7ec63b00000001','bc52239b7f34d530','dvdwalt@ri','','ri','127.0.0.1','1','Wireless-802.11','2012-04-06 12:53:10','2012-04-06 15:36:33',9803,'','','',12367171,5449146,'08-00-27-56-22-0B','08-00-27-3E-84-A7','User-Request','','','10.1.0.2',0,0,''),(104,'4f7ef16100000001','b674b6eec8784763','dvdwalt@ri','','ri','127.0.0.1','1','Wireless-802.11','2012-04-06 15:38:58','2012-04-06 15:40:23',85,'','','',17724,18744,'08-00-27-56-22-0B','08-00-27-3E-84-A7','User-Request','','','10.1.0.2',0,0,''),(105,'4f7ef24700000001','066aa12bdc8b6a7e','dvdwalt@ri','','ri','127.0.0.1','1','Wireless-802.11','2012-04-06 15:41:13','2012-04-06 15:42:10',57,'','','',29400,26508,'08-00-27-56-22-0B','08-00-27-3E-84-A7','User-Request','','','10.1.0.2',0,0,''),(106,'4f7ef2b200000001','47e39202ee642086','dvdwalt@ri','','ri','127.0.0.1','1','Wireless-802.11','2012-04-06 15:49:30','2012-04-06 16:10:30',1261,'','','',1423725,685443,'08-00-27-56-22-0B','08-00-27-3E-84-A7','User-Request','','','10.1.0.2',0,0,''),(107,'4f7ef95600000001','da15bf9610d1dbd7','dvdwalt@ri','','ri','127.0.0.1','1','Wireless-802.11','2012-04-06 16:15:23','2012-04-06 16:15:26',3,'','','',5563,3318,'08-00-27-56-22-0B','08-00-27-3E-84-A7','User-Request','','','10.1.0.2',0,0,'');
/*!40000 ALTER TABLE `radacct` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `radcheck`
--

DROP TABLE IF EXISTS `radcheck`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `radcheck` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '',
  `attribute` varchar(32) NOT NULL DEFAULT '',
  `op` char(2) NOT NULL DEFAULT '==',
  `value` varchar(253) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `username` (`username`(32))
) ENGINE=MyISAM AUTO_INCREMENT=15051 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `radcheck`
--

LOCK TABLES `radcheck` WRITE;
/*!40000 ALTER TABLE `radcheck` DISABLE KEYS */;
INSERT INTO `radcheck` VALUES (14778,'dvdwalt@ri','Cleartext-Password','==','dvdwalt@ri');
/*!40000 ALTER TABLE `radcheck` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `radgroupcheck`
--

DROP TABLE IF EXISTS `radgroupcheck`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `radgroupcheck` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groupname` varchar(64) NOT NULL DEFAULT '',
  `attribute` varchar(32) NOT NULL DEFAULT '',
  `op` char(2) NOT NULL DEFAULT '==',
  `value` varchar(253) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `groupname` (`groupname`(32))
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `radgroupcheck`
--

LOCK TABLES `radgroupcheck` WRITE;
/*!40000 ALTER TABLE `radgroupcheck` DISABLE KEYS */;
INSERT INTO `radgroupcheck` VALUES (49,'Voucher 10M CAP','ChilliSpot-Max-All-Octets','==','10485760'),(50,'Permanent 1h/day 7d/month','Yfi-Data','==','41943040'),(51,'Permanent 1h/day 7d/month','Yfi-Time','==','25200'),(52,'Permanent 1h/day 7d/month','Max-Daily-Session','==','3600'),(53,'Permanent 1h/day 7d/month','ChilliSpot-Max-Daily-Octets','==','10485760'),(45,'Permanent 250M CAP','Yfi-Data','==','262144000'),(44,'Permanent 10Days CAP','Yfi-Time','==','864000');
/*!40000 ALTER TABLE `radgroupcheck` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `radgroupreply`
--

DROP TABLE IF EXISTS `radgroupreply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `radgroupreply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groupname` varchar(64) NOT NULL DEFAULT '',
  `attribute` varchar(32) NOT NULL DEFAULT '',
  `op` char(2) NOT NULL DEFAULT '=',
  `value` varchar(253) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `groupname` (`groupname`(32))
) ENGINE=MyISAM AUTO_INCREMENT=67 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `radgroupreply`
--

LOCK TABLES `radgroupreply` WRITE;
/*!40000 ALTER TABLE `radgroupreply` DISABLE KEYS */;
INSERT INTO `radgroupreply` VALUES (66,'Permanent 1h/day 7d/month','ChilliSpot-Bandwidth-Max-Down','=','262144'),(64,'Permanent 1h/day 7d/month','Idle-Timeout','=','900'),(62,'Voucher 10M CAP','ChilliSpot-Bandwidth-Max-Down','=','262144'),(65,'Permanent 1h/day 7d/month','ChilliSpot-Bandwidth-Max-Up','=','262144'),(61,'Voucher 10M CAP','ChilliSpot-Bandwidth-Max-Up','=','262144'),(60,'Voucher 10M CAP','Idle-Timeout','=','900'),(63,'Permanent 1h/day 7d/month','Acct-Interim-Interval','=','120'),(59,'Voucher 10M CAP','Acct-Interim-Interval','=','120'),(52,'Permanent 250M CAP','Idle-Timeout','=','900'),(53,'Permanent 250M CAP','ChilliSpot-Bandwidth-Max-Up','=','262144'),(54,'Permanent 250M CAP','ChilliSpot-Bandwidth-Max-Down','=','262144'),(51,'Permanent 250M CAP','Acct-Interim-Interval','=','60'),(50,'Permanent 10Days CAP','ChilliSpot-Bandwidth-Max-Down','=','262144'),(49,'Permanent 10Days CAP','ChilliSpot-Bandwidth-Max-Up','=','262144'),(48,'Permanent 10Days CAP','Idle-Timeout','=','900'),(47,'Permanent 10Days CAP','Acct-Interim-Interval','=','120');
/*!40000 ALTER TABLE `radgroupreply` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `radpostauth`
--

DROP TABLE IF EXISTS `radpostauth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `radpostauth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '',
  `pass` varchar(64) NOT NULL DEFAULT '',
  `reply` varchar(32) NOT NULL DEFAULT '',
  `authdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `radpostauth`
--

LOCK TABLES `radpostauth` WRITE;
/*!40000 ALTER TABLE `radpostauth` DISABLE KEYS */;
/*!40000 ALTER TABLE `radpostauth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `radreply`
--

DROP TABLE IF EXISTS `radreply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `radreply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '',
  `attribute` varchar(32) NOT NULL DEFAULT '',
  `op` char(2) NOT NULL DEFAULT '=',
  `value` varchar(253) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `username` (`username`(32))
) ENGINE=MyISAM AUTO_INCREMENT=1135 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `radreply`
--

LOCK TABLES `radreply` WRITE;
/*!40000 ALTER TABLE `radreply` DISABLE KEYS */;
/*!40000 ALTER TABLE `radreply` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `radusergroup`
--

DROP TABLE IF EXISTS `radusergroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `radusergroup` (
  `username` varchar(64) NOT NULL DEFAULT '',
  `groupname` varchar(64) NOT NULL DEFAULT '',
  `priority` int(11) NOT NULL DEFAULT '1',
  KEY `username` (`username`(32))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `radusergroup`
--

LOCK TABLES `radusergroup` WRITE;
/*!40000 ALTER TABLE `radusergroup` DISABLE KEYS */;
INSERT INTO `radusergroup` VALUES ('dvdwalt@ri','Permanent 250M CAP',1);
/*!40000 ALTER TABLE `radusergroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `realms`
--

DROP TABLE IF EXISTS `realms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `realms` (
  `id` char(36) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `append_string_to_user` varchar(128) NOT NULL DEFAULT '',
  `icon_file_name` varchar(128) NOT NULL DEFAULT 'logo.jpg',
  `phone` varchar(14) NOT NULL DEFAULT '',
  `fax` varchar(14) NOT NULL DEFAULT '',
  `cell` varchar(14) NOT NULL DEFAULT '',
  `email` varchar(128) NOT NULL DEFAULT '',
  `url` varchar(128) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `street_no` char(10) NOT NULL DEFAULT '',
  `street` char(50) NOT NULL DEFAULT '',
  `town_suburb` char(50) NOT NULL DEFAULT '',
  `city` char(50) NOT NULL DEFAULT '',
  `lat` double DEFAULT NULL,
  `lon` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `realms`
--

LOCK TABLES `realms` WRITE;
/*!40000 ALTER TABLE `realms` DISABLE KEYS */;
INSERT INTO `realms` VALUES ('49d09ec6-5480-45d4-a5ae-2b0ea509ff00','Residence Inn','ri','49d09ec6-5480-45d4-a5ae-2b0ea509ff00.png','012-803-1234','012-803-1235','012-803-1236','wifi@residence-inn.co.za','http://www.residence-inn.co.za','2009-03-30 12:28:22','2012-04-06 16:01:15','40','President Street','Silverton','Pretoria',-25.734686,28.29587);
/*!40000 ALTER TABLE `realms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `right_categories`
--

DROP TABLE IF EXISTS `right_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `right_categories` (
  `id` char(36) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `right_categories`
--

LOCK TABLES `right_categories` WRITE;
/*!40000 ALTER TABLE `right_categories` DISABLE KEYS */;
INSERT INTO `right_categories` VALUES ('499ef4e3-ff68-4f25-ac59-190ea509ff00','Realms','2009-02-20 20:22:27','2009-02-20 20:22:27'),('49ab9486-879c-4e7b-bffc-19daa509ff00','Vouchers','2009-03-02 10:10:46','2009-03-02 10:10:46'),('49ae4e89-6518-4ccb-8b81-7146a509ff00','Permanent Users','2009-03-04 11:48:57','2009-05-23 16:20:18'),('49ba51de-e808-423f-a3f5-1609a509ff00','Profiles','2009-03-13 14:30:22','2009-03-13 14:30:22'),('49e1d707-154c-4181-8026-6543a509ff00','Activity','2009-04-12 13:56:55','2009-04-12 13:56:55'),('49e1dfb7-9d4c-4eae-b494-75eba509ff00','NAS Devices','2009-04-12 14:33:59','2009-04-12 14:33:59'),('4a6187d2-264c-412e-9d5f-4298a509ff00','Accounting','2009-07-18 10:29:06','2009-07-18 10:29:06'),('4b29d624-39d0-4a76-a5b4-4d55a509ff00','Internet Credits','2009-12-17 08:56:36','2009-12-17 08:56:36'),('4b91e5b7-2d80-4e9b-bde6-7047a509ff00','User Portal Tabs','2010-03-06 07:18:47','2010-03-06 07:18:47'),('4b969f70-2ebc-4cfc-b30f-3078a509ff00','User Portal -> User Detail','2010-03-09 21:20:16','2010-03-09 21:20:16'),('4b97e031-6e68-4a5b-84ff-51f7a509ff00','User Portal -> Notification','2010-03-10 20:08:49','2010-03-10 20:08:49'),('4b985731-2f68-41c3-bfe8-1943a509ff00','User Portal -> Usage','2010-03-11 04:36:33','2010-03-11 04:36:33'),('4e7d9d37-9228-4c64-ae0d-0e9fa509ff00','User Portal -> Devices','2011-09-24 11:04:55','2011-09-24 11:04:55');
/*!40000 ALTER TABLE `right_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `right_groups`
--

DROP TABLE IF EXISTS `right_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `right_groups` (
  `id` char(36) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `right_groups`
--

LOCK TABLES `right_groups` WRITE;
/*!40000 ALTER TABLE `right_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `right_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rights`
--

DROP TABLE IF EXISTS `rights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rights` (
  `id` char(36) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `description` varchar(128) NOT NULL DEFAULT '',
  `right_category_id` char(36) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rights`
--

LOCK TABLES `rights` WRITE;
/*!40000 ALTER TABLE `rights` DISABLE KEYS */;
INSERT INTO `rights` VALUES ('499ef500-c76c-4897-9bde-190da509ff00','realms/json_index','List Realms','499ef4e3-ff68-4f25-ac59-190ea509ff00','2009-02-20 20:22:56','2009-02-27 15:03:05'),('49ab942c-f9bc-46ad-b119-19dea509ff00','realms/json_add','Add Realm','499ef4e3-ff68-4f25-ac59-190ea509ff00','2009-03-02 10:09:16','2009-03-02 10:09:16'),('49ab946c-78cc-4b3f-ad02-2755a509ff00','vouchers/json_index','List Vouchers','49ab9486-879c-4e7b-bffc-19daa509ff00','2009-03-02 10:10:20','2009-03-02 10:11:02'),('49ae2bc4-ccd8-4660-81af-2ce7a509ff00','users/json_ap_index','List Users - Access Poroviders','499ef4e3-ff68-4f25-ac59-190ea509ff00','2009-03-04 09:20:36','2009-03-04 09:20:36'),('49ae4e7f-73b8-46c7-864c-7146a509ff00','users/json_ap_add','Add Access Poviders','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-03-04 11:48:47','2009-03-04 11:49:07'),('49ba51f9-26a0-4365-bf83-6ed1a509ff00','templates/json_index','List available templates (for grid)','49ba51de-e808-423f-a3f5-1609a509ff00','2009-03-13 14:30:49','2009-03-16 15:04:21'),('49ba5218-ddbc-47eb-8711-661ea509ff00','templates/json_index_list','List availabe templates (for select)','49ba51de-e808-423f-a3f5-1609a509ff00','2009-03-13 14:31:20','2009-03-16 14:54:08'),('49ba522f-0ff8-4ee3-b044-1854a509ff00','templates/json_add','Add templates','49ba51de-e808-423f-a3f5-1609a509ff00','2009-03-13 14:31:43','2009-03-16 14:54:16'),('49ba5245-2ba4-49d9-b806-1854a509ff00','templates/json_del','Delete templates','49ba51de-e808-423f-a3f5-1609a509ff00','2009-03-13 14:32:05','2009-03-16 14:54:28'),('49ba525b-c0ec-43ab-a149-6edea509ff00','templates/json_edit','View template and edit it','49ba51de-e808-423f-a3f5-1609a509ff00','2009-03-13 14:32:27','2009-03-17 11:09:11'),('49ba527b-1f3c-4490-ab05-6edea509ff00','templates/json_attr_add','Add attribute to template','49ba51de-e808-423f-a3f5-1609a509ff00','2009-03-13 14:32:59','2009-03-16 14:55:00'),('49ba5293-ab1c-420e-a5d3-6edea509ff00','templates/json_attr_edit','Edit attribute of template','49ba51de-e808-423f-a3f5-1609a509ff00','2009-03-13 14:33:23','2009-03-16 14:55:07'),('49ba52b6-01b8-4905-ac5d-66b6a509ff00','templates/json_attr_delete','Delete attribute from template','49ba51de-e808-423f-a3f5-1609a509ff00','2009-03-13 14:33:58','2009-03-16 14:55:14'),('49be4003-6570-4746-a8d3-3926a509ff00','realms/json_index_list','Form Controll Select [All AP\'s Need This]','499ef4e3-ff68-4f25-ac59-190ea509ff00','2009-03-16 14:03:15','2009-03-16 14:03:15'),('49bf5fe6-b858-42e6-b6f9-51e1a509ff00','profiles/json_index','List available profiles (for grid) ','49ba51de-e808-423f-a3f5-1609a509ff00','2009-03-17 10:31:34','2009-03-17 10:31:34'),('49bf60a9-1c58-49f3-83c3-3491a509ff00','profiles/json_add','Add profiles','49ba51de-e808-423f-a3f5-1609a509ff00','2009-03-17 10:34:49','2009-03-17 10:34:49'),('49bf6239-a734-4082-a33b-51e2a509ff00','profiles/json_del','Delete profiles','49ba51de-e808-423f-a3f5-1609a509ff00','2009-03-17 10:41:29','2009-03-17 10:41:29'),('49bf66f9-e324-404f-a9f0-3a21a509ff00','profiles/json_attribute_delete','Delete profile attributes','49ba51de-e808-423f-a3f5-1609a509ff00','2009-03-17 11:01:45','2009-03-17 11:01:45'),('49bf68a7-60d4-41e8-b2bb-2073a509ff00','profiles/json_edit','View profile and edit it','49ba51de-e808-423f-a3f5-1609a509ff00','2009-03-17 11:08:55','2009-03-17 11:17:30'),('49cfb442-d66c-4861-aeed-150ea509ff00','vouchers/json_change_profile','Change a voucher\'s profile','49ab9486-879c-4e7b-bffc-19daa509ff00','2009-03-29 19:47:46','2009-03-29 19:48:53'),('49cfb40a-1144-42de-947d-171fa509ff00','vouchers/json_add_batch','Add a voucher batch','49ab9486-879c-4e7b-bffc-19daa509ff00','2009-03-29 19:46:50','2009-03-29 20:45:21'),('49bf7d31-f050-42ae-b2fe-339ba509ff00','vouchers/json_add','Create Vouchers','49ab9486-879c-4e7b-bffc-19daa509ff00','2009-03-17 12:36:33','2009-03-17 12:36:33'),('49bf7d4d-5990-46c6-b22a-3491a509ff00','vouchers/json_del','Delete vouchers','49ab9486-879c-4e7b-bffc-19daa509ff00','2009-03-17 12:37:01','2009-03-17 12:37:01'),('49bfafd8-fcb4-4f9b-85c8-7acca509ff00','batches/json_index','List available batches (for grid) ','49ab9486-879c-4e7b-bffc-19daa509ff00','2009-03-17 16:12:40','2009-03-17 16:12:40'),('49c10118-d8e0-4483-a2fd-69fba509ff00','vouchers/only_view_own','only see vouchers created self','49ab9486-879c-4e7b-bffc-19daa509ff00','2009-03-18 16:11:36','2009-03-18 16:11:36'),('49cfb1ab-6bf0-4bc2-9f5a-21e2a509ff00','vouchers/pdf','Generate PDF files from selected vouchers','49ab9486-879c-4e7b-bffc-19daa509ff00','2009-03-29 19:36:43','2009-03-29 19:36:43'),('49cfb1d1-39ec-4b14-908e-21f0a509ff00','vouchers/csv','Export selected vouchers as CSV','49ab9486-879c-4e7b-bffc-19daa509ff00','2009-03-29 19:37:21','2009-03-29 19:37:21'),('49cfb4c0-8418-46c2-9d9b-21e2a509ff00','vouchers/json_add_private','Add extra private attributes to voucher','49ab9486-879c-4e7b-bffc-19daa509ff00','2009-03-29 19:49:52','2009-03-29 19:49:52'),('49cfb4e2-fa7c-4103-a509-21f0a509ff00','vouchers/json_edit_private','Edit private attributes of voucher','49ab9486-879c-4e7b-bffc-19daa509ff00','2009-03-29 19:50:26','2009-03-29 19:50:26'),('49cfb500-7d84-4d51-adac-21a0a509ff00','vouchers/json_del_private','Delete private attributes of voucher','49ab9486-879c-4e7b-bffc-19daa509ff00','2009-03-29 19:50:56','2009-03-29 19:50:56'),('49cfb5ce-01bc-4559-9690-5642a509ff00','batches/pdf','Generate PDF files from selected batches','49ab9486-879c-4e7b-bffc-19daa509ff00','2009-03-29 19:54:22','2009-03-29 19:54:22'),('49cfb5e5-ce94-4442-a3e3-6b04a509ff00','batches/csv','Export selected batches as CSV','49ab9486-879c-4e7b-bffc-19daa509ff00','2009-03-29 19:54:45','2009-03-29 19:54:45'),('49cfb616-0560-477f-b07d-563fa509ff00','batches/json_del','Delete voucher batches','49ab9486-879c-4e7b-bffc-19daa509ff00','2009-03-29 19:55:34','2009-03-29 19:55:34'),('49cfb636-b1c4-4f72-b360-171fa509ff00','batches/json_view','View voucher batches','49ab9486-879c-4e7b-bffc-19daa509ff00','2009-03-29 19:56:06','2009-03-29 19:56:06'),('49e1d876-9718-4ba0-816f-75eba509ff00','radaccts/json_show_active','Show active users','49e1d707-154c-4181-8026-6543a509ff00','2009-04-12 14:03:02','2009-04-12 14:03:02'),('49e1d8aa-152c-40c9-8571-35f4a509ff00','radaccts/json_kick_users_off','Terminate connected users','49e1d707-154c-4181-8026-6543a509ff00','2009-04-12 14:03:54','2009-04-12 14:03:54'),('49e29ed9-d918-4650-ae41-6541a509ff00','nas/json_add','Add NAS devices','49e1dfb7-9d4c-4eae-b494-75eba509ff00','2009-04-13 04:09:29','2009-04-13 04:09:57'),('49e29f0f-70c0-4c73-bd2b-6541a509ff00','nas/json_del','Remove NAS devices','49e1dfb7-9d4c-4eae-b494-75eba509ff00','2009-04-13 04:10:23','2009-04-13 04:10:23'),('49e29f4c-2e3c-4e2e-ae52-6541a509ff00','nas/json_add_vpn','Add VPN connected NAS device','49e1dfb7-9d4c-4eae-b494-75eba509ff00','2009-04-13 04:11:24','2009-04-13 04:11:24'),('49e29f69-ecc8-4bc1-92d8-6541a509ff00','nas/json_edit','Edit NAS Device','49e1dfb7-9d4c-4eae-b494-75eba509ff00','2009-04-13 04:11:53','2009-04-13 04:11:53'),('49e29f90-f0b8-411e-ad74-6541a509ff00','nas/json_edit_optional','Edit optional attributes of NAS device','49e1dfb7-9d4c-4eae-b494-75eba509ff00','2009-04-13 04:12:32','2009-04-13 04:12:32'),('4a18066d-d360-402c-8164-2f8aa509ff00','permanent_users/json_index','List permanent users','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-05-23 16:21:33','2009-05-23 16:21:33'),('4a18cf79-fa60-44a3-a555-1995a509ff00','permanent_users/json_add','Add permanent users','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-05-24 06:39:21','2009-05-24 06:40:02'),('4a18cff3-822c-4b78-bca9-2f88a509ff00','permanent_users/json_del','Remove pernanent users devices','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-05-24 06:41:23','2009-05-24 06:41:23'),('4a18d071-d67c-4079-8d28-1993a509ff00','permanent_users/json_edit','Edit permanent user\'s detail','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-05-24 06:43:29','2009-05-24 06:43:29'),('4a18d25f-9cfc-427f-b04f-1992a509ff00','permanent_users/csv','CSV export of user detail','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-05-24 06:51:43','2009-05-24 06:51:43'),('4a18d4e9-ada8-4ef5-b3ed-3239a509ff00','permanent_users/json_test_auth','Test authentication - permanent user','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-05-24 07:02:33','2009-05-24 07:02:33'),('4a18d595-e5c4-45cc-9e90-1994a509ff00','permanent_users/json_send_message','Send message to permanent users','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-05-24 07:05:25','2009-05-24 07:05:25'),('4a18d75c-85e8-4bce-80c6-1994a509ff00','permanent_users/json_disable','Activate or disable permanet user\'s accounts','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-05-24 07:13:00','2009-05-24 07:13:00'),('4a18ee98-f6d4-4d93-bcaa-26f2a509ff00','permanent_users/json_password','Change permanent user\'s password','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-05-24 08:52:08','2009-05-24 08:52:08'),('4a18f0ba-ff80-40d6-818a-12b1a509ff00','permanent_users/json_notify_save','Change notification detail','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-05-24 09:01:14','2009-05-24 09:01:14'),('4a18f26d-49dc-4553-bf68-26f2a509ff00','extras/json_time_add','Load user\'s account with extra time','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-05-24 09:08:29','2009-05-24 09:08:29'),('4a18f285-3980-4359-acb9-26f4a509ff00','extras/json_data_add','Load user\'s account with extra data','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-05-24 09:08:53','2009-05-24 09:08:53'),('4a18f395-97a8-4d3a-b761-12a4a509ff00','permanent_users/json_change_profile','Change profile','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-05-24 09:13:25','2009-05-24 09:13:25'),('4a18f3cf-60f8-4cec-8a09-26f1a509ff00','permanent_users/json_add_private','Add private attributes','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-05-24 09:14:23','2009-05-24 09:14:23'),('4a18f3f7-0584-40d3-b170-1241a509ff00','permanent_users/json_edit_private','Edit private attributes','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-05-24 09:15:03','2009-05-24 09:17:31'),('4a18f415-3eb0-4bb6-85c8-1291a509ff00','permanent_users/json_del_private','Delete private attributes','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-05-24 09:15:33','2009-05-24 09:17:17'),('4a1932a9-e81c-4058-bb37-129aa509ff00','permanent_users/only_view_own','Only see users created self','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-05-24 13:42:33','2009-05-24 13:42:33'),('4a193830-1b54-4b9b-8858-542ea509ff00','permanent_users/json_del_activity','Delete activity entries','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-05-24 14:06:08','2009-05-24 14:06:08'),('4a617c04-a940-4f3e-a020-19c9a509ff00','extras/json_cap_del','Remove Extra Data/Time for User\'s account','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-07-18 09:38:44','2009-07-18 09:58:30'),('4a618ae9-3acc-44d5-aecf-4324a509ff00','billing_plans/json_add','Add Billing Plans','4a6187d2-264c-412e-9d5f-4298a509ff00','2009-07-18 10:42:17','2009-07-18 10:42:17'),('4a618b0e-a9bc-45c5-bb2e-431aa509ff00','billing_plans/json_edit','Edit Billing Plan','4a6187d2-264c-412e-9d5f-4298a509ff00','2009-07-18 10:42:54','2009-07-18 10:42:54'),('4a618b3e-76b8-4fff-b772-19c7a509ff00','billing_plans/json_edit_promo','Edit Billing Plan - Promotion part','4a6187d2-264c-412e-9d5f-4298a509ff00','2009-07-18 10:43:42','2009-07-18 10:43:42'),('4a618b63-c3ac-4140-a4c6-19c9a509ff00','billing_plans/json_edit_extra','Edit Billing Plan - Extra Caps part','4a6187d2-264c-412e-9d5f-4298a509ff00','2009-07-18 10:44:19','2009-07-18 10:44:19'),('4a618b80-22c4-498a-ac5e-0a07a509ff00','billing_plans/json_del','Remove Billing Plan','4a6187d2-264c-412e-9d5f-4298a509ff00','2009-07-18 10:44:48','2009-07-18 10:44:48'),('4a619486-6aa8-4511-9b27-19caa509ff00','accnts/json_index','View account detail','4a6187d2-264c-412e-9d5f-4298a509ff00','2009-07-18 11:23:18','2009-07-18 11:23:18'),('4a6194ab-c9cc-45b4-976a-4298a509ff00','accnts/json_add','Create new invoices','4a6187d2-264c-412e-9d5f-4298a509ff00','2009-07-18 11:23:55','2009-07-18 11:23:55'),('4a6194db-920c-4e1b-9470-4324a509ff00','accnts/json_del','Delete invoices (main page)','4a6187d2-264c-412e-9d5f-4298a509ff00','2009-07-18 11:24:43','2009-07-18 11:24:43'),('4a619505-3378-4b35-b642-19c7a509ff00','accnts/json_payment_add','Add payments','4a6187d2-264c-412e-9d5f-4298a509ff00','2009-07-18 11:25:25','2009-07-18 11:25:25'),('4a61952c-5220-41a0-a2e7-19c9a509ff00','accnts/json_del_payment','Delete payments','4a6187d2-264c-412e-9d5f-4298a509ff00','2009-07-18 11:26:04','2009-07-18 11:26:04'),('4a61954f-e000-402b-9018-0a07a509ff00','accnts/json_del_invoice','Delete invoices (detail page)','4a6187d2-264c-412e-9d5f-4298a509ff00','2009-07-18 11:26:39','2009-07-18 11:26:39'),('4a62177d-84a8-40d4-94bf-09fda509ff00','extra_services/json_add','Add extra service','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-07-18 20:42:05','2009-07-18 20:42:05'),('4a6217a2-2608-484a-be64-0a07a509ff00','extra_services/json_del','Delete extra service','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-07-18 20:42:42','2009-07-18 20:42:42'),('4a6217b8-fed8-4ac4-ba3e-4298a509ff00','extra_services/json_edit','Edit extra service','49ae4e89-6518-4ccb-8b81-7146a509ff00','2009-07-18 20:43:04','2009-07-18 20:43:04'),('4a6f7860-42ec-4f61-b946-60b5a509ff00','nas/json_index','List NAS Devices','49e1dfb7-9d4c-4eae-b494-75eba509ff00','2009-07-29 00:14:56','2009-07-29 00:14:56'),('4b29d6aa-9afc-4568-a64a-1a02a509ff00','credits/json_index','List Internet Credits','4b29d624-39d0-4a76-a5b4-4d55a509ff00','2009-12-17 08:58:50','2009-12-17 09:05:09'),('4b29d6cc-41c4-4341-b71d-1a04a509ff00','credits/json_add','Create Internet Credits','4b29d624-39d0-4a76-a5b4-4d55a509ff00','2009-12-17 08:59:24','2009-12-17 09:05:22'),('4b29d71b-eb28-47ca-9bb9-4cb0a509ff00','credits/json_attach','Assign  Internet Credit to Prepaid users ','4b29d624-39d0-4a76-a5b4-4d55a509ff00','2009-12-17 09:00:43','2009-12-17 09:04:55'),('4b29d73e-a938-4d73-8641-593ca509ff00','credits/json_view','View Internet Credit','4b29d624-39d0-4a76-a5b4-4d55a509ff00','2009-12-17 09:01:18','2009-12-17 09:04:40'),('4b29d77b-5c00-4862-a683-1a00a509ff00','credits/json_edit','Modify Internet Credit values','4b29d624-39d0-4a76-a5b4-4d55a509ff00','2009-12-17 09:02:19','2009-12-17 09:02:19'),('4b29d797-9e5c-401b-bffa-1a01a509ff00','credits/json_del','Remove Internet Credits','4b29d624-39d0-4a76-a5b4-4d55a509ff00','2009-12-17 09:02:47','2009-12-17 09:02:47'),('4b29d7f4-3600-41b5-a20d-4d55a509ff00','credits/only_view_own','Only see Internet Credits created self','4b29d624-39d0-4a76-a5b4-4d55a509ff00','2009-12-17 09:04:20','2009-12-17 09:04:20'),('4b5de501-b0b4-4ee2-9b09-53e9a509ff00','realms/json_stats','Stats per Realm','499ef4e3-ff68-4f25-ac59-190ea509ff00','2010-01-25 20:37:53','2010-01-25 20:37:53'),('4b91e84b-64c4-48fb-b58b-13a4a509ff00','tab/show_profile_attributes','Show profile attributes tab','4b91e5b7-2d80-4e9b-bde6-7047a509ff00','2010-03-06 07:29:47','2010-03-06 07:29:47'),('4b91e887-eb50-4858-8dfb-1987a509ff00','tab/show_private_attributes','Show private attributes tab','4b91e5b7-2d80-4e9b-bde6-7047a509ff00','2010-03-06 07:30:47','2010-03-06 07:30:47'),('4b969fb2-29ec-4158-9c9d-1999a509ff00','update/cap_type','Change cap type','4b969f70-2ebc-4cfc-b30f-3078a509ff00','2010-03-09 21:21:22','2010-03-09 21:21:22'),('4b969fd6-c014-4b70-bf18-199ca509ff00','update/name','Change Name','4b969f70-2ebc-4cfc-b30f-3078a509ff00','2010-03-09 21:21:58','2010-03-09 21:21:58'),('4b969ff8-14c0-40ea-b198-308da509ff00','update/surname','Change Surname','4b969f70-2ebc-4cfc-b30f-3078a509ff00','2010-03-09 21:22:32','2010-03-09 21:22:32'),('4b96a01a-d620-423a-95a2-308ea509ff00','update/address','Change Address','4b969f70-2ebc-4cfc-b30f-3078a509ff00','2010-03-09 21:23:06','2010-03-09 21:23:06'),('4b96a036-2900-43e8-9c7b-3090a509ff00','update/phone','Change Phone','4b969f70-2ebc-4cfc-b30f-3078a509ff00','2010-03-09 21:23:34','2010-03-09 21:23:34'),('4b96a052-fe60-4c9a-81cb-308fa509ff00','update/email','Change e-mail','4b969f70-2ebc-4cfc-b30f-3078a509ff00','2010-03-09 21:24:02','2010-03-09 21:24:02'),('4b97e0ca-1594-4a40-8625-5202a509ff00','notify/type','Type of notification on usage','4b97e031-6e68-4a5b-84ff-51f7a509ff00','2010-03-10 20:11:22','2010-03-10 20:11:22'),('4b97e10c-492c-43f3-8a8b-198ba509ff00','notify/address1','Main notification address','4b97e031-6e68-4a5b-84ff-51f7a509ff00','2010-03-10 20:12:28','2010-03-10 20:12:28'),('4b97e12e-7eb8-432a-98fa-19aca509ff00','notify/address2','Secondary notification address','4b97e031-6e68-4a5b-84ff-51f7a509ff00','2010-03-10 20:13:02','2010-03-10 20:13:02'),('4b97e16a-0444-4c5c-9bd0-1943a509ff00','notify/start','Percentage to start notification','4b97e031-6e68-4a5b-84ff-51f7a509ff00','2010-03-10 20:14:02','2010-03-10 20:14:02'),('4b97e1a5-21ec-475e-a013-198fa509ff00','notify/increment','Every step after start percent to notify','4b97e031-6e68-4a5b-84ff-51f7a509ff00','2010-03-10 20:15:01','2010-03-10 20:15:01'),('4b985797-4e3c-48a8-9faf-5201a509ff00','usage/add_time','Add Extra time CAP','4b985731-2f68-41c3-bfe8-1943a509ff00','2010-03-11 04:38:15','2010-03-11 04:38:15'),('4b9857b9-66dc-4dbe-ae4a-5c7da509ff00','usage/add_data','Add Extra data CAP','4b985731-2f68-41c3-bfe8-1943a509ff00','2010-03-11 04:38:49','2010-03-11 04:38:49'),('4b994552-4640-4c95-a747-6c90a509ff00','update/profile','Change Profile for user','4b969f70-2ebc-4cfc-b30f-3078a509ff00','2010-03-11 21:32:34','2010-03-11 21:32:34'),('4c5863ae-3984-48e3-adde-0544a509ff00','permanent_users/json_prepaid_list','List Prepaid users','4b29d624-39d0-4a76-a5b4-4d55a509ff00','2010-08-03 20:45:02','2010-08-03 20:45:02'),('4e7d969a-efd8-43e5-bcc8-0eaaa509ff00','tab/show_devices','Right to show devices belonging to the user (MAC authentication)','4b91e5b7-2d80-4e9b-bde6-7047a509ff00','2011-09-24 10:36:42','2011-09-24 10:36:42'),('4e7d9d80-481c-4e70-853b-0fb3a509ff00','devices/json_index','List devices belonging to a user','4e7d9d37-9228-4c64-ae0d-0e9fa509ff00','2011-09-24 11:06:08','2011-09-24 11:09:01'),('4e7d9e4a-0c3c-4e87-9e9f-0eaba509ff00','devices/json_add','Add new device','4e7d9d37-9228-4c64-ae0d-0e9fa509ff00','2011-09-24 11:09:30','2011-09-24 11:09:30'),('4e7d9e5c-f788-4a29-b68b-0eaba509ff00','devices/json_del','Remove device','4e7d9d37-9228-4c64-ae0d-0e9fa509ff00','2011-09-24 11:09:48','2011-09-24 11:09:48');
/*!40000 ALTER TABLE `rights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rogue_aps`
--

DROP TABLE IF EXISTS `rogue_aps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rogue_aps` (
  `id` char(36) NOT NULL,
  `na_id` char(36) NOT NULL,
  `ssid` varchar(128) NOT NULL DEFAULT '',
  `mac` varchar(128) NOT NULL DEFAULT '',
  `mode` varchar(50) NOT NULL DEFAULT '',
  `channel` int(2) NOT NULL DEFAULT '0',
  `quality` varchar(20) NOT NULL DEFAULT '',
  `signal` varchar(5) NOT NULL DEFAULT '',
  `noise` varchar(5) NOT NULL DEFAULT '',
  `encryption` varchar(20) NOT NULL DEFAULT '',
  `state` enum('Known','Unknown') DEFAULT 'Unknown',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rogue_aps`
--

LOCK TABLES `rogue_aps` WRITE;
/*!40000 ALTER TABLE `rogue_aps` DISABLE KEYS */;
/*!40000 ALTER TABLE `rogue_aps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sections` (
  `id` char(36) NOT NULL,
  `name` varchar(80) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` VALUES ('4b920ee0-03c0-4abf-902a-13a4a509ff00','Technical','2010-03-06 10:14:24','2010-03-06 10:14:24'),('4b920ee9-fcf8-4f55-b0a3-13a4a509ff00','Accounting','2010-03-06 10:14:33','2010-03-06 10:14:33'),('4b920f0a-7264-4c95-a35f-1987a509ff00','General','2010-03-06 10:15:06','2010-03-06 10:15:06'),('4b974708-6cb4-48ef-af9e-3e83a509ff00','Self-service','2010-03-10 09:15:20','2010-03-10 09:15:20');
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `template_attributes`
--

DROP TABLE IF EXISTS `template_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `template_attributes` (
  `id` char(36) NOT NULL,
  `template_id` char(36) NOT NULL,
  `attribute` varchar(128) NOT NULL,
  `type` enum('Check','Reply') DEFAULT 'Check',
  `tooltip` varchar(200) NOT NULL,
  `unit` varchar(100) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `template_attributes`
--

LOCK TABLES `template_attributes` WRITE;
/*!40000 ALTER TABLE `template_attributes` DISABLE KEYS */;
INSERT INTO `template_attributes` VALUES ('4a62997f-aea8-4a43-8cd2-3807a509ff00','4a62982e-f070-4332-af98-19c9a509ff00','Acct-Interim-Interval','Reply','Update account data interval','Seconds','2009-07-19 05:56:47','2009-07-19 05:59:45'),('4a6299dd-b7dc-4c9b-9e66-1611a509ff00','4a62982e-f070-4332-af98-19c9a509ff00','Idle-Timeout','Reply','Disconnect after inactivity period','Seconds','2009-07-19 05:58:21','2009-07-19 05:59:27'),('4a629a43-4114-43dd-863b-19caa509ff00','4a62984f-d8e4-428b-bb45-39a8a509ff00','Acct-Interim-Interval','Reply','Update account data interval','Seconds','2009-07-19 06:00:03','2009-07-19 06:01:04'),('4a629a54-4d6c-4eff-99e9-0a08a509ff00','4a62984f-d8e4-428b-bb45-39a8a509ff00','Idle-Timeout','Reply','Disconnect after inactivity period','Seconds','2009-07-19 06:00:20','2009-07-19 06:01:08'),('4a629aa8-7f30-4f87-b904-3807a509ff00','4a629864-f8fc-475f-b694-39a8a509ff00','Acct-Interim-Interval','Reply','Update account data interval','Seconds','2009-07-19 06:01:44','2009-07-19 06:02:23'),('4a629ac7-697c-4e5f-bb81-39a8a509ff00','4a629864-f8fc-475f-b694-39a8a509ff00','Idle-Timeout','Reply','Disconnect after inactivity period','Seconds','2009-07-19 06:02:15','2009-07-19 06:02:35'),('4a629b3d-1d4c-47e0-aaa5-19caa509ff00','4a62982e-f070-4332-af98-19c9a509ff00','Yfi-Time','Check','Total amount of time online - reset monthly - allows extra caps','Text String','2009-07-19 06:04:13','2009-07-19 06:05:03'),('4a629b92-ea04-4894-9f64-431aa509ff00','4a62984f-d8e4-428b-bb45-39a8a509ff00','Yfi-Data','Check','Total amount of data - reset monthly - allows extra caps','Text String','2009-07-19 06:05:38','2009-07-19 06:06:13'),('4a629bd3-56dc-4e75-8921-39a8a509ff00','4a629864-f8fc-475f-b694-39a8a509ff00','Yfi-Data','Check','Total amount of data - reset monthly - allows extra caps','Text String','2009-07-19 06:06:43','2009-07-19 06:08:20'),('4a629be7-3fc4-44b0-a310-39a8a509ff00','4a629864-f8fc-475f-b694-39a8a509ff00','Yfi-Time','Check','Total amount of time online - reset monthly - allows extra caps','Text String','2009-07-19 06:07:03','2009-07-19 06:07:16'),('4a629c7c-0280-483c-b863-1611a509ff00','4a629864-f8fc-475f-b694-39a8a509ff00','Max-Daily-Session','Check','Daily - Only allow a daily total time of this value in seconds - Also need to activate the counter','Seconds','2009-07-19 06:09:32','2009-07-19 06:10:40'),('4a629c86-0f6c-497d-84ac-1611a509ff00','4a629864-f8fc-475f-b694-39a8a509ff00','ChilliSpot-Max-Daily-Octets','Check','Daily - Only allow a daily total bytes of this value - Also need to activate the counter','Bytes','2009-07-19 06:09:42','2009-07-19 06:10:45'),('4a629f89-6c90-4337-8a41-431aa509ff00','4a629d44-a244-4001-a547-4298a509ff00','Acct-Interim-Interval','Reply','Update account data interval','Seconds','2009-07-19 06:22:33','2009-07-19 06:34:33'),('4a629fcb-0de4-4df6-9ea3-3807a509ff00','4a629d44-a244-4001-a547-4298a509ff00','Idle-Timeout','Reply','Disconnect after inactivity period','Seconds','2009-07-19 06:23:39','2009-07-19 06:34:42'),('4a629ff7-3fa8-4bbe-a48c-0a08a509ff00','4a629d58-7114-4f73-a4db-4298a509ff00','Acct-Interim-Interval','Reply','Update account data interval','Seconds','2009-07-19 06:24:23','2009-07-19 12:54:09'),('4a629fff-1e94-4919-85b6-0a07a509ff00','4a629d58-7114-4f73-a4db-4298a509ff00','Idle-Timeout','Reply','Disconnect after inactivity period','Text String','2009-07-19 06:24:31','2009-07-19 12:57:54'),('4a62a00e-824c-4ead-94cf-19c9a509ff00','4a629d74-83d4-463a-82c1-19caa509ff00','Acct-Interim-Interval','Reply','Update account data interval','Seconds','2009-07-19 06:24:46','2009-07-19 06:26:12'),('4a62a012-2a24-4025-b64d-1611a509ff00','4a629d74-83d4-463a-82c1-19caa509ff00','Idle-Timeout','Reply','Disconnect after inactivity period','Seconds','2009-07-19 06:24:50','2009-07-19 06:26:05'),('4a62f509-7a68-423c-828c-1c7ba509ff00','4a62982e-f070-4332-af98-19c9a509ff00','ChilliSpot-Bandwidth-Max-Up','Reply','Speed Max upload','Bits','2009-07-19 12:27:21','2009-07-19 12:28:02'),('4a62f53b-c910-4fda-aa59-1c84a509ff00','4a62982e-f070-4332-af98-19c9a509ff00','ChilliSpot-Bandwidth-Max-Down','Reply','Speed Max download','Bits','2009-07-19 12:28:11','2009-07-19 12:28:37'),('4a62f959-4c68-4008-960a-1c69a509ff00','4a62984f-d8e4-428b-bb45-39a8a509ff00','ChilliSpot-Bandwidth-Max-Up','Reply','Max speed upload','Bits','2009-07-19 12:45:45','2009-07-19 12:46:08'),('4a62f97d-da44-48fb-8917-1c69a509ff00','4a62984f-d8e4-428b-bb45-39a8a509ff00','ChilliSpot-Bandwidth-Max-Down','Reply','Max speed download','Bits','2009-07-19 12:46:21','2009-07-19 12:46:47'),('4a62fbf4-7d88-4530-92dc-48a0a509ff00','4a629d58-7114-4f73-a4db-4298a509ff00','ChilliSpot-Max-All-Octets','Check','A limit on the amount of data to transfer - No reset','Bytes','2009-07-19 12:56:52','2009-07-19 13:05:53'),('4a62fc2e-0718-42c2-88ec-48a1a509ff00','4a629d58-7114-4f73-a4db-4298a509ff00','ChilliSpot-Bandwidth-Max-Up','Reply','Speed Max upload','Text String','2009-07-19 12:57:50','2009-07-19 14:27:50'),('4a62fc3f-887c-460b-ac32-48a1a509ff00','4a629d58-7114-4f73-a4db-4298a509ff00','ChilliSpot-Bandwidth-Max-Down','Reply','Speed Max download','Bits','2009-07-19 12:58:07','2009-07-19 14:28:00'),('4a631115-3c50-4849-9e0e-489da509ff00','4a629d44-a244-4001-a547-4298a509ff00','ChilliSpot-Bandwidth-Max-Up','Reply','Max upload speed','Bits','2009-07-19 14:27:01','2009-07-19 14:30:42'),('4a63111d-6ce4-4c8b-9ec5-489da509ff00','4a629d44-a244-4001-a547-4298a509ff00','ChilliSpot-Bandwidth-Max-Down','Reply','Max download speed','Bits','2009-07-19 14:27:09','2009-07-19 14:30:47'),('4a631202-8e74-4197-9488-1c69a509ff00','4a629d44-a244-4001-a547-4298a509ff00','Max-All-Session','Check','Time cap - no reset','Seconds','2009-07-19 14:30:58','2009-07-19 14:33:43'),('4a631359-50c4-4090-9e4d-489da509ff00','4a629864-f8fc-475f-b694-39a8a509ff00','ChilliSpot-Bandwidth-Max-Up','Reply','Max upload speed','Bits','2009-07-19 14:36:41','2009-07-19 14:37:11'),('4a631380-b9c0-43b6-8e01-489ea509ff00','4a629864-f8fc-475f-b694-39a8a509ff00','ChilliSpot-Bandwidth-Max-Down','Reply','Max download speed','Bits','2009-07-19 14:37:20','2009-07-19 14:37:41'),('4a6313cd-14b8-4f7a-b967-1c84a509ff00','4a629d74-83d4-463a-82c1-19caa509ff00','ChilliSpot-Bandwidth-Max-Up','Reply','Max upload speed','Bits','2009-07-19 14:38:37','2009-07-19 14:39:30'),('4a6313e3-b2dc-4899-9c87-1c84a509ff00','4a629d74-83d4-463a-82c1-19caa509ff00','ChilliSpot-Bandwidth-Max-Down','Reply','Max download speed','Bits','2009-07-19 14:38:59','2009-07-19 14:39:17'),('4a63141a-5bd4-4a0c-af12-489fa509ff00','4a629d74-83d4-463a-82c1-19caa509ff00','ChilliSpot-Max-All-Octets','Check','Max total bytes download - No reset','Bytes','2009-07-19 14:39:54','2009-07-19 14:41:26'),('4a631473-14cc-448b-8e9a-5962a509ff00','4a629d74-83d4-463a-82c1-19caa509ff00','ChilliSpot-Max-Daily-Octets','Check','Max daily bytes allowed - Reset each day','Bytes','2009-07-19 14:41:23','2009-07-19 14:41:58'),('4a6314da-ba8c-429d-837b-489da509ff00','4a629d74-83d4-463a-82c1-19caa509ff00','Max-Daily-Session','Check','Max daily time allowed - Reset each day','Seconds','2009-07-19 14:43:06','2009-07-19 14:43:43');
/*!40000 ALTER TABLE `template_attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `template_realms`
--

DROP TABLE IF EXISTS `template_realms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `template_realms` (
  `id` char(36) NOT NULL,
  `template_id` char(36) NOT NULL,
  `realm_id` char(36) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `template_realms`
--

LOCK TABLES `template_realms` WRITE;
/*!40000 ALTER TABLE `template_realms` DISABLE KEYS */;
/*!40000 ALTER TABLE `template_realms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `templates`
--

DROP TABLE IF EXISTS `templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `templates` (
  `id` char(36) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `templates`
--

LOCK TABLES `templates` WRITE;
/*!40000 ALTER TABLE `templates` DISABLE KEYS */;
INSERT INTO `templates` VALUES ('4a62982e-f070-4332-af98-19c9a509ff00','Permanent - Time Based','2009-07-19 05:51:10','2009-07-19 05:51:10'),('4a62984f-d8e4-428b-bb45-39a8a509ff00','Permanent - Data Based','2009-07-19 05:51:43','2009-07-19 05:51:43'),('4a629864-f8fc-475f-b694-39a8a509ff00','Permanent - Multi Counters','2009-07-19 05:52:04','2009-07-19 05:52:04'),('4a629d44-a244-4001-a547-4298a509ff00','Voucher - Time Based','2009-07-19 06:12:52','2009-07-19 06:12:52'),('4a629d58-7114-4f73-a4db-4298a509ff00','Voucher - Data Based','2009-07-19 06:13:12','2009-07-19 06:13:12'),('4a629d74-83d4-463a-82c1-19caa509ff00','Voucher - Multi Counters','2009-07-19 06:13:40','2009-07-19 06:13:40');
/*!40000 ALTER TABLE `templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `times`
--

DROP TABLE IF EXISTS `times`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `times` (
  `id` bigint(21) NOT NULL AUTO_INCREMENT,
  `acctsessionid` varchar(64) NOT NULL DEFAULT '',
  `username` varchar(64) NOT NULL DEFAULT '',
  `time` bigint(20) DEFAULT NULL,
  `data` bigint(20) DEFAULT NULL,
  `type` enum('Prime','Normal') DEFAULT 'Normal',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `times`
--

LOCK TABLES `times` WRITE;
/*!40000 ALTER TABLE `times` DISABLE KEYS */;
/*!40000 ALTER TABLE `times` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_realms`
--

DROP TABLE IF EXISTS `user_realms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_realms` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `realm_id` char(36) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_realms`
--

LOCK TABLES `user_realms` WRITE;
/*!40000 ALTER TABLE `user_realms` DISABLE KEYS */;
INSERT INTO `user_realms` VALUES ('4b3c03ff-93f8-4777-89f9-4847a509ff00','49d09f65-9b48-4c1e-baed-194ea509ff00','49d09ec6-5480-45d4-a5ae-2b0ea509ff00','2009-12-31 03:53:03','2009-12-31 03:53:03'),('4b3c0416-d768-42b9-abce-4845a509ff00','4a6f4cde-3f3c-4abe-84ba-7a68a509ff00','49d09ec6-5480-45d4-a5ae-2b0ea509ff00','2009-12-31 03:53:26','2009-12-31 03:53:26');
/*!40000 ALTER TABLE `user_realms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_rights`
--

DROP TABLE IF EXISTS `user_rights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_rights` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `right_id` char(36) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_rights`
--

LOCK TABLES `user_rights` WRITE;
/*!40000 ALTER TABLE `user_rights` DISABLE KEYS */;
INSERT INTO `user_rights` VALUES ('49a55424-f5dc-4403-88e5-471ca509ff00','499ef480-3e84-4725-a720-190aa509ff00','499ef500-c76c-4897-9bde-190da509ff00',0,'2009-02-25 16:22:28','2009-02-25 16:22:28'),('49b0f6b1-a178-4a91-a00b-657f924009bd','499ef49c-9c44-4279-975d-190ba509ff00','49ab946c-78cc-4b3f-ad02-2755a509ff00',1,'2009-03-06 12:10:57','2009-03-06 12:11:06'),('49b27d6f-2ee4-4341-9854-385aa509ff00','49ae6b8e-81d0-4f84-bbc1-7147a509ff00','49ae2bc4-ccd8-4660-81af-2ce7a509ff00',1,'2009-03-07 15:58:07','2009-03-07 15:58:07'),('49c24093-2a88-4f63-927f-1983924009bd','499ef49c-9c44-4279-975d-190ba509ff00','49ae2bc4-ccd8-4660-81af-2ce7a509ff00',1,'2009-03-19 14:54:43','2009-03-19 14:54:43'),('49cfbe49-c238-453b-b002-21a0a509ff00','499ef49c-9c44-4279-975d-190ba509ff00','49cfb500-7d84-4d51-adac-21a0a509ff00',1,'2009-03-29 20:30:33','2009-03-29 20:34:47'),('49cfbed2-f4f8-4b51-b66a-171fa509ff00','499ef49c-9c44-4279-975d-190ba509ff00','49cfb4e2-fa7c-4103-a509-21f0a509ff00',1,'2009-03-29 20:32:50','2009-03-29 20:33:09'),('49cfbee1-3178-40ba-8894-171fa509ff00','499ef49c-9c44-4279-975d-190ba509ff00','49cfb4c0-8418-46c2-9d9b-21e2a509ff00',1,'2009-03-29 20:33:05','2009-03-29 20:34:42'),('49cfc373-f274-46dc-9361-150ea509ff00','499ef49c-9c44-4279-975d-190ba509ff00','49cfb616-0560-477f-b07d-563fa509ff00',0,'2009-03-29 20:52:35','2009-03-29 20:52:35'),('4a61b0d1-5524-4c8c-9d19-0a08a509ff00','49d09f65-9b48-4c1e-baed-194ea509ff00','4a619486-6aa8-4511-9b27-19caa509ff00',1,'2009-07-18 13:24:01','2009-07-18 13:25:02'),('4a6f7a29-472c-4834-9566-19e5a509ff00','4a6f4cde-3f3c-4abe-84ba-7a68a509ff00','49ab946c-78cc-4b3f-ad02-2755a509ff00',0,'2009-07-29 00:22:33','2009-07-29 00:22:33'),('4a6f7a30-9ed4-40ee-b35d-19e5a509ff00','4a6f4cde-3f3c-4abe-84ba-7a68a509ff00','4a18066d-d360-402c-8164-2f8aa509ff00',0,'2009-07-29 00:22:40','2009-07-29 00:22:40'),('4a6f7a67-802c-4e84-80fd-19e6a509ff00','4a6f4cde-3f3c-4abe-84ba-7a68a509ff00','49bf5fe6-b858-42e6-b6f9-51e1a509ff00',0,'2009-07-29 00:23:35','2009-07-29 00:23:35'),('4a6f7ab5-05d4-42d6-911d-7a69a509ff00','4a6f4cde-3f3c-4abe-84ba-7a68a509ff00','4a6f7860-42ec-4f61-b946-60b5a509ff00',0,'2009-07-29 00:24:53','2009-07-29 00:24:53'),('4a6f7abf-8ed0-420e-9c12-7a69a509ff00','4a6f4cde-3f3c-4abe-84ba-7a68a509ff00','49e1d876-9718-4ba0-816f-75eba509ff00',0,'2009-07-29 00:25:03','2009-07-29 00:25:03'),('4a6f7b4b-8110-4edb-8375-19e7a509ff00','4a6f4cde-3f3c-4abe-84ba-7a68a509ff00','4a1932a9-e81c-4058-bb37-129aa509ff00',0,'2009-07-29 00:27:23','2009-07-29 00:27:23'),('4e7d9eb4-0298-40fc-81ac-03f8a509ff00','4a0b0cb6-9718-4221-886b-3706a509ff00','4e7d969a-efd8-43e5-bcc8-0eaaa509ff00',1,'2011-09-24 11:11:16','2011-09-24 11:11:16');
/*!40000 ALTER TABLE `user_rights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `username` varchar(127) NOT NULL,
  `password` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `address` varchar(200) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `cap` enum('soft','hard','local','degrade','prepaid') DEFAULT 'hard',
  `data` varchar(50) NOT NULL DEFAULT 'NA',
  `time` varchar(50) NOT NULL DEFAULT 'NA',
  `group_id` char(36) NOT NULL,
  `radcheck_id` int(11) NOT NULL DEFAULT '0',
  `profile_id` char(36) NOT NULL DEFAULT '',
  `user_id` char(36) NOT NULL DEFAULT '',
  `realm_id` char(36) NOT NULL DEFAULT '',
  `language_id` char(36) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('49d09f65-9b48-4c1e-baed-194ea509ff00','ap','6ca571061bd663816d81f119b99d78c6b6015272','Pieter','Viljoen','123 Lynwood road\nLynwood\n1234','012-880-1234','pviljoen@residence-inn.co.za',1,'hard','NA','NA','499ef455-acf4-469e-991b-2f51a509ff00',0,'','','','4a80e849-5300-46b5-9b64-4ba1a509ff00','2009-03-30 12:31:01','2009-12-31 03:53:03'),('49d09fb4-f23c-4b30-9a50-2b0ba509ff00','root','017879fe8523f697c58ee598f95843252c6ac147','Administrator','Administrator','','','',1,'hard','NA','NA','499ef44e-42e8-4615-8d51-2f51a509ff00',0,'','','','4a80e849-5300-46b5-9b64-4ba1a509ff00','2009-03-30 12:32:20','2009-12-31 03:49:50'),('4a0b0cb6-9718-4221-886b-3706a509ff00','dvdwalt@ri','87af6f583e3b19849c2d6b607ea9c8e3bba621fc','Dirk','van der Walt','15 Mimosa Street\nBrumeria\n0184\nPretoria','012-804-8080','dirkvanderwalt@gmail.com',1,'hard','13.03','NA','499ef45a-dc24-42b1-8d99-2f51a509ff00',14778,'4a62f9c4-25a8-45bc-ab56-1c84a509ff00','49d09fb4-f23c-4b30-9a50-2b0ba509ff00','49d09ec6-5480-45d4-a5ae-2b0ea509ff00','4a80e867-24d4-4824-8270-3e74a509ff00','2009-05-13 20:08:54','2011-09-23 11:39:07'),('4a6f4cde-3f3c-4abe-84ba-7a68a509ff00','accounts','8249695bc86ca246b5c68911b13d27ace3caf855','Bean','Counter','123 Bank Street\nPretoria\n0001','012-880-1234','accounts@residence-inn.co.za',1,'hard','NA','NA','499ef455-acf4-469e-991b-2f51a509ff00',0,'','','','4a80e849-5300-46b5-9b64-4ba1a509ff00','2009-07-28 21:09:18','2009-12-31 03:53:26');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vouchers`
--

DROP TABLE IF EXISTS `vouchers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vouchers` (
  `id` char(36) NOT NULL,
  `radcheck_id` int(11) NOT NULL DEFAULT '0',
  `profile_id` char(36) NOT NULL DEFAULT '',
  `user_id` char(36) NOT NULL DEFAULT '',
  `realm_id` char(36) NOT NULL DEFAULT '',
  `status` enum('new','used','depleted') DEFAULT 'new',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vouchers`
--

LOCK TABLES `vouchers` WRITE;
/*!40000 ALTER TABLE `vouchers` DISABLE KEYS */;
/*!40000 ALTER TABLE `vouchers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wireless_clients`
--

DROP TABLE IF EXISTS `wireless_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wireless_clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `na_id` int(11) NOT NULL DEFAULT '0',
  `mac` char(36) NOT NULL DEFAULT '',
  `ip` varchar(128) NOT NULL DEFAULT '',
  `user` varchar(128) NOT NULL DEFAULT '',
  `machine` varchar(128) NOT NULL DEFAULT '',
  `ssid` varchar(128) NOT NULL DEFAULT '',
  `aid` int(1) DEFAULT NULL,
  `chan` int(2) DEFAULT NULL,
  `rate` varchar(5) DEFAULT NULL,
  `rssi` int(3) DEFAULT NULL,
  `dbm` int(4) DEFAULT NULL,
  `idle` int(4) DEFAULT NULL,
  `txseq` int(4) DEFAULT NULL,
  `txfrag` int(4) DEFAULT NULL,
  `rxseq` int(4) DEFAULT NULL,
  `rxfrag` int(4) DEFAULT NULL,
  `caps` varchar(7) DEFAULT NULL,
  `erp` int(4) DEFAULT NULL,
  `state` int(2) DEFAULT NULL,
  `mode` varchar(7) DEFAULT NULL,
  `active` enum('yes','no') DEFAULT 'no',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wireless_clients`
--

LOCK TABLES `wireless_clients` WRITE;
/*!40000 ALTER TABLE `wireless_clients` DISABLE KEYS */;
/*!40000 ALTER TABLE `wireless_clients` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-04-06 17:29:49
