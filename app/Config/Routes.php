<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Racine du site -> login (supporte aussi /index.php)
$routes->get('/', static fn() => redirect()->to('/login'));
$routes->get('index.php', static fn() => redirect()->to('/login'));

// Auth
$routes->get('login', 'AuthController::loginForm');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');



$routes->group('user', ['filter' => 'auth:employe,admin,rh'], static function ($routes) {
    $routes->get('/', 'UserCongeController::new');

    // Dashboard employé
    $routes->get('dashboard', 'UserDashboardController::index');
    $routes->get('calendrier', 'UserDashboardController::calendar');

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
    $routes->get('dashboard', 'AdminController::dashboard');
    $routes->get('statistiques', 'AdminController::statistiques');
    $routes->get('employes', 'AdminController::employes');
    $routes->get('create-employe', static fn() => redirect()->to('/admin/employes#ajout-employe'));
    $routes->post('create-employe', 'AdminController::createEmploye');
    $routes->post('employes', 'AdminController::createEmploye');
    $routes->get('employes/(:num)/edit', 'AdminController::editEmploye/$1');
    $routes->post('employes/(:num)/edit', 'AdminController::updateEmploye/$1');
    $routes->get('employes/(:num)/toggle', 'AdminController::toggleEmploye/$1');
    $routes->get('toggle-employe/(:num)', 'AdminController::toggleEmploye/$1');

    // Édition des départements, types et soldes
    $routes->get('departements/(:num)/edit', 'AdminController::editDepartement/$1');
    $routes->post('departements/(:num)/edit', 'AdminController::updateDepartement/$1');
    $routes->get('types-conge/(:num)/edit', 'AdminController::editTypeConge/$1');
    $routes->post('types-conge/(:num)/edit', 'AdminController::updateTypeConge/$1');
    $routes->get('soldes/(:num)/edit', 'AdminController::editSolde/$1');
    $routes->post('soldes/(:num)/edit', 'AdminController::updateSolde/$1');

    // Autres pages admin
    $routes->get('departements', 'AdminController::departements');
    $routes->get('types-conge', 'AdminController::typesConge');
    $routes->get('soldes', 'AdminController::soldes');
});
