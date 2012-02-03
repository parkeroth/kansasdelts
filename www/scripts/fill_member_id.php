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
require_once $_SERVER['DOCUMENT_ROOT'].'/records/classes/Chapter_Attendance.php';


$meeting_ids = array(7, 15, 16);

$attendance_manager = new Chapter_Attendance_Manager();
foreach($meeting_ids as $meeting_id){
	
	echo 'MEETING id: '.$meeting_id.'<br>';
	$attendance_list = $attendance_manager->get_list_by_meeting($meeting_id, $sort = false);
	foreach($attendance_list as $record){
		echo 'MEMBER username: '.$record->username.'<br>';
		$member = new Member(NULL, $record->username);
		echo 'MEMBER id: '.$member->id.'<br>';
		$record->member_id = $member->id;
		$record->save();
	}
	echo '<br>';
}
?>
