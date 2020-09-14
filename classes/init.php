<?php

	session_start();

    require_once("db.php");

	// Create and open global database instance
	$db = new Database();
	$db->open();

	// redirects to specific path
	function redirect($path) {
		ob_clean();
		header("Location: $path");

		exit;
	}

	// gets specific session data
	function session($id) {
		if (!isset($_SESSION[$id])) {
			return null;
		}
		
		return trim(strval($_SESSION[$id]));
	}
	
	// gets specific get data
	function get($id) {
		if (!isset($_GET[$id])) {
			return null;
		}
		
		return trim(strval($_GET[$id]));
	}

	// gets specific post data
	function post($id) {
		if (!isset($_POST[$id])) {
			return null;
		}
		
		return trim(strval($_POST[$id]));
	}

    // include all db classes
	require_once("user.php");
	require_once("schedule.php");
	require_once("weekday.php");
	require_once("appointment.php");
	require_once("status.php");
	require_once("review.php");

?>