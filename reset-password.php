<?php
session_name('serverfund');
session_start();
require_once 'backend/inc/connect.php';
require_once 'backend/inc/config.php';

if (isset($_GET['r'])) {
	$reset_token = strip_tags($_GET['r']);
	//Pull information from database
	$sql  = "SELECT * FROM reset_tokens WHERE token = ?";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$reset_token]);
	$tInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $time_diff = $tInfo['made'] - $sqltimestamp;

	if($tInfo['made'] <= strtotime('-15 minutes')) {
        $_SESSION["errortype"] = "danger";
        $_SESSION["errormsg"] = "Your reset token has expired. Please generate a new one!";
        header('Location: forgot-password');
        exit();
    }
}

if (isset($_POST['pwrBtn'])) {
    $NewPass1 = strip_tags($_POST['newPass1']);
    $NewPass2 = strip_tags($_POST['newPass2']);

    if ($NewPass1 !== $NewPass2) {
        $_SESSION["errortype"] = "danger";
        $_SESSION["errormsg"] = "Your new password doesn't match your confirmation password";
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    }

    $passwordHash = password_hash($NewPass2, PASSWORD_BCRYPT, array("cost" => 12));

    $sql2 = "UPDATE `users` SET `password`=:new_pass WHERE `uid`=:uid";
    $stmt2 = $pdo->prepare($sql2);
    $stmt2->bindValue(':new_pass', $passwordHash);
    $stmt2->bindValue(':uid', $tInfo['uid']);
    $stmt2->execute();

    $_SESSION["errortype"] = "success";
    $_SESSION["errormsg"] = "Password reset, please login.";

    header('Location: login');
    exit();
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
    <title>Reset Password</title>
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
                        <h2 class="brand-text font-size-18">Reset Password</h2>
                    </div>
                    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <?php if (isset($message)) {
                            echo $message;
                        } ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-material floating" data-plugin="formMaterial">
                                    <input type="password" class="form-control" name="newPass1" placeholder="New Password..." required />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-material floating" data-plugin="formMaterial">
                                    <input type="password" class="form-control" name="newPass2" placeholder="Confirm New Password..." required />
                                </div>
                            </div>
                        </div>
                        <button type="submit" name="pwrBtn" class="btn btn-primary btn-block btn-lg mt-40">Reset Password</button>
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