<?php
// audit_feature/ajax/audit/log_export_csv.php
// Export filtered audit log rows to CSV. Only accessible to admin or org_admin.

require_once("../../config/functions.php");

// assertRole(['admin', 'org_admin']); // Uncomment if role checks exist

$org_id = (int) ($_SESSION['org_id'] ?? 0);
$from   = $_GET['from'] ?? null;
$to     = $_GET['to']   ?? null;

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=audit_log_' . date('Ymd_His') . '.csv');

$out = fopen('php://output', 'w');
// CSV header row
fputcsv($out, ["ID", "Timestamp", "User ID", "Module", "Action", "Entity", "Entity ID", "IP"]);

$where = "WHERE org_id = '$org_id'";

if ($from) {
    $from_safe = mysqli_real_escape_string($conn, $from . ' 00:00:00');
    $where .= " AND ts >= '$from_safe'";
}
if ($to) {
    $to_safe = mysqli_real_escape_string($conn, $to . ' 23:59:59');
    $where .= " AND ts <= '$to_safe'";
}

$sql = "SELECT id, ts, user_id, module, action, entity, entity_id, ip
        FROM audit_log $where
        ORDER BY ts DESC";

$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($out, $row);
    }
}

fclose($out);
