<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Racine du site -> login (supporte aussi /index.php)
$routes->get('/', static fn () => redirect()->to('/login'));
$routes->get('index.php', static fn () => redirect()->to('/login'));

// Auth
$routes->get('login', 'AuthController::loginForm');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');



$routes->group('user', ['filter' => 'auth:employe,admin,rh'], static function ($routes) {
    $routes->get('/', 'UserCongeController::new');

    // Dashboard employé
    $routes->get('dashboard', 'UserDashboardController::index');

    // Demandes
    $routes->get('conges', 'UserCongeController::index');
    $routes->get('conges/nouveau', 'UserCongeController::new');
    $routes->post('conges', 'UserCongeController::create');
    $routes->post('conges/(:num)/cancel', 'UserCongeController::cancel/$1');

    // Profil
    $routes->get('profil', 'UserProfileController::index');
    $routes->post('profil/password', 'UserProfileController::updatePassword');
});

$routes->group('rh', ['filter' => 'auth:rh,admin'], static function ($routes) {
    // Support de la redirection post-login vers /rh
    $routes->get('/', 'RhController::index');
    $routes->get('dashboard', 'RhController::dashboard');
    $routes->get('historique', 'RhController::historique');
    $routes->get('soldes', 'RhController::soldes');
    $routes->post('demandes/(:num)/approve', 'RhController::approve/$1');
    $routes->post('demandes/(:num)/refuse', 'RhController::refuse/$1');
});

$routes->group('admin', ['filter' => 'auth:admin'], static function ($routes) {
    // Support de la redirection post-login vers /admin
    $routes->get('/', 'AdminController::dashboard');
    $routes->get('employes', 'AdminController::employes');
    $routes->post('employes', 'AdminController::createEmploye');
    $routes->get('employes/(:num)/toggle', 'AdminController::toggleEmploye/$1');

    // Autres pages admin
    $routes->get('validation-rh', 'AdminController::validationRh');
    $routes->get('departements', 'AdminController::departements');
    $routes->get('types-conge', 'AdminController::typesConge');
    $routes->get('soldes', 'AdminController::soldes');
});
