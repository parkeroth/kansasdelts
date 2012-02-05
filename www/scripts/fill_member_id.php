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
require_once $_SERVER['DOCUMENT_ROOT'].'/honor/classes/Infraction_Log.php';

$log_manager = new Infraction_Log_Manager();
$log_list = $log_manager->get_all();
foreach($log_list as $log){
	$offender = new Member(NULL, $log->offender);
	echo $offender->first_name.'<br>';
	$reporter = new Member(NULL, $log->reporter);
	$log->reporter_id = $reporter->id;
	$log->offender_id = $offender->id;
	$log->save();
}

?>
