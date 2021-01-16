<?php
//Client Displayed Messages
if($_SESSION["errormsg"] != NULL) {
	$message = '<div class="alert dark alert-'.$_SESSION["errortype"].'" role="alert">'.$_SESSION["errormsg"].'</div>';
	$_SESSION["errormsg"] = NULL;
}


if (isset($_GET['cdm']) && $_GET['cdm'] === '1') {
	//Email Taken
	$message = '<div class="alert dark alert-danger" role="alert">That email is already taken.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '2') {
	//Registration Complete
	$message = '<div class="alert dark alert-success" role="alert">Registration complete! You can now login.</div>';
} elseif (isset($_SESSION["errormsg"])) {
	//Account not found
	$display_error = $_SESSION["errormsg"];
	echo $display_error;
    session_unset($_SESSION["errormsg"]);
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '4') {
	//Invalid Password
	$message = '<div class="alert dark alert-danger" role="alert">Invalid Password.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '5') {
	//Community Abbreviation Taken
	$message = '<div class="alert dark alert-danger" role="alert">Please use a different Community Abbreviation</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '6') {
	//Global Ban Message
	$message = '<div class="alert dark alert-danger" role="alert">Sorry, this account has been globally banned from ServerFund. This process can not be reversed.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '7') {
	//Panel Limit Message
	$message = '<div class="alert dark alert-info" role="alert">Your Membership only allows you to have one Panel created. If you require more panels, Please upgrade your account.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '8') {
	//Panel Limit Message
	$message = '<div class="alert dark alert-success" role="alert">Panel Created!</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '9') {
	//Panel Settings Updated
	$message = '<div class="alert dark alert-success" role="alert">Your panel settings have been updated! It may take up to 1 hour for changes to be seen for all users.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '10') {
	//Ticket Created Message
	$message = '<div class="alert dark alert-success" role="alert">New Ticket Created. Please allow time for Staff to respond. Attempting to bump your ticket will result in no support.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '11') {
	//Ticket Not Found Message
	$message = '<div class="alert dark alert-danger" role="alert">We couldn\'t find that ticket.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '12') {
	//Ticket Reply Message
	$message = '<div class="alert dark alert-success" role="alert">Reply Added.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '13') {
	//Ticket Reply Message
	$message = '<div class="alert dark alert-info" role="alert">Ticket Closed.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '14') {
	//Payment Canceled Message
	$message = '<div class="alert dark alert-danger" role="alert">You have canceled your payment.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '15') {
	//Account Needs to be verified Message
	$message = '<div class="alert dark alert-danger" role="alert">Sorry, this action requires your account being verified. Please check your email for instructions.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '16') {
	//Account Needs to be verified Message
	$message = '<div class="alert dark alert-success" role="alert">User Settings updated! Please login again.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '17') {
	//Passwords not matched
	$message = '<div class="alert dark alert-danger" role="alert">Your New Passwords did not match.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '18') {
	//Only Letters for name
	$message = '<div class="alert dark alert-danger" role="alert">Please only use A-Z for your name.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '19') {
	//Email Verified
	$message = '<div class="alert dark alert-success" role="alert">Your email has now been verified!</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '20') {
	//invalid URL Link
	$message = '<div class="alert dark alert-danger" role="alert">That is not a URL.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '21') {
	//Package Deleted
	$message = '<div class="alert dark alert-success" role="alert">Package Deleted.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '22') {
	//Package Hidden
	$message = '<div class="alert dark alert-success" role="alert">Package Hidden.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '23') {
	//Package Hidden
	$message = '<div class="alert dark alert-success" role="alert">Package Unhidden.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '24') {
	//Package Created
	$message = '<div class="alert dark alert-success" role="alert">Package Created!</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '25') {
	//Price Error
	$message = '<div class="alert dark alert-danger" role="alert">You can only use numbers and periods in your pricing!</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '26') {
	//Premium Settings Only
	$message = '<div class="alert dark alert-danger" role="alert">Oops! This is for premium users only</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '27') {
	//Package Edited
	$message = '<div class="alert dark alert-success" role="alert">Package Edited!</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '28') {
	//Wrong Characters in Community Abrv.
	$message = '<div class="alert dark alert-danger" role="alert">Please only use A-Z and 0-9 in your community abbreviation!</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '29') {
	//Forgot password
	$message = '<div class="alert dark alert-success" role="alert">We have sent an email with your reset token. This token will expire within 15 minutes.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '30') {
	//Bad token request
	$message = '<div class="alert dark alert-danger" role="alert">Sorry, the token provided is not valid. It also may have expired.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '31') {
	//Password no match
	$message = '<div class="alert dark alert-danger" role="alert">Your passwords do not match.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '32') {
	//Password no match
	$message = '<div class="alert dark alert-info" role="alert">Your password has been reset, You can now login.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '33') {
	//Password no match
	$message = '<div class="alert dark alert-success" role="alert">Package Confirmed.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '34') {
	$message = '<div class="alert dark alert-danger" role="alert">You can only use 1-9. Please do not include %</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '35') {
	$message = '<div class="alert dark alert-danger" role="alert">You did not include a package number</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '36') {
	$message = '<div class="alert dark alert-danger" role="alert">You do not have permissions for this package.</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '37') {
	$message = '<div class="alert dark alert-danger" role="alert">This package is already on sale!</div>';
} elseif (isset($_GET['cdm']) && $_GET['cdm'] === '38') {
	$message = '<div class="alert dark alert-success" role="alert">Sale Started!</div>';
}