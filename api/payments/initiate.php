<?php

/**
 * POST /api/payments/initiate.php
 *
 * Initiates a payment for a completed commission.
 *
 * Expected JSON body:
 *   commission_id     (int)  — ID of the commission being paid
 *   payment_method_id (int)  — FK to payment_method_tbl
 *                              1=GCash 2=Maya 3=PayPal 4=Credit Card 5=Bank Transfer
 */
require_once '../../config/constants.php';
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// ── 1. Auth — only logged-in users/clients may pay ──────────────────────────
if (empty($_SESSION['account_id']) || strtolower($_SESSION['role'] ?? '') !== 'user') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

// ── 2. Parse & validate input ────────────────────────────────────────────────
$data            = json_decode(file_get_contents('php://input'), true) ?? [];
$commissionId    = isset($data['commission_id'])      ? (int)   $data['commission_id']      : 0;
$paymentMethodId = isset($data['payment_method_id'])  ? (int)   $data['payment_method_id']  : 0;

// account_id stored in session — used to look up user_tbl
$accountId = (int) $_SESSION['account_id'];

if ($commissionId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid commission ID.']);
    exit;
}

// payment_method_id 1–5 must match payment_method_tbl
if ($paymentMethodId < 1 || $paymentMethodId > 5) {
    echo json_encode(['success' => false, 'message' => 'Invalid or unsupported payment method.']);
    exit;
}

$db = getDB();

try {
    // ── 3. Resolve user_id from account_id ──────────────────────────────────
    $stmtUser = $db->prepare('SELECT user_id FROM user_tbl WHERE account_id = ? LIMIT 1');
    $stmtUser->execute([$accountId]);
    $userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$userRow) {
        echo json_encode(['success' => false, 'message' => 'User profile not found.']);
        exit;
    }

    $userId = (int) $userRow['user_id'];

    // ── 4. Verify commission ownership & resolve assigned artist ────────────
    //    status_id 6 = Completed; only completed commissions should be payable
    $stmtComm = $db->prepare('
        SELECT commission_id, artist_id, price, status_id
        FROM   commission_tbl
        WHERE  commission_id = ?
          AND  user_id       = ?
        LIMIT 1
    ');
    $stmtComm->execute([$commissionId, $userId]);
    $commission = $stmtComm->fetch(PDO::FETCH_ASSOC);

    if (!$commission) {
        echo json_encode(['success' => false, 'message' => 'Commission not found or access denied.']);
        exit;
    }

    if (empty($commission['artist_id'])) {
        echo json_encode(['success' => false, 'message' => 'Cannot pay a commission with no assigned artist.']);
        exit;
    }

    // Prevent double-payment: check if a Paid transaction already exists
    $stmtDupCheck = $db->prepare('
        SELECT transaction_id
        FROM   transaction_tbl
        WHERE  commission_id = ?
          AND  status_id     = 10
        LIMIT 1
    ');
    $stmtDupCheck->execute([$commissionId]);
    if ($stmtDupCheck->fetch()) {
        echo json_encode(['success' => false, 'message' => 'This commission has already been paid.']);
        exit;
    }

    $amount = (float) $commission['price'];

    // ── 5. Atomic transaction block ─────────────────────────────────────────
    $db->beginTransaction();

    // Step A: Insert transaction record — status_id 2 = Pending
    $stmtTx = $db->prepare('
        INSERT INTO transaction_tbl (commission_id, total_amount, transaction_date, status_id)
        VALUES (?, ?, NOW(), 2)
    ');
    $stmtTx->execute([$commissionId, $amount]);
    $transactionId = (int) $db->lastInsertId();

    // Step B: Insert payment record — status_id 10 = Paid
    $stmtPay = $db->prepare('
        INSERT INTO payment_tbl (transaction_id, payment_method_id, amount, status_id, payment_date)
        VALUES (?, ?, ?, 10, NOW())
    ');
    $stmtPay->execute([$transactionId, $paymentMethodId, $amount]);

    // Step C: Mark the transaction as Paid — status_id 10
    $stmtUpdateTx = $db->prepare('
        UPDATE transaction_tbl
        SET    status_id = 10
        WHERE  transaction_id = ?
    ');
    $stmtUpdateTx->execute([$transactionId]);

    // Step D: Optionally advance the commission to Completed — status_id 6
    //         Uncomment if "Completed" should be set here rather than on delivery
    // $stmtUpdateComm = $db->prepare('
    //     UPDATE commission_tbl SET status_id = 6 WHERE commission_id = ?
    // ');
    // $stmtUpdateComm->execute([$commissionId]);

    $db->commit();

    echo json_encode([
        'success'        => true,
        'message'        => 'Payment processed successfully.',
        'transaction_id' => $transactionId,
        'redirect' => BASE_URL . 'payments/success?txn=' . $transactionId,
    ]);
} catch (PDOException $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    // Avoid leaking raw DB errors to the client in production
    error_log('[initiate.php] PDOException: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Payment processing failed. Please try again later.',
    ]);
}