<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';



$organizations = $_REQUEST['organizations'];


$getSizes=mysqli_query($conn,"SELECT sizes FROM bill_sizes WHERE status='1' AND pagetype='1' AND org_id='$SessionOrgId'");
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


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>HealthHub360</title>
    <link rel='shortcut icon' type='image/x-icon' href="../../assets/img/health.png" />

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

@media print {
      .button-class {
        display: none;
      }
    }

        body {
            background: white;
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

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #5D6975;
            text-decoration: underline;
        }

        header {
            padding: 10px 0;
            margin-bottom: 30px;
        }

        #logo {
            text-align: center;
            margin-bottom: 10px;
        }

        #logo img {
            width: 90px;
        }

        h1 {
            border-top: 1px solid  #5D6975;
            border-bottom: 1px solid  #5D6975;
            color: #5D6975;
            font-size: 2.4em;
            line-height: 1.4em;
            font-weight: normal;
            text-align: center;
            margin: 0 0 20px 0;
            /* background: url(dimension.png); */
        }

        #project {
            float: left;
        }

        #project span {
            color: #5D6975;
            text-align: right;
            width: 52px;
            margin-right: 10px;
            display: inline-block;
            font-size: 0.8em;
        }

        #company {
            float: right;
            text-align: right;
        }

        #project div,
        #company div {
            white-space: nowrap;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        table th,
        table td {
            text-align: center;
        }

        table th {
            padding: 5px 20px;
            color: #5D6975;
            border-bottom: 1px solid #C1CED9;
            white-space: nowrap;
            font-weight: normal;
        }

        table .service,
        table .desc {
            text-align: left;
        }

        table td {
            padding: 20px;
            text-align: right;
        }

        table td.service,
        table td.desc {
            vertical-align: top;
        }

        table td.unit,
        table td.qty,
        table td.total {
            font-size: 1.2em;
        }

        table td.grand {
            border-top: 1px solid #5D6975;;
        }

        #notices .notice {
            color: #5D6975;
            font-size: 1.2em;
        }

        footer {
            color: #5D6975;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #C1CED9;
            padding: 8px 0;
            text-align: center;
        }

        #qrcode {
            width: 150px;
            height: 150px;
            margin: 20px;
        }
    </style>
</head>
<body>
<form method="POST" id="FormId" action="" enctype="multipart/form-data" class="needs-validation" novalidate="">

   <input type="hidden" name="organizations" id="organizations" value="<?php echo $organizations ?>">

<?php

if($SessionUserId == "1"){
    $addorgid="";
}

  $Getpages1=mysqli_query($conn, "SELECT * FROM bill_sizes WHERE status='1' AND pagetype='1' $addorgid ORDER BY bill_size_id ASC") or die(mysqli_error($conn));
  $resSize=mysqli_fetch_object($Getpages1);

?>
<!-- <input type="hidden" name="size_id" id="size_id" value="<?php echo $resSize->bill_size_id ?>"> -->

