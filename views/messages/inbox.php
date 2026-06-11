<main class="py-0">
    <div class="container-fluid px-0">
        <!-- Inbox Wrapper -->
        <div class="d-flex" style="height: 100vh; background: var(--clr-bg-card); overflow: hidden;">

            <!-- Sidebar: Message List -->
            <div class="d-flex flex-column" style="width: 350px; border-right: 2px solid var(--clr-gold);">
                <div class="p-3" style="border-bottom: 2px solid var(--clr-gold);">
                    <h3 class="mb-3">Inbox</h3>

                    <div class="input-group mb-2">
                        <span class="input-group-text bg-white border-end-0 theme-border" style="border-right: none !important;">
                            <i class="bi bi-search" style="color: var(--clr-text-muted);"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 theme-border" placeholder="Search messages...."
                            style="border-left: none !important;">
                    </div>
                </div>

                <div class="flex-grow-1 overflow-y-auto hide-scrollbar">
                    <div class="p-3" style="border-bottom: 1px solid var(--clr-border); cursor: pointer; background: var(--clr-bg-card);">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-person-circle" style="font-size: 2rem; color: var(--clr-gold);"></i>
                            <div>
                                <h6 class="mb-0">BENten</h6>
                                <p class="mb-0 fs-fluid-xs text-muted">Hello po! I would like....</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-3" style="border-bottom: 1px solid var(--clr-border); cursor: pointer; background: var(--clr-bg-alt);">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-person-circle" style="font-size: 2rem; color: var(--clr-gold);"></i>
                            <div>
                                <h6 class="mb-0">BENtwenty</h6>
                                <p class="mb-0 fs-fluid-xs text-muted">Hello! I would like so....</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State Area -->
            <div class="flex-grow-1 d-flex align-items-center justify-content-center"
                style="background: linear-gradient(to bottom, var(--clr-bg), var(--clr-surface));">
                <div class="text-center p-4">
                    <i class="bi bi-chat-dots" style="font-size: 4rem; color: var(--clr-gold);"></i>
                    <h4 class="mt-3 joan" style="color: var(--clr-text-secondary);">Your Inbox</h4>
                    <p class="info-desc mx-auto" style="max-width: 300px;">
                        Select a conversation from the sidebar to view messages or start a new dialogue.
                    </p>
                    <button class="btn btn-artovia-outline mt-2">Start a Conversation</button>
                </div>
            </div>
        </div>
    </div>
</main>