<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/middleware/auth_middleware.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Map session tracking fields safely.
// $_SESSION['user_id'] holds sub-table IDs (user_id/artist_id/admin_id), NOT account_id.
$id   = $_SESSION['user_id'] ?? null;
$role = strtolower($_SESSION['role'] ?? '');

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Collect form inputs
$first_name  = trim($_POST['first_name']  ?? '');
$middle_name = trim($_POST['middle_name'] ?? '');
$last_name   = trim($_POST['last_name']   ?? '');
$username    = trim($_POST['username']    ?? '');
$email       = trim($_POST['email']       ?? '');
$phone       = trim($_POST['phone']       ?? '');
$delete_avatar = intval($_POST['delete_avatar'] ?? 0);

if (empty($first_name) || empty($last_name) || empty($username) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'First name, last name, username and email are required']);
    exit;
}

try {
    $db = getDB();
    $db->beginTransaction();

    // 1. DYNAMIC REVERSE-LOOKUP: Trace the exact primary account_id using the session's sub-profile ID
    $account_id = null;
    
    if ($role === 'user') {
        $stmt = $db->prepare('SELECT account_id FROM user_tbl WHERE user_id = ?');
        $stmt->execute([$id]);
        $account_id = $stmt->fetchColumn();
    } elseif ($role === 'artist') {
        $stmt = $db->prepare('SELECT account_id FROM artist_tbl WHERE artist_id = ?');
        $stmt->execute([$id]);
        $account_id = $stmt->fetchColumn();
    } elseif ($role === 'admin') {
        $stmt = $db->prepare('SELECT account_id FROM administrator_tbl WHERE admin_id = ?');
        $stmt->execute([$id]);
        $account_id = $stmt->fetchColumn();
    }

    // Safety fallback: Halt processing if the database cannot resolve who owns this identity session context
    if (!$account_id) {
        throw new Exception('Account context mapping mismatch error.');
    }

    // 2. Securely update the master credentials/personal info row matching the resolved account record
    $stmt = $db->prepare('
        UPDATE account_tbl
        SET first_name = ?, middle_name = ?, last_name = ?, username = ?, email = ?, phone = ?
        WHERE account_id = ?
    ');
    $stmt->execute([$first_name, $middle_name ?: null, $last_name, $username, $email, $phone ?: null, $account_id]);

    $_SESSION['username'] = $username;

    // 3. Update descriptive fields on the extension tables using the session ID ($id)
    if ($role === 'artist') {
        $starting_rate      = floatval($_POST['starting_rate'] ?? 0);
        $is_available       = isset($_POST['is_available']) ? 1 : 0;
        $artist_description = trim($_POST['artist_description'] ?? '');

        $stmt = $db->prepare('
            UPDATE artist_tbl
            SET starting_rate = ?, is_available = ?, artist_description = ?
            WHERE artist_id = ?
        ');
        $stmt->execute([$starting_rate, $is_available, $artist_description ?: null, $id]);
    }

    if ($role === 'user') {
        $card_number = trim($_POST['card_number'] ?? '');
        $stmt = $db->prepare('
            UPDATE user_tbl SET card_number = ? WHERE user_id = ?
        ');
        $stmt->execute([$card_number ?: null, $id]);
    }

    // 4. Secure Password Updating block
    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password']     ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!empty($current_password) && !empty($new_password)) {
        $stmt = $db->prepare('SELECT password_hash FROM account_tbl WHERE account_id = ?');
        $stmt->execute([$account_id]);
        $hash = $stmt->fetchColumn();

        if (!password_verify($current_password, $hash)) {
            throw new Exception('Current password is incorrect');
        }
        if ($new_password !== $confirm_password) {
            throw new Exception('New passwords do not match');
        }

        $db->prepare('UPDATE account_tbl SET password_hash = ? WHERE account_id = ?')
           ->execute([password_hash($new_password, PASSWORD_BCRYPT), $account_id]);
    }

    // 5. Image & Avatar File Management Block
    if ($role === 'user' || $role === 'artist') {
        $col = ($role === 'user') ? 'user_id' : 'artist_id';

        if ($delete_avatar === 1) {
            $db->prepare("DELETE FROM image_tbl WHERE {$col} = ? AND image_type_id = 1")
               ->execute([$id]);

        } elseif (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $ext     = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png'];

            if (!in_array($ext, $allowed)) {
                throw new Exception('Only JPG and PNG files are allowed');
            }

            // Generated filename hash utilizes master account_id for integrity across tracking points
            $newFile   = md5(time() . $account_id) . '.' . $ext;
            $uploadDir = __DIR__ . '/../../public/uploads/avatars/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $newFile)) {
                throw new Exception('Failed to save avatar file');
            }

            $relUrl = 'public/uploads/avatars/' . $newFile;

            $db->prepare("
                INSERT INTO image_tbl ({$col}, image_url, image_type_id, uploaded_at)
                VALUES (?, ?, 1, NOW())
                ON DUPLICATE KEY UPDATE image_url = ?, uploaded_at = NOW()
            ")->execute([$id, $relUrl, $relUrl]);
        }
    }

    $db->commit();
    echo json_encode(['success' => true, 'message' => 'Settings saved successfully']);

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}