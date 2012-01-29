<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Position.php';
require_once 'Report.php';

/**
 * This table serves to connect all the information about a meeting. This includes:
 *	Attendance Records
 *	Minutes
 *	Reports -> Tasks
 *
 * @author Parker Roth
 *
 * Schema Updated: 2011-01-29
 * 
CREATE TABLE IF NOT EXISTS `meetings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `type` set('chapter','exec','internal','external') NOT NULL,
  `chapter_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=59 ;
 * 
 */
class Meeting extends DB_Table
{
	public static $MEETING_TYPES = array('chapter', 'exec', 'internal', 'external');
	public static $PRESIDING_OFFICERS = array('chapter' => 'pres',
									'exec' => 'pres',
									'internal' => 'vpInternal',
									'external' => 'vpExternal');
	
	public $id = NULL;
	public $date = NULL;
	public $type = NULL;
	public $chapter_id = NULL;

	function __construct($meeting_id) {
		$this->table_name = 'meetings';
		$this->table_mapper = array(
			'id' => 'id',
			'date' => 'date',
			'type' => 'type',
			'chapter_id' => 'chapter_id'
		);
		$params = array('id' => $meeting_id);
		parent::__construct($params);
	}
	
	public function create_reports(){
		$report_manager = new ReportManager();
		$position_manager = new Position_Manager();
		$position_list = $position_manager->get_positions_by_board($this->type);
		foreach($position_list as $position){
			$report = $report_manager->get_reports_by_meeting($this->id, $position->id);
			if(!$report){
				$report = new Report();
				$report->meeting_id = $this->id;
				$report->position_id = $position->id;
				$report->status = 'blank';
				$report->insert();
			}
		}
	}
	
	public function has_past(){
		$cur_date = date('Y-m-d');
		if($this->date < $cur_date){
			return true;
		} else {
			return false;
		}
	}
	
	public function can_remove(){
		if($this->has_past() || $this->chapter_id){
			return false;
		} else {
			$report_manager = new ReportManager();
			$report_list = $report_manager->get_reports_by_meeting($this->id);
			foreach($report_list as $report){
				if(!$report->can_delete())
					return false;
			}
			return true;
		}
	}
	
	function delete(){
		$report_manager = new ReportManager();
		$report_list  =$report_manager->get_reports_by_meeting($this->id);
		foreach($report_list as $report){
			$report->delete();
		}
		parent::delete();
	}
	
}

class Meeting_Manager extends DB_Manager
{
	function __construct() {
		parent::__construct();
	}
	
	public function get_meetings_by_type($type, $limit = NULL, $date = NULL){
		// Check if valid type
		if(!in_array($type, Meeting::$MEETING_TYPES)){
			//error
			return NULL;
		} else {
			$where = "WHERE type = '$type'";
			if($date){
				$month = date('n', strtotime($date));
				$year = date('Y', strtotime($date));
				if($month < 8){
					$start = "$yaear-01-01";
					$end = "$year-08-01";
				} else {
					$start = "$year-08-01";
					$year++;
					$end = "$year-01-01";
				}
				$where .= " AND date >= '$start' AND date < '$end'";
			}
			return $this->get_meeting_list($where, $limit);
		}
	}
	
	public function get_meeting($type, $date){
		$where = "WHERE type = '$type' AND date = '$date'";
		return $this->get_meeting_list($where);
	}
	
	public function get_next_meeting($board, $date = NULL){
		if(!$date)
			$date = date('Y-m-d');
		$date_plus_week = date('Y-m-d', strtotime('+1 week', strtotime($date)));
		$query = "
			SELECT id FROM meetings
			WHERE date >= '$date'
			AND date < '$date_plus_week'
			AND type = '$board'
			ORDER BY date ASC
			LIMIT 1";
		$result = $this->connection->query($query); //echo $query;
		$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
		if($data){
			return new Meeting($data[id]);
		} else {
			return NULL;
		}
	}
	
	public function get_meetings_missing_report($position_id){
		$list = array();
		$report_manager = new  ReportManager();
		$position = new Position($position_id);
		$meeting_list = $this->get_meetings_by_type($position->board);
		foreach($meeting_list as $meeting){
			$report = $report_manager->get_reports_by_meeting($meeting->id, $position_id);
			if(!$report){
				array_push($list, $meeting);
			}
		}
		return array_reverse($list);
	}
	
	public function get_previous_meeting($meeting_id){
		$current_meeting = new Meeting($meeting_id);
		$query = "
			SELECT id FROM meetings
			WHERE date < '$current_meeting->date'
			ORDER BY date DESC
			LIMIT 1";
		$result = $this->connection->query($query); //echo $query;
		if($result){
			$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
			return new Meeting($data[id]);
		} else {
			return NULL;
		}
	}
	
	private function get_meeting_list($where, $limit = NULL){
		if(!$limit){
			$limit = 20;
		}
		$list = array();
		$query = "
			SELECT id FROM meetings
			$where
			ORDER BY date DESC
			LIMIT $limit"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Meeting($data[id]);
		}
		return $list;
	}
}
?>