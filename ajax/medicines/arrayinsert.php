<?php
require_once("../../config/functions.php");
requireCan('add', 'medicines.php', 'ajax'); // FIX_B_1810

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$medicine_id       = $_POST['medicine_id'] ?? [];
$type              = $_POST['type'] ?? [];
$medicienename     = $_POST['medicienename'] ?? [];
$scientificname    = $_POST['scientificname'] ?? [];
$dosage            = $_POST['dosage'] ?? [];
$notes             = $_POST['notes'] ?? [];
$organizations     = $_POST['organizations'] ?? '';
$medicine_price    = $_POST['medicine_price_array'] ?? [];
$excelDataJson     = $_POST['excelData'] ?? '';   // <-- NEW

$msg = 0;
$addorgid = "AND org_id='$SessionOrgId'";
if ($SessionUserId == "1") {
    $addorgid = "AND org_id='$organizations'";
}

/* ========= BULK UPLOAD FROM EXCEL ============ */
if (!empty($excelDataJson)) {
    $excelRows = json_decode($excelDataJson, true);

    if (is_array($excelRows)) {
        foreach ($excelRows as $row) {
            $typeVal   = $row['Medicine Type *'] ?? '';
            $brand     = $row['Brand Name *'] ?? '';
            $comp      = $row['Composition Name *'] ?? '';
            $unit      = $row['Unit *'] ?? '';
            $price     = $row['Price'] ?? '';
            $note      = $row['Note'] ?? '';

            $orgToUse = ($SessionUserId == "1") ? $organizations : $SessionOrgId;
            if ($orgToUse == '') { continue; }

            $check = mysqli_query(
                        $conn,
                        "SELECT medicine_id 
                        FROM medicines 
                        WHERE status='1' 
                            AND scientific_name='" . mysqli_real_escape_string($conn, $comp) . "' 
                            AND medicine_name='" . mysqli_real_escape_string($conn, $brand) . "' 
                            AND dosage='" . mysqli_real_escape_string($conn, $unit) . "' 
                            $addorgid"
                    );
            if (mysqli_num_rows($check) > 0) {
                $msg = "3"; 
                continue;
            }

            $getMedicineType = mysqli_query(
                $conn,
                "SELECT type_id
                   FROM madicine_type 
                  WHERE status='1' 
                    AND type_name='".mysqli_real_escape_string($conn, $typeVal)."'"
            );

            $type_id = null;
            if ($rowType = mysqli_fetch_assoc($getMedicineType)) {
                $type_id = $rowType['type_id'];
            }

            if (!$type_id) {
                continue;
            }

            $ins = mysqli_query(
                $conn,
                "INSERT INTO medicines
                    (org_id, medicine_type, medicine_name, scientific_name, dosage, price, notes, status, created_by, modifeid_by)
                 VALUES
                    ('$orgToUse',
                     '$type_id',
                     '".mysqli_real_escape_string($conn,$brand)."',
                     '".mysqli_real_escape_string($conn,$comp)."',
                     '".mysqli_real_escape_string($conn,$unit)."',
                     '".mysqli_real_escape_string($conn,$price)."',
                     '".mysqli_real_escape_string($conn,$note)."',
                     '1',
                     '$SessionUserId',
                     '$SessionUserId')"
            ) or die(mysqli_error($conn));

            if ($ins) { $msg = 1; }
        }
    }
    echo $msg;
    exit;
}
/* ========= END BULK UPLOAD ============ */


