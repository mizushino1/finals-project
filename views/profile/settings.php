<?php
require_once __DIR__ . '/../../src/middleware/auth_middleware.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/database.php';

$role = strtolower($_SESSION['role'] ?? 'user');
$account_id = $_SESSION['user_id'] ?? null;
$profile = null;

if ($account_id) {
    try {
        $db = getDB();
        if ($role === 'user') {
            $stmt = $db->prepare('
                SELECT (SELECT img.image_url 
                        FROM image_tbl img 
                        WHERE img.user_id = u.user_id AND img.image_type_id = 1 
                        ORDER BY img.uploaded_at DESC LIMIT 1) AS avatar_url
                FROM account_tbl a
                JOIN user_tbl u ON a.account_id = u.account_id
                WHERE a.account_id = ?
            ');
        } else {
            $stmt = $db->prepare('
                SELECT (SELECT img.image_url 
                        FROM image_tbl img 
                        WHERE img.artist_id = art.artist_id AND img.image_type_id = 1 
                        ORDER BY img.uploaded_at DESC LIMIT 1) AS avatar_url
                FROM account_tbl a
                JOIN artist_tbl art ON a.account_id = art.account_id
                WHERE a.account_id = ?
            ');
        }
        $stmt->execute([$account_id]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Fallback gracefully
    }
}
?>

<link rel="stylesheet" href="<?= BASE_URL ?>public/css/profile.css">
<link rel="stylesheet" href="<?= BASE_URL ?>public/css/settings.css">

<main class="py-5 settings-main">
    <div class="container-fluid settings-container">
        <div class="row justify-content-center">
            <div class="col-12 settings-card">

                <h2 class="settings-title">Account Settings</h2>

                <div id="settingsAlert" class="alert d-none mb-3" role="alert"></div>

                <form id="settingsForm" enctype="multipart/form-data">
                    <input type="hidden" name="delete_avatar" id="deleteAvatarFlag" value="0">

                    <div class="row g-4">

                        <div class="col-md-6">
                            <div class="settings-group-box">
                                <h5 class="settings-section-title"><i class="bi bi-person"></i> Personal Information</h5>

                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="settings-label">First Name</label>
                                        <div class="custom-input-group">
                                            <span class="settings-addon-icon"><i class="bi bi-person"></i></span>
                                            <input type="text" class="settings-input-override" id="settingsFirstName" name="first_name" placeholder="First Name" required>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="settings-label">Middle Name</label>
                                        <div class="custom-input-group">
                                            <span class="settings-addon-icon"><i class="bi bi-person"></i></span>
                                            <input type="text" class="settings-input-override" id="settingsMiddleName" name="middle_name" placeholder="Middle Name (optional)">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="settings-label">Last Name</label>
                                        <div class="custom-input-group">
                                            <span class="settings-addon-icon"><i class="bi bi-person"></i></span>
                                            <input type="text" class="settings-input-override" id="settingsLastName" name="last_name" placeholder="Last Name" required>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="settings-label">Username</label>
                                        <div class="custom-input-group">
                                            <span class="settings-addon-icon"><i class="bi bi-person-badge"></i></span>
                                            <input type="text" class="settings-input-override" id="settingsUsername" name="username" placeholder="Username" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="settings-label">Email</label>
                                        <div class="custom-input-group">
                                            <span class="settings-addon-icon"><i class="bi bi-envelope"></i></span>
                                            <input type="email" class="settings-input-override" id="settingsEmail" name="email" placeholder="Email" required>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="settings-label">Phone</label>
                                        <div class="custom-input-group">
                                            <span class="settings-addon-icon"><i class="bi bi-telephone"></i></span>
                                            <input type="text" class="settings-input-override" id="settingsPhone" name="phone" placeholder="Phone (optional)">
                                        </div>
                                    </div>
                                </div>

                                <?php if ($role === 'user'): ?>
                                    <div class="mb-3">
                                        <label class="settings-label">Card Number</label>
                                        <div class="custom-input-group">
                                            <span class="settings-addon-icon"><i class="bi bi-credit-card"></i></span>
                                            <input type="text" class="settings-input-override" id="settingsCardNumber" name="card_number" placeholder="Card Number (optional)">
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($role === 'artist'): ?>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label class="settings-label">Starting Rate (₱)</label>
                                            <div class="custom-input-group">
                                                <span class="settings-addon-icon"><i class="bi bi-currency-exchange"></i></span>
                                                <input type="number" class="settings-input-override" id="settingsStartingRate" name="starting_rate" placeholder="e.g. 500.00" min="0" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3 d-flex align-items-end pb-1">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="settingsIsAvailable" name="is_available" value="1">
                                                <label class="form-check-label settings-label" for="settingsIsAvailable">Open for commissions</label>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="settings-group-box">
                                <h5 class="settings-section-title"><i class="bi bi-lock"></i> Security</h5>
                                <div class="mb-3">
                                    <label class="settings-label">Current Password</label>
                                    <div class="custom-input-group">
                                        <input type="password" name="current_password" class="settings-input-override">
                                        <span class="settings-addon-icon interactive-cursor eye-toggle-icon"><i class="bi bi-eye-slash"></i></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <label class="settings-label">New Password</label>
                                        <div class="custom-input-group">
                                            <input type="password" name="new_password" class="settings-input-override">
                                            <span class="settings-addon-icon interactive-cursor eye-toggle-icon"><i class="bi bi-eye-slash"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="settings-label">Confirm Password</label>
                                        <div class="custom-input-group">
                                            <input type="password" name="confirm_password" class="settings-input-override">
                                            <span class="settings-addon-icon interactive-cursor eye-toggle-icon"><i class="bi bi-eye-slash"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="settings-group-box">
                                <h5 class="settings-section-title">
                                    <i class="bi bi-person-circle"></i> Avatar / Profile Picture
                                </h5>

                                <div class="d-flex align-items-start gap-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="position-relative">
                                            <?php
                                            $cacheBuster = '?t=' . time();
                                            $hasAvatar = !empty($profile['avatar_url']);
                                            $avatarSrc = $hasAvatar ? BASE_URL . htmlspecialchars($profile['avatar_url']) . $cacheBuster : '';
                                            ?>
                                            <img src="<?= $avatarSrc ?>"
                                                id="avatarPreview"
                                                class="profile-avatar <?= $hasAvatar ? '' : 'd-none' ?>"
                                                alt="User avatar">

                                            <div id="avatarPreviewPlaceholder" class="profile-avatar-placeholder <?= $hasAvatar ? 'd-none' : 'd-inline-flex' ?> align-items-center justify-content-center bg-light border text-secondary rounded-circle">
                                                <i class="bi bi-person-fill fs-2"></i>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-3 px-3 profile-sub-weight" id="removeAvatarBtn">Remove</button>
                                    </div>

                                    <div class="flex-grow-1">
                                        <div class="border p-3 text-center">
                                            <p class="mb-2 fw-bold">Choose New Avatar</p>
                                            <small class="text-muted d-block mb-3">Drag & drop an image here or click browse here</small>
                                            <button type="button" class="btn btn-sm btn-follow w-100 px-3 profile-sub-weight" id="uploadImageBtn">Upload New Image</button>
                                            <input type="file" id="avatarFileInput" name="avatar" class="d-none" accept="image/png, image/jpeg, image/jpg">
                                        </div>
                                        <small class="text-muted mt-2 d-block text-center">Recommended PNG</small>
                                    </div>
                                </div>
                            </div>

                            <?php if ($role === 'artist'): ?>
                                <div class="settings-group-box">
                                    <div id="artistHighlightBox">
                                        <h5 class="settings-section-title"><i class="bi bi-pencil-square"></i> Artist Description</h5>
                                        <p class="text-muted small">It's about yourself and your art</p>
                                        <textarea class="settings-textarea-override" rows="4" name="artist_description" id="artistDescText" maxlength="250" placeholder="Description........"></textarea>
                                        <small class="text-muted d-block mt-1 text-end" id="charCounter">0/250</small>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="py-3">
                                <a class="btn btn-outline-secondary btn-sm px-3 profile-sub-weight" href="<?= BASE_URL ?>profile">View Profile</a>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4 align-items-center settings-footer-actions">
                        <div class="col-6 d-flex gap-2">
                            <button type="submit" id="settingsSubmit" class="btn-follow">Save Changes</button>
                            <button type="button" id="clearForm" class="btn btn-outline-secondary btn-sm px-3 btn-cancel-custom">Cancel</button>
                        </div>
                        <div class="col-6 text-end">
                            <button type="button" id="deleteAccountBtn" class="btn btn-outline-danger btn-sm btn-delete-custom">
                                <i class="bi bi-trash"></i> Delete Account
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</main>

<script src="<?= BASE_URL ?>public/js/settings.js"></script>