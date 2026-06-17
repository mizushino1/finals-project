<?php
ob_start();
require_once '../../config/session.php';
require_once '../../config/database.php';
ob_clean();
header('Content-Type: application/json');

if (!isset($_SESSION['account_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

// ── 1. DYNAMIC INPUT RESOLUTION (JSON vs FormData) ───────────────────
// If multipart/form-data is used (such as for file uploads), parameters land in $_POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
    $commissionId = isset($_POST['commission_id']) ? intval($_POST['commission_id']) : 0;
    $requestId    = isset($_POST['request_id'])    ? intval($_POST['request_id'])    : 0;
    $newStatus    = isset($_POST['status'])        ? strtolower(trim($_POST['status'])) : '';
} else {
    // Fallback to standard application/json stream reading
    $data         = json_decode(file_get_contents('php://input'), true);
    $commissionId = isset($data['commission_id']) ? intval($data['commission_id']) : 0;
    $requestId    = isset($data['request_id'])    ? intval($data['request_id'])    : 0;
    $newStatus    = isset($data['status'])        ? strtolower(trim($data['status'])) : '';
}

if (empty($newStatus)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters provided.']);
    exit;
}

// 'rejected' and artist 'cancelled' only need a request_id; 'accepted' needs both; all others need a commission_id
if (in_array($newStatus, ['rejected', 'cancelled']) && $requestId <= 0 && $commissionId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters: request_id or commission_id required.']);
    exit;
}
if ($newStatus === 'rejected' && $requestId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters: request_id required for rejection.']);
    exit;
}
if ($newStatus === 'accepted' && ($requestId <= 0 || $commissionId <= 0)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters: commission_id and request_id required for acceptance.']);
    exit;
}
if (!in_array($newStatus, ['rejected', 'accepted', 'cancelled']) && $commissionId <= 0) {
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
    'user'   => ['active', 'cancelled', 'rejected', 'accepted'],
    'client' => ['active', 'cancelled', 'rejected', 'accepted'],
    'artist' => ['in_progress', 'completed', 'cancelled'],
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

            $stmtAccept = $db->prepare('UPDATE commission_request_tbl SET status_id = 3 WHERE request_id = ?');
            $stmtAccept->execute([$requestId]);

            $stmtRejectOthers = $db->prepare('
                UPDATE commission_request_tbl
                SET status_id = 4
                WHERE commission_id = ?
                  AND request_id   != ?
                  AND status_id     = 2
            ');
            $stmtRejectOthers->execute([$commissionId, $requestId]);

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

        // ── CASE C: User restores a cancelled commission ────────────────────────
        } elseif ($newStatus === 'active') {
            $stmtCheck = $db->prepare('
                SELECT c.commission_id, c.status_id
                FROM commission_tbl c
                JOIN user_tbl u ON c.user_id = u.user_id
                WHERE c.commission_id = ?
                  AND u.account_id = ?
            ');
            $stmtCheck->execute([$commissionId, $account_id]);
            $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized: You do not own this commission.']);
                exit;
            }
            if (intval($row['status_id']) !== 7) {
                echo json_encode(['success' => false, 'message' => 'Only cancelled commissions can be restored.']);
                exit;
            }

            $stmtUpdate = $db->prepare('UPDATE commission_tbl SET status_id = 1, artist_id = NULL WHERE commission_id = ?');
            $stmtUpdate->execute([$commissionId]);

            echo json_encode(['success' => true, 'message' => 'Commission restored and set back to Active.']);

        // ── CASE D: User cancels their own commission ─────────────────────────
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
        $stmtArtist = $db->prepare('SELECT artist_id FROM artist_tbl WHERE account_id = ?');
        $stmtArtist->execute([$account_id]);
        $artistRow = $stmtArtist->fetch(PDO::FETCH_ASSOC);

        if (!$artistRow) {
            echo json_encode(['success' => false, 'message' => 'Artist profile not found.']);
            exit;
        }
        $artistId = $artistRow['artist_id'];

        if ($newStatus === 'cancelled') {
            // ── Withdraw a pending request ──
            if ($requestId <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid parameters: request_id required to withdraw.']);
                exit;
            }

            $stmtCheck = $db->prepare('
                SELECT request_id FROM commission_request_tbl
                WHERE request_id = ? AND artist_id = ? AND status_id = 2
            ');
            $stmtCheck->execute([$requestId, $artistId]);
            if (!$stmtCheck->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Request not found or already resolved.']);
                exit;
            }

            $stmtUpdate = $db->prepare('UPDATE commission_request_tbl SET status_id = 7 WHERE request_id = ?');
            $stmtUpdate->execute([$requestId]);

            echo json_encode([
                'success' => true,
                'message' => 'Your request has been withdrawn.'
            ]);
        } else {
            // ── in_progress / completed ──
            $stmtCheck = $db->prepare('SELECT commission_id, user_id FROM commission_tbl WHERE commission_id = ? AND artist_id = ?');
            $stmtCheck->execute([$commissionId, $artistId]);
            $commissionRow = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            
            if (!$commissionRow) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized: You are not assigned to this commission.']);
                exit;
            }

            // ── VALIDATE AND UPLOAD PROOF UPON COMPLETION ────────────────────
            if ($newStatus === 'completed') {
                if (!isset($_FILES['completion_proof']) || $_FILES['completion_proof']['error'] !== UPLOAD_ERR_OK) {
                    echo json_encode(['success' => false, 'message' => 'Completion proof file attachment is required.']);
                    exit;
                }

                $fileTmpPath = $_FILES['completion_proof']['tmp_name'];
                $fileName    = $_FILES['completion_proof']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
                if (!in_array($fileExtension, $allowedExtensions)) {
                    echo json_encode(['success' => false, 'message' => 'Invalid file format. Allowed variants: JPG, PNG, PDF.']);
                    exit;
                }

                // Setup storage structures contextually
                $uploadDir = '../../public/uploads/proofs/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $newFileName = 'proof_' . $commissionId . '_' . time() . '.' . $fileExtension;
                $destPath    = $uploadDir . $newFileName;
                $dbSavePath  = 'public/uploads/proofs/' . $newFileName;

                if (!move_uploaded_file($fileTmpPath, $destPath)) {
                    echo json_encode(['success' => false, 'message' => 'Error moving file to the uploads directory folder.']);
                    exit;
                }

                // FIX: Use image_type_id = 3 ('Commission') to respect your DB schema limits
                // We also map the artist_id context into the image tracking system record
                $stmtImage = $db->prepare('
                    INSERT INTO image_tbl (user_id, artist_id, commission_id, image_type_id, image_url)
                    VALUES (?, ?, ?, 3, ?)
                ');
                $stmtImage->execute([$commissionRow['user_id'], $artistId, $commissionId, $dbSavePath]);
            }

            $stmtUpdate = $db->prepare('UPDATE commission_tbl SET status_id = ? WHERE commission_id = ?');
            $stmtUpdate->execute([$newStatusId, $commissionId]);

            echo json_encode([
                'success' => true,
                'message' => $newStatus === 'completed' 
                    ? 'Commission successfully finalized and proof of work uploaded!' 
                    : 'Commission marked as: ' . $newStatus
            ]);
        }
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
        // TEMPORARY FOR DEBUGGING:
        'message' => 'Database error: ' . $e->getMessage() 
    ]);
}