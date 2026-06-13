// Fallback in case this page didn't define window.BASE_URL via an inline script
if (typeof window.BASE_URL === 'undefined' || window.BASE_URL === null) {
    window.BASE_URL = './';
}

document.addEventListener('DOMContentLoaded', function () {

    /* ─── Follow button ───────────────────────────────────────── */
    const followBtn = document.getElementById('btn-follow-action');
    if (followBtn) {
        followBtn.addEventListener('click', handleFollowToggle);
    }

    /* ─── Tab listeners ──────────────────────────────────────── */
    const profileTabs = document.querySelectorAll('#profileTabs button');
    if (profileTabs.length > 0) {
        initTabListeners();
    }

    /* ─── Avatar sync on own profile / edit page ─────────────── */
    checkForExtendedProfileData();

    /* ─── Artwork grid ───────────────────────────────────────── */
    const grid = document.getElementById('artworks-grid');
    if (grid) {
        const accountId = parseInt(grid.getAttribute('data-account-id'), 10) || 0;
        if (accountId) {
            loadArtworks(accountId, 1);
        }
    }

    /* ─── Upload artwork modal (artists only) ────────────────── */
    initUploadArtworkModal();

    /* ─── My Commissions tab (own user profile) ─────────────── */
    if (window.IS_OWN_USER_PROFILE) {
        initProfileCommissionsTab();
        initPostCommissionModal();
    }
});

/* ═══════════════════════════════════════════════════════════════
   ARTWORK LOADING
═══════════════════════════════════════════════════════════════ */

let artworkCurrentPage = 1;

/**
 * Fetch and render artworks for a given account.
 * @param {number} accountId
 * @param {number} page
 */
function loadArtworks(accountId, page) {
    const grid = document.getElementById('artworks-grid');
    const paginationEl = document.getElementById('artworks-pagination');
    const loading = document.getElementById('artworks-loading');

    if (!grid) return;

    artworkCurrentPage = page;

    if (loading) loading.style.display = 'block';

    grid.querySelectorAll('.artwork-card-col, .artworks-empty').forEach(el => el.remove());

    const perPage = 12;
    const url = `${window.BASE_URL}api/profile/fetch_artworks.php?account_id=${accountId}&page=${page}&per_page=${perPage}`;

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
                    const data = JSON.parse(this.getAttribute('data-artwork'));
                    openArtworkModal(data);
                });

                grid.appendChild(col);
            });

            if (paginationEl) {
                renderArtworkPagination(paginationEl, result.pages, page, accountId);
            }
        })
        .catch(err => {
            if (loading) loading.style.display = 'none';
            renderArtworksEmpty(grid, 'Failed to load artworks. Please try again.');
            console.error('Artwork fetch error:', err);
        });
}

/**
 * Build HTML for a single artwork card.
 */
function buildArtworkCard(artwork) {
    const imgSrc = window.BASE_URL + escapeHtml(artwork.image_url);
    const date = artwork.uploaded_at
        ? new Date(artwork.uploaded_at).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' })
        : '';
    const title = escapeHtml(artwork.title || 'Untitled');
    const artist = escapeHtml(artwork.artist_name || 'Unknown');

    return `
        <div class="card artwork-card border-0 theme-border"
             style="cursor:pointer; background: var(--clr-bg-card); transition: all 0.3s ease; border-radius: var(--radius-md);"
             onmouseover="this.style.borderColor='var(--clr-gold)'; this.style.transform='translateY(-5px)';"
             onmouseout="this.style.borderColor='transparent'; this.style.transform='translateY(0)';"
             data-artwork='${JSON.stringify(artwork).replace(/'/g, "&#39;")}'>
             
            <!-- Image Area -->
            <div class="artwork-card-img-wrap" style="aspect-ratio: 1/1; overflow: hidden; border-bottom: 2px solid var(--clr-gold);">
                <img src="${imgSrc}"
                     alt="${title}"
                     loading="lazy"
                     class="card-img-top"
                     style="width:100%; height:100%; object-fit:cover;"
                     onerror="this.src='${window.BASE_URL}public/img/placeholder-artwork.png'">
            </div>
            
            <!-- Metadata Area -->
            <div class="card-body p-3">
                <h6 class="mb-1 text-white fw-bold text-truncate" style="font-family: 'Joan', serif;">${title}</h6>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="text-secondary" style="font-size: 0.8rem;">${artist}</span>
                    <span class="text-secondary" style="font-size: 0.75rem;">${date}</span>
                </div>
            </div>
        </div>`;
}

