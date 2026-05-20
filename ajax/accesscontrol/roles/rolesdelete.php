<?php
require_once("../../../config/config.php");
require_once("../../../config/functions.php");

// FIX_B_1850: per-action RBAC — only roles with 'delete' on roles.php may
// soft-delete a role. SA bypass preserved by userCan().
requireCan('delete', 'roles.php', 'ajax');

$langDeleted = 0;

$delete_id = $_POST['role_id'];
// FIX_B_031: org-scope guard for role + role_menus.
$SessionOrgId = $_SESSION['org_id'] ?? '';
$beforeQuery = mysqli_query($conn, "SELECT * FROM roles WHERE role_id='$delete_id' LIMIT 1");
    $before      = null;
    if ($beforeQuery && mysqli_num_rows($beforeQuery) > 0) {
        $before = mysqli_fetch_assoc($beforeQuery);
    }
$roleDelete = mysqli_query($conn,"UPDATE roles SET status='0' WHERE role_id ='$delete_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));

$deletePrevRoles = mysqli_query($conn, "DELETE FROM role_menus WHERE role_id='$delete_id' AND role_id IN (SELECT role_id FROM roles WHERE org_id='$SessionOrgId')") or die(mysqli_error($conn));
$after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM roles WHERE role_id='$delete_id'"));

     audit_log($conn, "Roles", "delete", "roles", $delete_id, $before, $after);
if($roleDelete) {
    $langDeleted = 1;
}

echo $langDeleted;







