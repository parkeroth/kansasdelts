<?php
include_once('RecruitCall.php');

class Recruit
{
	private $connection = NULL;
	public $id = NULL;
	public $firstName = NULL;
	public $lastName = '';
	public $dateAdded = '';
	public $currentSchool = '';
	public $hsGradYear = '';
	public $bio = '';
	public $otherContacts = '';
	public $primaryContact = '';
	public $phoneNumber = '';
	public $email = '';
	public $gpa = '';
	public $actScore = '';
	public $intendedMajor = '';
	public $interests = '';
	public $questions = '';
	public $referredBy = '';
	public $referrerInfo = '';
	public $address = '';
	public $city = '';
	public $state = '';
	public $zip = '';
	public $statusCode = '';
	
	
	public function Recruit($mysqli, $id) {
		$this->connection = $mysqli;
		
		$recruitQuery = "
			SELECT *
			FROM recruits 
			WHERE ID = '$id'"; //echo $recruitQuery;
		$getRecruit = mysqli_query($this->connection, $recruitQuery);
		$recruitArray = mysqli_fetch_array($getRecruit, MYSQLI_ASSOC);
		
		$this->id = $recruitArray[ID];
		$this->firstName = $recruitArray[firstName];
		$this->lastName = $recruitArray[lastName];
		$this->dateAdded = $recruitArray[dateAdded];
		$this->currentSchool = $recruitArray[currentSchool];
		$this->hsGradYear = $recruitArray[hsGradYear];
		$this->bio = $recruitArray[bio];
		$this->otherContacts = $recruitArray[otherContacts];
		$this->primaryContact = $recruitArray[primaryContact];
		$this->phoneNumber = $recruitArray[phoneNumber];
		$this->email = $recruitArray[email];
		$this->gpa = $recruitArray[gpa];
		$this->actScore = $recruitArray[actScore];
		$this->intendedMajor = $recruitArray[intendedMajor];
		$this->interests = $recruitArray[interests];
		$this->questions = $recruitArray[questions];
		$this->referredBy = $recruitArray[referredBy];
		$this->referrerInfo = $recruitArray[referrerInfo];
		$this->address = $recruitArray[address];
		$this->city = $recruitArray[city];
		$this->state = $recruitArray[state];
		$this->zip = $recruitArray[zip];
		$this->statusCode = $recruitArray[status];
		
	}
	
	public function saveVal($field, $val){
		$this->$field = $val;
		
		$query = "
			UPDATE recruits
			SET $field = '$val'
			WHERE ID = '$this->id'"; echo $query;
		$result = mysqli_query($this->connection, $query);
	}
	
	public function getVal($field){
		return $this->$field;
	}
	
	public function getName(){
		return $this->firstName.' '.$this->lastName;
	}
	
	public function get_phone(){
		
		$num = ereg_replace('[^0-9]', '', $this->phoneNumber); 
	
		$len = strlen($num); 
		if($len == 7) 
			$num = preg_replace('/([0-9]{3})([0-9]{4})/', '$1-$2', $num); 
		elseif($len == 10) 
			$num = preg_replace('/([0-9]{3})([0-9]{3})([0-9]{4})/', '($1) $2-$3', $num); 
	
		return $num;
	}
	
	public function getStatus(){
		$statusQuery = "
			SELECT status
			FROM recruitStatus 
			WHERE ID = '$this->statusCode'";
		$getStatus = mysqli_query($this->connection, $statusQuery);
		$statusArray = mysqli_fetch_array($getStatus, MYSQLI_ASSOC);
		
		return $statusArray[status];
	}
	
	public function last_contact_date($format = true){
		$callQuery = "
			SELECT MAX(dateCompleted) as val, id
			FROM recruitCalls 
			WHERE recruitId = '$this->id'"; //echo $callQuery;
		$getCall = mysqli_query($this->connection, $callQuery);
		$callArray = mysqli_fetch_array($getCall, MYSQLI_ASSOC);
		
		$date = $callArray[val];
		if($format){
			if($date == NULL || $date = '0000-00-00'){
				return 'Never';
			} else {
				return date('M j, Y',strtotime($date));
			}
		} else {
			return $date;
		}
	}
	
	public function last_contact_name($format = true){
		$callQuery = "
			SELECT 	m.firstName as firstName, 
					m.lastName as lastName, 
					username,
					MAX(dateCompleted) as num
			FROM recruitCalls c
			JOIN members m
			ON c.completedBy = m.username
			WHERE recruitID = '$this->id'"; //echo $callQuery;
		$getCall = mysqli_query($this->connection, $callQuery);
		$callArray = mysqli_fetch_array($getCall, MYSQLI_ASSOC);
		
		if($format){
			if($callArray[num] == NULL){
				return '';
			} else {
				return $callArray[firstName].' '.$callArray[lastName];
			}
		} else {
			return $callArray[username];
		}
	}
	
	public function get_owner($format = true){
		$query = "
			SELECT 	m.firstName as firstName, 
					m.lastName as lastName, 
					username
			FROM recruits r
			JOIN members m
			ON r.primaryContact = m.username
			WHERE r.ID = '6'"; //echo $query;
		$result = mysqli_query($this->connection, $query);
		$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		if($format){
			return $data[firstName].' '.$data[lastName];
		} else {
			return $data[username];
		}
	}
	
	public function referred_by(){
		$referredBy = $this->referredBy;
						
		if($referredBy == 'self'){
			$str = "Self";
		} else if($referredBy == 'alum'){
			$str = "<b>Alumni</b><br>";
			$str .= nl2br($this->referrerInfo);
		} else {
			$memberQuery = "
				SELECT firstName, lastName
				FROM members
				WHERE username = '$referredBy'";
			$getMember = mysqli_query($this->connection, $memberQuery);
			$memberArray = mysqli_fetch_array($getMember, MYSQLI_ASSOC);
			
			$str = $memberArray[firstName].' '.$memberArray[lastName];
		}
		
		return $str;
	}
	
	public function get_calls($status = NULL){
		$calls = array();
		
		if($status != NULL){
			$where = "AND status = '$status'";
		} else {
			$where = '';
		}
		
		$query = "
			SELECT ID
			FROM recruitCalls
			WHERE recruitID = $this->id
			".$query."
			ORDER BY dateCompleted DESC
			"; //echo $query;
		$result = mysqli_query($this->connection, $query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$calls[] = new RecruitCall($this->connection, $data[ID]);
		}
		return $calls;
	}
}
	
?>