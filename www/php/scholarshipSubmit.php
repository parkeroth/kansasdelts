<?php
include_once("login.php");

mysql_connect($db_host,$db_username,$db_password);
mysql_select_db($db_database) or die("Unable to select database");


if(file_get_contents("http://www.opencaptcha.com/validate.php?ans=".$_POST['code']."&img=".$_POST['img'])=='pass') {
  $add_sql = "	INSERT INTO scholarshipResults (name, address, city, state, zip, phone, email, gpa, highSchool, classRank, intendedMajor, honors, extracurricular, work, essayOption, essayAnswer, time) 
				VALUES (
					'".mysql_real_escape_string($_POST[name])."', 
					'".mysql_real_escape_string($_POST[address])."', 
					'".mysql_real_escape_string($_POST[city])."', 
					'".mysql_real_escape_string($_POST[state])."', 
					'".mysql_real_escape_string($_POST[zip])."', 
					'".mysql_real_escape_string($_POST[phone])."', 
					'".mysql_real_escape_string($_POST[email])."', 
					'".mysql_real_escape_string($_POST[gpa])."', 
					'".mysql_real_escape_string($_POST[highSchool])."', 
					'".mysql_real_escape_string($_POST[classRank])."', 
					'".mysql_real_escape_string($_POST[intendedMajor])."', 
					'".mysql_real_escape_string($_POST[honors])."', 
					'".mysql_real_escape_string($_POST[extracurricular])."',
					'".mysql_real_escape_string($_POST[work])."',
					'".mysql_real_escape_string($_POST[essayOption])."',
					'".mysql_real_escape_string($_POST[essayAnswer])."',
					'".date( 'Y-m-d H:i:s')."'
					)";

mysql_query($add_sql);

header("Location: ../scholarshipSuccess.php");
} else {
  header("LOCATION: ".$_SERVER['HTTP_REFERER']."?opencaptcha=failed");
}


?>