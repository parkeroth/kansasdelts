<?php
	session_start();
?>
<?php require('plogger.php'); ?>
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<style type="text/css">
	#exif-data {
		color: #666 !important;
	}
	body {
		background-color: #181818 !important;
	}
	#footerLinks {
		color: #EFBA2C !important;
	}
	#footer {
		color: #EFBA2C !important;
	}
</style>

<?php the_plogger_head(); ?>
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<?php the_plogger_gallery(); ?>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>