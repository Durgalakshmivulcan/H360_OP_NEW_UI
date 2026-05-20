<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';
?>

<table class="table" id="tableExportmedicine" style="width:100%;">
    <thead class="text-center">
        <tr>
            <th> S.No </th>
            <?php if($SessionUserId == "1"){ ?>
            <th> Organization Name </th>
            <?php } ?>
            <th> RX-Group Name </th>
            <th> Action </th>
        </tr>
    </thead>
    <tbody style="text-align:center">
<?php
if($SessionUserId == "1" && $SessionRoleId=="1"){
    $getAdminRx_groups = mysqli_query($conn, "SELECT * FROM rx_groups_names WHERE status='1' ORDER BY rx_group_id DESC") or die(mysqli_error($conn));
} else{
    $getAdminRx_groups = mysqli_query($conn, "SELECT * FROM rx_groups_names WHERE status='1' AND org_id='$SessionOrgId' ORDER BY rx_group_id DESC") or die(mysqli_error($conn));
}

$i = 1;
while($getAdminRx_group = mysqli_fetch_object($getAdminRx_groups)){
    $medicines = json_decode($getAdminRx_group->medicine_detailes, true);
    $medicinesJson = htmlspecialchars(json_encode($medicines), ENT_QUOTES, 'UTF-8'); // Fix here
?>
    <tr>
        <td> <?=$i++;?> </td>
        <?php if($SessionUserId == "1"){ ?>
        <td> <?=getUserNameByOrgId($conn, $getAdminRx_group->org_id)?> </td>
        <?php } ?>
        <td> <?=$getAdminRx_group->rx_group_name?> </td>
        <td class="text-center">
            <?php if (userCan('edit', 'rxgroup.php')) { /* FIX_B_1810 */ ?><a class="has-icon me-3" style="cursor:pointer;" 
               onclick='editMedicine(
                   "<?=$getAdminRx_group->rx_group_id?>",
                   "<?=$getAdminRx_group->rx_group_name?>",
                   "<?=$getAdminRx_group->org_id?>",
                   <?=$medicinesJson?> 
               )'>
               <i class="fa fa-edit fa-lg"></i>
            </a><?php } ?>
            <?php if (userCan('delete', 'rxgroup.php')) { /* FIX_B_1810 */ ?><a class="has-icon text-danger" style="cursor:pointer;" 
               onclick="deleteMedicine('<?=$getAdminRx_group->rx_group_id?>','<?=$getAdminRx_group->rx_group_name?>')">
               <i class="fa fa-trash fa-lg"></i>
            </a><?php } ?>
        </td>
    </tr>
<?php } ?>
    </tbody>                            
</table>
