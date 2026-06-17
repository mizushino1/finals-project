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
                <table class="table align-middle mb-0">
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

<script>
    async function setAccountStatus(btn, endpoint) {
        const row = btn.closest('tr');
        const id = btn.dataset.id;
        const type = btn.dataset.type;

        btn.disabled = true;

        try {
            const res = await fetch('<?= BASE_URL ?>api/admin/' + endpoint, {
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

                if (endpoint === 'ban_user.php') {
                    banBtn.disabled = true;
                    unbanBtn.disabled = false;
                    badge.textContent = 'Banned';
                    badge.style.backgroundColor = 'var(--clr-closed)';
                } else {
                    banBtn.disabled = false;
                    unbanBtn.disabled = true;
                    badge.textContent = 'Active';
                    badge.style.backgroundColor = 'var(--clr-open)';
                }
            } else {
                alert(data.message || 'Action failed.');
                btn.disabled = false;
            }
        } catch (err) {
            alert('Network error.');
            btn.disabled = false;
        }
    }

    document.querySelectorAll('.js-ban-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (confirm('Ban this account? They will lose access to Artovia.')) {
                setAccountStatus(btn, 'ban_user.php');
            }
        });
    });

    document.querySelectorAll('.js-unban-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (confirm('Restore this account to Active status?')) {
                setAccountStatus(btn, 'unban_user.php');
            }
        });
    });
</script>