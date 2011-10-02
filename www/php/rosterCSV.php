<?php
session_start();
$authUsers = array('brother');
include_once('authenticate.php');

header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=delts.csv");
header("Pragma: no-cache");
header("Expires: 0");


include_once('login.php');
	
	echo "Given Name,Family Name,Group Membership,E-mail 1 - Value,Mobile Phone\n";
	
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$userData = "
			SELECT * 
			FROM members
			ORDER BY lastName";
		$getUserData = mysqli_query($mysqli, $userData);
		
		while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
		{
			echo $userDataArray['firstName'].",";
			echo $userDataArray['lastName'].",";
			echo "Delts ::: ".ucwords($userDataArray['class']).",";
			echo $userDataArray['email'].",";
			echo $userDataArray['phone']."\n";
		}
?>