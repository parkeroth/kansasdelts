<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB_Table.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB_Manager.php';

class Study_Hour_Logs extends DB_Table
{
    /*
        CREATE TABLE IF NOT EXISTS `studyHourLogs` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `member_id` int(11) NOT NULL,
          `proctor_in` int(11) NOT NULL,
          `proctor_out` int(11) NOT NULL,
          `time_in` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          `duration` float DEFAULT NULL,
          `open` enum('yes','no','limbo') NOT NULL,
          PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Inividual Study Hour Logs' AUTO_INCREMENT=1 ;
     */

        public static $LOG_STATUS = array('yes', 'no', 'limbo');

        public $proctorIn = NULL;
        public $proctorOut = NULL;
        public $duration = NULL;
        public $id = NULL;
        public $timeIn = NULL;
        public $open = NULL;
        public $userID = NULL;

        function __construct ($log_id = NULL) {
		$this->table_name = 'studyHourLogs';
		$this->table_mapper = array(
                        'id' => 'ID',
                        'userID' => 'member_id',
			'timeIn' => 'time_in',
                        'proctorIn' => 'proctor_in',
                        'proctorOut' => 'proctor_out',
			'open' => 'open',
			'duration' => 'duration'
		);

		if($log_id){
			$params = array('id' => $log_id);
		} else {
			$params = NULL;
		}
		parent::__construct($params);
	}

        public function start_sh_session() {
                $curTime = time();
                $this->timeIn = date('Y-m-d H:i:s', $curTime);
                $this->proctorIn = $_SESSION['userID'];
                $this->open = "yes";
		return $this->insert();
        }

        public function end_sh_session() {
        	$this->proctorOut = $_SESSION['userID'];
		$curTime = time();

		$timeDiff = $curTime - strtotime($this->timeIn);
		//now convert to elapsed hours
		$this->duration = floatval($timeDiff/60/60);

		//Now set up our update query
               $this->open = 'no';                                   //close out the open session
               $this->save();                                           //and update the database
               return $this->duration;                            //return duration of current session back to caller
        }

        public function delete_sh_log() {
		return $this->delete();
        }
}

class Study_Hour_Requirements extends DB_Table
{
    /*
        CREATE TABLE IF NOT EXISTS `studyHourRequirements` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `member_id` int(11) NOT NULL,
          `start_date` date NOT NULL,
          `stop_date` date NOT NULL,
          `week_required` tinyint(4) NOT NULL,
          `total_complete` smallint(6) NOT NULL DEFAULT '0',
          `status` enum('in','out','limbo') NOT NULL,
          PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='requirements for anyone with study hours' AUTO_INCREMENT=1 ;
     */


        public $member_id = NULL;
        public $start_date = NULL;
        public $stop_date = NULL;
        public $week_required = NULL;
        public $week_complete = NULL;
        public $total_complete = NULL;
        public $status  = NULL;
        public $id = NULL;

	function __construct ($in_id = NULL) {
		$this->table_name = 'studyHourRequirements';
		$this->table_mapper = array(
                        'member_id' => 'member_id',
                        'id' => 'ID',
			'start_date' => 'start_date',
			'stop_date' => 'stop_date',
			'week_required' => 'week_required',
			'total_complete' => 'total_complete',
			'status' => 'status'
		);

		if($in_id){
			$params = array('id' => $in_id);
		} else {
			$params = NULL;
		}
		parent::__construct($params);
	}

        public function update_hrs_completed($newHrs) {
                //can both add and subtract hours if adjustments need to be made
                //pass in a negative value to subtract hours
                $this->total_complete += $newHrs;
                return $this->save();
        }

        public function set_user_status($newStatus) {
                $this->status = $newStatus;
                return $this->save();
        }

        public function remove_sh_user() {
                return $this->delete();
        }
}

class Study_Hour_Manager extends DB_Manager
{
        function __construct() {
		parent::__construct();
	}

        private function get_sh_user_list($where) {
		$list = array();
		$query = "
			SELECT ID FROM studyHourRequirements
			$where
			ORDER BY member_id ASC";
		$result = $this->connection->query($query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Study_Hour_Requirements($data[ID]);
		}
		return $list;
	}

