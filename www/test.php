<?php
<<<<<<< HEAD

phpinfo();
=======
require_once 'classes/Event.php';

$event = new Event();
//echo $event->type;
$event->title = 'test';
$event->event_date = '2011-11-11';
$event->term = 'spring';
$event->time = '1:00 AM';
$event->type = 'general';
$event->insert();
echo $event->id;

$event->delete();
echo $event->id;
>>>>>>> database_interaction_update

?>