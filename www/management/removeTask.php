<?php

// TODO: This should probably not be its own page

session_start();
$authUsers = array('brother');
include_once($_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php');

require_once 'classes/Task.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';


$task_id = $_GET[id];
$task = new Task($task_id);

if($task->can_edit($session->member_id)){
	$task->delete();
	$referrer = $_SERVER[HTTP_REFERER];
	header('location: '.$referrer);
}