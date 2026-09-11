# Reservation-Salles

Application web de réservation de salles, développée en PHP avec une architecture en couches.

## Fonctionnalités

- Affichage des salles actives
- Sélection d'une salle pour une réservation
- Création d'une réservation
- Validation des données saisies
- Vérification de l'existence et de l'état de la salle
- Détection des conflits de réservation
- Annulation des réservations confirmées
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
- Docker et Docker Compose
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

Pour repartir avec une base propre :

```bash
docker compose down -v
docker compose up -d --build
docker compose ps
```

Le premier démarrage initialise automatiquement le schéma et cinq salles depuis `database/schema.sql` et `database/seed.sql`.

L'application est ensuite disponible sur `http://localhost:8000` et MySQL sur le port `3307` de la machine hôte.

Si le volume `mysql_data` existe déjà, les scripts d'initialisation MySQL ne sont pas rejoués. Pour réinitialiser complètement la base, utilisez `docker compose down -v` avant de relancer Compose.

## Environnement Docker de production

Les valeurs sensibles ne sont pas écrites dans `docker-compose.prod.yml`. Le dépôt fournit uniquement `.env.prod.example` comme modèle de variables, sans secret réel.

**Aucun fichier `.env.prod` contenant des secrets ne doit être créé ou conservé dans le dépôt.** Pour le déploiement, les valeurs suivantes doivent être fournies par l'environnement de déploiement :

- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `MYSQL_ROOT_PASSWORD`

Pour le déploiement avec GitHub Actions, ces valeurs seront stockées dans l'environnement GitHub `production` et injectées dans le workflow au moment du déploiement. Elles ne sont donc pas nécessaires sur le poste de développement.

Le fichier de production peut ensuite être lancé avec les variables d'environnement fournies par le mécanisme de secrets du serveur :

```bash
docker compose -f docker-compose.prod.yml up -d
docker compose -f docker-compose.prod.yml ps
docker compose -f docker-compose.prod.yml logs
```

L'image applicative et l'image MySQL de production sont séparées dans `docker-compose.prod.yml`. L'application utilise une image PHP non privilégiée et la base de données utilise sa propre image.

## Lancer l'application sans Docker

```bash
php -S localhost:8000 -t public
```

Puis ouvrez `http://localhost:8000`.

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
./scripts/malang-kiya-ciss.sh tables
./scripts/malang-kiya-ciss.sh terrains
./scripts/malang-kiya-ciss.sh reservations
```

Le script accepte également directement une requête SQL :

```bash
./scripts/malang-kiya-ciss.sh "SELECT * FROM salles;"
```

## Règles métier principales

- La réservation doit commencer dans le futur.
- La date de début doit être antérieure à la date de fin.
- Une réservation doit durer au moins 30 minutes et au maximum 4 heures.
- La salle doit exister et être active.
- Deux réservations confirmées ne peuvent pas se chevaucher.
- Deux réservations adjacentes sont autorisées : 10h–12h puis 12h–14h.
- Une réservation annulée ne bloque pas la salle.
- Une réservation déjà annulée ne peut pas être annulée une seconde fois.

## Déploiement

Avant une release, il faut :

1. Valider les tests PHPUnit et la CI.
2. Construire et publier les images Docker de production.
3. Configurer l'environnement GitHub `production` avec les secrets nécessaires.
4. Déployer les images sur le serveur cible avec `docker-compose.prod.yml`.
5. Vérifier l'application et la base de données sur l'environnement déployé.
6. Effectuer ensuite la Pull Request finale vers `main`.
7. Créer la release correspondant au prochain numéro de version.

Le tag `v1.0.0` existe déjà sur l'historique du dépôt. Les correctifs de cette branche constituent une évolution postérieure à ce tag et ne doivent pas réécrire le tag existant.

Le serveur cible et son mécanisme d'accès seront configurés au moment de la phase de déploiement ; aucun secret de production n'est requis sur le poste local.
