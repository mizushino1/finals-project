<?php
require_once 'config/session.php';
require_once 'config/constants.php';

// Get the URL path
$request = $_SERVER['REQUEST_URI'];
$request = strtok($request, '?'); // strip query strings

// Route map
$routes = [
    '/'                     => 'views/home.php',
    '/login'                => 'views/auth/login.php',
    '/register'             => 'views/auth/register.php',
    '/commissions'          => 'views/commissions/browse.php',
    '/commissions/create'   => 'views/commissions/create.php',
    '/commissions/detail'   => 'views/commissions/detail.php',
    '/profile'              => 'views/profile/view.php',
    '/messages'             => 'views/messages/inbox.php',
    '/payments/checkout'    => 'views/payments/checkout.php',
    '/admin'                => 'views/admin/dashboard.php',
];

// Check if route exists
if (array_key_exists($request, $routes)) {
    require_once 'views/layouts/header.php';
    require_once $routes[$request];
    require_once 'views/layouts/footer.php';
} else {
    // 404
    http_response_code(404);
    require_once 'views/layouts/header.php';
    require_once 'views/404.php';
    require_once 'views/layouts/footer.php';
}