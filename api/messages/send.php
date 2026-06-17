<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// ── Auth Guard ────────────────────────────────────────────────────────────────
if (!isset($_SESSION['user_id']) || !in_array(strtolower($_SESSION['role']), ['user', 'client', 'artist'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$db   = getDB();
$role = strtolower($_SESSION['role']);

// ── Input Parsing ─────────────────────────────────────────────────────────────
// Support both JSON (text-only) and multipart/form-data (with optional image).
$isMultipart = isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'multipart/form-data');

if ($isMultipart) {
    $receiverId = isset($_POST['receiver_id'])      ? intval($_POST['receiver_id'])       : 0;
    $content    = isset($_POST['message_content'])  ? trim($_POST['message_content'])     : '';
} else {
    $data       = json_decode(file_get_contents('php://input'), true) ?? [];
    $receiverId = isset($data['receiver_id'])       ? intval($data['receiver_id'])        : 0;
    $content    = isset($data['message_content'])   ? trim($data['message_content'])      : '';
}

// ── Validation ────────────────────────────────────────────────────────────────
if ($receiverId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing message recipient identifier.']);
    exit;
}

$hasImage = $isMultipart && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK;

if (empty($content) && !$hasImage) {
    echo json_encode(['success' => false, 'message' => 'Message must contain text or an image.']);
    exit;
}

// ── Image Upload Config ───────────────────────────────────────────────────────
define('UPLOAD_DIR',      __DIR__ . '/../../public/uploads/messages/');
define('UPLOAD_URL_BASE', '/finals-project/public/uploads/messages/');
define('MAX_IMAGE_BYTES', 5 * 1024 * 1024);   // 5 MB
define('ALLOWED_MIME',    ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

try {
    // ── Resolve Sender account_id ─────────────────────────────────────────────
    $rawId = $_SESSION['user_id'];
    if ($role === 'artist') {
        $stmtAcc = $db->prepare("SELECT account_id FROM artist_tbl WHERE artist_id = ?");
    } else {
        $stmtAcc = $db->prepare("SELECT account_id FROM user_tbl WHERE user_id = ?");
    }
    $stmtAcc->execute([$rawId]);
    $senderAccountId = $stmtAcc->fetchColumn() ?: $rawId;

    // ── Resolve Sender's profile sub-ID (for image_tbl FK) ───────────────────
    $senderUserId   = null;
    $senderArtistId = null;
    if ($role === 'artist') {
        $senderArtistId = $rawId;
    } else {
        $senderUserId = $rawId;
    }

    // ── Handle Image Upload ───────────────────────────────────────────────────
    $imageId = null;

    if ($hasImage) {
        $file     = $_FILES['image'];
        $tmpPath  = $file['tmp_name'];
        $origName = basename($file['name']);
        $fileSize = $file['size'];

        // Size check
        if ($fileSize > MAX_IMAGE_BYTES) {
            echo json_encode(['success' => false, 'message' => 'Image exceeds the 5 MB size limit.']);
            exit;
        }

        // MIME check (use finfo for reliability)
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($tmpPath);
        if (!in_array($mimeType, ALLOWED_MIME, true)) {
            echo json_encode(['success' => false, 'message' => 'Only JPEG, PNG, GIF, and WebP images are allowed.']);
            exit;
        }

        // Build a unique filename
        $ext      = pathinfo($origName, PATHINFO_EXTENSION) ?: 'jpg';
        $newName  = uniqid('msg_', true) . '.' . strtolower($ext);
        $destPath = UPLOAD_DIR . $newName;
        $imageUrl = UPLOAD_URL_BASE . $newName;

        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }

        if (!move_uploaded_file($tmpPath, $destPath)) {
            echo json_encode(['success' => false, 'message' => 'Failed to save uploaded image.']);
            exit;
        }

        // image_type_id = 4  → "message" (add this type to image_type_tbl if absent)
        // Fallback: use image_type_id = 1 if your seeder has only profile/portfolio types.
        $imageTypeId = 4;

        $stmtImg = $db->prepare("
            INSERT INTO image_tbl (image_url, image_type_id, user_id, artist_id, uploaded_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmtImg->execute([$imageUrl, $imageTypeId, $senderUserId, $senderArtistId]);
        $imageId = (int) $db->lastInsertId();
    }

    // ── Insert Message ────────────────────────────────────────────────────────
    $safeContent = $content !== '' ? $content : null;  // allow NULL when image-only

    $stmt = $db->prepare("
        INSERT INTO message_tbl
            (sender_account_id, receiver_account_id, message_content, sent_at, status_id, conversation_id, image_id)
        VALUES (
            ?, ?, ?,
            NOW(),
            (SELECT status_id FROM status_tbl WHERE LOWER(status_name) = 'unread'),
            NULL,
            ?
        )
    ");
    $stmt->execute([$senderAccountId, $receiverId, $safeContent, $imageId]);
    $newMessageId = (int) $db->lastInsertId();

    // ── Return the persisted message so the UI can append it immediately ──────
    $fetchStmt = $db->prepare("
        SELECT m.message_id, m.sender_account_id, m.receiver_account_id,
               m.message_content, m.sent_at, m.status_id,
               img.image_url AS image_url
        FROM message_tbl m
        LEFT JOIN image_tbl img ON img.image_id = m.image_id
        WHERE m.message_id = ?
    ");
    $fetchStmt->execute([$newMessageId]);
    $newMessage = $fetchStmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'message' => 'Message dispatched successfully.',
        'data'    => $newMessage,
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database failure during transit: ' . $e->getMessage(),
    ]);
}
