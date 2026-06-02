<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data          = json_decode(file_get_contents('php://input'), true);
$commissionId  = intval($data['commission_id']);
$amount        = floatval($data['amount']);
$paymentMethod = trim($data['payment_method']);
$db            = getDB();
$userId        = $_SESSION['user_id'];

// Get artist_id from commission
$stmt = $db->prepare('SELECT artist_id FROM commission_tbl WHERE commission_id = ? AND user_id = ?');
$stmt->execute([$commissionId, $userId]);
$commission = $stmt->fetch();

if (!$commission) {
    echo json_encode(['success' => false, 'message' => 'Commission not found']);
    exit;
}

// Create transaction
$stmt = $db->prepare('
    INSERT INTO transaction_tbl (commission_id, user_id, artist_id, total_amount, transaction_date, status)
    VALUES (?, ?, ?, ?, ?, ?)
');
$stmt->execute([$commissionId, $userId, $commission['artist_id'], $amount, date('Y-m-d'), 'pending']);
$transactionId = $db->lastInsertId();

// Create payment
$stmt = $db->prepare('
    INSERT INTO payment_tbl (transaction_id, amount, payment_method, status, payment_date)
    VALUES (?, ?, ?, ?, ?)
');
$stmt->execute([$transactionId, $amount, $paymentMethod, 'completed', date('Y-m-d')]);

// Update transaction status
$db->prepare('UPDATE transaction_tbl SET status = ? WHERE transaction_id = ?')
   ->execute(['completed', $transactionId]);

echo json_encode(['success' => true, 'message' => 'Payment successful']);
?>