<?php
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>	
<div style="text-align:center">
	<h1>Report Broken Item</h1>
	<form method="POST" action="php/brokenItem.php">
		
		<table style="text-align:left" align="center">
			<tr>
				<th>Item in disrepair: </th>
				<td><input name="item" type="text" size="30"/></td>
				</tr>
			<tr>
				<th>Problem with item: </th>
				<td><textarea name="description" cols="50" rows="10"></textarea></td>
				</tr>
			<tr>
				<th></th>
				<td><input name="submit" type="submit" /> &nbsp;<input name="reset" type="reset" /></td>
				</tr>
			</table>
		
		</form>
</div>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>