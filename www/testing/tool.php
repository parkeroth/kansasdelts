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
	<link rel="stylesheet" type="text/css" href="../styles/tool-tips.css" />
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
	
	<h1>Examples of CSS ToolTips!</h1>
		<p><a class="tooltip" href="#">Classic<div class="classic">asdf
		
		<table style="text-align:center;" align="center">
	<tr style="text-align: center; font-weight: bold;"><td>Event</td><td>Hours</td></tr><tr><td>Manual Adjustment</td><td>-1</td></tr><tr><td>Manual Adjustment</td><td>4</td></tr></table>
		
		</div></a></p>
	
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