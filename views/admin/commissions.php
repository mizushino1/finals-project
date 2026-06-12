<?php require_once __DIR__ . '/../../src/middleware/admin_middleware.php'; ?>

<main class="py-5">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="joan mb-0">Admin Dashboard</h2>
                <p class="text-muted mb-0">Overview of all things going on in Artovia Web</p>
            </div>
            <button class="btn btn-artovia-primary">
                <i class="bi bi-plus-lg"></i> Create New Listing
            </button>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="card theme-border p-4 h-100" style="background: var(--clr-bg-card);">
                    <h5 class="mb-3">Quick Actions</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary">Manage Users</button>
                        <button class="btn btn-outline-secondary">Review Reports</button>
                        <button class="btn btn-outline-secondary">Payout Settings</button>
                        <button class="btn btn-outline-secondary">Category Edits</button>
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

        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card theme-border p-4 h-100" style="background: var(--clr-bg-card);">
                    <h6 class="text-muted">Total Pending</h6>
                    <h3 class="display-6 fw-bold">12</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card theme-border p-4 h-100" style="background: var(--clr-bg-card);">
                    <h6 class="text-muted">Active Commissions</h6>
                    <h3 class="display-6 fw-bold">8</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card theme-border p-4 h-100" style="background: var(--clr-bg-card);">
                    <h6 class="text-muted">Completed</h6>
                    <h3 class="display-6 fw-bold">45</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card theme-border p-4 h-100" style="background: var(--clr-bg-card);">
                    <h6 class="text-muted">Total Revenue</h6>
                    <h3 class="display-6 fw-bold">₱45,200</h3>
                </div>
            </div>
        </div>

        <div class="card theme-border border-0 shadow-sm p-0 overflow-hidden mb-4"
            style="background: var(--clr-bg-card);">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Commission Requests</h5>
                <a href="#" class="text-decoration-none" style="color: var(--clr-gold);">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead style="background-color: var(--clr-bg-alt);">
                        <tr>
                            <th class="p-3">Client</th>
                            <th class="p-3">Commission Type</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Budget</th>
                            <th class="p-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid var(--clr-border);">
                            <td class="p-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle me-3"
                                        style="width: 40px; height: 40px; background: var(--clr-gold-light);"></div>
                                    <div>
                                        <div class="fw-bold">Juan Dela Cruz</div>
                                        <small class="text-muted">ID: #C-1029</small>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3">Digital Illustration</td>
                            <td class="p-3"><span class="badge"
                                    style="background-color: var(--clr-open); color: white;">Pending</span></td>
                            <td class="p-3">₱2,500</td>
                            <td class="p-3 text-end"><button class="btn btn-sm btn-danger">Remove</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>