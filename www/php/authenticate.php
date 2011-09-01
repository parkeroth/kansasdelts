<?php 
header("Cache-control: private");
include_once($_SERVER['DOCUMENT_ROOT'].'/loginSystem/session.php');
$session->checkAuthType($authUsers);
?>