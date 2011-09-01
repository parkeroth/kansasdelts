<?php
	require 'php/class.phpmailer.php';
	
	try {
		$mail = new PHPMailer(true); //New instance, with exceptions enabled
	
		$mail->IsSMTP();                           			// tell the class to use SMTP
		$mail->SMTPAuth   = true;                  			// enable SMTP authentication
		$mail->Port       = 25;                    			// set the SMTP server port
		$mail->Host       = "smtp.gmail.com"; 				// SMTP server
		$mail->Username   = "dtdadmin@kansasdelts.org";     // SMTP server username
		$mail->Password   = "DTD1856GammaTau";           	// SMTP server password
	
		$mail->IsSendmail();  								// tell the class to use Sendmail
	
		$mail->From       = "mail@kansasdelts.org";
		
		$mail->AddAddress('parkeroth@gmail.com', 'Parker Roth');
		
		$mail->Subject  = $subject;
	
		$mail->WordWrap   = 80; // set word wrap
	
		$mail->MsgHTML('<p>Test</p>');
	
		$mail->IsHTML(true); // send as HTML
	
		
		
		$mail->Send();
	} catch (phpmailerException $e) {
		echo $e->errorMessage();
	}
	
?>