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
            <th> Organization </th>
            <?php
            }
            ?>
            <th> Department Name </th>
            <th> Description </th>
            <th> Action </th>
        </tr>
    </thead>
    <tbody  class="text-center">
<?php 
$addorgid= " AND org_id='$SessionOrgId'"; 
if($SessionUserId == "1"){ 
    $addorgid=""; 
}     

$getAdminDepartment = mysqli_query($conn, "SELECT * FROM department WHERE status='1' $addorgid ORDER BY dept_id DESC") or die(mysqli_error($conn)); 
$i = 1; 
while($resAdminDepartment = mysqli_fetch_object($getAdminDepartment)){ 
?>
    <tr>
        <td> <?=$i++;?> </td>
        <?php
        if($SessionUserId=="1"){
        ?>
        <td> <?=getUserNameByOrgId($conn, $resAdminDepartment->org_id)?> </td>
        <?php
        }
        ?> 
        <td> <?=$resAdminDepartment->departmentName?> </td>
        <td> <?=$resAdminDepartment->description?> </td>
        <td class="text-center">
            <?php if (userCan('edit', 'department.php')) { /* FIX_B_1810 */ ?><a class="has-icon me-3" style="cursor:pointer;"
            onclick='editDepartment(`<?=$resAdminDepartment->dept_id?>`, `<?=$resAdminDepartment->departmentName ?>`, `<?=$resAdminDepartment->description ?>`, `<?=$resAdminDepartment->org_id ?>`)'>
                <i class="fa fa-edit fa-lg"></i> 
            </a><?php } ?>
            <?php if (userCan('delete', 'department.php')) { /* FIX_B_1810 */ ?><a class="has-icon text-danger" style="cursor:pointer;"
            onclick="deleteDepartment('<?=$resAdminDepartment->dept_id?>', '<?=$resAdminDepartment->departmentName ?>')">
                <i class="fa fa-trash fa-lg"></i> 
            </a><?php } ?>
        </td>
    </tr>
<?php } ?>
    </tbody>                            
</table>