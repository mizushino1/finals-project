<!-- ═══════════════════════════════════════════════════════════
     EDIT COMMISSION MODAL (shared partial)
     Include once per page via:
       require_once __DIR__ . '/partials/edit_modal.php';
     Trigger by adding to any button/link:
       data-bs-toggle="modal" data-bs-target="#editCommissionModal" data-commission-id="<id>"
═══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="editCommissionModal" tabindex="-1" aria-labelledby="editCommissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg p-4 bg-card" style="border-radius: 1rem;">
            <div class="modal-header border-0 p-0 mb-3">
                <h5 class="modal-title fw-bold" id="editCommissionModalLabel" style="font-family: var(--font-ui);">Edit Commission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">

                <div id="editCommissionLoading" class="text-center py-5">
                    <div class="spinner-border" role="status"></div>
                    <p class="text-muted mt-2 mb-0 fs-fluid-sm">Loading commission...</p>
                </div>

                <div id="editCommissionFormAlert" class="alert d-none fs-fluid-xs"></div>

                <form id="editCommissionForm" class="d-none">
                    <input type="hidden" id="editCommissionId">

                    <div class="mb-3">
                        <label class="form-label">Commission Name</label>
                        <input type="text" id="editCommissionTitle" class="form-control theme-border" style="border-width: 1px !important;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Genre</label>
                        <select id="editCommissionCategory" class="form-select theme-border" style="border-width: 1px !important;" aria-label="Commission category">
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

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea id="editCommissionDescription" class="form-control theme-border hide-scrollbar" rows="6"
                                  style="resize: none; overflow-y: auto; border-width: 1px !important;"
                                  placeholder="Description here...."></textarea>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label">Budget</label>
                            <input type="text" id="editCommissionBudget" class="form-control theme-border"
                                   style="border-width: 1px !important;" placeholder="100000">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Upload (Optional)</label>
                            <button type="button" id="editCommissionImageBtn" class="btn-artovia-outline w-100">Select Image</button>
                            <input type="file" id="editCommissionImageFile" accept="image/*" class="d-none">
                            <small id="editCommissionImageName" class="text-muted d-block mt-1 fs-fluid-xxs"></small>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <button type="button" id="saveCommissionBtn" class="btn btn-success w-50">Save Draft</button>
                        <button type="button" id="cancelCommissionBtn" class="btn btn-danger w-50">Cancel Commission</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>