<?php

include_once('util.php');

$str = '(316) 684-1919';
$num = strip_phone($str);

echo formatPhone($num);

?>