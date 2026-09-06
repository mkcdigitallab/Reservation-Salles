CREATE TABLE reservations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    salle_id INT UNSIGNED NOT NULL,

    responsable VARCHAR(100) NOT NULL,

    email VARCHAR(255) NOT NULL,

    motif VARCHAR(255) NOT NULL,

    date_debut DATETIME NOT NULL,

    date_fin DATETIME NOT NULL,

    statut ENUM('confirmée', 'annulée') NOT NULL DEFAULT 'confirmée',

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_reservations_salle
        FOREIGN KEY (salle_id)
        REFERENCES salles(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);
