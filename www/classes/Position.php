<?php

class Position
{
	private static $COMMITTEE_SLUGS = array('admin', 'proctor', 'honorBoard');
	public static $BOARD_ARRAY = array(	'exec' => 'Execuitve',
								'internal' => 'Internal Affairs',
								'external' => 'External Affairs');
	
	private $connection = NULL;
	public $id = NULL;
	public $type = NULL;
	public $title = NULL;
	public $board = NULL;

	public function Position($mysqli, $id = NULL, $position_str = NULL) {
		$this->connection = $mysqli;
		
		if($position_str)
			$id = $this->get_position_id($position_str);
		
		if($id){
			$query = "
				SELECT *
				FROM positions
				WHERE ID = '$id'"; //echo $query.'<br>';
			$result = mysqli_query($this->connection, $query);
			$data = mysqli_fetch_array($result, MYSQLI_ASSOC);

			$this->id = $data[ID];
			$this->type = $data[type];
			$this->title = $data[title];
			$this->board = $data[board];
		}	
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