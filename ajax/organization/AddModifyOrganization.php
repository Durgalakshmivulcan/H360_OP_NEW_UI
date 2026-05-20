<?php
require_once("../../config/functions.php");


$msg = 0;
$SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

$org_id = $_POST['org_id'];

$organization_name = $_POST['organization_name'];
$contact = $_POST['contact'];
$email = $_POST['email'];
$description = $_POST['description'];
$longitude = $_POST['longitude'];
$latitude = $_POST['latitude'];
$gstNumber = $_POST['gstNumber'];
$tanNumber = $_POST['tanNumber'];
$logo_with_text = $_POST['logo_with_text'];
$logo_without_text = isset($_POST['logo_without_text']) ? $_POST['logo_without_text'] : "";
$address = $_POST['address'];
$userLimit =$_POST['userLimit'];
$opipaccess = isset($_POST['ipAccess']) ? $_POST['ipAccess'] : 'OP';


if($organization_name != "" && $contact != "" && $email != "" && $description != "" && $longitude != "" && $latitude != "" && $gstNumber != "" && $tanNumber != "" && $address != "") {
    if($org_id != "") {
        $getAdminOrganization = mysqli_query($conn, "SELECT * FROM organization WHERE status='1' AND org_id!='$org_id' AND organization_name LIKE '$organization_name'") or die(mysqli_error($conn));
        $result = mysqli_num_rows($getAdminOrganization);
        
        if ($result > 0) {
            $msg = 4;
        } else {
            $getAdminOrganizationByContact = mysqli_query($conn, "SELECT * FROM organization WHERE status='1' AND org_id!='$org_id' AND contact='$contact'") or die(mysqli_error($conn));
            $resultByContact = mysqli_num_rows($getAdminOrganizationByContact);
        
            if ($resultByContact > 0) {
                $msg = 5;
            } else {
                $getAdminOrganizationByEmail = mysqli_query($conn, "SELECT * FROM organization WHERE status='1' AND org_id!='$org_id' AND email='$email'") or die(mysqli_error($conn));
                $resultByEmail = mysqli_num_rows($getAdminOrganizationByEmail);
            
                if ($resultByEmail > 0) {
                    $msg = 6;
                } else {
                    $getAdminOrganizationByGSTNumber = mysqli_query($conn, "SELECT * FROM organization WHERE status='1' AND org_id!='$org_id' AND gstNumber='$gstNumber'") or die(mysqli_error($conn));
                    $resultByGST = mysqli_num_rows($getAdminOrganizationByGSTNumber);
                
                    if ($resultByGST > 0) {
                        $msg = 7;
                    } else {
                        $getAdminOrganizationByTANNumber = mysqli_query($conn, "SELECT * FROM organization WHERE status='1' AND org_id!='$org_id' AND tanNumber='$tanNumber'") or die(mysqli_error($conn));
                        $resultByTAN = mysqli_num_rows($getAdminOrganizationByTANNumber);
                    
                        if ($resultByTAN > 0) {
                            $msg = 8;
                        } else {
                            // $UpdateOrganizationData = mysqli_query($conn, "UPDATE organization SET organization_name='$organization_name', contact='$contact', email='$email', description='$description', address='$address', longitude='$longitude', latitude='$latitude', gstNumber='$gstNumber', tanNumber='$tanNumber', logo='$logo', modified_by='$SessionUserId' WHERE org_id='$org_id'") or die(mysqli_error($conn));
                            
                                $updateQuery = "UPDATE organization SET organization_name='$organization_name', contact='$contact', email='$email', description='$description', address='$address', longitude='$longitude', latitude='$latitude', gstNumber='$gstNumber', tanNumber='$tanNumber',modified_by='$SessionUserId', user_limit='$userLimit', opipaccess='$opipaccess'";

                                if ($logo_with_text != "") {
                                    $updateQuery .= ", logo='$logo_with_text'";
                                }
                                if ($logo_without_text != "") {
                                    $updateQuery .= ", logo_without_text='$logo_without_text'";
                                }
    
                                $updateQuery .= " WHERE org_id='$org_id'";
    
                                $UpdateOrganizationData = mysqli_query($conn, $updateQuery) or die(mysqli_error($conn));
                            if ($UpdateOrganizationData) {
                                $msg = 2;
                            }
                        }
                    }
                }
            }
        }
        
    } else{
        $getAdminOrganization = mysqli_query($conn, "SELECT * FROM organization WHERE status='1' AND organization_name LIKE '$organization_name'") or die(mysqli_error($conn));
        $result = mysqli_num_rows($getAdminOrganization);
        
        if ($result > 0) {
            $msg = 4;
        } else {
            $getAdminOrganizationByContact = mysqli_query($conn, "SELECT * FROM organization WHERE status='1' AND contact='$contact'") or die(mysqli_error($conn));
            $resultByContact = mysqli_num_rows($getAdminOrganizationByContact);
        
            if ($resultByContact > 0) {
                $msg = 5;
            } else {
                $getAdminOrganizationByEmail = mysqli_query($conn, "SELECT * FROM organization WHERE status='1' AND email='$email'") or die(mysqli_error($conn));
                $resultByEmail = mysqli_num_rows($getAdminOrganizationByEmail);
            
                if ($resultByEmail > 0) {
                    $msg = 6;
                } else {
                    $getAdminOrganizationByGSTNumber = mysqli_query($conn, "SELECT * FROM organization WHERE status='1' AND gstNumber='$gstNumber'") or die(mysqli_error($conn));
                    $resultByGST = mysqli_num_rows($getAdminOrganizationByGSTNumber);
                
                    if ($resultByGST > 0) {
                        $msg = 7;
                    } else {
                        $getAdminOrganizationByTANNumber = mysqli_query($conn, "SELECT * FROM organization WHERE status='1' AND tanNumber='$tanNumber'") or die(mysqli_error($conn));
                        $resultByTAN = mysqli_num_rows($getAdminOrganizationByTANNumber);
                    
                        if ($resultByTAN > 0) {
                            $msg = 8;
                        } else {
                            $InsertOrganizationData = mysqli_query($conn, "INSERT INTO organization(organization_name, contact, email, description, gstNumber, tanNumber, longitude, latitude,logo, logo_without_text, address, user_limit, opipaccess, status, created_date_time, created_by, modified_by) VALUES ('$organization_name','$contact','$email','$description','$gstNumber','$tanNumber','$longitude','$latitude','$logo_with_text','$logo_without_text','$address', '$userLimit', '$opipaccess', '1','$datetime','$SessionUserId', '$SessionUserId')") or die(mysqli_error($conn));
                            if($InsertOrganizationData) {
                                $msg = 1;
                            }
                        }
                    }
                }
            }
        }
    }
}
echo $msg;

?>
