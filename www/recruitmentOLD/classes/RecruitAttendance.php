<?php
include_once('Recruit.php');
include_once('RecruitCall.php');

class RecruitAttendance
{
	private $connection = NULL;
	public $id = NULL;
	public $recruitID = NULL;
	public $eventID = NULL;
	public $status = NULL;
	
	public function RecruitAttendance($mysqli, $eventID, $recruitID) {
		$this->connection = $mysqli;
		
		if($eventID == NULL || $recruitID == NULL){
			$query = "
				SELECT *
				FROM recruitAttendance
				WHERE eventID = '$eventID'
				AND recruitID = '$recruitID'"; //echo $query;
			$result = mysqli_query($this->connection, $query);
			$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
			
			$this->id = $data[ID];
			$this->recruitID = $data[recruitID];
			$this->eventID = $data[eventID];
			$this->status = $data[status];
		}
	}
	
	public function saveVal($field, $val){
		$this->$field = $val;
		$query = "
			UPDATE recruitAttendance
			SET $field = '$val'
			WHERE ID = '$this->id'"; echo $query;
		$result = mysqli_query($this->connection, $query);
	}
	
	public function insert(){
		$query = 	"INSERT INTO recruitAttendance 
						(recruitID, eventID, status)
					VALUES 
						('$this->recruitID', '$this->eventID', '$this->status')";
		$result = mysqli_query($mysqli, $query);
		$this->id = $this->connection->insert_id;
	}	
}
	
?>