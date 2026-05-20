<?php
require_once("ajax/header.php");
requireCan('view', basename(__FILE__)); // FIX_B_1810

?>

<style>
    .btn-group, .btn-group-vertical {
    position: relative;
    display: -webkit-inline-box;
    display: -ms-inline-flexbox;
    display: inline-flex;
    vertical-align: middle;
    margin-top:20px;
   
};

</style>
<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style ">
            <li class="breadcrumb-item">
              <h4 class="page-title m-b-0">Print Sizes</h4>
            </li>
            <li class="breadcrumb-item">
                <a href="dashboard.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </a>
            </li>
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item">Add & Modify Pages</li>
        </ul>
        
        <ul class="breadcrumb breadcrumb-style" >
            <li class="breadcrumb-item" style="z-index: 1; position: absolute; left: 91%; top: 0;">
                <div class="form-group">
                    
                </div>
            </li>
        </ul>

        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Consultation page</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Prescription page</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Test page</button>
            </li>
        </ul>

        <div class="col-12 col-md-12 col-lg-12">    
            <div class="card">
                <div class="card-header">
                    <h4>Pages Editor</h4>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="pills-tabContent">
                        <!-- Tab One -->
                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                            <form method="POST" id="FormId" action="" >
                                <?php
                                    $Getpages1=mysqli_query($conn, "SELECT * FROM bill_sizes WHERE status='1' AND pagetype='1' AND org_id='$SessionOrgId' ORDER BY bill_size_id ASC") or die(mysqli_error($conn));
                                    $resSize=mysqli_fetch_object($Getpages1);

                                    $GetValue=mysqli_query($conn, "SELECT size_id,size_name FROM pagessize WHERE status='1' AND size_id='$resSize->sizes' ORDER BY size_id ASC") or die(mysqli_error($conn));
                                ?>
                                <input type="hidden" name="size_id" id="size_id" value="<?php echo $resSize->bill_size_id ?>">
                                <div class="row">
                                    <div class="form-group col-lg-4 col-sm-12">
                                        <label for="sizes" class=""> Page Size <span class="text-danger">*</span></label>
                                        <select class="form-control form-select " name="sizes" id="sizes" >
                                            <?php
                                            while($Values=mysqli_fetch_object($GetValue)){
                                                ?>
                                            <option value="<?php echo $Values->size_id ?>"><?php echo $Values->size_name ?></option>
                                            <?php
                                            }
                                            $Getpages=mysqli_query($conn, "SELECT size_id,size_name FROM pagessize WHERE status='1' ORDER BY size_id ASC") or die(mysqli_error($conn));
                                            while($Respagesize=mysqli_fetch_object($Getpages)){
                                            ?>
                                                <option value="<?= $Respagesize->size_id?>"><?= $Respagesize->size_name?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <?php 
                                        $SessionUserId = $_SESSION['security_id'] ?? '';
                                        $SessionRoleId = $_SESSION['role_id'] ?? '';
                                        $SessionOrgId = $_SESSION['org_id'] ?? '';

                                        if($SessionUserId == "1" && $SessionRoleId=="1"){
                                    ?>
                                    <div class="form-group col-lg-4 col-sm-12">                                       
                                        <label for="Organization" class="Organization"> Organization <span class="text-danger">*</span></label>
                                        <select class="form-control form-select" name="organizations" id="organizations" onchange="getsizeid()">    
                                            <option value="">Select Organization</option>
                                            <?php
                                            $GetOrganization=mysqli_query($conn, "SELECT org_id, organization_name FROM organization WHERE status='1' ORDER BY org_id ASC") or die(mysqli_error($conn));
                                            while($ResOrganization=mysqli_fetch_object($GetOrganization)){
                                            ?>
                                                <option value="<?= $ResOrganization->org_id?>"><?= $ResOrganization->organization_name?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <?php
                                        } else {
                                    ?>
                                    <input type="hidden" id="organizations" name="organizations" value="<?= $SessionOrgId ?>" />
                                    <?php
                                        }
                                    ?>
                                
                                    <!-- <div class="form-group w-100">
                                        <textarea id="ckeditor_CP" name="ckeditor_CP" value=""><?php echo $resSize->note ?></textarea>
                                    </div> -->

                                    <div class="card-footer text-left">
                                        <a type="button" target="_blank" class="dropdown-item btn btn-primary" style="width:120px" onclick="setInputValues()">Sample view</a>
                                    </div>
                                </div>  
                                <div class="card-footer text-center">
                                    <?php if (userCan('add', 'billsizes.php') || userCan('edit', 'billsizes.php')) { /* FIX_B_1810 */ ?><button class="btn btn-primary" name="saveData" id="saveData" value="">Submit</button><?php } ?>
                                </div>
                            </form>
                        </div>

                        <!-- tab two -->
                        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                            <form method="POST" id="FormId1" action="" >
                                <?php
                                    $Getpages1=mysqli_query($conn, "SELECT * FROM bill_sizes WHERE status='1' AND pagetype='2' AND org_id='$SessionOrgId' ORDER BY bill_size_id ASC") or die(mysqli_error($conn));
                                    $resSize1=mysqli_fetch_object($Getpages1);

                                    $GetValue1=mysqli_query($conn, "SELECT size_id,size_name FROM pagessize WHERE status='1' AND size_id='$resSize1->sizes' ORDER BY size_id ASC") or die(mysqli_error($conn));
                                ?>
                                <input type="hidden" name="size_id1" id="size_id1" value="<?php echo $resSize1->bill_size_id ?>">
                                <div class="row">   
                                    <div class="form-group col-lg-4 col-sm-12">
                                        <label for="sizes" class=""> Page Size <span class="text-danger">*</span></label>
                                        <select class="form-control form-select " name="sizes1" id="sizes1" >
                                            <?php
                                                while($Respagesize1=mysqli_fetch_object($GetValue1)){
                                            ?>
                                                <option value="<?= $Respagesize1->size_id?>"><?= $Respagesize1->size_name?></option>
                                            <?php
                                                }
                                                $Getpages=mysqli_query($conn, "SELECT size_id,size_name FROM pagessize WHERE status='1' ORDER BY size_id ASC") or die(mysqli_error($conn));
                                                while($Respagesize=mysqli_fetch_object($Getpages)){
                                            ?>
                                                <option value="<?= $Respagesize->size_id?>"><?= $Respagesize->size_name?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <?php 
                                        $SessionUserId = $_SESSION['security_id'] ?? '';
                                        $SessionRoleId = $_SESSION['role_id'] ?? '';
                                        $SessionOrgId = $_SESSION['org_id'] ?? '';

                                        if($SessionUserId == "1" && $SessionRoleId=="1"){
                                    ?>
                                    <div class="form-group col-lg-4 col-sm-12">                                       
                                        <label for="Organization" class="Organization"> Organization <span class="text-danger">*</span></label>
                                        <select class="form-control form-select" name="organizations1" id="organizations1" onchange="getsizeid1()">
                                            <option value="">Select Organization</option>
                                            <?php
                                                $GetOrganization=mysqli_query($conn, "SELECT org_id, organization_name FROM organization WHERE status='1' ORDER BY org_id ASC") or die(mysqli_error($conn));
                                                while($ResOrganization=mysqli_fetch_object($GetOrganization)){
                                            ?>
                                                <option value="<?= $ResOrganization->org_id?>"><?= $ResOrganization->organization_name?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <?php
                                        } else {
                                    ?>
                                    <input type="hidden" id="organizations1" name="organizations1" value="<?= $SessionOrgId ?>" />
                                    <?php
                                        }
                                    ?>
                                    <!-- <div class="form-group w-100">
                                        <textarea id="ckeditor_PP"><?php echo $resSize1->note ?></textarea>
                                    </div> -->
                                    <div class="card-footer text-left">
                                            <a type="button" target="_blank" class="dropdown-item btn btn-primary" style="width:120px" onclick="setInputValues1()">Sample view</a>
                                    </div>
                                </div>
                                <div class="card-footer text-center">
                                    <?php if (userCan('add', 'billsizes.php') || userCan('edit', 'billsizes.php')) { /* FIX_B_1810 */ ?><button class="btn btn-primary" name="saveData1" id="saveData1" value="">Submit</button><?php } ?>
                                </div>
                            </form> 
                        </div>

                        <!-- Tab three -->
                        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0">
                            <form method="POST" id="FormId2" action="" >
                                <?php
                                    $Getpages1=mysqli_query($conn, "SELECT * FROM bill_sizes WHERE status='1' AND pagetype='3'  ORDER BY bill_size_id ASC") or die(mysqli_error($conn));
                                    $resSize=mysqli_fetch_object($Getpages1);

                                    $GetValue2=mysqli_query($conn, "SELECT size_id,size_name FROM pagessize WHERE status='1' AND size_id='$resSize1->sizes' ORDER BY size_id ASC") or die(mysqli_error($conn));
                                ?>
                                <input type="hidden" name="size_id2" id="size_id2" value="<?php echo $resSize->bill_size_id ?>">
                                <div class="row">   
                                    <div class="form-group col-lg-4 col-sm-12">
                                        <label for="sizes" class=""> Page Size <span class="text-danger">*</span></label>
                                        <select class="form-control form-select " name="sizes2" id="sizes2" >
                                            <?php
                                                while($Values1=mysqli_fetch_object($GetValue2)){
                                                ?>
                                            <option value="<?php echo $Values1->size_id ?>"><?php echo $Values1->size_name ?></option>
                                            <?php
                                                }
                                                $Getpages=mysqli_query($conn, "SELECT size_id,size_name FROM pagessize WHERE status='1' ORDER BY size_id ASC") or die(mysqli_error($conn));
                                                while($Respagesize=mysqli_fetch_object($Getpages)){
                                            ?>
                                                <option value="<?= $Respagesize->size_id?>"><?= $Respagesize->size_name?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <?php 
                                        $SessionUserId = $_SESSION['security_id'] ?? '';
                                        $SessionRoleId = $_SESSION['role_id'] ?? '';
                                        $SessionOrgId = $_SESSION['org_id'] ?? '';

                                        if($SessionUserId == "1" && $SessionRoleId=="1"){
                                    ?>
                                     <div class="form-group col-lg-4 col-sm-12">                                       
                                        <label for="Organization" class="Organization"> Organization <span class="text-danger">*</span></label>
                                        <select class="form-control form-select" name="organizations2" id="organizations2" onchange="getsizeid1()">
                                            <option value="">Select Organization</option>
                                            <?php
                                                $GetOrganization=mysqli_query($conn, "SELECT org_id, organization_name FROM organization WHERE status='1' ORDER BY org_id ASC") or die(mysqli_error($conn));
                                                while($ResOrganization=mysqli_fetch_object($GetOrganization)){
                                            ?>
                                                <option value="<?= $ResOrganization->org_id?>"><?= $ResOrganization->organization_name?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <?php
                                        } else {
                                    ?>
                                    <input type="hidden" id="organizations2" name="organizations2" value="<?= $SessionOrgId ?>" />
                                    <?php
                                        }
                                    ?>
                                
                                    <!-- <div class="form-group w-100">
                                        <textarea id="ckeditor_TP"><?php echo $resSize->note ?></textarea>
                                    </div>   -->
                                    
                                    <div class="card-footer text-left">
                                        <a type="button" target="_blank" class="dropdown-item btn btn-primary" style="width:120px" onclick="setInputValues2()">Sample view</a>
                                    </div>

                                    <div class="card-footer text-center">
                                        <?php if (userCan('add', 'billsizes.php') || userCan('edit', 'billsizes.php')) { /* FIX_B_1810 */ ?><button class="btn btn-primary" name="saveData2" id="saveData2" value="">Submit</button><?php } ?>
                                    </div>
                                </div>    
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <form action="" method="POST" id="deleteFormId">
            <input type="hidden" name="deleteID" id="deleteID" value="" />
        </form>

    </section>
</div>



<?php require_once("ajax/footer.php") ?>

<script>

$("document").ready(function() {
});

// $(function () {
//     CKEDITOR.replace("ckeditor_CP");
//     CKEDITOR.replace("ckeditor_PP");
//     CKEDITOR.replace("ckeditor_TP");
//     CKEDITOR.config.height = 300;
// });

$("#FormId").submit( function() {
    event.preventDefault();
    var size_id = $("#size_id").val();
    var sizes = $("#sizes").val();
    var organizations = $("#organizations").val();
    // Get the CKEditor instance and its content
    // var ckeditor_CP_instance = CKEDITOR.instances.ckeditor_CP;
    // var ckeditor_CP = ckeditor_CP_instance.getData();

    if(!sizes){
        swal('', 'All fields required!', 'warning');
        return;
    }

    $.ajax({
        url: 'ajax/billsize/insertpagedata.php',
        type: 'POST',
        data: {
            'size_id':size_id,
            'sizes': sizes,
            'organizations': organizations,
            // 'ckeditor_CP': ckeditor_CP,
        },
        success: function(data) {
            console.log(data);
            if (data == 1) {
                swal('', 'Added Successfully', 'success');
                $("#FormId")[0].reset();

            } else if (data == 2) {
                swal('', 'Update Successfully', 'success');
                $("#FormId")[0].reset();

            }else {
                swal('', 'All fields required!', 'warning');
            }

        },
        error: function(err) {
            console.log(err);
        }
    });
});


$("#FormId1").submit( function() {
    event.preventDefault();
    var size_id = $("#size_id1").val();
    var sizes = $("#sizes1").val();
    var organizations = $("#organizations1").val();
    // Get the CKEditor instance and its content
    // var ckeditor_PP_instance = CKEDITOR.instances.ckeditor_PP;
    // var ckeditor_PP = ckeditor_PP_instance.getData();

    if(!sizes){
        swal('', 'All fields required!', 'warning');
        return;
    }

    $.ajax({
        url: 'ajax/billsize/insertpressdata.php',
        type: 'POST',
        data: {
            'size_id':size_id,
            'sizes': sizes,
            'organizations': organizations
            // 'ckeditor_PP': ckeditor_PP
        },
        success: function(data) {
            console.log(data);
            if (data == 1) {
                swal('', 'Added Successfully', 'success');
                $("#FormId1")[0].reset();

            } else if (data == 2) {
                swal('', 'Update Successfully', 'success');
                $("#FormId1")[0].reset();

            }else {
                swal('', 'All fields required!', 'warning');
            }

        },
        error: function(err) {
            console.log(err);
        }
    });
});


$("#FormId2").submit( function() {
event.preventDefault();
    var size_id = $("#size_id2").val();
    var sizes = $("#sizes2").val();
    var organizations = $("#organizations2").val();
    // Get the CKEditor instance and its content
    // var ckeditor_TP_instance = CKEDITOR.instances.ckeditor_TP;
    // var ckeditor_TP = ckeditor_TP_instance.getData();

    if(!sizes){
    swal('', 'All fields required!', 'warning');
    return;
    }

    $.ajax({
        url: 'ajax/billsize/inserttestdata.php',
        type: 'POST',
        data: {
            'size_id':size_id,
            'sizes': sizes,
            'organizations': organizations,
            // 'ckeditor_TP': ckeditor_TP,
        },
        success: function(data) {
            console.log(data);
            if (data == 1) {
                swal('', 'Added Successfully', 'success');
                $("#FormId2")[0].reset();

            } else if (data == 2) {
                swal('', 'Update Successfully', 'success');
                $("#FormId2")[0].reset();

            }else {
                swal('', 'All fields required!', 'warning');
            }
        },
        error: function(err) {
            console.log(err);
        }
    });
});

function getsizeid(){
    var organizations = $("#organizations").val();
    // alert(organizations);

    $.ajax({
        type: 'POST',
        url: 'ajax/billsize/getsizeidbyorg.php',
        data: {
            'organizations':organizations
        },
        success: function(data) {
            console.log(data);
            $("#size_id").val(data);
        },
        error: function(err) {
            console.log(err);
        }
    });

}


function getsizeid1(){
    var organizations = $("#organizations1").val();
    // alert(organizations);

    $.ajax({
        type: 'POST',
        url: 'ajax/billsize/getsizeidbyorg1.php',
        data: {
            'organizations':organizations
        },
        success: function(data) {
            // console.log(data);
            $("#size_id1").val(data);
        },
        error: function(err) {
            console.log(err);
        }
    });

}


function getsizeid2(){
    var organizations = $("#organizations2").val();
    // alert(organizations);

    $.ajax({
        type: 'POST',
        url: 'ajax/billsize/getsizeidbyorg2.php',
        data: {
            'organizations':organizations
        },
        success: function(data) {
            // console.log(data);
            $("#size_id2").val(data);
        },
        error: function(err) {
            console.log(err);
        }
    });

}

function setInputValues() {

var organizations = $("#organizations").val();
if(organizations == ""){
    swal('', 'please select the organization', 'warning');
    return;
}
window.open("ajax/billsize/Consoltation.php?Org="+organizations,"_blank");

}

function setInputValues1() {

var organizations1 = $("#organizations1").val();
if(organizations1 == ""){
    swal('', 'please select the organization', 'warning');
    return false;
}


if (organizations1 == '1'){
    var url = "ajax/billsize/prescrptionDrpandas.php?Org_id=" + organizations1;
    window.open(url, '_blank');
} else {
    var url = "ajax/billsize/Prescrption.php?Org_id=" + organizations1;
    window.open(url, '_blank');
}

}

function setInputValues2() {
    var organizations2 = $("#organizations2").val();
    if(organizations2 == ""){
        swal('', 'please select the organization', 'warning');
        return;
    }
    var url = "ajax/billsize/TestBill.php?Org_id=" + organizations2;
    window.open(url, '_blank');
}

// function setInputValues() {

//             var organizations = $("#organizations").val();

//             // Use AJAX to send the data to PHP
//             $.ajax({
//                 type: "POST",
//                 url: "ajax/billsize/sample1_consoltation.php",
//                 data: { organizations: organizations },
//                 success: function(response) {
//                     // Handle the response from PHP if needed
//                     console.log(response);
//                 }
//             });
// }


</script>