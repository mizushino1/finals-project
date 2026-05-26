<main class="d-flex align-items-center justify-content-center min-vh-100 bg-dark text-light py-5">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h1 class="display-1 fw-bold text-danger animate__animated animate__bounceIn">404</h1>
                
                <h2 class="h3 mb-3">Oops! Page Not Found</h2>
                <p class="text-muted mb-4">
                    The page you are looking for might have been removed, had its name changed, 
                    or is temporarily unavailable. 
                </p>

                <div class="d-flex justify-content-center gap-3">
                    <a href="<?php echo BASE_URL; ?>" class="btn btn-primary px-4 py-2">
                        <i class="bi bi-house-door-fill me-2"></i>Back to Home
                    </a>
                    <button onclick="history.back()" class="btn btn-outline-secondary text-light px-4 py-2">
                        <i class="bi bi-arrow-left me-2"></i>Go Back
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>