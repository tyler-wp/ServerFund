<?php
require_once 'backend/inc/connect.php';
require_once 'backend/inc/config.php';

//Register
if (isset($_POST['registerBtn'])) {
    //Sanitize
    $first_name = !empty($_POST['firstname']) ? trim($_POST['firstname']) : null;
    $last_name  = !empty($_POST['lastname']) ? trim($_POST['lastname']) : null;
    $email     = !empty($_POST['email']) ? trim($_POST['email']) : null;
    $password  = !empty($_POST['password']) ? trim($_POST['password']) : null;

    $first_name = strip_tags($_POST['firstname']);
    $last_name  = strip_tags($_POST['lastname']);
    $email     = strip_tags($_POST['email']);
    $password  = strip_tags($_POST['password']);

    if (!preg_match("/^[a-zA-Z]+$/", $first_name) || !preg_match("/^[a-zA-Z]+$/", $last_name)) {
        $_SESSION["errortype"] = "danger";
        $_SESSION["errormsg"] = "Illegal Characters Used In Name Fields.";
        header('Location: register.php');
        exit();
    }

    //Check if the email already exists
    $sql       = "SELECT COUNT(email) AS num FROM users WHERE email = :email";
    $stmt      = $pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['num'] > 0) {
        $_SESSION["errortype"] = "danger";
        $_SESSION["errormsg"] = "Email Taken, Try Again.";
        header('Location: register.php');
        exit();
    }

    //Hash password
    $passwordHash = password_hash($password, PASSWORD_BCRYPT, array("cost" => 12));

    //Insert into the database
    $sql1          = "INSERT INTO users (first_name, last_name, email, password) VALUES (:first_name, :last_name, :email, :password)";
    $stmt1         = $pdo->prepare($sql1);
    $stmt1->bindValue(':first_name', $first_name);
    $stmt1->bindValue(':last_name', $last_name);
    $stmt1->bindValue(':email', $email);
    $stmt1->bindValue(':password', $passwordHash);
    $result_user = $stmt1->execute();
    if ($result_user) {
        sf_email($email, "[ServerFund] Email Verification", "Hey $first_name !\r\nWelcome to ServerFund, and thank you for joining us! You will need to verify your account to use some of our core features. To do so, please click the link below. \r\n$host_url/user/settings?a=verify");
        $_SESSION["errortype"] = "success";
        $_SESSION["errormsg"] = "Your account has been created! Please check your email (including spam folders) for your verification email.";
        header('Location: login');
        exit();
    }
}
?>
<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Donation Management System for Game Servers">
    <meta name="author" content="Tyler">
    <title>Register</title>
    <link rel="apple-touch-icon" href="assets/images/apple-touch-icon.png">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css?<?php echo $assets_ver; ?>">
    <link rel="stylesheet" href="assets/css/bootstrap-extend.min.css?<?php echo $assets_ver; ?>">
    <link rel="stylesheet" href="assets/css/site.css?<?php echo $assets_ver; ?>">
    <!-- Page -->
    <link rel="stylesheet" href="assets/examples/css/pages/login-v3.min.css?<?php echo $assets_ver; ?>">
    <!-- Fonts -->
    <link rel="stylesheet" href="assets/fonts/web-icons/web-icons.min.css?<?php echo $assets_ver; ?>">
    <link rel="stylesheet" href="assets/fonts/brand-icons/brand-icons.min.css?<?php echo $assets_ver; ?>">
    <link rel='stylesheet' href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic">
</head>

<body class="animsition site-navbar-small page-login-v3 layout-full">
    <!-- Page -->
    <div class="page vertical-align text-center" data-animsition-in="fade-in" data-animsition-out="fade-out">
        <div class="page-content vertical-align-middle animation-slide-top animation-duration-1">
            <div class="panel">
                <div class="panel-body">
                    <div class="brand">
                        <h2 class="brand-text font-size-18">ServerFund Register</h2>
                    </div>
                    <form method="post" action="register">
                        <?php if (isset($message)) {
                            echo $message;
                        } ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-material floating" data-plugin="formMaterial">
                                    <input type="text" class="form-control" name="firstname" placeholder="First Name" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-material floating" data-plugin="formMaterial">
                                    <input type="text" class="form-control" name="lastname" placeholder="Last Name" required />
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-material floating" data-plugin="formMaterial">
                            <input type="email" class="form-control" name="email" placeholder="Email" required />
                        </div>
                        <div class="form-group form-material floating" data-plugin="formMaterial">
                            <input type="password" class="form-control" name="password" placeholder="Password" required />
                        </div>
                        <div class="form-group clearfix">
                            <label>By signing up, you agree to our <br><a href="<?php echo $host_url; ?>/legal">Legal Policies</a></label>
                        </div>
                        <button type="submit" name="registerBtn" class="btn btn-primary btn-block btn-lg mt-20">Register</button>
                    </form>
                    <p>Already have an account? Login <a href="login">here</a></p>
                </div>
            </div>
            <footer class="page-copyright page-copyright-inverse">
                <p>ServerFund © 2018 - All Rights Reserved.</p>
            </footer>
        </div>
    </div>
    <!-- End Page -->
    <!-- Core  -->
    <script src="assets/vendor/jquery/jquery.min.js?<?php echo $assets_ver; ?>"></script>
    <script src="assets/vendor/popper-js/umd/popper.min.js?<?php echo $assets_ver; ?>"></script>
    <script src="assets/vendor/bootstrap/bootstrap.min.js?<?php echo $assets_ver; ?>"></script>
    <script src="assets/vendor/animsition/animsition.min.js?<?php echo $assets_ver; ?>"></script>
    <script src="assets/vendor/asscrollbar/jquery-asScrollbar.min.js?<?php echo $assets_ver; ?>"></script>
    <script src="assets/vendor/asscrollable/jquery-asScrollable.min.js?<?php echo $assets_ver; ?>"></script>
    <script src="assets/vendor/ashoverscroll/jquery-asHoverScroll.min.js?<?php echo $assets_ver; ?>"></script>
    <!-- Plugins -->
    <script src="assets/vendor/switchery/switchery.min.js?<?php echo $assets_ver; ?>"></script>
    <script src="assets/vendor/intro-js/intro.min.js?<?php echo $assets_ver; ?>"></script>
    <script src="assets/vendor/slidepanel/jquery-slidePanel.min.js?<?php echo $assets_ver; ?>"></script>
    <!-- Page -->
    <script src="assets/js/Site.min.js?<?php echo $assets_ver; ?>"></script>
    <script src="assets/js/Plugin/asscrollable.min.js?<?php echo $assets_ver; ?>"></script>
    <script src="assets/js/Plugin/slidepanel.min.js?<?php echo $assets_ver; ?>"></script>
    <script src="assets/js/Plugin/switchery.min.js?<?php echo $assets_ver; ?>"></script>
    <script src="assets/js/Plugin/material.min.js?<?php echo $assets_ver; ?>"></script>
</body>

</html>