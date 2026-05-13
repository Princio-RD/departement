<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Auth
$routes->get('login', 'AuthController::loginForm');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

// Routes Espace employé (Congés)
$routes->group('user', ['filter' => 'auth:employe,admin,rh'], static function ($routes) {
    $routes->get('/', 'UserCongeController::new');
    $routes->get('conges/nouveau', 'UserCongeController::new');
    $routes->post('conges', 'UserCongeController::create');
});

// (stubs) Espaces RH/Admin - à compléter
$routes->group('rh', ['filter' => 'auth:rh,admin'], static function ($routes) {
    $routes->get('/', 'Home::index');
});
$routes->group('admin', ['filter' => 'auth:admin'], static function ($routes) {
    $routes->get('/', 'Home::index');
});
