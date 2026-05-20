<?php
// B-2050 Accountant dashboard: last 5 finance-relevant audit log events
require_once("../../config/functions.php");
header('Content-Type: application/json');

$SessionRoleId = (int)($_SESSION['role_id'] ?? 0);
$SessionOrgId  = (int)($_SESSION['org_id'] ?? 0);

if ($SessionRoleId === 0 || $SessionOrgId === 0) {
    http_response_code(401);
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$org = (int)$SessionOrgId;

$q = mysqli_query($conn,
    "SELECT al.id, al.module, al.action, al.entity, al.entity_id, al.ts,
            COALESCE(s.admin_name,'system') AS user_name
     FROM audit_log al
     LEFT JOIN security s ON s.security_id = al.user_id
     WHERE al.org_id='$org'
     AND al.entity IN ('invoice','patient_test_billing','refund','payment','bill')
     ORDER BY al.ts DESC
     LIMIT 5");

$rows = [];
if ($q) {
    while ($r = mysqli_fetch_assoc($q)) {
        $rows[] = [
            'id'        => (int)$r['id'],
            'module'    => $r['module'],
            'action'    => $r['action'],
            'entity'    => $r['entity'],
            'entity_id' => $r['entity_id'] !== null ? (int)$r['entity_id'] : null,
            'when'      => $r['ts'],
            'user_name' => $r['user_name'],
        ];
    }
}

echo json_encode(['rows' => $rows, 'count' => count($rows)]);
