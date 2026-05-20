<?php
require_once("../../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';
?>

<table class="table" id="tableroles1" style="width:100%;">
<thead class="text-center">
    <tr>
        <th>S No</th>
        <th>Role Name</th>
        <th class="action">Action</th>
    </tr>
</thead>
<tbody class="text-center">
<?php
if($SessionUserId == "1" && $SessionRoleId=="1"){
    $getroles = mysqli_query($conn, "SELECT * FROM roles WHERE status='1' ORDER BY role_id DESC") or die(mysqli_error($conn));
} else{
    $getroles = mysqli_query($conn, "SELECT * FROM roles WHERE status='1' AND org_id='$SessionOrgId'  ORDER BY role_id DESC") or die(mysqli_error($conn));
}
$i = 1;

while($resroles = mysqli_fetch_object($getroles)){
    $resGetRoleMenus = mysqli_query($conn, "SELECT * FROM role_menus WHERE role_id= $resroles->role_id") or die(mysqli_error($conn));
    $rolemenuArray = array();
    $rolemenuAccess = array();
    $rolemenuPermissions = array(); // FIX_B_1801: menu_id → permissions string
    while($resrolemenus = mysqli_fetch_object($resGetRoleMenus)){
        $rolemenuArray[] = $resrolemenus->menu_id;
        $rolemenuAccess[] = $resrolemenus->menu_access;
        $rolemenuPermissions[$resrolemenus->menu_id] =
            isset($resrolemenus->permissions) ? $resrolemenus->permissions : '';
    }
?>
    <tr>
        <td> <?=$i++;?> </td>
        <td> <?=$resroles->role_name?></td>

        <td class="text-center">
            <a class="has-icon " style="cursor:pointer;margin-right: 20px;"
            onclick='editroles(`<?=$resroles->role_id?>`,`<?=$resroles->role_name?>`,<?=json_encode($rolemenuArray)?>,<?=json_encode($rolemenuAccess)?>,<?=json_encode($rolemenuPermissions)?>)'>
                <i class="fa fa-edit fa-lg"></i>
            </a>
            <a class="has-icon text-danger" style="cursor:pointer;" 
            onclick="deleteroles('<?=$resroles->role_id?>', '<?=$resroles->role_name?>')">
                <i class="fa fa-trash fa-lg"></i>
            </a>
        </td>
    </tr>
<?php } ?>   
</tbody>
</table>












