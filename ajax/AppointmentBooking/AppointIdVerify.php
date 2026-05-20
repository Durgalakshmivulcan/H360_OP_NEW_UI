<?php
    require_once("../../config/functions.php");

    $SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

    $appointID = $_POST['appointID'];
    $organizations = $_POST['organizations'];
    $currentDate = date("Y-m-d"); 

    function idExists($conn, $appointID, $organizations) {
        $sql = "SELECT 1 FROM appointment_online
                WHERE appoint_register_id = '$appointID' 
                AND org_id = '$organizations'
                LIMIT 1";

        $result = mysqli_query($conn, $sql);
        return mysqli_num_rows($result) > 0;
    }

    function getMaxAppointId($conn, $baseID, $organizations) {
        $sql = "SELECT MAX(appoint_register_id) AS max_id 
                FROM appointment_online 
                WHERE org_id = '$organizations' 
                AND appoint_register_id LIKE '{$baseID}%'";

        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        $row = mysqli_fetch_assoc($result);
        return $row['max_id'];
    }

    if (idExists($conn, $appointID, $organizations)) {
        $baseID = substr($appointID, 0, -4);

        $maxID = getMaxAppointId($conn, $baseID, $organizations);

        $number = (int)substr($maxID, -4);
        $newNumber = $number + 1;
        $newAppointID = $baseID . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        echo json_encode($newAppointID);
    } else {
        echo json_encode($appointID);
    }
?>
