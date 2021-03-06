<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB_Table.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB_Manager.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Position_Log.php';
include_once($_SERVER['DOCUMENT_ROOT'].'/recruitment/util.php');
require_once 'Report.php';
require_once 'Task.php';
require_once 'Meeting.php';

/**
 * This table contains all the relavent information about a weekly task. Each task is associated with a weekly 
 * report. The tasks state is determined by the status and progress variables.
 * 
 * @author Parker Roth
 *
 * Schema Updated: 2011-01-16
 * 
CREATE TABLE IF NOT EXISTS `tasks` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `position_id` int(11) NOT NULL,
  `deadline` date DEFAULT NULL,
  `status` set('new','proposed','committed','closed') NOT NULL,
  `progress` set('not-stated','in-progress','blocked','completed','cancelled') DEFAULT NULL,
  `priority` set('low','normal','high','critical') NOT NULL,
  `last_updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `notes` text,
  `report_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;
 * 
 */
class Task extends DB_Table
{
	public static $TASK_STATUS = array('new', 'proposed', 'committed', 'closed');
	public static $TASK_PROGRESS = array(	'not-started' => 'Not Started',
									'in-progress' => 'In Progress',
									'blocked' => 'Blocked',
									'completed' => 'Completed',
									'cancelled' => 'Cancelled');
	public static $TASK_PRIORITY = array('low', 'normal', 'high', 'critical');
	
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

	function __construct($id = NULL) {
		$this->table_name = 'tasks';
		$this->table_mapper = array(
			'id' => 'ID',
			'title' => 'title',
			'position_id' => 'position_id',
			'deadline' => 'deadline',
			'status' => 'status',
			'progress' => 'progress',
			'priority' => 'priority',
			'last_updated' => 'last_updated',
			'notes' => 'notes',
			'report_id' => 'report_id'
		);
		$params = array('id' => $id);
		parent::__construct($params);
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
		} else if($this->progress == 'not-started' || $this->progress == NULL){
			return 'progress-red';
		} else if($this->progress == 'cancelled') {
			return 'progress-grey';
		}
	}
	
	public function get_row_class(){
		$days_until_due = $this->days_until_due();
		if($this->status == 'committed'){
			return 'committed';
		} else {
			if($days_until_due < 0){
				return 'critical';
			} if($days_until_due <= 7){
				return 'strong';
			} else {
				return 'normal';
			}
		}
	}
	
	public function can_edit($member_id, $term = NULL, $year = NULL){
		$position_log_manager = new Position_Log_Manager();
		if(!$term){
			$month = date('n');
			if($month < 8){
				$term = 'spring';
			} else {
				$term = 'fall';
			}
		}
		if(!$year){
			$year = date('Y');
		}
		
		if($position_log_manager->member_in_committee($member_id, $this->position_id, $term, $year) ||
		   $position_log_manager->member_in_committee($member_id, 33, $term, $year)){
			return true;
		} else {
			return false;
		}
	}
}

class TaskManager extends DB_Manager
{
	function __construct() {
		parent::__construct();
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
	
	public function get_previous_tasks($meeting_id, $position_id){
		$meeting_manager = new Meeting_Manager();
		$report_manager = new ReportManager();
		$previous_meeting = $meeting_manager->get_previous_meeting($meeting_id);
		$report_list = $report_manager->get_reports_by_meeting($previous_meeting->id, $position_id);
		$previous_report = $report_list[0];
		$previous_tasks = $this->get_tasks_by_report_id($previous_report->id);
		return $previous_tasks;
	}

	private function get_task_list($where){
		$list = array();
		$query = "
			SELECT ID FROM tasks
			$where
			ORDER BY deadline ASC"; //echo $query.'<br>';
		$this->connect();
		$result = $this->connection->query($query);
		$this->disconnect();
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Task($data[ID]);
		}
		return $list;
	}
}
?>