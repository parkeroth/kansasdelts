<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/recruitment/util.php');
include_once('Member.php');
include_once('Recruit.php');

class RecruitCall
{
	private $connection = NULL;
	public $id = NULL;
	public $type = NULL;
	public $dateRequested = NULL;
	public $dateCompleted = NULL;
	public $completedBy = NULL;
	public $memberID = NULL;
	public $recruitID = NULL;
	public $eventID = NULL;
	public $status = NULL;
	public $notes = NULL;
	
	private $CALL_TYPES = array('initial' => 'Initial',
								'dinnerOut' => 'Dinner Out',
								'dinnerIn' => 'Dinner In',
								'houseVisit' => 'House Visit',
								'invite' => 'Invite' );
	
	private $CALL_STATUS = array('completed' => 'Completed',
								'leftMessage' => 'Left Message',
								'pending' => 'Pending',
								'giveUp' => 'Give Up' );
	
	public function RecruitCall($mysqli, $id = NULL) {
		$this->connection = $mysqli;
		
		if( $id != NULL){
			$callQuery = "
				SELECT *
				FROM recruitCalls
				WHERE ID = '$id'"; //echo $callQuery;
			$getCall = mysqli_query($this->connection, $callQuery);
			$callData = mysqli_fetch_array($getCall, MYSQLI_ASSOC);
			
			$this->id = $callData[ID];
			$this->type = $callData[type];
			$this->dateRequested = $callData[dateRequested];
			$this->dateCompleted = $callData[dateCompleted];
			$this->completedBy = $callData[completedBy];
			$this->memberID = $callData[memberID];
			$this->recruitID = $callData[recruitID];
			$this->eventID = $callData[eventID];
			$this->status = $callData[status];
			$this->notes = $callData[notes];
		}
	}
	
	public function saveVal($field, $val){
		$this->$field = $val;
		
		$query = "
			UPDATE recruitCalls
			SET $field = '$val'
			WHERE ID = '$this->id'"; echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
	}
	
	public function insert(){
		$query = "INSERT INTO recruitCalls 
					(type, dateRequested, dateCompleted, completedBy,
					 memberID, recruitID, eventID, status, notes)
				VALUES 
					(".make_null($this->type).", 
					 ".make_null($this->dateRequested).", 
					 ".make_null($this->dateCompleted).", 
					 ".make_null($this->completedBy).", 
					 ".make_null($this->memberID).", 
					 ".make_null($this->recruitID).", 
					 ".make_null($this->eventID).", 
					 ".make_null($this->status).", 
					 ".make_null($this->notes).")"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		$this->id = $this->connection->insert_id;
	}
	
	public function get_recruit(){
		return new Recruit($this->connection, $this->recruitID);
	}
	
	public function get_owner(){
		return new Member($this->connection, $this->memberID);
	}
	
	public function get_hist_string(){
		$query = "
			SELECT 	m.firstName as firstName, 
					m.lastName as lastName, 
					dateCompleted
			FROM recruitCalls c
			JOIN members m
			ON c.completedBy = m.username
			WHERE c.ID = '$this->id'"; //echo $query;
		$result = mysqli_query($this->connection, $query);
		$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		echo $date;
		if($this->status != 'pending'){
			$date = ' on '.date('M j, Y',strtotime($this->dateCompleted));
		} else {
			$date = '';
		}
		
		$type = $this->get_formatted_type();
		$status = $this->get_formatted_status();
		
		return $type.' - '.$data[firstName].' '.$data[lastName].$date.' ('.$status.')';
		
	}
	
	public function get_formatted_type(){
		return $this->CALL_TYPES[$this->type];
	}
	
	public function is_valid_type($val){
		return array_key_exists($val, $this->CALL_TYPES);
	}
	
	public function get_formatted_status(){
		return $this->CALL_STATUS[$this->status];
	}
	
	public function is_valid_status($val){
		return array_key_exists($val, $this->CALL_STATUS);
	}
}
	
?>