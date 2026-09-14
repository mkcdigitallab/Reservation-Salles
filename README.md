# 🏢 Reservation-Salles

Application web PHP de réservation de salles, développée avec une architecture PHP moderne et modulaire.

Le projet permet notamment de :

- afficher les salles actives ;
- ouvrir un formulaire de réservation ;
- valider les données saisies ;
- vérifier qu'une salle existe et qu'elle est active ;
- empêcher les réservations qui se chevauchent ;
- enregistrer une réservation ;
- annuler une réservation ;
- protéger les formulaires contre les attaques CSRF ;
- séparer les responsabilités entre contrôleurs, services, repositories, modèles, validation et vues.

> **Objectif pédagogique :** ce projet sert aussi d'exercice complet pour apprendre à construire, conteneuriser, publier et déployer une application PHP avec une base de données distante.

---

# 📌 Technologies utilisées

- **PHP 8.3** avec typage strict
- **Composer** et autoloading PSR-4
- **PHP-DI** pour l'injection de dépendances
- **FastRoute** pour le routage HTTP
- **Illuminate Database / Eloquent** pour l'accès aux données
- **PHP dotenv** pour les variables d'environnement
- **Respect/Validation** pour la validation
- **PHPUnit** pour les tests
- **Docker / Docker Compose** pour la conteneurisation
- **GitHub Actions** pour l'intégration et la publication de l'image Docker
- **Docker Hub** pour stocker l'image de production
- **Render** pour héberger l'application web et phpMyAdmin
- **Aiven MySQL** pour la base de données MySQL distante
- **phpMyAdmin** pour administrer visuellement la base de données
- **HTML / CSS** pour les vues

---

# 🧱 Architecture de l'application

Le projet utilise une architecture PHP personnalisée inspirée de MVC, enrichie par plusieurs patterns :

```text
                         NAVIGATEUR
                              │
                              ▼
                    public/index.php
                    Point d'entrée unique
                              │
                              ▼
                         routes/web.php
                              │
                              ▼
                         Controller
                              │
                  ┌───────────┴───────────┐
                  ▼                       ▼
                 DTO                    Service
                                          │
                         ┌────────────────┼────────────────┐
                         ▼                ▼                ▼
                    Validator       Repository        Security
                                          │
                                          ▼
                                        Model
                                          │
                                          ▼
                                      MySQL/Aiven

Controller ───────────────────────────────────────► Template/View
```

L'idée fondamentale est qu'une classe ne doit pas tout faire. Le contrôleur reçoit la requête et orchestre le cas d'utilisation, le service porte la logique métier, le repository s'occupe de l'accès aux données et les modèles représentent les données persistées.

---

# 📂 Structure principale

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

> **Important :** la base de données est maintenant initialisée avec les migrations présentes dans `database/migrations/`. Les anciens fichiers `schema.sql`, `seed.sql` et `database/Dockerfile` ne font plus partie de l'architecture actuelle.

---

# 🗄️ Base de données et migrations

Le projet utilise une approche **migration-driven**.

Les migrations actuelles sont :

```text
 database/migrations/
 ├── 001_create_salles.sql
 └── 002_create_reservations.sql
```

Le fichier `database/migrate.php` :

1. charge Composer ;
2. charge les variables d'environnement ;
3. initialise la connexion Eloquent ;
4. crée la table `migrations` si nécessaire ;
5. recherche les fichiers SQL dans `database/migrations/` ;
6. exécute uniquement les migrations qui ne sont pas encore enregistrées ;
7. enregistre chaque migration exécutée.

Exemple de résultat :

```text
[RUN ] 001_create_salles.sql
[RUN ] 002_create_reservations.sql
Migrations terminées.
```

Lors d'un nouveau déploiement, les migrations sont donc exécutées automatiquement avant le démarrage de l'application.

---

# 🌱 Initialisation des salles

Le fichier `database/seed.php` initialise les salles de démonstration avec Eloquent.

Le démarrage utilise la variable :

