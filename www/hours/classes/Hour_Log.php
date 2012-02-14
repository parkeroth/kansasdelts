<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Position.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/util.php';

/**
 * Description of Hour_Log
 *
 * @author Parker Roth
 * 
 * CREATE TABLE IF NOT EXISTS `hourLog` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `term` set('fall','spring') NOT NULL,
  `year` int(4) NOT NULL,
  `hours` int(4) NOT NULL,
  `type` varchar(20) NOT NULL,
  `eventID` int(11) DEFAULT NULL,
  `dateAdded` date NOT NULL,
  `notes` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1711 ;
 * 
 */
class Hour_Log extends DB_Table {
	
	public $id = NULL;
	public $member_id = NULL;
	public $type = NULL;
	public $term = NULL;
	public $year = NULL;
	public $hours = NULL;
	public $event_id = NULL;
	protected $date_added = NULL;
	public $notes = NULL;
	
	public static $carry_over_notes = 'Semester Carry Over';
	public static $HOUR_TYPES = array(	'house' => 'House Hours', 
								'service' => 'Service Hours',
								'philanthropy' => 'Philanthropy Events');
	
	function __construct($log_id) {
		$this->table_name = 'hourLog';
		$this->table_mapper = array(
			'id' => 'ID',
			'member_id' => 'member_id',
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
	
	public function get_date_added(){
		return date('M j, Y', strtotime($this->date_added));
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
	public function revert_carry_over($term, $year, $type, $member_id){
		$query = "
			DELETE FROM hourLog
			WHERE term = '$term'
			AND year = '$year'
			AND type = '$type'
			AND member_id = '$member_id'
			AND notes = '".Hour_Log::$carry_over_notes."'";
		//echo $query;
		$this->connect();
		$result = $this->connection->query($query);
		$this->disconnect();
	}
	
	//TODO: change username to member_id
	public function get_by_term($member_id, $type, $term, $year){
		$where = "WHERE member_id = '$member_id'
				AND term = '$term'
				AND year = '$year'
				AND type = '$type'";
		return $this->get_log_list($where, 100);
	}
	
	public function get_all(){
		$where = "WHERE 1=1";
		return $this->get_log_list($where);
	}
	
	public function get_total($type, Semester $sem, $member_id = NULL){
		$total = 0;
		$where = "WHERE type = '$type' AND term = '$sem->term' AND year = '$sem->year'";
		if($member_id != NULL)
			$where .= " AND member_id = '$member_id'";
		$list = $this->get_log_list($where);
		foreach($list as $record){
			$total  += $record->hours;
		}
		return $total;
	}
	
	private function get_log_list($where, $limit = NULL){
		$list = array();
		$query = "
			SELECT ID FROM hourLog
			$where
			ORDER BY dateAdded ASC";
		if($limit)
			$query .= " LIMIT $limit"; //echo $query.'<br>';
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