/* ======= your existing single insert / update logic (unchanged) ======= */
for ($i = 0; $i < count($medicine_id); $i++) {
    if ($medicine_id[$i] != "") {
        $getmedicine = mysqli_query($conn,
            "SELECT scientific_name 
               FROM medicines 
              WHERE status='1' 
                AND scientific_name='$scientificname[$i]' 
                AND medicine_id!='$medicine_id[$i]' 
                $addorgid") or die(mysqli_error($conn));
        $result = mysqli_num_rows($getmedicine);
        if ($result > 0) {
            $msg = 3;
        } else {
            if ($SessionUserId == "1") {
                $Updatemedic = mysqli_query($conn,
                    "UPDATE medicines 
                        SET medicine_type='$type[$i]',
                            medicine_name='$medicienename[$i]',
                            scientific_name='$scientificname[$i]',
                            dosage='$dosage[$i]',
                            price='$medicine_price[$i]',
                            notes='$notes[$i]',
                            modifeid_by='$SessionUserId',
                            org_id='$organizations'  
                      WHERE medicine_id='$medicine_id[$i]'") or die(mysqli_error($conn));
                if ($Updatemedic) { 
                    $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM medicines WHERE medicine_id='{$medicine_id[$i]}'"));
                                        audit_log($conn, "Medicines", "update", "medicines", $medicine_id[$i], $before, $after);
                    $msg = 2; }
            } else {
                $Updatemedic = mysqli_query($conn,
                    "UPDATE medicines 
                        SET medicine_type='$type[$i]',
                            medicine_name='$medicienename[$i]',
                            scientific_name='$scientificname[$i]',
                            dosage='$dosage[$i]',
                            price='$medicine_price[$i]',
                            notes='$notes[$i]',
                            modifeid_by='$SessionUserId',
                            org_id='$SessionOrgId'
                      WHERE medicine_id='$medicine_id[$i]'") or die(mysqli_error($conn));
                if ($Updatemedic) { 
                    $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM medicines WHERE medicine_id='{$medicine_id[$i]}'"));
                                        audit_log($conn, "Medicines", "update", "medicines", $medicine_id[$i], $before, $after);
                    $msg = 2; }
            }
        }
    } else {
        $getmedicine = mysqli_query($conn,
            "SELECT scientific_name 
               FROM medicines 
              WHERE status='1' 
                AND scientific_name='$scientificname[$i]' 
                AND medicine_id!='$medicine_id[$i]' 
                $addorgid") or die(mysqli_error($conn));
        $result = mysqli_num_rows($getmedicine);
        if ($result > 0) {
            $msg = 3;
        } else {
            if ($SessionUserId == "1") {
                if ($organizations != "") {
                    $Insertmedic = mysqli_query($conn,
                        "INSERT INTO medicines
                            (org_id,medicine_type,medicine_name,scientific_name,dosage,price,notes,status,created_by,modifeid_by)
                         VALUES
                            ('$organizations','$type[$i]','$medicienename[$i]','$scientificname[$i]',
                             '$dosage[$i]','$medicine_price[$i]','$notes[$i]','1','$SessionUserId','$SessionUserId')") or die(mysqli_error($conn));
                    if ($Insertmedic) { 
                        $newId = mysqli_insert_id($conn);
                                        $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM medicines WHERE medicine_id='$newId'"));
                                        audit_log($conn, "Medicines", "create", "medicines", $newId, null, $after);
                        $msg = 1; }
                }
            } else {
                if ($SessionOrgId != "") {
                    $Insertmedic = mysqli_query($conn,
                        "INSERT INTO medicines
                            (org_id,medicine_type,medicine_name,scientific_name,dosage,price,notes,status,created_by,modifeid_by)
                         VALUES
                            ('$SessionOrgId','$type[$i]','$medicienename[$i]','$scientificname[$i]',
                             '$dosage[$i]','$medicine_price[$i]','$notes[$i]','1','$SessionUserId','$SessionUserId')") or die(mysqli_error($conn));
                    if ($Insertmedic) { 
                        $newId = mysqli_insert_id($conn);
                                        $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM medicines WHERE medicine_id='$newId'"));
                                        audit_log($conn, "Medicines", "create", "medicines", $newId, null, $after);
                        $msg = 1; }
                }
            }
        }
    }
}

echo $msg;
?>
