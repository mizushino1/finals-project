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
        window.USER_ROLE = "<?php echo isset($_SESSION['role']) ? $_SESSION['role'] : 'guest'; ?>";
        (function() {
            const savedTheme = localStorage.getItem('artovia-theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>
</head>

<body data-bs-theme="dark">
    <header class="sticky-top">
        <nav class="navbar glass-card-dark">
            <div class="container-fluid flex-wrap">

                <div class="d-flex align-items-center w-100">

                    <!-- Logo -->
                    <a class="navbar-brand ms-3 me-4" href="#">
                        <img src="<?php echo BASE_URL; ?>public/img/logo.svg" alt="" class="navbar-logo">
                    </a>

                    <!-- Always-visible nav links (hidden on small screens) -->
                    <div class="navbar-nav d-none d-sm-flex flex-row gap-4 flex-grow-1 justify-content-center">
                        <a class="nav-link active text-light fw-bold fs-fluid-sm" aria-current="page"
                            href="<?php echo BASE_URL; ?>#">HOME</a>
                        <a class="nav-link text-light fw-bold fs-fluid-sm"
                            href="<?php echo BASE_URL; ?>commissions">COMMISSIONS</a>
                        <a class="nav-link text-light fw-bold fs-fluid-sm"
                            href="<?php echo BASE_URL; ?>artists">ARTISTS</a>
                    </div>

                    <!-- Spacer on small screens to push right side to the end -->
                    <div class="flex-grow-1 d-sm-none"></div>

                    <!-- Right side — always stays here, never collapses -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="d-flex align-items-center gap-2 ms-3">
                            <a href="<?php echo BASE_URL; ?>profile" class="nav-user-avatar text-decoration-none d-flex align-items-center gap-2 flex-shrink-0" title="View Profile">
                                <i class=" bi bi-person-circle text-light" style="font-size:1.75rem; line-height:1;"></i>

                            </a>
                            <span class="fw-bold fs-fluid-xs text-light text-nowrap"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <button class="navbar-toggler border-secondary ms-1" type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#accountActionsNav"
                                aria-controls="accountActionsNav"
                                aria-expanded="false"
                                aria-label="Toggle account menu">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="d-flex align-items-center gap-2 ms-3">
                            <button class="btn text-light p-1 border-0 dynamic-theme-solo" title="Toggle Theme">
                                <i class="bi bi-sun-fill fs-5"></i>
                            </button>
                            <a class="btn text-light fw-bold glass-card fs-fluid-xs"
                                href="<?php echo BASE_URL; ?>login">LOG IN</a>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Row 2: Collapsible account actions — hidden by default on ALL screen sizes -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="collapse w-100" id="accountActionsNav">
                        <div class="navbar-nav d-flex flex-column gap-2 ps-3 pt-2 pb-3 border-top border-secondary mt-2">

                            <!-- Nav links only visible here on small screens -->
                            <div class="d-sm-none d-flex flex-column gap-2">
                                <a class="nav-link text-light fw-bold fs-fluid-sm" href="<?php echo BASE_URL; ?>#">HOME</a>
                                <a class="nav-link text-light fw-bold fs-fluid-sm" href="<?php echo BASE_URL; ?>commissions">COMMISSIONS</a>
                                <a class="nav-link text-light fw-bold fs-fluid-sm" href="<?php echo BASE_URL; ?>artists">ARTISTS</a>
                                <hr class="border-secondary my-1">
                            </div>

                            <!-- Account actions always visible when expanded -->
                            <a class="nav-link text-light fw-bold fs-fluid-sm"
                                href="<?php echo BASE_URL; ?>settings">
                                <i class="bi bi-gear me-2"></i>Settings
                            </a>
                            <button class="nav-link text-light fw-bold fs-fluid-sm border-0 bg-transparent text-start theme-toggle-btn d-none"
                                data-set-theme="light">
                                <i class="bi bi-sun-fill me-2"></i>Light Mode
                            </button>
                            <button class="nav-link text-light fw-bold fs-fluid-sm border-0 bg-transparent text-start theme-toggle-btn d-none"
                                data-set-theme="dark">
                                <i class="bi bi-moon-stars-fill me-2"></i>Dark Mode
                            </button>
                            <a class="nav-link text-danger fw-bold fs-fluid-sm"
                                href="<?php echo BASE_URL; ?>logout">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </nav>
    </header>

    <script>
        const BASE_URL = '<?= BASE_URL ?>';
        document.addEventListener('DOMContentLoaded', () => {
            const getTheme = () => localStorage.getItem('artovia-theme') || 'dark';

            const setTheme = (theme) => {
                document.documentElement.setAttribute('data-bs-theme', theme);
                document.body.setAttribute('data-bs-theme', theme);
                localStorage.setItem('artovia-theme', theme);
                updateThemeUI(theme);
            };

            const updateThemeUI = (currentTheme) => {
                document.querySelectorAll('.theme-toggle-btn').forEach(btn => {
                    const targetTheme = btn.getAttribute('data-set-theme');
                    btn.classList.toggle('d-none', targetTheme === currentTheme);
                });

                document.querySelectorAll('.dynamic-theme-solo i').forEach(icon => {
                    icon.className = currentTheme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
                });
            };

            document.querySelectorAll('.theme-toggle-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    setTheme(btn.getAttribute('data-set-theme'));
                });
            });

            document.querySelectorAll('.dynamic-theme-solo').forEach(btn => {
                btn.addEventListener('click', () => {
                    setTheme(getTheme() === 'dark' ? 'light' : 'dark');
                });
            });

            setTheme(getTheme());
        });
    </script>