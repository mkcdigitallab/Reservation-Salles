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
- Protection CSRF et en-têtes HTTP de sécurité
- Tests unitaires avec PHPUnit

## Architecture

```text
config/          Configuration de l'application et du conteneur
database/        Migrations SQL et données initiales
public/          Point d'entrée HTTP et assets
routes/          Définition des routes
src/Controller/  Contrôleurs HTTP
src/DTO/         Objets de transfert de données
src/Model/       Modèles Eloquent
src/Repository/  Accès aux données
src/Service/     Règles métier
src/Validation/  Validation des données
src/Security/    Protection CSRF
templates/       Vues PHP
tests/           Tests automatisés
```

## Prérequis

- PHP 8.2 ou supérieur
- Composer
- Docker et Docker Compose pour l'environnement conteneurisé
- MySQL ou MariaDB pour une installation locale

## Installation locale

```bash
git clone https://github.com/mkcdigitallab/Reservation-Salles.git
cd Reservation-Salles
composer install
cp .env.example .env
```

Configurez ensuite les variables `DB_*` dans `.env`.

Créez la base de données puis exécutez les migrations SQL dans `database/migrations/`.

Initialisez les salles avec :

```bash
php database/seed.php
```

## Environnement Docker de développement

L'environnement de développement utilise `docker-compose.yml` et peut être construit localement :

```bash
docker compose up -d --build
docker compose ps
```

L'application est ensuite disponible sur `http://localhost:8000`.

## Environnement Docker de production

Les valeurs sensibles ne sont pas écrites dans `docker-compose.prod.yml` et ne doivent pas être commit dans Git. Le dépôt fournit uniquement un modèle : `.env.prod.example`.

Créer localement un fichier ignoré par Git :

```bash
cp .env.prod.example .env.prod.local
```

Puis remplacer les valeurs `CHANGE_ME...` par de vrais mots de passe.

Lancer la production avec cet environnement :

```bash
docker compose --env-file .env.prod.local -f docker-compose.prod.yml up -d
```

Vérifier les conteneurs :

```bash
docker compose --env-file .env.prod.local -f docker-compose.prod.yml ps
docker compose --env-file .env.prod.local -f docker-compose.prod.yml logs
```

> `.env.prod.local` est couvert par la règle `.env.*.local` du `.gitignore`. Les secrets restent donc hors du dépôt.

L'image applicative et l'image MySQL de production sont séparées dans `docker-compose.prod.yml`. L'application utilise une image PHP non privilégiée et la base de données utilise sa propre image.

## Lancer l'application sans Docker

```bash
php -S localhost:8000 -t public
```

Puis ouvrez `http://localhost:8000` dans votre navigateur.

## Tests

```bash
composer install
vendor/bin/phpunit
```

## Scripts de maintenance

```bash
./scripts/malang-kiya-ciss.sh test
./scripts/malang-kiya-ciss.sh lint
./scripts/malang-kiya-ciss.sh security
./scripts/malang-kiya-ciss.sh audit
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
