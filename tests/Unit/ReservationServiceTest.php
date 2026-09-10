<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\ReservationDTO;
use App\Model\Reservation;
use App\Model\Salle;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Service\ReservationService;
use App\Validation\ReservationValidator;
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
        $reservationRepository->expects($this->never())->method('create');

        $service = new ReservationService(
            new ReservationValidator(),
            $salleRepository,
            $reservationRepository,
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('La salle demandée n’existe pas.');

        $service->createReservation($this->dto());
    }

    public function testRefuseUneSalleInactive(): void
    {
    $salleRepository = $this->createStub(SalleRepositoryInterface::class);
    $reservationRepository = $this->createMock(ReservationRepositoryInterface::class);

        $salleRepository->method('findById')->willReturn($this->salle(false));
        $reservationRepository->expects($this->never())->method('create');

        $service = new ReservationService(
            new ReservationValidator(),
            $salleRepository,
            $reservationRepository,
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('La salle demandée est inactive.');

        $service->createReservation($this->dto());
    }

    public function testRefuseUnChevauchement(): void
    {
    $salleRepository = $this->createStub(SalleRepositoryInterface::class);
    $reservationRepository = $this->createMock(ReservationRepositoryInterface::class);

        $salleRepository->method('findById')->willReturn($this->salle());
        $reservationRepository->method('hasConflict')->willReturn(true);
        $reservationRepository->expects($this->never())->method('create');

        $service = new ReservationService(
            new ReservationValidator(),
            $salleRepository,
            $reservationRepository,
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('La salle est déjà réservée pour cette période.');

        $service->createReservation($this->dto());
    }

    public function testAutoriseDeuxReservationsAdjacentes(): void
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
                    && $data['statut'] === 'confirmée';
            }))
            ->willReturn($reservation);

        $service = new ReservationService(
            new ReservationValidator(),
            $salleRepository,
            $reservationRepository,
        );

        $dto = $this->dto(
            new DateTimeImmutable('2035-09-10 11:00:00'),
            new DateTimeImmutable('2035-09-10 12:00:00'),
        );

        self::assertSame($reservation, $service->createReservation($dto));
    }

    public function testUneReservationSansConflitEstCreee(): void
    {
        $salleRepository = $this->createStub(SalleRepositoryInterface::class);
        $reservationRepository = $this->createStub(ReservationRepositoryInterface::class);
        $reservation = $this->createStub(Reservation::class);

        $salleRepository->method('findById')->willReturn($this->salle());
        $reservationRepository->method('hasConflict')->willReturn(false);
        $reservationRepository->method('create')->willReturn($reservation);

        $service = new ReservationService(
            new ReservationValidator(),
            $salleRepository,
            $reservationRepository,
        );

        self::assertSame($reservation, $service->createReservation($this->dto()));
    }

    private function dto(
        ?DateTimeImmutable $debut = null,
        ?DateTimeImmutable $fin = null,
    ): ReservationDTO {
        $debut ??= new DateTimeImmutable('2035-09-10 10:00:00');
        $fin ??= new DateTimeImmutable('2035-09-10 11:00:00');

        return new ReservationDTO(
            salleId: 1,
            responsable: 'Malang',
            email: 'malang@example.com',
            motif: 'Cours de PHP',
            dateDebut: $debut,
            dateFin: $fin,
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
