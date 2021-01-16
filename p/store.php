<?php
session_name('serverfund');
session_start();
require_once '../backend/inc/connect.php';
require_once '../backend/inc/config.php';
$page['title'] = "Store";

if (loggedIn) {
  require_once '../backend/user/auth/loggedIn.php';
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

$paypalURL = 'https://www.paypal.com/cgi-bin/webscr';


//CHANGE THIS EMAIL TO YOUR PAYPAL EMAIL.
$paypalID = 'YOUR_EMAIL@EMAIL.COM';

$fulldomain = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
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
      <?php if ($user['verified'] === "false") : ?>
        <div class="alert dark alert-danger" role="alert">Your account isn't verified! Without verification, some core features of ServerFund will be disabled. Please check your email, along with any spam / junk folders.</div>
      <?php endif; ?>
      <?php if (isset($message)) {
        echo $message;
      } ?>

      <section class="pricing py-5">
        <div class="row">
          <?php
          $sql             = "SELECT * FROM products WHERE cid = ? AND deleted = 'false' AND visible = 'true'";
          $stmt            = $pdo->prepare($sql);
          $stmt->execute([$_SESSION['cid']]);
          $getPanelPackages = $stmt->fetchAll(PDO::FETCH_ASSOC);
          ?>
          <?php if (count($getPanelPackages) > 0) : ?>
            <?php foreach ($getPanelPackages as $listPackages) : ?>
              <?php $taxprice = sf_salestax($listPackages['price']); ?>
              <?php $saleTotal = $listPackages['price'] - ($listPackages['price'] * ($listPackages['sale']/100)); ?>
              <div class="col-lg-4">
                <div class="card mb-5 mb-lg-0">
                  <?php if($listPackages['sale'] !== NULL): ?>
                  <div class="ribbon ribbon-badge ribbon-danger">
                    <span class="ribbon-inner">SALE</span>
                  </div>
                  <?php endif; ?>
                  <div class="card-body">
                    <h5 class="card-title text-muted text-uppercase text-center"><?php echo $listPackages['name']; ?></h5>
                    <?php if($listPackages['sale'] !== NULL): ?>
                    <h6 class="card-price text-center">$<strike><?php echo $listPackages['price']; ?></strike> $<?php echo $saleTotal; ?></h6>
                    <?php else: ?>
                    <h6 class="card-price text-center">$<?php echo $listPackages['price']; ?></h6>
                    <?php endif; ?>
                    <hr>
                    <ul class="fa-ul">
                      <?php echo htmlspecialchars_decode(stripslashes($listPackages['description'])); ?>
                    </ul>
                    <?php if (loggedIn) : ?>
                      <form target="_self" action="<?php echo $paypalURL; ?>" method="post">
                        <input type="hidden" name="business" value="<?php echo $paypalID; ?>">
                        <input type="hidden" name="cmd" value="_xclick">
                        <input type="hidden" name="item_name" value="<?php echo $listPackages['name']; ?>">
                        <input type="hidden" name="item_number" value="<?php echo $listPackages['id']; ?>">
                        <input type="hidden" name="amount" value="<?php echo $taxprice; ?>">
                        <input type="hidden" name="custom" value="<?php echo $_SESSION['user_id']; ?>,<?php echo $_SESSION['cid']; ?>,<?php echo $client_ip; ?>">
                        <input type="hidden" name="currency_code" value="USD">
                        <input type="hidden" name="no_shipping" value="1">
                        <input type='hidden' name='cancel_return' value='https://serverfund.net/p/<?php echo $_SESSION['panel_abrv']; ?>/store'>
                        <input type='hidden' name='return' value='https://serverfund.net/payment-complete'>
                        <input type='hidden' name='notify_url' value='https://serverfund.net/backend/paypal/ipn.php'>
                        <button class="btn btn-block btn-primary text-uppercase" name="submit" type="submit">Checkout</button>
                      </form>
                    <?php else : ?>
                      <div class="alert dark alert-danger" role="alert">
                        <strong>You must be <a href="../../login?rdir=<?php echo $fulldomain; ?>">signed in</a> to purchase.</strong>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else : ?>
            <div class="col-md-12">
              <div class="alert dark alert-danger" role="alert">
                <strong>No Packages Found.</strong>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </section>
    </div>
  </div>
  <!-- End Page -->

  <?php include '../backend/inc/footer.php'; ?>
  <?php include '../backend/inc/js.php'; ?>
</body>

</html>