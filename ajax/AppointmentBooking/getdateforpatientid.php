<?php
    require_once("../../config/functions.php");
    $SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

    $orgId = isset($_GET['org_id']) && !empty($_GET['org_id']) ? $_GET['org_id'] : $SessionOrgId;


    function generateNextID($conn, $orgId, $SessionUserId, $currentDate){
        // FIX_B_986: previously COUNT(*) of (appointment_online ∪ appointment_existing)
        // was used to compute the next sequence number, while the verifier
        // (AppointIdVerify.php) uses MAX(appoint_register_id) on appointment_online.
        // The two regularly disagreed (soft-deleted rows + appointment_existing
        // rows inflate the count), so every booking attempt triggered the
        // "Appointment ID already exists" swal. Now we mirror the verifier:
        // parse MAX(appoint_register_id) for today's A-prefix.
        $today = date('d-m-Y');
        $dateComponents = explode('-', $today);
        $date  = $dateComponents[0];
        $month = $dateComponents[1];
        $year  = $dateComponents[2];
        $baseID  = 'A' . $year . $month . $date;
        $orgEsc  = mysqli_real_escape_string($conn, $orgId);
        $baseEsc = mysqli_real_escape_string($conn, $baseID);

        $maxSql = "SELECT MAX(appoint_register_id) AS max_id
                   FROM appointment_online
                   WHERE org_id='$orgEsc'
                     AND appoint_register_id LIKE '{$baseEsc}%'";
        $maxRes = mysqli_query($conn, $maxSql) or die(mysqli_error($conn));
        $maxRow = mysqli_fetch_assoc($maxRes);
        $maxID  = $maxRow['max_id'] ?? null;

        if ($maxID && strlen($maxID) >= 4) {
            $number = (int) substr($maxID, -4);
            $next   = $number + 1;
        } else {
            $next = 1;
        }

        return $baseID . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    $id = generateNextID($conn, $orgId, $SessionUserId, $currentDate);
    // if ($SessionUserId == "1") {
    //     $id = "";
    // }

echo json_encode($id);
?>