<?php
session_name('serverfund');
session_start();
require_once '../backend/inc/connect.php';
require_once '../backend/inc/config.php';
$page['title'] = "Support Portal";

if (loggedIn) {
  require_once '../backend/user/auth/loggedIn.php';
} else {
  header('Location: ../login');
  exit();
}

if (isset($_POST['submitTicketBtn'])) {
  //Sanitize
  $newTicket['subject'] = !empty($_POST['ticketSubject']) ? trim($_POST['ticketSubject']) : null;
  $newTicket['severity']  = !empty($_POST['ticketSeverity']) ? trim($_POST['ticketSeverity']) : null;

  $newTicket['subject'] = strip_tags($_POST['ticketSubject']);
  $newTicket['severity']  = strip_tags($_POST['ticketSeverity']);
  $newTicket['body']     = nl2br(htmlentities($_POST['ticketBody'], ENT_QUOTES, 'UTF-8'));

  //Insert into the database
  $sql1          = "INSERT INTO tickets (
		subject, severity, status, body, creator, ip) VALUES (
		:subject, :severity, :status, :body, :creator, :ip)";
  $stmt1         = $pdo->prepare($sql1);
  $stmt1->bindValue(':subject', $newTicket['subject']);
  $stmt1->bindValue(':severity', $newTicket['severity']);
  $stmt1->bindValue(':status', 'Open');
  $stmt1->bindValue(':body', $newTicket['body']);
  $stmt1->bindValue(':creator', $_SESSION['user_id']);
  $stmt1->bindValue(':ip', $client_ip);
  $result_user = $stmt1->execute();
  if ($result_user) {
    ticketWebhook("**New Ticket**\n**Subject**: " . $newTicket['subject'] . "\n**Made**: " . $sqltimestamp);
    $_SESSION["errortype"] = "success";
    $_SESSION["errormsg"] = "New Ticket created";

    header('Location: support');
    exit();
  }
}

$_SESSION['panel'] = "N/A";
?>
<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">

