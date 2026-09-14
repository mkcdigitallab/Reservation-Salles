<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;

return static function (): Capsule {
    $env = static function (string $key): string {
        $value = $_ENV[$key] ?? getenv($key);

        if ($value === false || $value === null || $value === '') {
            throw new RuntimeException("Variable d'environnement manquante : {$key}");
        }

        return (string) $value;
    };

    $capsule = new Capsule();

    $capsule->addConnection([
        'driver' => $env('DB_DRIVER'),
        'host' => $env('DB_HOST'),
        'port' => $env('DB_PORT'),
        'database' => $env('DB_DATABASE'),
        'username' => $env('DB_USERNAME'),
        'password' => $env('DB_PASSWORD'),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
    ]);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};
