<?php
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'academics');
include_once('php/authenticate.php');


/**
 * Processing Section
 */
 
if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	$year = $_POST[year];
	$term = $_POST[term];
	
	$userData = "
		SELECT * 
		FROM members";
	
	$getUserData = mysqli_query($mysqli, $userData);

	while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC)){
		
		$check = "SELECT ID 
			FROM grades
			WHERE year = '$year'
			AND term = '$term'
			AND username = '".$userDataArray[username]."'";
		$checkTable = mysqli_query($mysqli, $check);
		
		if(mysqli_fetch_row($checkTable))
		{
			$modify = "UPDATE grades
				SET gpa = '".$_POST[$userDataArray[username]."GPA"]."' 
				WHERE year = '$year'
				AND term = '$term'
				AND username = '".$userDataArray[username]."'";
			$doModification = mysqli_query($mysqli, $modify);
		}
		else
		{
			$modify = "INSERT INTO grades
				(username, gpa, hours, year, term)
				VALUES ('".$userDataArray[username]."', '".$_POST[$userDataArray[username]."GPA"]."', '".$_POST[hours]."', '$year', '$term')";
			$doModification = mysqli_query($mysqli, $modify);
		}
	
	}
	
}
	
/**
 * Form Section
 */

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>

<div style="text-align:center;">
	<?php
	if(isset($_GET['season']) && isset($_GET['year']))
	{
		$year = $_GET['year'];
		$season = $_GET['season'];
	} else {
		
		$year = date(Y);
		$month = date(n);
		
		if($month > 0 && $month <= 6){
			$season = "spring";
		} else if($month > 6 && $month < 13){
			$season = "fall";
		}
	
	}
	
	
	
	echo "<h2> House Grades - ".ucwords($season)." ".$year."</h2>";
	?>
	
	<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr> 
			<td><div align="right"><a href="<? 
	  		
			if($season == "fall"){
				echo "changeGradesForm.php?year=$year&amp;season=spring"; 
			} else {
				$lastYear = $year-1;
				echo "changeGradesForm.php?year=$lastYear&amp;season=fall"; 
			}
			
			?>">&lt;&lt;</a></div></td>
			<td width="200"><div align="center">
				
				<select name="season" id="month" onChange="MM_jumpMenu('parent',this,0)">
					<?
			if($season == "fall"){
		  		echo "<option value=\"changeGradesForm.php?year=$year&amp;season=spring\" >Spring</option>\n";
				echo "<option value=\"changeGradesForm.php?year=$year&amp;season=fall\" selected>Fall</option>\n";
			} else {
				echo "<option value=\"changeGradesForm.php?year=$year&amp;season=spring\" selected>Spring</option>\n";
				echo "<option value=\"changeGradesForm.php?year=$year&amp;season=fall\" >Fall</option>\n";
			}
			?>
					</select>
				<select name="year" id="year" onChange="MM_jumpMenu('parent',this,0)">
					<?
		  $yearLoop = date("Y");
		  
		  for ($i = $yearLoop+1; $i >= $yearLoop-3; $i--) {
		  	if($i == $year){
				$selected = "selected";
			} else {
				$selected = "";
			}
		  	echo "<option value=\"changeGradesForm.php?season=$season&amp;year=$i\" $selected>$i</option>\n";
		  }
		  ?>
					</select>
				</div></td>
			<td><div align="left"><a href="<? 
	  	
		if($season == "fall"){
				$nextYear = $year+1;
				echo "changeGradesForm.php?year=$nextYear&amp;season=spring"; 
			} else {
				echo "changeGradesForm.php?year=$year&amp;season=fall"; 
			}
		
		?>">&gt;&gt;</a></div></td>
			</tr>
		</table>
</div>
	<form id="gap" name="gpa" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<table style="text-align:center;" align="center">
			<tr>
				<td><strong>Member</strong></td><td width="100"px><strong>GPA</strong></td><td width="100px"><strong>Hours</strong></td></tr>
			<?php 
		include_once('php/login.php');
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		if($season == "fall")
		{
			$dateAdded = $year."-08-01";
		}
		else
		{
			$dateAdded = $year."-01-01";
		}
		
		$userData = "
			SELECT * 
			FROM members
			WHERE dateAdded <= '$dateAdded'
			ORDER BY lastName";
		$getUserData = mysqli_query($mysqli, $userData);
		
		while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC)){
			
			$hourData = "
				SELECT hours
				FROM classes
				WHERE username = '".$userDataArray['username']."'
				AND termYear='$year'
				AND termSeason='$season'";
			$getHourData = mysqli_query($mysqli, $hourData);
			$hours = 0;
			
			while($hourDataArray = mysqli_fetch_array($getHourData, MYSQLI_ASSOC))
			{
				$hours = $hours + $hourDataArray[hours];
			}
			
			$gpaData = "
				SELECT gpa
				FROM grades
				WHERE username = '".$userDataArray['username']."'
				AND year='$year'
				AND term='$season'";
			$getGpaDetail = mysqli_query($mysqli, $gpaData);
			$gpaDataArray = mysqli_fetch_array($getGpaDetail, MYSQLI_ASSOC);
			$gpa = 0;
			$gpa = $gpa + $gpaDataArray[gpa];
			
			
			echo "<tr><td style=\"text-align: left;\"><label>".$userDataArray['firstName']." ".$userDataArray['lastName']." </td>\n";
			echo "<td><input type=\"text\" name=\"".$userDataArray['username']."GPA\" value=\"$gpa\" size=\"4\"/></label></td>\n";
			echo "<td>$hours</td></tr>\n";
		}
	?>
			</table>
		<p style="text-align:center;">
			<input  type="hidden" name="term" value="<?php echo $season; ?>" />
			<input  type="hidden" name="year" value="<?php echo $year; ?>" />
			<input type="hidden" name="hours" value="<?php echo $hours; ?>" />
			<input type="submit" name="submit" id="submit" value="Submit" />
			<label>
				<input type="reset" name="Reset" id="Reset" value="Reset" />
				</label>
			</p>
</form>
	<p>&nbsp;</p>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>