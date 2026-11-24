-- =====================================================================
--  VITE & GOURMAND - Jeu de données de démonstration
--  À exécuter APRÈS create_database.sql
--  Auteur : Hedi EL AOUINI
--
--  Règles de calcul utilisées pour les commandes :
--    - prix_menu  = (prix_minimum / nombre_minimum_personnes) x nombre_personnes
--    - réduction  = 10 % du prix_menu si nombre_personnes >= minimum + 5
--    - livraison  = 0 € à Bordeaux, sinon 5 € + 0,59 € par kilomètre
--    - prix_total = prix_menu - réduction + frais_livraison
--
--  Comptes de test (mot de passe hashé en bcrypt, jamais en clair) :
--    jose@vite-et-gourmand.fr       -> administrateur
--    employe@vite-et-gourmand.fr    -> employé (actif)
--    jean@vite-et-gourmand.fr       -> client
--    Steve@gmail.com                -> client
-- =====================================================================

USE vite_et_gourmand;
SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- 1. Utilisateurs
-- ---------------------------------------------------------------------
INSERT INTO utilisateur (id, email, roles, password, nom, prenom, telephone, adresse, actif) VALUES
(1, 'jose@vite-et-gourmand.fr',         '["ROLE_ADMINISTRATEUR"]',
    '$2y$13$q/2/sYf7XTf/ISkxTOshF.NNHq1RUj/qhB5R9ByrZobqhELlHl2eS',
    'José', 'José', '0600000000', 'Adresse à compléter', 1),
(2, 'jean@vite-et-gourmand.fr',         '["ROLE_USER"]',
    '$2y$13$7B/8L5.hkn1eTn5W8hhFw.g3ZzEvE.6o/ABegDeqA8bS0JMofcWuq',
    'Dupont', 'Jean', '0612345678', '10 rue Sainte-Catherine, 33000 Bordeaux', 1),
(3, 'employe.test@vite-et-gourmand.fr', '["ROLE_EMPLOYE"]',
    '$2y$13$tO3RFa/5VKzMo1Mvl35roufM7Cnvg1arPW8AZHwnaEdNAaIenG06i',
    NULL, NULL, NULL, NULL, 0),   -- compte désactivé par l'administrateur
(4, 'employe@vite-et-gourmand.fr',      '["ROLE_EMPLOYE"]',
    '$2y$13$cd9qvwtnUXbosgPyd4U68O4jeTO.Ayn/YZfNCqD34akx9Y6WfUr9.',
    NULL, NULL, NULL, NULL, 1),
(5, 'Steve@gmail.com',                  '["ROLE_USER"]',
    '$2y$13$gmLDVQ.m7jcBHQgJzTejCetj5whgewWqkQI8LH.skHBYML1.n8hc6',
    'Migo', 'Steve', '0612131415', '1 rue Louis de Jabrun, 33000 Bordeaux', 1);


-- ---------------------------------------------------------------------
-- 2. Tables de référence
-- ---------------------------------------------------------------------
INSERT INTO theme (id, nom) VALUES
(1, 'Classique'),
(2, 'Noël'),
(3, 'Pâques'),
(4, 'Événement');

INSERT INTO regime (id, nom) VALUES
(1, 'Classique'),
(2, 'Végétarien'),
(3, 'Vegan');

INSERT INTO allergene (id, nom) VALUES
(1, 'Gluten'),
(2, 'Lait'),
(3, 'Œufs'),
(4, 'Fruits à coque');

INSERT INTO horaire (id, jour, heure_ouverture, heure_fermeture) VALUES
(1, 'Lundi',    '09:00:00', '23:00:00'),
(2, 'Mardi',    '09:30:00', '22:00:00'),
(3, 'Mercredi', '10:00:00', '22:30:00'),
(4, 'Jeudi',    '10:30:00', '23:00:00'),
(5, 'Vendredi', '09:15:00', '22:40:00'),
(6, 'Samedi',   '10:00:00', '23:59:00'),
(7, 'Dimanche', '09:00:00', '16:00:00');


-- ---------------------------------------------------------------------
-- 3. Plats et allergènes
-- ---------------------------------------------------------------------
INSERT INTO plat (id, nom, description, type) VALUES
(1, 'Salade gourmande',       'Salade fraîche aux légumes de saison.',                                        'entrée'),
(2, 'Saumon rôti',            'Saumon rôti accompagné de légumes.',                                           'plat'),
(3, 'Gratin de légumes',      'Gratin de légumes de saison.',                                                 'plat'),
(4, 'Fondant au chocolat',    'Fondant au chocolat maison.',                                                  'dessert'),
(5, 'Salade de fruits',       'Fruits frais de saison.',                                                      'dessert'),
(6, 'Curry de pois chiches',  'Curry de pois chiches, lentilles corail et épinards au lait de coco, riz basmati.', 'plat'),
(7, 'Salade méditerranéenne', 'Tomates, concombre, olives et herbes aromatiques.',                            'entrée'),
(8, 'Soupe de fruits rouges', 'Fruits rouges infusés à la menthe et sorbet framboise maison.',                'dessert');

