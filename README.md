# 🏢 Reservation-Salles

Application web PHP de réservation de salles. Ce projet a deux objectifs :

1. fournir une application fonctionnelle de réservation de salles ;
2. servir de projet pédagogique complet pour apprendre à concevoir, coder, tester, conteneuriser et déployer une application PHP moderne.

> **But de ce README :** ne pas seulement expliquer comment lancer l'application. Il doit permettre à un développeur qui ne connaît pas encore le projet de comprendre **pourquoi chaque dossier existe, comment les fichiers communiquent, comment une réservation circule dans l'application et comment reconstruire le projet depuis zéro**.

---

# 1. 🎯 Fonctionnalités

L'application permet de :

- afficher les salles actives ;
- afficher les réservations ;
- ouvrir le formulaire de réservation ;
- sélectionner une salle ;
- saisir un responsable, un email et un motif ;
- définir une date de début et une date de fin ;
- vérifier les données avant enregistrement ;
- vérifier qu'une salle existe ;
- vérifier qu'une salle est active ;
- empêcher les réservations qui se chevauchent ;
- enregistrer une réservation en base MySQL ;
- annuler une réservation ;
- protéger les formulaires avec un token CSRF ;
- utiliser des migrations pour créer la base ;
- initialiser des salles de démonstration ;
- fonctionner localement avec Docker ;
- publier automatiquement une image Docker avec GitHub Actions ;
- déployer l'application sur Render ;
- utiliser une base MySQL distante sur Aiven ;
- administrer la base avec phpMyAdmin.

---

# 2. 🧠 Vue d'ensemble : comment fonctionne le projet ?

Le chemin principal d'une réservation est :

```text
Navigateur
    │
    │ POST /reservations
    ▼
public/index.php
    │
    │ cherche la route
    ▼
routes/web.php
    │
    │ ReservationController::store()
    ▼
ReservationController
    │
    ├── vérifie le CSRF
    ├── récupère les données POST
    ├── crée un ReservationDTO
    │
    ▼
ReservationService
    │
    ├── validation
    ├── vérification de la salle
    ├── vérification du chevauchement
    │
    ▼
ReservationRepository
    │
    ▼
Reservation (Model Eloquent)
    │
    ▼
MySQL / Aiven
```

Pour afficher le résultat :

```text
MySQL
  │
  ▼
Repository / Model
  │
  ▼
Controller
  │
  ▼
Template PHP
  │
  ▼
Navigateur
```

## Pourquoi cette séparation ?

Parce qu'on ne veut pas mettre toute l'application dans un seul fichier.

- Le **Controller** s'occupe de HTTP et orchestre.
- Le **Service** contient les règles métier.
- Le **Validator** vérifie les données.
- Le **Repository** communique avec la base.
- Le **Model** représente les données persistées.
- Le **DTO** transporte les données d'une couche à l'autre.
- Le **View/Template** affiche les données.
- Le **Security/CsrfToken** gère la protection CSRF.

C'est la raison pour laquelle le projet contient plusieurs fichiers : **chaque responsabilité est séparée**.

---

# 3. 🧱 Architecture complète

```text
                              NAVIGATEUR
                                  │
                                  ▼
                         public/index.php
                       Front Controller HTTP
                                  │
                                  ▼
                           routes/web.php
                                  │
                                  ▼
                            Controller
                         /              \
                        /                \
                      DTO              Service
                                        │
                         ┌──────────────┼──────────────┐
                         ▼              ▼              ▼
                    Validator      Repository       Security
                                        │
                                        ▼
                                      Model
                                        │
                                        ▼
                                   MySQL/Aiven

Controller ─────────────────────────────────────► Template/View
```

Cette architecture est inspirée de MVC, mais elle va plus loin qu'un MVC minimal : elle ajoute une couche Service, Repository, DTO, Validation et Security.

---

# 4. 📂 Structure du projet expliquée

```text
Reservation-Salles/
│
├── .github/
│   └── workflows/
│       └── docker-publish.yml
│
├── config/
│   ├── container.php
│   └── database.php
│
├── database/
│   ├── migrations/
│   │   ├── 001_create_salles.sql
│   │   └── 002_create_reservations.sql
│   ├── migrate.php
│   ├── seed.php
│   └── start.php
│
├── public/
│   ├── index.php
│   └── assets/
│       └── style.css
│
├── routes/
│   └── web.php
│
├── src/
│   ├── Application.php
│   ├── Controller/
│   ├── DTO/
│   ├── Model/
│   ├── Repository/
│   ├── Security/
│   ├── Service/
│   └── Validation/
│
├── templates/
│   ├── error/
│   ├── layout/
│   ├── reservation/
│   └── salle/
│
├── tests/
│   ├── Integration/
│   └── Unit/
│
├── .env.example
├── .gitignore
├── .dockerignore
├── CHANGELOG.md
├── composer.json
├── composer.lock
├── Dockerfile
├── docker-compose.yml
├── docker-compose.prod.yml
└── phpunit.xml
```

