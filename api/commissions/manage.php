<?php
ob_start(); // buffer any accidental output
require_once '../../config/session.php';
require_once '../../config/database.php';
ob_clean(); // discard anything that slipped out
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$role = strtolower($_SESSION['role'] ?? '');
$userId = $_SESSION['user_id']; // user_tbl.user_id for clients

if ($role !== 'user' && $role !== 'client' && $role !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Only commission owners can manage this resource.']);
    exit;
}

try {
    $db = getDB();

    // ─────────────────────────────────────────────────────────
    // GET — fetch a single commission for editing (ownership check)
    // ─────────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $commissionId = isset($_GET['commission_id']) ? (int) $_GET['commission_id'] : 0;

        if (!$commissionId) {
            echo json_encode(['success' => false, 'message' => 'Missing commission_id.']);
            exit;
        }

        if ($role === 'admin') {
            $stmt = $db->prepare('
                SELECT c.commission_id, c.description, c.price, c.category_id, c.status_id,
                       img.image_url
                FROM commission_tbl c
                LEFT JOIN image_tbl img ON img.commission_id = c.commission_id AND img.image_type_id = 4
                WHERE c.commission_id = ?
                LIMIT 1
            ');
            $stmt->execute([$commissionId]);
        } else {
            $stmt = $db->prepare('
                SELECT c.commission_id, c.description, c.price, c.category_id, c.status_id,
                       img.image_url
                FROM commission_tbl c
                LEFT JOIN image_tbl img ON img.commission_id = c.commission_id AND img.image_type_id = 4
                WHERE c.commission_id = ? AND c.user_id = ?
                LIMIT 1
            ');
            $stmt->execute([$commissionId, $userId]);
        }

        $commission = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$commission) {
            echo json_encode(['success' => false, 'message' => 'Commission not found or you do not have permission to edit it.']);
            exit;
        }

        // Split "Title\n\nBody" description format used across the app
        $raw = $commission['description'] ?? '';
        $parts = explode("\n\n", $raw, 2);
        if (count($parts) === 2) {
            $title = trim($parts[0]);
            $body  = trim($parts[1]);
        } else {
            $title = '';
            $body  = trim($raw);
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'commission_id' => (int) $commission['commission_id'],
                'title'         => $title,
                'description'   => $body,
                'price'         => (float) $commission['price'],
                'category_id'   => $commission['category_id'] !== null ? (int) $commission['category_id'] : null,
                'status_id'     => (int) $commission['status_id'],
                'image_url'     => $commission['image_url'],
            ]
        ]);
        exit;
    }

    // ─────────────────────────────────────────────────────────
    // POST — update or cancel a commission
    // ─────────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Accept either FormData ($_POST) or raw JSON
        $isJson = str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json');
        $input  = $isJson
            ? (json_decode(file_get_contents('php://input'), true) ?? [])
            : $_POST;

        $commissionId = isset($input['commission_id']) ? (int) $input['commission_id'] : 0;
        $action = $input['action'] ?? 'update';

        if (!$commissionId) {
            echo json_encode(['success' => false, 'message' => 'Missing commission_id.']);
            exit;
        }

        // Verify ownership / permission and current status
        if ($role === 'admin') {
            $ownerCheck = $db->prepare('SELECT status_id FROM commission_tbl WHERE commission_id = ?');
            $ownerCheck->execute([$commissionId]);
        } else {
            $ownerCheck = $db->prepare('SELECT status_id FROM commission_tbl WHERE commission_id = ? AND user_id = ?');
            $ownerCheck->execute([$commissionId, $userId]);
        }
        $existing = $ownerCheck->fetch(PDO::FETCH_ASSOC);

        if (!$existing) {
            echo json_encode(['success' => false, 'message' => 'Commission not found or you do not have permission to edit it.']);
            exit;
        }

        // Only Active (1) or Pending (2) commissions can be edited/cancelled by their owner
        if (!in_array((int) $existing['status_id'], [1, 2], true) && $role !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'This commission can no longer be edited.']);
            exit;
        }

        // ── Cancel ───────────────────────────────────────────
        if ($action === 'cancel') {
            $cancelStmt = $db->prepare('UPDATE commission_tbl SET status_id = 7 WHERE commission_id = ?');
            $cancelStmt->execute([$commissionId]);

            echo json_encode(['success' => true, 'message' => 'Commission cancelled successfully.']);
            exit;
        }

        // ── Update (Save Draft) ──────────────────────────────
        $title       = trim($input['title'] ?? '');
        $description = trim($input['description'] ?? '');
        $budget      = isset($input['budget']) ? (float) $input['budget'] : 0;
        $categoryId  = isset($input['category_id']) ? (int) $input['category_id'] : 0;

        if ($title === '') {
            echo json_encode(['success' => false, 'message' => 'Please provide a commission name.']);
            exit;
        }
        if ($description === '') {
            echo json_encode(['success' => false, 'message' => 'Please provide a project description.']);
            exit;
        }
        if ($budget <= 0) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid budget higher than ₱0.']);
            exit;
        }
        if (!$categoryId) {
            echo json_encode(['success' => false, 'message' => 'Please select a category.']);
            exit;
        }

        $fullDescription = $title . "\n\n" . $description;

        $updateStmt = $db->prepare('
            UPDATE commission_tbl
            SET description = ?, price = ?, category_id = ?
            WHERE commission_id = ?
        ');
        $updateStmt->execute([$fullDescription, $budget, $categoryId, $commissionId]);

        // ── Handle reference image upload/replacement ─────────
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file     = $_FILES['image'];
            $allowed  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $mimeType = mime_content_type($file['tmp_name']);

            if (in_array($mimeType, $allowed)) {
                $ext       = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename  = 'commission_' . $commissionId . '_ref_' . time() . '.' . $ext;
                $uploadDir = '../../public/uploads/commissions/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                    $imageUrl = 'public/uploads/commissions/' . $filename;

                    // Replace existing reference image or insert new one
                    $imgCheck = $db->prepare('SELECT image_id FROM image_tbl WHERE commission_id = ? AND image_type_id = 4 LIMIT 1');
                    $imgCheck->execute([$commissionId]);
                    $existingImg = $imgCheck->fetch(PDO::FETCH_ASSOC);

                    if ($existingImg) {
                        $imgStmt = $db->prepare('UPDATE image_tbl SET image_url = ? WHERE image_id = ?');
                        $imgStmt->execute([$imageUrl, $existingImg['image_id']]);
                    } else {
                        $imgStmt = $db->prepare('INSERT INTO image_tbl (commission_id, image_type_id, image_url) VALUES (?, 4, ?)');
                        $imgStmt->execute([$commissionId, $imageUrl]);
                    }
                }
            }
        }

        echo json_encode(['success' => true, 'message' => 'Commission updated successfully.']);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Unsupported request method.']);

} catch (PDOException $e) {
    error_log('PDO ERROR: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database operation failed: ' . $e->getMessage()
    ]);
}