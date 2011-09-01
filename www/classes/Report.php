<?php
/*
 * Number of seconds reports are due before date of meeting
 * Set As: 12 noon day of meeting (+12 hrs)
 */
require_once 'Task.php';

$deadline = 43200;

/*
 * CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_submitted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `position_id` int(11) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `meeting_date` date NOT NULL,
  `discussion` text,
  `agenda` text,
  `minutes` text,
  PRIMARY KEY (`id`)
)
 */
class Report
{
	public static $REPORT_STATUS = array('pending', 'complete', 'incomplete');
	
	private $connection = NULL;
	public $id = NULL;
	public $date_submitted = NULL;
	public $position_id = NULL;
         public $status = NULL;
         public $meeting_date = NULL;
	public $discussion = NULL;
	public $agenda = NULL;
	public $minutes = NULL;

	public function Report($mysqli, $id) {
		$this->connection = $mysqli;

		$query = "
			SELECT *
			FROM reports
			WHERE ID= '$id'"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		$data = mysqli_fetch_array($result, MYSQLI_ASSOC);

		$this->id = $data[id];
		$this->date_submitted = $data[date_submitted];
		$this->position_id = $data[position_id];
		$this->status = $data[status];
		$this->meeting_date = $data[meeting_date];
		$this->discussion = $data[discussion];
		$this->agenda = $data[agenda];
		$this->minutes = $data[minutes];
	}
	
	public function saveVal($field, $val){
		if($val != NULL){
			echo "is NULL <br>";
			$val = "'$val'";
		}
			
		$this->$field = $val;
		$query = "
			UPDATE reports
			SET $field = $val
			WHERE id = '$this->id'"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
	}

	public function insert(){
		$report_exists = $this->report_exists($this->position_id, $this->meeting_date);
		if(!$report_exists){
			$query = "INSERT INTO reports
						(meeting_date, position_id, 
						discussion, agenda, status)
					VALUES
						(".make_null($this->meeting_date).",
						 ".make_null($this->position_id).",
						 ".make_null($this->discussion).",
						 ".make_null($this->agenda).",
						 'pending')"; //echo $query.'<br>';
			$result = mysqli_query($this->connection, $query);
			$this->id = $this->connection->insert_id;
		} else {

		}
	}

	private function report_exists($position_id, $meeting_date){
		$query = "
			SELECT ID
			FROM reports
			WHERE position_id= '$position_id'
			AND meeting_date='$meeting_date'"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
		if($data){
			return $data[ID];
		} else {
			return 0;
		}
	}

	public function is_late(){
		$time_due = strtotime($this->meeting_date) + $deadline;
		$time_submitted = strtotime($this->date_submitted);
		return $time_submitted > $time_due;
	}

	public function can_edit(){
		return $this->status == 'pending';
	}
	
	public function get_tasks($status){
		$task_manager = new TaskManager($this->connection);
		return $task_manager->get_tasks_by_report_id($this->id, $status);
	}
	
	/*
	 * Takes a list of task ids and associates them with the provided status to the record.
	 * Sets all previously associated tasks to new status
	 * 
	 * @param status	status to be applied to the task
	 *				acceptable values in task.php Task::$TASK_STATUS
	 * @param task_ids	list of ids to associate with report
	 */
	public function assign_tasks($status, $task_ids){
		$task_manager = new TaskManager($this->connection);
		$previously_committed_tasks = $task_manager->get_tasks_by_report_id($this->id);
		if($previously_committed_tasks){
			foreach($previously_committed_tasks as $task){
				$task->saveVal('status', 'new');
				$task->saveVal('report_id', NULL);
			}
		}
		foreach($task_ids as $task_id){
			$task = new Task($this->connection, $task_id);
			$task->saveVal('report_id', $this->id);
			$task->saveVal('status', $status);
		}
	}
	
	public function get_previous_report_id(){
		$query = "
			SELECT ID
			FROM reports
			WHERE position_id= '$this->position_id'
			AND meeting_date < '$this->meeting_date'
			ORDER BY meeting_date DESC
			LIMIT 1"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
		if(isset($data[ID])){
			return $data[ID];
		} else {
			return 0;
		}
	}
}

class ReportManager
{
	private $connection = NULL;

	public function ReportManager($mysqli) {
		$this->connection = $mysqli;
	}

	public function get_reports_by_position($position_id){
		$where = "WHERE position_id = '$position_id'";
		return $this->get_report_list($where);
	}
	
	public function get_reports_by_date_board($date, $board){
		$date = date('Y-m-d', strtotime($date));
		$where = "WHERE board = '$board'
					AND meeting_date = '$date'";
		return $this->get_report_list($where);
	}

	public function get_reports_by_date_position($date, $position_id){
		$date = date('Y-m-d', strtotime($date));
		$where = "WHERE position_id = '$position_id'
					AND meeting_date = '$date'";
		return $this->get_report_list($where);
	}

	private function get_report_list($where){
		$list = array();
		$query = "
			SELECT ID FROM reports
			$where
			ORDER BY ID ASC"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Report($this->connection, $data[ID]);
		}
		return $list;
	}
	
	public function get_latest_report_by_position($position_id){
		$query = "
			SELECT ID
			FROM reports
			WHERE position_id= '$position_id'
			ORDER BY meeting_date DESC
			LIMIT 1"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
		if(isset($data[ID])){
			return new Report($this->connection, $data[ID]);
		} else {
			return NULL;
		}
	}
	
	public function get_next_meeting_date($position_id){
		$latest_report = $this->get_latest_report_by_position($position_id);
		if($latest_report){
			$latest_meeting = $latest_report->meeting_date;
			return strtotime( 'next Sunday', strtotime($latest_meeting));
		} else {
			return strtotime( 'next Sunday');
		}
	}
}
?>