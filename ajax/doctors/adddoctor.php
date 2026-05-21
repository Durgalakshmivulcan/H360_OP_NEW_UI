<?php
// IDOR_FIXED B-594
require_once("../../config/functions.php");
requireCan(empty($_POST['doc_id']) ? 'add' : 'edit', 'doctor.php', 'ajax'); // FIX_B_1810

$SessionUserId = $_SESSION['security_id'];
$SessionRoleId = $_SESSION['role_id'];
$SessionOrgId = $_SESSION['org_id'];

$msg = 0;

// POST values
$doc_id = $_POST['doc_id'];
$doc_num = $_POST['doc_num'];
$doc_name = $_POST['doc_name'];
$doctor_type = $_POST['doctor_type'];
$Gender = $_POST['gender'];
$phonenumber = $_POST['phonenumber'];
$email = $_POST['doc_email'];
$specialtis = $_POST['specialtis'];
$doc_services = $_POST['doc_services'];
$departments = $_POST['departments'];
$doctor_fee = $_POST['doctor_fee'];
$organizations = $_POST['organizations'];
$details = $_POST['details'];
$doctor_charge = $_POST['doctor_charge'];
$doctor_visit_charge = $_POST['doctor_visit_charge'];
$time_slot_duration = $_POST['time_slot_duration'];
$doc_img = $_POST['doc_img'];
$securityId = $_POST['securityId'];
$receptionistIdsRaw   = $_POST['receptionistIds'] ?? '';
$receptionistUsersRaw = $_POST['receptionistUsers'] ?? '';

$receptionistIds = array_values(array_filter(array_unique(array_map('trim', explode(',', $receptionistIdsRaw))), 'strlen'));
$receptionistUsers = array_values(array_map('trim', explode(',', $receptionistUsersRaw)));


$addorgid = "AND org_id='$SessionOrgId'";
if ($SessionUserId == "1") {
    $addorgid = "";
}

