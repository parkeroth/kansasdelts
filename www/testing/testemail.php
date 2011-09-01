<?php
/**
* Simple example script using PHPMailer with exceptions enabled
* @package phpmailer
* @version $Id$
*/

require '../php/mailTo.php';

$positions[] = 'webmaster';

mailPosition($positions, 'admin@ku.edu', 'Test Email');
?>