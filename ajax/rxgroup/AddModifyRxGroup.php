<?php
// IDOR_FIXED B-592
require_once("../../config/functions.php");
requireCan(empty($_POST['rx_group_id']) ? 'add' : 'edit', 'rxgroup.php', 'ajax'); // FIX_B_1810
$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$msg = 0;

$rx_group_id   = $_POST['rx_group_id'];
$rx_group_name = $_POST['rx_group_name'];
$medicine      = $_POST['medicine'];
$type          = $_POST['type'];
$dosage        = $_POST['dosage'];
$unit          = $_POST['unit'];
$time          = $_POST['time'];
$when          = $_POST['when'];
$duration      = $_POST['duration'];
$notes         = $_POST['notes'];
$organizations = $_POST['organizations'];
$medicineRaw = $_POST['medicine']; // this is JSON string
$medicineArr = json_decode($medicineRaw, true); // convert to PHP array

$medicinesArr = [];
if (is_array($medicineArr)) {
    foreach ($medicineArr as $i => $m) {
        $medicinesArr[] = [
            "medicine_id"    => $m['medicine_id'],
            "medicine_name"  => $m['medicine_name'],
            "type_id"        => $m['type_id'],
            "type_text"      => $m['type_text'],
            "unit_id"        => $m['unit_id'],
            "unit_text"      => $m['unit_text'],
            "dosage_id"      => $m['dosage_id'],
            "when_id"        => $m['when_id'],
            "time_id"        => $m['time_id'],
            "duration_value" => $m['duration_value'],
            "duration"       => $m['duration'],
            "notes"          => $m['notes'],
            "med_status"     => $m['med_status'],
            "timeText"       => $m['timeText'],
            "dosageText"     => $m['dosageText'],
            "whenText"       => $m['whenText']
        ];
    }
}

$medicineJson = mysqli_real_escape_string(
    $conn,
    json_encode($medicinesArr, JSON_UNESCAPED_UNICODE)
);
if ($rx_group_name != "" && count($medicinesArr) > 0) {

    if ($rx_group_id != "") {
        // UPDATE FLOW
        if ($SessionUserId == "1") {
            $checkExist = mysqli_query($conn, "SELECT rx_group_name FROM rx_groups_names WHERE status='1' AND rx_group_name='$rx_group_name' AND rx_group_id!='$rx_group_id' AND org_id='$organizations'") or die(mysqli_error($conn));
        } else {
            $checkExist = mysqli_query($conn, "SELECT rx_group_name FROM rx_groups_names WHERE status='1' AND rx_group_name='$rx_group_name' AND rx_group_id!='$rx_group_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
        }

        if (mysqli_num_rows($checkExist) > 0) {
            $msg = 3; // Duplicate
        } else {
            $before = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rx_groups_names WHERE rx_group_id='$rx_group_id'"));

            if ($SessionUserId == "1") {
                mysqli_query($conn, "UPDATE rx_groups_names SET rx_group_name='$rx_group_name', medicine_detailes='$medicineJson', modify_by='$SessionUserId', org_id='$organizations' WHERE rx_group_id='$rx_group_id'") or die(mysqli_error($conn));
            } else {
                mysqli_query($conn, "UPDATE rx_groups_names SET rx_group_name='$rx_group_name', medicine_detailes='$medicineJson', modify_by='$SessionUserId' WHERE rx_group_id='$rx_group_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
            }

            $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rx_groups_names WHERE rx_group_id='$rx_group_id'"));
            audit_log($conn, "Rx Groups", "update", "rx_groups_names", $rx_group_id, $before, $after);

            $msg = 2;
        }

    } else {
        // INSERT FLOW
        if ($SessionUserId == "1") {
            $checkExist = mysqli_query($conn, "SELECT rx_group_name FROM rx_groups_names WHERE status='1' AND rx_group_name='$rx_group_name' AND org_id='$organizations'") or die(mysqli_error($conn));
        } else {
            $checkExist = mysqli_query($conn, "SELECT rx_group_name FROM rx_groups_names WHERE status='1' AND rx_group_name='$rx_group_name' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
        }

        if (mysqli_num_rows($checkExist) > 0) {
            $msg = 3; // Duplicate
        } else {
            $org_id = ($SessionUserId == "1") ? $organizations : $SessionOrgId;

            mysqli_query($conn, "INSERT INTO rx_groups_names(rx_group_name, medicine_detailes, status, create_by, modify_by, org_id) 
            VALUES ('$rx_group_name', '$medicineJson', '1', '$SessionUserId', '$SessionUserId', '$org_id')") or die(mysqli_error($conn));

            $newId = mysqli_insert_id($conn);

            $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rx_groups_names WHERE rx_group_id='$newId'"));
            audit_log($conn, "Rx Groups", "create", "rx_groups_names", $newId, null, $after);

            $msg = 1;
        }
    }

} else {
    echo "0";
    exit;
}

echo $msg;
?>
