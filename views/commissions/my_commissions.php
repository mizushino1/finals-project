<?php require_once __DIR__ . '/../../src/middleware/auth_middleware.php'; ?>

<main>
    <div class="container-fluid">
        <div class="row pt-3 px-3">
            <div class="col-12">
                <h3 class="theme-font theme-bottom-border">COMMISSIONS</h3>
            </div>

            <div class="col">
                <div class="row d-flex flex-row justify-content-between">
                    <div class="col-12 col-sm-6">
                        <button class="btn btn-fill w-100">CREATE</button>
                    </div>
                    <div class="col-12 col-sm-6 d-flex flex-row mt-3 mt-sm-0">
                        <input type="text" id="inputSearch" class="form-control me-2"
                            aria-describedby="searchHelpBlock" placeholder="Search Commission">
                        <div id="searchHelpBlock" class="form-text">
                        </div>
                        <button class="btn btn-fill w-50">SEARCH</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row my-3 px-3">
            <div class="col border border-dark" id="commissionListContainer" style="min-height: 75vh;">

            </div>
        </div>
    </div>
</main>