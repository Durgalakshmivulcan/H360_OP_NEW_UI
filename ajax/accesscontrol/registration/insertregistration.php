<?php

// IDOR_FIXED B-595,B-596
require_once("../../../config/functions.php");

// $msg = 0;

$SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';


$security_id = $_POST['regis_hid_id'];

// FIX_B_1850: per-action RBAC. Split add vs edit by regis_hid_id presence.
// Empty → 'add'; non-empty → 'edit'. SA bypass preserved by userCan().
requireCan(empty($security_id) ? 'add' : 'edit', 'registration.php', 'ajax');

$admin_name = $_POST['admin_name'];
$email = $_POST['email'];
$contact = $_POST['contact'];
$security_password = md5($_POST['security_password']);
$role_id = $_POST['security'];
$org_id = $_POST['organizations'];
// FIX_B_2320: opt-in capability flag — only meaningful for Receptionist (role_id=3).
// Cast to 0/1 and zero it out for any other role so the column isn't set spuriously.
$canSwitchDoctor = (((int)$role_id === 3) && !empty($_POST['can_switch_doctor'])) ? 1 : 0;

// FIX_B_2330: hard server-side block — the Super Admin role is bootstrap-only.
// Nobody (including SA herself) may create, promote-to, or edit an SA account
// through this form, even by forging the POST. Also block any attempt to
// touch an existing SA record (security_type='SA') as a defence-in-depth guard.
if ((int)$role_id === 1) { http_response_code(403); echo 'forbidden_sa_role'; exit; }
if (!empty($security_id)) {
    $_sa = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT role_id, security_type FROM security WHERE security_id='" . (int)$security_id . "' LIMIT 1"));
    if ($_sa && ((int)($_sa['role_id'] ?? 0) === 1 || ($_sa['security_type'] ?? '') === 'SA')) {
        http_response_code(403); echo 'forbidden_sa_record'; exit;
    }
}

