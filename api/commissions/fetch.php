<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$db   = getDB();
$role = $_SESSION['role'];
$id   = $_SESSION['user_id'];

// Artist sees all open commission posts from users
if ($role === 'artist') {
    $stmt = $db->prepare('
        SELECT c.*, u.username AS posted_by
        FROM commission_tbl c
        JOIN user_tbl u ON c.user_id = u.account_id
        WHERE c.status = "open"
        ORDER BY c.commission_date DESC
    ');
    $stmt->execute([]);

// User sees their own posts and their status
} elseif ($role === 'user') {
    $stmt = $db->prepare('
        SELECT c.*
        FROM commission_tbl c
        WHERE c.user_id = ?
        ORDER BY c.commission_date DESC
    ');
    $stmt->execute([$id]);

// Admin sees everything
} elseif ($role === 'admin') {
    $stmt = $db->prepare('
        SELECT c.*, u.username AS posted_by
        FROM commission_tbl c
        JOIN user_tbl u ON c.user_id = u.account_id
        ORDER BY c.commission_date DESC
    ');
    $stmt->execute([]);
}

echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);