## Rôle des dossiers

| Dossier/fichier | Rôle |
|---|---|
| `public/` | Point d'entrée HTTP public |
| `routes/` | Définition des URLs et méthodes HTTP |
| `src/Controller/` | Orchestration des requêtes HTTP |
| `src/Service/` | Logique métier / cas d'utilisation |
| `src/Repository/` | Accès aux données |
| `src/Model/` | Modèles Eloquent |
| `src/DTO/` | Transport structuré des données |
| `src/Validation/` | Validation métier des entrées |
| `src/Security/` | Protection CSRF |
| `config/` | Configuration et injection de dépendances |
| `database/migrations/` | Création/versionnement du schéma SQL |
| `database/seed.php` | Données initiales |
| `database/start.php` | Initialisation puis démarrage du serveur PHP |
| `templates/` | HTML/PHP affiché à l'utilisateur |
| `tests/` | Tests automatisés |
| `.github/workflows/` | Automatisation CI/CD |
| `Dockerfile` | Construction de l'image Docker |
| `docker-compose.yml` | Environnement local PHP + MySQL |
| `docker-compose.prod.yml` | Description de l'environnement Docker de production |
| `composer.json` | Dépendances PHP et autoloading |

> **Important :** la base actuelle utilise les migrations dans `database/migrations/`. Les anciens fichiers `schema.sql`, `seed.sql` et `database/Dockerfile` ne font plus partie de l'architecture actuelle.

---

# 5. 📦 Dépendances Composer

Le projet utilise Composer.

Dépendances principales :

```json
"php-di/php-di": "^7.0"
"vlucas/phpdotenv": "^5.7"
"illuminate/database": "^12.0"
"nikic/fast-route": "^1.3"
"respect/validation": "^2.4"
```

Tests :

```json
"phpunit/phpunit": "^12.0"
```

Autoloading :

```json
"App\\": "src/"
```

Cela signifie qu'une classe :

```php
namespace App\Service;
```

est recherchée dans :

```text
src/Service/
```

Le projet utilise donc l'autoloading PSR-4 de Composer.

Installer les dépendances :

```bash
composer install
```

---

# 6. 🚪 `public/index.php` : le point d'entrée

`public/index.php` est le **Front Controller**.

Le navigateur ne doit pas appeler directement chaque contrôleur. Toutes les requêtes passent par ce point d'entrée.

Son rôle est notamment de :

1. charger Composer ;
2. charger les variables d'environnement ;
3. démarrer la session ;
4. configurer des protections HTTP ;
5. créer le conteneur DI ;
6. charger les routes ;
7. déterminer la méthode HTTP et l'URI ;
8. demander à FastRoute quelle route correspond ;
9. récupérer le contrôleur depuis le conteneur ;
10. appeler la méthode correspondante.

Exemple :

```text
GET /
```

arrive dans :

```text
public/index.php
      ↓
routes/web.php
      ↓
SalleController::index()
```

Pourquoi un seul point d'entrée ?

Parce que cela permet de centraliser :

- l'autoloading ;
- la session ;
- la sécurité HTTP ;
- la configuration ;
- le routage ;
- l'injection de dépendances.

---

# 7. 🛣️ `routes/web.php` : les routes

Les routes actuelles sont :

```text
GET  /                         → SalleController::index
GET  /reservations/create     → ReservationController::create
POST /reservations            → ReservationController::store
POST /reservations/{id}/annuler → ReservationController::cancel
```

## Pourquoi GET et POST ?

- `GET` sert principalement à demander/afficher une ressource.
- `POST` sert ici à envoyer une action qui modifie les données.

Exemple :

```text
GET /reservations/create
```

affiche le formulaire.

Puis :

```text
POST /reservations
```

envoie le formulaire pour créer la réservation.

Enfin :

```text
POST /reservations/12/annuler
```

demande l'annulation de la réservation 12.

---

# 8. 🎮 Les Controllers

Un Controller reçoit une requête HTTP et orchestre le traitement.

Il ne doit pas contenir toute la logique métier.

## `ReservationController`

Il possède notamment :

```php
create()
store()
cancel()
```

### `create()`

Son rôle :

1. récupérer les salles actives ;
2. éventuellement récupérer une salle sélectionnée ;
3. récupérer/générer le token CSRF ;
4. charger le template du formulaire.

### `store()`

Son rôle :

1. récupérer le token CSRF ;
2. vérifier le token ;
3. récupérer les données POST ;
4. transformer les données en `ReservationDTO` ;
5. appeler `ReservationService` ;
6. rediriger vers `/` si tout fonctionne ;
7. afficher une erreur HTTP 422 en cas de problème de validation.

Il ne décide pas lui-même si la salle est disponible. Cette responsabilité appartient au Service/Repository.

### `cancel()`

Son rôle est similaire :

```text
POST
 ↓
CSRF
 ↓
validation de l'identifiant
 ↓
AnnulerReservationService
 ↓
redirection
```

## Pourquoi ne pas mettre le SQL dans le Controller ?

