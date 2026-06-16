(function () {
    // ── Config ────────────────────────────────────────────────────────────────
    const FETCH_API = window.location.origin + '/finals-project/api/messages/fetch.php';
    const SEND_API  = window.location.origin + '/finals-project/api/messages/send.php';
    const POLL_MS   = 3000;

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
    let loadedMessageIds      = new Set();
    let pollTimer             = null;
    let activeContactAvatar   = null;  // avatar_url of the contact
    let myAvatar              = null;  // avatar_url of the current user

    // ── Helpers ───────────────────────────────────────────────────────────────
    function escapeHtml(str) {
        const d = document.createElement('div');
        d.textContent = str ?? '';
        return d.innerHTML;
    }

    function scrollToBottom() {
        if (messagePane) messagePane.scrollTop = messagePane.scrollHeight;
    }

    // ── Avatar element builder (keeps original icon size: 1.2rem) ────────────
    function buildMsgAvatar(avatarUrl, isMine) {
        if (!avatarUrl) {
            // Fallback: original icon, original size & color
            const i = document.createElement('i');
            i.className = 'bi bi-person-circle';
            i.style.cssText = `font-size:2rem; color:${isMine ? 'var(--clr-gold)' : 'var(--clr-text-muted)'}; flex-shrink:0;`;
            return i;
        }

        const img = document.createElement('img');
        img.src = (window.BASE_URL ?? '') + avatarUrl;
        img.alt = '';
        img.style.cssText = 'width:2rem;height:2rem;border-radius:50%;object-fit:cover;flex-shrink:0;';
        img.addEventListener('error', () => {
            const i = document.createElement('i');
            i.className = 'bi bi-person-circle';
            i.style.cssText = `font-size:2rem; color:${isMine ? 'var(--clr-gold)' : 'var(--clr-text-muted)'}; flex-shrink:0;`;
            img.replaceWith(i);
        });
        return img;
    }

    // ── Message Bubble Builder ────────────────────────────────────────────────
    function buildBubble(msg) {
        const userAccountId = window.CURRENT_ACCOUNT_ID ?? 0;
        const isMine = parseInt(msg.sender_account_id, 10) === parseInt(userAccountId, 10);
        const text   = escapeHtml(msg.message_content);

        const wrapper = document.createElement('div');
        wrapper.dataset.msgId = msg.message_id;
        wrapper.className = 'd-flex align-items-start gap-2';
        wrapper.style.justifyContent = isMine ? 'flex-end' : '';

        const bubble = document.createElement('div');
        bubble.className = 'p-3';
        bubble.style.cssText = `border-radius:var(--radius-md); max-width:75%; word-break:break-word;`;
        bubble.innerHTML = text;

        if (isMine) {
            bubble.style.background = 'var(--clr-gold)';
            bubble.style.color = '#fff';
            wrapper.appendChild(bubble);
            wrapper.appendChild(buildMsgAvatar(myAvatar, true));
        } else {
            bubble.classList.add('theme-border');
            bubble.style.borderWidth = '1px';
            bubble.style.background = 'var(--clr-bg-card)';
            wrapper.appendChild(buildMsgAvatar(activeContactAvatar, false));
            wrapper.appendChild(bubble);
        }

        return wrapper;
    }

    function appendMessages(messages) {
        if (!messagePane) return;
        let hasNewContent = false;

        messages.forEach(msg => {
            const msgId = msg.message_id;
            if (!msgId || loadedMessageIds.has(msgId)) return;
            loadedMessageIds.add(msgId);

            document.getElementById('msgPlaceholder')?.remove();
            messagePane.appendChild(buildBubble(msg));
            hasNewContent = true;
        });

        if (hasNewContent) scrollToBottom();
    }

    // ── Message Data Polling ──────────────────────────────────────────────────
    async function loadMessages() {
        if (!activeTargetAccountId) return;
        try {
            const res = await fetch(`${FETCH_API}?action=messages&target_id=${activeTargetAccountId}`);
            if (!res.ok) return;

            const json = await res.json();
            if (json.success && Array.isArray(json.data)) {
                if (json.data.length === 0) {
                    const placeholder = document.getElementById('msgPlaceholder');
                    if (placeholder) placeholder.innerHTML = '<div class="text-center text-muted p-4 fs-fluid-xs">No messages yet. Send a wave!</div>';
                    return;
                }
                appendMessages(json.data);
            }
        } catch (e) {
            console.warn('Failed polling active chat records:', e);
        }
    }

    function switchConversation(targetAccountId, targetName, contactAvatarUrl, myAvatarUrl) {
        clearInterval(pollTimer);
        activeTargetAccountId = targetAccountId;
        activeContactAvatar   = contactAvatarUrl ?? null;
        myAvatar              = myAvatarUrl ?? null;
        loadedMessageIds.clear();

        if (chatHeader) chatHeader.textContent = targetName;
        if (msgInput) { msgInput.removeAttribute('disabled'); msgInput.value = ''; }
        if (sendBtn)  sendBtn.removeAttribute('disabled');
        if (sendError) sendError.style.display = 'none';

        if (messagePane) {
            messagePane.innerHTML = '<div class="text-center text-muted fs-fluid-xs" id="msgPlaceholder">Loading messages…</div>';
        }

        loadMessages();
        pollTimer = setInterval(loadMessages, POLL_MS);
    }

    // ── Outbound Message Handlers ─────────────────────────────────────────────
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
                json.data ? appendMessages([json.data]) : loadMessages();
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

    // ── Mobile Navigation ─────────────────────────────────────────────────────
    if (mobileChatBackButton) {
        mobileChatBackButton.addEventListener('click', () => {
            clearInterval(pollTimer);
            activeTargetAccountId = null;
            if (conversationContainer) conversationContainer.classList.remove('active-mobile-view');
            if (inboxSidebarPanel) inboxSidebarPanel.classList.add('active-mobile-view');
        });
    }

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            inboxSidebarPanel?.classList.remove('active-mobile-view');
            conversationContainer?.classList.remove('active-mobile-view');
        } else if (!activeTargetAccountId) {
            inboxSidebarPanel?.classList.add('active-mobile-view');
        }
    });

    // ── Inter-Module Observers ────────────────────────────────────────────────
    document.addEventListener('artovia:threadChanged', (e) => {
        const { id, name, contactAvatarUrl, myAvatarUrl } = e.detail;
        switchConversation(id, name, contactAvatarUrl, myAvatarUrl);
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