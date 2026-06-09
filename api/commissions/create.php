<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// Ensure the session is authenticated and has a valid role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || ($_SESSION['role'] !== 'user' && $_SESSION['role'] !== 'client')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$data        = json_decode(file_get_contents('php://input'), true);
$title       = isset($data['title'])       ? trim($data['title'])       : '';
$description = isset($data['description']) ? trim($data['description']) : '';
$budget      = isset($data['budget'])      ? floatval($data['budget'])  : 0.00;
$categoryId  = isset($data['category_id']) ? intval($data['category_id']) : null;

// Validation
if (empty($title)) {
    echo json_encode(['success' => false, 'message' => 'Please provide a commission name.']);
    exit;
}

if (empty($description)) {
    echo json_encode(['success' => false, 'message' => 'Please provide a project description.']);
    exit;
}

if ($budget <= 0) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid budget higher than ₱0.']);
    exit;
}

if (empty($categoryId)) {
    echo json_encode(['success' => false, 'message' => 'Please select a category.']);
    exit;
}

$db          = getDB();
$userId      = $_SESSION['user_id'];
$sessionRole = $_SESSION['role']; // Captures exactly whether they are a 'user' or 'client'

try {
    // Combine title + description into the description field
    $fullDescription = $title . "\n\n" . $description;

    /* Optional Branching Note:
       If your 'commission_tbl' eventually gets a 'posted_by_role' column, 
       you can just add it to the column definition matrix below ($sessionRole).
    */

    $stmt = $db->prepare('
        INSERT INTO commission_tbl
            (user_id, artist_id, description, status_id, commission_date, price, category_id)
        VALUES
            (?, NULL, ?, 1, NOW(), ?, ?)
    ');

    $stmt->execute([$userId, $fullDescription, $budget, $categoryId]);

    echo json_encode([
        'success' => true,
        'message' => 'Commission posted! Artists can now submit bids.'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to publish commission: ' . $e->getMessage()
    ]);
}