Parce que le Controller deviendrait responsable de plusieurs choses à la fois : HTTP + métier + base de données.

Cela violerait le principe de séparation des responsabilités et rendrait le code plus difficile à tester et maintenir.

---

# 9. 📦 DTO : `ReservationDTO`

Le DTO signifie **Data Transfer Object**.

Le fichier :

```text
src/DTO/ReservationDTO.php
```

regroupe les données nécessaires à la création d'une réservation :

```text
salleId
responsable
email
motif
dateDebut
dateFin
```

Le Controller reçoit des données HTTP sous forme de chaînes et de tableaux `$_POST`.

Il transforme ces données en objet :

```php
$dto = new ReservationDTO(...);
```

Puis le Service reçoit :

```php
createReservation(ReservationDTO $dto)
```

## Pourquoi utiliser un DTO ?

Sans DTO, on pourrait envoyer plusieurs paramètres partout :

```text
createReservation($salleId, $responsable, $email, $motif, ...)
```

Avec le DTO, le cas d'utilisation reçoit un objet clairement défini.

Le DTO ne contient pas la logique métier. Il transporte les données.

---

# 10. 🧠 Service Layer : `ReservationService`

Le Service contient le **cas d'utilisation métier**.

La méthode principale est :

```php
createReservation(ReservationDTO $dto)
```

Elle suit cet ordre :

```text
1. Valider les données
        ↓
2. Chercher la salle
        ↓
3. Vérifier qu'elle existe
        ↓
4. Vérifier qu'elle est active
        ↓
5. Vérifier les conflits
        ↓
6. Créer la réservation
```

C'est ici qu'on trouve les décisions métier importantes.

Exemple :

```php
if (!$salle->active) {
    throw new InvalidArgumentException(...);
}
```

et :

```php
if ($this->reservationRepository->hasConflict(...)) {
    throw new InvalidArgumentException(...);
}
```

## Pourquoi un Service ?

Parce que la réservation est un **cas d'utilisation**.

Le Controller ne doit pas connaître toutes les règles nécessaires pour réserver une salle.

Le Service centralise ces règles.

---

# 11. ✅ Validation : `ReservationValidator`

Le Validator vérifie que les données respectent les règles prévues.

Règles actuelles :

- responsable obligatoire ;
- responsable maximum 100 caractères ;
- email valide et maximum 255 caractères ;
- motif entre 5 et 255 caractères ;
- début dans le futur ;
- début avant la fin ;
- durée minimale : 30 minutes ;
- durée maximale : 4 heures.

Exemple :

```text
dateDebut = 14:00
 dateFin  = 14:10
```

La durée est de 10 minutes → refus.

Pourquoi séparer Validator et Service ?

Parce que :

- le Validator vérifie la validité des données ;
- le Service orchestre le cas d'utilisation.

On peut résumer ainsi :

```text
Validator = Est-ce que les données sont valides ?
Service   = Est-ce que l'opération peut être réalisée ?
```

---

# 12. 🗃️ Repository Pattern

Les repositories sont responsables de l'accès aux données.

Exemple :

```text
src/Repository/ReservationRepository.php
```

Méthodes importantes :

```php
create(array $data)
findById(int $id)
cancel(Reservation $reservation)
hasConflict(...)
```

## `create()`

Crée une réservation en base avec Eloquent.

## `findById()`

Recherche une réservation par identifiant.

## `cancel()`

Modifie le statut :

```text
confirmée → annulée
```

## `hasConflict()`

C'est une méthode métier liée à la disponibilité des données persistées.

Elle vérifie si une réservation confirmée existe déjà sur la même salle et une période qui chevauche la nouvelle période.

La condition centrale est :

```text
date_debut_existante < date_fin_nouvelle
ET
 date_fin_existante > date_debut_nouvelle
```

Pourquoi mettre l'accès SQL dans un Repository ?

Pour que le Service n'ait pas besoin de connaître les détails de la base.

---

# 13. 🔌 Interfaces et inversion de dépendance

Le projet possède notamment :

```text
ReservationRepositoryInterface
SalleRepositoryInterface
```

Le Service dépend d'une abstraction :

```php
private ReservationRepositoryInterface $reservationRepository
```

et non directement d'une implémentation précise.

Le conteneur DI sait ensuite que :

```text
ReservationRepositoryInterface
          ↓
ReservationRepository
```

## Pourquoi une interface ?

Une interface est un **contrat**.

Elle dit en substance :

> toute classe qui implémente cette interface doit fournir les méthodes prévues.

Avantages :

- faible couplage ;
- remplacement plus facile d'une implémentation ;
- tests plus faciles avec des doubles/mocks ;
- respect du principe de Dependency Inversion.

---

# 14. 💉 Injection de dépendances et PHP-DI

Le projet utilise PHP-DI.

Exemple conceptuel :

```php
public function __construct(
    private ReservationService $reservationService
) {}
```

Le Controller ne fait pas :

```php
new ReservationService(...)
```

