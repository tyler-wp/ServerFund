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
$paypalID = 'billing@serverfund.net';

$fulldomain = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
	<?php include '../backend/inc/header.php'; ?>
</head>

<body class="animsition dashboard">
	<?php include '../backend/inc/nav.php'; ?>
	<!-- Page -->
	<div class="page">
		<div class="page-header h-200 mb-30">
			<div class="text-center blue-white-800 m-0">
				<span class="avatar avatar-online">
					<img src="https://via.placeholder.com/100" alt="...">
				</span>
				<div class="font-size-30 mb-10 blue-white-800"><?php echo $_SESSION['panel_name']; ?></div>
			</div>
		</div>
		<div class="page-content container-fluid">
			<?php if ($user['verified'] === "false") : ?>
				<div class="alert dark alert-danger" role="alert">Your account isn't verified! Without verification, some core features of ServerFund will be disabled. Please check your email, along with any spam / junk folders.</div>
			<?php endif; ?>
			<?php if (isset($message)) {
				echo $message;
			} ?>
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
				<div class="col-md-3">
					<div class="sfproduct">
						<img class="sfproduct-img-top" src="http://hdimages.org/wp-content/uploads/2017/03/placeholder-image5.jpg" height="120" alt="Shop Item">
						<div class="sfproduct-block text-center mb-0">
							<h4 class="sfproduct-text"><?php echo $listPackages['name']; ?></h4>						
						</div>
						<div class="sfproduct-footer sfproduct-footer-transparent">
							<div class="float-left">
								<?php if($listPackages['sale'] !== NULL): ?>
								<h5 class="sfproduct-text">$<strike><?php echo $listPackages['price']; ?></strike> $<?php echo $saleTotal; ?></h5>
								<?php else: ?>
								<h5 class="sfproduct-text">$<?php echo $listPackages['price']; ?></h5>
								<?php endif; ?>	
							</div>
							<div class="float-right">
								<h5 class="sfproduct-text">∞</h5>
							</div>
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
		</div>
	</div>
	<!-- End Page -->

	<?php include '../backend/inc/footer.php'; ?>
	<?php include '../backend/inc/js.php'; ?>
	<script src="<?php echo $host_url; ?>/assets/examples/js/dashboard/v2.min.js?<?php echo $assets_ver; ?>"></script>
</body>

</html>