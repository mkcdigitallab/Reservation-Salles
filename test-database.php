<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$database = require __DIR__ . '/config/database.php';

try {
    $capsule = $database();

    $connection = $capsule->getConnection();

    $connection->getPdo();

    echo "Connexion MySQL réussie !" . PHP_EOL;
} catch (Throwable $e) {
    echo "Erreur de connexion : " . $e->getMessage() . PHP_EOL;
}
