<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
echo 'A';
require_once('classes/Task.class.php');
echo 'B';
$m = Task::objects()->get(1);
echo 'C';
foreach($m as $thing)
	print $thing->title;
echo 'D';
?>
