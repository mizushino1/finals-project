<main class="py-md-4">
    <div class="container-fluid px-md-4">
        <div class="theme-border d-flex position-relative overflow-hidden chat-responsive-wrapper">

            <div id="inboxSidebarPanel" class="d-flex flex-column h-100 chat-sidebar-panel chat-panel-view active-mobile-view">
                <div class="p-3 chat-sidebar-header">
                    <h3 class="mb-3 joan">Inbox</h3>
                    <div class="input-group mb-2">
                        <span class="input-group-text bg-white theme-border chat-search-prepend">
                            <i class="bi bi-search" style="color: var(--clr-text-muted);"></i>
                        </span>
                        <input id="inboxSearch" type="text" class="form-control theme-border chat-search-input" placeholder="Search messages…" autocomplete="off">
                    </div>
                </div>

                <div id="threadList" class="flex-grow-1 overflow-y-auto hide-scrollbar">
                    <div class="text-center p-4 text-muted fs-fluid-xs" id="threadPlaceholder">
                        Loading conversations…
                    </div>
                </div>
            </div>

            <div id="conversationContainer" class="flex-grow-1 d-flex flex-column h-100 chat-conversation-container chat-panel-view">
                
                <div class="p-3 d-flex align-items-center gap-3 chat-conversation-header">
                    <button id="mobileChatBackButton" class="btn btn-link text-decoration-none p-0 d-lg-none" type="button" aria-label="Return to list">
                        <i class="bi bi-arrow-left fs-4" style="color: var(--clr-gold);"></i>
                    </button>
                    
                    <h5 id="chatHeader" class="mb-0 text-truncate font-heading fw-bold" style="color: var(--clr-text-primary);">Select a Chat</h5>
                </div>

                <div id="messagePane" class="flex-grow-1 p-3 p-md-4 chat-message-pane hide-scrollbar d-flex flex-column gap-3">
                    <div class="text-center text-muted fs-fluid-xs" id="msgPlaceholder">
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
    // Global scope authorization and mapping identity keys logic references
    window.CURRENT_ACCOUNT_ID = <?= json_encode($_SESSION['account_id'] ?? ($_SESSION['user_id'] ?? 0)); ?>;
</script>
<script src="<?php echo BASE_URL; ?>public/js/inbox.js"></script>
<script src="<?php echo BASE_URL; ?>public/js/conversation.js"></script>