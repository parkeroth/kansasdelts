<?php

/*
 * This script is intended to cary over any hours from the previous semester that did not meet the predefined goals
 * 
 * @TODO Move this to an actual website page under the room points milestone
 * 
 */



session_start();
$authUsers = array('admin');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/hours/classes/Hour_Log.php';

$hour_manager = new Hour_Log_Manager();
$hour_list = $hour_manager->get_all();
foreach($hour_list as $record){
	echo $record->username;
	$member = new Member(NULL, $record->username);
	$record->member_id =  $member->id;
	echo $member->id;
	$record->save();
}

?>
