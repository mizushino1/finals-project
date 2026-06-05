<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$data         = json_decode(file_get_contents('php://input'), true);
$commissionId = isset($data['commission_id']) ? intval($data['commission_id']) : 0;
$requestId    = isset($data['request_id']) ? intval($data['request_id']) : 0;
$userId       = $_SESSION['user_id'];

if ($commissionId <= 0 || $requestId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters provided.']);
    exit;
}

$db = getDB();

try {
    // 1. Verify this commission belongs to the logged-in user
    $stmt = $db->prepare('SELECT commission_id FROM commission_tbl WHERE commission_id = ? AND user_id = ?');
    $stmt->execute([$commissionId, $userId]);
    if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized: This commission does not belong to your account.']);
        exit;
    }

    // 2. Validate the request exists and get the artist_id
    $stmt = $db->prepare('SELECT artist_id FROM commission_request_tbl WHERE request_id = ? AND commission_id = ?');
    $stmt->execute([$requestId, $commissionId]);
    $acceptedRequest = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$acceptedRequest) {
        echo json_encode(['success' => false, 'message' => 'Artist bid could not be found.']);
        exit;
    }

    $artistId = $acceptedRequest['artist_id'];

    $db->beginTransaction();

    // Step A: Mark the accepted request — status_id 3 = Accepted
    $stmt1 = $db->prepare('UPDATE commission_request_tbl SET status_id = 3 WHERE request_id = ?');
    $stmt1->execute([$requestId]);

    // Step B: Reject all other bids — status_id 4 = Rejected
    $stmt2 = $db->prepare('
        UPDATE commission_request_tbl 
        SET status_id = 4
        WHERE commission_id = ? AND request_id != ?
    ');
    $stmt2->execute([$commissionId, $requestId]);

    // Step C: Assign artist and set commission to In Progress — status_id 5 = In Progress
    $stmt3 = $db->prepare('
        UPDATE commission_tbl 
        SET artist_id = ?, status_id = 5
        WHERE commission_id = ?
    ');
    $stmt3->execute([$artistId, $commissionId]);

    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Artist assigned successfully. Your project is now in progress!'
    ]);

} catch (PDOException $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => 'Failed to process assignment: ' . $e->getMessage()
    ]);
}
?>