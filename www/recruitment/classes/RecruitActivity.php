<?php
include_once('Recruit.php');
include_once('RecruitCall.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/recruitment/util.php');

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
						(".make_null($this->type).", 
						 ".make_null($this->time).", 
						 ".make_null($this->date).", 
						 ".make_null($this->location).", 
						 ".make_null($this->recruitID).", 
						 ".make_null($this->callID).", 
						 ".make_null($this->status).", 
						 ".make_null($this->notes).")"; //echo $query;
		$result = mysqli_query($this->connection, $query);
		$this->id = $this->connection->insert_id;
	}	
}
	
?>