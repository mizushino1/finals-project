/* ══════════════════════════════════════════════════════════════
   profile.js — Artist / User profile page
   Requires: artovia.core.js
══════════════════════════════════════════════════════════════ */

(function (global) {
    'use strict';

    const A    = global.Artovia;
    const BASE = () => A.config.baseUrl;
    const esc  = (s) => A.escapeHtml(s);

    if (!A) { console.error('profile.js: Artovia core not loaded.'); return; }

    // ── Boot ───────────────────────────────────────────────────

    document.addEventListener('DOMContentLoaded', function () {
        const followBtn = document.getElementById('btn-follow-action');
        if (followBtn) followBtn.addEventListener('click', handleFollowToggle);

        if (document.querySelectorAll('#profileTabs button').length > 0) {
            initTabListeners();
        }

        checkForExtendedProfileData();

        const grid      = document.getElementById('artworks-grid');
        const accountId = parseInt(grid?.getAttribute('data-account-id'), 10) || 0;
        if (grid && accountId) loadArtworks(accountId, 1);

        initUploadArtworkModal();

        if (window.IS_OWN_USER_PROFILE) {
            initProfileCommissionsTab();
            initPostCommissionModal();
        }
    });

    /* ═══════════════════════════════════════════════════════════
       ARTWORK LOADING
    ═══════════════════════════════════════════════════════════ */

    let artworkCurrentPage = 1;

    function loadArtworks(accountId, page) {
        const grid         = document.getElementById('artworks-grid');
        const paginationEl = document.getElementById('artworks-pagination');
        const loading      = document.getElementById('artworks-loading');
        if (!grid) return;

        artworkCurrentPage = page;
        if (loading) loading.style.display = 'block';
        grid.querySelectorAll('.artwork-card-col, .artworks-empty').forEach(el => el.remove());

        const url = `${BASE()}api/profile/fetch_artworks.php?account_id=${accountId}&page=${page}&per_page=12`;

        fetch(url)
            .then(res => {
                if (!res.ok) throw new Error('Network error fetching artworks');
                return res.json();
            })
            .then(result => {
                if (loading) loading.style.display = 'none';

                if (!result.success) {
                    renderArtworksEmpty(grid, result.message || 'Could not load artworks.');
                    return;
                }

                const artworks = result.data || [];
                if (artworks.length === 0) {
                    renderArtworksEmpty(grid, 'No artworks uploaded yet.');
                    return;
                }

                artworks.forEach(artwork => {
                    const col = document.createElement('div');
                    col.className = 'col artwork-card-col';
                    col.innerHTML = buildArtworkCard(artwork);
                    col.querySelector('.artwork-card').addEventListener('click', function () {
                        openArtworkModal(JSON.parse(this.getAttribute('data-artwork')));
                    });
                    grid.appendChild(col);
                });

                if (paginationEl) {
                    renderPagination(paginationEl, result.pages, page, (p) => loadArtworks(accountId, p));
                }
            })
            .catch(err => {
                if (loading) loading.style.display = 'none';
                renderArtworksEmpty(grid, 'Failed to load artworks. Please try again.');
                console.error('Artwork fetch error:', err);
            });
    }

    function buildArtworkCard(artwork) {
        const imgSrc = BASE() + esc(artwork.image_url);
        const date   = artwork.uploaded_at
            ? new Date(artwork.uploaded_at).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' })
            : '';
        const title  = esc(artwork.title  || 'Untitled');
        const artist = esc(artwork.username || 'Unknown');

        return `
            <div class="card artwork-card border-0 theme-border"
                 style="cursor:pointer;background:var(--clr-bg-card);transition:all 0.3s ease;border-radius:var(--radius-md);"
                 onmouseover="this.style.borderColor='var(--clr-gold)';this.style.transform='translateY(-5px)';"
                 onmouseout="this.style.borderColor='transparent';this.style.transform='translateY(0)';"
                 data-artwork='${JSON.stringify(artwork).replace(/'/g, "&#39;")}'>
                <div class="artwork-card-img-wrap" style="aspect-ratio:1/1;overflow:hidden;border-bottom:2px solid var(--clr-gold);">
                    <img src="${imgSrc}" alt="${title}" loading="lazy" class="card-img-top"
                         style="width:100%;height:100%;object-fit:cover;"
                         onerror="this.src='${BASE()}public/img/placeholder-artwork.png'">
                </div>
                <div class="card-body p-3">
                    <h6 class="mb-1 fw-bold text-truncate" style="font-family:'Joan',serif;">${title}</h6>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="text-secondary" style="font-size:0.8rem;">${artist}</span>
                        <span class="text-secondary" style="font-size:0.75rem;">${date}</span>
                    </div>
                </div>
            </div>`;
    }

    function renderArtworksEmpty(grid, message) {
        grid.querySelector('.artworks-empty')?.remove();
        const empty = document.createElement('div');
        empty.className = 'col-12 text-center py-5 text-muted artworks-empty';
        empty.innerHTML = `<p class="mb-0">${esc(message)}</p>`;
        grid.appendChild(empty);
    }

    /* ═══════════════════════════════════════════════════════════
       PAGINATION (shared by artworks & reviews)
    ═══════════════════════════════════════════════════════════ */

    // Generic paginator — onPageClick(page) is called when the user picks a page.
    function renderPagination(container, totalPages, currentPage, onPageClick) {
        container.innerHTML = '';
        if (totalPages <= 1) return;

        const nav = document.createElement('nav');
        nav.setAttribute('aria-label', 'Pagination');
        const ul = document.createElement('ul');
        ul.className = 'pagination pagination-sm';

        const addPage = (label, page, disabled, active) => {
            const li = document.createElement('li');
            li.className = `page-item${disabled ? ' disabled' : ''}${active ? ' active' : ''}`;
            const a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.innerHTML = label;
            if (!disabled && !active) {
                a.addEventListener('click', e => {
                    e.preventDefault();
                    onPageClick(page);
                    container.scrollIntoView?.({ behavior: 'smooth', block: 'start' });
                });
            }
            li.appendChild(a);
            ul.appendChild(li);
        };

        addPage('&laquo;', currentPage - 1, currentPage === 1, false);

        const delta = 2;
        const start = Math.max(1, currentPage - delta);
        const end   = Math.min(totalPages, currentPage + delta);

        if (start > 1) {
            addPage('1', 1, false, false);
            if (start > 2) addPage('…', null, true, false);
        }
        for (let p = start; p <= end; p++) addPage(p, p, false, p === currentPage);
        if (end < totalPages) {
            if (end < totalPages - 1) addPage('…', null, true, false);
            addPage(totalPages, totalPages, false, false);
        }

        addPage('&raquo;', currentPage + 1, currentPage === totalPages, false);

        nav.appendChild(ul);
        container.appendChild(nav);
    }

    /* ═══════════════════════════════════════════════════════════
       ARTWORK UPLOAD MODAL
    ═══════════════════════════════════════════════════════════ */

    function initUploadArtworkModal() {
        if (!document.getElementById('btn-edit-profile')) return;
        if (!window.IS_ARTIST) return;

        const editBtn = document.getElementById('btn-edit-profile');
        if (editBtn && !document.getElementById('btn-upload-artwork')) {
            const uploadBtn = document.createElement('button');
            uploadBtn.id        = 'btn-upload-artwork';
            uploadBtn.type      = 'button';
            uploadBtn.className = 'btn btn-fill-static ms-2';
            uploadBtn.innerHTML = '<i class="fas fa-upload me-1"></i> Upload Artwork';
            uploadBtn.addEventListener('click', openUploadModal);
            editBtn.parentNode.insertBefore(uploadBtn, editBtn.nextSibling);
        }

        if (!document.getElementById('uploadArtworkModal')) {
            document.body.insertAdjacentHTML('beforeend', buildUploadModalHTML());
        }

        document.getElementById('upload-artwork-form')?.addEventListener('submit', handleArtworkUpload);
        document.getElementById('artwork-file-input')?.addEventListener('change', handleArtworkPreview);
    }

    function openUploadModal() {
        const modalEl = document.getElementById('uploadArtworkModal');
        if (!modalEl) return;

        document.getElementById('upload-artwork-form')?.reset();
        document.getElementById('artwork-preview-wrap')?.classList.add('d-none');
        setUploadAlert('', '');

        // Clone confirm button to clear stale listeners
        const confirmBtn = document.getElementById('btn-confirm-upload');
        if (confirmBtn) {
            const freshBtn = confirmBtn.cloneNode(true);
            confirmBtn.replaceWith(freshBtn);
            freshBtn.addEventListener('click', () => {
                document.getElementById('upload-artwork-form')?.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
            });
        }

        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }

    function handleArtworkPreview(event) {
        const file = event.target.files[0];
        if (!file) return;
        const wrap = document.getElementById('artwork-preview-wrap');
        const img  = document.getElementById('artwork-preview-img');
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; wrap.classList.remove('d-none'); };
        reader.readAsDataURL(file);
    }

    function handleArtworkUpload(event) {
        event.preventDefault();

        const fileInput  = document.getElementById('artwork-file-input');
        const titleInput = document.getElementById('artwork-title');
        const spinner    = document.getElementById('upload-spinner');
        const confirmBtn = document.getElementById('btn-confirm-upload');

        if (!fileInput.files[0])        { setUploadAlert('Please select an image file.',           'danger'); return; }
        if (!titleInput.value.trim())    { setUploadAlert('Please enter a title for your artwork.', 'danger'); return; }

        if (spinner)    spinner.classList.remove('d-none');
        if (confirmBtn) confirmBtn.disabled = true;
        setUploadAlert('', '');

        const formData = new FormData();
        formData.append('artwork',      fileInput.files[0]);
        formData.append('title',        titleInput.value.trim());
        formData.append('description',  document.getElementById('artwork-description')?.value.trim() || '');

        fetch(`${BASE()}api/profile/upload_artwork.php`, {
            method:  'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body:    formData,
        })
            .then(res => { if (!res.ok) throw new Error('Server error'); return res.json(); })
            .then(result => {
                if (result.success) {
                    setUploadAlert('Artwork uploaded successfully!', 'success');
                    const grid      = document.getElementById('artworks-grid');
                    const accountId = parseInt(grid?.getAttribute('data-account-id'), 10) || 0;
                    if (accountId) {
                        setTimeout(() => {
                            loadArtworks(accountId, 1);
                            bootstrap.Modal.getInstance(document.getElementById('uploadArtworkModal'))?.hide();
                        }, 800);
                    }
                } else {
                    setUploadAlert(result.message || 'Upload failed. Please try again.', 'danger');
                }
            })
            .catch(err => {
                console.error('Upload error:', err);
                setUploadAlert('An unexpected error occurred. Please try again.', 'danger');
            })
            .finally(() => {
                if (spinner)    spinner.classList.add('d-none');
                if (confirmBtn) confirmBtn.disabled = false;
            });
    }

    function setUploadAlert(message, type) {
        const el = document.getElementById('upload-artwork-alert');
        if (!el) return;
        if (!message) { el.className = 'alert d-none'; el.textContent = ''; return; }
        el.className   = `alert alert-${type}`;
        el.textContent = message;
    }

    function buildUploadModalHTML() {
        return `
        <div class="modal fade" id="uploadArtworkModal" tabindex="-1"
             aria-labelledby="uploadArtworkModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="uploadArtworkModalLabel">Upload Artwork</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div id="upload-artwork-alert" class="alert d-none" role="alert"></div>
                <div id="artwork-preview-wrap" class="mb-3 text-center d-none">
                    <img id="artwork-preview-img" src="" alt="Preview"
                         class="img-fluid rounded" style="max-height:240px;object-fit:contain;">
                </div>
                <form id="upload-artwork-form" novalidate>
                  <div class="mb-3">
                    <label for="artwork-file-input" class="form-label">Image <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="artwork-file-input" name="artwork"
                           accept=".jpg,.jpeg,.png,.gif,.webp" required>
                    <div class="form-text">JPG, PNG, GIF or WEBP · max 10 MB</div>
                  </div>
                  <div class="mb-3">
                    <label for="artwork-title" class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="artwork-title" name="title"
                           maxlength="255" placeholder="e.g. Dragon Chibi Commission" required>
                  </div>
                  <div class="mb-3">
                    <label for="artwork-description" class="form-label">Description</label>
                    <textarea class="form-control" id="artwork-description" name="description"
                              rows="3" placeholder="Describe this artwork…"></textarea>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-fill-static" id="btn-confirm-upload">
                    <span id="upload-spinner" class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                    Upload
                </button>
              </div>
            </div>
          </div>
        </div>`;
    }

    /* ═══════════════════════════════════════════════════════════
       ARTWORK VIEW MODAL
    ═══════════════════════════════════════════════════════════ */

    function openArtworkModal(artwork) {
        if (!document.getElementById('artworkViewModal')) {
            document.body.insertAdjacentHTML('beforeend', buildArtworkViewModalHTML());
        }

        const date = artwork.uploaded_at
            ? new Date(artwork.uploaded_at).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' })
            : 'Unknown date';

        document.getElementById('artworkViewTitle').textContent  = artwork.title  || 'Untitled';
        document.getElementById('artworkViewDesc').textContent   = artwork.description || 'No description provided.';
        document.getElementById('artworkViewArtist').textContent = artwork.username || 'Unknown Artist';
        document.getElementById('artworkViewDate').textContent   = date;

        const img = document.getElementById('artworkViewImg');
        img.src = BASE() + artwork.image_url;
        img.alt = artwork.title || 'Untitled';

        bootstrap.Modal.getOrCreateInstance(document.getElementById('artworkViewModal')).show();
    }

    function buildArtworkViewModalHTML() {
        return `
<style>
#artworkViewModal .modal-dialog { max-width: min(1100px, 95vw) !important; width: 100% !important; margin: 0.5rem auto !important; }
@media (min-width: 992px) { #artworkViewModal .modal-dialog { max-width: 800px !important; } }
#artworkViewModal .artwork-img-col { background: var(--clr-bg-alt); display: flex; align-items: center; justify-content: center; min-height: 260px; }
#artworkViewImg { max-width: 100%; max-height: 55vh; object-fit: contain; padding: 24px; }
@media (min-width: 992px) { #artworkViewImg { max-height: 600px; padding: 40px; } }
#artworkViewModal .artwork-meta-label  { font-size: 0.65rem; letter-spacing: 0.15em; white-space: nowrap; }
#artworkViewModal .artwork-title       { font-size: clamp(1.4rem, 3vw, 2.2rem); }
#artworkViewModal .artwork-meta-value  { white-space: nowrap; }
</style>
<div class="modal fade" id="artworkViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="background:var(--clr-bg-card);border:2px solid var(--clr-gold);border-radius:var(--radius-lg);overflow:hidden;">
            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                    data-bs-dismiss="modal" aria-label="Close" style="z-index:10;"></button>
            <div class="row g-0 flex-column flex-lg-row">
                <div class="col-12 col-lg-7 artwork-img-col">
                    <img id="artworkViewImg" src="" alt="" class="img-fluid">
                </div>
                <div class="col-12 col-lg-5 d-flex flex-column" style="min-width:0;">
                    <div class="p-4 p-lg-5 flex-grow-1 d-flex flex-column justify-content-center">
                        <div class="mb-3">
                            <h2 id="artworkViewTitle" class="joan mb-1 artwork-title" style="color:var(--clr-text-primary);"></h2>
                            <div style="width:60px;height:3px;background:var(--clr-gold);"></div>
                        </div>
                        <div class="mb-4">
                            <p id="artworkViewDesc" class="text-secondary" style="line-height:1.8;font-size:1.05rem;"></p>
                        </div>
                        <div class="mt-auto border-top pt-3" style="border-color:var(--clr-border)!important;">
                            <div class="d-flex flex-row justify-content-start gap-4">
                                <div>
                                    <p class="text-muted text-uppercase fw-bold mb-1 artwork-meta-label">Artist</p>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-fill" style="color:var(--clr-gold);font-size:1.1rem;"></i>
                                        <span id="artworkViewArtist" class="ms-2 text-white fw-medium artwork-meta-value"></span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-muted text-uppercase fw-bold mb-1 artwork-meta-label">Uploaded</p>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-calendar-event" style="color:var(--clr-gold);font-size:1rem;"></i>
                                        <span id="artworkViewDate" class="ms-2 text-white fw-medium artwork-meta-value"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>`;
    }

    /* ═══════════════════════════════════════════════════════════
       REVIEWS
    ═══════════════════════════════════════════════════════════ */

    function loadReviews(accountId, page) {
        const list       = document.getElementById('reviews-list');
        const pagination = document.getElementById('reviews-pagination');
        const pane       = document.getElementById('pane-reviews');
        if (!list || !accountId) return;

        list.innerHTML = `
            <div class="text-center py-5 text-muted">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                Loading reviews…
            </div>`;

        fetch(`${BASE()}api/profile/fetch_reviews.php?account_id=${accountId}&page=${page}&per_page=10`)
            .then(res => res.json())
            .then(result => {
                if (!result.success || !result.data.length) {
                    list.innerHTML = '<p class="text-center text-muted py-4">No reviews yet.</p>';
                    return;
                }

                list.innerHTML = result.data.map(buildReviewCard).join('');
                if (pane) pane.setAttribute('data-loaded', 'true');

                if (result.avg_rating !== undefined) {
                    const avgEl    = document.getElementById('review-summary-avg');
                    const statAvg  = document.getElementById('stat-avg-rating');
                    const countEl  = document.getElementById('stat-review-count');
                    if (avgEl)   avgEl.textContent   = result.avg_rating || '—';
                    if (statAvg) statAvg.innerHTML   = `<i class="fas fa-star"></i> ${result.avg_rating || '—'}/5`;
                    if (countEl) countEl.textContent = `(${result.total ?? 0})`;
                }

                if (pagination) {
                    renderPagination(pagination, result.pages, page, (p) => loadReviews(accountId, p));
                }
            })
            .catch(err => {
                console.error('Reviews fetch error:', err);
                list.innerHTML = '<p class="text-center text-danger py-4">Failed to load reviews.</p>';
            });
    }

    function buildReviewCard(r) {
        const stars = Array.from({ length: 5 }, (_, i) =>
            `<i class="${i < r.rating ? 'fas' : 'far'} fa-star text-warning"></i>`
        ).join('');
        const date = r.created_at
            ? new Date(r.created_at).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' })
            : '';
        return `
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <strong>${esc(r.reviewer_username || 'Anonymous')}</strong>
                        <small class="text-muted">${date}</small>
                    </div>
                    <div class="mb-2">${stars}</div>
                    <p class="mb-0">${esc(r.comment || '')}</p>
                </div>
            </div>`;
    }

    /* ═══════════════════════════════════════════════════════════
       AVATAR SYNC
    ═══════════════════════════════════════════════════════════ */

    function checkForExtendedProfileData() {
        const isEditPage         = window.location.pathname.includes('edit.php');
        const isOwnProfileNoFollow = !document.getElementById('btn-follow-action');
        if (!isEditPage && !isOwnProfileNoFollow) return;

        fetch(`${BASE()}api/profile/fetch.php`)
            .then(res => res.json())
            .then(resData => {
                if (!resData.success || !resData.data) return;
                const avatarContainer = document.querySelector('.profile-avatar-container');
                if (!avatarContainer) return;

                if (resData.data.avatar_url && !resData.data.avatar_url.includes('default-')) {
                    avatarContainer.innerHTML = `
                        <img src="${BASE()}${resData.data.avatar_url}"
                             alt="User avatar" class="profile-avatar"
                             style="width:100%;height:100%;object-fit:cover;">`;
                } else {
                    avatarContainer.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                             fill="currentColor" class="profile-avatar"
                             style="width:100%;height:100%;background:#e9ecef;padding:2rem;box-sizing:border-box;">
                            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4
                                     7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6
                                     1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                        </svg>`;
                }
            })
            .catch(err => console.warn('Avatar sync skipped:', err));
    }

    /* ═══════════════════════════════════════════════════════════
       MY COMMISSIONS TAB (own user profile)
    ═══════════════════════════════════════════════════════════ */

    let profileCommissionsLoaded = false;

    function initProfileCommissionsTab() {
        const tabBtn = document.getElementById('tab-commissions');
        if (!tabBtn) return;

        tabBtn.addEventListener('shown.bs.tab', () => {
            if (!profileCommissionsLoaded) loadProfileCommissions();
        });

        // Tab already active on load (non-artist profiles) — shown.bs.tab won't fire
        if (tabBtn.classList.contains('active')) loadProfileCommissions();

        // Delegated click handler for the Review button on dynamically-rendered cards
        const grid = document.getElementById('profileCommissionGrid');
        if (grid) {
            grid.addEventListener('click', e => {
                const reviewBtn = e.target.closest('.btn-review-trigger');
                if (!reviewBtn) return;

                const commId      = reviewBtn.getAttribute('data-commission-id');
                const reviewModalEl = document.getElementById('reviewModal');
                if (!reviewModalEl) { console.error('reviewModal not found in DOM'); return; }

                const hiddenInput = document.getElementById('reviewCommissionId');
                if (hiddenInput) hiddenInput.value = commId;
                reviewModalEl.querySelector('form')?.reset();
                bootstrap.Modal.getOrCreateInstance(reviewModalEl).show();
            });
        }
    }

    function loadProfileCommissions() {
        const grid    = document.getElementById('profileCommissionGrid');
        const loading = document.getElementById('profileCommissionsLoading');
        const errorEl = document.getElementById('profileCommissionsError');
        const emptyEl = document.getElementById('profileCommissionsEmpty');
        if (!grid) return;

        errorEl?.classList.add('d-none');
        emptyEl?.classList.add('d-none');
        if (loading) loading.style.display = 'block';
        grid.querySelectorAll('.profile-commission-card-col').forEach(el => el.remove());

        fetch(`${BASE()}api/commissions/fetch.php`)
            .then(res => res.json())
            .then(result => {
                if (loading) loading.style.display = 'none';
                profileCommissionsLoaded = true;

                if (!result.success) { errorEl?.classList.remove('d-none'); return; }

                const commissions = result.data || [];
                if (commissions.length === 0) { emptyEl?.classList.remove('d-none'); return; }

                commissions.forEach((c, i) => {
                    const col = document.createElement('div');
                    col.className = 'col profile-commission-card-col';
                    col.innerHTML = buildProfileCommissionCard(c, i);
                    grid.appendChild(col);
                });
            })
            .catch(err => {
                console.error('Profile commissions fetch error:', err);
                if (loading) loading.style.display = 'none';
                errorEl?.classList.remove('d-none');
            });
    }

    function buildProfileCommissionActionBtn(c) {
        const statusId = parseInt(c.status_id);

        if (statusId === 6) {
            if (c.has_review) {
                return `<span class="text-success fw-semibold fs-fluid-xxs">✓ Reviewed</span>`;
            }
            return `<button type="button"
                        class="btn btn-warning text-dark btn-review-trigger py-1 px-3 fs-fluid-xs rounded-2 fw-semibold shadow-sm"
                        data-commission-id="${c.commission_id}">
                        <i class="bi bi-star-fill me-1"></i>Review
                    </button>`;
        }

        if (statusId === 5) {
            return `<a href="${BASE()}commissions/payment?id=${c.commission_id}"
                       class="btn btn-success text-white py-1 px-3 fs-fluid-xs rounded-2 fw-semibold">
                       <i class="bi bi-credit-card me-1"></i>Pay
                   </a>`;
        }

        if (statusId === 1) {
            return `<button type="button"
                        class="btn-artovia-outline py-1 px-3 fs-fluid-xs rounded-2"
                        data-bs-toggle="modal" data-bs-target="#editCommissionModal"
                        data-commission-id="${c.commission_id}">Manage</button>`;
        }

        return `<a href="${BASE()}commissions/view?id=${c.commission_id}"
                   class="btn-artovia-outline py-1 px-3 fs-fluid-xs rounded-2">View</a>`;
    }

    function buildProfileCommissionCard(c, index) {
        const clientName  = c.posted_by ?? 'Anonymous Client';
        const status      = A.getStatusConfig(c.status_id);
        const [bg, fg]    = A.PALETTE[index % A.PALETTE.length];
        const { title, body } = A.parseDescription(c.description);
        const budgetDisplay   = A.formatBudget(c.price);
        const dateStr         = A.formatDate(c.commission_date);
        const avatarHtml      = A.makeAvatar(clientName, index, c.client_avatar_url ?? c.avatar_url ?? null);
        const categoryBadge   = A.makeCategoryBadge(esc(c.category_name), bg, fg);
        const actionBtn       = buildProfileCommissionActionBtn(c);

        const refUrl = c.image_url ?? c.reference_image ?? c.reference_url ?? null;
        const refImg = refUrl
            ? `<img src="${BASE()}${refUrl}" alt="Reference"
                    class="rounded-2 object-fit-cover flex-shrink-0"
                    style="width:clamp(56px,22%,88px);aspect-ratio:1/1;border:1px solid var(--border-color,#ffffff18);background:var(--bg-subtle,#1a1a1a);"
                    onerror="this.style.display='none'">`
            : '';

        return `
            <div class="artist-card h-100 border rounded-3 d-flex flex-column shadow-sm bg-card p-3">
                <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        ${avatarHtml}
                        <div class="text-truncate">
                            <p class="m-0 fw-bold fs-fluid-sm text-truncate lh-1">${esc(clientName)}</p>
                            <small class="text-muted fs-fluid-xxs">${dateStr}</small>
                        </div>
                    </div>
                    <span class="artist-card__badge d-inline-flex flex-shrink-0 align-items-center fw-bold text-uppercase ${status.class}">
                        ${status.text}
                    </span>
                </div>
                <div class="flex-grow-1 mb-3">
                    ${categoryBadge}
                    <div class="d-flex align-items-start gap-2">
                        <div class="flex-grow-1 overflow-hidden">
                            ${title ? `<p class="m-0 fw-semibold fs-fluid-xs mb-1 text-truncate">${esc(title)}</p>` : ''}
                            <p class="text-muted small m-0" style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;line-height:1.6;">
                                ${esc(body)}
                            </p>
                        </div>
                        ${refImg}
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                    <div>
                        <p class="m-0 text-muted fs-fluid-xxs text-uppercase" style="letter-spacing:0.05em;">Budget</p>
                        <p class="m-0 fw-bold fs-fluid-sm">${budgetDisplay}</p>
                    </div>
                    ${actionBtn}
                </div>
            </div>`;
    }

    /* ═══════════════════════════════════════════════════════════
       POST COMMISSION MODAL (own user profile)
    ═══════════════════════════════════════════════════════════ */

    function initPostCommissionModal() {
        const submitBtn     = document.getElementById('submitCommissionBtn');
        const titleInput    = document.getElementById('commissionTitle');
        const descInput     = document.getElementById('commissionDescription');
        const budgetInput   = document.getElementById('commissionBudget');
        const categoryInput = document.getElementById('commissionCategory');
        const imageFile     = document.getElementById('commissionImageFile');
        const imageName     = document.getElementById('commissionImageName');
        const formAlert     = document.getElementById('commissionFormAlert');

        imageFile?.addEventListener('change', () => {
            if (imageName) imageName.textContent = imageFile.files[0]?.name ?? '';
        });

        function resetModal() {
            if (titleInput)    titleInput.value      = '';
            if (descInput)     descInput.value       = '';
            if (budgetInput)   budgetInput.value     = '';
            if (categoryInput) categoryInput.value   = '';
            if (imageFile)     imageFile.value       = '';
            if (imageName)     imageName.textContent = '';
            A.hide(formAlert);
        }

        if (submitBtn) {
            submitBtn.addEventListener('click', async () => {
                A.hide(formAlert);

                const title       = titleInput?.value.trim()    ?? '';
                const description = descInput?.value.trim()     ?? '';
                const budget      = parseFloat(budgetInput?.value ?? 0);
                const category_id = parseInt(categoryInput?.value ?? 0);

                if (!title)                       { A.showAlert(formAlert, 'Please provide a commission name.');            return; }
                if (!description)                 { A.showAlert(formAlert, 'Please provide a project description.');        return; }
                if (isNaN(budget) || budget <= 0) { A.showAlert(formAlert, 'Please enter a valid budget higher than ₱0.'); return; }
                if (!category_id)                 { A.showAlert(formAlert, 'Please select a category.');                   return; }

                A.btnLoading(submitBtn, 'Posting…');

                try {
                    const data = await A.postJSON(`${BASE()}api/commissions/create.php`, { title, description, budget, category_id });

                    if (data?.success) {
                        A.showAlert(formAlert, data.message, true);
                        setTimeout(() => {
                            bootstrap.Modal.getInstance(document.getElementById('postCommissionModal'))?.hide();
                            profileCommissionsLoaded = false;
                            loadProfileCommissions();
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

        document.getElementById('postCommissionModal')?.addEventListener('hidden.bs.modal', resetModal);
    }

    /* ═══════════════════════════════════════════════════════════
       FOLLOW / UNFOLLOW
    ═══════════════════════════════════════════════════════════ */

    function handleFollowToggle(event) {
        const btn         = event.currentTarget;
        const artistId    = parseInt(btn.getAttribute('data-artist-id'), 10) || null;
        const userId      = parseInt(btn.getAttribute('data-user-id'),   10) || null;
        const isFollowing = btn.getAttribute('data-following') === '1';

        btn.disabled = true;

        A.postJSON(`${BASE()}api/profile/follow_action.php`, {
            artist_id: artistId,
            user_id:   userId,
            action:    isFollowing ? 'unfollow' : 'follow',
        })
            .then(result => {
                if (result.success) {
                    if (isFollowing) {
                        btn.setAttribute('data-following', '0');
                        btn.className = 'btn btn-follow';
                        btn.innerHTML = '<i class="fas fa-plus me-1"></i> Follow';
                    } else {
                        btn.setAttribute('data-following', '1');
                        btn.className = 'btn btn-success';
                        btn.innerHTML = '<i class="fas fa-check me-1"></i> Following';
                    }

                    const followersEl = document.getElementById('stat-followers');
                    if (followersEl) {
                        let count = parseInt(followersEl.innerText.replace(/,/g, ''), 10) || 0;
                        count = isFollowing ? Math.max(0, count - 1) : count + 1;
                        followersEl.innerText = count.toLocaleString();
                    }

                    if (result.following_count !== undefined &&
                        window.VIEWER_ACCOUNT_ID === window.PROFILE_ACCOUNT_ID) {
                        const followingEl = document.getElementById('stat-following');
                        if (followingEl) followingEl.innerText = result.following_count.toLocaleString();
                    }
                } else {
                    alert(result.message || 'An error occurred.');
                }
            })
            .catch(err => {
                console.error('Follow toggle failed:', err);
                alert('Could not update follow status. Please check your connection.');
            })
            .finally(() => { btn.disabled = false; });
    }

    /* ═══════════════════════════════════════════════════════════
       TAB LISTENERS
    ═══════════════════════════════════════════════════════════ */

    function initTabListeners() {
        document.querySelectorAll('#profileTabs button').forEach(tabEl => {
            tabEl.addEventListener('shown.bs.tab', function (event) {
                const target = event.target.getAttribute('data-bs-target');

                if (target === '#pane-artworks') {
                    const grid      = document.getElementById('artworks-grid');
                    const accountId = parseInt(grid?.getAttribute('data-account-id'), 10) || 0;
                    if (accountId) loadArtworks(accountId, artworkCurrentPage);
                }

                if (target === '#pane-reviews') {
                    const pane      = document.getElementById('pane-reviews');
                    const accountId = parseInt(pane?.getAttribute('data-account-id'), 10) || 0;
                    if (pane?.getAttribute('data-loaded') === 'false') loadReviews(accountId, 1);
                }
            });
        });
    }

}(window));