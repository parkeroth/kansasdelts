<?
session_start();
$authUsers = array('brother');
include_once('php/authenticate.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Change Notify Form</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="images/cal.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="js/gen_validatorv31.js" type="text/javascript"></script>
<SCRIPT LANGUAGE="JavaScript"></script>


</head>
<body onLoad="Check('type');">
<?php
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	$userData = "
		SELECT * 
		FROM members 
		WHERE username='".$_SESSION['username']."'";

	$getUserData = mysqli_query($mysqli, $userData);

	$userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC);
	
?>
<form id="form" name="form" method="post" action="php/changeNotify.php" onSubmit="window.close()">
	
    <table style="text-align:center;">
   		<tr style="font-weight: bold;">
        	<td>&nbsp;</td>
			<td>Email</td>
			<td>Text</td>
        </tr>
		<tr>
			<td>New Events</td>
			<td><input type="checkbox" name="newEvent[]" value="email" 
			<?php
				if(strpos($userDataArray[notifyNewEvent], "mail") != NULL){
					echo "checked";
				}
			?>
			 /></td>
			<td><input type="checkbox" name="newEvent[]" value="text" 
			<?php
				if(strpos($userDataArray[notifyNewEvent], "text") != NULL){
					echo "checked";
				}
			?>
			/></td>
		</tr>
		<tr>
			<td>Reminders</td>
			<td><input type="checkbox" name="reminder[]" value="email" 
			<?php
				if(strpos($userDataArray[notifyReminder], "mail") != NULL){
					echo "checked";
				}
			?>
			/></td>
			<td><input type="checkbox" name="reminder[]" value="text" 
			<?php
				if(strpos($userDataArray[notifyReminder], "text") != NULL){
					echo "checked";
				}
			?>
			/></td>
		</tr>
    </table>

  <p>
      <input type="submit" name="submit" id="submit" value="Submit" />
  </p>
</form>
</body>
</html>