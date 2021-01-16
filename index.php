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

if (isset($_GET['rdir'])) {
  $rdir = $_GET['rdir'];
  header('Location: '.$rdir);
  exit();
}

$_SESSION['panel'] = "N/A";

if (isset($_POST['createPanelBtn'])) {
  //Sanitize
  $communityName = !empty($_POST['communityName']) ? trim($_POST['communityName']) : null;
  $communityAbbreviation  = !empty($_POST['communityAbbreviation']) ? trim($_POST['communityAbbreviation']) : null;
  $communityDiscord     = !empty($_POST['communityDiscord']) ? trim($_POST['communityDiscord']) : null;

  $communityName = strip_tags($_POST['communityName']);
  $communityAbbreviation  = strip_tags($_POST['communityAbbreviation']);
  $communityDiscord     = strip_tags($_POST['communityDiscord']);

  if ($user['verified'] === "false") {
    $_SESSION["errortype"] = "warning";
    $_SESSION["errormsg"] = "Your account must be verified to use this feature.";
    header('Location: index');
    exit();
  }

  if (filter_var($communityDiscord, FILTER_VALIDATE_URL) === FALSE) {
    $_SESSION["errortype"] = "danger";
    $_SESSION["errormsg"] = "You entered an invalid URL";
    header('Location: index');
    exit();
  }

  if (preg_match('/([%\$#\*-=_+!^&., ]+)/', $communityAbbreviation)) {
    $_SESSION["errortype"] = "danger";
    $_SESSION["errormsg"] = "Illegal characters used in abbreviation field.";
    header('Location: index');
    exit();
  }

  //Check if the community abbreviation is taken
  $sql       = "SELECT COUNT(abbreviation) AS num FROM communities WHERE abbreviation = :abbreviation";
  $stmt      = $pdo->prepare($sql);
  $stmt->bindValue(':abbreviation', $communityAbbreviation);
  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row['num'] > 0) {
    $_SESSION["errortype"] = "danger";
    $_SESSION["errormsg"] = "That abbreviation is already taken, Please try a different one.";
    header('Location: index');
    exit();
  }

  //Check if user already has a panel.
  $sql2       = "SELECT COUNT(owner) AS num FROM communities WHERE owner = :owner";
  $stmt2      = $pdo->prepare($sql2);
  $stmt2->bindValue(':owner', $_SESSION['user_id']);
  $stmt2->execute();
  $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
  if ($row2['num'] > 0) {
    if (is_premium === 'false') {
      $_SESSION["errortype"] = "danger";
      $_SESSION["errormsg"] = "You can only have (1) store opened. If you need to open another store, please upgrade your account to Premium.";
  
      header('Location: index');
      exit();
    }
  }

  //Insert into the database
  $sql1          = "INSERT INTO communities (name, abbreviation, discord, owner) VALUES (:name, :abbreviation, :discord, :owner)";
  $stmt1         = $pdo->prepare($sql1);
  $stmt1->bindValue(':name', $communityName);
  $stmt1->bindValue(':abbreviation', $communityAbbreviation);
  $stmt1->bindValue(':discord', $communityDiscord);
  $stmt1->bindValue(':owner', $_SESSION['user_id']);
  $result_panelCreate = $stmt1->execute();
  if ($result_panelCreate) {
    header('Location: /p/' . $communityAbbreviation . '');
    exit();
  }
}
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
      <?php if ($user['verified'] === "false") : ?>
        <div class="alert dark alert-danger" role="alert">Your account isn't verified! Without verification, some core features of ServerFund will be disabled. Please check your email, along with any spam / junk folders.</div>
      <?php endif; ?>
      <?php if (isset($message)) {
        echo $message;
      } ?>
      <div class="alert dark alert-dark" role="alert">
        <strong>ServerFund is <i>NOT AFFILIATED, OR HAS ANY FORM OF CONNECTION</i> with FiveM, or any other game companies. This service is 100% safe to use, and can <i>NOT</i> get you blacklisted, or banned.</strong>
      </div>
      <div class="panel">
        <div class="panel-heading">
          <h3 class="panel-title">Create Store</h3>
        </div>
        <div class="panel-body">
          <form method="POST" action="index" autocomplete="off">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group form-material" data-plugin="formMaterial">
                  <label class="form-control-label" for="inputText">Community Name</label>
                  <input type="text" class="form-control" id="communityName" name="communityName" placeholder="My Community RP" required />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group form-material" data-plugin="formMaterial">
                  <label class="form-control-label" for="inputText">Community Abbreviation</label>
                  <input type="text" class="form-control" id="communityAbbreviation" name="communityAbbreviation" placeholder="MCRP" required />
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group form-material" data-plugin="formMaterial">
                  <label class="form-control-label" for="inputText">Discord Link</label>
                  <input type="text" class="form-control" id="communityDiscord" name="communityDiscord" placeholder="https://discord.gg/xxxxxx" value="https://discord.gg/" required />
                </div>
              </div>
            </div>
            <button type="submit" name="createPanelBtn" class="btn btn-primary btn-md float-right">Create Panel</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Page -->


  <?php include 'backend/inc/footer.php'; ?>
  <?php include 'backend/inc/js.php'; ?>
</body>

</html>