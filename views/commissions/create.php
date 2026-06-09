<?php require_once __DIR__ . '/../../src/middleware/auth_middleware.php'; ?>

<main class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-7">
                <div class="card theme-border p-5 my-5">
                    <h2 class="mb-4">CREATE COMMISSION</h2>

                    <form>
                        <div class="mb-3">
                            <label class="form-label">Commission Name</label>
                            <input type="text" class="form-control theme-border" style="border-width: 1px !important;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Genre</label>
                            <select class="form-select theme-border" style="border-width: 1px !important;" aria-label="Filter by genre">
                                <option value="digital">Digital Art</option>
                                <option value="traditional">Traditional</option>
                                <option value="concept">Concept Art</option>
                                <option value="portrait">Portrait</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control theme-border hide-scrollbar" rows="6"
                                style="resize: none; overflow-y: auto; border-width: 1px !important;"
                                placeholder="Description here...."></textarea>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="form-label">Budget</label>
                                <input type="text" class="form-control theme-border"
                                    style="border-width: 1px !important;" placeholder="$100,000">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Upload (Optional)</label>
                                <button type="button" class="btn-artovia-outline w-100">Select Image</button>
                            </div>
                        </div>

                        <button type="submit" class="btn-artovia-primary w-100 mb-4">POST COMMISSION</button>

                        <div class="d-flex gap-3">
                            <button type="button" class="btn btn-success w-50">Save Draft</button>
                            <button type="button" class="btn btn-danger w-50">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>