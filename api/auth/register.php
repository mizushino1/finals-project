<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artovia - Register</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Color Palette Variables */
        :root {
            --bg-dark: #121212;
            --card-dark: #1c1c1e;
            --card-light: #ffffff;
            --accent-orange: #fcdbb0;
            --text-muted: #a1a1a1;
        }

        body {
            background-color: var(--bg-dark);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #ffffff;
            min-height: 100vh;
            margin: 0;
        }

        /* --- Header / Navbar Styling --- */
        .navbar-header {
            background-color: #111111;
            border-bottom: 1px solid #222;
            padding: 10px 0;
        }

        .logo {
            color: #ffffff !important;
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 2px;
            text-decoration: none;
        }

        .brand-initial {
            font-size: 2rem;
            font-family: 'Georgia', serif;
            font-style: italic;
            margin-right: -2px;
        }

        .navbar-nav .nav-link {
            color: #ffffff !important;
            font-weight: 600;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .navbar-nav .nav-link.active {
            color: var(--accent-orange) !important;
        }

        .btn-login-top {
            background-color: #3a3a3c;
            color: #ffffff;
            border: 1px solid #555;
            border-radius: 20px;
            padding: 6px 20px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-login-top:hover {
            background-color: #4a4a4c;
            color: #fff;
        }

        /* --- Split Card Layout --- */
        .auth-card {
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            border: 4px solid var(--card-dark);
            min-height: 550px;
        }

        /* Left Side (White Register Form) */
        .form-side {
            background-color: var(--card-light);
            color: #333333;
            border-top-left-radius: 25px;
            border-bottom-left-radius: 25px;
        }

        .form-title {
            font-family: 'Georgia', serif;
            font-weight: bold;
            font-size: 3rem;
            color: #e2c092;
            text-shadow: 1px 2px 3px rgba(0, 0, 0, 0.15);
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #444;
            margin-bottom: 4px;
        }

        /* Input Fields Customized Styling */
        .form-side .input-group {
            border: 2px solid var(--accent-orange);
            border-radius: 8px;
            overflow: hidden;
        }

        .form-side .input-group-text {
            background-color: transparent;
            border: none;
            color: #888;
            padding-left: 12px;
            padding-right: 8px;
        }

        .form-side .form-control {
            border: none;
            padding: 10px 10px 10px 0;
            font-size: 0.95rem;
        }

        .form-side .form-control:focus {
            box-shadow: none;
        }

        .eye-icon {
            cursor: pointer;
            padding-right: 12px !important;
        }

        /* Register Button Styling */
        .btn-register {
            background-color: #ffffff;
            color: #1c1c1e;
            border: 2px solid var(--accent-orange);
            border-radius: 8px;
            font-weight: bold;
            padding: 10px;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            background-color: var(--accent-orange);
            color: #1c1c1e;
        }

        /* Social Media Icons Styling */
        .social-icon {
            font-size: 1.8rem;
            transition: transform 0.2s ease;
        }

        .social-icon.google { color: #ea4335; }
        .social-icon.facebook { color: #1877f2; }
        .social-icon.tiktok { color: #000000; }

        .social-icon:hover {
            transform: scale(1.1);
        }

        /* Right Side (Dark Welcome Information) */
        .info-side {
            background-color: var(--card-dark);
            color: #ffffff;
            border-top-right-radius: 25px;
            border-bottom-right-radius: 25px;
            padding-left: 10% !important;
        }

        .info-title {
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: 1.5px;
            color: var(--accent-orange);
            line-height: 1.3;
        }

        .info-text {
            color: #e5e5ea;
            font-size: 1.2rem;
            max-width: 85%;
            line-height: 1.5;
        }

        .btn-info-login {
            background-color: #545456;
            color: #ffffff;
            border: 2px solid var(--accent-orange);
            border-radius: 8px;
            font-size: 1.3rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-info-login:hover {
            background-color: var(--accent-orange);
            color: var(--card-dark);
        }

        /* Responsive structural adjustments for smaller viewports */
        @media (max-width: 767.98px) {
            .form-side {
                border-radius: 25px 25px 0 0;
            }
            .info-side {
                border-radius: 0 0 25px 25px;
                padding-left: 3rem !important;
            }
        }
    </style>
</head>
<body>

    <header class="navbar-header">
        <nav class="navbar navbar-expand-lg container-fluid px-5">
            <a class="navbar-brand logo" href="#">
                <span class="brand-initial">A</span>RTOVIA
            </a>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav gap-4">
                    <li class="nav-item"><a class="nav-link active" href="#">HOME</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">EXPLORE</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">NEWS</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">HOW IT WORKS</a></li>
                </ul>
            </div>
            <button class="btn btn-login-top">Log in/Sign up</button>
        </nav>
    </header>

    <main class="py-5">
        <div class="container d-flex justify-content-center align-items-center min-vh-75">
            <div class="row w-100 justify-content-center">
                <div class="col-12 col-xl-10">
                    
                    <div class="auth-card row g-0">
                        
                        <div class="col-md-6 form-side p-5 d-flex flex-column align-items-center">
                            <h1 class="form-title mb-4">Register</h1>
                            
                            <form class="w-100 px-3">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control" placeholder="Name">
                                    </div>
                                </div>
                                
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
                                        <span class="input-group-text eye-icon"><i class="bi bi-eye"></i></span>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-register w-100 mb-4">REGISTER</button>
                            </form>
                            
                            <div class="social-login d-flex gap-4 mt-2">
                                <a href="#" class="social-icon google"><i class="fab fa-google"></i></a>
                                <a href="#" class="social-icon facebook"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="social-icon tiktok"><i class="fab fa-tiktok"></i></a>
                            </div>
                        </div>
                        
                        <div class="col-md-6 info-side p-5 d-flex flex-column justify-content-center align-items-start">
                            <h2 class="info-title mb-3">CREATE AN ACCOUNT TO CONTINUE</h2>
                            <p class="info-text mb-4">If you already have an account, proceed to log in.</p>
                            <button class="btn btn-info-login px-5 py-2">Log in</button>
                        </div>

                    </div> </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>