<?php
require_once("ajax/header.php");
// FIX_B_1850: same posture as profile.php — every authenticated role should
// be able to change their own password (default seed maps it). Belt-and-
// suspenders gate so a role stripped of 'view' is bounced. SA bypass intact.
requireCan('view', 'change_passowrd.php');
?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <ul class="breadcrumb breadcrumb-style ">
            <li class="breadcrumb-item">
                <h4 class="page-title m-b-0">Menus</h4>
            </li>
            <li class="breadcrumb-item active">Change Password</li>
        </ul>
        
        <ul class="breadcrumb breadcrumb-style" >
            <li class="breadcrumb-item" style="z-index: 1; position: absolute; left: 91%; top: 0;">
                <div class="form-group">
                    
                </div>
            </li>
        </ul>

        <div class="col-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>Change Password</h4>
                </div>
                
                <form method="POST" id="FormId" action="" enctype="multipart/form-data" class="needs-validation" novalidate="">
                <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 col-sm-12">
                                
                                <div class="form-group">
                                    <label for="old_password"> Old Password <span class="text-danger">*</span> </label>
                                    <input type="password" class="form-control" name="old_password" id="old_password" value="" required>
                                </div>

                                <div class="form-group">
                                    <label for="new_password"> New Password <span class="text-danger">*</span> </label>
                                    <input type="password" class="form-control" name="new_password" id="new_password" value="" required>
                                </div>

                                <div class="form-group">
                                    <label for="confirm_password"> Confirm Password <span class="text-danger">*</span> </label>
                                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" value="" required>
                                </div>

                                <div class="card-footer text-center">
                                    <button class="btn btn-primary" name="saveData" id="saveData" value="">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
    </section>

</div>

<?php require_once("ajax/footer.php") ?>

<script>
    $("document").ready(function(){

    });

    $("#FormId").submit(function() {
        var old_password = $("#old_password").val();
        var new_password = $("#new_password").val();
        var confirm_password = $("#confirm_password").val();

        if(old_password != "" && new_password != "" && confirm_password != "") {
            event.preventDefault();
            $.ajax({
                url: 'ajax/ChangePassword/ChangePassword.php',
                type: 'POST',
                data: {
                    'old_password': old_password,
                    'new_password': new_password,
                    'confirm_password': confirm_password
                },
                success: function(data) {
                    if(data == 1) {
                        swal('', 'Password changed Successfully', 'success').then((result) => {
                            window.location = "dashboard.php";
                        });
                    } else if(data == 2) {
                        swal('Incorrect Password', 'Please enter correct password to change the existing password.', 'error');
                    } else {
                        swal('','Error occured. Please try again', 'error')
                    }
                },
                error: function(err)  {
                    console.log(err);
                }
            });
        }

    });

</script>



