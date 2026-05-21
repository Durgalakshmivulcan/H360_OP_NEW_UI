<?php
/*
 * This script returns a JSON array of upcoming/outstanding appointments for
 * the receptionist dashboard. It joins appointments with doctors and services,
 * and sums the total time a patient has spent in the doctor's room.
 */

require_once("../../config/functions.php");

header('Content-Type: application/json');

$SessionUserId = $_SESSION['security_id'] ?? 0;
$SessionRoleId = $_SESSION['role_id'] ?? 0;
$SessionOrgId  = $_SESSION['org_id'] ?? 1;

// Optional filters
$fromDate  = !empty($_POST['fromDate']) ? $_POST['fromDate'] : date('Y-m-d');
$toDate    = !empty($_POST['toDate']) ? $_POST['toDate'] : date('Y-m-d');
$doctorId  = !empty($_POST['doctor']) ? (int)$_POST['doctor'] : null;
$serviceId = !empty($_POST['service']) ? (int)$_POST['service'] : null;

$where = [];
$where[] = "DATE(a.appoint_date) BETWEEN '$fromDate' AND '$toDate'";

if ($doctorId) {
    $where[] = "d.doc_id = $doctorId";
}

if ($serviceId) {
    // Keep logic same: check if doctor offers this service
    $where[] = "FIND_IN_SET($serviceId, d.doctor_services) > 0";
}

$whereClause = implode(' AND ', $where);

// FIX_B_1903: doctor-scope filter — receptionists/admins return '' (see all),
// doctors are restricted to their own appoint_online rows.
$docScope = currentDoctorScopeSql('a.doctor_name');

// Build SQL query
$sql = "
    SELECT 
        a.appoint_register_id,
        a.appoint_unicode,
        a.patient_name,
        a.appoint_date,
        a.start_time,
        a.end_time,
        d.doctor_name,
        s.service_name,
        a.appoint_status,
        a.visitor_status,
        a.valid_from,
        a.valid_to,
        a.amount,
        a.org_id AS AppointOrgId,
        a.appoint_id,
        a.invoice_payment,
        -- FIX_B_072: surface payment details for receptionist board cols 13-15.
        a.transaction_number,
        a.transaction_amount,
        a.cash_amount,
        p.prescription_id,
        p.org_id AS PrescriptionOrgId,
        p.prescription_id AS prescription_id,
        IFNULL(SUM(TIMESTAMPDIFF(MINUTE, dpd.check_in, dpd.check_out)), 0) AS minutes_spent,
        COUNT(CASE WHEN dpd.check_out IS NULL THEN 1 END) AS open_sessions
    FROM appointment_online a
    JOIN doctors d ON a.doctor_name = d.doc_id
    JOIN services s ON FIND_IN_SET(s.service_id, d.doctor_services) > 0
    LEFT JOIN doctor_patient_duration dpd ON a.appoint_register_id = dpd.appointment_id
    LEFT JOIN prescripition p ON a.appoint_register_id = p.appoint_register_id
    WHERE 
        a.org_id = '$SessionOrgId'
        AND a.appoint_status = '1'
        AND (d.security_id = '$SessionUserId' 
             OR d.doc_id IN (SELECT r.doc_id FROM receptionnist r WHERE r.security_id = '$SessionUserId'))
        AND $whereClause
        $docScope
    GROUP BY a.appoint_register_id, s.service_name
";

$result = mysqli_query($conn, $sql);

$appointments = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $statusText = '';
        switch ($row['visitor_status']) {
            case '1': $statusText = 'Pending'; break;
            case '2': $statusText = 'Active';  break;
            case '3': $statusText = 'Done';    break;
            case '0': $statusText = 'Lapsed';  break;
            default:  $statusText = 'Pending';
        }

        $appointments[] = [
            'appoint_register_id' => $row['appoint_register_id'],
            'patient_id'         => $row['appoint_unicode'],
            'patient_name'       => $row['patient_name'],
            'doctor_name'        => $row['doctor_name'],
            'service_name'       => $row['service_name'],
            'appoint_date'       => $row['appoint_date'],
            'start_time'         => $row['start_time'],
            'end_time'           => $row['end_time'],
            'status'             => $statusText,
            'visitor_status'     => $row['visitor_status'],
            'valid_from'         => $row['valid_from'],
            'valid_to'           => $row['valid_to'],
            'minutes_spent'      => $row['minutes_spent'],
            'open_sessions'      => $row['open_sessions'],
            'prescription_id'    => $row['prescription_id'] ?? null,
            'org_id'             => $row['AppointOrgId'],
            'appoint_id'         => $row['appoint_id'] ?? null,
            'invoice_payment'    => $row['invoice_payment'] ?? null,
            'amount'             => $row['amount'] ?? null,
            'appoint_unicode'    => $row['appoint_unicode'] ?? null,
            'has_prescription'   => !empty($row['prescription_id']) ? true : false,
        ];
    }
}

echo json_encode($appointments);
?>
