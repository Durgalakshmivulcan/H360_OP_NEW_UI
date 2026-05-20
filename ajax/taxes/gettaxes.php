
<?php

require_once("../../config/functions.php");

    $SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

?>
<table class="table" id="tabletax1" style="width:100%;">
<thead class="text-center">
    <tr>
        <th>S No</th>
        <?php
            if($SessionUserId=="1"){
        ?>
        <th>Organization Name</th>
        <?php
            }
        ?>
        <th>CGST Number</th>
        <th>SGST Number</th>
        <th>Percentage</th>
        <th class="action">Action</th>
    </tr>
</thead>
<tbody class="text-center">
    <?php
if($SessionUserId == "1" && $SessionRoleId=="1"){
    $gettax = mysqli_query($conn, "SELECT * FROM taxes WHERE status='1' ORDER BY tax_id DESC") or die(mysqli_error($conn));
} else{
    $gettax = mysqli_query($conn, "SELECT * FROM taxes WHERE status='1' AND org_id='$SessionOrgId' ORDER BY tax_id DESC") or die(mysqli_error($conn));  
}
    $i = 1;
    while($restaxes = mysqli_fetch_object($gettax)){
?>

    <tr>
        <td> <?=$i++;?> </td>
        <?php
            if($SessionUserId=="1"){
        ?>
        <td> <?=getUserNameByOrgId($conn, $restaxes->org_id)?> </td>
        <?php
            }
        ?>
        <td> <?=$restaxes->cgstNumber?> </td>
        <td> <?=$restaxes->sgstNumber?> </td>
        <td> <?=$restaxes->percentage?> </td>
        
        <td class="text-center">
            <?php if (userCan('edit', 'taxes.php')) { /* FIX_B_1810 */ ?><a class="has-icon me-3" style="cursor:pointer;"
            onclick='edittaxes(`<?=$restaxes->tax_id?>`,`<?=$restaxes->cgstNumber?>`,`<?=$restaxes->sgstNumber?>`,`<?=$restaxes->percentage?>`,`<?=$restaxes->org_id?>`)'>
                <i class="fa fa-edit fa-lg"></i> 
            </a><?php } ?>
            <?php if (userCan('delete', 'taxes.php')) { /* FIX_B_1810 */ ?><a class="has-icon text-danger" style="cursor:pointer;"
            onclick="deletetaxes('<?=$restaxes->tax_id?>', '<?=$restaxes->cgstNumber?>')">
                <i class="fa fa-trash fa-lg"></i> 
            </a><?php } ?>
        </td>

    </tr>
    <?php } ?>

</tbody>
</table>














