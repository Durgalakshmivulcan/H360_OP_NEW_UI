<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

$organizations2 = $_REQUEST['organizations2'];

$getSizes=mysqli_query($conn,"SELECT sizes FROM bill_sizes WHERE    status='1' AND pagetype='3' AND org_id='$SessionOrgId'");
$resData=mysqli_fetch_object($getSizes);

$getSingleSize=mysqli_query($conn,"SELECT w_size, h_size FROM pagessize WHERE size_id='$resData->sizes' AND status='1'");
$resSingleData=mysqli_fetch_object($getSingleSize); 

$width = '21cm';
$height = '29.7cm';

if($width !=""){
  $width=$resSingleData->w_size;
}
if($height !=""){
  $height=$resSingleData->w_size;
}

?>


<style>

@media print {
      .button-hide {
        display: none;
      }
    }

page {
        background: white;
        display: block;
        margin: 0 auto;
        margin-bottom: 0.5cm;
        /* box-shadow: 0 0 0.5cm rgba(0,0,0,0.5); */
        position: relative; 
        font-family: Arial, sans-serif; 
        font-size: 12px; 
        font-family: Arial;
            }
      page[size="A4"] {  
        width: <?= $width ?>;
        height: <?= $height ?>; 
      }


.form-group {
    margin-bottom: -4px;
}

#qrcodeOne{
  width: 150px;
  height: 150px;
  margin: 20px;
}
#qrcodeTwo{
  width: 150px;
  height: 150px;
  margin: 20px 0px 0px 280px;
  text-align: right;
}
</style>

<meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/css/bootstrap-tokenfield.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel='shortcut icon' type='image/x-icon' href="../../assets/img/health.png" />
        
<div class="col-12 col-md-12 col-lg-12 mt-5">                      
    <form method="POST" id="FormId" action="" enctype="multipart/form-data" class="needs-validation" novalidate="">

    <input type="hidden" name="organizations2" id="organizations2" value="<?php echo $organizations2 ?>">
<?php

$addorgid= "AND org_id='$SessionOrgId'";

if($SessionUserId == "1"){
$addorgid="";
}

  $Getpages1=mysqli_query($conn, "SELECT * FROM bill_sizes WHERE status='1' AND pagetype='3' $addorgid ORDER BY bill_size_id ASC") or die(mysqli_error($conn));
