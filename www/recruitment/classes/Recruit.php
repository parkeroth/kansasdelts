<?php
include_once('RecruitCall.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/recruitment/util.php');

class Recruit
{
	private $connection = NULL;
	public $id = NULL;
	public $firstName = NULL;
	public $lastName = NULL;
	public $dateAdded = NULL;
	public $currentSchool = NULL;
	public $hsGradYear = NULL;
	public $bio = NULL;
	public $otherContacts = NULL;
	public $primaryContact = NULL;
	public $phoneNumber = NULL;
	public $email = NULL;
	public $gpa = NULL;
	public $actScore = NULL;
	public $intendedMajor = NULL;
	public $interests = NULL;
	public $questions = NULL;
	public $referredBy = NULL;
	public $referrerInfo = NULL;
	public $address = NULL;
	public $city = NULL;
	public $state = NULL;
	public $zip = NULL;
	public $statusCode = NULL;


	public function Recruit($mysqli, $id = NULL) {
		$this->connection = $mysqli;

		if($id != NULL){
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

	public function insert(){
		$query = "INSERT INTO recruits
					(firstName, lastName, dateAdded,
					 currentSchool, hsGradYear, status,
					 bio, otherContacts, primaryContact,
					 phoneNumber, email, gpa,
					 actScore, intendedMajor, interests,
					 questions, referredBy, referrerInfo,
					 address, city, state, zip)
				VALUES
					(".make_null($this->firstName).",
					 ".make_null($this->lastName).",
					 ".make_null($this->dateAdded).",
					 ".make_null($this->currentSchool).",
					 ".make_null($this->hsGradYear).",
					 ".make_null($this->status).",
					 ".make_null($this->bio).",
					 ".make_null($this->otherContacts).",
					 ".make_null($this->primaryContact).",
					 ".make_null($this->phoneNumber).",
					 ".make_null($this->email).",
					 ".make_null($this->gpa).",
					 ".make_null($this->actScore).",
					 ".make_null($this->intendedMajor).",
					 ".make_null($this->interests).",
					 ".make_null($this->questions).",
					 ".make_null($this->referredBy).",
					 ".make_null($this->referrerInfo).",
					 ".make_null($this->address).",
					 ".make_null($this->city).",
					 ".make_null($this->state).",
					 ".make_null($this->zip).")"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		$this->id = $this->connection->insert_id;
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
			if($date == NULL || $date == '0000-00-00'){
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
			WHERE r.ID = '$this->id'"; //echo $query;
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
			WHERE recruitID = '$this->id'
			".$where."
			ORDER BY dateCompleted DESC"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$calls[] = new RecruitCall($this->connection, $data[ID]);
		}
		return $calls;
	}

	public function get_dinners($status = NULL){
		$where = "	WHERE recruitID = '$this->id'
					AND (type = 'dinnerOut' OR type = 'dinnerIn')" ;

		if($status != NULL){
			$where .= " AND status = '$status'";
		}

		return $this->get_activity($where);
	}

	public function get_visits($status = NULL){
		$where = "	WHERE recruitID = '$this->id'
					AND type = 'houseVisit'" ;

		if($status != NULL){
			$where .= " AND status = '$status'";
		}

		return $this->get_activity($where);
	}

	private function get_activity($where){
		$list = array();
		$query = "
			SELECT ID
			FROM recruitLog
			".$where."
			ORDER BY date DESC"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new RecruitCall($this->connection, $data[ID]);
		}
		return $list;
	}
}

class RecruitManager
{
	private $connection = NULL;
	private $RECRUIT_STATUS = array('2' => 'Bid Offered',
									'3' => 'Bid Ready',
									'6' => 'Interested',
									'7' => 'Uncontacted',
									'1' => 'Bid Accepted',
									'8' => 'Not Interested');

	public function RecruitManager($mysqli) {
		$this->connection = $mysqli;
	}

	public function get_status_list(){
		return $this->RECRUIT_STATUS;
	}

	public function get_new_recruits(){
		$where = 'WHERE primaryContact IS NULL';
		return $this->get_recruit_list($where);
	}

	public function get_recruits_by_status($status){
		$where = "WHERE status = '$status'
						AND primaryContact IS NOT NULL";
		return $this->get_recruit_list($where);
	}

	public function get_recruits_by_owner($username){
		$where = "WHERE primaryContact = '$username'";
		return $this->get_recruit_list($where);
	}

	private function get_recruit_list($where){
		$recruitList = array();
		$recruitQuery = "
			SELECT ID FROM recruits
			$where
			ORDER BY firstName ASC"; //echo $recruitQuery.'<br>';
		$getRecruits = mysqli_query($this->connection, $recruitQuery);
		while($recruitArray = mysqli_fetch_array($getRecruits, MYSQLI_ASSOC)){
			$recruitList[] = new Recruit($this->connection, $recruitArray[ID]);
		}

		return $recruitList;
	}

	public function remove_recruit($id){
		$query = "	DELETE
					FROM recruits
					WHERE ID = '$id'";
		$result = mysqli_query($this->connection, $query);
	}
}

?>