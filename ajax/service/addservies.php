<?php
// IDOR_FIXED B-591
require_once("../../config/functions.php");
requireCan(empty($_POST['service_id']) ? 'add' : 'edit', 'services.php', 'ajax'); // FIX_B_1810
$SessionUserId = $_SESSION['security_id'] ?? '';
    $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

$msg = 0;
$service_id = $_POST['service_id'];
$service_name = $_POST['service_name'];
$service_price = $_POST['service_price'];
$services_gst = $_POST['services_gst'];
$organizations = $_POST['organizations'];

if($service_name != "" && $service_price != "" && $services_gst != "") {

    if($service_id !="") {
        if($SessionUserId == "1"){
            $getAdminDoctor = mysqli_query($conn, "SELECT service_name FROM services WHERE service_name='$service_name' AND  status='1' AND modified_by='$SessionUserId' AND service_id!='$service_id' AND org_id='$organizations'") or die(mysqli_error($conn));
        }else{
            $getAdminDoctor = mysqli_query($conn, "SELECT service_name FROM services WHERE service_name='$service_name' AND  status='1' AND modified_by='$SessionUserId' AND org_id='$SessionOrgId' AND service_id!='$service_id'") or die(mysqli_error($conn));
        }

        $result=mysqli_num_rows($getAdminDoctor);
        if ($result > 0) {  
            $msg = 3;
        }else{
            if($SessionUserId == "1"){
                if($organizations != ""){
                    $UpdateMenuData = mysqli_query($conn, "UPDATE services SET service_name='$service_name', price='$service_price', service_GST='$services_gst',modified_by='$SessionUserId',org_id='$organizations' WHERE service_id='$service_id'") or die(mysqli_error($conn));
                    if($UpdateMenuData) {
                        $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT 
                            s.*, 
                            o.organization_name
                        FROM 
                            services s
                        LEFT JOIN 
                            organization o ON s.org_id = o.org_id
                        WHERE 
                            s.service_id = '$service_id';
                        "));

                    audit_log($conn, "Services", "update", "services", $service_id, $before, $after);
                        $msg = 2;
                    }
                }
            } else{
                $UpdateMenuData = mysqli_query($conn, "UPDATE services SET service_name='$service_name', price='$service_price', service_GST='$services_gst',modified_by='$SessionUserId' WHERE service_id='$service_id' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
                if($UpdateMenuData) {
                    $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM services WHERE service_id='$service_id'"));

                    audit_log($conn, "Services", "update", "services", $service_id, $before, $after);
                    $msg = 2;
                }
            }
        }
    } else {
        if($SessionUserId == "1"){
            $getAdminDoctor = mysqli_query($conn, "SELECT service_name FROM services WHERE service_name='$service_name' AND status='1' AND org_id='$organizations' ") or die(mysqli_error($conn));
            $result=mysqli_num_rows($getAdminDoctor);
        }else{
            $getAdminDoctor = mysqli_query($conn, "SELECT service_name FROM services WHERE service_name='$service_name' AND status='1' AND org_id='$SessionOrgId'") or die(mysqli_error($conn));
            $result=mysqli_num_rows($getAdminDoctor);
        }
       
        if ($result > 0) {
            $msg = 3;
        }else{
            if($SessionUserId == "1"){
                if($organizations != ""){
                    $InserMenuData = mysqli_query($conn, "INSERT INTO services(service_name, price, service_GST,org_id, status,created_by, modified_by) VALUES ('$service_name', '$service_price', '$services_gst','$organizations','1','$SessionUserId', '$SessionUserId')") or die(mysqli_error($conn));
                    if($InserMenuData) {
                        $newId = mysqli_insert_id($conn);
                        $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM services WHERE service_id='$newId'"));

                        audit_log($conn, "Services", "create", "services", $newId, null, $after);
                        $msg = 1;
                    }
                }
            } else{
                if($SessionOrgId != ""){
                    $InserMenuData = mysqli_query($conn, "INSERT INTO services(service_name, price, service_GST,org_id, status,created_by, modified_by) VALUES ('$service_name', '$service_price', '$services_gst','$SessionOrgId','1','$SessionUserId', '$SessionUserId')") or die(mysqli_error($conn));
                    if($InserMenuData) {
                        $newId = mysqli_insert_id($conn);
                        $after = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM services WHERE service_id='$newId'"));

                        audit_log($conn, "Services", "create", "services", $newId, null, $after);
                        $msg = 1;
                    }
                }
            }
        }
    }
}else{
    echo "0";
}
echo $msg;