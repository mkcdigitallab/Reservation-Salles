# 🏢 Reservation-Salles

Application web PHP de réservation de salles, développée avec une architecture PHP moderne et modulaire.

Le projet permet notamment de :

- afficher les salles actives ;
- ouvrir un formulaire de réservation ;
- vérifier les données saisies ;
- vérifier qu'une salle existe et qu'elle est active ;
- empêcher les réservations qui se chevauchent ;
- enregistrer une réservation ;
- annuler une réservation ;
- protéger les formulaires contre les attaques CSRF ;
- séparer les responsabilités entre contrôleurs, services, repositories, modèles, validation et vues.

> **Objectif pédagogique :** ce projet est particulièrement intéressant pour comprendre comment passer d'un simple projet PHP à une application organisée autour de la POO, de l'injection de dépendances, des interfaces, du pattern Repository, du Service Layer, des DTO et d'un point d'entrée unique.

---

## 📌 Technologies utilisées

- **PHP** avec typage strict
- **Composer** pour les dépendances et l'autoloading PSR-4
- **PHP-DI** pour l'injection de dépendances
- **FastRoute** pour le routage HTTP
- **Illuminate Database / Eloquent** pour l'accès aux données
- **PHP dotenv** pour les variables d'environnement
- **Respect/Validation** comme dépendance de validation disponible dans le projet
- **PHPUnit** prévu pour les tests
- **Docker / Docker Compose** pour la conteneurisation
- **HTML / CSS** pour les vues

Les dépendances principales et l'autoloading `App\\ => src/` sont définis dans `composer.json`. fileciteturn10file0L2-L2

---

# 🧱 Architecture générale

Le projet n'est pas un MVC de framework classique comme Laravel. Il s'agit plutôt d'une **architecture PHP personnalisée inspirée de MVC**, enrichie par plusieurs patterns :

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
                                      Database

Controller ───────────────────────────────────────► Template/View
```

L'idée fondamentale est la suivante : **une classe ne doit pas tout faire**.

Par exemple, `ReservationController` ne doit pas :

- construire lui-même les requêtes SQL ;
- contenir toutes les règles métier ;
- gérer toute la validation ;
- gérer directement toute la sécurité ;
- manipuler toute la base de données.

Il délègue ces responsabilités aux classes spécialisées.

Dans le projet actuel, le contrôleur crée le DTO puis appelle `ReservationService`; le service utilise le validator et les repositories; les repositories utilisent les modèles Eloquent. fileciteturn4file0L2-L2 fileciteturn3file0L2-L2

---

# 📂 Structure du projet

```text
Reservation-Salles/
│
├── .github/
│   └── workflows/
│       └── ci.yml
│
├── config/
│   ├── container.php
│   └── database.php
│
├── database/
│   ├── Dockerfile
│   ├── migrations/
│   │   ├── 001_create_salles.sql
│   │   └── 002_create_reservations.sql
│   ├── schema.sql
│   ├── seed.php
│   └── seed.sql
│
├── public/
│   ├── index.php
│   └── assets/
│       └── style.css
│
├── routes/
│   └── web.php
│
├── scripts/
│   └── malang-kiya-ciss.sh
│
├── src/
│   ├── Application.php
│   ├── Controller/
│   │   ├── ReservationController.php
│   │   └── SalleController.php
│   │
│   ├── DTO/
│   │   └── ReservationDTO.php
│   │
│   ├── Model/
│   │   ├── Reservation.php
│   │   └── Salle.php
│   │
│   ├── Repository/
│   │   ├── ReservationRepository.php
│   │   ├── ReservationRepositoryInterface.php
│   │   ├── SalleRepository.php
│   │   └── SalleRepositoryInterface.php
│   │
│   ├── Security/
│   │   └── CsrfToken.php
│   │
│   ├── Service/
│   │   ├── ReservationService.php
│   │   └── AnnulerReservationService.php
│   │
│   └── Validation/
│       └── ReservationValidator.php
│
├── templates/
│   ├── error/
│   │   └── 422.php
│   ├── layout/
│   │   ├── header.php
│   │   └── footer.php
│   ├── reservation/
│   │   └── create.php
│   └── salle/
│       └── index.php
│
├── tests/
│   ├── Integration/
│   └── Unit/
│
├── .env.example
├── .env.prod.example
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

