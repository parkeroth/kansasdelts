<?php
require_once 'DB_Table.php';

class Member extends DB_Table
{
	public $id = NULL;
	public $username = NULL;
	public $accountType = NULL;		//TODO: Uncamel Case
	public $lastName = NULL;		//TODO: Uncamel Case
	public $firstName = NULL;		//TODO: Uncamel Case

	function __construct($mysqli = NULL, $username = NULL, $member_id = NULL) {
		if($mysqli) { // Will remove later when I am sure no calls are made to new Member($mysqli)
			$this->connection = $mysqli;

			$memberQuery = "
				SELECT *
				FROM members
				WHERE username = '$username'"; //echo $memberQuery.'<br>';
			$getMember = mysqli_query($this->connection, $memberQuery);
			$memberData = mysqli_fetch_array($getMember, MYSQLI_ASSOC);

			$this->id = $memberData[ID];
			$this->username = $memberData[username];
			$this->accountType = $memberData[accountType];
			$this->lastName = $memberData[lastName];
			$this->firstName = $memberData[firstName];
		} else {
			$this->table_name = 'members';
			$this->table_mapper = array('id' => 'ID',
								'username' => 'username',
								'accountType' => 'accountType',
								'lastName' => 'lastName',
								'firstName' => 'firstName');
			$params = array('id' => $member_id);
			parent::__construct($params);
		}
	}
	
	public function is_a($position_slugs){
		foreach($position_slugs as $slug){
			if(strpos($this->accountType, $slug)){
				return true;
			}
		}
		return false;
	}
}

class MemberManager
{
	private $connection = NULL;

	public function MemberManager($mysqli) {
		$this->connection = $mysqli;
	}

	public function get_members_by_position($position){
		$where = "WHERE accountType LIKE '%|$position%'
						AND memberStatus != 'limbo'";
		return $this->get_member_list($where);
	}

	private function get_member_list($where){
		$list = array();
		$query = "
			SELECT username FROM members
			$where
			ORDER BY firstName ASC"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new Member($this->connection, $data[username]);
		}
		return $list;
	}
}
?>