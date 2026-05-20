<?php
require_once("../../config/functions.php");
// FIX_B_032: gate menu mutators behind admin/org_admin role.
require_once(__DIR__ . '/../../include/auth_guard.php');
assertRole(['admin','org_admin','1']);

$msg = 0;

$menu_id = $_POST['menu_id'];

// FIX_B_1850: layered per-action RBAC on top of the FIX_B_032 role gate.
// Empty menu_id → 'add'; non-empty → 'edit'. SA bypass preserved by
// userCan().
requireCan(empty($menu_id) ? 'add' : 'edit', 'menus.php', 'ajax');
$menu_name = $_POST['menu_name'];
$menu_type = $_POST['menu_type'];
$menu_order = $_POST['menu_order'];
$menu_web_url = $_POST['menu_web_url'];
$parent_id = $_POST['parent_id'];
$web_class_name = $_POST['web_class_name'];
$web_icon = $_POST['web_icon'];
$menu_access = $_POST['menu_access'];

if($menu_name != "" && $menu_type != "" && $menu_order != "" ) {
    if($menu_id != "") {
        $UpdateMenuData = mysqli_query($conn, "UPDATE menus SET menu_name='$menu_name', menu_type='$menu_type', menu_order='$menu_order', menu_web_url='$menu_web_url', parent_id='$parent_id', web_class_name='$web_class_name', web_icon='$web_icon', menu_access='$menu_access', modified_by='$SessionUserId' WHERE menu_id='$menu_id'") or die(mysqli_error($conn));
        if($UpdateMenuData) {
            $msg = 2;
        }
    } else {
        $InserMenuData = mysqli_query($conn, "INSERT INTO menus(menu_name, menu_type, menu_order, status, menu_web_url, parent_id, create_date_time, web_class_name, web_icon, menu_access, created_by, modified_by) VALUES ('$menu_name', '$menu_type', '$menu_order', '1', '$menu_web_url', '$parent_id', '$datetime', '$web_class_name', '$web_icon', '$menu_access', '$SessionUserId', '$SessionUserId')") or die(mysqli_error($conn));
        if($InserMenuData) {
            $msg = 1;
        }
    }
}

echo $msg;