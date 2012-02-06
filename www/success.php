<?php
	session_start();
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>

<?php 
	
		$url = $_SERVER['HTTP_REFERER']; //Get previous URL
		
		if($_GET['page'] == "AlumniReferral"){ //If from alumni referal
			
			echo "<p>&nbsp;</p>";
			echo "<strong>";
			echo "<p style=\"text-align: center;\">Thank you for your referal.</p>";
			echo '<p style="text-align: center;"><a href="alumniReferralForm.php">Click here</a> if you would like to refer another man.</p>';
			echo "</strong>";
			
		} else if($_GET['page'] == "rushSelf"){
			echo "<p>&nbsp;</p>";
			echo "<strong>";
			echo "<p style=\"text-align: center;\">Thank you for your interest.</p>";
			echo '<p style="text-align: center;">A recruitment member will contact you soon.</p>';
			echo "</strong>";
		} else if($_GET['page'] == "rushAlumni"){
			echo "<p>&nbsp;</p>";
			echo "<strong>";
			echo "<p style=\"text-align: center;\">Thank you for the referral.</p>";
			echo '<p style="text-align: center;">A member of the recruitment committee will contact them shortly.</p>';
			echo "</strong>";
		} else if($_GET['page'] == "rushMember"){
			echo "<p>&nbsp;</p>";
			echo "<strong>";
			echo "<p style=\"text-align: center;\">Thank you for the referral.</p>";
			echo '<p style="text-align: center;">A member of the recruitment committee will contact them shortly.</p>';
			echo "</strong>";
		} else if($_GET['page'] == "ContactUs"){
			echo "<p>&nbsp;</p>";
			echo "<strong>";
			echo "<p style=\"text-align: center;\">Your question was submitted successfully.</p>";
			echo "</strong>";
		} else if($_GET['page'] == "Bylaws"){
			echo "<p>&nbsp;</p>";
			echo "<strong>";
			echo "<p style=\"text-align: center;\">Bylaws uploaded successfully.</p>";
			echo "</strong>";
		} else if($_GET['page'] == "TextMessage"){
			echo "<p>&nbsp;</p>";
			echo "<strong>";
			echo "<p style=\"text-align: center;\">Text message sent successfully.</p>";
			echo "</strong>";
		} else if($_GET['page'] == "MissedDuty"){
			echo "<p>&nbsp;</p>";
			echo "<strong>";
			echo "<p style=\"text-align: center;\">Missed duty was successfully reported.</p>";
			echo "</strong>";
		} else if($_GET['page'] == "fine"){
			echo "<p>&nbsp;</p>";
			echo "<strong>";
			echo "<p style=\"text-align: center;\">Fine submission was successful.</p>";
			echo "</strong>";
		} else {
			echo "<p>&nbsp;</p>";
			echo '<p style="text-align: center;">How did you get here?</p>';
		}
?>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>