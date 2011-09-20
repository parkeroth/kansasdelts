<?php

require_once 'DB_Table.php';

class Event extends DB_Table {
	public $id = NULL;
	public $title = NULL;
	public $description = NULL;
	public $date_added = NULL;
	public $date_awarded = NULL;
	public $event_date = NULL;
	public $term = NULL; //TODO: get rid of this field in database DONT USE!
	public $time = NULL;
	public $type = NULL;
	public $mandatory = NULL;
	public $max_attendance = NULL;
	
	function __construct($event_id = NULL) {
		$this->table_name = 'events';
		$this->table_mapper = array('id' => 'ID',
							'title' => 'title',
							'description' => 'description',
							'date_added' => 'dateAdded',
							'date_awarded' => 'dateAwarded',
							'event_date' => 'eventDate',
							'term' => 'term',
							'time' => 'time',
							'type' => 'type',
							'mandatory' => 'mandatory',
							'max_attendance' => 'maxAttendance');
		$params = array('id' => $event_id);
		parent::__construct($params);
	}
	
	function __destruct() {
		parent::__destruct();
	}
}

class EventManager
{
	private $connection = NULL;

	public function PositionManager($mysqli) {
		$this->connection = $mysqli;
	}

	public function get_positions_by_board($board){
		$where = "WHERE board LIKE '%$board%'";
		return $this->get_position_list($where);
	}

	private function get_position_list($where){
		$list = array();
		$query = "
			SELECT ID FROM positions
			$where
			ORDER BY ID ASC"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Position($this->connection, $data[ID]);
		}
		return $list;
	}
}
?>
