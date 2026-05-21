<?php
require_once("../../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

$role_id = $_POST['role_id'];

// FIX_B_1850: per-action RBAC. Empty role_id → 'add'; non-empty → 'edit'.
// SA bypass preserved by userCan() so cannot lock SA out.
requireCan(empty($role_id) ? 'add' : 'edit', 'roles.php', 'ajax');

$menus_ids = $_POST['menu_id'];
$role_name = $_POST['role_name'];
$menu_access = $_POST['menu_access'];
// FIX_B_1801 (scope 2): per-action permissions per menu_id, comma-separated
// subset of {view,add,edit,delete}. Optional for back-compat — if absent we
// derive 'view,add,edit,delete' for menu_access='1' rows and '' otherwise.
$permissions_in = $_POST['permissions'] ?? [];

$msg = 0;

if ($role_name != '' && is_array($menus_ids) && count($menus_ids) > 0) {
    if ($role_id != '') {
        $beforeQuery = mysqli_query($conn, "SELECT * FROM roles WHERE role_id='$role_id' LIMIT 1");
        $before      = null;
        if ($beforeQuery && mysqli_num_rows($beforeQuery) > 0) {
            $before = mysqli_fetch_assoc($beforeQuery);
        }
        $getrole = mysqli_query($conn, "SELECT role_name FROM roles WHERE status='1' AND role_name='$role_name' AND role_id!='$role_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
        $result = mysqli_num_rows($getrole);
        if ($result > 0) {     
            $msg = 3; 
        } else { 
            $existing_role_query = mysqli_query($conn, "SELECT * FROM roles WHERE role_id='$role_id'");
            $existing_role_data = mysqli_fetch_assoc($existing_role_query);

            $updateRole = mysqli_query($conn, "UPDATE roles SET role_name='$role_name', modified_by='$SessionUserId' WHERE role_id='$role_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
            $msg = 2;
            $afterQuery = mysqli_query($conn, "SELECT * FROM roles WHERE role_id='$role_id' LIMIT 1");
            $after      = null;
            if ($afterQuery && mysqli_num_rows($afterQuery) > 0) {
                $after = mysqli_fetch_assoc($afterQuery);
            }

            // log update
            audit_log($conn, "Roles", "update", "roles", $role_id, $before, $after);
        }
    } else {
        $getrole = mysqli_query($conn, "SELECT role_name FROM roles WHERE status='1' AND role_name LIKE '$role_name' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
        $result = mysqli_num_rows($getrole);
        if ($result > 0) {
            $msg = 3;
        } else {
            $insertRole = mysqli_query($conn, "INSERT INTO roles (role_name, created_by, modified_by, created_date_time, status, org_id) VALUES ('$role_name', '$SessionUserId', '$SessionUserId', NOW(), '1', '$SessionOrgId')") or die(mysqli_error($conn));
            $role_id = mysqli_insert_id($conn);
            $msg = 1;
            $afterQuery = mysqli_query($conn, "SELECT * FROM roles WHERE role_id='$role_id' LIMIT 1");
            $after      = null;
            if ($afterQuery && mysqli_num_rows($afterQuery) > 0) {
                $after = mysqli_fetch_assoc($afterQuery);
            }

            // log create
            audit_log($conn, "Roles", "create", "roles", $role_id, null, $after);
        }
    }

    if ($msg == 1 || $msg == 2) {
        // FIX_B_1800: wrap DELETE+INSERT in a transaction. Previously, if any
        // INSERT failed mid-batch the role lost every prior mapping with no
        // recovery (the DELETE had already run unconditionally). Now, on any
        // failure we rollback so the role keeps its previous menus.
        mysqli_begin_transaction($conn);
        try {
            if (!mysqli_query($conn, "DELETE FROM role_menus WHERE role_id='$role_id'")) {
                throw new Exception(mysqli_error($conn));
            }
            foreach ($menus_ids as $menu_id) {
                $access = isset($menu_access[$menu_id]) ? $menu_access[$menu_id] : '0';
                // FIX_B_1801 (scope 2): persist per-action permissions. Validate
                // each action against the SET column's allowed values before
                // joining; ignore unknown actions defensively.
                $allowed = ['view', 'add', 'edit', 'delete'];
                $rawPerm = $permissions_in[$menu_id] ?? '';
                if (is_array($rawPerm)) { $rawPerm = implode(',', $rawPerm); }
                $perms = array_values(array_intersect(
                    array_map('trim', explode(',', strtolower($rawPerm))),
                    $allowed
                ));
                // Fallback A: client sent nothing — derive from legacy menu_access.
                if (empty($perms) && !isset($permissions_in[$menu_id])) {
                    $perms = ($access === '1') ? $allowed : [];
                }
                // Fallback B: client sent an empty string (parent menus have no
                // permission checkboxes, so the JS loop produces '').
                // Parent menus only need 'view' so the sidebar gate passes.
                if (empty($perms) && isset($permissions_in[$menu_id]) && $permissions_in[$menu_id] === '' && $access === '1') {
                    $pmChk = mysqli_query($conn, "SELECT menu_type FROM menus WHERE menu_id='" . (int)$menu_id . "' LIMIT 1");
                    if ($pmChk && ($pmRow = mysqli_fetch_assoc($pmChk)) && $pmRow['menu_type'] === 'p') {
                        $perms = ['view'];
                    }
                }
                $permsCsv = mysqli_real_escape_string($conn, implode(',', $perms));
                $ok = mysqli_query($conn, "
                    INSERT INTO role_menus
                        (menu_id, role_id, org_id, menu_access, permissions, created_by, created_date_time)
                    VALUES
                        ('$menu_id', '$role_id', '$SessionOrgId', '$access', '$permsCsv', '$SessionUserId', NOW())
                ");
                if (!$ok) {
                    throw new Exception(mysqli_error($conn));
                }
            }
            mysqli_commit($conn);
        } catch (Exception $e) {
            mysqli_rollback($conn);
            // Distinct code so the JS can show a specific message and the role
            // keeps its previous menu set untouched.
            $msg = 4;
        }
    }
}
echo $msg;
?>
