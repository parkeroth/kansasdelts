<?php
require_once 'Report.php';

class Meeting
{
	private $connection = NULL;
	public $meeting_date = NULL;
	public $board = NULL;
	
	public function Meeting($mysqli, $meeting_date = NULL, $board = NULL){
		$this->connection = $mysqli;
		$this->meeting_date = $meeting_date;
		$this->board = $board;
	}
	
	public function has_been_processed(){
		$report_manager = new ReportManager($this->connection);
		$report_list = $report_manager->get_reports_by_date_board($this->meeting_date, $this->board);
		
		$processed = true;
		foreach($report_list as $report){
			if($report->status == 'pending'){
				$processed = false;
			}
		}
		
		if($processed){
			return true;
		} else {
			return false;
		}
	}
}

class MeetingManager
{
	private $connection = NULL;

	public function MeetingManager($mysqli) {
		$this->connection = $mysqli;
	}
	
	//TODO: This is kinda janky
	public function get_meetings($board_slug){
		$where = "WHERE position_id IN (
					SELECT ID
					FROM positions
					WHERE board = '$board_slug')";
		return $this->get_meeting_list($where);
	}
	
	public function get_chapters(){
		$where = 'WHERE agenda IS NOT NULL';
	}
	
	public function get_latest_date(){
		$query = "
			SELECT MAX(meeting_date) AS meeting_date 
			FROM reports
			WHERE agenda IS NOT NULL
			LIMIT 1"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		return $data[meeting_date];
	}

	private function get_meeting_list($where){
		$list = array();
		$query = "
			SELECT DISTINCT meeting_date FROM reports
			$where
			ORDER BY meeting_date DESC
			LIMIT 20"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Meeting($this->connection, $data[meeting_date], $data[board]);
		}
		return $list;
	}
}
?>