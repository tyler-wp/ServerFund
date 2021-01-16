<?php
session_name('serverfund');
session_start();
require_once '../backend/inc/connect.php';
require_once '../backend/inc/config.php';
$page['title'] = "User Stores";

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
      <?php if ($user['verified'] === "false") : ?>
        <div class="alert dark alert-danger" role="alert">Your account isn't verified! Without verification, some core features of ServerFund will be disabled. Please check your email, along with any spam / junk folders.</div>
      <?php endif; ?>
      <?php if (isset($message)) {
        echo $message;
      } ?>

      <div class="panel">
        <div class="panel-heading">
          <h3 class="panel-title">User Stores</h3>
        </div>
        <div class="panel-body">
          <table class="table">
            <thead>
              <tr>
                <th class="hidden-xs-down">ID</th>
                <th>Name</th>
                <th>Abbreviation</th>
                <th class="hidden-xs-down">Created</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql             = "SELECT * FROM communities WHERE owner = ?";
              $stmt            = $pdo->prepare($sql);
              $stmt->execute([$_SESSION['user_id']]);
              $getPanelsDB = $stmt->fetchAll(PDO::FETCH_ASSOC);
              foreach ($getPanelsDB as $listPanels) : ?>
                <tr>
                  <td class="hidden-xs-down"><?php echo $listPanels['cid']; ?></td>
                  <td><?php echo $listPanels['name']; ?></td>
                  <td><?php echo $listPanels['abbreviation']; ?></td>
                  <td class="hidden-xs-down"><?php echo $listPanels['created']; ?></td>
                  <td><a class="btn btn-primary btn-xs" href="<?php echo $host_url; ?>/p/<?php echo $listPanels['abbreviation']; ?>"><i class="icon wb-arrow-right mr-5" aria-hidden="true"></i></a></td>
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