<?php
header('Content-Type: application/json');
require_once("../../config/functions.php");

$org_id = (int)($_SESSION['org_id'] ?? 0);
$logged_in_security_id = (int)($_SESSION['security_id'] ?? 0);

// DataTables params
$start  = max(0, (int)($_GET['start'] ?? 0));
$length = min(max((int)($_GET['length'] ?? 25), 10), 100);
$search = trim($_GET['search']['value'] ?? '');
$action_filter = $_GET['action_filter'] ?? null;
$module_filter = $_GET['module_filter'] ?? null;
$user_filter   = $_GET['user_filter'] ?? null;
$from_date     = $_GET['from'] ?? null;
$to_date       = $_GET['to'] ?? null;

// ------------------------------------------------------
// Step 0: Check if admin
// ------------------------------------------------------
$isAdmin = false;
$res = mysqli_query($conn, "SELECT security_type FROM security WHERE security_id = $logged_in_security_id AND org_id = $org_id");
if ($row = mysqli_fetch_assoc($res)) {
    // SA_FATAL_FIXED_B_554: SA also gets admin scope
    if ($row['security_type'] === 'A' || $row['security_type'] === 'SA') $isAdmin = true;
}

// ------------------------------------------------------
// Step 1: Determine relevant user_ids & doctor filter
// ------------------------------------------------------
$user_ids = [$logged_in_security_id];
$appointment_only_doctor_filter = ''; // holds doc_id when doctor-scoped

if (!$isAdmin) {
    // --- Case 1: Receptionist ---
    $res = mysqli_query($conn, "SELECT doc_id FROM receptionnist WHERE security_id = $logged_in_security_id AND org_id = $org_id");
    $receptionist_doc_ids = [];
    while ($row = mysqli_fetch_assoc($res)) $receptionist_doc_ids[] = (int)$row['doc_id'];

    if (!empty($receptionist_doc_ids)) {
        $in = implode(',', $receptionist_doc_ids);
        $res2 = mysqli_query($conn, "SELECT DISTINCT d.security_id 
            FROM doctors d
            INNER JOIN audit_log a ON a.user_id = d.security_id
            WHERE d.doc_id IN ($in) AND d.org_id = $org_id AND a.org_id = $org_id");
        $user_ids = [$logged_in_security_id];
        while ($row = mysqli_fetch_assoc($res2)) $user_ids[] = (int)$row['security_id'];
    } else {
        // --- Case 2: Doctor ---
        $res3 = mysqli_query($conn, "SELECT doc_id FROM doctors WHERE security_id = $logged_in_security_id AND org_id = $org_id");
        if ($row = mysqli_fetch_assoc($res3)) {
            $docId = (int)$row['doc_id'];
            $res4 = mysqli_query($conn, "SELECT security_id FROM receptionnist WHERE doc_id = $docId AND org_id = $org_id");
            $user_ids = [$logged_in_security_id];
            while ($r2 = mysqli_fetch_assoc($res4)) $user_ids[] = (int)$r2['security_id'];
            $appointment_only_doctor_filter = $docId;
        }
    }

    if (!empty($user_filter)) $user_ids = [(int)$user_filter];
}

// ------------------------------------------------------
// Step 1b: Admin doctor filter
// ------------------------------------------------------
$admin_selected_doctor = false;
if ($isAdmin && !empty($_GET['doctor_filter'])) {
    $admin_selected_doctor = true;
    $doctor_filter = (int)$_GET['doctor_filter'];
    $user_ids = [];

    $res = mysqli_query($conn, "SELECT doc_id, security_id FROM doctors WHERE doc_id = $doctor_filter AND org_id = $org_id");
    if ($doc = mysqli_fetch_assoc($res)) {
        $user_ids[] = (int)$doc['security_id'];
        $docId = (int)$doc['doc_id'];
        $appointment_only_doctor_filter = $docId;

        $res2 = mysqli_query($conn, "SELECT security_id FROM receptionnist WHERE doc_id = $docId AND org_id = $org_id");
        while ($r = mysqli_fetch_assoc($res2)) $user_ids[] = (int)$r['security_id'];
    }
}

// ------------------------------------------------------
// Step 2: Build WHERE clause
// ------------------------------------------------------
$where = "WHERE a.org_id = $org_id";

$apply_user_scope = (!$isAdmin) || ($isAdmin && $admin_selected_doctor);

if ($apply_user_scope) {
    if (!empty($user_ids)) {
        $in = implode(',', $user_ids);

        if (!empty($appointment_only_doctor_filter)) {
            $where .= " AND (
                (a.user_id IN ($in) AND a.module NOT IN ('Appointments','Doctor Timeslot'))
                OR (a.module = 'Doctor Timeslot' AND JSON_UNQUOTE(JSON_EXTRACT(a.after_json, '$.doctorName_registrationNumber')) = '$appointment_only_doctor_filter')
                OR (a.module = 'Appointments' AND JSON_UNQUOTE(JSON_EXTRACT(a.after_json, '$.doctor_name')) = '$appointment_only_doctor_filter')
            )";
        } else {
            $where .= " AND a.user_id IN ($in)";
        }
    } else {
        $where .= " AND 1=0";
    }
}

// Other filters
if ($search !== '') $where .= " AND (a.entity LIKE '%$search%' OR a.module LIKE '%$search%' OR a.action LIKE '%$search%' OR a.before_json LIKE '%$search%' OR a.after_json LIKE '%$search%')";
if ($action_filter) $where .= " AND a.action = '$action_filter'";
if ($module_filter) $where .= " AND a.module = '$module_filter'";
if ($from_date) $where .= " AND a.ts >= '$from_date 00:00:00'";
if ($to_date) $where .= " AND a.ts <= '$to_date 23:59:59'";

// ------------------------------------------------------
// Step 3: Total count
// ------------------------------------------------------
$sqlCount = "SELECT COUNT(*) as cnt FROM audit_log a $where";
$res = mysqli_query($conn, $sqlCount) or die(mysqli_error($conn));
$total = mysqli_fetch_assoc($res)['cnt'];

// ------------------------------------------------------
// Step 4: Fetch paginated data
// ------------------------------------------------------
$sqlData = "SELECT 
                a.id, a.ts, a.user_id, s.admin_name, 
                a.module, a.action, a.entity, a.entity_id, a.ip
            FROM audit_log a
            LEFT JOIN security s ON a.user_id = s.security_id
            $where
            ORDER BY a.ts DESC
            LIMIT $start, $length";
$res = mysqli_query($conn, $sqlData) or die(mysqli_error($conn));

$rows = [];
while ($row = mysqli_fetch_assoc($res)) $rows[] = $row;

// ------------------------------------------------------
// Step 5: Return JSON
// ------------------------------------------------------
echo json_encode([
    'recordsTotal' => $total,
    'recordsFiltered' => $total,
    'data' => $rows
]);
?>