Cette structure correspond à l'organisation actuelle du dépôt : les couches `Controller`, `DTO`, `Model`, `Repository`, `Security`, `Service` et `Validation` sont présentes dans `src`, tandis que les vues sont séparées dans `templates`. fileciteturn2file0L2-L2 fileciteturn22file0L2-L2

---

# 1. `public/` — la partie exposée au navigateur

## Pourquoi ce dossier existe ?

`public/` représente la **racine publique de l'application**.

Le serveur web doit idéalement pointer vers ce dossier et non vers la racine complète du projet.

Cela évite notamment d'exposer directement :

- `.env` ;
- les fichiers de configuration ;
- le code source métier ;
- les scripts internes ;
- les fichiers de base de données.

## `public/index.php`

C'est le **Front Controller** du projet.

Un Front Controller est un point d'entrée unique par lequel passent les requêtes HTTP de l'application.

Il :

1. charge l'autoloader Composer ;
2. charge les variables d'environnement ;
3. configure la session ;
4. configure plusieurs protections HTTP ;
5. construit le container de dépendances ;
6. initialise Eloquent ;
7. charge les routes ;
8. demande à FastRoute de trouver la route correspondant à la requête ;
9. récupère le contrôleur dans le container ;
10. appelle la méthode correspondante.

Le fichier configure notamment `HttpOnly`, `SameSite`, plusieurs headers de sécurité et une Content Security Policy. fileciteturn7file0L2-L2

### Qui appelle qui ?

```text
Navigateur
   ↓
public/index.php
   ↓
routes/web.php
   ↓
Controller
```

Le Front Controller ne contient donc pas la logique métier de réservation.

---

# 2. `routes/` — le système de routage

## `routes/web.php`

Ce dossier existe pour **déclarer les URL de l'application indépendamment du code métier**.

Actuellement, les routes principales sont :

```text
GET  /                         → SalleController@index
GET  /reservations/create      → ReservationController@create
POST /reservations             → ReservationController@store
POST /reservations/{id}/annuler → ReservationController@cancel
```

Ces routes sont enregistrées avec FastRoute. fileciteturn8file0L2-L2

## Pourquoi ne pas mettre les routes dans `index.php` ?

Parce que cela mélangerait deux responsabilités :

- démarrer l'application ;
- définir toutes les URL.

Le fichier `index.php` orchestre, alors que `web.php` décrit les routes.

### Flux

```text
GET /
 ↓
FastRoute
 ↓
SalleController::index()
```

Et pour une réservation :

```text
POST /reservations
 ↓
FastRoute
 ↓
ReservationController::store()
```

---

# 3. `src/Controller/` — recevoir la requête et coordonner

Le contrôleur est la couche qui fait le lien entre **HTTP** et **le métier**.

Il reçoit une requête, récupère les paramètres nécessaires, appelle les bonnes classes et choisit la réponse à retourner.

## `SalleController.php`

Responsabilité : gérer les requêtes concernant les salles.

Il travaille avec `SalleRepository` plutôt que de construire directement les requêtes de base de données dans le contrôleur.

## `ReservationController.php`

C'est le contrôleur principal du processus de réservation.

Il reçoit plusieurs dépendances :

```text
SalleRepositoryInterface
ReservationService
AnnulerReservationService
CsrfToken
```

Cela montre clairement qu'il ne possède pas lui-même toute la logique. fileciteturn4file0L2-L2

### Méthode `create()`

Elle :

1. demande les salles actives au repository ;
2. vérifie éventuellement si une salle a été présélectionnée dans l'URL ;
3. récupère un token CSRF ;
4. charge la vue `templates/reservation/create.php`.

### Méthode `store()`

Elle reçoit le formulaire envoyé par le navigateur.

Elle :

1. récupère le token CSRF ;
2. vérifie le token ;
3. récupère les champs POST ;
4. vérifie les champs obligatoires ;
5. construit un `ReservationDTO` ;
6. appelle `ReservationService::createReservation()` ;
7. redirige vers `/` si tout est correct ;
8. affiche une erreur 422 si une exception métier ou de format est levée.

Le code réel suit précisément cette chaîne. fileciteturn4file0L2-L2

### Méthode `cancel()`

Elle suit une logique similaire :

```text
Requête POST
 ↓
Vérification CSRF
 ↓
Validation de l'ID
 ↓
AnnulerReservationService
 ↓
ReservationRepository
 ↓
Base de données
```

