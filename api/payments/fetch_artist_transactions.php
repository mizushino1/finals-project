<?php
ob_start();

/**
 * GET /api/payments/fetch_artist_transactions.php
 *
 * Returns paginated payment records for commissions assigned to the
 * currently logged-in artist (role = Artist).
 *
 * Query params (all optional):
 *   search      (string)  — filter by numeric transaction ID
 *   status      (string)  — Paid | Pending | Cancelled | (empty = all)
 *   date_start  (string)  — YYYY-MM-DD
 *   date_end    (string)  — YYYY-MM-DD
 *   page        (int)     — 1-based (default 1)
 *   per_page    (int)     — rows per page (default 10, max 50)
 */

require_once '../../config/constants.php';
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// ── Auth: artists only ────────────────────────────────────────────────────────
if (empty($_SESSION['account_id']) || strtolower($_SESSION['role'] ?? '') !== 'artist') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$accountId = (int) $_SESSION['account_id'];
$db        = getDB();

// ── Resolve artist_id ─────────────────────────────────────────────────────────
$stmtArtist = $db->prepare('SELECT artist_id FROM artist_tbl WHERE account_id = ? LIMIT 1');
$stmtArtist->execute([$accountId]);
$artistRow = $stmtArtist->fetch(PDO::FETCH_ASSOC);

if (!$artistRow) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Artist profile not found.']);
    exit;
}

$artistId = (int) $artistRow['artist_id'];

// ── Parse query params ────────────────────────────────────────────────────────
$search       = trim($_GET['search']     ?? '');
$statusFilter = trim($_GET['status']     ?? '');
$dateStart    = trim($_GET['date_start'] ?? '');
$dateEnd      = trim($_GET['date_end']   ?? '');
$page         = max(1, (int) ($_GET['page']     ?? 1));
$perPage      = min(50, max(1, (int) ($_GET['per_page'] ?? 10)));
$offset       = ($page - 1) * $perPage;

// ── Build WHERE clauses ───────────────────────────────────────────────────────
$conditions = ['c.artist_id = :artist_id'];
$params     = [':artist_id' => $artistId];

if ($search !== '') {
    $searchInt = (int) preg_replace('/\D/', '', $search);
    if ($searchInt > 0) {
        $conditions[] = 't.transaction_id = :search_id';
        $params[':search_id'] = $searchInt;
    }
}

$allowedStatuses = ['Paid', 'Pending', 'Cancelled'];
if ($statusFilter !== '' && in_array($statusFilter, $allowedStatuses, true)) {
    $conditions[] = 'st.status_name = :status_filter';
    $params[':status_filter'] = $statusFilter;
}

if ($dateStart !== '') {
    $conditions[] = 'DATE(p.payment_date) >= :date_start';
    $params[':date_start'] = $dateStart;
}

if ($dateEnd !== '') {
    $conditions[] = 'DATE(p.payment_date) <= :date_end';
    $params[':date_end'] = $dateEnd;
}

$whereSQL = 'WHERE ' . implode(' AND ', $conditions);

// ── Base JOIN (shared by count + data queries) ────────────────────────────────
$baseJoin = "
    FROM payment_tbl p
    JOIN transaction_tbl    t   ON p.transaction_id    = t.transaction_id
    JOIN commission_tbl     c   ON t.commission_id      = c.commission_id
    JOIN payment_method_tbl pm  ON p.payment_method_id  = pm.payment_method_id
    JOIN status_tbl         st  ON p.status_id          = st.status_id
    -- client info
    JOIN user_tbl           u   ON c.user_id            = u.user_id
    JOIN account_tbl        ua  ON u.account_id         = ua.account_id
    -- category
    LEFT JOIN category_tbl  cat ON c.category_id        = cat.category_id
    $whereSQL
";

// ── Count total rows ──────────────────────────────────────────────────────────
$stmtCount = $db->prepare("SELECT COUNT(*) AS total $baseJoin");
$stmtCount->execute($params);
$totalRows  = (int) $stmtCount->fetchColumn();
$totalPages = (int) ceil($totalRows / $perPage);

// ── Fetch paginated rows ──────────────────────────────────────────────────────
$dataSQL = "
    SELECT
        p.payment_id,
        t.transaction_id,
        pm.payment_method_name,
        p.amount,
        st.status_name      AS payment_status,
        p.payment_date,
        c.commission_id,
        cat.category_name,
        ua.first_name       AS client_first_name,
        ua.last_name        AS client_last_name
    $baseJoin
    ORDER BY p.payment_date DESC
    LIMIT :limit OFFSET :offset
";

$stmtData = $db->prepare($dataSQL);
foreach ($params as $key => $val) {
    $stmtData->bindValue($key, $val);
}
$stmtData->bindValue(':limit',  $perPage, PDO::PARAM_INT);
$stmtData->bindValue(':offset', $offset,  PDO::PARAM_INT);
$stmtData->execute();
$rows = $stmtData->fetchAll(PDO::FETCH_ASSOC);

// ── Total earnings (Paid only) for this artist ────────────────────────────────
$stmtEarnings = $db->prepare("
    SELECT COALESCE(SUM(p.amount), 0)
    FROM payment_tbl p
    JOIN transaction_tbl t ON p.transaction_id = t.transaction_id
    JOIN commission_tbl  c ON t.commission_id  = c.commission_id
    WHERE c.artist_id = ? AND p.status_id = 10
");
$stmtEarnings->execute([$artistId]);
$totalEarnings = (float) $stmtEarnings->fetchColumn();

// ── Format output ─────────────────────────────────────────────────────────────
$transactions = array_map(function ($row) {
    return [
        'payment_id'        => (int)   $row['payment_id'],
        'transaction_id'    => (int)   $row['transaction_id'],
        'payment_method'    => $row['payment_method_name'],
        'amount'            => number_format((float) $row['amount'], 2),
        'amount_raw'        => (float) $row['amount'],
        'payment_status'    => $row['payment_status'],
        'payment_date'      => date('M d, Y', strtotime($row['payment_date'])),
        'payment_date_full' => date('M d, Y h:i A', strtotime($row['payment_date'])),
        'commission_id'     => (int)   $row['commission_id'],
        'category'          => $row['category_name'] ?? 'Uncategorized',
        'client_name'       => trim(($row['client_first_name'] ?? '') . ' ' . ($row['client_last_name'] ?? '')) ?: 'Unknown Client',
    ];
}, $rows);

echo json_encode([
    'success'        => true,
    'total_earnings' => number_format($totalEarnings, 2),
    'data'           => $transactions,
    'pagination'     => [
        'total_rows'   => $totalRows,
        'total_pages'  => $totalPages,
        'current_page' => $page,
        'per_page'     => $perPage,
    ],
]);