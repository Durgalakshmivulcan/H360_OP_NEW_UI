<?php
// IDOR_FIXED B-579
require_once("../../config/functions.php");
// FIX_B_2252: specialization gate — only doctors whose doctors.doctor_specialization
// is in menus.restricted_to_specializations for gynaec_prescription.php may hit
// these AJAX endpoints. SA + non-doctor users (admin/receptionist/etc.) are
// bypassed inside userMaySeeBySpecialization(). 403 JSON for AJAX mode.
if (function_exists('requireSpecializationFor')) requireSpecializationFor('gynaec_prescription.php', 'ajax');

header('Content-Type: application/json');
ensureGynaecVitalsColumns($conn);

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id']      ?? '';
if (!$SessionUserId) { echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit; }

$id          = (int)($_POST['gynaec_rx_id'] ?? 0);
$orgId       = mysqli_real_escape_string($conn, $_POST['org_id']         ?? $SessionOrgId);
$appointId   = mysqli_real_escape_string($conn, $_POST['appointment_id'] ?? '');
$patientId   = mysqli_real_escape_string($conn, $_POST['patient_id']     ?? '');
$patientName = mysqli_real_escape_string($conn, $_POST['patient_name']   ?? '');
$age         = mysqli_real_escape_string($conn, $_POST['age']            ?? '');
$gender      = mysqli_real_escape_string($conn, $_POST['gender']         ?? 'Female');
$mobile      = mysqli_real_escape_string($conn, $_POST['mobile']         ?? '');
$rxDate      = mysqli_real_escape_string($conn, $_POST['rx_date']        ?? date('Y-m-d'));
$refBy       = mysqli_real_escape_string($conn, $_POST['ref_by']         ?? '');
$docName     = mysqli_real_escape_string($conn, $_POST['doctor_name']    ?? '');
$docCred     = mysqli_real_escape_string($conn, $_POST['doctor_credentials'] ?? '');

$menstrualHx   = mysqli_real_escape_string($conn, $_POST['menstrual_history']       ?? '');
$lmp           = mysqli_real_escape_string($conn, $_POST['lmp']                     ?? '');
$pmc           = mysqli_real_escape_string($conn, $_POST['pmc']                     ?? '');
$edd           = mysqli_real_escape_string($conn, $_POST['edd']                     ?? '');
$riskFactors   = mysqli_real_escape_string($conn, $_POST['risk_factors']            ?? '');
$scanType      = mysqli_real_escape_string($conn, $_POST['scan_type']               ?? '');
$scanDate      = mysqli_real_escape_string($conn, $_POST['scan_date']               ?? '');
$scanFindings  = mysqli_real_escape_string($conn, $_POST['scan_findings']           ?? '');
$scanRemarks   = mysqli_real_escape_string($conn, $_POST['scan_remarks']            ?? '');
$reviewNotes   = mysqli_real_escape_string($conn, $_POST['review_notes']            ?? '');
$finalDiag     = mysqli_real_escape_string($conn, $_POST['final_diagnosis']         ?? '');
$chiefCompl    = mysqli_real_escape_string($conn, $_POST['chief_complaints']        ?? '');
$gynaecHx      = mysqli_real_escape_string($conn, $_POST['gynaec_history']         ?? '');
$obstetricHx   = mysqli_real_escape_string($conn, $_POST['obstetric_history']      ?? '');
$familyHx      = mysqli_real_escape_string($conn, $_POST['family_history']         ?? '');
$personalHx    = mysqli_real_escape_string($conn, $_POST['personal_history']       ?? '');
$genExam       = mysqli_real_escape_string($conn, $_POST['general_examination']    ?? '');
$prevInvest    = mysqli_real_escape_string($conn, $_POST['previous_investigations'] ?? '');
$plan          = mysqli_real_escape_string($conn, $_POST['plan']                   ?? '');
$advice        = mysqli_real_escape_string($conn, $_POST['advice']                 ?? '');
$reviewAfter     = mysqli_real_escape_string($conn, $_POST['review_after']     ?? '');
$reviewAfterDate = mysqli_real_escape_string($conn, $_POST['reviewafterdate'] ?? '');

