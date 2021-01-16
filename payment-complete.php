<?php
session_name('serverfund');
session_start();
require_once 'backend/inc/connect.php';
require_once 'backend/inc/config.php';
$page['title'] = "Payment Complete";

if (loggedIn) {
	require_once 'backend/user/auth/loggedIn.php';
} else {
  header('Location: login');
	exit();
}

$item_number = $_GET['item_number'];
$item_name = $_GET['item_name'];
$txn_id = $_GET['tx'];
$payment_gross = $_GET['amt'];
$currency_code = $_GET['cc'];
$payment_status = $_GET['st'];

//Get packages
$sql = "SELECT * FROM products WHERE id = :id AND cid = :cid";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $item_number);
$stmt->bindValue(':cid', $_SESSION['cid']);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);


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
      <div class="alert dark alert-success" role="alert">
        <strong>Your payment has been processed! Below you can find information on your order. <br><i>Please note that all orders must be manually validated by the community owner that you purchased from. If your package came with perks and they are not recieved within 48 hours, please make a support ticket.</i></strong>
      </div>
      <h4>Package: <?php echo $item_name; ?> (<?php echo $item_number; ?>)</h4>
      <h4>Price: $<?php echo $payment_gross; ?></h4>
      <h4>Payment Status: <?php echo $payment_status; ?></h4>
    </div>
  </div>
  <!-- End Page -->

  <?php include 'backend/inc/footer.php'; ?>
  <?php include 'backend/inc/js.php'; ?>
</body>

</html>