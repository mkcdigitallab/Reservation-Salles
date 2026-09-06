<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Repository\SalleRepository;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$database = require __DIR__ . '/../config/database.php';
$database();

$salleRepository = new SalleRepository();

$salles = $salleRepository->findAllActive();

require __DIR__ . '/../templates/salle/index.php';
