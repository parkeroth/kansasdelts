<?php
	session_start();
	$authUsers = array('brother');
	include_once('authenticate.php');
	include_once('login.php');

	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	$removeQuery = "DELETE FROM accomplishments WHERE username = '$_SESSION[username]'";
	$doRemove = mysqli_query($mysqli, $removeQuery);
	
	$types = "
		SELECT * 
		FROM accomplishmentTypes";
	$getTypes = mysqli_query($mysqli, $types);
	
	while($typeArray = mysqli_fetch_array($getTypes, MYSQLI_ASSOC))
	{
		if($_POST[$typeArray[type]] == TRUE) {
			$insertQuery = "INSERT INTO accomplishments (username, type) VALUES ('$_SESSION[username]', '$typeArray[type]')";
			$doInsert = mysqli_query($mysqli, $insertQuery);
		}
	}
	
	header("location: ../accomplishmentForm.php");
?>