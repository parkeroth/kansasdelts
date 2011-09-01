<?php 
session_start();
header("Cache-control: private");

include_once('login.php');
$isAuthorized = false;

$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$accountType = "
	SELECT accountType 
	FROM members
	WHERE username='".$_SESSION['username']."'";
	
$getAccountType = mysqli_query($mysqli, $accountType);

$accountTypeArray = mysqli_fetch_array($getAccountType, MYSQLI_ASSOC);

$accountType = $accountTypeArray['accountType'];

for($i=0; $i < count($authUsers); $i++){
	if($authUsers[$i] == $accountType){
		$isAuthorized = true;
	} else if($authUsers[$i] == 'brother'){
		$isAuthorized = true;
	}
}

if ($_SESSION["access"] == "granted" && $isAuthorized == true) {
	//Blank
} else {
	header("Location: loginForm.php");
}
?>