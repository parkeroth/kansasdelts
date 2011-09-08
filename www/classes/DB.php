<?php

/**
 * Description of DB
 *
 * @author Parker Roth
 */
class DB {
	
	// TODO: Add functionality for specified environments [local, prod, test, etc]
	private $host = 'localhost';
	private $database = 'delt';
	private $username = 'root';
	private $password = '';
    
	protected $connection = NULL;
    
	function __construct(){
		$this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);
		
		if(mysqli_connect_errno()){
			throw new Exception('Cannot connect to database: '.$this->database.' Errorcode: '.mysqli_connect_error());
			exit;
		}
	}
	
	function __destruct() {
		$this->connection->close();
	}
}

?>
