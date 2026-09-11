<?php

declare(strict_types=1);

namespace App\Validation;

use DateTimeInterface;
use InvalidArgumentException;

final class ReservationValidator
{
    public function validate(
        string $responsable,
        string $email,
        string $motif,
        DateTimeInterface $dateDebut,
        DateTimeInterface $dateFin
    ): void {
        $responsable = trim($responsable);
        $motif = trim($motif);

        if ($responsable === '') {
            throw new InvalidArgumentException(
                'Le responsable est obligatoire.'
            );
        }

        if (mb_strlen($responsable) > 100) {
            throw new InvalidArgumentException(
                'Le responsable ne peut pas dépasser 100 caractères.'
            );
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255) {
            throw new InvalidArgumentException(
                'L’adresse email est invalide.'
            );
        }

        if (mb_strlen($motif) < 5 || mb_strlen($motif) > 255) {
            throw new InvalidArgumentException(
                'Le motif doit contenir entre 5 et 255 caractères.'
            );
        }

        $maintenant = new \DateTimeImmutable();

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

        if ($duree < 30 * 60) {
            throw new InvalidArgumentException(
                'Une réservation doit durer au moins 30 minutes.'
            );
        }

        if ($duree > 4 * 60 * 60) {
            throw new InvalidArgumentException(
                'Une réservation ne peut pas dépasser 4 heures.'
            );
        }
    }
}
