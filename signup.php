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
	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;
	
	function signup() {
		
		$htmlOutput = <<<EOL
        <form class="ui container very padded form segment" style="width: 40%; margin-top: 8vh;" method="post" id="signupForm">
            <div class="ui huge header" style="text-align: center;">Create an Account</div>
            <div class="ui info message">
                <div class="header">Your password should</div>
	            <div class="ui bulleted list">
	                <div class="item">be at least 8 characters</div>
	                <div class="item">contain at least 1 letter and number</div>
	                <div class="item">contain at least 1 of the following: @$!%*#?&</div>
	            </div>
			</div><br/>
            <div class="ui form">
				<div class="required field">
                    <label>Name</label>
                    <div class="two fields">
                        <div class="field"><input type="text" name="firstName" placeholder="First Name" required></div>
			            <div class="field"><input type="text" name="lastName" placeholder="Last Name" required></div>
					</div>
				</div>
				<div class="required field">
					<label>Email</label>
					<input type="text" name="email" placeholder="Email" required>
				</div>
				<div style="margin: 20px 0 15px 0" class="ui divider"></div>
				<div class="required field">
					<label>Username</label>
					<input type="text" name="username" placeholder="Username" required>
				</div>
				<div class="required field">
                    <label>Password</label>
                    <div class="two fields">
                        <div class="field"><input type="password" name="password" placeholder="Password" required></div>
			            <div class="field"><input type="password" name="confirmPassword" placeholder="Confirm Password" required></div>
					</div>
				</div><br/>
				<input type="hidden" name="signupSubmit" value="signupSubmit">
                <div class="ui primary right floated submit button">Create</div><br/><br/>
                <div class="ui error message"></div>
			</div>
        </form>
EOL;
		
		$htmlOutput .= <<<EOL
			<script type="text/javascript">
				$('#signupForm').transition('hide').transition('scale');
			    $('#signupForm')
				    .form({
				        on: 'blur',
				        fields: {
				            password: {
				                identifier: 'password',
				                rules: [
/*				                    { type: 'regExp[^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[z]).{8,}$]',
				                            prompt: 'Please enter a password that follows the above rules.' },*/
			                        { type: 'maxLength[255]', prompt: 'Please enter a password of at most 255 characters' },
			                        { type: 'match[password]', prompt: 'Please put the same password in both fields' },
	                            ]
				            },
				            username: {
				                identifier: 'username',
				                rules: [
				                    { type: 'maxLength[20]', prompt: 'Please enter a username of at most 255 characters' },
				                    { type: 'minLength[4]', prompt: 'Please enter a username of at least 4 characters' }
				                ]
				            },
				            email: {
				                identifier: 'email',
				                rules: [{ type: 'email', prompt: 'Please enter a valid email' }]
				            },
				        }
			        });
			</script>
EOL;
		
		echo $htmlOutput;
	}
	
	
	function signupSubmit() {
		
		$mysqli = new mysqli(HOST, USER, PASS, DB);
		if ($mysqli->connect_error) {
			die("Connection error: " . $mysqli->connect_error);
		}
		
		$statement = $mysqli->prepare(
				"INSERT INTO USERS (date, firstName, lastName, email, username, password, activation)
				VALUES (?, ?, ?, ?, ?, ?, ?)");
		$statement->bind_param("sssssss", $date, $firstName, $lastName, $email, $username, $password, $activation);
		
		$date = time();
		$firstName = $_POST["firstName"];
		$lastName = $_POST["lastName"];
		$email = $_POST["email"];
		$username = $_POST["username"];
		$password = password_hash($_POST["password"], PASSWORD_DEFAULT);
		$activation = hash("md5", "$date $email");
		
		if ($statement->execute()) {
			$mail = new PHPMailer(true);
			try {
				//Server settings
				$mail->SMTPDebug = 0;                                       // Enable verbose debug output
				$mail->isSMTP();                                            // Send using SMTP
				$mail->Host = '';                                           // Set the SMTP server to send through
				$mail->SMTPAuth = true;                                     // Enable SMTP authentication
				$mail->Username = '';                                       // SMTP username
				$mail->Password = '';                                       // SMTP password
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
				$mail->Port = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
				
				//Recipients
				$mail->setFrom('', '');
				$mail->addAddress($email, "$firstName $lastName");     // Add a recipient
				
				// Content
				$mail->isHTML(true);                                  // Set email format to HTML
				$mail->Subject = 'Activate your Task Manager account';
				$mail->Body = "
				Hi $firstName $lastName,<br/><br/>
				Please click on the following link to activate your account: $activation<br/><br/>
				Thanks for your interest in using Task Manager!<br/><br/><br/>
				Sincerely,<br/>
				Christina C.<br/>
			";
				$mail->AltBody = "
				Hi $firstName $lastName,\n\n
				Please click on the following link to activate your account: $activation\n\n
				Thanks for your interest in using Task Manager!\n\n\n
				Sincerely,\n\n
				.\n
			";
				
				$mail->send();
			} catch (Exception $e) {
				echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
			}
			
			echo <<<EOL
		<div style="position: relative">
			<div class="ui compact success message" style="position: absolute; top: 20vh; left: 0; right: 0">
				<div class="header">Thanks for creating an account with us!</div>
				<p>An email for activating your account has been sent to $email.</p>
			</div>
		</div>
EOL;
		} else {
			echo $mysqli->error;
		}

		
	}
	
	
?>





</body>
</html>