---

# 4. `src/DTO/` — transporter les données

## `ReservationDTO.php`

DTO signifie **Data Transfer Object**.

Son rôle est de transporter un ensemble cohérent de données d'une couche à une autre.

Le DTO contient :

```text
salleId
responsable
email
motif
dateDebut
dateFin
```

Il est `readonly`, ce qui signifie que ses propriétés ne sont pas destinées à être modifiées après sa construction. fileciteturn13file0L2-L2

## Pourquoi utiliser un DTO ?

Sans DTO, on pourrait avoir :

```php
createReservation(
    $salleId,
    $responsable,
    $email,
    $motif,
    $dateDebut,
    $dateFin
);
```

Avec un DTO :

```php
createReservation($dto);
```

Le service reçoit donc **un objet représentant une demande de réservation**, au lieu d'une longue liste de paramètres.

### Idée importante

Le DTO ne décide pas si la réservation est autorisée.

Il **transporte** les données.

La décision appartient au service.

---

# 5. `src/Validation/` — vérifier les règles de saisie

## `ReservationValidator.php`

Cette classe contient les règles de validation de la réservation.

Elle vérifie notamment :

- responsable obligatoire ;
- longueur maximale du responsable ;
- email valide ;
- motif entre 5 et 255 caractères ;
- date de début dans le futur ;
- date de début avant la date de fin ;
- durée minimale de 30 minutes ;
- durée maximale de 4 heures.

Ces règles sont centralisées dans `validate()`. fileciteturn12file0L2-L2

## Pourquoi créer une classe dédiée ?

Parce que le service ne doit pas devenir un énorme bloc de conditions.

On préfère :

```text
ReservationService
       ↓
ReservationValidator
       ↓
Validation OK / Exception
```

### Différence entre validation et métier

Validation :

> « L'email est-il valide ? »

Métier :

> « Cette salle est-elle libre pendant cette période ? »

Le projet sépare ces deux responsabilités.

---

# 6. `src/Service/` — la logique métier

Le **Service Layer** contient les opérations métier importantes.

C'est l'une des couches les plus importantes du projet.

## `ReservationService.php`

Sa responsabilité est de réaliser une réservation correctement.

Le service reçoit :

```text
ReservationValidator
SalleRepositoryInterface
ReservationRepositoryInterface
```

Puis il exécute les règles métier dans le bon ordre. fileciteturn3file0L2-L2

### Processus réel

```text
ReservationDTO
      ↓
Validator
      ↓
SalleRepository
      ↓
Salle existe ?
      ↓
Salle active ?
      ↓
ReservationRepository
      ↓
Conflit ?
      ↓
Création de la réservation
```

### Pourquoi le service existe ?

Parce qu'une réservation n'est pas simplement :

```sql
INSERT INTO reservations ...
```

Il faut d'abord respecter plusieurs règles.

Le service centralise donc le **cas d'utilisation** :

> « Créer une réservation ».

---

## `AnnulerReservationService.php`

Cette classe représente un autre cas d'utilisation :

> « Annuler une réservation ».

Elle vérifie :

1. que l'identifiant est valide ;
2. que la réservation existe ;
3. qu'elle n'est pas déjà annulée ;
4. puis demande au repository de l'annuler.

fileciteturn16file0L2-L2

### Pourquoi séparer les deux services ?

Parce que :

```text
Créer une réservation
```

et

```text
Annuler une réservation
```

sont deux cas d'utilisation différents.

Cela respecte mieux le principe de responsabilité unique.

---

# 7. `src/Repository/` — accès aux données

Le Repository sert d'intermédiaire entre la logique métier et la persistance des données.

Il répond à des questions comme :

- trouver une salle ;
- récupérer les salles actives ;
- trouver une réservation ;
- créer une réservation ;
- vérifier un conflit ;
- annuler une réservation.

## `SalleRepository.php`

Il récupère notamment toutes les salles actives et peut chercher une salle par son ID. fileciteturn15file0L2-L2

## `ReservationRepository.php`

Il s'occupe de la persistance des réservations.

Il peut :

```text
create()
findById()
cancel()
hasConflict()
```

La méthode `hasConflict()` recherche une réservation confirmée dont l'intervalle de temps chevauche celui demandé. fileciteturn5file0L2-L2

