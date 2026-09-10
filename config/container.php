<?php

declare(strict_types=1);

use App\Repository\SalleRepository;
use App\Repository\SalleRepositoryInterface;
use App\Repository\ReservationRepository;
use App\Repository\ReservationRepositoryInterface;
use DI\ContainerBuilder;
use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Container\ContainerInterface;

return static function (): ContainerInterface {
    $builder = new ContainerBuilder();

    $builder->addDefinitions([
        Capsule::class => static function (): Capsule {
            $database = require __DIR__ . '/database.php';

            return $database();
        },
        SalleRepositoryInterface::class => \DI\autowire(SalleRepository::class),
        ReservationRepositoryInterface::class => \DI\autowire(ReservationRepository::class),
    ]);

    return $builder->build();
};
