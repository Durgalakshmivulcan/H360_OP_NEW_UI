<?php
require_once("../../config/functions.php");
// FIX_B_032: gate menu mutators behind admin/org_admin role.
require_once(__DIR__ . '/../../include/auth_guard.php');
assertRole(['admin','org_admin','1']);

// FIX_B_1850: layered per-action RBAC. Only roles with 'delete' on menus.php
// may soft-delete a menu. SA bypass preserved by userCan().
requireCan('delete', 'menus.php', 'ajax');

$langDeleted = 0;

$delete_id = $_POST['menu_id'];
$qryDelete = mysqli_query($conn,"UPDATE menus SET status='0' WHERE menu_id ='$delete_id'") or die(mysqli_error($conn));
if($qryDelete) {
    $langDeleted = 1;
}

echo $langDeleted;