---

# 8. Les interfaces Repository

Le projet possède :

```text
ReservationRepositoryInterface
SalleRepositoryInterface
```

et leurs implémentations :

```text
ReservationRepository
SalleRepository
```

## Pourquoi une interface ?

L'interface définit **ce que l'on peut faire**, sans imposer **comment on le fait**.

Par exemple :

```text
ReservationRepositoryInterface
        │
        ├── create()
        ├── findById()
        ├── cancel()
        └── hasConflict()
```

Puis :

```text
ReservationRepository
        ↓
Implémentation réelle avec Eloquent
```

Le service dépend donc de :

```php
ReservationRepositoryInterface
```

et non directement de :

```php
ReservationRepository
```

C'est une application du **Dependency Inversion Principle (DIP)**.

---

# 9. `src/Model/` — représenter les données métier persistées

Les modèles représentent les entités manipulées par l'application et correspondent aux tables de la base de données.

## `Reservation.php`

Le modèle correspond à la table :

```text
reservations
```

Il définit notamment les champs autorisés en mass assignment et convertit les dates en objets date/heure. Il définit également une relation `belongsTo` vers `Salle`. fileciteturn9file0L2-L2

## Eloquent et Active Record

Le projet utilise `Illuminate\\Database\\Eloquent\\Model`.

Eloquent rapproche le modèle de sa représentation persistée et permet des opérations telles que :

```php
Reservation::query()->find($id);
Reservation::query()->create($data);
```

Le Repository encapsule ces appels afin que le reste de l'application ne soit pas obligé de connaître les détails de persistance.

---

# 10. `src/Security/` — sécurité applicative

## `CsrfToken.php`

CSRF signifie **Cross-Site Request Forgery**.

Le token CSRF permet de vérifier qu'une requête POST vient bien d'un formulaire généré par l'application et associé à la session attendue.

La classe :

- génère un token aléatoire ;
- le conserve dans la session ;
- vérifie le token envoyé ;
- utilise `hash_equals()` pour comparer les valeurs.

fileciteturn14file0L2-L2

Le contrôleur vérifie ce token avant de créer ou d'annuler une réservation. fileciteturn4file0L2-L2

---

# 11. `templates/` — les vues

Les templates représentent la partie **présentation** de l'application.

Ils sont séparés du code métier.

## `templates/salle/index.php`

Vue associée à l'affichage des salles.

## `templates/reservation/create.php`

Vue contenant le formulaire de création d'une réservation.

Le contrôleur charge explicitement ce template lorsqu'il doit afficher le formulaire. fileciteturn4file0L2-L2

## `templates/error/422.php`

Vue utilisée lorsque l'application doit afficher une erreur de validation ou une requête invalide. fileciteturn26file0L2-L2

## `templates/layout/`

Contient les éléments communs de présentation :

```text
header.php
footer.php
```

L'objectif est d'éviter de répéter le même HTML dans toutes les pages. fileciteturn25file0L2-L2

---

# 12. `config/` — configuration technique

## `config/database.php`

Ce fichier construit et configure la connexion à la base de données avec Illuminate Database.

Les informations suivantes viennent des variables d'environnement :

```text
DB_DRIVER
DB_HOST
DB_PORT
DB_DATABASE
DB_USERNAME
DB_PASSWORD
```

Puis Eloquent est configuré avec `setAsGlobal()` et `bootEloquent()`. fileciteturn17file0L2-L2

### Pourquoi utiliser `.env` ?

Les informations d'environnement ne doivent pas être écrites directement dans le code source.

Par exemple, le mot de passe de production ne doit pas devenir :

```php
$password = 'mon-vrai-mot-de-passe';
```

On préfère :

```text
.env
   ↓
$_ENV
   ↓
config/database.php
```

---

## `config/container.php`

Ce fichier configure le **Dependency Injection Container**.

Il indique notamment :

```text
SalleRepositoryInterface
        ↓
SalleRepository

ReservationRepositoryInterface
        ↓
ReservationRepository
```

PHP-DI sait alors quelle implémentation fournir lorsqu'une classe demande une interface. fileciteturn6file0L2-L2

### Pourquoi c'est puissant ?

Le service peut demander :

```php
ReservationRepositoryInterface $repository
```

sans avoir besoin de faire lui-même :

