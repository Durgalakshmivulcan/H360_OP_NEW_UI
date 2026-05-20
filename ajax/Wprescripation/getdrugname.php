
<?php
require_once "../../config/config.php";
if($_POST['medicine_id']){
$id=$_POST['medicine_id'];
if($id==0){
	echo "<option>Select City</option>";
}else{
	$sql = mysqli_query($conn,"SELECT  * FROM `rx_groups` WHERE medicine_id='$id'");
	while($row = mysqli_fetch_array($sql)){
		echo '<option value="'.$row['rx_id'].'">'.$row['medicine_name'].'</option>';
		// echo '<option value="'.$row['rx_id'].'">'.$row['dosage'].'</option>';
		// echo '<option value="'.$row['rx_id'].'">'.$row['in_time_period'].'</option>';

		}
	}
}
?>