<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

use App\Controller\ReservationController;
use App\Controller\SalleController;
use App\Repository\ReservationRepository;
use App\Repository\SalleRepository;
use App\Service\ReservationService;
use App\Validation\ReservationValidator;
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$database = require __DIR__ . '/../config/database.php';
$database();

$salleRepository = new SalleRepository();
$reservationRepository = new ReservationRepository();
$validator = new ReservationValidator();

$reservationService = new ReservationService(
    $validator,
    $salleRepository,
    $reservationRepository
);

$salleController = new SalleController($salleRepository);

$reservationController = new ReservationController(
    $salleRepository,
    $reservationService
);$routes = require __DIR__ . '/../routes/web.php';

$dispatcher = FastRoute\simpleDispatcher($routes);

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {

    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404 - Page introuvable';
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo '405 - Méthode HTTP non autorisée';
        break;

    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        [$controller, $method] = $handler;

        $instances = [
            SalleController::class => $salleController,
            ReservationController::class => $reservationController,
        ];

            $instance = $instances[$controller];

            $instance->$method(...array_values($vars));
        break;
}
