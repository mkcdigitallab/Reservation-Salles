<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\SalleRepository;

final class SalleController
{
    public function __construct(
        private SalleRepository $salleRepository
    ) {
    }

    public function index(): void
    {
        $salles = $this->salleRepository->findAllActive();

        require __DIR__ . '/../../templates/salle/index.php';
    }
}
