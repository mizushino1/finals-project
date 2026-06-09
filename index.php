<?php
ob_start();
require_once 'config/session.php';
require_once 'config/constants.php'; // Defines BASE_URL
 
// Get the URL path from the browser
$request = $_SERVER['REQUEST_URI'];
$request = strtok($request, '?'); // Strip query strings
 
// Automatically find and strip the subfolder path from the request
$script_directory = parse_url(BASE_URL, PHP_URL_PATH); // Returns "/finals-project/"
if (strpos($request, $script_directory) === 0) {
    $request = '/' . substr($request, strlen($script_directory));
}
 
// Ensure it defaults to '/' if the request becomes empty
if (empty($request) || $request === '//') {
    $request = '/';
}
 
// ── Handle logout before any output ──
if ($request === '/logout') {
    session_destroy();
    header('Location: ' . BASE_URL . 'login');
    exit;
}
 
// Route map
$routes = [
    '/'                      => 'views/home.php',
    '/login'                 => 'views/auth/login.php',
    '/register'              => 'views/auth/register.php',
    '/commissions'           => 'views/commissions/browse.php',
    '/artists'           => 'views/profile/browse.php',
    '/commissions/create'    => 'views/commissions/create.php',
    '/commissions/detail'    => 'views/commissions/detail.php',
    '/profile'               => 'views/profile/view.php',
    '/messages'              => 'views/messages/inbox.php',
    '/payments/checkout'     => 'views/payments/checkout.php',
    '/payments/history'     => 'views/payments/history.php',
    '/admin'                 => 'views/admin/dashboard.php',
    '/settings'              => 'views/profile/settings.php',
    '/settings/edit-profile' => 'views/profile/edit.php',
    '/login/forgot-password' => 'views/auth/forgot_password.php',
    '/commissions/my-commissions' => 'views/commissions/my_commissions.php',
    '/commissions/create-commission' => 'views/commissions/create.php',
    '/commissions/edit-commission' => 'views/commissions/edit.php',
    '/commissions/delete-commission' => 'views/commissions/delete_commission.php',
    '/commissions/drafts' => 'views/commissions/drafts.php',
];
 
// Check if route exists
if (array_key_exists($request, $routes)) {
    require_once 'views/layouts/header.php';
    require_once $routes[$request];
    require_once 'views/layouts/footer.php';
} else {
    http_response_code(404);
    require_once 'views/layouts/header.php';
    require_once 'views/404.php';
    require_once 'views/layouts/footer.php';
}