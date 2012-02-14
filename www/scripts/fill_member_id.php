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
require_once $_SERVER['DOCUMENT_ROOT'].'/honor/classes/Punishment.php';

$hour_manager = new Hour_Log_Manager();
$hour_list = $hour_manager->get_all();
foreach($hour_list as $record){
	$member = new Member(NULL, $record->username);
	$record->notes = addslashes($record->notes);
	$record->member_id =  $member->id;
	$type = $record->type;
	if($type == 'houseHours'){
		$record->type = 'house';
	} else if($type == 'serviceHours'){
		$record->type = 'service';
	} else if($type == 'philanthropyHours'){
		$record->type = 'philanthropy';
	}
	$record->save();
}

$punishment_manager = new Punishment_Manager();
$punishment_list = $punishment_manager->get_all();
foreach($punishment_list as $record){
	$type = $record->hour_type;
	if($type == 'houseHours'){
		$record->hour_type = 'house';
	} else if($type == 'serviceHours'){
		$record->hour_type = 'service';
	} else if($type == 'philanthropyHours'){
		$record->hour_type = 'philanthropy';
	}
	$record->save();
}

?>