<page size="A4" id="A4">
    <div class="row">
      <div class="col-10"></div>
      <div class="col-2">
      <button type="button" id="increase-top-margin" class="btn btn-primary button-class" onclick='insertmargine()'>
        <i class="fas fa-plus-square"></i>+
      </button>
      <button type="button" id="decrease-top-margin" class="btn btn-primary button-class" onclick='insertmargine()'>
        <i class="bi bi-dash-square"></i>-
      </button>

      </div>
    </div>
    <header class="clearfix">
      <div id="logo">
        <!-- <img src="logo.png"> -->
      </div>
      <!-- <h1>INVOICE 3-2-1</h1> -->
      <div id="company" class="clearfix">
        <div>
          <table>
            <tr>
              <td>
                <b>Date:- </b><br>
                <b>Name:- </b><br>
                <b>Age/Sex:- </b>
              </td>
              <td>
                <?php echo date("d/m/Y") ?><br>
                <?= $resAppoint['patient_name'] ?><br>
                <?= $resAppoint['age'] ?>/<?= $resAppoint['gender'] ?>
              </td>
            </tr>
          </table>
        </div>
      </div>
      <div id="project">
        <!-- <div><span>PROJECT</span> Website development</div>
        <div><span>CLIENT</span> John Doe</div>
        <div><span>ADDRESS</span> 796 Silver Harbour, TX 79273, US</div>
        <div><span>EMAIL</span> <a href="mailto:john@example.com">john@example.com</a></div>
        <div><span>DATE</span> August 17, 2015</div>
        <div><span>DUE DATE</span> September 17, 2015</div> -->
      </div>
    </header>
    <input type="hidden" name="id" id="id" value="<?= $resAppoint['appoint_id'] ?>"/>
    <main>
      <table>
        <thead>
          <tr>
            <th class="service"></th>
            <th class="desc"></th>
            <th></th>
            <th></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="service"><b>Consultant</b></td>
            <td class="desc"> : Dr.<?= $resDoc1['doctor_name'] ?></td>
            <td class="unit"></td>
            <td class="qty"></td>
            <td class="total"></td>
          </tr>
          <tr>
            <td class="service"><b>Consultant fee :</b></td>
            <td class="desc">
              <?php
                $value = "$payment";
                if (strcasecmp($value, "N/A") === 0) {
                    echo "N/A";
                } else {
                    echo "Rs. " . $value . "/-";
                }
              ?>
            </td>
            <td class="unit"></td>
            <td class="qty"></td>
            <td class="total"></td>
          </tr>
          <tr>
            <td class="service"><b>Payment Mode</b></td>
            <td class="desc"><b>Payment Number</b></td>
            <td class="unit">Payment Bank</td>
            <td class="qty"></td>
            <td class="total">Receipt Amount</td>
          </tr>
          <tr>
            <td class="service"><?php echo $payment_method; ?></td>
            <td class="desc"><?php echo $mobileNumber; ?>@ybl</td>
            <td class="unit"></td>
            <td class="qty"></td>
            <td class="total">
              <?php 
                $value = "$payment";
                if (strcasecmp($value, "N/A") === 0) {
                    echo "N/A";
                } else {
                    echo "Rs. " . $value . "/-";
                }
              ?>
            </td>
          </tr>
          <tr>
            <td class="grand total">In Words :</td>
            <td class="grand total"><?php echo convertNumber($value) ?>.</td>
            <td class="grand total"></td>
            <td class="grand total">GRAND TOTAL</td>
            <td class="grand total">
              <?php
                $value = "$payment";
                if (strcasecmp($value, "N/A") === 0) {
                    echo "N/A";
                } else {
                    echo "Rs. " . $value . "/-";
                }
              ?>
            </td>
          </tr>
        </tbody>
      </table> 
      <div id="qrcode">
      </div>
    </main>
    <div class="row">
        <div class="col-10"></div>
        <div class="col-2">
          <button type="button" id="increase-bottom-margin" class="btn btn-primary button-class" onclick='insertmargine()'>
            <i class="fas fa-plus-square"></i>+
          </button>
          <button type="button" id="decrease-bottom-margin" class="btn btn-primary button-class" onclick='insertmargine()'>
            <i class="bi bi-dash-square"></i>-
          </button>
        </div>
      </div>
      <!-- <center>
          <a href="../../billsizes.php" type="button"  class="btn btn-primary mt-5" style="width:100px; " type="button"  value="Back" onclick='setInputValues()'></a>
        </center> -->
          <div class="card-footer text-center">
            <!-- <button type="button" class="btn btn-primary button-class" name="saveData" id="saveData" value="" onclick="insertmargine()">Submit</button> -->
        </div>
    <!-- <footer> </footer> -->
</page>
</form>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<script>
    // Function to generate a QR code
    // function generateQRCode(data) {
    //     var billId = data[0].bill_id;
    //     var appointRegisterId = data[0].appoint_register_id;
    //     var patientId = data[0].appoint_unicode;
    //     var patientName = data[0].patient_name;
    //     var qrcode = new QRCode(document.getElementById("qrcode"), {
    //         text: `Bill_Id: ${billId}\nAppointment_Id: ${appointRegisterId}\nPatient_Id: ${patientId}\nPatient_Name: ${patientName}`,
    //         width: 128,
    //         height: 128,
    //     });
    // }

    // Fetch data and generate the QR code
    // var id = $("#id").val();
    // $.ajax({
    //     url: "ajax/billing/QRCodePatientData.php",
    //     type: 'POST',
    //     data: {
    //         id: id
    //     },
    //     dataType: 'json',
    //     success: function (response) {
    //         generateQRCode(response);
    //     },
    //     error: function (err) {
    //         console.log(err);
    //     }
    // });

    // Margin adjustment
    const mainContent = document.querySelector('#A4');
    var topMargin = parseInt(getURLParam('topMargin')) || 100;
    var bottomMargin = parseInt(getURLParam('bottomMargin')) || 0;

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
        history.pushState({}, '', url)
    }


function insertmargine() {
  var organizations = $('#organizations').val();

  $.ajax({
    url: 'insertmargin1.php',
    type: 'POST',
    data: {
      'topMargin': topMargin,
      'bottomMargin': bottomMargin,
      'organizations': organizations
    },
    success: function(data) {
      console.log(data);
      if (data == 1) {
        // swal('', 'Added Successfully', 'success');
      }
    },
    error: function(err) {
      console.log(err);
    }
  });
}




// function postmargines(){

// }

        // if (organizations) {
        //     // Do something with the ID, e.g., display it on the page
        //     document.write("Received ID: " + organizations);
        // } else {
        //     document.write("No ID received.");
        // }
       

</script>


</body>
</html>
