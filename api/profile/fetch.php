<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// 1. Verify Authentication
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$db   = getDB();
$role = $_SESSION['role'];
$id   = $_SESSION['user_id'];

try {
    // 2. Query based on the role stored in the session
    if ($role === 'user') {
        $stmt = $db->prepare('
            SELECT account_id, role, username, acc_creation_date, 
                   first_name, middle_name, last_name, account_status, card_number 
            FROM user_tbl 
            WHERE account_id = ?
        ');
        $stmt->execute([$id]);
        
    } elseif ($role === 'artist') {
        $stmt = $db->prepare('
            SELECT artist_id, role, username, acc_creation_date, 
                   first_name, last_name, account_status, start_at, card_number 
            FROM artist_tbl 
            WHERE artist_id = ?
        ');
        $stmt->execute([$id]);
        
    } elseif ($role === 'admin') {
        $stmt = $db->prepare('
            SELECT admin_id, username, role 
            FROM administrator 
            WHERE admin_id = ?
        ');
        $stmt->execute([$id]);
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid account role']);
        exit;
    }

    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. Return the profile data
    if ($profile) {
        echo json_encode(['success' => true, 'data' => $profile]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Profile not found']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>