// ---------------------- Validation ----------------------
if ($doc_name != "" && $Gender != "" && $specialtis != "" && $doc_services != "" && count($receptionistIds) >= 1) {

    // ================================
    //        UPDATE DOCTOR
    // ================================
    if ($doc_id != "") {
        $beforeQuery = mysqli_query($conn, "SELECT * FROM doctors WHERE doc_id='$doc_id' LIMIT 1");
        $before      = null;
        if ($beforeQuery && mysqli_num_rows($beforeQuery) > 0) {
            $before = mysqli_fetch_assoc($beforeQuery);
        }

        $checkPhone = mysqli_query($conn, "SELECT * FROM doctors WHERE status='1' AND doc_id!='$doc_id' AND phone_number='$phonenumber' $addorgid") or die(mysqli_error($conn));
        if (mysqli_num_rows($checkPhone) > 0) {
            $msg = 3; // Phone number exists
        } else {
            $checkEmail = mysqli_query($conn, "SELECT * FROM doctors WHERE status='1' AND doc_id!='$doc_id' AND email='$email' $addorgid") or die(mysqli_error($conn));
            if (mysqli_num_rows($checkEmail) > 0) {
                $msg = 4; // Email exists
            } else {
                // Prepare doctor update fields
                $updateFields = "
                    doc_registration_number='$doc_num',
                    doctor_name='$doc_name',
                    doctor_type='$doctor_type',
                    gender='$Gender',
                    phone_number='$phonenumber',
                    email='$email',
                    doctor_specialization='$specialtis',
                    doctor_services='$doc_services',
                    departments='$departments',
                    doctor_fee='$doctor_fee',
                    details='$details',
                    doctor_charge='$doctor_charge',
                    doctor_visit_charge='$doctor_visit_charge',
                    time_slot_duration='$time_slot_duration',
                    modified_by='$SessionUserId',
                    doc_img='$doc_img'
                ";

                if ($SessionUserId == "1" && $organizations != "") {
                    $updateFields .= ", org_id='$organizations'";
                }

                $update = mysqli_query($conn, "UPDATE doctors SET $updateFields WHERE doc_id='$doc_id'") or die(mysqli_error($conn));

                if ($update) {
                    $msg = 2;

                    $afterQuery = mysqli_query($conn, "SELECT * FROM doctors WHERE doc_id='$doc_id' LIMIT 1");
                    $after      = null;
                    if ($afterQuery && mysqli_num_rows($afterQuery) > 0) {
                        $after = mysqli_fetch_assoc($afterQuery);
                    }

                    // log doctor update
                    audit_log($conn, "Doctors", "update", "doctor", $doc_id, $before, $after);

                    // ----------------- RECEPTIONIST UPDATE -----------------
                    $recBefore = [];
                    $recBeforeQuery = mysqli_query($conn, "SELECT * FROM receptionnist WHERE doc_id='$doc_id' AND status='1' ORDER BY rep_id ASC");
                    if ($recBeforeQuery) {
                        while ($row = mysqli_fetch_assoc($recBeforeQuery)) {
                            $recBefore[] = $row;
                        }
                    }

                    mysqli_query($conn, "UPDATE receptionnist SET status='0', modified_by='$SessionUserId' WHERE doc_id='$doc_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));

                    foreach ($receptionistIds as $index => $receptionistId) {
                        $receptionistId = mysqli_real_escape_string($conn, $receptionistId);
                        $receptionistUser = mysqli_real_escape_string($conn, $receptionistUsers[$index] ?? '');

                        $existingMap = mysqli_query($conn, "SELECT rep_id FROM receptionnist WHERE doc_id='$doc_id' AND security_id='$receptionistId' LIMIT 1");
                        if ($existingMap && mysqli_num_rows($existingMap) > 0) {
                            $existingMapRow = mysqli_fetch_assoc($existingMap);
                            mysqli_query($conn, "
                                UPDATE receptionnist SET
                                    user_name='$receptionistUser',
                                    org_id='" . (($SessionUserId == "1" && $organizations != "") ? $organizations : $SessionOrgId) . "',
                                    status='1',
                                    modified_by='$SessionUserId'
                                WHERE rep_id='" . $existingMapRow['rep_id'] . "'
                            ") or die(mysqli_error($conn));
                        } else {
                            mysqli_query($conn, "
                                INSERT INTO receptionnist (doc_id, security_id, user_name, org_id, status, created_by, modified_by)
                                VALUES ('$doc_id', '$receptionistId', '$receptionistUser', '" . (($SessionUserId == "1" && $organizations != "") ? $organizations : $SessionOrgId) . "', '1', '$SessionUserId', '$SessionUserId')
                            ") or die(mysqli_error($conn));
                        }
                    }

                    $recAfter = [];
                    $recAfterQuery = mysqli_query($conn, "SELECT * FROM receptionnist WHERE doc_id='$doc_id' AND status='1' ORDER BY rep_id ASC");
                    if ($recAfterQuery) {
                        while ($row = mysqli_fetch_assoc($recAfterQuery)) {
                            $recAfter[] = $row;
                        }
                    }

                    audit_log($conn, "Receptionnist", "update", "receptionnist", $doc_id, $recBefore, $recAfter);
                }
            }
        }

    // ================================
    //        INSERT NEW DOCTOR
    // ================================
    } else {
        $checkPhone = mysqli_query($conn, "SELECT * FROM doctors WHERE status='1' AND phone_number='$phonenumber' $addorgid") or die(mysqli_error($conn));
        if (mysqli_num_rows($checkPhone) > 0) {
            $msg = 3;
        } else {
            $checkEmail = mysqli_query($conn, "SELECT * FROM doctors WHERE status='1' AND email='$email' $addorgid") or die(mysqli_error($conn));
            if (mysqli_num_rows($checkEmail) > 0) {
                $msg = 4;
            } else {
                // Generate new doc number if exists
                $newDocNum = $doc_num;
                $checkDocNum = mysqli_query($conn, "SELECT doc_registration_number FROM doctors WHERE doc_registration_number='$doc_num' $addorgid") or die(mysqli_error($conn));
                if (mysqli_num_rows($checkDocNum) > 0) {
                    $lastDocNum = mysqli_fetch_assoc(mysqli_query($conn, "SELECT doc_registration_number FROM doctors WHERE doc_registration_number LIKE 'D%' ORDER BY doc_registration_number DESC LIMIT 1"));
                    $lastNumber = (int)substr($lastDocNum['doc_registration_number'], -4) + 1;
                    $newDocNum = "D" . date("Ymd") . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);
                }

                $orgIdToUse = ($SessionUserId == "1" && $organizations != "") ? $organizations : $SessionOrgId;

                // Insert doctor
                $insert = mysqli_query($conn, "
                    INSERT INTO doctors (
                        doc_registration_number, doctor_name, doctor_type, gender, phone_number, 
                        email, doctor_specialization, doctor_services, departments, doctor_fee,
                        org_id, details, doctor_charge, doctor_visit_charge, time_slot_duration,
                        status, created_by, modified_by, doc_img, security_id
                    ) VALUES (
                        '$newDocNum', '$doc_name', '$doctor_type', '$Gender', '$phonenumber',
                        '$email', '$specialtis', '$doc_services', '$departments', '$doctor_fee',
                        '$orgIdToUse', '$details', '$doctor_charge', '$doctor_visit_charge', '$time_slot_duration',
                        '1', '$SessionUserId', '$SessionUserId', '$doc_img', '$securityId'
                    )
                ") or die(mysqli_error($conn));

                if ($insert) {
                    $msg = 1;
                    $newId = mysqli_insert_id($conn);

                    $afterQuery = mysqli_query($conn, "SELECT * FROM doctors WHERE doc_id='$newId' LIMIT 1");
                    $after      = null;
                    if ($afterQuery && mysqli_num_rows($afterQuery) > 0) {
                        $after = mysqli_fetch_assoc($afterQuery);
                    }

                    // log doctor create
                    audit_log($conn, "Doctors", "create", "doctor", $newId, null, $after);

                    // Assign D-code to the doctor's security account
                    if (!empty($securityId)) {
                        ensureUserCodeColumn($conn);
                        $chkD = mysqli_fetch_assoc(mysqli_query($conn, "SELECT user_code FROM security WHERE security_id='" . (int)$securityId . "' LIMIT 1"));
                        if (empty($chkD['user_code']) || substr($chkD['user_code'], 0, 1) !== 'D') {
                            $dCode = generateUserCode($conn, 'D');
                            mysqli_query($conn, "UPDATE security SET user_code='$dCode' WHERE security_id='" . (int)$securityId . "'");
                        }
                    }

                    // ----------------- RECEPTIONIST INSERT -----------------
                    $recAfter = [];
                    foreach ($receptionistIds as $index => $receptionistId) {
                        $receptionistId = mysqli_real_escape_string($conn, $receptionistId);
                        $receptionistUser = mysqli_real_escape_string($conn, $receptionistUsers[$index] ?? '');

                        mysqli_query($conn, "
                            INSERT INTO receptionnist (doc_id, security_id, user_name, org_id, status, created_by, modified_by)
                            VALUES ('$newId', '$receptionistId', '$receptionistUser', '$orgIdToUse', '1', '$SessionUserId', '$SessionUserId')
                        ") or die(mysqli_error($conn));
                    }

                    $recAfterQuery = mysqli_query($conn, "SELECT * FROM receptionnist WHERE doc_id='$newId' AND status='1' ORDER BY rep_id ASC");
                    if ($recAfterQuery) {
                        while ($row = mysqli_fetch_assoc($recAfterQuery)) {
                            $recAfter[] = $row;
                        }
                    }

                    audit_log($conn, "Receptionnist", "create", "receptionnist", $newId, null, $recAfter);
                }
            }
        }
    }
} else {
    $msg = count($receptionistIds) < 1 ? 5 : 0; // Validation fail
}

echo $msg;
?>
