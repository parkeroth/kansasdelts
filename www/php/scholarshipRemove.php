<?php
include_once("login.php");

mysql_connect($db_host,$db_username,$db_password);
mysql_select_db($db_database) or die("Unable to select database");


$add_sql = "	DELETE FROM scholarshipResults WHERE ID = '$_GET[ID]'";

mysql_query($add_sql);

header("Location: ../scholarshipReview.php");

?>