<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;
use App\Service\AnnulerReservationService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class AnnulerReservationServiceTest extends TestCase
{
    public function testRefuseUneReservationInexistante(): void
    {
        $repository = $this->createMock(ReservationRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('findById')
            ->with(12)
            ->willReturn(null);
        $repository->expects($this->never())->method('cancel');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('La réservation demandée n’existe pas.');

        $this->service($repository)->cancel(12);
    }

    public function testRefuseUneReservationDejaAnnulee(): void
    {
        $repository = $this->createMock(ReservationRepositoryInterface::class);
        $reservation = new Reservation(['id' => 12, 'statut' => 'annulée']);
        $repository->method('findById')->willReturn($reservation);
        $repository->expects($this->never())->method('cancel');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('La réservation est déjà annulée.');

        $this->service($repository)->cancel(12);
    }

    public function testAnnuleUneReservationConfirmee(): void
    {
        $repository = $this->createMock(ReservationRepositoryInterface::class);
        $reservation = new Reservation(['id' => 12, 'statut' => 'confirmée']);
        $repository->method('findById')->willReturn($reservation);
        $repository->expects($this->once())
            ->method('cancel')
            ->with($reservation)
            ->willReturn($reservation);

        self::assertSame($reservation, $this->service($repository)->cancel(12));
    }

    private function service(ReservationRepositoryInterface $repository): AnnulerReservationService
    {
        return new AnnulerReservationService($repository);
    }
}
