<?php
	session_start();
	$authUsers = array('brother');
	include_once('authenticate.php');
	include_once('login.php');

	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	$query = "SELECT * FROM members WHERE username = '".$_SESSION['username']."' AND password = SHA('".$_POST['oldPass']."')";
	$result = mysqli_query($mysqli, $query);
	
	if (mysqli_fetch_row($result)){
		$modify = "UPDATE members
					SET password = SHA('".$_POST['newPass']."')
					WHERE username = '".$_SESSION['username']."'";
		$doModification = mysqli_query($mysqli, $modify);
		header("location: ../account.php");
	} else {
		header("location: ../passwordChangeForm.php?error=1");
	}
?>