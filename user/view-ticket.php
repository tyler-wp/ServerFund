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

if (isset($_GET['t'])) {
  $t = strip_tags($_GET['t']);
  //Pull information from database
  $sql_tInfo  = "SELECT * FROM tickets WHERE id = :id";
  $stmt_tInfo = $pdo->prepare($sql_tInfo);
  $stmt_tInfo->bindValue(':id', $t);
  $stmt_tInfo->execute();
  $tInfo = $stmt_tInfo->fetch(PDO::FETCH_ASSOC);

  //Check if the panel actually exists or not
  if ($tInfo === false) {
    $_SESSION["errortype"] = "danger";
    $_SESSION["errormsg"] = "Ticket not found";

    header('Location: ../support');
    exit();
  } elseif ($tInfo['creator'] === $_SESSION['user_id'] || is_staff === 'true') {
    $ticketView['id']      = $tInfo['id'];
    $ticketView['subject'] = $tInfo['subject'];
    $ticketView['severity'] = $tInfo['severity'];
    $ticketView['status'] = $tInfo['status'];
    $ticketView['body'] = $tInfo['body'];
    $ticketView['creator'] = $tInfo['creator'];
    $ticketView['created'] = $tInfo['created'];
    $ticketView['creator_ip'] = $tInfo['ip'];

    $sql_tcInfo  = "SELECT * FROM users WHERE uid = :id";
    $stmt_tcInfo = $pdo->prepare($sql_tcInfo);
    $stmt_tcInfo->bindValue(':id', $ticketView['creator']);
    $stmt_tcInfo->execute();
    $tcInfo = $stmt_tcInfo->fetch(PDO::FETCH_ASSOC);

    $sql_tcgInfo  = "SELECT * FROM usergroups WHERE gid = :id";
    $stmt_tcgInfo = $pdo->prepare($sql_tcgInfo);
    $stmt_tcgInfo->bindValue(':id', $tcInfo['usergroup']);
    $stmt_tcgInfo->execute();
    $tcgInfo = $stmt_tcgInfo->fetch(PDO::FETCH_ASSOC);
  } else {
    $_SESSION["errortype"] = "danger";
    $_SESSION["errormsg"] = "Ticket not found";

    header('Location: ../support');
    exit();
  }
} else {
  $_SESSION["errortype"] = "danger";
  $_SESSION["errormsg"] = "Ticket not found";

  header('Location: ../support');
  exit();
}

if (isset($_POST['replyTicketBtn'])) {
  //Sanitize
  $ticketReply     = nl2br(htmlentities($_POST['ticketReply'], ENT_QUOTES, 'UTF-8'));

  if (is_moderator === 'true' || is_admin === 'true' || is_superAdmin === 'user') {
    //Staff response
    $sql1          = "INSERT INTO ticket_replys (reply, tid, uid, type) VALUES (:reply, :tid, :uid, :type)";
    $stmt1         = $pdo->prepare($sql1);
    $stmt1->bindValue(':reply', $ticketReply);
    $stmt1->bindValue(':tid', $ticketView['id']);
    $stmt1->bindValue(':uid', $_SESSION['user_id']);
    $stmt1->bindValue(':type', 'staff');
    $result_user = $stmt1->execute();
    if ($result_user) {
      ticketWebhook("**New Ticket Reply**\n**Ticket #**" . $ticketView['id'] . "\n**Reply**: " . $ticketReply . "\n**Made**: " . $sqltimestamp);
      $sql3 = "UPDATE `tickets` SET `status`=:status WHERE `id`=:tid";
      $stmt3 = $pdo->prepare($sql3);
      $stmt3->bindValue(':status', 'Staff Response');
      $stmt3->bindValue(':tid', $ticketView['id']);
      $result3 = $stmt3->execute();

      $_SESSION["errortype"] = "success";
      $_SESSION["errormsg"] = "Ticket reply added";

      header('Location: ' . $_SERVER['REQUEST_URI']);
      exit();
    }
  } else {
    //User response
    ticketWebhook("**New Ticket Reply**\n**Ticket #**" . $ticketView['id'] . "\n**Reply**: " . $ticketReply . "\n**Made**: " . $sqltimestamp);
    $sql1          = "INSERT INTO ticket_replys (reply, tid, uid, type) VALUES (:reply, :tid, :uid, :type)";
    $stmt1         = $pdo->prepare($sql1);
    $stmt1->bindValue(':reply', $ticketReply);
    $stmt1->bindValue(':tid', $ticketView['id']);
    $stmt1->bindValue(':uid', $_SESSION['user_id']);
    $stmt1->bindValue(':type', 'user');
    $result_user = $stmt1->execute();
    if ($result_user) {
      $sql3 = "UPDATE `tickets` SET `status`=:status WHERE `id`=:tid";
      $stmt3 = $pdo->prepare($sql3);
      $stmt3->bindValue(':status', 'User Response');
      $stmt3->bindValue(':tid', $ticketView['id']);
      $result3 = $stmt3->execute();

      $_SESSION["errortype"] = "success";
      $_SESSION["errormsg"] = "Ticket reply added";

      header('Location: ' . $_SERVER['REQUEST_URI']);
      exit();
    }
  }
}

