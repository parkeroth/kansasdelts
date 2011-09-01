<?php
session_start();
$authUsers = array('brother');
include_once('authenticate.php');
	
include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

if($_GET[action] == "accept")
{
	$tradeQuery = "SELECT * FROM `messages` WHERE `id`='$_GET[ID]'";
	$tradeQueryData = mysqli_query($mysqli, $tradeQuery);
	$tradeQueryArray = mysqli_fetch_array($tradeQueryData, MYSQLI_ASSOC);
	
	$query = "UPDATE `messages` SET `status`='rejected' WHERE `to`='$_SESSION[username]' AND `type`='baddTrade' AND `content`='$tradeQueryArray[content]'";
	$add_res = mysqli_query($mysqli, $query);
	
	$query = "UPDATE `messages` SET `status`='accepted' WHERE `id`='$_GET[ID]'";
	$add_res = mysqli_query($mysqli, $query);
}
else if($_GET[action] == "reject")
{
	$query = "UPDATE `messages` SET `status`='rejected' WHERE `id`='$_GET[ID]'";
	$add_res = mysqli_query($mysqli, $query);
}

header("location: ../account.php");

?>