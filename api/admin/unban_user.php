<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// 1. Enforce strict administrative authentication gate
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized administrative access.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id   = isset($data['id']) ? intval($data['id']) : 0;
$type = isset($data['type']) ? strtolower(trim($data['type'])) : '';
$db   = getDB();

if ($id <= 0 || !in_array($type, ['user', 'artist', 'client'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid account identifier or role type provided.']);
    exit;
}

try {
    $accountId = 0;

    // Resolve the absolute account_id depending on the incoming profile type
    if ($type === 'user' || $type === 'client') {
        $stmtCheck = $db->prepare('SELECT account_id FROM user_tbl WHERE account_id = ?');
        $stmtCheck->execute([$id]);
        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $accountId = $row['account_id'];
        }
    } elseif ($type === 'artist') {
        $stmtCheck = $db->prepare('SELECT account_id FROM artist_tbl WHERE artist_id = ?');
        $stmtCheck->execute([$id]);
        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $accountId = $row['account_id'];
        }
    }

    if ($accountId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Target profile record could not be found.']);
        exit;
    }

    // Resolve the "Active" status_id from account_status_tbl
    $stmtStatus = $db->prepare('SELECT account_status_id FROM account_status_tbl WHERE status_name = ?');
    $stmtStatus->execute(['Active']);
    $statusRow = $stmtStatus->fetch(PDO::FETCH_ASSOC);

    if (!$statusRow) {
        echo json_encode(['success' => false, 'message' => '"Active" status is not configured in account_status_tbl.']);
        exit;
    }
    $activeStatusId = $statusRow['account_status_id'];

    $stmt = $db->prepare('UPDATE account_tbl SET account_status_id = ? WHERE account_id = ?');
    $stmt->execute([$activeStatusId, $accountId]);

    echo json_encode([
        'success' => true,
        'message' => "The target $type account has been restored to Active status."
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Administrative modification failed: ' . $e->getMessage()
    ]);
}