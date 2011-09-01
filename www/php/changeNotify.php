<?php
session_start();
$authUsers = array('brother');
include_once('authenticate.php');
include_once('login.php');

$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$newEventArray = $_POST[newEvent];
$newReminderArray = $_POST[reminder];

$newEvent = "";
$reminder = "";

if($newEventArray[0] != NULL){
	$newEvent = $newEvent."email|";
}

if($newEventArray[1] != NULL){
	$newEvent = $newEvent."text|";
}

if($newReminderArray[0] != NULL){
	$reminder = $reminder."email|";
}

if($newReminderArray[1] != NULL){
	$reminder = $reminder."text|";
}

$add_sql = "UPDATE members SET notifyNewEvent='$newEvent', notifyReminder='$reminder' WHERE username='".$_SESSION[username]."'";
$add_res = mysqli_query($mysqli, $add_sql);
header("location: ../account.php");

?>