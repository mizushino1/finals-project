<?php
require_once __DIR__ . '/auth_middleware.php';

if ($_SESSION['role'] !== 'user') {
    http_response_code(403);
    
    // 1. Try to use a server-side file path constant if you have one defined (e.g., ROOT_PATH)
    if (defined('ROOT_PATH')) {
        $path = ROOT_PATH . '/views/403.php';
    } else {
        // 2. Fallback to a precise absolute path calculation via realpath()
        // This calculates the real file-system location up two directories
        $path = realpath(__DIR__ . '/../../views/403.php');
    }

    // Verify the file system actually sees the file before trying to require it
    if ($path && file_exists($path)) {
        require_once $path;
    } else {
        // Emergency text fallback if the file path is completely lost
        echo "<h1>403 Forbidden</h1><p>You do not have permission to access this resource.</p>";
    }
    exit;
}