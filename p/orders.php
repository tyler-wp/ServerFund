<?php
session_name('serverfund');
session_start();
require_once '../backend/inc/connect.php';
require_once '../backend/inc/config.php';
$page['title'] = "Store Settings";

if (loggedIn) {
  require_once '../backend/user/auth/loggedIn.php';
} else {
  header('Location: ../login');
  exit();
}

if (isset($_GET['panel'])) {
  $panel = strip_tags($_GET['panel']);
  //Pull information from database
  $sql_pInfo  = "SELECT * FROM communities WHERE abbreviation = :abbreviation";
  $stmt_pInfo = $pdo->prepare($sql_pInfo);
  $stmt_pInfo->bindValue(':abbreviation', $panel);
  $stmt_pInfo->execute();
  $pInfo = $stmt_pInfo->fetch(PDO::FETCH_ASSOC);

  //Check if the panel actually exists or not
  if ($pInfo === false) {
    header('Location: ../404');
    exit();
  } else {
    $panelView['id']             = $pInfo['cid'];
    $_SESSION['cid'] = $panelView['id'];

    $panelView['name']             = $pInfo['name'];
    $_SESSION['panel_name'] = $panelView['name'];

    $panelView['abbreviation']             = $pInfo['abbreviation'];
    $_SESSION['panel_abrv'] = $panelView['abbreviation'];

    $panelView['discord']             = $pInfo['discord'];
    $_SESSION['panel_discord'] = $panelView['discord'];

    $panelView['created']             = $pInfo['created'];
    $_SESSION['panel_created'] = $panelView['created'];

    $panelView['owner']             = $pInfo['owner'];
    $_SESSION['panel_owner'] = $panelView['owner'];

    $panelView['paypal']             = $pInfo['paypal'];
    $_SESSION['panel_paypal'] = $panelView['paypal'];

    $panelView['status']             = $pInfo['status'];
    $_SESSION['panel_status'] = $panelView['status'];

    $panelView['discord_webhook']             = $pInfo['discord_webhook'];
    $_SESSION['discord_webhook'] = $panelView['discord_webhook'];

    $panelView['discord_webhook_status']             = $pInfo['discord_webhook_status'];
    $_SESSION['discord_webhook_status'] = $panelView['discord_webhook_status'];

    $panelView['nav_color']             = $pInfo['nav_color'];
    $_SESSION['nav_color'] = $panelView['nav_color'];

    $panelView['home_page']             = $pInfo['home_page'];
    $_SESSION['home_page'] = $panelView['home_page'];

    $_SESSION['panel'] = $panel;

    if ($panelView['status'] === "suspended") {
      die('Sorry, this panel has been suspended by ServerFund Staff. If you are a user, please contact the Community Owner. If you are the Community Owner, contact ServerFund Staff via a Support Ticket for more information on why this suspension occured. Please note that in most cases, Panel Suspensions occur from a Terms of Service violation. If this panel was suspended because of a Terms of Service violation, the suspension can not be undone.');
    }
  }
}

if ($_SESSION['panel_owner'] !== $_SESSION['user_id']) {
  header('Location: ../login');
  exit();
}

