<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// 1. Verify Authentication Safeguard
if (!isset($_SESSION['account_id']) || !isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$db   = getDB();
$role = strtolower($_SESSION['role']);
$id   = $_SESSION['account_id']; 

try {
    // 2. Query data matching your actual database schema
    if ($role === 'user' || $role === 'client') {
        $stmt = $db->prepare('
            SELECT 
                a.account_id, 
                a.username, 
                a.email,
                a.first_name, 
                a.middle_name, 
                a.last_name, 
                a.creation_date,
                s.status_name AS account_status,
                u.card_number 
            FROM account_tbl a
            JOIN account_status_tbl s ON a.account_status_id = s.account_status_id
            JOIN user_tbl u ON a.account_id = u.account_id
            WHERE a.account_id = ?
        ');
        $stmt->execute([$id]);
        
    } elseif ($role === 'artist') {
        $stmt = $db->prepare('
            SELECT 
                a.account_id, 
                a.username, 
                a.email,
                a.first_name, 
                a.last_name, 
                a.creation_date,
                s.status_name AS account_status,
                art.artist_id,
                art.starting_rate,
                art.is_available
            FROM account_tbl a
            JOIN account_status_tbl s ON a.account_status_id = s.account_status_id
            JOIN artist_tbl art ON a.account_id = art.account_id
            WHERE a.account_id = ?
        ');
        $stmt->execute([$id]);
        
    } elseif ($role === 'admin') {
        $stmt = $db->prepare('
            SELECT 
                a.account_id, 
                a.username, 
                a.email, 
                a.creation_date,
                s.status_name AS account_status
            FROM account_tbl a
            JOIN account_status_tbl s ON a.account_status_id = s.account_status_id
            WHERE a.account_id = ?
        ');
        $stmt->execute([$id]);
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid account role profile configuration.']);
        exit;
    }

    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. Return the compiled unified profile record 
    if ($profile) {
        $profile['role'] = $_SESSION['role'];
        echo json_encode(['success' => true, 'data' => $profile]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Profile record could not be found.']);
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database operation error: ' . $e->getMessage()
    ]);
}
?>