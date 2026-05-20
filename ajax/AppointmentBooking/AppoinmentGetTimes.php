<?php
require_once("../../config/functions.php");
$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$doctorName_registrationNumber = $_POST['doctorName_registrationNumber'];
$appoint_date = $_POST['available_date'];
$organizations = $_POST['organizations'];

if ($SessionOrgId) {
    $organizations = $SessionOrgId;
}

$i = $_POST['TimeSlot_id1'];
$result = [];

$getDoctorTime = mysqli_query($conn, "SELECT m.doctors_time_id, mr.starting_Time, mr.ending_Time 
    FROM doctors_time_slot m, doctors_time_slot2 mr 
    WHERE m.status='1' 
      AND m.doctors_time_id=mr.doctors_time_id 
      AND m.doctorName_registrationNumber='$doctorName_registrationNumber' 
      AND m.available_date='$appoint_date' 
      AND mr.org_id='$organizations' 
      AND m.org_id=mr.org_id 
    ORDER BY m.doctors_time_id DESC") or die(mysqli_error($conn));
// ✅ Fetch dynamic interval (time_slot_duration) from doctor table
$getDoctor = mysqli_query($conn, "SELECT time_slot_duration FROM doctors 
    WHERE status='1' 
    AND doc_id='$doctorName_registrationNumber' 
    AND org_id='$organizations'") or die(mysqli_error($conn));

$doctorData = mysqli_fetch_assoc($getDoctor);
$interval = isset($doctorData['time_slot_duration']) ? intval($doctorData['time_slot_duration']) : 15;

$count = 1;

$current_time = date('H:i'); 
$today_date = date('Y-m-d');
?>

<style>
    .slots-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }

    .slot-box {
        width: 70px;
        text-align: center;
        min-height: 100px;
    }

    .TimeSlot {
        width: 70px;
        height: 50px;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 5px;
        border-radius: 5px;
        font-size: 13px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: 0.3s;
        cursor: pointer;
    }

    .TimeSlot.booked {
        background-color: gray;
        color: white;
        cursor: not-allowed;
    }

    .TimeSlot.available {
        background-color: #5fe3c0;
        color: white;
    }

    .patient-name {
        width: 70px;
        text-align: start;
        font-size: 12px;
        color: red;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin-top: 3px;
        margin-left: 13px;
    }

</style>

<?php
if ($doctorName_registrationNumber == "") {
    ?>
    <div class="col-lg-12 col-sm-12">
        <p>Please select a doctor.</p>
    </div>
    <?php
} else {
    $availableSlots = false;
    $FinalData = [];

    while ($resDoctorTime = mysqli_fetch_object($getDoctorTime)) {
        $result = getTimeSlot($interval, $resDoctorTime->starting_Time, $resDoctorTime->ending_Time, $appoint_date, $doctorName_registrationNumber);
        foreach ($result as $slot) {
            $FinalData[] = $slot;
        }
    }

    usort($FinalData, function ($a, $b) {
        return strtotime($a['slot_start_time']) - strtotime($b['slot_start_time']);
    });

    echo "<div class='slots-container'>";

    foreach ($FinalData as $FinalSlot) {
        $res_starting_Time = $FinalSlot['slot_start_time'];
        $res_ending_Time = $FinalSlot['slot_end_time'];

        $isToday = ($appoint_date == $today_date);
        $isPastSlot = ($isToday && strtotime($res_starting_Time) <= strtotime($current_time));

        if ($SessionUserId == "1" && $SessionRoleId == "1") {
            $sql = "SELECT appoint_id, patient_name FROM appointment_online 
                    WHERE appoint_status='1' AND start_time='$res_starting_Time' AND end_time='$res_ending_Time' 
                      AND appoint_date='$appoint_date' AND doctor_name='$doctorName_registrationNumber'";
        } else {
            $sql = "SELECT appoint_id, patient_name FROM appointment_online 
                    WHERE appoint_status='1' AND start_time='$res_starting_Time' AND end_time='$res_ending_Time' 
                      AND appoint_date='$appoint_date' AND doctor_name='$doctorName_registrationNumber' 
                      AND org_id='$SessionOrgId'";
        }

        $combinedQuery = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        $rowCount = mysqli_num_rows($combinedQuery);
        $patientName = ($rowCount > 0) ? mysqli_fetch_assoc($combinedQuery)['patient_name'] : "";

        echo "<div class='slot-box'>";

        $tooltip = (!empty($patientName)) ? htmlspecialchars($patientName) : '';
        $class = ($isPastSlot || $rowCount > 0) ? 'booked' : 'available';
        $onClickAttr = ($class === 'available') ? "onclick=\"move('$res_starting_Time','$res_ending_Time','$appoint_date','$count')\"" : '';

        $displayTime = date("g:i A", strtotime($res_starting_Time));

        echo "<div class='TimeSlot $class' id='myDIV$count' title='$tooltip' $onClickAttr>";
        echo "$displayTime";
        echo "</div>";

        if (!empty($patientName)) {
            // echo "<div class='patient-name'>" . htmlspecialchars($patientName) . "</div>";
            echo "<div class='patient-name' title='" . htmlspecialchars($patientName) . "'>" . htmlspecialchars($patientName) . "</div>";

        }

        echo "</div>";
        $count++;

        if ($class === 'available') {
            $availableSlots = true;
        }
    }

    echo "</div>"; 

    if (!$availableSlots) {
        ?>
        <div class="col-lg-12 col-sm-12">
            <p>No Time Slots for the selected doctor.</p>
        </div>
        <?php
    }
}
?>

<script>
// FIX_B_271: this script block previously contained a CSS rule (.TimeSlot.selected{...})
// inside <script>, which the JS parser choked on with "Unexpected token '.'", breaking
// AppointmentOnline.php's GetDoctorTime success handler. Moved the rule to <style> below.
function move(startTime, endTime, date, count) {
    // FIX_B_985: previous version of this AJAX-injected move() (added by FIX_B_271)
    // dropped the #start_time / #end_time hidden-input population that the page's
    // original AppointmentOnline.php move() did. The AJAX response is injected
    // AFTER the page-level move() is defined, so this redefinition shadowed the
    // original — clicking a slot highlighted it but left the hidden inputs empty,
    // and the Book Appointment button stayed disabled forever.
    console.log("Selected slot:", startTime, endTime, date, count);
    document.querySelectorAll('.TimeSlot').forEach(el => el.classList.remove('selected'));
    document.getElementById('myDIV' + count).classList.add('selected');
    var st = document.getElementById('start_time'); if (st) { st.value = startTime; st.dispatchEvent(new Event('change', { bubbles: true })); }
    var et = document.getElementById('end_time');   if (et) { et.value = endTime;   et.dispatchEvent(new Event('change', { bubbles: true })); }
    var ad = document.getElementById('appointDate'); if (ad) { ad.value = date; }
    if (typeof checkBookFields === 'function') { try { checkBookFields(); } catch(e) {} }
}
</script>
<style>
.TimeSlot.selected {
    background-color: orange !important;
    border: none !important;
}
</style>

