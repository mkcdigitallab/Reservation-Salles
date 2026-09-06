# Changelog

Toutes les modifications importantes du projet sont documentées dans ce fichier.

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
