--
-- Table structure for table `auto_contacts`
--

DROP TABLE IF EXISTS `auto_contacts`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `auto_contacts` (
  `id` char(36) NOT NULL,
  `auto_mac_id` char(36) NOT NULL default '',
  `ip_address` varchar(15) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

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
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `auto_groups` (
  `id` char(36) NOT NULL,
  `name` varchar(80) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `auto_groups`
--

LOCK TABLES `auto_groups` WRITE;
/*!40000 ALTER TABLE `auto_groups` DISABLE KEYS */;
INSERT INTO `auto_groups` VALUES ('4b41de4a-6048-407c-b2a0-19dda509ff00','Network','2010-01-04 14:25:46','2010-01-04 14:25:46'),('4b42e302-a4e8-4d9d-8d35-3b96a509ff00','OpenVPN','2010-01-05 08:58:10','2010-01-05 08:58:10'),('4b444e2a-e32c-4f34-90b7-2252a509ff00','Wireless','2010-01-06 10:47:38','2010-01-06 10:47:38');
/*!40000 ALTER TABLE `auto_groups` ENABLE KEYS */;
UNLOCK TABLES;


LOCK TABLES `auto_groups` WRITE;
/*!40000 ALTER TABLE `auto_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `auto_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auto_macs`
--

DROP TABLE IF EXISTS `auto_macs`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `auto_macs` (
  `id` char(36) NOT NULL,
  `name` varchar(17) NOT NULL,
  `contact_ip` varchar(17) NOT NULL default '',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

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
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `auto_setups` (
  `id` char(36) NOT NULL,
  `auto_group_id` char(36) NOT NULL default '',
  `auto_mac_id` char(36) NOT NULL default '',
  `description` varchar(80) NOT NULL,
  `value` varchar(2000) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `auto_setups`
--

LOCK TABLES `auto_setups` WRITE;
/*!40000 ALTER TABLE `auto_setups` DISABLE KEYS */;
/*!40000 ALTER TABLE `auto_setups` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `credits`
--

DROP TABLE IF EXISTS `credits`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `credits` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL default '',
  `realm_id` char(36) NOT NULL default '',
  `used_by_id` char(36) default NULL,
  `expires` datetime default NULL,
  `time` bigint(20) default NULL,
  `data` bigint(20) default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;


--
-- Table structure for table `devices`
--

DROP TABLE IF EXISTS `devices`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `devices` (
  `id` char(36) NOT NULL,
  `name` varchar(17) NOT NULL,
  `description` varchar(40) NOT NULL,
  `user_id` char(36) NOT NULL default '',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;



--
-- Table structure for table `maps`
--

DROP TABLE IF EXISTS `maps`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `maps` (
  `id` char(36) NOT NULL,
  `name` varchar(40) NOT NULL,
  `value` varchar(40) NOT NULL,
  `user_id` char(36) NOT NULL default '',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;



--
-- Dumping data for table `group_rights`
--

LOCK TABLES `group_rights` WRITE;
/*!40000 ALTER TABLE `group_rights` DISABLE KEYS */;
INSERT INTO `group_rights` VALUES ('4b29dd87-8e3c-43cc-bacf-4cb0a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4b29d6aa-9afc-4568-a64a-1a02a509ff00',1,'2009-12-17 09:28:07','2009-12-17 09:28:07'),('4b29ddad-2894-4bc8-b62c-4cb0a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4b29d6cc-41c4-4341-b71d-1a04a509ff00',1,'2009-12-17 09:28:45','2009-12-17 09:28:45'),('4b29ddbf-4300-4cff-a4fb-4cb0a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4b29d71b-eb28-47ca-9bb9-4cb0a509ff00',1,'2009-12-17 09:29:03','2009-12-17 09:29:03'),('4b29dddc-0f1c-4268-a2b4-593ca509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4b29d73e-a938-4d73-8641-593ca509ff00',1,'2009-12-17 09:29:32','2009-12-17 09:29:32'),('4b29ddf3-ca78-4c88-93dc-593ca509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4b29d77b-5c00-4862-a683-1a00a509ff00',1,'2009-12-17 09:29:55','2009-12-17 09:29:55'),('4b29de05-dcb8-4c60-9745-593ca509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4b29d797-9e5c-401b-bffa-1a01a509ff00',1,'2009-12-17 09:30:13','2009-12-17 09:30:13'),('4b29de50-22d8-42d1-a48b-1a00a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4b29d7f4-3600-41b5-a20d-4d55a509ff00',1,'2009-12-17 09:31:28','2009-12-17 09:31:28'),
('4b5de52a-cc54-48da-9d26-53f3a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4b5de501-b0b4-4ee2-9b09-53e9a509ff00',1,'2010-01-25 20:38:34','2010-01-25 20:38:34');
/*!40000 ALTER TABLE `group_rights` ENABLE KEYS */;
UNLOCK TABLES;


LOCK TABLES `right_categories` WRITE;
/*!40000 ALTER TABLE `right_categories` DISABLE KEYS */;
INSERT INTO `right_categories` VALUES ('4b29d624-39d0-4a76-a5b4-4d55a509ff00','Internet Credits','2009-12-17 08:56:36','2009-12-17 08:56:36');
/*!40000 ALTER TABLE `right_categories` ENABLE KEYS */;
UNLOCK TABLES;


LOCK TABLES `rights` WRITE;
/*!40000 ALTER TABLE `rights` DISABLE KEYS */;
INSERT INTO `rights` VALUES ('4b29d6aa-9afc-4568-a64a-1a02a509ff00','credits/json_index','List Internet Credits','4b29d624-39d0-4a76-a5b4-4d55a509ff00','2009-12-17 08:58:50','2009-12-17 09:05:09'),('4b29d6cc-41c4-4341-b71d-1a04a509ff00','credits/json_add','Create Internet Credits','4b29d624-39d0-4a76-a5b4-4d55a509ff00','2009-12-17 08:59:24','2009-12-17 09:05:22'),('4b29d71b-eb28-47ca-9bb9-4cb0a509ff00','credits/json_attach','Assign  Internet Credit to Prepaid users ','4b29d624-39d0-4a76-a5b4-4d55a509ff00','2009-12-17 09:00:43','2009-12-17 09:04:55'),('4b29d73e-a938-4d73-8641-593ca509ff00','credits/json_view','View Internet Credit','4b29d624-39d0-4a76-a5b4-4d55a509ff00','2009-12-17 09:01:18','2009-12-17 09:04:40'),('4b29d77b-5c00-4862-a683-1a00a509ff00','credits/json_edit','Modify Internet Credit values','4b29d624-39d0-4a76-a5b4-4d55a509ff00','2009-12-17 09:02:19','2009-12-17 09:02:19'),('4b29d797-9e5c-401b-bffa-1a01a509ff00','credits/json_del','Remove Internet Credits','4b29d624-39d0-4a76-a5b4-4d55a509ff00','2009-12-17 09:02:47','2009-12-17 09:02:47'),('4b29d7f4-3600-41b5-a20d-4d55a509ff00','credits/only_view_own','Only see Internet Credits created self','4b29d624-39d0-4a76-a5b4-4d55a509ff00','2009-12-17 09:04:20','2009-12-17 09:04:20'),('4b5de501-b0b4-4ee2-9b09-53e9a509ff00','realms/json_stats','Stats per Realm','499ef4e3-ff68-4f25-ac59-190ea509ff00','2010-01-25 20:37:53','2010-01-25 20:37:53');
/*!40000 ALTER TABLE `rights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rogue_aps`
--

DROP TABLE IF EXISTS `rogue_aps`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `rogue_aps` (
  `id` char(36) NOT NULL,
  `na_id` char(36) NOT NULL,
  `ssid` varchar(128) NOT NULL default '',
  `mac` varchar(128) NOT NULL default '',
  `mode` varchar(50) NOT NULL default '',
  `channel` int(2) NOT NULL default '0',
  `quality` varchar(20) NOT NULL default '',
  `signal` varchar(5) NOT NULL default '',
  `noise` varchar(5) NOT NULL default '',
  `encryption` varchar(20) NOT NULL default '',
  `state` enum('Known','Unknown') default 'Unknown',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;


--
-- Add Spanish Language to list of languages
--
LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES ('4b374060-7c78-4fcf-935e-2322a509ff00','Spanish','es_ES','2009-12-27 13:09:20','2009-12-27 13:09:20');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Add prepaid as a choice
--
alter table users modify column `cap` enum('soft','hard','local','degrade','prepaid') default 'hard';


--
-- Alter float to double lon and lat (google maps)
--
alter table nas modify column `lat` double default NULL;
alter table nas modify column `lon` double default NULL;

--
-- Alter the nas table by adding a photo location per nas
--
alter table nas add column `photo_file_name` varchar(128) NOT NULL default 'logo.jpg';

--
-- Table structure for table `times`
--

DROP TABLE IF EXISTS `times`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `times` (
  `id` bigint(21) NOT NULL auto_increment,
  `acctsessionid` varchar(64) NOT NULL default '',
  `username` varchar(64) NOT NULL default '',
  `time` bigint(20) default NULL,
  `data` bigint(20) default NULL,
  `type` enum('Prime','Normal') default 'Normal',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

