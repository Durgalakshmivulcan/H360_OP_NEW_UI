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
             <th> Action </th>
            <?php
                if($SessionUserId=="1"){
            ?>
            <th> Organization Name </th>
            <?php
                }
            ?>
            <th> Test Name </th>
            <th>Normal Range</th> 
            <th> Price </th>
            <th> GST </th>
           
        </tr>
    </thead>
    <tbody class="text-center">
<?php
if($SessionUserId == "1" && $SessionRoleId=="1"){
    $getAdminTest = mysqli_query($conn, "SELECT * FROM tests WHERE status='1' ORDER BY test_id DESC") or die(mysqli_error($conn));
} else{
    $getAdminTest = mysqli_query($conn, "SELECT * FROM tests WHERE status='1' AND org_id='$SessionOrgId' ORDER BY test_id DESC") or die(mysqli_error($conn));
}
$i = 1;
while($resAdminTest = mysqli_fetch_object($getAdminTest)){
?>
    <tr>
        <td> <?=$i++;?> </td>
        <td class="text-center">
            <?php if (userCan('edit', 'test.php')) { /* FIX_B_1810 */ ?><a class="has-icon me-3" style="cursor:pointer;" onclick="editTest(`<?=$resAdminTest->test_id?>`, `<?=$resAdminTest->test_name?>`,`<?=$resAdminTest->normal_range?>`, `<?=$resAdminTest->test_price?>`, `<?=$resAdminTest->test_gst?>`, `<?=$resAdminTest->org_id?>`)"> <i class="fa fa-edit fa-lg"></i> </a><?php } ?>
            <?php if (userCan('delete', 'test.php')) { /* FIX_B_1810 */ ?><a class="has-icon text-danger" style="cursor:pointer;" onclick="deleteTest('<?=$resAdminTest->test_id?>', '<?=$resAdminTest->test_name ?>')"> <i class="fa fa-trash fa-lg"></i> </a><?php } ?>
        </td>
        <?php
            if($SessionUserId=="1"){
        ?>
        <td> <?=getUserNameByOrgId($conn, $resAdminTest->org_id)?> </td>
        <?php
            }
        ?>
        <td> <?=$resAdminTest->test_name?> </td>
        <td> <?=$resAdminTest->normal_range?> </td>
        <td> <?=$resAdminTest->test_price?> </td>
        <td> <?=$resAdminTest->test_gst?>% </td>
        
    </tr>
<?php } ?>

    </tbody>                            
</table>



