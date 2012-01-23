<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB_Table.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB_Manager.php';

class Study_Hour_Logs extends DB_Table
{
        public static $LOG_STATUS = array('yes', 'no', 'limbo');

        public $proctorIn = NULL;
        public $proctorOut = NULL;
        public $duration = NULL;
        public $sessionID = NULL;
        public $timeIn = NULL;
        public $open = NULL;
        public $userID = NULL;

        function __construct ($log_id = NULL) {
		$this->table_name = 'studyHourLogs';
		$this->table_mapper = array(
                        'userID' => 'userID',
                        'sessionID' => 'ID',
			'timeIn' => 'timeIn',
                        'proctorIn' => 'proctorIn',
                        'proctorOut' => 'proctorOut',
			'open' => 'open',
			'duration' => 'duration'
		);

		if($log_id){
			$params = array('sessionID' => $log_id);
		} else {
			$params = NULL;
		}
		parent::__construct($params);
	}

        public function start_sh_session() {
                $curTime = time();
                $this->timeIn = date('Y-m-d H:i:s', $curTime);
                $this->proctorIn = $_SESSION['username'];
                $this->open = "yes";
		return $this->insert();
        }

        public function end_sh_session($shUser, $timeCompleted) {
                //they were in, so we have work to do

		//The easiest way to do this is to search for any logs with the
		//"open" status, close them, and calculate the duration
		//NOTE: this will cause problems if more them one session is open, but
		//that shouldn't be the case.  working the solution another way will be more
		//time/resource comsuming, so i'll just do it the "drity" way until it causes problems

        	$this->proctorOut = $_SESSION['username'];
		$curTime = time();

		$timeDiff = $curTime - strtotime($this->timeIn);
		//now convert to elapsed hours
		$this->duration = floatval($timeDiff/60/60);

		//Now set up our update query
               $this->open = 'no';                                   //close out the open session
               $this->save();          //and update the database
               return $this->duration;                  //return duration of current session back to caller
        }

        public function delete_sh_log() {
		return $this->delete();
        }
}

class Study_Hour_Requirements extends DB_Table
{
        public $userID = NULL;
        public $startDate = NULL;
        public $stopDate = NULL;
        public $weeklyHrs = NULL;
        public $hoursCompleted = NULL;
        public $hoursRequired = NULL;
        public $status  = NULL;
        public $ID = NULL;

	function __construct ($log_id = NULL) {
		$this->table_name = 'studyHourRequirements';
		$this->table_mapper = array(
                        'userID' => 'userID',
                        'ID' => 'ID',
			'startDate' => 'startDate',
			'stopDate' => 'stopDate',
			'hoursRequired' => 'hoursRequired',
			'hoursCompleted' => 'hoursCompleted',
			'status' => 'status'
		);

		if($log_id){
			$params = array('id' => $log_id);
		} else {
			$params = NULL;
		}
		parent::__construct($params);
	}

        public function add_sh_user() {
                //set all class variables externally and then call this
		return $this->insert();
        }

        public function update_hrs_completed($newHrs) {
                //can both add and subtract hours if adjustments need to be made
                $this->hoursCompleted += $newHrs;
                return $this->save();
        }

        public function remove_sh_user($shUser, $timeCompleted) {
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
			ORDER BY userID ASC";
		$result = mysqli_query($this->connection, $query);
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

        public function add_sh_user($userID, $weeklyHrs, $startDate, $stopDate) {
                $new_sh_user = new Study_Hour_Requirements();
                $new_sh_user->userID = $userID;
                $new_sh_user->hoursRequired = $weeklyHrs;
                $new_sh_user->startDate = $startDate;
                $new_sh_user->stopDate = $stopDate;
                $new_sh_user->hoursCompleted = 0;
                $new_sh_user->status = 'out';
		return $new_sh_user->add_sh_user();
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
		$result = mysqli_query($this->connection, $query);
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
                $where = "WHERE open='yes";
                $retVal = $userID ? $this->get_session_list($where) : $this->get_user_sessions($userID, $where);
                return $retVal;
        }
        
}

?>
