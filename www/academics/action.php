<?php
session_start();
$authUsers = array('brother');
include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');

$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$type = $_GET[type];

if($type == "remove")
{
	$add_sql = "DELETE FROM studyHourLogs WHERE ID='".$_GET[ID]."'";
	
	//echo $add_sql;
	
	$add_res = mysqli_query($mysqli, $add_sql);

	header("location: memberLog.php?uname=$_GET[username]");
} 

echo "ERROR!";

?>