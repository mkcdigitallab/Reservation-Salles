<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;
use Illuminate\Database\Eloquent\Collection;

interface SalleRepositoryInterface
{
    /**
     * @return Collection<int, Salle>
     */
    public function findAllActive(): Collection;

    public function findById(int $id): ?Salle;
}