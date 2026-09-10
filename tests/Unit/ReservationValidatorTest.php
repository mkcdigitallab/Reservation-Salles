<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Validation\ReservationValidator;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ReservationValidatorTest extends TestCase
{
    private ReservationValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new ReservationValidator();
    }

    public function testReservationValide(): void
    {
        $debut = new DateTimeImmutable('+1 day 10:00');
        $fin = $debut->modify('+2 hours');

        $this->validator->validate(
            'Malang Kiya Cissé',
            'malang@example.com',
            'Cours de PHP',
            $debut,
            $fin
        );

        $this->expectNotToPerformAssertions();
    }

    public function testResponsableObligatoire(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->validator->validate(
            '',
            'malang@example.com',
            'Cours',
            new DateTimeImmutable('+1 day'),
            new DateTimeImmutable('+1 day 2 hours')
        );
    }

    public function testEmailInvalide(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->validator->validate(
            'Malang',
            'email-invalide',
            'Cours',
            new DateTimeImmutable('+1 day'),
            new DateTimeImmutable('+1 day 2 hours')
        );
    }

    public function testMotifTropCourtRefuse(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('entre 5 et 255 caractères');

        $debut = new DateTimeImmutable('+1 day 10:00');

        $this->validator->validate(
            'Malang',
            'malang@example.com',
            'PHP',
            $debut,
            $debut->modify('+1 hour')
        );
    }

    public function testMotifTropLongRefuse(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('entre 5 et 255 caractères');

        $debut = new DateTimeImmutable('+1 day 10:00');

        $this->validator->validate(
            'Malang',
            'malang@example.com',
            str_repeat('A', 256),
            $debut,
            $debut->modify('+1 hour')
        );
    }

    public function testDureeInferieureATrenteMinutesRefusee(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('au moins 30 minutes');

        $debut = new DateTimeImmutable('+1 day 10:00');

        $this->validator->validate(
            'Malang',
            'malang@example.com',
            'Cours de PHP',
            $debut,
            $debut->modify('+29 minutes')
        );
    }

    public function testDureeSuperieureAQuatreHeuresRefusee(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $debut = new DateTimeImmutable('+1 day 10:00');

        $this->validator->validate(
            'Malang',
            'malang@example.com',
            'Cours de PHP',
            $debut,
            $debut->modify('+5 hours')
        );
    }

    public function testDateDeDebutDansLePasseRefusee(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $debut = new DateTimeImmutable('-1 hour');

        $this->validator->validate(
            'Malang',
            'malang@example.com',
            'Cours de PHP',
            $debut,
            $debut->modify('+2 hours')
        );
    }

    public function testDateDeFinDoitEtreApresLeDebut(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $debut = new DateTimeImmutable('+1 day 10:00');

        $this->validator->validate(
            'Malang',
            'malang@example.com',
            'Cours de PHP',
            $debut,
            $debut
        );
    }
}
