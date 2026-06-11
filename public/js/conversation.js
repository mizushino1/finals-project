(function () {
    // ── Config ────────────────────────────────────────────────────────────────
    const FETCH_API = window.location.origin + '/finals-project/api/messages/fetch.php';
    const SEND_API  = window.location.origin + '/finals-project/api/messages/send.php';
    const POLL_MS   = 3000; // Poll for updates every 3 seconds

    // ── DOM References ────────────────────────────────────────────────────────
    const chatHeader            = document.getElementById('chatHeader');
    const messagePane           = document.getElementById('messagePane');
    const msgInput              = document.getElementById('msgInput');
    const sendBtn               = document.getElementById('sendBtn');
    const sendError             = document.getElementById('sendError');
    const mobileChatBackButton  = document.getElementById('mobileChatBackButton');
    const inboxSidebarPanel     = document.getElementById('inboxSidebarPanel');
    const conversationContainer = document.getElementById('conversationContainer');

    // ── State Variables ───────────────────────────────────────────────────────
    let activeTargetAccountId = null; 
    let loadedMessageIds     = new Set();
    let pollTimer            = null;

    // ── Helpers ───────────────────────────────────────────────────────────────
    function escapeHtml(str) {
        const d = document.createElement('div');
        d.textContent = str ?? '';
        return d.innerHTML;
    }

    function scrollToBottom() {
        if (messagePane) {
            messagePane.scrollTop = messagePane.scrollHeight;
        }
    }

    // ── HTML Message Bubble Builder ───────────────────────────────────────────
    function buildBubble(msg) {
        const userAccountId = window.CURRENT_ACCOUNT_ID ?? 0;
        const isMine = parseInt(msg.sender_account_id, 10) === parseInt(userAccountId, 10);
        const text   = escapeHtml(msg.message_content);

        if (isMine) {
            return `
            <div class="d-flex align-items-start justify-content-end gap-2" data-msg-id="${msg.message_id}">
                <div class="p-3 text-white" style="border-radius:var(--radius-md); background:var(--clr-gold); max-width:75%; word-break:break-word;">
                    ${text}
                </div>
                <i class="bi bi-person-circle" style="font-size:1.2rem; color:var(--clr-gold); flex-shrink:0;"></i>
            </div>`;
        }

        return `
        <div class="d-flex align-items-start gap-2" data-msg-id="${msg.message_id}">
            <i class="bi bi-person-circle" style="font-size:1.2rem; color:var(--clr-text-muted); flex-shrink:0;"></i>
            <div class="p-3 theme-border" style="border-width:1px !important; border-radius:var(--radius-md); background:var(--clr-bg-card); max-width:75%; word-break:break-word;">
                ${text}
            </div>
        </div>`;
    }

    function appendMessages(messages) {
        if (!messagePane) return;
        let hasNewContent = false;
        
        messages.forEach(msg => {
            const msgId = msg.message_id;
            if (!msgId || loadedMessageIds.has(msgId)) return;
            loadedMessageIds.add(msgId);

            const placeholder = document.getElementById('msgPlaceholder');
            if (placeholder) placeholder.remove();

            const containerDiv = document.createElement('div');
            containerDiv.innerHTML = buildBubble(msg);
            messagePane.appendChild(containerDiv.firstElementChild);
            hasNewContent = true;
        });

        if (hasNewContent) {
            scrollToBottom();
        }
    }

    // ── Message Data Polling ─────────────────────────────────────────────────
    async function loadMessages() {
        if (!activeTargetAccountId) return;
        try {
            const res = await fetch(`${FETCH_API}?action=messages&target_id=${activeTargetAccountId}`);
            if (!res.ok) return;

            const json = await res.json();
            if (json.success && Array.isArray(json.data)) {
                if (json.data.length === 0) {
                    const placeholder = document.getElementById('msgPlaceholder');
                    if (placeholder) {
                        placeholder.innerHTML = '<div class="text-center text-muted p-4 fs-fluid-xs">No messages yet. Send a wave!</div>';
                    }
                    return;
                }
                appendMessages(json.data);
            }
        } catch (e) {
            console.warn('Failed polling active chat records:', e);
        }
    }

    function switchConversation(targetAccountId, targetName) {
        clearInterval(pollTimer);
        activeTargetAccountId = targetAccountId;
        loadedMessageIds.clear();

        if (chatHeader) chatHeader.textContent = targetName;
        if (msgInput) {
            msgInput.removeAttribute('disabled');
            msgInput.value = '';
        }
        if (sendBtn) sendBtn.removeAttribute('disabled');
        if (sendError) sendError.style.display = 'none';
        
        if (messagePane) {
            messagePane.innerHTML = '<div class="text-center text-muted fs-fluid-xs" id="msgPlaceholder">Loading messages…</div>';
        }
        
        loadMessages();
        pollTimer = setInterval(loadMessages, POLL_MS);
    }

    // ── Outbound Message Handlers ────────────────────────────────────────────
    async function sendMessage() {
        if (!msgInput || !sendBtn) return;
        const textContent = msgInput.value.trim();
        if (!textContent || !activeTargetAccountId) return;

        sendBtn.disabled = true;
        if (sendError) sendError.style.display = 'none';

        try {
            const res = await fetch(SEND_API, {
                method  : 'POST',
                headers : { 'Content-Type': 'application/json' },
                body    : JSON.stringify({ 
                    receiver_id: activeTargetAccountId,
                    message_content: textContent 
                }),
            });
            const json = await res.json();

            if (json.success) {
                msgInput.value = '';
                if (json.data) {
                    appendMessages([json.data]);
                } else {
                    loadMessages();
                }
                document.dispatchEvent(new CustomEvent('artovia:refreshThreads'));
            } else if (sendError) {
                sendError.textContent = json.message || 'Could not send message.';
                sendError.style.display = 'block';
            }
        } catch (e) {
            if (sendError) {
                sendError.textContent = 'Network error. Please try again.';
                sendError.style.display = 'block';
            }
        } finally {
            sendBtn.disabled = false;
            msgInput.focus();
        }
    }

    // ── Mobile Navigation Controls Handlers ──────────────────────────────────
    if (mobileChatBackButton) {
        mobileChatBackButton.addEventListener('click', () => {
            // Terminate polling processing requests while looking at the thread lists panel
            clearInterval(pollTimer);
            activeTargetAccountId = null;

            // Slide back panel view alignments smoothly
            if (conversationContainer) conversationContainer.classList.remove('active-mobile-view');
            if (inboxSidebarPanel) inboxSidebarPanel.classList.add('active-mobile-view');
        });
    }

    // Watch for desktop window scaling modifications to keep display states synced
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            if (inboxSidebarPanel) inboxSidebarPanel.classList.remove('active-mobile-view');
            if (conversationContainer) conversationContainer.classList.remove('active-mobile-view');
        } else if (!activeTargetAccountId) {
            // If on mobile screen sizes with no active thread focused, show the list panel
            if (inboxSidebarPanel) inboxSidebarPanel.classList.add('active-mobile-view');
        }
    });

    // ── Inter-Module Observers ───────────────────────────────────────────────
    document.addEventListener('artovia:threadChanged', (e) => {
        const { id, name } = e.detail;
        switchConversation(id, name);
    });

    // ── Form Input Listeners ──────────────────────────────────────────────────
    if (sendBtn) sendBtn.addEventListener('click', sendMessage);
    if (msgInput) {
        msgInput.addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }
})();