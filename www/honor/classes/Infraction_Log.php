<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/util.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB_Table.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/finance/classes/Fine.php';
include_once 'Punishment.php';

/**
 * This table holds the information related to a single offence for a punishment set in the punishments table
 *
 * @author Parker Roth
 *
 * Schema Updated: 2011-02-05
 * 
CREATE TABLE IF NOT EXISTS `infractionLog` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `offender_id` int(11) NOT NULL,
  `reporter_id` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `dateReported` date NOT NULL,
  `dateOccured` date NOT NULL,
  `status` set('approved','reverted','rejected','pending') NOT NULL,
  `description` text,
  `meeting_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=119 ;
 * 
 */
class Infraction_Log extends DB_Table {
	public static $INFRACTION_STATUS = array('approved', 'reverted', 'rejected', 'pending');
	public static $INFRACTION_TYPES = array(	'missedDaily' => 'Missed Daily',
									'missedCleaning' => 'Missed Cleanings',
									'unexcusedChapter' => 'Unexcused Chapter Absence',
									'missedBaddDuty' => 'Missed BADD Duty',
									'missedTailgateDuty' => 'Missed Tailgate Duty');
	
	public $id = NULL;
	public $offender_id = NULL;
	public $reporter_id = NULL;
	public $type = NULL;
	public $date_reported = NULL;
	public $date_occured = NULL;
	public $status = NULL;
	public $description = NULL;
	public $meeting_id = NULL;
	
	function __construct($record_id) {
		$this->table_name = 'infractionLog';
		$this->table_mapper = array('id' => 'ID',
							'offender_id' => 'offender_id',
							'reporter_id' => 'reporter_id',
							'type' => 'type',
							'date_reported' => 'dateReported',
							'date_occured' => 'dateOccured',
							'status' => 'status',
							'description' => 'description',
							'meeting_id' => 'meeting_id');
		$params = array('id' => $record_id);
		parent::__construct($params);
	}
	
	public function insert(){
		$this->date_reported = date('Y-m-d');
		$this->status = 'pending';
		parent::insert();
	}
	
	public function apply_punishment(){
		$punishment = $this->get_punishment();
		$offender = new Member($this->offender_id);
		if($punishment->fine > 0)
		{
			$fine = new Fine();
			$fine->amount = $punishment->fine;
			$fine->member_id = $this->offender_id;
			$fine->date = $this->date_occured;
			$fine->description = Infraction_Log::$INFRACTION_TYPES[$this->type].": $occurance_num";
			$fine->insert();
		}

		// Apply Hours to account
		if($punishment->hours > 0)
		{
			$query = "	INSERT INTO hourLog (username, term, year, hours, type, eventID, dateAdded, notes)
						VALUES ('$offender->username', '$sem->term', '$sem->year', '-$punishment->hours', '$punishment->hour_type',
								'0', '$this->date_occured', '".Infraction_Log::$INFRACTION_TYPES[$this->type].": $occurance_num')";
			$result = $this->connection->query($query);
		}
		if($punishment->suspension != 'none' && $punishment->suspension != 'NULL')
		{
			echo "Apply suspension<br>";
		}
		if($punishment->expel)
		{
			echo "Apply expel<br>";
		}	

		$this->status = 'approved';
		$this->save();
	}
	
	public function revert_punishment(){
		$punishment = $this->get_punishment();
		$offender = new Member($this->offender_id);
		
		if($punishment->fine > 0)
		{
			$fine = new Fine();
			$fine->amount = $punishment->fine * -1;
			$fine->member_id = $this->offender_id;
			$fine->date = $this->date_occured;
			$fine->description = "SAA Correction";
			$fine->insert();
		}
		if($punishment->hours > 0)
		{
			$query = "	INSERT INTO hourLog (username, term, year, hours, type, eventID, dateAdded, notes)
						VALUES ('$offender->username', '$sem->term', '$sem->year', '$punishment->hours', '$punishment->hour_type',
								'0', '$this->date_occured', 'SAA Correction')";
			$result = $this->connection->query($query);
		}

		$this->status = 'reverted';
		$this->save();
	}
	
	public function get_punishment(){
		$occurance_num = $this->get_occurance_num();
		$punishment_manager = new Punishment_Manager();
		$list = $punishment_manager->get_by_type($infraction->type, $offence_num);
		return $list[0];
	}
	
	public function get_occurance_num(){
		$sem = new Semester($this->date_occured);
		$occurances = 0;
		$infraction_manager = new Infraction_Log_Manager();
		$infraction_list = $infraction_manager->get_by_offender($this->offender_id, $this->type, $sem);
		foreach($infraction_list as $record){
			if($record->status == 'approved')
				$occurances++;
		}
		return $occurances + 1;
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
	
	public function get_by_offender($offender_id, $type = NULL, $sem = NULL, $status = NULL){
		$where = "WHERE offender_id = '$offender_id'";
		if($type)
			$where .= " AND type = '$type'";
		if($sem){
			$where .= " AND dateOccured BETWEEN '".$sem->get_start_date()."' AND '".$sem->get_end_date()."'";
		}
		if($status){
			$where .= " AND status = '$status'";
		}
		return $this->get_log_list($where);
	}
	
	public function get_pending($type = NULL){
		$where = "WHERE status = 'pending'";
		if($type)
			$where .= " AND type = '$type'";
		return $this->get_log_list($where);
	}
	
	private function get_log_list($where, $limit = NULL){
		$list = array();
		$query = "
			SELECT ID FROM infractionLog
			$where
			ORDER BY dateOccured 
			$limit"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Infraction_Log($data[ID]);
		}
		return $list;
	}
	
	
}
?>
