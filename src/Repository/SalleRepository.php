<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;
use Illuminate\Database\Eloquent\Collection;

final class SalleRepository implements SalleRepositoryInterface
{
    /**
     * @return Collection<int, Salle>
     */
    public function findAllActive(): Collection
    {
        return Salle::query()
            ->where('active', true)
            ->orderBy('nom')
            ->get();
    }

    public function findById(int $id): ?Salle
    {
        return Salle::query()->find($id);
    }
}

