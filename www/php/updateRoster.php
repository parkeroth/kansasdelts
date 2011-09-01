<?php
	session_start();
	$authUsers = array('brother');
	include_once('authenticate.php');
	include_once('login.php');

	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	$query = "SELECT * FROM members WHERE username = '".$_SESSION['username']."'";
	$result = mysqli_query($mysqli, $query);
	
	if (mysqli_fetch_row($result)){
		$modify = "UPDATE members
					SET email='".$_POST['email']."', 
						phone='".$_POST['phoneNumber']."', 
						carrier='".$_POST['carrier']."', 
						major='".$_POST['major']."', 
						gradYear='".$_POST['gradYear']."', 
						homeTown='".$_POST['homeTown']."', 
						shirtSize='".$_POST['shirtSize']."', 
						parentName='".$_POST['parentName']."',
						parentAddress='".$_POST['parentAddress']."',
						parentEmail='".$_POST['parentEmail']."',
						school='".$_POST['school']."'
					WHERE username = '".$_SESSION['username']."'";
		$doModification = mysqli_query($mysqli, $modify);
		header("location: ../memberInfoForm.php");
	}
?>