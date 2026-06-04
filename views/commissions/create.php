<?php require_once __DIR__ . '/../../src/middleware/auth_middleware.php'; ?>
<main class="py-4">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card border-3 mx-auto my-5" style="height: 600px; width: 750px; border-color: gold;">
                    <div class="col-12">
                        <h3 class="p-3 mt-1">CREATE COMMISSIONS</h3>
                    </div>

                    <div class="col-12">
                        <label class="p-3 mb-0 pb-0" style="margin-left: 75px; font-weight: 400">COMMISSION NAME</label>
                        <input class="theme-fill w-75 mb-2" style="margin-left: 90px; height: 35px;" type="text">

                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <label class="col-form-label mb-0" style="margin-left: 90px; font-weight: 400">Description</label>
                                <label class="col-form-label" style="margin-left: 287px;">Genre</label>
                            </div>
                            <div class="col-auto">
                                <select class="form-control form-select theme-fill w-100" aria-label="Filter by genre">
                                    <option value="digital">Digital Art</option>
                                    <option value="traditional">Traditional</option>
                                    <option value="concept">Concept Art</option>
                                    <option value="portrait">Portrait</option>
                                </select>
                            </div>

                            <div class="col-auto">
                                <input style="margin-left: 90px; height: 150px; width: 556px;" type="text">
                            </div>

                            <div class="col-auto">
                                <label class="col-form-label    " style="margin-left: 200px; font-weight: 400">Budget</label>
                                <label class="col-form-label" style="margin-left: 160px; font-weight: 400">Upload(Optional)</label>
                            </div>

                            <div class="col-auto">
                                <input class="theme-fill w-50 mb-2" style="margin-left: 143px; height: 35px;" type="text" placeholder="$100,000">
                            </div>

                            <button class="btn btn-fill">POST COMMISSION</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>