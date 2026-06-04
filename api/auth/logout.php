<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/constants.php';

// Clear session variables and destroy session safely
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Check if this was requested via JavaScript Fetch/AJAX
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || 
          (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if ($isAjax) {
    // Return JSON so your JavaScript fetch code knows it worked and can redirect
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'redirect' => BASE_URL . 'login']);
    exit;
} else {
    // Fallback for traditional <a> href HTML links
    header('Location: ' . BASE_URL . 'login');
    exit;
}
?>