$resSize=mysqli_fetch_object($Getpages1);
?>  
<input type="hidden" name="size_id2" id="size_id2" value="<?php echo $resSize->bill_size_id ?>">
    <page size="A4" id="A4">
        <input type="hidden" name="menu_id" id="menu_id" value="" >

        <div class="row">
                         <div class="col-12 text-right">
                         <button id="increase-top-margin" class="btn btn-primary button-hide" onclick="insertmargine2()">
                            <i class="fas fa-plus-square"></i>+
                        </button>
                        <button id="decrease-top-margin" class="btn btn-primary button-hide" onclick="insertmargine2()">
                            <i class="bi bi-dash-square"></i>-
                        </button>
                        </div>

            <div class="form-group col-lg-7 col-sm-12 border-end">
            <!-- <div class="row"> -->
                <!-- <div class="form-group col-lg-4  col-sm-12" style="margin-top: 0px; margin-right: 0px;"> -->
                    <?php
                    
                    $qry = mysqli_query($conn, "SELECT * FROM security WHERE status='1' AND security_id='$SessionUserId'") or die(mysqli_error($conn));
                    
                    $ProfileData=mysqli_fetch_object($qry);
                    ?>
                    <!-- <img alt="image" src="img/<?php echo $ProfileData->image_url; ?> " width="290px" height="200px" style="margin-top: 0px; margin-right: 200px;"> -->
                <!-- </div> -->
                <!-- <div class="form-group col-lg-8 col-sm-12" style="text-align: right"> -->
                    <!-- <h2 class="mt-5"> -->
                        <?php
                        $qry = mysqli_query($conn, "SELECT * FROM organization WHERE org_id='$SessionOrgId'") or die(mysqli_error($conn));
                        $res = mysqli_fetch_object($qry);
                        ?>
                        <!-- <b><?= $res->organization_name?></b> -->
                    <!-- </h2> -->
                    <!-- <p class="" style="padding-left: 25px"> --><!-- </p> -->
                <!-- </div> -->
            <!-- </div> -->
        </div>
        <div class="form-group col-lg-5 col-sm-12">
            <div class="row">
                <?php

                $gettest=mysqli_query($conn,"SELECT * From prescripition WHERE Status='1' ORDER BY prescription_id ASC");
                $restest=mysqli_fetch_object($gettest);

                        $id=$restest->appoint_register_id;
                        $id2=$restest->patient_uid;
                    if($SessionUserId == "1"){
                    $getappoint = mysqli_query($conn, "SELECT * FROM appointment_online WHERE appoint_status='1' AND appoint_register_id='$id' AND appoint_unicode='$id2' ");
                    }else{
                        $getappoint = mysqli_query($conn, "SELECT * FROM appointment_online WHERE appoint_status='1' AND appoint_register_id='$id' AND appoint_unicode='$id2' AND org_id='$SessionOrgId'");
                    }

                    // echo "SELECT * FROM appointment_online WHERE appoint_status='1' AND appoint_register_id='$id' AND appoint_unicode='$id2' AND org_id='$SessionOrgId'";
                    $resapport=mysqli_fetch_object($getappoint);
                    $bill_id = $resapport->bill_id;

                    $getuid=mysqli_query($conn,"SELECT * FROM prescripition WHERE status='1' AND patient_uid='$resapport->appoint_unicode'") or die(mysqli_error($conn));
                    $resuid=mysqli_fetch_object($getuid);

                    $getdoctime = mysqli_query($conn, "SELECT * FROM doctors_time_slot WHERE status='1' AND doctors_time_id='$resapport->doctor_name'");
                    $restime=mysqli_fetch_object($getdoctime);

                    $getdoc = mysqli_query($conn, "SELECT * FROM doctors WHERE status='1' AND doc_id='$restime->doctorName_registrationNumber'");
                    $resdoc=mysqli_fetch_object($getdoc);

                    $getservice = mysqli_query($conn, "SELECT * FROM services WHERE status='1' AND service_id='$resdoc->doctor_services'");
                    $resservice=mysqli_fetch_object($getservice);

                    
                    
                ?>
                    <!-- <div class="form-group col-lg-7 col-sm-12">
                    <p class="mt-5">
                    Patient Name : <?= $resapport->patient_name?><br>
                    Age/Sex : <?= $resapport->age ?> / <?= $resapport->gender?><br>
                    Bill No: <?= $bill_id ?> <br>
                    UMR No : V4U122498
                    </p>
                    </div>
                    <div class="form-group col-lg-5 col-sm-12">

                    <p class="mt-5 pt-4">
                    Bill Date : <?= $resuid->create_date ?><br>
                    Phone : <?= $resapport->mobile_number?>
                    </p>
                    </div> -->
                </div>
                </div>
            </div>
            <!-- <hr style="border:1px solid gray"> -->
            <input type="hidden" name="id" id="id" value="<?= $resapport->appoint_id ?>" />
            <div class="row" style="margin-top: 170px">
            <div class="col-lg-2" style="padding-left:50px;">
            <?=$resuid->create_date  ?>
            </div>
            <div class="col-lg-5" style="padding-left:300px;margin-top:-20px">
            <?= $resapport->patient_name ?>
        
            </div>
            <div class="col-lg-10" style="padding-left:800px;margin-top:-20px">
            <?= $resapport->age ?>/<?= $resapport->gender ?>
            </div>
            
            </div>
            <!-- <hr style="border:1px solid gray"> -->
            <div class="row border-bottom"  style=" padding-top: 20px;">
                <?php
                 $gettest=mysqli_query($conn,"SELECT * From prescripition WHERE Status='1' ORDER BY prescription_id ASC");
                 $restest=mysqli_fetch_object($gettest);
 
                         $id=$restest->appoint_register_id;
                         $id2=$restest->patient_uid;
                if($SessionUserId == "1"){
                $getappoint = mysqli_query($conn, "SELECT * FROM appointment_online WHERE appoint_status='1' AND appoint_register_id='$id' AND appoint_unicode='$id2'");
                }else{
                    $getappoint = mysqli_query($conn, "SELECT * FROM appointment_online WHERE appoint_status='1' AND appoint_register_id='$id' AND appoint_unicode='$id2' AND org_id='$SessionOrgId'");
                }
                while($resapport=mysqli_fetch_object($getappoint)){
                    $getuid=mysqli_query($conn,"SELECT * FROM prescripition WHERE status='1' AND patient_uid='$resapport->appoint_unicode' ORDER BY prescription_id DESC") or die(mysqli_error($conn));
                    $resuid=mysqli_fetch_object($getuid);
                    $datetime=$resuid->create_date_time;
                    // echo $resuid->test_id;

                    $getdoctime = mysqli_query($conn, "SELECT * FROM doctors_time_slot WHERE status='1' AND doctors_time_id='$resapport->doctor_name'");
                    $restime=mysqli_fetch_object($getdoctime);


                    $getdoc = mysqli_query($conn, "SELECT * FROM doctors WHERE status='1' AND doc_id='$restime->doctorName_registrationNumber'");
                    $resdoc=mysqli_fetch_object($getdoc);

                    $getservice = mysqli_query($conn, "SELECT * FROM services WHERE status='1' AND service_id='$resdoc->doctor_services'");
                    $resservice=mysqli_fetch_object($getservice);
                ?>

                    <table class="table" style="width: 100%;">
                        <thead class="text-center" style="text-align:left;">
                            <tr>
                                <th>S.no</th>
                                <th>Date</th>
                                <th>Test</th>
                                <th>GST</th>
                                <th>Test Amount</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                                
                        <?php
                        
                        $gettest= mysqli_query($conn, "SELECT * FROM tests WHERE status='1' AND test_id='$resuid->test_id'");
                        $restest=mysqli_fetch_object($gettest);
                        // $sum += $restest['Value'];
                        // $test=$restest->test_name;
                        //  echo "SELECT * FROM tests WHERE test_id='$resuid->test_id'";

                        $test_ids = $resuid->test_id;

                        // Convert the comma-separated string to an array of integers
                        $test_id_array = array_map('intval', explode(',', $test_ids));
        
                        // Convert the array back to a comma-separated string with single quotes for the IN clause
                        $test_id_string = "'" . implode("','", $test_id_array) . "'";
        
                        // Construct the query using the IN clause
                        $query = "SELECT * FROM tests WHERE test_id IN ($test_id_string)";
        
                        $gettests = mysqli_query($conn, $query) or die(mysqli_error($conn));

                        if (mysqli_num_rows($gettests) > 0) {
                            $total = 0;
                            $i = 1;
                            while ($res6 = mysqli_fetch_object($gettests)){
                                $date= $res6->create_date_time;
                                $test = $res6->test_name;
                                $testprice = $res6->test_price;
                                $testgst = $res6->test_gst;

                                $total += $testprice*$res6->test_gst/100+$testprice;
                            ?>
                    <tr>
                        <td><?=$i++;?></td>
                        <td><?= $resuid->create_date_time ?></td>
                        <td><?php echo $test ?></td>
                            <td><?php echo $testgst ?>%</td>
                        <td>
                    <?php    
                    if (strcasecmp($testprice, "N/A") === 0) {
                            echo "N/A";
                        } else {
                            echo "Rs. " . $testprice . "/-";
                            }
                    ?></td>

                    </tr>
                    <?php
                        }
                    } else {
                            // No records found
                            echo "<h5><b>No tests found for this Patient ID.</b></h5>";
                        }
                    ?>
                    
                    </tbody>

                </table>
                <div class="form-group col-lg-12" style=" padding-top: 0px;">       
                    <div class="row">
                            <div class="form-group col-lg-7 col-sm-6" style=" padding-left:20px;" >
                               <h5> <p ><b> In Words </b>  <span > : </span> <span >    
                                <?php echo convertNumber($total); ?></span></p></h5>
                            </div>
                        <div class="form-group col-lg-10 col-sm-9" style=" padding-left:550px;margin-top:-40px" >
                            <h4>Total Amount  <span style="" > : </span> <span style="">    
                            Rs <?php  echo number_format($total, 2, '.', '');?> /-</span></h4>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            }
            ?>
            <div class="row">

                <div class="row">
                    <div class="form-group col-lg-7 col-sm-12 ">
                        <div class="row">
                            <div class="form-group col-lg-12 col-sm-12">
                            
                            </div>
                        </div>
                    </div>  
                    <div class="form-group col-lg-5 col-sm-12">
                        <div class="row">
                            <div class="form-group col-lg-4 col-sm-12"></div>
                            <div class="form-group col-lg-8 col-sm-12"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-7 col-sm-12 ">
                        <div class="row">
                            <div class="form-group col-lg-12 col-sm-12"></div>
                        </div>
                    </div>
                        <div class="form-group col-lg-5 col-sm-12">
                            <div class="row">
                                <div class="form-group col-lg-4 col-sm-12"></div>
                                <div class="form-group col-lg-8 col-sm-12"></div>
                            </div>
                        </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-lg-7 col-sm-12 " >
                    <div class="row mt-5">
                        <div class="form-group col-lg-12 col-sm-12" id="qrcodeOne">
                        <!-- <img src="img/barcode.png" alt="" style="width:250px; float:left"> -->
                        </div>
                    </div>
                </div>
                <div class="form-group col-lg-5 col-sm-12">
                    <div class="row">
                        <!-- <div class="form-group col-lg-12 col-sm-12" id="qrcodeTwo"> -->
                        <!-- <img src="img/barcode.png" alt="" style="width:250px; float:right"> -->
                        <!-- </div> -->
                        <div class="form-group col-lg-8 col-sm-12"></div>
                    </div>
                    <div class="col-12 text-right" >
                        <button id="increase-bottom-margin" class="btn btn-primary button-hide" onclick="insertmargine2()">
                            <i class="fas fa-plus-square"></i>+
                        </button>
                        <button id="decrease-bottom-margin" class="btn btn-primary button-hide" onclick="insertmargine2()">
                            <i class="bi bi-dash-square"></i>-
                        </button>
                    </div>
                </div>
            
            </div>
            <div class="card-footer text-center">
                <!-- <button type="button" class="btn btn-primary button-hide" name="saveData" id="saveData" value="" onclick="insertmargine2()">Submit</button> -->
            </div>
        </div> 
     </page>     
    </form>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/bootstrap-tokenfield.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>





    // QR Code One