Il déclare simplement ce dont il a besoin.

Le conteneur s'occupe de construire les dépendances.

Dans :

```text
config/container.php
```

on indique notamment :

```text
SalleRepositoryInterface → SalleRepository
ReservationRepositoryInterface → ReservationRepository
```

## Pourquoi faire cela ?

Pour éviter que chaque classe connaisse la manière exacte de construire toutes ses dépendances.

C'est ce qu'on appelle l'**injection de dépendances**.

---

# 15. 🧱 Models et Eloquent

Les modèles sont dans :

```text
src/Model/
```

Ils représentent les données de l'application.

Le projet utilise **Illuminate Database / Eloquent**.

Eloquent est un ORM : **Object-Relational Mapper**.

Cela permet de manipuler les lignes de base avec des objets PHP.

Exemple :

```php
Reservation::query()->find($id);
```

ou :

```php
Reservation::query()->create($data);
```

Au lieu d'écrire directement tout le SQL dans chaque classe.

---

# 16. 🔐 Protection CSRF

Le fichier :

```text
src/Security/CsrfToken.php
```

gère le token CSRF.

CSRF signifie **Cross-Site Request Forgery**.

Principe :

```text
Session
  │
  └── token secret
          │
          ▼
      formulaire
          │
          └── csrf_token
          │
          ▼
       POST
          │
          ▼
     vérification
```

Le token est généré avec :

```php
random_bytes(32)
```

Puis comparé avec :

```php
hash_equals(...)
```

Si le token est absent ou incorrect, la requête est refusée.

Pourquoi ?

Pour empêcher qu'un autre site tente de faire exécuter une action avec la session de l'utilisateur.

---

# 17. 🗄️ Base de données

Le projet utilise MySQL.

Schéma simplifié :

```text
salles
-------
id PK
nom
batiment
capacite
type
active
created_at
updated_at

        1
        │
        │
        │ N
        ▼
reservations
------------
id PK
salle_id FK
responsable
email
motif
date_debut
date_fin
statut
created_at
updated_at
```

Une salle peut avoir plusieurs réservations.

Une réservation appartient à une salle.

La clé étrangère :

```text
reservations.salle_id
        ↓
    salles.id
```

avec :

```sql
ON DELETE RESTRICT
ON UPDATE CASCADE
```

---

# 18. 🧱 Migrations

Les migrations sont dans :

```text
database/migrations/
```

Actuellement :

```text
001_create_salles.sql
002_create_reservations.sql
```

## Migration 001

Crée la table `salles`.

Elle contient notamment :

```text
id
nom
batiment
capacite
type
active
```

## Migration 002

Crée la table `reservations`.

Elle contient notamment :

```text
id
salle_id
responsable
email
motif
date_debut
date_fin
statut
```

## Pourquoi les migrations ?

Une migration permet de versionner la structure de la base.

Au lieu de dire :

> "Crée manuellement ces tables dans phpMyAdmin."

on peut dire :

> "Le code du projet contient les instructions permettant de construire la base."

Cela rend l'installation reproductible.

---

# 19. ▶️ `database/migrate.php`

Ce script :

1. charge Composer ;
2. charge `.env` ;
3. initialise la connexion ;
4. crée la table `migrations` si elle n'existe pas ;
5. récupère les fichiers `.sql` ;
6. les trie ;
7. vérifie lesquels ont déjà été exécutés ;
8. exécute les nouvelles migrations ;
9. enregistre leur nom dans `migrations`.

Exemple :

```text
[RUN ] 001_create_salles.sql
[RUN ] 002_create_reservations.sql
Migrations terminées.
```

Si elles ont déjà été exécutées :

```text
[SKIP] 001_create_salles.sql
[SKIP] 002_create_reservations.sql
```

C'est le principe d'un système de migration simple.

---

# 20. 🌱 Seed

`database/seed.php` insère les données initiales de démonstration.

La variable :

```env
RUN_SEED=true
```

permet de l'activer au démarrage.

Le déploiement actuel initialise 5 salles.

Le seed utilise Eloquent et `updateOrCreate` afin d'éviter de créer inutilement les mêmes salles à chaque démarrage.

---

# 21. ▶️ `database/start.php`

C'est le script de démarrage du conteneur.

Ordre :

```text
database/start.php
       │
       ├── migrate.php
       │
       ├── seed.php si RUN_SEED=true
       │
       └── php -S 0.0.0.0:$PORT -t public
```

Pourquoi ne pas simplement démarrer PHP directement ?

Parce qu'en production il faut d'abord s'assurer que la base possède le bon schéma et éventuellement les données initiales.

Le port est lu depuis :

```env
PORT
```

Ce comportement est important pour Render, qui fournit son propre port d'écoute.

---

# 22. ⚙️ Variables d'environnement

Exemple local :

```env
APP_ENV=development
APP_DEBUG=true
RUN_SEED=true

DB_DRIVER=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=reservation_salles
DB_USERNAME=reservation_user
DB_PASSWORD=reservation_password
MYSQL_ROOT_PASSWORD=root_password
```

