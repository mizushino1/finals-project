<?php require_once __DIR__ . '/../../src/middleware/auth_middleware.php'; ?>

<main class="d-flex justify-content-center align-items-center" style="min-height: 100vh; background-color: var(--clr-bg);">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <!-- Adjusted col-lg-5 and col-md-7 to make the total card width smaller -->
            <div class="col-lg-5 col-md-7">
                
                <div class="card p-4 theme-border" style="background-color: var(--clr-bg-card);">
                    
                    <!-- Header -->
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-circle me-3" style="width: 50px; height: 50px; background-color: var(--clr-bg-alt); border: 2px solid var(--clr-gold);"></div>
                        <div class="flex-grow-1">
                            <h2 class="mb-0 fs-5" style="color: var(--clr-text-primary);">Bentito</h2>
                            <p class="mb-0" style="color: var(--clr-text-muted); font-size: 0.8rem;">Commissioner's Name</p>
                        </div>
                        <button class="btn-artovia-outline btn-sm">View Profile</button>
                    </div>

                    <hr style="color: var(--clr-border-strong);">

                    <!-- Grid Section -->
                    <div class="row g-2"> 
                        <div class="col-7">
                            <div class="mb-3">
                                <p class="form-label mb-1" style="font-size: 0.85rem;">Description</p>
                                <p style="color: var(--clr-text-secondary); line-height: 1.4; font-size: 0.9rem;">
                                    HAHAHA HAHAHA HAHAHA HAHAHA
                                </p>
                            </div>

                            <div class="mb-4">
                                <p class="mb-1" style="color: var(--clr-text-muted); font-size: 0.8rem;"><strong>Genre:</strong> <span style="color: var(--clr-text-primary);">Anime</span></p>
                                <p class="mb-1" style="color: var(--clr-text-muted); font-size: 0.8rem;"><strong>Offer:</strong> <span style="color: var(--clr-text-primary);">$500 - $1000</span></p>
                                <p class="mb-1" style="color: var(--clr-text-muted); font-size: 0.8rem;"><strong>Deadline:</strong> <span style="color: var(--clr-text-primary);">July 28, 2026</span></p>
                                <p class="mb-1" style="color: var(--clr-text-muted); font-size: 0.8rem;"><strong>Time:</strong> <span style="color: var(--clr-text-primary);">11:59 pm</span></p>
                            </div>
                        </div>

                        <div class="col-5">
                            <div class="theme-border d-flex align-items-center justify-content-center h-100" 
                                 style="background-color: var(--clr-bg-alt); border-radius: var(--radius-md); min-height: 140px;">
                                <span style="color: var(--clr-text-muted); font-size: 0.8rem;">Image Preview</span>
                            </div>
                        </div>
                    </div>

                    <hr style="color: var(--clr-border-strong);">

                    <div class="d-flex gap-2">
                        <button class="btn-artovia-primary flex-fill btn-sm">Accept</button>
                        <button class="btn-artovia-outline flex-fill btn-sm">Decline</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>