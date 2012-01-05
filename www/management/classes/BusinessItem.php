<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB_Table.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB_Manager.php';
include_once($_SERVER['DOCUMENT_ROOT'].'/recruitment/util.php');

class BusinessItem extends DB_Table
{
	public static $MEETING_TYPES = array('exec', 'admin', 'internal', 'external');
	public static $ITEM_TYPES = array('vote', 'announcement', 'survey');
	
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

	function __construct($item_id = NULL) {
		$this->table_name = 'business_items';
		$this->table_mapper = array(
			'id' => 'id',
			'title' => 'title',
			'details' => 'details',
			'item_type' => 'item_type',
			'meeting_type' => 'meeting_type',
			'meeting_date' => 'meeting_date',
			'notes' => 'notes',
			'votes_for' => 'votes_for',
			'votes_against' => 'votes_against',
			'votes_abstain' => 'votes_abstain',
			'submitted_by' => 'submitted_by'
		);
		$params = array('id' => $item_id);
		parent::__construct($params);
	}
	
	public function list_row(){
		if($this->item_type == 'vote'){
			echo '<span class="redHeading">Vote Rquired</span>';
		}
	}
}

class BusinessItemManager extends DB_Manager
{
	function __construct() {
		parent::__construct();
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