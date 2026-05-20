<?php
require_once("../../config/functions.php");
requireCan(empty($_POST['concession_id']) ? 'add' : 'edit', 'concession.php', 'ajax'); // FIX_B_1810

$msg = 0;
$concession_id   = $_POST['concession_id'];
$concession_name = trim($_POST['concessionName']);
$concession_type = trim($_POST['concessionType']);
$concession_value= trim($_POST['concessionValue']);
$SessionOrgId    = $_POST['organizations'];
$SessionUserId   = $_POST['RoleId'];

if ($concession_name != "" && $concession_type != "" && $concession_value != "") {
    if ($concession_id != "") {
        // fetch before update row
        $beforeQuery = mysqli_query($conn, "SELECT * FROM concessions WHERE concession_id='$concession_id' LIMIT 1");
        $before      = null;
        if ($beforeQuery && mysqli_num_rows($beforeQuery) > 0) {
            $before = mysqli_fetch_assoc($beforeQuery);
        }

        if ($SessionUserId == "1") {
            if ($SessionOrgId != "") {
                $UpdateConcession = mysqli_query($conn, "
                    UPDATE concessions SET 
                        concession_name='$concession_name',
                        concession_type='$concession_type',
                        concession_value='$concession_value',
                        modified_by='$SessionUserId',
                        org_id='$SessionOrgId',
                        updated_date_time=NOW()
                    WHERE concession_id='$concession_id'
                ") or die(mysqli_error($conn));

                if ($UpdateConcession) {
                    $msg = 2;
                    // fetch after update
                    $afterQuery = mysqli_query($conn, "SELECT * FROM concessions WHERE concession_id='$concession_id' LIMIT 1");
                    $after      = null;
                    if ($afterQuery && mysqli_num_rows($afterQuery) > 0) {
                        $after = mysqli_fetch_assoc($afterQuery);
                    }
                    audit_log($conn, "Concessions", "update", "concessions", $concession_id, $before, $after);
                }
            }
        } else {
            $UpdateConcession = mysqli_query($conn, "
                UPDATE concessions SET 
                    concession_name='$concession_name',
                    concession_type='$concession_type',
                    concession_value='$concession_value',
                    modified_by='$SessionUserId',
                    updated_date_time=NOW()
                WHERE concession_id='$concession_id'
            ") or die(mysqli_error($conn));

            if ($UpdateConcession) {
                $msg = 2;
                // fetch after update
                $afterQuery = mysqli_query($conn, "SELECT * FROM concessions WHERE concession_id='$concession_id' LIMIT 1");
                $after      = null;
                if ($afterQuery && mysqli_num_rows($afterQuery) > 0) {
                    $after = mysqli_fetch_assoc($afterQuery);
                }
                audit_log($conn, "Concessions", "update", "concessions", $concession_id, $before, $after);
            }
        }

    } else {
        $checkConcession = mysqli_query($conn, "
            SELECT concession_id 
            FROM concessions 
            WHERE concession_name='$concession_name' 
              AND concession_type='$concession_type'
              AND concession_value='$concession_value'
              AND org_id='$SessionOrgId'
              AND status='1'
        ") or die(mysqli_error($conn));

        $result = mysqli_num_rows($checkConcession);
        if ($result > 0) {
            $msg = 3;
        } else {
            if ($SessionUserId == "1") {
                if ($SessionOrgId != "") {
                    $InsertConcession = mysqli_query($conn, "
                        INSERT INTO concessions (
                            concession_name, concession_type, concession_value,
                            org_id, status, created_by, modified_by, created_date_time, updated_date_time
                        ) VALUES (
                            '$concession_name', '$concession_type', '$concession_value',
                            '$SessionOrgId', '1', '$SessionUserId', '$SessionUserId', NOW(), NOW()
                        )
                    ") or die(mysqli_error($conn));

                    if ($InsertConcession) {
                        $msg = 1;
                        $concession_id = mysqli_insert_id($conn);
                        // fetch after insert
                        $afterQuery = mysqli_query($conn, "SELECT * FROM concessions WHERE concession_id='$concession_id' LIMIT 1");
                        $after      = null;
                        if ($afterQuery && mysqli_num_rows($afterQuery) > 0) {
                            $after = mysqli_fetch_assoc($afterQuery);
                        }
                        audit_log($conn, "COncessions", "create", "concessions", $concession_id, null, $after);
                    }
                }
            } else {
                if ($SessionOrgId != "") {
                    $InsertConcession = mysqli_query($conn, "
                        INSERT INTO concessions (
                            concession_name, concession_type, concession_value,
                            org_id, status, created_by, modified_by, created_date_time, updated_date_time
                        ) VALUES (
                            '$concession_name', '$concession_type', '$concession_value',
                            '$SessionOrgId', '1', '$SessionUserId', '$SessionUserId', NOW(), NOW()
                        )
                    ") or die(mysqli_error($conn));

                    if ($InsertConcession) {
                        $msg = 1;
                        $concession_id = mysqli_insert_id($conn);
                        // fetch after insert
                        $afterQuery = mysqli_query($conn, "SELECT * FROM concessions WHERE concession_id='$concession_id' LIMIT 1");
                        $after      = null;
                        if ($afterQuery && mysqli_num_rows($afterQuery) > 0) {
                            $after = mysqli_fetch_assoc($afterQuery);
                        }
                        audit_log($conn, "Concessions", "create", "concessions", $concession_id, null, $after);
                    }
                }
            }
        }
    }
}

echo $msg;
?>
