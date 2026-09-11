<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use DateTimeInterface;

interface ReservationRepositoryInterface
{
    public function create(array $data): Reservation;

    public function findById(int $id): ?Reservation;

    public function cancel(Reservation $reservation): Reservation;

    public function hasConflict(
        int $salleId,
        DateTimeInterface $dateDebut,
        DateTimeInterface $dateFin
    ): bool;
}
