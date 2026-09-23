# Vite & Gourmand

Vite & Gourmand est une application web réalisée dans le cadre de l'ECF du titre professionnel Développeur Web et Web Mobile (DWWM).

Le projet représente le site d'un traiteur qui permet aux clients de consulter les menus, passer une commande et suivre son état.  
Les employés peuvent gérer les menus, les plats, les commandes, les horaires et les avis.  
L'administrateur possède en plus la gestion des employés et l'accès aux statistiques.

## Application en ligne

Application déployée :

https://vite-et-gourmand-production-7905.up.railway.app

Dépôt GitHub :

https://github.com/HEA2025/vite-et-gourmand

Gestion de projet Trello :

https://trello.com/b/z1TJHSKX/vite-gourmand-ecf-dwwm

## Technologies utilisées

### Back-end

- PHP 8.4
- Symfony 7.4 LTS
- Doctrine ORM
- Symfony Security
- Symfony Forms
- Symfony Mailer
- Twig

### Bases de données

- MySQL 8.0 pour les données principales de l'application
- MongoDB pour les statistiques des commandes

### Front-end

- HTML
- CSS
- Bootstrap 5
- JavaScript
- Twig

### Outils

- Composer
- Git
- GitHub
- VS Code
- Symfony CLI
- MySQL
- MongoDB
- Railway

## Fonctionnalités principales

### Visiteur

- consulter la page d'accueil ;
- consulter les menus ;
- filtrer les menus ;
- consulter le détail d'un menu ;
- consulter les horaires ;
- utiliser le formulaire de contact ;
- créer un compte ;
- se connecter.

### Client connecté

- modifier ses informations personnelles ;
- commander un menu ;
- consulter ses commandes ;
- modifier ou annuler une commande tant qu'elle n'a pas été acceptée ;
- suivre le statut d'une commande ;
- déposer un avis lorsque la commande est terminée ;
- utiliser la procédure de mot de passe oublié.

### Employé

- gérer les menus ;
- gérer les plats et leurs allergènes ;
- gérer la galerie d'images des menus ;
- gérer les horaires ;
- consulter les commandes clients ;
- modifier le statut des commandes ;
- gérer les avis.

### Administrateur

L'administrateur possède les droits d'un employé et peut également :

- créer un compte employé ;
- désactiver un compte employé ;
- consulter les statistiques ;
- consulter le chiffre d'affaires selon une période.

## Installation locale

### 1. Prérequis

Avant de lancer le projet, installer :

- PHP 8.4 ;
- Composer ;
- Symfony CLI ;
- MySQL 8 ;
- MongoDB ;
- Git.

### 2. Récupérer le projet

```bash
git clone https://github.com/HEA2025/vite-et-gourmand.git
cd vite-et-gourmand