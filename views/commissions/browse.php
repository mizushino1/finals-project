<?php require_once __DIR__ . '/../../src/middleware/auth_middleware.php'; ?>

<main class="container-xl px-3 px-sm-4 py-4">

    <section class=" text-center py-5 my-2 position-relative">
        <p class="browse-hero__eyebrow text-uppercase fs-fluid-xs">The Commission Board</p>
        <h1 class="browse-hero__title fs-fluid-2xl my-3">
            Find your next <em>creative project</em>
        </h1>
        <p class="browse-hero__sub mx-auto text-muted mb-4 col-md-8 col-lg-6 fs-fluid-sm">
            Browse open commission requests from clients looking for your unique style, or track your active postings.
        </p>

        <div class="search-bar mx-auto mb-4">
            <svg class="search-bar__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            <input type="text" class="search-bar__input fs-fluid-sm" id="searchInput" placeholder="Search by description, client name, or keywords…">
            <button class="search-bar__btn fs-fluid-xs" id="searchBtn">Search</button>
        </div>
        <?php if ($_SESSION['role'] === 'user' || $_SESSION['role'] === 'client'): ?>
            <button
                class="btn-artovia-primary px-4 py-2 fs-fluid-xs rounded-2"
                data-bs-toggle="modal"
                data-bs-target="#postCommissionModal">
                + Post a Commission
            </button>
        <?php endif; ?>
    </section>

    <section class="browse my-4">
        <div class="row g-0">
            <aside class="col-lg-3 mb-5">
                <div class="browse__sidebar sticky-top shadow-sm border rounded-3 p-3 bg-card" style="top: 90px; z-index: 100;">
                    <div class="sidebar__header d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom">
                        <span class="sidebar__header-title text-uppercase fw-bold  fs-fluid-xxs">Filters</span>
                        <button class="sidebar__clear-btn border-0 bg-transparent p-0 fw-semibold fs-fluid-xs" id="clearFilters">Clear all</button>
                    </div>

                    <div class="sidebar__content-wrapper d-flex flex-wrap flex-lg-column gap-3 gap-lg-0">
                        <div class="filter-group pb-lg-3 mb-lg-3 border-bottom-lg flex-shrink-0">
                            <h3 class="filter-group__title text-uppercase fw-bold text-muted mb-2 fs-fluid-xxs">Budget Range</h3>
                            <ul class="filter-group__list list-unstyled m-0 p-0 d-flex flex-row flex-lg-column gap-1">
                                <li><label class="rounded p-2 d-flex align-items-center gap-1 fs-fluid-xs"><input type="radio" name="budget" value="0-999999" checked> <span class="text-nowrap">Any Budget</span></label></li>
                                <li><label class="rounded p-2 d-flex align-items-center gap-1 fs-fluid-xs"><input type="radio" name="budget" value="0-1000"> <span class="text-nowrap">Under ₱1,000</span></label></li>
                                <li><label class="rounded p-2 d-flex align-items-center gap-1 fs-fluid-xs"><input type="radio" name="budget" value="1000-5000"> <span class="text-nowrap">₱1,000 – ₱5,000</span></label></li>
                                <li><label class="rounded p-2 d-flex align-items-center gap-1 fs-fluid-xs"><input type="radio" name="budget" value="5000-999999"> <span class="text-nowrap">₱5,000+</span></label></li>
                            </ul>
                        </div>

                        <div class="filter-group flex-shrink-0">
                            <h3 class="filter-group__title text-uppercase fw-bold text-muted mb-2 fs-fluid-xxs">Project Status</h3>
                            <ul class="filter-group__list list-unstyled m-0 p-0 d-flex flex-row flex-lg-column gap-1">
                                <li><label class="rounded p-2 d-flex align-items-center gap-1 fs-fluid-xs"><input type="radio" name="status" value="all" checked> <span class="text-nowrap">All Projects</span></label></li>
                                <li><label class="rounded p-2 d-flex align-items-center gap-1 fs-fluid-xs"><input type="radio" name="status" value="1"> <span class="text-nowrap">Active / Open</span></label></li>
                                <?php if ($_SESSION['role'] === 'user' || $_SESSION['role'] === 'admin'): ?>
                                    <li><label class="rounded p-2 d-flex align-items-center gap-1 fs-fluid-xs"><input type="radio" name="status" value="5"> <span class="text-nowrap">In Progress</span></label></li>
                                    <li><label class="rounded p-2 d-flex align-items-center gap-1 fs-fluid-xs"><input type="radio" name="status" value="6"> <span class="text-nowrap">Completed</span></label></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="col-lg-9 ps-3">

                <!-- ── Artist: My Pending Requests (awaiting client decision) ── -->
                <?php if ($_SESSION['role'] === 'artist'): ?>
                <div id="artistPendingSection">
                    <div class="browse-results-bar d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                        <p class="browse-results-bar__count m-0 text-muted fw-semibold fs-fluid-xs">
                            My Pending Requests
                            <span class="badge bg-warning text-dark ms-2 fs-fluid-xxs" id="artistPendingBadge" style="display:none;"></span>
                        </p>
                        <span class="text-muted fs-fluid-xxs">Waiting for client response</span>
                    </div>
                    <div class="overflow-x-auto pb-3 mb-4">
                        <!-- Skeleton -->
                        <div id="artistPendingLoading" class="d-flex flex-nowrap gap-3" style="width: max-content;">
                            <?php for ($i = 0; $i < 3; $i++): ?>
                                <div style="width: 280px; flex-shrink: 0;">
                                    <div class="artist-card h-100 border rounded-3 p-3 bg-card d-flex flex-column shadow-sm">
                                        <div class="d-flex gap-2 align-items-center mb-3">
                                            <div class="skeleton rounded-circle" style="width:36px; height:36px;"></div>
                                            <div class="d-flex flex-column gap-1">
                                                <div class="skeleton rounded" style="width:80px; height:12px;"></div>
                                                <div class="skeleton rounded" style="width:50px; height:8px;"></div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 mb-3 d-flex flex-column gap-2">
                                            <div class="skeleton rounded w-100" style="height:10px;"></div>
                                            <div class="skeleton rounded w-75" style="height:10px;"></div>
                                        </div>
                                        <div class="skeleton rounded-pill" style="width:70px; height:22px;"></div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                        <!-- Populated by JS -->
                        <div id="artistPendingGrid" class="d-flex flex-nowrap gap-3 d-none" style="width: max-content;"></div>
                        <!-- Empty state -->
                        <div id="artistPendingEmpty" class="d-none text-center py-4 px-3 border rounded-3 bg-card shadow-sm">
                            <p class="text-muted fs-fluid-xs m-0">You have no pending requests right now.</p>
                        </div>
                    </div>
                </div>

                <!-- ── Artist: Accepted Commissions ── -->
                <div id="artistAcceptedSection">
                    <div class="browse-results-bar d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                        <p class="browse-results-bar__count m-0 text-muted fw-semibold fs-fluid-xs">
                            Accepted Commissions
                            <span class="badge theme-fill text-dark ms-2 fs-fluid-xxs" id="artistAcceptedBadge" style="display:none;"></span>
                        </p>
                        <span class="text-muted fs-fluid-xxs">Commissions assigned to you</span>
                    </div>
                    <div class="overflow-x-auto pb-3 mb-5">
                        <!-- Skeleton -->
                        <div id="artistAcceptedLoading" class="d-flex flex-nowrap gap-3" style="width: max-content;">
                            <?php for ($i = 0; $i < 3; $i++): ?>
                                <div style="width: 280px; flex-shrink: 0;">
                                    <div class="artist-card h-100 border rounded-3 p-3 bg-card d-flex flex-column shadow-sm">
                                        <div class="d-flex gap-2 align-items-center mb-3">
                                            <div class="skeleton rounded-circle" style="width:36px; height:36px;"></div>
                                            <div class="d-flex flex-column gap-1">
                                                <div class="skeleton rounded" style="width:80px; height:12px;"></div>
                                                <div class="skeleton rounded" style="width:50px; height:8px;"></div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 mb-3 d-flex flex-column gap-2">
                                            <div class="skeleton rounded w-100" style="height:10px;"></div>
                                            <div class="skeleton rounded w-75" style="height:10px;"></div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <div class="skeleton rounded-2" style="width:100px; height:32px;"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                        <!-- Populated by JS -->
                        <div id="artistAcceptedGrid" class="d-flex flex-nowrap gap-3 d-none" style="width: max-content;"></div>
                        <!-- Empty state -->
                        <div id="artistAcceptedEmpty" class="d-none text-center py-4 px-3 border rounded-3 bg-card shadow-sm">
                            <p class="text-muted fs-fluid-xs m-0">No accepted commissions yet.</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ── Pending Received Requests (horizontal scroll strip) ── -->
                <div id="pendingSection">
                    <div class="browse-results-bar d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                        <p class="browse-results-bar__count m-0 text-muted fw-semibold fs-fluid-xs">Pending Received Requests</p>
                    </div>

                    <div class="overflow-x-auto pb-3 mb-4">
                        <!-- Skeleton shown while loading -->
                        <div id="pendingGridLoading" class="d-flex flex-nowrap gap-3" style="width: max-content;">
                            <?php for ($i = 0; $i < 4; $i++): ?>
                                <div style="width: 280px; flex-shrink: 0;">
                                    <div class="artist-card h-100 border rounded-3 p-3 bg-card d-flex flex-column shadow-sm">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="d-flex gap-2 align-items-center">
                                                <div class="skeleton rounded-circle" style="width:36px; height:36px;"></div>
                                                <div class="d-flex flex-column gap-1">
                                                    <div class="skeleton rounded" style="width:80px; height:12px;"></div>
                                                    <div class="skeleton rounded" style="width:50px; height:8px;"></div>
                                                </div>
                                            </div>
                                            <div class="skeleton rounded-pill" style="width:45px; height:18px;"></div>
                                        </div>
                                        <div class="flex-grow-1 mb-3 d-flex flex-column gap-2">
                                            <div class="skeleton rounded w-100" style="height:10px;"></div>
                                            <div class="skeleton rounded w-100" style="height:10px;"></div>
                                            <div class="skeleton rounded w-75" style="height:10px;"></div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                            <div class="skeleton rounded" style="width:60px; height:16px;"></div>
                                            <div class="skeleton rounded-2" style="width:80px; height:32px;"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                        <!-- Populated by JS -->
                        <div id="pendingGrid" class="d-flex flex-nowrap gap-3 d-none" style="width: max-content;"></div>
                    </div>
                </div>

                <!-- ── Main Commission Grid ── -->
                <div class="browse-results-bar d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                    <p class="browse-results-bar__count m-0 text-muted fs-fluid-xs">
                        Showing <strong id="resultsNumber" class=" fw-semibold">—</strong> Commissions
                    </p>
                    <div class="browse-sort d-flex align-items-center gap-2">
                        <span class="text-muted text-nowrap fs-fluid-xs">Sort by</span>
                        <select id="sortSelect" class="form-select form-select-sm border bg-card  fs-fluid-xs" aria-label="Sort commissions">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="budget_desc">Highest Budget</option>
                            <option value="budget_asc">Lowest Budget</option>
                        </select>
                    </div>
                </div>

                <div id="commissionGridLoading" class="row g-2 g-sm-3 row-cols-1 row-cols-md-2 row-cols-xl-3">
                    <?php for ($i = 0; $i < 6; $i++): ?>
                        <div class="col">
                            <div class="artist-card h-100 border rounded-3 p-3 bg-card d-flex flex-column shadow-sm">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex gap-2 align-items-center">
                                        <div class="skeleton rounded-circle" style="width:36px; height:36px;"></div>
                                        <div class="d-flex flex-column gap-1">
                                            <div class="skeleton rounded" style="width:80px; height:12px;"></div>
                                            <div class="skeleton rounded" style="width:50px; height:8px;"></div>
                                        </div>
                                    </div>
                                    <div class="skeleton rounded-pill" style="width:45px; height:18px;"></div>
                                </div>
                                <div class="flex-grow-1 mb-3 d-flex flex-column gap-2">
                                    <div class="skeleton rounded w-100" style="height:10px;"></div>
                                    <div class="skeleton rounded w-100" style="height:10px;"></div>
                                    <div class="skeleton rounded w-75" style="height:10px;"></div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                    <div class="skeleton rounded" style="width:60px; height:16px;"></div>
                                    <div class="skeleton rounded-2" style="width:80px; height:32px;"></div>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>

                <div id="commissionGridError" class="grid-state d-none text-center py-5 px-3 border rounded-3 bg-card shadow-sm">
                    <p class="grid-state__title fw-bold  m-0 mb-1 fs-fluid-sm">Failed to load commissions</p>
                    <p class="grid-state__sub text-muted mx-auto mb-3 fs-fluid-xs">There was an issue communicating with the server.</p>
                    <button class="btn btn-outline-secondary btn-sm fs-fluid-xs" onclick="location.reload()">Retry</button>
                </div>

                <div id="commissionGridEmpty" class="grid-state d-none text-center py-5 px-3 border rounded-3 bg-card shadow-sm">
                    <p class="grid-state__title fw-bold  m-0 mb-1 fs-fluid-sm">No commissions found</p>
                    <p class="grid-state__sub text-muted mx-auto m-0 fs-fluid-xs">Try adjusting your filters or search keywords.</p>
                </div>

                <div class="row g-2 g-sm-3 row-cols-1 row-cols-md-2 row-cols-xl-3 d-none" id="commissionGrid"></div>

            </div>
        </div>
    </section>
