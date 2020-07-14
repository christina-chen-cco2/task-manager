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
	
	function login($errorMessage) {
		if (!empty($errorMessage)) {
			$errorMessage = "<div class='ui negative message'>$errorMessage</div>";
		}
		$htmlOutput = <<<EOL
            <form class="ui container very padded form segment" style="width: 40%; margin-top: 15vh;" id="loginForm" method="post">
                <div class="ui huge header" style="text-align: center;">Welcome!</div>
                $errorMessage
                <br/>
                <div class="ui form">
                    <div class="field">
                        <label>Username</label>
                        <input type="text" name="username" placeholder="Username" required>
                    </div>
                    <div class="field">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Password" required>
					</div>
				</div>
				<br/>
				<table>
					<tr>
						<td><a href="">Forgot Password?</a></td>
						<td rowspan="2" style="text-align: right">
							<input type="hidden" name="loginSubmit" value="loginSubmit">
							<button type="submit" class="ui submit button">Login</button>
						</td>
					</tr>
					<tr><td><a href="signup.php">Create Account</a></td></tr>
				</table>
            </form>

	<script type="text/javascript">
		$('#loginForm').transition('hide').transition('scale');
	</script>
EOL;
		echo $htmlOutput;
		
	}
	
	function loginSubmit() {
		
		$mysqli = new mysqli(HOST, USER, PASS, DB);
		if ($mysqli->connect_error) {
			die("Connection error: " . $mysqli->connect_error);
		}
		
		$statement = $mysqli->prepare("SELECT username, password, activated FROM USERS WHERE username=?");
		$statement->bind_param("s", $username);
		
		$username = $_POST["username"];

		if ($statement->execute()) {
			$statement->store_result();
			if ($statement->num_rows() > 0) {
				$statement->bind_result($resultUsername, $resultPassword, $resultActivated);
				$statement->fetch();
				$password = $_POST["password"];
				if (password_verify($password, $resultPassword) && $resultActivated) {
					session_start();
					$_SESSION["username"] = $username;
					$_SESSION["password"] = $password;
					$_SESSION["lastActivity"] = time();
					header("Location: /task-manager/index.php?page=projectHome");
				} else if (password_verify($password, $resultPassword) && !$resultActivated) {
					login("Please activate your account. An activation link has been previously sent to the email you registered with.");
				} else if (!password_verify($password, $resultPassword)) {
					login("Either the username or password entered is incorrect.");
				}
			} else {
				login("Either the username or password entered is incorrect.");
			}
		} else {
			echo $mysqli->error;
		}
	}
	
	

?>
</body>
</html>




