<?php
session_start();

include_once('php/login.php');		//TODO: Remove
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);		//TODO: Remove

include_once '/core/classes/Position.php';
include_once '/core/classes/Position_Log.php';
include_once '/core/classes/Member.php';

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
?>

<style type="text/css">
th.accomplishments {
	padding-left, padding-right: 10px;
}
</style>

<?php
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>

<h1>Meet the Guys</h1>
	  <p>More about the men of Kansas University Delta Tau Delta Fraternity.</p>
	  
      <h2>Executive Council</h2>
      <p>&nbsp;      </p>
	  <?php
			
	  	$positionData = "SELECT * FROM positions";
		$getPositionData = mysqli_query($mysqli, $positionData);
		$positionCount = 0;
		
		while($positionDataArray = mysqli_fetch_array($getPositionData, MYSQLI_ASSOC))
		{
			$positions[$positionCount]['type'] = $positionDataArray['type'];
			$positions[$positionCount]['title'] = $positionDataArray['title'];
			$positions[$positionCount]['board'] = $positionDataArray['board'];
			$positionCount++;
		}
		
	  	function getPosition($type, $positionArray, $count){
			for($i=0; $i < $count; $i++)
			{
				if(strpos($type, $positionArray[$i]['type'])){
					return $positionArray[$i]['title'];
				}
			}
		}
		
	  	function printRecord($i, $members, $positions, $positionCount){
			echo '<td width="127">';
			
			
			echo "<img src=\"photos/composite/";
			
			if(file_exists("photos/composite/".$members[$i]['username'].".jpg"))
			{
				echo $members[$i]['username'].".jpg";
			}
			else
			{
				echo "mystery.png";
			}
			echo "\">";
			
			echo '</td>';
			echo '<td width="300"><span id="name">'.$members[$i]['firstName'].' '.$members[$i]['lastName'].'</span><br />';
			if(!strpos($members[$i]['accountType'], "brother"))
			{		
				echo 'Position: '.getPosition($members[$i]['accountType'], $positions, $positionCount).'<br />';
			}	
			echo 'Class: '.ucwords($members[$i]['class']).'<br />';
			echo 'Major: '.$members[$i]['major'].'</td>';
		}
		
		function find($members, $index, $memberCount, $positions, $positionCount){
			for($j=0; $j < $memberCount; $j++){
					if(strpos($members[$j]['accountType'], $index))
						printRecord($j, $members, $positions, $positionCount);
			}
		}
	  
	  	$userData = "
			SELECT * 
			FROM members
			ORDER BY lastName";
	
		$getUserData = mysqli_query($mysqli, $userData);
		
		$memberCount = 0;
		while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
		{
			$members[$memberCount]['username'] = $userDataArray['username'];
			$members[$memberCount]['firstName'] = $userDataArray['firstName'];
			$members[$memberCount]['lastName'] = $userDataArray['lastName'];
			$members[$memberCount]['accountType'] = $userDataArray['accountType'];
			$members[$memberCount]['class'] = $userDataArray['class'];
			$members[$memberCount]['major'] = $userDataArray['major'];
			$memberCount++;
		}
		
		$classData = "SELECT DISTINCT class FROM members ORDER BY ID";
		$getClassData = mysqli_query($mysqli, $classData);
		
		$classCount = 0;
		while($classDataArray = mysqli_fetch_array($getClassData, MYSQLI_ASSOC))
		{
			$class[$classCount] = $classDataArray['class'];
			$classCount++;
		}
		
		function print_record($member, $position){
			echo '<td width="127">';
			
			
			echo "<img src=\"photos/composite/";
			
			if(file_exists("photos/composite/".$member->username.".jpg"))
			{
				echo $member->username.".jpg";
			}
			else
			{
				echo "mystery.png";
			}
			echo "\">";
			
			echo '</td>';
			echo '<td width="300"><span id="name">'.$member->first_name.' '.$member->last_name.'</span><br />';
			echo 'Position: '.$position->title.'<br />';	
			echo 'Class: '.ucwords($members->class).'<br />';
			echo 'Major: '.$member->major.'</td>';
		}
		
		function display_positions($board, $term, $year){
			$display_count = 0;
			
			$position_manager = new Position_Manager();
			$position_list = $position_manager->get_positions_by_board($board);
			
			$position_log_manager = new Position_Log_Manager();
			foreach($position_list as $position)
			{
				$entry_list = $position_log_manager->get_logs_by_position($position->id, $term, $year);
				foreach($entry_list as $entry)
				{
					$member = new Member($entry->member_id);
					
					if($display_count % 2 == 0){
						echo '<tr>';
					}
					
					print_record($member, $position);
					
					if($display_count % 2 != 0){
						echo '</tr>';
					}
					
					$display_count++;
				}
			}
		}
	  ?>
      <table width="650" border="0" id="exec">
      	<?php
		$month = date('n');
		if($month < 8){
			$term = 'spring';
		} else {
			$term = 'fall';
		}
		$year = date('Y');
		
		display_positions('exec', $term, $year); 
	?>
      </table>
	  
	  	<?php for($i = 0; $i < $classCount; $i++)
	  		{
				$row = 1;
				$first = true;
				
				echo "<table>";
				
	  			for($j = 0; $j < $memberCount; $j++)
				{
					if($members[$j]['class'] == $class[$i])
					{
						if($first == true)
						{
							echo "<p>&nbsp;</p>";
							echo "<h2>".ucwords($class[$i])." Class</h2>";
							$first = false;
						}
						if($row%2 == 1){echo "<tr>\n";}
						printRecord($j, $members, $positions, $positionCount);
						if(($row+1)%2 == 1){echo "</tr>\n";}
						$row++;
					}
				}
				
				echo "</table>";
	  		}
		?>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>