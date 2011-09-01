<?php
session_start();
include_once('php/login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
?>

<style type="text/css">
th.accomplishments {
	padding-left, padding-right: 10px;
}
</style>

 <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'School');
        data.addColumn('number', 'Members Enrolled');
		
		
		<?php 
			// Get number of members in roster
			$query = "SELECT COUNT(DISTINCT school) AS num FROM members";
			if($result = mysqli_query($mysqli, $query)){
				
				$row = mysqli_fetch_object($result);
				$numSchools = $row->num;
				
				mysqli_free_result($result);
			}
			
			echo "data.addRows($numSchools);\n";
			
			$query = "
				SELECT COUNT(members.ID) AS count, schools.name AS name
				FROM members 
				JOIN schools ON schools.code = members.school
				GROUP BY members.school
				ORDER BY count";
			
			$get = mysqli_query($mysqli, $query);
			
			$i=0;
			
			while ($data = mysqli_fetch_array($get, MYSQLI_ASSOC)){
				echo "data.setValue($i, 0, '$data[name]');\n";
				echo "data.setValue($i, 1, $data[count]);\n";
				$i++;
			}
		?>
		
       
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, {width: 450, height: 300, title: 'Number of Members Per School', backgroundColor: '#fafafa'});
      }
    </script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h1>Who We Are</h1>
	  <p>More about the men of Kansas University Delta Tau Delta Fraternity.</p>
      
	  <h3 align="center">Honors &amp; Memberships</h3>
	  <table align="center">
	  <?php
			$typeQuery = "SELECT * FROM accomplishmentTypes";
			$typeResult = mysqli_query($mysqli, $typeQuery);
			
			$count = 1;
			
			while($typeRow = mysqli_fetch_array($typeResult, MYSQLI_ASSOC))
			{
				// Get number of members who have the specified honor
				$query = "	SELECT COUNT(username) AS numUsers 
							FROM accomplishments 
							WHERE type = '$typeRow[type]'";
				if($result = mysqli_query($mysqli, $query)){
					
					$row = mysqli_fetch_object($result);
					$numMembers = $row->numUsers;
					
					mysqli_free_result($result);
				}
				
				if($numMembers > 0) {
					if($count % 2 == 1)
					{
						echo "<tr>";
					}
					
					echo "<th class=\"accomplishments\">";
					echo $typeRow[title].":";
					echo "</th>";
					
					echo "<td style=\"color: #FBB117\">";
					
					echo $numMembers;
					
					echo "</td>";
					
					if($count % 2 == 0)
					{
						echo "</tr>";
					}
					else
					{
						echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>";
					}
					
					$count++;
				}
			}
	  ?>
	  </table>
	  <p>&nbsp;</p>
	  
   	<h3 align="center">Studies</h3>
 
    <div id="chart_div" style="width:100%; text-align:center;"></div>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>