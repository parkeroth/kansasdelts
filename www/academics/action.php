<?php
session_start();
$authUsers = array('brother');
include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
include_once 'classes/studyHours.php';

$type = $_GET['type'];
$id = $_GET['ID'];                      //this is the study hour log id!

if($type == "remove")
{
	$sh_log = new Study_Hour_Logs($id);
        $sh_log->delete_sh_log();
	header("location: memberLog.php?uid=".$_GET['uid']);
}

echo "ERROR!";

?>