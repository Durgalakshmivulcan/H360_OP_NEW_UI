<?php
require_once("../../config/functions.php");

// FIX_B_1850: per-action RBAC on change_passowrd.php. The UPDATE is bound to
// $SessionUserId so a user can only change their own password. SA bypass
// preserved by userCan().
requireCan('edit', 'change_passowrd.php', 'ajax');

$msg = 0;

$old_password = md5($_POST['old_password']);
$new_password = md5($_POST['new_password']);
$confirm_password = md5($_POST['confirm_password']);


$qryCheckOldPassword = mysqli_query($conn, "SELECT * FROM security WHERE security_password='$old_password' AND security_id='$SessionUserId'") or die(mysqli_error($conn));
$countCheck = mysqli_num_rows($qryCheckOldPassword);

if($countCheck == 1) {
    
    $UpdateMenuData = mysqli_query($conn, "UPDATE security SET security_password='$new_password' WHERE security_id='$SessionUserId'") or die(mysqli_error($conn));
    if($UpdateMenuData) {
        $msg = 1;
    }
} else {
    $msg = 2;
}


echo trim($msg);
exit;