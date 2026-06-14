<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$db   = getDB();
$role = strtolower($_SESSION['role']);
$id   = $_SESSION['user_id']; // Contains user_id / artist_id / admin_id

try {
    if ($role === 'user') {
        $stmt = $db->prepare('
            SELECT
                a.account_id, a.username, a.email, a.phone, a.first_name, a.middle_name, a.last_name,
                a.account_status_id, ast.status_name AS account_status, a.creation_date,
                u.user_id,
                img.image_url AS avatar_url
            FROM account_tbl a
            JOIN account_status_tbl ast ON a.account_status_id = ast.account_status_id
            JOIN user_tbl u             ON a.account_id = u.account_id
            LEFT JOIN image_tbl img     ON img.user_id = u.user_id AND img.image_type_id = 1
            WHERE u.user_id = ?
        ');
        $stmt->execute([$id]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($profile) {
            // Fetch all saved payment methods for this user
            $stmtPm = $db->prepare('
                SELECT upm.user_payment_method_id, upm.payment_method_id, pm.payment_method_name,
                       upm.mobile_number, upm.email_address, upm.card_number, upm.card_expiry,
                       upm.bank_name, upm.account_number, upm.is_default
                FROM user_payment_method_tbl upm
                JOIN payment_method_tbl pm ON upm.payment_method_id = pm.payment_method_id
                WHERE upm.user_id = ?
                ORDER BY upm.is_default DESC, upm.created_at ASC
            ');
            $stmtPm->execute([$profile['user_id']]);
            $profile['payment_methods'] = $stmtPm->fetchAll(PDO::FETCH_ASSOC);
        }

    } elseif ($role === 'artist') {
        $stmt = $db->prepare('
            SELECT
                a.account_id, a.username, a.email, a.phone, a.first_name, a.middle_name, a.last_name,
                a.account_status_id, ast.status_name AS account_status, a.creation_date,
                art.artist_id, art.starting_rate, art.is_available, art.artist_description,
                img.image_url AS avatar_url
            FROM account_tbl a
            JOIN account_status_tbl ast ON a.account_status_id = ast.account_status_id
            JOIN artist_tbl art         ON a.account_id = art.account_id
            LEFT JOIN image_tbl img     ON img.artist_id = art.artist_id AND img.image_type_id = 1
            WHERE art.artist_id = ?
        ');
        $stmt->execute([$id]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($profile) {
            // Fetch the artist's saved payout method (stored in user_payment_method_tbl
            // via a user_id that matches the artist's own user account, if they have one,
            // OR we store it keyed directly — here we resolve via account_id → user_tbl)
            $stmtUserId = $db->prepare('SELECT user_id FROM user_tbl WHERE account_id = ?');
            $stmtUserId->execute([$profile['account_id']]);
            $userRow = $stmtUserId->fetch(PDO::FETCH_ASSOC);

            if ($userRow) {
                $stmtPayout = $db->prepare('
                    SELECT upm.user_payment_method_id, upm.payment_method_id, pm.payment_method_name,
                           upm.mobile_number, upm.email_address, upm.card_number, upm.card_expiry,
                           upm.bank_name, upm.account_number, upm.is_default
                    FROM user_payment_method_tbl upm
                    JOIN payment_method_tbl pm ON upm.payment_method_id = pm.payment_method_id
                    WHERE upm.user_id = ?
                    ORDER BY upm.is_default DESC, upm.created_at ASC
                ');
                $stmtPayout->execute([$userRow['user_id']]);
                $profile['payout_methods'] = $stmtPayout->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $profile['payout_methods'] = [];
            }
        }

    } elseif ($role === 'admin') {
        $stmt = $db->prepare('
            SELECT
                a.account_id, a.username, a.email, a.first_name, a.last_name,
                ast.status_name AS account_status, a.creation_date
            FROM account_tbl a
            JOIN account_status_tbl ast ON a.account_status_id = ast.account_status_id
            JOIN administrator_tbl adm  ON a.account_id = adm.account_id
            WHERE adm.admin_id = ?
        ');
        $stmt->execute([$id]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($profile) {
        $profile['role'] = $role;
        echo json_encode(['success' => true, 'data' => $profile]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Profile not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}