<?php
require_once __DIR__ . '/constants.php';

if (session_status() === PHP_SESSION_NONE) {
    // 1. Resolve path explicitly to avoid directory-scoped session splitting
    $cookiePath = parse_url(BASE_URL, PHP_URL_PATH);
    if (empty($cookiePath)) {
        $cookiePath = '/';
    }

    // 2. Reliable HTTPS Detection (Accounting for Local environments & Reverse Proxies)
    $isSecure = false;
    if (
        isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] == 1) ||
        isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443 ||
        isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
    ) {
        $isSecure = true;
    }

    // 3. Deploy Immutable Security Flags
    session_set_cookie_params([
        'lifetime' => 0,              // Session cookie expires when the browser closes
        'path'     => $cookiePath,     // Scoped to the application root path
        'domain'   => '',              // Defaults to current host domain
        'secure'   => $isSecure,       // Enforced if SSL connection is detected
        'httponly' => true,            // Mitigates XSS cookie theft risks
        'samesite' => 'Strict',        // Defends against Cross-Site Request Forgery (CSRF)
    ]);

    // 4. Start the engine safely
    session_start();
}