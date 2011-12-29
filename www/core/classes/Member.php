<?php

require_once 'DB_Table.php';
require_once 'DB_Manager.php';

class Member extends DB_Table
{
	public $id = NULL;
	public $username = NULL;
	public $accountTpe = NULL;
	public $last_name = NULL;
	public $first_name = NULL;

	function __construct($member_id = NULL, $username = NULL) {
		$this->table_name = 'members';
		$this->table_mapper = array(
			'id' => 'ID',
			'accountTpe' => 'accountTpe',	//DON'T USE THIS!
			'last_name' => 'lastName',
			'first_name' => 'firstName'
		);
		if($username){
			$params = array('username' => $username);
		} else {
			$params = array('id' => $member_id);
		}
		parent::__construct($params);
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