-- Gratin : lait / Fondant : gluten, lait, œufs
INSERT INTO plat_allergene (plat_id, allergene_id) VALUES
(3, 2),
(4, 1),
(4, 2),
(4, 3);


-- ---------------------------------------------------------------------
-- 4. Menus, composition et images
-- ---------------------------------------------------------------------
INSERT INTO menu (id, titre, description, nombre_minimum_personnes, prix_minimum, conditions, stock, theme_id, regime_id) VALUES
(1, 'Menu Gourmand',      'Un menu gourmand et traditionnel.',
    2, 45.00, 'Commande au minimum 48 heures à l''avance.', 10, 1, 1),
(2, 'Menu Végétarien',    'Un menu sans viande à base de légumes de saison.',
    2, 35.00, 'Commande au minimum 48 heures à l''avance.',  8, 1, 2),
(3, 'Menu Vegan',         'Un repas composé exclusivement d''aliments d''origine végétale.',
    2, 35.00, 'Commande au minimum 48 heures à l''avance.',  4, 1, 3),
(4, 'Menu Méditerranéen', 'Un menu frais et gourmand aux saveurs méditerranéennes.',
    4, 80.00, 'Commande au minimum 12 heures à l''avance.',  4, 1, 1);

-- Chaque menu = 1 entrée + 1 plat + 1 dessert
INSERT INTO menu_plat (menu_id, plat_id) VALUES
(1, 1), (1, 2), (1, 4),   -- Gourmand      : salade gourmande, saumon, fondant
(2, 1), (2, 3), (2, 5),   -- Végétarien    : salade gourmande, gratin, salade de fruits
(3, 1), (3, 6), (3, 8),   -- Vegan         : salade gourmande, curry, soupe de fruits rouges
(4, 7), (4, 2), (4, 8);   -- Méditerranéen : salade méditerranéenne, saumon, soupe de fruits rouges

INSERT INTO image_menu (id, nom_fichier, menu_id) VALUES
(1, 'menu-gourmand.png',   1),
(2, 'plat-gourmand.png',   1),
(3, 'dessert-chocolat.png', 1),
(4, 'menu-vegetarien.png', 2);


-- ---------------------------------------------------------------------
-- 5. Commandes (un exemple pour chaque cas métier)
-- ---------------------------------------------------------------------
INSERT INTO commande (id, adresse_prestation, date_prestation, heure_livraison, nombre_personnes,
                      prix_menu, reduction, frais_livraison, prix_total, distance_livraison,
                      materiel_prete, moyen_contact_employe, motif_intervention_employe,
                      date_contact_employe, date_creation, menu_id, utilisateur_id) VALUES
-- 1. Cas simple, Bordeaux, commande terminée puis notée
(1, '10 rue Sainte-Catherine, 33000 Bordeaux', '2026-09-27', '12:30:00', 2,
    45.00,  0.00,  0.00,  45.00,  0, 0, NULL, NULL, NULL, '2026-09-19 13:18:37', 1, 2),
-- 2. Hors Bordeaux (10 km) + réduction : 7 pers. >= 2 + 5
--    157,50 - 15,75 + (5 + 0,59 x 10) = 152,65
(2, '1 avenue de Paris, 33600 Pessac',          '2026-09-25', '12:30:00', 7,
    157.50, 15.75, 10.90, 152.65, 10, 0, NULL, NULL, NULL, '2026-09-19 12:55:58', 1, 2),
-- 3. Deuxième client, commande terminée puis notée
(3, '10 rue Sainte-Catherine, 33000 Bordeaux', '2026-09-25', '19:20:00', 2,
    45.00,  0.00,  0.00,  45.00,  0, 0, NULL, NULL, NULL, '2026-09-21 13:18:11', 1, 5),
-- 4. Matériel prêté : la commande attend le retour du matériel
(4, '10 rue Sainte-Catherine, 33000 Bordeaux', '2026-09-22', '11:30:00', 4,
    70.00,  0.00,  0.00,  70.00,  0, 1, NULL, NULL, NULL, '2026-09-20 19:18:04', 2, 2),
