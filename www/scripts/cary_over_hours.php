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
require_once 'Hour_Logs.php';


$REQUIRED_HOURS = array('houseHours' => 5, 'serviceHours' => 10);


$type = $_GET[type];
$term = $_GET[term];
$year = $_GET[year];
$revert =$_GET[revert];

if($term == 'spring'){
	$term_next = 'fall';
	$year_next = $year;
} else {
	$term_next = 'spring';
	$year_next = $year + 1;
}

$required = $REQUIRED_HOURS[$type];

function get_total_hours($log_list){
	$hour_total = 0;
	foreach($log_list as $log){
		$hour_total += $log->hours;
	}
	return $hour_total;
}

function get_missing_hours($type, $term, $year){
	
}

if(!isset($revert)){
	
	$member_manager = new Member_Manager();
	$member_list = $member_manager->get_all_members();
	$hour_log_manager = new Hour_Log_Manager();
	
	$previous_hours = array();
	foreach($member_list as $member){
		$log_list = $hour_log_manager->get_by_term($member->username, $type, $term, $year);
		$previous_hours[$member->id] = get_total_hours($log_list);
	}
	
	echo 'PEOPLE MISSING<BR>';
	foreach($member_list as $member){
		$total = $previous_hours[$member->id];
		if($total < $required){
			$missing = $required - $total;
			echo $member->first_name.' '.$member->last_name.' - '.$missing.'<br>';
			
			$new_log = new Hour_Log();
			$new_log->username = $member->username;	//TODO: change to member_id
			$new_log->notes = Hour_Log::$carry_over_notes;
			$new_log->term = $term_next;
			$new_log->type = $type;
			$new_log->year = $year_next;
			$new_log->hours = '-'.$missing;
			$new_log->insert();
		}
	}
}
else
{
	$member_manager = new Member_Manager();
	$member_list = $member_manager->get_all_members();
	$hour_log_manager = new Hour_Log_Manager();
	
	foreach($member_list as $member){
		$hour_log_manager->revert_carry_over($term_next, $year_next, $type, $member->username);
	}
}
?>
