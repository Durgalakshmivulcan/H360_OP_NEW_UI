<?php
require_once("../../config/functions.php");

$getprescriptionemr = mysqli_query($conn, "SELECT * FROM prescription_emr WHERE status='1' ORDER BY emr_id DESC") or die(mysqli_error($conn));
$i = 1;
while($resprescriptionemr = mysqli_fetch_object($getprescriptionemr)){
?>
    <tr>
        <td> <?=$i++;?> </td>
        <td> <?=$resprescriptionemr->patient_name?> </td>
        <td> <?=$resprescriptionemr->ph_number?> </td>
        <td> <?=$resprescriptionemr->p_gender?> </td>
        <td> <?=$resprescriptionemr->blood_pressure?> </td>
        <td> <?=$resprescriptionemr->pluse ?></td>
        <td> <?=$resprescriptionemr->height?> </td>
        <td> <?=$resprescriptionemr->weight?> </td>
        <td> <?=$resprescriptionemr->temperature?> </td>
        <td> <?=$resprescriptionemr->spo2?> </td>
        <td> <?=$resprescriptionemr->complaint?> </td>
        <td> <?=$resprescriptionemr->diagnosis?> </td>
        <td> <?=$resprescriptionemr->advice?> </td>
        <td> <?=$resprescriptionemr->tests_requested?> </td>
        <td> <?=$resprescriptionemr->date?> </td>
        <td> <?=$resprescriptionemr->next_visit	?> </td>
        <td> <?=$resprescriptionemr->doctor_name?> </td>
        <td> <?=$resprescriptionemr->speciality ?> </td>
        <td> <?=$resprescriptionemr->phone_number?> </td>
        <td> <?=$resprescriptionemr->email_id?> </td>
        <td> <?=$resprescriptionemr->medicine_name?> </td>
        <td> <?=$resprescriptionemr->dose?> </td>
        <td> <?=$resprescriptionemr->timing?> </td>
        <td> <?=$resprescriptionemr->duration?> </td>
        <td> <?=$resprescriptionemr->frequency?> </td>
        <td> <?=$resprescriptionemr->note?> </td>
    
        
        <!-- <td class="text-center">
            <ul class="navbar-nav">
                <li class="dropdown">
                    <a href="#" data-bs-toggle="dropdown" class="nav-link" style="color:black">
                        <i class="fa fa-ellipsis-v"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-left">
                        <a href="#" class="dropdown-item has-icon" style="cursor:pointer;" onclick='editprescriptionemr(`<?=$resprescriptionemr->emr_id?>`,`<?=$resprescriptionemr->patient_name?>`,`<?=$resprescriptionemr->ph_number?>`,`<?=$resprescriptionemr->p_gender?>`,`<?=$resprescriptionemr->blood_pressure?>`, `<?=$resprescriptionemr->pluse?>`, `<?=$resprescriptionemr->height?>`, `<?=$resprescriptionemr->weight?>`, `<?=$resprescriptionemr->temperature?>`, `<?=$resprescriptionemr->spo2 ?>`, `<?=$resprescriptionemr->complaint ?>`, `<?=$resprescriptionemr->diagnosis?>`, `<?=$resprescriptionemr->advice?>`, `<?=$resprescriptionemr->tests_requested?>`,`<?=$resprescriptionemr->date?>`, `<?=$resprescriptionemr->next_visit?>`,`<?=$resprescriptionemr->doctor_name?>`, `<?=$resprescriptionemr->speciality?>`, `<?=$resprescriptionemr->phone_number?>`, `<?=$resprescriptionemr->email_id?>`,`<?=$resprescriptionemr->medicine_name?>`,`<?=$resprescriptionemr->dose?>`,`<?=$resprescriptionemr->timing?>`,`<?=$resprescriptionemr->duration?>`,`<?=$resprescriptionemr->frequency?>`,`<?=$resprescriptionemr->note?>`)'> 
                        <i class="fa fa-edit"></i> Update</a>
                        <a class="dropdown-item has-icon" style="cursor:pointer;" onclick="deleteprescriptionemr('<?=$resprescriptionemr->emr_id?>', '<?=$resprescriptionemr->blood_pressure?>')"> <i class="fa fa-trash"></i> Delete</a>
                        <a class="dropdown-item has-icon" style="cursor:pointer;" onclick="printprescriptionemr(`<?=$resprescriptionemr->emr_id?>`,`<?=$resprescriptionemr->patient_name?>`,`<?=$resprescriptionemr->ph_number?>`,`<?=$resprescriptionemr->p_gender?>`,`<?=$resprescriptionemr->blood_pressure?>`, `<?=$resprescriptionemr->pluse?>`, `<?=$resprescriptionemr->height?>`, `<?=$resprescriptionemr->weight?>`, `<?=$resprescriptionemr->temperature?>`, `<?=$resprescriptionemr->spo2?>`, `<?=$resprescriptionemr->complaint?>`, `<?=$resprescriptionemr->diagnosis?>`, `<?=$resprescriptionemr->advice?>`, `<?=$resprescriptionemr->tests_requested?>`,`<?=$resprescriptionemr->date?>`, `<?=$resprescriptionemr->next_visit?>`,`<?=$resprescriptionemr->doctor_name?>`, `<?=$resprescriptionemr->speciality?>`, `<?=$resprescriptionemr->phone_number?>`, `<?=$resprescriptionemr->email_id?>`,`<?=$resprescriptionemr->medicine_name?>`,`<?=$resprescriptionemr->dose?>`,`<?=$resprescriptionemr->timing?>`,`<?=$resprescriptionemr->duration?>`,`<?=$resprescriptionemr->frequency?>`,`<?=$resprescriptionemr->note?>` )"> <i class="fa fa-print"></i> Print</a>
                    </div>
                </li>
            </ul>
        </td> -->
    </tr>
<?php } ?>







