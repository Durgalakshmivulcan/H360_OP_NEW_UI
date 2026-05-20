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
            <th> Organization Name </th>
            <?php
                }
            ?>
            <th> Service Name </th>
            <th> Price </th>
            <th> GST </th>
            <th> Action </th>
        </tr>
    </thead>
    <tbody  class="text-center">
                        
    <?php
    if($SessionUserId == "1" && $SessionRoleId=="1"){
        $getAdminconsult = mysqli_query($conn, "SELECT * FROM services WHERE status='1' ORDER BY service_id DESC") or die(mysqli_error($conn));
    } else{
        $getAdminconsult = mysqli_query($conn, "SELECT * FROM services WHERE status='1' AND org_id='$SessionOrgId' ORDER BY service_id DESC") or die(mysqli_error($conn));
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
            <td> <?=$resAdminconsult->service_name?> </td>
            <td> <?=$resAdminconsult->price?> </td>
            <td> <?=$resAdminconsult->service_GST?> %</td>

            <td class="text-center">
                <?php if (userCan('edit', 'services.php')) { /* FIX_B_1810 */ ?><a href="#" class="has-icon me-3" onclick='editservices(`<?=$resAdminconsult->service_id?>`,`<?=$resAdminconsult->service_name ?>`, `<?=$resAdminconsult->price?>`, `<?=$resAdminconsult->service_GST?>`,`<?=$resAdminconsult->org_id?>`)'> <i class="fa fa-edit fa-lg"></i> </a><?php } ?>
                <?php if (userCan('delete', 'services.php')) { /* FIX_B_1810 */ ?><a class="text-danger has-icon" style="cursor:pointer;" onclick="deleteservices('<?=$resAdminconsult->service_id?>', '<?=$resAdminconsult->service_name ?>')"> <i class="fa fa-trash fa-lg"></i> </a><?php } ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>                            
</table>






