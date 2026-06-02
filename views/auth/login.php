<?php
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: ' . BASE_URL . 'admin');
    } else {
        header('Location: ' . BASE_URL);
    }
    exit;
}
?>

<main class="py-5 mb-5">
    <div class="mt-5 container d-flex justify-content-center align-items-center">
        <div class="row w-100 justify-content-center">
            <div class="col-12 col-lg-10 col-xl-9">
                <div class="auth-card row g-0" id="authCard">

                    <!-- Info panel -->
                    <div class="col-md-5 info-side p-5 d-flex flex-column justify-content-center align-items-start" id="infoPanel">
                        <div class="info-content" id="infoLogin">
                            <div class="d-flex flex-row align-items-center mb-3">
                                <img src="<?= BASE_URL ?>public/img/icon.svg" class="large-brand-icon me-3" alt="Artovia Icon">
                                <span class="welcome-text">WELCOME</span>
                            </div>
                            <p class="info-desc mb-4">Please log in to your existing account or create a new account if you don't have one yet.</p>
                            <button class="btn btn-fill px-4 py-2" id="goToRegister">Create Account</button>
                        </div>
                        <div class="info-content d-none" id="infoRegister">
                            <div class="d-flex flex-row align-items-center mb-3">
                                <img src="<?= BASE_URL ?>public/img/icon.svg" class="large-brand-icon me-3" alt="Artovia Icon">
                                <span class="welcome-text">CREATE AN ACCOUNT TO CONTINUE</span>
                            </div>
                            <p class="info-desc mb-4">If you already have an account, proceed to log in.</p>
                            <button class="btn btn-fill px-4 py-2 login-trigger" id="goToLogin1">Log In</button>
                        </div>
                    </div>

                    <!-- Form panel -->
                    <div class="col-md-7 form-side p-5 d-flex flex-column align-items-center justify-content-center" id="formPanel">

                        <!-- Alert box -->
                        <div id="auth-alert" class="alert w-100 px-lg-4 d-none" role="alert"></div>

                        <!-- Login form -->
                        <div class="form-content w-100" id="formLogin">
                            <h1 class="form-title mb-4">Log In</h1>
                            <form class="w-100 px-lg-4" id="loginForm">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <div class="input-group auth-input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control" id="loginUsername" placeholder="Username" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Password</label>
                                    <div class="input-group auth-input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" class="form-control" id="loginPassword" placeholder="Password" required>
                                        <span class="input-group-text eye-toggle-icon"><i class="bi bi-eye"></i></span>
                                    </div>
                                </div>
                                <div class="text-center mb-4 d-flex flex-column align-items-center">
                                    <button type="submit" class="btn btn-outline px-5 py-2 mb-3">LOGIN</button>
                                    <a href="<?= BASE_URL ?>login/forgot-password" class="forgot-password-link mb-4">Forgot Password?</a>
                                    <p class="login-divider-text small text-muted mb-3">or login with</p>
                                    <div class="social-medias d-flex gap-4 justify-content-center align-items-center">
                                        <a href="#" class="social-icon-wrapper google">
                                            <img src="https://static.freepnglogo.com/images/all_img/google-logo-2025-6ffb.png" alt="Google" class="social-logo">
                                        </a>
                                        <a href="#" class="social-icon-wrapper facebook">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6c/Facebook_Logo_2023.png" alt="Facebook" class="social-logo">
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Register form -->
                        <div class="form-content w-100 d-none" id="formRegister">
                            <h1 class="form-title mb-4">Register</h1>
                            <form class="w-100 px-lg-4" id="registerForm">
                                <div class="mb-3">
                                    <label class="form-label">Account Type</label>
                                    <ul class="nav nav-tabs border-0 mb-3" id="roleTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="user-tab" data-bs-toggle="tab" data-bs-target="#user-content" type="button" role="tab" aria-selected="true" onclick="setRole('user')">User</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="artist-tab" data-bs-toggle="tab" data-bs-target="#artist-content" type="button" role="tab" aria-selected="false" onclick="setRole('artist')">Artist</button>
                                        </li>
                                    </ul>

                                    <input type="hidden" name="registerRole" id="registerRole" value="user">
                                </div>
                                <div class="row">
                                    <div class="mb-3 col">
                                        <label class="form-label">First Name</label>
                                        <div class="input-group auth-input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input type="text" class="form-control" id="registerFirstName" placeholder="First Name" required>
                                        </div>
                                    </div>
                                    <div class="mb-3 col" >
                                        <label class="form-label">Last Name</label>
                                        <div class="input-group auth-input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input type="text" class="form-control" id="registerLastName" placeholder="Last Name" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <div class="input-group auth-input-group">
                                        <span class="input-group-text"><i class="bi bi-at"></i></span>
                                        <input type="text" class="form-control" id="registerUsername" placeholder="Username" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Password</label>
                                    <div class="input-group auth-input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" class="form-control" id="registerPassword" placeholder="Password" required>
                                        <span class="input-group-text eye-toggle-icon"><i class="bi bi-eye"></i></span>
                                    </div>
                                </div>

                                <!-- Artist only field -->
                                <div class="mb-4 d-none" id="artistStartAtWrapper">
                                    <label class="form-label">Starting Price ($)</label>
                                    <div class="input-group auth-input-group">
                                        <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                        <input type="number" class="form-control" id="registerStartAt" placeholder="e.g. 500.00" min="0" step="0.01">
                                    </div>
                                </div>

                                <div class="text-center mb-4 d-flex flex-column align-items-center">
                                    <button type="submit" class="btn btn-outline px-5 py-2 mb-3">REGISTER</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<div class="modal fade" id="authModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center" id="modalMessage">
        </div>
      <div class="modal-footer border-0 justify-content-center">
        <button class="btn btn-fill px-4 py-2" id="modalActionBtn">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="<?= BASE_URL ?>public/js/auth.js"></script>