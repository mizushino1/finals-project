<?php
// conversation.php
// Pass target info via query string: ?target_id=X&name=DisplayName
$targetId   = isset($_GET['target_id']) ? (int) $_GET['target_id'] : 0;
$targetName = htmlspecialchars($_GET['name'] ?? 'User', ENT_QUOTES, 'UTF-8');
?>
<main class="py-4">
    <div class="container-fluid px-4">
        <div class="theme-border d-flex" style="height: 600px; background: var(--clr-bg-card); overflow: hidden;">

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

                <div id="threadList" class="flex-grow-1 overflow-y-auto hide-scrollbar">
                    <div class="text-center p-4 text-muted fs-fluid-xs">Loading…</div>
                </div>
            </div>

            <div class="flex-grow-1 d-flex flex-column"
                 style="background: linear-gradient(to bottom, var(--clr-bg), var(--clr-surface));">

                <div class="p-3 d-flex align-items-center justify-content-between"
                     style="border-bottom: 2px solid var(--clr-gold);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-person-circle" style="font-size:1.5rem; color:var(--clr-gold);"></i>
                        <h5 class="mb-0" id="chatHeader"><?= $targetName ?></h5>
                    </div>
                    <i class="bi bi-three-dots-vertical" style="font-size:1.25rem; cursor:pointer;"></i>
                </div>

                <div id="messagePane"
                     class="flex-grow-1 p-4 overflow-y-auto hide-scrollbar d-flex flex-column gap-3">
                    <div class="text-center text-muted fs-fluid-xs" id="msgPlaceholder">
                        Loading messages…
                    </div>
                </div>

                <div class="p-3" style="border-top: 2px solid var(--clr-gold);">
                    <div class="input-group">
                        <input id="msgInput" type="text"
                               class="form-control theme-border"
                               placeholder="Type a message…"
                               style="border-width:1px !important;"
                               autocomplete="off">
                        <button id="sendBtn" class="btn btn-artovia-primary">
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
    const API       = window.location.origin + '/finals-project/messages/fetch.php';
    const TARGET_ID = <?= $targetId ?>;
    const POLL_MS   = 4000; // refresh messages every 4 s

    // ── DOM refs ──────────────────────────────────────────────────────────────
    const threadList  = document.getElementById('threadList');
    const searchInput = document.getElementById('inboxSearch');
    const messagePane = document.getElementById('messagePane');
    const msgInput    = document.getElementById('msgInput');
    const sendBtn     = document.getElementById('sendBtn');
    const sendError   = document.getElementById('sendError');
    const msgPlaceholder = document.getElementById('msgPlaceholder');

    // ── State ─────────────────────────────────────────────────────────────────
    let allConversations = [];
    let knownIds         = new Set();   // message IDs already in the DOM
    let activeThreadId   = TARGET_ID;
    let pollTimer        = null;

    // ── Utilities ─────────────────────────────────────────────────────────────
    function escapeHtml(str) {
        const d = document.createElement('div');
        d.textContent = str ?? '';
        return d.innerHTML;
    }
    function truncate(str, n = 35) {
        return str && str.length > n ? str.slice(0, n) + '…' : (str ?? '');
    }
    function scrollToBottom() {
        messagePane.scrollTop = messagePane.scrollHeight;
    }

    // ── Sidebar render ────────────────────────────────────────────────────────
    function renderThreads(list) {
        if (!list.length) {
            threadList.innerHTML = '<div class="p-4 text-muted fs-fluid-xs text-center">No conversations yet.</div>';
            return;
        }
        threadList.innerHTML = list.map(c => {
            const unread  = parseInt(c.unread_count, 10) || 0;
            const badge   = unread > 0
                ? `<span class="badge rounded-pill ms-auto" style="background:var(--clr-gold);color:#fff;">${unread}</span>`
                : '';
            const isActive = (parseInt(c.contact_id, 10) === activeThreadId);
            const bg       = isActive ? 'var(--clr-bg-alt)' : 'var(--clr-bg-card)';
            const bold     = unread > 0 ? 'fw-bold' : '';

            return `
            <div class="thread-item p-3"
                 style="border-bottom:1px solid var(--clr-border);cursor:pointer;background:${bg};"
                 data-id="${c.contact_id}"
                 data-name="${escapeHtml(c.contact_name)}">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-person-circle" style="font-size:2rem;color:var(--clr-gold);flex-shrink:0;"></i>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0 ${bold} text-truncate">${escapeHtml(c.contact_name)}</h6>
                            ${badge}
                        </div>
                        <p class="mb-0 fs-fluid-xs text-muted text-truncate">
                            ${escapeHtml(truncate(c.last_message))}
                        </p>
                    </div>
                </div>
            </div>`;
        }).join('');

        threadList.querySelectorAll('.thread-item').forEach(el => {
            el.addEventListener('click', () => {
                const id   = parseInt(el.dataset.id, 10);
                const name = el.dataset.name;
                if (id === activeThreadId) return;
                // Navigate to the same page with new target
                window.location.href = `conversation.php?target_id=${id}&name=${encodeURIComponent(name)}`;
            });
        });
    }

    searchInput.addEventListener('input', () => {
        const q = searchInput.value.trim().toLowerCase();
        renderThreads(q ? allConversations.filter(c => c.contact_name.toLowerCase().includes(q)) : allConversations);
    });

    async function loadConversations() {
        try {
            const res  = await fetch(`${API}?action=conversations`);
            if (!res.ok) return; // sidebar is non-critical; fail silently
            const json = await res.json();
            allConversations = json.data ?? [];
            renderThreads(allConversations);
        } catch (e) {
            console.warn('Sidebar load error:', e);
        }
    }

    // ── Message rendering ─────────────────────────────────────────────────────
    const CURRENT_USER_ID = window.CURRENT_USER_ID ?? 0;

    function buildBubble(msg) {
        const isMine = parseInt(msg.sender_id, 10) === parseInt(CURRENT_USER_ID, 10);
        const text   = escapeHtml(msg.body);

        if (isMine) {
            return `
            <div class="d-flex align-items-start justify-content-end gap-2" data-msg-id="${msg.message_id}">
                <div class="p-3 text-white"
                     style="border-radius:var(--radius-md);background:var(--clr-gold);max-width:60%;word-break:break-word;">
                    ${text}
                </div>
                <i class="bi bi-person-circle" style="font-size:1.2rem;color:var(--clr-gold);flex-shrink:0;"></i>
            </div>`;
        }

        return `
        <div class="d-flex align-items-start gap-2" data-msg-id="${msg.message_id}">
            <i class="bi bi-person-circle" style="font-size:1.2rem;color:var(--clr-text-muted);flex-shrink:0;"></i>
            <div class="p-3 theme-border"
                 style="border-width:1px !important;border-radius:var(--radius-md);background:var(--clr-bg-card);max-width:60%;word-break:break-word;">
                ${text}
            </div>
        </div>`;
    }

    function appendMessages(messages) {
        let appended = false;
        messages.forEach(msg => {
            if (knownIds.has(msg.message_id)) return;
            knownIds.add(msg.message_id);
            if (msgPlaceholder) msgPlaceholder.remove();
            const div = document.createElement('div');
            div.innerHTML = buildBubble(msg);
            messagePane.appendChild(div.firstElementChild);
            appended = true;
        });
        if (appended) scrollToBottom();
    }

    // ── Fetch messages (polling) ───────────────────────────────────────────────
    async function loadMessages() {
        if (!activeThreadId) return;
        try {
            const res  = await fetch(`${API}?action=messages&target_id=${activeThreadId}`);
            const json = await res.json();
            if (json.success) appendMessages(json.data ?? []);
        } catch (e) {
            console.warn('Poll error:', e);
        }
    }

    function startPolling() {
        loadMessages();
        pollTimer = setInterval(loadMessages, POLL_MS);
    }

    // ── Send ──────────────────────────────────────────────────────────────────
    async function sendMessage() {
        const body = msgInput.value.trim();
        if (!body || !activeThreadId) return;

        sendBtn.disabled = true;
        sendError.style.display = 'none';

        try {
            const res  = await fetch(`${API}?action=send`, {
                method  : 'POST',
                headers : { 'Content-Type': 'application/json' },
                body    : JSON.stringify({ target_id: activeThreadId, body }),
            });
            const json = await res.json();

            if (json.success) {
                msgInput.value = '';
                appendMessages([json.data]);
                loadConversations();
            } else {
                sendError.textContent = json.message ?? 'Could not send message.';
                sendError.style.display = 'block';
            }
        } catch (e) {
            sendError.textContent = 'Network error. Please try again.';
            sendError.style.display = 'block';
            console.error('Send error:', e);
        } finally {
            sendBtn.disabled = false;
            msgInput.focus();
        }
    }

    sendBtn.addEventListener('click', sendMessage);
    msgInput.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // ── Init ──────────────────────────────────────────────────────────────────
    if (!TARGET_ID) {
        messagePane.innerHTML = '<div class="text-center text-muted p-4">No conversation selected.</div>';
    } else {
        loadConversations();
        startPolling();
        setInterval(loadConversations, 15000);
    }
})();
</script>