<?php

require_once("../../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';
ensureUserCodeColumn($conn);
?>
<table class="table" id="tableregis1" style="width:100%;">
<thead class="text-center">
    <tr>
        <th>S No</th>
        <th>User Name</th>
        <th>Email</th>
        <th>Contact</th>
        <th>Role</th>
        <th>User Code</th>
        <?php
if($SessionUserId == "1"){
        ?>
        <th>Organization</th>
        <?php
}
        ?>
        <th class="action">Action</th>
    </tr>
</thead>
<tbody class="text-center">

<?php
/* FIX_B_2330: hide every Super Admin record (role_id=1) from the user-list,
   regardless of who is viewing. The SA account is bootstrap-only — it must
   not be discoverable, edited, deleted, or password-reset through the UI.
   Filter is on role_id AND security_type='SA' as a belt-and-braces guard. */
if($SessionUserId == "1"){
    $getregis = mysqli_query($conn, "SELECT * FROM security
                                       WHERE status='1'
                                         AND role_id <> 1
                                         AND security_type <> 'SA'
                                       ORDER BY security_id DESC") or die(mysqli_error($conn));
} else{
    $getregis = mysqli_query($conn, "SELECT * FROM security
                                       WHERE status='1'
                                         AND org_id='$SessionOrgId'
                                         AND role_id <> 1
                                         AND security_type <> 'SA'
                                       ORDER BY security_id DESC") or die(mysqli_error($conn));
}
$i = 1;

while($resRegis = mysqli_fetch_object($getregis)){
    $getorg= mysqli_query($conn, "SELECT * FROM organization WHERE status='1' AND org_id='$resRegis->org_id'");
    $resorg=mysqli_fetch_object($getorg);

    $getrole=mysqli_query($conn,"SELECT * FROM roles WHERE status='1' AND role_id='$resRegis->role_id'");
    $resrole=mysqli_fetch_object($getrole);
?>

    <tr>
        <td> <?=$i++;?> </td>
        <td> <?=$resRegis->admin_name?> </td>
        <td> <?=$resRegis->email?> </td>
        <td> <?=$resRegis->contact?> </td>
        <td> <?=$resrole->role_name?> </td>
        <td><?php
            $uc = $resRegis->user_code ?? '';
            echo $uc ? '<span style="background:#0d6efd;color:#fff;padding:2px 10px;border-radius:20px;font-size:12px;">' . htmlspecialchars($uc) . '</span>' : '-';
        ?></td>
        <?php
        if($SessionUserId == "1"){
                ?>
                <td> <?=$resorg->organization_name?></td>
                <?php
        }
        ?>
        <td class="text-center">
            <a class="has-icon " style="cursor:pointer;margin-right: 20px;" 
               onclick='editregis(`<?=$resRegis->security_id?>`,`<?=$resRegis->admin_name?>`,`<?=$resRegis->email?>`,`<?=$resRegis->contact?>`,`<?=$resRegis->security_password?>`,`<?=$resRegis->role_id?>`,`<?=$resRegis->org_id?>`,`<?=(int)($resRegis->can_switch_doctor ?? 0)?>`)'>
               <i class="fa fa-edit fa-lg"></i>
            </a>
            <a class="has-icon text-danger" style="cursor:pointer;" 
               onclick="deleteregis('<?=$resRegis->security_id?>', '<?=$resRegis->admin_name?>')">
               <i class="fa fa-trash fa-lg"></i>
            </a>
        </td>
    </tr>
    <?php } ?>
    <?php

//     ?>

   <?php
// }
// ?>

</tbody>
</table>














