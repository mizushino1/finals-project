(function () {
    // ── Config ────────────────────────────────────────────────────────────────
    const API = window.location.origin + '/finals-project/api/messages/fetch.php';

    // ── DOM references ────────────────────────────────────────────────────────
    const threadList = document.getElementById('threadList');
    const searchInput = document.getElementById('inboxSearch');
    const inboxSidebarPanel = document.getElementById('inboxSidebarPanel');
    const conversationContainer = document.getElementById('conversationContainer');

    let allConversations = [];
    let selectedContactId = null;

    // ── Helpers ───────────────────────────────────────────────────────────────
    function escapeHtml(str) {
        const d = document.createElement('div');
        d.textContent = str ?? '';
        return d.innerHTML;
    }

    const AVATAR_PALETTE = [
        ['#e8d5b0', '#a8834a'],
        ['#d4e8d0', '#3a7a4a'],
        ['#d0dce8', '#3a5a7a'],
        ['#e8d0e0', '#7a3a5a'],
        ['#e0e8d0', '#5a7a3a'],
    ];

    function buildAvatarEl(name, avatarUrl, index) {
        const letter = (name.trim().split(/[\s_]+/)[0]?.[0] ?? '?').toUpperCase();
        const [bg, fg] = AVATAR_PALETTE[index % AVATAR_PALETTE.length];

        const wrap = document.createElement('div');
        wrap.style.cssText = 'width:2.25rem;height:2.25rem;border-radius:50%;flex-shrink:0;overflow:hidden;';

        if (avatarUrl) {
            const img = document.createElement('img');
            img.src = (window.BASE_URL ?? '') + avatarUrl;
            img.alt = name;
            img.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block;';
            img.addEventListener('error', () => {
                wrap.innerHTML = '';
                wrap.style.background = bg;
                wrap.style.display = 'flex';
                wrap.style.alignItems = 'center';
                wrap.style.justifyContent = 'center';
                wrap.style.fontWeight = '700';
                wrap.style.fontSize = '0.9rem';
                wrap.style.color = fg;
                wrap.textContent = letter;
            });
            wrap.appendChild(img);
        } else {
            wrap.style.background = bg;
            wrap.style.display = 'flex';
            wrap.style.alignItems = 'center';
            wrap.style.justifyContent = 'center';
            wrap.style.fontWeight = '700';
            wrap.style.fontSize = '0.9rem';
            wrap.style.color = fg;
            wrap.textContent = letter;
        }

        return wrap;
    }

    // ── Render thread items ───────────────────────────────────────────────────
    function renderThreads(list) {
        if (!list.length) {
            threadList.innerHTML = '<div class="text-center p-4 text-muted fs-fluid-xs">No conversations yet.</div>';
            return;
        }

        threadList.innerHTML = '';

        list.forEach((c, index) => {
            const unread = parseInt(c.unread_count, 10) || 0;
            const isActive = selectedContactId === parseInt(c.contact_id, 10);

            const item = document.createElement('div');
            item.className = 'thread-item p-3 border-bottom cp transition-all';
            item.dataset.id = c.contact_id;
            item.dataset.name = c.contact_name;
            if (isActive) item.style.background = 'var(--clr-surface)';

            const row = document.createElement('div');
            row.className = 'd-flex align-items-center gap-3';

            row.appendChild(buildAvatarEl(c.contact_name, c.avatar_url ?? null, index));

            const textWrap = document.createElement('div');
            textWrap.className = 'flex-grow-1 min-w-0';

            const nameRow = document.createElement('div');
            nameRow.className = 'd-flex align-items-center justify-content-between mb-1';

            const nameEl = document.createElement('h6');
            nameEl.className = 'mb-0 text-truncate fs-fluid-xs text-capitalize fw-semibold';
            nameEl.textContent = c.contact_name;
            nameRow.appendChild(nameEl);

            if (unread > 0) {
                const badge = document.createElement('span');
                badge.className = 'badge rounded-pill ms-auto';
                badge.style.cssText = 'background:var(--clr-gold);color:#fff;';
                badge.textContent = unread;
                nameRow.appendChild(badge);
            }

            const preview = document.createElement('p');
            preview.className = 'mb-0 fs-fluid-xs text-muted text-truncate';
            preview.textContent = c.last_message;

            textWrap.appendChild(nameRow);
            textWrap.appendChild(preview);
            row.appendChild(textWrap);
            item.appendChild(row);

            item.addEventListener('click', () => {
                selectedContactId = parseInt(c.contact_id, 10);
                renderThreads(list);

                if (window.innerWidth < 992) {
                    if (inboxSidebarPanel) inboxSidebarPanel.classList.remove('active-mobile-view');
                    if (conversationContainer) conversationContainer.classList.add('active-mobile-view');
                }

                document.dispatchEvent(new CustomEvent('artovia:threadChanged', {
                    detail: {
                        id: parseInt(c.contact_id, 10),
                        name: c.contact_name,
                        contactAvatarUrl: c.avatar_url ?? null,
                        myAvatarUrl: window.MY_AVATAR_URL ?? null,
                    }
                }));
            });

            threadList.appendChild(item);
        });
    }

    // ── Search Filters ────────────────────────────────────────────────────────
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const q = searchInput.value.trim().toLowerCase();
            const filtered = q
                ? allConversations.filter(c => c.contact_name.toLowerCase().includes(q))
                : allConversations;
            renderThreads(filtered);
        });
    }

    // ── Load conversations ────────────────────────────────────────────────────
    async function loadConversations() {
        try {
            const res = await fetch(`${API}?action=conversations`);
            if (!res.ok) return;

            const json = await res.json();
            if (json.success) {
                allConversations = json.data ?? [];
                renderThreads(allConversations);
            }
        } catch (err) {
            console.error('Thread listing loading failure: ', err);
        }
    }

    // ── Fetch current user's own avatar once on init ──────────────────────────
    async function loadMyAvatar() {
        try {
            const res = await fetch(`${window.BASE_URL}api/profile/fetch_avatar.php`);
            if (!res.ok) return;
            const json = await res.json();
            if (json.success && json.avatar_url) {
                window.MY_AVATAR_URL = json.avatar_url;
            }
        } catch (_) { /* non-critical, falls back to icon */ }
    }

    // ── Initialization Logic & Query String Router ────────────────────────────
    Promise.all([loadConversations(), loadMyAvatar()]).then(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const targetId = parseInt(urlParams.get('target_id'), 10);
        const targetName = urlParams.get('name');

        if (targetId && targetName) {
            selectedContactId = targetId;
            renderThreads(allConversations);

            if (window.innerWidth < 992) {
                if (inboxSidebarPanel) inboxSidebarPanel.classList.remove('active-mobile-view');
                if (conversationContainer) conversationContainer.classList.add('active-mobile-view');
            }

            const matched = allConversations.find(c => parseInt(c.contact_id, 10) === targetId);
            document.dispatchEvent(new CustomEvent('artovia:threadChanged', {
                detail: {
                    id: targetId,
                    name: targetName,
                    contactAvatarUrl: matched?.avatar_url ?? null,
                    myAvatarUrl: window.MY_AVATAR_URL ?? null,
                }
            }));

            const cleanUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
            window.history.replaceState({ path: cleanUrl }, '', cleanUrl);
        }
    });

    setInterval(loadConversations, 6000);
    document.addEventListener('artovia:refreshThreads', loadConversations);
})();