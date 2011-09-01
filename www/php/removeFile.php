<?php
	
	unlink($_GET[name]);
	
	header("location: ../$_GET[return]");
	
?>