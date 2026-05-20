<?php
// audit_feature/include/audit_log.php
// Insert rows into the audit_log table with proper session/org/user checks.

require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? null;
$SessionRoleId = $_SESSION['role_id'] ?? null;
$SessionOrgId  = $_SESSION['org_id'] ?? null;

// ------------------------------------------------------
// Step 0: Ensure session and organization context
// ------------------------------------------------------
if (!$SessionUserId || !$SessionOrgId) {
    die("Unauthorized: Missing session data");
}

// ------------------------------------------------------
// Step 1: Check if admin user
// ------------------------------------------------------
$isAdmin = false;
$qrySec = mysqli_query($conn, "SELECT security_type 
                               FROM security 
                               WHERE security_id='$SessionUserId' 
                                 AND org_id='$SessionOrgId'") 
          or die(mysqli_error($conn));
if ($row = mysqli_fetch_assoc($qrySec)) {
    // SA_FATAL_FIXED_B_557: SA also gets admin scope
    if ($row['security_type'] === 'A' || $row['security_type'] === 'SA') {
        $isAdmin = true;
    }
}

// ------------------------------------------------------
// Step 2: Define audit_log function
// ------------------------------------------------------
function audit_log(
    mysqli $conn,
    string $module,
    string $action,
    string $entity,
    ?int $entity_id = null,
    $before = null,
    $after  = null
): bool {
    global $SessionUserId, $SessionOrgId;

    // Encode JSON for before/after states
    $before_json = $before ? mysqli_real_escape_string($conn, json_encode($before, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) : null;
    $after_json  = $after  ? mysqli_real_escape_string($conn, json_encode($after,  JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) : null;

    $ip = mysqli_real_escape_string($conn, $_SERVER['REMOTE_ADDR'] ?? '');
    $ua = mysqli_real_escape_string($conn, $_SERVER['HTTP_USER_AGENT'] ?? '');

    $module = mysqli_real_escape_string($conn, $module);
    $action = mysqli_real_escape_string($conn, $action);
    $entity = mysqli_real_escape_string($conn, $entity);
    $entity_id_val = $entity_id !== null ? (int)$entity_id : 'NULL';

    // ------------------------------------------------------
    // Step 3: Build query
    // ------------------------------------------------------
    $sql = "INSERT INTO audit_log 
            (user_id, org_id, module, action, entity, entity_id, ip, ua, before_json, after_json)
            VALUES ('$SessionUserId', '$SessionOrgId', '$module', '$action', '$entity', $entity_id_val, 
                    '$ip', '$ua', " 
            . ($before_json !== null ? "'$before_json'" : "NULL") . ", "
            . ($after_json  !== null ? "'$after_json'"  : "NULL") . ")";

    // ------------------------------------------------------
    // Step 4: Execute and return
    // ------------------------------------------------------
    return mysqli_query($conn, $sql) ? true : false;
}
?>
