<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB_Table.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/DB_Manager.php';

class ReportingTask extends DB_Table
{
	public static $DOCUMENTS = array('FAAR', 'Greek Awards');
	
	public $id = NULL;
	public $task = NULL;
	public $description = NULL;
	public $document = NULL;
         public $section = NULL;
         public $owner = NULL;
	public $helper = NULL;
	public $notes = NULL;
	public $status = NULL;

	function __construct($id) {
		$this->table_name = 'reportingtasks';
		$this->table_mapper = array(
			'id' => 'ID',
			'task' => 'task',
			'description' => 'description',
			'document' => 'document',
			'section' => 'section',
			'owner' => 'owner',
			'helper' => 'helper',
			'notes' => 'notes',
			'status' => 'status'
		);
		$params = array('id' => $id);
		parent::__construct($params);
	}
}

class ReportingTaskManager extends DB_Manager
{
	function __construct() {
		parent::__construct();
	}

	public function get_tasks_owned_by_position_document($position){
		$where = "WHERE owner = '$position'";
		return $this->get_task_list($where);
	}

	private function get_task_list($where){
		$list = array();
		$query = "
			SELECT ID FROM reportingTask
			$where
			ORDER BY ID ASC"; //echo $query.'<br>';
		$result = mysqli_query($this->connection, $query);
		while($data = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			$list[] = new ReportingTask($this->connection, $data[ID]);
		}
		return $list;
	}
}
?>