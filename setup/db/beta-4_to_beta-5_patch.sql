--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `notes` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL default '',
  `section_id` char(36) NOT NULL default '',
  `value` varchar(500) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;


--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `sections` (
  `id` char(36) NOT NULL,
  `name` varchar(80) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;


--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` VALUES ('4b920ee0-03c0-4abf-902a-13a4a509ff00','Technical','2010-03-06 10:14:24','2010-03-06 10:14:24'),('4b920ee9-fcf8-4f55-b0a3-13a4a509ff00','Accounting','2010-03-06 10:14:33','2010-03-06 10:14:33'),('4b920f0a-7264-4c95-a35f-1987a509ff00','General','2010-03-06 10:15:06','2010-03-06 10:15:06'),('4b974708-6cb4-48ef-af9e-3e83a509ff00','Self-service','2010-03-10 09:15:20','2010-03-10 09:15:20');
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `group_rights`
--

LOCK TABLES `group_rights` WRITE;
/*!40000 ALTER TABLE `group_rights` DISABLE KEYS */;
INSERT INTO `group_rights` VALUES ('4b91d65b-49c4-47c6-9157-13a4a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49ab942c-f9bc-46ad-b119-19dea509ff00',0,'2010-03-06 06:13:15','2010-03-06 06:13:15'),('4b91d68e-9fb8-4ab6-8fcf-1989a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49ae2bc4-ccd8-4660-81af-2ce7a509ff00',0,'2010-03-06 06:14:06','2010-03-06 06:14:06'),('4b91d6ea-0fcc-4858-b529-15e0a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4a193830-1b54-4b9b-8858-542ea509ff00',0,'2010-03-06 06:15:38','2010-03-06 06:15:38'),('4b91d70b-772c-47c0-a263-1986a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','49ae4e7f-73b8-46c7-864c-7146a509ff00',0,'2010-03-06 06:16:11','2010-03-06 06:16:11'),('4b91f163-ac3c-47e8-9063-52a9a509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b91e84b-64c4-48fb-b58b-13a4a509ff00',0,'2010-03-06 08:08:35','2010-03-06 08:08:35'),('4b91f173-408c-4652-839d-52a9a509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b91e887-eb50-4858-8dfb-1987a509ff00',0,'2010-03-06 08:08:51','2010-03-06 08:08:51'),('4b96a075-3b64-45d2-8a20-308fa509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b969fb2-29ec-4158-9c9d-1999a509ff00',0,'2010-03-09 21:24:37','2010-03-09 21:24:37'),('4b96a08d-03d0-4332-ad5c-199aa509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b969fd6-c014-4b70-bf18-199ca509ff00',1,'2010-03-09 21:25:01','2010-03-09 21:25:01'),('4b96a09f-a260-4e7f-9bca-199aa509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b969ff8-14c0-40ea-b198-308da509ff00',1,'2010-03-09 21:25:19','2010-03-09 21:25:19'),('4b96a108-1c70-4477-986b-199ba509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b96a01a-d620-423a-95a2-308ea509ff00',1,'2010-03-09 21:27:04','2010-03-09 21:27:04'),('4b96a115-6c68-4cdf-be57-199ba509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b96a036-2900-43e8-9c7b-3090a509ff00',1,'2010-03-09 21:27:17','2010-03-09 21:27:17'),('4b96a121-1b24-4245-96bb-199ba509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b96a052-fe60-4c9a-81cb-308fa509ff00',1,'2010-03-09 21:27:29','2010-03-09 21:27:29'),('4b97e1bb-6d24-41d3-9b2b-198fa509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b97e0ca-1594-4a40-8625-5202a509ff00',1,'2010-03-10 20:15:23','2010-03-10 20:15:23'),('4b97e1cb-74ec-40ca-96f3-198fa509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b97e10c-492c-43f3-8a8b-198ba509ff00',1,'2010-03-10 20:15:39','2010-03-10 20:15:39'),('4b97e1db-7d08-4329-a5be-198fa509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b97e12e-7eb8-432a-98fa-19aca509ff00',1,'2010-03-10 20:15:55','2010-03-10 20:15:55'),('4b97e1ee-bff8-4385-8a84-198fa509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b97e16a-0444-4c5c-9bd0-1943a509ff00',1,'2010-03-10 20:16:14','2010-03-10 20:16:14'),('4b97e1fb-6f28-4656-a5bc-198fa509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b97e1a5-21ec-475e-a013-198fa509ff00',1,'2010-03-10 20:16:27','2010-03-10 20:16:27'),('4b9857e5-fda8-4f99-a755-5202a509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b985797-4e3c-48a8-9faf-5201a509ff00',0,'2010-03-11 04:39:33','2010-03-11 04:41:23'),('4b9857f7-d730-49d1-8963-5202a509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b9857b9-66dc-4dbe-ae4a-5c7da509ff00',0,'2010-03-11 04:39:51','2010-03-11 04:41:35'),('4b994567-e814-436d-810a-6c90a509ff00','499ef45a-dc24-42b1-8d99-2f51a509ff00','4b994552-4640-4c95-a747-6c90a509ff00',0,'2010-03-11 21:32:55','2010-03-11 21:32:55'),('4c5863ce-8020-4e51-b810-0545a509ff00','499ef455-acf4-469e-991b-2f51a509ff00','4c5863ae-3984-48e3-adde-0544a509ff00',1,'2010-08-03 20:45:34','2010-08-03 20:45:34');
/*!40000 ALTER TABLE `group_rights` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `right_categories`
--

LOCK TABLES `right_categories` WRITE;
/*!40000 ALTER TABLE `right_categories` DISABLE KEYS */;
INSERT INTO `right_categories` VALUES ('4b91e5b7-2d80-4e9b-bde6-7047a509ff00','User Portal Tabs','2010-03-06 07:18:47','2010-03-06 07:18:47'),('4b969f70-2ebc-4cfc-b30f-3078a509ff00','User Portal -> User Detail','2010-03-09 21:20:16','2010-03-09 21:20:16'),('4b97e031-6e68-4a5b-84ff-51f7a509ff00','User Portal -> Notification','2010-03-10 20:08:49','2010-03-10 20:08:49'),('4b985731-2f68-41c3-bfe8-1943a509ff00','User Portal -> Usage','2010-03-11 04:36:33','2010-03-11 04:36:33');
/*!40000 ALTER TABLE `right_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `rights`
--

LOCK TABLES `rights` WRITE;
/*!40000 ALTER TABLE `rights` DISABLE KEYS */;
INSERT INTO `rights` VALUES ('4b91e84b-64c4-48fb-b58b-13a4a509ff00','tab/show_profile_attributes','Show profile attributes tab','4b91e5b7-2d80-4e9b-bde6-7047a509ff00','2010-03-06 07:29:47','2010-03-06 07:29:47'),('4b91e887-eb50-4858-8dfb-1987a509ff00','tab/show_private_attributes','Show private attributes tab','4b91e5b7-2d80-4e9b-bde6-7047a509ff00','2010-03-06 07:30:47','2010-03-06 07:30:47'),('4b969fb2-29ec-4158-9c9d-1999a509ff00','update/cap_type','Change cap type','4b969f70-2ebc-4cfc-b30f-3078a509ff00','2010-03-09 21:21:22','2010-03-09 21:21:22'),('4b969fd6-c014-4b70-bf18-199ca509ff00','update/name','Change Name','4b969f70-2ebc-4cfc-b30f-3078a509ff00','2010-03-09 21:21:58','2010-03-09 21:21:58'),('4b969ff8-14c0-40ea-b198-308da509ff00','update/surname','Change Surname','4b969f70-2ebc-4cfc-b30f-3078a509ff00','2010-03-09 21:22:32','2010-03-09 21:22:32'),('4b96a01a-d620-423a-95a2-308ea509ff00','update/address','Change Address','4b969f70-2ebc-4cfc-b30f-3078a509ff00','2010-03-09 21:23:06','2010-03-09 21:23:06'),('4b96a036-2900-43e8-9c7b-3090a509ff00','update/phone','Change Phone','4b969f70-2ebc-4cfc-b30f-3078a509ff00','2010-03-09 21:23:34','2010-03-09 21:23:34'),('4b96a052-fe60-4c9a-81cb-308fa509ff00','update/email','Change e-mail','4b969f70-2ebc-4cfc-b30f-3078a509ff00','2010-03-09 21:24:02','2010-03-09 21:24:02'),('4b97e0ca-1594-4a40-8625-5202a509ff00','notify/type','Type of notification on usage','4b97e031-6e68-4a5b-84ff-51f7a509ff00','2010-03-10 20:11:22','2010-03-10 20:11:22'),('4b97e10c-492c-43f3-8a8b-198ba509ff00','notify/address1','Main notification address','4b97e031-6e68-4a5b-84ff-51f7a509ff00','2010-03-10 20:12:28','2010-03-10 20:12:28'),('4b97e12e-7eb8-432a-98fa-19aca509ff00','notify/address2','Secondary notification address','4b97e031-6e68-4a5b-84ff-51f7a509ff00','2010-03-10 20:13:02','2010-03-10 20:13:02'),('4b97e16a-0444-4c5c-9bd0-1943a509ff00','notify/start','Percentage to start notification','4b97e031-6e68-4a5b-84ff-51f7a509ff00','2010-03-10 20:14:02','2010-03-10 20:14:02'),('4b97e1a5-21ec-475e-a013-198fa509ff00','notify/increment','Every step after start percent to notify','4b97e031-6e68-4a5b-84ff-51f7a509ff00','2010-03-10 20:15:01','2010-03-10 20:15:01'),('4b985797-4e3c-48a8-9faf-5201a509ff00','usage/add_time','Add Extra time CAP','4b985731-2f68-41c3-bfe8-1943a509ff00','2010-03-11 04:38:15','2010-03-11 04:38:15'),('4b9857b9-66dc-4dbe-ae4a-5c7da509ff00','usage/add_data','Add Extra data CAP','4b985731-2f68-41c3-bfe8-1943a509ff00','2010-03-11 04:38:49','2010-03-11 04:38:49'),('4b994552-4640-4c95-a747-6c90a509ff00','update/profile','Change Profile for user','4b969f70-2ebc-4cfc-b30f-3078a509ff00','2010-03-11 21:32:34','2010-03-11 21:32:34'),('4c5863ae-3984-48e3-adde-0544a509ff00','permanent_users/json_prepaid_list','List Prepaid users','4b29d624-39d0-4a76-a5b4-4d55a509ff00','2010-08-03 20:45:02','2010-08-03 20:45:02');
/*!40000 ALTER TABLE `rights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Add Thai Language support
-- 

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES ('4bcffb4b-b3c8-45eb-a578-6f52a509ff00','Thai','th_TH','2010-04-22 09:31:23','2010-04-22 09:31:23'),('4c99e65a-d014-4dd1-984d-1bb3a509ff00','Portugues','pt_BR','2010-09-22 13:19:54','2010-09-22 13:19:54');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;


