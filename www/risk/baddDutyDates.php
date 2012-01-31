<?php
$authUsers = array('brother');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';
include_once '../php/login.php';

$db_connection = mysql_connect ($db_host, $db_username, $db_password) OR die (mysql_error());  
$db_select = mysql_select_db ($db_database) or die (mysql_error());


$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$super_list = array('admin', 'drm', 'pres');
$haz_super_powers = $session->isAuth($super_list);

function getmicrotime(){ 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec); 
} 

$time_start = getmicrotime();

IF(!isset($_GET['year'])){
    $_GET['year'] = date("Y");
}
IF(!isset($_GET['month'])){
    $_GET['month'] = date("n")+1;
}


//Get the dates the user is assigned to
$myDatesData = "
	SELECT date 
	FROM baddDutyLog
	WHERE username = '$_SESSION[username]'
	AND date >= '".date("Y-m-d")."'";

$getMyDatesData = mysqli_query($mysqli, $myDatesData);
while($daysDataArray = mysqli_fetch_array($getMyDatesData, MYSQLI_ASSOC))
{
	$myDays[] = $daysDataArray['date'];
}

$numMyDays = count($myDays);
//End get dates user is assigned to

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
	$memberCount++;
}

$month = addslashes($_GET['month'] - 1);
$year = addslashes($_GET['year']);

$query = "SELECT DAY(date) AS day, date, status, ID 
			FROM baddDutyDays 
			WHERE MONTH(date)='".date ("n", mktime(0,0,0,$_GET['month']-1,1,$_GET['year']))."'
				AND YEAR(date) = '$_GET[year]'";
$query_result = mysql_query ($query);
while ($info = mysql_fetch_array($query_result))
{
	$day = $info['day'];
    $events[$day] = true;
	$status[$day] = $info['status'];
	$ids[$day] = $info['ID'];
} //end while

$todays_date = date("j");
$todays_month = date("n");

$days_in_month = date ("t", mktime(0,0,0,$_GET['month'],0,$_GET['year']));
$first_day_of_month = date ("w", mktime(0,0,0,$_GET['month']-1,1,$_GET['year']));
$first_day_of_month = $first_day_of_month + 1;
$count_boxes = 0;
$days_so_far = 0;

IF($_GET['month'] == 13){
    $next_month = 2;
    $next_year = $_GET['year'] + 1;
} ELSE {
    $next_month = $_GET['month'] + 1;
    $next_year = $_GET['year'];
}

IF($_GET['month'] == 2){
    $prev_month = 13;
    $prev_year = $_GET['year'] - 1;
} ELSE {
    $prev_month = $_GET['month'] - 1;
    $prev_year = $_GET['year'];
}


include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<link type="text/css" href="../styles/popUp.css" rel="stylesheet" />
<link type="text/css" href="../styles/BADD.css" rel="stylesheet" />
<link type="text/css" href="../styles/cal.css" rel="stylesheet" />

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

<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/BADD.js"></script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<div align="center"><span class="currentdate"><? echo date ("F Y", mktime(0,0,0,$_GET['month']-1,1,$_GET['year'])); ?></span><br>
	<br>
