<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';
?>
<table class="table" id="tableExport1" style="width:100%;">
<thead class="text-center">
    <tr>
        <th> S.No </th>
        <?php
        if($SessionUserId=="1"){
        ?>
        <th> Organization Name</th>
        <?php
        }
        ?>
        <th> Specializations </th>
        <th> Action </th>
    </tr>
</thead>
<tbody id="" class="text-center">


<?php
if($SessionUserId == "1" && $SessionRoleId=="1"){
    $getAdminconsult = mysqli_query($conn, "SELECT * FROM specialtis WHERE status='1' ORDER BY specialtis_id DESC") or die(mysqli_error($conn));
} else{
    $getAdminconsult = mysqli_query($conn, "SELECT * FROM specialtis WHERE status='1' AND org_id='$SessionOrgId' ORDER BY specialtis_id DESC") or die(mysqli_error($conn));
}
$i = 1;
while($resAdminconsult = mysqli_fetch_object($getAdminconsult)){
?>
    <tr>
        <td> <?=$i++;?> </td>
        <?php
        if($SessionUserId=="1"){
        ?>
        <td> <?=getUserNameByOrgId($conn, $resAdminconsult->org_id)?> </td>
        <?php
        }
        ?>
        <td> <?=$resAdminconsult->specialtisname?> </td>
      
        <td class="text-center">
            <?php if (userCan('edit', 'Specialization.php')) { /* FIX_B_1810 */ ?><a class="has-icon me-3" style="cursor:pointer;"
            onclick='edit(`<?=$resAdminconsult->specialtis_id?>`,`<?=$resAdminconsult->specialtisname ?>`,`<?=$resAdminconsult->org_id ?>`)'>
                <i class="fa fa-edit fa-lg"></i> 
            </a><?php } ?>
            <?php if (userCan('delete', 'Specialization.php')) { /* FIX_B_1810 */ ?><a class="has-icon text-danger" style="cursor:pointer;"
            onclick="deletespecialtis('<?=$resAdminconsult->specialtis_id?>', '<?=$resAdminconsult->specialtisname ?>')">
                <i class="fa fa-trash fa-lg"></i>
            </a><?php } ?>
        </td>
    </tr>
<?php } ?>
</tbody>                            
</table>







