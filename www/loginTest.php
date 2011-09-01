<?php
//echo "start";
$authUsers = array('admin');
include_once($_SERVER['DOCUMENT_ROOT'].'/loginSystem/include/session.php');
//echo "done with include";

//test to check if authorized
echo 'Auth Type Evaluated to: '.$session->checkAuthType($authUsers);

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>

<?php
	
	echo 'success!';	

?>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>