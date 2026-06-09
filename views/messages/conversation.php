<main class="py-4">
    <div class="container-fluid px-4">
        <div class="theme-border d-flex" style="height: 600px; background: var(--clr-bg-card); overflow: hidden;">

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

            <div class="flex-grow-1 d-flex flex-column" style="background: linear-gradient(to bottom, var(--clr-bg), var(--clr-surface));">
                <div class="p-3 d-flex align-items-center justify-content-between" style="border-bottom: 2px solid var(--clr-gold);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-person-circle" style="font-size: 1.5rem; color: var(--clr-gold);"></i>
                        <h5 class="mb-0">BENten</h5>
                    </div>
                    <i class="bi bi-three-dots-vertical" style="font-size: 1.25rem;"></i>
                </div>

                <div class="flex-grow-1 p-4 overflow-y-auto hide-scrollbar d-flex flex-column gap-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-person-circle" style="font-size: 1.2rem; color: var(--clr-text-muted);"></i>
                        <div class="p-3 theme-border" style="border-width: 1px !important; border-radius: var(--radius-md); background: var(--clr-bg-card); max-width: 60%;">
                            Hello po! I would like to inquire about a commission.
                        </div>
                    </div>
                    <div class="d-flex align-items-start justify-content-end gap-2">
                        <div class="p-3 text-white" style="border-radius: var(--radius-md); background: var(--clr-gold); max-width: 60%;">
                            Sure! What kind of art style are you looking for?
                        </div>
                        <i class="bi bi-person-circle" style="font-size: 1.2rem; color: var(--clr-gold);"></i>
                    </div>
                </div>

                <div class="p-3" style="border-top: 2px solid var(--clr-gold);">
                    <div class="input-group">
                        <input type="text" class="form-control theme-border" placeholder="Type a message..." style="border-width: 1px !important;">
                        <button class="btn btn-artovia-primary"><i class="bi bi-send-fill"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>