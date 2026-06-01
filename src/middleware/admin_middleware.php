<?php
require_once __DIR__ . '/auth_middleware.php';

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    require_once __DIR__ . '/../../views/403.php';
    exit;
}