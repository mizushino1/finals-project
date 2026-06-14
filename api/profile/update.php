<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/middleware/auth_middleware.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// $_SESSION['user_id'] holds sub-table IDs (user_id / artist_id / admin_id), NOT account_id.
$id   = $_SESSION['user_id'] ?? null;
$role = strtolower($_SESSION['role'] ?? '');

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Collect core form inputs
$first_name   = trim($_POST['first_name']   ?? '');
$middle_name  = trim($_POST['middle_name']  ?? '');
$last_name    = trim($_POST['last_name']    ?? '');
$username     = trim($_POST['username']     ?? '');
$email        = trim($_POST['email']        ?? '');
$phone        = trim($_POST['phone']        ?? '');
$delete_avatar = intval($_POST['delete_avatar'] ?? 0);

if (empty($first_name) || empty($last_name) || empty($username) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'First name, last name, username and email are required']);
    exit;
}

try {
    $db = getDB();
    $db->beginTransaction();

    // ── 1. Reverse-lookup account_id from session's sub-profile ID ────────────
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

    if (!$account_id) {
        throw new Exception('Account context mapping mismatch error.');
    }

    // ── 2. Update master account credentials ──────────────────────────────────
    $stmt = $db->prepare('
        UPDATE account_tbl
        SET first_name = ?, middle_name = ?, last_name = ?, username = ?, email = ?, phone = ?
        WHERE account_id = ?
    ');
    $stmt->execute([$first_name, $middle_name ?: null, $last_name, $username, $email, $phone ?: null, $account_id]);

    $_SESSION['username'] = $username;

    // ── 3. Role-specific profile fields ───────────────────────────────────────
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

        // ── 3a. Artist payout method upsert ───────────────────────────────────
        // Artists receive payments, so their payout method is saved in user_payment_method_tbl
        // keyed to the user_id that shares the same account (dual-role) or their own account's user row.
        $payoutMethodId = intval($_POST['payout_method_id'] ?? 0);

        if ($payoutMethodId >= 1 && $payoutMethodId <= 5) {
            // Resolve the user_id for this artist's account
            $stmtUid = $db->prepare('SELECT user_id FROM user_tbl WHERE account_id = ?');
            $stmtUid->execute([$account_id]);
            $artistUserId = $stmtUid->fetchColumn();

            if ($artistUserId) {
                // Build credential columns based on the chosen method
                // 1=GCash 2=Maya 3=PayPal 4=Credit Card 5=Bank Transfer
                $mobileNumber  = null;
                $emailAddress  = null;
                $cardNumber    = null;
                $cardExpiry    = null;
                $bankName      = null;
                $accountNumber = null;

                switch ($payoutMethodId) {
                    case 1: // GCash
                    case 2: // Maya
                        $mobileNumber = trim($_POST['payout_mobile_number'] ?? '') ?: null;
                        break;
                    case 3: // PayPal
                        $emailAddress = trim($_POST['payout_email_address'] ?? '') ?: null;
                        break;
                    case 4: // Credit Card
                        $cardNumber = trim($_POST['payout_card_number'] ?? '') ?: null;
                        $cardExpiry = trim($_POST['payout_card_expiry'] ?? '') ?: null;
                        break;
                    case 5: // Bank Transfer
                        $bankName      = trim($_POST['payout_bank_name']      ?? '') ?: null;
                        $accountNumber = trim($_POST['payout_account_number'] ?? '') ?: null;
                        break;
                }

                // Check if a payout method already exists for this user+method combo
                $stmtCheck = $db->prepare('
                    SELECT user_payment_method_id FROM user_payment_method_tbl
                    WHERE user_id = ? AND payment_method_id = ?
                    LIMIT 1
                ');
                $stmtCheck->execute([$artistUserId, $payoutMethodId]);
                $existingId = $stmtCheck->fetchColumn();

                if ($existingId) {
                    // Update in place
                    $stmtUpsert = $db->prepare('
                        UPDATE user_payment_method_tbl
                        SET mobile_number = ?, email_address = ?, card_number = ?,
                            card_expiry = ?, bank_name = ?, account_number = ?, is_default = 1
                        WHERE user_payment_method_id = ?
                    ');
                    $stmtUpsert->execute([
                        $mobileNumber, $emailAddress, $cardNumber,
                        $cardExpiry, $bankName, $accountNumber, $existingId
                    ]);
                } else {
                    // Clear any previous default, then insert
                    $db->prepare('UPDATE user_payment_method_tbl SET is_default = 0 WHERE user_id = ?')
                       ->execute([$artistUserId]);

                    $stmtUpsert = $db->prepare('
                        INSERT INTO user_payment_method_tbl
                            (user_id, payment_method_id, mobile_number, email_address,
                             card_number, card_expiry, bank_name, account_number, is_default)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
                    ');
                    $stmtUpsert->execute([
                        $artistUserId, $payoutMethodId, $mobileNumber, $emailAddress,
                        $cardNumber, $cardExpiry, $bankName, $accountNumber
                    ]);
                }
            }
        }
    }

    // Users manage their own payment methods separately (checkout flow), 
    // so no card_number update here — user_payment_method_tbl is handled
    // via its own dedicated payment-method management endpoints.

    // ── 4. Password update ────────────────────────────────────────────────────
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

    // ── 5. Avatar file management ─────────────────────────────────────────────
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

            $newFile   = md5(time() . $account_id) . '.' . $ext;
            $uploadDir = __DIR__ . '/../../public/uploads/avatars/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $newFile)) {
                throw new Exception('Failed to save avatar file');
            }

            $relUrl = 'public/uploads/avatars/' . $newFile;

            $db->prepare("DELETE FROM image_tbl WHERE {$col} = ? AND image_type_id = 1")
               ->execute([$id]);

            $db->prepare("
                INSERT INTO image_tbl ({$col}, image_url, image_type_id, uploaded_at)
                VALUES (?, ?, 1, NOW())
            ")->execute([$id, $relUrl]);
        }
    }

    $db->commit();
    echo json_encode(['success' => true, 'message' => 'Settings saved successfully']);
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}