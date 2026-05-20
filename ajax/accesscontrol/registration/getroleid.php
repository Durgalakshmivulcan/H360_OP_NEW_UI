<?php
require_once("../../../config/functions.php");


$role_id = $_POST['role_id'];


$getroleid = mysqli_query($conn, "SELECT role_id FROM roles") or die(mysqli_error($conn));
$resrole=mysqli_fetch_object($getroleid);
$resroleid = $resrole->role_id;

// echo $role_id . "=======================";
$getrolemenus=mysqli_query($conn, "SELECT menu_id FROM role_menus WHERE role_id='$role_id'") or die(mysqli_error($conn));
while($resrolemenus=mysqli_fetch_object($getrolemenus))
{   
    
      $resrolemenus = $getrolemenus->menu_id;
      echo $resrolemenus; 

      ?>
      <?php
}
      ?>