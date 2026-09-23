# Vite & Gourmand

Vite & Gourmand est une application web réalisée dans le cadre de l'ECF du titre professionnel Développeur Web et Web Mobile (DWWM).

Le projet représente le site d'un traiteur. Il permet aux visiteurs de consulter les menus et aux clients inscrits de passer une commande, suivre son évolution et laisser un avis.

Les employés disposent d'un espace permettant de gérer les menus, les plats, les commandes, les horaires et les avis.

L'administrateur possède en plus la gestion des employés et l'accès aux statistiques.

---

## Liens du projet

### Application en ligne

https://vite-et-gourmand-production-7905.up.railway.app

### Dépôt GitHub

https://github.com/HEA2025/vite-et-gourmand

### Gestion de projet Trello

https://trello.com/b/z1TJHSKX/vite-gourmand-ecf-dwwm

---

# Technologies utilisées et justification des choix

## PHP 8.4

J'ai utilisé PHP car c'est le langage back-end utilisé avec Symfony et étudié pendant ma formation.

Il permet de gérer toute la logique côté serveur : formulaires, utilisateurs, commandes, calculs, accès aux données et envoi des e-mails.

J'ai travaillé avec PHP 8.4 pour utiliser une version récente compatible avec la version de Symfony choisie.

---

## Symfony 7.4 LTS

J'ai choisi Symfony car il fournit une structure MVC claire et adaptée à une application complète comme Vite & Gourmand.

Le projet contient plusieurs fonctionnalités qui ont besoin d'être bien organisées : authentification, formulaires, rôles, base de données, commandes, e-mails et administration.

Symfony permet de séparer les différentes responsabilités avec les contrôleurs, les entités, les formulaires et les templates Twig.

C'est également le framework étudié dans ma formation, ce qui me permet de comprendre l'architecture utilisée et de pouvoir l'expliquer.

J'ai choisi la version 7.4 LTS afin de travailler avec une version stable et maintenue sur une longue durée.

---

## Doctrine ORM

J'ai utilisé Doctrine ORM pour faire le lien entre les objets PHP et la base MySQL.

Le projet contient beaucoup de relations entre les données : par exemple un utilisateur possède plusieurs commandes, un menu possède plusieurs plats et un plat peut contenir plusieurs allergènes.

Doctrine permet de représenter ces données avec des entités PHP et de gérer plus simplement les relations entre elles.

Il évite également d'avoir à écrire manuellement toutes les requêtes SQL pour les opérations courantes.

---

## MySQL

J'ai choisi MySQL pour stocker les données principales de l'application.

Les données du projet sont fortement liées entre elles : utilisateurs, menus, plats, commandes, avis, allergènes ou encore historiques de statut.

Une base de données relationnelle est donc adaptée à ce fonctionnement.

MySQL est utilisé pour toutes les données métier principales de Vite & Gourmand.

---

## MongoDB

MongoDB est utilisé en complément de MySQL pour répondre à l'exigence NoSQL du projet.

Je l'ai utilisé uniquement pour la partie statistiques de l'administrateur.

Les données principales restent dans MySQL et les informations nécessaires aux statistiques sont synchronisées vers MongoDB.

Cela me permet de garder une séparation simple :

- MySQL pour les données métier ;
- MongoDB pour les statistiques.

---

## Twig

J'ai choisi Twig car c'est le moteur de templates utilisé avec Symfony.

Il permet de séparer le traitement PHP de l'affichage HTML.

J'ai également utilisé un template principal `base.html.twig` afin d'éviter de répéter la barre de navigation, le footer et les éléments communs sur toutes les pages.

---

## Bootstrap 5

J'ai utilisé Bootstrap pour construire plus rapidement une interface responsive.

Le site devait fonctionner sur ordinateur et sur mobile.

Bootstrap fournit notamment une grille responsive, une barre de navigation mobile, des formulaires et différents composants déjà adaptés aux différentes tailles d'écran.

Cela m'a permis de garder une interface simple sans devoir recréer tous les composants CSS moi-même.

---

## JavaScript

J'ai utilisé JavaScript uniquement pour les parties qui ont besoin d'être dynamiques dans le navigateur.

Il est notamment utilisé pour les filtres des menus et pour afficher certains calculs avant validation d'une commande.

