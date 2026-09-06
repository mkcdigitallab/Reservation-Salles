<?php

declare(strict_types=1);

namespace App\DTO;

use DateTimeImmutable;

final readonly class ReservationDTO
{
    public function __construct(
        public int $salleId,
        public string $responsable,
        public string $email,
        public string $motif,
        public DateTimeImmutable $dateDebut,
        public DateTimeImmutable $dateFin,
    ) {
    }
}
