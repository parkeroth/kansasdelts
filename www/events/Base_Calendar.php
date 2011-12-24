<?php
class Base_Calendar{
	
	protected $month = NULL;
	protected $year = NULL;
	
	protected $todays_day = NULL;
	protected $todays_month = NULL;
	
	protected $first_day_of_month = NULL;
	
	protected $box_count = 0;
	protected $days_so_far = 0;
	
	protected $days_in_month = NULL;
	
	function __construct($month, $year){
		if($month >0 && $month < 13){
			$this->month = $month;
		} else {
			return 0;
		}
		if(is_numeric($year) && strlen($year) == 4 ){
			$this->year = $year;
		}
		
		$this->todays_day = date('j');
		$this->todays_month = date('n');
		$this->days_in_month = date ("t", strtotime("$this->year-$this->month-01"));
		$this->first_day_of_month = date ('w', strtotime("$this->year-$this->month-01"))+1;
	}
	
	private function draw_header(){	
?>
		<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#000000">
			<tr>
				<td><table width="100%" border="0" cellpadding="0" cellspacing="1">
					<tr class="topdays"> 
						<td><div align="center">Sunday</div></td>
						<td><div align="center">Monday</div></td>
						<td><div align="center">Tuesday</div></td>
						<td><div align="center">Wednesday</div></td>
						<td><div align="center">Thursday</div></td>
						<td><div align="center">Friday</div></td>
						<td><div align="center">Saturday</div></td>
					</tr>
					<tr valign="top" bgcolor="#FFFFFF"> 
<?php	
	}
	
	private function draw_days_before(){
		for ($i = 1; $i < $this->first_day_of_month; $i++) {
			$this->days_so_far++;
			$this->box_count++;
			echo "<td width=\"100\" height=\"100\" class=\"beforedayboxes\"></td>\n";
		}
	}
	
	private function draw_days_after(){
		$num_on_last_row = ($this->days_in_month - 1 + $this->first_day_of_month) % 7;
		for ($i = $num_on_last_row; $i%7 != 0; $i++) {
			echo "<td width=\"100\" height=\"100\" class=\"afterdayboxes\"></td>\n";
		}
	}
	
	private function draw_footer(){
		?>
						
				</tr>
			</table></td>
		</tr>
	</table> 	
		
		<?php
	}
	
	public function pre_draw(){
		// Create days of week header
		$this->draw_header();
		// Fill calendar with boxes before 1st
		$this->draw_days_before();	
	}
	
	public function post_draw(){
		// Fill boxes with days after 30/31st
		$this->draw_days_after();
		// Create footer
		$this->draw_footer();
	}
}
?>
