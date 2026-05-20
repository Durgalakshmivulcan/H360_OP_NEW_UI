<?php
// /ajax/reports/op_lab_util_list.php
// Expects POST: from, to, doctor (optional), test_search (optional)
// Returns: JSON array of { test_name, orders, doctor_name }

require_once("../../config/functions.php"); // expects $conn to be defined

header('Content-Type: application/json; charset=utf-8');

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$action = $_POST['action'] ?? '';

if ($action === "doctors") {
    // Get current user security type
    $checkDoctor = mysqli_query($conn, "SELECT security_type FROM security WHERE status='1' AND security_id = '$SessionUserId'");
    $securityType = mysqli_fetch_assoc($checkDoctor)['security_type'] ?? '';

    // SA_FATAL_FIXED_B_390: include SA so $sql is defined for super-admin (was B-390)
    if ($securityType === 'A' || $securityType === 'SA') {
        $sql = "SELECT doc_id, doctor_name FROM doctors WHERE status='1' ORDER BY doctor_name ASC";
    }
    // Receptionist: only assigned doctors
    elseif ($securityType === 'U') {
        $sql = "SELECT d.doc_id, d.doctor_name
                FROM doctors d
                WHERE d.status='1'
                AND (
                    d.security_id = '$SessionUserId'
                    OR d.doc_id IN (
                        SELECT r.doc_id FROM receptionnist r WHERE r.security_id='$SessionUserId'
                    )
                )
                ORDER BY d.doctor_name ASC";
    }

    $res = mysqli_query($conn, $sql) or die(json_encode(['error' => mysqli_error($conn)]));

    $doctors = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $doctors[] = $row;
    }

    echo json_encode(["doctors" => $doctors]);
    exit;
}

if ($action === "tests" || $action === "data") {
    // Read filters
    $from   = trim($_POST['from'] ?? '');
    $to     = trim($_POST['to'] ?? '');
    $doctor = trim($_POST['doctor'] ?? '');
    $testq  = trim($_POST['test_search'] ?? '');

    // Get current user security type
    $checkDoctor = mysqli_query($conn, "SELECT security_type FROM security WHERE status='1' AND security_id = '$SessionUserId'");
    $securityType = mysqli_fetch_assoc($checkDoctor)['security_type'] ?? '';

    // Build WHERE conditions
    $where = [];

    // Date filter
    if ($from !== '' && $to !== '') {
        $from_s = mysqli_real_escape_string($conn, $from) . '';
        $to_s   = mysqli_real_escape_string($conn, $to) . '';
        $where[] = "p.prescriptiondate BETWEEN '{$from_s}' AND '{$to_s}'";
    } elseif ($from !== '') {
        $from_s = mysqli_real_escape_string($conn, $from) . '';
        $where[] = "p.prescriptiondate >= '{$from_s}'";
    } elseif ($to !== '') {
        $to_s = mysqli_real_escape_string($conn, $to) . ' ';
        $where[] = "p.prescriptiondate <= '{$to_s}'";
    }

    // Doctor filter + security
    $where_doctor_security = '';
    if ($securityType === 'U') {
        $where_doctor_security = " AND (
            d.security_id = '$SessionUserId'
            OR d.doc_id IN (SELECT r.doc_id FROM receptionnist r WHERE r.security_id='$SessionUserId')
        )";
    }

    if ($doctor !== '') {
        $doctor_clean = (int)$doctor;
        $where[] = "d.doc_id = {$doctor_clean}{$where_doctor_security}";
    } elseif ($securityType === 'U') {
        $where[] = "1=1 {$where_doctor_security}";
    }

    // Test filter
    if ($testq !== '') {
        $testq_esc = mysqli_real_escape_string($conn, $testq);
        $where[] = "(
            a.patient_name LIKE '%{$testq_esc}%'
            OR p.test_id LIKE '%\"test_name\":\"{$testq_esc}\"%'
            OR p.test_id LIKE '%{$testq_esc}%'
        )";
    }

    // Fetch all tests
    $all_tests = [];
    $test_sql = "SELECT test_name FROM tests WHERE status='1' ORDER BY test_name ASC";
    $test_res = mysqli_query($conn, $test_sql) or die(json_encode(['error' => mysqli_error($conn)]));
    while ($row = mysqli_fetch_assoc($test_res)) {
        $all_tests[trim($row['test_name'])] = 0;
    }

    // Fetch prescriptions
    $base_sql = "
        SELECT 
            p.prescription_id,
            p.test_id,
            p.prescriptiondate,
            d.doctor_name
        FROM prescripition AS p
        LEFT JOIN appointment_online AS a
               ON p.appoint_register_id = a.appoint_register_id
        LEFT JOIN doctors AS d
               ON a.doctor_name = d.doc_id
    ";

    if ($where) {
        $base_sql .= " WHERE " . implode(" AND ", $where);
    }

    $base_sql .= " ORDER BY p.prescriptiondate DESC";

    $q = mysqli_query($conn, $base_sql) or die(json_encode(['error' => mysqli_error($conn)]));

    // Aggregate prescription counts
    $counts = [];
    $doctor_name = '';
    if ($row = mysqli_fetch_assoc($q)) {
        $doctor_name = $row['doctor_name'] ?? '';
        mysqli_data_seek($q, 0);
    }

    while ($row = mysqli_fetch_assoc($q)) {
        $pres_id   = $row['prescription_id'];
        $test_json = $row['test_id'];
        if (!$test_json || trim($test_json) === '' || $test_json === '[]') continue;

        $decoded = json_decode($test_json, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            foreach ($decoded as $t) {
                $tname = '';
                $qty   = null;
                if (is_array($t)) {
                    $tname = trim($t['test_name'] ?? '');
                    $qty   = (int)($t['quantity'] ?? $t['qty'] ?? $t['count'] ?? 0);
                } elseif (is_string($t)) {
                    $tname = trim($t);
                }

                if ($tname === '') continue;

                if (!isset($counts[$tname])) {
                    $counts[$tname] = ['orders' => 0, 'seen_pres' => []];
                }

                if ($qty > 0) {
                    $counts[$tname]['orders'] += $qty;
                } else {
                    if (!isset($counts[$tname]['seen_pres'][$pres_id])) {
                        $counts[$tname]['orders'] += 1;
                        $counts[$tname]['seen_pres'][$pres_id] = true;
                    }
                }
            }
        }
    }

    // Merge counts with all tests
    $result = [];

    if ($testq !== '') {
        // Test filter applied → only matching from counts
        foreach ($counts as $tname => $info) {
            if (stripos($tname, $testq) !== false) {
                $result[] = [
                    'test_name'   => $tname,
                    'orders'      => $info['orders'],
                    'doctor_name' => $info['doctor_name'] ?? ''
                ];
            }
        }
    } else {
        // No filter → show counts first
        foreach ($counts as $tname => $info) {
            $result[] = [
                'test_name'   => $tname,
                'orders'      => $info['orders'],
                'doctor_name' => $info['doctor_name'] ?? ''
            ];
        }

        // Then add tests from all_tests that are missing in counts (with 0 orders)
        foreach ($all_tests as $tname => $zero) {
            if (!isset($counts[$tname])) {
                $result[] = [
                    'test_name'   => $tname,
                    'orders'      => 0,
                    'doctor_name' => ''
                ];
            }
        }
    }

    usort($result, fn($a, $b) => $b['orders'] - $a['orders']);
    echo json_encode($result);
}
exit;
