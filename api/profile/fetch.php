<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$db   = getDB();
$role = strtolower($_SESSION['role']);
$id   = $_SESSION['user_id']; // This contains ID 4 (the user_id/artist_id)

try {
    if ($role === 'user') {
        $stmt = $db->prepare('
            SELECT
                a.account_id, a.username, a.email, a.phone, a.first_name, a.middle_name, a.last_name,
                a.account_status_id, ast.status_name AS account_status, a.creation_date,
                u.user_id, u.card_number, img.image_url AS avatar_url
            FROM account_tbl a
            JOIN account_status_tbl ast ON a.account_status_id = ast.account_status_id
            JOIN user_tbl u             ON a.account_id = u.account_id
            LEFT JOIN image_tbl img     ON img.user_id = u.user_id AND img.image_type_id = 1
            WHERE u.user_id = ?
        '); // Fixed target
        $stmt->execute([$id]);

    } elseif ($role === 'artist') {
        $stmt = $db->prepare('
            SELECT
                a.account_id, a.username, a.email, a.phone, a.first_name, a.middle_name, a.last_name,
                a.account_status_id, ast.status_name AS account_status, a.creation_date,
                art.artist_id, art.starting_rate, art.is_available, art.artist_description,
                img.image_url AS avatar_url
            FROM account_tbl a
            JOIN account_status_tbl ast ON a.account_status_id = ast.account_status_id
            JOIN artist_tbl art         ON a.account_id = art.account_id
            LEFT JOIN image_tbl img     ON img.artist_id = art.artist_id AND img.image_type_id = 1
            WHERE art.artist_id = ?
        '); // Fixed target
        $stmt->execute([$id]);

    } elseif ($role === 'admin') {
        $stmt = $db->prepare('
            SELECT
                a.account_id, a.username, a.email, a.first_name, a.last_name,
                ast.status_name AS account_status, a.creation_date
            FROM account_tbl a
            JOIN account_status_tbl ast ON a.account_status_id = ast.account_status_id
            JOIN administrator_tbl adm  ON a.account_id = adm.account_id
            WHERE adm.admin_id = ?
        '); // Fixed target
        $stmt->execute([$id]);
    }

    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($profile) {
        $profile['role'] = $role;
        echo json_encode(['success' => true, 'data' => $profile]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Profile not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}