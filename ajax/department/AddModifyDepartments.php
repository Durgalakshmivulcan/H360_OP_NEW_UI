<?php
// IDOR_FIXED B-582
require_once("../../config/functions.php");
requireCan(empty($_POST['dept_id']) ? 'add' : 'edit', 'department.php', 'ajax'); // FIX_B_1810

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$msg = 0;

$dept_id        = $_POST['dept_id'];
$organizations  = $_POST['organizations'];
$departmentName = $_POST['departmentName'];
$description    = $_POST['description'];

$addorgid = " AND org_id='$SessionOrgId'";

if ($SessionUserId == "1") {
    $addorgid = " AND org_id='$organizations'";
}

if ($departmentName != "" && $description != "") {
    if ($dept_id != "") {
        // —— UPDATE CASE —— 
        $getAdminDepartment = mysqli_query(
            $conn,
            "SELECT departmentName 
             -- FIX_B_080: drop modified_by from dup-check; cross-user collisions
             -- in the same org must be rejected. Keep dept_id != self exclusion.
             FROM department 
             WHERE status='1' 
               AND departmentName='$departmentName' 
               AND dept_id!='$dept_id' 
               $addorgid"
        ) or die(mysqli_error($conn));

        $result = mysqli_num_rows($getAdminDepartment);
        if ($result > 0) {
            $msg = 3;
        } else {
            // 🔹 Fetch current row BEFORE update
            $beforeRow = null;
            $beforeQuery = mysqli_query($conn, "SELECT * FROM department WHERE dept_id='$dept_id' LIMIT 1");
            if ($beforeQuery && mysqli_num_rows($beforeQuery) > 0) {
                $beforeRow = mysqli_fetch_assoc($beforeQuery);
            }

            // 🔹 Perform update
            if ($SessionUserId == "1" && $organizations != "") {
                $UpdateDepartment = mysqli_query(
                    $conn,
                    "UPDATE department 
                     SET departmentName='$departmentName', description='$description', 
                         org_id='$organizations', modified_by='$SessionUserId' 
                     WHERE dept_id='$dept_id' AND org_id='$SessionOrgId'"
                ) or die(mysqli_error($conn));
            } else {
                $UpdateDepartment = mysqli_query(
                    $conn,
                    "UPDATE department 
                     SET departmentName='$departmentName', description='$description', 
                         org_id='$SessionOrgId', modified_by='$SessionUserId' 
                     WHERE dept_id='$dept_id'"
                ) or die(mysqli_error($conn));
            }

            if ($UpdateDepartment) {
                $msg = 2;

                // 🔹 Fetch updated row AFTER update
                $afterRow = null;
                $afterQuery = mysqli_query($conn, "SELECT * FROM department WHERE dept_id='$dept_id' LIMIT 1");
                if ($afterQuery && mysqli_num_rows($afterQuery) > 0) {
                    $afterRow = mysqli_fetch_assoc($afterQuery);
                }

                // 🔹 Audit log update
                audit_log($conn, "Department", "update", "department", $dept_id, $beforeRow, $afterRow);
            }
        }
    } else {
        // —— INSERT CASE —— 
        $getAdminDepartment = mysqli_query(
            $conn,
            "SELECT departmentName 
             FROM department 
             WHERE status='1' 
               AND departmentName LIKE '$departmentName' 
               $addorgid"
        ) or die(mysqli_error($conn));

        $result = mysqli_num_rows($getAdminDepartment);

        if ($result > 0) {
            $msg = 3;
        } else {
            if ($SessionUserId == "1" && $organizations != "") {
                $InsertDepartment = mysqli_query(
                    $conn,
                    "INSERT INTO department
                        (departmentName, description, status, created_by, modified_by, org_id) 
                     VALUES 
                        ('$departmentName', '$description', '1', '$SessionUserId', '$SessionUserId','$organizations')"
                ) or die(mysqli_error($conn));

                if ($InsertDepartment) {
                    $msg = 1;
                    $new_id = mysqli_insert_id($conn);

                    // Fetch inserted row for after snapshot
                    $afterRow = null;
                    $afterQuery = mysqli_query($conn, "SELECT * FROM department WHERE dept_id='$new_id' LIMIT 1");
                    if ($afterQuery && mysqli_num_rows($afterQuery) > 0) {
                        $afterRow = mysqli_fetch_assoc($afterQuery);
                    }

                    // 🔹 Audit log create
                    audit_log($conn, "Department", "create", "department", $new_id, null, $afterRow);
                }
            } else {
                $InsertDepartment = mysqli_query(
                    $conn,
                    "INSERT INTO department
                        (departmentName, description, status, created_by, modified_by, org_id) 
                     VALUES 
                        ('$departmentName', '$description', '1', '$SessionUserId', '$SessionUserId','$SessionOrgId')"
                ) or die(mysqli_error($conn));

                if ($InsertDepartment) {
                    $msg = 1;
                    $new_id = mysqli_insert_id($conn);

                    // Fetch inserted row for after snapshot
                    $afterRow = null;
                    $afterQuery = mysqli_query($conn, "SELECT * FROM department WHERE dept_id='$new_id' LIMIT 1");
                    if ($afterQuery && mysqli_num_rows($afterQuery) > 0) {
                        $afterRow = mysqli_fetch_assoc($afterQuery);
                    }
                    
                    audit_log($conn, "Department", "create", "department", $new_id, null, $afterRow);
                }
            }
        }
    }
}

echo $msg;
