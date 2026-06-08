<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Artovia</title>
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>public/img/icon.svg">
    <link href="https://fonts.googleapis.com/css2?family=Joan&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/glass.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/header.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/footer.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/otp.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/login.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/browse.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/profile.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/settings.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/home.css">

    <script>
        (function() {
            const savedTheme = localStorage.getItem('artovia-theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>
</head>

<body data-bs-theme="dark">
    <header class="sticky-top">
        <nav class="navbar navbar-expand-md glass-card-dark">
            <div class="container-fluid">
                <a class="navbar-brand ms-3" href="#">
                    <img src="<?php echo BASE_URL; ?>public/img/logo.svg" alt="" class="navbar-logo">
                </a>

                <div class="navbar-nav d-flex flex-row d-md-none text-end justify-self-end align-items-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="dropdown nav-user-dropdown me-2">
                            <div class="d-flex align-items-center gap-2">
                                <a href="<?php echo BASE_URL; ?>profile" class="nav-user-avatar" title="View Profile">
                                    <i class="bi bi-person-circle text-light" style="font-size:1.6rem; line-height:1;"></i>
                                </a>
                                <button class="btn p-0 border-0 d-flex align-items-center gap-1 text-light nav-kebab-btn"
                                    data-bs-toggle="dropdown" aria-expanded="false" title="Options">
                                    <span class="fw-bold fs-fluid-xs"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                                    <i class="bi bi-three-dots-vertical" style="font-size:1rem;"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end glass-card-dark border-0 mt-2 shadow">
                                    <li>
                                        <a class="dropdown-item text-light" href="<?php echo BASE_URL; ?>settings">
                                            <i class="bi bi-gear me-2"></i>Settings
                                        </a>
                                    </li>
                                    <li>
                                        <button class="dropdown-item text-light theme-toggle-btn d-none" data-set-theme="light">
                                            <i class="bi bi-sun-fill me-2"></i>Light Mode
                                        </button>
                                        <button class="dropdown-item text-light theme-toggle-btn d-none" data-set-theme="dark">
                                            <i class="bi bi-moon-stars-fill me-2"></i>Dark Mode
                                        </button>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider border-secondary my-1">
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>logout">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    <?php else: ?>
                        <button class="btn text-light me-2 p-1 border-0 dynamic-theme-solo" title="Toggle Theme">
                            <i class="bi bi-sun-fill fs-5"></i>
                        </button>
                        <a class="btn d-inline-block my-auto mx-2 text-light fw-bold glass-card fs-fluid-xs h-50"
                            href="<?php echo BASE_URL; ?>login">LOG IN</a>
                    <?php endif; ?>
                    <button class="navbar-toggler ms-1" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <div class="navbar-nav justify-content-center w-100 gap-4 ms-3 mt-2">
                        <a class="nav-link active text-light fw-bold fs-fluid-sm" aria-current="page"
                            href="<?php echo BASE_URL; ?>#">HOME</a>
                        <a class="nav-link text-light fw-bold fs-fluid-sm"
                            href="<?php echo BASE_URL; ?>commissions">COMMISSIONS</a>
                        <a class="nav-link text-light fw-bold fs-fluid-sm"
                            href="<?php echo BASE_URL; ?>artists">ARTISTS</a>
                    </div>
                </div>

                <div class="navbar-nav d-none d-md-flex align-items-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="dropdown nav-user-dropdown">
                            <div class="d-flex align-items-center gap-2">
                                <a href="<?php echo BASE_URL; ?>profile" class="nav-user-avatar" title="View Profile">
                                    <i class="bi bi-person-circle text-light" style="font-size:1.75rem; line-height:1;"></i>
                                </a>
                                <button class="btn p-0 border-0 d-flex align-items-center gap-1 text-light nav-kebab-btn"
                                    data-bs-toggle="dropdown" aria-expanded="false" title="Options">
                                    <span class="fw-bold fs-fluid-xs"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                                    <i class="bi bi-three-dots-vertical" style="font-size:1rem;"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end glass-card-dark border-0 mt-2 shadow">
                                    <li>
                                        <a class="dropdown-item text-light" href="<?php echo BASE_URL; ?>settings">
                                            <i class="bi bi-gear me-2"></i>Settings
                                        </a>
                                    </li>
                                    <li>
                                        <button class="dropdown-item text-light theme-toggle-btn d-none" data-set-theme="light">
                                            <i class="bi bi-sun-fill me-2"></i>Light Mode
                                        </button>
                                        <button class="dropdown-item text-light theme-toggle-btn d-none" data-set-theme="dark">
                                            <i class="bi bi-moon-stars-fill me-2"></i>Dark Mode
                                        </button>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider border-secondary my-1">
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>logout">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    <?php else: ?>
                        <button class="btn text-light me-3 p-1 border-0 dynamic-theme-solo" title="Toggle Theme">
                            <i class="bi bi-sun-fill fs-5"></i>
                        </button>
                        <a class="btn text-light fw-bold glass-card fs-fluid-xs" href="<?php echo BASE_URL; ?>login">LOG IN</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>
    <script>
        const BASE_URL = '<?= BASE_URL ?>';
        document.addEventListener('DOMContentLoaded', () => {
            const getTheme = () => localStorage.getItem('artovia-theme') || 'dark';
            
            const setTheme = (theme) => {
                // Apply to root element for Bootstrap 5.3 rules, and backup body attribute
                document.documentElement.setAttribute('data-bs-theme', theme);
                document.body.setAttribute('data-bs-theme', theme);
                localStorage.setItem('artovia-theme', theme);
                updateThemeUI(theme);
            };

            const updateThemeUI = (currentTheme) => {
                // 1. Manage interactive dropdown layout options
                document.querySelectorAll('.theme-toggle-btn').forEach(btn => {
                    const targetTheme = btn.getAttribute('data-set-theme');
                    if (targetTheme === currentTheme) {
                        btn.classList.add('d-none');
                    } else {
                        btn.classList.remove('d-none');
                    }
                });

                // 2. Manage single icon button state for logged-out viewers
                document.querySelectorAll('.dynamic-theme-solo i').forEach(icon => {
                    if (currentTheme === 'dark') {
                        icon.className = 'bi bi-sun-fill';
                    } else {
                        icon.className = 'bi bi-moon-stars-fill';
                    }
                });
            };

            // Setup dropdown button event handlers
            document.querySelectorAll('.theme-toggle-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    setTheme(btn.getAttribute('data-set-theme'));
                });
            });

            // Setup fallback solo button event handlers
            document.querySelectorAll('.dynamic-theme-solo').forEach(btn => {
                btn.addEventListener('click', () => {
                    const nextTheme = getTheme() === 'dark' ? 'light' : 'dark';
                    setTheme(nextTheme);
                });
            });

            // Run alignment configuration on mount
            setTheme(getTheme());
        });
    </script>