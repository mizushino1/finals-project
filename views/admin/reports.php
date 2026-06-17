<?php require_once __DIR__ . '/../../src/middleware/admin_middleware.php'; ?>

<main class="py-5">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="joan mb-0">Power BI</h2>
                <p class="text-muted mb-0">Overview of all used visuals in Power BI</p>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="card theme-border p-4 h-100" style="background: var(--clr-bg-card);">
                    <h5 class="mb-3">Quick Actions</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?= BASE_URL ?>admin" class="btn btn-outline-secondary">Dashboard</a>
                        <a href="<?= BASE_URL ?>admin/users" class="btn btn-outline-secondary">Manage Users</a>
                        <a href="<?= BASE_URL ?>admin/commissions" class="btn btn-outline-secondary">Review Commissions</a>
                        <a href="<?= BASE_URL ?>admin/payments" class="btn btn-outline-secondary">Payment Records</a>
                        <a href="<?= BASE_URL ?>admin/reports" class="btn btn-outline-secondary">Reports</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card theme-border p-4 h-100 d-flex align-items-center justify-content-center"
                    style="background: var(--clr-bg-card);">
                    <h6 class="text-muted mb-0">System Status: <span class="text-success fw-bold">Operational</span>
                    </h6>
                </div>
            </div>
        </div>

        <div class="card theme-border border-0 shadow-sm p-3" style="background: var(--clr-bg-card);">
            <iframe title="Power BI Report" width="100%" height="800"
                src="https://app.powerbi.com/view?r=eyJrIjoiMzY1NTE4ZmUtODVlMi00NTZkLWJmMDAtZTM5NTA2NzlhZGZjIiwidCI6IjRkYTk4NTcxLWRjZWEtNDgzOS04ZmIxLTBiZGQ1ZGM5NjlmOSIsImMiOjEwfQ%3D%3D"
                frameborder="0" allowFullScreen="true">
            </iframe>
        </div>
    </div>
</main>