```php
new ReservationRepository();
```

La création des objets est donc centralisée dans le container.

---

# 13. `database/` — structure et données de la base

Le dossier contient :

```text
database/
├── migrations/
├── schema.sql
├── seed.php
├── seed.sql
└── Dockerfile
```

Ces fichiers servent à définir et initialiser la base de données. fileciteturn28file0L2-L2

## `migrations/`

Les migrations SQL permettent de représenter les évolutions de la structure de la base.

Ici :

```text
001_create_salles.sql
002_create_reservations.sql
```

L'ordre numéroté permet de comprendre dans quel ordre les changements doivent être appliqués.

## `schema.sql`

Représente le schéma global de la base.

## `seed.sql` et `seed.php`

Permettent d'insérer des données initiales ou de démonstration.

## `database/Dockerfile`

Sert à définir l'image du service de base de données utilisé dans l'environnement Docker.

---

# 14. Docker

Le projet possède :

```text
Dockerfile
docker-compose.yml
docker-compose.prod.yml
database/Dockerfile
```

L'objectif est de rendre l'environnement reproductible.

Au lieu de demander à chaque développeur de configurer manuellement chaque composant, Docker permet de décrire les services nécessaires.

On distingue :

```text
docker-compose.yml
        ↓
Environnement de développement
```

et :

```text
docker-compose.prod.yml
        ↓
Environnement de production
```

---

# 15. `tests/` — préparer la vérification automatique

Le projet possède actuellement :

```text
tests/
├── Integration/
└── Unit/
```

ainsi qu'un `phpunit.xml` et PHPUnit dans les dépendances de développement. fileciteturn27file0L2-L2 fileciteturn10file0L2-L2

## Tests unitaires

Les tests unitaires doivent vérifier une classe ou une fonction isolée.

Exemples pertinents :

```text
ReservationValidator
AnnulerReservationService
CsrfToken
```

## Tests d'intégration

Ils doivent vérifier plusieurs composants ensemble.

Exemple :

```text
Controller
   ↓
Service
   ↓
Repository
   ↓
Base de données
```

> Les dossiers de tests existent dans la structure actuelle, mais ils sont encore à compléter.

---

# 16. `composer.json` — dépendances et autoloading

`composer.json` décrit le projet et ses dépendances.

Les dépendances principales sont :

| Dépendance | Rôle |
|---|---|
| PHP-DI | Injection de dépendances |
| phpdotenv | Variables d'environnement |
| illuminate/database | Eloquent / accès aux données |
| nikic/fast-route | Routage HTTP |
| respect/validation | Validation |
| PHPUnit | Tests |

Le projet utilise l'autoloading PSR-4 :

```text
App\\ → src/
Tests\\ → tests/
```

Cela signifie qu'une classe comme :

```php
App\\Service\\ReservationService
```

est automatiquement recherchée dans :

```text
src/Service/ReservationService.php
```

fileciteturn10file0L2-L2

---

# 🔄 Exemple complet : créer une réservation

Voici le parcours le plus important du projet.

## Étape 1 — le navigateur

L'utilisateur remplit le formulaire.

```text
Formulaire HTML
     ↓
POST /reservations
```

## Étape 2 — Front Controller

La requête arrive dans :

```text
public/index.php
```

Le fichier demande à FastRoute quelle route correspond à la requête. fileciteturn7file0L2-L2

## Étape 3 — Route

`routes/web.php` indique :

```text
POST /reservations
        ↓
ReservationController::store()
```

fileciteturn8file0L2-L2

## Étape 4 — Controller

Le contrôleur :

```text
récupère POST
    ↓
vérifie CSRF
    ↓
convertit les données
    ↓
crée ReservationDTO
    ↓
appelle ReservationService
```

fileciteturn4file0L2-L2

## Étape 5 — DTO

Le DTO transporte :

```text
Salle
Responsable
Email
Motif
Date début
Date fin
```

fileciteturn13file0L2-L2

## Étape 6 — Service

Le service appelle le validator.

```text
ReservationService
        ↓
ReservationValidator
```

fileciteturn3file0L2-L2

## Étape 7 — vérification de la salle

Le service demande :

```text
SalleRepositoryInterface
        ↓
SalleRepository
        ↓
Salle::query()->find()
```

Puis il vérifie que la salle existe et qu'elle est active. fileciteturn3file0L2-L2

