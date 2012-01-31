<?php
session_start();
$authUsers = array('saa', 'admin');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';
	
include_once('../php/login.php');
include_once('snippets.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$month = date('n');
$year = date('Y');

if($month < 6){
	$startDate = "$year-01-01";
	$endDate = "$year-05-31";
	$term = 'spring';
} else {
	$startDate = "$year-08-01";
	$endDate = "$year-12-31";
	$term = 'fall';
}

if($_GET[type] == 'auth')
{
	$id = $_GET[id];
	
	$infractionQuery = "
		SELECT offender, type, name, dateOccured as date
		FROM infractionLog l
		JOIN infractionTypes t
		ON l.type = t.code
		WHERE l.ID='$id'";
	$getInfraction = mysqli_query($mysqli, $infractionQuery);
	$infractionArray = mysqli_fetch_array($getInfraction, MYSQLI_ASSOC);
	
	$numOccurance = 1 + checkOccurance($mysqli, $infractionArray[type], $infractionArray[offender], $startDate, $endDate);
	
	$punishQuery = "
		SELECT ID, fine, hours, hourType, suspension, expel
		FROM punishments
		WHERE type='$infractionArray[type]'
		AND offenceNum='$numOccurance'";
	$getPunishment = mysqli_query($mysqli, $punishQuery);
	$punishArray = mysqli_fetch_array($getPunishment, MYSQLI_ASSOC);
	
	// Assess fines
	if($punishArray[fine] > 0)
	{
		$query = "	INSERT INTO fines (amount, username, status, date, description)
					VALUES ('$punishArray[fine]', '$infractionArray[offender]', 'pending', '$infractionArray[date]', '$infractionArray[name]: $numOccurance')";
		$result = mysqli_query($mysqli, $query);
	}
	
	// Apply Hours to account
	if($punishArray[hours] > 0)
	{
		$query = "	INSERT INTO hourLog (username, term, year, hours, type, eventID, dateAdded, notes)
					VALUES ('$infractionArray[offender]', '$term', '$year', '-$punishArray[hours]', '$punishArray[hourType]',
							'0', '".date('Y-m-d')."', '$infractionArray[name]: $numOccurance')";
		$result = mysqli_query($mysqli, $query);
	}
	if($punishArray[suspension] != 'none' && $punishArray[suspension] != 'NULL')
	{
		echo "Apply suspension<br>";
	}
	if($punishArray[expel])
	{
		echo "Apply expel<br>";
	}	
	
	$query = "	UPDATE infractionLog 
				SET status = '$_GET[status]'
				WHERE ID = '$id'";
	$result = mysqli_query($mysqli, $query);
	
	header("location: manageMissedDuties.php");
	
}
else if($_GET[type] == 'revert')
{
	$id = $_GET[id];
	
	$infractionQuery = "
		SELECT offender, type, name
		FROM infractionLog l
		JOIN infractionTypes t
		ON l.type = t.code
		WHERE l.ID='$id'";
	$getInfraction = mysqli_query($mysqli, $infractionQuery);
	$infractionArray = mysqli_fetch_array($getInfraction, MYSQLI_ASSOC);
	
	$numOccurance = checkOccurance($mysqli, $infractionArray[type], $infractionArray[offender], $startDate, $endDate);
	
	$punishQuery = "
		SELECT ID, fine, hours, hourType, suspension, expel
		FROM punishments
		WHERE type='$infractionArray[type]'
		AND offenceNum='$numOccurance'";
	$getPunishment = mysqli_query($mysqli, $punishQuery);
	$punishArray = mysqli_fetch_array($getPunishment, MYSQLI_ASSOC);
	
	if($punishArray[fine] > 0)
	{
		echo "Correct fine<br>";
	}
	if($punishArray[hours] > 0)
	{
		$query = "	INSERT INTO hourLog (username, term, year, hours, type, eventID, dateAdded, notes)
					VALUES ('$infractionArray[offender]', '$term', '$year', '$punishArray[hours]', '$punishArray[hourType]',
							'0', '".date('Y-m-d')."', 'SAA Correction')";
		$result = mysqli_query($mysqli, $query);
	}
	if($punishArray[fine] > 0)
	{
		$query = "	INSERT INTO fines (amount, username, status, date, description)
					VALUES ('-$punishArray[fine]', '$infractionArray[offender]', 'pending', '$infractionArray[date]', 'SAA Correction')";
		$result = mysqli_query($mysqli, $query);
	}
	
	$query = "	UPDATE infractionLog 
				SET status = 'reverted'
				WHERE ID = '$id'";
	
	$result = mysqli_query($mysqli, $query);
	
	header("location: manageMissedDuties.php");
}


?>