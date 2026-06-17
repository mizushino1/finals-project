<?php require_once __DIR__ . '/../../src/middleware/auth_middleware.php'; ?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/inbox.css">
<main class="py-0">
    <div class="container-fluid p-0">
        <div id="chatMainContainer" class="d-flex position-relative overflow-hidden chat-responsive-wrapper" style="height: 90vh; background: var(--clr-bg-card);">

            <!-- ── Sidebar ────────────────────────────────────────────────── -->
            <div id="inboxSidebarPanel" class="d-flex flex-column h-100 chat-sidebar-panel chat-panel-view active-mobile-view" style="width: 350px; border-right: 2px solid var(--clr-border);">
                <div class="p-3 chat-sidebar-header" style="border-bottom: 2px solid var(--clr-border);">
                    <h3 class="mb-3 joan">Inbox</h3>
                    <div class="input-group mb-2">
                        <span class="input-group-text bg-white theme-border chat-search-prepend" style="border-right: none !important;">
                            <i class="bi bi-search" style="color: var(--clr-text-muted);"></i>
                        </span>
                        <input id="inboxSearch" type="text" class="form-control theme-border chat-search-input"
                            placeholder="Search messages…" autocomplete="off" style="border-left: none !important;">
                    </div>
                </div>

                <div id="threadList" class="flex-grow-1 overflow-y-auto hide-scrollbar">
                    <div class="text-center p-4 text-muted fs-fluid-xs" id="threadPlaceholder">
                        Loading conversations…
                    </div>
                </div>
            </div>

            <!-- ── Conversation Panel ─────────────────────────────────────── -->
            <div id="conversationContainer" class="flex-grow-1 d-flex flex-column h-100 chat-conversation-container chat-panel-view"
                style="background: linear-gradient(to bottom, var(--clr-bg), var(--clr-surface));">

                <!-- Header -->
                <div class="p-3 d-flex align-items-center gap-3 chat-conversation-header"
                    style="border-bottom: 2px solid var(--clr-border); height: 75px; flex-shrink: 0;">
                    <button id="mobileChatBackButton" class="btn btn-link text-decoration-none p-0 d-lg-none"
                        type="button" aria-label="Return to list">
                        <i class="bi bi-arrow-left fs-4" style="color: var(--clr-gold);"></i>
                    </button>
                    <h5 id="chatHeader" class="mb-0 text-truncate font-heading fw-bold"
                        style="color: var(--clr-text-primary);">Select a Chat</h5>
                </div>

                <!-- Message Pane -->
                <div id="messagePane" class="flex-grow-1 p-3 p-md-4 chat-message-pane hide-scrollbar d-flex flex-column gap-3">
                    <div class="text-center text-muted fs-fluid-xs" id="msgPlaceholder">
                        Select a conversation from the sidebar to view messages or start a new dialogue.
                    </div>
                </div>

                <!-- ── Footer ────────────────────────────────────────────── -->
                <div class="p-3 chat-conversation-footer" style="border-top: 2px solid var(--clr-border);">

                    <!-- Image preview strip (hidden until a file is chosen) -->
                    <div id="imagePreviewStrip" class="mb-2 d-none d-flex align-items-center gap-2">
                        <div style="position:relative; display:inline-block;">
                            <img id="imagePreviewThumb"
                                src="" alt="Preview"
                                style="height:72px; width:72px; object-fit:cover;
                                        border-radius:var(--radius-sm,6px);
                                        border:2px solid var(--clr-gold);">
                            <button id="clearImageBtn" type="button"
                                class="btn p-0 d-flex align-items-center justify-content-center"
                                aria-label="Remove image"
                                style="position:absolute; top:-8px; right:-8px;
                                           width:20px; height:20px; border-radius:50%;
                                           background:var(--clr-danger, #dc3545); color:#fff;
                                           font-size:.65rem; line-height:1; border:none;">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                        <span class="fs-fluid-xs text-muted" id="imagePreviewName"
                            style="max-width:180px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;"></span>
                    </div>

                    <!-- Input row -->
                    <div class="input-group">
                        <!-- Hidden file input -->
                        <input id="imageInput" type="file" accept="image/jpeg,image/png,image/gif,image/webp"
                            class="d-none" aria-label="Attach image">

                        <!-- Attach button -->
                        <button id="attachBtn" type="button"
                            class="btn theme-border"
                            style="border-right:none; border-radius:var(--radius-sm,6px) 0 0 var(--radius-sm,6px);
                                       background:var(--clr-bg-card); color:var(--clr-gold);"
                            title="Attach image" disabled>
                            <i class="bi bi-image"></i>
                        </button>

                        <input id="msgInput" type="text" class="form-control theme-border chat-footer-input"
                            placeholder="Type a message…" autocomplete="off" disabled
                            style="border-left:none; border-right:none;">

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
    window.CURRENT_ACCOUNT_ID = <?= json_encode($_SESSION['account_id'] ?? ($_SESSION['user_id'] ?? 0)); ?>;
</script>
<script src="<?php echo BASE_URL; ?>public/js/inbox.js"></script>
<script src="<?php echo BASE_URL; ?>public/js/conversation.js"></script>