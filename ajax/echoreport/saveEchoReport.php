<?php
require_once("../../config/functions.php");
header('Content-Type: application/json');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

if (!$SessionUserId) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$echoId        = (int)   ($_POST['echo_report_id']   ?? 0);
$orgId         = mysqli_real_escape_string($conn, $_POST['org_id']         ?? $SessionOrgId);
$appointmentId = mysqli_real_escape_string($conn, $_POST['appointment_id'] ?? '');
$patientId     = mysqli_real_escape_string($conn, $_POST['patient_id']     ?? '');
$patientName   = mysqli_real_escape_string($conn, $_POST['patient_name']   ?? '');
$reportDate    = mysqli_real_escape_string($conn, $_POST['report_date']    ?? date('Y-m-d'));
$age           = mysqli_real_escape_string($conn, $_POST['age']            ?? '');
$gender        = mysqli_real_escape_string($conn, $_POST['gender']         ?? '');
$refBy         = mysqli_real_escape_string($conn, $_POST['ref_by']         ?? '');
$indication    = mysqli_real_escape_string($conn, $_POST['indication']     ?? '');

$mitralValve    = mysqli_real_escape_string($conn, $_POST['mitral_valve']    ?? 'Normal');
$aorticValve    = mysqli_real_escape_string($conn, $_POST['aortic_valve']    ?? 'Normal');
$tricuspidValve = mysqli_real_escape_string($conn, $_POST['tricuspid_valve'] ?? 'Normal');
$pulmonaryValve = mysqli_real_escape_string($conn, $_POST['pulmonary_valve'] ?? 'Normal');
$leftAtrium     = mysqli_real_escape_string($conn, $_POST['left_atrium']     ?? '');

$lvidD          = mysqli_real_escape_string($conn, $_POST['lvid_d']          ?? '');
$lvidS          = mysqli_real_escape_string($conn, $_POST['lvid_s']          ?? '');
$ef             = mysqli_real_escape_string($conn, $_POST['ef']              ?? '');
$ivsThickness   = mysqli_real_escape_string($conn, $_POST['ivs_thickness']   ?? '');
$pwd            = mysqli_real_escape_string($conn, $_POST['pwd']             ?? '');
$lvRwma         = mysqli_real_escape_string($conn, $_POST['lv_rwma']         ?? 'NO RWMA');

$rightAtrium    = mysqli_real_escape_string($conn, $_POST['right_atrium']    ?? 'Normal');
$rightVentricle = mysqli_real_escape_string($conn, $_POST['right_ventricle'] ?? 'Normal');
$tapse          = mysqli_real_escape_string($conn, $_POST['tapse']           ?? '');
$aorta          = mysqli_real_escape_string($conn, $_POST['aorta']           ?? '');
$ajv            = mysqli_real_escape_string($conn, $_POST['ajv']             ?? '');
$pulmonaryArtery= mysqli_real_escape_string($conn, $_POST['pulmonary_artery']?? 'Normal');
$pjv            = mysqli_real_escape_string($conn, $_POST['pjv']             ?? '');
$ivsStatus      = mysqli_real_escape_string($conn, $_POST['ivs_status']      ?? 'Intact');
$iasStatus      = mysqli_real_escape_string($conn, $_POST['ias_status']      ?? 'Intact');
$ivcSvcCs       = mysqli_real_escape_string($conn, $_POST['ivc_svc_cs']      ?? 'Normal');
$pericardium    = mysqli_real_escape_string($conn, $_POST['pericardium']     ?? 'No PE');
$mitralFlow     = mysqli_real_escape_string($conn, $_POST['mitral_flow']     ?? '');

