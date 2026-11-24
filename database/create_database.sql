-- =====================================================================
--  VITE & GOURMAND - Script de création de la base de données
--  SGBD    : MySQL 8
--  Auteur  : Hedi EL AOUINI
--  Rôle    : crée la base, les tables, les clés et les contraintes.
--  Ordre   : les tables "référentiel" d'abord, puis les tables qui
--            dépendent d'elles (clés étrangères).
-- =====================================================================
SET NAMES utf8mb4;
DROP DATABASE IF EXISTS vite_et_gourmand;
CREATE DATABASE vite_et_gourmand
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE vite_et_gourmand;


-- ---------------------------------------------------------------------
-- 1. TABLES DE RÉFÉRENCE (aucune clé étrangère)
-- ---------------------------------------------------------------------

-- Utilisateurs : clients, employés et administrateur.
-- Le rôle est stocké en JSON (format attendu par Symfony Security).
-- Le mot de passe n'est jamais stocké en clair : uniquement son hash bcrypt.
CREATE TABLE utilisateur (
    id          INT           NOT NULL AUTO_INCREMENT,
    email       VARCHAR(180)  NOT NULL,
    roles       JSON          NOT NULL,
    password    VARCHAR(255)  NOT NULL,
    nom         VARCHAR(20)   NULL,
    prenom      VARCHAR(20)   NULL,
    telephone   VARCHAR(20)   NULL,
    adresse     VARCHAR(255)  NULL,
    actif       TINYINT(1)    NOT NULL DEFAULT 1,
    CONSTRAINT pk_utilisateur       PRIMARY KEY (id),
    CONSTRAINT uq_utilisateur_email UNIQUE (email)
) ENGINE = InnoDB;

-- Thèmes des menus (Classique, Noël, Pâques, Événement).
CREATE TABLE theme (
    id   INT          NOT NULL AUTO_INCREMENT,
    nom  VARCHAR(50)  NOT NULL,
    CONSTRAINT pk_theme     PRIMARY KEY (id),
    CONSTRAINT uq_theme_nom UNIQUE (nom)
) ENGINE = InnoDB;

-- Régimes alimentaires (Classique, Végétarien, Vegan).
CREATE TABLE regime (
    id   INT          NOT NULL AUTO_INCREMENT,
    nom  VARCHAR(50)  NOT NULL,
    CONSTRAINT pk_regime     PRIMARY KEY (id),
    CONSTRAINT uq_regime_nom UNIQUE (nom)
) ENGINE = InnoDB;

-- Allergènes pouvant être présents dans un plat.
CREATE TABLE allergene (
    id   INT          NOT NULL AUTO_INCREMENT,
    nom  VARCHAR(50)  NOT NULL,
    CONSTRAINT pk_allergene     PRIMARY KEY (id),
    CONSTRAINT uq_allergene_nom UNIQUE (nom)
) ENGINE = InnoDB;

-- Plats : type = 'entrée', 'plat' ou 'dessert'.
CREATE TABLE plat (
    id           INT           NOT NULL AUTO_INCREMENT,
    nom          VARCHAR(100)  NOT NULL,
    description  LONGTEXT      NOT NULL,
    type         VARCHAR(20)   NOT NULL,
    CONSTRAINT pk_plat PRIMARY KEY (id)
) ENGINE = InnoDB;

-- Horaires d'ouverture affichés dans le footer.
CREATE TABLE horaire (
    id               INT          NOT NULL AUTO_INCREMENT,
    jour             VARCHAR(20)  NOT NULL,
    heure_ouverture  TIME         NOT NULL,
    heure_fermeture  TIME         NOT NULL,
    CONSTRAINT pk_horaire      PRIMARY KEY (id),
    CONSTRAINT uq_horaire_jour UNIQUE (jour)
) ENGINE = InnoDB;


-- ---------------------------------------------------------------------
-- 2. MENUS ET COMPOSITION
-- ---------------------------------------------------------------------

