<?php

declare(strict_types=1);

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
    ]);

    return $builder->build();
};
