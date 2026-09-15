
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

/*!40000 ALTER TABLE `allergene` DISABLE KEYS */;
INSERT INTO `allergene` VALUES (5,'Gluten'),(6,'Lait'),(7,'Œufs'),(8,'Fruits à coque');
/*!40000 ALTER TABLE `allergene` ENABLE KEYS */;

/*!40000 ALTER TABLE `avis` DISABLE KEYS */;
/*!40000 ALTER TABLE `avis` ENABLE KEYS */;

/*!40000 ALTER TABLE `commande` DISABLE KEYS */;
/*!40000 ALTER TABLE `commande` ENABLE KEYS */;

/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20260915140711','2026-09-15 14:19:51',1693);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;

/*!40000 ALTER TABLE `historique_statut_commande` DISABLE KEYS */;
/*!40000 ALTER TABLE `historique_statut_commande` ENABLE KEYS */;

/*!40000 ALTER TABLE `horaire` DISABLE KEYS */;
/*!40000 ALTER TABLE `horaire` ENABLE KEYS */;

/*!40000 ALTER TABLE `image_menu` DISABLE KEYS */;
/*!40000 ALTER TABLE `image_menu` ENABLE KEYS */;

/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,'Menu Gourmand','Un menu gourmand et traditionnel.',2,45.00,'Commande au minimum 48 heures à l’avance.',10,5,4),(2,'Menu Végétarien','Un menu sans viande à base de légumes de saison.',2,35.00,'Commande au minimum 48 heures à l’avance.',8,5,5);
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;

/*!40000 ALTER TABLE `menu_plat` DISABLE KEYS */;
INSERT INTO `menu_plat` VALUES (1,1),(1,2),(1,4),(2,1),(2,3),(2,5);
/*!40000 ALTER TABLE `menu_plat` ENABLE KEYS */;

/*!40000 ALTER TABLE `plat` DISABLE KEYS */;
INSERT INTO `plat` VALUES (1,'Salade gourmande','Salade fraîche aux légumes de saison.','entree'),(2,'Saumon rôti','Saumon rôti accompagné de légumes.','plat'),(3,'Gratin de légumes','Gratin de légumes de saison.','plat'),(4,'Fondant au chocolat','Fondant au chocolat maison.','dessert'),(5,'Salade de fruits','Fruits frais de saison.','dessert');
/*!40000 ALTER TABLE `plat` ENABLE KEYS */;

/*!40000 ALTER TABLE `plat_allergene` DISABLE KEYS */;
INSERT INTO `plat_allergene` VALUES (3,6),(4,5),(4,6),(4,7);
/*!40000 ALTER TABLE `plat_allergene` ENABLE KEYS */;

/*!40000 ALTER TABLE `regime` DISABLE KEYS */;
INSERT INTO `regime` VALUES (4,'Classique'),(5,'Végétarien'),(6,'Vegan');
/*!40000 ALTER TABLE `regime` ENABLE KEYS */;

/*!40000 ALTER TABLE `theme` DISABLE KEYS */;
INSERT INTO `theme` VALUES (5,'Classique'),(6,'Noël'),(7,'Pâques'),(8,'Événement');
/*!40000 ALTER TABLE `theme` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

