<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// Verify Authentication (Any logged-in user, artist, or admin can view the artist marketplace)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$db = getDB();

try {
    // JOIN the tables to pull credentials from account_tbl and pricing from artist_tbl
    $stmt = $db->prepare('
        SELECT 
            art.artist_id, 
            a.account_id,
            a.username, 
            a.first_name,
            a.last_name,
            art.start_at, 
            a.account_status
        FROM artist_tbl art
        INNER JOIN account_tbl a ON art.account_id = a.account_id
        WHERE LOWER(a.account_status) = "active"
        ORDER BY art.start_at ASC
    ');
    $stmt->execute();

    $artists = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true, 
        'data' => $artists ? $artists : []
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database operation error: ' . $e->getMessage()
    ]);
}
?>