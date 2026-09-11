<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\ReservationDTO;
use App\Model\Reservation;
use App\Model\Salle;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Service\ReservationService;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ReservationServiceTest extends TestCase
{
    public function testRefuseUneSalleInexistante(): void
    {
        $salleRepository = $this->createStub(SalleRepositoryInterface::class);
        $reservationRepository = $this->createMock(ReservationRepositoryInterface::class);
        $salleRepository->method('findById')->willReturn(null);
        $reservationRepository->expects($this->never())->method('hasConflict');
        $reservationRepository->expects($this->never())->method('create');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('La salle demandée n’existe pas.');

        $this->service($salleRepository, $reservationRepository)->createReservation($this->dto());
    }

    public function testRefuseUneSalleInactive(): void
    {
        $salleRepository = $this->createStub(SalleRepositoryInterface::class);
        $reservationRepository = $this->createMock(ReservationRepositoryInterface::class);
        $salleRepository->method('findById')->willReturn($this->salle(false));
        $reservationRepository->expects($this->never())->method('hasConflict');
        $reservationRepository->expects($this->never())->method('create');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('La salle demandée est inactive.');

        $this->service($salleRepository, $reservationRepository)->createReservation($this->dto());
    }

    public function testRefuseUnChevauchement(): void
    {
        $salleRepository = $this->createStub(SalleRepositoryInterface::class);
        $reservationRepository = $this->createMock(ReservationRepositoryInterface::class);
        $salleRepository->method('findById')->willReturn($this->salle());
        $reservationRepository->method('hasConflict')->willReturn(true);
        $reservationRepository->expects($this->never())->method('create');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('La salle est déjà réservée pour cette période.');

        $this->service($salleRepository, $reservationRepository)->createReservation($this->dto());
    }

    public function testUneReservationSansConflitEstCreeeAvecLesDonneesAttendues(): void
    {
        $salleRepository = $this->createStub(SalleRepositoryInterface::class);
        $reservationRepository = $this->createMock(ReservationRepositoryInterface::class);
        $reservation = $this->createStub(Reservation::class);
        $salleRepository->method('findById')->willReturn($this->salle());
        $reservationRepository->method('hasConflict')->willReturn(false);
        $reservationRepository->expects($this->once())
            ->method('create')
            ->with($this->callback(static function (array $data): bool {
                return $data['salle_id'] === 1
                    && $data['responsable'] === 'Malang'
                    && $data['email'] === 'malang@example.com'
                    && $data['motif'] === 'Cours de PHP'
                    && $data['date_debut'] instanceof DateTimeImmutable
                    && $data['date_fin'] instanceof DateTimeImmutable
                    && !array_key_exists('statut', $data);
            }))
            ->willReturn($reservation);

        self::assertSame(
            $reservation,
            $this->service($salleRepository, $reservationRepository)->createReservation($this->dto())
        );
    }

    private function service(
        SalleRepositoryInterface $salleRepository,
        ReservationRepositoryInterface $reservationRepository,
    ): ReservationService {
        return new ReservationService(
            new \App\Validation\ReservationValidator(),
            $salleRepository,
            $reservationRepository,
        );
    }

    private function dto(): ReservationDTO
    {
        $debut = new DateTimeImmutable('2035-09-10 10:00:00');

        return new ReservationDTO(
            salleId: 1,
            responsable: 'Malang',
            email: 'malang@example.com',
            motif: 'Cours de PHP',
            dateDebut: $debut,
            dateFin: $debut->modify('+1 hour'),
        );
    }

    private function salle(bool $active = true): Salle
    {
        return new Salle([
            'id' => 1,
            'nom' => 'Salle A101',
            'batiment' => 'Bâtiment A',
            'capacite' => 40,
            'type' => 'cours',
            'active' => $active,
        ]);
    }
}
