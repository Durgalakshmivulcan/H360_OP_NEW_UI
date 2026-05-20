<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? ''; 

if (isset($_GET['fetch'])) {
    $fetchType = $_GET['fetch']; 
    
    if ($fetchType == '1' && isset($_GET['dose_id'])) {

        $doseId = mysqli_real_escape_string($conn, $_GET['dose_id']);

        $query = mysqli_query($conn, "SELECT * FROM intake_time WHERE dose_id = '$doseId' AND Status='1' ORDER BY intake_time_id ASC");

        $intakePeriods = [];

        if (mysqli_num_rows($query) > 0) {
            while ($resMenus = mysqli_fetch_object($query)) {
                
                $doseParts = [];
                if (!empty($resMenus->morning)) $doseParts[] = $resMenus->morning;
                if (!empty($resMenus->afternoon)) $doseParts[] = $resMenus->afternoon;
                if (!empty($resMenus->evening)) $doseParts[] = $resMenus->evening;

                $doseText = implode(" - ", $doseParts); 
                $intakePeriods[] = ['id' => $resMenus->intake_time_id, 'text' => $doseText];
            }

            echo json_encode(["status" => "success", "intakePeriods" => $intakePeriods]);
        } else {
            echo json_encode(["status" => "error"]);
        }
    } elseif ($fetchType == '2' && isset($_GET['intake_id'])) {

        $intakeId = mysqli_real_escape_string($conn, $_GET['intake_id']);

        $query = mysqli_query($conn, "SELECT * FROM intake_time WHERE intake_time_id = '$intakeId' AND Status='1' ORDER BY intake_time_id ASC");

        $times = [];

        if (mysqli_num_rows($query) > 0) {
            while ($resMenus = mysqli_fetch_object($query)) {
                
                $timeParts = [];
                if (!empty($resMenus->morning_time)) $timeParts[] = $resMenus->morning_time;
                if (!empty($resMenus->afternoon_time)) $timeParts[] = $resMenus->afternoon_time;
                if (!empty($resMenus->evening_time)) $timeParts[] = $resMenus->evening_time;

                $timeText = implode(" - ", $timeParts); 
                $times[] = ['id' => $resMenus->intake_time_id, 'text' => $timeText];
            }

            echo json_encode(["status" => "success", "times" => $times]);
        } else {
            echo json_encode(["status" => "error"]);
        }
    } else {
        echo json_encode(["status" => "error"]);
    }
} else {
    echo json_encode(["status" => "error"]);
}
?>
