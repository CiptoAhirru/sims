<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Auth::login', ['filter' => 'guest']);
$routes->post('actionlogin', 'Auth::actionlogin', ['filter' => 'guest']);
$routes->get('/register', 'Auth::register', ['filter' => 'guest']);
$routes->post('auth/registrasi', 'Auth::registrasi', ['filter' => 'guest']);

$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);
$routes->get('/profile', 'Dashboard::profile', ['filter' => 'auth']);
$routes->post('edit-profile', 'Dashboard::editProfile', ['filter' => 'auth']);
$routes->get('/topup', 'Dashboard::topup', ['filter' => 'auth']);
$routes->post('actiontopup', 'Dashboard::actiontopup', ['filter' => 'auth']);
$routes->get('/transaction', 'Dashboard::transaction', ['filter' => 'auth']);
$routes->get('/transactions/loadMore', 'Dashboard::loadMore', ['filter' => 'auth']);
$routes->post('actiontransaksi', 'Dashboard::actiontransaksi', ['filter' => 'auth']);
$routes->post('logout', 'Auth::logout', ['filter' => 'auth']);
