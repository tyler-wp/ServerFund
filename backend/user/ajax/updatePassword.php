<?php
session_name('serverfund');
session_start();
require_once '../../inc/connect.php';

require_once '../../inc/config.php';

$NewPass1 = strip_tags($_POST['userNewPassword1Input']);
$NewPass2 = strip_tags($_POST['userNewPassword2Input']);

$error = array();

//Check if new password, and confirm password match
if ($NewPass1 !== $NewPass2) {
    $error['msg'] = "Your passwords don't match!";
    echo json_encode($error);
    exit();
}

sf_email($user['email'], "[ServerFund] Password Changed", "This email is to alert you that your ServerFund password has been changed. If this was you, please ignore this message. If this was not you, please change your account password and contact Staff to help secure your account. \r\nIP: $ip");

$passwordHash = password_hash($NewPass2, PASSWORD_BCRYPT, array("cost" => 12));

$sql2 = "UPDATE `users` SET `password`=:new_pass WHERE `uid`=:uid";
$stmt2 = $pdo->prepare($sql2);
$stmt2->bindValue(':new_pass', $passwordHash);
$stmt2->bindValue(':uid', $_SESSION['user_id']);
$stmt2->execute();

$error['msg'] = "";
echo json_encode($error);
exit();