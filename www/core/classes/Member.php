<?php

require_once 'DB_Table.php';
require_once 'DB_Manager.php';

class Member extends DB_Table
{
	public $raw_fields = array('password');
	
	public $id = NULL;
	public $username = NULL;
	public $accountType = NULL;
	public $last_name = NULL;
	public $first_name = NULL;
	protected $password = NULL;
	protected $date_added = NULL;
	public $standing = NULL;
	public $status = NULL;
	public $residency = NULL;

	function __construct($member_id = NULL, $username = NULL) {
		$this->table_name = 'members';
		$this->table_mapper = array(
			'id' => 'ID',
			'accountType' => 'accountType',	//DON'T USE THIS!
			'last_name' => 'lastName',
			'first_name' => 'firstName',
			'username' => 'username',
			'password' => 'password',
			'date_added' => 'dateAdded',
			'standing' => 'standing',
			'status' => 'memberStatus',
			'residency' => 'residency'
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
	
	function __toString() {
		$this->first_name;
	}
}

class Member_Manager extends DB_Manager
{
	function __construct() {
		parent::__construct();
	}

	public function get_members_by_position($position){
		$where = "WHERE accountType LIKE '%|$position%'
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
			ORDER BY firstName ASC"; //echo $query.'<br>';
		$result = $this->connection->query($query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$member = new Member($data[ID]);
			$list[] = $member;
		}
		return $list;
	}
}
?>