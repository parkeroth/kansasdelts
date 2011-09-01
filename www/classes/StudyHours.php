<?php
class Member
{
	private $connection = NULL;
	public $id = NULL;
	public $username = NULL;
	public $accountTpe = NULL;
	public $lastName = NULL;
	public $firstName = NULL;

	public function Member($mysqli, $username) {
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