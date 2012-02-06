<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB_Table.php';

/**
 * This table contains all the relevant information about a fine
 *
 * @author Parker Roth
 *
 * Schema Updated: 2011-02-05
 * 
 * 
 */
class Fine extends DB_Table {
	public static $STATUSES = array('approved', 'pending', 'rejected');
	
	public $id = NULL;
	public $amount = NULL;
	public $username = NULL;		//REMOVE after running script
	public $member_id = NULL;
	public $status = NULL;
	public $date = NULL;
	public $description = NULL;
	public $infraction_id = NULL;
	
	function __construct($fine_id) {
		$this->table_name = 'fines';
		$this->table_mapper = array('id' => 'ID',
							'amount' => 'amount',
							'member_id' => 'member_id',
							'username' => 'username',	//REMOVE after running script
							'status' => 'status',
							'date' => 'date',
							'description' => 'description',
							'infraction_id' => 'infraction_id');
		$params = array('id' => $fine_id);
		parent::__construct($params);
	}
	
	public function accept(){
		$this->status = 'approved';
		$this->save();
	}
	
	public function reject(){
		$this->status = 'rejected';
		$this->save();
	}
	
	public function insert(){
		$this->date = date('Y-m-d');
		parent::insert();
	}
	
	function __destruct() {
		parent::__destruct();
	}
}

class Fine_Manager extends DB_Manager {
	
	public function get_all($status = NULL){
		if($status){
			$where = "WHERE status = '$status'";
		} else {
			$where = "WHERE 1=1";
		}
		return $this->get_fine_list($where);
	}
	
	private function get_fine_list($where, $limit = NULL){
		$list = array();
		$query = "
			SELECT ID FROM fines
			$where
			ORDER BY date 
			$limit"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Fine($data[ID]);
		}
		return $list;
	}
	
	
}
?>
