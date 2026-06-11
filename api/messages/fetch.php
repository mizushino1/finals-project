<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// ── Auth Guard ────────────────────────────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$db        = getDB();
$role      = strtolower($_SESSION['role'] ?? '');
$sessionId = (int) $_SESSION['user_id'];
$action    = $_GET['action'] ?? 'conversations';

// ── Admin Block ───────────────────────────────────────────────────────────────
if ($role === 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Admin role cannot access chat streams.']);
    exit;
}

try {
    // RESOLVE SUB-PROFILE ID TO TRUE ACCOUNT_ID 
    $currentAccountId = null;

    if ($role === 'artist') {
        $stmtAcc = $db->prepare("SELECT account_id FROM artist_tbl WHERE artist_id = ?");
        $stmtAcc->execute([$sessionId]);
        $currentAccountId = $stmtAcc->fetchColumn();
    } else {
        $stmtAcc = $db->prepare("SELECT account_id FROM user_tbl WHERE user_id = ?");
        $stmtAcc->execute([$sessionId]);
        $currentAccountId = $stmtAcc->fetchColumn();
    }

    if (!$currentAccountId) {
        $currentAccountId = $sessionId;
    }

    // ── ACTION 1: FETCH CONVERSATIONS LIST ────────────────────────────────────
    if ($action === 'conversations') {
        $stmt = $db->prepare("
    SELECT 
        a.account_id                    AS contact_id,
        a.username                      AS contact_name,
        latest.message_content          AS last_message,
        latest.sent_at                  AS last_sent_at,
        COALESCE(unread.cnt, 0)         AS unread_count
    FROM (
        SELECT 
            CASE 
                WHEN sender_account_id = ? THEN receiver_account_id
                ELSE sender_account_id
            END AS contact_id,
            MAX(message_id) AS latest_message_id
        FROM message_tbl
        WHERE sender_account_id = ? OR receiver_account_id = ?
        GROUP BY contact_id
    ) thread_summary
    JOIN account_tbl a ON a.account_id = thread_summary.contact_id
    JOIN message_tbl latest ON latest.message_id = thread_summary.latest_message_id
    LEFT JOIN (
        SELECT sender_account_id, COUNT(*) AS cnt
        FROM message_tbl m
        JOIN status_tbl s ON m.status_id = s.status_id
        WHERE m.receiver_account_id = ?
          AND LOWER(s.status_name) = 'unread'
        GROUP BY sender_account_id
    ) unread ON unread.sender_account_id = thread_summary.contact_id
    ORDER BY latest.sent_at DESC
");

        $stmt->execute([
            $currentAccountId,
            $currentAccountId,
            $currentAccountId,
            $currentAccountId
        ]);

        $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $conversations]);
        exit;
    }

    // ── ACTION 2: FETCH INDIVIDUAL MESSAGES HISTORY ───────────────────
    if ($action === 'messages') {
        $targetId = isset($_GET['target_id']) ? (int)$_GET['target_id'] : 0;

        if ($targetId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid target account ID.']);
            exit;
        }

        // 1. Mark incoming unread messages from this target user as read
        $updateStmt = $db->prepare("
            UPDATE message_tbl 
            SET status_id = (SELECT status_id FROM status_tbl WHERE LOWER(status_name) = 'read')
            WHERE sender_account_id = ? 
              AND receiver_account_id = ? 
              AND status_id = (SELECT status_id FROM status_tbl WHERE LOWER(status_name) = 'unread')
        ");
        $updateStmt->execute([$targetId, $currentAccountId]);

        // 2. Fetch all chronological dialog exchanges
        $stmt = $db->prepare("
            SELECT message_id, sender_account_id, receiver_account_id, message_content, sent_at, status_id 
            FROM message_tbl
            WHERE (sender_account_id = ? AND receiver_account_id = ?)
               OR (sender_account_id = ? AND receiver_account_id = ?)
            ORDER BY sent_at ASC
        ");
        $stmt->execute([$currentAccountId, $targetId, $targetId, $currentAccountId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $messages]);
        exit;
    }

    // Fallback error if an action parameter is requested that isn't handled above
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => "Unknown action: {$action}"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
