<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// 1. Enforce strict client-only authentication
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'user' && $_SESSION['role'] !== 'client')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$data          = json_decode(file_get_contents('php://input'), true);
$commissionId  = isset($data['commission_id']) ? intval($data['commission_id']) : 0;
$amount        = isset($data['amount']) ? floatval($data['amount']) : 0.00;
$paymentMethod = isset($data['payment_method']) ? strtolower(trim($data['payment_method'])) : '';
$userId        = $_SESSION['user_id']; // base profile account_id

// 2. Comprehensive Input Safeguards
if ($commissionId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid commission tracking ID.']);
    exit;
}

if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Payment amount must be greater than zero.']);
    exit;
}

if (empty($paymentMethod)) {
    echo json_encode(['success' => false, 'message' => 'Please specify a valid payment method.']);
    exit;
}

$db = getDB();

try {
    // 3. Verify project ownership and resolve the assigned artist_id
    $stmt = $db->prepare('
        SELECT artist_id, price 
        FROM commission_tbl 
        WHERE commission_id = ? AND user_id = ?
    ');
    $stmt->execute([$commissionId, $userId]);
    $commission = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$commission) {
        echo json_encode(['success' => false, 'message' => 'Commission file not found or profile access denied.']);
        exit;
    }

    if (empty($commission['artist_id'])) {
        echo json_encode(['success' => false, 'message' => 'Cannot pay for a commission that has no assigned artist.']);
        exit;
    }

    $artistId = $commission['artist_id'];
    $today    = date('Y-m-d');

    // 4. Begin Transaction to guarantee financial data sync
    $db->beginTransaction();

    // Step A: Create the transaction record (initially 'pending')
    $stmtTx = $db->prepare('
        INSERT INTO transaction_tbl (commission_id, user_id, artist_id, total_amount, transaction_date, status)
        VALUES (?, ?, ?, ?, ?, "pending")
    ');
    $stmtTx->execute([$commissionId, $userId, $artistId, $amount, $today]);
    $transactionId = $db->lastInsertId();

    // Step B: Record the concrete payment gateway ledger entry
    $stmtPay = $db->prepare('
        INSERT INTO payment_tbl (transaction_id, amount, payment_method, status, payment_date)
        VALUES (?, ?, ?, "completed", ?)
    ');
    $stmtPay->execute([$transactionId, $amount, $paymentMethod, $today]);

    // Step C: Securely promote the transaction file status to 'completed'
    $stmtUpdateTx = $db->prepare('
        UPDATE transaction_tbl 
        SET status = "completed" 
        WHERE transaction_id = ?
    ');
    $stmtUpdateTx->execute([$transactionId]);

    // Step D: Optional Milestone Update — switch the main project state to 'in_progress' or 'completed'
    // depending on whether this represents a partial deposit or a final settlement.

    // Commit all financial ledger writes safely to disk
    $db->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Payment processed successfully! Your receipt has been logged.'
    ]);

} catch (PDOException $e) {
    // Drop all partial table updates immediately if any query faults out
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    
    echo json_encode([
        'success' => false, 
        'message' => 'Financial ledger settlement failed: ' . $e->getMessage()
    ]);
}
?>