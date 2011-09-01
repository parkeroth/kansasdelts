<?php
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'scholarship', 'recruitment');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>

<h1>Scholarship Entries</h1>
    	<p>&nbsp;</p>
		
		<?
if($_GET['opencaptcha']=='failed') { echo "<script>alert('You Did Not Fill In The Security Code Correctly');</script>";}
$date = date("Ymd");
$rand = rand(0,9999999999999);
$height = "80";
$width  = "240";
$img    = "$date$rand-$height-$width.jpgx";
echo "<input type='hidden' name='img' value='$img'>";
echo "<a href='http://www.opencaptcha.com'><img src='http://www.opencaptcha.com/img/$img' height='$height' alt='captcha' width='$width' border='0' /></a><br />";
echo "<input type=text name=code value='Enter The Code' size='35' />";
?>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>