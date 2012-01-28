<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB_Table.php';

class Minutes extends DB_Table {
	public static $MEETING_TYPES = array('chapter', 'exec', 'internal', 'external');
	
	public $id = NULL;
	public $meeting_id = NULL;
	public $formal = NULL;
	public $presiding_officer_id = NULL;
	public $old_business = NULL;
	public $new_business = NULL;
	public $unfinished_business = NULL;
	public $good_of_order = NULL;
	public $start_time = NULL;
	public $end_time = NULL;
	
	function __construct($minutes_id) {
		$this->table_name = 'minutes';
		$this->table_mapper = array('id' => 'ID',
							'meeting_id' => 'meeting_id',
							'formal' => 'formal',
							'presiding_officer_id' => 'presiding_officer',
							'old_business' => 'old_business',
							'new_business' => 'new_business',
							'unfinished_business' => 'unfinished_business',
							'good_of_order' => 'good_of_order',
							'start_time' => 'start_time',
							'end_time' => 'end_time');
		$params = array('id' => $minutes_id);
		parent::__construct($params);
	}
	
	function __destruct() {
		parent::__destruct();
	}
}

class Minutes_Manager extends DB {
	
	public function get_by_meeting($meeting_id){
		$where  = "WHERE meeting_id = '$meeting_id'";
		$list = $this->get_minutes_list($where);
		if($list){
			return $list[0];
		} else {
			return NULL;
		}
	}
	
	public function get_all_minutes($board, $limit = 12){
		$where = NULL;
		$limit = "LIMIT $limit";
		return $this->get_minutes_list($where, $limit);
	}
	
	private function get_minutes_list($where, $limit = NULL){
		$list = array();
		$query = "
			SELECT ID FROM minutes
			$where
			ORDER BY meeting_id DESC
			$limit"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Minutes($data[ID]);
		}
		return $list;
	}
	
	
}
?>
