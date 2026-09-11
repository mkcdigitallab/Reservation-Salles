<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ReservationDTO;
use App\Repository\SalleRepositoryInterface;
use App\Security\CsrfToken;
use App\Service\ReservationService;
use DateMalformedStringException;
use DateTimeImmutable;
use InvalidArgumentException;

final class ReservationController
{
    public function __construct(
        private SalleRepositoryInterface $salleRepository,
        private ReservationService $reservationService,
        private CsrfToken $csrfToken,
    ) {
    }

    public function create(): void
    {
        $salles = $this->salleRepository->findAllActive();
        $salleSelectionnee = null;

        if (isset($_GET['salle']) && filter_var($_GET['salle'], FILTER_VALIDATE_INT) !== false) {
            $salleSelectionnee = $this->salleRepository->findById((int) $_GET['salle']);
        }

        $csrfToken = $this->csrfToken->get();

        require dirname(dirname(__DIR__)) . '/templates/reservation/create.php';
    }

    public function store(): void
    {
        try {
            $token = isset($_POST['csrf_token']) && is_string($_POST['csrf_token'])
                ? $_POST['csrf_token']
                : null;

            if (!$this->csrfToken->validate($token)) {
                http_response_code(419);
                $message = 'Le formulaire a expiré ou le token de sécurité est invalide.';
                require dirname(dirname(__DIR__)) . '/templates/error/422.php';
                return;
            }

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
            require dirname(dirname(__DIR__)) . '/templates/error/422.php';
        }
    }
}
