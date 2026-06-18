<?php

/**
 * GET /api/payments/fetch_payment_history.php
 *
 * Returns paginated payment records for the currently logged-in user (role = User).
 *
 * Query params (all optional):
 *   search      (string)  — filter by transaction ID prefix (digits only)
 *   status      (string)  — filter by payment status name: Paid | Pending | Cancelled | (empty = all)
 *   date_start  (string)  — YYYY-MM-DD
 *   date_end    (string)  — YYYY-MM-DD
 *   page        (int)     — 1-based page number (default 1)
 *   per_page    (int)     — rows per page (default 10, max 50)
 */

require_once '../../config/constants.php';
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// ── Auth: only logged-in users may call this ─────────────────────────────────
if (empty($_SESSION['account_id']) || strtolower($_SESSION['role'] ?? '') !== 'user') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$accountId = (int) $_SESSION['account_id'];
$db        = getDB();

// ── Resolve user_id ───────────────────────────────────────────────────────────
$stmtUser = $db->prepare('SELECT user_id FROM user_tbl WHERE account_id = ? LIMIT 1');
$stmtUser->execute([$accountId]);
$userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$userRow) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'User profile not found.']);
    exit;
}

$userId = (int) $userRow['user_id'];

// ── Parse query params ────────────────────────────────────────────────────────
$search    = trim($_GET['search']     ?? '');
$statusFilter = trim($_GET['status'] ?? '');           // e.g. "Paid", "Pending", "Cancelled"
$dateStart = trim($_GET['date_start'] ?? '');
$dateEnd   = trim($_GET['date_end']   ?? '');
$page      = max(1, (int) ($_GET['page']     ?? 1));
$perPage   = min(50, max(1, (int) ($_GET['per_page'] ?? 10)));
$offset    = ($page - 1) * $perPage;

// ── Build WHERE clauses ───────────────────────────────────────────────────────
$conditions = ['c.user_id = :user_id'];
$params     = [':user_id' => $userId];

if ($search !== '') {
    // Allow searching by numeric transaction ID
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

// ── Count total rows (for pagination) ────────────────────────────────────────
$countSQL = "
    SELECT COUNT(*) AS total
    FROM payment_tbl p
    JOIN transaction_tbl  t   ON p.transaction_id    = t.transaction_id
    JOIN commission_tbl   c   ON t.commission_id      = c.commission_id
    JOIN payment_method_tbl pm ON p.payment_method_id = pm.payment_method_id
    JOIN status_tbl        st ON p.status_id          = st.status_id
    -- artist name
    LEFT JOIN artist_tbl   a   ON c.artist_id = a.artist_id
    LEFT JOIN account_tbl  aa  ON a.account_id = aa.account_id
    -- category
    LEFT JOIN category_tbl cat ON c.category_id = cat.category_id
    $whereSQL
";

$stmtCount = $db->prepare($countSQL);
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
        aa.first_name       AS artist_first_name,
        aa.last_name        AS artist_last_name
    FROM payment_tbl p
    JOIN transaction_tbl   t   ON p.transaction_id    = t.transaction_id
    JOIN commission_tbl    c   ON t.commission_id      = c.commission_id
    JOIN payment_method_tbl pm ON p.payment_method_id = pm.payment_method_id
    JOIN status_tbl         st ON p.status_id          = st.status_id
    LEFT JOIN artist_tbl    a   ON c.artist_id = a.artist_id
    LEFT JOIN account_tbl   aa  ON a.account_id = aa.account_id
    LEFT JOIN category_tbl  cat ON c.category_id = cat.category_id
    $whereSQL
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

// ── Format output ─────────────────────────────────────────────────────────────
$payments = array_map(function ($row) {
    return [
        'payment_id'         => (int)   $row['payment_id'],
        'transaction_id'     => (int)   $row['transaction_id'],
        'payment_method'     => $row['payment_method_name'],
        'amount'             => number_format((float) $row['amount'], 2),
        'amount_raw'         => (float) $row['amount'],
        'payment_status'     => $row['payment_status'],
        'payment_date'       => date('M d, Y', strtotime($row['payment_date'])),
        'payment_date_full'  => date('M d, Y h:i A', strtotime($row['payment_date'])),
        'commission_id'      => (int)   $row['commission_id'],
        'category'           => $row['category_name'] ?? 'Uncategorized',
        'artist_name'        => trim(($row['artist_first_name'] ?? '') . ' ' . ($row['artist_last_name'] ?? '')) ?: 'Unknown Artist',
    ];
}, $rows);

echo json_encode([
    'success'     => true,
    'data'        => $payments,
    'pagination'  => [
        'total_rows'   => $totalRows,
        'total_pages'  => $totalPages,
        'current_page' => $page,
        'per_page'     => $perPage,
    ],
]);