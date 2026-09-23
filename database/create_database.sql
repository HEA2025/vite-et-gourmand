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
-- Table structure for table `allergene`
--

DROP TABLE IF EXISTS `allergene`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `allergene` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `avis`
--

DROP TABLE IF EXISTS `avis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `note` int NOT NULL,
  `commentaire` longtext NOT NULL,
  `statut` varchar(20) NOT NULL,
  `date_creation` datetime NOT NULL,
  `commande_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8F91ABF082EA2E54` (`commande_id`),
  CONSTRAINT `FK_8F91ABF082EA2E54` FOREIGN KEY (`commande_id`) REFERENCES `commande` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `commande`
--

DROP TABLE IF EXISTS `commande`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commande` (
  `id` int NOT NULL AUTO_INCREMENT,
  `adresse_prestation` varchar(255) NOT NULL,
  `date_prestation` date NOT NULL,
  `heure_livraison` time NOT NULL,
  `nombre_personnes` int NOT NULL,
  `prix_menu` decimal(10,2) NOT NULL,
  `reduction` decimal(10,2) NOT NULL,
  `frais_livraison` decimal(10,2) NOT NULL,
  `prix_total` decimal(10,2) NOT NULL,
  `date_creation` datetime NOT NULL,
  `menu_id` int NOT NULL,
  `utilisateur_id` int NOT NULL,
  `distance_livraison` double DEFAULT NULL,
  `materiel_prete` tinyint NOT NULL DEFAULT '0',
  `moyen_contact_employe` varchar(20) DEFAULT NULL,
  `motif_intervention_employe` longtext,
  `date_contact_employe` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6EEAA67DCCD7E912` (`menu_id`),
  KEY `IDX_6EEAA67DFB88E14F` (`utilisateur_id`),
  CONSTRAINT `FK_6EEAA67DCCD7E912` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`),
  CONSTRAINT `FK_6EEAA67DFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `historique_statut_commande`
--

DROP TABLE IF EXISTS `historique_statut_commande`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historique_statut_commande` (
  `id` int NOT NULL AUTO_INCREMENT,
  `statut` varchar(50) NOT NULL,
  `date_modification` datetime NOT NULL,
  `commande_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CAB4641E82EA2E54` (`commande_id`),
  CONSTRAINT `FK_CAB4641E82EA2E54` FOREIGN KEY (`commande_id`) REFERENCES `commande` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `horaire`
--

DROP TABLE IF EXISTS `horaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `horaire` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jour` varchar(20) NOT NULL,
  `heure_ouverture` time NOT NULL,
  `heure_fermeture` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `image_menu`
--

DROP TABLE IF EXISTS `image_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `image_menu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_fichier` varchar(255) NOT NULL,
  `menu_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8F3FD00DCCD7E912` (`menu_id`),
  CONSTRAINT `FK_8F3FD00DCCD7E912` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(100) NOT NULL,
  `description` longtext NOT NULL,
  `nombre_minimum_personnes` int NOT NULL,
  `prix_minimum` decimal(10,2) NOT NULL,
  `conditions` longtext NOT NULL,
  `stock` int NOT NULL,
  `theme_id` int NOT NULL,
  `regime_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7D053A9359027487` (`theme_id`),
  KEY `IDX_7D053A9335E7D534` (`regime_id`),
  CONSTRAINT `FK_7D053A9335E7D534` FOREIGN KEY (`regime_id`) REFERENCES `regime` (`id`),
  CONSTRAINT `FK_7D053A9359027487` FOREIGN KEY (`theme_id`) REFERENCES `theme` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu_plat`
--

DROP TABLE IF EXISTS `menu_plat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_plat` (
  `menu_id` int NOT NULL,
  `plat_id` int NOT NULL,
  PRIMARY KEY (`menu_id`,`plat_id`),
  KEY `IDX_E8775249CCD7E912` (`menu_id`),
  KEY `IDX_E8775249D73DB560` (`plat_id`),
  CONSTRAINT `FK_E8775249CCD7E912` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_E8775249D73DB560` FOREIGN KEY (`plat_id`) REFERENCES `plat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plat`
--

DROP TABLE IF EXISTS `plat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `description` longtext NOT NULL,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plat_allergene`
--

DROP TABLE IF EXISTS `plat_allergene`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plat_allergene` (
  `plat_id` int NOT NULL,
  `allergene_id` int NOT NULL,
  PRIMARY KEY (`plat_id`,`allergene_id`),
  KEY `IDX_6FA44BBFD73DB560` (`plat_id`),
  KEY `IDX_6FA44BBF4646AB2` (`allergene_id`),
  CONSTRAINT `FK_6FA44BBF4646AB2` FOREIGN KEY (`allergene_id`) REFERENCES `allergene` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_6FA44BBFD73DB560` FOREIGN KEY (`plat_id`) REFERENCES `plat` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `regime`
--

DROP TABLE IF EXISTS `regime`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `regime` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reset_password_request`
--

DROP TABLE IF EXISTS `reset_password_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reset_password_request` (
  `id` int NOT NULL AUTO_INCREMENT,
  `selector` varchar(20) NOT NULL,
  `hashed_token` varchar(100) NOT NULL,
  `requested_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7CE748AA76ED395` (`user_id`),
  CONSTRAINT `FK_7CE748AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `theme`
--

DROP TABLE IF EXISTS `theme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `theme` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `utilisateur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) NOT NULL,
  `nom` varchar(20) DEFAULT NULL,
  `prenom` varchar(20) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `actif` tinyint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-23 20:58:58
