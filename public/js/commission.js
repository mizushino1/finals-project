/* ══════════════════════════════════════════════════════════════
   commission.js — Commissions page entry point
   Requires (in order): artovia.core.js, artovia.cards.js, artovia.api.js
══════════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    const A = window.Artovia;
    if (!A) { console.error('commission.js: Artovia core not loaded.'); return; }

    // ── State ──────────────────────────────────────────────────

    let allCommissions = [];

    // ── DOM refs ───────────────────────────────────────────────

    const grid              = document.getElementById('commissionGrid');
    const gridLoading       = document.getElementById('commissionGridLoading');
    const gridError         = document.getElementById('commissionGridError');
    const gridEmpty         = document.getElementById('commissionGridEmpty');

    const pendingStrip      = document.getElementById('pendingGrid');
    const pendingStripLoading = document.getElementById('pendingGridLoading');
    const pendingSection    = document.getElementById('pendingSection');

    const artistPendingSection  = document.getElementById('artistPendingSection');
    const artistPendingGrid     = document.getElementById('artistPendingGrid');
    const artistPendingLoading  = document.getElementById('artistPendingLoading');
    const artistPendingEmpty    = document.getElementById('artistPendingEmpty');
    const artistPendingBadge    = document.getElementById('artistPendingBadge');
    const artistAcceptedSection = document.getElementById('artistAcceptedSection');
    const artistAcceptedGrid    = document.getElementById('artistAcceptedGrid');
    const artistAcceptedLoading = document.getElementById('artistAcceptedLoading');
    const artistAcceptedEmpty   = document.getElementById('artistAcceptedEmpty');
    const artistAcceptedBadge   = document.getElementById('artistAcceptedBadge');

    const searchInput = document.getElementById('searchInput');
    const searchBtn   = document.getElementById('searchBtn');
    const sortSelect  = document.getElementById('sortSelect');
    const resultsNum  = document.getElementById('resultsNumber');
    const clearBtn    = document.getElementById('clearFilters');

    // Post commission modal
    const submitBtn     = document.getElementById('submitCommissionBtn');
    const titleInput    = document.getElementById('commissionTitle');
    const descInput     = document.getElementById('commissionDescription');
    const budgetInput   = document.getElementById('commissionBudget');
    const categoryInput = document.getElementById('commissionCategory');
    const imageFile     = document.getElementById('commissionImageFile');
    const imageName     = document.getElementById('commissionImageName');
    const formAlert     = document.getElementById('commissionFormAlert');

    // Edit commission modal
    const editModal        = document.getElementById('editCommissionModal');
    const editTitle        = document.getElementById('editCommissionTitle');
    const editDesc         = document.getElementById('editCommissionDescription');
    const editBudget       = document.getElementById('editCommissionBudget');
    const editCategory     = document.getElementById('editCommissionCategory');
    const editImageFile    = document.getElementById('editCommissionImageFile');
    const editImageName    = document.getElementById('editCommissionImageName');
    const editImagePreview = document.getElementById('editCommissionImagePreview');
    const editAlert        = document.getElementById('editCommissionFormAlert');
    const editSaveBtn      = document.getElementById('saveCommissionBtn');
    const editCancelBtn    = document.getElementById('cancelCommissionBtn');

    let activeEditId = null;

    const BASE = A.config.baseUrl;

    // ── Filtering & Sorting ────────────────────────────────────

    function sortData(data, sortVal) {
        const list = [...data];
        switch (sortVal) {
            case 'newest':      return list.sort((a, b) => new Date(b.commission_date || 0) - new Date(a.commission_date || 0));
            case 'oldest':      return list.sort((a, b) => new Date(a.commission_date || 0) - new Date(b.commission_date || 0));
            case 'budget_desc': return list.sort((a, b) => parseFloat(b.price ?? 0) - parseFloat(a.price ?? 0));
            case 'budget_asc':  return list.sort((a, b) => parseFloat(a.price ?? 0) - parseFloat(b.price ?? 0));
            default:            return list;
        }
    }

    function applyFilters() {
        const budgetVal = document.querySelector('input[name="budget"]:checked')?.value ?? '0-999999';
        const statusVal = document.querySelector('input[name="status"]:checked')?.value ?? 'all';
        const searchVal = searchInput?.value.trim().toLowerCase() ?? '';
        const sortVal   = sortSelect?.value ?? 'newest';

        const [minPrice, maxPrice] = budgetVal.split('-').map(Number);

        let filtered = allCommissions.filter(c => {
            const price          = parseFloat(c.price ?? 0);
            const matchesBudget  = price >= minPrice && price <= maxPrice;
            const matchesStatus  = statusVal === 'all' || parseInt(c.status_id) === parseInt(statusVal);
            const searchIn       = [c.posted_by, c.description, c.category_name].map(s => (s || '').toLowerCase());
            const matchesSearch  = !searchVal || searchIn.some(s => s.includes(searchVal));
            return matchesBudget && matchesStatus && matchesSearch;
        });

        filtered = sortData(filtered, sortVal);
        if (resultsNum) resultsNum.textContent = filtered.length;

        if (filtered.length === 0) {
            A.hide(grid);
            A.show(gridEmpty);
        } else {
            A.hide(gridEmpty);
            A.show(grid);
            if (grid) grid.innerHTML = filtered.map((c, i) => A.buildCard(c, i)).join('');
        }
    }

    // ── Data loaders ───────────────────────────────────────────

    async function loadCommissions() {
        A.show(gridLoading);
        A.hide(grid);
        A.hide(gridError);
        A.hide(gridEmpty);

        try {
            const res  = await fetch(`${BASE}api/commissions/fetch.php`);
            const data = await res.json();

            A.hide(gridLoading);

            if (!data?.success) {
                A.hide(pendingStripLoading);
                A.hide(pendingSection);
                A.show(gridError);
                if (resultsNum) resultsNum.textContent = '0';
                return;
            }

            allCommissions = Array.isArray(data.data) ? data.data : [];
            loadPendingRequests();
            loadArtistCommissions();
            applyFilters();

        } catch (err) {
            console.error('Commission load error:', err);
            A.hide(gridLoading);
            A.hide(pendingStripLoading);
            A.hide(pendingSection);
            A.show(gridError);
        }
    }

    async function loadPendingRequests() {
        const role = A.config.currentRole;
        if (role !== 'user' && role !== 'client' && role !== 'admin') {
            A.hide(pendingSection);
            return;
        }

        try {
            const res  = await fetch(`${BASE}api/commissions/fetch_pending_requests.php`);
            const data = await res.json();

            A.hide(pendingStripLoading);

            if (!data?.success || !Array.isArray(data.data) || data.data.length === 0) {
                A.hide(pendingSection);
                return;
            }

            if (pendingStrip) {
                pendingStrip.innerHTML = data.data.map((r, i) => A.buildRequestCard(r, i)).join('');
                A.show(pendingStrip);
            }
            A.show(pendingSection);

        } catch (err) {
            console.error('Pending requests load error:', err);
            A.hide(pendingStripLoading);
            A.hide(pendingSection);
        }
    }

    async function loadArtistCommissions() {
        if (A.config.currentRole !== 'artist') {
            A.hide(artistPendingSection);
            A.hide(artistAcceptedSection);
            return;
        }

        try {
            const res  = await fetch(`${BASE}api/commissions/fetch_artist_commissions.php`);
            const data = await res.json();

            A.hide(artistPendingLoading);
            A.hide(artistAcceptedLoading);

            // Pending strip
            if (data.success && data.pending?.length > 0) {
                if (artistPendingGrid) {
                    artistPendingGrid.innerHTML = data.pending.map((r, i) => A.buildArtistPendingCard(r, i)).join('');
                    A.show(artistPendingGrid);
                }
                A.hide(artistPendingEmpty);
                if (artistPendingBadge) { artistPendingBadge.textContent = data.pending.length; artistPendingBadge.style.display = ''; }
            } else {
                A.hide(artistPendingGrid);
                A.show(artistPendingEmpty);
                if (artistPendingBadge) artistPendingBadge.style.display = 'none';
            }

            // Accepted strip
            if (data.success && data.accepted?.length > 0) {
                if (artistAcceptedGrid) {
                    artistAcceptedGrid.innerHTML = data.accepted.map((c, i) => A.buildArtistAcceptedCard(c, i)).join('');
                    A.show(artistAcceptedGrid);
                }
                A.hide(artistAcceptedEmpty);
                if (artistAcceptedBadge) { artistAcceptedBadge.textContent = data.accepted.length; artistAcceptedBadge.style.display = ''; }
            } else {
                A.hide(artistAcceptedGrid);
                A.show(artistAcceptedEmpty);
                if (artistAcceptedBadge) artistAcceptedBadge.style.display = 'none';
            }

        } catch (err) {
            console.error('Artist commissions load error:', err);
            A.hide(artistPendingLoading);
            A.hide(artistAcceptedLoading);
            A.show(artistPendingEmpty);
            A.show(artistAcceptedEmpty);
        }
    }

    // ── Review modal ───────────────────────────────────────────

    function openReviewModal(commissionId) {
        const reviewModalEl = document.getElementById('reviewModal');
        const hiddenInput   = document.getElementById('reviewCommissionId');

        if (!reviewModalEl) {
            console.error('Review modal not found in DOM.');
            return;
        }

        reviewModalEl.querySelector('form')?.reset();
        if (hiddenInput) hiddenInput.value = commissionId;

        bootstrap.Modal.getOrCreateInstance(reviewModalEl).show();
    }

    // ── Post commission modal ──────────────────────────────────

    function resetPostModal() {
        if (titleInput)    titleInput.value    = '';
        if (descInput)     descInput.value     = '';
        if (budgetInput)   budgetInput.value   = '';
        if (categoryInput) categoryInput.value = '';
        if (imageFile)     imageFile.value     = '';
        if (imageName)     imageName.textContent = '';
        A.hide(formAlert);
    }

    if (imageFile) {
        imageFile.addEventListener('change', () => {
            if (imageName) imageName.textContent = imageFile.files[0]?.name ?? '';
        });
    }

    if (submitBtn) {
        submitBtn.addEventListener('click', async () => {
            A.hide(formAlert);

            const title       = titleInput?.value.trim()    ?? '';
            const description = descInput?.value.trim()     ?? '';
            const budget      = parseFloat(budgetInput?.value ?? 0);
            const category_id = parseInt(categoryInput?.value ?? 0);

            if (!title)                        { A.showAlert(formAlert, 'Please provide a commission name.');            return; }
            if (!description)                  { A.showAlert(formAlert, 'Please provide a project description.');        return; }
            if (isNaN(budget) || budget <= 0)  { A.showAlert(formAlert, 'Please enter a valid budget higher than ₱0.'); return; }
            if (!category_id)                  { A.showAlert(formAlert, 'Please select a category.');                   return; }

            A.btnLoading(submitBtn, 'Posting…');

            try {
                const formData = new FormData();
                formData.append('title',       title);
                formData.append('description', description);
                formData.append('budget',      budget);
                formData.append('category_id', category_id);
                if (imageFile?.files[0]) formData.append('image', imageFile.files[0]);

                const res  = await fetch(`${BASE}api/commissions/create.php`, { method: 'POST', body: formData });
                const data = await res.json();

                if (data?.success) {
                    A.showAlert(formAlert, data.message, true);
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('postCommissionModal'))?.hide();
                        loadCommissions();
                    }, 1500);
                } else {
                    A.showAlert(formAlert, data?.message || 'Something went wrong.');
                }
            } catch {
                A.showAlert(formAlert, 'Network error — please try again.');
            } finally {
                A.btnReset(submitBtn, 'Post Commission');
            }
        });
    }

    document.getElementById('postCommissionModal')?.addEventListener('hidden.bs.modal', resetPostModal);

    // ── Edit commission modal ──────────────────────────────────

    function showEditAlert(message, isSuccess = false) {
        A.showAlert(editAlert, message, isSuccess);
    }

    if (editModal) {
        editModal.addEventListener('show.bs.modal', async (e) => {
            const commissionId = parseInt(e.relatedTarget?.getAttribute('data-commission-id') ?? 0);
            if (!commissionId) return;

            activeEditId = commissionId;
            A.hide(editAlert);

            // Reset fields while fetching
            if (editTitle)    editTitle.value        = '';
            if (editDesc)     editDesc.value         = '';
            if (editBudget)   editBudget.value       = '';
            if (editCategory) editCategory.value     = '';
            if (editImageName) editImageName.textContent = '';
            if (editImagePreview) { editImagePreview.src = ''; A.hide(editImagePreview); }

            try {
                const res  = await fetch(`${BASE}api/commissions/manage.php?commission_id=${commissionId}`);
                const data = await res.json();

                if (!data?.success) { showEditAlert(data?.message || 'Failed to load commission data.'); return; }

                const d = data.data;
                if (editTitle)    editTitle.value    = d.title       ?? '';
                if (editDesc)     editDesc.value     = d.description ?? '';
                if (editBudget)   editBudget.value   = d.price       ?? '';
                if (editCategory) editCategory.value = d.category_id ?? '';

                if (editImagePreview && d.image_url) {
                    editImagePreview.src = `${BASE}${d.image_url}`;
                    A.show(editImagePreview);
                }
            } catch (err) {
                console.error('Edit modal load error:', err);
                showEditAlert('Network error loading commission data.');
            }
        });

        editModal.addEventListener('hidden.bs.modal', () => {
            activeEditId = null;
            A.hide(editAlert);
            if (editImagePreview) { editImagePreview.src = ''; A.hide(editImagePreview); }
        });
    }

    if (editImageFile) {
        editImageFile.addEventListener('change', () => {
            if (editImageName) editImageName.textContent = editImageFile.files[0]?.name ?? '';
            if (editImagePreview && editImageFile.files[0]) {
                editImagePreview.src = URL.createObjectURL(editImageFile.files[0]);
                A.show(editImagePreview);
            }
        });
    }

    if (editSaveBtn) {
        editSaveBtn.addEventListener('click', async () => {
            if (!activeEditId) return;
            A.hide(editAlert);

            const title       = editTitle?.value.trim()    ?? '';
            const description = editDesc?.value.trim()     ?? '';
            const budget      = parseFloat(editBudget?.value ?? 0);
            const category_id = parseInt(editCategory?.value ?? 0);

            if (!title)                        { showEditAlert('Please provide a commission name.');            return; }
            if (!description)                  { showEditAlert('Please provide a project description.');        return; }
            if (isNaN(budget) || budget <= 0)  { showEditAlert('Please enter a valid budget higher than ₱0.'); return; }
            if (!category_id)                  { showEditAlert('Please select a category.');                   return; }

            A.btnLoading(editSaveBtn, 'Saving…');

            try {
                const formData = new FormData();
                formData.append('commission_id', activeEditId);
                formData.append('action',        'update');
                formData.append('title',         title);
                formData.append('description',   description);
                formData.append('budget',        budget);
                formData.append('category_id',   category_id);
                if (editImageFile?.files[0]) formData.append('image', editImageFile.files[0]);

                const res  = await fetch(`${BASE}api/commissions/manage.php`, { method: 'POST', body: formData });
                const data = await res.json();

                if (data?.success) {
                    showEditAlert(data.message || 'Commission updated successfully.', true);
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(editModal)?.hide();
                        loadCommissions();
                    }, 1200);
                } else {
                    showEditAlert(data?.message || 'Failed to save changes.');
                }
            } catch (err) {
                console.error('Save commission error:', err);
                showEditAlert('Network error — please try again.');
            } finally {
                A.btnReset(editSaveBtn, 'Save Changes');
            }
        });
    }

    if (editCancelBtn) {
        editCancelBtn.addEventListener('click', async () => {
            if (!activeEditId) return;
            if (!confirm('Are you sure you want to cancel this commission? This cannot be undone.')) return;

            A.btnLoading(editCancelBtn, 'Cancelling…');

            try {
                const data = await A.postJSON(`${BASE}api/commissions/manage.php`, {
                    commission_id: activeEditId,
                    action:        'cancel',
                });

                if (data?.success) {
                    bootstrap.Modal.getInstance(editModal)?.hide();
                    A.showSuccessModal('Commission Cancelled', data.message || 'Your commission has been cancelled.');
                    loadCommissions();
                } else {
                    showEditAlert(data?.message || 'Failed to cancel commission.');
                }
            } catch (err) {
                console.error('Cancel commission error:', err);
                showEditAlert('Network error — please try again.');
            } finally {
                A.btnReset(editCancelBtn, 'Cancel Commission');
            }
        });
    }

    // ── Filter controls ────────────────────────────────────────

    document.querySelectorAll('input[type="radio"]').forEach(r => r.addEventListener('change', applyFilters));
    if (sortSelect) sortSelect.addEventListener('change', applyFilters);
    if (searchBtn)  searchBtn.addEventListener('click', applyFilters);
    if (searchInput) searchInput.addEventListener('keydown', e => { if (e.key === 'Enter') applyFilters(); });

    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            const budgetAll = document.querySelector('input[name="budget"][value="0-999999"]');
            const statusAll = document.querySelector('input[name="status"][value="all"]');
            if (budgetAll)  budgetAll.checked = true;
            if (statusAll)  statusAll.checked = true;
            if (searchInput) searchInput.value = '';
            if (sortSelect)  sortSelect.value  = 'newest';
            applyFilters();
        });
    }

    // ── Delegated click router ─────────────────────────────────

    document.addEventListener('click', e => {
        const t = e.target;

        const reviewBtn = t.closest('.btn-review-trigger');
        if (reviewBtn) {
            openReviewModal(reviewBtn.getAttribute('data-commission-id'));
            return;
        }

        if (t.classList.contains('assign-artist-btn')) {
            A.handleAssignArtist(t, () => { loadCommissions(); loadPendingRequests(); });
            return;
        }
        if (t.classList.contains('decline-artist-btn')) {
            A.handleDeclineArtist(t, () => { loadCommissions(); loadPendingRequests(); });
            return;
        }
        if (t.classList.contains('take-commission-btn')) {
            A.handleTakeCommission(t, loadCommissions);
            return;
        }
        if (t.classList.contains('artist-progress-btn')) {
            A.handleArtistStatusUpdate(t, 'in_progress', () => { loadArtistCommissions(); loadCommissions(); });
            return;
        }
        if (t.classList.contains('artist-complete-btn')) {
            A.handleArtistStatusUpdate(t, 'completed', () => { loadArtistCommissions(); loadCommissions(); });
            return;
        }
        if (t.classList.contains('artist-cancel-request-btn')) {
            A.handleCancelRequest(t, loadArtistCommissions);
            return;
        }

        // ── Restore cancelled commission ───────────────────────
        if (t.classList.contains('restore-commission-btn')) {
            const commissionId = parseInt(t.getAttribute('data-commission-id'));
            if (!commissionId) return;
            if (!confirm('Restore this commission? It will be set back to Active and open for artists to apply.')) return;

            t.disabled = true;
            A.postJSON(`${BASE}api/commissions/update_status.php`, {
                commission_id: commissionId,
                status: 'active',
            }).then(data => {
                if (data?.success) {
                    loadCommissions();
                } else {
                    alert(data?.message || 'Failed to restore commission.');
                    t.disabled = false;
                }
            }).catch(() => {
                alert('Network error — please try again.');
                t.disabled = false;
            });
            return;
        }

        // ── Permanently delete cancelled commission ────────────
        if (t.classList.contains('delete-commission-btn')) {
            const commissionId = parseInt(t.getAttribute('data-commission-id'));
            if (!commissionId) return;
            if (!confirm('Permanently delete this commission? This cannot be undone.')) return;

            t.disabled = true;
            A.postJSON(`${BASE}api/commissions/delete.php`, {
                commission_id: commissionId,
            }).then(data => {
                if (data?.success) {
                    loadCommissions();
                } else {
                    alert(data?.message || 'Failed to delete commission.');
                    t.disabled = false;
                }
            }).catch(() => {
                alert('Network error — please try again.');
                t.disabled = false;
            });
            return;
        }
    });

    // ── Boot ───────────────────────────────────────────────────

    loadCommissions();
});