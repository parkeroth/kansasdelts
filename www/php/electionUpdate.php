<?php
	session_start();
	$authUsers = array('admin', 'secretary');
	include_once('authenticate.php');
	include_once('login.php');

	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	if(isset($_POST[year]))
	{
		$year = $_POST[year];
	}
	else if(date("j") < 10)
	{
		$year = date("Y")+1;
	}
	else
	{
		$year = date("Y");
	}
	
	$month = date(n);
	$season = $_POST[term];
	
	if( ($season == "both") || ($season == "fall" && $month > 4) || ($season == "spring" && $month < 5) )
	{
		$update = true;	
	}
	else
	{
		$update = false;	
	}
	
	$positionData = "
		SELECT * 
		FROM positions
		ORDER BY ID";
	$getPositionData = mysqli_query($mysqli, $positionData);
	
	$positionCount = 0;
	while($positionDataArray = mysqli_fetch_array($getPositionData, MYSQLI_ASSOC))
	{
		$positions[$positionCount]['type'] = $positionDataArray['type'];
		$positions[$positionCount]['title'] = $positionDataArray['title'];
		$positions[$positionCount]['board'] = $positionDataArray['board'];
		$positionCount++;
	}
	
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
	
	
	////////////////////////    UPDATE POSITION LOGS     ///////////////////////
	
	for($i = 0; $i < $positionCount; $i++)
	{
		if($season == "both")
		{
			$check = "SELECT ID 
				FROM positionLog
				WHERE year = '$year'
				AND season = 'spring'
				AND position = '".$positions[$i][type]."'";
			$checkTable = mysqli_query($mysqli, $check);
			
			if(mysqli_fetch_row($checkTable))
			{
				$modify = "UPDATE positionLog
					SET username = '".$_POST['NEW'.$positions[$i]['type']]."'
					WHERE year = '$year'
					AND season = 'spring'
					AND position = '".$positions[$i][type]."'";
				$doModification = mysqli_query($mysqli, $modify);
			}
			else
			{
				$modify = "INSERT INTO positionLog
					(username, position, year, season)
					VALUES ('".$_POST['NEW'.$positions[$i]['type']]."', '".$positions[$i][type]."', $year, 'spring')";
				$doModification = mysqli_query($mysqli, $modify);
			}
			
			$check = "SELECT ID 
				FROM positionLog
				WHERE year = '$year'
				AND season = 'fall'
				AND position = '".$positions[$i][type]."'";
			$checkTable = mysqli_query($mysqli, $check);
			
			if(mysqli_fetch_row($checkTable))
			{
				$modify = "UPDATE positionLog
					SET username = '".$_POST['NEW'.$positions[$i]['type']]."'
					WHERE year = '$year'
					AND season = 'fall'
					AND position = '".$positions[$i][type]."'";
				$doModification = mysqli_query($mysqli, $modify);
			}
			else
			{
				$modify = "INSERT INTO positionLog
					(username, position, year, season)
					VALUES ('".$_POST['NEW'.$positions[$i]['type']]."', '".$positions[$i][type]."', $year, 'fall')";
				$doModification = mysqli_query($mysqli, $modify);
			}
		}
		else
		{
			$check = "SELECT ID 
				FROM positionLog
				WHERE year = '$year'
				AND season = '$season'
				AND position = '".$positions[$i][type]."'";
			$checkTable = mysqli_query($mysqli, $check);
			
			if(mysqli_fetch_row($checkTable))
			{
				$modify = "UPDATE positionLog
					SET username = '".$_POST['NEW'.$positions[$i]['type']]."'
					WHERE year = '$year'
					AND season = '$season'
					AND position = '".$positions[$i][type]."'";
				$doModification = mysqli_query($mysqli, $modify);
			}
			else
			{
				$modify = "INSERT INTO positionLog
					(username, position, year, season)
					VALUES ('".$_POST['NEW'.$positions[$i]['type']]."', '".$positions[$i][type]."', $year, '$season')";
				$doModification = mysqli_query($mysqli, $modify);
			}
		}
	}
	
	////////////////////////    UPDATE MEMBER RECORDS     ////////////////////////
	if($update)
	{
		for($i = 0; $i < $memberCount; $i++)
		{
			$modify = "UPDATE members
					SET accountType = '|brother'
					WHERE username = '".$members[$i]['username']."'";
			$doModification = mysqli_query($mysqli, $modify);
		}
		
		for($i = 0; $i < $positionCount; $i++)
		{
			if($positions[$i]['type'] == "webmaster" || $positions[$i]['type'] == "pres" || $positions[$i]['type'] == "vicePres")
			{
				$admin = "|admin";
			}
			else
			{
				$admin = "";
			}
			$modify = "UPDATE members
				SET accountType = '$admin|".$positions[$i]['type']."'
				WHERE username = '".$_POST['NEW'.$positions[$i]['type']]."'";
			$doModification = mysqli_query($mysqli, $modify);
		}
	}
	header("location: ../account.php");
?>