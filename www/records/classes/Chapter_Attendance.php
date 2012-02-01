<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB_Table.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';

class Chapter_Attendance extends DB_Table {
	private static $STATUS = array('present', 'excused', 'absent');
	
	public $id = NULL;
	public $memeber_id = NULL;
	public $meeting_id = NULL;
	public $username = NULL; // Deprecated
	public $status = NULL;
	public $date = NULL;	// Deprecated
	
	function __construct($log_id) {
		$this->table_name = 'attendance'; //TODO: Change to chapter_attendance
		$this->table_mapper = array('id' => 'ID',
							'member_id' => 'member_id',
							'meeting_id' => 'meeting_id',
							'username' => 'username', // Deprecated
							'status' => 'status',
							'date' => 'date');		 // Deprecated
		$params = array('id' => $log_id);
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

class Chapter_Attendance_Manager extends DB_Manager {
	
	public function get_record_by_meeting_member($member_id, $meeting_id){
		$where = "WHERE member_id='$member_id' AND meeting_id ='$meeting_id'";
		$list = $this->get_attendance_list($where);
		if($list){
			return $list[0];
		} else {
			return null;
		}
	}
	
	private function get_attendance_list($where, $limit = NULL){
		if(!$limit){
			$limit = 20;
		}
		$list = array();
		$query = "
			SELECT ID FROM attendance
			$where
			LIMIT $limit"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Chapter_Attendance($data[ID]);
		}
		return $list;
	}
}
?>
