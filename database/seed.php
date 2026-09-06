<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Model\Salle;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$database = require __DIR__ . '/../config/database.php';
$database();

$salles = [
    ['nom' => 'Salle A101', 'batiment' => 'Bâtiment A', 'capacite' => 30, 'type' => 'cours', 'active' => true],
    ['nom' => 'Salle Informatique 1', 'batiment' => 'Bâtiment B', 'capacite' => 25, 'type' => 'informatique', 'active' => true],
    ['nom' => 'Laboratoire 1', 'batiment' => 'Bâtiment C', 'capacite' => 20, 'type' => 'laboratoire', 'active' => true],
    ['nom' => 'Amphithéâtre Central', 'batiment' => 'Bâtiment A', 'capacite' => 150, 'type' => 'amphitheatre', 'active' => true],
    ['nom' => 'Salle de Réunion', 'batiment' => 'Bâtiment D', 'capacite' => 12, 'type' => 'reunion', 'active' => true],
];

foreach ($salles as $salle) {
    Salle::query()->updateOrCreate(
        ['nom' => $salle['nom']],
        $salle
    );
}

echo count($salles) . " salles initialisées avec succès." . PHP_EOL;
