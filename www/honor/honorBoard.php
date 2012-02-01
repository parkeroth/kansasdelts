<?php
	session_start();
	$authUsers = array('admin', 'saa');
	include_once('authenticate.php');
	include_once('login.php');

	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	$userData = "
		SELECT * 
		FROM members
		ORDER BY lastName";
	$getUserData = mysqli_query($mysqli, $userData);
	
	$memberCount = 0;
	while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
	{
		$members[$memberCount]['username'] = $userDataArray['username'];
		$members[$memberCount]['firstName'] = $userDataArray['firstName'];
		$members[$memberCount]['lastName'] = $userDataArray['lastName'];
		$members[$memberCount]['accountType'] = $userDataArray['accountType'];
		$members[$memberCount]['class'] = $userDataArray['class'];
		$members[$memberCount]['major'] = $userDataArray['major'];
		$memberCount++;
	}
	
	
	
	for($i = 0; $i < $memberCount; $i++) // Remove all people from honorboard
	{	
		
		if( strpos($members[$i]['accountType'], "honorBoard") ){
			
			$str = substr_replace($members[$i]['accountType'], '', strpos($members[$i]['accountType'], '|honorBoard'), 11);
			
			$modify = "UPDATE members
				SET accountType = '$str'
				WHERE username = '".$members[$i]['username']."'";
			$doModification = mysqli_query($mysqli, $modify);
			
			$members[$i]['accountType'] = $str;
			
		}
		
		if( isset($_POST[$members[$i]['username']]) ){
			
			$str = $members[$i]['accountType']."|honorBoard";
			
			$modify = "UPDATE members
				SET accountType = '$str'
				WHERE username = '".$members[$i]['username']."'";
			$doModification = mysqli_query($mysqli, $modify);
			
		}
	}
	
	
		
	header("location: ../honorBoardForm.php");
?>