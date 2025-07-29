# 📌 Exercice de Formation : Projet Symfony – Gestion de sessions de formation

## 📝 Description du Projet

Ce projet est le **deuxième projet Symfony** réalisé dans le cadre de ma formation de développeuse web.  
L’objectif est de développer une application permettant de **gérer des sessions de formation** avec une logique métier plus poussée.

Chaque session de formation est liée à :
- un **nombre de places limité**
- une **période définie** (date de début et de fin)
- un **programme personnalisé**, composé de plusieurs **modules**, chacun appartenant à une **catégorie**.

Le nombre de **places restantes** est automatiquement mis à jour en fonction du nombre de personnes inscrites.

## 🚀 Fonctionnalités

* Affichage des **sessions de formation** avec leurs dates, modules, et places disponibles
* Ajout / modification / suppression de :
  - Sessions
  - Modules
  - Catégories
  - Inscriptions
* Visualisation dynamique du **nombre de places restantes**
* Organisation des modules par **catégories** (ex: Bureautique, Dev Web…)

## 🛠️ Langages et Technologies Utilisés

* **Symfony**
   * Framework backend structuré, puissant et extensible
* **Twig**
   * Pour les templates front-end
* **Doctrine ORM**
   * Pour la gestion des entités et des relations complexes (Sessions ↔ Modules ↔ Inscriptions)
* **Bootstrap**
   * Pour un affichage responsive et une interface utilisateur claire
* **HTML / CSS / PHP**
   * Technologies de base du projet
* **MySQL**
   * Système de gestion de base de données relationnelle

## 📌 Points Importants

* Implémentation de **relations multiples** (ManyToMany, OneToMany, etc.) entre les entités
* Gestion d’un **nombre dynamique de places** en fonction des inscriptions
* Modularité du programme de formation par **catégories de modules**
* Adaptabilité des **durées de modules** par session
* Calcul automatique de la **durée totale d’une session**
* Organisation de l’interface pour une gestion fluide et intuitive

## 🎯 Objectifs Pédagogiques

* Approfondir la maîtrise de Symfony avec des relations de données complexes, des requêtes DQL
* Implémenter une logique métier personnalisée (gestion de places, dates, durées)
* Manipuler des entités interconnectées et dynamiques
* Développer une interface administrateur efficace
* Renforcer les compétences en architecture MVC, validation de données, et navigation dynamique

## 🛠️ Améliorations Possibles

* Ajouter une **recherche filtrée** (par date, catégorie, places restantes…)
* Intégrer un **calendrier visuel** des sessions (ex: FullCalendar)
* Ajouter une gestion des **formateurs** assignés à une session
* Ajouter un système d’authentification pour une gestion multi-utilisateurs (admin, formateur, etc.) 
* Calcul automatique du **nombre de jours restant** pour chaque session, en fonction des modules inclus

## 📥 Installation

    git clone https://github.com/Oarlina/session  
    cd session  
    composer install  
    symfony server:start  

Configurer le fichier `.env.local` avec vos identifiants de base de données.  
Exécuter ensuite les migrations :

    php bin/console doctrine:database:create  
    php bin/console doctrine:migrations:migrate  

## 🎬 Vidéo

*À venir* – une démonstration de l’interface et des fonctionnalités sera bientôt ajoutée.

## 👤 Auteur

Ce projet a été réalisé par **Rachel Marquant**, dans le cadre de sa formation de développeuse web.  
Vous pouvez me contacter pour toute remarque, suggestion ou collaboration.
