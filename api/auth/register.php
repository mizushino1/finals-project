<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$role      = trim($data['role']);
$firstName = trim($data['first_name']);
$lastName  = trim($data['last_name']);
$username  = trim($data['username']);
$password  = trim($data['password']);
$startAt   = isset($data['start_at']) ? floatval($data['start_at']) : 0;

$db = getDB();

// Check username not already taken in either table
$stmtU = $db->prepare('SELECT account_id FROM user_tbl WHERE username = ?');
$stmtU->execute([$username]);
$stmtA = $db->prepare('SELECT artist_id FROM artist_tbl WHERE username = ?');
$stmtA->execute([$username]);

if ($stmtU->fetch() || $stmtA->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Username is already taken']);
    exit;
}

$hashed = password_hash($password, PASSWORD_DEFAULT);
$today  = date('Y-m-d');

if ($role === 'user') {
    $stmt = $db->prepare('
        INSERT INTO user_tbl (role, username, password, acc_creation_date, last_name, first_name, account_status)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute(['user', $username, $hashed, $today, $lastName, $firstName, 'active']);
    echo json_encode(['success' => true, 'message' => 'Account created successfully']);

} elseif ($role === 'artist') {
    $stmt = $db->prepare('
        INSERT INTO artist_tbl (role, username, password, acc_creation_date, last_name, first_name, account_status, start_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute(['artist', $username, $hashed, $today, $lastName, $firstName, 'active', $startAt]);
    echo json_encode(['success' => true, 'message' => 'Artist account created successfully']);

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid account type']);
}