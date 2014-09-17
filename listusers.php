<?php
	if ($_COOKIE["auth"] == "1"){
		$mysql_host = "mysql4.000webhost.com";
		$mysql_database = "a3498016_casc";
		$mysql_user = "a3498016_casc";
		$mysql_password = "IWant2Swim";
		$mysqli = mysqli_connect($mysql_host, $mysql_user, $mysql_password, $mysql_database);
		$temp = "";
		if(isset($_POST["Upload"])){
			include 'Classes/PHPExcel/IOFactory.php';
			$objPHPExcel = PHPExcel_IOFactory::load($_FILES['uploadedfile']['tmp_name']);
			$objWorksheet = $objPHPExcel->getActiveSheet();
			$highestRow = $objWorksheet->getHighestRow();
			$highestColumn = $objWorksheet->getHighestColumn();
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			for($Column = 0; $Column <= $highestColumnIndex; ++$Column){
				switch($objWorksheet->getCellByColumnAndRow($Column, 1)->getValue()){
					case "MemberID": $SystemIndex = $Column; break;
					case "First Name": $FirstNameIndex = $Column; break;
					case "Last Name": $LastNameIndex = $Column; break;
					case "Email Address": $EmailIndex = $Column; break;
					case "DOB": $DOBIndex = $Column; break;
					case "Gender": $GenderIndex = $Column; break;
					case "UserName": $UserNameIndex = $Column; break;
					case "Password": $PasswordIndex = $Column; break;
					case "Member Types": $MemberTypeIndex = $Column; break;
					case "Membership Number": $MemberNoIndex = $Column; break;
					case "Meet Manager ID": $MMIDIndex = $Column; break;
					case "Primary": $PrimaryIndex = $Column; break;
				}
			}
			if(isset($SystemIndex) AND isset($FirstNameIndex) AND isset($LastNameIndex) 
					AND isset($EmailIndex) AND isset($DOBIndex) AND isset($GenderIndex) 
					AND isset($UserNameIndex) AND isset($PasswordIndex) AND isset($MemberNoIndex) 
					AND isset($MMIDIndex) AND isset($PrimaryIndex)){
				for ($row = 2; $row <= $highestRow; ++$row) {
					$SystemId = $objWorksheet->getCellByColumnAndRow($SystemIndex, $row)->getValue();
					$FirstName = $objWorksheet->getCellByColumnAndRow($FirstNameIndex, $row)->getValue();
					$LastName = $objWorksheet->getCellByColumnAndRow($LastNameIndex, $row)->getValue();
					$email = $objWorksheet->getCellByColumnAndRow($EmailIndex, $row)->getValue();
					$Gender = $objWorksheet->getCellByColumnAndRow($GenderIndex, $row)->getValue();
					if($Gender == "Male"){$G="m";}else{$G="f";}
					$BirthDate = date('Y-m-d',strtotime('1899-12-31+'.($objWorksheet->getCellByColumnAndRow($DOBIndex, $row)->getValue()-1).' days'));
					$Username = $objWorksheet->getCellByColumnAndRow($UserNameIndex, $row)->getValue();
					$Password = $objWorksheet->getCellByColumnAndRow($PasswordIndex, $row)->getValue();
					$MemberType = $objWorksheet->getCellByColumnAndRow($MemberTypeIndex, $row)->getValue();
					$MemberNo = $objWorksheet->getCellByColumnAndRow($MemberNoIndex, $row)->getValue();
					$MMID = $objWorksheet->getCellByColumnAndRow($MMIDIndex, $row)->getValue();
					$Primary = $objWorksheet->getCellByColumnAndRow($PrimaryIndex, $row)->getValue();
					$sql="SELECT * FROM tblCompetitor WHERE MembershipNo = ".$MemberNo;
					$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
					if($result){
						if(mysqli_num_rows($result) > 0){
							if($info = mysqli_fetch_array($result)){
								$sql = "UPDATE tblCompetitor SET MMID='".$MMID."', LastName='".$LastName."', ";
								$sql .= "FirstName='".$FirstName."', Gender='".$G."', BirthDate='".$BirthDate."', Club=1, email='".$email."', ";
								$sql .= "Username='".$Username."', password=PASSWORD('".$Password."'), MemberType='".$MemberType."', AccessLevel=1, ";
								$sql .= "SystemId=".$SystemId.", PrimaryLink='".$Primary."', PrimaryId=0";
								$sql .= " WHERE MembershipNo=".$MemberNo;
								$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
							}
						} else {
							$sql="INSERT INTO tblCompetitor (MembershipNo, MMID, LastName, FirstName, Gender, BirthDate, Club, email, Username, password, MemberType, AccessLevel, SystemId, PrimaryLink, PrimaryId) ";
							$sql=$sql."VALUES (".$MemberNo.",'".$MMID."', '".$LastName."', '".$FirstName."', '".$G."', '".$BirthDate."', 1, ";
							$sql=$sql."'".$email."', '".$Username."', PASSWORD('".$Password."'), '".$MemberType."', 1 , ".$SystemId.", '".$Primary."', 0 )";
							$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
						}
					} else {
						$sql="INSERT INTO tblCompetitor (MembershipNo, MMID, LastName, FirstName, Gender, BirthDate, Club, email, Username, password, MemberType, AccessLevel, SystemId, PrimaryLink, PrimaryId) ";
						$sql=$sql."VALUES (".$MemberNo.",'".$MMID."', '".$LastName."', '".$FirstName."', '".$G."', ".$BirthDate."', 1, ";
						$sql=$sql."'".$email."', '".$Username."', PASSWORD('".$Password."'), '".$MemberType."', 1, ".$SystemId.", '".$Primary."', 0 )";
						$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
					}
				}
				$sql="SELECT * FROM tblCompetitor WHERE PrimaryLink <> '0'";
				$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
				if($result){
					if(mysqli_num_rows($result) > 0){
						while($info = mysqli_fetch_array($result)){
							$Primary = $info["SystemId"];
							$PrimaryLink = $info["PrimaryLink"];
							$Secondary = strtok($PrimaryLink,"|");
							while($Secondary != false) {
								$sql="UPDATE tblCompetitor SET PrimaryId = ".$Primary." WHERE SystemId = ".$Secondary;
								$result2=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
								$Secondary = strtok("|");
							}
						}
					}
				}
			}
		}
		if(isset($_POST["Add"])){
			$sql="INSERT INTO tblCompetitor VALUES (".$_POST['MembershipNo'].",'".$_POST['MMID']."','".$_POST['LastName']."','".$_POST['FirstName']."','";
			$sql=$sql.$_POST['Gender']."',STR_TO_DATE('".$_POST['BirthDate']."','%d/%m/%y'),".$_POST['Club'].",'".$_POST['email']."',PASSWORD('".$_POST['MembershipNo']."'),'1')";
			$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		} else if(isset($_POST["Submit"])){ 
			$sql="UPDATE tblCompetitor SET MembershipNo=".$_POST['MembershipNo'].", MMID='".$_POST['MMID']."', LastName='".$_POST['LastName']."', ";
			$sql=$sql."FirstName='".$_POST['FirstName']."', Gender='".$_POST['Gender']."', BirthDate=STR_TO_DATE('".$_POST['BirthDate']."','%d/%m/%y'), Club=".$_POST['Club'].", ";
			$sql=$sql."email='".$_POST['email']."', TMID='".$_POST['TMID']."', password=PASSWORD('".$_POST['MembershipNo']."'), AccessLevel='1' ";
			$sql=$sql."WHERE MembershipNo=".$_POST["MemberId"];
			$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		} else if(isset($_POST["AutoTMID"])){
			$sql = "SELECT FirstName, LastName, DATE_FORMAT(BirthDate,'%d%m%y') AS BirthDate, MembershipNo, TMID FROM tblCompetitor";
			$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
			if($result){
				if(mysqli_num_rows($result) > 0){
					while($info = mysqli_fetch_array($result)){
						if(strlen($info["TMID"])==0){
							$sql="UPDATE tblCompetitor SET TMID = '".strtoupper(substr($info["LastName"],0,3).substr($info["FirstName"],0,2)).$info["BirthDate"]."' WHERE MembershipNo = ".$info["MembershipNo"];
$temp .= $sql;
							$result2=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
						} else {$temp.=strlen($info["TMID"]);}
					}
				}
			}
		}
		// Find out if one of the edit buttons has been hit
		$sql="SELECT * FROM tblCompetitor";
		$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		$Edit = 0;
		if($result){
			if(mysqli_num_rows($result) > 0){
				while ($info = mysqli_fetch_array($result)){
					if(isset($_POST["Edit".$info["MembershipNo"]])){
						$Edit = $info["MembershipNo"];
					}
				}
			}
		}
		// Find out if one of the delete buttons has been hit
		$sql="SELECT * FROM tblCompetitor";
		$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		$Delete = 0;
		if($result){
			if(mysqli_num_rows($result) > 0){
				while ($info = mysqli_fetch_array($result)){
					if(isset($_POST["Delete".$info["MembershipNo"]])){
						$Delete = $info["MembershipNo"];
						$sql="DELETE FROM tblCompetitor WHERE MembershipNo = ".$Delete;
						$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
					}
				}
			}
		}
		$display_block = $temp.'<form enctype="multipart/form-data" method="post" action="listusers.php"><table border="1"><thead><tr><th>First Name</th><th>Last Name</th><th>Gender</th><th>Date of Birth</th>';
		$display_block = $display_block.'<th>Membership No</th><th>Meet Manager No</th><th>Club</th><th>email</th><th>Team Manager ID</th><th></th></tr></thead><tbody>';
		$sql = "SELECT tblCompetitor.FirstName AS FirstName, tblCompetitor.LastName AS LastName, tblGender.Gender AS Gender, DATE_FORMAT(BirthDate,'%d/%m/%Y') AS BirthDate, ";
		$sql .= "tblCompetitor.MembershipNo AS MembershipNo, tblCompetitor.MMID AS MMID, tblClub.Name As ClubName, tblCompetitor.email AS email, tblCompetitor.TMID AS TMID ";
		$sql .= "FROM (tblCompetitor INNER JOIN tblGender ON tblCompetitor.Gender = tblGender.Id) INNER JOIN tblClub ON tblCompetitor.Club = tblClub.Id";
		$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		if($result){
			if(mysqli_num_rows($result) > 0){
				while ($info = mysqli_fetch_array($result)){
					if($Edit == $info["MembershipNo"]){ // If the user has hit the edit button for this record
						$display_block = $display_block.'<tr><td><input type="text" name="FirstName" value="'.$info['FirstName'].'"/></td>';
						$display_block = $display_block.'<td><input type="text" name="LastName" value="'.$info['LastName'].'"/></td>';
						$sql="SELECT * FROM tblGender";
						$tblGender=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
						if($tblGender){
							if(mysqli_num_rows($tblGender) >= 1){
								$display_block = $display_block.'<td><select name="Gender"'.$info['Gender'].' value="'.$info['Gender'].'"/>';
								while ($Genders = mysqli_fetch_array($tblGender)){
									if($Genders['gender'] == $info['Gender']){
										$display_block = $display_block.'<option value='.$Genders['Id'].' selected>'.$Genders['gender'].'</option>';
									} else {
										$display_block = $display_block.'<option value='.$Genders['Id'].'>'.$Genders['gender'].'</option>';
									}
								}
								$display_block = $display_block.'</select></td>';
							} else {
								$display_block = $display_block.'<td><input type="text" name="Gender"'.$info['Gender'].' value="'.$info['Gender'].'"/></td>';
							}
						} else {
							$display_block = $display_block.'<td><input type="text" name="Gender"'.$info['Gender'].' value="'.$info['Gender'].'"/></td>';
						}
						$display_block = $display_block.'<td><input type="text" name="BirthDate" value="'.$info['BirthDate'].'"/></td>';
						$display_block = $display_block.'<td><input type="text" name="MembershipNo" value="'.$info['MembershipNo'].'"/></td>';
						$display_block = $display_block.'<td><input type="text" name="MMID" value="'.$info['MMID'].'"/></td>';
						$sql="SELECT * FROM tblClub";
						$tblClub=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
						if($tblClub){
							if(mysqli_num_rows($tblClub) >= 1){
								$display_block = $display_block.'<td><select name="Club" value="'.$info['Club'].'"/>';
								while ($ClubNames = mysqli_fetch_array($tblClub)){
									if($ClubNames['Name'] == $info['ClubName']){
										$display_block = $display_block.'<option value='.$ClubNames['Id'].' selected>'.$ClubNames['Name'].'</option>';
									} else {
										$display_block = $display_block.'<option value='.$ClubNames['Id'].'>'.$ClubNames['Name'].'</option>';
									}
								}
								$display_block = $display_block.'</select></td>';
							} else {
								$display_block = $display_block.'<td><input type="text" name="Club" value="'.$info['ClubName'].'"/></td>';
							}
						} else {
							$display_block = $display_block.'<td><input type="text" name="Club" value="'.$info['Club'].'"/></td>';
						}
						$display_block = $display_block.'<td><input type="text" name="email" value="'.$info['email'].'"/></td>';
						$display_block = $display_block.'<td><input type="text" name="TMID" value="'.$info['TMID'].'"/></td>';
						$display_block = $display_block.'<td><input type="submit" name="Submit" value="Submit"/>';
						$display_block = $display_block.'<input type="hidden" id="MemberId" name="MemberId" value="'.$info["MembershipNo"].'" /></td>';
					} else if ($Edit == 0) { // If the user has not hit any edit button
						$display_block = $display_block.'<tr><td>'.$info['FirstName'].'</td><td>'.$info['LastName'].'</td>';
						$display_block = $display_block.'<td>'.$info['Gender'].'</td><td>'.$info["BirthDate"].'</td>';
						$display_block = $display_block.'<td>'.$info['MembershipNo'].'</td><td>'.$info['MMID'].'</td>';
						$display_block = $display_block.'<td>'.$info['ClubName'].'</td><td>'.$info['email'].'</td><td>'.$info['TMID'].'</td>';
						$display_block = $display_block.'<td><input type="submit" name="Edit'.$info["MembershipNo"].'" value="Edit"/>';
						$display_block = $display_block.'<input type="submit" name="Delete'.$info["MembershipNo"].'" value="Delete"/></td></tr>';
					} else { // If the user has hit the edit button of a different record then don't display the edit button
						$display_block = $display_block.'<tr><td>'.$info['FirstName'].'</td><td>'.$info['LastName'].'</td>';
						$display_block = $display_block.'<td>'.$info['Gender'].'</td><td>'.$info["BirthDate"].'</td>';
						$display_block = $display_block.'<td>'.$info['MembershipNo'].'</td><td>'.$info['MMID'].'</td>';
						$display_block = $display_block.'<td>'.$info['ClubName'].'</td><td>'.$info['email'].'</td><td>'.$info['TMID'].'</td><td></td></tr>';
					}
				}
			}
		}
		if($Edit == 0){ // If the user has not hit any edit button then display the Add line
			$display_block = $display_block.'<tr><td><input type="text" name="FirstName"/></td>';
			$display_block = $display_block.'<td><input type="text" name="LastName"/></td>';
			$sql="SELECT * FROM tblGender";
			$tblGender=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
			if($tblGender){
				if(mysqli_num_rows($tblGender) >= 1){
					$display_block = $display_block.'<td><select name="Gender" value="'.$info['Gender'].'"/>';
					while ($Genders = mysqli_fetch_array($tblGender)){
						$display_block = $display_block.'<option value='.$Genders['Id'].'>'.$Genders['gender'].'</option>';
					}
					$display_block = $display_block.'</select></td>';
				} else {
					$display_block = $display_block.'<td><input type="text" name="Gender" value="'.$info['Gender'].'"/></td>';
				}
			} else {
				$display_block = $display_block.'<td><input type="text" name="Gender" value="'.$info['Gender'].'"/></td>';
			}
			$display_block = $display_block.'<td><input type="text" name="BirthDate"/></td>';
			$display_block = $display_block.'<td><input type="text" name="MembershipNo"/></td>';
			$display_block = $display_block.'<td><input type="text" name="MMID"/></td>';
			$sql="SELECT * FROM tblClub";
			$tblClub=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
			if($tblClub){
				if(mysqli_num_rows($tblClub) >= 1){
					$display_block = $display_block.'<td><select name="Club">';
					while ($ClubNames = mysqli_fetch_array($tblClub)){
						$display_block = $display_block.'<option value='.$ClubNames['Id'].'>'.$ClubNames['Name'].'</option>';
					}
					$display_block = $display_block.'</select></td>';
				} else {
					$display_block = $display_block.'<td><input type="text" name="Club"/></td>';
				}
			} else {
				$display_block = $display_block.'<td><input type="text" name="Club"/></td>';
			}
			$display_block = $display_block.'<td><input type="text" name="email"/></td>';
			$display_block = $display_block.'<td><input type="submit" name="Add" value="Add"/></td></tr>';
		}
		$display_block = $display_block.'</tbody></table>';
		if($Edit == 0){ // If the user has not hit any edit button then display Upload File
			$display_block = $display_block.'<input type="hidden" name="MAX_FILE_SIZE" value="100000" />';
			$display_block = $display_block.'Choose a file to upload: <input name="uploadedfile" type="file" /><br />';
			$display_block = $display_block.'<input type="submit" name="Upload" value="Upload File" />';
			$display_block = $display_block.'<br/><input type="submit" name="AutoTMID" value="Auto Generate TMID" />';
		}
		$display_block = $display_block.'</form>';
	} else {
		header("Location: loginform.html");
	}
?>
<html>
	<head>
		<title>Competitors</title>
	</head>
	<body>
		<?php echo "$display_block"; ?>
	</body>
</html>