<?php
session_start();
include_once('php/login.php');
$authUsers = array('brother');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>

<div style="text-align:center">
	<h1>Idea Form</h1>
	<form method="POST" action="php/idea.php">
		
		<table style="text-align:left" align="center">
			<tr>
				<th>Title of idea: </th>
				<td><input name="title" type="text" size="30"/></td>
				</tr>
			<tr>
				<th>Details of idea: </th>
				<td><textarea name="details" cols="50" rows="10"></textarea></td>
				</tr>
			<tr>
				<th>Send idea to: </th>
				<td>
					<select name="to" id="to">
						<option value="select">Select One</option>
						<?
					  	$query = "
							SELECT title, type 
							FROM positions
							ORDER BY title";
						$result = mysqli_query($mysqli, $query);
							
						while($positions = mysqli_fetch_array($result, MYSQLI_ASSOC))
						{
							echo "<option value=\"$positions[type]\">$positions[title]</option>\n";
						}
					  ?>
						</select>
					</td>
				</tr>
			<tr>
				<th></th>
				<td><input name="submit" type="submit" /> &nbsp;<input name="reset" type="reset" /></td>
				</tr>
			</table>
		
		</form>
	</div>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>