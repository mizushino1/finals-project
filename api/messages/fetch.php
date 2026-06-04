<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$db        = getDB();
$role      = strtolower($_SESSION['role']);
$currentId = $_SESSION['user_id']; // Current logged-in user's account_id
$targetId  = 0;

// Determine target based on who is asking
if ($role === 'user' || $role === 'client') {
    $targetId = isset($_GET['artist_id']) ? intval($_GET['artist_id']) : 0;
} elseif ($role === 'artist') {
    $targetId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
} else {
    echo json_encode(['success' => false, 'message' => 'Admin role cannot view chat streams directly.']);
    exit;
}

if ($targetId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing target profile identifier.']);
    exit;
}

try {
    /**
     * NOTE ON SCHEMA NORMALIZATION:
     * If your message_box table stores artist_id instead of their base account_id, 
     * uncomment the block below to map the account variables cleanly.
     */
    /*
    if ($role === 'artist') {
        $stmtArt = $db->prepare('SELECT artist_id FROM artist_tbl WHERE account_id = ?');
        $stmtArt->execute([$currentId]);
        $artRow = $stmtArt->fetch(PDO::FETCH_ASSOC);
        if ($artRow) { $currentId = $artRow['artist_id']; }
    } else {
        $stmtArt = $db->prepare('SELECT artist_id FROM artist_tbl WHERE account_id = ?');
        $stmtArt->execute([$targetId]);
        $artRow = $stmtArt->fetch(PDO::FETCH_ASSOC);
        if ($artRow) { $targetId = $artRow['artist_id']; }
    }
    */

    // 1. Fetch both sides of the conversation (Sent OR Received)
    $stmt = $db->prepare('
        SELECT * FROM message_box
        WHERE (sender_id = ? AND receiver_id = ?)
           OR (sender_id = ? AND receiver_id = ?)
        ORDER BY sent_at ASC
    ');
    $stmt->execute([$currentId, $targetId, $targetId, $currentId]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Safely mark messages from ONLY this specific sender as read
    $updateStmt = $db->prepare('
        UPDATE message_box 
        SET status = "read" 
        WHERE receiver_id = ? AND sender_id = ? AND status = "unread"
    ');
    $updateStmt->execute([$currentId, $targetId]);

    echo json_encode([
        'success' => true, 
        'data' => $messages ? $messages : []
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database operation error: ' . $e->getMessage()
    ]);
}
?>