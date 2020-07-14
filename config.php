<?php

	define("HOST", "");
	define("USER", "");
	define("PASS", "");
	define("DB", "");
	
	if (isset($_SESSION["lastActivity"]) && time() - $_SESSION["lastActivity"] >= 3600) {
		session_unset();
		session_destroy();
		// and then redirect to some log out page
	} else {
		$_SESSION["lastActivity"] = time();
	}