-- Un menu appartient à UN thème et à UN régime (relation 1,n).
-- prix_minimum = prix pour nombre_minimum_personnes.
CREATE TABLE menu (
    id                        INT            NOT NULL AUTO_INCREMENT,
    titre                     VARCHAR(100)   NOT NULL,
    description               LONGTEXT       NOT NULL,
    nombre_minimum_personnes  INT            NOT NULL,
    prix_minimum              DECIMAL(10,2)  NOT NULL,
    conditions                LONGTEXT       NOT NULL,
    stock                     INT            NOT NULL DEFAULT 0,
    theme_id                  INT            NOT NULL,
    regime_id                 INT            NOT NULL,
    CONSTRAINT pk_menu            PRIMARY KEY (id),
    CONSTRAINT fk_menu_theme      FOREIGN KEY (theme_id)  REFERENCES theme (id),
    CONSTRAINT fk_menu_regime     FOREIGN KEY (regime_id) REFERENCES regime (id),
    CONSTRAINT ck_menu_min_pers   CHECK (nombre_minimum_personnes >= 1),
    CONSTRAINT ck_menu_prix       CHECK (prix_minimum >= 0),
    CONSTRAINT ck_menu_stock      CHECK (stock >= 0)
) ENGINE = InnoDB;

-- Photos d'un menu (un menu peut avoir plusieurs images).
CREATE TABLE image_menu (
    id           INT           NOT NULL AUTO_INCREMENT,
    nom_fichier  VARCHAR(255)  NOT NULL,
    menu_id      INT           NOT NULL,
    CONSTRAINT pk_image_menu      PRIMARY KEY (id),
    CONSTRAINT fk_image_menu_menu FOREIGN KEY (menu_id) REFERENCES menu (id)
        ON DELETE CASCADE
) ENGINE = InnoDB;

-- Table d'association menu <-> plat (relation n,n).
-- Si un menu est supprimé, ses liens avec les plats disparaissent aussi.
CREATE TABLE menu_plat (
    menu_id  INT  NOT NULL,
    plat_id  INT  NOT NULL,
    CONSTRAINT pk_menu_plat      PRIMARY KEY (menu_id, plat_id),
    CONSTRAINT fk_menu_plat_menu FOREIGN KEY (menu_id) REFERENCES menu (id) ON DELETE CASCADE,
    CONSTRAINT fk_menu_plat_plat FOREIGN KEY (plat_id) REFERENCES plat (id) ON DELETE CASCADE
) ENGINE = InnoDB;

-- Table d'association plat <-> allergène (relation n,n).
CREATE TABLE plat_allergene (
    plat_id       INT  NOT NULL,
    allergene_id  INT  NOT NULL,
    CONSTRAINT pk_plat_allergene           PRIMARY KEY (plat_id, allergene_id),
    CONSTRAINT fk_plat_allergene_plat      FOREIGN KEY (plat_id)      REFERENCES plat (id)      ON DELETE CASCADE,
    CONSTRAINT fk_plat_allergene_allergene FOREIGN KEY (allergene_id) REFERENCES allergene (id) ON DELETE CASCADE
) ENGINE = InnoDB;


-- ---------------------------------------------------------------------
-- 3. COMMANDES, SUIVI ET AVIS
-- ---------------------------------------------------------------------