if (isset($_POST['closeTicketBtn'])) {
  $sql = "UPDATE `tickets` SET `status`=:status WHERE `id`=:tid";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':status', 'Closed');
  $stmt->bindValue(':tid', $ticketView['id']);
  $result = $stmt->execute();

  $_SESSION["errortype"] = "success";
  $_SESSION["errormsg"] = "Ticket closed";


  header('Location: ../support');
  exit();
}

$_SESSION['panel'] = "N/A";
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

      <div class="row">
        <div class="col-lg-4">
          <div class="panel panel-bordered">
            <div class="panel-heading">
              <h3 class="panel-title">Ticket Information</h3>
            </div>
            <div class="panel-body">
              <p>
                <strong>Subject:</strong> <?php echo $ticketView['subject']; ?> <br>
                <strong>Created On:</strong> <?php echo $ticketView['created']; ?> <br>
                <strong>Created By:</strong>
                <?php echo $tcInfo['first_name'] . ' ' . $tcInfo['last_name'] . ' [' . $tcgInfo['name'] . ']'; ?>
                <?php if ($ticketView['status'] !== "Closed") : ?>
                  <form method="POST"><button type="submit" name="closeTicketBtn" class="btn btn-danger btn-sm btn-block">Close Ticket</button></form>
                <?php endif; ?>
              </P>
            </div>
          </div>
        </div>
        <div class="col-lg-8">
          <div class="panel panel-bordered">
            <div class="panel-heading">
              <h3 class="panel-title"><i class="icon wb-user" aria-hidden="true"></i><?php echo $tcInfo['first_name'] . ' ' . $tcInfo['last_name'] . ' [' . $tcgInfo['name'] . ']'; ?>
              </h3>
            </div>
            <div class="panel-body">
              <p><?php echo htmlspecialchars_decode(stripslashes($ticketView['body'])); ?></P>
            </div>
            <div class="panel-footer"><?php echo $ticketView['created']; ?> -
              <?php echo $ticketView['creator_ip'];  ?></div>
          </div>

          <?php
          $sql = "SELECT * FROM ticket_replys WHERE tid = ?";
          $stmt = $pdo->prepare($sql);
          $stmt->execute([$ticketView['id']]);
          $getTicketRepliesDB = $stmt->fetchAll(PDO::FETCH_ASSOC);
          foreach ($getTicketRepliesDB as $listTicketReply) :

            $sql_trInfo  = "SELECT * FROM users WHERE uid = :id";
            $stmt_trInfo = $pdo->prepare($sql_trInfo);
            $stmt_trInfo->bindValue(':id', $listTicketReply['uid']);
            $stmt_trInfo->execute();
            $trInfo = $stmt_trInfo->fetch(PDO::FETCH_ASSOC);

            $sql_trgInfo  = "SELECT * FROM usergroups WHERE gid = :id";
            $stmt_trgInfo = $pdo->prepare($sql_trgInfo);
            $stmt_trgInfo->bindValue(':id', $trInfo['usergroup']);
            $stmt_trgInfo->execute();
            $trgInfo = $stmt_trgInfo->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class="panel panel-bordered">
              <div class="panel-heading">
                <h3 class="panel-title"><i class="icon wb-user" aria-hidden="true"></i><?php echo $trInfo['first_name'] . ' ' . $trInfo['last_name'] . ' [' . $trgInfo['name'] . ']'; ?>
                </h3>
              </div>
              <?php
              if ($listTicketReply['type'] === "staff") {
                echo '<div class="alert dark alert-danger" role="alert"><strong>Staff Response</strong></div>';
              }
              ?>
              <div class="panel-body">
                <p><?php echo htmlspecialchars_decode(stripslashes($listTicketReply['reply'])); ?></P>
              </div>
              <div class="panel-footer"><?php echo $listTicketReply['timestamp']; ?></div>
            </div>
          <?php endforeach; ?>
          <?php if ($ticketView['status'] === "Closed") : ?>
            <div class="alert dark alert-danger" role="alert"><strong>This ticket is closed to further
                responses. Please open a new ticket if you require assistance</strong></div>
          <?php else : ?>
            <div class="panel panel-bordered">
              <form method="POST">
                <div class="panel-heading">
                  <h3 class="panel-title">Ticket Reply <button type="submit" name="replyTicketBtn" class="btn btn-primary btn-sm float-right">Add Reply</button></h3>
                </div>
                <textarea id="summernote" name="ticketReply" required></textarea>
              </form>
            </div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>
  <!-- End Page -->

  <?php include '../backend/inc/footer.php'; ?>
  <?php include '../backend/inc/js.php'; ?>
</body>

</html>