$dopplerMr      = mysqli_real_escape_string($conn, $_POST['doppler_mr']      ?? 'NO');
$dopplerAr      = mysqli_real_escape_string($conn, $_POST['doppler_ar']      ?? 'NO');
$dopplerTr      = mysqli_real_escape_string($conn, $_POST['doppler_tr']      ?? 'NO');
$dopplerPr      = mysqli_real_escape_string($conn, $_POST['doppler_pr']      ?? 'NO');
$conclusion     = mysqli_real_escape_string($conn, $_POST['conclusion']      ?? '');
$doctorName     = mysqli_real_escape_string($conn, $_POST['doctor_name']     ?? '');
$doctorCred     = mysqli_real_escape_string($conn, $_POST['doctor_credentials'] ?? '');

if (empty($patientName)) {
    echo json_encode(['success' => false, 'message' => 'Patient name is required.']);
    exit;
}

if ($echoId > 0) {
    $sql = "UPDATE echo_reports SET
        appointment_id='$appointmentId', patient_id='$patientId', patient_name='$patientName',
        report_date='$reportDate', age='$age', gender='$gender', ref_by='$refBy', indication='$indication',
        mitral_valve='$mitralValve', aortic_valve='$aorticValve', tricuspid_valve='$tricuspidValve',
        pulmonary_valve='$pulmonaryValve', left_atrium='$leftAtrium',
        lvid_d='$lvidD', lvid_s='$lvidS', ef='$ef', ivs_thickness='$ivsThickness', pwd='$pwd',
        lv_rwma='$lvRwma', right_atrium='$rightAtrium', right_ventricle='$rightVentricle',
        tapse='$tapse', aorta='$aorta', ajv='$ajv', pulmonary_artery='$pulmonaryArtery', pjv='$pjv',
        ivs_status='$ivsStatus', ias_status='$iasStatus', ivc_svc_cs='$ivcSvcCs',
        pericardium='$pericardium', mitral_flow='$mitralFlow',
        doppler_mr='$dopplerMr', doppler_ar='$dopplerAr', doppler_tr='$dopplerTr', doppler_pr='$dopplerPr',
        conclusion='$conclusion', doctor_name='$doctorName', doctor_credentials='$doctorCred'
        /* FIX_B_182: do not let UPDATE rewrite org_id; rely on SessionOrgId predicate */
        WHERE echo_report_id='$echoId' AND org_id='$SessionOrgId'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        audit_log($conn, 'EchoReport', 'update', 'echo_reports', $echoId, null, ['patient_name' => $patientName]);
        echo json_encode(['success' => true, 'message' => 'Echo report updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
} else {
    $sql = "INSERT INTO echo_reports
        (appointment_id, patient_id, patient_name, report_date, age, gender, ref_by, indication,
         mitral_valve, aortic_valve, tricuspid_valve, pulmonary_valve, left_atrium,
         lvid_d, lvid_s, ef, ivs_thickness, pwd, lv_rwma,
         right_atrium, right_ventricle, tapse, aorta, ajv, pulmonary_artery, pjv,
         ivs_status, ias_status, ivc_svc_cs, pericardium, mitral_flow,
         doppler_mr, doppler_ar, doppler_tr, doppler_pr,
         conclusion, doctor_name, doctor_credentials,
         status, org_id, created_by, created_at)
        VALUES
        ('$appointmentId','$patientId','$patientName','$reportDate','$age','$gender','$refBy','$indication',
         '$mitralValve','$aorticValve','$tricuspidValve','$pulmonaryValve','$leftAtrium',
         '$lvidD','$lvidS','$ef','$ivsThickness','$pwd','$lvRwma',
         '$rightAtrium','$rightVentricle','$tapse','$aorta','$ajv','$pulmonaryArtery','$pjv',
         '$ivsStatus','$iasStatus','$ivcSvcCs','$pericardium','$mitralFlow',
         '$dopplerMr','$dopplerAr','$dopplerTr','$dopplerPr',
         '$conclusion','$doctorName','$doctorCred',
         '1','$orgId','$SessionUserId',NOW())";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $newId = mysqli_insert_id($conn);
        audit_log($conn, 'EchoReport', 'create', 'echo_reports', $newId, null, ['patient_name' => $patientName]);
        echo json_encode(['success' => true, 'message' => 'Echo report saved successfully.', 'echo_report_id' => $newId]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
}
