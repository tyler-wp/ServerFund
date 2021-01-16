<?php
session_name('serverfund');
session_start();
require_once '../backend/inc/connect.php';
require_once '../backend/inc/config.php';
$page['title'] = "Billing";

if (loggedIn) {
	require_once '../backend/user/auth/loggedIn.php';
} else {
  header('Location: ../login');
	exit();
}

$_SESSION['panel'] = "N/A";
?>
<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">

<head>
  <?php include '../backend/inc/header.php'; ?>
</head>

<body class="animsition dashboard">
  <?php include '../backend/inc/nav.php'; ?>
  <!-- Page -->
  <div class="page">
    <div class="page-content container-fluid">
      <?php if($user['verified'] === "false") : ?>
				<div class="alert dark alert-danger" role="alert">Your account isn't verified! Without verification, some core features of ServerFund will be disabled. Please check your email, along with any spam / junk folders.</div>
			<?php endif; ?>
			<?php if (isset($message)) {
				echo $message;
      } ?>
      <div class="alert dark alert-info" role="alert">These are all packages you've purchased in the duration of your account.</div>
      <div class="panel">
        <div class="panel-heading">
          <h3 class="panel-title">User Billing</h3>
        </div>
        <div class="panel-body">
          <table class="table">
            <thead>
              <tr>
                <th>Package ID</th>
                <th>Package Name</th>
                <th>Payment Status</th>
                <th>Package Status</th>
                <th>Price</th>
                <th>Purchased</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM payments WHERE buyer_uid = ?";
              $stmt = $pdo->prepare($sql);
              $stmt->execute([$_SESSION['user_id']]);
              $getUserBilling = $stmt->fetchAll(PDO::FETCH_ASSOC);
              foreach ($getUserBilling as $listBilling) : ?>
              <?php 
              //Get package info
              $sql2 = "SELECT * FROM products WHERE id = :id";
              $stmt2 = $pdo->prepare($sql2);
              $stmt2->bindValue(':id', $listBilling['package_id']);
              $stmt2->execute();
              $product = $stmt2->fetch(PDO::FETCH_ASSOC);
              ?>
                <tr>
                  <td><?php echo $listBilling['package_id']; ?></td>
                  <td><?php echo $product['name']; ?></td>
                  <td><?php echo $listBilling['payment_status']; ?></td>
                  <td><?php echo $listBilling['validation']; ?></td>
                  <td>$<?php echo $listBilling['payment_gross']; ?></td>
                  <td><?php echo $listBilling['timestamp']; ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    
    </div>
  </div>
  <!-- End Page -->

  <?php include '../backend/inc/footer.php'; ?>
  <?php include '../backend/inc/js.php'; ?>
</body>

</html>