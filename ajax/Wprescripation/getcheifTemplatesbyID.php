<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');


$cc_id = $_POST['id'];

    $qry = mysqli_query($conn, "SELECT * FROM cheifcomplaint_template WHERE status = '1' AND cc_id='$cc_id'") or die(mysqli_error($conn));

    $qryExists = mysqli_num_rows($qry);
    if ($qryExists > 0) {
        $res = mysqli_fetch_object($qry);

        $result = array(
            "success" => true,
            "template_name" => $res->template_name,
            "template_data" => $res->template_data
        );
    } else {
        $result = array("success" => false, "message" => "Template not found.");
    }

echo json_encode($result);