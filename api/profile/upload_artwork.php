<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Only artists can upload artworks
$role = strtolower($_SESSION['role'] ?? '');
$id   = $_SESSION['user_id'] ?? null;

if (!$id || $role !== 'artist') {
    echo json_encode(['success' => false, 'message' => 'Only artists can upload artworks']);
    exit;
}

if (!isset($_FILES['artwork']) || $_FILES['artwork']['error'] !== UPLOAD_ERR_OK) {
    $uploadErrors = [
        UPLOAD_ERR_INI_SIZE   => 'File exceeds server upload limit',
        UPLOAD_ERR_FORM_SIZE  => 'File exceeds form upload limit',
        UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE    => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION  => 'Upload blocked by server extension',
    ];
    $errCode = $_FILES['artwork']['error'] ?? UPLOAD_ERR_NO_FILE;
    echo json_encode(['success' => false, 'message' => $uploadErrors[$errCode] ?? 'Unknown upload error']);
    exit;
}

$title       = trim($_POST['title']       ?? '');
$description = trim($_POST['description'] ?? '');
$category_id = intval($_POST['category_id'] ?? 0);

if (empty($title)) {
    echo json_encode(['success' => false, 'message' => 'Artwork title is required']);
    exit;
}

try {
    $db = getDB();

    // Resolve artist_id from session
    $stmt = $db->prepare('SELECT artist_id FROM artist_tbl WHERE artist_id = ?');
    $stmt->execute([$id]);
    $artist = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$artist) {
        echo json_encode(['success' => false, 'message' => 'Artist profile not found']);
        exit;
    }

    $artist_id = $artist['artist_id'];

    // Validate file type
    $file     = $_FILES['artwork'];
    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $maxBytes = 10 * 1024 * 1024; // 10 MB

    if (!in_array($ext, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Only JPG, PNG, GIF and WEBP files are allowed']);
        exit;
    }

    if ($file['size'] > $maxBytes) {
        echo json_encode(['success' => false, 'message' => 'File size must not exceed 10 MB']);
        exit;
    }

    // Verify it is actually an image (not just a renamed file)
    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        echo json_encode(['success' => false, 'message' => 'Uploaded file is not a valid image']);
        exit;
    }

    // Build upload path
    $uploadDir = __DIR__ . '/../../public/uploads/artworks/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $newFilename = md5(uniqid($artist_id, true)) . '.' . $ext;
    $destination = $uploadDir . $newFilename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        echo json_encode(['success' => false, 'message' => 'Failed to save artwork file']);
        exit;
    }

    $relUrl = 'public/uploads/artworks/' . $newFilename;

    $db->beginTransaction();

    // image_type_id = 2 → 'Artwork' (per your INSERT into image_type_tbl)
    $stmt = $db->prepare('
        INSERT INTO image_tbl (artist_id, image_url, image_type_id, uploaded_at)
        VALUES (?, ?, 2, NOW())
    ');
    $stmt->execute([$artist_id, $relUrl]);
    $image_id = $db->lastInsertId();

    // If a portfolio exists for this artist, attach the image there too.
    // Otherwise create a default portfolio first.
    $stmt = $db->prepare('SELECT portfolio_id FROM portfolio_tbl WHERE artist_id = ? LIMIT 1');
    $stmt->execute([$artist_id]);
    $portfolio = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$portfolio) {
        $stmt = $db->prepare('
            INSERT INTO portfolio_tbl (artist_id, title, description, created_at)
            VALUES (?, ?, ?, NOW())
        ');
        $stmt->execute([$artist_id, 'My Portfolio', null]);
        $portfolio_id = $db->lastInsertId();
    } else {
        $portfolio_id = $portfolio['portfolio_id'];
    }

    $stmt = $db->prepare('
        INSERT INTO portfolio_image_tbl (portfolio_id, image_url)
        VALUES (?, ?)
    ');
    $stmt->execute([$portfolio_id, $relUrl]);

    $db->commit();

    echo json_encode([
        'success'   => true,
        'message'   => 'Artwork uploaded successfully',
        'image_url' => $relUrl,
        'image_id'  => (int) $image_id,
    ]);

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}