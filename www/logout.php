<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/loginSystem/session.php');
	//now do the actual logout stuff
	$session->logout();
	header("location:index.php");
?>