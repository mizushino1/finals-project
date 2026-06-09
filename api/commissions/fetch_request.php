<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Please log in.']);
    exit;
}

$data         = json_decode(file_get_contents('php://input'), true);
$commissionId = isset($data['commission_id']) ? intval($data['commission_id']) : 0;
// Dynamically sanitizing incoming multi-line messages securely
$message      = isset($data['message']) ? trim(htmlspecialchars($data['message'], ENT_QUOTES, 'UTF-8')) : '';
$artistId     = $_SESSION['user_id'];

if ($commissionId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid commission ID.']);
    exit;
}

if (empty($message)) {
    echo json_encode(['success' => false, 'message' => 'An application message text is required.']);
    exit;
}

$db = getDB();

try {
    // Check if the commission is valid and open
    $stmt = $db->prepare('SELECT status_id, user_id FROM commission_tbl WHERE commission_id = ?');
    $stmt->execute([$commissionId]);
    $commission = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$commission) {
        echo json_encode(['success' => false, 'message' => 'Commission job layout not found.']);
        exit;
    }

    if (intval($commission['status_id']) !== 1) {
        echo json_encode(['success' => false, 'message' => 'This commission is no longer active.']);
        exit;
    }

    if (intval($commission['user_id']) === $artistId) {
        echo json_encode(['success' => false, 'message' => 'You cannot take your own posted commission job.']);
        exit;
    }

    // Check for duplicate requests
    $stmt = $db->prepare('SELECT request_id FROM commission_request_tbl WHERE commission_id = ? AND artist_id = ?');
    $stmt->execute([$commissionId, $artistId]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'You have already submitted an application request for this commission job.']);
        exit;
    }

    // Insert request with custom written message text, status_id 2 = Pending
    $stmt = $db->prepare('
        INSERT INTO commission_request_tbl (commission_id, artist_id, message, status_id, requested_at) 
        VALUES (?, ?, ?, 2, NOW())
    ');
    $stmt->execute([$commissionId, $artistId, $message]);

    echo json_encode([
        'success' => true,
        'message' => 'Your request and application pitch have been submitted successfully!'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error encountered: ' . $e->getMessage()
    ]);
}
?>