<?php

session_start();
include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
$authUsers = array('admin', 'photo', 'historian');
include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');

if ($_SESSION["access"] == "granted" && $isAuthorized == true) {
	header('Location: plog-upload.php');
	exit;
}

?>