En production, les vraies valeurs sont configurées dans Render.

Ne jamais mettre un vrai secret dans Git.

---

# 23. 🐳 Docker : comprendre le Dockerfile

Le `Dockerfile` construit l'image de l'application.

Étapes principales :

```text
php:8.3-cli
      ↓
installation extensions PHP
      ↓
installation Composer
      ↓
composer install
      ↓
copie du projet
      ↓
permissions
      ↓
USER www-data
      ↓
CMD php database/start.php
```

## Pourquoi `php:8.3-cli` ?

Parce que l'application utilise le serveur PHP intégré pour ce projet.

## Pourquoi `pdo_mysql` ?

Pour permettre à PHP de communiquer avec MySQL.

## Pourquoi Composer ?

Pour installer les bibliothèques PHP du projet.

## Pourquoi `USER www-data` ?

Pour éviter d'exécuter l'application en root dans le conteneur.

## Pourquoi `CMD ["php", "database/start.php"]` ?

C'est la commande exécutée au démarrage du conteneur.

---

# 24. 🐳 Docker Compose local

`docker-compose.yml` lance deux services :

```text
Docker Compose
│
├── app
│   └── PHP
│
└── db
    └── MySQL 8.4
```

Le réseau Docker permet à l'application d'utiliser :

```env
DB_HOST=db
```

et non `localhost`.

Pourquoi ?

Parce que dans le conteneur `app`, `localhost` désigne le conteneur lui-même.

`db` désigne le service MySQL du même réseau Docker.

Le port :

```text
8000:8000
```

signifie :

```text
port 8000 de la machine → port 8000 du conteneur
```

MySQL est exposé localement avec :

```text
3307:3306
```

---

# 25. 🚀 Lancer le projet localement

## Avec Docker

```bash
git clone https://github.com/mkcdigitallab/Reservation-Salles.git
cd Reservation-Salles

docker compose up -d --build
```

Vérifier :

```bash
docker compose ps
```

Voir les logs :

```bash
docker compose logs -f app
```

Application :

```text
http://localhost:8000
```

Arrêter :

```bash
docker compose down
```

Supprimer aussi les données MySQL locales :

```bash
docker compose down -v
```

> `down -v` supprime le volume MySQL local. C'est donc une remise à zéro de la base locale.

---

# 26. 🧪 Tests

PHPUnit est installé comme dépendance de développement.

Configuration :

```text
phpunit.xml
    ↓
tests/Unit
```

Après installation des dépendances :

```bash
vendor/bin/phpunit
```

Les tests sont séparés des fichiers de production afin de vérifier le comportement sans mélanger les responsabilités.

---

# 27. 🔄 Git et GitHub

Le dépôt est :

```text
https://github.com/mkcdigitallab/Reservation-Salles
```

Cycle de travail conseillé :

```text
modifier le code
      ↓
tester localement
      ↓
git status
      ↓
git add .
      ↓
git commit -m "message"
      ↓
git push origin main
```

Exemple :

```bash
git status
git add .
git commit -m "feat: ajouter une fonctionnalité"
git push origin main
```

---

# 28. 🔄 GitHub Actions : CI/CD

Workflow :

```text
git push main
      ↓
GitHub Actions
      ↓
Checkout
      ↓
Docker Buildx
      ↓
Connexion Docker Hub
      ↓
Docker build
      ↓
Docker push
      ↓
Docker Hub
```

Le fichier est :

```text
.github/workflows/docker-publish.yml
```

Il publie :

```text
mkcdigitallab/reservation-salles:latest
mkcdigitallab/reservation-salles:1.1
```

Secrets utilisés :

```text
DOCKERHUB_USERNAME
DOCKERHUB_TOKEN
```

Le token est un secret GitHub et ne doit jamais être écrit dans le README.

> **Important :** le workflow actuel construit et publie l'image Docker. Le déploiement Render utilise ensuite l'image publiée ; le workflow n'est pas présenté comme une étape magique qui contient toute la configuration Render.

---

# 29. 📦 Docker Hub

Image principale :

```text
mkcdigitallab/reservation-salles:latest
```

Version taguée :

```text
mkcdigitallab/reservation-salles:1.1
```

Docker Hub joue le rôle de **registre d'images**.

Il stocke l'image construite par GitHub Actions afin qu'un environnement de production puisse la récupérer.

---

# 30. ☁️ Déploiement Render

L'application de production actuelle utilise Render.

Architecture :

```text
                    INTERNET
                       │
                       ▼
              Render Web Service
              reservation-salles
                       │
                       │ MySQL/TLS
                       ▼
                Aiven MySQL
```

Image utilisée par le service :

```text
docker.io/mkcdigitallab/reservation-salles:latest
```

URL actuelle :

```text
https://reservation-salles-latest.onrender.com
```

Variables principales :

