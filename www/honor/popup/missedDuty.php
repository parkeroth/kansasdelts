<?php
session_start();
$authUsers = array('saa', 'admin');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/util.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';
include_once '../classes/Infraction_Log.php';
include_once '../classes/Punishment.php';

include_once '../../php/login.php';		// REMOVE
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);		//REMOVE
include_once('snippets.php');		// REMOVE

$sem = new Semester();

$infraction_id = $_GET[id];
$infraction = new Infraction_Log($infraction_id);
$occurance_num = $infraction->get_occurance_num();

$punishment_manager = new Punishment_Manager();
$list = $punishment_manager->get_by_type($infraction->type, $offence_num);
$punishment = $list[0];

$offender = new Member($infraction->offender_id);

if($_GET[type] == 'auth')
{
	// Assess fines
	if($punishment->fine > 0)
	{
		$query = "	INSERT INTO fines (amount, username, status, date, description)
					VALUES ('$punishment->fine', '$offender->username', 'pending', '$infraction->date_occured', 
							'".Infraction_Log::$INFRACTION_TYPES[$infraction->type].": $occurance_num')";
		$result = mysqli_query($mysqli, $query);
	}
	
	// Apply Hours to account
	if($punishArray[hours] > 0)
	{
		$query = "	INSERT INTO hourLog (username, term, year, hours, type, eventID, dateAdded, notes)
					VALUES ('$offender->username', '$sem->term', '$sem->year', '-$punishment->hours', '$punishment->hour_type',
							'0', '$infraction->date_occured', '".Infraction_Log::$INFRACTION_TYPES[$infraction->type].": $occurance_num')";
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
	
	$infraction->status = $_GET[status];
	$infraction->save();
	
	header("location: ../manageMissedDuties.php");
	
}
else if($_GET[type] == 'revert')
{	
	if($punishment->fine > 0)
	{
		$query = "	INSERT INTO fines (amount, username, status, date, description)
					VALUES ('-$punishment->fine', '$offender->username', 'pending', '$infraction->date_occured', 'SAA Correction')";
		$result = mysqli_query($mysqli, $query);
	}
	if($punishment->hours > 0)
	{
		$query = "	INSERT INTO hourLog (username, term, year, hours, type, eventID, dateAdded, notes)
					VALUES ('$offender->username', '$sem->term', '$sem->year', '$punishment->hours', '$punishment->hour_type',
							'0', '$infraction->date_occured', 'SAA Correction')";
		$result = mysqli_query($mysqli, $query);
	}
	
	$infraction->status = 'reverted';
	$infraction->save();
	
	header("location: ../manageMissedDuties.php");
}


?>