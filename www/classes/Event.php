<?php

require_once 'DB_Table.php';
require_once 'DB_Manager.php';

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
							'max_attendance' => 'maxAttendance',
							'sent_invites' => 'sentInvites');
		$params = array('id' => $event_id);
		parent::__construct($params);
	}
	
	function __destruct() {
		parent::__destruct();
	}
}

class EventManager extends DB_Manager
{
	function __construct() {
		parent::__construct();
	}
	
	public function get_events_by_date($date){
		$where = "WHERE eventDate = '$date'";
		return $this->get_list($where);
	}
	
	public function test(){
		echo 'test';
	}
	
	protected function get_list($where){
		$list = array();
		$query = "
			SELECT ID FROM events
			$where
			ORDER BY time ASC"; //echo $query.'<br>';
		$result = $this->connection->query($query); //echo $query;
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Event($data[ID]);
		}
		return $list;
	}

	function __destruct() {
		parent::__destruct();
	}
}
?>
