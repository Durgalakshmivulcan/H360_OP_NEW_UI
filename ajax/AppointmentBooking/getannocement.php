<?php
require_once("../../config/functions.php");

$getAdmin = mysqli_query($conn,"SELECT * FROM announcement") or die(mysqli_error($conn));
$i = 1;
while($resAdmin = mysqli_fetch_object($getAdmin)){
?>
    <tr>
        <td> <?=$resAdmin->names?> </td> 
    </tr>

<?php } ?>