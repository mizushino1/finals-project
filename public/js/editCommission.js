/* ══════════════════════════════════════════════════════════════
    editCommission.js — Artovia Manage / Edit Commission Modal
══════════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

    const APP_BASE_URL = window.BASE_URL ?? './';

    const modalEl = document.getElementById('editCommissionModal');
    if (!modalEl) return;

    // Elements
    const loadingEl = document.getElementById('editCommissionLoading');
    const formEl = document.getElementById('editCommissionForm');
    const alertEl = document.getElementById('editCommissionFormAlert');

    const idInput = document.getElementById('editCommissionId');
    const titleInput = document.getElementById('editCommissionTitle');
    const categoryInput = document.getElementById('editCommissionCategory');
    const descInput = document.getElementById('editCommissionDescription');
    const budgetInput = document.getElementById('editCommissionBudget');

    const imageBtn = document.getElementById('editCommissionImageBtn');
    const imageFile = document.getElementById('editCommissionImageFile');
    const imageName = document.getElementById('editCommissionImageName');

    const saveBtn = document.getElementById('saveCommissionBtn');
    const cancelBtn = document.getElementById('cancelCommissionBtn');

    // ── Helpers ───────────────────────────────────────────────

    function show(el) { el?.classList.remove('d-none'); }
    function hide(el) { el?.classList.add('d-none'); }

    function showAlert(message, isSuccess) {
        if (!alertEl) return;
        alertEl.textContent = message;
        alertEl.className = `alert fs-fluid-xs ${isSuccess ? 'alert-success' : 'alert-danger'}`;
        show(alertEl);
    }

    function resetForm() {
        hide(formEl);
        hide(alertEl);
        show(loadingEl);

        if (idInput) idInput.value = '';
        if (titleInput) titleInput.value = '';
        if (categoryInput) categoryInput.value = '';
        if (descInput) descInput.value = '';
        if (budgetInput) budgetInput.value = '';
        if (imageFile) imageFile.value = '';
        if (imageName) imageName.textContent = '';

        if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = 'Save Draft'; }
        if (cancelBtn) { cancelBtn.disabled = false; cancelBtn.textContent = 'Cancel Commission'; }
    }

    function getCommissionId() {
        // Resolve the commission id either from the modal trigger or the URL query string
        if (modalEl.dataset.commissionId) return parseInt(modalEl.dataset.commissionId, 10);

        const params = new URLSearchParams(window.location.search);
        const fromUrl = parseInt(params.get('id') ?? params.get('commission_id') ?? '', 10);
        return Number.isNaN(fromUrl) ? 0 : fromUrl;
    }

    // ── Load commission data when the modal opens ─────────────

    modalEl.addEventListener('show.bs.modal', async (event) => {
        resetForm();

        // If triggered by a button with a data-commission-id, capture it on the modal
        const trigger = event.relatedTarget;
        if (trigger?.hasAttribute('data-commission-id')) {
            modalEl.dataset.commissionId = trigger.getAttribute('data-commission-id');
        }

        const commissionId = getCommissionId();

        if (!commissionId) {
            hide(loadingEl);
            showAlert('No commission selected to edit.', false);
            return;
        }

        try {
            const res = await fetch(`${APP_BASE_URL}api/commissions/manage.php?commission_id=${commissionId}`);
            const data = await res.json();

            hide(loadingEl);

            if (!data || !data.success) {
                showAlert(data?.message || 'Failed to load commission.', false);
                return;
            }

            const c = data.data;
            if (idInput) idInput.value = c.commission_id;
            if (titleInput) titleInput.value = c.title || '';
            if (descInput) descInput.value = c.description || '';
            if (budgetInput) budgetInput.value = c.price > 0 ? c.price : '';
            if (categoryInput && c.category_id) categoryInput.value = c.category_id;
            if (imageName) imageName.textContent = c.image_url ? 'Current image attached' : '';

            show(formEl);

        } catch (err) {
            console.error('Edit commission load error:', err);
            hide(loadingEl);
            showAlert('A network error occurred while loading this commission.', false);
        }
    });

    modalEl.addEventListener('hidden.bs.modal', () => {
        delete modalEl.dataset.commissionId;
    });

    // ── Image select ──────────────────────────────────────────

    if (imageBtn && imageFile) {
        imageBtn.addEventListener('click', () => imageFile.click());
        imageFile.addEventListener('change', () => {
            if (imageName) imageName.textContent = imageFile.files[0]?.name ?? '';
        });
    }

    // ── Save Draft ────────────────────────────────────────────

    if (saveBtn) {
        saveBtn.addEventListener('click', async () => {
            hide(alertEl);

            const commissionId = parseInt(idInput?.value ?? '0', 10);
            const title = titleInput?.value.trim() ?? '';
            const description = descInput?.value.trim() ?? '';
            const budget = parseFloat(budgetInput?.value ?? 0);
            const category_id = parseInt(categoryInput?.value ?? '0', 10);

            if (!commissionId) { showAlert('Missing commission reference.', false); return; }
            if (!title) { showAlert('Please provide a commission name.', false); return; }
            if (!description) { showAlert('Please provide a project description.', false); return; }
            if (isNaN(budget) || budget <= 0) { showAlert('Please enter a valid budget higher than ₱0.', false); return; }
            if (!category_id) { showAlert('Please select a category.', false); return; }

            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving…';

            try {
                const formData = new FormData();
                formData.append('action', 'update');
                formData.append('commission_id', commissionId);
                formData.append('title', title);
                formData.append('description', description);
                formData.append('budget', budget);
                formData.append('category_id', category_id);
                if (imageFile?.files[0]) {
                    formData.append('image', imageFile.files[0]);
                }

                const res = await fetch(`${APP_BASE_URL}api/commissions/manage.php`, {
                    method: 'POST',
                    // No Content-Type header — browser sets multipart boundary automatically
                    body: formData
                });
                const data = await res.json();

                if (data && data.success) {
                    showAlert(data.message || 'Commission updated successfully.', true);
                    setTimeout(() => {
                        if (typeof bootstrap !== 'undefined') {
                            bootstrap.Modal.getInstance(modalEl)?.hide();
                        }
                        if (typeof window.loadCommissions === 'function') window.loadCommissions();
                    }, 1200);
                } else {
                    showAlert(data?.message || 'Failed to update commission.', false);
                }
            } catch (err) {
                console.error('Save commission error:', err);
                showAlert('A network error occurred. Please try again.', false);
            } finally {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save Draft';
            }
        });
    }

    // ── Cancel Commission ────────────────────────────────────

    if (cancelBtn) {
        cancelBtn.addEventListener('click', async () => {
            hide(alertEl);

            const commissionId = parseInt(idInput?.value ?? '0', 10);
            if (!commissionId) { showAlert('Missing commission reference.', false); return; }

            if (!confirm('Cancel this commission? This cannot be undone.')) return;

            cancelBtn.disabled = true;
            cancelBtn.textContent = 'Cancelling…';

            try {
                const res = await fetch(`${APP_BASE_URL}api/commissions/manage.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'cancel',
                        commission_id: commissionId
                    })
                });
                const data = await res.json();

                if (data && data.success) {
                    showAlert(data.message || 'Commission cancelled.', true);
                    setTimeout(() => {
                        if (typeof bootstrap !== 'undefined') {
                            bootstrap.Modal.getInstance(modalEl)?.hide();
                        }
                        if (typeof window.loadCommissions === 'function') window.loadCommissions();
                    }, 1200);
                } else {
                    showAlert(data?.message || 'Failed to cancel commission.', false);
                    cancelBtn.disabled = false;
                    cancelBtn.textContent = 'Cancel Commission';
                }
            } catch (err) {
                console.error('Cancel commission error:', err);
                showAlert('A network error occurred. Please try again.', false);
                cancelBtn.disabled = false;
                cancelBtn.textContent = 'Cancel Commission';
            }
        });
    }
});