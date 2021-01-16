<?php
session_name('serverfund');
session_start();
require_once '../../inc/connect.php';

require_once '../../inc/config.php';

$packageName = strip_tags($_POST['packageName']);
$packageDesc = nl2br(htmlentities($_POST['packageDesc'], ENT_QUOTES, 'UTF-8'));
$packagePrice = strip_tags($_POST['packagePrice']);
$packageVisible = strip_tags($_POST['packageVisible']);

$error = array();

$sql = "UPDATE `products` SET `name`=:name, `description`=:desc, `price`=:price, `visible`=:visible WHERE `id`=:id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $_SESSION['editingPackage']);
$stmt->bindValue(':name', $packageName);
$stmt->bindValue(':desc', $packageDesc);
$stmt->bindValue(':price', $packagePrice);
$stmt->bindValue(':visible', $packageVisible);
$result = $stmt->execute();
if ($result) {
    $error['msg'] = "";
    echo json_encode($error);
    exit();
}