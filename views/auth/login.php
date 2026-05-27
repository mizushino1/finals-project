
<main class="py-5">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row w-100 justify-content-center">
            <div class="col-12 col-md-11 col-lg-10 col-xl-9">
                
                <div class="auth-card row g-0" style="border-radius: 30px; overflow: hidden; min-height: 550px; box-shadow: 0 10px 30px rgba(0,0,0,0.25);">
                    
                    <div class="col-12 col-md-5 info-side p-5 d-flex flex-column justify-content-center align-items-start" style="background-color: #0d0d0d; color: #ffffff;">
                        <div class="d-flex flex-row align-items-center mb-3">
                            <img src="<?php echo BASE_URL; ?>public/img/icon.svg" class="large-brand-icon me-3" alt="Artovia Icon" style="max-width: 50px;">
                            <span class="welcome-text" style="font-weight: 700; font-size: 1.8rem; letter-spacing: 1px; color: #dfba94;">WELCOME</span>
                        </div>
                        <p class="info-desc mb-4" style="color: #b3b3b3; font-size: 1rem; line-height: 1.6;">
                            Please log in to your existing account or create a new account if you don't have one yet.
                        </p>
                        <button class="btn btn-create-account px-4 py-2 text-light" style="border: 1px solid #dfba94; background: transparent; border-radius: 8px;">Create Account</button>
                    </div>
                    
                    <div class="col-12 col-md-7 form-side p-5 d-flex flex-column align-items-center justify-content-center" style="background-color: #ffffff; border-top-right-radius: 30px; border-bottom-right-radius: 30px;">
                        <h1 class="form-title mb-4" style="color: #dfba94; font-family: serif; font-weight: bold; font-size: 2.8rem;">Log In</h1>
                        
                        <form class="w-100 px-lg-4">
                            <div class="mb-3">
                                <label class="form-label" style="color: #666; font-weight: 500;">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: transparent; border-right: none; border-color: #f1d9be;"><i class="bi bi-envelope" style="color: #999;"></i></span>
                                    <input type="email" class="form-control" placeholder="Email" style="border-left: none; border-color: #f1d9be; box-shadow: none;">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label" style="color: #666; font-weight: 500;">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: transparent; border-right: none; border-color: #f1d9be;"><i class="bi bi-lock" style="color: #999;"></i></span>
                                    <input type="password" class="form-control" placeholder="Password" style="border-left: none; border-right: none; border-color: #f1d9be; box-shadow: none;">
                                    <span class="input-group-text eye-toggle-icon" style="background: transparent; border-left: none; border-color: #f1d9be;"><i class="bi bi-eye" style="color: #999;"></i></span>
                                </div>
                            </div>
                            
                            <div class="text-center mb-4 d-flex flex-column align-items-center">
                                <button type="submit" class="btn btn-login-submit px-5 py-2 mb-3" style="border: 1px solid #dfba94; color: #dfba94; background: transparent; font-weight: 600; border-radius: 6px; width: 100%; max-width: 180px;">LOGIN</button>
                                
                                <a href="#" class="forgot-password-link" style="color: #dfba94; text-decoration: underline; text-underline-offset: 4px; font-size: 0.9rem; font-weight: 500; transition: opacity 0.2s;">Forgot Password?</a>
                            </div>
                        </form>
                        
                        <div class="social-medias d-flex gap-4">
                            <a href="#" class="social-icon-wrapper google" style="color: #de4b39; font-size: 1.4rem;"><i class="fab fa-google"></i></a>
                            <a href="#" class="social-icon-wrapper facebook" style="color: #3b5998; font-size: 1.4rem;"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon-wrapper tiktok" style="color: #000000; font-size: 1.4rem;"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </div>

                </div> 
            </div>
        </div>
    </div>
</main>

