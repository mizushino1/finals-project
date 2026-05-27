<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artovia - Login</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/main.css">
</head>
<body style="background-color: #1e1e1e;">

    <main class="py-5">
        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <div class="row w-100 justify-content-center">
                <div class="col-12 col-lg-10 col-xl-9">
                    
                    <div class="auth-card row g-0">
                        
                        <div class="col-md-5 info-side p-5 d-flex flex-column justify-content-center align-items-start">
                            <div class="d-flex flex-row align-items-center mb-3">
                                <img src="<?php echo BASE_URL; ?>public/img/icon.svg" class="large-brand-icon me-3" alt="Artovia Icon">
                                <span class="welcome-text">WELCOME</span>
                            </div>
                            <p class="info-desc mb-4">
                                Please log in to your existing account or create a new account if you don't have one yet.
                            </p>
                            <button class="btn btn-create-account px-4 py-2 text-light">Create Account</button>
                        </div>
                        
                        <div class="col-md-7 form-side p-5 d-flex flex-column align-items-center justify-content-center">
                            <h1 class="form-title mb-4">Log In</h1>
                            
                            <form class="w-100 px-lg-4">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control" placeholder="Email">
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" class="form-control" placeholder="Password">
                                        <span class="input-group-text eye-toggle-icon"><i class="bi bi-eye"></i></span>
                                    </div>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <button type="submit" class="btn btn-login-submit px-5">LOGIN</button>
                                </div>
                            </form>
                            
                            <div class="social-medias d-flex gap-4">
                                <a href="#" class="social-icon-wrapper google"><i class="fab fa-google"></i></a>
                                <a href="#" class="social-icon-wrapper facebook"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="social-icon-wrapper tiktok"><i class="fab fa-tiktok"></i></a>
                            </div>
                        </div>

                    </div> </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>