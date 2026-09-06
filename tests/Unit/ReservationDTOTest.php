<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\ReservationDTO;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class ReservationDTOTest extends TestCase
{
    public function testLeDtoConserveLesDonneesDeReservation(): void
    {
        $debut = new DateTimeImmutable('+1 day 10:00');
        $fin = $debut->modify('+2 hours');

        $dto = new ReservationDTO(
            salleId: 3,
            responsable: 'Malang Kiya Cissé',
            email: 'malang@example.com',
            motif: 'Réunion',
            dateDebut: $debut,
            dateFin: $fin,
        );

        self::assertSame(3, $dto->salleId);
        self::assertSame('Malang Kiya Cissé', $dto->responsable);
        self::assertSame('malang@example.com', $dto->email);
        self::assertSame('Réunion', $dto->motif);
        self::assertSame($debut, $dto->dateDebut);
        self::assertSame($fin, $dto->dateFin);
    }
}
