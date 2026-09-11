<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\SalleRepositoryInterface;

final class SalleController
{
    public function __construct(
        private SalleRepositoryInterface $salleRepository
    ) {
    }

    public function index(): void
    {
        $salles = $this->salleRepository->findAllActive();

        require dirname(dirname(__DIR__)) . '/templates/salle/index.php';
    }
}
