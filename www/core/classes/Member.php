<?php

require_once 'DB_Table.php';
require_once 'DB_Manager.php';
require_once 'Position_Log.php';

class Member extends DB_Table
{
	public $raw_fields = array('password');
	
	public $id = NULL;
	public $username = NULL;
	public $class = NULL;
	public $accountType = NULL;
	public $last_name = NULL;
	public $first_name = NULL;
	protected $password = NULL;
	protected $date_added = NULL;
	public $standing = NULL;
	public $status = NULL;
	public $residency = NULL;
	
	public $email = NULL;
	protected $phone_number = NULL;
	public $phone_carrier = NULL;
	
	public $school = NULL;
	public $major = NULL;
	public $grad_year = NULL;
	public $shirt_size = NULL;
	public $home_town = NULL;
	
	public $parent_name = NULL;
	public $parent_address = NULL;
	public $parent_email = NULL;
	
	public $dad_id = NULL;
	public $excused = NULL;

	function __construct($member_id = NULL, $username = NULL) {
		$this->table_name = 'members';
		$this->table_mapper = array(
			'id' => 'ID',
			'accountType' => 'accountType',	//DON'T USE THIS!
			'last_name' => 'lastName',
			'first_name' => 'firstName',
			'username' => 'username',
			'password' => 'password',
			'class' => 'class',
			'date_added' => 'dateAdded',
			'standing' => 'standing',
			'status' => 'memberStatus',
			'residency' => 'residency',
			'email' => 'email',
			'phone_number' => 'phone',		//TODO: Change to phone_number in schema
			'phone_carrier' => 'carrier',
			'school' => 'school',
			'major' => 'major',
			'grad_year' => 'gradYear',
			'shirt_size' => 'shirtSize',
			'home_town' => 'homeTown',
			'parent_name' => 'parentName',
			'parent_address' => 'parentAddress',
			'parent_email' => 'parentEmail',
			'dad_id' => 'dad_id',
			'excused' => 'excused'
		);
		if($username){
			$params = array('username' => $username);
		} else if($member_id) {
			$params = array('id' => $member_id);
		} else {
			$params = NULL;
		}
		parent::__construct($params);
	}
	
	public function set_password($phrase){
		$this->password = "SHA('$phrase')";
	}
	
	public function set_date_added($date = NULL){
		if(!$date){
			$date = date('Y-m-d');
		}
		$this->date_added = $date;
	}
	
	public function get_phone_number(){
		$str = '('.substr($this->phone_number, 0, 3).') ';
		$str .= substr($this->phone_number, 3, 3).'-';
		$str .= substr($this->phone_number, 6);
		return $str;
	}
	
	public function set_phone_number($str){
		$numbers = preg_replace('/\D/', '', $str);
		if(strlen($str) == 10){
			$this->phone_number = $str;
			return true;
		} else {
			return false;
		}
	}
	
	function is_position($position_id){
		$position_log_manager = new Position_Log_Manager();
		$month = date('n');
		$year = date('Y');
		if($month < 8){
			$term = 'spring';
		} else {
			$term = 'fall';
		}
		return $position_log_manager->member_in_committee($this->id, $position_id, $term, $year);
	}
	
	public function get_class(){
		return $this->class;
	}
	
	function __toString() {
		$this->first_name;
	}
}

class Member_Manager extends DB_Manager
{
	public static $PHONE_CARRIERS = array('none' => 'None',
								'verizon' => 'Verizon',
								'tmobile' => 'T-Mobile',
								'sprint' => 'Sprint',
								'att' => 'AT&T');
	
	public static $SCHOOLS = array(	'allied' => 'Allied Health',
							'architecture' => 'Architecture, Design & Planning',
							'business' => 'Business',
							'education' => 'Education',
							'engineering' => 'Engineering',
							'journalism' => 'Journalism',
							'music' => 'Music',
							'nursing' => 'Nursing',
							'pharmacy' => 'Pharmacy',
							'social' => 'Social Welfare',
							'liberal' => 'Liberal Arts & Sciences',
							'other' => 'Other');
	
	function __construct() {
		parent::__construct();
	}
	
	// IF THIS CHANGES UPDATE /records/chapterMinutes.php
	public function get_total_voting(){
		$query = "
			SELECT COUNT(ID) as total 
			FROM members
			WHERE memberStatus = 'active'"; //echo $query.'<br>';
		$result = $this->connection->query($query);
		$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $data[total];
	}

	public function get_members_by_position($position_type){
		$where = "WHERE accountType LIKE '%|$position_type%'
						AND memberStatus != 'limbo'";
		return $this->get_member_list($where);
	}
	
	public function get_all_members(){
		$where = "WHERE memberStatus != 'limbo'";
		return $this->get_member_list($where);
	}

	private function get_member_list($where){
		$list = array();
		$query = "
			SELECT ID FROM members
			$where
			ORDER BY lastName ASC"; //echo $query.'<br>';
		$result = $this->connection->query($query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$member = new Member($data[ID]);
			$list[] = $member;
		}
		return $list;
	}
}
?>