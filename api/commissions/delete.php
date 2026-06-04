<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$data         = json_decode(file_get_contents('php://input'), true);
$commissionId = isset($data['commission_id']) ? intval($data['commission_id']) : 0;

if ($commissionId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid commission ID.']);
    exit;
}

$db   = getDB();
$role = strtolower($_SESSION['role']); // Handle casing safely
$uid  = $_SESSION['user_id'];

try {
    // Verify eligibility before touching anything
    if ($role === 'user' || $role === 'client') {
        // If they are a regular user, make sure they actually own it first
        $stmtCheck = $db->prepare('SELECT commission_id FROM commission_tbl WHERE commission_id = ? AND user_id = ?');
        $stmtCheck->execute([$commissionId, $uid]);
        if (!$stmtCheck->fetch(PDO::FETCH_ASSOC)) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized: You do not own this commission listing.']);
            exit;
        }
    } elseif ($role !== 'admin') {
        // Artists or other unhandled roles are blocked completely
        echo json_encode(['success' => false, 'message' => 'Unauthorized operation.']);
        exit;
    }

    // Begin transaction to safely remove data across related tables
    $db->beginTransaction();

    // Step 1: Clean up child records (Artist proposals/bids linked to this commission)
    $stmtDeleteRequests = $db->prepare('DELETE FROM commission_request_tbl WHERE commission_id = ?');
    $stmtDeleteRequests->execute([$commissionId]);

    // Step 2: Delete the parent commission item itself
    $stmtDeleteCommission = $db->prepare('DELETE FROM commission_tbl WHERE commission_id = ?');
    $stmtDeleteCommission->execute([$commissionId]);

    // Commit changes to database
    $db->commit();

    echo json_encode(['success' => true, 'message' => 'Commission listing and all associated proposals deleted successfully.']);

} catch (PDOException $e) {
    // If something breaks halfway, rollback immediately to prevent fragmented deletion
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode([
        'success' => false, 
        'message' => 'Database error encountered during deletion: ' . $e->getMessage()
    ]);
}
?>