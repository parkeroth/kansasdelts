<?php

require_once 'DB_Table.php';

class Position extends DB_Table
{
	private static $COMMITTEE_SLUGS = array('admin', 'proctor', 'honorBoard');
	public static $BOARD_ARRAY = array(	'exec' => 'Execuitve',
								'internal' => 'Internal Affairs',
								'external' => 'External Affairs');
	
	public $id = NULL;
	public $type = NULL;
	public $title = NULL;
	public $board = NULL;

	function __construct ($position_id = NULL, $position_str = NULL) {
		if($position_str){
			$position_id = $this->get_position_id($position_str);
		}
		
		$this->table_name = 'positions';
		$this->table_mapper = array(
			'id' => 'ID',
			'type' => 'type',
			'title' => 'title',
			'board' => 'board'
		);
		$params = array('ID' => $position_id);
		parent::__construct($params);
	}

	public function __toString() {
		return $this->title.' '.$this->board;
	}
	
	private function get_position_id($str){
		function clean_string($str, $sub_str){
			$sub_position = strpos($str, $sub_str);
			if($sub_position){
				$sub_length = strlen($sub_str);
				return  substr_replace($str, '', $sub_position - 1, $sub_length + 1);
			} else {
				return $str;
			}
		}
		foreach(Position::$COMMITTEE_SLUGS as $slug)
			$str = clean_string($str, $slug);
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

class PositionManager
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