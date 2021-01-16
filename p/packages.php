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

//Delete the package (not rlly for security reasons)
if (isset($_GET['del'])) {
  $id = strip_tags($_GET['del']);

  //Checks if they have permission
  if ($pInfo['owner'] !== $_SESSION['user_id']) {
    header('Location: 404');
    exit();
  }

  //Set it as delete
  $sql2 = "UPDATE products SET deleted=? WHERE id=?";
  $pdo->prepare($sql2)->execute(['true', $id]);

  //Redirect when done
  $_SESSION["errortype"] = "success";
  $_SESSION["errormsg"] = "Package Deleted";

  header('Location: packages');
  exit();
} elseif (isset($_GET['hide'])) {
  $id = strip_tags($_GET['hide']);

  //Checks if they have permission
  if ($pInfo['owner'] !== $_SESSION['user_id']) {
    header('Location: ../404');
    exit();
  }

  //Set it as hidden
  $sql2 = "UPDATE products SET visible=? WHERE id=?";
  $pdo->prepare($sql2)->execute(['false', $id]);

  //Redirect when done
  $_SESSION["errortype"] = "success";
  $_SESSION["errormsg"] = "Package Hidden";

  header('Location: packages');
  exit();
} elseif (isset($_GET['unhide'])) {
  $id = strip_tags($_GET['unhide']);

  //Checks if they have permission
  if ($pInfo['owner'] !== $_SESSION['user_id']) {
    header('Location: ../404');
    exit();
  }

  //Set it as hidden
  $sql2 = "UPDATE products SET visible=? WHERE id=?";
  $pdo->prepare($sql2)->execute(['true', $id]);

  //Redirect when done
  $_SESSION["errortype"] = "success";
  $_SESSION["errormsg"] = "Package Unhidden";

  header('Location: packages');
  exit();
} elseif (isset($_POST['addPackageBtn'])) {
  //Sanitize
  $newPackage['name'] = !empty($_POST['packageName']) ? trim($_POST['packageName']) : null;
  $newPackage['price'] = !empty($_POST['packagePrice']) ? trim($_POST['packagePrice']) : null;

  $newPackage['name']  = strip_tags($_POST['packageName']);
  $newPackage['price']  = strip_tags($_POST['packagePrice']);

  $newPackage['desc']     = nl2br(htmlentities($_POST['packageDesc'], ENT_QUOTES, 'UTF-8'));

  if (!preg_match("/^[0-9.]+$/i", $newPackage['price'])) {
    $_SESSION["errortype"] = "danger";
    $_SESSION["errormsg"] = "Illegal characters used in price field.";

    header('Location: packages');
    exit();
  }

  //Insert into the database
  $sql1          = "INSERT INTO products (
		name, description, price, cid) VALUES (
		:name, :description, :price, :cid)";
  $stmt1         = $pdo->prepare($sql1);
  $stmt1->bindValue(':name', $newPackage['name']);
  $stmt1->bindValue(':description', $newPackage['desc']);
  $stmt1->bindValue(':price', $newPackage['price']);
  $stmt1->bindValue(':cid', $_SESSION['cid']);
  $result = $stmt1->execute();
  if ($result) {
    $_SESSION["errortype"] = "success";
    $_SESSION["errormsg"] = "New Package Created";

    header('Location: packages');
    exit();
  }
} elseif (isset($_POST['editPackageBtn'])) {
  //Sanitize
  $editPackage['name'] = !empty($_POST['packageName']) ? trim($_POST['packageName']) : null;
  $editPackage['price'] = !empty($_POST['packagePrice']) ? trim($_POST['packagePrice']) : null;

  $editPackage['name']  = strip_tags($_POST['packageName']);
  $editPackage['price']  = strip_tags($_POST['packagePrice']);

  $editPackage['desc']     = nl2br(htmlentities($_POST['packageDesc'], ENT_QUOTES, 'UTF-8'));

  if (!preg_match("/^[0-9.]+$/i", $editPackage['price'])) {
      $_SESSION["errortype"] = "danger";
      $_SESSION["errormsg"] = "Illegal characters used in price field";

      header('Location: packages');
      exit();
  }

  $sql3 = "UPDATE `products` SET `name`=:name, `description`=:description, `price`=:price WHERE `cid`=:cid AND `id`=:pkg_id";
  $stmt3 = $pdo->prepare($sql3);
  $stmt3->bindValue(':name', $editPackage['name']);
  $stmt3->bindValue(':description', $editPackage['desc']);
  $stmt3->bindValue(':price', $editPackage['price']);
  $stmt3->bindValue(':cid', $_SESSION['cid']);
  $stmt3->bindValue(':pkg_id', $_SESSION['editingPackageID']);
  $result3 = $stmt3->execute();
  if ($result3) {
    $_SESSION["errortype"] = "success";
    $_SESSION["errormsg"] = "Package Updated";

    header('Location: packages');
    exit();
  }
} elseif (isset($_POST['createSaleBtn'])) {
  if(is_premium === "true") {
    //Sanitize
    $newSale['pkgId']     = strip_tags($_POST['packageID']);
    $newSale['discount']  = strip_tags($_POST['percentOff']);
    $newSale['end']       = strip_tags($_POST['saleEnd']);

    $sql21             = "SELECT * FROM `products` WHERE `id` = ?";
    $stmt21            = $pdo->prepare($sql21);
    $stmt21->execute([$newSale['pkgId']]);
    $checkPkgForSale = $stmt21->fetch(PDO::FETCH_ASSOC);

    if ($checkPkgForSale['cid'] !== $_SESSION['cid']) {
      $_SESSION["errortype"] = "danger";
      $_SESSION["errormsg"] = "You can not edit that package!";
  
      header('Location: packages');
      exit();
    } elseif ($checkPkgForSale['sale'] !== NULL) {
      $_SESSION["errortype"] = "danger";
      $_SESSION["errormsg"] = "That package is already on sale!";
  
      header('Location: packages');
      exit();
    } else {
      $sql22 = "UPDATE `products` SET `sale`=:sale, `sale_end`=:sale_end WHERE `id`=:pkg_id";
      $stmt22 = $pdo->prepare($sql22);
      $stmt22->bindValue(':sale', $newSale['discount']);
      $stmt22->bindValue(':sale_end', $newSale['end']);
      $stmt22->bindValue(':pkg_id', $newSale['pkgId']);
      $result22 = $stmt22->execute();
      if ($result22) {
        $_SESSION["errortype"] = "success";
        $_SESSION["errormsg"] = "Sale started on Package #".$newSale['pkgId']." and will end on ".$newSale['end']."";

        header('Location: packages');
        exit();
      }
    }
  } else {
      $_SESSION["errortype"] = "danger";
      $_SESSION["errormsg"] = "This feature is only for Premium users.";

      header('Location: packages');
      exit();
  }
}
?>
<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">

