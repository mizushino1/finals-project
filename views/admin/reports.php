<?php require_once __DIR__ . '/../../src/middleware/admin_middleware.php'; ?>

<main class="py-5">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="joan mb-0">Power BI</h2>
                <p class="text-muted mb-0">Overview of all used visuals in Power BI</p>
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