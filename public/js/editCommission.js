/* ══════════════════════════════════════════════════════════════
   editCommission.js — Artovia Manage / Edit Commission Modal
══════════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    const A = window.Artovia;
    if (!A) { console.error('editCommission.js: Artovia core not loaded.'); return; }

    const APP_BASE_URL = window.BASE_URL ?? './';

    const modalEl = document.getElementById('editCommissionModal');
    if (!modalEl) return;

    // Elements
    const loadingEl = document.getElementById('editCommissionLoading');
    const formEl = document.getElementById('editCommissionForm');

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

    function resetForm() {
        hide(formEl);
        show(loadingEl);

        if (idInput) idInput.value = '';
        if (titleInput) titleInput.value = '';
        if (categoryInput) categoryInput.value = '';
        if (descInput) descInput.value = '';
        if (budgetInput) budgetInput.value = '';
        if (imageFile) imageFile.value = '';
        if (imageName) imageName.textContent = '';

        if (saveBtn) { A.btnReset(saveBtn, 'Save Draft'); }
        if (cancelBtn) { A.btnReset(cancelBtn, 'Cancel Commission'); }
    }

    function getCommissionId() {
        if (modalEl.dataset.commissionId) return parseInt(modalEl.dataset.commissionId, 10);

        const params = new URLSearchParams(window.location.search);
        const fromUrl = parseInt(params.get('id') ?? params.get('commission_id') ?? '', 10);
        return Number.isNaN(fromUrl) ? 0 : fromUrl;
    }

    // ── Load commission data when the modal opens ─────────────

    modalEl.addEventListener('show.bs.modal', async (event) => {
        resetForm();

        const trigger = event.relatedTarget;
        if (trigger?.hasAttribute('data-commission-id')) {
            modalEl.dataset.commissionId = trigger.getAttribute('data-commission-id');
        }

        const commissionId = getCommissionId();

        if (!commissionId) {
            hide(loadingEl);
            A.showErrorModal('No commission selected to edit.');
            return;
        }

        try {
            const res = await fetch(`${APP_BASE_URL}api/commissions/manage.php?commission_id=${commissionId}`);
            const data = await res.json();

            hide(loadingEl);

            if (!data || !data.success) {
                A.showErrorModal(data?.message || 'Failed to load commission.');
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
            A.showErrorModal('A network error occurred while loading this commission.');
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


});