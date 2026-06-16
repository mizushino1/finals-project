<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

try {
    $db   = getDB();
    $role = strtolower($_SESSION['role'] ?? '');
    $sid  = (int) $_SESSION['user_id'];

    // Resolve to account_id
    if ($role === 'artist') {
        $s = $db->prepare("SELECT account_id FROM artist_tbl WHERE artist_id = ?");
    } else {
        $s = $db->prepare("SELECT account_id FROM user_tbl WHERE user_id = ?");
    }
    $s->execute([$sid]);
    $accountId = $s->fetchColumn() ?: $sid;

    // Fetch avatar (image_type_id = 1)
    $stmt = $db->prepare("
        SELECT img.image_url
        FROM image_tbl img
        LEFT JOIN user_tbl   u  ON img.user_id   = u.user_id
        LEFT JOIN artist_tbl ar ON img.artist_id  = ar.artist_id
        WHERE img.image_type_id = 1
          AND (u.account_id = ? OR ar.account_id = ?)
        ORDER BY img.uploaded_at DESC
        LIMIT 1
    ");
    $stmt->execute([$accountId, $accountId]);
    $avatarUrl = $stmt->fetchColumn();

    if ($avatarUrl) {
        echo json_encode(['success' => true, 'avatar_url' => $avatarUrl]);
    } else {
        echo json_encode(['success' => false]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false]);
}