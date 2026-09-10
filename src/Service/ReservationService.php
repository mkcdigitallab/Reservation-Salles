<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ReservationDTO;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Validation\ReservationValidator;
use App\Model\Reservation;
use InvalidArgumentException;

final class ReservationService
{
   public function __construct(
        private ReservationValidator $validator,
        private SalleRepositoryInterface $salleRepository,
        private ReservationRepositoryInterface $reservationRepository,
) {
}

    public function createReservation(
        ReservationDTO $dto
    ): Reservation {
        $this->validator->validate(
            $dto->responsable,
            $dto->email,
            $dto->motif,
            $dto->dateDebut,
            $dto->dateFin
        );

        $salle = $this->salleRepository->findById($dto->salleId);

        if ($salle === null) {
            throw new InvalidArgumentException(
                'La salle demandée n’existe pas.'
            );
        }

        if (!$salle->active) {
            throw new InvalidArgumentException(
                'La salle demandée est inactive.'
            );
        }

        if ($this->reservationRepository->hasConflict(
            $dto->salleId,
            $dto->dateDebut,
            $dto->dateFin
        )) {
            throw new InvalidArgumentException(
                'La salle est déjà réservée pour cette période.'
            );
        }

        return $this->reservationRepository->create([
            'salle_id' => $dto->salleId,
            'responsable' => $dto->responsable,
            'email' => $dto->email,
            'motif' => $dto->motif,
            'date_debut' => $dto->dateDebut,
            'date_fin' => $dto->dateFin,
            'statut' => 'confirmée',
        ]);
    }
}
