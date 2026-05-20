<?php
require_once(__DIR__ . "/sa_guard.php");

$limit = isset($_GET['limit']) ? max(1, min(50, intval($_GET['limit']))) : 10;

/* Governance-relevant entities: roles, menus, role_menus, doctors, security */
$sql = "SELECT a.id, a.org_id, a.user_id, a.module, a.action, a.entity, a.entity_id, a.ts,
               s.admin_name,
               o.organization_name
        FROM audit_log a
        LEFT JOIN security     s ON s.security_id = a.user_id
        LEFT JOIN organization o ON o.org_id      = a.org_id
        WHERE a.entity IN ('roles','role_menus','menus','doctors','security','organization')
        ORDER BY a.ts DESC, a.id DESC
        LIMIT $limit";
$res = mysqli_query($conn, $sql);

$rows = [];
$entityHref = [
    'roles'        => 'roles.php',
    'role_menus'   => 'roles.php',
    'menus'        => 'menus.php',
    'doctors'      => 'doctor.php',
    'security'     => 'registration.php',
    'organization' => 'organization.php',
];
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $href = $entityHref[$r['entity']] ?? 'audit_log.php';
        $rows[] = [
            'id'        => (int) $r['id'],
            'ts'        => $r['ts'],
            'user_name' => $r['admin_name'] ?: ('User #' . (int)$r['user_id']),
            'org_name'  => $r['organization_name'] ?: '—',
            'module'    => $r['module'],
            'action'    => $r['action'],
            'entity'    => $r['entity'],
            'entity_id' => $r['entity_id'] !== null ? (int) $r['entity_id'] : null,
            'href'      => $href,
        ];
    }
}

echo json_encode([
    'rows'  => $rows,
    'count' => count($rows),
    'generated_at' => date('c'),
]);
