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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/login.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/otp.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/profile.css">

</head>

<body>
    <header class="sticky-top">
        <nav class="navbar navbar-expand-md glass-card-dark">
            <div class="container-fluid">
                <a class="navbar-brand ms-3" href="#">
                    <img src="<?php echo BASE_URL; ?>public/img/logo.svg" alt="" class="navbar-logo">
                </a>
                <div class="navbar-nav d-flex flex-row d-md-none text-end justify-self-end align">
                    <a class="btn d-inline-block my-auto mx-2 text-light fw-bold glass-card fs-fluid-xs h-50"
                        href="<?php echo BASE_URL; ?>login">LOG
                        IN</a>
                    <button class="navbar-toggler ms-0" type="button" data-bs-toggle="collapse"
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
                            href="<?php echo BASE_URL; ?>commissions">ARTISTS</a>
                    </div>

                </div>
                <div class="navbar-nav d-none d-md-block">
                    <a class="btn text-light fw-bold glass-card fs-fluid-xs" href="<?php echo BASE_URL; ?>login">LOG
                        IN</a>
                </div>
            </div>
        </nav>
    </header>
    <script>
        const BASE_URL = '<?= BASE_URL ?>';
    </script>