/**
 * Render an empty-state message inside the grid.
 */
function renderArtworksEmpty(grid, message) {
    const existing = grid.querySelector('.artworks-empty');
    if (existing) existing.remove();

    const empty = document.createElement('div');
    empty.className = 'col-12 text-center py-5 text-muted artworks-empty';
    empty.innerHTML = `<p class="mb-0">${escapeHtml(message)}</p>`;
    grid.appendChild(empty);
}

/**
 * Render Bootstrap pagination for the artworks grid.
 */
function renderArtworkPagination(container, totalPages, currentPage, accountId) {
    container.innerHTML = '';
    if (totalPages <= 1) return;

    const nav = document.createElement('nav');
    nav.setAttribute('aria-label', 'Artworks pagination');

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
                loadArtworks(accountId, page);
                document.getElementById('artworks-grid')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        }

        li.appendChild(a);
        ul.appendChild(li);
    };

    addPage('&laquo;', currentPage - 1, currentPage === 1, false);

    // Show at most 5 page links centered around current
    const delta = 2;
    let start = Math.max(1, currentPage - delta);
    let end = Math.min(totalPages, currentPage + delta);

    if (start > 1) {
        addPage('1', 1, false, false);
        if (start > 2) addPage('…', null, true, false);
    }

    for (let p = start; p <= end; p++) {
        addPage(p, p, false, p === currentPage);
    }

    if (end < totalPages) {
        if (end < totalPages - 1) addPage('…', null, true, false);
        addPage(totalPages, totalPages, false, false);
    }

    addPage('&raquo;', currentPage + 1, currentPage === totalPages, false);

    nav.appendChild(ul);
    container.appendChild(nav);
}

/* ═══════════════════════════════════════════════════════════════
   ARTWORK UPLOAD MODAL
═══════════════════════════════════════════════════════════════ */

function initUploadArtworkModal() {
    // Only inject the modal + button if the "Edit Account Settings" link is present
    // (meaning this is the owner's own profile) AND there's no upload modal yet.
    const isOwnProfile = !!document.getElementById('btn-edit-profile');
    if (!isOwnProfile) return;

    // Insert "Upload Artwork" button next to "Edit Account Settings"
    const editBtn = document.getElementById('btn-edit-profile');
    if (editBtn && !document.getElementById('btn-upload-artwork')) {
        const uploadBtn = document.createElement('button');
        uploadBtn.id = 'btn-upload-artwork';
        uploadBtn.type = 'button';
        uploadBtn.className = 'btn btn-primary ms-2';
        uploadBtn.innerHTML = '<i class="fas fa-upload me-1"></i> Upload Artwork';
        uploadBtn.addEventListener('click', () => openUploadModal());
        editBtn.parentNode.insertBefore(uploadBtn, editBtn.nextSibling);
    }

    // Inject modal HTML once
    if (!document.getElementById('uploadArtworkModal')) {
        document.body.insertAdjacentHTML('beforeend', buildUploadModalHTML());
    }

    // Wire up form submit
    const form = document.getElementById('upload-artwork-form');
    if (form) {
        form.addEventListener('submit', handleArtworkUpload);
    }

    // Preview image before upload
    const fileInput = document.getElementById('artwork-file-input');
    if (fileInput) {
        fileInput.addEventListener('change', handleArtworkPreview);
    }
}

