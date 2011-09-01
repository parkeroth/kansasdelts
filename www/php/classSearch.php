<?php
session_start();
$authUsers = array('brother');
include_once('authenticate.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><!-- InstanceBegin template="/Templates/Default.dwt.php" codeOutsideHTMLIsLocked="false" -->
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
	<!-- InstanceBeginEditable name="Enter Content" -->
	<?php
include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$department = $_POST['department'];
$department = strtoupper($department);

if($_POST['section'] != NULL){
	
$ClassSearch = "
			SELECT username, termYear, termSeason 
			FROM classes 
			WHERE department='".$department."'
			AND section='".$_POST['section']."'";
	
		$getClassSearch = mysqli_query($mysqli, $ClassSearch);
		$counter = 0;
		
		echo "<table>";
		echo '<tr><td valign="top" style="padding-right:10px; font-weight: bold;">'.$department." ".$_POST['section']."</td><td>";
		while ($searchResults = mysqli_fetch_array($getClassSearch, MYSQLI_ASSOC)){
			
			$NameSearch = "
				SELECT firstName, lastName 
				FROM members 
				WHERE username='".$searchResults['username']."'";
			
			$getNameSearch = mysqli_query($mysqli, $NameSearch);
			$nameResults = mysqli_fetch_array($getNameSearch, MYSQLI_ASSOC);
			
			echo $nameResults['firstName']." ".$nameResults['lastName']." | ".ucfirst($searchResults['termSeason'])." ".$searchResults['termYear']."<br>";
			$counter++;
		}
	if ($counter == 0){
		echo "No recods for ".$_POST['department']." ".$_POST['section'];
	}
	echo '</td></tr></table>';
} else {
	$ClassSearch = "
			SELECT DISTINCT section 
			FROM classes 
			WHERE department='".$department."'
			ORDER BY section";
	
		$getClassSearch = mysqli_query($mysqli, $ClassSearch);
		
		while ($searchResults = mysqli_fetch_array($getClassSearch, MYSQLI_ASSOC)){
			
			echo "<table>";
			
			$SectionSearch = "
				SELECT username, termYear, termSeason
				FROM classes
				WHERE department='".$department."'
				AND section='".$searchResults['section']."'";
				
			$getSectionSearch = mysqli_query($mysqli, $SectionSearch);
			$counter = 0;
			
			
			
			echo '<tr><td valign="top" style="padding-right:10px; font-weight: bold;">'.$department." ".$searchResults['section']."</td><td>";
			
			while ($sectionResults = mysqli_fetch_array($getSectionSearch, MYSQLI_ASSOC)){
				
				$NameSearch = "
					SELECT firstName, lastName 
					FROM members 
					WHERE username='".$sectionResults['username']."'";
			
				$getNameSearch = mysqli_query($mysqli, $NameSearch);
				$nameResults = mysqli_fetch_array($getNameSearch, MYSQLI_ASSOC);
			
				echo $nameResults['firstName']." ".$nameResults['lastName']." | ".ucfirst($sectionResults['termSeason'])." ".$sectionResults['termYear']."<br>";
				$counter++;
			}
			echo '</td></tr></table>';
			
			if ($counter == 0){
				echo "No recods for ".$_POST['department']." ".$_POST['section'];
			}
		}
}
?>
	<p>&nbsp;</p>
	<p><a href="../schedule.php">Click here</a> to return to academic page.</p>
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