```text
APP_ENV=production
APP_DEBUG=false
RUN_SEED=true
PORT=10000
DB_DRIVER=mysql
DB_HOST=reservation-salles-db-mkcdigitallab-118b.d.aivencloud.com
DB_PORT=28676
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD=<secret Aiven>
```

Le vrai mot de passe n'est volontairement pas présent ici.

---

# 31. 🗃️ Aiven MySQL

La base de production est hébergée sur Aiven.

Configuration :

```text
Host     : reservation-salles-db-mkcdigitallab-118b.d.aivencloud.com
Port     : 28676
Database : defaultdb
User     : avnadmin
SSL      : requis
```

Architecture :

```text
Render Application
       │
       │ MySQL + TLS
       ▼
Aiven MySQL
```

Pourquoi Aiven ?

Parce que l'application de production a besoin d'une base accessible depuis l'environnement cloud, indépendante du conteneur PHP.

La base ne doit pas être stockée dans le conteneur de l'application : le conteneur peut être recréé, alors que les données doivent rester persistantes.

---

# 32. 🧰 phpMyAdmin

phpMyAdmin est un **outil d'administration**, pas la base de données.

Architecture :

```text
Render Application ──────┐
                         │
                         ▼
                    Aiven MySQL
                         ▲
                         │
phpMyAdmin ──────────────┘
```

Service Render :

```text
phpmyadmin-reservation
```

Image :

```text
phpmyadmin/phpmyadmin:latest
```

URL :

```text
https://phpmyadmin-reservation.onrender.com
```

Configuration :

```text
PMA_HOST=reservation-salles-db-mkcdigitallab-118b.d.aivencloud.com
PMA_PORT=28676
PMA_SSL=1
```

Connexion :

```text
Serveur     : reservation-salles-db-mkcdigitallab-118b.d.aivencloud.com
Utilisateur : avnadmin
Mot de passe : mot de passe Aiven
Base        : defaultdb
```

Le mot de passe Aiven n'est pas écrit dans le dépôt.

---

# 33. 🔎 Diagnostic d'une réservation

Si l'utilisateur dit :

> "J'ai créé une réservation mais je ne vois rien."

ne pas modifier le code au hasard.

Suivre cette méthode :

## Étape 1 — vérifier le navigateur

Vérifier l'URL et le comportement du formulaire.

## Étape 2 — vérifier les logs Render

Chercher la requête :

```text
POST /reservations
```

Puis vérifier la redirection :

```text
302
```

ou une erreur :

```text
4xx / 5xx
```

## Étape 3 — vérifier la validation

Regarder `ReservationValidator`.

## Étape 4 — vérifier la salle

Dans phpMyAdmin :

```sql
SELECT * FROM salles ORDER BY id;
```

## Étape 5 — vérifier les réservations

```sql
SELECT *
FROM reservations
ORDER BY id DESC;
```

## Étape 6 — déterminer où se trouve le problème

```text
POST reçu ?
   │
   ├── NON → problème HTTP / route / formulaire
   │
   └── OUI
        │
        ▼
   validation OK ?
        │
        ├── NON → ReservationValidator
        │
        └── OUI
             │
             ▼
        salle valide ?
             │
             ▼
        conflit ?
             │
             ▼
        INSERT en base
             │
             ▼
        affichage
```

Cette méthode permet de localiser le problème au lieu de modifier plusieurs fichiers au hasard.

---

# 34. 🧩 Principes et patterns à connaître

## MVC / MVC inspiré

Sépare :

```text
Model      → données
View       → affichage
Controller → orchestration HTTP
```

Le projet ajoute des couches autour de cette idée.

## Front Controller

Une entrée HTTP centrale :

```text
public/index.php
```

## Service Layer

Centralise les cas d'utilisation métier.

## Repository Pattern

Centralise l'accès aux données.

## DTO

Transporte les données structurées.

## Dependency Injection

Les dépendances sont fournies par l'extérieur plutôt que créées directement dans les classes.

## Dependency Inversion

Les services dépendent d'interfaces plutôt que d'implémentations concrètes.

## Single Responsibility Principle

Une classe doit avoir une responsabilité principale claire.

## ORM

Eloquent fait le lien entre objets PHP et données relationnelles.

## Migration

Versionne la structure de la base.

## CSRF protection

Protège les formulaires contre certaines requêtes forgées depuis un autre site.

---

# 35. 🗣️ Questions qu'un formateur peut poser

## Pourquoi `ReservationController` existe ?

Parce qu'il reçoit les requêtes HTTP liées aux réservations et orchestre le traitement.

## Pourquoi `ReservationService` existe ?

Parce que les règles métier de création d'une réservation ne doivent pas être dans le Controller.

## Pourquoi `ReservationRepository` existe ?

Pour isoler l'accès aux données et éviter de mélanger SQL/persistance et HTTP/métier.

## Pourquoi une interface ?

Pour définir un contrat et réduire le couplage entre le Service et l'implémentation du Repository.

## Pourquoi un DTO ?

Pour transporter de manière structurée les données nécessaires au cas d'utilisation.

## Pourquoi un Validator ?

