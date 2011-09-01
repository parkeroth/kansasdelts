<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
	$authUsers = array('admin', 'publicRel', 'pres');
	include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');
	
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	//get the previous crap first
	$bid = $_GET['blogID'];
	$deletetEntryQ = '
		DELETE FROM blogContent
		WHERE id="'.$bid.'"
	';
	$deleteEntry = mysqli_query($mysqli, $deletetEntryQ);
	
	header("location: manageBlog.php");
?>