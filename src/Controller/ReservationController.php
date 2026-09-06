<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ReservationDTO;
use App\Repository\SalleRepository;
use App\Service\ReservationService;
use DateTimeImmutable;

final class ReservationController
{
    public function __construct(
        private SalleRepository $salleRepository,
        private ReservationService $reservationService
    ) {
    }

    public function create(): void
    {
        $salles = $this->salleRepository->findAllActive();

        $salleSelectionnee = null;

        if (isset($_GET['salle'])) {
            $salleSelectionnee = $this->salleRepository->findById(
                (int) $_GET['salle']
            );
        }

        require __DIR__ . '/../../templates/reservation/create.php';
    }

    public function store(): void
    {
        $dto = new ReservationDTO(
            salleId: (int) $_POST['salle_id'],
            responsable: trim($_POST['responsable']),
            email: trim($_POST['email']),
            motif: trim($_POST['motif']),
            dateDebut: new DateTimeImmutable($_POST['date_debut']),
            dateFin: new DateTimeImmutable($_POST['date_fin']),
        );

        $this->reservationService->createReservation($dto);

        header('Location: /');
        exit;
    }
}