-- Une commande est passée par UN utilisateur pour UN menu.
-- Les prix sont recopiés au moment de la commande : si le prix du menu
-- change plus tard, la commande garde le prix payé.
-- prix_total = prix_menu - reduction + frais_livraison
CREATE TABLE commande (
    id                          INT            NOT NULL AUTO_INCREMENT,
    adresse_prestation          VARCHAR(255)   NOT NULL,
    date_prestation             DATE           NOT NULL,
    heure_livraison             TIME           NOT NULL,
    nombre_personnes            INT            NOT NULL,
    prix_menu                   DECIMAL(10,2)  NOT NULL,
    reduction                   DECIMAL(10,2)  NOT NULL DEFAULT 0,
    frais_livraison             DECIMAL(10,2)  NOT NULL DEFAULT 0,
    prix_total                  DECIMAL(10,2)  NOT NULL,
    distance_livraison          DOUBLE         NULL,
    materiel_prete              TINYINT(1)     NOT NULL DEFAULT 0,
    moyen_contact_employe       VARCHAR(20)    NULL,
    motif_intervention_employe  LONGTEXT       NULL,
    date_contact_employe        DATETIME       NULL,
    date_creation               DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    menu_id                     INT            NOT NULL,
    utilisateur_id              INT            NOT NULL,
    CONSTRAINT pk_commande             PRIMARY KEY (id),
    CONSTRAINT fk_commande_menu        FOREIGN KEY (menu_id)        REFERENCES menu (id),
    CONSTRAINT fk_commande_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id),
    CONSTRAINT ck_commande_nb_pers     CHECK (nombre_personnes >= 1),
    CONSTRAINT ck_commande_montants    CHECK (prix_menu >= 0 AND reduction >= 0
                                              AND frais_livraison >= 0 AND prix_total >= 0)
) ENGINE = InnoDB;

-- Historique des statuts : une ligne par changement de statut,
-- ce qui permet au client de suivre sa commande étape par étape.
CREATE TABLE historique_statut_commande (
    id                 INT          NOT NULL AUTO_INCREMENT,
    statut             VARCHAR(50)  NOT NULL,
    date_modification  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    commande_id        INT          NOT NULL,
    CONSTRAINT pk_historique          PRIMARY KEY (id),
    CONSTRAINT fk_historique_commande FOREIGN KEY (commande_id) REFERENCES commande (id)
        ON DELETE CASCADE
) ENGINE = InnoDB;

-- Avis client : UN seul avis par commande (contrainte UNIQUE),
-- publié sur l'accueil seulement après validation par un employé.
CREATE TABLE avis (
    id             INT          NOT NULL AUTO_INCREMENT,
    note           INT          NOT NULL,
    commentaire    LONGTEXT     NOT NULL,
    statut         VARCHAR(20)  NOT NULL DEFAULT 'en attente',
    date_creation  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    commande_id    INT          NOT NULL,
    CONSTRAINT pk_avis          PRIMARY KEY (id),
    CONSTRAINT uq_avis_commande UNIQUE (commande_id),
    CONSTRAINT fk_avis_commande FOREIGN KEY (commande_id) REFERENCES commande (id)
        ON DELETE CASCADE,
    CONSTRAINT ck_avis_note     CHECK (note BETWEEN 1 AND 5)
) ENGINE = InnoDB;

-- Demandes de réinitialisation de mot de passe.
-- Le jeton est stocké hashé et expire au bout d'une heure.
CREATE TABLE reset_password_request (
    id            INT           NOT NULL AUTO_INCREMENT,
    selector      VARCHAR(20)   NOT NULL,
    hashed_token  VARCHAR(100)  NOT NULL,
    requested_at  DATETIME      NOT NULL,
    expires_at    DATETIME      NOT NULL,
    user_id       INT           NOT NULL,
    CONSTRAINT pk_reset_password      PRIMARY KEY (id),
    CONSTRAINT fk_reset_password_user FOREIGN KEY (user_id) REFERENCES utilisateur (id)
        ON DELETE CASCADE
) ENGINE = InnoDB;


-- ---------------------------------------------------------------------
-- 4. INDEX (accélèrent les recherches les plus fréquentes)
-- ---------------------------------------------------------------------

-- Filtres de la page "Nos menus" : par thème, par régime, par prix.
CREATE INDEX idx_menu_prix ON menu (prix_minimum);

-- Espace employé : liste des commandes triées par date de prestation.
CREATE INDEX idx_commande_date_prestation ON commande (date_prestation);
