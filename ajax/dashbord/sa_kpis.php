<?php
require_once(__DIR__ . "/sa_guard.php");

/* ---------- KPI 1: active organizations ---------- */
$activeOrgs = (int) mysqli_fetch_array(mysqli_query(
    $conn,
    "SELECT COUNT(*) FROM organization WHERE status='1'"
))[0];

$totalOrgs = (int) mysqli_fetch_array(mysqli_query(
    $conn,
    "SELECT COUNT(*) FROM organization"
))[0];

/* ---------- KPI 2: active users (with role breakdown for sparkline) ---------- */
$activeUsers = (int) mysqli_fetch_array(mysqli_query(
    $conn,
    "SELECT COUNT(*) FROM security WHERE status='1'"
))[0];

$roleBreak = [];
$rb = mysqli_query(
    $conn,
    "SELECT r.role_name, COUNT(s.security_id) AS c
     FROM security s
     LEFT JOIN roles r ON r.role_id = s.role_id
     WHERE s.status='1'
     GROUP BY s.role_id
     ORDER BY c DESC
     LIMIT 8"
);
while ($r = mysqli_fetch_assoc($rb)) {
    $roleBreak[] = [
        'label' => $r['role_name'] ?: ('role#' . 0),
        'count' => (int) $r['c'],
    ];
}

/* ---------- KPI 3: audit-log activity in last 24h vs prior 24h ---------- */
$audit24 = (int) mysqli_fetch_array(mysqli_query(
    $conn,
    "SELECT COUNT(*) FROM audit_log WHERE ts >= (NOW() - INTERVAL 24 HOUR)"
))[0];

$auditPrev24 = (int) mysqli_fetch_array(mysqli_query(
    $conn,
    "SELECT COUNT(*) FROM audit_log
     WHERE ts >= (NOW() - INTERVAL 48 HOUR)
       AND ts <  (NOW() - INTERVAL 24 HOUR)"
))[0];

$auditDelta = $auditPrev24 > 0
    ? round((($audit24 - $auditPrev24) / $auditPrev24) * 100, 1)
    : ($audit24 > 0 ? 100.0 : 0.0);

/* ---------- KPI 4: active doctors across all orgs ---------- */
$activeDoctors = (int) mysqli_fetch_array(mysqli_query(
    $conn,
    "SELECT COUNT(*) FROM doctors WHERE status='1'"
))[0];

/* ---------- KPI 5: active roles + menus mapped ---------- */
$activeRoles = (int) mysqli_fetch_array(mysqli_query(
    $conn,
    "SELECT COUNT(*) FROM roles WHERE status='1'"
))[0];

$mappedMenus = (int) mysqli_fetch_array(mysqli_query(
    $conn,
    "SELECT COUNT(DISTINCT menu_id) FROM role_menus"
))[0];

/* ---------- KPI 6: DB health (fast row-counts of major tables) ---------- */
$tables = [
    'organization', 'security', 'roles', 'menus', 'doctors',
    'appointment_online', 'audit_log',
];
$dbHealth = [];
foreach ($tables as $t) {
    // information_schema is fast and avoids full COUNT(*) on large tables
    $q = mysqli_query(
        $conn,
        "SELECT TABLE_ROWS FROM information_schema.TABLES
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '" . mysqli_real_escape_string($conn, $t) . "'"
    );
    $row = $q ? mysqli_fetch_array($q) : null;
    $approx = $row ? (int) $row[0] : 0;
    // For the canonical small tables, prefer exact count
    if (in_array($t, ['organization', 'roles', 'menus'], true)) {
        $exact = mysqli_fetch_array(mysqli_query(
            $conn,
            "SELECT COUNT(*) FROM `" . mysqli_real_escape_string($conn, $t) . "`"
        ));
        if ($exact) $approx = (int) $exact[0];
    }
    $dbHealth[] = ['table' => $t, 'rows' => $approx];
}

echo json_encode([
    'orgs' => [
        'active' => $activeOrgs,
        'total'  => $totalOrgs,
        'href'   => 'org_reports.php',
    ],
    'users' => [
        'active'   => $activeUsers,
        'by_role'  => $roleBreak,
        'href'     => 'registration.php',
    ],
    'audit' => [
        'last_24h'  => $audit24,
        'prev_24h'  => $auditPrev24,
        'delta_pct' => $auditDelta,
        'href'      => 'audit_log.php',
    ],
    'doctors' => [
        'active' => $activeDoctors,
        'href'   => 'doctor.php',
    ],
    'governance' => [
        'roles' => $activeRoles,
        'menus' => $mappedMenus,
        'href'  => 'roles.php',
    ],
    'db_health' => [
        'tables' => $dbHealth,
        'server_now' => date('c'),
    ],
    'generated_at' => date('c'),
]);
