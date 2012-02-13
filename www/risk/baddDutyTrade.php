<?php
session_start();
$authUsers = array('admin', 'drm');
include_once('authenticate.php');
	
include_once('login.php');

$query = "SELECT date FROM baddDutyDays WHERE ID = '$_POST[id]'";
$result = mysqli_query($mysqli, $query);
$info = mysqli_fetch_array($result, MYSQLI_ASSOC);


$query = "	UPDATE baddDutyLog 
			SET username = '$_POST[newPerson]' 
			WHERE date = '$info[date]' 
				AND username = '$_POST[origPerson]'";

$add_res = mysqli_query($mysqli, $query);

$month = date("n", strtotime($info['date'])) +1;
$year = date("Y", strtotime($info['date']));


header("location: ../baddDutyDates.php?month=$month&year=$year");

?>