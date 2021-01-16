<?php
session_name('serverfund');
session_start();
require_once '../backend/inc/connect.php';
require_once '../backend/inc/config.php';
$page['title'] = "[Staff] All Users";

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
  <link rel="stylesheet" href="<?php echo $host_url; ?>/assets/vendor/datatables.net-bs4/dataTables.bootstrap4.min.css?v4.0.2">
  <link rel="stylesheet" href="<?php echo $host_url; ?>/assets/vendor/datatables.net-fixedheader-bs4/dataTables.fixedheader.bootstrap4.min.css?v4.0.2">
  <link rel="stylesheet" href="<?php echo $host_url; ?>/assets/vendor/datatables.net-fixedcolumns-bs4/dataTables.fixedcolumns.bootstrap4.min.css?v4.0.2">
  <link rel="stylesheet" href="<?php echo $host_url; ?>/assets/vendor/datatables.net-rowgroup-bs4/dataTables.rowgroup.bootstrap4.min.css?v4.0.2">
  <link rel="stylesheet" href="<?php echo $host_url; ?>/assets/vendor/datatables.net-scroller-bs4/dataTables.scroller.bootstrap4.min.css?v4.0.2">
  <link rel="stylesheet" href="<?php echo $host_url; ?>/assets/vendor/datatables.net-select-bs4/dataTables.select.bootstrap4.min.css?v4.0.2">
  <link rel="stylesheet" href="<?php echo $host_url; ?>/assets/vendor/datatables.net-responsive-bs4/dataTables.responsive.bootstrap4.min.css?v4.0.2">
  <link rel="stylesheet" href="<?php echo $host_url; ?>/assets/vendor/datatables.net-buttons-bs4/dataTables.buttons.bootstrap4.min.css?v4.0.2">

  <!-- Page -->
  <link rel="stylesheet" href="<?php echo $host_url; ?>/assets/examples/css/tables/datatable.min.css?v4.0.2">

  <!-- Page -->
  <div class="page">
    <div class="page-content container-fluid">
      <?php if (isset($message)) {
        echo $message;
      } ?>

      <div class="panel">
        <div class="panel-heading">
          <h3 class="panel-title">Displaying all users</h3>
        </div>
        <div class="panel-body">
          <table class="table table-hover dataTable table-striped w-full" data-plugin="dataTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Usergroup</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql             = "SELECT * FROM users";
              $stmt            = $pdo->prepare($sql);
              $stmt->execute();
              $listAllUsersDB = $stmt->fetchAll(PDO::FETCH_ASSOC);
              foreach ($listAllUsersDB as $listUsers) : ?>
                <tr>
                  <td><?php echo $listUsers['uid']; ?></td>
                  <td><?php echo $listUsers['first_name'] .' '. $listUsers['last_name']; ?></td>
                  <td><?php echo $listUsers['email']; ?></td>
                  <td><?php echo $listUsers['usergroup']; ?></td>
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
  <script src="<?php echo $host_url; ?>/assets/vendor/datatables.net/jquery.dataTables.js?v4.0.2"></script>
	<script src="<?php echo $host_url; ?>/assets/vendor/datatables.net-bs4/dataTables.bootstrap4.js?v4.0.2"></script>
	<script src="<?php echo $host_url; ?>/assets/vendor/datatables.net-fixedheader/dataTables.fixedHeader.js?v4.0.2">
	</script>
	<script src="<?php echo $host_url; ?>/assets/vendor/datatables.net-fixedcolumns/dataTables.fixedColumns.js?v4.0.2">
	</script>
	<script src="<?php echo $host_url; ?>/assets/vendor/datatables.net-rowgroup/dataTables.rowGroup.js?v4.0.2"></script>
	<script src="<?php echo $host_url; ?>/assets/vendor/datatables.net-scroller/dataTables.scroller.js?v4.0.2"></script>
	<script src="<?php echo $host_url; ?>/assets/vendor/datatables.net-responsive/dataTables.responsive.js?v4.0.2">
	</script>
	<script src="<?php echo $host_url; ?>/assets/vendor/datatables.net-responsive-bs4/responsive.bootstrap4.js?v4.0.2">
	</script>
	<script src="<?php echo $host_url; ?>/assets/vendor/datatables.net-buttons/dataTables.buttons.js?v4.0.2"></script>
	<script src="<?php echo $host_url; ?>/assets/vendor/datatables.net-buttons/buttons.html5.js?v4.0.2"></script>
	<script src="<?php echo $host_url; ?>/assets/vendor/datatables.net-buttons/buttons.flash.js?v4.0.2"></script>
	<script src="<?php echo $host_url; ?>/assets/vendor/datatables.net-buttons/buttons.print.js?v4.0.2"></script>
	<script src="<?php echo $host_url; ?>/assets/vendor/datatables.net-buttons/buttons.colVis.js?v4.0.2"></script>
	<script src="<?php echo $host_url; ?>/assets/vendor/datatables.net-buttons-bs4/buttons.bootstrap4.js?v4.0.2">
	</script>

</body>

</html>