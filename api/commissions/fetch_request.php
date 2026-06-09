<?php
// 1. Initialize sessions and load the database connection setup
require_once '../../config/session.php';
require_once '../../config/database.php';

// 2. Enforce standard API JSON headers
header('Content-Type: application/json');

// 3. Authenticate active session status
if (!isset($_SESSION['account_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Please log in.']);
    exit;
}

$account_id   = $_SESSION['account_id']; // The reliable global ID from account_tbl
$data         = json_decode(file_get_contents('php://input'), true);
$commissionId = isset($data['commission_id']) ? intval($data['commission_id']) : 0;

try {
    $db = getDB();

    // 1. Resolve the true artist_id for the currently logged-in account
    $stmtArtist = $db->prepare('SELECT artist_id FROM artist_tbl WHERE account_id = ?');
    $stmtArtist->execute([$account_id]);
    $artistRow = $stmtArtist->fetch(PDO::FETCH_ASSOC);

    if (!$artistRow) {
        echo json_encode(['success' => false, 'message' => 'You must have an Artist profile to take commissions.']);
        exit;
    }
    $artistId = $artistRow['artist_id'];

    // 2. Look up the commission AND the account_id of the person who posted it
    $stmtJob = $db->prepare('
        SELECT c.commission_id, u.account_id AS owner_account_id 
        FROM commission_tbl c
        JOIN user_tbl u ON c.user_id = u.user_id
        WHERE c.commission_id = ?
    ');
    $stmtJob->execute([$commissionId]);
    $job = $stmtJob->fetch(PDO::FETCH_ASSOC);

    if (!$job) {
        echo json_encode(['success' => false, 'message' => 'Commission job not found.']);
        exit;
    }

    // 3. SECURE COMPARISON: Compare original post owner\'s account_id with current logged-in account_id
    if (intval($job['owner_account_id']) === intval($account_id)) {
        echo json_encode(['success' => false, 'message' => 'You cannot take your own posted commission job.']);
        exit;
    }

    // 4. Safety Check: Prevent applying to the same job twice while a decision is pending
    $stmtDuplicate = $db->prepare('SELECT request_id FROM commission_request_tbl WHERE commission_id = ? AND artist_id = ? AND status_id = 2');
    $stmtDuplicate->execute([$commissionId, $artistId]);
    if ($stmtDuplicate->fetch()) {
        echo json_encode(['success' => false, 'message' => 'You have already submitted a pending request for this commission.']);
        exit;
    }

    // 5. If it passes all guards, proceed with the INSERT query...
    $messageText = isset($data['message']) ? trim($data['message']) : '';
    $stmtInsert = $db->prepare('
        INSERT INTO commission_request_tbl (commission_id, artist_id, message, status_id, requested_at) 
        VALUES (?, ?, ?, 2, NOW())
    ');
    $stmtInsert->execute([$commissionId, $artistId, $messageText]);

    echo json_encode(['success' => true, 'message' => 'Your application has been sent successfully!']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>