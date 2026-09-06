<?php

declare(strict_types=1);

namespace App\Validation;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

class ReservationValidator
{
    public function validate(
        string $responsable,
        string $email,
        string $motif,
        DateTimeInterface $dateDebut,
        DateTimeInterface $dateFin
    ): void {
        if (trim($responsable) === '') {
            throw new InvalidArgumentException(
                'Le responsable est obligatoire.'
            );
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(
                'L’adresse email est invalide.'
            );
        }

        if (trim($motif) === '') {
            throw new InvalidArgumentException(
                'Le motif est obligatoire.'
            );
        }

        $maintenant = new DateTimeImmutable();

        if ($dateDebut <= $maintenant) {
            throw new InvalidArgumentException(
                'La date de début doit être dans le futur.'
            );
        }

        if ($dateDebut >= $dateFin) {
            throw new InvalidArgumentException(
                'La date de début doit être antérieure à la date de fin.'
            );
        }

        $duree = $dateFin->getTimestamp() - $dateDebut->getTimestamp();

        if ($duree > 4 * 60 * 60) {
            throw new InvalidArgumentException(
                'Une réservation ne peut pas dépasser 4 heures.'
            );
        }
    }
}
