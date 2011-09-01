<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/recruitment/util.php');

class BusinessItem
{
	public static $MEETING_TYPES = array('exec', 'admin', 'internal', 'external');
	public static $ITEM_TYPES = array('vote', 'announcement', 'survey');
	
	private $connection = NULL;
	public $id = NULL;
	public $title = NULL;
	public $details = NULL;
	public $item_type = NULL;
	public $meeting_type = NULL;
	public $meeting_date = NULL;
	public $notes = NULL;
         public $votes_for = NULL;
         public $votes_against = NULL;
	public $votes_abstain = NULL;
	public $submitted_by = NULL;

	public function BusinessItem($mysqli, $id = NULL) {
		$this->connection = $mysqli;

		if($id != NULL){
			$query = "
				SELECT *
				FROM business_items
				WHERE id= '$id'"; //echo $query.'<br>';
			$result = mysqli_query($this->connection, $query);
			$data = mysqli_fetch_array($result, MYSQLI_ASSOC);

			$this->id = $data[ID];
			$this->title = $data[title];
			$this->details = $data[details];
			$this->item_type = $data[item_type];
			$this->meeting_type = $data[meeting_type];
			$this->meeting_date = $data[meeting_date];
			$this->notes = $data[notes];
			$this->votes_for = $data[votes_for];
			$this->votes_against = $data[votes_against];
			$this->votes_abstain = $data[votes_abstain];
			$this->submitted_by = $data[submitted_by];
		}
	}

	public function insert(){
		$query = "INSERT INTO business_items
					(title, details, item_type,
					 meeting_date, meeting_type, submitted_by)
				VALUES
					(".make_null($this->title).",
					 ".make_null($this->details).",
					 ".make_null($this->item_type).",
					 ".make_null($this->meeting_date).",
					 ".make_null($this->meeting_type).",
					 ".make_null($this->submitted_by).")"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		$this->id = $this->connection->insert_id;
	}

	public function saveVal($field, $val){
		if($val == NULL){
			$val = 'NULL';
		} else {
			$val = "'$val'";
		}
		
		$this->$field = $val;
		$query = "
			UPDATE business_items
			SET $field = $val
			WHERE id = '$this->id'"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
	}

	public function delete(){
		$query = "
			DELETE FROM business_items
			WHERE id = '$this->id'"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
	}
	
	public function list_row(){
		if($this->item_type == 'vote'){
			echo '<span class="redHeading">Vote Rquired</span>';
		}
	}
}

class BusinessItemManager
{
	private $connection = NULL;

	public function BusinessItemManager($mysqli) {
		$this->connection = $mysqli;
	}
	
	public function get_items_by_meeting_date_type($date, $type){
		$meeting_date = date('Y-m-d', strtotime($date));
		$where = " WHERE meeting_date = '$meeting_date' ";
		$where .= "AND meeting_type = '$type'" ;
		if($type == 'internal' || $type == 'external')
			$where .= "OR meeting_type = 'admin' ";
		return $this->get_item_list($where);
	}
	
	private function get_item_list($where){
		$list = array();
		$query = "
			SELECT id FROM business_items
			$where"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new BusinessItem($this->connection, $data[id]);
		}
		return $list;
	}
}
?>