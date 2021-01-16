<?php
session_name('serverfund');
session_start();
require_once 'backend/inc/connect.php';
require_once 'backend/inc/config.php';
$page['title'] = "Home";

if (loggedIn) {
	require_once 'backend/user/auth/loggedIn.php';
} else {
  header('Location: login');
	exit();
}

$_SESSION['panel'] = "N/A";
?>
<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">

<head>
  <?php include 'backend/inc/header.php'; ?>
</head>

<body class="animsition dashboard">
  <?php include 'backend/inc/nav.php'; ?>
  <!-- Page -->
  <div class="page">
    <div class="page-content container-fluid">
      <?php if($user['verified'] === "false") : ?>
				<div class="alert dark alert-danger" role="alert">Your account isn't verified! Without verification, some core features of ServerFund will be disabled. Please check your email, along with any spam / junk folders.</div>
			<?php endif; ?>
			<?php if (isset($message)) {
				echo $message;
			} ?>
    </div>
  </div>
  <!-- End Page -->

  <?php include 'backend/inc/footer.php'; ?>
  <?php include 'backend/inc/js.php'; ?>
</body>

</html>