<head>
  <?php include '../backend/inc/header.php'; ?>
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
  <link rel="stylesheet" href="<?php echo $host_url; ?>/assets/vendor/summernote/summernote-bs4.css?v4.0.2">
  <script src="<?php echo $host_url; ?>/assets/vendor/summernote/summernote-bs4.js?v4.0.2"></script>
  <script>
    $(document).ready(function() {
      $('#summernote').summernote({
        toolbar: [
          ['style', ['bold', 'italic', 'underline', 'clear']]
        ],
        placeholder: 'What can we help you with?',
        tabsize: 2,
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
      <?php if ($user['verified'] === "false") : ?>
        <div class="alert dark alert-danger" role="alert">Your account isn't verified! Without verification, some core features of ServerFund will be disabled. Please check your email, along with any spam / junk folders.</div>
      <?php endif; ?>
      <?php if (isset($message)) {
        echo $message;
      } ?>

      <div class="panel">
        <div class="panel-heading">
          <h3 class="panel-title">Support Portal</h3>
        </div>
        <div class="panel-body container-fluid">
          <div class="nav-tabs-horizontal" data-plugin="tabs">
            <ul class="nav nav-tabs nav-tabs-line" role="tablist">
              <li class="nav-item" role="presentation"><a class="nav-link active" data-toggle="tab" href="#ticketsTab" aria-controls="ticketsTab" role="tab">Tickets</a></li>
              <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#statusTab" aria-controls="statusTab" role="tab">Server Status
                  <?php
                  $sql = "SELECT count(*) FROM `server_issues`";
                  $result = $pdo->prepare($sql);
                  $result->execute();
                  $number_of_rows = $result->fetchColumn();
                  if ($number_of_rows !== 0) : ?>
                    <span class="badge badge-round badge-danger">!</span>
                  <?php endif; ?></a></li>
            </ul>
            <div class="tab-content pt-20">
              <div class="tab-pane active" id="ticketsTab" role="tabpanel">
                <button class="btn btn-sm btn-primary float-right" data-target="#newTicketModal" data-toggle="modal" type="button">New Ticket</button>
                <table class="table table-hover dataTable table-striped w-full" data-plugin="dataTable">
                  <thead>
                    <tr>
                      <th>Ticket ID</th>
                      <th>Subject</th>
                      <th>Severity</th>
                      <th>Status</th>
                      <th>View</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (is_staff === 'true') : ?>
                      <?php
                      $sql = "SELECT * FROM tickets";
                      $stmt = $pdo->prepare($sql);
                      $stmt->execute();
                      $getTicketsDB = $stmt->fetchAll(PDO::FETCH_ASSOC);
                      foreach ($getTicketsDB as $listTickets) : ?>
                        <tr>
                          <td><?php echo $listTickets['id']; ?></td>
                          <td><?php echo $listTickets['subject']; ?></td>
                          <td><?php echo $listTickets['severity']; ?></td>
                          <td><?php echo $listTickets['status']; ?></td>
                          <td><a class="btn btn-primary btn-xs" href="ticket/<?php echo $listTickets['id']; ?>"><i class="icon wb-arrow-right mr-5" aria-hidden="true"></i></a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else : ?>
                      <?php
                      $sql = "SELECT * FROM tickets WHERE creator = ?";
                      $stmt = $pdo->prepare($sql);
                      $stmt->execute([$_SESSION['user_id']]);
                      $getTicketsDB = $stmt->fetchAll(PDO::FETCH_ASSOC);
                      foreach ($getTicketsDB as $listTickets) : ?>
                        <tr>
                          <td><?php echo $listTickets['id']; ?></td>
                          <td><?php echo $listTickets['subject']; ?></td>
                          <td><?php echo $listTickets['severity']; ?></td>
                          <td><?php echo $listTickets['status']; ?></td>
                          <td><a class="btn btn-primary btn-xs" href="ticket/<?php echo $listTickets['id']; ?>"><i class="icon wb-arrow-right mr-5" aria-hidden="true"></i></a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <div class="tab-pane" id="statusTab" role="tabpanel">
                <?php
                $sql = "SELECT count(*) FROM `server_issues`";
                $result = $pdo->prepare($sql);
                $result->execute();
                $number_of_rows = $result->fetchColumn();

                if ($number_of_rows === 0) {
                  echo "<div class='alert dark alert-success' role='alert'>
										All Services Functional as of $date $time
									</div>";
                } else {
                  $sql2 = "SELECT * FROM server_issues";
                  $stmt2 = $pdo->prepare($sql2);
                  $stmt2->execute();
                  $getServerIssuesDB = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                  foreach ($getServerIssuesDB as $serverIssue) {
                    echo '<div class="panel panel-bordered panel-danger">
											<div class="panel-heading">
											  <h4 class="panel-title">Server Issue | Reported on ' . $serverIssue['timestamp'] . '</h4>
											</div>
											<div class="panel-body"><strong>' . $serverIssue['issue'] . '</strong></div>
										  </div>';
                  }
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- New Ticket Modal -->
      <div class="modal fade modal-fill-in" id="newTicketModal" aria-hidden="false" aria-labelledby="newTicketModal" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-simple">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
              <h4 class="modal-title" id="newTicketModalTitle">Create New Support Ticket</h4>
            </div>
            <div class="modal-body">
              <div class="alert dark alert-info" role="alert">
                Normal Support hours are <strong>Monday through Friday, 10 AM - 10 PM EST</strong>
              </div>
              <form method="POST" action="support">
                <div class="row">
                  <div class="col-xl-6 form-group form-material" data-plugin="formMaterial">
                    <input type="text" class="form-control" name="ticketSubject" placeholder="Subject" required>
                  </div>
                  <div class="col-xl-6 form-group form-material" data-plugin="formMaterial">
                    <select class="form-control" name="ticketSeverity" required>
                      <option disabled>Ticket Severity</option>
                      <option selected value="Low">Low</option>
                      <option value="Medium">Medium</option>
                      <option value="High">High</option>
                    </select>
                  </div>
                  <div class="col-xl-12">
                    <label class="form-control-label" for="inputText">What can we help you with?</label>
                    <textarea id="summernote" name="ticketBody" placeholder="What can we help you with?" required></textarea>
                  </div>
                  <div class="col-xl-12">
                    <button type="submit" name="submitTicketBtn" class="btn btn-primary mt-10 float-right">Create Ticket</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- End New Ticket Modal -->

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