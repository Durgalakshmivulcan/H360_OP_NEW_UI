<?php
require_once("../../config/functions.php");


$qry1 = mysqli_query($conn,"SELECT prescription_emr.emr_id, prescription_emr.medicine_name, prescription_report.prescriptions, prescription_report.patients, prescription_report.view 
FROM prescription_emr INNER JOIN prescription_report ON prescription_emr.emr_id = prescription_report.preReport_id") or die(mysqli_error($conn));
while ($res1 = mysqli_fetch_object($qry1)){
?>
<tr>
    <td><?php echo $res1->emr_id;?></td>
<td><?php echo $res1->medicine_name;?></td>
<td><?php echo $res1->prescriptions;?></td>
<td><?php echo $res1->patients;?></td>
<td><div>
    <a href="viewpatients.php">View Patients</a>
</div></td>
</tr>
<?php
  
}

?>