$result = 0;
$msg    = 0;

    if ($result == 0 || ($security_id != "" && $result == 1)) {
            if($admin_name != "" && $email != "" && $contact != "" && $role_id != "") {
                if ($security_id != "") {
                    $getmedicine1 = mysqli_query($conn, "SELECT * FROM security WHERE status='1' AND security_id != '$security_id' AND admin_name ='$admin_name' AND org_id='$SessionOrgId' ") or die(mysqli_error($conn));
                    $name = mysqli_num_rows($getmedicine1);
                    if ($name > 0) {
                        $msg = 4; 
                    } else {
                        $getmedicine2 = mysqli_query($conn, "SELECT * FROM security WHERE status='1' AND security_id != '$security_id' AND email ='$email' ") or die(mysqli_error($conn));
                        $email_exists = mysqli_num_rows($getmedicine2);
                        if ($email_exists > 0) {
                            $msg = 5;
                        } else {
                            $getmedicine3 = mysqli_query($conn, "SELECT * FROM security WHERE status='1' AND security_id != '$security_id' AND contact ='$contact' ") or die(mysqli_error($conn));
                            $contact_exists = mysqli_num_rows($getmedicine3);
                            if ($contact_exists > 0) {
                                $msg = 6; 
                            } else {
                                 $before = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM security WHERE security_id='$security_id' AND org_id='$SessionOrgId'"));
                                if($SessionUserId == "1"){
                                $Updateregis = mysqli_query($conn, "UPDATE security SET admin_name ='$admin_name', email='$email', contact='$contact', role_id='$role_id', can_switch_doctor='$canSwitchDoctor', org_id='$org_id', modified_by='$SessionUserId' WHERE security_id='$security_id'") or die(mysqli_error($conn));
                                if ($Updateregis) {
                                    $msg = 2;
                                } else {
                                    $msg = "Error updating data.";
                                }
                            }else{
                                $Updateregis = mysqli_query($conn, "UPDATE security SET admin_name ='$admin_name', email='$email', contact='$contact', role_id='$role_id', can_switch_doctor='$canSwitchDoctor', org_id='$SessionOrgId', modified_by='$SessionUserId' WHERE security_id='$security_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
                                if ($Updateregis) {
                                    $msg = 2;
                                    $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM security WHERE security_id='$security_id' AND org_id='$SessionOrgId'"));

                           
                            audit_log($conn, "Security", "update", "security", $security_id, $before, $after);
                                } else {
                                    $msg = "Error updating data.";
                                }
                            }
                            }
                        }
                    }
                } else {
                   
                    if( $SessionOrgId === '0' ) {
                        $sqlLimit = "SELECT user_limit 
                            FROM organization 
                            WHERE status='1' 
                            AND org_id = '$org_id' ";
                            $resLimit = mysqli_query($conn, $sqlLimit) or die(mysqli_error($conn));
                        }else{
                            $sqlLimit = "SELECT user_limit 
                            FROM organization 
                            WHERE status='1' 
                            AND org_id = '$SessionOrgId' ";
                            $resLimit = mysqli_query($conn, $sqlLimit) or die(mysqli_error($conn));
                        }
                        if ($row = mysqli_fetch_assoc($resLimit)) {
                            $userLimit = (int)$row['user_limit'];
                             if( $SessionOrgId === '0' ) {
                            // Count only regular users (exclude SA and A types — admins don't consume slots)
                            $sqlCount = "SELECT COUNT(*) AS total_users
                                        FROM security
                                        WHERE org_id = '$org_id' AND status='1'
                                          AND security_type NOT IN ('SA','A')";
                             }else{
                                $sqlCount = "SELECT COUNT(*) AS total_users
                                        FROM security
                                        WHERE org_id = '$SessionOrgId' AND status='1'
                                          AND security_type NOT IN ('SA','A')";
                             }
                            $resCount = mysqli_query($conn, $sqlCount) or die(mysqli_error($conn));
                            $countRow = mysqli_fetch_assoc($resCount);
                            $totalUsers = (int)$countRow['total_users'];

                            // 3️⃣ Compare
                            if ($totalUsers >= $userLimit) {
                                $msg = 7;
                            } else {
                                $getname = mysqli_query($conn, "SELECT * FROM security WHERE status='1' AND admin_name ='$admin_name' ") or die(mysqli_error($conn));
                                $name = mysqli_num_rows($getname);

                                if ($name > 0) {
                                    $msg = 4;
                                } else {
                                    $getemail = mysqli_query($conn, "SELECT * FROM security WHERE status='1' AND email ='$email'") or die(mysqli_error($conn));
                                    $emailCount = mysqli_num_rows($getemail);

                                    if ($emailCount > 0) {
                                        $msg = 5;
                                    } else {
                                        $getcontact = mysqli_query($conn, "SELECT * FROM security WHERE status='1' AND contact ='$contact' ") or die(mysqli_error($conn));
                                        $contactCount = mysqli_num_rows($getcontact);

                                        if ($contactCount > 0) {
                                            $msg = 6;
                                        } else { 
                                           $getSecurityType = mysqli_query($conn, "
                                                SELECT security_type 
                                                FROM security 
                                                WHERE status = '1' AND security_id = '$SessionUserId'
                                            ") or die(mysqli_error($conn));

                                            $roleType = mysqli_fetch_assoc($getSecurityType);
                                            $securityType = $roleType['security_type'] ?? '';

                                            if ($securityType === 'SA') {
                                                $securityType = 'A';
                                            } elseif ($securityType === 'A') {
                                                $securityType = 'U';
                                            } else {
                                                $securityType = 'U';
                                            }

                                            if($SessionUserId == "1"){
                                            $Insertregis = mysqli_query($conn, "INSERT INTO security (security_id, admin_name, email, contact, security_password, role_id, can_switch_doctor, security_type, org_id, created_by, modified_by, status, create_date_time) VALUES ('$security_id','$admin_name','$email','$contact','$security_password','$role_id','$canSwitchDoctor','$securityType','$org_id','$SessionUserId','$SessionUserId','1','$datetime')") or die(mysqli_error($conn));

                                            if ($Insertregis) {
                                                $msg = 1;
                                                $newId = mysqli_insert_id($conn);
                            
                                                $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM security WHERE security_id='$newId'"));

                                                
                                                audit_log($conn, "Security", "create", "security", $newId, null, $after);
                                            } else {
                                                $msg = 0; 
                                            }
                                        }else{
                                                $Insertregis = mysqli_query($conn, "INSERT INTO security (security_id, admin_name, email, contact, security_password, role_id, can_switch_doctor, security_type, org_id, created_by, modified_by, status, create_date_time) VALUES ('$security_id','$admin_name','$email','$contact','$security_password','$role_id','$canSwitchDoctor','$securityType','$SessionOrgId','$SessionUserId','$SessionUserId','1','$datetime')") or die(mysqli_error($conn));
                
                                                if ($Insertregis) {
                                                    $msg = 1;
                                                    $newId = mysqli_insert_id($conn);

                            
                                    $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM security WHERE security_id='$newId'"));

                                    
                                    audit_log($conn, "Security", "create", "security", $newId, null, $after);
                                                } else {
                                                    $msg = 0; 
                                                }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }    
    }

// Assign P-code if the registered role is Pharmacist
if ($msg == 1 || $msg == 2) {
    ensureUserCodeColumn($conn);
    $targetId = ($msg == 1) ? (int)($newId ?? 0) : (int)$security_id;
    if ($targetId > 0) {
        $rnQ = mysqli_query($conn, "SELECT role_name FROM roles WHERE role_id='" . (int)$role_id . "' LIMIT 1");
        if ($rnQ && $rn = mysqli_fetch_assoc($rnQ)) {
            if (strtolower(trim($rn['role_name'])) === 'pharmacist') {
                $chkP = mysqli_fetch_assoc(mysqli_query($conn, "SELECT user_code FROM security WHERE security_id='$targetId' LIMIT 1"));
                if (empty($chkP['user_code']) || substr($chkP['user_code'], 0, 1) !== 'P') {
                    $pCode = generateUserCode($conn, 'P');
                    mysqli_query($conn, "UPDATE security SET user_code='$pCode' WHERE security_id='$targetId' AND org_id='$SessionOrgId'");
                }
            }
        }
    }
}

echo $msg;
?>







