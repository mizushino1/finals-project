<main class="py-5 mb-5">
    <div class="mt-5 container d-flex justify-content-center align-items-center">
        <div class="row w-100 justify-content-center">
            <div class="col-12 col-lg-10 col-xl-9">

                <div class="auth-card row g-0" id="authCard">

                    <!-- ── Info panel ── -->
                    <div class="col-md-5 info-side p-5 d-flex flex-column justify-content-center align-items-start"
                        id="infoPanel">

                        <!-- Login info -->
                        <div class="info-content" id="infoLogin">
                            <div class="d-flex flex-row align-items-center mb-3">
                                <img src="<?php echo BASE_URL; ?>public/img/icon.svg" class="large-brand-icon me-3"
                                    alt="Artovia Icon">
                                <span class="welcome-text">WELCOME</span>
                            </div>
                            <p class="info-desc mb-4">
                                Please log in to your existing account or create a new account if you don't have one
                                yet.
                            </p>
                            <button class="btn btn-fill px-4 py-2" id="goToRegister">Create Account</button>
                        </div>

                        <!-- Register info -->
                        <div class="info-content d-none" id="infoRegister">
                            <div class="d-flex flex-row align-items-center mb-3">
                                <img src="<?php echo BASE_URL; ?>public/img/icon.svg" class="large-brand-icon me-3"
                                    alt="Artovia Icon">
                                <span class="welcome-text">CREATE AN ACCOUNT TO CONTINUE</span>
                            </div>
                            <p class="info-desc mb-4">
                                If you already have an account, proceed to log in.
                            </p>
                            <button class="btn btn-fill px-4 py-2" id="goToLogin">Log In</button>
                        </div>

                    </div>

                    <!-- ── Form panel ── -->
                    <div class="col-md-7 form-side p-5 d-flex flex-column align-items-center justify-content-center"
                        id="formPanel">

                        <!-- Login form -->
                        <div class="form-content w-100" id="formLogin">
                            <h1 class="form-title mb-4">Log In</h1>
                            <form class="w-100 px-lg-4">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <div class="input-group auth-input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control" placeholder="Email">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Password</label>
                                    <div class="input-group auth-input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" class="form-control" placeholder="Password">
                                        <span class="input-group-text eye-toggle-icon">
                                            <i class="bi bi-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="text-center mb-4 d-flex flex-column align-items-center">
                                    <button type="submit" class="btn btn-outline px-5 py-2 mb-3">LOGIN</button>
                                    <a href="<?php echo BASE_URL; ?>login/forgot-password"
                                        class="forgot-password-link mb-4">Forgot Password?</a>
                                    <p class="login-divider-text small text-muted mb-3">or login with</p>
                                    <div class="social-medias d-flex gap-4 justify-content-center align-items-center">
                                        <a href="#" class="social-icon-wrapper google">
                                            <img src="https://static.freepnglogo.com/images/all_img/google-logo-2025-6ffb.png"
                                                alt="Google" class="social-logo">
                                        </a>
                                        <a href="#" class="social-icon-wrapper facebook">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6c/Facebook_Logo_2023.png"
                                                alt="Facebook" class="social-logo">
                                        </a>
                                        <a href="#" class="social-icon-wrapper tiktok">
                                            <img src="https://img.magnific.com/premium-vector/tik-tok-logo_578229-290.jpg?semt=ais_hybrid&w=740&q=80"
                                                alt="TikTok" class="social-logo social-logo--rounded">
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Register form -->
                        <div class="form-content w-100 d-none" id="formRegister">
                            <h1 class="form-title mb-4">Register</h1>
                            <form class="w-100 px-lg-4">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <div class="input-group auth-input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control" placeholder="Name">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <div class="input-group auth-input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control" placeholder="Email">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Password</label>
                                    <div class="input-group auth-input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" class="form-control" placeholder="Password">
                                        <span class="input-group-text eye-toggle-icon">
                                            <i class="bi bi-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="text-center mb-4 d-flex flex-column align-items-center">
                                    <button type="submit" class="btn btn-outline px-5 py-2 mb-3">REGISTER</button>
                                    <p class="login-divider-text small text-muted mb-3">or register with</p>
                                    <div class="social-medias d-flex gap-4 justify-content-center align-items-center">
                                        <a href="#" class="social-icon-wrapper google">
                                            <img src="https://static.freepnglogo.com/images/all_img/google-logo-2025-6ffb.png"
                                                alt="Google" class="social-logo">
                                        </a>
                                        <a href="#" class="social-icon-wrapper facebook">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6c/Facebook_Logo_2023.png"
                                                alt="Facebook" class="social-logo">
                                        </a>
                                        <a href="#" class="social-icon-wrapper tiktok">
                                            <img src="https://img.magnific.com/premium-vector/tik-tok-logo_578229-290.jpg?semt=ais_hybrid&w=740&q=80"
                                                alt="TikTok" class="social-logo social-logo--rounded">
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<script src="<?php echo BASE_URL; ?>public/js/auth.js"></script>