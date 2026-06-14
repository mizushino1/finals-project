<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || ($_SESSION['role'] !== 'user' && $_SESSION['role'] !== 'client')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

// Switch to $_POST since we now receive multipart/form-data (for file upload)
$title       = trim($_POST['title']       ?? '');
$description = trim($_POST['description'] ?? '');
$budget      = floatval($_POST['budget']  ?? 0);
$categoryId  = intval($_POST['category_id'] ?? 0);

if (empty($title))       { echo json_encode(['success' => false, 'message' => 'Please provide a commission name.']);         exit; }
if (empty($description)) { echo json_encode(['success' => false, 'message' => 'Please provide a project description.']);    exit; }
if ($budget <= 0)        { echo json_encode(['success' => false, 'message' => 'Please enter a valid budget higher than ₱0.']); exit; }
if (empty($categoryId))  { echo json_encode(['success' => false, 'message' => 'Please select a category.']);                exit; }

$db     = getDB();
$userId = $_SESSION['user_id'];

try {
    $fullDescription = $title . "\n\n" . $description;

    $stmt = $db->prepare('
        INSERT INTO commission_tbl (user_id, artist_id, description, status_id, commission_date, price, category_id)
        VALUES (?, NULL, ?, 1, NOW(), ?, ?)
    ');
    $stmt->execute([$userId, $fullDescription, $budget, $categoryId]);
    $commissionId = (int) $db->lastInsertId();

    // ── Handle reference image upload ──────────────────────
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file     = $_FILES['image'];
        $allowed  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mimeType = mime_content_type($file['tmp_name']);

        if (!in_array($mimeType, $allowed)) {
            // Commission was created; just skip the image with a warning
            echo json_encode(['success' => true, 'message' => 'Commission posted, but image type is not allowed (jpeg/png/gif/webp only).']);
            exit;
        }

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'commission_' . $commissionId . '_ref_' . time() . '.' . $ext;
        $uploadDir = '../../public/uploads/commissions/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            $imageUrl = 'public/uploads/commissions/' . $filename;

            $imgStmt = $db->prepare('
                INSERT INTO image_tbl (commission_id, image_type_id, image_url)
                VALUES (?, 4, ?)
            ');
            $imgStmt->execute([$commissionId, $imageUrl]);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Commission posted! Artists can now submit bids.']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to publish commission: ' . $e->getMessage()]);
}