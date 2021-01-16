<?php
session_name('serverfund');
session_start();
require_once '../backend/inc/connect.php';
require_once '../backend/inc/config.php';
$page['title'] = "Membership";

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
  <script>
  function membershipBuy() {
    toastr.warning('If you would like to buy premium, please make a ticket on the website with the subject "Membership Upgrade".')
  }
  </script>
</head>

<body class="animsition dashboard">
  <?php include '../backend/inc/nav.php'; ?>
  <!-- Page -->
  <div class="page">
    <div class="page-content container-fluid">
      <?php if ($user['verified'] === "false") : ?>
        <div class="alert dark alert-danger" role="alert">Your account isn't verified! Without verification, some core features of ServerFund will be disabled. Please check your email, along with any spam / junk folders.</div>
      <?php endif; ?>
      <?php if (isset($message)) {
        echo $message;
      } ?>

      <section class="pricing py-5">
        <div class="row">
          <div class="col-lg-6">
            <div class="card mb-5 mb-lg-0">
              <div class="card-body">
                <h5 class="card-title text-muted text-uppercase text-center">Premium</h5>
                <h6 class="card-price text-center">$4.99 / Monthly</h6>
                <hr>
                <ul class="fa-ul">
                  Premium is a great choice for those who want more control over their stores! Premium allows Store Owners to edit some theme settings, along with our fraud and chargeback prevention system! Premium users also get new features before they're released to the general public along with priority support! All profit made from memberships goes directly back into the development of ServerFund. Premium users may also request a payout before the 3rd of each month!<br>
                  ✔️ Monthly Transaction Limit ($1500)<br>
                  ✔️ Priority Support<br>
                  ✔️ Theme Options<br>
                  ✔️ Unlimited Panels<br>
                  ✔️ Sale System<br>
                  ✔️ No Service Fee<br>
                  ✔️ Advanced Fraud Checks<br>
                  ✔️ Chargeback Protection<br>
                  ✔️ Request Payout Before 3rd
                </ul>
                <button class="btn btn-block btn-primary text-uppercase" onclick="membershipBuy()">Get Premium!</button>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="card mb-5 mb-lg-0">
              <div class="card-body">
                <h5 class="card-title text-muted text-uppercase text-center">Premium</h5>
                <h6 class="card-price text-center">$12.99 / Tri-Monthly</h6>
                <hr>
                <ul class="fa-ul">
                  Premium is a great choice for those who want more control over their stores! Premium allows Store Owners to edit some theme settings, along with our fraud and chargeback prevention system! Premium users also get new features before they're released to the general public along with priority support! All profit made from memberships goes directly back into the development of ServerFund. Premium users may also request a payout before the 3rd of each month!<br>
                  ✔️ Monthly Transaction Limit ($1500)<br>
                  ✔️ Priority Support<br>
                  ✔️ Theme Options<br>
                  ✔️ Unlimited Panels<br>
                  ✔️ Sale System<br>
                  ✔️ No Service Fee<br>
                  ✔️ Advanced Fraud Checks<br>
                  ✔️ Chargeback Protection<br>
                  ✔️ Request Payout Before 3rd
                </ul>
                <button class="btn btn-block btn-primary text-uppercase" onclick="membershipBuy()">Get Premium!</button>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
  <!-- End Page -->

  <?php include '../backend/inc/footer.php'; ?>
  <?php include '../backend/inc/js.php'; ?>
</body>

</html>