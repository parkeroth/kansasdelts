<?php
	session_start();
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>
<?php

		$url = $_SERVER['HTTP_REFERER']; //Get previous URL

		if($_GET['page'] == "newReport"){ //If from alumni referal

			echo "<p>&nbsp;</p>";
			echo "<strong>";
			echo "<p style=\"text-align: center;\">Report already submitted for that meeting!</p>";
			echo "</strong>";

		} if($_GET['page'] == "unauthorized"){ //If from alumni referal

			echo "<p>&nbsp;</p>";
			echo "<strong>";
			echo "<p style=\"text-align: center;\">Were you trying to look at something you weren't supposed to?</p>";
			echo "</strong>";

		} if($_GET['page'] == "board-assign") {
			echo '<p>&nbsp;</p>';
			echo '<p style="text-align: center;">Chapter meeting has no board meeting assigned to it.</p>';
		}else {
			echo "<p>&nbsp;</p>";
			echo '<p style="text-align: center;">How did you get here?</p>';
		}		
?>
<p style="text-align: center;">
<img src="img/sad_panda.png" />
</p>
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>
