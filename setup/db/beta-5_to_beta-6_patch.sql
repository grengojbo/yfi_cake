LOCK TABLES `rights` WRITE;
/*!40000 ALTER TABLE `rights` DISABLE KEYS */;
INSERT INTO `rights` VALUES ('4e7d969a-efd8-43e5-bcc8-0eaaa509ff00','tab/show_devices','Right to show devices belonging to the user (MAC authentication)','4b91e5b7-2d80-4e9b-bde6-7047a509ff00','2011-09-24 10:36:42','2011-09-24 10:36:42'),('4e7d9d80-481c-4e70-853b-0fb3a509ff00','devices/json_index','List devices belonging to a user','4e7d9d37-9228-4c64-ae0d-0e9fa509ff00','2011-09-24 11:06:08','2011-09-24 11:09:01'),('4e7d9e4a-0c3c-4e87-9e9f-0eaba509ff00','devices/json_add','Add new device','4e7d9d37-9228-4c64-ae0d-0e9fa509ff00','2011-09-24 11:09:30','2011-09-24 11:09:30'),('4e7d9e5c-f788-4a29-b68b-0eaba509ff00','devices/json_del','Remove device','4e7d9d37-9228-4c64-ae0d-0e9fa509ff00','2011-09-24 11:09:48','2011-09-24 11:09:48');
/*!40000 ALTER TABLE `rights` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `user_rights` WRITE;
/*!40000 ALTER TABLE `user_rights` DISABLE KEYS */;
INSERT INTO `user_rights` VALUES ('4e7d9eb4-0298-40fc-81ac-03f8a509ff00','4a0b0cb6-9718-4221-886b-3706a509ff00','4e7d969a-efd8-43e5-bcc8-0eaaa509ff00',1,'2011-09-24 11:11:16','2011-09-24 11:11:16');
/*!40000 ALTER TABLE `user_rights` ENABLE KEYS */;
UNLOCK TABLES;


LOCK TABLES `right_categories` WRITE;
/*!40000 ALTER TABLE `right_categories` DISABLE KEYS */;
INSERT INTO `right_categories` VALUES ('4e7d9d37-9228-4c64-ae0d-0e9fa509ff00','User Portal -> Devices','2011-09-24 11:04:55','2011-09-24 11:04:55');
/*!40000 ALTER TABLE `right_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `group_rights`
--

LOCK TABLES `group_rights` WRITE;
/*!40000 ALTER TABLE `group_rights` DISABLE KEYS */;
INSERT INTO `group_rights` VALUES ('4e7d96c5-508c-42c5-992b-0ea8a509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4e7d969a-efd8-43e5-bcc8-0eaaa509ff00',0,'2011-09-24 10:37:25','2011-09-24 10:37:25'),('4e7d9f4a-0154-4152-a3a7-03faa509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4e7d9d80-481c-4e70-853b-0fb3a509ff00',1,'2011-09-24 11:13:46','2011-09-24 11:13:46'),('4e7d9f69-4578-47d3-8956-0ea9a509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4e7d9e4a-0c3c-4e87-9e9f-0eaba509ff00',1,'2011-09-24 11:14:17','2011-09-24 11:14:17'),('4e7d9f78-786c-436b-bc54-0ea9a509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4e7d9e5c-f788-4a29-b68b-0eaba509ff00',1,'2011-09-24 11:14:32','2011-09-24 11:14:32');
/*!40000 ALTER TABLE `group_rights` ENABLE KEYS */;
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
-- Alter the nas table by a column to specify a default realm
--
alter table nas add column `realm_id` char(36) NOT NULL;

--
-- Add the Italian language 
--
LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES ('4fd2d35a-ef54-452b-ba6d-0f83a509ff00', 'Italian','it_IT','2011-08-14 15:45:21','2011-08-14 15:45:21');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;



