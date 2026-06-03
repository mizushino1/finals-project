<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$db = getDB();

$stmt = $db->prepare('
    SELECT artist_id, username, start_at, account_status
    FROM artist_tbl
    WHERE account_status = "active"
    ORDER BY start_at DESC
');
$stmt->execute();

echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);