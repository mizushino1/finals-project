<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

$data       = json_decode(file_get_contents('php://input'), true);
$role       = trim($data['role']       ?? '');
$firstName  = trim($data['first_name'] ?? '');
$lastName   = trim($data['last_name']  ?? '');
$email      = trim($data['email']      ?? '');
$username   = trim($data['username']   ?? '');
$password   = trim($data['password']   ?? '');
$startAt    = floatval($data['start_at'] ?? 0);

if (empty($role) || empty($firstName) || empty($lastName) || empty($email) || empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

$db = getDB();

try {
    // Check username not already taken
    $stmt = $db->prepare('SELECT account_id FROM account_tbl WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Username is already taken.']);
        exit;
    }

    // Check email not already taken
    $stmt = $db->prepare('SELECT account_id FROM account_tbl WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email is already registered.']);
        exit;
    }

    // Get role_id from role_tbl
    $stmt = $db->prepare('SELECT role_id FROM role_tbl WHERE LOWER(role_name) = LOWER(?)');
    $stmt->execute([$role]);
    $roleRow = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$roleRow) {
        echo json_encode(['success' => false, 'message' => 'Invalid account type.']);
        exit;
    }

    // Active status id = 1
    $hashed = password_hash($password, PASSWORD_BCRYPT);

    $db->beginTransaction();

    // Insert into account_tbl
    $stmt = $db->prepare('
        INSERT INTO account_tbl (role_id, account_status_id, username, password_hash, first_name, last_name, email)
        VALUES (?, 1, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([$roleRow['role_id'], $username, $hashed, $firstName, $lastName, $email]);
    $accountId = $db->lastInsertId();

    // Insert into role-specific sub table
    if ($role === 'user') {
        $stmt = $db->prepare('INSERT INTO user_tbl (account_id) VALUES (?)');
        $stmt->execute([$accountId]);

    } elseif ($role === 'artist') {
        $stmt = $db->prepare('INSERT INTO artist_tbl (account_id, starting_rate, is_available) VALUES (?, ?, 1)');
        $stmt->execute([$accountId, $startAt]);
    }

    $db->commit();
    echo json_encode(['success' => true, 'message' => 'Account created successfully.']);

} catch (PDOException $e) {
    if ($db->inTransaction()) $db->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error encountered.']);
}