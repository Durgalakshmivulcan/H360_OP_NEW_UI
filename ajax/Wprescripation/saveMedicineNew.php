<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

/* B-1830 RBAC */ requireCan(empty($_REQUEST['prescription_id']) ? 'add' : 'edit', 'prescription.php', 'ajax');
header('Content-Type: application/json');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id']      ?? '';

if (!$SessionUserId) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$input = file_get_contents('php://input');
if (!empty($input)) {
    $_POST = json_decode($input, true);
}

$orgId             = ($SessionUserId == "1" && isset($_POST['orgId'])) ? $_POST['orgId'] : $SessionOrgId;
$medicine          = $_POST['medicine']            ?? [];
$patientId         = $_POST['patient_id']          ?? '';
$application_id    = $_POST['appoint_register_id'] ?? '';
$prescription_date = $_POST['prescription_date']   ?? date('Y-m-d');
$prescription_id   = $_POST['prescription_id']     ?? '';
$created_at        = date('Y-m-d H:i:s');
$updated_at        = date('Y-m-d H:i:s');

if (empty($medicine)) {
    echo json_encode(['error' => 'No medicine data provided']);
    exit;
}

try {
    $conn->begin_transaction(); // ✅ START TRANSACTION

    $getDoctorId = mysqli_query($conn, "
        SELECT doctor_name 
        FROM inp_doctor_allocation 
        WHERE application_id = '$application_id' 
        LIMIT 1
    ");
    if (!$getDoctorId) {
        throw new Exception('Failed to retrieve doctor information');
    }
    $docId = mysqli_fetch_assoc($getDoctorId)['doctor_name'];

    if (!empty($prescription_id)) {
        $prescriptionId = $prescription_id;
        $sqlPrescription = "
            UPDATE inp_prescription SET 
                patient_id     = '$patientId',
                application_id = '$application_id',
                prescribed_by  = '$docId',
                prescribed_on  = '$prescription_date',
                org_id         = '$orgId',
                updated_at     = NOW(),
                modified_by    = '$SessionUserId'
            WHERE prescription_id = '$prescriptionId'
        ";
    } else {
        $sqlPrescription = "
            INSERT INTO inp_prescription (
                patient_id, application_id, prescribed_by, prescribed_on, org_id, created_at, created_by
            ) VALUES (
                '$patientId', '$application_id', '$docId', '$prescription_date', '$orgId', NOW(), '$SessionUserId'
            )
        ";
    }

    if (!mysqli_query($conn, $sqlPrescription)) {
        throw new Exception('Failed to save prescription: ' . mysqli_error($conn));
    }
    if (empty($prescriptionId)) {
        $prescriptionId = mysqli_insert_id($conn);
    }

    $successCount = 0;
    $errors       = [];
    foreach ($medicine as $med) {
        $listId        = trim($med['id'] ?? '');
        $medicine_name = trim($med['drugName'] ?? '');
        $total_amount  = trim($med['total_cost'] ?? '0');
        $price         = floatval($med['price'] ?? 0);
        $dosage        = trim($med['dosageId'] ?? '');
        $unit          = trim($med['unitText'] ?? '');
        $when          = trim($med['whenId'] ?? '');
        $time          = trim($med['timeId'] ?? '');
        $durationVal   = trim($med['duration_value'] ?? '');
        $durationUnit  = trim($med['duration'] ?? '');
        $durationDays  = $durationVal . ' ' . $durationUnit;
        $route         = trim($med['routeText'] ?? '');
        $status        = trim($med['medicineStatus'] ?? '');
        $notes         = escape($med['notes'] ?? '');
        $status1       = 1;

        if (!empty($listId)) {
            $sqlList = "
              UPDATE inp_prescription_list SET
                medicine_name   = '$medicine_name',
                price           = '$price',
                dosage          = '$dosage',
                unit            = '$unit',
                intake_period   = '$when',
                time            = '$time',
                duration_days   = '$durationDays',
                route           = '$route',
                medicine_status = '$status',
                instructions    = '$notes',
                modified_by     = '$SessionUserId',
                updated_at      = '$updated_at'
              WHERE id = '$listId'
                AND prescription_id = '$prescriptionId'
                AND org_id = '$orgId'
            ";
            if (!mysqli_query($conn, $sqlList)) {
                throw new Exception("Update List (ID:$listId): " . mysqli_error($conn));
            }

            $sqlChargeUp = "
                UPDATE inp_charges SET
                    total_amount = '$total_amount',
                    modified_by  = '$SessionUserId',
                    updated_at   = '$updated_at'
                WHERE 
                    patient_id = '$patientId'
                    AND application_id = '$application_id'
                    AND charge_type = 'Medication'
                    AND org_id = '$orgId'
                    AND charge_name = '$listId'
                LIMIT 1
            ";
            $updateChargeResult = mysqli_query($conn, $sqlChargeUp);

            if (!$updateChargeResult || mysqli_affected_rows($conn) == 0) {
                $sqlChargeIns = "
                    INSERT INTO inp_charges (
                        patient_id, application_id, charge_type, charge_name, total_amount,
                        org_id, created_by, created_at, status1
                    ) VALUES (
                        '$patientId', '$application_id', 'Medication', '$listId', '$total_amount',
                        '$orgId', '$SessionUserId', '$created_at', '$status1'
                    )
                ";
                if (!mysqli_query($conn, $sqlChargeIns)) {
                    throw new Exception("Insert Charge (prescription ID $listId): " . mysqli_error($conn));
                }
            }

            $successCount++;
        } else {
            $sqlListIns = "
              INSERT INTO inp_prescription_list (
                prescription_id, patient_id, application_id, medicine_name, price,
                dosage, unit, intake_period, time, duration_days,
                route, medicine_status, instructions,
                org_id, created_by, created_at, status1
              ) VALUES (
                '$prescriptionId', '$patientId', '$application_id', '$medicine_name', '$price',
                '$dosage', '$unit', '$when', '$time', '$durationDays',
                '$route', '$status', '$notes',
                '$orgId', '$SessionUserId', '$created_at', '$status1'
              )
            ";
            if (!mysqli_query($conn, $sqlListIns)) {
                throw new Exception("Insert List ($medicine_name): " . mysqli_error($conn));
            }

            $newListId = mysqli_insert_id($conn); 
            
            $sqlChargeIns = "
                INSERT INTO inp_charges (
                    patient_id, application_id, charge_type, charge_name, total_amount,
                    org_id, created_by, created_at, status1
                ) VALUES (
                    '$patientId', '$application_id', 'Medication', '$newListId', '$total_amount',
                    '$orgId', '$SessionUserId', '$created_at', '$status1'
                )
            ";
            if (!mysqli_query($conn, $sqlChargeIns)) {
                throw new Exception("Insert Charge (new prescription ID $newListId): " . mysqli_error($conn));
            }

            $successCount++;
        }
    }

    $conn->commit(); // ✅ COMMIT if everything went well

    echo json_encode([
        'success' => true,
        'message' => "$successCount medicine(s) processed successfully.",
        'errors'  => []
    ]);

} catch (Exception $e) {
    $conn->rollback(); // ❌ ROLLBACK on error
    echo json_encode([
        'error'   => 'Transaction failed',
        'details' => $e->getMessage()
    ]);
}
?>