        public function get_all_sh_users() {
                $where = '';
                return $this->get_sh_user_list($where);
        }

        public function get_by_sh_status($in = true) {
                $where = $in ? "WHERE status = 'in'" : "WHERE status = 'out'";    //ternary if to check active status
                return $this->get_sh_user_list($where);
        }

        public function add_sh_user($userID, $week_required, $start_date, $stop_date) {
                $new_sh_user = new Study_Hour_Requirements();
                $new_sh_user->member_id = $userID;
                $new_sh_user->week_required = $week_required;
                $new_sh_user->start_date = $start_date;
                $new_sh_user->stop_date = $stop_date;
                $new_sh_user->total_complete = 0;
                $new_sh_user->status = 'out';
		return $new_sh_user->add_sh_user();
        }

        public function get_user_sh_requirements($userID) {
                $where = "member_id = '$userID'";
                $shList = $this->get_sh_user_list($where);
                //should only return one result, so just return the first
                //instance of Study_Hour_Requirements
                return $shList[0];
        }
        
        public function is_in_table($member_id) {
                //$query  = "SELECT EXISTS(SELECT 1 FROM studyHourRequirements WHERE member_id='$member_id')";
                $query = "SELECT member_id FROM studyHourRequirements WHERE member_id='$member_id'";
                $rows = mysqli_num_rows($this->connection->query($query));
                if($rows > 0) {
                    return true;
                } else {
                    return false;
                }
        }
}

class Study_Hour_Log_Manager extends DB_Manager
{
        function __construct() {
		parent::__construct();
	}

        private function get_session_list($where) {
		$list = array();
		$query = "
			SELECT ID FROM studyHourLogs
			$where
			ORDER BY startTime DESC
                        LIMIT 50";
		$result = $this->connection->query($query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Study_Hour_Logs($data[ID]);
		}
		return $list;
	}

        private function get_user_sessions($userID, $where = "") {
                $where .= $where=="" ? "WHERE userID = '$userID'" : " AND userID = '$userID'";    //ternary if to see if where clause is empty or not
                return $this->get_session_list($where);
        }

        public function get_all_sessions($userID = false) {
                $where = "";
                $retVal = $userID ? $this->get_session_list($where) : $this->get_user_sessions($userID);
                return $retVal;
        }

        public function get_open_sessions($userID = false) {
                $where = "WHERE open='yes'";
                $retVal = $userID ? $this->get_session_list($where) : $this->get_user_sessions($userID, $where);
                return $retVal;
        }

        public function get_week_data($userID, $week_offset = 0) {
                $weekStart = mktime(1, 0, 0, date('m'), date('d')-date('w'), date('Y'));        //start of current week
                $weekStart = $weekStart - ($week_offset * 7 * 24 * 60 * 60);                    //subtract week offset
                $where = "WHERE YEARWEEK(timeIn) = YEARWEEK($weekStart)";
                return $this->get_user_sessions($userID, $where);
        }

        public function get_weekly_block_complete($userID, $week_offset = 0) {
                $weekStart = mktime(1, 0, 0, date('m'), date('d')-date('w'), date('Y'));        //start of current week
                $weekStart = $weekStart - ($week_offset * 7 * 24 * 60 * 60);                    //subtract week offset
                $query = "
                    SELECT COUNT(*) AS count
                    FROM studyHourLogs
                    WHERE userID='$userID'
                        AND duration > 2.5
                        AND YEARWEEK(timeIn) = YEARWEEK($weekStart)
                    GROUP BY userID";
                $result = $this->connection->query($query);
                $row = mysql_fetch_row($result);
                return $row[0];
        }

        public function get_weekly_total_complete($userID, $week_offset = 0) {
                $weekStart = mktime(1, 0, 0, date('m'), date('d')-date('w'), date('Y'));        //start of current week
                $weekStart = $weekStart - ($week_offset * 7 * 24 * 60 * 60);                    //subtract week offset
                $query = "
                    SELECT SUM(duration) AS count
                    FROM studyHourLogs
                    WHERE userID='$userID'
                        AND YEARWEEK(timeIn) = YEARWEEK($weekStart)
                    GROUP BY userID";
                $result = $this->connection->query($query);
                $row = mysql_fetch_row($result);
                return $row[0];
        }
}

?>