Le calcul définitif des commandes est quand même refait côté PHP afin de ne pas faire confiance uniquement aux données envoyées par le navigateur.

---

## Git et GitHub

J'ai utilisé Git pour garder l'historique du développement et enregistrer les différentes étapes du projet.

GitHub est utilisé comme dépôt distant et permet également de fournir le code source demandé pour l'ECF.

L'organisation du projet repose principalement sur :

- `main` pour les versions stables ;
- `develop` pour intégrer les développements ;
- des branches `feature/...` simples pour certaines fonctionnalités.

Cette organisation permet de séparer le développement de la version stable.

---

## Visual Studio Code

J'ai utilisé Visual Studio Code comme éditeur principal.

Je l'ai choisi car il me permet de travailler sur PHP, Twig, HTML, CSS et JavaScript dans le même outil.

Son terminal intégré m'a également permis d'utiliser directement les commandes Symfony, Composer et Git sans changer d'application.

---

## Railway

J'ai utilisé Railway pour mettre l'application en ligne.

Cette solution m'a permis de déployer le projet Symfony à partir du dépôt GitHub et de configurer les variables d'environnement nécessaires à l'application.

La version déployée utilise également une base MySQL en ligne.

Railway m'a permis d'avoir une solution de déploiement assez simple à mettre en place dans le temps disponible pour l'ECF.

---

## Symfony Mailer et Brevo

Les e-mails de l'application sont gérés avec le composant Symfony Mailer.

Il est utilisé par exemple pour :

- l'inscription ;
- la création d'un employé ;
- la confirmation d'une commande ;
- le mot de passe oublié ;
- le retour du matériel ;
- l'invitation à laisser un avis.

Pour l'application déployée, j'ai utilisé le transport API de Brevo afin d'envoyer réellement les e-mails depuis Railway.

---

# Fonctionnalités principales

## Visiteur

Un visiteur peut :

- consulter la page d'accueil ;
- consulter les menus ;
- utiliser les filtres des menus ;
- consulter le détail d'un menu ;
- consulter les horaires ;
- utiliser le formulaire de contact ;
- créer un compte ;
- se connecter.

## Client connecté

Un client peut :

- modifier ses informations personnelles ;
- commander un menu ;
- consulter ses commandes ;
- modifier une commande avant son acceptation ;
- annuler une commande avant son acceptation ;
- suivre l'état de ses commandes ;
- déposer un avis lorsqu'une commande est terminée ;
- utiliser la procédure de mot de passe oublié.

## Employé

Un employé peut :

- gérer les menus ;
- gérer les plats ;
- gérer les allergènes ;
- gérer les images des menus ;
- consulter les commandes clients ;
- modifier le statut des commandes ;
- gérer les avis ;
- modifier les horaires.

## Administrateur

L'administrateur possède les droits de l'employé et peut également :

- créer un compte employé ;
- désactiver un compte employé ;
- consulter les statistiques ;
- comparer le nombre de commandes par menu ;
- consulter le chiffre d'affaires ;
- filtrer le chiffre d'affaires selon une période et un menu.

---

# Mise en place de l'environnement de travail

Avant de commencer le développement, j'ai installé et vérifié les principaux outils nécessaires au projet.

Mon environnement local comprend :

- Windows ;
- Visual Studio Code ;
- PHP 8.4 ;
- Composer ;
- Symfony CLI ;
- MySQL 8 ;
- MongoDB ;
- MongoDB Compass ;
- Git ;
- GitHub ;
- un navigateur web récent.

Composer est utilisé pour installer et gérer les dépendances PHP du projet.

Symfony CLI me permet notamment de lancer l'application localement et d'utiliser facilement les commandes Symfony.

MySQL est installé comme service Windows et MongoDB est également installé localement pour pouvoir tester la partie NoSQL avant le déploiement.

---

# Installation du projet en local

## 1. Prérequis

Avant de lancer le projet, installer :

- PHP 8.4 ;
- Composer ;
- Symfony CLI ;
- MySQL 8 ;
- MongoDB ;
- Git.

---

## 2. Récupérer le projet

```bash
git clone https://github.com/HEA2025/vite-et-gourmand.git
cd vite-et-gourmand