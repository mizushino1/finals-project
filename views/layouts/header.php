<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>artovia</title>
    <link rel="icon" type="image/png" href="./public/img/icon.svg">
    <link href="https://fonts.googleapis.com/css2?family=Joan&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/glass.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/header.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/footer.css">

</head>

<body>
    <header class="sticky-top">
        <nav class="navbar navbar-expand-md" style="background-color: #111112;">
            <div class="container-fluid">
                <a class="navbar-brand ms-3" href="#"><img src="<?php echo BASE_URL; ?>public/img/logo.svg" alt=""
                        style="filter:invert(1);height:2rem"></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <div class="navbar-nav justify-content-center w-100 gap-4">
                        <a class="nav-link active text-light fw-bold fs-fluid-xs" aria-current="page"
                            href="<?php echo BASE_URL; ?>#">HOME</a>
                        <a class="nav-link text-light fw-bold fs-fluid-xs"
                            href="<?php echo BASE_URL; ?>commissions">COMMISSIONS</a>
                        <a class="nav-link text-light fw-bold fs-fluid-xs"
                            href="<?php echo BASE_URL; ?>commissions">ARTISTS</a>
                    </div>
                    <div class="navbar-nav">
                        <a class="btn btn-primary glass-card fs-fluid-xs"
                            href="<?php echo BASE_URL; ?>login">Log in/ Sign
                            up</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>