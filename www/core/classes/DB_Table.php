<?php
require_once 'DB.php';

/**
 * Description of DB
 *
 * @author Parker Roth
 */
class DB_Table extends DB{
	protected $table_name = NULL;
	protected $table_mapper = NULL;
	
	function __construct($params){
		parent::__construct();
		$where = '';
		$first = true;
		foreach($params as $member_var => $value){
			if($value){
				$where .= $this->table_mapper[$member_var]." = '$value' ";
			}
			if($first){
				$first = false;
			} else {
				$where .= 'AND ';
			}
		}
		if($where != ''){
			$query = "
				SELECT *
				FROM $this->table_name
				WHERE $where";
			$result = $this->connection->query($query); //echo $query;
			$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
			foreach($this->table_mapper as $member_var => $table_field){
				$this->{$member_var}  = $data[$table_field];
			}
		}
	}
	
	public function save(){
		$query ="UPDATE $this->table_name
				SET ";
		$first = true;
		foreach($this->table_mapper as $member_var => $table_field){
			if($first){
				$first = false;
			} else {
				$query .= ', ';
			}
			$query .= $table_field." = '".$this->{$member_var}."'";
		}
		$id_field = $this->table_mapper[id];
		$query .= " WHERE $id_field = '$this->id'";
		
		$this->connection->query($query);
	}
	
	public function insert(){
		function make_null($value){
			if($value == NULL){
				return 'NULL';
			} else {
				return "'".$value."'";
			}
		}

		$query ="INSERT INTO $this->table_name ( ";
		$first = true;
		foreach($this->table_mapper as $member_var => $table_field){
			if($member_var != 'id'){
				if($first){
					$first = false;
				} else {
					$query .= ', ';
				}
				$query .= $table_field;
			}
		}
		$query .= ' ) VALUES ( ';
		$first = true;
		foreach($this->table_mapper as $member_var => $table_field){
			if($member_var != 'id'){
				if($first){
					$first = false;
				} else {
					$query .= ', ';
				}
				$query .= make_null($this->{$member_var});
			}
		}
		$id_field = $this->table_mapper[id];
		$query .= ' ) ';
		echo $query;
		$this->connection->query($query);
		$this->id = $this->connection->insert_id;
	}
	
	public function delete(){
		$query =	"DELETE FROM $this->table_name 
				WHERE ".$this->table_mapper[id]." = '$this->id'";
		$this->connection->query($query);
		unset($this); // Could be a problem?
	}
	
	function __destruct() {
		parent::__destruct();
	}
}

?>