</main>

<?php if ($_SESSION['role'] === 'user' || $_SESSION['role'] === 'client'): ?>
    <div class="modal fade" id="postCommissionModal" tabindex="-1" aria-labelledby="postCommissionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-card border rounded-3 shadow">

                <div class="modal-header border-bottom px-4 pt-4 pb-3">
                    <h5 class="modal-title fw-bold fs-fluid-sm" id="postCommissionModalLabel">Create Commission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 py-4">
                    <div id="commissionFormAlert" class="alert d-none mb-3 fs-fluid-xs" role="alert"></div>

                    <!-- Commission Name -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-fluid-xs">Commission Name <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            id="commissionTitle"
                            class="form-control theme-border fs-fluid-xs"
                            style="border-width:1px !important;"
                            placeholder="e.g. Character portrait for my OC">
                    </div>

                    <!-- Category (Genre) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-fluid-xs">Genre <span class="text-danger">*</span></label>
                        <select
                            id="commissionCategory"
                            class="form-select theme-border fs-fluid-xs"
                            style="border-width:1px !important;"
                            aria-label="Commission category">
                            <option value="" disabled selected>Select a genre</option>
                            <option value="1">Anime</option>
                            <option value="2">Chibi</option>
                            <option value="3">Pixel Art</option>
                            <option value="4">Watercolor</option>
                            <option value="5">Fantasy</option>
                            <option value="6">Logo Design</option>
                            <option value="7">Portrait</option>
                            <option value="8">Character Design</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-fluid-xs">Description <span class="text-danger">*</span></label>
                        <textarea
                            id="commissionDescription"
                            class="form-control theme-border hide-scrollbar fs-fluid-xs"
                            rows="5"
                            style="resize:none; overflow-y:auto; border-width:1px !important;"
                            placeholder="Describe your project — style, mood, references, deliverables…"></textarea>
                    </div>

                    <!-- Budget + Upload -->
                    <div class="row g-3 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold fs-fluid-xs">Budget (₱) <span class="text-danger">*</span></label>
                            <input
                                type="number"
                                id="commissionBudget"
                                class="form-control theme-border fs-fluid-xs"
                                style="border-width:1px !important;"
                                placeholder="e.g. 2500"
                                min="1"
                                step="any">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold fs-fluid-xs">Reference Image <span class="text-muted fw-normal">(Optional)</span></label>
                            <input type="file" id="commissionImageFile" accept="image/*" class="d-none">
                            <button
                                type="button"
                                class="btn-artovia-outline w-100 fs-fluid-xs"
                                onclick="document.getElementById('commissionImageFile').click()">
                                Select Image
                            </button>
                            <p id="commissionImageName" class="text-muted fs-fluid-xxs mt-1 mb-0 text-truncate"></p>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top px-4 pb-4 pt-3 d-flex gap-2 flex-nowrap">
                    <button type="button" id="submitCommissionBtn" class="btn-artovia-primary w-50 fs-fluid-xs rounded-2">
                        Post Commission
                    </button>
                    <button type="button" class="btn btn-outline w-50 fs-fluid-xs" data-bs-dismiss="modal">Cancel</button>

                </div>

            </div>
        </div>
    </div>
<?php endif; ?>

<script src="<?= BASE_URL ?>public/js/commission.js"></script>