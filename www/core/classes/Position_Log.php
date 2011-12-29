<?php
require_once 'DB_Table.php';
require_once 'DB_Manager.php';
require_once 'Position.php';
require_once 'Member.php';

class Position_Log extends DB_Table
{
	public $id = NULL;
	public $member_id = NULL;
	public $position_id = NULL;
	public $term = NULL;
	public $year = NULL;

	function __construct ($log_id = NULL) {	
		$this->table_name = 'position_log';
		$this->table_mapper = array(
			'id' => 'ID',
			'member_id' => 'member_id',
			'position_id' => 'position_id',
			'term' => 'term',
			'year' => 'year'
		);
		
		if($log_id){
			$params = array('id' => $log_id);
		} else {
			$params = NULL;
		}
		parent::__construct($params);
	}

}

class Position_Log_Manager extends DB_Manager
{
	function __construct() {
		parent::__construct();
	}

	public function get_logs_by_member($member_id, $term = NULL, $year = NULL){
		$where = "WHERE member_id = '$member_id'";
		if($term){
			$where .= " AND term = '$term'";
		}
		if($year){
			$where .= " AND year = '$year'";
		}
		return $this->get_logs($where, $term, $year);
	}
	
	public function get_logs_by_semester($term, $year, $board = NULL){
		$where = "WHERE term = '$term' AND year = '$year'";
		return $this->get_logs($where, $term, $year);
	}

	private function get_logs($where, $term, $year){
		$list = array();
		$query = "
			SELECT ID FROM position_log
			$where
			ORDER BY ID ASC"; //echo $query.'<br>';
		$result = $this->connection->query($query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Position_Log($data[ID], $term, $year);
		}
		return $list;
	}
}
?>