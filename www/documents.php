<?php
session_start();
include_once('php/login.php');
$authUsers = array('brother');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>

<h1>Document Box</h1>
	<h3>Reference</h3>
		<ul>
			<li><a href="docs/bylaws.docx">Gamma Tau Bylaws</a></li>
			<li><a href="docs/houseRules.docx">House Rules</a></li>
		</ul>
	<h3>Forms</h3>
		<ul>
			<li><a href="docs/IndividualCommunityService.docx">Individual Community Service Form</a></li>
			<li><a href="docs/Reimbursement.docx">Financial Reimbursement Form</a></li>
		</ul>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>