<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// 1. Allow both regular users/clients AND artists to use this communication pathway
if (!isset($_SESSION['user_id']) || !in_array(strtolower($_SESSION['role']), ['user', 'client', 'artist'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$data       = json_decode(file_get_contents('php://input'), true);
$receiverId = isset($data['receiver_id']) ? intval($data['receiver_id']) : 0;
$content    = isset($data['message_content']) ? trim($data['message_content']) : '';

// Validation Guards
if ($receiverId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing message recipient identifier.']);
    exit;
}

if (empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Message content cannot be blank.']);
    exit;
}

$db       = getDB();
$role     = strtolower($_SESSION['role']);
$senderId = $_SESSION['user_id']; // Default base profile account_id

try {
    /**
     * NOTE ON RELATIONAL SCHEMA MATCHING:
     * If your message_box table stores actual artist_id values instead of base account_ids,
     * uncomment this structural map block to normalize the keys before sending.
     */
    /*
    if ($role === 'artist') {
        // Resolve current sender's artist_id from sub-table mapping
        $stmtArt = $db->prepare('SELECT artist_id FROM artist_tbl WHERE account_id = ?');
        $stmtArt->execute([$senderId]);
        $artRow = $stmtArt->fetch(PDO::FETCH_ASSOC);
        if (!$artRow) {
            echo json_encode(['success' => false, 'message' => 'Sender artist sub-profile missing.']);
            exit;
        }
        $senderId = $artRow['artist_id'];
    } else {
        // The receiver is an artist; look up their structural artist_id if needed
        // (Only use this if your frontend passes the artist's account_id instead of their artist_id)
        $stmtArt = $db->prepare('SELECT artist_id FROM artist_tbl WHERE account_id = ?');
        $stmtArt->execute([$receiverId]);
        $artRow = $stmtArt->fetch(PDO::FETCH_ASSOC);
        if ($artRow) { $receiverId = $artRow['artist_id']; }
    }
    */

    // 2. Insert message with exact payload binding mappings
    $stmt = $db->prepare('
        INSERT INTO message_box (sender_id, receiver_id, message_content, sent_at, status)
        VALUES (?, ?, ?, NOW(), "unread")
    ');
    
    $stmt->execute([
        $senderId, 
        $receiverId, 
        $content
    ]);

    echo json_encode([
        'success' => true, 
        'message' => 'Message dispatched successfully.'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database failure during transit: ' . $e->getMessage()
    ]);
}
?>