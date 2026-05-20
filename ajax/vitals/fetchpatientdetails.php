<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

if (isset($_POST['appointID'])) {
    $appointID = $_POST['appointID'];

    // FIX_B_1903: doctor-scope filter
    $docScope = currentDoctorScopeSql('a.doctor_name');
    $query = "SELECT
                a.appoint_id, a.appoint_register_id, a.patient_name, a.gender, a.age, a.mobile_number,
                a.appoint_date, a.start_time, a.end_time, a.amount_method, a.amount,
                a.doctor_fee, d.doctor_name, a.patient_email,
                a.temperature, a.diastolic, a.systolic, a.glucose_level
            FROM appointment_online a
            JOIN doctors d ON a.doctor_name = d.doc_id
            WHERE a.appoint_id = '$appointID' AND a.appoint_status = '1' $docScope

            UNION

            SELECT
                a.appoint_id, a.appoint_register_id, a.patient_name, a.gender, a.age, a.mobile_number,
                a.appoint_date, a.start_time, a.end_time, a.amount_method, a.amount,
                a.doctor_fee, d.doctor_name, a.patient_email,
                a.temperature, a.diastolic, a.systolic, a.glucose_level
            FROM appointment_existing a
            JOIN doctors d ON a.doctor_name = d.doc_id
            WHERE a.appoint_id = '$appointID' AND a.appoint_status = '1' $docScope";
            

    $stmt = mysqli_query($conn, $query) or die(mysqli_error($conn));
   

    if (mysqli_num_rows($stmt) > 0) {
        // $row = $result->fetch_assoc();
        $row = mysqli_fetch_array($stmt);
        
        $appoint_register_id = $row['appoint_register_id'];

        $vitalsQuery = "SELECT * FROM vitals WHERE appointment_id = '$appoint_register_id'";
        $vitalsStmt = mysqli_query($conn, $vitalsQuery) or die(mysqli_error($conn));
        // $vitalsResult = $vitalsStmt->get_result();
        $hasVitals = mysqli_num_rows($vitalsStmt) > 0;
        $vitalsData = mysqli_fetch_array($vitalsStmt);
        
        ?>
        <div class="card">
            <div class="card-header">
                <h4>Patient Details:</h4>
            </div>
            <div class="card-body table-responsive" >
                <table class="table table-borderless" >
                    <tbody>
                        <tr>
                            <th>Name</th><td>:</td><td><?php echo $row['patient_name'] ?></td>
                            <th>Doctor Name</th><td>:</td><td><?php echo $row['doctor_name'] ?></td>
                            <th>Appointment Date</th><td>:</td><td><?php echo $row['appoint_date'] ?></td>
                        </tr>
                        <tr>
                            <th>Gender</th><td>:</td><td><?php echo $row['gender'] ?></td>
                            <th>Temperature</th><td>:</td><td><?php echo $row['temperature'] ?></td>
                            <th>Time</th><td>:</td><td><?php echo $row['start_time'] ?> - <?php echo $row['end_time'] ?></td>
                        </tr>
                        <tr>
                            <th>Age</th><td>:</td><td><?php echo $row['age'] ?></td>
                            <th>Diastolic</th><td>:</td><td><?php echo $row['diastolic'] ?></td>
                            <th>Payment Method</th><td>:</td><td><?php echo $row['amount_method'] ?></td>                            
                        </tr>
                        <tr>
                            <th>Mobile</th><td>:</td><td><?php echo $row['mobile_number'] ?></td>
                            <th>Systolic</th><td>:</td><td><?php echo $row['systolic'] ?></td>
                            <th>Fee</th><td>:</td><td><?php echo $row['doctor_fee'] ?></td>                            
                        </tr>
                        <tr>
                            <th>Email</th><td>:</td><td><?php echo $row['patient_email'] ?></td>
                            <th>Glucose Level</th><td>:</td><td><?php echo $row['glucose_level'] ?></td>                            
                            <th>Amount</th><td>:</td><td><?php echo $row['amount'] ?></td>
                        </tr>
                    </tbody>
                </table>

                <?php if ($hasVitals){ 
                    $overview = $vitalsData['Overviewofpatient'];
                ?>
                    <div class="card-header ">
                        <h4>Vitals:</h4>
                    </div>
                    <div class="card-body py-0 shadow-none">
                        <table class="table table-borderless" >
                            <tbody>
                                <tr>
                                    <th>BP // (mmHg)</th><td>:</td><td><?php echo $vitalsData['BPsit'] ?></td>
                                    <th>GRBS (mg/dL)</th><td>:</td><td><?php echo $vitalsData['GRBS'] ?></td>
                                    <th>Blood Group</th><td>:</td><td><?php echo $vitalsData['bloodgroup'] ?></td>
                                </tr>
                                <tr>
                                    <th>BP (Pediatric) (mmHg)</th><td>:</td><td><?php echo $vitalsData['BPstand'] ?></td>
                                    <th>Heart Rate (min)</th><td>:</td><td><?php echo $vitalsData['heartrate'] ?></td>
                                    <th>CPAP</th><td>:</td><td><?php echo $vitalsData['CPAP'] ?> - <?php echo $vitalsData['end_time'] ?></td>
                                </tr>
                                <tr>
                                    <th>Weight (Kg)</th><td>:</td><td><?php echo $vitalsData['weight'] ?></td>
                                    <th>Temp (°F)</th><td>:</td><td><?php echo $vitalsData['temperature'] ?></td>
                                    <th>HFNC</th><td>:</td><td><?php echo $vitalsData['HFNC'] ?></td>                            
                                </tr>
                                <tr>
                                    <th>Height (cm)</th><td>:</td><td><?php echo $vitalsData['height'] ?></td>
                                    <th>Resp (min)</th><td>:</td><td><?php echo $vitalsData['resp'] ?></td>
                                    <th>VO2</th><td>:</td><td><?php echo $vitalsData['VO2'] ?></td>                            
                                </tr>
                                <tr>
                                    <th>SP02 (%) (on Room Air)</th><td>:</td><td><?php echo $vitalsData['sp02percent'] ?></td>                            
                                    <th>BMI Value</th><td>:</td><td><?php echo $vitalsData['BMIvalue'] ?></td>
                                    <th>Over-View of Patient</th><td>:</td><td><?php echo strlen($overview) > 5 ? substr($overview, 0, 5) . '...' : $overview; ?></td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>

            <div class="card-footer text-end">
            <?php if ($hasVitals){ ?>
                <button type="button" class="btn btn-primary edit-vital" 
                    data-vital='<?= json_encode($vitalsData) ?>'>
                    Update Vitals
                </button>
            <?php }else{ ?>
                <button type="button" class="btn btn-primary add-vitals-btn" 
                    data-bs-toggle="modal"
                    data-appointId="<?php echo $row['appoint_register_id']; ?>"
                    data-bs-target="#addVitalsModal">
                    Add Vitals
                </button>
            <?php } ?>
                
            </div>
            
        </div>        
        <?php
        $vitalsStmt->close();
    } else {
        echo "<p class='text-danger'>No patient details found.</p>";
    }

    $stmt->close();
} else {
    echo "<p class='text-danger'>Invalid request.</p>";
}
?>