## Étape 8 — vérification du conflit

Le service appelle :

```text
ReservationRepository::hasConflict()
```

Le repository vérifie les réservations confirmées qui se chevauchent. fileciteturn5file0L2-L2

## Étape 9 — création

Si toutes les règles sont respectées :

```text
ReservationRepository
        ↓
Reservation::query()->create()
        ↓
Base de données
```

fileciteturn5file0L2-L2

## Étape 10 — réponse

Le contrôleur redirige ensuite vers `/`.

---

# 🧩 Patterns utilisés

## 1. Front Controller

**Où ?** `public/index.php`

**But :** centraliser le point d'entrée HTTP.

```text
Toutes les requêtes
       ↓
public/index.php
```

---

## 2. MVC / séparation Présentation-Métier-Données

Le projet reprend l'idée générale de MVC :

```text
Controller → coordination HTTP
Model      → données
Templates  → présentation
```

Mais il va plus loin en ajoutant Service, Repository, DTO et Validation.

Il est donc plus juste de parler d'une **architecture inspirée de MVC avec couches supplémentaires** plutôt que d'un MVC minimal.

---

## 3. Repository Pattern

**Où ?** `src/Repository/`

Le repository encapsule l'accès aux données.

```text
Service
   ↓
RepositoryInterface
   ↓
Repository
   ↓
Eloquent
   ↓
Database
```

Cela évite de disperser les requêtes dans les contrôleurs et services.

---

## 4. Service Layer Pattern

**Où ?** `src/Service/`

Chaque service représente une opération métier importante.

```text
ReservationService
AnnulerReservationService
```

---

## 5. DTO Pattern

**Où ?** `src/DTO/`

Le DTO transporte les données entre couches sans porter toute la logique métier.

---

## 6. Dependency Injection

Les dépendances sont reçues dans les constructeurs :

```php
public function __construct(
    ReservationValidator $validator,
    SalleRepositoryInterface $salleRepository,
    ReservationRepositoryInterface $reservationRepository,
) {}
```

Cela évite que les classes créent elles-mêmes leurs dépendances. fileciteturn3file0L2-L2

---

## 7. Dependency Injection Container

PHP-DI construit les objets et résout les interfaces vers leurs implémentations. fileciteturn6file0L2-L2

---

## 8. Active Record via Eloquent

Les modèles Eloquent permettent de manipuler les données de manière orientée objet.

Exemple présent dans le repository :

```php
Reservation::query()->create($data);
```

fileciteturn5file0L2-L2

---

# 🧠 Principes de conception appliqués

## 1. Single Responsibility Principle — SRP

Une classe doit avoir une responsabilité principale.

Exemples :

```text
Controller    → HTTP / orchestration
Validator     → validation
Service       → métier
Repository    → persistance
DTO           → transport de données
CsrfToken     → protection CSRF
Model         → représentation persistée
```

C'est l'un des principes les plus visibles dans ce projet.

---

## 2. Dependency Inversion Principle — DIP

Le métier dépend des abstractions plutôt que des implémentations concrètes.

Exemple :

```text
ReservationService
        ↓
ReservationRepositoryInterface
        ↓
ReservationRepository
```

et le container fait le lien entre les deux. fileciteturn6file0L2-L2

---

## 3. Open/Closed Principle — OCP

Les interfaces permettent de remplacer ou ajouter une implémentation sans modifier toute la logique métier.

Par exemple, on pourrait théoriquement créer :

```text
ReservationRepositoryInterface
          │
          ├── ReservationRepository
          └── ReservationRepositoryTest
```

Le service peut continuer à travailler avec l'interface.

---

## 4. Encapsulation

Les détails internes d'une responsabilité sont regroupés dans la classe concernée.

Exemple : le service n'a pas besoin de connaître tous les détails de la requête utilisée pour détecter un conflit.

Il demande simplement au repository :

```php
hasConflict(...)
```

Le repository cache la manière dont cette vérification est réalisée. fileciteturn5file0L2-L2

---

## 5. Separation of Concerns

Le projet sépare :

```text
Présentation
Métier
Validation
Persistance
Sécurité
Configuration
Routage
```

C'est l'un des principes architecturaux centraux du projet.

---

## 6. DRY — Don't Repeat Yourself

Le code cherche à centraliser les responsabilités communes.

