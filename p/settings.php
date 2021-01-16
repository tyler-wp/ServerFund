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

if (isset($_POST['updatePanelSettingsBtn'])) {
  //Sanitize
  $communityName = !empty($_POST['communityName']) ? trim($_POST['communityName']) : null;
  $communityAbbreviation  = !empty($_POST['communityAbbreviation']) ? trim($_POST['communityAbbreviation']) : null;
  $communityDiscord     = !empty($_POST['communityDiscord']) ? trim($_POST['communityDiscord']) : null;
  $communityPayPal     = !empty($_POST['communityPayPal']) ? trim($_POST['communityPayPal']) : null;
  $communityDiscordWebhook     = !empty($_POST['communityDiscordWebhook']) ? trim($_POST['communityDiscordWebhook']) : null;
  $communityDiscordWebhookStatus     = !empty($_POST['communityDiscordWebhookStatus']) ? trim($_POST['communityDiscordWebhookStatus']) : null;

  $communityName = strip_tags($_POST['communityName']);
  $communityAbbreviation  = strip_tags($_POST['communityAbbreviation']);
  $communityDiscord     = strip_tags($_POST['communityDiscord']);
  $communityPayPal     = strip_tags($_POST['communityPayPal']);
  $communityDiscordWebhook     = strip_tags($_POST['communityDiscordWebhook']);
  $communityDiscordWebhookStatus     = strip_tags($_POST['communityDiscordWebhookStatus']);
  $communityMenuColor     = strip_tags($_POST['communityMenuColor']);

  if (empty($communityDiscordWebhook)) {
    $communityDiscordWebhook = null;
  }

  if (is_premium === "false") {
    if ($communityMenuColor !== "default") {
      header('Location: ' . $_SERVER['REQUEST_URI']);
      exit();
    }
  }

  // Only run the check if it's actually changed
  if ($communityAbbreviation !== $_SESSION['panel_abrv']) {
    //Check if the community abbreviation is taken
    $sql2       = "SELECT COUNT(abbreviation) AS num FROM communities WHERE abbreviation = :abbreviation";
    $stmt2      = $pdo->prepare($sql2);
    $stmt2->bindValue(':abbreviation', $communityAbbreviation);
    $stmt2->execute();
    $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    if ($row2['num'] > 0) {
      $_SESSION["errortype"] = "danger";
      $_SESSION["errormsg"] = "That abbreviation is already taken, Please try a different one.";
    
      header('Location: ' . $_SESSION['panel_abrv']);
      exit();
    }
  }

  //Do the update
  $sql3 = "UPDATE `communities` SET `name`=:name, `abbreviation`=:abbreviation, `discord`=:discord, `paypal`=:paypal, `discord_webhook`=:webhook, `discord_webhook_status`=:whstatus, `nav_color`=:nav_color WHERE `cid`=:cid";
  $stmt3 = $pdo->prepare($sql3);
  $stmt3->bindValue(':name', $communityName);
  $stmt3->bindValue(':abbreviation', $communityAbbreviation);
  $stmt3->bindValue(':discord', $communityDiscord);
  $stmt3->bindValue(':paypal', $communityPayPal);
  $stmt3->bindValue(':webhook', $communityDiscordWebhook);
  $stmt3->bindValue(':whstatus', $communityDiscordWebhookStatus);
  $stmt3->bindValue(':nav_color', $communityMenuColor);
  $stmt3->bindValue(':cid', $_SESSION['cid']);
  $result3 = $stmt3->execute();
  if ($result3) {
    $_SESSION["errortype"] = "success";
    $_SESSION["errormsg"] = "Store settings updated";

    header('Location: ../../' . $_SESSION['panel_abrv']);
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
      <?php if (isset($message)){ echo $message;} ?>
      <div class="panel">
        <div class="panel-heading">
          <h3 class="panel-title"><?php echo $_SESSION['panel_abrv']; ?> Settings</h3>
        </div>
        <div class="panel-body">
          <form method="POST" action="settings" autocomplete="off">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group form-material" data-plugin="formMaterial">
                  <label class="form-control-label" for="communityName">Community Name</label>
                  <input type="text" class="form-control" id="communityName" name="communityName" placeholder="My Community RP" value="<?php echo $_SESSION['panel_name']; ?>" required />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group form-material" data-plugin="formMaterial">
                  <label class="form-control-label" for="communityAbbreviation">Community Abbreviation</label>
                  <input type="text" class="form-control" id="communityAbbreviation" name="communityAbbreviation" value="<?php echo $_SESSION['panel_abrv']; ?>" placeholder="MCRP" required />
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group form-material" data-plugin="formMaterial">
                  <label class="form-control-label" for="communityDiscord">Discord Link</label>
                  <input type="text" class="form-control" id="communityDiscord" name="communityDiscord" value="<?php echo $_SESSION['panel_discord']; ?>" placeholder="https://discord.gg/xxxxxx" required />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group form-material" data-plugin="formMaterial">
                  <label class="form-control-label" for="communityPayPal">PayPal Email</label>
                  <input type="text" class="form-control" id="communityPayPal" name="communityPayPal" value="<?php echo $_SESSION['panel_paypal']; ?>" placeholder="PayPal Email" required />
                </div>
              </div>
            </div>
            <?php if(is_premium): ?>
            <div class="row">
              <div class="col-md-4">
                <div class="form-group form-material" data-plugin="formMaterial">
                  <label class="form-control-label" for="communityMenuColor">Menu Color</label>
                  <select class="form-control" id="communityMenuColor" name="communityMenuColor" required>
                    <option value="<?php echo $_SESSION['nav_color']; ?>" selected><?php echo $_SESSION['nav_color']; ?></option>
                    <option value="default">default</option>
                    <option value="green">green</option>
                    <option value="blue">blue</option>
                    <option value="purple">purple</option>
                    <option value="pink">pink</option>
                    <option value="red">red</option>
                    <option value="orange">orange</option>
                    <option value="yellow">yellow</option>
                    <option value="teal">teal</option>
                    <option value="cyan">cyan</option>
                    <option value="gray">gray</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group form-material" data-plugin="formMaterial">
                  <label class="form-control-label" for="communityDiscordWebhook">Discord Webhook Link</label>
                  <input type="text" class="form-control" id="communityDiscordWebhook" name="communityDiscordWebhook" value="<?php echo $_SESSION['discord_webhook']; ?>" placeholder="Discord Webhook..." />
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group form-material" data-plugin="formMaterial">
                  <label class="form-control-label" for="communityDiscordWebhookStatus">Discord Webhook Status</label>
                  <select class="form-control" id="communityDiscordWebhookStatus" name="communityDiscordWebhookStatus" required>
                      <?php if($_SESSION['discord_webhook_status'] === "disabled"): ?>
                      <option selected value="disabled">disabled</option>
                      <option value="enabled">enabled</option>
                      <?php else: ?>
                      <option selected value="enabled">enabled</option>
                      <option value="disabled">disabled</option>
                      <?php endif; ?>
                    </select>
                </div>
              </div>
            </div>
            <?php endif; ?>
            <button type="submit" name="updatePanelSettingsBtn" class="btn btn-success btn-sm float-right">Update Settings</button>
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