// function generateQRCodeOne(data) {
//     var billId = data[0].bill_id;
//     var appointRegisterId = data[0].appoint_register_id;
//     var patientId = data[0].appoint_unicode;
//     var patientName = data[0].patient_name;
//     var qrcode = new QRCode(document.getElementById("qrcodeOne"), {
//         text: `Bill_Id: ${billId}\nAppointment_Id: ${appointRegisterId}\nPatient_Id: ${patientId}\nPatient_Name: ${patientName}`,
//         width: 128,
//         height: 128,
//     });
// }
//   var id= $("#id").val();
//   $.ajax({
//       url: "ajax/billing/TestQRCODEs.php",
//       type: 'POST',
//       data: {
//         id:id
//       },
//       dataType: 'json',
//       success: function (response) {
//           console.log(response);
//           generateQRCodeOne(response);
//       },
//       error: function (err) {
//           console.log(err);
//       }
// });

// QR Code Two
// function generateQRCodeTwo(data) {
//     var billId = data[0].bill_id;
//     var appointRegisterId = data[0].appoint_register_id;
//     var patientId = data[0].appoint_unicode;
//     var patientName = data[0].patient_name;
//     var qrcode = new QRCode(document.getElementById("qrcodeTwo"), {
//         text: `Bill_Id: ${billId}\nAppointment_Id: ${appointRegisterId}\nPatient_Id: ${patientId}\nPatient_Name: ${patientName}`,
//         width: 128,
//         height: 128,
//     });
// }
//   var id= $("#id").val();
//   $.ajax({
//       url: "ajax/billing/TestQRCODEs.php",
//       type: 'POST',
//       data: {
//         id:id
//       },
//       dataType: 'json',
//       success: function (response) {
//           console.log(response);
//           generateQRCodeTwo(response);
//       },
//       error: function (err) {
//           console.log(err);
//       }
// });


