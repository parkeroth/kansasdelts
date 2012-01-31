<?php
	session_start();
	$authUsers = array('admin', 'drm', 'pres');
	include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';
	include_once('../php/login.php');

	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	if($_POST[action] == "assign")
	{
		$eventData = "
			SELECT * 
			FROM soberGentEvents 
			WHERE ID = '".$_POST[id]."'";
		$getEventData = mysqli_query($mysqli, $eventData);
		$eventArray = mysqli_fetch_array($getEventData, MYSQLI_ASSOC);
		
		$userData = "
			SELECT * 
			FROM members
			WHERE residency != 'limbo'
			ORDER BY lastName";
		$getUserData = mysqli_query($mysqli, $userData);
		
		$memberCount = 0;
		while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
		{
			$members[$memberCount]['username'] = $userDataArray['username'];
			$memberCount++;
		}
		
		if(date("n") > 5)
		{
			$year = date("Y")+1;
		}
		else
		{
			$year = date("Y");
		}
		
		for($i = 0; $i < $memberCount; $i++)
		{
			if($_POST[$members[$i][username]] == 1)
			{
				$modify = "INSERT INTO soberGentLog
					(username, year, dateServed, eventID)
					VALUES ('".$members[$i][username]."', '$year', '".$eventArray[eventDate]."', '".$_POST[id]."')";
				$doModification = mysqli_query($mysqli, $modify);
				
				$query = "
					SELECT username
					FROM volunteer
					WHERE type = 'sober'
					AND username = '".$members[$i][username]."'";
				$result = mysqli_query($mysqli, $query);
				if($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
					$modify = "DELETE FROM volunteer
						WHERE type='sober'
						AND username='".$members[$i][username]."'";
					$doModification = mysqli_query($mysqli, $modify);
				}
			}
		}
	}
	else if($_POST[action] == "remove")
	{
		$modify = "DELETE FROM soberGentLog
			WHERE eventID='".$_POST[id]."'
			AND username='".$_POST[user]."'";
		$doModification = mysqli_query($mysqli, $modify);
	}
	
	
	
	header("location: editSoberGentEvent.php?id=".$_POST[id]."");
?>