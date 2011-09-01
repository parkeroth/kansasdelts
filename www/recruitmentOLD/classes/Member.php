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
			WHERE username = '$username'"; //echo $callQuery;
		$getMember = mysqli_query($this->connection, $memberQuery);
		$memberData = mysqli_fetch_array($getMember, MYSQLI_ASSOC);
		
		$this->id = $memberData[ID];
		$this->username = $callData[username];
		$this->accountType = $callData[accountType];
		$this->lastName = $callData[lastName];
		$this->firstName = $callData[firstName];
	}
}
	
?>