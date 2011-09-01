<?php
session_start();
$authUsers = array('brother');
include_once('authenticate.php');
	
include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$userData = "
	SELECT email, firstName, lastName 
	FROM members
	WHERE accountType like '%|$_POST[to]%'";
$getUserData = mysqli_query($mysqli, $userData);
	
while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
{
	$to = $userDataArray[email];
	$toName = $userDataArray[firstName]." ".$userDataArray[lastName];
}

$senderData = "
	SELECT firstName, lastName 
	FROM members
	WHERE username = '$_SESSION[username]'";
$getSenderData = mysqli_query($mysqli, $senderData);
	
while($senderDataArray = mysqli_fetch_array($getSenderData, MYSQLI_ASSOC))
{
	$from = $senderDataArray[firstName]." ".$senderDataArray[lastName];
}

	require '../php/class.phpmailer.php';

	try {
		$mail = new PHPMailer(true); //New instance, with exceptions enabled
	
		$body =  "<html><head></head><body>";
		$body .= "<table>";
		$body .= "<tr><th>From:</th><td>$from</td></tr>";
		$body .= "<tr><th>Title:</th><td>$_POST[title]</td></tr>";
		$body .= "<tr><th>Details:</th><td>$_POST[details]</td></tr>";
		$body .= "</table>";
		
			
		$mail->IsSMTP();                           // tell the class to use SMTP
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->Port       = 25;                    // set the SMTP server port
		$mail->Host       = "smtp.gmail.com"; // SMTP server
		$mail->Username   = "dtdadmin@kansasdelts.org";     // SMTP server username
		$mail->Password   = "DTD1856GammaTau";            // SMTP server password
	
		$mail->IsSendmail();  // tell the class to use Sendmail
	
		$mail->AddReplyTo("dtdadmin@kansasdelts.org","DTD Webmaster");
	
		$mail->From       = "dtdadmin@kansasdelts.org";
		$mail->FromName   = "DTD Webmaster";
		
		
		$mail->AddAddress($to, $toName);
		
		$mail->Subject  = "New Idea For Your Position";
		
		$mail->WordWrap   = 80; // set word wrap
	
		$mail->MsgHTML($body);
	
		$mail->IsHTML(true); // send as HTML
	
		$mail->Send();
	} catch (phpmailerException $e) {
		echo $e->errorMessage();
	}

header("location: ../account.php");

?>