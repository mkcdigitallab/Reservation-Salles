<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use DateTimeInterface;

final class ReservationRepository implements ReservationRepositoryInterface
{
    public function create(array $data): Reservation
    {
        return Reservation::query()->create($data);
    }

    public function findById(int $id): ?Reservation
    {
        return Reservation::query()->find($id);
    }

    public function cancel(Reservation $reservation): Reservation
    {
        $reservation->statut = 'annulée';
        $reservation->save();

        return $reservation;
    }

    public function hasConflict(
        int $salleId,
        DateTimeInterface $dateDebut,
        DateTimeInterface $dateFin
    ): bool {
        return Reservation::query()
            ->where('salle_id', $salleId)
            ->where('statut', 'confirmée')
            ->where('date_debut', '<', $dateFin)
            ->where('date_fin', '>', $dateDebut)
            ->exists();
    }
}
