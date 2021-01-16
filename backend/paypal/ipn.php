<?php
include '../inc/connect.php';
include '../inc/config.php';
// reading posted data from directly from $_POST causes serialization 
// issues with array data in POST
// reading raw POST data from input stream instead. 
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
  $keyval = explode ('=', $keyval);
  if (count($keyval) == 2)
     $myPost[$keyval[0]] = urldecode($keyval[1]);
}
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
if(function_exists('get_magic_quotes_gpc')) {
   $get_magic_quotes_exists = true;
} 
foreach ($myPost as $key => $value) {        
   if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) { 
        $value = urlencode(stripslashes($value)); 
   } else {
        $value = urlencode($value);
   }
   $req .= "&$key=$value";
}


// STEP 2: Post IPN data back to paypal to validate

$ch = curl_init('https://www.paypal.com/cgi-bin/webscr');
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

// In wamp like environments that do not come bundled with root authority certificates,
// please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path 
// of the certificate as shown below.
// curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
if( !($res = curl_exec($ch)) ) {
    // error_log("Got " . curl_error($ch) . " when processing IPN data");
    curl_close($ch);
    exit;
}
curl_close($ch);


// STEP 3: Inspect IPN validation result and act accordingly

if (strcmp ($res, "VERIFIED") == 0) {
   // check whether the payment_status is Completed
   // check that txn_id has not been previously processed
   // check that receiver_email is your Primary PayPal email
   // check that payment_amount/payment_currency are correct
   // process payment

   // assign posted variables to local variables
   $item_number = $_POST['item_number'];
   $txn_id = $_POST['txn_id'];
   $payment_gross = $_POST['mc_gross'];
   $currency_code = $_POST['mc_currency'];
   $payment_status = $_POST['payment_status'];
   $payer_email = $_POST['payer_email'];

   $cv = explode(',',$_POST['custom']);


   //Check if payment data exists with the same TXN ID.
   $sql2       = "SELECT COUNT(txn_id) AS num FROM payments WHERE txn_id = :txn_id";
   $stmt2      = $pdo->prepare($sql2);
   $stmt2->bindValue(':txn_id', $txn_id);
   $stmt2->execute();
   $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
   if ($row2['num'] > 0) {
      exit();
   } else {
      $sql3 = "INSERT INTO payments (
         txn_id, 
         payment_gross, 
         currency_code, 
         payment_status, 
         payer_email, 
         package_id, 
         buyer_uid,
         cid,
         ip) VALUES (
            :txn_id, 
            :payment_gross, 
            :currency_code, 
            :payment_status, 
            :payer_email, 
            :package_id, 
            :buyer_uid,
            :cid,
            :ip)";
      $stmt3 = $pdo->prepare($sql3);
      $stmt3->bindValue(':txn_id', $txn_id);
      $stmt3->bindValue(':payment_gross', $payment_gross);
      $stmt3->bindValue(':currency_code', $currency_code);
      $stmt3->bindValue(':payment_status', $payment_status);
      $stmt3->bindValue(':payer_email', $payer_email);
      $stmt3->bindValue(':package_id', $item_number);
      $stmt3->bindValue(':buyer_uid', $cv[0]);
      $stmt3->bindValue(':cid', $cv[1]);
      $stmt3->bindValue(':ip', $cv[2]);
      $insertPayment = $stmt3->execute();
   }
} else if (strcmp ($res, "INVALID") == 0) {
    $sql1 = "INSERT INTO payments (
       txn_id, 
       payment_gross, 
       currency_code, 
       payment_status, 
       payer_email, 
       package_id, 
       fruad,
       buyer_uid,
       cid,
       ip) VALUES (
          :txn_id, 
          :payment_gross, 
          :currency_code, 
          :payment_status, 
          :payer_email, 
          :package_id, 
          :fruad,
          :buyer_uid,
          :cid,
          :ip)";
    $stmt1 = $pdo->prepare($sql1);
    $stmt1->bindValue(':txn_id', $txn_id);
    $stmt1->bindValue(':payment_gross', $payment_gross);
    $stmt1->bindValue(':currency_code', $currency_code);
    $stmt1->bindValue(':payment_status', 'Manual Review Required');
    $stmt1->bindValue(':payer_email', $payer_email);
    $stmt1->bindValue(':package_id', $item_number);
    $stmt1->bindValue(':fruad', 'true');
    $stmt1->bindValue(':buyer_uid', $cv[0]);
    $stmt1->bindValue(':cid', $cv[1]);
    $stmt1->bindValue(':ip', $cv[2]);
    $stmt1->execute();
}
