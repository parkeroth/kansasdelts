<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/util.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB_Table.php';

/**
 * This table holds the information related to a single offence for a punishment set in the punishments table
 *
 * @author Parker Roth
 *
 * Schema Updated: 2011-02-02
 * 
CREATE TABLE IF NOT EXISTS `infractionlog` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `offender` varchar(6) NOT NULL,
  `reporter` varchar(6) NOT NULL,
  `type` varchar(50) NOT NULL,
  `dateReported` date NOT NULL,
  `dateOccured` date NOT NULL,
  `status` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `numOccurance` int(4) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=113 ;
 * 
 */
class Infraction_Log extends DB_Table {
	public static $INFRACTION_STATUS = array('approved', 'reverted');
	public static $INFRACTION_TYPES = array(	'missedDaily' => 'Missed Daily',
									'missedCleaning' => 'Missed Cleanings',
									'unexcusedChapter' => 'Unexcused Chapter Absence',
									'missedBaddDuty' => 'Missed BADD Duty',
									'missedTailgateDuty' => 'Missed Tailgate Duty');
	
	public $id = NULL;
	public $offender_id = NULL;
	public $reporter_id = NULL;
	public $offender = NULL;			//REMVOE Deprecated
	public $reporter = NULL;			//REMVOE Deprecated
	public $type = NULL;
	public $date_reported = NULL;
	public $date_occured = NULL;
	public $status = NULL;
	public $description = NULL;
	public $num_occurance = NULL;		//REMOVE Deprecated
	public $meeting_id = NULL;
	
	function __construct($record_id) {
		$this->table_name = 'infractionLog';
		$this->table_mapper = array('id' => 'ID',
							'offender_id' => 'offender_id',
							'reporter_id' => 'reporter_id',
							'offender' => 'offender',
							'reporter' => 'reporter',
							'type' => 'type',
							'date_reported' => 'dateReported',
							'date_occured' => 'dateOccured',
							'status' => 'status',
							'description' => 'description',
							'num_occurance' => 'numOccurance',
							'meeting_id' => 'meeting_id');
		$params = array('id' => $record_id);
		parent::__construct($params);
	}
	
	public function insert(){
		$this->date_reported = date('Y-m-d');
		$this->status = 'pending';
		parent::insert();
	}
	
	function __destruct() {
		parent::__destruct();
	}
}

class Infraction_Log_Manager extends DB_Manager {
	
	public function get_all(){
		$where = "WHERE 1 = 1 ";
		return $this->get_log_list($where);
	}
	
	public function get_by_meeting_id($meeting_id, $offender_id = NULL){
		$where = "WHERE meeting_id = '$meeting_id'";
		if($offender_id)
			$where .= " AND offender_id = '$offender_id'";
		return $this->get_log_list($where);
	}
	
	public function get_by_offender($offender_id, $type = NULL, $sem = NULL){
		$where = "WHERE offender_id = '$offender_id'";
		if($type)
			$where .= " AND type = '$type'";
		if($sem){
			$where .= " AND dateOccured BETWEEN ".$sem->get_start_date()." AND ".$sem->get_end_date();
		}
		return $this->get_log_list($where);
	}
	
	private function get_log_list($where, $limit = NULL){
		$list = array();
		$query = "
			SELECT ID FROM infractionLog
			$where
			ORDER BY dateOccured 
			$limit"; echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Infraction_Log($data[ID]);
		}
		return $list;
	}
	
	
}
?>
