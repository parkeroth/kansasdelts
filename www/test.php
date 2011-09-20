<?php
echo $session->member_id;
require_once 'classes/Member.php';
$member = new Member(NULL, NULL, 38);
echo $member->accountType;

?>