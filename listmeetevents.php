<?php
	if ($_COOKIE["auth"] == "1"){
		$mysql_host = "mysql4.000webhost.com";
		$mysql_database = "a3498016_casc";
		$mysql_user = "a3498016_casc";
		$mysql_password = "IWant2Swim";
		$mysqli = mysqli_connect($mysql_host, $mysql_user, $mysql_password, $mysql_database);
		if(isset($_POST["Add"])){
			$sql="INSERT INTO tblTemplateEvent (Template, EventNo, Distance, Stroke, Swim) VALUES (".$_POST['templateId'].",".$_POST['EventNo'].",".$_POST['Distance'].",".$_POST['Stroke'].",".$_POST['Swim'].")";
			$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		} else if(isset($_POST["Submit"])){ //UPDATE table_name  SET column1=value1,column2=value2,... WHERE some_column=some_value;
			$sql="UPDATE tblTemplateEvent SET Template=".$_POST['templateId'].", EventNo=".$_POST['EventNo'].", Distance=".$_POST['Distance'].", Stroke=".$_POST['Stroke'].", Swim=".$_POST['Swim']." ";
			$sql=$sql."WHERE Id=".$_POST["Id"];
			$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		}
		// Find out if one of the edit buttons has been hit
		$sql="SELECT * FROM tblTemplateEvent WHERE Template = ".$_POST["templateId"]." ORDER BY Id";
		$tblTemplateEvent=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		$Edit = 0;
		if($tblTemplateEvent){
			if(mysqli_num_rows($tblTemplateEvent) > 0){
				while ($info = mysqli_fetch_array($tblTemplateEvent)){
					if(isset($_POST["Edit".$info["Id"]])){
						$Edit = $info["Id"];
					}
				}
			}
		}
		// Find out if one of the delete buttons has been hit
		$sql="SELECT * FROM tblTemplateEvent WHERE Template = ".$_POST["templateId"]." ORDER BY Id";
		$tblTemplateEvent=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		$Delete = 0;
		if($tblTemplateEvent){
			if(mysqli_num_rows($tblTemplateEvent) > 0){
				while ($info = mysqli_fetch_array($tblTemplateEvent)){
					if(isset($_POST["Delete".$info["Id"]])){
						$Delete = $info["Id"];
					}
				}
			}
		}
		if($Delete <> 0){
			$sql="DELETE FROM tblTemplateEvent WHERE Id = ".$Delete;
			$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		}
		if((isset($_POST["Edit"])) or (isset($_POST["Add"])) or (isset($_POST["Submit"])) or ($Edit > 0) or ($Delete > 0)){
			$sql = "SELECT * FROM tblTemplate WHERE Id = ".$_POST["templateId"];
			$tblTemplate=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
			if($tblTemplate){
				if(mysqli_num_rows($tblTemplate) > 0){
					if($info = mysqli_fetch_array($tblTemplate)){
						$display_block = '<h1>'.$info['Name'].'</h1>';
					} else {
						$display_block = '<h1>Name unknown</h1>';
					}
					$display_block = $display_block.'<form method="post" action="listtemplateevents.php"><input type="hidden" id="templateId" name="templateId" value="'.$_POST["templateId"].'" />';
					$display_block = $display_block.'<table border="1"><thead><tr><th>Event No</th><th>Distance</th><th>Stroke</th><th>Swim</th><th></th></tr></thead><tbody>';
					$sql="SELECT * FROM tblTemplateEvent WHERE Template = ".$_POST["templateId"];
					$tblTemplateEvent=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
					if($tblTemplateEvent){
						if(mysqli_num_rows($tblTemplateEvent) > 0){
							while ($info = mysqli_fetch_array($tblTemplateEvent)){
								$sql="SELECT * FROM tblStroke WHERE Id='".$info['Stroke']."'";
								$tblStroke=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
								if($tblStroke){
									if(mysqli_num_rows($tblStroke) >= 1){
										$Strokes = mysqli_fetch_array($tblStroke);
										$Stroke = $Strokes['Stroke'];
									} else {
										$Stroke = 'Unknown';
									}
								} else {
									$Stroke = 'Unknown';
								}
								if($Edit == $info["Id"]){ // If the user has hit the edit button for this record
									$display_block = $display_block.'<tr><td><input type="text" name="EventNo" value="'.$info['EventNo'].'"/></td>';
									$sql="SELECT * FROM tblDistance";
									$tblDistance=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
									if($tblDistance){
										if(mysqli_num_rows($tblDistance) >= 1){
											$display_block = $display_block.'<td><select name="Distance">';
											while ($Distances = mysqli_fetch_array($tblDistance)){
												if ($Distances['distance'] == $info['Distance'] ){
													$display_block = $display_block.'<option value='.$Distances['distance'].' selected>'.$Distances['distance'].'</option>';
												} else {
													$display_block = $display_block.'<option value='.$Distances['distance'].'>'.$Distances['distance'].'</option>';
												}
											}
											$display_block = $display_block.'</select></td>';
										} else {
											$display_block = $display_block.'<td><input type="text" name="Distance"/></td>';
										}
									} else {
										$display_block = $display_block.'<td><input type="text" name="Distance"/></td>';
									}
									$sql="SELECT * FROM tblStroke";
									$tblStroke=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
									if($tblStroke){
										if(mysqli_num_rows($tblStroke) >= 1){
											$display_block = $display_block.'<td><select name="Stroke">';
											while ($Strokes = mysqli_fetch_array($tblStroke)){
												if ($Strokes['Id'] == $info['Stroke']){
													$display_block = $display_block.'<option value='.$Strokes['Id'].' selected>'.$Strokes['Stroke'].'</option>';
												} else {
													$display_block = $display_block.'<option value='.$Strokes['Id'].'>'.$Strokes['Stroke'].'</option>';
												}
											}
											$display_block = $display_block.'</select></td>';
										} else {
											$display_block = $display_block.'<td><input type="text" name="Stroke"/></td>';
										}
									} else {
										$display_block = $display_block.'<td><input type="text" name="Stroke"/></td>';
									}
									$display_block = $display_block.'<td><input type="text" name="Swim" value="'.$info['Swim'].'"/></td>';
									$display_block = $display_block.'<td><input type="submit" name="Submit"] value="Submit"/>';
									$display_block = $display_block.'<input type="hidden" id="Id" name="Id" value="'.$info["Id"].'" /></td>';
								} else if ($Edit == 0) { // If the user has not hit any edit button
									$display_block = $display_block.'<tr><td>'.$info['EventNo'].'</td><td>'.$info['Distance'].'</td><td>'.$Stroke.'</td><td>'.$info['Swim'].'</td>';
									$display_block = $display_block.'<td><input type="submit" name="Edit'.$info["Id"].'" value="Edit"/>';
									$display_block = $display_block.'<input type="submit" name="Delete'.$info["Id"].'" value="Delete"/></td></tr>';
								} else { // If the user has hit the edit button of a different record then don't display the edit button
									$display_block = $display_block.'<tr><td>'.$info['EventNo'].'</td><td>'.$info['Distance'].'</td><td>'.$Stroke.'</td>';
									$display_block = $display_block.'<td>'.$info['Swim'].'</td></tr>';
								}
							}
						}
					}
					if($Edit == 0){ // If the user has not hit any edit button then display the Add line
						$display_block = $display_block.'<tr><td><input type="text" name="EventNo"/></td>';
						$sql="SELECT * FROM tblDistance";
						$tblDistance=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
						if($tblDistance){
							if(mysqli_num_rows($tblDistance) >= 1){
								$display_block = $display_block.'<td><select name="Distance">';
								while ($Distances = mysqli_fetch_array($tblDistance)){
									$display_block = $display_block.'<option value='.$Distances['distance'].'>'.$Distances['distance'].'</option>';
								}
								$display_block = $display_block.'</select></td>';
							} else {
								$display_block = $display_block.'<td><input type="text" name="Distance"/></td>';
							}
						} else {
							$display_block = $display_block.'<td><input type="text" name="Distance"/></td>';
						}
						$sql="SELECT * FROM tblStroke";
						$tblStroke=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
						if($tblStroke){
							if(mysqli_num_rows($tblStroke) >= 1){
								$display_block = $display_block.'<td><select name="Stroke">';
								while ($Strokes = mysqli_fetch_array($tblStroke)){
									$display_block = $display_block.'<option value='.$Strokes['Id'].'>'.$Strokes['Stroke'].'</option>';
								}
								$display_block = $display_block.'</select></td>';
							} else {
								$display_block = $display_block.'<td><input type="text" name="Stroke"/></td>';
							}
						} else {
							$display_block = $display_block.'<td><input type="text" name="Stroke"/></td>';
						}
						$display_block = $display_block.'<td><input type="text" name="Swim"/></td>';
						$display_block = $display_block.'<td><input type="submit" name="Add" value="Add"/></td></tr>';
					}
					$display_block = $display_block.'</tbody></table></form>';
				}
			}
		} else {
			$sql="SELECT * FROM tblTemplate";
			$tblTemplate=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
			if($tblTemplate){
				if(mysqli_num_rows($tblTemplate) > 0){
					while ($info = mysqli_fetch_array($tblTemplate)){
						$meetID = $info['Id'];
						$display_block = '<h1>'.$info['Name'].'</h1.';
						$sql="SELECT * FROM tblTemplateEvent WHERE Template = ".$meetID." ORDER BY Id";
						$tblTemplateEvent=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
						if($tblTemplateEvent){
							if(mysqli_num_rows($tblTemplateEvent) > 0){
								$display_block = '<table border="1"><thead><tr><th>Event No</th><th>Distance</th><th>Stroke</th></tr></thead><tbody>';
								while ($info = mysqli_fetch_array($tblTemplateEvent)){
									$sql="SELECT * FROM tblStroke WHERE Id='".$info['Stroke']."'";
									$tblStroke=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
									if($tblStroke){
										if(mysqli_num_rows($tblStroke) >= 1){
											$Strokes = mysqli_fetch_array($tblStroke);
											$Stroke = $Strokes['Stroke'];
										} else {
											$Stroke = 'Unknown';
										}
									} else {
										$Stroke = 'Unknown';
									}
									$display_block = $display_block.'<tr><td>'.$info['EventNo'].'</td><td>'.$info['Distance'].'</td><td>'.$Stroke.'</td>';
								}
								$display_block = $display_block.'</tbody></table>';
							}
						}
					}
				}
			}
		}
		
	} else {
		header("Location: loginform.html");
	}
?>
<html>
	<head>
		<title>Events</title>
	</head>
	<body>
		<?php echo "$display_block"; ?>
	</body>
</html>