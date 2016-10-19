<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Bootstrap contact form with Recaptcha support</title>

		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

		<!-- Recaptcha api -->
		<script src='https://www.google.com/recaptcha/api.js'></script>
    
  </head>
  <body>
 
		<!-- Mail script, provided by http://www.html-form-guide.com/email-form/php-form-to-email.html. Slightly adjusted in order to implement Recaptcha -->
		<?php
		
			$name = $_POST['name'];
			$visitor_email = $_POST['email'];
			$message = $_POST['message'];
			$captcha = $_POST['g-recaptcha-response'];

			if(!$captcha){
				echo "<div class='alert alert-danger' role='alert'>Please verify that you are not a robot. <br /><< <a href='contact.html'>Back</a></div>";
				exit;
			}
			$secretKey = " "; // Your Recaptcha secret site key
			$ip = $_SERVER['REMOTE_ADDR'];
			$response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha);
			$responseKeys = json_decode($response,true);

			// Validate first
			if(empty($name)||empty($visitor_email)) 
			{
				echo "<div class='alert alert-danger' role='alert'>Please fill in all necessary fields. <br /><< <a href='contact.html'>Back</a></div>";
				exit;
			}

			if(IsInjected($visitor_email))
			{
				echo "<div class='alert alert-danger' role='alert'>Please fill in your e-mail address. <br /><< <a href='contact.php'>Back</a></div>";
				exit;
			}

			$email_from = " "; // Your e-mail address
			$email_subject = "My Form Subject";
			$email_body = "This message has been sent by $name.\n\n".
			"$message";
				
			$to = " "; // Your e-mail address
			$headers = "From: $email_from \r\n";
			$headers = "Reply-To: $visitor_email \r\n";

			// Check captcha
			if(intval($responseKeys["success"]) !== 1) {
				echo "<div class='alert alert-danger' role='alert'>Er ging iets mis met de captcha.</div>";
				exit;
			} else {
				// Send the email
				mail($to,$email_subject,$email_body,$headers);

				// Thank mailer
				echo "<div class='alert alert-success' role='alert'>Thank you for sending your message.</div>";
				exit;
			}

			// Function to validate against any email injection attempts
			function IsInjected($str)
			{
				$injections = array('(\n+)',
							'(\r+)',
							'(\t+)',
							'(%0A+)',
							'(%0D+)',
							'(%08+)',
							'(%09+)'
							);
				$inject = join('|', $injections);
				$inject = "/$inject/i";
				if(preg_match($inject,$str))
				{
				return true;
				}
				else
				{
				return false;
				}
			}
			
		?>

  </body>
</html>