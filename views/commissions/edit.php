<?php require_once __DIR__ . '/../../src/middleware/user_middleware.php'; ?>

<main class="py-4">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card border-3 mx-auto my-5" style="height: 600px; width: 750px; border-color: gold;">
                    <div class="col-12">
                        <h2 class="p-3 mt-3 ms-4 mb-0">EDIT COMMISSION</h2>
                    </div>

                    <div class="col-12">
                        <label class="p-3 mb-0 pb-0" style="margin-left: 75px; font-weight: 450">COMMISSION
                            NAME</label>
                        <input class="theme-fill w-75 mb-2" style="margin-left: 90px; height: 35px;" type="text">

                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <label class="col-form-label" style="margin-left: 398px;">Genre</label>
                            </div>
                            <div class="col-auto mb-0">
                                <select class="form-control form-select theme-fill" style="width: 200px;"
                                    aria-label="Filter by genre">
                                    <option value="digital">Digital Art</option>
                                    <option value="traditional">Traditional</option>
                                    <option value="concept">Concept Art</option>
                                    <option value="portrait">Portrait</option>
                                </select>
                            </div>

                            <div class="col-auto mt-0">
                                <label class="col-form-label mb-0"
                                    style="margin-left: 90px; font-weight: 450">Description</label>
                                <textarea style="margin-left: 90px; height: 150px; width: 558px;" type="text"
                                    placeholder="Description here...."></textarea>
                            </div>

                            <div class="col-auto">
                                <label class="col-form-label"
                                    style="margin-left: 223px; font-weight: 450">Budget</label>
                                <label class="col-form-label" style="margin-left: 115px; font-weight: 450">Upload
                                    (Optional)</label>
                            </div>

                            <div class="col-auto d-flex align-items-center mt-0">
                                <input class="theme-fill mb-2"
                                    style="margin-left: 175px; height: 35px; width: 150px;" type="text"
                                    placeholder="$100,000">
                                <button class="btn mb-2"
                                    style="margin-left: 35px; height: 35px; width: 200px; background-color: #fcd6a1; font-weight: 500;">
                                    Select Image
                                </button>
                            </div>

                            <div class="d-flex align-items-center" style="margin-left: 90px; gap: 20px;">
                                <div class="col-auto">
                                    <button type="button" class="btn btn-success" style="width: 268px;">Save</button>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-danger"
                                        style="width: 268px;">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>