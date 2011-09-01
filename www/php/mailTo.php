<?php
	
	function mailTo($to, $from, $body, $subject) {
		require 'class.phpmailer.php';
	
		try {
			$mail = new PHPMailer(true); //New instance, with exceptions enabled
		
			$mail->IsSMTP();                           			// tell the class to use SMTP
			$mail->SMTPAuth   = true;                  			// enable SMTP authentication
			$mail->Port       = 25;                    			// set the SMTP server port
			$mail->Host       = "smtp.gmail.com"; 				// SMTP server
			$mail->Username   = "dtdadmin@kansasdelts.org";     // SMTP server username
			$mail->Password   = "DTD1856GammaTau";           	// SMTP server password
		
			$mail->IsSendmail();  								// tell the class to use Sendmail
		
			$mail->From       = $from;
			$mail->AddAddress($to, 'Webmaster');
			
			$mail->Subject  = $subject;
		
			$mail->WordWrap   = 80; // set word wrap
		
			$mail->MsgHTML($body);
		
			$mail->IsHTML(true); // send as HTML
		
			$mail->Send();
		} catch (phpmailerException $e) {
			echo $e->errorMessage();
		}
	}
	
	/**
	*
	* Takes array of position and emails message to them
	*
	*/
	function mailPosition($position, $from, $body, $subject) {
		require_once('login.php');
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$to="";
		
		for($i=0; $i < count($position); $i++) {
			$toQuery = "SELECT email FROM members where accountType LIKE '%|".$position[$i]."%' LIMIT 1";
			$getTo = mysqli_query($mysqli, $toQuery);
			$toArray = mysqli_fetch_array($getTo, MYSQLI_ASSOC);
			
			$to .= $toArray['email'].", ";
		}
		
		mailTo($to, $from, $body, $subject);
	}
	
?>