</div>
<div align="center"><br>
	
	<table width="600" border="0" cellspacing="0" cellpadding="0">
		<tr> 
			<td><div align="right"><a href="<? echo "baddDutyDates.php?month=$prev_month&amp;year=$prev_year"; ?>">&lt;&lt;</a></div></td>
			<td width="200"><div align="center">
				
				<select name="month" id="month" onChange="MM_jumpMenu('parent',this,0)">
					<?
			for ($i = 1; $i <= 12; $i++) {
				$link = $i+1;
				IF($_GET['month'] == $link){
					$selected = "selected";
				} ELSE {
					$selected = "";
				}
				echo "<option value=\"baddDutyDates.php?month=$link&amp;year=$_GET[year]\" $selected>" . date ("F", mktime(0,0,0,$i,1,$_GET['year'])) . "</option>\n";
			}
			?>
					</select>
				<select name="year" id="year" onChange="MM_jumpMenu('parent',this,0)">
					<?
		  $yearLoop = date("Y");
		  
		  for ($i = $yearLoop; $i <= $yearLoop+1; $i++) {
		  	IF($i == $_GET['year']){
				$selected = "selected";
			} ELSE {
				$selected = "";
			}
		  	echo "<option value=\"baddDutyDates.php?month=$_GET[month]&amp;year=$i\" $selected>$i</option>\n";
		  }
		  ?>
					</select>
				</div></td>
			<td><div align="left"><a href="<? echo "baddDutyDates.php?month=$next_month&amp;year=$next_year"; ?>">&gt;&gt;</a></div></td>
			</tr>
		</table>
	<?php if($haz_super_powers) { ?>
	<p>To fill empty slots with people <a href="baddRandom.php?<?php echo "year=$_GET[year]&amp;month=$_GET[month]"; ?>">click here</a>.</p>
	<?php } ?>
	<?php if($myTrading) { ?>
	<p style="text-align:left">You currently have a <b>trade request outstaning</b>. Please wait for the other person to respond. If you would like to cancel this request click the day you are trading (it is blue).</p>
	<?php } ?>
	<?php if($theirTrading) { ?>
	<p style="text-align:left">Someone has asked to trade days with you. Please respond to this reqest either at the main page or by clicking the day. (it is purple).</p>
	<?php } ?>
	<br />
