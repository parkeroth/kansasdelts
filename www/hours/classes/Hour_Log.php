<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Position.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Hour_Log
 *
 * @author Parker Roth
 */
class Hour_Log extends DB_Table {
	
	public $id = NULL;
	public $username = NULL;
	public $term = NULL;
	public $year = NULL;
	public $hours = NULL;
	public $event_id = NULL;
	protected $date_added = NULL;
	public $notes = NULL;
	
	public static $carry_over_notes = 'Semester Carry Over';
	
	function __construct($log_id) {
		$this->table_name = 'hourLog';
		$this->table_mapper = array(
			'id' => 'ID',
			'member_id' => 'member_id',
			'username' => 'username',		//Deprecated
			'term' => 'term',
			'year' => 'year',
			'hours' => 'hours',
			'type' => 'type',
			'event_id' => 'eventID',
			'date_added' => 'dateAdded',
			'notes' => 'notes'
		);
		$params = array('id' => $log_id);
		parent::__construct($params);
	}
	
	public function insert(){
		$this->date_added = date('Y-m-d');
		parent::insert();
	}
}

class Hour_Log_Manager extends DB_Manager
{
	function __construct() {
		parent::__construct();
	}
	
	//TODO: change username to member_id
	public function revert_carry_over($term, $year, $type, $username){
		$query = "
			DELETE FROM hourLog
			WHERE term = '$term'
			AND year = '$year'
			AND type = '$type'
			AND username = '$username'
			AND notes = '".Hour_Log::$carry_over_notes."'";
		//echo $query;
		$this->connect();
		$result = $this->connection->query($query);
		$this->disconnect();
	}
	
	//TODO: change username to member_id
	public function get_by_term($username, $type, $term, $year){
		$where = "WHERE username = '$username'
				AND term = '$term'
				AND year = '$year'
				AND type = '$type'";
		return $this->get_log_list($where, 100);
	}
	
	public function get_all(){
		$where = "WHERE 1=1";
		return $this->get_log_list($where);
	}
	
	private function get_log_list($where, $limit = NULL){
		$list = array();
		$query = "
			SELECT ID FROM hourLog
			$where
			ORDER BY dateAdded ASC";
		if($limit)
			$query .= "LIMIT $limit"; //echo $query.'<br>';
		$this->connect();
		$result = $this->connection->query($query);
		$this->disconnect();
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Hour_Log($data[ID]);
		}
		return $list;
	}
}

?>
