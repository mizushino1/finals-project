<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$roleInput = isset($data['role']) ? trim($data['role']) : ''; // 'user' or 'artist'
$firstName = isset($data['first_name']) ? trim($data['first_name']) : '';
$lastName  = isset($data['last_name']) ? trim($data['last_name']) : '';
$username  = isset($data['username']) ? trim($data['username']) : '';
$password  = isset($data['password']) ? trim($data['password']) : '';
$email     = isset($data['email']) ? trim($data['email']) : ''; // Added: Required field in your schema
$startAt   = isset($data['start_at']) ? floatval($data['start_at']) : 0.00;

// Basic validation
if (empty($roleInput) || empty($firstName) || empty($lastName) || empty($username) || empty($password) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'All mandatory fields must be filled.']);
    exit;
}

$db = getDB();

try {
    // 1. Resolve Lookup IDs based on your database configuration
    // Roles
    $stmtRole = $db->prepare('SELECT role_id, role_name FROM role_tbl WHERE LOWER(role_name) = ?');
    $stmtRole->execute([strtolower($roleInput)]);
    $roleRow = $stmtRole->fetch(PDO::FETCH_ASSOC);

    if (!$roleRow) {
        echo json_encode(['success' => false, 'message' => 'Invalid or unrecognized account role.']);
        exit;
    }
    $roleId = $roleRow['role_id'];

    // Default Account Status (Assuming 'Active' by default)
    $stmtStatus = $db->prepare("SELECT account_status_id FROM account_status_tbl WHERE status_name = 'Active'");
    $stmtStatus->execute();
    $statusRow = $stmtStatus->fetch(PDO::FETCH_ASSOC);
    $statusId = $statusRow ? $statusRow['account_status_id'] : 1;

    // 2. Uniqueness Checks (Username and Email are UNIQUE in account_tbl)
    $stmtCheck = $db->prepare('SELECT username, email FROM account_tbl WHERE username = ? OR email = ?');
    $stmtCheck->execute([$username, $email]);
    $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        if (strcasecmp($existing['username'], $username) === 0) {
            echo json_encode(['success' => false, 'message' => 'Username is already taken.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Email address is already registered.']);
        }
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 3. Begin Transaction to prevent fragmented records
    $db->beginTransaction();

    // Insert into Base Account Table
    $insertAccountQuery = '
        INSERT INTO account_tbl (role_id, account_status_id, username, password_hash, first_name, last_name, email)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ';
    $stmtAccount = $db->prepare($insertAccountQuery);
    $stmtAccount->execute([$roleId, $statusId, $username, $hashedPassword, $firstName, $lastName, $email]);
    
    // Grab the auto-incremented account_id
    $accountId = $db->lastInsertId();

    // 4. Insert into the target specialized profile table
    $roleNormalized = strtolower($roleRow['role_name']);
    
    if ($roleNormalized === 'user' || $roleNormalized === 'client') {
        $stmtProfile = $db->prepare('INSERT INTO user_tbl (account_id) VALUES (?)');
        $stmtProfile->execute([$accountId]);
        $msg = 'Account created successfully!';

    } elseif ($roleNormalized === 'artist') {
        // Schema field name: starting_rate
        $stmtProfile = $db->prepare('INSERT INTO artist_tbl (account_id, starting_rate) VALUES (?, ?)');
        $stmtProfile->execute([$accountId, $startAt]);
        $msg = 'Artist account created successfully!';
    } else {
        // Fallback safety safety rollback
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Unauthorized profile insertion attempt.']);
        exit;
    }

    // Commit changes if everything goes smoothly
    $db->commit();
    echo json_encode(['success' => true, 'message' => $msg]);

} catch (PDOException $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    // Debug helper (remove $e->getMessage() in production environments)
    echo json_encode(['success' => false, 'message' => 'Registration database error: ' . $e->getMessage()]);
}

?>