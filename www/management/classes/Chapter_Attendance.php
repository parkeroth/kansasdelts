<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB_Table.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';

class Chapter_Attendance extends DB_Table {
	private static $STATUS = array('present', 'excused', 'absent');
	
	public $id = NULL;
	public $username = NULL; //TODO: Change to member_id
	public $status = NULL;
	public $date = NULL;
	
	function __construct($meeting_date) {
		$this->table_name = 'attendance'; //TODO: Change to chapter_attendance
		$this->table_mapper = array('id' => 'ID',
							'username' => 'username', //TODO: Change to member_id
							'status' => 'status',
							'date' => 'date');
		$params = array('date' => $meeting_date);
		parent::__construct($params);
	}
	
	public function get_count($attendanceStatus, $memberStatus = NULL){
		
		if($attendanceStatus != 'present'){
			$query = "
				SELECT COUNT(ID) AS total
				FROM $this->table_name 
				WHERE date = '$this->date'
				AND status = '$attendanceStatus'";
		} else {
			$query = "
				SELECT COUNT(ID) AS total
				FROM members
				WHERE username NOT IN(
					SELECT username
					FROM $this->table_name
					WHERE date = '$this->date'
				)";
		}		
		if($memberStatus != NULL){
			$query .= " AND memberStatus = '$memberStatus'";
		}
		//echo $query;
		$result = $this->connection->query($query);
		$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $data[total];
	}
	
	public function haz_quorum(){
		$memeber_manager = new Member_Manager(); //TODO: Fix this call
		
		$can_vote = sizeof($memeber_manager->get_members_by_status('active'));
		$voting_present = $this->get_count('present', 'active');
		//echo $can_vote.' '.$voting_present;
		if($voting_present/$can_vote > .5){
			return true;
		} else {
			return false;
		}
	}
	
	function __destruct() {
		parent::__destruct();
	}
}
?>
