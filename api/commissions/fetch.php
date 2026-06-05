<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

try {
    $db   = getDB();
    $role = strtolower($_SESSION['role']); // Handle casing safely
    $id   = $_SESSION['user_id']; // This is the account_id from account_tbl

    // 1. ARTIST: Sees all open commission posts from users/clients
    if ($role === 'artist') {
        $stmt = $db->prepare('
            SELECT 
                c.*, 
                a.username AS posted_by,
                a.first_name,
                a.last_name
            FROM commission_tbl c
            JOIN account_tbl a ON c.user_id = a.account_id
            WHERE c.status_id = 2   -- Pending
WHERE c.status_id = 1   -- Active/Open
SET status_id = 3       -- Accepted
            ORDER BY c.commission_date DESC
        ');
        $stmt->execute();

        // 2. USER/CLIENT: Sees only their own submitted requests
    } elseif ($role === 'user' || $role === 'client') {
        $stmt = $db->prepare('
            SELECT c.*
            FROM commission_tbl c
            WHERE c.user_id = ?
            ORDER BY c.commission_date DESC
        ');
        $stmt->execute([$id]);

        // 3. ADMIN: Sees absolutely everything across the system
    } elseif ($role === 'admin') {
        $stmt = $db->prepare('
            SELECT 
                c.*, 
                a.username AS posted_by,
                a.first_name,
                a.last_name
            FROM commission_tbl c
            JOIN account_tbl a ON c.user_id = a.account_id
            ORDER BY c.commission_date DESC
        ');
        $stmt->execute();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid account role profile.']);
        exit;
    }

    // Fetch all results as associative array
    $commissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $commissions ? $commissions : []
    ]);
} catch (PDOException $e) {
    // Return structured JSON error for your frontend JavaScript fetch catch block
    echo json_encode([
        'success' => false,
        'message' => 'Database operation failed: ' . $e->getMessage()
    ]);
}