```env
RUN_SEED=true
```

Lorsque cette variable est activée, les données initiales sont chargées automatiquement.

Le déploiement de production actuel initialise **5 salles**.

---

# 🔐 Variables d'environnement

Les secrets et paramètres de connexion ne doivent pas être commités dans Git.

Exemple local :

```env
APP_ENV=development
APP_DEBUG=true

DB_DRIVER=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=reservation_salles
DB_USERNAME=reservation_user
DB_PASSWORD=change_me
MYSQL_ROOT_PASSWORD=change_me
```

En production, les variables sont configurées directement dans l'environnement de déploiement.

> **Sécurité :** ne jamais mettre un vrai mot de passe Aiven, Docker Hub ou une autre clé secrète dans le dépôt GitHub ou dans ce README.

---

# 🐳 Docker

## Développement local

Le projet possède un `docker-compose.yml` qui lance :

```text
Docker Compose
│
├── app
│   └── PHP 8.3
│
└── db
    └── MySQL 8.4
```

L'application écoute localement sur :

```text
http://localhost:8000
```

La base MySQL de développement est accessible depuis l'hôte sur le port `3307`.

### Lancer le projet

```bash
git clone https://github.com/mkcdigitallab/Reservation-Salles.git
cd Reservation-Salles

docker compose up -d --build
```

Puis ouvrir :

```text
http://localhost:8000
```

### Arrêter

```bash
docker compose down
```

### Supprimer également les données MySQL locales

```bash
docker compose down -v
```

> `docker compose down -v` supprime le volume de la base locale. Les données stockées dans ce volume seront perdues.

---

# 🏭 Image Docker de production

L'image de production est construite à partir du `Dockerfile` principal.

Image publiée sur Docker Hub :

```text
mkcdigitallab/reservation-salles:latest
```

Une version taguée est également publiée :

```text
mkcdigitallab/reservation-salles:1.1
```

Le conteneur démarre avec :

```text
php database/start.php
```

`database/start.php` :

1. exécute les migrations ;
2. exécute le seed si `RUN_SEED=true` ;
3. récupère le port fourni par l'environnement ;
4. démarre le serveur PHP sur `0.0.0.0`.

Le port est dynamique afin d'être compatible avec les plateformes cloud comme Render :

```text
PORT=10000
```

---

# 🔄 CI/CD avec GitHub Actions

Le projet utilise GitHub Actions pour automatiser la publication de l'image Docker.

Le workflow se déclenche lorsqu'un commit est envoyé sur `main` ou manuellement.

Flux :

```text
git push
   │
   ▼
GitHub Actions
   │
   ├── Checkout
   ├── Docker Buildx
   ├── Connexion Docker Hub
   └── Build + Push
          │
          ▼
Docker Hub
```

Les secrets GitHub utilisés sont :

```text
DOCKERHUB_USERNAME
DOCKERHUB_TOKEN
```

Le token Docker Hub est utilisé pour authentifier GitHub Actions sans exposer le mot de passe du compte.

La publication réussie produit notamment :

```text
mkcdigitallab/reservation-salles:latest
mkcdigitallab/reservation-salles:1.1
```

---

# ☁️ Déploiement de production avec Render

L'application est déployée sur **Render** à partir de l'image Docker publiée sur Docker Hub.

Architecture actuelle :

```text
                         INTERNET
                            │
              ┌─────────────┴─────────────┐
              │                           │
              ▼                           ▼
     Render Web Service             Render phpMyAdmin
     reservation-salles:latest      phpmyadmin-reservation
              │                           │
              │                           │
              └─────────────┬─────────────┘
                            │
                            ▼
                    Aiven MySQL 8.4
```

## Application Render

Image utilisée :

```text
mkcdigitallab/reservation-salles:latest
```

URL actuelle :

https://reservation-salles-latest.onrender.com

Variables principales configurées dans Render :

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

Le mot de passe réel n'est volontairement pas présent dans ce README.

