<?php
session_start();
$authUsers = array('admin', 'drm', 'pres');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';

include_once('../php/login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

if(date("n") > 5)
{
	$year = date("Y")+1;
}
else
{
	$year = date("Y");
}

$query = "SELECT date FROM baddDutyDays";
$result = mysqli_query($mysqli, $query);

while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
	$peopleQuery = "SELECT username FROM baddDutyLog WHERE date='$row[date]'";
	$peopleResult = mysqli_query($mysqli, $peopleQuery);
	
	$count=0;
	while($info = mysqli_fetch_array($peopleResult, MYSQLI_ASSOC))
	{
		$count++;
	}
	
	$need = 2-$count;
	
	if($need > 0)
	{
		$zeroQuery = "
			SELECT username
			FROM members
			WHERE username NOT 
			IN (
				SELECT username
				FROM baddDutyLog
				WHERE year = '$year'
			) AND username NOT IN (
				SELECT username
				FROM members
				WHERE gradYear <= '$year'
			)
			AND residency != 'limbo'
			ORDER BY ID, RAND()
			LIMIT $need";
		$zeroResult = mysqli_query($mysqli, $zeroQuery);
		$added=0;
		while($zeroData = mysqli_fetch_array($zeroResult, MYSQLI_ASSOC))
		{
			$checkQuery = "SELECT ID FROM baddDutyLog WHERE username = '$zeroData[username]' AND year='$year' AND date='$row[date]'";
			$checkResult = mysqli_query($mysqli, $checkQuery);
			if(!mysqli_fetch_row($checkResult))
			{
				$insertQuery = "INSERT 	INTO baddDutyLog (username, date, year) VALUES ('$zeroData[username]', '$row[date]', '$year')";
				$peopleResult = mysqli_query($mysqli, $insertQuery);
				$added++;
			}
		}
		
		if($need > $added)
		{	
			while($need > $added)
			{
				$generalQuery = "
					SELECT members.username AS username, COUNT( baddDutyLog.ID ) AS count
					FROM members
					JOIN baddDutyLog ON members.username = baddDutyLog.username
					WHERE baddDutyLog.year =  '$year'
					AND members.gradYear > '$year'
					AND residency != 'limbo'
					GROUP BY members.username
					ORDER BY count, members.ID DESC, RAND()";
				$generalResult = mysqli_query($mysqli, $generalQuery);
				$arrayCount=0;
				$minCount=100;			///////// CONSTANT SET //////////////
				while($generalData = mysqli_fetch_array($generalResult, MYSQLI_ASSOC))
				{
					$usersArray[$arrayCount][username] = $generalData[username];
					$usersArray[$arrayCount]['count'] = $generalData['count'];
					$arrayCount++;
					if($generalData['count'] < $minCount)
					{
						$minCount = $generalData['count'];
					}
				}
				for($i=0; $i < $arrayCount && $need > $added; $i++)
				{
					if($usersArray[$i]['count'] == $minCount)
					{
						$checkQuery = "SELECT ID FROM baddDutyLog WHERE username = '".$usersArray[$i][username]."' AND year='$year' AND date='$row[date]'";
						$checkResult = mysqli_query($mysqli, $checkQuery);
						if(!mysqli_fetch_row($checkTable))
						{
							$insertQuery = "INSERT 	INTO baddDutyLog (username, date, year) VALUES ('".$usersArray[$i][username]."', '$row[date]', '$year')";
							$peopleResult = mysqli_query($mysqli, $insertQuery);
							$added++;
						}
					}
				}
			}
		}
	}
}

$query = "UPDATE baddDutyDays SET status='closed' WHERE status='open'";
$result = mysqli_query($mysqli, $query);

header("location: baddDutyDates.php?month=$_GET[month]&year=$_GET[year]");

?>