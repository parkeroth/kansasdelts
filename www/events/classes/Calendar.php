<?php

require_once '../classes/Member.php';

class Calendar{
	private static $DRAW_TYPES = array('general');
	
	private $month = NULL;
	private $year = NULL;
	private $current_member = NULL;
	
	private $todays_day = NULL;
	private $todays_month = NULL;
	
	private $days_in_month = NULL;
	private $first_day_of_month = NULL;
	
	private $box_count = 0;
	private $days_so_far = 0;
	
	function __construct($month, $year, $member_id){
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
		
		$this->current_member = new Member(NULL, NULL, $member_id);
		
	}
	
	private function can_add_event(){
		$authorized_slugs = array(	'admin' , 'houseManager', 'brotherhood', 
							'secretary', 'communityService', 'recruitment', 
							'pledgeEd', 'homecoming', 'vpInternal', 
							'drm', 'social', 'philanthropy');
		return $this->current_member->is_a($authorized_slugs);
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
		for ($i = 1; $i <= $this->first_day_of_month-1; $i++) {
			$this->days_so_far++;
			$this->box_count++;
			echo "<td width=\"100\" height=\"100\" class=\"beforedayboxes\"></td>\n";
		}
	}
	
	private function get_events($type){
		$event_manager = new E
	}
	
	private function draw_days($type){
		for ($i = 1; $i <= $this->days_in_month; $i++) {
   			$this->days_so_far++;
			$this->box_count++;
			
			// If box is for today  set class
			if($this->month == $this->todays_month && $i == $this->todays_day){
				$class = "highlighteddayboxes";
			} else {
				$class = "dayboxes";
			}
			
			echo "<td width=\"100\" height=\"100\" class=\"$class\">\n";
			
			// If authorized set event add link
			if($this->can_add_event())
			{
				echo "<div align=\"right\"><span class=\"toprightnumber\">\n";
				echo "<a class=\"topRightNum\" href=\"javascript:MM_openBrWindow('AddCalEvent.php?";
				echo "day=$i&amp;month=$this->month&amp;year=$this->year&amp;type=".$this->current_member->accountType;
				echo "','','width=500,height=400, scrollbars=1');\"><b><u>$i</u></b></a>";
				echo "&nbsp;</span></div>\n";
			}
			else
			{ 
				echo "<div align=\"right\"><span class=\"toprightnumber\">\n$i&nbsp;</span></div>\n";
			}
			/*
			// If event in day
			if(isset($events[$i])){
				echo "<div align=\"left\"><span class=\"eventinbox\">\n";
				
				// Iterate through all events in array
				while (list($key, $value) = each ($events[$i])) {
					
					// Set event link
					if($event_info[$value]['3'] == "general" || $event_info[$value]['3'] == "social" || $event_info[$value]['3'] == "pr")
					{
						echo "<a class=\"eventLinkgeneral ".$type[$value]."Filter\"";
						echo "href=\"javascript:MM_openBrWindow('viewCalEvent.php?ID=$value";
						echo "', '', 'width=500, height=200');\">";
					}
					else if($event_info[$value]['3'] != "notInvited")
					{
						echo "<a class=\"eventLink".$event_info[$value]['3']." ".$type[$value]."Filter\"";
						echo "href=\"javascript:MM_openBrWindow('viewCalEvent.php?ID=$value&amp;";
						echo "status=".$event_info[$value]['3']."','','width=500,height=220');\">";
					}
					
					// If Mandatory
					if($event_info[$value]['4']){echo "<b>";}
					
					echo $event_info[$value]['0'];	// Print event title
					echo "<br>&nbsp;".$event_info[$value][1];
					
					// If Mandatory
					if($event_info[$value]['4']){echo "</b>";}
					
					echo "</a>\n<br>\n";		
			
				}
				echo "</span></div>\n";
			} */
			echo "</td>\n";
			
			// End row if end of week
			if(($this->box_count == 7) && ($this->days_so_far != (($this->first_day_of_month) + $this->days_in_month))){
				$this->box_count = 0;
				echo "</TR><TR valign=\"top\">\n";
			}
		}
	}
	
	private function draw_days_after(){
		$extra_boxes = 7 - $this->box_count;
		for ($i = 1; $i <= $extra_boxes; $i++) {
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
	
	public function draw_calendar($type){
		// Create days of week header
		$this->draw_header();
		// Fill calendar with boxes before 1st
		$this->draw_days_before();
		// Fill calendar with boxes for each day
		$this->draw_days($type);
		// Fill boxes with days after 30/31st
		$this->draw_days_after();
		// Create footer
		$this->draw_footer();
	}
}
?>