## Vérification du déploiement

Un déploiement réussi doit montrer dans les logs quelque chose de similaire à :

```text
[RUN ] 001_create_salles.sql
[RUN ] 002_create_reservations.sql
Migrations terminées.
5 salles initialisées avec succès.
PHP 8.3.x Development Server (http://0.0.0.0:10000) started
GET / ... 200
Your service is live
```

---

# 🗃️ Base de données de production avec Aiven

La base de données de production est hébergée sur **Aiven MySQL 8.4**.

Configuration utilisée par l'application :

```text
Host     : reservation-salles-db-mkcdigitallab-118b.d.aivencloud.com
Port     : 28676
Database : defaultdb
User     : avnadmin
SSL      : requis
```

La connexion entre Render et Aiven se fait donc directement :

```text
Render
   │
   │ MySQL + TLS
   ▼
Aiven MySQL
```

> Le mot de passe Aiven ne doit jamais être écrit dans le dépôt, le README, un commit ou une capture publique.

---

# 🧰 phpMyAdmin sur Render

Pour administrer visuellement la base MySQL Aiven, un service **phpMyAdmin séparé** a été déployé sur Render.

C'est volontairement séparé de l'application principale :

```text
Render
│
├── reservation-salles:latest
│       │
│       └──────────► Aiven MySQL
│
└── phpmyadmin-reservation
            │
            └────────► Aiven MySQL
```

Cette architecture permet à l'application et à l'outil d'administration de partager la même base sans mélanger leurs responsabilités.

## Image utilisée

```text
phpmyadmin/phpmyadmin:latest
```

## Service Render

Nom :

```text
phpmyadmin-reservation
```

Port du conteneur :

```text
80
```

URL actuelle :

https://phpmyadmin-reservation.onrender.com

## Variables phpMyAdmin

```text
PMA_HOST=reservation-salles-db-mkcdigitallab-118b.d.aivencloud.com
PMA_PORT=28676
PMA_SSL=1
```

`PMA_SSL=1` est important car la connexion Aiven MySQL nécessite TLS/SSL.

Le mot de passe n'est pas stocké dans phpMyAdmin comme variable publique. L'utilisateur se connecte depuis l'écran phpMyAdmin avec ses identifiants Aiven.

### Connexion à phpMyAdmin

Depuis l'URL :

```text
https://phpmyadmin-reservation.onrender.com
```

utiliser :

```text
Serveur     : reservation-salles-db-mkcdigitallab-118b.d.aivencloud.com
Utilisateur : avnadmin
Mot de passe : mot de passe Aiven
```

La base à administrer est :

```text
defaultdb
```

---

# 🔎 Vérifier une réservation avec phpMyAdmin

phpMyAdmin sert notamment à vérifier ce qui se passe réellement dans la base lorsque l'utilisateur crée une réservation.

Après connexion :

```text
defaultdb
   │
   ├── migrations
   ├── reservations
   └── salles
```

Pour vérifier les réservations, ouvrir la table :

```text
reservations
```

Puis consulter les lignes enregistrées.

On peut également exécuter une requête SQL de lecture comme :

```sql
SELECT *
FROM reservations
ORDER BY id DESC;
```

Pour vérifier les salles :

```sql
SELECT *
FROM salles
ORDER BY id;
```

Cette séparation est très utile pour le diagnostic :

```text
Utilisateur crée une réservation
          │
          ▼
Application Render
          │
          ▼
ReservationService
          │
          ▼
ReservationRepository
          │
          ▼
Aiven MySQL
          │
          ▼
phpMyAdmin permet de vérifier les données
```

---

# 🩺 Diagnostic d'une réservation qui ne semble pas fonctionner

Lorsqu'une réservation semble ne rien faire, il faut distinguer plusieurs problèmes possibles.

## 1. La requête n'arrive pas au serveur

Vérifier les logs Render.

## 2. La requête arrive mais la validation échoue

Vérifier les données saisies et les règles de `ReservationValidator`.