</div>
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
				<?
		for ($i = 1; $i <= $first_day_of_month-1; $i++) {
			$days_so_far = $days_so_far + 1;
			$count_boxes = $count_boxes + 1;
			echo "<td width=\"100\" height=\"100\" class=\"beforedayboxes\"></td>\n";
		}
		
		?>
				
				<?php
		for ($i = 1; $i <= $days_in_month; $i++)
		{
   			$days_so_far = $days_so_far + 1;
    			$count_boxes = $count_boxes + 1;
			IF($_GET['month'] == $todays_month+1){
				IF($i == $todays_date){
					$class = "highlighteddayboxes";
				} ELSE {
					$class = "dayboxes";
				}
			} ELSE {
				$class = "dayboxes";
				
			}
			$linkMonth = $_GET[month]-1;
			$queryDate = $year."-".$linkMonth."-".$i;
			$formattedQueryDate = $year."-".sprintf("%02d",$linkMonth)."-".sprintf("%02d",$i);
			
			if($events[$i])
			{ 
				//Set Action Variables
				$updateAction = "add";
				$confirmMessage = "Sign up for this day?";
				
				$query = "
					SELECT username
					FROM baddDutyLog
					WHERE date = '$queryDate'";
				$result = mysql_query ($query);
				while ($row = mysql_fetch_array($result))
				{
					for($j=0; $j < $memberCount; $j++)
					{
						if($row[username] == $members[$j][username])
						{
							$assigned[] = $members[$j][firstName]." ".$members[$j][lastName];
							$assignedUsers[] = $row[username];
						}
						if($row[username] == $_SESSION[username]){
							$updateAction = "remove";
							$confirmMessage = "Remove yourself from this day?";
						}
					}
				}
				
				//Set Class of day
				$class = "baddDayAvailable";
				
				if($updateAction == "remove"){
					$class = "baddDayYours";
				}
				else if($status[$i] == "closed")
				{
					$class = "baddDayClosed";
				}
				else if(count($assigned) > 1)
				{ 
					$class = "baddDayFull";
				}
				
				if(date("Y-m-j") > $formattedQueryDate)
				{
					$class = "baddDayPast";
				}
				
				//Set Day Action
				$dayAction = "remove";
			}
			else
			{
				$dayAction = "add";
			}
			
			
			echo "<td width=\"100\" height=\"100\" class=\"$class\">\n";
			echo "<div align=\"right\"><span class=\"toprightnumber\">\n";
			
			//Set details for day number
			if($haz_super_powers)
			{
				echo "<a class=\"topRightNum\" href=\"baddDayAction.php?date=$formattedQueryDate&amp;action=$dayAction\"";
				if($dayAction == "remove")
				{
					if($class == "baddDayPast")
					{
						echo "onclick=\"return confirm('This day has already passed and its should be kept for the records. Would you still like to remove this day?')\"";
					}
					else
					{
						echo "onclick=\"return confirm('Are you sure you want to remove this day?')\"";
					}
				}
				echo ">";
				echo "$i&nbsp;</a>";
			}
			else
			{
				echo "$i&nbsp;";
			}
			echo "</span></div>\n";
			//end details for day number
			
			if($events[$i])
			{	
				//Voluntarily Add or remove yourself from a day
				echo "<div class=\"baddDayActive\"";
				if(( count($assigned) < 2 || in_array($_SESSION[username], $assignedUsers) ) && $status[$i] == "open" || $class == 'baddDayMyTrade')
				{
					echo "onclick=\"if(confirm('".$confirmMessage."')) window.location.href='baddAction.php?action=$updateAction&amp;date=$formattedQueryDate'\"";
				}
				echo ">";
				//end add/remove
				
				//Fill in day with people assigned
				for($k=0; $k < count($assigned); $k++)
				{
					echo "<p>";
					/*
					if( !in_array($_SESSION[username], $assignedUsers) && $class != "baddDayPast")
					{
						if(!$myTrading && !$theirTrading)//If no trade is currently active
						{
							//If user has no days to trade
							if($numMyDays == 0){
								echo "<a href=\"javascript: alert('You have no dates to trade!');\">".$assigned[$k]."</a>";
								
							//If user can trade
							} else {
								echo "<span id=\"$assignedUsers[$k]".date ("Y-m-d", mktime(0,0,0,$_GET['month']-1,$i,$_GET['year']))."$assigned[$k]\" 
											class=\"tradable\"><a href=\"#\">$assigned[$k]</a></span>";
							}
						}
						else if($myTrading)
						{
							echo "	<a href=\"javascript: alert('
										You are currently negotiating a trade. Please either cancel that transaction or conclude it.
									');\">".$assigned[$k]."</a>";
						}
						else if($theirTrading)
						{
							echo "	<a href=\"javascript: alert('
										Someone has requested to trade days with you. Please respond to that request first.
									');\">".$assigned[$k]."</a>";
						}
					} // end if
					else
					{ */
					if($haz_super_powers)
					{
						echo "<span id=\"$assignedUsers[$k]$ids[$i]\" class=\"tradable\"><a href=\"#\">$assigned[$k]</a></span>";
					}
					else
					{
						echo $assigned[$k];
					}
					//}
					
					echo "</p>";
					
				} // end for
				
				unset($assigned);
				unset($assignedUsers);
		
				echo "</div>";
			} // end if
			
			
			echo "</td>\n";
			
			if(($count_boxes == 7) AND ($days_so_far != (($first_day_of_month-1) + $days_in_month))){
				$count_boxes = 0;
				echo "</TR><TR valign=\"top\">\n";
			}
			
			
		} // end for
		?>
				
				<?php
		$extra_boxes = 7 - $count_boxes;
		for ($i = 1; $i <= $extra_boxes; $i++) {
			echo "<td width=\"100\" height=\"100\" class=\"afterdayboxes\"></td>\n";
		}
		$time_end = getmicrotime();
		$time = round($time_end - $time_start, 3);
		?>
				</tr>
			</table></td>
		</tr>
</table>


<!-- end container div -->
<div id="popupNotification">
	
	<h2>Swap Members</h2>
	
	<div id="popupNotificationClose"><a href="#">x</a></div>
	
	<form name="trade" id="trade" action="php/baddDutyTrade.php" method="post">
	<p>
		
		<select id="userSelect" name="newPerson">
			
			<?
	for($x = 0; $x < $memberCount; $x++){
		echo "<option id=\"".$members[$x][username]."\" value=\"".$members[$x][username]."\">".$members[$x][firstName]." ".$members[$x][lastName]."</option>\n";
	}
	?>
			
			</select>
		
		</p>
		
		<input id="origPerson" type="hidden" name="origPerson" value=""/>
		<input id="dayID" type="hidden" name="id" value=""/>
		
	<p><input name="submit" value="Change" type="submit" /></p>
	</form>
	
</div>
	
<div id="backgroundPopup"></div>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>