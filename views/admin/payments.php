<?php require_once __DIR__ . '/../../src/middleware/admin_middleware.php'; ?>

<main class="py-5">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="joan mb-0">Payments Overview</h2>
                <p class="text-muted mb-0">Track all financial transactions and payment gateway activities</p>
            </div>
            <button class="btn btn-artovia-primary">
                <i class="bi bi-download"></i> Export Reports
            </button>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card theme-border p-4" style="background: var(--clr-bg-card);">
                    <h6 class="text-muted">Total Processed</h6>
                    <h3 class="fw-bold">₱128,450</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card theme-border p-4" style="background: var(--clr-bg-card);">
                    <h6 class="text-muted">Pending Payouts</h6>
                    <h3 class="fw-bold">₱12,200</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card theme-border p-4" style="background: var(--clr-bg-card);">
                    <h6 class="text-muted">Success Rate</h6>
                    <h3 class="fw-bold">98.5%</h3>
                </div>
            </div>
        </div>

        <div class="card theme-border border-0 shadow-sm p-0 overflow-hidden" style="background: var(--clr-bg-card);">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead style="background-color: var(--clr-bg-alt);">
                        <tr>
                            <th class="p-3">Transaction ID</th>
                            <th class="p-3">Client</th>
                            <th class="p-3"> Payment Method</th>
                            <th class="p-3">Amount</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid var(--clr-border);">
                            <td class="p-3">#TXN-99281</td>
                            <td class="p-3">Jay-R Umandap</td>
                            <td class="p-3">
                                <span class="badge bg-light text-dark">GCash</span>
                            </td>
                            <td class="p-3 fw-bold">₱2,500</td>
                            <td class="p-3">
                                <span class="badge" style="background-color: var(--clr-open); color: white;">Completed</span>
                            </td>
                            <td class="p-3 text-end">
                                <button class="btn btn-sm btn-outline-secondary">View</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>