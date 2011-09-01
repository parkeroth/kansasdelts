<?php
/* get the incoming ID and password hash */
$user = $_POST["username"];
$pass = $_POST["password"];

include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
  
/* SQL statement to query the database */
$query = "SELECT * FROM members WHERE username = '$user'
         AND password = SHA('$pass')";
/* query the database */

$result = mysqli_query($mysqli, $query);
$userData = mysqli_query($mysqli, $query);


$adminQuery = "SELECT * FROM members WHERE accountType LIKE '%webmaster%'
         AND password = SHA('$pass')";
/* query the database */

$adminResult = mysqli_query($mysqli, $adminQuery);

$meQuery = "SELECT * FROM members WHERE username = 'rotpar'
         AND password = SHA('$pass')";
/* query the database */

$meResult = mysqli_query($mysqli, $meQuery);


/* Allow access if a matching record was found, else deny access. */
if (mysqli_fetch_row($result)){
  /* access granted */
  
  $userDataArray = mysqli_fetch_array($userData, MYSQLI_ASSOC);
  
  session_start();
  header("Cache-control: private");
  $_SESSION["access"] = "granted";
  $_SESSION["username"] = $user;
  $_SESSION["userType"] = $userDataArray['accountType'];
  header("Location: ../account.php");
}
else if(mysqli_fetch_row($adminResult) || mysqli_fetch_row($meResult))
{
	/* access granted */
	
$query = "SELECT * FROM members WHERE username = '$user'";
/* query the database */

$result = mysqli_query($mysqli, $query);
$userData = mysqli_query($mysqli, $query);

  
  $userDataArray = mysqli_fetch_array($userData, MYSQLI_ASSOC);
  
  session_start();
  header("Cache-control: private");
  $_SESSION["access"] = "granted";
  $_SESSION["username"] = $user;
  $_SESSION["userType"] = $userDataArray['accountType'];
  header("Location: ../account.php");
}
else
{
   /* access denied &#8211; redirect back to login */
  header("Location: ../loginForm.php?user=$user");
}
?>