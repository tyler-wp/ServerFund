<?php
	session_name('serverfund');
	session_start();

	session_unset();
	session_destroy();

	if (isset($_GET['rm']) && $_GET['rm'] === 'settings-updated') {
		header('Location: login');
		exit();
	} else {
		header('Location: login');
		exit();
	}
?>