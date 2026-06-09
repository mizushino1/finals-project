<?php require_once __DIR__ . '/../../src/middleware/auth_middleware.php'; ?>

<main class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-9">
                <h2 class="mt-3 mb-1">Payment Records</h2>
                <p class="mb-4">Track and monitor all payment transactions.</p>

                <div class="row align-items-center mb-4 g-3">
                    <div class="col-12 col-md-5 col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text theme-border border-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control theme-border border-start-0"
                                placeholder="Search transaction ID..." style="border-width: 2px !important;">
                        </div>
                    </div>

                    <div class="col-12 col-md-4 col-lg-3 ms-auto">
                        <button class="btn btn-outline d-flex align-items-center justify-content-between w-100 theme-border"
                            type="button" id="dateRangeDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                            style="border-width: 2px !important; padding: 0.4rem 1rem;">
                            <span><i class="bi bi-calendar"></i> Date Range</span>
                            <i class="bi bi-chevron-down"></i>
                        </button>

                        <div class="dropdown-menu p-3 shadow-sm theme-border" style="width: 300px;">
                            <div class="mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control theme-border" style="border-width: 1px !important;">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control theme-border" style="border-width: 1px !important;">
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-fill w-100 btn-sm">Apply</button>
                                <button class="btn btn-outline w-100 btn-sm">Reset</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <div class="d-inline-flex p-1 mb-4 theme-border" style="border-radius: var(--radius-lg) !important;">
                        <button class="btn btn-outline btn-sm" style="border-radius: var(--radius-lg); border: none;">All Payments</button>
                        <button class="btn btn-outline btn-sm" style="border-radius: var(--radius-lg); border: none;">Completed</button>
                        <button class="btn btn-outline btn-sm" style="border-radius: var(--radius-lg); border: none;">Pending</button>
                        <button class="btn btn-outline btn-sm" style="border-radius: var(--radius-lg); border: none;">Refunded</button>
                        <button class="btn btn-outline btn-sm" style="border-radius: var(--radius-lg); border: none;">Failed</button>
                    </div>
                </div>

                <div class="theme-border p-0 hide-scrollbar"
                    style="border-width: 2px !important; border-radius: var(--radius-lg); height: 400px; 
                    overflow-y: auto; overflow-x: hidden; position: relative;">

                    <table class="table mb-0 align-middle" style="width: calc(100% - 5px);">
                        <thead class="sticky-top" style="background-color: var(--clr-gold-light); z-index: 10; top: 0;">
                            <tr>
                                <th class="p-3" style="color: var(--clr-text-primary);">Transaction ID</th>
                                <th class="p-3" style="color: var(--clr-text-primary);">Artist</th>
                                <th class="p-3" style="color: var(--clr-text-primary);">Commission</th>
                                <th class="p-3" style="color: var(--clr-text-primary);">Amount</th>
                                <th class="p-3" style="color: var(--clr-text-primary);">Status</th>
                                <th class="p-3" style="color: var(--clr-text-primary);">Date</th>
                                <th class="p-3 text-center" style="color: var(--clr-text-primary);">Receipt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom: 1px solid var(--clr-border);">
                                <td class="p-3">#01</td>
                                <td class="p-3">Jolo</td>
                                <td class="p-3">Anime</td>
                                <td class="p-3">₱1,500</td>
                                <td class="p-3"><span class="badge" style="background-color: var(--clr-open); color: white; padding: 4px 8px;">completed</span></td>
                                <td class="p-3">June 1, 2026</td>
                                <td class="p-3 text-center">
                                    <button class="btn btn-sm btn-outline" style="padding: 2px 10px; font-size: 0.8rem;">
                                        <i class="bi bi-file-earmark-text"></i> View Receipt
                                    </button>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--clr-border);">
                                <td class="p-3">#02</td>
                                <td class="p-3">Jolo</td>
                                <td class="p-3">Anime</td>
                                <td class="p-3">₱1,500</td>
                                <td class="p-3"><span class="badge" style="background-color: var(--clr-open); color: white; padding: 4px 8px;">completed</span></td>
                                <td class="p-3">June 1, 2026</td>
                                <td class="p-3 text-center">
                                    <button class="btn btn-sm btn-outline" style="padding: 2px 10px; font-size: 0.8rem;">
                                        <i class="bi bi-file-earmark-text"></i> View Receipt
                                    </button>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--clr-border);">
                                <td class="p-3">#03</td>
                                <td class="p-3">Jolo</td>
                                <td class="p-3">Anime</td>
                                <td class="p-3">₱1,500</td>
                                <td class="p-3"><span class="badge" style="background-color: var(--clr-open); color: white; padding: 4px 8px;">completed</span></td>
                                <td class="p-3">June 1, 2026</td>
                                <td class="p-3 text-center">
                                    <button class="btn btn-sm btn-outline" style="padding: 2px 10px; font-size: 0.8rem;">
                                        <i class="bi bi-file-earmark-text"></i> View Receipt
                                    </button>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--clr-border);">
                                <td class="p-3">#04</td>
                                <td class="p-3">Jolo</td>
                                <td class="p-3">Anime</td>
                                <td class="p-3">₱1,500</td>
                                <td class="p-3"><span class="badge" style="background-color: var(--clr-open); color: white; padding: 4px 8px;">completed</span></td>
                                <td class="p-3">June 1, 2026</td>
                                <td class="p-3 text-center">
                                    <button class="btn btn-sm btn-outline" style="padding: 2px 10px; font-size: 0.8rem;">
                                        <i class="bi bi-file-earmark-text"></i> View Receipt
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>