<?php
// Set all values to equal nothing first just to make sure nobody some how gets staff access or whatever
$group['access'] = false;
$group['premium'] = false;
$group['moderator'] = false;
$group['admin'] = false;
$group['superadmin'] = false;

// Pull the usergroup from the database
$sql1_gp             = "SELECT * FROM usergroups WHERE gid = :usergroup";
$stmt1_gp            = $pdo->prepare($sql1_gp);
$stmt1_gp->bindValue(':usergroup', $user['usergroup']);
$stmt1_gp->execute();
$groupRow = $stmt1_gp->fetch(PDO::FETCH_ASSOC);

// Define variables
$group['access'] = $groupRow['access'];
$group['premium'] = $groupRow['premium'];
$group['moderator'] = $groupRow['moderator'];
$group['admin'] = $groupRow['admin'];
$group['superadmin'] = $groupRow['superadmin'];

define("has_access", $group['access']);
define("is_premium", $group['premium']);
define("is_moderator", $group['moderator']);
define("is_admin", $group['admin']);
define("is_superAdmin", $group['superadmin']);

if (is_moderator === "true" || is_admin === "true" || is_superAdmin === "true") {
  define("is_staff", "true");
} else {
  define("is_staff", "false");
}

if (has_access === 'false') {
    session_unset();
    session_destroy();
    header('Location: login.php?cdm=6');
    exit();
}
