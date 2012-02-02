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
	
	public function get_meeting_date(){
		$meeting = new Meeting($this->meeting_id);
		return $meeting->date;
	}
	
	function __destruct() {
		parent::__destruct();
	}
}

class Chapter_Attendance_Manager extends DB_Manager {
	
	public function get_total_by_meeting($meeting_id, $status, $only_voting = false){
		if($only_voting){
			$restrict_str = " AND m.memberStatus = 'active'";
		} else {
			$restrict_str = '';
		}
		$query = "SELECT count(a.ID) as total 
				FROM attendance AS a
				JOIN members AS m
				ON a.member_id = m.ID
				WHERE a.meeting_id = '$meeting_id'
				AND a.status = '$status' 
				$restrict_str"; //echo $query;
		$result = mysqli_query($this->connection, $query);
		$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		return $data[total];
	}
	
	public function get_list_by_meeting($meeting_id, $sort = true){
		$list = array();
		$query = "SELECT a.ID 
				FROM attendance AS a
				JOIN members AS m
				ON a.member_id = m.ID
				WHERE a.meeting_id = '$meeting_id'
				ORDER BY m.lastName"; //echo $query;
		$result = mysqli_query($this->connection, $query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Chapter_Attendance($data[ID]);
		}
		return $list;
	}
	
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
