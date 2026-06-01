<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username']);
$password = trim($data['password']);
$db = getDB();

// Check user_tbl
$stmt = $db->prepare('SELECT account_id, username, password, role, account_status FROM user_tbl WHERE username = ?');
$stmt->execute([$username]);
$found = $stmt->fetch();

if ($found && password_verify($password, $found['password'])) {
    if ($found['account_status'] !== 'active') {
        echo json_encode(['success' => false, 'message' => 'Account is suspended']);
        exit;
    }
    $_SESSION['user_id']  = $found['account_id'];
    $_SESSION['username'] = $found['username'];
    $_SESSION['role']     = 'user';
    echo json_encode(['success' => true, 'role' => 'user']);
    exit;
}

// Check artist_tbl
$stmt = $db->prepare('SELECT artist_id, username, password, role, account_status FROM artist_tbl WHERE username = ?');
$stmt->execute([$username]);
$found = $stmt->fetch();

if ($found && password_verify($password, $found['password'])) {
    if ($found['account_status'] !== 'active') {
        echo json_encode(['success' => false, 'message' => 'Account is suspended']);
        exit;
    }
    $_SESSION['user_id']  = $found['artist_id'];
    $_SESSION['username'] = $found['username'];
    $_SESSION['role']     = 'artist';
    echo json_encode(['success' => true, 'role' => 'artist']);
    exit;
}

// Check administrator
$stmt = $db->prepare('SELECT admin_id, username, password FROM administrator WHERE username = ?');
$stmt->execute([$username]);
$found = $stmt->fetch();

if ($found && password_verify($password, $found['password'])) {
    $_SESSION['user_id']  = $found['admin_id'];
    $_SESSION['username'] = $found['username'];
    $_SESSION['role']     = 'admin';
    echo json_encode(['success' => true, 'role' => 'admin']);
    exit;
}

// Nothing matched
echo json_encode(['success' => false, 'message' => 'Invalid username or password']);