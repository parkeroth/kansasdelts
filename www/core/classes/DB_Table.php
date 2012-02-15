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
	
	/**
	 * This constructor takes a set of parameters and initializes the object either as empty or fill with information
	 * from the database depending on the values of the params parameter.
	 *
	 * @param (params) array of member_variable => value used to initialize an object
	 * @return None
	 */
	function __construct($params){
		parent::__construct();
		
		if($params){
			$where = '';
			$first = true;
			// Build up the where clause based on the values in params
			foreach($params as $member_var => $value){
				if($value){
					$where .= $this->table_mapper[$member_var]." = '$value' ";
				}
				// Decide where to place the AND if at all
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
				// Fill the new objects member variables with the information from the database
				foreach($this->table_mapper as $member_var => $table_field){
					$this->{$member_var}  = $data[$table_field];
				}
			}
		}
	}
	
	/**
	 * This function "saves the current values of the object into the database. This is done by iterating over the 
	 * table mapper array to translate each member variable into a database field.
	 *
	 * @return None
	 */
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
		//echo $query;
		$this->connection->query($query);
	}
	
	/**
	 * This function takes field and value and prepares it for entry into an SQL expression.
	 *
	 * @return (value = NULL) returns NULL without single quotes
	 * @return (field in raw_fields array)	returns value without sigle quotes. Useful for fields like password that
	 *							should not have single quotes
	 * @return returns value surrounded by single quotes
	 */
	private function make_null($field, $value){
		if($value === NULL){
			return 'NULL';
		} if(in_array($field, $this->raw_fields)){
			return $value;
		} else {
			return "'".$value."'";
		}
	}
	
	/**
	 * This function "inserts" the current object into the database. This is done by iterating over the 
	 * table mapper array to translate each member variable into a database field. Insert is usually called after
	 * manually filling each member variable with the desired value.
	 *
	 * @return None
	 */
	public function insert(){
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
				$query .= $this->make_null($table_field, $this->{$member_var});
			}
		}
		$id_field = $this->table_mapper[id];
		$query .= ' ) '; //echo $query;
		$this->connection->query($query);
		// Find the proper value for the id of the newly inserted object
		$this->id = $this->connection->insert_id;
	}
	
	/**
	 * This function removes the current object from the database.
	 *
	 * @return None
	 */
	public function delete(){
		$query =	"DELETE FROM $this->table_name 
				WHERE ".$this->table_mapper[id]." = '$this->id'";
		//echo $query;
		$this->connection->query($query);
		unset($this); // Could be a problem?
	}
	
	function __destruct() {
		parent::__destruct();
	}
}

?>
