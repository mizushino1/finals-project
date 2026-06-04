<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// Ensure only authorized artists can request listings
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'artist') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$data         = json_decode(file_get_contents('php://input'), true);
$commissionId = isset($data['commission_id']) ? intval($data['commission_id']) : 0;
$message      = isset($data['message']) ? trim($data['message']) : '';

if ($commissionId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid commission reference ID.']);
    exit;
}

$db        = getDB();
$accountId = $_SESSION['user_id']; // Base profile account_id from session

try {
    // 1. Resolve artist_id from artist_tbl using the account_id if necessary
    // (If your table links directly on account_id, you can omit this step and use $accountId)
    $stmtArtist = $db->prepare('SELECT artist_id FROM artist_tbl WHERE account_id = ?');
    $stmtArtist->execute([$accountId]);
    $artistRow  = $stmtArtist->fetch(PDO::FETCH_ASSOC);

    if (!$artistRow) {
        echo json_encode(['success' => false, 'message' => 'Artist profile record could not be found.']);
        exit;
    }
    $artistId = $artistRow['artist_id'];

    // 2. Prevent duplicate entries: Check if artist already applied
    $stmtCheck = $db->prepare('
        SELECT request_id FROM commission_request_tbl
        WHERE commission_id = ? AND artist_id = ?
    ');
    $stmtCheck->execute([$commissionId, $artistId]);
    
    if ($stmtCheck->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(['success' => false, 'message' => 'You have already submitted a proposal for this listing.']);
        exit;
    }

    // 3. Status confirmation: Ensure the parent commission is valid and open
    $stmtComm = $db->prepare('SELECT status FROM commission_tbl WHERE commission_id = ?');
    $stmtComm->execute([$commissionId]);
    $commission = $stmtComm->fetch(PDO::FETCH_ASSOC);

    if (!$commission || strtolower($commission['status']) !== 'open') {
        echo json_encode(['success' => false, 'message' => 'This commission is no longer accepting submissions.']);
        exit;
    }

    // 4. Safe entry injection
    $today = date('Y-m-d');
    $stmtInsert = $db->prepare('
        INSERT INTO commission_request_tbl (commission_id, artist_id, message, status, requested_at)
        VALUES (?, ?, ?, "pending", ?)
    ');
    $stmtInsert->execute([$commissionId, $artistId, $message, $today]);

    echo json_encode([
        'success' => true, 
        'message' => 'Proposal submitted successfully! Waiting for client confirmation.'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to log application details: ' . $e->getMessage()
    ]);
}
?>