<head>
  <?php include '../backend/inc/header.php'; ?>
  <link rel="stylesheet" href="<?php echo $host_url; ?>/assets/vendor/bootstrap-datepicker/bootstrap-datepicker.min.css?<?php echo $assets_ver; ?>">
  <link rel="stylesheet" href="<?php echo $host_url; ?>/assets/vendor/summernote/summernote-bs4.css?<?php echo $assets_ver; ?>">
  <script src="<?php echo $host_url; ?>/assets/vendor/summernote/summernote-bs4.js?<?php echo $assets_ver; ?>"></script>
  <script>
    $(document).ready(function() {
      $('#summernote').summernote({
        tabsize: 2,
        height: 100,
        codeviewIframeFilter: true
      });
      function isNumberKey(evt){
          var charCode = (evt.which) ? evt.which : event.keyCode
          if (charCode > 31 && (charCode < 48 || charCode > 57))
              return false;
          return true;
      }
    });
  </script>
</head>

<body class="animsition dashboard">
  <?php include '../backend/inc/nav.php'; ?>
  <!-- Page -->
  <div class="page">
    <div class="page-content container-fluid">
      <?php if (isset($message)) {
        echo $message;
      } ?>
      <div class="panel">
        <div class="panel-heading">
          <h3 class="panel-title"><?php echo $_SESSION['panel_abrv']; ?> Packages 
          <div class="float-right">
            <button class="btn btn-sm btn-primary" data-target="#newPackageModal" data-toggle="modal" type="button">Create Package</button>
            <button class="btn btn-sm btn-danger" data-target="#newSaleModal" data-toggle="modal" type="button">Start A Sale</button>
          </div>
        </h3>
        </div>
        <div class="panel-body">
          <table class="table">
            <thead>
              <tr>
                <th width='15%'>Package ID</th>
                <th width='30%'>Package Name</th>
                <th width='30%'>Price</th>
                <th width='10%'>Visible</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql             = "SELECT * FROM products WHERE cid = ? AND deleted = 'false'";
              $stmt            = $pdo->prepare($sql);
              $stmt->execute([$_SESSION['cid']]);
              $getPanelPackages = $stmt->fetchAll(PDO::FETCH_ASSOC);
              foreach ($getPanelPackages as $listPackages) : ?>
                <tr <?php if ($listPackages['visible'] === "false") {
                      echo 'class="table-danger"';
                    } ?>>
                  <td><?php echo $listPackages['id']; ?></td>
                  <td><?php echo $listPackages['name']; ?></td>
                  <td>$<?php echo $listPackages['price']; ?></td>
                  <td><?php echo $listPackages['visible']; ?></td>
                  <td>
                    <a class="btn btn-primary btn-xs openEditPackageModal" href="javascript:void(0);" data-href="../../../../../../backend/user/ajax/getPackageInfo.php?id=<?php echo $listPackages['id']; ?>"><i class="icon wb-pencil mr-5" aria-hidden="true"></i></a>
                    <a class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this package? This can not be undone')" href="packages?del=<?php echo $listPackages['id']; ?>"><i class="icon wb-minus mr-5" aria-hidden="true"></i></a>
                    <?php if ($listPackages['visible'] === "true") : ?>
                      <a class="btn btn-info btn-xs" onclick="return confirm('Are you sure you want to hide this package from displaying?')" href="packages?hide=<?php echo $listPackages['id']; ?>"><i class="icon wb-eye-close mr-5" aria-hidden="true"></i></a>
                    <?php else : ?>
                      <a class="btn btn-info btn-xs" onclick="return confirm('Are you sure you want to unhide this package from displaying?')" href="packages?unhide=<?php echo $listPackages['id']; ?>"><i class="icon wb-eye mr-5" aria-hidden="true"></i></a>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Edit Package Modal -->
      <div class="modal fade modal-fill-in" id="editPackageModal" aria-hidden="false" aria-labelledby="editPackageModal" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-simple">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
              <h4 class="modal-title" id="editPackageModalTitle">Edit Package</h4>
            </div>
            <div id="editPackageModalBody" class="modal-body">
              
            </div>
          </div>
        </div>
      </div>
      <!-- End Edit Package Modal -->

      <!-- New Package Modal -->
      <div class="modal fade modal-fill-in" id="newPackageModal" aria-hidden="false" aria-labelledby="newPackageModal" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-simple">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
              <h4 class="modal-title" id="newPackageModalTitle">New Package</h4>
            </div>
            <div class="modal-body">
              <form method="POST" action="packages">
                <div class="row">
                  <div class="col-xl-12 form-group form-material" data-plugin="formMaterial">
                    <input type="text" class="form-control" name="packageName" placeholder="Package Name" required>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xl-12 form-group form-material" data-plugin="formMaterial">
                    <input type="number" class="form-control" name="packagePrice" placeholder="Package Price (USD)" required>
                  </div>
                </div>
                <!-- <div class="row">
                  <div class="col-xl-6 form-group form-material" data-plugin="formMaterial">
                    <label class="form-control-label" for="packageDiscordRequired">Discord Required</label>
                    <select class="form-control" name="packageDiscordRequired" required>
                      <option value="false">No</option>
                      <option value="true">Yes</option>
                    </select>
                  </div>
                  <div class="col-xl-6 form-group form-material" data-plugin="formMaterial">
                    <label class="form-control-label" for="packageSteamRequired">Steam Required</label>
                    <select class="form-control" name="packageSteamRequired" required>
                      <option value="false">No</option>
                      <option value="true">Yes</option>
                    </select>
                  </div>
                </div> -->
                <div class="row">
                  <div class="col-xl-12">
                    <label class="form-control-label" for="inputText">Package Description</label>
                    <textarea id="summernote" name="packageDesc" placeholder="Package Description" required></textarea>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xl-12">
                    <button type="submit" name="addPackageBtn" class="btn btn-primary mt-10 float-right">Create Package</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- End New Package Modal -->

      <!-- New Sale Modal -->
      <div class="modal fade modal-fill-in" id="newSaleModal" aria-hidden="false" aria-labelledby="newSaleModal" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-simple">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
              <h4 class="modal-title" id="newSaleModalTitle">New Sale</h4>
            </div>
            <div class="modal-body">
              <?php if(is_premium === "true"): ?>
              <form method="POST" action="packages">
                <div class="row">
                  <div class="col-xl-12 form-group form-material" data-plugin="formMaterial">
                    <input type="number" class="form-control" name="packageID" placeholder="Package ID" required>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xl-12 form-group form-material" data-plugin="formMaterial">
                    <input type="number" class="form-control" name="percentOff" placeholder="Percent Off %" required>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xl-12 form-group form-material" data-plugin="formMaterial">
                    <input type="text" class="form-control" name="saleEnd" placeholder="Sale Until" data-plugin="datepicker" required>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xl-12">
                    <button type="submit" name="createSaleBtn" class="btn btn-primary mt-10 float-right">Create Sale</button>
                  </div>
                </div>
              </form>
              <?php else: ?>
              <div class="alert dark alert-danger" role="alert">Oops! This is for premium users only</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <!-- End New Sale Modal -->
    </div>
  </div>
  <!-- End Page -->

  <?php include '../backend/inc/footer.php'; ?>
  <?php include '../backend/inc/js.php'; ?>
  <script src="<?php echo $host_url; ?>/assets/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js?<?php echo $assets_ver; ?>"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('.openEditPackageModal').on('click',function(){
          var dataURL = $(this).attr('data-href');
          $('#editPackageModalBody.modal-body').load(dataURL,function(){
              $('#editPackageModal').modal({show:true});
          });
      });
    });
    </script>
</body>

</html>