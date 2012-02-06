<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB_Table.php';

define("TABLE", "punishments");

/**
 * This table contains all the relevant information about a punishment set by the SAA for a common offence
 *
 * @author Parker Roth
 *
 * Schema Updated: 2011-02-05
 * 
CREATE TABLE IF NOT EXISTS `punishments` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `offenceNum` int(5) NOT NULL,
  `type` varchar(20) NOT NULL,
  `fine` int(10) NOT NULL,
  `hours` int(5) NOT NULL,
  `hourType` varchar(20) NOT NULL,
  `suspension` varchar(20) NOT NULL,
  `expel` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ;
 * 
 */
class Punishment extends DB_Table {
	public static $PUNISHMENT_TYPES = array('missedDaily', 'missedCleaning', 'unexcusedChapter', 'missedBaddDuty','missedTailgateDuty');
	public static $HOUR_TYPES = array('houseHours', 'serviceHours');
	
	public $id = NULL;
	public $offence_num = NULL;
	public $type = NULL;
	public $fine = NULL;
	public $hours = NULL;
	public $hour_type = NULL;
	public $suspension = NULL;
	public $expel = NULL;
	
	function __construct($punishment_id) {
		$this->table_name = TABLE;
		$this->table_mapper = array('id' => 'ID',
							'offence_num' => 'offenceNum',
							'type' => 'type',
							'fine' => 'fine',
							'hours' => 'hours',
							'hour_type' => 'hourType',
							'suspension' => 'suspension',
							'expel' => 'expel');
		$params = array('id' => $punishment_id);
		parent::__construct($params);
	}
	
	function __destruct() {
		parent::__destruct();
	}
}

class Punishment_Manager extends DB_Manager {
	
	public function get_by_type($type, $offence_num = NULL){
		$where = "WHERE type = '$type'";
		if($offence_num)
			$where .= " AND offenceNum = '$offence_num'";
		return $this->get_punishment_list($where);
	}
	
	private function get_punishment_list($where, $limit = NULL){
		$list = array();
		$query = "
			SELECT ID FROM ".TABLE."
			$where
			ORDER BY offenceNum 
			$limit"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Punishment($data[ID]);
		}
		return $list;
	}
	
	
}
?>