const mainContent = document.querySelector('#A4');
      let topMargin = parseInt(getURLParam('topMargin')) || 100;
      let bottomMargin = parseInt(getURLParam('bottomMargin')) || 0;

      mainContent.style.marginTop = topMargin + 'px';
      mainContent.style.marginBottom = bottomMargin + 'px';

      // Button events for top margin adjustment
      const increaseTopMarginButton = document.getElementById('increase-top-margin');
      const decreaseTopMarginButton = document.getElementById('decrease-top-margin');

      increaseTopMarginButton.addEventListener('click', function() {
        topMargin += 10;
        mainContent.style.marginTop = topMargin + 'px';
        updateURLParams();
      });

      decreaseTopMarginButton.addEventListener('click', function() {
        if (topMargin > 100) {
          topMargin -= 10;
          mainContent.style.marginTop = topMargin + 'px';
          updateURLParams();
        }
      });

      // Button events for bottom margin adjustment
      const increaseBottomMarginButton = document.getElementById('increase-bottom-margin');
      const decreaseBottomMarginButton = document.getElementById('decrease-bottom-margin');

      increaseBottomMarginButton.addEventListener('click', function() {
        bottomMargin += 10;
        mainContent.style.marginBottom = bottomMargin + 'px';
        updateURLParams();
      });

      decreaseBottomMarginButton.addEventListener('click', function() {
        if (bottomMargin > 0) {
          bottomMargin -= 10;
          mainContent.style.marginBottom = bottomMargin + 'px';
          updateURLParams();
        }
      });

      function getURLParam(paramName) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(paramName);
      }

      function updateURLParams() {
        const url = new URL(window.location.href);
        url.searchParams.set('topMargin', topMargin);
        url.searchParams.set('bottomMargin', bottomMargin);
        history.pushState({}, '', url);
      }
      

function insertmargine2() {

var size_id=$('#size_id2').val();
var organizations2=$('#organizations2').val();
  $.ajax({
  url: 'insertmargin3.php',
  type: 'POST',
  data: {
      'size_id':size_id,
      'topMargin':topMargin,
      'bottomMargin':bottomMargin,
      'organizations2':organizations2
  },
  success: function(data) {
      console.log(data);
      if (data == 1) {
        //   swal('', 'Added Successfully', 'success');
      }
  },
  error: function(err) {
      console.log(err);
  }
});
}
</script>
