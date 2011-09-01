<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/recruitment/util.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/query.php');
require_once 'Report.php';

class Task
{
	public static $TASK_STATUS = array('new', 'proposed', 'committed', 'closed');
	public static $TASK_PROGRESS = array(	'not-started' => 'Not Started',
									'in-progress' => 'In Progress',
									'blocked' => 'Blocked',
									'completed' => 'Completed',
									'cancelled' => 'Cancelled');
	public static $TASK_PRIORITY = array('low', 'normal', 'high', 'critical');
	
	private $connection = NULL;
	public $id = NULL;
	public $title = NULL;
	public $position_id = NULL;
	public $deadline = NULL;
	public $status = NULL;
	public $progress = NULL;
	public $priority = NULL;
         public $notes = NULL;
         public $last_updated = NULL;
	public $report_id = NULL;

	public function Task($mysqli, $id = NULL) {
		$this->connection = $mysqli;

		if($id != NULL){
			$query = "
				SELECT *
				FROM tasks
				WHERE ID= '$id'"; //echo $query.'<br>';
			$result = mysqli_query($this->connection, $query);
			$data = mysqli_fetch_array($result, MYSQLI_ASSOC);

			$this->id = $data[ID];
			$this->title = $data[title];
			$this->position_id = $data[position_id];
			$this->deadline = $data[deadline];
			$this->status = $data[status];
			$this->progress = $data[progress];
			$this->priority = $data[priority];
			$this->last_updated = $data[last_updated];
			$this->notes = $data[notes];
			$this->report_id = $data[report_id];
		}
	}

	public function insert(){
		$query = "INSERT INTO tasks
					(title, position_id, deadline,
					 status, priority, notes)
				VALUES
					(".make_null($this->title).",
					 ".make_null($this->position_id).",
					 ".make_null($this->deadline).",
					 'new',
					 ".make_null($this->priority).",
					 ".make_null($this->notes).")"; echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		$this->id = $this->connection->insert_id;
	}

	public function saveVal($field, $val){
		if($val == NULL){
			$val = 'NULL';
		} else {
			$val = "'$val'";
		}
		
		$this->$field = $val;
		$query = "
			UPDATE tasks
			SET $field = $val
			WHERE ID = '$this->id'"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
	}

	public function delete(){
		$query = "
			DELETE FROM tasks
			WHERE ID = '$this->id'"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
	}

	public function get_deadline(){
		$deadline = strtotime($this->deadline);
		$now = time();

		if($deadline > $now){ //Deadline has not yet passed
			$days = round( ($deadline - $now)/86400, 0);
			if ($days == 0){
				$str  = 'Today';
			} else if($days == 1){
				$str = 'Tomorrow';
			} else {
				$str = $days.' days';
			}
		} else { //Deadline has passed
			$days = round( ($now - $deadline)/86400, 0);
			if ($days == 0){
				$str  = 'Today';
			} else if($days == 1){
				$str = 'Yesterday';
			} else {
				$str = $days.' days ago';
			}
		}
		return $str;
	}

	public function days_until_due(){
		$deadline = strtotime($this->deadline);
		$now = time();
		return round( ($deadline-$now)/86400 , 0);
	}
	
	public function get_progress_class(){
		if($this->progress == 'in-progress' || $this->progress == 'completed'){
			return 'progress-green';
		} else if($this->progress == 'blocked') {
			return 'progress-yellow';
		} else if($this->progress == 'not-started'){
			return 'progress-red';
		}
	}
}

class TaskManager
{
	private $connection = NULL;

	public function TaskManager($mysqli) {
		$this->connection = $mysqli;
	}

	public function get_tasks_by_position($position_id, $status = NULL){
		$where = "WHERE position_id = '$position_id'";
		if($status != NULL){
			$where .= " AND status = '$status'";
		}
		return $this->get_task_list($where);
	}

	public function get_tasks_by_report_id($report_id, $status = NULL, $equal = true, $operator = 'and'){
		$where = "WHERE report_id = '$report_id' ";
		return $this->get_task_list($where);
	}
	
	public function get_previous_tasks($report_id, $position_id = NULL){
		if($report_id){
			$report = new Report($this->connection, $report_id);
			$previous_report_id = $report->get_previous_report_id();
		} else {
			$report_manager = new ReportManager($this->connection);
			$previous_report_id = $report_manager->get_latest_report_by_position($position_id)->id;
		}
		$previous_tasks = $this->get_tasks_by_report_id($previous_report_id);
		return $previous_tasks;
	}

	private function get_task_list($where){
		$list = array();
		$query = "
			SELECT ID FROM tasks
			$where
			ORDER BY deadline ASC"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Task($this->connection, $data[ID]);
		}
		return $list;
	}
}

function get_row_class($task){
	$days_until_due = $task->days_until_due();
	if($days_until_due < 0){
		return 'critical';
	} if($days_until_due <= 7){
		return 'strong';
	} else {
		return 'normal';
	}
}
?>