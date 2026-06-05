<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// Ensure only authorized clients/users can post listings
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'user' && $_SESSION['role'] !== 'client')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$data        = json_decode(file_get_contents('php://input'), true);
$description = isset($data['description']) ? trim($data['description']) : '';
$budget      = isset($data['budget']) ? floatval($data['budget']) : 0.00;

// Input Validation Guard
if (empty($description)) {
    echo json_encode(['success' => false, 'message' => 'Please provide a project description.']);
    exit;
}

if ($budget <= 0) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid budget allocation higher than 0.']);
    exit;
}

$db = getDB();

try {
    // Current date timestamp injection
    $today = date('Y-m-d');
    $uid   = $_SESSION['user_id']; // Base profile account_id

    $stmt = $db->prepare('
INSERT INTO commission_tbl (user_id, artist_id, description, status_id, commission_date, price)
VALUES (?, NULL, ?, 2, ?, ?)
    ');

    $stmt->execute([
        $uid,
        $description,
        $today,
        $budget
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Commission posted successfully! Artists can now submit bids.'
    ]);
} catch (PDOException $e) {
    // Deliver structured error message instead of raw system traces
    echo json_encode([
        'success' => false,
        'message' => 'Failed to publish commission: ' . $e->getMessage()
    ]);
}
