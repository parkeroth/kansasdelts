<?php
session_start();
$authUsers = array('brother');
include_once('../php/authenticate.php');

$db_connection = mysql_connect ($db_host, $db_username, $db_password) OR die (mysql_error());  
$db_select = mysql_select_db ($db_database) or die (mysql_error());
$db_table = $TBL_PR . "events";


$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);


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

$month = addslashes($_GET['month'] - 1);
$year = addslashes($_GET['year']);

$query = "SELECT DAY(date) AS day FROM baddDutyDays WHERE MONTH(date)='".date ("n", mktime(0,0,0,$_GET['month'],1,$_GET['year']))."'";
$query_result = mysql_query ($query);
while ($info = mysql_fetch_array($query_result))
{
	$day = $info[day];
    $events[$day] = true;
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



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html><!-- InstanceBegin template="/Templates/Default.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<!-- InstanceBeginEditable name="doctitle" -->

<title>Delta Tau Delta Fraternity: Gamma Tau Chapter</title>

<!-- InstanceEndEditable --> 

<link rel="icon" type="image/vnd.microsoft.icon" href="favicon.ico" />

<link rel="stylesheet" href="../styles/style.css" type="text/css" />

<link rel="stylesheet" href="../styles/menuh.css" type="text/css" />

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<meta name="author" content="DTD">

	<meta name="keywords" content="delta tau delta, kansas university, fraternity">

	<meta name="description" content="The website for the Kansas University Delta Tau Delta fraternity.">

    <!-- InstanceBeginEditable name="head" -->
<link href="../styles/cal2.css" rel="stylesheet" type="text/css">
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
	<!-- InstanceEndEditable -->

</head>



<body>



<div id="container">

  <div id="header">
  

         <div id="headerimg">
         	<img src="../img/header.jpg"  alt="" />
          <div id="headerNavBarLeft">
            	<?php echo strtolower(date("l, F j, Y"));?>
            </div>
         	<div id="headerNavBarRight">
               	<a href="../account.php">my account</a> | 
                <?php
					if(isset($_SESSION['username'])){ ?>
						<a href="../logout.php">logout</a> <?php
					} else { ?>
						<a href="../loginForm.php">login</a> <?php
					}
				?>
            </div>
         </div>
    </div>

    	<!-- end the menuh div -->

    <div id="beef">
		<div id="menuh-container">

      <div id="menuh">

            <ul>

                <li><a href="../index.php" class="top_parent">Home</a></li>

            </ul>

            <ul>	

                <li><a href="#" class="top_parent">About Us</a>

                <ul>

                    <li><a href="../meetTheGuys.php">Meet the Guys</a></li>

                    <li><a href="../creed.php">Delt Creed</a></li>

                    <li><a href="../gammatau.php">Gamma Tau</a></li>
					
					<ul>

						<li><a href="#" class="top_parent">Photos</a>
		
						<ul>
		
							<!--<li><a href="housetour.html">House Tour</a></li>-->
		
							<li><a href="../photos/social.php">Social</a></li>
							<li><a href="../photos/service.php">Service</a></li>
							<li><a href="../photos/alumni.php">Alumni</a></li>
		
						</ul>
		
						</li>
		
					</ul>
                    
                    <li><a href="../contactUs.php">Contact Us</a></li>

                    <li><a href="http://delts.org" target="_blank">National Site</a></li>

                </ul>

                </li>

            </ul>

            <ul>

                <li><a href="#" class="top_parent">Scholarship</a>

                <ul>

                    <li><a href="../ryanFamilySummary.php">Ryan Family</a></li>
					
                </ul>

                </li>

            </ul>

            <ul>

                <li><a href="../alumni.php" class="top_parent">Alumni</a>
                	<ul>

                   		<li><a href="../recruitment/forms/submitAlumni.php">Refer a Man</a></li>
                        <li><a href="../contactUs.php">Contact Chapter</a></li>

                	</ul>
                </li>

            </ul>

            <ul>

                <li><a href="../account.php" class="top_parent">Members</a>
                	
					
					<?php if(isset($_SESSION[username])) { ?>
                    <ul>
                    	
						<li><a href="../account.php">My Account</a></li>
                        <li><a href="#">My Academics</a>
						
						<ul>
		
							<li><a href="../schedule.php">My Schedule</a></li>
							<li><a href="../classSearchForm.php">Search Classes</a></li>
		
						</ul>
						
						</li>
                        <li><a href="../calendar.php">Calendar</a></li>
						<li><a href="../baddDutyDates.php">BADD Duty</a></li>
						<li><a href="#">Forms</a>
						
						<ul>
		
							<li><a href="../writeUpForm.php">Write Up Form</a></li>
							<li><a href="../ideaForm.php">Submit Idea</a></li>
							<li><a href="../brokenItemForm.php">Broken Item</a></li>
							<?php 
							if($_SESSION["userType"] != "|brother")
							{
								echo "<li><a href=\"../taskSheetForm.php\">Weekly Report</a></li>";
							}?>
		
						</ul>
						
						</li>
                        <li><a href="../showRoster.php" target="_blank">Roster</a></li>
						<li><a href="../documents.php">Document Box</a></li>
						
					</ul>
					<?php } ?>
                    
                </li>

            </ul>

            <ul>

                <li><a href="../rushdelt.php" class="top_parent">Rush Delt</a>
                
                	<ul>

                   		<li><a href="../unique.php">Why Delt?</a></li>
                    	<li><a href="../heritage.php">Heritage</a></li>
                    	<li><a href="../parents.php">Parents</a></li>
                        <li><a href="../values.php">Values</a></li>
                        <li><a href="../scholarship.php">Scholarship</a></li>
                        <li><a href="../recruitment/forms/submitSelf.php">Rush Form</a></li>

                	</ul>
                
                </li>

            </ul>

            

        </div> 	<!-- end the menuh-container div -->  

    </div>
	<!-- InstanceBeginEditable name="Enter Content" --><div align="center"><span class="currentdate"><? echo date ("F Y", mktime(0,0,0,$_GET['month']-1,1,$_GET['year'])); ?></span><br>
  <br>
</div>
<div align="center"><br>
  <table width="600" border="0" cellspacing="0" cellpadding="0">
    <tr> 
      <td><div align="right"><a href="<? echo "baddAddCal.php?month=$prev_month&amp;year=$prev_year"; ?>">&lt;&lt;</a></div></td>
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
				echo "<option value=\"baddAddCal.php?month=$link&amp;year=$_GET[year]\" $selected>" . date ("F", mktime(0,0,0,$i,1,$_GET['year'])) . "</option>\n";
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
		  	echo "<option value=\"baddAddCal.php?month=$_GET[month]&amp;year=$i\" $selected>$i</option>\n";
		  }
		  ?>
          </select>
        </div></td>
      <td><div align="left"><a href="<? echo "baddAddCal.php?month=$next_month&amp;year=$next_year"; ?>">&gt;&gt;</a></div></td>
    </tr>
  </table>
  <br>
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
		for ($i = 1; $i <= $days_in_month; $i++) {
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
			
			if($events[$i]){ $class = "baddDay"; }
			
			$link_month = $_GET['month'] - 1;
			echo "<td width=\"100\" height=\"100\" class=\"$class\"";
			
			if($events[$i]){ $class = "baddDay"; $action = "remove";}
			else { $action = "add"; }
			
			echo "onclick=\"window.location.href='../php/baddAdd.php?date=$_GET[year]-$_GET[month]-$i&amp;action=$action'\">\n";
			
			echo "<div align=\"right\"><span class=\"toprightnumber\">\n$i&nbsp;</span></div>\n";
			
			
			echo "</td>\n";
			IF(($count_boxes == 7) AND ($days_so_far != (($first_day_of_month-1) + $days_in_month))){
				$count_boxes = 0;
				echo "</TR><TR valign=\"top\">\n";
			}
		}
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
<!-- InstanceEndEditable --> 

    </div>

	
	<div id="footer">
    	<img src="../img/footer.jpg" />
		<div id="footerLinks">
		<h3><a href="../rushdelt.php">Rush</a> | <a href="../contactUs.php">Contact Us</a> | <a href="../alumni.htm">Alumni</a> | <a href="../account.php">Members</a></h3></div>
        <div id="footerContent">
    		&copy; Copyright <?php echo date("Y"); ?> Kansas University Delta Tau Delta | Design by <a href="http://www.love-revolution.org">Heather Onnen</a> | Webmaster <a href="mailto:parkeroth@gmail.com" title="[ Email Webmaster ]">Parker Roth</a>
        </div>
    </div>

</div>

<!-- end container div -->
<!-- InstanceBeginEditable name="EditRegion5" --><!-- InstanceEndEditable -->
</body>



<!-- InstanceEnd --></html>