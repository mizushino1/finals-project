<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// Ensure the client is securely logged in
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'user' && $_SESSION['role'] !== 'client')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$data         = json_decode(file_get_contents('php://input'), true);
$commissionId = isset($data['commission_id']) ? intval($data['commission_id']) : 0;
$requestId    = isset($data['request_id']) ? intval($data['request_id']) : 0;
$userId       = $_SESSION['user_id']; // This is the user's account_id from account_tbl

if ($commissionId <= 0 || $requestId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters provided.']);
    exit;
}

$db = getDB();

try {
    // 1. Verify this commission belongs to the logged-in client
    $stmt = $db->prepare('SELECT commission_id FROM commission_tbl WHERE commission_id = ? AND user_id = ?');
    $stmt->execute([$commissionId, $userId]);
    if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized: This commission record does not match your profile.']);
        exit;
    }

    // 2. Validate the request exists and retrieve the associated artist_id
    $stmt = $db->prepare('SELECT artist_id FROM commission_request_tbl WHERE request_id = ? AND commission_id = ?');
    $stmt->execute([$requestId, $commissionId]);
    $acceptedRequest = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$acceptedRequest) {
        echo json_encode(['success' => false, 'message' => 'The requested artist bid could not be identified.']);
        exit;
    }

    $artistId = $acceptedRequest['artist_id'];

    // 3. Begin Transaction to execute workflow updates safely
    $db->beginTransaction();

    // Step A: Mark target request as accepted
    $stmt1 = $db->prepare('UPDATE commission_request_tbl SET status = "accepted" WHERE request_id = ?');
    $stmt1->execute([$requestId]);

    // Step B: Reject alternative rival bids for this specific tracking file
    $stmt2 = $db->prepare('
        UPDATE commission_request_tbl 
        SET status = "rejected" 
        WHERE commission_id = ? AND request_id != ?
    ');
    $stmt2->execute([$commissionId, $requestId]);

    // Step C: Link the winning artist profile and update main status
    $stmt3 = $db->prepare('
        UPDATE commission_tbl 
        SET artist_id = ?, status = "in_progress" 
        WHERE commission_id = ?
    ');
    $stmt3->execute([$artistId, $commissionId]);

    // All steps succeeded; commit modifications safely to disk
    $db->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Artist assigned successfully. Your project is now in progress!'
    ]);

} catch (PDOException $e) {
    // Drop all partial operations instantly if any errors occurs
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to process assignment: ' . $e->getMessage()
    ]);
}
?>