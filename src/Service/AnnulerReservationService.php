<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;
use InvalidArgumentException;

final class AnnulerReservationService
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository,
    ) {
    }

    public function cancel(int $reservationId): Reservation
    {
        if ($reservationId <= 0) {
            throw new InvalidArgumentException('La réservation demandée est invalide.');
        }

        $reservation = $this->reservationRepository->findById($reservationId);

        if ($reservation === null) {
            throw new InvalidArgumentException('La réservation demandée n’existe pas.');
        }

        if ($reservation->statut === 'annulée') {
            throw new InvalidArgumentException('La réservation est déjà annulée.');
        }

        return $this->reservationRepository->cancel($reservation);
    }
}
