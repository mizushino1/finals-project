<?php
ob_start(); // buffer any accidental output
require_once '../../config/session.php';
require_once '../../config/database.php';
ob_clean(); // discard anything that slipped out
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

try {
    $db   = getDB();
    $role = strtolower($_SESSION['role']);
    $id   = $_SESSION['user_id']; // user_tbl.user_id (or artist_id for artists)

    // 1. ARTIST: Sees all open (status_id = 1) commission posts
    if ($role === 'artist') {
        $stmt = $db->prepare('
            SELECT
                c.*,
                a.username  AS posted_by,
                a.first_name,
                a.last_name,
                cat.category_name,
                img.image_url AS client_avatar_url
            FROM commission_tbl c
            JOIN user_tbl      u   ON c.user_id     = u.user_id
            JOIN account_tbl   a   ON u.account_id  = a.account_id
            LEFT JOIN category_tbl cat ON c.category_id = cat.category_id
            LEFT JOIN image_tbl    img ON img.user_id   = u.user_id AND img.image_type_id = 1
            WHERE c.status_id = 1
            ORDER BY c.commission_date DESC
        ');
        $stmt->execute();

    // 2. USER/CLIENT: Sees only their own commissions (all statuses)
    } elseif ($role === 'user' || $role === 'client') {
        $stmt = $db->prepare('
            SELECT
                c.*,
                a.username  AS posted_by,
                a.first_name,
                a.last_name,
                cat.category_name,
                img.image_url AS client_avatar_url
            FROM commission_tbl c
            JOIN user_tbl      u   ON c.user_id     = u.user_id
            JOIN account_tbl   a   ON u.account_id  = a.account_id
            LEFT JOIN category_tbl cat ON c.category_id = cat.category_id
            LEFT JOIN image_tbl    img ON img.user_id   = u.user_id AND img.image_type_id = 1
            WHERE u.user_id = ?
            ORDER BY c.commission_date DESC
        ');
        $stmt->execute([$id]);

    // 3. ADMIN: Sees everything
    } elseif ($role === 'admin') {
        $stmt = $db->prepare('
            SELECT
                c.*,
                a.username  AS posted_by,
                a.first_name,
                a.last_name,
                cat.category_name,
                img.image_url AS client_avatar_url
            FROM commission_tbl c
            JOIN user_tbl      u   ON c.user_id     = u.user_id
            JOIN account_tbl   a   ON u.account_id  = a.account_id
            LEFT JOIN category_tbl cat ON c.category_id = cat.category_id
            LEFT JOIN image_tbl    img ON img.user_id   = u.user_id AND img.image_type_id = 1
            ORDER BY c.commission_date DESC
        ');
        $stmt->execute();

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid account role.']);
        exit;
    }

    $commissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data'    => $commissions ?: []
    ]);

} catch (PDOException $e) {
    error_log('PDO ERROR: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database operation failed: ' . $e->getMessage()
    ]);
}