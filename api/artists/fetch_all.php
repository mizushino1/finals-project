<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// 1. Optional Authentication Check 
// (Remove this gate if you want non-logged-in visitors to browse artists!)
if (!isset($_SESSION['account_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$db = getDB();

try {
    // 2. Fetch all registered artists, joining their status and core account details
    $stmt = $db->prepare('
        SELECT 
            a.account_id,
            a.username,
            a.email,
            s.status_name AS account_status,
            art.artist_id,
            art.starting_rate,
            art.is_available
        FROM account_tbl a
        JOIN account_status_tbl s ON a.account_status_id = s.account_status_id
        JOIN artist_tbl art ON a.account_id = art.account_id
        WHERE s.status_name = "Active" AND art.is_available = 1
        ORDER BY a.username ASC
    ');
    $stmt->execute();
    
    $artists = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Dispatch unified JSON array back to browse.js
    echo json_encode([
        'success' => true,
        'data'    => $artists
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database operation error: ' . $e->getMessage()
    ]);
}
?>