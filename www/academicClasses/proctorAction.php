<?php
session_start();
$authUsers = array('brother');
include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
//include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/studyHours.php';

$type = $_GET[type];
$user_id = $_GET[userID];

if($type == "remove")
{
	//we're removing a proctor with the given id
}  elseif ($type == "add") {
        //we're adding a proctor with the given id
}

echo "ERROR!";

?>