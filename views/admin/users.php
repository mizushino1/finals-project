<?php require_once __DIR__ . '/../../src/middleware/admin_middleware.php'; ?>

<main class="py-5">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="joan mb-0">Admin Dashboard</h2>
                <h3 class="joan mb-0">Users</h3>
                <p class="text-muted mb-0">Overview of all users in the web.</p>
            </div>
            <button class="btn btn-artovia-primary">
                <i class="bi bi-plus-lg"></i> Create New Listing
            </button>
        </div>

        <div class="card theme-border border-0 shadow-sm p-0 overflow-hidden mb-4"
            style="background: var(--clr-bg-card);">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0">System User Management</h5>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead style="background-color: var(--clr-bg-alt);">
                        <tr>
                            <th class="p-3">Username</th>
                            <th class="p-3">Account Type</th>
                            <th class="p-3">Location</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid var(--clr-border);">
                            <td class="p-3">
                                <div class="fw-bold">Neils</div>
                                <small class="text-muted">ID: #U-1001</small>
                            </td>
                            <td class="p-3">User</td>
                            <td class="p-3">Makiling</td>
                            <td class="p-3">
                                <span class="badge"
                                    style="background-color: var(--clr-open); color: white;">Active</span>
                            </td>
                            <td class="p-3 text-end">
                                <button class="btn btn-sm btn-outline-danger me-2">Ban</button>
                                <button class="btn btn-sm btn-outline-success">Unban</button>
                            </td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--clr-border);">
                            <td class="p-3">
                                <div class="fw-bold">Jay-Art</div>
                                <small class="text-muted">ID: #A-1001</small>
                            </td>
                            <td class="p-3 text-warning">Artist</td>
                            <td class="p-3">Suplang</td>
                            <td class="p-3">
                                <span class="badge bg-danger text-white">Banned</span>
                            </td>
                            <td class="p-3 text-end">
                                <button class="btn btn-sm btn-outline-danger me-2">Ban</button>
                                <button class="btn btn-sm btn-outline-success">Unban</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>