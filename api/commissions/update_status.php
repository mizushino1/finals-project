<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

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
$userId = $_SESSION['user_id'];

// Map status name → status_id (matches your status_tbl INSERT order)
$statusMap = [
    'active'      => 1,
    'pending'     => 2,
    'accepted'    => 3,
    'rejected'    => 4,
    'in_progress' => 5,
    'completed'   => 6,
    'cancelled'   => 7,
];

// Allowed transitions per role (status name strings for readability)
$allowedTransitions = [
    'user'   => ['cancelled'],
    'artist' => ['completed'],
    'admin'  => ['active', 'in_progress', 'completed', 'cancelled']
];

if (!in_array($newStatus, $allowedTransitions[$role] ?? [])) {
    echo json_encode(['success' => false, 'message' => 'Requested status transition denied for this role.']);
    exit;
}

// Resolve the status_id — safe because $newStatus is already validated above
$newStatusId = $statusMap[$newStatus];

try {
    if ($role === 'user') {
        // Verify ownership
        $stmtCheck = $db->prepare('SELECT commission_id FROM commission_tbl WHERE commission_id = ? AND user_id = ?');
        $stmtCheck->execute([$commissionId, $userId]);
        if (!$stmtCheck->fetch(PDO::FETCH_ASSOC)) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized: You do not own this commission.']);
            exit;
        }

        $stmtUpdate = $db->prepare('UPDATE commission_tbl SET status_id = ? WHERE commission_id = ?');
        $stmtUpdate->execute([$newStatusId, $commissionId]);

    } elseif ($role === 'artist') {
        // Resolve artist_id from account_id
        $stmtArtist = $db->prepare('SELECT artist_id FROM artist_tbl WHERE account_id = ?');
        $stmtArtist->execute([$userId]);
        $artistRow = $stmtArtist->fetch(PDO::FETCH_ASSOC);

        if (!$artistRow) {
            echo json_encode(['success' => false, 'message' => 'Artist profile not found.']);
            exit;
        }
        $artistId = $artistRow['artist_id'];

        // Verify artist is assigned to this commission
        $stmtCheck = $db->prepare('SELECT commission_id FROM commission_tbl WHERE commission_id = ? AND artist_id = ?');
        $stmtCheck->execute([$commissionId, $artistId]);
        if (!$stmtCheck->fetch(PDO::FETCH_ASSOC)) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized: You are not assigned to this commission.']);
            exit;
        }

        $stmtUpdate = $db->prepare('UPDATE commission_tbl SET status_id = ? WHERE commission_id = ?');
        $stmtUpdate->execute([$newStatusId, $commissionId]);

    } elseif ($role === 'admin') {
        $stmtUpdate = $db->prepare('UPDATE commission_tbl SET status_id = ? WHERE commission_id = ?');
        $stmtUpdate->execute([$newStatusId, $commissionId]);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Commission status updated to: ' . $newStatus
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>