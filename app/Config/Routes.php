<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');


$routes->get('login', 'AuthController::loginForm');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');


$routes->group('user', ['filter' => 'auth:employe,admin,rh'], static function ($routes) {
    $routes->get('/', 'UserCongeController::new');
    $routes->get('conges/nouveau', 'UserCongeController::new');
    $routes->post('conges', 'UserCongeController::create');
});

$routes->group('rh', ['filter' => 'auth:rh,admin'], static function ($routes) {
    // Support de la redirection post-login vers /rh
    $routes->get('/', 'RhController::index');
    $routes->get('dashboard', 'RhController::dashboard');
    $routes->post('demandes/(:num)/approve', 'RhController::approve/$1');
    $routes->post('demandes/(:num)/refuse', 'RhController::refuse/$1');
});

$routes->group('admin', ['filter' => 'auth:admin'], static function ($routes) {
    // Support de la redirection post-login vers /admin
    $routes->get('/', 'AdminController::dashboard');
    $routes->get('employes', 'AdminController::employes');
    $routes->post('employes', 'AdminController::createEmploye');
    $routes->post('employes/(:num)/toggle', 'AdminController::toggleEmploye/$1');
});