## 3. La salle n'existe pas ou n'est pas active

Vérifier la table `salles` dans phpMyAdmin.

## 4. La réservation chevauche une autre réservation

Vérifier la table `reservations` et la logique du `ReservationRepository`.

## 5. La réservation est enregistrée mais n'apparaît pas dans l'interface

Vérifier directement la table `reservations` avec phpMyAdmin.

Cette dernière vérification permet de savoir immédiatement si le problème vient :

- de l'enregistrement en base ; ou
- de l'affichage après l'enregistrement.

---

# 🔐 Sécurité du déploiement

Les éléments suivants ne doivent jamais être commités :

- mots de passe Aiven ;
- token Docker Hub ;
- fichiers `.env` contenant de vrais secrets ;
- clés privées ;
- tokens d'API.

Le dépôt doit contenir uniquement des exemples comme `.env.example`.

Pour une utilisation publique de phpMyAdmin, il est recommandé de protéger fortement les comptes utilisés et de limiter autant que possible l'exposition de l'outil d'administration.

---

# 🚀 Installation rapide

## Méthode Docker locale

```bash
git clone https://github.com/mkcdigitallab/Reservation-Salles.git
cd Reservation-Salles

docker compose up -d --build
```

Application :

```text
http://localhost:8000
```

Arrêt :

```bash
docker compose down
```

Suppression des volumes :

```bash
docker compose down -v
```

---

# 🧪 Cycle complet du projet

Le cycle actuel du projet est :

```text
1. Développement PHP
        │
        ▼
2. Tests locaux
        │
        ▼
3. Git commit
        │
        ▼
4. Git push main
        │
        ▼
5. GitHub Actions
        │
        ▼
6. Build de l'image Docker
        │
        ▼
7. Push vers Docker Hub
        │
        ▼
8. Render récupère l'image
        │
        ▼
9. Migrations MySQL
        │
        ▼
10. Seed des salles
        │
        ▼
11. Application disponible
        │
        ▼
12. Aiven MySQL stocke les données
        │
        ▼
13. phpMyAdmin permet l'administration
```

---

# 🌐 Services de production

| Service | Rôle | Plateforme |
|---|---|---|
| Reservation-Salles | Application PHP | Render |
| MySQL | Base de données | Aiven |
| phpMyAdmin | Administration MySQL | Render |
| Docker image | Image de production | Docker Hub |
| CI/CD | Build et publication | GitHub Actions |

---

# 📚 Ce que ce projet permet d'apprendre

Ce projet couvre plusieurs compétences importantes :

### PHP / POO

- classes et objets ;
- interfaces ;
- injection de dépendances ;
- DTO ;
- Repository Pattern ;
- Service Layer ;
- séparation des responsabilités.

### Base de données

- MySQL ;
- migrations ;
- Eloquent ;
- relations et requêtes ;
- contrôle des conflits de réservation.

### Web

- HTTP ;
- GET / POST ;
- routage ;
- formulaires ;
- sessions ;
- CSRF ;
- redirections.

### DevOps

- Docker ;
- Docker Compose ;
- images Docker ;
- Docker Hub ;
- GitHub Actions ;
- CI/CD ;
- variables d'environnement ;
- déploiement Render ;
- base distante Aiven ;
- administration avec phpMyAdmin.

---

# 👨‍💻 Auteur

**MKC Digital Lab**

Projet développé dans un objectif d'apprentissage pratique et de montée en compétence sur le développement web PHP, l'architecture logicielle et le déploiement.

---

# 🔗 Dépôt

https://github.com/mkcdigitallab/Reservation-Salles

# 🌍 Application

https://reservation-salles-latest.onrender.com

# 🗃️ phpMyAdmin

https://phpmyadmin-reservation.onrender.com

---

> **État du projet :** application PHP déployée sur Render, image Docker publiée sur Docker Hub, base MySQL hébergée sur Aiven et outil phpMyAdmin séparé déployé sur Render pour l'administration de la base.
