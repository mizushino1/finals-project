<?php require_once __DIR__ . '/../../src/middleware/user_middleware.php'; ?>

<main class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-7 text-center my-5">
                <h2 class="mb-3">MANAGE COMMISSIONS</h2>
                <p class="text-muted mb-4">Use the "Manage" button on your commission cards to edit a post.</p>
                <a href="<?= defined('BASE_URL') ? BASE_URL : './' ?>commissions" class="btn-artovia-outline px-4 py-2">
                    Back to Commissions
                </a>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/partials/edit_modal.php'; ?>

<script src="<?= defined('BASE_URL') ? BASE_URL : './' ?>public/js/editCommission.js"></script>