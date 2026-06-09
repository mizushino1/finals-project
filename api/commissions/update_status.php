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
$requestId    = isset($data['request_id']) ? intval($data['request_id']) : 0;
$newStatus    = isset($data['status']) ? strtolower(trim($data['status'])) : '';

// Validation logic checks based on status type
if (($newStatus === 'rejected' && $requestId <= 0) || ($newStatus !== 'rejected' && $commissionId <= 0) || empty($newStatus)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters provided.']);
    exit;
}

$db     = getDB();
$role   = strtolower($_SESSION['role']);
$userId = $_SESSION['user_id'];

$statusMap = [
    'active'      => 1,
    'pending'     => 2,
    'accepted'    => 3,
    'rejected'    => 4, // Used for declining an artist request
    'in_progress' => 5,
    'completed'   => 6,
    'cancelled'   => 7,
];

// Allow 'user' to decline requests by adding 'rejected' to their list
$allowedTransitions = [
    'user'   => ['cancelled', 'rejected'],
    'client' => ['cancelled', 'rejected'], // Adding client fallback just in case
    'artist' => ['completed'],
    'admin'  => ['active', 'in_progress', 'completed', 'cancelled']
];

if (!in_array($newStatus, $allowedTransitions[$role] ?? [])) {
    echo json_encode(['success' => false, 'message' => 'Requested status transition denied for this role.']);
    exit;
}

$newStatusId = $statusMap[$newStatus];

try {
    if ($role === 'user' || $role === 'client') {
        
        // ── CASE A: Declining a specific Artist Request ──
        if ($newStatus === 'rejected') {
            // Verify that the logged-in user owns the commission attached to this request
            $stmtCheck = $db->prepare('
                SELECT c.commission_id 
                FROM commission_request_tbl r
                JOIN commission_tbl c ON r.commission_id = c.commission_id
                WHERE r.request_id = ? AND c.user_id = ?
            ');
            $stmtCheck->execute([$requestId, $userId]);
            
            if (!$stmtCheck->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized: You do not own the parent commission.']);
                exit;
            }

            // Update request status to 4 (Rejected)
            $stmtUpdate = $db->prepare('UPDATE commission_request_tbl SET status_id = ? WHERE request_id = ?');
            $stmtUpdate->execute([$newStatusId, $requestId]);
            
            $msg = 'Artist request declined successfully.';

        // ── CASE B: Cancelling the parent Commission ──
        } else {
            $stmtCheck = $db->prepare('SELECT commission_id FROM commission_tbl WHERE commission_id = ? AND user_id = ?');
            $stmtCheck->execute([$commissionId, $userId]);
            if (!$stmtCheck->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized: You do not own this commission.']);
                exit;
            }

            $stmtUpdate = $db->prepare('UPDATE commission_tbl SET status_id = ? WHERE commission_id = ?');
            $stmtUpdate->execute([$newStatusId, $commissionId]);
            $msg = 'Commission status updated to: ' . $newStatus;
        }

    } elseif ($role === 'artist') {
        // Resolve artist_id from account_id (Direct assign bypass check if your architecture differs)
        $stmtArtist = $db->prepare('SELECT artist_id FROM artist_tbl WHERE account_id = ?');
        $stmtArtist->execute([$userId]);
        $artistRow = $stmtArtist->fetch(PDO::FETCH_ASSOC);

        if (!$artistRow) {
            echo json_encode(['success' => false, 'message' => 'Artist profile not found.']);
            exit;
        }
        $artistId = $artistRow['artist_id'];

        $stmtCheck = $db->prepare('SELECT commission_id FROM commission_tbl WHERE commission_id = ? AND artist_id = ?');
        $stmtCheck->execute([$commissionId, $artistId]);
        if (!$stmtCheck->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized: You are not assigned to this commission.']);
            exit;
        }

        $stmtUpdate = $db->prepare('UPDATE commission_tbl SET status_id = ? WHERE commission_id = ?');
        $stmtUpdate->execute([$newStatusId, $commissionId]);
        $msg = 'Commission status updated to: ' . $newStatus;

    } elseif ($role === 'admin') {
        $stmtUpdate = $db->prepare('UPDATE commission_tbl SET status_id = ? WHERE commission_id = ?');
        $stmtUpdate->execute([$newStatusId, $commissionId]);
        $msg = 'Commission status updated to: ' . $newStatus;
    }

    echo json_encode([
        'success' => true,
        'message' => $msg
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>