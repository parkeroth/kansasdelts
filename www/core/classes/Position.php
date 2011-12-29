<?php

require_once 'DB_Table.php';
require_once 'DB_Manager.php';

class Position extends DB_Table
{
	public static $BOARD_ARRAY = array(	'exec' => 'Execuitve',
								'internal' => 'Internal Affairs',
								'external' => 'External Affairs',
								'committee' => '');
	public $id = NULL;
	public $type = NULL;
	public $title = NULL;
	public $board = NULL;
	public $active = NULL;
	public $points = NULL;

	function __construct ($position_id = NULL, $position_str = NULL) {
		if($position_str){
			$position_id = $this->get_position_id($position_str);
		}
		
		$this->table_name = 'positions';
		$this->table_mapper = array(
			'id' => 'ID',
			'type' => 'type',
			'title' => 'title',
			'board' => 'board',
			'active' => 'active',
			'points' => 'points'
		);
		$params = array('id' => $position_id);
		parent::__construct($params);
	}

	public function __toString() {
		return $this->title.' '.$this->board;
	}
	
	// Could be renamed to somthing more descriptive
	private function clean_string($str, $sub_str){
		$sub_position = strpos($str, $sub_str);
		if($sub_position){
			$sub_length = strlen($sub_str);
			return  substr_replace($str, '', $sub_position - 1, $sub_length + 1);
		} else {
			return $str;
		}
	}
	
	private function get_position_id($str){		
		foreach(Position::$COMMITTEE_SLUGS as $slug)
			$str = $this->clean_string($str, $slug);
		$position_slug = str_replace('|', '', $str);
		
		$query = "
			SELECT ID
			FROM positions
			WHERE type = '$position_slug'
			ORDER BY ID"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		return $data[ID];
	}

}

class Position_Manager extends DB_Manager
{
	function __construct() {
		parent::__construct();
	}

	public function get_positions_by_board($board){
		$where = "WHERE board LIKE '%$board%'";
		return $this->get_position_list($where);
	}
	
	public function get_all_positions($include_committees = true){
		if(!$include_committees){
			$where  = "WHERE board != 'committee'";
		} else {
			$where = ' ';
		}
		return $this->get_position_list($where);
	}

	private function get_position_list($where){
		$list = array();
		$query = "
			SELECT ID FROM positions
			$where
			ORDER BY title ASC"; //echo $query.'<br>';
		$result = $this->connection->query($query); //echo $query;
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Position($data[ID]);
		}
		if(count($list) == 1){
			return $list[0];
		} else if(count($list) == 0){
			return NULL;
		}  else {
			return $list;
		}
	}
}
?>