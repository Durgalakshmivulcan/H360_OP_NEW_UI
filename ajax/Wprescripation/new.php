<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

/* B-1830 RBAC */ requireCan(empty($_REQUEST['prescription_id']) ? 'add' : 'edit', 'prescription.php', 'ajax');

$getAdminMenus = mysqli_query($conn, "SELECT * FROM Announcements WHERE status='1' ORDER BY menu_id DESC") or die(mysqli_error($conn));
$i = 1;
while($resAdminMenus = mysqli_fetch_object($getAdminMenus)){
?>
    <tr>
        <td> <?=$i++;?> </td>
        <td> <?=$resAdminMenus->Announcements?></td>
        <td> <?=$resAdminMenus->date?></td>
        <td class="text-center">
            <ul class="navbar-nav">
                <li class="dropdown">
                    <a href="#" data-bs-toggle="dropdown" class="nav-link" style="color:black">
                        <i class="fa fa-ellipsis-v"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-left">
                        <a href="#" class="dropdown-item has-icon" onclick='editP(`<?=$resAdminMenus->menu_id?>`, `<?=$resAdminMenus->Announcements ?>`,`<?=$resAdminMenus->date ?>`,)'>
                         <i class="fa fa-edit"></i> Update</a>

                        <a class="dropdown-item has-icon" style="cursor:pointer;" onclick="deleteP('<?=$resAdminMenus->menu_id?>', '<?=$resAdminMenus->Announcements ?>')"> <i class="fa fa-trash"></i> Delete</a>
                    </div>
                </li>
            </ul>
        </td>
    </tr>
<?php } ?>







