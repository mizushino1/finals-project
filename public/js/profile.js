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
    const grid         = document.getElementById('artworks-grid');
    const paginationEl = document.getElementById('artworks-pagination');
    const loading      = document.getElementById('artworks-loading');

    if (!grid) return;

    artworkCurrentPage = page;

    if (loading) loading.style.display = 'block';

    grid.querySelectorAll('.artwork-card-col, .artworks-empty').forEach(el => el.remove());

    const perPage = 12;
    const url     = `${window.BASE_URL}api/profile/fetch_artworks.php?account_id=${accountId}&page=${page}&per_page=${perPage}`;

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

    return `
        <div class="card h-100 artwork-card border-0 shadow-sm"
             style="cursor:pointer;"
             data-artwork='${JSON.stringify(artwork).replace(/'/g, "&#39;")}'>
            <div class="artwork-card-img-wrap"
                 style="aspect-ratio:1/1;overflow:hidden;background:#f0f0f0;">
                <img src="${imgSrc}"
                     alt="${title}"
                     loading="lazy"
                     class="card-img-top"
                     style="width:100%;height:100%;object-fit:cover;"
                     onerror="this.src='${window.BASE_URL}public/img/placeholder-artwork.png'">
            </div>
            <div class="card-body p-2">
                <p class="mb-0 small fw-semibold text-truncate" title="${title}">${title}</p>
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
    const isOwnProfile = !!document.querySelector('a[href*="edit.php"]');
    if (!isOwnProfile) return;

    // Insert "Upload Artwork" button next to "Edit Account Settings"
    const editBtn = document.querySelector('a[href*="edit.php"]');
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
            <div class="modal fade" id="artworkViewModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold" id="artworkViewTitle"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <img id="artworkViewImg" src="" alt=""
                                 class="img-fluid rounded mb-3 d-block mx-auto"
                                 style="max-height:480px;object-fit:contain;width:100%;">
                            <p id="artworkViewDesc" class="text-muted mb-2"></p>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i>
                                    <span id="artworkViewArtist"></span>
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    <span id="artworkViewDate"></span>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`);
    }

    // Populate fields
    const title    = artwork.title       || 'Untitled';
    const desc     = artwork.description || 'No description provided.';
    const artist   = artwork.username    || 'Unknown Artist';
    const date     = artwork.uploaded_at
        ? new Date(artwork.uploaded_at).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' })
        : 'Unknown date';
    const imgSrc   = window.BASE_URL + artwork.image_url;

    document.getElementById('artworkViewTitle').textContent  = title;
    document.getElementById('artworkViewImg').src            = imgSrc;
    document.getElementById('artworkViewImg').alt            = title;
    document.getElementById('artworkViewDesc').textContent   = desc;
    document.getElementById('artworkViewArtist').textContent = artist;
    document.getElementById('artworkViewDate').textContent   = date;

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