if (isset($_GET['mav'])) {

  if ($_SESSION['panel_owner'] !== $_SESSION['user_id']) {
    header('Location: ../login');
    exit();
  }

  $id = strip_tags($_GET['mav']);

  $sql = "SELECT * FROM payments WHERE payment_id = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':id', $id);
  $stmt->execute();
  $mavPro = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($mavPro['cid'] !== $_SESSION['cid']) {
    die('Fatal System Error (PkgVal)');
  } else {
    //Set it as delete
    $sql2 = "UPDATE payments SET validation=? WHERE payment_id=?";
    $pdo->prepare($sql2)->execute(['Validated', $id]);

    //Redirect when done
    $_SESSION["errortype"] = "success";
    $_SESSION["errormsg"] = "Order Validated.";

    header('Location: orders');
    exit();
  }
}
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
      <?php if (isset($message)) {
        echo $message;
      }?>
      <?php if(is_premium === "false"): ?>
      <div class="alert dark alert-warning" role="alert">Warning: You are not a premium member, so prices listed below may be inaccurate due to fees.</div>
      <?php endif;?> 
      <div class="row">
        <div class="col-md-3">
          <?php 
          $sql= "SELECT SUM(payment_gross) FROM payments WHERE cid = ?"; 
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array($_SESSION['cid']));
          $totalEarned = (int) $stmt->fetchColumn();
          ?>
          <div class="card card-block p-25 bg-green-300">
            <div class="counter counter-lg counter-inverse">
              <div class="counter-label text-uppercase">Total Earned</div>
              <span class="counter-number">$<?php echo $totalEarned; ?></span>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <?php 
          $sql= "SELECT SUM(payment_gross) FROM payments WHERE cid = ? AND `timestamp` + INTERVAL 30 DAY > NOW()          "; 
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array($_SESSION['cid']));
          $totalEarned30Day = (int) $stmt->fetchColumn();
          ?>
          <div class="card card-block p-25 bg-green-400">
            <div class="counter counter-lg counter-inverse">
              <div class="counter-label text-uppercase">Last 30 Days</div>
              <span class="counter-number">$<?php echo $totalEarned30Day; ?></span>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <?php 
          $sql= "SELECT SUM(payment_gross) FROM payments WHERE cid = ? AND `timestamp` + INTERVAL 60 DAY > NOW()          "; 
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array($_SESSION['cid']));
          $totalEarned60Day = (int) $stmt->fetchColumn();
          ?>
          <div class="card card-block p-25 bg-green-800">
            <div class="counter counter-lg counter-inverse">
              <div class="counter-label text-uppercase">Last 60 Days</div>
              <span class="counter-number">$<?php echo $totalEarned60Day; ?></span>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <?php 
          $sql= "SELECT SUM(payment_gross) FROM payments WHERE cid = ? AND `timestamp` + INTERVAL 90 DAY > NOW()          "; 
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array($_SESSION['cid']));
          $totalEarned90Day = (int) $stmt->fetchColumn();
          ?>
          <div class="card card-block p-25 bg-green-900">
            <div class="counter counter-lg counter-inverse">
              <div class="counter-label text-uppercase">Last 90 Days</div>
              <span class="counter-number">$<?php echo $totalEarned90Day; ?></span>
            </div>
          </div>
        </div>
      </div>
      <div class="panel">
        <div class="panel-heading">
          <h3 class="panel-title">Orders</h3>
        </div>
        <div class="panel-body">
          <table class="table">
            <thead>
              <tr>
                <th>User ID</th>
                <th>Package ID</th>
                <th>Package Name</th>
                <th>Payment Status</th>
                <th>Price</th>
                <th>Purchased</th>
                <th>Package Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM payments WHERE cid = ?";
              $stmt = $pdo->prepare($sql);
              $stmt->execute([$_SESSION['cid']]);
              $getStoreOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
              foreach ($getStoreOrders as $listOrders) : ?>
                <?php
                //Get package info
                $sql2 = "SELECT * FROM products WHERE id = :id";
                $stmt2 = $pdo->prepare($sql2);
                $stmt2->bindValue(':id', $listOrders['package_id']);
                $stmt2->execute();
                $product = $stmt2->fetch(PDO::FETCH_ASSOC);
                ?>
                <tr>
                  <td><?php echo $listOrders['buyer_uid']; ?></td>
                  <td><?php echo $listOrders['package_id']; ?></td>
                  <td><?php echo $product['name']; ?></td>
                  <td><?php echo $listOrders['payment_status']; ?></td>
                  <td>$<?php echo $listOrders['payment_gross']; ?></td>
                  <td><?php echo $listOrders['timestamp']; ?></td>
                  <td><?php echo $listOrders['validation']; ?></td>
                  <td><?php if ($listOrders['validation'] === "Pending Validation") : ?>
                    <a class="btn btn-success btn-xs" onclick="return confirm('Are you sure you want to mark this package as confirmed? Please only do this after you have given any perks or features that come with the donation.')" href="orders?mav=<?php echo $listOrders['payment_id']; ?>"><i class="icon wb-check mr-5" aria-hidden="true"></i></a>
                  <?php else : ?>
                    <a class="btn btn-success btn-xs" disabled><i class="icon wb-check mr-5" aria-hidden="true"></i></a>
                  <?php endif; ?></td>
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