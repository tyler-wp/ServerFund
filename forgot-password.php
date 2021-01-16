<?php
session_name('serverfund');
session_start();
require_once 'backend/inc/connect.php';
require_once 'backend/inc/config.php';

//Register
if (isset($_POST['pwrBtn'])) {
    //Sanitize
    $email     = !empty($_POST['email']) ? trim($_POST['email']) : null;
    $email     = strip_tags($_POST['email']);

    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user === false) {
        header('Location: forgot-password?cdm=3');
        exit();
    } else {
        $reset_token = random_str(12);
        //Insert into the database
        $sql1          = "INSERT INTO reset_tokens (token, uid, made) VALUES (:token, :uid, :made)";
        $stmt1         = $pdo->prepare($sql1);
        $stmt1->bindValue(':token', $reset_token);
        $stmt1->bindValue(':uid', $user['uid']);
        $stmt1->bindValue(':made', time());
        $result = $stmt1->execute();
        if ($result) {
            sf_email($email, "[ServerFund] Password Reset", "This email was sent to you because a password request was sent to an account with this email. If you did not request this, you can ignore this email. If you did request this, please cick the link below. The link will expire in 15 minutes.\r\n$host_url/reset-password?r=$reset_token");
            header('Location: forgot-password?cdm=29');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="bootstrap admin template">
    <meta name="author" content="">
    <title>Forgot Password</title>
    <link rel="apple-touch-icon" href="assets/images/apple-touch-icon.png">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css?v4.0.2">
    <link rel="stylesheet" href="assets/css/bootstrap-extend.min.css?v4.0.2">
    <link rel="stylesheet" href="assets/css/site.min.css?v4.0.2">
    <!-- Page -->
    <link rel="stylesheet" href="assets/examples/css/pages/login-v3.min.css?v4.0.2">
    <!-- Fonts -->
    <link rel="stylesheet" href="assets/fonts/web-icons/web-icons.min.css?v4.0.2">
    <link rel="stylesheet" href="assets/fonts/brand-icons/brand-icons.min.css?v4.0.2">
    <link rel='stylesheet' href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic">
</head>

<body class="animsition site-navbar-small page-login-v3 layout-full">
    <!-- Page -->
    <div class="page vertical-align text-center" data-animsition-in="fade-in" data-animsition-out="fade-out">
        >
        <div class="page-content vertical-align-middle animation-slide-top animation-duration-1">
            <div class="panel">
                <div class="panel-body">
                    <div class="brand">
                        <h2 class="brand-text font-size-18">Forgot Your Password?</h2>
                    </div>
                    <form method="post" action="forgot-password">
                        <?php if (isset($message)) {
                            echo $message;
                        }?>
                        <div class="form-group form-material floating" data-plugin="formMaterial">
                            <input type="email" class="form-control" name="email" placeholder="Email" required />
                        </div>
                        <button type="submit" name="pwrBtn" class="btn btn-primary btn-block btn-lg mt-40">Continue...</button>
                    </form>
                </div>
            </div>
            <footer class="page-copyright page-copyright-inverse">
                <p>ServerFund © 2018 - All Rights Reserved.</p>
            </footer>
        </div>
    </div>
    <!-- End Page -->
    <!-- Core  -->
    <script src="assets/vendor/babel-external-helpers/babel-external-helpers599c.min.js?v4.0.2"></script>
    <script src="assets/vendor/jquery/jquery.min.js?v4.0.2"></script>
    <script src="assets/vendor/popper-js/umd/popper.min.js?v4.0.2"></script>
    <script src="assets/vendor/bootstrap/bootstrap.min.js?v4.0.2"></script>
    <script src="assets/vendor/animsition/animsition.min.js?v4.0.2"></script>
    <script src="assets/vendor/mousewheel/jquery.mousewheel599c.min.js?v4.0.2"></script>
    <script src="assets/vendor/asscrollbar/jquery-asScrollbar.min.js?v4.0.2"></script>
    <script src="assets/vendor/asscrollable/jquery-asScrollable.min.js?v4.0.2"></script>
    <script src="assets/vendor/ashoverscroll/jquery-asHoverScroll.min.js?v4.0.2"></script>
    <!-- Plugins -->
    <script src="assets/vendor/switchery/switchery.min.js?v4.0.2"></script>
    <script src="assets/vendor/intro-js/intro.min.js?v4.0.2"></script>
    <script src="assets/vendor/screenfull/screenfull599c.min.js?v4.0.2"></script>
    <script src="assets/vendor/slidepanel/jquery-slidePanel.min.js?v4.0.2"></script>
    <!-- Plugins For This Page -->
    <script src="assets/vendor/jquery-placeholder/jquery.placeholder599c.min.js?v4.0.2"></script>
    <!-- Page -->
    <script src="assets/js/Site.min.js?v4.0.2"></script>
    <script src="assets/js/Plugin/asscrollable.min.js?v4.0.2"></script>
    <script src="assets/js/Plugin/slidepanel.min.js?v4.0.2"></script>
    <script src="assets/js/Plugin/switchery.min.js?v4.0.2"></script>
    <script src="assets/js/Plugin/jquery-placeholder.min.js?v4.0.2"></script>
    <script src="assets/js/Plugin/material.min.js?v4.0.2"></script>
</body>

</html>