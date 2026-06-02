<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data        = json_decode(file_get_contents('php://input'), true);
$description = trim($data['description']);
$budget      = floatval($data['budget']);
$db          = getDB();

$stmt = $db->prepare('
    INSERT INTO commission_tbl (user_id, artist_id, description, status, commission_date, price)
    VALUES (?, NULL, ?, ?, ?, ?)
');
$stmt->execute([
    $_SESSION['user_id'],
    $description,
    'open',
    date('Y-m-d'),
    $budget
]);

echo json_encode(['success' => true, 'message' => 'Commission posted successfully']);

?>