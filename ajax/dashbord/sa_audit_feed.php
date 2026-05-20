<?php
require_once(__DIR__ . "/sa_guard.php");

$limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 20;

$sql = "SELECT a.id, a.org_id, a.user_id, a.module, a.action, a.entity, a.entity_id, a.ts, a.ip,
               s.admin_name,
               o.organization_name
        FROM audit_log a
        LEFT JOIN security     s ON s.security_id = a.user_id
        LEFT JOIN organization o ON o.org_id      = a.org_id
        ORDER BY a.ts DESC, a.id DESC
        LIMIT $limit";
$res = mysqli_query($conn, $sql);

$rows = [];
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $rows[] = [
            'id'        => (int) $r['id'],
            'org_id'    => (int) $r['org_id'],
            'org_name'  => $r['organization_name'] ?: ('Org #' . (int)$r['org_id']),
            'user_id'   => (int) $r['user_id'],
            'user_name' => $r['admin_name'] ?: ('User #' . (int)$r['user_id']),
            'module'    => $r['module'],
            'action'    => $r['action'],
            'entity'    => $r['entity'],
            'entity_id' => $r['entity_id'] !== null ? (int) $r['entity_id'] : null,
            'ts'        => $r['ts'],
            'ip'        => $r['ip'],
        ];
    }
}

echo json_encode([
    'rows'         => $rows,
    'count'        => count($rows),
    'generated_at' => date('c'),
]);
