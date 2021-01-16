<?php
// General Configuration
$GLOBAL['language'] = "en-us"; // Set Language
// Version Number -- Do Not Change
$assets_ver = "0001";
$host_url = "https://serverfund.net/dev";

require_once "functions.php";

date_default_timezone_set('America/New_York');

$client_ip = getUserIP();
$date = date('Y-m-d');
$us_date = date_format(date_create_from_format('Y-m-d', $date) , 'm/d/Y');
$time = date('h:i:s', time());
$datetime = $us_date . ' ' . $time;
$doul = $_SERVER['HTTP_HOST'];
$message = '';

$sqltimestamp = date('Y-m-d H:i:s', time());


if (isset($_SESSION['user_id'])) {
	$lp['loggedIn'] = true;
	define("loggedIn", $lp['loggedIn']);
} else {
	$lp['loggedIn'] = false;
	define("loggedIn", $lp['loggedIn']);
}
//if (checkProxy($client_ip)) {
//	die("<strong>ServerFund VPN Detection triggered for IP: $client_ip <br />If this is an error, contact staff@ServerFund.net<br />Please use your real IP to access ServerFund.</strong>");
//}

include "cdm.php";

// $fulldomain = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];