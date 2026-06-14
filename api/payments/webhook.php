<?php

/**
 * POST /api/payments/webhook.php
 *
 * Receives asynchronous payment status callbacks from a payment gateway
 * (GCash, Maya, PayPal, or a card processor) and reconciles the local
 * transaction and payment records with the gateway's ground truth.
 *
 * Expected JSON payload (gateway-agnostic normalised form):
 * {
 *   "event"          : "payment.success" | "payment.failed" | "payment.refunded",
 *   "transaction_id" : <our local transaction_id (int)>,
 *   "gateway_ref"    : "<gateway-side reference string>",
 *   "amount"         : <decimal>,
 *   "currency"       : "PHP",
 *   "signature"      : "<HMAC-SHA256 of payload body using WEBHOOK_SECRET>"
 * }
 *
 * Status IDs used:
 *   2  = Pending
 *   6  = Completed
 *   7  = Cancelled
 *   10 = Paid
 */

require_once '../../config/database.php';

// ── Security: only accept POST ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

header('Content-Type: application/json');

// ── Read raw body (needed for HMAC verification) ─────────────────────────────
$rawBody = file_get_contents('php://input');
$payload = json_decode($rawBody, true);

if (json_last_error() !== JSON_ERROR_NONE || empty($payload)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON payload.']);
    exit;
}

// ── 1. Verify webhook signature ──────────────────────────────────────────────
// The gateway signs the raw request body with a shared secret.
// Define WEBHOOK_SECRET in your config or as an environment variable.
$webhookSecret = defined('WEBHOOK_SECRET') ? WEBHOOK_SECRET : getenv('WEBHOOK_SECRET');

if (!empty($webhookSecret)) {
    $receivedSig  = $_SERVER['HTTP_X_SIGNATURE'] ?? '';                   // header name may vary by gateway
    $expectedSig  = hash_hmac('sha256', $rawBody, $webhookSecret);

    if (!hash_equals($expectedSig, $receivedSig)) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Signature mismatch.']);
        exit;
    }
}

// ── 2. Extract and validate required fields ──────────────────────────────────
$event         = trim($payload['event']          ?? '');
$transactionId = isset($payload['transaction_id']) ? (int) $payload['transaction_id'] : 0;
$gatewayRef    = trim($payload['gateway_ref']    ?? '');
$amount        = isset($payload['amount'])         ? (float) $payload['amount']        : 0.00;

if (empty($event) || $transactionId <= 0 || $amount <= 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Missing required webhook fields.']);
    exit;
}

$db = getDB();

try {
    // ── 3. Load the transaction ──────────────────────────────────────────────
    $stmtTx = $db->prepare('
        SELECT t.transaction_id,
               t.commission_id,
               t.total_amount,
               t.status_id       AS tx_status_id,
               p.payment_id,
               p.status_id       AS pay_status_id
        FROM   transaction_tbl t
        LEFT JOIN payment_tbl  p ON p.transaction_id = t.transaction_id
        WHERE  t.transaction_id = ?
        LIMIT 1
    ');
    $stmtTx->execute([$transactionId]);
    $record = $stmtTx->fetch(PDO::FETCH_ASSOC);

    if (!$record) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Transaction not found.']);
        exit;
    }

    // Idempotency: if already Paid, acknowledge without re-processing
    if ((int) $record['tx_status_id'] === 10) {
        echo json_encode(['success' => true, 'message' => 'Already processed.']);
        exit;
    }

    $db->beginTransaction();

    switch ($event) {

        // ── Payment succeeded ────────────────────────────────────────────────
        case 'payment.success':

            // Sanity-check amount matches what we expect (within ₱0.01 tolerance)
            if (abs($amount - (float) $record['total_amount']) > 0.01) {
                $db->rollBack();
                error_log("[webhook] Amount mismatch for txn {$transactionId}: "
                    . "expected {$record['total_amount']}, got {$amount}");
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Amount mismatch.']);
                exit;
            }

            // Mark transaction as Paid (status_id 10)
            $db->prepare('
                UPDATE transaction_tbl
                SET    status_id = 10
                WHERE  transaction_id = ?
            ')->execute([$transactionId]);

            // Mark payment as Paid (status_id 10)
            if (!empty($record['payment_id'])) {
                $db->prepare('
                    UPDATE payment_tbl
                    SET    status_id = 10
                    WHERE  payment_id = ?
                ')->execute([$record['payment_id']]);
            }

            // Advance commission to Completed (status_id 6)
            $db->prepare('
                UPDATE commission_tbl
                SET    status_id = 6
                WHERE  commission_id = ?
                  AND  status_id    != 6
            ')->execute([$record['commission_id']]);

            break;

        // ── Payment failed ───────────────────────────────────────────────────
        case 'payment.failed':

            // Revert transaction to Pending (status_id 2) so user can retry
            $db->prepare('
                UPDATE transaction_tbl
                SET    status_id = 2
                WHERE  transaction_id = ?
            ')->execute([$transactionId]);

            if (!empty($record['payment_id'])) {
                $db->prepare('
                    UPDATE payment_tbl
                    SET    status_id = 2
                    WHERE  payment_id = ?
                ')->execute([$record['payment_id']]);
            }

            break;

        // ── Payment refunded ─────────────────────────────────────────────────
        case 'payment.refunded':

            // Mark transaction as Cancelled (status_id 7)
            $db->prepare('
                UPDATE transaction_tbl
                SET    status_id = 7
                WHERE  transaction_id = ?
            ')->execute([$transactionId]);

            if (!empty($record['payment_id'])) {
                $db->prepare('
                    UPDATE payment_tbl
                    SET    status_id = 7
                    WHERE  payment_id = ?
                ')->execute([$record['payment_id']]);
            }

            // Revert commission back to In Progress (status_id 5) so it can be re-paid
            $db->prepare('
                UPDATE commission_tbl
                SET    status_id = 5
                WHERE  commission_id = ?
            ')->execute([$record['commission_id']]);

            break;

        default:
            $db->rollBack();
            // Unknown event — acknowledge receipt so gateway doesn't retry, but log it
            error_log("[webhook] Unknown event type: {$event} for txn {$transactionId}");
            echo json_encode(['success' => true, 'message' => 'Event ignored.']);
            exit;
    }

    $db->commit();

    // Log the gateway reference for audit trail
    error_log("[webhook] Event '{$event}' processed for txn {$transactionId}. Gateway ref: {$gatewayRef}");

    http_response_code(200);
    echo json_encode(['success' => true, 'message' => "Event '{$event}' handled."]);
} catch (PDOException $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log('[webhook.php] PDOException: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error.']);
}