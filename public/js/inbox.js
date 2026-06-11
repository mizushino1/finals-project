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

    // ── Render thread items ───────────────────────────────────────────────────
    function renderThreads(list) {
        if (!list.length) {
            threadList.innerHTML = '<div class="text-center p-4 text-muted fs-fluid-xs">No conversations yet.</div>';
            return;
        }

        threadList.innerHTML = list.map((c) => {
            const unread = parseInt(c.unread_count, 10) || 0;
            const badge = unread > 0
                ? `<span class="badge rounded-pill ms-auto" style="background:var(--clr-gold);color:#fff;">${unread}</span>`
                : '';

            const isActive = (selectedContactId === parseInt(c.contact_id, 10)) ? 'style="background: var(--clr-surface);"' : '';

            return `
        <div class="thread-item p-3 border-bottom cp transition-all" data-id="${c.contact_id}" data-name="${escapeHtml(c.contact_name)}" ${isActive}>
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-person-circle" style="font-size:2rem; color:var(--clr-gold); flex-shrink:0;"></i>
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <h6 class="mb-0 text-truncate fs-fluid-xs text-capitalize fw-semibold">${escapeHtml(c.contact_name)}</h6>
                        ${badge}
                    </div>
                    <p class="mb-0 fs-fluid-xs text-muted text-truncate">
                        ${escapeHtml(c.last_message)}
                    </p>
                </div>
            </div>
        </div>`;
        }).join('');

        // ATTACH CLICK HANDLERS TO REVEAL THE CONVERSATION
        threadList.querySelectorAll('.thread-item').forEach(el => {
            el.addEventListener('click', () => {
                const targetId = parseInt(el.dataset.id, 10);
                const targetName = el.dataset.name;

                selectedContactId = targetId;

                // Re-render sidebar to apply active item tracking color configurations
                renderThreads(list);

                // RESPONSIVE LAYOUT SWITCH: Slide conversation window into view on mobile viewport sizes
                if (window.innerWidth < 992) {
                    if (inboxSidebarPanel) inboxSidebarPanel.classList.remove('active-mobile-view');
                    if (conversationContainer) conversationContainer.classList.add('active-mobile-view');
                }

                // Dispatch custom event payload module hooks
                const threadChangeEvent = new CustomEvent('artovia:threadChanged', {
                    detail: { id: targetId, name: targetName }
                });
                document.dispatchEvent(threadChangeEvent);
            });
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

    // ── Load conversations ───────────────────────────────────────────────
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

    // ── Initialization Logic & Query String Router ───────────────────────
    loadConversations().then(() => {
        // AUTOMATIC QUERY STRING CAPTURE DETECTOR:
        const urlParams = new URLSearchParams(window.location.search);
        const targetId = parseInt(urlParams.get('target_id'), 10);
        const targetName = urlParams.get('name');

        if (targetId && targetName) {
            // Track state alignment 
            selectedContactId = targetId;

            // Re-render sidebar immediately to apply selection highlighting styling
            renderThreads(allConversations);

            // Open view pane layouts cleanly if they are operating on mobile window view boundaries
            if (window.innerWidth < 992) {
                if (inboxSidebarPanel) inboxSidebarPanel.classList.remove('active-mobile-view');
                if (conversationContainer) conversationContainer.classList.add('active-mobile-view');
            }

            // Broadcast downstream message retrieval events to initialize the thread viewport container wrapper
            const threadChangeEvent = new CustomEvent('artovia:threadChanged', {
                detail: { id: targetId, name: targetName }
            });
            document.dispatchEvent(threadChangeEvent);

            // Clean up parameters out of the address bar to prevent recursive reload cycling behavior
            const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({ path: cleanUrl }, '', cleanUrl);
        }
    });

    // Initialize regular polling routines
    setInterval(loadConversations, 6000);

    // Listen for updates from the conversation panel to refresh the sidebar values
    document.addEventListener('artovia:refreshThreads', loadConversations);
})();