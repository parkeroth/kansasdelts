<?php
session_start();
$authUsers = array('admin', 'secretary');
include_once('authenticate.php');
	
include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	$date = date("Y-m-d");
	
	$userData = "
		SELECT * 
		FROM members";
	
	$getUserData = mysqli_query($mysqli, $userData);

	while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC)){
		
		if($_POST[$userDataArray[username]])
		{
			
			$modify = "DELETE FROM positionLog
						WHERE username='$userDataArray[username]'";
			$doModification = mysqli_query($mysqli, $modify);
			
			$modify = "DELETE FROM classes
						WHERE username='$userDataArray[username]'";
			$doModification = mysqli_query($mysqli, $modify);
			
			$modify = "DELETE FROM grades
						WHERE username='$userDataArray[username]'";
			$doModification = mysqli_query($mysqli, $modify);
			
			$modify = "DELETE FROM hourLog
						WHERE username='$userDataArray[username]'";
			$doModification = mysqli_query($mysqli, $modify);
			
			$modify = "DELETE FROM writeUps
						WHERE partyResponsible='$userDataArray[username]'";
			$doModification = mysqli_query($mysqli, $modify);
			
			$modify = "DELETE FROM members
						WHERE username='$userDataArray[username]'";
			$doModification = mysqli_query($mysqli, $modify);
			
		}
	
	}

header("location: ../account.php");

?>