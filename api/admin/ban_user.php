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

    // 2. Resolve the absolute account_id depending on the incoming profile type
    if ($type === 'user' || $type === 'client') {
        // Users map 1:1 with account_id in a unified schema layout
        $stmtCheck = $db->prepare('SELECT account_id FROM user_tbl WHERE account_id = ?');
        $stmtCheck->execute([$id]);
        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $accountId = $row['account_id'];
        }
    } elseif ($type === 'artist') {
        // Artists must be looked up to translate artist_id -> account_id
        $stmtCheck = $db->prepare('SELECT account_id FROM artist_tbl WHERE artist_id = ?');
        $stmtCheck->execute([$id]);
        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $accountId = $row['account_id'];
        }
    }

    // Guard against non-existent records
    if ($accountId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Target profile record could not be found.']);
        exit;
    }

    // Protection Guard: Prevent an admin from accidentally banning themselves
    if ($accountId === intval($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Self-destructive actions are blocked. You cannot ban your own administrative account.']);
        exit;
    }

    // 3. Execute the status modification at the root authentication layer
    $stmt = $db->prepare('UPDATE account_tbl SET account_status = "banned" WHERE account_id = ?');
    $stmt->execute([$accountId]);

    /**
     * NOTE ON SESSION TERMINATION:
     * To prevent a banned user from remaining active until they naturally log out,
     * consider clearing active session file handles or managing a centralized token blacklist token.
     */

    echo json_encode([
        'success' => true, 
        'message' => "The target $type account has been successfully banned and restricted from the platform."
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Administrative modification failed: ' . $e->getMessage()
    ]);
}
?>