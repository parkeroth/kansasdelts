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
require_once $_SERVER['DOCUMENT_ROOT'].'/finance/classes/Fine.php';

$fine_manager = new Fine_Manager();
$fine_list = $fine_manager->get_all();
foreach($fine_list as $record){
	echo $record->username;
	$member = new Member(NULL, $record->username);
	$record->member_id =  $member->id;
	echo $member->id;
	$record->save();
}

?>