Pour centraliser les règles de validation des entrées.

## Pourquoi Eloquent ?

Pour simplifier l'accès à MySQL avec un ORM et des modèles PHP.

## Pourquoi des migrations ?

Pour rendre la création et l'évolution de la base reproductibles.

## Pourquoi Docker ?

Pour avoir un environnement reproductible et isolé.

## Pourquoi Docker Hub ?

Pour stocker et distribuer l'image Docker construite.

## Pourquoi Aiven ?

Pour disposer d'une base MySQL distante et persistante en production.

## Pourquoi phpMyAdmin ?

Pour administrer et diagnostiquer visuellement la base sans modifier directement le code.

## Pourquoi `DB_HOST=db` en local ?

Parce que `db` est le nom du service MySQL dans le réseau Docker Compose.

## Pourquoi `DB_HOST` est différent en production ?

Parce que la base de production est externe au conteneur et hébergée sur Aiven.

## Pourquoi `PORT` est utilisé ?

Parce que Render fournit le port sur lequel le service doit écouter.

---

# 36. 🔁 Refaire le projet depuis zéro

Cette section est volontairement importante : elle décrit l'ordre logique de reconstruction.

## Étape 1 — créer le projet

```bash
mkdir Reservation-Salles
cd Reservation-Salles
git init
```

## Étape 2 — initialiser Composer

Créer `composer.json` avec les dépendances du projet puis :

```bash
composer install
```

## Étape 3 — créer l'autoload PSR-4

Configurer :

```text
App\ → src/
Tests\ → tests/
```

Puis :

```bash
composer dump-autoload
```

## Étape 4 — créer l'arborescence

Créer :

```text
config/
database/migrations/
public/
routes/
src/Controller/
src/DTO/
src/Model/
src/Repository/
src/Security/
src/Service/
src/Validation/
templates/
tests/
```

## Étape 5 — créer la base

Créer les migrations :

```text
001_create_salles.sql
002_create_reservations.sql
```

## Étape 6 — créer la configuration DB

Créer :

```text
config/database.php
```

Elle doit lire les variables :

```text
DB_DRIVER
DB_HOST
DB_PORT
DB_DATABASE
DB_USERNAME
DB_PASSWORD
```

puis configurer Eloquent.

## Étape 7 — créer les modèles

Créer notamment :

```text
Salle.php
Reservation.php
```

## Étape 8 — créer les repositories

Créer :

```text
SalleRepositoryInterface
SalleRepository
ReservationRepositoryInterface
ReservationRepository
```

## Étape 9 — créer le DTO

Créer :

```text
ReservationDTO.php
```

## Étape 10 — créer le Validator

Créer :

```text
ReservationValidator.php
```

## Étape 11 — créer les Services

Créer :

```text
ReservationService.php
AnnulerReservationService.php
```

## Étape 12 — créer la sécurité

Créer :

```text
CsrfToken.php
```

## Étape 13 — configurer PHP-DI

Créer :

```text
config/container.php
```

Déclarer les correspondances :

```text
Interface → Implementation
```

## Étape 14 — créer les Controllers

Créer :

```text
SalleController.php
ReservationController.php
```

## Étape 15 — créer les routes

Créer :

```text
routes/web.php
```

## Étape 16 — créer le Front Controller

Créer :

```text
public/index.php
```

Il charge le système et distribue les requêtes vers les Controllers.

## Étape 17 — créer les templates

Créer les vues nécessaires dans :

```text
templates/
```

## Étape 18 — créer le système de migration

Créer :

```text
database/migrate.php
```

## Étape 19 — créer le seed

Créer :

```text
database/seed.php
```

## Étape 20 — créer le démarrage

Créer :

```text
database/start.php
```

Ordre :

```text
migrations → seed → serveur PHP
```

## Étape 21 — tester sans Docker

Vérifier que PHP, Composer et MySQL fonctionnent.

## Étape 22 — ajouter Docker

Créer :

```text
Dockerfile
docker-compose.yml
```

## Étape 23 — tester avec Docker

```bash
docker compose up -d --build
```

## Étape 24 — ajouter les tests

```text
tests/
phpunit.xml
```

Puis :

```bash
vendor/bin/phpunit
```

## Étape 25 — créer le workflow CI/CD

Créer :

```text
.github/workflows/docker-publish.yml
```

## Étape 26 — publier l'image

GitHub Actions construit puis pousse :

```text
mkcdigitallab/reservation-salles:latest
```

## Étape 27 — créer la base Aiven

Créer la base MySQL distante et récupérer :

```text
host
port
database
username
password
SSL
```

## Étape 28 — déployer l'image sur Render

Configurer Render avec l'image Docker et les variables d'environnement.

## Étape 29 — vérifier les migrations

Les logs doivent montrer :

```text
[RUN ] 001_create_salles.sql
[RUN ] 002_create_reservations.sql
Migrations terminées.
```

## Étape 30 — ajouter phpMyAdmin

Déployer :

```text
phpmyadmin/phpmyadmin:latest
```

