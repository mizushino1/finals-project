<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$data         = json_decode(file_get_contents('php://input'), true);
$commissionId = isset($data['commission_id']) ? intval($data['commission_id']) : 0;
$newStatus    = isset($data['status']) ? strtolower(trim($data['status'])) : '';

if ($commissionId <= 0 || empty($newStatus)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters provided.']);
    exit;
}

$db     = getDB();
$role   = strtolower($_SESSION['role']);
$userId = $_SESSION['user_id']; // base profile account_id

// Strict state lifecycle matrices for commission_tbl
$allowedTransitions = [
    'user'   => ['cancelled'],
    'artist' => ['completed'], // Artists can declare work finalized
    'admin'  => ['open', 'in_progress', 'completed', 'cancelled']
];

if (!in_array($newStatus, $allowedTransitions[$role] ?? [])) {
    echo json_encode(['success' => false, 'message' => 'Requested status transition denied for this profile role.']);
    exit;
}

try {
    // Context-Aware Scoping Verification
    if ($role === 'user' || $role === 'client') {
        // Clients can only update state if they own the primary listing file
        $stmtCheck = $db->prepare('SELECT commission_id FROM commission_tbl WHERE commission_id = ? AND user_id = ?');
        $stmtCheck->execute([$commissionId, $userId]);
        if (!$stmtCheck->fetch(PDO::FETCH_ASSOC)) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized: Access mapping violation.']);
            exit;
        }

        $stmtUpdate = $db->prepare('UPDATE commission_tbl SET status = ? WHERE commission_id = ?');
        $stmtUpdate->execute([$newStatus, $commissionId]);

    } elseif ($role === 'artist') {
        // First resolve structural artist_id from sub-table configuration
        $stmtArtist = $db->prepare('SELECT artist_id FROM artist_tbl WHERE account_id = ?');
        $stmtArtist->execute([$userId]);
        $artistRow = $stmtArtist->fetch(PDO::FETCH_ASSOC);

        if (!$artistRow) {
            echo json_encode(['success' => false, 'message' => 'Artist validation tracking profile missing.']);
            exit;
        }
        $artistId = $artistRow['artist_id'];

        // Verify the artist is actively assigned to this specific project ledger file
        $stmtCheck = $db->prepare('SELECT commission_id FROM commission_tbl WHERE commission_id = ? AND artist_id = ?');
        $stmtCheck->execute([$commissionId, $artistId]);
        if (!$stmtCheck->fetch(PDO::FETCH_ASSOC)) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized: You are not assigned to this project file.']);
            exit;
        }

        $stmtUpdate = $db->prepare('UPDATE commission_tbl SET status = ? WHERE commission_id = ?');
        $stmtUpdate->execute([$newStatus, $commissionId]);

    } elseif ($role === 'admin') {
        // Global administrative overrides can access any target directly
        $stmtUpdate = $db->prepare('UPDATE commission_tbl SET status = ? WHERE commission_id = ?');
        $stmtUpdate->execute([$newStatus, $commissionId]);
    }

    echo json_encode([
        'success' => true, 
        'message' => 'Project milestone classification updated to: ' . $newStatus
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database operation fault encountered: ' . $e->getMessage()
    ]);
}
?>