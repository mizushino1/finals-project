<?php 
// 1. Safe Session Engine Initialization
if (session_status() === PHP_SESSION_NONE) {
    require_once __DIR__ . '/../../config/session.php'; 
}

// Fallback checks linking raw session arrays or database field maps
$user_name        = htmlspecialchars($_SESSION['user']['name'] ?? ($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? ''));
$user_username    = htmlspecialchars($_SESSION['user']['username'] ?? ($_SESSION['username'] ?? ''));
$user_bio         = htmlspecialchars($_SESSION['user']['bio'] ?? '');
$user_email       = htmlspecialchars($_SESSION['user']['email'] ?? ($_SESSION['email'] ?? ''));
$user_phone       = htmlspecialchars($_SESSION['user']['phone'] ?? '');
$artist_desc      = htmlspecialchars($_SESSION['user']['artist_description'] ?? '');
$avatar_url       = $_SESSION['user']['avatar_url'] ?? '';
?>

<link rel="stylesheet" href="<?= BASE_URL ?>public/css/profile.css">
<link rel="stylesheet" href="<?= BASE_URL ?>public/css/settings.css">

<main class="py-5 settings-main">
    <div class="container-fluid settings-container">
        <div class="row justify-content-center">
            <div class="col-12 settings-card">

                <h2 class="settings-title">Account Settings</h2>

                <form action="<?= BASE_URL; ?>api/profile/update.php" method="POST" enctype="multipart/form-data" id="settingsForm">
                    <input type="hidden" name="delete_avatar" id="deleteAvatarFlag" value="0">
                    
                    <div class="row g-4">
                        
                        <div class="col-md-6">
                            <div class="settings-group-box">
                                <h5 class="settings-section-title"><i class="bi bi-person"></i> Personal Information</h5>
                                
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="settings-label">Full Name</label>
                                        <div class="custom-input-group">
                                            <span class="settings-addon-icon"><i class="bi bi-person"></i></span>
                                            <input type="text" class="settings-input-override" id="settingsFormName" name="name" value="<?= $user_name ?>" placeholder="Name" required>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="settings-label">Username</label>
                                        <div class="custom-input-group">
                                            <span class="settings-addon-icon"><i class="bi bi-person-badge"></i></span>
                                            <input type="text" class="settings-input-override" id="settingsFormUsername" name="username" value="<?= $user_username ?>" placeholder="Username" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="settings-label">Edit Bio</label>
                                    <textarea class="settings-textarea-override" rows="2" id="settingsFormBio" name="bio" placeholder="Bio..."><?= $user_bio ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="settings-label">Email</label>
                                        <div class="custom-input-group">
                                            <span class="settings-addon-icon"><i class="bi bi-envelope"></i></span>
                                            <input type="email" id="settingsFormEmail" name="email" value="<?= $user_email ?>" class="settings-input-override" required>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="settings-label">Phone</label>
                                        <div class="custom-input-group">
                                            <span class="settings-addon-icon"><i class="bi bi-telephone"></i></span>
                                            <input type="text" id="settingsFormPhone" name="phone" value="<?= $user_phone ?>" class="settings-input-override">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="settings-group-box">
                                <h5 class="settings-section-title"><i class="bi bi-lock"></i> Security</h5>
                                
                                <div class="mb-3">
                                    <label class="settings-label">Current Password</label>
                                    <div class="custom-input-group">
                                        <input type="password" name="current_password" class="settings-input-override password-toggle-field">
                                        <span class="settings-addon-icon interactive-cursor eye-toggle-icon">
                                            <i class="bi bi-eye-slash"></i>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <label class="settings-label">New Password</label>
                                        <div class="custom-input-group">
                                            <input type="password" name="new_password" class="settings-input-override password-toggle-field">
                                            <span class="settings-addon-icon interactive-cursor eye-toggle-icon">
                                                <i class="bi bi-eye-slash"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="settings-label">Confirm Password</label>
                                        <div class="custom-input-group">
                                            <input type="password" name="confirm_password" class="settings-input-override password-toggle-field">
                                            <span class="settings-addon-icon interactive-cursor eye-toggle-icon">
                                                <i class="bi bi-eye-slash"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="settings-group-box">
                                <h5 class="settings-section-title"><i class="bi bi-person-circle"></i> Avatar / Profile Picture</h5>

                                <div class="row align-items-center">
                                    <div class="col-5 text-center">
                                        <?php if (!empty($avatar_url)): ?>
                                            <img src="<?= BASE_URL . htmlspecialchars($avatar_url) ?>" alt="Avatar Preview" class="profile-avatar mb-2" id="avatarPreview">
                                            <div id="avatarPreviewPlaceholder" class="avatar-fb-placeholder mb-2 d-none align-items-center justify-content-center">
                                                <svg viewBox="0 0 24 24" fill="#adb5bd">
                                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                                </svg>
                                            </div>
                                        <?php else: ?>
                                            <img src="" alt="Avatar Preview" class="profile-avatar mb-2 d-none" id="avatarPreview">
                                            <div id="avatarPreviewPlaceholder" class="avatar-fb-placeholder mb-2 d-inline-flex align-items-center justify-content-center">
                                                <svg viewBox="0 0 24 24" fill="#adb5bd">
                                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                                </svg>
                                            </div>
                                        <?php endif; ?>

                                        <button type="button" id="removeAvatarBtn" class="btn btn-outline-danger btn-sm w-100 btn-font-sz mt-2">Remove Avatar</button>
                                    </div>

                                    <div class="col-7 text-center">
                                        <div class="p-3 avatar-upload-zone" id="dropZone">
                                            <p class="avatar-upload-text">
                                                <strong>Choose New Avatar</strong><br>
                                                Drag & drop an image here or click browse below
                                            </p>
                                            <input type="file" name="avatar" id="avatarFileInput" accept="image/png, image/jpeg" class="hidden-file-input" style="display: none;">
                                            <button type="button" id="uploadImageBtn" class="btn-follow w-100 py-1 btn-font-sz">
                                                <i class="bi bi-upload"></i> Select Image
                                            </button>
                                        </div>
                                        <small class="text-muted btn-font-sz mt-1 d-block">Recommended PNG/JPG</small>
                                    </div>
                                </div>
                            </div>

                            <div class="artist-box-highlight" id="artistHighlightBox">
                                <h5 class="settings-section-title"><i class="bi bi-pencil-square"></i> Artist Description</h5>
                                <textarea class="settings-textarea-override" rows="6" name="artist_description" id="artistDescText" maxlength="250" placeholder="Tell us about your art..."><?= $artist_desc ?></textarea>
                                <small class="text-muted d-block mt-1 text-end" id="charCounter">0/250</small>
                            </div>
                            
                            <div class="py-3">
                                <a class="btn btn-outline-secondary btn-sm px-3 profile-sub-weight" href="<?= BASE_URL; ?>views/profile/view.php">View Profile Structure</a>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4 align-items-center settings-footer-actions">
                        <div class="col-6 d-flex gap-2">
                            <button type="submit" id="submit" class="btn-follow">Save Changes</button>
                            <button type="button" id="clearForm" class="btn btn-outline-secondary btn-sm px-3 btn-cancel-custom">Cancel</button>
                        </div>
                        <div class="col-6 text-end">
                            <button type="button" id="deleteAccountBtn" class="btn btn-outline-danger btn-sm btn-delete-custom"><i class="bi bi-trash"></i> Delete Account</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="<?= BASE_URL; ?>public/js/settings.js"></script>