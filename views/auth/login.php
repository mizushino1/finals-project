<main class="py-5">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row w-100 justify-content-center">
            <div class="col-12 col-md-11 col-lg-10 col-xl-9">
                
                <div class="auth-card row g-0" style="border-radius: 30px; overflow: hidden; min-height: 550px; box-shadow: 0 10px 30px rgba(0,0,0,0.25); border: none !important;">
                    
                    <div class="col-12 col-md-5 info-side p-5 d-flex flex-column justify-content-center align-items-start" style="background-color: #0d0d0d !important; color: #ffffff !important;">
                        <div class="d-flex flex-row align-items-center mb-3" style="display: flex !important; flex-direction: row !important; align-items: center !important;">
                            <img src="<?php echo BASE_URL; ?>public/img/icon.svg" class="large-brand-icon me-3" alt="Artovia Icon" style="width: 55px !important; max-width: 55px !important; height: auto !important; object-fit: contain !important; display: inline-block !important;">
                            <span class="welcome-text" style="font-weight: 700 !important; font-size: 1.8rem !important; letter-spacing: 1px !important; color: #dfba94 !important; line-height: 1 !important; margin: 0 !important; padding-left: 4px !important;">WELCOME</span>
                        </div>
                        <p class="info-desc mb-4" style="color: #b3b3b3 !important; font-size: 1rem !important; line-height: 1.6 !important;">
                            Please log in to your existing account or create a new account if you don't have one yet.
                        </p>
                        <button class="btn btn-create-account px-4 py-2 text-light" style="border: 2px solid #dfba94 !important; background: transparent !important; border-radius: 8px !important; font-weight: 600 !important; color: #ffffff !important;">Create Account</button>
                    </div>
                    
                    <div class="col-12 col-md-7 form-side p-5 d-flex flex-column align-items-center justify-content-center" style="background-color: #ffffff !important; border-top-right-radius: 30px !important; border-bottom-right-radius: 30px !important;">
                        <h1 class="form-title mb-4" style="color: #dfba94 !important; font-family: serif !important; font-weight: bold !important; font-size: 2.8rem !important;">Log In</h1>
                        
                        <form class="w-100 px-lg-4">
                            <div class="mb-3">
                                <label class="form-label" style="color: #666 !important; font-weight: 500 !important;">Email</label>
                                <div class="input-group" style="border: 1px solid #f1d9be !important; border-radius: 6px !important; overflow: hidden !important;">
                                    <span class="input-group-text" style="background: transparent !important; border: none !important;"><i class="bi bi-envelope" style="color: #999 !important;"></i></span>
                                    <input type="email" class="form-control" placeholder="Email" style="border: none !important; box-shadow: none !important; background: transparent !important;">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label" style="color: #666 !important; font-weight: 500 !important;">Password</label>
                                <div class="input-group" style="border: 1px solid #f1d9be !important; border-radius: 6px !important; overflow: hidden !important;">
                                    <span class="input-group-text" style="background: transparent !important; border: none !important;"><i class="bi bi-lock" style="color: #999 !important;"></i></span>
                                    <input type="password" class="form-control" placeholder="Password" style="border: none !important; box-shadow: none !important; background: transparent !important;">
                                    <span class="input-group-text eye-toggle-icon" style="background: transparent !important; border: none !important; cursor: pointer !important;"><i class="bi bi-eye" style="color: #999 !important;"></i></span>
                                </div>
                            </div>
                            
                            <div class="text-center mb-4 d-flex flex-column align-items-center">
                                <button type="submit" class="btn btn-login-submit px-5 py-2 mb-3" style="border: 2px solid #dfba94 !important; color: #dfba94 !important; background: transparent !important; font-weight: 600 !important; border-radius: 6px !important; width: 100% !important; max-width: 180px !important;">LOGIN</button>
                                <a href="#" class="forgot-password-link mb-4" style="color: #dfba94 !important; text-decoration: underline !important; text-underline-offset: 4px !important; font-size: 0.9rem !important; font-weight: 500 !important;">Forgot Password?</a>
                                
                                <p class="small text-muted mb-3" style="font-size: 0.8rem; letter-spacing: 0.5px;">or login with</p>

                                <div class="social-medias d-flex gap-4 justify-content-center align-items-center" style="display: flex !important; justify-content: center !important; align-items: center !important; gap: 1.5rem !important;">
                                    <a href="#" class="social-icon-wrapper google" style="text-decoration: none !important; display: inline-flex; align-items: center; justify-content: center;">
                                        <img src="https://static.freepnglogo.com/images/all_img/google-logo-2025-6ffb.png" alt="Google" style="width: 32px; height: 32px; object-fit: contain;">
                                    </a>
                                    
                                    <a href="#" class="social-icon-wrapper facebook" style="text-decoration: none !important; display: inline-flex; align-items: center; justify-content: center;">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6c/Facebook_Logo_2023.png" alt="Facebook" style="width: 32px; height: 32px; object-fit: contain;">
                                    </a>
                                    
                                    <a href="#" class="social-icon-wrapper tiktok" style="text-decoration: none !important; display: inline-flex; align-items: center; justify-content: center; margin-top: -5px; margin-bottom: -5px;">
                                        <img src="https://img.magnific.com/premium-vector/tik-tok-logo_578229-290.jpg?semt=ais_hybrid&w=740&q=80" alt="TikTok" style="width: 32px; height: 32px; object-fit: contain; border-radius: 4px;">
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                </div> 
            </div>
        </div>
    </div>
</main>