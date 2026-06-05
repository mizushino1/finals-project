<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$db = getDB();

try {
    $stmt = $db->prepare('
        SELECT 
            a.account_id,
            a.username,
            a.email,
            s.status_name AS account_status,
            art.artist_id,
            art.starting_rate,
            art.is_available,
            art.artist_description,
            img.image_url AS avatar_url
        FROM account_tbl a
        JOIN account_status_tbl s ON a.account_status_id = s.account_status_id
        JOIN artist_tbl art ON a.account_id = art.account_id
        LEFT JOIN image_tbl img ON img.artist_id = art.artist_id AND img.image_type_id = 1
        WHERE s.status_name = "Active"
        ORDER BY a.username ASC
    ');
    $stmt->execute();

    $artists = $stmt->fetchAll(PDO::FETCH_ASSOC);

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