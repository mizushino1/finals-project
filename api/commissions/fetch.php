<?php
ob_start();
require_once '../../config/session.php';
require_once '../../config/database.php';
ob_clean();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

try {
    $db   = getDB();
    $role = strtolower($_SESSION['role']);
    $id   = $_SESSION['user_id'];

    $baseSelect = '
    SELECT
        c.*,
        a.username       AS posted_by,
        a.first_name,
        a.last_name,
        cat.category_name,
        avatar.image_url AS client_avatar_url,
        ref.image_url    AS reference_image,
        proof.image_url  AS completion_proof_url
    FROM commission_tbl c
    JOIN user_tbl        u      ON c.user_id      = u.user_id
    JOIN account_tbl     a      ON u.account_id   = a.account_id
    LEFT JOIN category_tbl  cat    ON c.category_id  = cat.category_id
    
    -- Client Avatar (Type 1)
    LEFT JOIN image_tbl     avatar ON avatar.user_id  = u.user_id AND avatar.image_type_id = 1
    
    -- Reference Image (Type 4)
    LEFT JOIN image_tbl     ref    ON ref.commission_id = c.commission_id AND ref.image_type_id = 4
    
    -- Artist Completion Proof (Type 3)
    LEFT JOIN image_tbl     proof  ON proof.commission_id = c.commission_id AND proof.image_type_id = 3
';

    if ($role === 'artist') {
        $stmt = $db->prepare($baseSelect . 'WHERE c.status_id = 1 ORDER BY c.commission_date DESC');
        $stmt->execute();
    } elseif ($role === 'user' || $role === 'client') {
        $stmt = $db->prepare($baseSelect . 'WHERE u.user_id = ? ORDER BY c.commission_date DESC');
        $stmt->execute([$id]);
    } elseif ($role === 'admin') {
        $stmt = $db->prepare($baseSelect . 'ORDER BY c.commission_date DESC');
        $stmt->execute();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid account role.']);
        exit;
    }

    $commissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data'    => $commissions ?: []
    ]);
} catch (PDOException $e) {
    error_log('PDO ERROR: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database operation failed: ' . $e->getMessage()
    ]);
}