Exemples :

- règles de validation dans `ReservationValidator` ;
- accès aux données dans les repositories ;
- configuration des dépendances dans le container ;
- header/footer dans `templates/layout`.

---

# 🔐 Principes de sécurité

Le projet applique déjà plusieurs protections :

## CSRF

Les formulaires de création et d'annulation sont protégés par token CSRF. fileciteturn14file0L2-L2

## Cookies de session

Les cookies sont configurés avec :

```text
HttpOnly
SameSite=Lax
Secure si HTTPS
```

## Headers HTTP

Le Front Controller ajoute notamment :

```text
X-Content-Type-Options
X-Frame-Options
Referrer-Policy
Content-Security-Policy
```

fileciteturn7file0L2-L2

## Variables d'environnement

Les paramètres de connexion à la base sont fournis par l'environnement plutôt que codés en dur dans les classes métier. fileciteturn17file0L2-L2

## Validation des entrées

Les données reçues du navigateur sont contrôlées avant leur utilisation métier. Le contrôleur filtre notamment l'identifiant de salle et les dates, puis le validator applique les règles métier de validation. fileciteturn4file0L2-L2 fileciteturn12file0L2-L2

---

# 🧭 Qui appelle qui ?

Voici la chaîne complète à retenir pour comprendre le projet :

```text
                 NAVIGATEUR
                     │
                     ▼
              public/index.php
                     │
                     ▼
                FastRoute
                     │
                     ▼
                Controller
                     │
             ┌───────┴────────┐
             │                │
             ▼                ▼
            DTO          Security/CSRF
             │
             ▼
          Service
             │
       ┌─────┴─────┐
       │           │
       ▼           ▼
 Validator     Repository
                   │
                   ▼
                 Model
                   │
                   ▼
                Database
```

Pour l'affichage :

```text
Controller
    ↓
Repository
    ↓
Model / Database
    ↓
Controller
    ↓
Template
    ↓
Navigateur
```

---

# ❓ Pourquoi chaque couche existe-t-elle ?

| Couche | Pourquoi elle existe ? | Elle appelle principalement |
|---|---|---|
| `public` | Entrée HTTP unique | Router / Container |
| `routes` | Déclarer les URL | Controllers |
| `Controller` | Traduire HTTP en actions applicatives | Services / Repositories / Security / Views |
| `DTO` | Transporter les données | Aucune logique externe importante |
| `Service` | Porter les cas d'utilisation métier | Validator / Repositories |
| `Validation` | Vérifier les données | PHP / règles de validation |
| `Repository` | Accéder aux données | Models / Eloquent |
| `Model` | Représenter les données persistées | Eloquent / Database |
| `Security` | Gérer la protection CSRF | Session |
| `templates` | Présenter les résultats | Données préparées par le Controller |
| `config` | Configurer l'application | Container / Database |
| `database` | Définir et initialiser les données | SGBD / Docker |
| `tests` | Vérifier le comportement | Classes de l'application |

---

# 🎯 Pourquoi cette architecture est meilleure qu'un gros fichier PHP ?

Un projet débutant pourrait faire ceci :

```php
if ($_POST) {
    // validation
    // sécurité
    // requête SQL
    // règles métier
    // insertion
    // HTML
}
```

Cela fonctionne au début, mais devient rapidement difficile à maintenir.

Dans ce projet :

```text
HTTP
 ↓
Controller
 ↓
Service
 ↓
Repository
 ↓
Model
 ↓
Database
```

Chaque niveau a un rôle précis.

Cela rend le projet :

- plus lisible ;
- plus testable ;
- plus maintenable ;
- plus facile à faire évoluer ;
- plus facile à expliquer lors d'une interrogation technique ;
- plus facile à travailler en équipe.

---

# 🧪 Exemple : remplacer le Repository

Grâce à l'interface, le service ne dépend pas directement de l'implémentation.

Aujourd'hui :

```text
ReservationService
        ↓
ReservationRepositoryInterface
        ↓
ReservationRepository
        ↓
Eloquent
```

Pour un test, on pourrait avoir :

```text
ReservationService
        ↓
ReservationRepositoryInterface
        ↓
FakeReservationRepository
```

Le service pourrait ainsi être testé sans forcément utiliser la vraie base de données.

C'est précisément l'intérêt de la dépendance à une abstraction.

---

