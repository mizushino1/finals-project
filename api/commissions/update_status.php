<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['account_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$data         = json_decode(file_get_contents('php://input'), true);
$commissionId = isset($data['commission_id']) ? intval($data['commission_id']) : 0;
$requestId    = isset($data['request_id'])    ? intval($data['request_id'])    : 0;
$newStatus    = isset($data['status'])        ? strtolower(trim($data['status'])) : '';

if (empty($newStatus)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters provided.']);
    exit;
}

// 'rejected' only needs a request_id; 'accepted' needs both; all others need a commission_id
if ($newStatus === 'rejected' && $requestId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters: request_id required for rejection.']);
    exit;
}
if ($newStatus === 'accepted' && ($requestId <= 0 || $commissionId <= 0)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters: commission_id and request_id required for acceptance.']);
    exit;
}
if (!in_array($newStatus, ['rejected', 'accepted']) && $commissionId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters: commission_id required.']);
    exit;
}

$db   = getDB();
$role = strtolower($_SESSION['role'] ?? '');

$statusMap = [
    'active'      => 1,
    'pending'     => 2,
    'accepted'    => 3,
    'rejected'    => 4,
    'in_progress' => 5,
    'completed'   => 6,
    'cancelled'   => 7,
];

$allowedTransitions = [
    'user'   => ['cancelled', 'rejected', 'accepted'],
    'client' => ['cancelled', 'rejected', 'accepted'],
    'artist' => ['completed'],
    'admin'  => ['active', 'in_progress', 'completed', 'cancelled'],
];

if (!in_array($newStatus, $allowedTransitions[$role] ?? [])) {
    echo json_encode(['success' => false, 'message' => 'Requested status transition denied for this role.']);
    exit;
}

$account_id  = $_SESSION['account_id'];
$user_id     = $_SESSION['user_id'] ?? null;
$newStatusId = $statusMap[$newStatus];

try {
    if ($role === 'user' || $role === 'client') {

        // ── CASE A: User accepts an artist's request ──────────────────────────
        if ($newStatus === 'accepted') {
            // 1. Verify ownership and grab the artist_id from the accepted request in one query
            $stmtCheck = $db->prepare('
                SELECT c.commission_id, c.status_id AS comm_status, r.artist_id
                FROM commission_request_tbl r
                JOIN commission_tbl c ON r.commission_id = c.commission_id
                JOIN user_tbl u ON c.user_id = u.user_id
                WHERE r.request_id = ?
                  AND r.commission_id = ?
                  AND u.account_id = ?
            ');
            $stmtCheck->execute([$requestId, $commissionId, $account_id]);
            $job = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$job) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized: Commission not found or not owned by you.']);
                exit;
            }
            if (intval($job['comm_status']) !== 1) {
                echo json_encode(['success' => false, 'message' => 'This commission is no longer open for assignment.']);
                exit;
            }

            $acceptedArtistId = intval($job['artist_id']);

            // 2. Accept the chosen request (status 3 = accepted)
            $stmtAccept = $db->prepare('UPDATE commission_request_tbl SET status_id = 3 WHERE request_id = ?');
            $stmtAccept->execute([$requestId]);

            // 3. Decline all other pending requests for the same commission (status 4 = rejected)
            $stmtRejectOthers = $db->prepare('
                UPDATE commission_request_tbl
                SET status_id = 4
                WHERE commission_id = ?
                  AND request_id   != ?
                  AND status_id     = 2
            ');
            $stmtRejectOthers->execute([$commissionId, $requestId]);

            // 4. Stamp the accepted artist_id and set commission status to Accepted (status 3)
            $stmtCommission = $db->prepare('
                UPDATE commission_tbl
                SET status_id = 3, artist_id = ?
                WHERE commission_id = ?
            ');
            $stmtCommission->execute([$acceptedArtistId, $commissionId]);

            echo json_encode([
                'success' => true,
                'message' => 'Artist accepted! The commission is now in progress.'
            ]);

            // ── CASE B: User declines a specific artist request ───────────────────
        } elseif ($newStatus === 'rejected') {
            $stmtCheck = $db->prepare('
                SELECT c.commission_id
                FROM commission_request_tbl r
                JOIN commission_tbl c ON r.commission_id = c.commission_id
                JOIN user_tbl u ON c.user_id = u.user_id
                WHERE r.request_id = ?
                  AND u.account_id = ?
            ');
            $stmtCheck->execute([$requestId, $account_id]);

            if (!$stmtCheck->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized: You do not own the parent commission.']);
                exit;
            }

            $stmtUpdate = $db->prepare('UPDATE commission_request_tbl SET status_id = 4 WHERE request_id = ?');
            $stmtUpdate->execute([$requestId]);

            echo json_encode([
                'success' => true,
                'message' => 'Artist request declined successfully.'
            ]);

            // ── CASE C: User cancels their own commission ─────────────────────────
        } else {
            $stmtCheck = $db->prepare('
                SELECT c.commission_id
                FROM commission_tbl c
                JOIN user_tbl u ON c.user_id = u.user_id
                WHERE c.commission_id = ?
                  AND u.account_id = ?
            ');
            $stmtCheck->execute([$commissionId, $account_id]);

            if (!$stmtCheck->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized: You do not own this commission.']);
                exit;
            }

            $stmtUpdate = $db->prepare('UPDATE commission_tbl SET status_id = ? WHERE commission_id = ?');
            $stmtUpdate->execute([$newStatusId, $commissionId]);

            echo json_encode([
                'success' => true,
                'message' => 'Commission cancelled.'
            ]);
        }
    } elseif ($role === 'artist') {
        // Resolve artist_id from account_id
        $stmtArtist = $db->prepare('SELECT artist_id FROM artist_tbl WHERE account_id = ?');
        $stmtArtist->execute([$account_id]);
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

        echo json_encode([
            'success' => true,
            'message' => 'Commission marked as: ' . $newStatus
        ]);
    } elseif ($role === 'admin') {
        $stmtUpdate = $db->prepare('UPDATE commission_tbl SET status_id = ? WHERE commission_id = ?');
        $stmtUpdate->execute([$newStatusId, $commissionId]);

        echo json_encode([
            'success' => true,
            'message' => 'Commission status updated to: ' . $newStatus
        ]);
    }
} catch (PDOException $e) {
    error_log('PDO ERROR (update_status): ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