// Vitals + patient data
$patientData    = mysqli_real_escape_string($conn, $_POST['patient_data']      ?? '');
$bpSitSys       = mysqli_real_escape_string($conn, $_POST['bpSit_systolic']    ?? '');
$bpSitDia       = mysqli_real_escape_string($conn, $_POST['bpSit_diastolic']   ?? '');
$bpStandSys     = mysqli_real_escape_string($conn, $_POST['bpStand_systolic']  ?? '');
$bpStandDia     = mysqli_real_escape_string($conn, $_POST['bpStand_diastolic'] ?? '');
$weight         = mysqli_real_escape_string($conn, $_POST['weight']            ?? '');
$height         = mysqli_real_escape_string($conn, $_POST['height']            ?? '');
$bmi            = mysqli_real_escape_string($conn, $_POST['bmi']               ?? '');
$grbs           = mysqli_real_escape_string($conn, $_POST['grbs']              ?? '');
$heartRate      = mysqli_real_escape_string($conn, $_POST['heart_rate']        ?? '');
$temperature    = mysqli_real_escape_string($conn, $_POST['temperature']       ?? '');
$respirationRate= mysqli_real_escape_string($conn, $_POST['respiration_rate']  ?? '');
$spO2           = mysqli_real_escape_string($conn, $_POST['spO2']              ?? '');
$patientOverview= mysqli_real_escape_string($conn, $_POST['patient_overview']  ?? '');

// Store medicines & investigations as JSON
$medsJson  = mysqli_real_escape_string($conn, $_POST['medicines']      ?? '[]');
$invsJson  = mysqli_real_escape_string($conn, $_POST['investigations'] ?? '[]');

$lmpVal  = $lmp      ? "'$lmp'"      : 'NULL';
$eddVal  = $edd      ? "'$edd'"      : 'NULL';
$sdVal   = $scanDate ? "'$scanDate'" : 'NULL';

if (empty($patientName)) {
    echo json_encode(['success'=>false,'message'=>'Patient name is required.']); exit;
}

if ($id > 0) {
    $sql = "UPDATE gynaec_prescriptions SET
        appointment_id='$appointId', patient_id='$patientId', patient_name='$patientName',
        age='$age', gender='$gender', mobile='$mobile', rx_date='$rxDate',
        ref_by='$refBy', doctor_name='$docName', doctor_credentials='$docCred',
        menstrual_history='$menstrualHx', lmp=$lmpVal, pmc='$pmc', edd=$eddVal,
        risk_factors='$riskFactors', scan_type='$scanType', scan_date=$sdVal,
        scan_findings='$scanFindings', scan_remarks='$scanRemarks',
        review_notes='$reviewNotes', final_diagnosis='$finalDiag',
        chief_complaints='$chiefCompl', gynaec_history='$gynaecHx',
        obstetric_history='$obstetricHx', family_history='$familyHx',
        personal_history='$personalHx', general_examination='$genExam',
        previous_investigations='$prevInvest', plan='$plan', advice='$advice',
        review_after='$reviewAfter', reviewafterdate='$reviewAfterDate', medicines_json='$medsJson',
        investigations_json='$invsJson', org_id='$orgId',
        patient_data='$patientData',
        bpSit_systolic='$bpSitSys', bpSit_diastolic='$bpSitDia',
        bpStand_systolic='$bpStandSys', bpStand_diastolic='$bpStandDia',
        weight='$weight', height='$height', bmi='$bmi', grbs='$grbs',
        heart_rate='$heartRate', temperature='$temperature',
        respiration_rate='$respirationRate', spO2='$spO2', patient_overview='$patientOverview'
        WHERE gynaec_rx_id='$id' AND org_id='$SessionOrgId'";
    $result = mysqli_query($conn, $sql);
    if (!$result) { echo json_encode(['success'=>false,'message'=>mysqli_error($conn)]); exit; }
    $rxId = $id;
    audit_log($conn,'GynaecRx','update','gynaec_prescriptions',$rxId,null,['patient_name'=>$patientName]);
} else {
    $sql = "INSERT INTO gynaec_prescriptions
        (appointment_id,patient_id,patient_name,age,gender,mobile,rx_date,
         ref_by,doctor_name,doctor_credentials,
         menstrual_history,lmp,pmc,edd,risk_factors,
         scan_type,scan_date,scan_findings,scan_remarks,review_notes,
         final_diagnosis,chief_complaints,gynaec_history,obstetric_history,
         family_history,personal_history,general_examination,previous_investigations,
         plan,advice,review_after,reviewafterdate,medicines_json,investigations_json,
         patient_data,bpSit_systolic,bpSit_diastolic,bpStand_systolic,bpStand_diastolic,
         weight,height,bmi,grbs,heart_rate,temperature,respiration_rate,spO2,patient_overview,
         status,org_id,created_by,created_at)
        VALUES
        ('$appointId','$patientId','$patientName','$age','$gender','$mobile','$rxDate',
         '$refBy','$docName','$docCred',
         '$menstrualHx',$lmpVal,'$pmc',$eddVal,'$riskFactors',
         '$scanType',$sdVal,'$scanFindings','$scanRemarks','$reviewNotes',
         '$finalDiag','$chiefCompl','$gynaecHx','$obstetricHx',
         '$familyHx','$personalHx','$genExam','$prevInvest',
         '$plan','$advice','$reviewAfter','$reviewAfterDate','$medsJson','$invsJson',
         '$patientData','$bpSitSys','$bpSitDia','$bpStandSys','$bpStandDia',
         '$weight','$height','$bmi','$grbs','$heartRate','$temperature','$respirationRate','$spO2','$patientOverview',
         '1','$orgId','$SessionUserId',NOW())";
    $result = mysqli_query($conn, $sql);
    if (!$result) { echo json_encode(['success'=>false,'message'=>mysqli_error($conn)]); exit; }
    $rxId = mysqli_insert_id($conn);
    audit_log($conn,'GynaecRx','create','gynaec_prescriptions',$rxId,null,['patient_name'=>$patientName]);
}