function openArtworkModal(artwork) {
    // Inject modal if not already present
    if (!document.getElementById('artworkViewModal')) {
        document.body.insertAdjacentHTML('beforeend', `
<style>
#artworkViewModal .modal-dialog {
    max-width: min(1100px, 95vw) !important;
    width: 100% !important;
    margin: 0.5rem auto !important;
}
@media (min-width: 992px) {
    #artworkViewModal .modal-dialog {
        max-width: 800px !important;
    }
}
    #artworkViewModal .artwork-img-col {
        background: var(--clr-bg-alt);
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 260px;
    }
    #artworkViewImg {
        max-width: 100%;
        max-height: 55vh;
        object-fit: contain;
        padding: 24px;
    }
    @media (min-width: 992px) {
        #artworkViewImg {
            max-height: 600px;
            padding: 40px;
        }
    }
    #artworkViewModal .artwork-meta-label {
        font-size: 0.65rem;
        letter-spacing: 0.15em;
        white-space: nowrap;
    }
    #artworkViewModal .artwork-title {
        font-size: clamp(1.4rem, 3vw, 2.2rem);
    }
    #artworkViewModal .artwork-meta-value {
        white-space: nowrap;
    }
</style>
<div class="modal fade" id="artworkViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="background: var(--clr-bg-card); border: 2px solid var(--clr-gold); border-radius: var(--radius-lg); overflow: hidden;">

            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                    data-bs-dismiss="modal" aria-label="Close" style="z-index: 10;"></button>

            <div class="row g-0 flex-column flex-lg-row">
                <!-- Image Column -->
                <div class="col-12 col-lg-7 artwork-img-col">
                    <img id="artworkViewImg" src="" alt="" class="img-fluid">
                </div>

                <!-- Content Column -->
                <div class="col-12 col-lg-5 d-flex flex-column" style="min-width:0;">
                    <div class="p-4 p-lg-5 flex-grow-1 d-flex flex-column justify-content-center">

                        <div class="mb-3">
                            <h2 id="artworkViewTitle" class="joan mb-1 artwork-title" style="color: var(--clr-text-primary);"></h2>
                            <div style="width: 60px; height: 3px; background: var(--clr-gold);"></div>
                        </div>

                        <div class="mb-4">
                            <p id="artworkViewDesc" class="text-secondary" style="line-height: 1.8; font-size: 1.05rem;"></p>
                        </div>

                        <!-- Metadata -->
                        <div class="mt-auto border-top pt-3" style="border-color: var(--clr-border) !important;">
                            <div class="d-flex flex-row justify-content-start gap-4">
                                <div>
                                    <p class="text-muted text-uppercase fw-bold mb-1 artwork-meta-label">Artist</p>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-fill" style="color: var(--clr-gold); font-size: 1.1rem;"></i>
                                        <span id="artworkViewArtist" class="ms-2 text-white fw-medium artwork-meta-value"></span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-muted text-uppercase fw-bold mb-1 artwork-meta-label">Uploaded</p>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-calendar-event" style="color: var(--clr-gold); font-size: 1rem;"></i>
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
</div>`);
    }

    // Populate fields
    const title = artwork.title || 'Untitled';
    const desc = artwork.description || 'No description provided.';
    const artist = artwork.username || 'Unknown Artist';
    const date = artwork.uploaded_at
        ? new Date(artwork.uploaded_at).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' })
        : 'Unknown date';
    const imgSrc = window.BASE_URL + artwork.image_url;

    document.getElementById('artworkViewTitle').textContent = title;
    document.getElementById('artworkViewImg').src = imgSrc;
    document.getElementById('artworkViewImg').alt = title;
    document.getElementById('artworkViewDesc').textContent = desc;
    document.getElementById('artworkViewArtist').textContent = artist;
    document.getElementById('artworkViewDate').textContent = date;

    bootstrap.Modal.getOrCreateInstance(document.getElementById('artworkViewModal')).show();
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
                <img id="artwork-preview-img"
                     src=""
                     alt="Preview"
                     class="img-fluid rounded"
                     style="max-height:240px;object-fit:contain;">
            </div>

            <form id="upload-artwork-form" novalidate>
              <div class="mb-3">
                <label for="artwork-file-input" class="form-label">Image <span class="text-danger">*</span></label>
                <input type="file"
                       class="form-control"
                       id="artwork-file-input"
                       name="artwork"
                       accept=".jpg,.jpeg,.png,.gif,.webp"
                       required>
                <div class="form-text">JPG, PNG, GIF or WEBP · max 10 MB</div>
              </div>

              <div class="mb-3">
                <label for="artwork-title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text"
                       class="form-control"
                       id="artwork-title"
                       name="title"
                       maxlength="255"
                       placeholder="e.g. Dragon Chibi Commission"
                       required>
              </div>

              <div class="mb-3">
                <label for="artwork-description" class="form-label">Description</label>
                <textarea class="form-control"
                          id="artwork-description"
                          name="description"
                          rows="3"
                          placeholder="Describe this artwork…"></textarea>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="btn-confirm-upload">
                <span id="upload-spinner" class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                Upload
            </button>
          </div>
        </div>
      </div>
    </div>`;
}

function openUploadModal() {
    const modalEl = document.getElementById('uploadArtworkModal');
    if (!modalEl) return;

    // Reset form state
    const form = document.getElementById('upload-artwork-form');
    if (form) form.reset();

    const preview = document.getElementById('artwork-preview-wrap');
    if (preview) preview.classList.add('d-none');

    setUploadAlert('', '');

    const confirmBtn = document.getElementById('btn-confirm-upload');
    if (confirmBtn) {
        // Remove old listener to avoid duplicates, then re-add
        const newBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);
        newBtn.addEventListener('click', () => {
            document.getElementById('upload-artwork-form')?.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
        });
    }

    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

function handleArtworkPreview(event) {
    const file = event.target.files[0];
    if (!file) return;

    const wrap = document.getElementById('artwork-preview-wrap');
    const img = document.getElementById('artwork-preview-img');

    const reader = new FileReader();
    reader.onload = e => {
        img.src = e.target.result;
        wrap.classList.remove('d-none');
    };
    reader.readAsDataURL(file);
}

function handleArtworkUpload(event) {
    event.preventDefault();

    const fileInput = document.getElementById('artwork-file-input');
    const titleInput = document.getElementById('artwork-title');

    if (!fileInput.files[0]) {
        setUploadAlert('Please select an image file.', 'danger');
        return;
    }
    if (!titleInput.value.trim()) {
        setUploadAlert('Please enter a title for your artwork.', 'danger');
        return;
    }

    const spinner = document.getElementById('upload-spinner');
    const confirmBtn = document.getElementById('btn-confirm-upload');
    if (spinner) spinner.classList.remove('d-none');
    if (confirmBtn) confirmBtn.disabled = true;

    setUploadAlert('', '');

    const formData = new FormData();
    formData.append('artwork', fileInput.files[0]);
    formData.append('title', titleInput.value.trim());
    formData.append('description', document.getElementById('artwork-description')?.value.trim() || '');

    fetch(`${window.BASE_URL}api/profile/upload_artwork.php`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
        .then(res => {
            if (!res.ok) throw new Error('Server returned an error response');
            return res.json();
        })
        .then(result => {
            if (result.success) {
                setUploadAlert('Artwork uploaded successfully!', 'success');

                // Refresh the artworks grid
                const grid = document.getElementById('artworks-grid');
                const accountId = parseInt(grid?.getAttribute('data-account-id'), 10) || 0;
                if (accountId) {
                    setTimeout(() => {
                        loadArtworks(accountId, 1);
                        const modalEl = document.getElementById('uploadArtworkModal');
                        bootstrap.Modal.getInstance(modalEl)?.hide();
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
            if (spinner) spinner.classList.add('d-none');
            if (confirmBtn) confirmBtn.disabled = false;
        });
}

function setUploadAlert(message, type) {
    const el = document.getElementById('upload-artwork-alert');
    if (!el) return;
    if (!message) {
        el.className = 'alert d-none';
        el.textContent = '';
        return;
    }
    el.className = `alert alert-${type}`;
    el.textContent = message;
}

/* ═══════════════════════════════════════════════════════════════
   FOLLOW / UNFOLLOW
═══════════════════════════════════════════════════════════════ */

function handleFollowToggle(event) {
    const button = event.currentTarget;
    const artistId = parseInt(button.getAttribute('data-artist-id'), 10) || null;
    const userId = parseInt(button.getAttribute('data-user-id'), 10) || null;
    const isFollowing = button.getAttribute('data-following') === '1';

    button.disabled = true;

    fetch(`${window.BASE_URL}api/profile/follow_action.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            artist_id: artistId,
            user_id: userId,
            action: isFollowing ? 'unfollow' : 'follow'
        })
    })
        .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.json();
        })
        .then(result => {
            if (result.success) {
                if (isFollowing) {
                    button.setAttribute('data-following', '0');
                    button.className = 'btn btn-follow';
                    button.innerHTML = '<i class="fas fa-plus me-1"></i> Follow';
                } else {
                    button.setAttribute('data-following', '1');
                    button.className = 'btn btn-success';
                    button.innerHTML = '<i class="fas fa-check me-1"></i> Following';
                }

                // Update the viewed profile's Followers count
                const followersEl = document.getElementById('stat-followers');
                if (followersEl) {
                    let count = parseInt(followersEl.innerText.replace(/,/g, ''), 10) || 0;
                    count = isFollowing ? Math.max(0, count - 1) : count + 1;
                    followersEl.innerText = count.toLocaleString();
                }

                // Only update Following stat if we're on our own profile page
                if (result.following_count !== undefined &&
                    window.VIEWER_ACCOUNT_ID === window.PROFILE_ACCOUNT_ID) {
                    const followingEl = document.getElementById('stat-following');
                    if (followingEl) {
                        followingEl.innerText = result.following_count.toLocaleString();
                    }
                }
            } else {
                alert(result.message || 'An error occurred.');
            }
        })
        .catch(err => {
            console.error('Follow toggle failed:', err);
            alert('Could not update follow status. Please check your connection.');
        })
        .finally(() => {
            button.disabled = false;
        });
}

