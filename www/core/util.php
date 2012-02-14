<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Position.php';


class Semester {
	public static $TERMS = array('spring', 'fall');
	
	public $term = NULL;
	public $year = NULL;
	
	function __construct($date = NULL){
		if($date){
			$date = date('Y-m-d', strtotime($date));
		} else {
			$date = date('Y-m-d');
		}
		$time = strtotime($date);
		$this->year = date('Y', $time);
		$this->term = $this->get_term($time);
	}
	
	private function get_term($time){
		$month = date('n', $time);
		if($month < 8){
			return 'spring';
		} else {
			return 'fall';
		}
	}
	
	public function get_start_date(){
		if($this->term == 'spring'){
			$date_str = $this->year.'-01-01';
		} else {
			$date_str = $this->year.'-08-01';
		}
		return $date_str;
	}
	
	public function get_end_date(){
		if($this->term == 'spring'){
			$date_str = $this->year.'-07-31';
		} else {
			$date_str = $this->year.'-12-31';
		}
		return $date_str;
	}
	
	public function next(){
		if($this->term == 'spring'){
			$this->term = 'fall';
		} else {
			$this->term = 'spring';
			$this->year = $this->year + 1;
		}
	}
	
	public function previous(){
		if($this->term == 'spring'){
			$this->term = 'fall';
			$this->year = $this->year - 1;
		} else {
			$this->term = 'spring';
		}
	}
}

function second_stage_auth($page_auth, $key, $member_id){
	$AUTHORIZED = false;
	
	$member = new Member($member_id);
	$position_slugs = $page_auth[$key];
	foreach($position_slugs as $slug){
		$position = new Position(NULL, $slug);
		if($member->is_position($position->id)){
			$AUTHORIZED = true;
		}
	}
	return $AUTHORIZED;
}
?>
