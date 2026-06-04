<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// Get and clean raw input data
$data = json_decode(file_get_contents('php://input'), true);
$username = isset($data['username']) ? trim($data['username']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
    exit;
}

$db = getDB();

try {
    // 1. Fetch credentials, role name, and status name by joining lookup tables
    $query = '
        SELECT 
            a.account_id, 
            a.username, 
            a.password_hash, 
            r.role_name, 
            s.status_name AS status
        FROM account_tbl a
        INNER JOIN role_tbl r ON a.role_id = r.role_id
        INNER JOIN account_status_tbl s ON a.account_status_id = s.account_status_id
        WHERE a.username = ?
    ';
    
    $stmt = $db->prepare($query);
    $stmt->execute([$username]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Validate account existence and password
    if ($account && password_verify($password, $account['password_hash'])) {
        
        // 3. Check if account is allowed to log in
        if (strcasecmp($account['status'], 'Active') !== 0) {
            echo json_encode([
                'success' => false, 
                'message' => 'Your account is ' . strtolower($account['status']) . '.'
            ]);
            exit;
        }

        // 4. Fetch the specific profile ID depending on their role
        $specific_id = null;
        $role_lower = strtolower($account['role_name']);

        if ($role_lower === 'user' || $role_lower === 'client') { // Match whatever you inserted into role_tbl
            $pStmt = $db->prepare('SELECT user_id FROM user_tbl WHERE account_id = ?');
            $pStmt->execute([$account['account_id']]);
            $profile = $pStmt->fetch(PDO::FETCH_ASSOC);
            $specific_id = $profile ? $profile['user_id'] : null;
            $session_role = 'user';

        } elseif ($role_lower === 'artist') {
            $pStmt = $db->prepare('SELECT artist_id FROM artist_tbl WHERE account_id = ?');
            $pStmt->execute([$account['account_id']]);
            $profile = $pStmt->fetch(PDO::FETCH_ASSOC);
            $specific_id = $profile ? $profile['artist_id'] : null;
            $session_role = 'artist';

        } elseif ($role_lower === 'admin' || $role_lower === 'administrator') {
            $pStmt = $db->prepare('SELECT admin_id FROM administrator_tbl WHERE account_id = ?');
            $pStmt->execute([$account['account_id']]);
            $profile = $pStmt->fetch(PDO::FETCH_ASSOC);
            $specific_id = $profile ? $profile['admin_id'] : null;
            $session_role = 'admin';
        }

        // Set global session values
        $_SESSION['account_id'] = $account['account_id'];
        $_SESSION['user_id']    = $specific_id; // Keeps compatibility with your old code's role-specific tracking
        $_SESSION['username']   = $account['username'];
        $_SESSION['role']       = $session_role;

        // 5. Send consistent JSON structure back to JS
        echo json_encode([
            'success' => true, 
            'role' => $session_role
        ]);
        exit;
    }

    // Fallback for wrong credentials
    echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);

} catch (PDOException $e) {
    // Prevent database errors from breaking JSON format output
    echo json_encode(['success' => false, 'message' => 'Database error encountered.']);
}

?>