-- 5. Annulée par un employé après contact du client par e-mail
(5, '10 rue Sainte-Catherine, 33000 Bordeaux', '2026-09-22', '14:00:00', 3,
    52.50,  0.00,  0.00,  52.50,  0, 0, 'email', 'Erreur d''adresse', '2026-09-22 11:57:10',
    '2026-09-22 11:54:33', 2, 5),
-- 6. Nouvelle commande en attente, hors Bordeaux + réduction : 10 pers. >= 4 + 5
--    200,00 - 20,00 + 10,90 = 190,90
(6, '1 avenue de Paris, 33600 Pessac',          '2026-12-05', '20:00:00', 10,
    200.00, 20.00, 10.90, 190.90, 10, 0, NULL, NULL, NULL, '2026-11-20 18:49:30', 4, 2);


-- ---------------------------------------------------------------------
-- 6. Historique des statuts (suivi de commande)
-- ---------------------------------------------------------------------
INSERT INTO historique_statut_commande (statut, date_modification, commande_id) VALUES
-- Commande 1 : parcours complet
('en attente',            '2026-09-19 13:18:37', 1),
('acceptée',              '2026-09-19 13:38:42', 1),
('en préparation',        '2026-09-27 09:40:05', 1),
('en cours de livraison', '2026-09-27 11:41:44', 1),
('livrée',                '2026-09-27 12:25:20', 1),
('terminée',              '2026-09-27 12:30:27', 1),
-- Commande 2 : acceptée
('en attente',            '2026-09-19 12:55:58', 2),
('acceptée',              '2026-09-21 13:24:06', 2),
-- Commande 3 : parcours complet
('en attente',            '2026-09-21 13:18:11', 3),
('acceptée',              '2026-09-21 13:21:21', 3),
('en préparation',        '2026-09-25 16:00:00', 3),
('en cours de livraison', '2026-09-25 18:50:00', 3),
('livrée',                '2026-09-25 19:15:00', 3),
('terminée',              '2026-09-25 19:20:00', 3),
-- Commande 4 : matériel à rendre
('en attente',            '2026-09-20 19:18:04', 4),
('acceptée',              '2026-09-21 13:22:19', 4),
('en préparation',        '2026-09-22 09:00:00', 4),
('en cours de livraison', '2026-09-22 11:00:00', 4),
('livrée',                '2026-09-22 11:30:00', 4),
('en attente du retour de matériel', '2026-09-22 11:35:00', 4),
-- Commande 5 : annulée
('en attente',            '2026-09-22 11:54:33', 5),
('annulée',               '2026-09-22 11:57:10', 5),
-- Commande 6 : vient d'être passée
('en attente',            '2026-11-20 18:49:30', 6);


-- ---------------------------------------------------------------------
-- 7. Avis (uniquement sur des commandes terminées)
-- ---------------------------------------------------------------------
INSERT INTO avis (id, note, commentaire, statut, date_creation, commande_id) VALUES
(1, 5, 'Délicieux, livré à l''heure.', 'validé', '2026-09-27 15:41:37', 1),
(2, 5, 'Excellent, je recommande.',    'validé', '2026-09-25 21:32:37', 3);


-- ---------------------------------------------------------------------
-- 8. Requêtes de vérification
-- ---------------------------------------------------------------------

-- Menus avec leur thème et leur régime (page "Nos menus")
SELECT m.titre, t.nom AS theme, r.nom AS regime, m.prix_minimum, m.nombre_minimum_personnes
FROM menu m
JOIN theme  t ON t.id = m.theme_id
JOIN regime r ON r.id = m.regime_id
ORDER BY m.prix_minimum;

-- Allergènes de chaque menu
SELECT m.titre, GROUP_CONCAT(DISTINCT a.nom ORDER BY a.nom SEPARATOR ', ') AS allergenes
FROM menu m
JOIN menu_plat      mp ON mp.menu_id = m.id
LEFT JOIN plat_allergene pa ON pa.plat_id = mp.plat_id
LEFT JOIN allergene      a  ON a.id = pa.allergene_id
GROUP BY m.id, m.titre;

-- Contrôle du calcul : doit renvoyer 0 ligne
SELECT id, prix_total, prix_menu - reduction + frais_livraison AS total_recalcule
FROM commande
WHERE prix_total <> prix_menu - reduction + frais_livraison;

-- Chiffre d'affaires par menu (commandes non annulées)
SELECT m.titre, COUNT(c.id) AS nb_commandes, SUM(c.prix_total) AS chiffre_affaires
FROM commande c
JOIN menu m ON m.id = c.menu_id
WHERE c.id NOT IN (SELECT commande_id FROM historique_statut_commande WHERE statut = 'annulée')
GROUP BY m.id, m.titre;