/* ═══════════════════════════════════════════════════════════════
   TAB LISTENERS
═══════════════════════════════════════════════════════════════ */

function initTabListeners() {
    document.querySelectorAll('#profileTabs button').forEach(tabEl => {
        tabEl.addEventListener('shown.bs.tab', function (event) {
            const target = event.target.getAttribute('data-bs-target');

            if (target === '#pane-artworks') {
                const grid = document.getElementById('artworks-grid');
                const accountId = parseInt(grid?.getAttribute('data-account-id'), 10) || 0;
                if (accountId) loadArtworks(accountId, artworkCurrentPage);
            }

            if (target === '#pane-reviews') {
                const pane = document.getElementById('pane-reviews');
                if (pane && pane.getAttribute('data-loaded') === 'false') {
                    loadReviews(parseInt(pane.getAttribute('data-account-id'), 10) || 0, 1);
                }
            }
        });
    });
}

/* ═══════════════════════════════════════════════════════════════
   REVIEWS (lazy-loaded when tab opens)
═══════════════════════════════════════════════════════════════ */

function loadReviews(accountId, page) {
    const pane = document.getElementById('pane-reviews');
    const list = document.getElementById('reviews-list');
    const pagination = document.getElementById('reviews-pagination');
    if (!list || !accountId) return;

    list.innerHTML = `
        <div class="text-center py-5 text-muted">
            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
            Loading reviews…
        </div>`;

    const url = `${window.BASE_URL}api/profile/fetch_reviews.php?account_id=${accountId}&page=${page}&per_page=10`;

    fetch(url)
        .then(res => res.json())
        .then(result => {
            if (!result.success || !result.data.length) {
                list.innerHTML = '<p class="text-center text-muted py-4">No reviews yet.</p>';
                return;
            }

            list.innerHTML = result.data.map(r => buildReviewCard(r)).join('');
            if (pane) pane.setAttribute('data-loaded', 'true');

            // Update summary stats
            if (result.avg_rating !== undefined) {
                const avgEl = document.getElementById('review-summary-avg');
                const statAvg = document.getElementById('stat-avg-rating');
                const countEl = document.getElementById('stat-review-count');
                if (avgEl) avgEl.textContent = result.avg_rating || '—';
                if (statAvg) statAvg.innerHTML = `<i class="fas fa-star"></i> ${result.avg_rating || '—'}/5`;
                if (countEl) countEl.textContent = `(${result.total ?? 0})`;
            }

            if (pagination) {
                // Re-use artwork pagination renderer for reviews
                renderArtworkPagination(pagination, result.pages, page, accountId);
                // Override click to call loadReviews instead
                pagination.querySelectorAll('.page-link').forEach(link => {
                    link.addEventListener('click', e => {
                        e.preventDefault();
                        const p = parseInt(link.textContent, 10);
                        if (!isNaN(p)) loadReviews(accountId, p);
                    });
                });
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
                    <strong>${escapeHtml(r.reviewer_username || 'Anonymous')}</strong>
                    <small class="text-muted">${date}</small>
                </div>
                <div class="mb-2">${stars}</div>
                <p class="mb-0">${escapeHtml(r.comment || '')}</p>
            </div>
        </div>`;
}

/* ═══════════════════════════════════════════════════════════════
   AVATAR SYNC (own profile / edit page)
═══════════════════════════════════════════════════════════════ */

function checkForExtendedProfileData() {
    const isEditPage = window.location.pathname.includes('edit.php');
    const isOwnProfileNoFollowBtn = !document.getElementById('btn-follow-action');

    if (!isEditPage && !isOwnProfileNoFollowBtn) return;

    fetch(`${window.BASE_URL}api/profile/fetch.php`)
        .then(res => res.json())
        .then(resData => {
            if (!resData.success || !resData.data) return;

            const avatarContainer = document.querySelector('.profile-avatar-container');
            if (!avatarContainer) return;

            if (resData.data.avatar_url && !resData.data.avatar_url.includes('default-')) {
                avatarContainer.innerHTML = `
                    <img src="${window.BASE_URL}${resData.data.avatar_url}"
                         alt="User avatar"
                         class="profile-avatar"
                         style="width:100%;height:100%;object-fit:cover;">`;
            } else {
                avatarContainer.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                         fill="currentColor" class="profile-avatar"
                         style="width:100%;height:100%;background:#e9ecef;
                                padding:2rem;box-sizing:border-box;">
                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4
                                 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6
                                 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                    </svg>`;
            }
        })
        .catch(err => console.warn('Avatar sync skipped:', err));
}