// FIX_B_1900: downstream wiring — gynaec needs the same effects as a normal Rx
// (Wprescripation/addpatient.php) so billing, pharmacy, and prescription-reports
// pipelines see Dr Rama's prescriptions. Otherwise tests don't auto-bill,
// medicines don't reach the pharmacist queue, and prescriptionreports.php
// silently misses gynaec rows.
//
// (a) Mirror a row into `prescripition` (the table everything else queries)
//     keyed off appoint_register_id. medicines/investigations stored as JSON
//     to mirror addpatient.php's persistence shape (test_id+medicine_id
//     longtext columns are JSON blobs in this schema).
// (b) UPDATE appointment_online with the captured vitals + flip
//     appointment_status='1' (matches addpatient.php line 162+).
// Wrap in transaction so a partial mirror doesn't leave the gynaec row
// orphaned from its downstream effects.
mysqli_begin_transaction($conn);
try {
    // (a) Mirror to prescripition. images='' to satisfy strict-mode (B-995);
    //     rx_id=0, test_group_id=0 for the same reason (B-996).
    $mirrorSql = "INSERT INTO prescripition (
            patient_name, appoint_register_id, patient_uid, age, gender, rx_id,
            test_group_id, test_id, medicine_id, prescriptiondate, patient_vitals,
            finalDiagnosis, chiefcomplaint, pasthistory, personal_note, reviewafter, reviewafterdate, images, patient_data, advise,
            status, create_date_time, create_by, modify_by, org_id
        ) VALUES (
            '$patientName', '$appointId', '$patientId', '$age', '$gender', 0,
            0, '$invsJson', '$medsJson', NOW(), '$appointId',
            '$finalDiag', '$chiefCompl', '$gynaecHx', '$reviewNotes', '$reviewAfter', '$reviewAfterDate', '', '$patientData', '$advice',
            '1', NOW(), '$SessionUserId', '$SessionUserId', '$orgId'
        )";
    if (!mysqli_query($conn, $mirrorSql)) {
        throw new Exception('mirror prescripition INSERT failed: ' . mysqli_error($conn));
    }
    $mirrorPrescriptionId = mysqli_insert_id($conn);

    // (b) UPDATE appointment_online with vitals + flip appointment_status.
    $updateSql = "UPDATE appointment_online SET
            bpSit_systolic='$bpSitSys', bpSit_diastolic='$bpSitDia',
            bpStand_systolic='$bpStandSys', bpStand_diastolic='$bpStandDia',
            weight='$weight', height='$height', bmi='$bmi', grbs='$grbs',
            heart_rate='$heartRate', temperature='$temperature',
            respiration_rate='$respirationRate', spO2='$spO2',
            patient_overview='$patientOverview', appointment_status='1'
          WHERE appoint_register_id='$appointId' AND org_id='$orgId'";
    if (!mysqli_query($conn, $updateSql)) {
        throw new Exception('appointment_online UPDATE failed: ' . mysqli_error($conn));
    }

    mysqli_commit($conn);
} catch (Exception $e) {
    mysqli_rollback($conn);
    // Don't fail the whole save — gynaec row already persisted. Surface a
    // partial-success message so the UI can show a soft warning.
    echo json_encode([
        'success' => true,
        'message' => 'Gynaec Rx saved but downstream sync failed: ' . $e->getMessage(),
        'gynaec_rx_id' => $rxId,
        'partial_sync' => true,
    ]);
    exit;
}

echo json_encode(['success'=>true,'message'=>$id>0?'Prescription updated.':'Prescription saved.','gynaec_rx_id'=>$rxId]);
