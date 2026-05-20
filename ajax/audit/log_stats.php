<?php
header('Content-Type: application/json');
require_once("../../config/functions.php");

// ------------------------------------------------------
// Step 0: Get session & GET params
// ------------------------------------------------------
$org_id = (int)($_SESSION['org_id'] ?? 0);
$logged_in_security_id = (int)($_SESSION['security_id'] ?? 0);

$from   = $_GET['from'] ?? null;
$to     = $_GET['to'] ?? null;
$module = $_GET['module_filter'] ?? null;
$action = $_GET['action_filter'] ?? null;
$user   = $_GET['user_filter'] ?? null;
$doctor_filter = isset($_GET['doctor_filter']) ? (int)$_GET['doctor_filter'] : null;

// ------------------------------------------------------
// Step 1: Check if admin
// ------------------------------------------------------
$isAdmin = false;
$res = mysqli_query($conn, "SELECT security_type 
                            FROM security 
                            WHERE security_id = {$logged_in_security_id} 
                              AND org_id = {$org_id}");
if ($row = mysqli_fetch_assoc($res)) {
    // SA_FATAL_FIXED_B_553: SA also gets admin scope
    if ($row['security_type'] === 'A' || $row['security_type'] === 'SA') $isAdmin = true;
}

// ------------------------------------------------------
// Step 2: Determine relevant user_ids & doctor scope
// ------------------------------------------------------
$user_ids = [];
$appointment_doctor_id = null;

// --- Non-admin users (receptionist / doctor)
if (!$isAdmin) {
    $user_ids = [$logged_in_security_id];

    // Receptionist: get linked doctors
    $doc_ids = [];
    $res = mysqli_query($conn, "SELECT doc_id 
                                FROM receptionnist 
                                WHERE security_id = {$logged_in_security_id} 
                                  AND org_id = {$org_id}");
    while ($r = mysqli_fetch_assoc($res)) $doc_ids[] = (int)$r['doc_id'];

    if (!empty($doc_ids)) {
        foreach ($doc_ids as $docid) {
            $res2 = mysqli_query($conn, "SELECT security_id 
                                         FROM doctors 
                                         WHERE doc_id = {$docid} 
                                           AND org_id = {$org_id} 
                                         LIMIT 1");
            if ($row = mysqli_fetch_assoc($res2)) $user_ids[] = (int)$row['security_id'];
        }
    }

    // Doctor: get linked receptionists
    $doc_id_of_logged = null;
    $res = mysqli_query($conn, "SELECT doc_id 
                                FROM doctors 
                                WHERE security_id = {$logged_in_security_id} 
                                  AND org_id = {$org_id} 
                                LIMIT 1");
    if ($row = mysqli_fetch_assoc($res)) $doc_id_of_logged = (int)$row['doc_id'];

    if ($doc_id_of_logged) {
        $res = mysqli_query($conn, "SELECT security_id 
                                    FROM receptionnist 
                                    WHERE doc_id = {$doc_id_of_logged} 
                                      AND org_id = {$org_id}");
        while ($r = mysqli_fetch_assoc($res)) $user_ids[] = (int)$r['security_id'];
        $appointment_doctor_id = $doc_id_of_logged;
    }

    $user_ids = array_values(array_unique(array_map('intval', $user_ids)));
}

// --- Admin-selected doctor
if ($isAdmin && $doctor_filter) {
    $admin_user_ids = [];
    $res = mysqli_query($conn, "SELECT doc_id, security_id 
                                FROM doctors 
                                WHERE doc_id = {$doctor_filter} 
                                  AND org_id = {$org_id} 
                                LIMIT 1");
    if ($doc = mysqli_fetch_assoc($res)) {
        $admin_user_ids[] = (int)$doc['security_id'];
        $appointment_doctor_id = (int)$doc['doc_id'];

        $res2 = mysqli_query($conn, "SELECT security_id 
                                     FROM receptionnist 
                                     WHERE doc_id = {$appointment_doctor_id} 
                                       AND org_id = {$org_id}");
        while ($r = mysqli_fetch_assoc($res2)) $admin_user_ids[] = (int)$r['security_id'];
    }

    if (!empty($admin_user_ids)) $user_ids = $admin_user_ids;
    else {
        echo json_encode(['total'=>0,'create'=>0,'update'=>0,'delete'=>0]);
        exit;
    }
}

// --- Explicit user filter
if (!empty($user)) {
    $user_ids = [(int)$user];
}

// ------------------------------------------------------
// Step 3: Build WHERE clause (unified logic)
// ------------------------------------------------------
$where = "WHERE org_id = {$org_id}";
$orParts = [];

if (!empty($user_ids)) {
    $user_ids_sql = implode(',', array_map('intval', $user_ids));

    if (!empty($appointment_doctor_id)) {
        $orParts[] = "(user_id IN ($user_ids_sql) AND module NOT IN ('Appointments','Doctor Timeslot'))";
        $orParts[] = "(module = 'Appointments' AND JSON_UNQUOTE(JSON_EXTRACT(after_json, '$.doctor_name')) = '{$appointment_doctor_id}')";
        $orParts[] = "(module = 'Doctor Timeslot' AND JSON_UNQUOTE(JSON_EXTRACT(after_json, '$.doctorName_registrationNumber')) = '{$appointment_doctor_id}')";
    } else {
        $orParts[] = "user_id IN ($user_ids_sql)";
    }
}

if (!empty($orParts)) $where .= " AND (" . implode(" OR ", $orParts) . ")";

// Date filters
if ($from) { 
    $from = mysqli_real_escape_string($conn, $from);
    $where .= " AND ts >= '{$from} 00:00:00'";
}
if ($to) { 
    $to = mysqli_real_escape_string($conn, $to);
    $where .= " AND ts <= '{$to} 23:59:59'";
}

// Module & Action filters
if ($module) {
    $module = mysqli_real_escape_string($conn, $module);
    $where .= " AND module = '{$module}'";
}
if ($action) {
    $action = mysqli_real_escape_string($conn, $action);
    $where .= " AND action = '{$action}'";
}

// ------------------------------------------------------
// Step 4: Fetch stats
// ------------------------------------------------------
$sql = "
    SELECT 
      COUNT(*) AS total,
      SUM(action = 'create') AS `create`,
      SUM(action = 'update') AS `update`,
      SUM(action = 'delete') AS `delete`
    FROM audit_log
    $where
";

$res = mysqli_query($conn, $sql);
$row = $res ? mysqli_fetch_assoc($res) : null;

echo json_encode($row ?: ['total'=>0,'create'=>0,'update'=>0,'delete'=>0]);
?>
