<?php
session_start();
$authUsers = array('saa', 'admin');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';
include_once '../classes/Infraction_Log.php';

$infraction_id = $_GET[id];
$infraction = new Infraction_Log($infraction_id);

if($_GET[type] == 'auth')
{
	$infraction->apply_punishment();
	
	header("location: ../manageMissedDuties.php");
	
}
else if($_GET[type] == 'revert')
{	
	$infraction->revert_punishment();
	
	header("location: ../manageMissedDuties.php");
}


?>
