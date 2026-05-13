<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Routes Espace employé (Congés)
$routes->group('user', static function ($routes) {
    $routes->get('conges/nouveau', 'UserCongeController::new');
    $routes->post('conges', 'UserCongeController::create');
});
