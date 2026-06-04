<?php
// 1. Initialize System Configurations and Core Lifecycle Engines
if (session_status() === PHP_SESSION_NONE) {
    require_once __DIR__ . '/../../config/session.php'; 
}
require_once __DIR__ . '/../../config/database.php';

// 2. Import and trigger your explicit routing safeguard gate
// This automatically verifies active contexts or gracefully halts unauthorized traffic
require_once __DIR__ . '/../../src/middleware/auth_middleware.php'; 

header('Content-Type: application/json');

// Verify incoming request protocol methodology matches form bindings
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request protocol methodology.']);
    exit;
}

// Fallback lookup: Ensure safe alignment with profile/fetch.php structures 
$account_id = $_SESSION['account_id'] ?? $_SESSION['user_id'] ?? null;

if (!$account_id) {
    echo json_encode(['success' => false, 'message' => 'Session identification index missing. Please re-authenticate.']);
    exit;
}

// 3. Collect sanitized input components from layout form bindings
$name        = trim($_POST['name'] ?? '');
$username    = trim($_POST['username'] ?? '');
$email       = trim($_POST['email'] ?? '');
$bio         = trim($_POST['bio'] ?? '');
$phone       = trim($_POST['phone'] ?? '');
$artist_desc = trim($_POST['artist_description'] ?? '');
$delete_avatar = intval($_POST['delete_avatar'] ?? 0);

if (empty($name) || empty($username) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Required user parameter profiles are missing.']);
    exit;
}

try {
    $db = getDB();
    $db->beginTransaction();

    // Parse the compound name back into structural components
    $parts = explode(' ', $name, 2);
    $first_name = $parts[0];
    $last_name = $parts[1] ?? '';

    // Update Master Profile Data Account structures
    $stmt = $db->prepare("
        UPDATE account_tbl 
        SET username = ?, email = ?, first_name = ?, last_name = ? 
        WHERE account_id = ?
    ");
    $stmt->execute([$username, $email, $first_name, $last_name, $account_id]);

    // 4. Conditional Sub-Tier Context Updates (User vs Artist configurations)
    $role = strtolower($_SESSION['role'] ?? '');
    
    if ($role === 'user' || $role === 'client') {
        $updateUser = $db->prepare("UPDATE user_tbl SET card_number = ? WHERE account_id = ?");
        $updateUser->execute([$phone, $account_id]);
    } elseif ($role === 'artist') {
        // If your database schema separates user details, you can update additional text fields here
        // e.g., UPDATE artist_tbl SET dynamic_description = ? WHERE account_id = ?
    }

    // 5. Explicit Security Mutation Handler (Password Update Execution)
    if (!empty($_POST['current_password']) && !empty($_POST['new_password'])) {
        $checkPwd = $db->prepare("SELECT password_hash FROM account_tbl WHERE account_id = ?");
        $checkPwd->execute([$account_id]);
        $hash = $checkPwd->fetchColumn();

        if ($hash && password_verify($_POST['current_password'], $hash)) {
            if ($_POST['new_password'] === $_POST['confirm_password']) {
                $newHash = password_hash($_POST['new_password'], PASSWORD_ARGON2ID);
                $updatePwd = $db->prepare("UPDATE account_tbl SET password_hash = ? WHERE account_id = ?");
                $updatePwd->execute([$newHash, $account_id]);
            } else {
                throw new Exception("New confirmation fields do not match structural specifications.");
            }
        } else {
            throw new Exception("The verification password context you provided is incorrect.");
        }
    }

    // 6. Avatar Media Asset Management
    if ($delete_avatar === 1) {
        $delImg = $db->prepare("DELETE FROM image_tbl WHERE account_id = ? AND image_type_id = 1");
        $delImg->execute([$account_id]);
    } elseif (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['avatar']['tmp_name'];
        $fileName = $_FILES['avatar']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $newFileName = md5(time() . $account_id) . '.' . $fileExtension;
        $uploadFileDir = __DIR__ . '/../../public/uploads/avatars/';
        
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }
        
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $relativeUrlPath = 'public/uploads/avatars/' . $newFileName;
            
            $db->prepare("
                INSERT INTO image_tbl (account_id, image_url, image_type_id) 
                VALUES (?, ?, 1) 
                ON DUPLICATE KEY UPDATE image_url = ?, uploaded_at = NOW()
            ")->execute([$account_id, $relativeUrlPath, $relativeUrlPath]);
        }
    }

    $db->commit();
    echo json_encode(['success' => true, 'message' => 'Account modifications successfully compiled.']);

} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    echo json_encode(['success' => false, 'message' => 'Operation aborted: ' . $e->getMessage()]);
}
?>