<?php
require_once("../../config/functions.php");

$getAdminMenus = mysqli_query($conn, "SELECT * FROM menus WHERE status='1' ORDER BY menu_id DESC") or die(mysqli_error($conn));
$i = 1;
while($resAdminMenus = mysqli_fetch_object($getAdminMenus)){
?>
    <tr>
        <td> <?=$i++;?> </td>
        <td> <?=$resAdminMenus->menu_name?> </td>
        <td> <?=($resAdminMenus->menu_type == 'p')?"Parent":"Sub"?> </td>
        <td> <?=$resAdminMenus->menu_order?> </td>
        <td> <?=$resAdminMenus->menu_web_url?> </td>
        <td> <?=getMenuNameById($conn, $resAdminMenus->parent_id)?> </td>
        <td> <?=$resAdminMenus->web_class_name?> </td>
        <td> <?=$resAdminMenus->web_icon?> </td>
        <td>
        <?= $resAdminMenus->menu_access == 1 ? 'Allow' : 'Deny'; ?>
        </td>
        <td> <?=getUserNameById($conn, $resAdminMenus->created_by)?> </td>
        <td class="text-center">
            <a href="#" class="has-icon" onclick='editMenus(`<?=$resAdminMenus->menu_id?>`, `<?=$resAdminMenus->menu_name ?>`, `<?=$resAdminMenus->menu_type ?>`, `<?=$resAdminMenus->menu_order ?>`, `<?=$resAdminMenus->menu_web_url ?>`, `<?=$resAdminMenus->parent_id ?>`, `<?=$resAdminMenus->web_class_name ?>`, `<?=$resAdminMenus->web_icon ?>`, `<?=$resAdminMenus->menu_access ?>`)'> <i class="fa fa-edit"></i></a>
            <a class="text-danger has-icon" style="cursor:pointer;" onclick="deleteMenus('<?=$resAdminMenus->menu_id?>', '<?=$resAdminMenus->menu_name ?>', '<?=$resAdminMenus->menu_access ?>')"> <i class="fa fa-trash"></i></a>
        </td>
    </tr>
<?php } ?>







