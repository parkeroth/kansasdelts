<?php
class ReportingTask
{
	public static $DOCUMENTS = array('FAAR', 'Greek Awards');
	
	private $connection = NULL;
	public $id = NULL;
	public $task = NULL;
	public $description = NULL;
	public $document = NULL;
         public $section = NULL;
         public $owner = NULL;
	public $helper = NULL;
	public $notes = NULL;
	public $status = NULL;

	public function ReportingTask($mysqli, $id) {
		$this->connection = $mysqli;

		$query = "
			SELECT *
			FROM reportingTask
			WHERE ID= '$id'"; //echo $memberQuery.'<br>';
		$result = mysqli_query($this->connection, $query);
		$data = mysqli_fetch_array($result, MYSQLI_ASSOC);

		$this->id = $data[ID];
		$this->task = $data[task];
		$this->description = $data[username];
		$this->document = $data[document];
		$this->section = $data[section];
		$this->owner = $data[owner];
		$this->helper = $data[helper];
		$this->notes = $data[notes];
		$this->status = $data[status];
	}
}

class ReportingTaskManager
{
	private $connection = NULL;

	public function ReportingTaskManager($mysqli) {
		$this->connection = $mysqli;
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