<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;

$root = dirname(__DIR__);

if (is_file($root . '/.env')) {
    Dotenv::createImmutable($root)->safeLoad();
}

$database = require $root . '/config/database.php';
/** @var Capsule $capsule */
$capsule = $database();
$connection = $capsule->getConnection();

$connection->statement(
    'CREATE TABLE IF NOT EXISTS migrations (' .
    'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,' .
    'migration VARCHAR(255) NOT NULL UNIQUE,' .
    'executed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP' .
    ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
);

$migrations = glob(__DIR__ . '/migrations/*.sql') ?: [];
sort($migrations, SORT_STRING);

$applied = $connection->table('migrations')
    ->pluck('migration')
    ->all();
$applied = array_flip($applied);

foreach ($migrations as $migrationFile) {
    $migration = basename($migrationFile);

    if (isset($applied[$migration])) {
        echo "[SKIP] {$migration}" . PHP_EOL;
        continue;
    }

    echo "[RUN ] {$migration}" . PHP_EOL;

    $sql = trim((string) file_get_contents($migrationFile));
    if ($sql === '') {
        throw new RuntimeException("Migration vide : {$migration}");
    }

    $connection->unprepared($sql);
    $connection->table('migrations')->insert(['migration' => $migration]);
}

echo "Migrations terminées." . PHP_EOL;
