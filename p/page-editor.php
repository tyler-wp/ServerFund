<?php
session_name('serverfund');
session_start();
require_once '../backend/inc/connect.php';
require_once '../backend/inc/config.php';
$page['title'] = "Page Editor";

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

if (isset($_POST['editHomePageBtn'])) {
  //Sanitize
  $homePage = nl2br(htmlentities($_POST['homePage'], ENT_QUOTES, 'UTF-8')); 
  //Do the update
  $sql3 = "UPDATE `communities` SET `home_page`=:home_page WHERE `cid`=:cid";
  $stmt3 = $pdo->prepare($sql3);
  $stmt3->bindValue(':home_page', $homePage);
  $stmt3->bindValue(':cid', $_SESSION['cid']);
  $result3 = $stmt3->execute();
  if ($result3) {
    $_SESSION["errortype"] = "success";
    $_SESSION["errormsg"] = "Home page edited";

    header('Location: ../../' . $_SESSION['panel_abrv']);
    exit();
  }
}
?>
<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">

<head>
  <?php include '../backend/inc/header.php'; ?>
  <link rel="stylesheet" href="<?php echo $host_url; ?>/assets/vendor/summernote/summernote-bs4.css?v4.0.2">
  <script src="<?php echo $host_url; ?>/assets/vendor/summernote/summernote-bs4.js?v4.0.2"></script>
  <script>
    $(document).ready(function() {
      $('#summernote').summernote({
        tabsize: 2,
        rows: 6,
        height: 100,
        codeviewIframeFilter: true
      });
    });
  </script>
</head>

<body class="animsition dashboard">
  <?php include '../backend/inc/nav.php'; ?>
  <!-- Page -->
  <div class="page">
    <div class="page-content container-fluid">
      <?php if (isset($message)){ echo $message;} ?>
      <div class="panel">
        <div class="panel-heading">
          <h3 class="panel-title">Edit Home Page</h3>
        </div>
        <div class="panel-body">
          <form method="POST">
            <div class="row">
              <div class="col-xl-12">
                <textarea id="summernote" name="homePage" placeholder="Home Page" required><?php echo htmlspecialchars_decode(stripslashes($panelView['home_page'])); ?></textarea>
              </div>
            </div>
            <div class="row">
              <div class="col-xl-12">
                <button type="submit" name="editHomePageBtn" class="btn btn-primary mt-10 float-right">Edit Home Page</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- End Page -->

  <?php include '../backend/inc/footer.php'; ?>
  <?php include '../backend/inc/js.php'; ?>
</body>

</html>