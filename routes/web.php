<?php

declare(strict_types=1);

use FastRoute\RouteCollector;

return static function (RouteCollector $router): void {

    $router->addRoute(
        'GET',
        '/',
        ['App\Controller\SalleController', 'index']
    );

    $router->addRoute(
        'GET',
        '/reservations/create',
        ['App\Controller\ReservationController', 'create']
    );

    $router->addRoute(
        'POST',
        '/reservations',
        ['App\Controller\ReservationController', 'store']
    );

    $router->addRoute(
        'POST',
        '/reservations/{id:\\d+}/annuler',
        ['App\Controller\ReservationController', 'cancel']
    );
};
