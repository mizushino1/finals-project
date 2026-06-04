<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// 1. Verify Authentication Safeguard
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$db   = getDB();
$role = strtolower($_SESSION['role']);
$id   = $_SESSION['user_id']; // This is the base account_id from account_tbl

try {
    // 2. Query data using explicit relational JOINs based on the account structure
    if ($role === 'user' || $role === 'client') {
        $stmt = $db->prepare('
            SELECT 
                a.account_id, 
                a.role, 
                a.username, 
                a.email,
                a.first_name, 
                a.middle_name, 
                a.last_name, 
                a.acc_creation_date,
                a.account_status,
                u.card_number 
            FROM account_tbl a
            JOIN user_tbl u ON a.account_id = u.account_id
            WHERE a.account_id = ?
        ');
        $stmt->execute([$id]);
        
    } elseif ($role === 'artist') {
        $stmt = $db->prepare('
            SELECT 
                a.account_id, 
                a.role, 
                a.username, 
                a.email,
                a.first_name, 
                a.last_name, 
                a.acc_creation_date,
                a.account_status,
                art.artist_id,
                art.start_at, 
                art.card_number 
            FROM account_tbl a
            JOIN artist_tbl art ON a.account_id = art.account_id
            WHERE a.account_id = ?
        ');
        $stmt->execute([$id]);
        
    } elseif ($role === 'admin') {
        // Checking base account_tbl if admin credentials reside there, 
        // or keeping mapping bound to separate administrator table if siloed.
        $stmt = $db->prepare('
            SELECT account_id, username, role, email, account_status
            FROM account_tbl
            WHERE account_id = ? AND LOWER(role) = "admin"
        ');
        $stmt->execute([$id]);
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid account role profile configuration.']);
        exit;
    }

    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. Return the compiled unified profile record 
    if ($profile) {
        echo json_encode(['success' => true, 'data' => $profile]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Profile profile record could not be found.']);
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database operation error: ' . $e->getMessage()
    ]);
}
?>