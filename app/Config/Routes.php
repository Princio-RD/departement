<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('user', static function ($routes) {
    $routes->get('conges/nouveau', 'UserCongeController::new');

  $routes->get('conges', 'UserCongeController::index');

    $routes->post('conges', 'UserCongeController::create');
    $routes->post('conges/(:num)/annuler', 'UserCongeController::cancel/$1');
});
