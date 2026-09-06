# Reservation-Salles

Application web de réservation de salles, développée en PHP avec une architecture en couches.

## Fonctionnalités

- Affichage des salles actives
- Sélection d'une salle pour une réservation
- Création d'une réservation
- Validation des données saisies
- Vérification de l'existence et de l'état de la salle
- Détection des conflits de réservation
- Gestion des réservations annulées
- Routage HTTP avec FastRoute
- Injection de dépendances avec PHP-DI
- Accès aux données avec Eloquent
- Configuration par variables d'environnement
- Tests unitaires avec PHPUnit

## Architecture

```text
config/          Configuration de l'application et du conteneur
 database/       Migrations SQL et données initiales
public/          Point d'entrée HTTP et assets
routes/          Définition des routes
src/Controller/  Contrôleurs HTTP
src/DTO/         Objets de transfert de données
src/Model/       Modèles Eloquent
src/Repository/  Accès aux données
src/Service/     Règles métier
src/Validation/  Validation des données
templates/       Vues PHP
tests/           Tests automatisés
```

## Prérequis

- PHP 8.2 ou supérieur
- Composer
- MySQL ou MariaDB

## Installation

```bash
git clone https://github.com/mkcdigitallab/Reservation-Salles.git
cd Reservation-Salles
composer update
cp .env.example .env
```

Configurez ensuite les variables `DB_*` dans `.env`.

Créez la base de données puis exécutez les migrations SQL dans `database/migrations/`.

Initialisez les salles avec :

```bash
php database/seed.php
```

## Lancer l'application

```bash
php -S localhost:8000 -t public
```

Puis ouvrez `http://localhost:8000` dans votre navigateur.

## Tests

```bash
vendor/bin/phpunit
```

## Règles métier principales

- La réservation doit commencer dans le futur.
- La date de début doit être antérieure à la date de fin.
- Une réservation ne peut pas dépasser 4 heures.
- La salle doit exister et être active.
- Deux réservations confirmées ne peuvent pas se chevaucher.
- Deux réservations adjacentes sont autorisées : 10h–12h puis 12h–14h.
- Une réservation annulée ne bloque pas la salle.

## Version

Version initiale : `1.0.0`.
