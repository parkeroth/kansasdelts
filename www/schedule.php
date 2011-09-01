<?php
session_start();
$url = "Location: publicAcademics.php";
$authUsers = array('public');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<script language="JavaScript" src="js/gen_validatorv31.js" type="text/javascript"></script>

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<div style="text-align:center;">
	<?php
	if(isset($_GET['season']) && isset($_GET['year']))
	{
		$year = $_GET['year'];
		$season = $_GET['season'];
	} else {
		$year = date(Y);
		$month = date(n);
		
		if($month > 0 && $month < 7){
			$season = "spring";
		} else if($month > 7 && $month < 13){
			$season = "fall";
		}
	}
	
	
	
	echo "<h2>".ucwords($season)." ".$year."</h2>"; ?>
	
	<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr> 
			<td><div align="right"><a href="<? 
	  		
			if($season == "fall"){
				echo "schedule.php?year=$year&amp;season=spring"; 
			} else {
				$lastYear = $year-1;
				echo "schedule.php?year=$lastYear&amp;season=fall"; 
			}
			
			?>">&lt;&lt;</a></div></td>
			<td width="200"><div align="center">
				
				<select name="season" id="month" onChange="MM_jumpMenu('parent',this,0)">
					<?
			if($season == "fall"){
		  		echo "<option value=\"schedule.php?year=$year&amp;season=spring\" >Spring</option>\n";
				echo "<option value=\"schedule.php?year=$year&amp;season=fall\" selected>Fall</option>\n";
			} else {
				echo "<option value=\"schedule.php?year=$year&amp;season=spring\" selected>Spring</option>\n";
				echo "<option value=\"schedule.php?year=$year&amp;season=fall\" >Fall</option>\n";
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
		  	echo "<option value=\"schedule.php?season=$season&amp;year=$i\" $selected>$i</option>\n";
		  }
		  ?>
					</select>
				</div></td>
			<td><div align="left"><a href="<? 
	  	
		if($season == "fall"){
				$nextYear = $year+1;
				echo "schedule.php?year=$nextYear&amp;season=spring"; 
			} else {
				echo "schedule.php?year=$year&amp;season=fall"; 
			}
		
		?>">&gt;&gt;</a></div></td>
			</tr>
		</table>
</div>
  <?php
	
	include_once('php/login.php');
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	echo '<div style="float: left; width: 300px;">';
	
	
	echo "<table><tr><td colspan=\"2\"><h3>Who's in your classes?</h3></td></tr> \n";
	
	$MyClasses = "
		SELECT * 
		FROM classes 
		WHERE termSeason='".$season."'
		AND termYear='".$year."'
		AND username='".$_SESSION['username']."'";
	
	$getMyClasses = mysqli_query($mysqli, $MyClasses); 
	
	while ($classArray = mysqli_fetch_array($getMyClasses, MYSQLI_ASSOC)){
		
		echo "<tr>";
		echo "<td style=\"width: auto;\">".$classArray[department]." ".$classArray[section].": </td>";
		
		$ClassSearch = "
			SELECT username 
			FROM classes 
			WHERE termSeason='".$season."'
			AND termYear='".$year."'
			AND department='".$classArray[department]."'
			AND section='".$classArray[section]."'
			AND username!='".$_SESSION['username']."'";
	
		$getClassSearch = mysqli_query($mysqli, $ClassSearch);
		
		$first = true;
		
		while ($searchResults = mysqli_fetch_array($getClassSearch, MYSQLI_ASSOC)){
			
			$NameSearch = "
				SELECT firstName, lastName 
				FROM members 
				WHERE username='".$searchResults['username']."'";
			
			$getNameSearch = mysqli_query($mysqli, $NameSearch);
			$nameResults = mysqli_fetch_array($getNameSearch, MYSQLI_ASSOC);
			
			if($first){
				echo "<td>".$nameResults['firstName']." ".$nameResults['lastName']."</td></tr>";
				$first = false;
			} else {
				echo "<tr><td>&nbsp;</td><td>".$nameResults['firstName']." ".$nameResults['lastName']."</td></tr>";
			}
		}
	}
	
	echo "</table>";
	echo "</div>";
	
	echo '<div style="float: right; width: 300px;">';
	echo "<table><tr><td colspan=\"2\"><h3>Who has taken your classes?</h3></td></tr> \n";
	
	$MyClasses = "
		SELECT * 
		FROM classes 
		WHERE termSeason='".$season."'
		AND termYear='".$year."'
		AND username='".$_SESSION['username']."'";
	
	$getMyClasses = mysqli_query($mysqli, $MyClasses);
	
	while ($classArray = mysqli_fetch_array($getMyClasses, MYSQLI_ASSOC)){
		
		echo "<tr>";
		echo "<td>".$classArray[department]." ".$classArray[section].": </td>";
		
		$ClassSearch = "
			SELECT username 
			FROM classes 
			WHERE department='".$classArray[department]."'
			AND section='".$classArray[section]."'
			AND (termYear!='".$year."' OR termSeason!='".$season."')";
		
		$getClassSearch = mysqli_query($mysqli, $ClassSearch);
		
		$first = true;
		
		while ($searchResults = mysqli_fetch_array($getClassSearch, MYSQLI_ASSOC)){
			
			$NameSearch = "
				SELECT firstName, lastName 
				FROM members 
				WHERE username='".$searchResults['username']."'";
			
			$getNameSearch = mysqli_query($mysqli, $NameSearch);
			$nameResults = mysqli_fetch_array($getNameSearch, MYSQLI_ASSOC);
			
			if($first){
				echo "<td>".$nameResults['firstName']." ".$nameResults['lastName']."</td></tr>";
				$first = false;
			} else {
				echo "<tr><td>&nbsp;</td><td>".$nameResults['firstName']." ".$nameResults['lastName']."</td></tr>";
			}
		}
	}
	
	echo "</table>";
	echo "</div>";
	
?>
<div style="clear:both; text-align:right; padding-right: 100px;">
	<p>&nbsp;</p>
	
</div>
<p>
	<a href="addClassForm.php?<?php echo "season=$season&amp;year=$year"; ?>">Add class to term.</a><br />
	<a href="removeClassForm.php?<?php echo "season=$season&amp;year=$year"; ?>">Remove class from term.</a>
</p>


<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>