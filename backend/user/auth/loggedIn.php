<?php
$uid = $_SESSION['user_id'];
$sql_ili             = "SELECT * FROM users WHERE uid = :uid";
$stmt_ili            = $pdo->prepare($sql_ili);
$stmt_ili->bindValue(':uid', $uid);
$stmt_ili->execute();
$userRow = $stmt_ili->fetch(PDO::FETCH_ASSOC);

if ($userRow === false) {
	header('Location: logout.php');
	exit();
}

// Define variables

$user['first_name'] = $userRow['first_name'];
$user['last_name'] = $userRow['last_name'];
$user['email'] = $userRow['email'];
$user['usergroup'] = $userRow['usergroup'];
$user['verified'] = $userRow['verified'];
$user['membership_expire'] = $userRow['membership_expire'];

//Membership check
$msc_date = new DateTime($user['membership_expire']);
$msc_now = new DateTime();
if ($user['membership_expire'] !== NULL) {
	if($msc_date < $msc_now) {
		$stmt_msc   = $pdo->prepare("UPDATE `users` SET `usergroup`='1', `membership_expire` = NULL WHERE uid = ?");
		$result_msc = $stmt_msc->execute([$_SESSION['user_id']]);
	}
}

require_once 'groupPerms.php';

// require('groupPermissions.php');
