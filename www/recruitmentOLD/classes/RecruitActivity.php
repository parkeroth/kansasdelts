<?php
include_once('Recruit.php');
include_once('RecruitCall.php');

class RecruitActivity
{
	private $connection = NULL;
	public $id = NULL;
	public $recruitID = NULL;
	public $callID = NULL;
	public $type = NULL;
	public $status = NULL;
	public $time = NULL;
	public $date = NULL;
	public $location = NULL;
	public $notes = NULL;
	
	public function RecruitActivity($mysqli, $id) {
		$this->connection = $mysqli;
		
		if($id != NULL){
			$query = "
				SELECT *
				FROM recruitLog
				WHERE ID = '$id'"; //echo $query;
			$result = mysqli_query($this->connection, $query);
			$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
			
			$this->id = $data[ID];
			$this->recruitID = $data[recruitID];
			$this->callID = $data[callID];
			$this->type = $data[type];
			$this->status = $data[status];
			$this->time = $data[time];
			$this->date = $data[date];
			$this->location = $data[location];
			$this->notes = $data[notes];
		}
	}
	
	public function saveVal($field, $val){
		$this->$field = $val;
		$query = "
			UPDATE recruits
			SET $field = '$val'
			WHERE ID = '$this->id'"; echo $query;
		$result = mysqli_query($this->connection, $query);
	}
	
	public function insert(){
		$query = 	"INSERT INTO recruitLog 
						(type, time, date, location, recruitID, callID, status, notes)
					VALUES 
						('$this->type', '$this->time', '$this->date', 
						'$this->location', '$this->recruitID', '$this->callID', 
						'$this->status', '$this->notes)";
		$result = mysqli_query($mysqli, $query);
		$this->id = $this->connection->insert_id;
	}	
}
	
?>