<?php

class DB_Table
{
	protected $table_name = NULL;
	
	function __construct($table_name){
		$this->table_name  = $table_name;
	}
	
	public function saveVal($field, $val){
		if($val != NULL){
			echo "is NULL <br>";
			$val = "'$val'";
		}
			
		$this->$field = $val;
		$query = "
			UPDATE $this->table_name
			SET $field = $val
			WHERE id = '$this->id'"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
	}
}
?>
