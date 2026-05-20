<?php
require_once("../../../config/config.php");
require_once("../../../config/functions.php");

// FIX_B_1850: per-action RBAC — only roles with 'delete' on registration.php
// may soft-delete users. SA bypass preserved by userCan().
requireCan('delete', 'registration.php', 'ajax');

$langDeleted = 0;

$delete_id = $_POST['security_id'];
// FIX_B_030: org-scope guard.
$SessionOrgId = $_SESSION['org_id'] ?? '';
$before = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM security WHERE security_id ='$delete_id'"));
// FIX_B_2330: hard server-side block on deleting any Super Admin record,
// regardless of who is asking. SA is bootstrap-only; losing it would brick
// access to org/role/menu management entirely. Belt-and-braces: role_id=1
// AND security_type='SA' AND id=1 are all rejected.
if ($before && ((int)($before['role_id'] ?? 0) === 1 || ($before['security_type'] ?? '') === 'SA')) {
    http_response_code(403); echo '0'; exit;
}
$qryDelete = mysqli_query($conn,"UPDATE security SET status='0' WHERE security_id ='$delete_id' AND org_id='$SessionOrgId' AND role_id <> 1 AND security_type <> 'SA'") or die(mysqli_error($conn));
if($qryDelete) {
    $langDeleted = 1;
    $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM security WHERE security_id='$delete_id'"));

     audit_log($conn, "Security", "delete", "security", $delete_id, $before, $after);
}

echo $langDeleted;










