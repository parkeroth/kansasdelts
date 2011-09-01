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
	
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<input type="hidden" name="cmd" value="_xclick">
		<input type="hidden" name="business" value="dtdadmin@kansasdelts.org">
		<input type="hidden" name="lc" value="US">
		<input type="hidden" name="item_name" value="Southern Tide T-Shirt">
		<input type="hidden" name="item_number" value="123456">
		<input type="hidden" name="button_subtype" value="services">
		<input type="hidden" name="no_note" value="0">
		<input type="hidden" name="tax_rate" value="0.000">
		<input type="hidden" name="shipping" value="0.00">
		<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHostedGuest">
		<table>
		<tr><td><input type="hidden" name="on0" value="Sizes">Sizes</td></tr><tr><td><select name="os0">
			<option value="Small">Small $15.00</option>
			<option value="Medium">Medium $15.00</option>
			<option value="Large">Large $15.00</option>
			<option value="Extra Large">Extra Large $20.00</option>
		</select> </td></tr>
		</table>
		<input type="hidden" name="currency_code" value="USD">
		<input type="hidden" name="option_select0" value="Small">
		<input type="hidden" name="option_amount0" value="15.00">
		<input type="hidden" name="option_select1" value="Medium">
		<input type="hidden" name="option_amount1" value="15.00">
		<input type="hidden" name="option_select2" value="Large">
		<input type="hidden" name="option_amount2" value="15.00">
		<input type="hidden" name="option_select3" value="Extra Large">
		<input type="hidden" name="option_amount3" value="20.00">
		<input type="hidden" name="option_index" value="0">
		<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
		<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</form>
	
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