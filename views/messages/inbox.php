<main class="py-0">
    <div class="container-fluid px-0">
        <div class="d-flex" style="height: 100vh; background: var(--clr-bg-card); overflow: hidden;">

            <!-- ── Sidebar: Conversation List ──────────────────────────────── -->
            <div class="d-flex flex-column" style="width: 350px; border-right: 2px solid var(--clr-gold);">
                <div class="p-3" style="border-bottom: 2px solid var(--clr-gold);">
                    <h3 class="mb-3">Inbox</h3>
                    <div class="input-group mb-2">
                        <span class="input-group-text bg-white border-end-0 theme-border"
                              style="border-right: none !important;">
                            <i class="bi bi-search" style="color: var(--clr-text-muted);"></i>
                        </span>
                        <input id="inboxSearch" type="text"
                               class="form-control border-start-0 theme-border"
                               placeholder="Search messages…"
                               style="border-left: none !important;">
                    </div>
                </div>

                <!-- Thread list populated by JS -->
                <div id="threadList" class="flex-grow-1 overflow-y-auto hide-scrollbar">
                    <div class="text-center p-4 text-muted fs-fluid-xs" id="threadPlaceholder">
                        Loading conversations…
                    </div>
                </div>
            </div>

            <!-- ── Empty / Welcome State ───────────────────────────────────── -->
            <div class="flex-grow-1 d-flex align-items-center justify-content-center"
                 style="background: linear-gradient(to bottom, var(--clr-bg), var(--clr-surface));">
                <div class="text-center p-4">
                    <i class="bi bi-chat-dots" style="font-size: 4rem; color: var(--clr-gold);"></i>
                    <h4 class="mt-3 joan" style="color: var(--clr-text-secondary);">Your Inbox</h4>
                    <p class="info-desc mx-auto" style="max-width: 300px;">
                        Select a conversation from the sidebar to view messages or start a new dialogue.
                    </div>
                </div>

                <div class="p-3 chat-conversation-footer">
                    <div class="input-group">
                        <input id="msgInput" type="text" class="form-control theme-border chat-footer-input" placeholder="Type a message…" autocomplete="off" disabled>
                        <button id="sendBtn" class="btn btn-artovia-primary" disabled>
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                    <div id="sendError" class="text-danger fs-fluid-xs mt-1" style="display:none;"></div>
                </div>

            </div>

        </div>
    </div>
</main>

<script>
(function () {
    // ── Config ────────────────────────────────────────────────────────────────
    // Use an absolute path from root so this works regardless of the current URL
    const API = window.location.origin + '/finals-project/messages/fetch.php';

    // ── DOM refs ──────────────────────────────────────────────────────────────
    const threadList    = document.getElementById('threadList');
    const searchInput   = document.getElementById('inboxSearch');
    const placeholder   = document.getElementById('threadPlaceholder');

    let allConversations = [];

    // ── Helpers ───────────────────────────────────────────────────────────────
    function avatarIcon(size = '2rem') {
        return `<i class="bi bi-person-circle" style="font-size:${size}; color:var(--clr-gold); flex-shrink:0;"></i>`;
    }

    function escapeHtml(str) {
        const d = document.createElement('div');
        d.textContent = str ?? '';
        return d.innerHTML;
    }

    function truncate(str, n = 35) {
        if (!str) return '';
        return str.length > n ? str.slice(0, n) + '…' : str;
    }

    // ── Render thread items ───────────────────────────────────────────────────
    function renderThreads(list) {
        if (!list.length) {
            threadList.innerHTML = '<div class="text-center p-4 text-muted fs-fluid-xs">No conversations yet.</div>';
            return;
        }

        threadList.innerHTML = list.map((c, i) => {
            const unread   = parseInt(c.unread_count, 10) || 0;
            const badge    = unread > 0
                ? `<span class="badge rounded-pill ms-auto" style="background:var(--clr-gold);color:#fff;">${unread}</span>`
                : '';
            const bg       = i === 0 ? 'var(--clr-bg-card)' : 'var(--clr-bg-alt)';
            const boldName = unread > 0 ? 'fw-bold' : '';

            return `
            <div class="thread-item p-3"
                 style="border-bottom:1px solid var(--clr-border);cursor:pointer;background:${bg};"
                 data-id="${c.contact_id}"
                 data-name="${escapeHtml(c.contact_name)}">
                <div class="d-flex align-items-center gap-3">
                    ${avatarIcon('2rem')}
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0 ${boldName} text-truncate">${escapeHtml(c.contact_name)}</h6>
                            ${badge}
                        </div>
                        <p class="mb-0 fs-fluid-xs text-muted text-truncate">
                            ${escapeHtml(truncate(c.last_message))}
                        </p>
                    </div>
                </div>
            </div>`;
        }).join('');

        // Click → navigate to conversation
        threadList.querySelectorAll('.thread-item').forEach(el => {
            el.addEventListener('click', () => {
                const id   = el.dataset.id;
                const name = el.dataset.name;
                window.location.href = `conversation.php?target_id=${id}&name=${encodeURIComponent(name)}`;
            });
        });
    }

    // ── Search filter ─────────────────────────────────────────────────────────
    searchInput.addEventListener('input', () => {
        const q = searchInput.value.trim().toLowerCase();
        const filtered = q
            ? allConversations.filter(c => c.contact_name.toLowerCase().includes(q))
            : allConversations;
        renderThreads(filtered);
    });

    // ── Load conversations ────────────────────────────────────────────────────
    async function loadConversations() {
        try {
            const res  = await fetch(`${API}?action=conversations`);

            // Show HTTP-level errors (401, 403, 500, etc.)
            if (!res.ok) {
                const text = await res.text();
                let msg = `Server error ${res.status}`;
                try { msg = JSON.parse(text).message || msg; } catch {}
                threadList.innerHTML = `<div class="text-center p-4 text-danger fs-fluid-xs">${escapeHtml(msg)}</div>`;
                console.error('Conversations HTTP error:', res.status, text);
                return;
            }

            const json = await res.json();

            if (!json.success) {
                threadList.innerHTML = `<div class="text-center p-4 text-danger fs-fluid-xs">${escapeHtml(json.message)}</div>`;
                return;
            }

            allConversations = json.data ?? [];
            renderThreads(allConversations);
        } catch (err) {
            threadList.innerHTML = `<div class="text-center p-4 text-danger fs-fluid-xs">Failed to load conversations: ${escapeHtml(err.message)}</div>`;
            console.error('Inbox load error:', err);
        }
    }

    loadConversations();
    // Refresh sidebar every 15 s for new unread counts
    setInterval(loadConversations, 15000);
})();
</script>