/* ═══════════════════════════════════════════════════════════════
   MY COMMISSIONS TAB (own user profile)
═══════════════════════════════════════════════════════════════ */

let profileCommissionsLoaded = false;

function initProfileCommissionsTab() {
    const tabBtn = document.getElementById('tab-commissions');
    if (!tabBtn) return;

    tabBtn.addEventListener('shown.bs.tab', () => {
        if (!profileCommissionsLoaded) {
            loadProfileCommissions();
        }
    });

    // If this tab is already active on page load (non-artist profiles),
    // the 'shown.bs.tab' event never fires for it, so load now.
    if (tabBtn.classList.contains('active')) {
        loadProfileCommissions();
    }
}

function loadProfileCommissions() {
    const grid = document.getElementById('profileCommissionGrid');
    const loading = document.getElementById('profileCommissionsLoading');
    const errorEl = document.getElementById('profileCommissionsError');
    const emptyEl = document.getElementById('profileCommissionsEmpty');
    if (!grid) return;

    if (errorEl) errorEl.classList.add('d-none');
    if (emptyEl) emptyEl.classList.add('d-none');
    if (loading) loading.style.display = 'block';

    grid.querySelectorAll('.profile-commission-card-col').forEach(el => el.remove());

    fetch(`${window.BASE_URL}api/commissions/fetch.php`)
        .then(res => res.json())
        .then(result => {
            if (loading) loading.style.display = 'none';
            profileCommissionsLoaded = true;

            if (!result.success) {
                if (errorEl) errorEl.classList.remove('d-none');
                return;
            }

            const commissions = result.data || [];
            if (commissions.length === 0) {
                if (emptyEl) emptyEl.classList.remove('d-none');
                return;
            }

            commissions.forEach(c => {
                const col = document.createElement('div');
                col.className = 'col profile-commission-card-col';
                col.innerHTML = buildProfileCommissionCard(c);
                grid.appendChild(col);
            });
        })
        .catch(err => {
            console.error('Profile commissions fetch error:', err);
            if (loading) loading.style.display = 'none';
            if (errorEl) errorEl.classList.remove('d-none');
        });
}

