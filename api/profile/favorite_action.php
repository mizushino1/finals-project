// api/favorite_action.php
<?php
require_once '../config/session.php';
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['account_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to modify favorites.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$artist_id = intval($data['artist_id'] ?? 0);
$action = $data['action'] ?? '';
$user_account_id = $_SESSION['account_id'];

if (!$artist_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid Artist Parameter Context.']);
    exit;
}

try {
    $db = getDB();
    if ($action === 'favorite') {
        // Insert entry safely
        $stmt = $db->prepare("INSERT IGNORE INTO favorite_tbl (account_id, artist_id) VALUES (?, ?)");
        $stmt->execute([$user_account_id, $artist_id]);
    } else {
        // Delete entry safely
        $stmt = $db->prepare("DELETE FROM favorite_tbl WHERE account_id = ? AND artist_id = ?");
        $stmt->execute([$user_account_id, $artist_id]);
    }
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>