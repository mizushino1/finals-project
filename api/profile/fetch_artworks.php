<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

// account_id of the profile being viewed (passed as ?account_id=X)
$account_id = intval($_GET['account_id'] ?? 0);
if (!$account_id) {
    echo json_encode(['success' => false, 'message' => 'account_id is required']);
    exit;
}

$page     = max(1, intval($_GET['page'] ?? 1));
$per_page = max(1, min(50, intval($_GET['per_page'] ?? 12)));
$offset   = ($page - 1) * $per_page;

try {
    $db = getDB();

    // Resolve artist_id and user_id for this account
    $stmt = $db->prepare('
        SELECT
            art.artist_id,
            u.user_id
        FROM account_tbl a
        LEFT JOIN artist_tbl art ON a.account_id = art.account_id
        LEFT JOIN user_tbl   u   ON a.account_id = u.account_id
        WHERE a.account_id = ?
    ');
    $stmt->execute([$account_id]);
    $ids = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ids) {
        echo json_encode(['success' => false, 'message' => 'Account not found']);
        exit;
    }

    $artist_id = $ids['artist_id'];
    $user_id   = $ids['user_id'];

    if (!$artist_id && !$user_id) {
        echo json_encode(['success' => true, 'data' => [], 'total' => 0, 'page' => $page, 'per_page' => $per_page]);
        exit;
    }

    // Build WHERE clause – match either ownership column depending on account type
    // image_type_id = 2 → 'Artwork'
    $conditions = [];
    $params     = [];

    if ($artist_id) {
        $conditions[] = 'img.artist_id = ?';
        $params[]     = $artist_id;
    }
    if ($user_id) {
        $conditions[] = 'img.user_id = ?';
        $params[]     = $user_id;
    }

    $where = '(' . implode(' OR ', $conditions) . ') AND img.image_type_id = 2';

    // Total count for pagination
    $countStmt = $db->prepare("SELECT COUNT(*) FROM image_tbl img WHERE {$where}");
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();

    // Paginated artworks
    $fetchParams   = array_merge($params, [$per_page, $offset]);
    $artworkStmt = $db->prepare("
    SELECT
        img.image_id,
        img.image_url,
        img.uploaded_at,
        a.username,
        a.account_id,
        COALESCE(art.title, 'Untitled')  AS title,
        art.description
    FROM image_tbl img
    JOIN account_tbl a ON (
        (img.artist_id IS NOT NULL AND img.artist_id = (
            SELECT art2.artist_id FROM artist_tbl art2 WHERE art2.account_id = a.account_id LIMIT 1
        ))
        OR
        (img.user_id IS NOT NULL AND img.user_id = (
            SELECT u2.user_id FROM user_tbl u2 WHERE u2.account_id = a.account_id LIMIT 1
        ))
    )
    LEFT JOIN artworks_tbl art ON art.image_id = img.image_id
    WHERE {$where}
    ORDER BY img.uploaded_at DESC
    LIMIT ? OFFSET ?
");
    $artworkStmt->execute($fetchParams);
    $artworks = $artworkStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success'  => true,
        'data'     => $artworks,
        'total'    => $total,
        'page'     => $page,
        'per_page' => $per_page,
        'pages'    => (int) ceil($total / $per_page),
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