function getCommissionStatusBadge(statusId) {
    switch (parseInt(statusId)) {
        case 1: return { text: 'Active', class: 'artist-card__badge--open' };
        case 2: return { text: 'Pending', class: 'bg-warning text-dark border border-warning' };
        case 3: return { text: 'Accepted', class: 'theme-fill text-dark border border-info' };
        case 4: return { text: 'Rejected', class: 'artist-card__badge--closed' };
        case 5: return { text: 'In Progress', class: 'theme-fill text-dark border border-primary' };
        case 6: return { text: 'Completed', class: 'theme-fill text-dark border border-success' };
        case 7: return { text: 'Cancelled', class: 'bg-danger text-white border border-secondary' };
        default: return { text: 'Unknown', class: 'bg-dark text-white' };
    }
}

function buildProfileCommissionCard(c) {
    const status = getCommissionStatusBadge(c.status_id);
    const budget = parseFloat(c.price ?? 0);
    const budgetDisplay = budget > 0
        ? `₱${budget.toLocaleString('en-PH', { minimumFractionDigits: 2 })}`
        : 'Open Budget';

    const raw = (c.description ?? '').split('\n\n');
    let title = '';
    let body = raw.join('\n\n');
    if (raw.length >= 2) {
        title = raw[0].trim();
        body = raw.slice(1).join('\n\n').trim();
    }

    const dateStr = c.commission_date
        ? new Date(c.commission_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
        : 'Recent';

    const category = c.category_name
        ? `<span class="badge rounded-pill fs-fluid-xxs fw-semibold text-uppercase mb-2"
                style="background:#e8d5b0; color:#a8834a; border:1px solid #a8834a33; letter-spacing:0.04em;">
                ${escapeHtml(c.category_name)}
           </span>`
        : '';

    return `
        <div class="artist-card h-100 border rounded-3 d-flex flex-column shadow-sm bg-card p-3">
            <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                <small class="text-muted fs-fluid-xxs">${dateStr}</small>
                <span class="artist-card__badge d-inline-flex flex-shrink-0 align-items-center fw-bold text-uppercase ${status.class}">
                    ${status.text}
                </span>
            </div>
            <div class="flex-grow-1 mb-3">
                ${category}
                ${title ? `<p class="m-0 fw-semibold fs-fluid-xs mb-1 text-truncate">${escapeHtml(title)}</p>` : ''}
                <p class="text-muted small m-0" style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;line-height:1.6;">
                    ${escapeHtml(body)}
                </p>
            </div>
            <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                <div>
                    <p class="m-0 text-muted fs-fluid-xxs text-uppercase" style="letter-spacing:0.05em;">Budget</p>
                    <p class="m-0 fw-bold fs-fluid-sm">${budgetDisplay}</p>
                </div>
                <a href="${window.BASE_URL}commissions/manage?id=${c.commission_id}" class="btn-artovia-outline py-1 px-3 fs-fluid-xs rounded-2">Manage</a>
            </div>
        </div>`;
}

/* ═══════════════════════════════════════════════════════════════
   POST COMMISSION MODAL (own user profile)
═══════════════════════════════════════════════════════════════ */

function initPostCommissionModal() {
    const submitBtn = document.getElementById('submitCommissionBtn');
    const titleInput = document.getElementById('commissionTitle');
    const descInput = document.getElementById('commissionDescription');
    const budgetInput = document.getElementById('commissionBudget');
    const categoryInput = document.getElementById('commissionCategory');
    const imageFile = document.getElementById('commissionImageFile');
    const imageName = document.getElementById('commissionImageName');
    const formAlert = document.getElementById('commissionFormAlert');

    if (imageFile) {
        imageFile.addEventListener('change', () => {
            if (imageName) imageName.textContent = imageFile.files[0]?.name ?? '';
        });
    }

    function showModalAlert(message, isSuccess) {
        if (!formAlert) return;
        formAlert.textContent = message;
        formAlert.className = `alert fs-fluid-xs ${isSuccess ? 'alert-success' : 'alert-danger'}`;
        formAlert.classList.remove('d-none');
    }

    function resetModal() {
        if (titleInput) titleInput.value = '';
        if (descInput) descInput.value = '';
        if (budgetInput) budgetInput.value = '';
        if (categoryInput) categoryInput.value = '';
        if (imageFile) imageFile.value = '';
        if (imageName) imageName.textContent = '';
        if (formAlert) formAlert.classList.add('d-none');
    }

    if (submitBtn) {
        submitBtn.addEventListener('click', async () => {
            if (formAlert) formAlert.classList.add('d-none');

            const title = titleInput?.value.trim() ?? '';
            const description = descInput?.value.trim() ?? '';
            const budget = parseFloat(budgetInput?.value ?? 0);
            const category_id = parseInt(categoryInput?.value ?? 0);

            if (!title) { showModalAlert('Please provide a commission name.', false); return; }
            if (!description) { showModalAlert('Please provide a project description.', false); return; }
            if (isNaN(budget) || budget <= 0) { showModalAlert('Please enter a valid budget higher than ₱0.', false); return; }
            if (!category_id) { showModalAlert('Please select a category.', false); return; }

            submitBtn.disabled = true;
            submitBtn.textContent = 'Posting…';

            try {
                const res = await fetch(`${window.BASE_URL}api/commissions/create.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ title, description, budget, category_id })
                });
                const data = await res.json();

                if (data && data.success) {
                    showModalAlert(data.message, true);
                    setTimeout(() => {
                        if (typeof bootstrap !== 'undefined') {
                            bootstrap.Modal.getInstance(document.getElementById('postCommissionModal'))?.hide();
                        }
                        profileCommissionsLoaded = false;
                        loadProfileCommissions();
                    }, 1500);
                } else {
                    showModalAlert(data?.message || 'Something went wrong.', false);
                }
            } catch {
                showModalAlert('Network error — please try again.', false);
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Post Commission';
            }
        });
    }

    document.getElementById('postCommissionModal')?.addEventListener('hidden.bs.modal', resetModal);
}

/* ═══════════════════════════════════════════════════════════════
   UTILITY
═══════════════════════════════════════════════════════════════ */

function escapeHtml(str) {
    if (str == null) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}