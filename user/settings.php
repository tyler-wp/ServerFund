<?php
session_name('serverfund');
session_start();
require_once '../backend/inc/connect.php';
require_once '../backend/inc/config.php';
$page['title'] = "User Settings";

if (loggedIn) {
  require_once '../backend/user/auth/loggedIn.php';
} else {
  header('Location: ../login');
  exit();
}

$_SESSION['panel'] = "N/A";

if (isset($_GET['a'])) {
  $a = strip_tags($_GET['a']);
  if ($a === "verify") {
    $sql = "UPDATE `users` SET `verified`='true' WHERE `uid`=:uid";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':uid', $_SESSION['user_id']);
    $stmt->execute();
    $_SESSION["errortype"] = "success";
    $_SESSION["errormsg"] = "Email verified";

    header('Location: ../user/settings');
    exit();
  }
}

// Update user information
if (isset($_POST['updateUserInfoBtn'])) {
  //Sanitize
  $newEmail = strip_tags($_POST['userEmailInput']);

  $sql2       = "SELECT COUNT(email) AS num FROM users WHERE email = :email";
  $stmt2      = $pdo->prepare($sql2);
  $stmt2->bindValue(':email', $newEmail);
  $stmt2->execute();
  $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
  if ($row2['num'] > 0) {
    $_SESSION["errortype"] = "danger";
    $_SESSION["errormsg"] = "That email is already taken";

    header('Location: ../user/settings');
    exit();
  } else {
    //Do the update
    $sql3 = "UPDATE `users` SET `email`=:email WHERE `uid`=:uid";
    $stmt3 = $pdo->prepare($sql3);
    $stmt3->bindValue(':email', $newEmail);
    $stmt3->bindValue(':uid', $_SESSION['user_id']);
    $result3 = $stmt3->execute();
    if ($result3) {
      $_SESSION["errortype"] = "success";
      $_SESSION["errormsg"] = "User settings updated";
  
      header('Location: ../logout?rm=settings-updated');
      exit();
    }
  }
}
?>
<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">

<head>
  <?php include '../backend/inc/header.php'; ?>
  <script type="text/javascript">
    $(document).ready(function() {
      $('#userSettingsPassword').ajaxForm(function(error) {
        console.log(error);
        error = JSON.parse(error);
        if (error['msg'] === "") {
          toastr.success('Password Updated!', 'System:', {
            timeOut: 10000
          })
        } else {
          toastr.error(error['msg'], 'System:', {
            timeOut: 10000
          })
        }
      });
    });
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

      <div class="panel">
        <div class="panel-body">
          <div class="nav-tabs-vertical" data-plugin="tabs">
            <ul class="nav nav-tabs nav-tabs-line mr-25" role="tablist">
              <li class="nav-item" role="presentation"><a class="nav-link active" data-toggle="tab" href="#userSettingsInfoTab" aria-controls="userSettingsInfoTab" role="tab">Information</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#userSettingsPasswordTab" aria-controls="userSettingsPasswordTab" role="tab">Password</a></li>
            </ul>
            <div class="tab-content py-15">
              <div class="tab-pane active" id="userSettingsInfoTab" role="tabpanel">
                <form method="POST">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group form-material" data-plugin="formMaterial">
                        <label class="form-control-label" for="inputText">First Name</label>
                        <input type="text" class="form-control" value="<?php echo $user['first_name']; ?>" disabled />
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group form-material" data-plugin="formMaterial">
                        <label class="form-control-label" for="inputText">Last Name</label>
                        <input type="text" class="form-control" value="<?php echo $user['last_name']; ?>" disabled />
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group form-material" data-plugin="formMaterial">
                        <label class="form-control-label" for="userEmailInput">Email</label>
                        <input type="text" class="form-control" id="userEmailInput" name="userEmailInput" value="<?php echo $user['email']; ?>" required />
                      </div>
                    </div>
                  </div>
                  <button type="submit" name="updateUserInfoBtn" class="btn btn-success btn-sm float-right">Update</button>
                </form>
              </div>

              <div class="tab-pane" id="userSettingsPasswordTab" role="tabpanel">
                <form method="POST" action="../backend/user/ajax/updatePassword.php" id="userSettingsPassword">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group form-material" data-plugin="formMaterial">
                        <label class="form-control-label" for="userNewPassword1Input">New Password</label>
                        <input type="password" class="form-control" id="userNewPassword1Input" name="userNewPassword1Input" placeholder="New Password..." required />
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group form-material" data-plugin="formMaterial">
                        <label class="form-control-label" for="userNewPassword2Input">Confirm New Password</label>
                        <input type="password" class="form-control" id="userNewPassword2Input" name="userNewPassword2Input" placeholder="Confirm New Password..." required />
                      </div>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-success btn-sm float-right">Change Password</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End Page -->

  <?php include '../backend/inc/footer.php'; ?>
  <?php include '../backend/inc/js.php'; ?>
</body>

</html>