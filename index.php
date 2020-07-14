<!DOCTYPE html>
<html lang="en">
<head>
	<title>Task Manager</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.13/semantic.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.13/semantic.min.js"></script>
	<link rel="stylesheet" href="style/stylesheet.css">
</head>

<body>



<?php
	
	require_once "navbars.php";
	require_once "config.php";
	
	require_once 'PHPMailer/src/Exception.php';
	require_once 'PHPMailer/src/PHPMailer.php';
	require_once 'PHPMailer/src/SMTP.php';
	
	if (!empty($_GET["page"])) {
		if ($_GET["page"] == "projectBoard") {
			generalNavbar('user');
			projectNavbar("Project Name");
			require_once "projectBoard.php";
			return;
		} else if ($_GET["page"] == "chatRoom") {
			generalNavbar('user');
			projectNavbar("Project Name");
			require_once "projectChat.php";
			return;
		} else if ($_GET["page"] == "login") {
			generalNavbar('visitor');
			require_once "login.php";
			if (empty($_POST["loginSubmit"])) {
				login(null);
			} else {
				loginSubmit();
			}
			return;
		} else if ($_GET["page"] == "logout") {
			generalNavbar('visitor');
			require_once "logout.php";
			return;
		} else if ($_GET["page"] == "signup") {
			generalNavbar("visitor");
			require_once "signup.php";
			if (empty($_POST["signupSubmit"])) {
				signup();
			} else {
				signupSubmit();
			}
			return;
		} else if ($_GET["page"] == "todo") {
			generalNavbar('user');
			require_once "todo.php";
		}
	} else {
		generalNavbar("visitor");
		echo "welcome";
	}

?>




</body>
</html>




