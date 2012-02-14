<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/util.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB_Table.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/honor/classes/Punishment.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/honor/classes/Infraction_Log.php';

/**
 * This table holds all the relevent information about chapter attendance records
 *
 * @author Parker Roth
 *
 * Schema Updated: 2011-02-02
 * 
CREATE TABLE IF NOT EXISTS `attendance` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `meeting_id` int(11) NOT NULL,
  `username` varchar(6) DEFAULT NULL COMMENT 'Deprecated',
  `status` set('absent','excused') NOT NULL,
  `date` date DEFAULT NULL COMMENT 'Deprecated',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1444 ;
 * 
 */
class Chapter_Attendance extends DB_Table {
	private static $STATUS = array('present', 'excused', 'absent');
	
	public $id = NULL;
	public $member_id = NULL;
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
	
	public function insert(){
		$this->check_for_punishment();
		parent::insert();
	}
	
	public function save(){
		$this->check_for_punishment();
		parent::save();
	}
	
	function __destruct() {
		parent::__destruct();
	}
	
	private function check_for_punishment(){
		$infraction_type = 'unexcusedChapter';
		$sem = new Semester();
		$punishment_manager = new Punishment_Manager();
		$infraction_manager = new Infraction_Log_Manager();
		
		// If person is absent check for and apply the appropraite punishment
		if($this->status == 'absent'){
			$existing_log_list = $infraction_manager->get_by_meeting_id($this->meeting_id, $this->member_id);
			if(count($existing_log_list) == 0){	// If no existing punishment has been issued for this meeting
				$infraction_log = new Infraction_Log();
				$infraction_log->date_occured = $this->get_meeting_date();
				$infraction_log->description = 'Automated punishment submitted for unexcused chapter absense';
				$infraction_log->meeting_id = $this->meeting_id;
				$infraction_log->offender_id = $this->member_id;
				$infraction_log->type = $infraction_type;
				$infraction_log->insert();
			}
		} else if($this->status == 'excused'){
			$existing_log_list = $infraction_manager->get_by_meeting_id($this->meeting_id, $this->member_id);
			if(count($existing_log_list) > 0){ // If records exist remove them
				foreach($existing_log_list as $log){
					if($log->status != 'pending'){
						throw Exception('Cannot delete a non pending punishment!');
					} else {
						$log->delete();
					}
				}
			}
		}
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
		$this->connect();
		$result = $this->connection->query($query);
		$this->disconnect();
		$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		return $data[total];
	}
	
	public function get_list_by_meeting($meeting_id, $sort = true){
		$list = array();
		if($sort){
			$query = "SELECT a.ID 
					FROM attendance AS a
					JOIN members AS m
					ON a.member_id = m.ID
					WHERE a.meeting_id = '$meeting_id'
					ORDER BY m.lastName";
		} else {
			$query = "SELECT ID FROM attendance
					WHERE meeting_id ='$meeting_id'";
		}
		// echo $query;
		$this->connect();
		$result = $this->connection->query($query);
		$this->disconnect();
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
		$this->connect();
		$result = $this->connection->query($query);
		$this->disconnect();
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Chapter_Attendance($data[ID]);
		}
		return $list;
	}
}
?>
