<?php
	if ($_COOKIE["auth"] == "1"){
		$mysql_host = "mysql4.000webhost.com";
		$mysql_database = "a3498016_casc";
		$mysql_user = "a3498016_casc";
		$mysql_password = "IWant2Swim";
		$mysqli = mysqli_connect($mysql_host, $mysql_user, $mysql_password, $mysql_database);
		if(isset($_POST["Add"])){
			$sql="INSERT INTO tblTemplate (Name) VALUES ('".$_POST['Name']."')";
			$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		} else if(isset($_POST["Submit"])){ //UPDATE table_name  SET column1=value1,column2=value2,... WHERE some_column=some_value;
			$sql="UPDATE tblTemplate SET Name='".$_POST['Name']."' ";
			$sql=$sql."WHERE Id=".$_POST["Id"];
			$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		}
		// Find out if one of the edit buttons has been hit
		$sql="SELECT * FROM tblTemplate";
		$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		$Edit = 0;
		if($result){
			if(mysqli_num_rows($result) > 0){
				while ($info = mysqli_fetch_array($result)){
					if(isset($_POST["Edit".$info["Id"]])){
						$Edit = $info["Id"];
					}
				}
			}
		}
		// Find out if one of the delete buttons has been hit
		$sql="SELECT * FROM tblTemplate";
		$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		$Delete = 0;
		if($result){
			if(mysqli_num_rows($result) > 0){
				while ($info = mysqli_fetch_array($result)){
					if(isset($_POST["Delete".$info["Id"]])){
						$Delete = $info["Id"];
					}
				}
			}
		}
		if($Delete <> 0){
			$sql="DELETE FROM tblTemplate WHERE Id = ".$Delete;
			$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		}
		if ($Edit == 0) {$display_block = '<form method="post" action="listtemplates.php">';} else { $display_block = '';}
		$display_block = $display_block.'<table border="1"><thead><tr><th>Description</th><th></th></tr></thead><tbody>';
		$sql="SELECT Id, Name FROM tblTemplate";
		$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		if($result){
			if(mysqli_num_rows($result) > 0){
				while ($info = mysqli_fetch_array($result)){
					if($Edit == $info["Id"]){ // If the user has hit the edit button for this record
						$display_block = $display_block.'<tr><form method="post" action="listtemplates.php"><td><input type="text" name="Name" value="'.$info['Name'].'"/></td>';
						$display_block = $display_block.'<td><input type="submit" name="Submit" value="Submit"/>';
						$display_block = $display_block.'<input type="hidden" id="Id" name="Id" value="'.$info["Id"].'" /></td></form>';
						$display_block = $display_block.'<form method="post" action="listtemplateevents.php"><td><input type="submit" name="Edit" value="Edit"/>';
						$display_block = $display_block.'<input type="hidden" id="templateId" name="templateId" value="'.$info["Id"].'" /></td></form></tr>';
					} else if ($Edit == 0) { // If the user has not hit any edit button
						$display_block = $display_block.'<tr><td>'.$info['Name'].'</td>';
						$display_block = $display_block.'<td><input type="submit" name="Edit'.$info["Id"].'" value="Edit"/>';
						$display_block = $display_block.'<input type="submit" name="Delete'.$info["Id"].'" value="Delete"/></td></tr>';
					} else { // If the user has hit the edit button of a different record then don't display the edit button
						$display_block = $display_block.'<tr><td>'.$info['Name'].'</td>';
						$display_block = $display_block.'<td></td></tr>';
					}
				}
			}
		}
		if($Edit == 0){ // If the user has not hit any edit button then display the Add line
			$display_block = $display_block.'<tr><td><input type="text" name="Name"/></td>';
			$display_block = $display_block.'<td><input type="submit" name="Add" value="Add"/></td></tr>';
		}
		$display_block = $display_block.'</tbody></table>';
		if ($Edit == 0) {$display_block = $display_block.'</form>';}
	} else {
		header("Location: loginform.html");
	}
?>
<html>
	<head>
		<title>Templates</title>
	</head>
	<body>
		<?php echo "$display_block"; ?>
	</body>
</html>