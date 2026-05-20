<?php
/**
 * Super Admin guard for SA-only AJAX endpoints.
 * Loaded by sa_kpis.php / sa_audit_feed.php / sa_doctor_heatmap.php / sa_governance_changes.php / sa_db_health.php.
 *
 * Establishes:
 *   - $conn (MySQLi)  via config/functions.php
 *   - $SessionUserId, $SessionRoleId, $SessionOrgId
 *
 * Rejects with HTTP 403 if the caller is not a Super Admin (role_id=1, org_id=0).
 */
require_once(__DIR__ . "/../../config/functions.php");

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

$SessionUserId = isset($_SESSION['security_id']) ? intval($_SESSION['security_id']) : 0;
$SessionRoleId = isset($_SESSION['role_id'])     ? intval($_SESSION['role_id'])     : 0;
$SessionOrgId  = isset($_SESSION['org_id'])      ? intval($_SESSION['org_id'])      : -1;

if ($SessionUserId <= 0) {
    http_response_code(401);
    echo json_encode(['error' => 'unauthenticated']);
    exit;
}

// SA convention in this codebase: role_id = 1 AND org_id = 0 (see security row for superadmin@gmail.com).
if ($SessionRoleId !== 1) {
    http_response_code(403);
    echo json_encode(['error' => 'forbidden', 'reason' => 'super_admin_only']);
    exit;
}
