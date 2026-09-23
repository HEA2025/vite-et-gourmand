-- MySQL dump 10.13  Distrib 8.0.39, for Win64 (x86_64)
--
-- Host: localhost    Database: vite_et_gourmand
-- ------------------------------------------------------
-- Server version	8.0.39

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `allergene`
--

LOCK TABLES `allergene` WRITE;
/*!40000 ALTER TABLE `allergene` DISABLE KEYS */;
INSERT INTO `allergene` VALUES (17,'Gluten'),(18,'Lait'),(19,'Œufs'),(20,'Fruits à coque');
/*!40000 ALTER TABLE `allergene` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `avis`
--

LOCK TABLES `avis` WRITE;
/*!40000 ALTER TABLE `avis` DISABLE KEYS */;
INSERT INTO `avis` VALUES (1,5,'delicieux','validé','2026-09-19 15:41:37',4),(2,5,'Excellent','validé','2026-09-21 13:32:37',13);
/*!40000 ALTER TABLE `avis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `commande`
--

LOCK TABLES `commande` WRITE;
/*!40000 ALTER TABLE `commande` DISABLE KEYS */;
INSERT INTO `commande` VALUES (1,'10 rue Sainte-Catherine, 33000 Bordeaux','2026-09-25','12:30:00',2,45.00,0.00,0.00,45.00,'2026-09-19 12:54:19',8,7,0,0,NULL,NULL,NULL),(2,'10 rue Sainte-Catherine, 33000 Bordeaux','2026-09-25','12:30:00',4,45.00,0.00,0.00,45.00,'2026-09-19 12:54:42',8,7,0,1,'gsm','Modification demandée par le client','2026-09-19 15:27:04'),(3,'1 avenue de Paris, 33600 Pessac','2026-09-25','12:30:00',7,45.00,4.50,10.90,51.40,'2026-09-19 12:55:58',8,7,10,0,NULL,NULL,NULL),(4,'10 rue Sainte-Catherine, 33000 Bordeaux','2026-09-27','12:30:00',2,45.00,0.00,0.00,45.00,'2026-09-19 13:18:37',8,7,0,0,NULL,NULL,NULL),(5,'10 rue Sainte-Catherine, 33000 Bordeaux','2026-09-26','12:30:00',2,45.00,0.00,0.00,45.00,'2026-09-19 14:47:12',8,6,0,0,NULL,NULL,NULL),(6,'10 rue Sainte-Catherine, 33000 Bordeaux','2026-09-26','12:30:00',7,45.00,4.50,0.00,40.50,'2026-09-19 14:47:35',8,6,0,0,NULL,NULL,NULL),(7,'10 rue Sainte-Catherine, 33000 Bordeaux','2026-09-26','12:30:00',7,45.00,4.50,10.90,51.40,'2026-09-19 14:47:50',8,6,10,0,NULL,NULL,NULL),(8,'10 rue Sainte-Catherine, 33000 Bordeaux','2026-09-28','14:30:00',8,45.00,4.50,0.00,40.50,'2026-09-19 16:04:54',8,7,0,0,NULL,NULL,NULL),(9,'1 avenue de Paris, 33600 Pessac','2026-09-22','14:00:00',4,35.00,0.00,10.90,45.90,'2026-09-20 18:49:30',9,6,10,0,NULL,NULL,NULL),(10,'10 rue Sainte-Catherine, 33000 Bordeaux','2026-09-23','12:00:00',8,35.00,3.50,0.00,31.50,'2026-09-20 18:52:28',9,6,0,0,NULL,NULL,NULL),(11,'10 rue Sainte-Catherine, 33000 Bordeaux','2026-09-22','11:30:00',4,70.00,0.00,0.00,70.00,'2026-09-20 19:18:04',9,6,0,0,NULL,NULL,NULL),(12,'1 avenue de Paris, 33600 Pessac','2026-09-25','20:00:00',8,140.00,14.00,10.90,136.90,'2026-09-20 19:20:10',9,6,10,0,NULL,NULL,NULL),(13,'10 rue Sainte-Catherine, 33000 Bordeaux','2026-09-25','19:20:00',2,45.00,0.00,0.00,45.00,'2026-09-21 13:18:11',8,10,0,0,NULL,NULL,NULL),(14,'10 rue Sainte-Catherine, 33000 Bordeaux','2026-09-22','14:00:00',3,52.50,0.00,0.00,52.50,'2026-09-22 11:49:57',9,10,0,0,NULL,NULL,NULL),(15,'10 rue Sainte-Catherine, 33000 Bordeaux','2026-09-22','14:00:00',3,52.50,0.00,0.00,52.50,'2026-09-22 11:54:33',9,10,0,0,'email','Erreur d\'adresse','2026-09-22 11:57:10');
/*!40000 ALTER TABLE `commande` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `doctrine_migration_versions`
--

LOCK TABLES `doctrine_migration_versions` WRITE;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20260915140711','2026-09-15 14:19:51',1693),('DoctrineMigrations\\Version20260916095242','2026-09-16 09:53:45',81),('DoctrineMigrations\\Version20260916203049','2026-09-16 20:36:10',20),('DoctrineMigrations\\Version20260917145143','2026-09-17 14:52:29',178),('DoctrineMigrations\\Version20260917160835','2026-09-17 16:09:24',181),('DoctrineMigrations\\Version20260917200517','2026-09-17 20:12:41',84),('DoctrineMigrations\\Version20260919123917','2026-09-19 12:39:35',59),('DoctrineMigrations\\Version20260919145735','2026-09-19 14:58:02',56);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `historique_statut_commande`
--

LOCK TABLES `historique_statut_commande` WRITE;
/*!40000 ALTER TABLE `historique_statut_commande` DISABLE KEYS */;
INSERT INTO `historique_statut_commande` VALUES (1,'en attente','2026-09-19 13:18:37',4),(2,'acceptée','2026-09-19 13:38:42',4),(3,'en préparation','2026-09-19 13:40:05',4),(4,'en cours de livraison','2026-09-19 13:41:44',4),(5,'livrée','2026-09-19 13:42:20',4),(6,'terminée','2026-09-19 13:42:27',4),(7,'en attente','2026-09-19 14:47:12',5),(8,'en attente','2026-09-19 14:47:35',6),(9,'en attente','2026-09-19 14:47:50',7),(10,'acceptée','2026-09-19 15:27:49',2),(11,'en préparation','2026-09-19 15:27:59',2),(12,'en cours de livraison','2026-09-19 15:28:08',2),(13,'livrée','2026-09-19 15:28:33',2),(14,'acceptée','2026-09-19 15:28:53',2),(15,'en attente du retour de matériel','2026-09-19 15:29:07',2),(16,'terminée','2026-09-19 15:29:58',2),(17,'en attente','2026-09-19 16:04:55',8),(18,'annulée','2026-09-19 16:05:48',1),(19,'en attente','2026-09-20 18:49:31',9),(20,'en attente','2026-09-20 18:52:28',10),(21,'en attente','2026-09-20 19:18:04',11),(22,'en attente','2026-09-20 19:20:11',12),(23,'en attente','2026-09-21 13:18:11',13),(24,'acceptée','2026-09-21 13:21:21',13),(25,'acceptée','2026-09-21 13:22:00',12),(26,'acceptée','2026-09-21 13:22:19',11),(27,'acceptée','2026-09-21 13:22:47',10),(28,'acceptée','2026-09-21 13:23:00',9),(29,'acceptée','2026-09-21 13:23:13',8),(30,'acceptée','2026-09-21 13:23:22',7),(31,'en préparation','2026-09-21 13:23:50',4),(32,'acceptée','2026-09-21 13:24:06',3),(33,'en préparation','2026-09-21 13:24:23',5),(34,'en préparation','2026-09-21 13:24:41',3),(35,'en cours de livraison','2026-09-21 13:24:56',3),(36,'en cours de livraison','2026-09-21 13:25:08',5),(37,'en cours de livraison','2026-09-21 13:25:19',4),(38,'en cours de livraison','2026-09-21 13:25:33',10),(39,'en cours de livraison','2026-09-21 13:25:44',11),(40,'en cours de livraison','2026-09-21 13:26:00',9),(41,'livrée','2026-09-21 13:26:11',11),(42,'livrée','2026-09-21 13:26:23',10),(43,'livrée','2026-09-21 13:26:34',9),(44,'en cours de livraison','2026-09-21 13:27:02',13),(45,'livrée','2026-09-21 13:27:15',13),(46,'terminée','2026-09-21 13:27:35',13),(47,'en attente','2026-09-22 11:49:57',14),(48,'annulée','2026-09-22 11:52:07',14),(49,'en attente','2026-09-22 11:54:33',15),(50,'annulée','2026-09-22 11:57:10',15);
/*!40000 ALTER TABLE `historique_statut_commande` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `horaire`
--

LOCK TABLES `horaire` WRITE;
/*!40000 ALTER TABLE `horaire` DISABLE KEYS */;
INSERT INTO `horaire` VALUES (1,'Lundi','09:00:00','23:00:00'),(2,'Mardi','09:30:00','22:00:00'),(3,'Mercredi','10:00:00','22:30:00'),(4,'Jeudi','10:30:00','23:00:00'),(5,'Vendredi','09:15:00','22:40:00'),(6,'Samedi','10:00:00','00:00:00'),(7,'Dimanche','09:00:00','16:00:00');
/*!40000 ALTER TABLE `horaire` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `image_menu`
--

LOCK TABLES `image_menu` WRITE;
/*!40000 ALTER TABLE `image_menu` DISABLE KEYS */;
INSERT INTO `image_menu` VALUES (5,'menu-gourmand.png',8),(6,'plat-gourmand.png',8),(7,'dessert-chocolat.png',8),(8,'menu-vegetarien.png',9);
/*!40000 ALTER TABLE `image_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (8,'Menu Gourmand','Un menu gourmand et traditionnel.',2,45.00,'Commande au minimum 48 heures à l’avance.',10,17,13),(9,'Menu Végétarien','Un menu sans viande à base de légumes de saison.',2,35.00,'Commande au minimum 48 heures à l’avance.',8,17,14),(10,'Menu Vegan','un repas composé exclusivement d\'aliments d\'origine végétale.',2,35.00,'Conditions du menu\nCommande au minimum 48 heures à l’avance.',4,17,13),(12,'Menu Méditerranéen','Un menu frais et gourmand aux saveurs méditerranéennes.',4,80.00,'Conditions du menu\nCommande au minimum 12 heures à l’avance.',4,17,13);
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `menu_plat`
--

LOCK TABLES `menu_plat` WRITE;
/*!40000 ALTER TABLE `menu_plat` DISABLE KEYS */;
INSERT INTO `menu_plat` VALUES (8,17),(8,18),(8,20),(9,17),(9,19),(9,21),(10,17),(10,23),(10,29),(12,18),(12,25),(12,29);
/*!40000 ALTER TABLE `menu_plat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `plat`
--

LOCK TABLES `plat` WRITE;
/*!40000 ALTER TABLE `plat` DISABLE KEYS */;
INSERT INTO `plat` VALUES (17,'Salade gourmande','Salade fraîche aux légumes de saison.','entrée'),(18,'Saumon rôti','Saumon rôti accompagné de légumes.','plat'),(19,'Gratin de légumes','Gratin de légumes de saison.','plat'),(20,'Fondant au chocolat','Fondant au chocolat maison.','dessert'),(21,'Salade de fruits','Fruits frais de saison.','dessert'),(23,'Curry de poid chiches','Curry onctueux de pois chiches, lentilles corail et jeunes épinards au lait de coco infusé aux épices douces, servi avec son riz basmati.','plat'),(25,'Salade méditerranéenne','Salade fraîche composée de tomates, concombre, olives et herbes aromatiques.','entrée'),(29,'Soupe de fruits rouges','Fruits rouges fraîchement infusée à la menthe, surmontée d\'un sorbet à la framboise maison.','dessert');
/*!40000 ALTER TABLE `plat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `plat_allergene`
--

LOCK TABLES `plat_allergene` WRITE;
/*!40000 ALTER TABLE `plat_allergene` DISABLE KEYS */;
INSERT INTO `plat_allergene` VALUES (19,18),(20,17),(20,18),(20,19);
/*!40000 ALTER TABLE `plat_allergene` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `regime`
--

LOCK TABLES `regime` WRITE;
/*!40000 ALTER TABLE `regime` DISABLE KEYS */;
INSERT INTO `regime` VALUES (13,'Classique'),(14,'Végétarien'),(15,'Vegan');
/*!40000 ALTER TABLE `regime` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `reset_password_request`
--

LOCK TABLES `reset_password_request` WRITE;
/*!40000 ALTER TABLE `reset_password_request` DISABLE KEYS */;
INSERT INTO `reset_password_request` VALUES (1,'Uvkanrxxb381LyaKTNzP','8s+OP6n7Rgq4k8bc0l90E4PC8aC2wGdj1DbqY5N+jqk=','2026-09-21 10:06:50','2026-09-21 11:06:50',7);
/*!40000 ALTER TABLE `reset_password_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `theme`
--

LOCK TABLES `theme` WRITE;
/*!40000 ALTER TABLE `theme` DISABLE KEYS */;
INSERT INTO `theme` VALUES (17,'Classique'),(18,'Noël'),(19,'Pâques'),(20,'Événement');
/*!40000 ALTER TABLE `theme` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `utilisateur`
--

LOCK TABLES `utilisateur` WRITE;
/*!40000 ALTER TABLE `utilisateur` DISABLE KEYS */;
INSERT INTO `utilisateur` VALUES (6,'jose@vite-et-gourmand.fr','[\"ROLE_ADMINISTRATEUR\"]','$2y$13$q/2/sYf7XTf/ISkxTOshF.NNHq1RUj/qhB5R9ByrZobqhELlHl2eS','José','José','0600000000','Adresse à compléter',1),(7,'jean@vite-et-gourmand.fr','[\"ROLE_USER\"]','$2y$13$7B/8L5.hkn1eTn5W8hhFw.g3ZzEvE.6o/ABegDeqA8bS0JMofcWuq','Dupont','Jean','0612345678','10 rue Sainte-Catherine, 33000 Bordeaux',1),(8,'employe.test@vite-et-gourmand.fr','[\"ROLE_EMPLOYE\"]','$2y$13$tO3RFa/5VKzMo1Mvl35roufM7Cnvg1arPW8AZHwnaEdNAaIenG06i',NULL,NULL,NULL,NULL,0),(9,'employe@vite-et-gourmand.fr','[\"ROLE_EMPLOYE\"]','$2y$13$cd9qvwtnUXbosgPyd4U68O4jeTO.Ayn/YZfNCqD34akx9Y6WfUr9.',NULL,NULL,NULL,NULL,1),(10,'Steve@gmail.com','[\"ROLE_USER\"]','$2y$13$gmLDVQ.m7jcBHQgJzTejCetj5whgewWqkQI8LH.skHBYML1.n8hc6','Migo','Steve','0612131415','1 rue Louis de Jabrun, 33000 Bordeaux',1);
/*!40000 ALTER TABLE `utilisateur` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-23 21:01:25
