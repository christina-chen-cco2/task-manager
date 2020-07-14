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
	session_unset();
	session_destroy();
?>

<div class="ui message">
	<div class="header">You are successfully logged out.</div>
</div>


</body>
</html>