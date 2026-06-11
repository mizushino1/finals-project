<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// ── Auth guard ────────────────────────────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$db        = getDB();
$role      = strtolower($_SESSION['role'] ?? '');
$currentId = (int) $_SESSION['user_id'];
$action    = $_GET['action'] ?? 'messages'; // messages | conversations | send

// ── Admin block ───────────────────────────────────────────────────────────────
if ($role === 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Admin role cannot access chat streams.']);
    exit;
}

// ── Helpers ───────────────────────────────────────────────────────────────────

/**
 * Resolve the target's account_id from the GET param.
 * Accepts ?target_id=  (preferred, works for all roles)
 * or legacy ?artist_id= / ?user_id= params.
 */
function resolveTargetId(array $get): int
{
    if (!empty($get['target_id'])) return (int) $get['target_id'];
    if (!empty($get['artist_id'])) return (int) $get['artist_id'];
    if (!empty($get['user_id']))   return (int) $get['user_id'];
    return 0;
}

/**
 * Map account_id → artist_id when the message_box uses artist_id as FK.
 * Uncomment the body if your schema stores artist_id instead of account_id.
 */
function resolveArtistPk(PDO $db, int $accountId): int
{
    // $stmt = $db->prepare('SELECT artist_id FROM artist_tbl WHERE account_id = ? LIMIT 1');
    // $stmt->execute([$accountId]);
    // $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // return $row ? (int) $row['artist_id'] : $accountId;
    return $accountId; // default: account_id IS the FK
}

// ── Route ─────────────────────────────────────────────────────────────────────
try {

    // ── 1. CONVERSATIONS LIST ─────────────────────────────────────────────────
    // GET ?action=conversations
    // Returns every distinct thread for the current user with the latest message
    // and an unread count (for the sidebar).
    if ($action === 'conversations') {

        $stmt = $db->prepare("
            SELECT
                p.account_id                          AS contact_id,
                p.username                            AS contact_name,
                p.profile_picture                     AS contact_avatar,
                latest.body                           AS last_message,
                latest.sent_at                        AS last_sent_at,
                COALESCE(unread.cnt, 0)               AS unread_count
            FROM (
                -- All distinct contacts who ever messaged with us
                SELECT DISTINCT
                    CASE
                        WHEN sender_id   = ? THEN receiver_id
                        WHEN receiver_id = ? THEN sender_id
                    END AS contact_id
                FROM message_box
                WHERE sender_id = ? OR receiver_id = ?
            ) contacts
            JOIN account_tbl p ON p.account_id = contacts.contact_id

            -- Latest message in each thread
            JOIN message_box latest ON latest.message_id = (
                SELECT message_id FROM message_box
                WHERE (sender_id = ?   AND receiver_id = contacts.contact_id)
                   OR (sender_id = contacts.contact_id AND receiver_id = ?)
                ORDER BY sent_at DESC
                LIMIT 1
            )

            -- Unread count per thread
            LEFT JOIN (
                SELECT sender_id, COUNT(*) AS cnt
                FROM message_box
                WHERE receiver_id = ? AND status = 'unread'
                GROUP BY sender_id
            ) unread ON unread.sender_id = contacts.contact_id

            ORDER BY latest.sent_at DESC
        ");
        // PDO named params can't repeat — use positional params instead
        $stmt->execute([
            $currentId, // CASE WHEN sender_id = ?
            $currentId, // CASE WHEN receiver_id = ?
            $currentId, // WHERE sender_id = ?
            $currentId, // OR receiver_id = ?
            $currentId, // latest: sender_id = ?
            $currentId, // latest: receiver_id = ?
            $currentId, // unread: receiver_id = ?
        ]);
        $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $conversations]);
        exit;
    }

    // ── 2. MESSAGES IN A THREAD ───────────────────────────────────────────────
    // GET ?action=messages&target_id=X
    if ($action === 'messages') {

        $targetId = resolveTargetId($_GET);
        if ($targetId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing or invalid target_id.']);
            exit;
        }

        // Optional: remap to artist PK if needed
        // $currentId = resolveArtistPk($db, $currentId);
        // $targetId  = resolveArtistPk($db, $targetId);

        // Fetch full thread
        $stmt = $db->prepare("
            SELECT
                m.message_id,
                m.sender_id,
                m.receiver_id,
                m.body,
                m.status,
                m.sent_at,
                s.username        AS sender_name,
                s.profile_picture AS sender_avatar
            FROM message_box m
            JOIN account_tbl  s ON s.account_id = m.sender_id
            WHERE (m.sender_id = :me  AND m.receiver_id = :them)
               OR (m.sender_id = :them AND m.receiver_id = :me)
            ORDER BY m.sent_at ASC
        ");
        $stmt->execute([':me' => $currentId, ':them' => $targetId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mark incoming messages as read
        $markRead = $db->prepare("
            UPDATE message_box
            SET status = 'read'
            WHERE receiver_id = :me
              AND sender_id   = :them
              AND status      = 'unread'
        ");
        $markRead->execute([':me' => $currentId, ':them' => $targetId]);

        echo json_encode(['success' => true, 'data' => $messages]);
        exit;
    }

    // ── 3. SEND A MESSAGE ─────────────────────────────────────────────────────
    // POST ?action=send  body: { target_id, body }
    if ($action === 'send') {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'POST required.']);
            exit;
        }

        // Accept JSON body or form-encoded
        $input    = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $targetId = (int) ($input['target_id'] ?? 0);
        $body     = trim($input['body'] ?? '');

        if ($targetId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing or invalid target_id.']);
            exit;
        }
        if ($body === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Message body cannot be empty.']);
            exit;
        }
        if (mb_strlen($body) > 5000) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Message exceeds 5 000 character limit.']);
            exit;
        }

        // Verify target account exists
        $check = $db->prepare('SELECT account_id FROM account_tbl WHERE account_id = ? LIMIT 1');
        $check->execute([$targetId]);
        if (!$check->fetch()) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Target user not found.']);
            exit;
        }

        $insert = $db->prepare("
            INSERT INTO message_box (sender_id, receiver_id, body, status, sent_at)
            VALUES (:sender, :receiver, :body, 'unread', NOW())
        ");
        $insert->execute([
            ':sender'   => $currentId,
            ':receiver' => $targetId,
            ':body'     => $body,
        ]);
        $newId = (int) $db->lastInsertId();

        // Return the freshly inserted row so the UI can append it immediately
        $fetch = $db->prepare("
            SELECT m.*, s.username AS sender_name, s.profile_picture AS sender_avatar
            FROM message_box m
            JOIN account_tbl s ON s.account_id = m.sender_id
            WHERE m.message_id = ?
        ");
        $fetch->execute([$newId]);
        $newMessage = $fetch->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $newMessage]);
        exit;
    }

    // ── Unknown action ────────────────────────────────────────────────────────
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => "Unknown action: {$action}"]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}