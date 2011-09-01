<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_Testing = "localhost:3306";
$database_Testing = "delt";
$username_Testing = "root";
$password_Testing = "root";
$Testing = mysql_pconnect($hostname_Testing, $username_Testing, $password_Testing) or trigger_error(mysql_error(),E_USER_ERROR); 
?>