<?php
    require_once("../../config/functions.php");

    $SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';
?>

<style>
    .notes{
        white-space:nowrap;
        }
</style>
<table class="table" id="tableExportmedicine" style="width:100%;">
    <thead class="text-center ">
        <tr>
            <th> S.No </th>
            <?php
                if($SessionUserId=="1"){
            ?>
            <th> Organization Name </th>
            <?php
                }
            ?>
            <th> Type </th>
            <th> Brand Name </th>
            <th> composition Name </th> 
            <th> Unit </th>
            <th style="width:50px"> Note </th>
            <th> Action </th>
        </tr>
    </thead>
    <tbody class="content-loader"  style="text-align:center">
      
    <?php
    if($SessionUserId == "1" && $SessionRoleId=="1"){
        $getAdminmedicine = mysqli_query($conn, "SELECT * FROM medicines WHERE status='1' ORDER BY medicine_id DESC") or die(mysqli_error($conn));
    } else{
        $getAdminmedicine = mysqli_query($conn, "SELECT * FROM medicines WHERE status='1' AND org_id='$SessionOrgId' ORDER BY medicine_id DESC") or die(mysqli_error($conn));
    }
    $i = 1;
    while($resAdminmedicine = mysqli_fetch_object($getAdminmedicine)){
    ?>
        <tr>
            <td> <?=$i++;?> </td>
            <?php
                if($SessionUserId=="1"){
            ?>
            <td> <?=getUserNameByOrgId($conn, $resAdminmedicine->org_id)?> </td>
            <?php
                }
            ?>
            <td> <?=gettypeById($conn,$resAdminmedicine->medicine_type)?> </td>
            <td> <?=$resAdminmedicine->medicine_name?> </td>
            <td> <?=$resAdminmedicine->scientific_name?> </td>
            <td> <?=$resAdminmedicine->dosage?> </td>
            <td style="width:350px"> <?=$resAdminmedicine->notes?> </td>
            <td class="text-center">
                <?php if (userCan('edit', 'medicines.php')) { /* FIX_B_1810 */ ?><a class="has-icon me-3" style="cursor:pointer;" onclick='editmedicine(`<?=$resAdminmedicine->medicine_id?>`, `<?=$resAdminmedicine->medicine_type ?>`, `<?=$resAdminmedicine->medicine_name ?>`, `<?=$resAdminmedicine->scientific_name ?>`, `<?=$resAdminmedicine->dosage?>`, `<?=$resAdminmedicine->notes?>`, `<?=$resAdminmedicine->org_id?>`, `<?=$resAdminmedicine->price?>`)'>
                <i class="fa fa-edit fa-lg"></i> </a><?php } ?>    
                <?php if (userCan('delete', 'medicines.php')) { /* FIX_B_1810 */ ?><a class="has-icon text-danger" style="cursor:pointer;" onclick="deletemedicine('<?=$resAdminmedicine->medicine_id?>','<?=$resAdminmedicine->medicine_name ?>')"> <i class="fa fa-trash fa-lg"></i> </a><?php } ?>  
            </td>
        </tr>
        
    <?php } ?>

    </tbody>                            
</table>







