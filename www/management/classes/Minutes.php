<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB_Table.php';

class Minutes extends DB_Table {
	public static $MEETING_TYPES = array('chapter', 'exec', 'admin');
	
	public $id = NULL;
	public $meeting_date = NULL;
	public $meeting_type = NULL;
	public $formal = NULL;
	public $presiding_officer_id = NULL;
	public $old_business = NULL;
	public $new_business = NULL;
	public $unfinished_business = NULL;
	public $good_of_order = NULL;
	public $start_time = NULL;
	public $end_time = NULL;
	
	function __construct($date, $type) {
		$this->table_name = 'minutes';
		$this->table_mapper = array('id' => 'ID',
							'meeting_date' => 'meeting_date',
							'meeting_type' => 'meeting_type',
							'formal' => 'formal',
							'presiding_officer_id' => 'presiding_officer',
							'old_business' => 'old_business',
							'new_business' => 'new_business',
							'unfinished_business' => 'unfinished_business',
							'good_of_order' => 'good_of_order',
							'start_time' => 'start_time',
							'end_time' => 'end_time');
		$params = array('meeting_date' => $date, 'meeting_type' => $type);
		parent::__construct($params);
		if(!$this->meeting_date){
			$this->meeting_date = $date;
			$this->meeting_type = $type;
		}
	}
	
	function __destruct() {
		parent::__destruct();
	}
}

class Minutes_Manager extends DB {
	public function need_minutes($date){
		if($this->get_minutes($date)){
			return false;
		} else {
			return true;
		}
	}
	
	public function get_all_minutes($limit = 12){
		$where = NULL;
		$limit = "LIMIT $limit";
		return $this->get_minutes_list($where, $limit);
	}
	
	private function get_minutes_list($where, $limit = NULL){
		$list = array();
		$query = "
			SELECT meeting_date FROM minutes
			$where
			ORDER BY meeting_date DESC
			$limit"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Minutes($data[meeting_date]);
		}
		return $list;
	}
	
	public function get_minutes($date){
		$query_date = date('Y-m-d', strtotime($date));
		$query = "
			SELECT ID
			FROM minutes
			WHERE meeting_date='$query_date' 
			LIMIT 1"; //echo $query;
		$result = $this->connection->query($query);
		$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
		if($data){
			return $data[ID];
		} else {
			return NULL;
		}
	}
}
?>