et le connecter à Aiven.

---

# 37. 🧭 Ordre conseillé pour apprendre le projet

Si tu dois apprendre ce projet au lieu de simplement le copier, étudie-le dans cet ordre :

```text
1. HTML / formulaire
       ↓
2. HTTP GET / POST
       ↓
3. routes/web.php
       ↓
4. public/index.php
       ↓
5. Controller
       ↓
6. DTO
       ↓
7. Validator
       ↓
8. Service
       ↓
9. Repository
       ↓
10. Model / Eloquent
       ↓
11. MySQL
       ↓
12. Interface
       ↓
13. Injection de dépendances
       ↓
14. CSRF
       ↓
15. migrations
       ↓
16. Docker
       ↓
17. GitHub Actions
       ↓
18. Docker Hub
       ↓
19. Render
       ↓
20. Aiven
       ↓
21. phpMyAdmin
```

Si tu comprends ce chemin, tu peux reconstruire progressivement le projet sans dépendre du README lui-même.

---

# 38. 🔄 Cycle complet du projet

```text
DÉVELOPPEMENT
     │
     ▼
PHP / HTML / SQL
     │
     ▼
TEST LOCAL
     │
     ▼
Docker Compose
     │
     ▼
Git commit
     │
     ▼
Git push main
     │
     ▼
GitHub Actions
     │
     ▼
Docker Build
     │
     ▼
Docker Hub
     │
     ▼
Render
     │
     ├──────────────► Application PHP
     │
     └──────────────► Aiven MySQL
                          ▲
                          │
                     phpMyAdmin
```

---

# 39. 🔐 Sécurité : règles à ne jamais oublier

Ne jamais commit :

- mot de passe Aiven ;
- token Docker Hub ;
- clés API ;
- `.env` contenant de vrais secrets ;
- clés privées.

Le dépôt doit utiliser des exemples comme :

```text
.env.example
```

et les vraies valeurs doivent être fournies par l'environnement local ou la plateforme de déploiement.

---

# 40. 🌐 Services actuels

| Élément | Rôle | Hébergement |
|---|---|---|
| Reservation-Salles | Application PHP | Render |
| MySQL | Base de production | Aiven |
| phpMyAdmin | Administration DB | Render |
| Image Docker | Distribution de l'application | Docker Hub |
| CI/CD | Build et publication | GitHub Actions |

---

# 41. 📚 Ce que ce projet permet de pratiquer

### PHP

- namespaces ;
- classes ;
- interfaces ;
- typage ;
- exceptions ;
- autoloading ;
- injection de dépendances.

### Architecture

- MVC ;
- Front Controller ;
- Service Layer ;
- Repository Pattern ;
- DTO ;
- séparation des responsabilités ;
- Dependency Inversion.

### Web

- HTTP ;
- GET / POST ;
- routing ;
- formulaires ;
- sessions ;
- redirections ;
- codes HTTP ;
- CSRF.

### Base de données

- MySQL ;
- clés primaires ;
- clés étrangères ;
- migrations ;
- contraintes ;
- Eloquent ;
- requêtes ;
- gestion des conflits de réservation.

### DevOps

- Docker ;
- Docker Compose ;
- Docker Hub ;
- Git ;
- GitHub Actions ;
- CI/CD ;
- Render ;
- Aiven ;
- variables d'environnement ;
- phpMyAdmin.

---

# 42. 🆘 Commandes utiles

```bash
# Installer les dépendances
composer install

# Régénérer l'autoload
composer dump-autoload

# Lancer les tests
vendor/bin/phpunit

# Construire et lancer Docker
 docker compose up -d --build

# Voir les conteneurs
 docker compose ps

# Voir les logs
 docker compose logs -f app

# Arrêter
 docker compose down

# Arrêter et supprimer les volumes
 docker compose down -v

# Vérifier Git
git status

# Ajouter les fichiers
git add .

# Commit
git commit -m "message"

# Envoyer sur GitHub
git push origin main
```

---

# 👨‍💻 Auteur

**MKC Digital Lab**

Projet développé dans un objectif d'apprentissage pratique et de montée en compétence sur le développement web PHP, l'architecture logicielle, les bases de données et le déploiement.

---

# 🔗 Liens

Dépôt :

```text
https://github.com/mkcdigitallab/Reservation-Salles
```

Application :

```text
https://reservation-salles-latest.onrender.com
```

phpMyAdmin :

```text
https://phpmyadmin-reservation.onrender.com
```

---

# ✅ État actuel

Le projet est une application PHP de réservation de salles déployée sur Render.

Elle utilise :

```text
PHP 8.3
Composer
PHP-DI
FastRoute
Eloquent
MySQL
Docker
GitHub Actions
Docker Hub
Render
Aiven
phpMyAdmin
```

L'objectif pédagogique reste de comprendre le projet suffisamment pour pouvoir **expliquer chaque couche, suivre une requête de bout en bout et reconstruire l'application étape par étape sans dépendre d'un copier-coller aveugle**.
