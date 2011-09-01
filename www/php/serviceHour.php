<?php
session_start();
$authUsers = array('admin','communityService');
include_once('authenticate.php');
include_once('login.php');

	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	$userData = "
		SELECT * 
		FROM members";
	
	$getUserData = mysqli_query($mysqli, $userData);

	while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC)){
		
		$add_sql = "UPDATE members
			SET serviceHours='".$_POST[$userDataArray['username']]."'
			WHERE username='".$userDataArray['username']."'";
		
		$add_res = mysqli_query($mysqli, $add_sql);
	
	}
header("location: ../account.php");
?>