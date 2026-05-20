<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');

/* B-1830 RBAC */ requireCan(empty($_REQUEST['prescription_id']) ? 'add' : 'edit', 'prescription.php', 'ajax');
header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

if (!$SessionUserId) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$medicine = $data['medicine'] ?? [];
$patientId = mysqli_real_escape_string($conn, $data['patient_id'] ?? '');
$application_id = mysqli_real_escape_string($conn, $data['appoint_register_id'] ?? '');
$prescription_date = mysqli_real_escape_string($conn, $data['prescription_date'] ?? date('Y-m-d'));
$prescription_id = mysqli_real_escape_string($conn, $data['prescription_id'] ?? '');
$isUpdate = $data['isUpdate'] ?? false;
$created_at = date('Y-m-d H:i:s');
$updated_at = date('Y-m-d H:i:s');
$id = $data['id'] ?? '';
$org_id = ($SessionUserId == "1" && isset($data['organizations'])) ? 
    mysqli_real_escape_string($conn, $data['organizations']) : $SessionOrgId;

if (empty($medicine)) {
    echo json_encode(['error' => 'No medicine data provided']);
    exit;
}

if (empty($patientId) || empty($application_id)) {
    echo json_encode(['error' => 'Patient ID and Application ID are required']);
    exit;
}

try {
    $conn->autocommit(FALSE); // Start transaction

    // Insert new prescription if no ID
    if (empty($id)) {
        $insertPrescription = "
            INSERT INTO inp_prescription (
                patient_id, application_id, prescribed_by, prescribed_on, org_id, 
                created_by, modified_by, created_at, updated_at, status1
            ) VALUES (
                '$patientId', '$application_id', '$SessionUserId', '$prescription_date', '$org_id',
                '$SessionUserId', '$SessionUserId', '$created_at', '$updated_at', '1'
            )";

        if (!mysqli_query($conn, $insertPrescription)) {
            throw new Exception("Failed to insert prescription: " . mysqli_error($conn));
        }

        $prescription_id = mysqli_insert_id($conn);
    }

    foreach ($medicine as $item) {
        $id = mysqli_real_escape_string($conn, $item['id'] ?? '');
        $medicine_name = mysqli_real_escape_string($conn, $item['drugName'] ?? '');
        $dosage = mysqli_real_escape_string($conn, $item['dosageId'] ?? '');
        $unit = mysqli_real_escape_string($conn, $item['unitText'] ?? '');
        $intake_period = mysqli_real_escape_string($conn, $item['whenId'] ?? '');
        $time = mysqli_real_escape_string($conn, $item['timeId'] ?? '');
        $duration_value = mysqli_real_escape_string($conn, $item['duration_value'] ?? '');
        $duration_unit = mysqli_real_escape_string($conn, $item['duration'] ?? '');
        $duration_combined = trim($duration_value . ' ' . $duration_unit);
        $route = mysqli_real_escape_string($conn, $item['routeText'] ?? '');
        $medicine_status = mysqli_real_escape_string($conn, $item['medicineStatus'] ?? 'CONTINUE');
        $instructions = mysqli_real_escape_string($conn, $item['notes'] ?? '');
        $status1 = 1;

        if (!empty($id)) {
            // Update existing
            $updateList = "
                UPDATE inp_prescription_list SET 
                    medicine_name = '$medicine_name', 
                    dosage = '$dosage', 
                    unit = '$unit', 
                    intake_period = '$intake_period', 
                    time = '$time',
                    duration_days = '$duration_combined', 
                    route = '$route', 
                    medicine_status = '$medicine_status', 
                    instructions = '$instructions',
                    modified_by = '$SessionUserId', 
                    updated_at = '$updated_at' 
                WHERE id = '$id' AND prescription_id = '$prescription_id' AND org_id = '$org_id'
            ";

            if (!mysqli_query($conn, $updateList)) {
                throw new Exception("Failed to update medicine item: " . mysqli_error($conn));
            }
        } else {
            // Insert new
            $insertList = "
                INSERT INTO inp_prescription_list (
                    prescription_id, patient_id, application_id, medicine_name, dosage, unit,
                    intake_period, time, duration_days, route, medicine_status, instructions,
                    org_id, created_by, modified_by, created_at, updated_at, status1
                ) VALUES (
                    '$prescription_id', '$patientId', '$application_id', '$medicine_name', '$dosage', '$unit',
                    '$intake_period', '$time', '$duration_combined', '$route', '$medicine_status', '$instructions',
                    '$org_id', '$SessionUserId', '$SessionUserId', '$created_at', '$updated_at', '$status1'
                )";

            if (!mysqli_query($conn, $insertList)) {
                throw new Exception("Failed to insert medicine item: " . mysqli_error($conn));
            }
        }
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => $isUpdate ? 'Prescription updated successfully' : 'Prescription saved successfully',
        'prescription_id' => $prescription_id
    ]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Prescription Error: " . $e->getMessage());
    echo json_encode([
        'error' => $e->getMessage(),
        'debug' => [
            'patientId' => $patientId,
            'application_id' => $application_id,
            'prescription_id' => $prescription_id ?? null,
            'isUpdate' => $isUpdate
        ]
    ]);
} finally {
    $conn->autocommit(TRUE);
}
?>
