# Changelog

Toutes les modifications importantes du projet sont documentées dans ce fichier.

## [Unreleased]

### Corrigé

- Ajout de `respect/validation:^2.4` avec verrouillage Composer.
- Injection des interfaces de repositories dans les contrôleurs.
- Implémentation complète de l'annulation d'une réservation avec contrôle CSRF.
- Restauration de `database/schema.sql`, utilisé par Docker au premier démarrage.
- Ajout du seed SQL Docker pour initialiser les salles de démonstration.
- Suppression de `cap_drop: ALL` sur MySQL afin de préserver son démarrage normal.
- Renforcement de la CI avec validation Composer, PHPUnit, audit des dépendances et validation/build Docker.
- Nettoyage du script de maintenance et des tests CSRF.

### Documentation

- Guide de démarrage Docker et de réinitialisation du volume MySQL.
- Procédure de déploiement et gestion des secrets de production.

## [1.0.0] - 2026-09-06

### Ajouté

- Configuration Composer et autoload PSR-4
- Connexion MySQL avec Eloquent
- Modèles `Salle` et `Reservation`
- Migrations SQL
- Données initiales des salles
- Validation des réservations
- `ReservationDTO`
- Repositories pour les salles et réservations
- Service métier de réservation
- Interface web responsive
- Routage avec FastRoute
- Contrôleurs `SalleController` et `ReservationController`
- Conteneur de dépendances PHP-DI
- Gestion des erreurs de validation HTTP
- Tests unitaires PHPUnit
- Documentation du projet

### Règles métier

- Réservation dans le futur uniquement
- Début avant fin
- Durée maximale de 4 heures
- Salle existante et active
- Aucun chevauchement entre réservations confirmées
- Les créneaux adjacents sont autorisés
- Les réservations annulées ne bloquent pas une salle
