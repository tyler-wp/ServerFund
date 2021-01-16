<?php
session_name('serverfund');
session_start();
require_once 'backend/inc/connect.php';
require_once 'backend/inc/config.php';

//Register
if (isset($_POST['loginBtn'])) {
    //Sanitize
    $email     = !empty($_POST['email']) ? trim($_POST['email']) : null;
    $password  = !empty($_POST['password']) ? trim($_POST['password']) : null;

    $email     = strip_tags($_POST['email']);
    $password  = strip_tags($_POST['password']);

    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user === false) {
        $_SESSION["errortype"] = "danger";
        $_SESSION["errormsg"] = "User not found";
        header('Location: login');
        exit();
    } else {
        $validPassword = password_verify($password, $user['password']);
        if ($validPassword) {

            sf_email($user['email'], "[ServerFund] Login Notification", "This email is to alert you that your ServerFund account has been logged into. If this was you, please ignore this message. If this was not you, please change your account password and contact Staff to help secure your account. \r\nIP: $client_ip");

            $_SESSION['user_id'] = $user['uid'];
            $_SESSION['logged_in'] = time();

            sf_log('Login', $user['uid'], $client_ip);

            if ($user['verified'] === "false") {
                sf_email($email, "[ServerFund] Email Verification", "Hey $first_name !\r\nWelcome to ServerFund, and thank you for joining us! You will need to verify your account to use some of our core features. To do so, please click the link below. \r\n$host_url/user/settings?a=verify");
            }

            if (isset($_GET['rdir'])) {
                $rdir = $_GET['rdir'];
                header('Location: index?rdir=' . $rdir);
                exit();
            } else {
                header('Location: index');
                exit();
            }
        } else {
            sf_email($user['email'], "[ServerFund] Failed Login Attempt", "Our system has detected a failed login attempt for your ServerFund account from \r\nIP: $ip");
            $_SESSION["errortype"] = "danger";
            $_SESSION["errormsg"] = "Invalid Password";
            header('Location: login');
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
    <meta name="description" content="Donation Management System for Game Servers">
    <meta name="author" content="Tyler">
    <title>Login</title>
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
                        <h2 class="brand-text font-size-18">ServerFund Login</h2>
                    </div>
                    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                        <?php if (isset($message)) {
                            echo $message;
                        } ?>
                        <div class="form-group form-material floating" data-plugin="formMaterial">
                            <input type="email" class="form-control" name="email" placeholder="Email" required />
                        </div>
                        <div class="form-group form-material floating" data-plugin="formMaterial">
                            <input type="password" class="form-control" name="password" placeholder="Password" required />
                        </div>
                        <div class="form-group clearfix">
                            <div class="checkbox-custom checkbox-inline checkbox-primary checkbox-lg float-left">
                                <input type="checkbox" id="inputCheckbox" name="remember">
                                <label for="inputCheckbox">Remember me</label>
                            </div>
                            <a class="float-right" href="forgot-password">Forgot password?</a>
                        </div>
                        <button type="submit" name="loginBtn" class="btn btn-primary btn-block btn-lg mt-40">Login</button>
                    </form>
                    <p>No account? Sign up <a href="register">here</a></p>
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