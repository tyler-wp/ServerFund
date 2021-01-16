<?php
// MySQL Injection Prevention
function escapestring($value)
{
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	if ($conn->connect_errno) {
		die('Could not connect: ' . $conn->connect_error);
	}
	return strip_tags(mysqli_real_escape_string($conn, $value));
}
// Insert into Database
function dbquery($sql, $returnresult = true)
{
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	if ($conn->connect_errno) {
		error_log('MySQL could not connect: ' . $conn->connect_error);
		return $conn->connect_error;
	}
	$return = array();
	$result = mysqli_query($conn, $sql);
	if ($returnresult) {
		if (mysqli_num_rows($result) != 0) {
			while ($r = $result->fetch_assoc()) {
				array_push($return, $r);
			}
		} else {
			$return = array();
		}
	} else {
		$return = array();
	}
	return $return;
}

// Throw Visual Error (Only works after Header is loaded)
function throwError($error, $log = false) {
	// Load Toastr JavaScript and CSS
	echo '
		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
		<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
		<script type="text/javascript">
			if(window.toastr != undefined) {
				if (typeof jQuery == "undefined") {
					alert("Error Handler: ' . $error . '")
				} else {
					toastr.error("' . $error . '")
				}
			} else {
				alert("Error Handler: ' . $error . '")
			}
		</script>
	';
}

// Throw Notification (Only works after Header is loaded)
function clientNotify($type, $error) {
	echo '
		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
		<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
		<script type="text/javascript">
			if(window.toastr != undefined) {
				if (typeof jQuery == "undefined") {
					alert("System: ' . $error . '")
				} else {
					toastr.' . $type . '("' . $error . '")
				}
			} else {
				alert("System: ' . $error . '")
			}
		</script>
	';
}

function ticketWebhook($message) {
	//=======================================================================
	// Create new webhook in your Discord channel settings and copy&paste URL
	//=======================================================================
	$webhookurl = "https://discordapp.com/api/webhooks/593101255526711306/gGK6VjT1g4lddsKc8qoJBZieigFXoSRMYx8ILGeLluGNXFV_h5_Y5ws0V15LMdHISqEr";
	//=======================================================================
	// Compose message. You can use Markdown
	//=======================================================================
	$json_data = array(
		'content' => "$message"
	);
	$make_json = json_encode($json_data);
	$ch = curl_init($webhookurl);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $make_json);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec($ch);

	return $response;
}

// Log Function
function sf_log($action, $uid, $ip) {
	global $pdo;
	$sql_log = "INSERT INTO logs (action, uid, ip) VALUES (:action, :uid, :ip)";
	$stmt_log = $pdo->prepare($sql_log);
	$stmt_log->bindValue(':action', $action);
	$stmt_log->bindValue(':uid', $uid);
	$stmt_log->bindValue(':ip', $ip);
	$result_log = $stmt_log->execute();
}

function truncate_string($string, $maxlength, $extension) {

	// Set the replacement for the "string break" in the wordwrap function
	$cutmarker = "**cut_here**";

	// Checking if the given string is longer than $maxlength
	if (strlen($string) > $maxlength) {

		// Using wordwrap() to set the cutmarker
		// NOTE: wordwrap (PHP 4 >= 4.0.2, PHP 5)
		$string = wordwrap($string, $maxlength, $cutmarker);

		// Exploding the string at the cutmarker, set by wordwrap()
		$string = explode($cutmarker, $string);

		// Adding $extension to the first value of the array $string, returned by explode()
		$string = $string[0] . $extension;
	}

	// returning $string
	return $string;

}

function str_replacer($filename, $string_to_replace, $replace_with){
	$content=file_get_contents($filename);
	$content_chunks=explode($string_to_replace, $content);
	$content=implode($replace_with, $content_chunks);
	file_put_contents($filename, $content);
}

function phpAlert($msg) {
	echo '<script type="text/javascript">alert("' . $msg . '")</script>';
}

function time_php2sql($unixtime){
	return gmdate("Y-m-d H:i:s", $unixtime);
}

function sf_email ($to, $subject, $message) {
	$to      = $to;
	$subject = $subject;
	$message = $message;
	$headers = 'From: no-reply@serverfund.net' . "\r\n" .
		'Reply-To: no-reply@serverfund.net' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();

	mail($to, $subject, $message, $headers);
}

function sf_salestax($price) {
	$sales_tax = '0.0475';
	return $price + ($price * $sales_tax);
}

function getUserIP()
{
    if( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')>0) {
            $addr = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($addr[0]);
        } else {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    }
    else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function checkProxy($ip){
	$contactEmail="staff@serverfund.net"; //you must change this to your own email address
	$timeout=5; //by default, wait no longer than 5 secs for a response
	$banOnProbability=0.99; //if getIPIntel returns a value higher than this, function returns true, set to 0.99 by default
	
	//init and set cURL options
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	//if you're using custom flags (like flags=m), change the URL below
	curl_setopt($ch, CURLOPT_URL, "http://check.getipintel.net/check.php?ip=$ip&contact=$contactEmail");
	$response=curl_exec($ch);
	
	curl_close($ch);
	
	
	if ($response > $banOnProbability) {
			return true;
	} else {
		if ($response < 0 || strcmp($response, "") == 0 ) {
			echo "Fatal System Error (NETCHK)";
		}
			return false;
	}
}

function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}