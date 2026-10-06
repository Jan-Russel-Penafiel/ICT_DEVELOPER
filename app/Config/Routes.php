<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('id', 'Home::id');

// Keep Shield's named magic-link route available for compatibility with any
// cached/overridden login view. The Auth config keeps it hidden by default.
service('auth')->routes($routes, ['except' => ['register']]);

$routes->get('register/(:segment)', 'PublicPortal::register/$1');
$routes->post('register/(:segment)', 'PublicPortal::storeRegistration/$1');
$routes->get('track', 'PublicPortal::track');
$routes->post('track', 'PublicPortal::track');
$routes->get('reset-request', 'PublicPortal::resetRequest');
$routes->post('reset-request', 'PublicPortal::storeResetRequest');
$routes->get('repair-request', 'PublicPortal::repairRequest');
$routes->post('repair-request', 'PublicPortal::storeRepairRequest');
$routes->get('repair-request/(:segment)/print', 'PublicPortal::printRepair/$1');

$routes->group('staff', ['filter' => 'auth'], static function ($routes) {
    $routes->get('', 'Staff\DashboardController::index');
    $routes->get('links', 'Staff\IdController::links', ['filter' => 'group:admin']);
    $routes->post('links', 'Staff\IdController::createLink', ['filter' => 'group:admin']);
    $routes->post('links/(:num)/edit', 'Staff\IdController::updateLink/$1', ['filter' => 'group:admin']);
    $routes->post('links/(:num)/delete', 'Staff\IdController::deleteLink/$1', ['filter' => 'group:admin']);
    $routes->get('links/(:num)/qr', 'Staff\IdController::qr/$1', ['filter' => 'group:admin,registrar']);
    $routes->get('ids', 'Staff\IdController::index', ['filter' => 'group:admin,ict,registrar']);
    $routes->post('ids/(:num)/status', 'Staff\IdController::changeStatus/$1', ['filter' => 'group:admin,ict,registrar']);
    $routes->post('ids/(:num)/edit', 'Staff\IdController::updateRequest/$1', ['filter' => 'group:admin,ict']);
    $routes->post('ids/(:num)/delete', 'Staff\IdController::deleteRequest/$1', ['filter' => 'group:admin']);
    $routes->get('ids/export', 'Staff\IdController::export', ['filter' => 'group:admin,ict']);
    $routes->get('replacements', 'Staff\IdController::replacements', ['filter' => 'group:admin,ict,cashier']);
    $routes->post('replacements', 'Staff\IdController::recordReplacement', ['filter' => 'group:admin,cashier']);
    $routes->post('replacements/(:num)/edit', 'Staff\IdController::updateReplacement/$1', ['filter' => 'group:admin,cashier']);
    $routes->post('replacements/(:num)/delete', 'Staff\IdController::deleteRequest/$1', ['filter' => 'group:admin']);
    $routes->get('accounts', 'Staff\AccountsController::index', ['filter' => 'group:admin,ict']);
    $routes->post('accounts/import', 'Staff\AccountsController::import', ['filter' => 'group:admin,ict']);
    $routes->get('accounts/import-template', 'Staff\AccountsController::downloadTemplate', ['filter' => 'group:admin,ict']);
    $routes->get('accounts/(:num)/export', 'Staff\AccountsController::export/$1', ['filter' => 'group:admin,ict']);
    $routes->post('accounts/(:num)/edit', 'Staff\AccountsController::updateBatch/$1', ['filter' => 'group:admin,ict']);
    $routes->post('accounts/(:num)/delete', 'Staff\AccountsController::deleteBatch/$1', ['filter' => 'group:admin']);
    $routes->get('users', 'Staff\StaffAccountsController::index', ['filter' => 'group:admin']);
    $routes->post('users', 'Staff\StaffAccountsController::create', ['filter' => 'group:admin']);
    $routes->post('users/(:num)/edit', 'Staff\StaffAccountsController::update/$1', ['filter' => 'group:admin']);
    $routes->post('users/(:num)/delete', 'Staff\StaffAccountsController::delete/$1', ['filter' => 'group:admin']);
    $routes->get('resets', 'Staff\AccountsController::resets', ['filter' => 'group:admin,ict']);
    $routes->post('resets/(:num)/status', 'Staff\AccountsController::changeReset/$1', ['filter' => 'group:admin,ict']);
    $routes->post('resets/(:num)/edit', 'Staff\AccountsController::updateReset/$1', ['filter' => 'group:admin,ict']);
    $routes->post('resets/(:num)/delete', 'Staff\AccountsController::deleteReset/$1', ['filter' => 'group:admin']);
    $routes->get('repairs', 'Staff\RepairsController::index', ['filter' => 'group:admin,ict']);
    $routes->post('repairs/(:num)/status', 'Staff\RepairsController::changeStatus/$1', ['filter' => 'group:admin,ict']);
    $routes->post('repairs/(:num)/edit', 'Staff\RepairsController::update/$1', ['filter' => 'group:admin,ict']);
    $routes->post('repairs/(:num)/delete', 'Staff\RepairsController::delete/$1', ['filter' => 'group:admin']);
    $routes->get('settings', 'Staff\DashboardController::settings', ['filter' => 'group:admin']);
    $routes->post('settings', 'Staff\DashboardController::saveSettings', ['filter' => 'group:admin']);
});
