<?php
session_name('serverfund');
session_start();
require_once '../../inc/connect.php';

require_once '../../inc/config.php';

$packageID = strip_tags($_GET['id']);
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$packageID]);
$packageInfo = $stmt->fetch(PDO::FETCH_ASSOC);

$_SESSION['editingPackageID'] = $packageID;
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet" href="<?php echo $host_url; ?>/assets/vendor/summernote/summernote-bs4.css?v4.0.2">
    <script src="<?php echo $host_url; ?>/assets/vendor/summernote/summernote-bs4.js?v4.0.2"></script>
    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                tabsize: 2,
                height: 100,
                codeviewIframeFilter: true
            });
        });
    </script>
</head>

<body>
    <form method="POST" action="packages">
        <div class="row">
            <div class="col-xl-12 form-group form-material" data-plugin="formMaterial">
                <input type="text" class="form-control" name="packageName" placeholder="Package Name" value="<?php echo $packageInfo['name']; ?>" required>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12 form-group form-material" data-plugin="formMaterial">
                <input type="text" class="form-control" name="packagePrice" placeholder="Package Price (USD)" value="<?php echo $packageInfo['price']; ?>" required>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <label class="form-control-label" for="inputText">Package Description</label>
                <textarea id="summernote" name="packageDesc" placeholder="Package Description" required><?php echo $packageInfo['description']; ?></textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <button type="submit" name="editPackageBtn" class="btn btn-primary mt-10 float-right">Edit Package</button>
            </div>
        </div>
    </form>
</body>

</html>