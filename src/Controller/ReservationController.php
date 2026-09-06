<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ReservationDTO;
use App\Repository\SalleRepository;
use App\Service\ReservationService;
use DateMalformedStringException;
use DateTimeImmutable;
use InvalidArgumentException;

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

        if (isset($_GET['salle']) && filter_var($_GET['salle'], FILTER_VALIDATE_INT) !== false) {
            $salleSelectionnee = $this->salleRepository->findById((int) $_GET['salle']);
        }

        require __DIR__ . '/../../templates/reservation/create.php';
    }

    public function store(): void
    {
        try {
            $salleId = filter_input(INPUT_POST, 'salle_id', FILTER_VALIDATE_INT);
            $responsable = trim((string) ($_POST['responsable'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $motif = trim((string) ($_POST['motif'] ?? ''));
            $dateDebut = trim((string) ($_POST['date_debut'] ?? ''));
            $dateFin = trim((string) ($_POST['date_fin'] ?? ''));

            if ($salleId === false || $salleId === null || $dateDebut === '' || $dateFin === '') {
                throw new InvalidArgumentException('Tous les champs obligatoires doivent être renseignés.');
            }

            $dto = new ReservationDTO(
                salleId: $salleId,
                responsable: $responsable,
                email: $email,
                motif: $motif,
                dateDebut: new DateTimeImmutable($dateDebut),
                dateFin: new DateTimeImmutable($dateFin),
            );

            $this->reservationService->createReservation($dto);

            header('Location: /');
            exit;
        } catch (InvalidArgumentException | DateMalformedStringException $exception) {
            http_response_code(422);
            $message = $exception->getMessage();
            require __DIR__ . '/../../templates/error/422.php';
        }
    }
}
