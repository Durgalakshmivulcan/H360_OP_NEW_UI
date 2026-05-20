<?php
require_once("config/config.php");
session_start();

if (isset($_POST['log'])) {
    $email = $_POST['user_name'];
    // $contact = $_POST['user_name'];
    $password = md5($_POST['password']);
    $qry = mysqli_query($conn, "SELECT * from security WHERE email='$email' AND security_password='$password'") or die(mysqli_error($conn));
    $count = mysqli_num_rows($qry);
    if ($count == 1) {
        $result = mysqli_fetch_object($qry);
        $_SESSION['security_id'] = $result->security_id;
        $_SESSION['role_id'] = $result->role_id;
        $_SESSION['org_id'] = $result->org_id;

        if($_SESSION['role_id'] == "1" && $_SESSION['org_id']=="0"){
            $message = "Login Successfully";
            $color = "color:green";
            header("Location:registration.php");
        }elseif($_SESSION['role_id'] == "1"){
            $message = "Login Successfully";
            $color = "color:green";
            header("Location:dashboard.php");
        }else{

            if($_SESSION['org_id']){
                $message = "Login Successfully";
                $color = "color:green";
                header("Location:dashboard.php");
                }else{
                    $message = "Login Failed";
                    $color = "color:red";
                }
        }
        
    } else { 
        $message = "Login Failed";
        $color = "color:red";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Health360 Web admin</title>
    <!-- General CSS Files -->
    <link rel="stylesheet" href="assets/css/app.min.css">
    <link rel="stylesheet" href="assets/bundles/bootstrap-social/bootstrap-social.css">
    <!-- Template CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <!-- Custom style CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel='shortcut icon' type='image/x-icon' href="assets/img/health.png" />
    <!-- H360 UI · Sovereign Institutional (see /design.md) -->
    <link rel="stylesheet" href="assets/h360-ui/h360-ui.css?v=5">
</head>

<body>
    <div class="loader"></div>
    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <div class="row">
                    <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                        <div class="card card-primary">
                            <!-- <div class="card-header">
                                <h4>Login</h4>

                            </div> -->
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- error msg -->
                                </div>
                            </div>

                            <p style="<?= $color; ?>;font-size:20px;padding-left:25px;"><?= $message; ?></p>

                            <div class="card-body">
                                <div class="sidebar-brand " style="text-align:center; ">
                                    <a href="index.html"> 
                                        <img alt="image" src="assets/img/h360.png" class="header-logo w-50" /> 
                                        <!-- <h1>LOGO</h1> -->
                                        <span class="logo-name"></span>
                                    <!-- <h3>Health360</h3> -->
                                    </a>  
                                </div>

                                <form method="POST" id="login_form" action="#" class="needs-validation" novalidate="">

                                    <div class="form-group">
                                        <label for="email">Email <span id="patient" class="text-danger">*</span></label>
                                        <input id="user_name" type="user_name" class="form-control" name="user_name" placeholder="Email" value="" tabindex="1" required autofocus>
                                        <div class="invalid-feedback">
                                            Please fill in your email
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="d-block">
                                            <label for="password" class="control-label">Password <span id="patient" class="text-danger">*</span></label>
                                            <!-- <div class="float-right">
                                                <a href="auth-forgot-password.html" class="text-small">
                                                    Forgot Password?
                                                </a>
                                            </div> -->
                                        </div>
                                        <input id="password" type="password" class="form-control" name="password" placeholder="Password.." value="" tabindex="2" required>
                                        <div class="invalid-feedback">
                                            please fill in your password
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="float-right">
                                                <!-- <a href="auth-forgot-password.html" class="text-small">
                                                    Forgot Password?
                                                </a> -->
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <!-- <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">
                                            <label class="custom-control-label" for="remember-me">Remember Me</label> -->
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4" name="log" id="log" onclick="return check_form()">
                                            Login
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
        </section>
    </div>
    <!-- General JS Scripts -->
    <script src="assets/js/app.min.js"></script>
    <!-- JS Libraies -->
    <!-- Page Specific JS File -->
    <!-- Template JS File -->
    <script src="assets/js/scripts.js"></script>
    <!-- Custom JS File -->
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/main.js"></script>

    <script>
        // Guard: legacy onclick="return check_form()" on Login button.
        // Real validator is commented out below; provide a no-op so the
        // submit click does not throw "check_form is not defined".
        if (typeof window.check_form !== 'function') {
            window.check_form = function () { return true; };
        }
    </script>

    <!-- <script>
        function check_form()
        {
            var email = $("#email").val();
            var password = $("#password").val();
            if($('input[type="checkbox"]').is(":checked"))
            {
                //console.log("Checkbox is checked.");
                sessionStorage.setItem("rememberemail",email);
                sessionStorage.setItem("rememberpassword",password);
                sessionStorage.setItem("rememberchecked","true");
            }
            else if($('input[type="checkbox"]').is(":not(:checked)"))
            {
                //console.log("Checkbox is unchecked.");
                sessionStorage.setItem("rememberemail","");
                sessionStorage.setItem("rememberpassword","");
                sessionStorage.setItem("rememberchecked","false");
            }
            return true;
        }

        $(document).ready(function()
        {
            $("#email").val(sessionStorage.getItem("rememberemail"));
            $("#password").val(sessionStorage.getItem("rememberpassword"));
            //console.log("checked: "+sessionStorage.getItem("rememberchecked"));
            var check  = sessionStorage.getItem("rememberchecked");
            if(check == "true")
            {
                $('input[type="checkbox"]').prop('checked', true);
            }
            else if(check == "false")
            {
                $('input[type="checkbox"]').prop('checked', false);
            }
        });
    </script> -->
</body>

</html>