(function () {
    // ── Config ────────────────────────────────────────────────────────────────
    const FETCH_API = window.location.origin + '/finals-project/api/messages/fetch.php';
    const SEND_API  = window.location.origin + '/finals-project/api/messages/send.php';
    const POLL_MS   = 3000;
    const MAX_MB    = 5;

    // ── DOM References ────────────────────────────────────────────────────────
    const chatHeader            = document.getElementById('chatHeader');
    const messagePane           = document.getElementById('messagePane');
    const msgInput              = document.getElementById('msgInput');
    const sendBtn               = document.getElementById('sendBtn');
    const sendError             = document.getElementById('sendError');
    const mobileChatBackButton  = document.getElementById('mobileChatBackButton');
    const inboxSidebarPanel     = document.getElementById('inboxSidebarPanel');
    const conversationContainer = document.getElementById('conversationContainer');

    // Photo-attach elements
    const attachBtn         = document.getElementById('attachBtn');
    const imageInput        = document.getElementById('imageInput');
    const imagePreviewStrip = document.getElementById('imagePreviewStrip');
    const imagePreviewThumb = document.getElementById('imagePreviewThumb');
    const imagePreviewName  = document.getElementById('imagePreviewName');
    const clearImageBtn     = document.getElementById('clearImageBtn');

    // ── State ─────────────────────────────────────────────────────────────────
    let activeTargetAccountId = null;
    let loadedMessageIds      = new Set();
    let pollTimer             = null;
    let activeContactAvatar   = null;
    let myAvatar              = null;
    let pendingImageFile      = null;   // File object staged for send

    // ── Helpers ───────────────────────────────────────────────────────────────
    function escapeHtml(str) {
        const d = document.createElement('div');
        d.textContent = str ?? '';
        return d.innerHTML;
    }

    function scrollToBottom() {
        if (messagePane) messagePane.scrollTop = messagePane.scrollHeight;
    }

    function showError(msg) {
        if (!sendError) return;
        sendError.textContent = msg;
        sendError.style.display = 'block';
    }

    function clearError() {
        if (sendError) sendError.style.display = 'none';
    }

    // ── Avatar Builder ────────────────────────────────────────────────────────
    function buildMsgAvatar(avatarUrl, isMine) {
        if (!avatarUrl) {
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
        const isMine        = parseInt(msg.sender_account_id, 10) === parseInt(userAccountId, 10);

        const wrapper = document.createElement('div');
        wrapper.dataset.msgId = msg.message_id;
        wrapper.className     = 'd-flex align-items-start gap-2';
        wrapper.style.justifyContent = isMine ? 'flex-end' : '';

        const bubble = document.createElement('div');
        bubble.className  = 'p-3';
        bubble.style.cssText = `
            border-radius: var(--radius-md);
            max-width: 75%;
            word-break: break-word;
        `;

        // ── Image attachment ──────────────────────────────────────────────────
        if (msg.image_url) {
            const imageUrl = msg.image_url;

            const imgEl = document.createElement('img');
            imgEl.src   = imageUrl;
            imgEl.alt   = 'Sent image';
            imgEl.style.cssText = `
                display: block;
                max-width: 100%;
                max-height: 300px;
                border-radius: var(--radius-sm, 6px);
                cursor: pointer;
                margin-bottom: ${msg.message_content ? '0.5rem' : '0'};
            `;

            // Click → open full-size in new tab
            imgEl.addEventListener('click', () => window.open(imageUrl, '_blank'));

            bubble.appendChild(imgEl);
        }

        // ── Text content ──────────────────────────────────────────────────────
        if (msg.message_content) {
            const textNode = document.createElement('span');
            textNode.innerHTML = escapeHtml(msg.message_content);
            bubble.appendChild(textNode);
        }

        if (isMine) {
            bubble.style.background = 'var(--clr-gold)';
            bubble.style.color      = '#fff';
            wrapper.appendChild(bubble);
            wrapper.appendChild(buildMsgAvatar(myAvatar, true));
        } else {
            bubble.classList.add('theme-border');
            bubble.style.borderWidth  = '1px';
            bubble.style.background   = 'var(--clr-bg-card)';
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

    // ── Polling ───────────────────────────────────────────────────────────────
    async function loadMessages() {
        if (!activeTargetAccountId) return;
        try {
            const res = await fetch(`${FETCH_API}?action=messages&target_id=${activeTargetAccountId}`);
            if (!res.ok) return;
            const json = await res.json();
            if (json.success && Array.isArray(json.data)) {
                if (json.data.length === 0) {
                    const placeholder = document.getElementById('msgPlaceholder');
                    if (placeholder) placeholder.innerHTML =
                        '<div class="text-center text-muted p-4 fs-fluid-xs">No messages yet. Send a wave!</div>';
                    return;
                }
                appendMessages(json.data);
            }
        } catch (e) {
            console.warn('Failed polling active chat records:', e);
        }
    }

    // ── Switch Conversation ───────────────────────────────────────────────────
    function switchConversation(targetAccountId, targetName, contactAvatarUrl, myAvatarUrl) {
        clearInterval(pollTimer);
        activeTargetAccountId = targetAccountId;
        activeContactAvatar   = contactAvatarUrl ?? null;
        myAvatar              = myAvatarUrl ?? null;
        loadedMessageIds.clear();
        clearStagedImage();

        if (chatHeader) chatHeader.textContent = targetName;
        if (msgInput)  { msgInput.removeAttribute('disabled'); msgInput.value = ''; }
        if (sendBtn)   sendBtn.removeAttribute('disabled');
        if (attachBtn) attachBtn.removeAttribute('disabled');
        clearError();

        if (messagePane) {
            messagePane.innerHTML =
                '<div class="text-center text-muted fs-fluid-xs" id="msgPlaceholder">Loading messages…</div>';
        }

        loadMessages();
        pollTimer = setInterval(loadMessages, POLL_MS);
    }

    // ── Image Staging ─────────────────────────────────────────────────────────
    function stageImage(file) {
        if (!file) return;

        if (file.size > MAX_MB * 1024 * 1024) {
            showError(`Image must be under ${MAX_MB} MB.`);
            imageInput.value = '';
            return;
        }

        pendingImageFile = file;

        const reader = new FileReader();
        reader.onload = (e) => {
            if (imagePreviewThumb) imagePreviewThumb.src = e.target.result;
            if (imagePreviewName)  imagePreviewName.textContent = file.name;
            if (imagePreviewStrip) imagePreviewStrip.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }

    function clearStagedImage() {
        pendingImageFile = null;
        if (imageInput)        imageInput.value = '';
        if (imagePreviewThumb) imagePreviewThumb.src = '';
        if (imagePreviewName)  imagePreviewName.textContent = '';
        if (imagePreviewStrip) imagePreviewStrip.classList.add('d-none');
    }

    // ── Send Message ──────────────────────────────────────────────────────────
    async function sendMessage() {
        if (!msgInput || !sendBtn) return;

        const textContent = msgInput.value.trim();
        if (!textContent && !pendingImageFile) return;
        if (!activeTargetAccountId) return;

        sendBtn.disabled = true;
        if (attachBtn) attachBtn.disabled = true;
        clearError();

        try {
            let res;

            if (pendingImageFile) {
                // Multipart upload when an image is attached
                const form = new FormData();
                form.append('receiver_id',     activeTargetAccountId);
                form.append('message_content', textContent);
                form.append('image',           pendingImageFile, pendingImageFile.name);

                res = await fetch(SEND_API, { method: 'POST', body: form });
            } else {
                // JSON for text-only messages (unchanged behaviour)
                res = await fetch(SEND_API, {
                    method  : 'POST',
                    headers : { 'Content-Type': 'application/json' },
                    body    : JSON.stringify({
                        receiver_id     : activeTargetAccountId,
                        message_content : textContent,
                    }),
                });
            }

            const json = await res.json();

            if (json.success) {
                msgInput.value = '';
                clearStagedImage();

                // Append the returned message immediately, or fall back to polling
                json.data ? appendMessages([json.data]) : loadMessages();
                document.dispatchEvent(new CustomEvent('artovia:refreshThreads'));
            } else {
                showError(json.message || 'Could not send message.');
            }
        } catch (e) {
            showError('Network error. Please try again.');
        } finally {
            sendBtn.disabled  = false;
            if (attachBtn) attachBtn.disabled = false;
            msgInput.focus();
        }
    }

    // ── Image Input Events ────────────────────────────────────────────────────
    if (attachBtn) {
        attachBtn.addEventListener('click', () => imageInput?.click());
    }

    if (imageInput) {
        imageInput.addEventListener('change', () => {
            const file = imageInput.files?.[0];
            if (file) stageImage(file);
        });
    }

    if (clearImageBtn) {
        clearImageBtn.addEventListener('click', () => {
            clearStagedImage();
            clearError();
        });
    }

    // Drag-and-drop onto the message pane
    if (messagePane) {
        messagePane.addEventListener('dragover', (e) => {
            e.preventDefault();
            messagePane.style.outline = '2px dashed var(--clr-gold)';
        });

        messagePane.addEventListener('dragleave', () => {
            messagePane.style.outline = '';
        });

        messagePane.addEventListener('drop', (e) => {
            e.preventDefault();
            messagePane.style.outline = '';
            if (!activeTargetAccountId) return;
            const file = e.dataTransfer.files?.[0];
            if (file && file.type.startsWith('image/')) stageImage(file);
        });
    }

    // ── Mobile Navigation ─────────────────────────────────────────────────────
    if (mobileChatBackButton) {
        mobileChatBackButton.addEventListener('click', () => {
            clearInterval(pollTimer);
            activeTargetAccountId = null;
            conversationContainer?.classList.remove('active-mobile-view');
            inboxSidebarPanel?.classList.add('active-mobile-view');
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