<?php
require_once __DIR__ . '/../../src/middleware/admin_middleware.php';
require_once __DIR__ . '/../../config/database.php';

$db = getDB();

// All accounts with role + status info.
// Resolves artist_id (for type='artist') so the ban endpoint receives the
// correct id depending on account type, matching ban_user.php's expectations.
$stmtUsers = $db->query('
    SELECT
        a.account_id,
        a.username,
        a.first_name,
        a.last_name,
        r.role_name,
        s.status_name AS account_status,
        u.user_id,
        art.artist_id
    FROM account_tbl a
    JOIN role_tbl r            ON a.role_id = r.role_id
    JOIN account_status_tbl s  ON a.account_status_id = s.account_status_id
    LEFT JOIN user_tbl u       ON a.account_id = u.account_id
    LEFT JOIN artist_tbl art   ON a.account_id = art.account_id
    ORDER BY a.username ASC
');
$users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

$statusColors = [
    'Active'    => 'var(--clr-open)',
    'Banned'    => 'var(--clr-closed)',
    'Suspended' => 'var(--clr-star)',
];
?>

<style>
    /* Modal Overlay - Slightly tinted dark overlay to match the warm palette */
    .admin-modal {
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(25, 23, 20, 0.4);
        /* Warm dark tint */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Modal Content Box - Styled like your dashboard cards */
    .modal-content {
        background-color: #ffffff;
        padding: 24px;
        border: 2px solid #c8b189;
        /* 2px crisp border matching dashboard cards */
        border-radius: 12px;
        width: 100%;
        max-width: 450px;
        box-shadow: 0 10px 25px rgba(200, 177, 137, 0.15);
        animation: fadeIn 0.2s ease-out;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    /* Heading matching your serif/dark dashboard typography */
    .modal-header h3 {
        margin: 0;
        font-family: inherit;
        color: #1a1a1a;
        font-size: 1.25rem;
        font-weight: 700;
    }

    /* Close (X) Button - Uses generic close class or can also support your unique account element close button */
    .close-modal-btn,
    .close-account-modal-btn {
        font-size: 24px;
        cursor: pointer;
        color: #c8b189;
        /* Uses the accent tan color */
        transition: color 0.2s ease;
        line-height: 1;
    }

    .close-modal-btn:hover,
    .close-account-modal-btn:hover {
        color: #1a1a1a;
    }

    .modal-body {
        color: #555555;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 24px;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    /* Button UI Basics */
    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        border: none;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    /* Cancel Button - Subtle and clean */
    .btn-secondary {
        background-color: #f4f0e6;
        /* Cream shade to match the dashboard vibe */
        color: #555555;
        border: 1px solid #e2dacb;
    }

    .btn-secondary:hover {
        background-color: #eae3d2;
        color: #1a1a1a;
    }

    /* Danger Button - The exact red from your dashboard screenshot */
    .btn-danger {
        background-color: #dc3545;
        color: #fff;
    }

    .btn-danger:hover {
        background-color: #bd2130;
        /* Darker red on hover */
    }

    /* Success Button - Added for safe/constructive actions like Unban/Restore */
    .btn-success {
        background-color: #198754;
        /* Operational status green */
        color: #fff;
    }

    .btn-success:hover {
        background-color: #157347;
        /* Darker green on hover */
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<main class="py-5">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="joan mb-0">Admin Dashboard</h2>
                <h3 class="joan mb-0">Users</h3>
                <p class="text-muted mb-0">Overview of all users in the web.</p>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="card theme-border p-4 h-100" style="background: var(--clr-bg-card);">
                    <h5 class="mb-3">Quick Actions</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?= BASE_URL ?>admin" class="btn btn-outline-secondary">Dashboard</a>
                        <a href="<?= BASE_URL ?>admin/users" class="btn btn-outline-secondary">Manage Users</a>
                        <a href="<?= BASE_URL ?>admin/commissions" class="btn btn-outline-secondary">Review Commissions</a>
                        <a href="<?= BASE_URL ?>admin/payments" class="btn btn-outline-secondary">Payment Records</a>
                        <a href="<?= BASE_URL ?>admin/reports" class="btn btn-outline-secondary">Reports</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card theme-border p-4 h-100 d-flex align-items-center justify-content-center"
                    style="background: var(--clr-bg-card);">
                    <h6 class="text-muted mb-0">System Status: <span class="text-success fw-bold">Operational</span>
                    </h6>
                </div>
            </div>
        </div>

        <div class="card theme-border border-0 shadow-sm p-0 overflow-hidden mb-4"
            style="background: var(--clr-bg-card);">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0">System User Management</h5>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 fs-fluid-3xs">
                    <thead style="background-color: var(--clr-bg-alt);">
                        <tr>
                            <th class="p-3">Username</th>
                            <th class="p-3">Account Type</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td class="p-3 text-muted text-center" colspan="4">No users found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $u):
                                $role = strtolower($u['role_name']);

                                // Determine which id + type to send to ban/unban endpoints,
                                // matching ban_user.php's expected ['user','artist','client'] types
                                if ($role === 'artist' && $u['artist_id']) {
                                    $banType = 'artist';
                                    $banId   = $u['artist_id'];
                                } elseif ($u['user_id']) {
                                    $banType = 'user';
                                    $banId   = $u['user_id'];
                                } else {
                                    // Administrators have no user_tbl/artist_tbl row —
                                    // ban_user.php currently cannot resolve admin accounts.
                                    $banType = null;
                                    $banId   = null;
                                }

                                $isBanned = strtolower($u['account_status']) === 'banned';
                                $idPrefix = $role === 'artist' ? 'A' : ($role === 'admin' || $role === 'administrator' ? 'AD' : 'U');
                            ?>
                                <tr style="border-bottom: 1px solid var(--clr-border);" data-account-id="<?= (int) $u['account_id'] ?>">
                                    <td class="p-3">
                                        <div class="fw-bold"><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></div>
                                        <small class="text-muted">@<?= htmlspecialchars($u['username']) ?> &middot; ID: #<?= $idPrefix ?>-<?= (int) $u['account_id'] ?></small>
                                    </td>
                                    <td class="p-3 <?= $role === 'artist' ? 'text-warning' : '' ?>">
                                        <?= htmlspecialchars($u['role_name']) ?>
                                    </td>
                                    <td class="p-3">
                                        <?php $color = $statusColors[$u['account_status']] ?? 'var(--clr-text-muted)'; ?>
                                        <span class="badge" style="background-color: <?= $color ?>; color: white;"><?= htmlspecialchars($u['account_status']) ?></span>
                                    </td>
                                    <td class="p-3 text-end">
                                        <?php if ($banType): ?>
                                            <button
                                                class="btn btn-sm btn-outline-danger me-2 js-ban-btn"
                                                data-id="<?= (int) $banId ?>"
                                                data-type="<?= $banType ?>"
                                                <?= $isBanned ? 'disabled' : '' ?>>Ban</button>
                                            <button
                                                class="btn btn-sm btn-outline-success js-unban-btn"
                                                data-id="<?= (int) $banId ?>"
                                                data-type="<?= $banType ?>"
                                                <?= !$isBanned ? 'disabled' : '' ?>>Unban</button>
                                        <?php else: ?>
                                            <span class="text-muted small">Administrator accounts cannot be banned</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<div id="accountActionModal" class="admin-modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="accountModalTitle">Confirm Action</h3>
            <span class="close-account-modal-btn">&times;</span>
        </div>
        <div class="modal-body">
            <p id="accountModalMessage">Are you sure you want to proceed with this action?</p>
            <div id="accountModalErrorMessage" style="display: none; color: #dc3545; font-size: 0.85rem; margin-top: 12px; font-weight: 600;"></div>
        </div>
        <div class="modal-footer">
            <button id="cancelAccountBtn" class="btn btn-secondary">Cancel</button>
            <button id="confirmAccountBtn" class="btn btn-danger">
                <span class="account-btn-text">Confirm</span>
                <span class="account-btn-spinner" style="display: none;">Processing...</span>
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('accountActionModal');
        const titleElem = document.getElementById('accountModalTitle');
        const messageElem = document.getElementById('accountModalMessage');
        const confirmBtn = document.getElementById('confirmAccountBtn');
        const cancelBtn = document.getElementById('cancelAccountBtn');
        const closeX = document.querySelector('.close-account-modal-btn');
        const errorContainer = document.getElementById('accountModalErrorMessage');

        let currentTriggeringBtn = null;
        let currentEndpoint = '';

        // Open modal on Ban request (Red theme)
        document.querySelectorAll('.js-ban-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                currentTriggeringBtn = btn;
                currentEndpoint = 'ban_user.php';

                titleElem.textContent = 'Ban Account';
                messageElem.textContent = 'Ban this account? They will lose access to Artovia.';
                confirmBtn.querySelector('.account-btn-text').textContent = 'Ban Account';

                // Adjust to danger color
                confirmBtn.classList.remove('btn-success');
                confirmBtn.classList.add('btn-danger');

                openModal();
            });
        });

        // Open modal on Unban request (Green theme)
        document.querySelectorAll('.js-unban-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                currentTriggeringBtn = btn;
                currentEndpoint = 'unban_user.php';

                titleElem.textContent = 'Restore Account';
                messageElem.textContent = 'Restore this account to Active status?';
                confirmBtn.querySelector('.account-btn-text').textContent = 'Restore Status';

                // Adjust to success color
                confirmBtn.classList.remove('btn-danger');
                confirmBtn.classList.add('btn-success');

                openModal();
            });
        });

        function openModal() {
            if (errorContainer) {
                errorContainer.style.display = 'none';
                errorContainer.textContent = '';
            }
            modal.style.display = 'flex';
        }

        const closeModal = () => {
            modal.style.display = 'none';
            currentTriggeringBtn = null;
            currentEndpoint = '';

            confirmBtn.disabled = false;
            confirmBtn.querySelector('.account-btn-text').style.display = 'inline';
            confirmBtn.querySelector('.account-btn-spinner').style.display = 'none';
        };

        cancelBtn.addEventListener('click', closeModal);
        closeX.addEventListener('click', closeModal);
        window.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        // Handle AJAX Action submission
        confirmBtn.addEventListener('click', async () => {
            if (!currentTriggeringBtn || !currentEndpoint) return;

            const row = currentTriggeringBtn.closest('tr');
            const id = currentTriggeringBtn.dataset.id;
            const type = currentTriggeringBtn.dataset.type;

            confirmBtn.disabled = true;
            confirmBtn.querySelector('.account-btn-text').style.display = 'none';
            confirmBtn.querySelector('.account-btn-spinner').style.display = 'inline';
            confirmBtn.querySelector('.account-btn-spinner').textContent = currentEndpoint === 'ban_user.php' ? 'Banning...' : 'Restoring...';
            if (errorContainer) errorContainer.style.display = 'none';

            try {
                const res = await fetch('<?= BASE_URL ?>api/admin/' + currentEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id,
                        type
                    })
                });
                const data = await res.json();

                if (data.success) {
                    const banBtn = row.querySelector('.js-ban-btn');
                    const unbanBtn = row.querySelector('.js-unban-btn');
                    const badge = row.querySelector('.badge');

                    if (currentEndpoint === 'ban_user.php') {
                        if (banBtn) banBtn.disabled = true;
                        if (unbanBtn) unbanBtn.disabled = false;
                        if (badge) {
                            badge.textContent = 'Banned';
                            badge.style.backgroundColor = 'var(--clr-closed)';
                        }
                    } else {
                        if (banBtn) banBtn.disabled = false;
                        if (unbanBtn) unbanBtn.disabled = true;
                        if (badge) {
                            badge.textContent = 'Active';
                            badge.style.backgroundColor = 'var(--clr-open)';
                        }
                    }
                    closeModal();
                } else {
                    throw new Error(data.message || 'Action failed.');
                }
            } catch (err) {
                if (errorContainer) {
                    errorContainer.textContent = err.message || 'Network error.';
                    errorContainer.style.display = 'block';
                }
                confirmBtn.disabled = false;
                confirmBtn.querySelector('.account-btn-text').style.display = 'inline';
                confirmBtn.querySelector('.account-btn-spinner').style.display = 'none';
            }
        });
    });
</script>