# ⚠️ Points importants à connaître sur l'architecture

## Ce projet n'est pas un Laravel MVC standard

Même si certaines bibliothèques Laravel/Illuminate sont utilisées, le projet possède son propre système d'entrée, de routage, de container, de services et de repositories.

## `Application.php`

`src/Application.php` existe actuellement comme classe d'application très simple avec une méthode `run()` qui affiche un message. Elle n'est pas le point d'entrée HTTP principal : c'est `public/index.php` qui orchestre réellement l'application web. fileciteturn11file0L2-L2

## Les tests sont encore à développer

La structure `tests/Unit` et `tests/Integration` est présente, mais le dépôt actuel contient essentiellement les dossiers de préparation. fileciteturn27file0L2-L2

---

# 🚀 Installation

## 1. Cloner le projet

```bash
git clone https://github.com/mkcdigitallab/Reservation-Salles.git
cd Reservation-Salles
```

## 2. Installer les dépendances

```bash
composer install
```

## 3. Préparer l'environnement

Copier le fichier d'exemple :

```bash
cp .env.example .env
```

Puis renseigner les paramètres de connexion à la base de données.

## 4. Initialiser la base

Le projet fournit plusieurs fichiers dans `database/` pour le schéma, les migrations et les données initiales.

## 5. Lancer l'application

Pour un environnement PHP local, le serveur doit utiliser `public/` comme racine publique.

Exemple :

```bash
php -S localhost:8000 -t public
```

---

# 🐳 Docker

Le projet fournit également des fichiers Docker et Docker Compose :

```text
Dockerfile
docker-compose.yml
docker-compose.prod.yml
database/Dockerfile
```

Pour utiliser Docker Compose, consulter la configuration du projet puis lancer l'environnement correspondant.

---

# 📚 Ce que ce projet permet d'apprendre

Ce projet constitue un bon exercice pour comprendre progressivement :

### Niveau 1 — PHP

- classes ;
- objets ;
- méthodes ;
- propriétés ;
- exceptions ;
- namespaces ;
- typage strict.

### Niveau 2 — POO

- encapsulation ;
- constructeur ;
- classes finales ;
- interfaces ;
- dépendances ;
- injection de dépendances ;
- composition d'objets.

### Niveau 3 — Architecture

- MVC ;
- Front Controller ;
- Repository ;
- Service Layer ;
- DTO ;
- séparation des responsabilités.

### Niveau 4 — Base de données

- modèles ;
- relations ;
- requêtes ;
- Eloquent ;
- persistance ;
- migrations ;
- seeders.

### Niveau 5 — Sécurité

- sessions ;
- CSRF ;
- validation des entrées ;
- headers HTTP ;
- variables d'environnement.

### Niveau 6 — Qualité logicielle

- SOLID ;
- Dependency Inversion ;
- tests unitaires ;
- tests d'intégration ;
- injection de dépendances ;
- conteneur de services.

### Niveau 7 — Déploiement

- Docker ;
- Docker Compose ;
- configuration de production ;
- CI.

---

# 🧠 Résumé à retenir

Si tu dois expliquer ce projet à un formateur, retiens cette phrase :

> **Le navigateur envoie une requête au Front Controller, le routeur détermine le contrôleur, le contrôleur reçoit les données HTTP et construit éventuellement un DTO, puis délègue le cas d'utilisation à un service. Le service applique les règles métier et utilise des interfaces de repositories pour accéder aux données. Les repositories utilisent les modèles Eloquent pour communiquer avec la base. Enfin, le contrôleur charge une vue pour produire la réponse HTML.**

Et pour les principes :

```text
SRP
→ une responsabilité principale par classe

DIP
→ dépendre d'abstractions plutôt que d'implémentations

Encapsulation
→ cacher les détails internes

Separation of Concerns
→ séparer HTTP, métier, données, sécurité et présentation

DRY
→ éviter de répéter la même logique

Dependency Injection
→ recevoir les dépendances au lieu de les créer partout
```

---

# 👨‍💻 Auteur

**Malang Kiya Cisse**

Projet : **Reservation-Salles**

Licence : **MIT**

---

# 📄 Licence

Ce projet est distribué sous licence MIT.

---

## 🔗 Dépôt

Le code source complet est disponible sur GitHub dans le dépôt `mkcdigitallab/Reservation-Salles`.
