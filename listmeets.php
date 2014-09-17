<?php
	function ImportHy3($mysqli,$meet,$importFile,$fileType){
		if ($importFile["error"] > 0){
			echo "Error: " . $importFile["error"] . "<br>";
		} else {
$temp.="<p>".$importFile['name']."</p>";
			$file = fopen($importFile['tmp_name'],"r");
			$valid = false;
			$errorstr = "File not valid<br/>";
			$MembershipNo = 0;
			while(!feof($file)){
				$line = fgets($file);
$temp.="<p>".$line.strlen($line)."</p>";
				if (strlen($line)==132){
					switch(substr($line,0,2)){
						case "A1": //Hy-Tek File Info
							$A1FileType = substr($line,2,2);
							$A1FileTypeDesc = rtrim(substr($line,4,25));
							$A1VendorName = rtrim(substr($line,29,15));
							$A1SoftwareVersion = rtrim(substr($line,44,14));
							$A1FileDate = strtotime(substr($line,58,2)."/".substr($line,60,2)."/".substr($line,62,4));
							$A1FileTime = strtotime(substr($line,67,8));
							$A1FileLicence = rtrim(substr($line,75,53));
$temp.= "<p>File Type: ".$A1FileType." ".$fileType."</p>";
$temp.= "<p>File Description: ".$A1FileTypeDesc."</p>";
$temp.= "<p>Vendor Name: ".$A1VendorName."</p>";
$temp.= "<p>Software Version: ".$A1SoftwareVersion."</p>";
$temp.= "<p>File Date: ".$A1FileDate."</p>";
$temp.= "<p>File Time: ".$A1FileTime."</p>";
$temp.= "<p>File Licence: ".$A1FileLicence."</p>";
							if($fileType == "Entries"){
								if(($A1FileType == "02")and($A1FileTypeDesc == "Meet Entries")){$valid = true; $errorstr = "";} else {$errorstr = "Entries error";}
							} elseif($fileType == "Results") {
								if(($A1FileType == "07")and($A1FileTypeDesc == "Results From MM to TM")){$valid = true; $errorstr = "";} else {$errorstr = "Results error";}
							} else {$errorstr = "File Type or Desc does not match";}
$temp.= "<p>File Date: ".date("d/m/Y",$A1FileDate)."</p>";
							break;
						case "B1": //Meet Information
							if($valid){
								$B1MeetName = rtrim(substr($line,2,45));
								$B1MeetFacility = rtrim(substr($line,47,45));
								$B1MeetStart = strtotime(substr($line,92,2)."/".substr($line,94,2)."/".substr($line,96,4));
								$B1MeetEnd = strtotime(substr($line,100,2)."/".substr($line,102,2)."/".substr($line,104,4));
								$B1MeetAgeUp = strtotime(substr($line,108,2)."/".substr($line,110,2)."/".substr($line,112,4));
								$B1MeetElevation = ltrim(substr($line,116,5));
								if($meet["Name"]<>$B1MeetName){$valid = false; $errorstr .= "Meet Name not valid ".$meet["Name"]."|".$B1MeetName."<br/>";}
								if($meet["Location"]<>$B1MeetFacility){$valid = false; $errorstr .= "Meet Location not valid ".$meet["Location"]."|".$B1MeetFacility."<br/>";}
								if($meet["StartDate"]<>date("Y-m-d",$B1MeetStart)){$valid = false; $errorstr .= "Start date not valid ".$meet["StartDate"]."|".date("d/m/Y",$B1MeetStart)."<br/>";}
								if($meet["EndDate"]<>date("Y-m-d",$B1MeetEnd)){$valid=false; $errorstr .= "End date not valid".$meet["EndDate"]."|".date("d/m/Y",$B1MeetEnd)."<br/>";}
								if($meet["AgeUpDate"]<>date("Y-m-d",$B1MeetAgeUp)){$valid=false; $errorstr .= "Age Up date not valid ".$meet["AgeUpDate"]."|".date("d/m/Y",$B1MeetAgeUp)."<br/>";}
								//if($meet["Elevation"]<>$B1MeetElevation){$valid=false; $errorstr .= "Elevation not valid ".$meet["Elevation"]."|".$B1MeetElevation."<br/>";}
$temp.= "<p>Meet Name: ".$B1MeetName."</p>";
$temp.= "<p>Meet Facility: ".$B1MeetFacility."</p>";
$temp.= "<p>Meet Start: ".date("d/m/Y",$B1MeetStart)."</p>";
$temp.= "<p>Meet End: ".date("d/m/Y",$B1MeetEnd)."</p>";
$temp.= "<p>Meet Age Up: ".date("d/m/Y",$B1MeetAgeUp)."</p>";
$temp.= "<p>Meet Elevation: ".$B1MeetElevation."</p>";
							}
							break;
						case "B2": //Meet 
							if($valid and $B1MeetName){
								$B2MeetCourseCode = substr($line,98,1);
								if($meet["CourseCode"]<>$B2MeetCourseCode){$valid = false; $errorstr .= "Meet Course Code not valid ".$meet["CourseCode"]."|".$B2MeetCourseCode."<br/>";}
$temp.= "<p>Meet Course Code: ".$B2MeetCourseCode."</p>";
							}
							break;
						case "C1": //Swim Team Name Info
							if($valid and $B1MeetName){
								$C1SwimTeamAbbr = rtrim(substr($line,2,5));
								$C1SwimTeamName = rtrim(substr($line,7,30));
								$C1SwimTeamShortName = rtrim(substr($line,37,16));
								$C1SwimTeamLSC = rtrim(substr($line,53,2));
								$C1SwimTeamCoach = rtrim(substr($line,55,30));
								$C1SwimTeamType = rtrim(substr($line,119,3));
$temp.= "<p>Swim Team Abbr: ".$C1SwimTeamAbbr."</p>";
$temp.= "<p>Swim Team Name: ".$C1SwimTeamName."</p>";
$temp.= "<p>Swim Team Short Name: ".$C1SwimTeamShortName."</p>";
$temp.= "<p>Swim Team LSC: ".$C1SwimTeamLSC."</p>";
$temp.= "<p>Swim Team Coach: ".$C1SwimTeamCoach."</p>";
$temp.= "<p>Swim Team Type: ".$C1SwimTeamType."</p>";
								$sql = "Select * FROM tblClub WHERE Name  = '".$C1SwimTeamName."'";
$temp.=$sql; 
								$tblClub=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
								if(mysqli_num_rows($tblClub) > 0){
									if ($Club = mysqli_fetch_array($tblClub)){
										if($Club["Code"]<>$C1SwimTeamAbbr){
											$sql="UPDATE tblClub SET Code = '".$C1SwimTeamAbbr."' WHERE Name  = '".$C1SwimTeamName."'";
											$setClub=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
										}
										if($Club["ShortName"]<>$C1SwimTeamShortName){
											$sql="UPDATE tblClub SET ShortName = '".$C1SwimTeamShortName."' WHERE Name  = '".$C1SwimTeamName."'";
											$setClub=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
										}
										if($Club["LSC"]<>$C1SwimTeamLSC){
											$sql="UPDATE tblClub SET LSC = '".$C1SwimTeamLSC."' WHERE Name  = '".$C1SwimTeamName."'";
											$setClub=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
										}
										if($Club["Coach"]<>$C1SwimTeamCoach){
											$sql="UPDATE tblClub SET Coach = '".$C1SwimTeamCoach."' WHERE Name  = '".$C1SwimTeamName."'";
											$setClub=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
										}
										if($Club["Type"]<>$C1SwimTeamType){
											$sql="UPDATE tblClub SET Type = '".$C1SwimTeamType."' WHERE Name  = '".$C1SwimTeamName."'";
											$setClub=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
										}
									} else {
										$sql="INSERT INTO tblClub (Name, Code, ShortName, LSC, Coach, Type) VALUES ('".$C1SwimTeamName."', '".$C1SwimTeamAbbr."', '".$C1SwimTeamShortName."','".$C1SwimTeamLSC."','".$C1SwimTeamCoach."', '".$C1SwimTeamType."')";
										$setClub=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
									}
								} else {
									$sql="INSERT INTO tblClub (Name, Code, ShortName, LSC, Coach, Type) VALUES ('".$C1SwimTeamName."', '".$C1SwimTeamAbbr."', '".$C1SwimTeamShortName."','".$C1SwimTeamLSC."','".$C1SwimTeamCoach."', '".$C1SwimTeamType."')";
									$setClub=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
								}
							}
							break;
						case "C2": //Swim Team Address Info
							if($valid and $B1MeetName){
								$C2SwimTeamMailTo  = rtrim(substr($line,2,30));
								$C2SwimTeamAddress  = rtrim(substr($line,32,30));
								$C2SwimTeamCity  = rtrim(substr($line,62,30));
								$C2SwimTeamState  = rtrim(substr($line,92,2));
								$C2SwimTeamZip  = rtrim(substr($line,94,10));
								$C2SwimTeamCountry  = rtrim(substr($line,104,3));
								$C2SwimTeamRegistration  = rtrim(substr($line,107,4));
$temp.= "<p>Swim Team Mail To: ".$C2SwimTeamMailTo."</p>";
$temp.= "<p>Swim Team Address: ".$C2SwimTeamAddress."</p>";
$temp.= "<p>Swim Team City: ".$C2SwimTeamCity."</p>";
$temp.= "<p>Swim Team State: ".$C2SwimTeamState."</p>";
$temp.= "<p>Swim Team Zip: ".$C2SwimTeamZip."</p>";
$temp.= "<p>Swim Team Country: ".$C2SwimTeamCountry."</p>";
$temp.= "<p>Swim Team Registration: ".$C2SwimTeamRegistration."</p>";
							}
							break;
						case "C3": //Swim Team Address Info
							if($valid and $B1MeetName){
								$C3SwimTeamPhoneDay  = rtrim(substr($line,32,20));
								$C3SwimTeamPhoneNight  = rtrim(substr($line,52,20));
								$C3SwimTeamFax  = rtrim(substr($line,72,20));
								$C3SwimTeamEmail  = rtrim(substr($line,92,36));
$temp.= "<p>Swim Team Phone (Day): ".$C3SwimTeamPhoneDay."</p>";
$temp.= "<p>Swim Team Phone (Night): ".$C3SwimTeamPhoneNight."</p>";
$temp.= "<p>Swim Team Fax: ".$C3SwimTeamFax."</p>";
$temp.= "<p>Swim Team Email: ".$C3SwimTeamEmail."</p>";
							}
							break;
						case "D1": //Swimer Entry
							if($valid and $B1MeetName){
								$D1SwimmerGender = substr($line,2,1);
								$D1SwimmerId = ltrim(substr($line,3,5));
								$D1SwimmerLastName = rtrim(substr($line,8,20));
								$D1SwimmerFirstName = rtrim(substr($line,28,20));
								$D1SwimmerNickName = rtrim(substr($line,48,20));
								$D1SwimmerInitial = substr($line,68,1);
								$D1SwimmerUSSNUM = ltrim(substr($line,69,14));
								$D1SwimmerTeamId = ltrim(substr($line,83,5));
								$D1SwimmerDOB = strtotime(substr($line,88,2)."/".substr($line,90,2)."/".substr($line,92,4));
								$D1SwimmerAge = ltrim(substr($line,96,3));
$temp.= "<p>Swimmer Gender: ".$D1SwimmerGender."</p>";
$temp.= "<p>Swimmer Id: ".$D1SwimmerId."</p>";
$temp.= "<p>Swimmer Last Name: ".$D1SwimmerLastName."</p>";
$temp.= "<p>Swimmer First Name: ".$D1SwimmerFirstName."</p>";
$temp.= "<p>Swimmer Nick Name: ".$D1SwimmerNickName."</p>";
$temp.= "<p>Swimmer Initial: ".$D1SwimmerInitial."</p>";
$temp.= "<p>Swimmer USS Num: ".$D1SwimmerUSSNUM."</p>";
$temp.= "<p>Swimmer Team Id: ".$D1SwimmerTeamId."</p>";
$temp.= "<p>Swimmer Date of Birth: ".$D1SwimmerDOB."</p>";
$temp.= "<p>Swimmer Age: ".$D1SwimmerAge."</p>";
								$sql = "Select MembershipNo FROM tblCompetitor WHERE TMID = '".$D1SwimmerUSSNUM."'";
$temp.=$sql; 
								$tblCompetitor=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
								if(mysqli_num_rows($tblCompetitor) > 0){
									if ($Competitor = mysqli_fetch_array($tblCompetitor)){
										$MembershipNo = $Competitor["MembershipNo"];
										if($Competitor["Gender"]<>$D1SwimmerGender){
											$sql="UPDATE tblCompetitor SET Gender = '".$D1SwimmerGender."' WHERE MembershipNo  = '".$MembershipNo."'";
											$setCompetitor=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
										}
										if($Competitor["LastName"]<>$D1SwimmerLastName){
											$sql="UPDATE tblCompetitor SET LastName = '".$D1SwimmerLastName."' WHERE MembershipNo  = '".$MembershipNo."'";
											$setCompetitor=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
										}
										if($Competitor["FirstName"]<>$D1SwimmerFirstName){
											$sql="UPDATE tblCompetitor SET FirstName = '".$D1SwimmerFirstName."' WHERE MembershipNo  = '".$MembershipNo."'";
											$setCompetitor=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
										}
										if($Competitor["NickName"]<>$D1SwimmerNickName){
											$sql="UPDATE tblCompetitor SET NickName = '".$D1SwimmerNickName."' WHERE MembershipNo  = '".$MembershipNo."'";
											$setCompetitor=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
										}
										if($Competitor["Initial"]<>$D1SwimmerInitial){
											$sql="UPDATE tblCompetitor SET Initial = '".$D1SwimmerInitial."' WHERE MembershipNo  = '".$MembershipNo."'";
											$setCompetitor=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
										}
										if($Competitor["TeamId"]<>$D1SwimmerTeamId){
											$sql="UPDATE tblCompetitor SET TeamId = '".$D1SwimmerTeamId."' WHERE MembershipNo  = '".$MembershipNo."'";
											$setCompetitor=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
										}
										if($meet["BirthDate"]<>date("Y-m-d",$D1SwimmerDOB)){
											$sql="UPDATE tblCompetitor SET BirthDate = ".date("Y-m-d",$D1SwimmerDOB)." WHERE MembershipNo  = '".$MembershipNo."'";
											$setCompetitor=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
										}
										$entryId = 0;
									} else {
										$sql = "Select max(MembershipNo) AS MaxMembershipNo FROM tblCompetitor";
										$findMaxCompetitor=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
										if($findMaxCompetitor){
											if($maxCompetitor = mysqli_fetch_array($findMaxCompetitor)){
												$MembershipNo = intval($maxCompetitor["MaxMembershipNo"]) + 1;
												$sql="INSERT INTO tblCompetitor (MembershipNo, Gender, LastName, FirstName, NickName, Initial, TeamId, BirthDate) VALUES (".$MembershipNo.", '".$D1SwimmerGender."', '".$D1SwimmerLastName."', '".$D1SwimmerFirstName."','".$D1SwimmerNickName."','".$D1SwimmerInitial."', '".$D1SwimmerTeamId."',".date("Y-m-d",$D1SwimmerDOB).")";
											}
										}
										$setCompetitor=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
									}
								} else {
									$sql = "Select max(MembershipNo) AS MaxMembershipNo FROM tblCompetitor";
									$findMaxCompetitor=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
									if($findMaxCompetitor){
										if($maxCompetitor = mysqli_fetch_array($findMaxCompetitor)){
											$MembershipNo = intval($maxCompetitor["MaxMembershipNo"]) + 1;
											$sql="INSERT INTO tblCompetitor (MembershipNo, Gender, LastName, FirstName, NickName, Initial, TeamId, BirthDate) VALUES (".$MembershipNo.", '".$D1SwimmerGender."', '".$D1SwimmerLastName."', '".$D1SwimmerFirstName."','".$D1SwimmerNickName."','".$D1SwimmerInitial."', '".$D1SwimmerTeamId."',".date("Y-m-d",$D1SwimmerDOB).")";
										}
									}
									$setCompetitor=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
								}
							}
							break;
						case "E1": //Event Entry
							if($valid and $B1MeetName){
								if($MembershipNo <> 0){
									$E1EventSwimmerGender = substr($line,2,1);
									$E1EventTeamId = ltrim(substr($line,3,5));
									$E1SwimmerLastNameAbbr = rtrim(substr($line,8,5));
									$E1EventGender = substr($line,13,1);
									$E1EventGenderType = substr($line,14,1);
									$E1EventDistance = ltrim(substr($line,15,6));
									$E1EventStroke = substr($line,21,1);
									$E1EventAgeLower = ltrim(substr($line,22,3));
									$E1EventAgeUpper = ltrim(substr($line,25,3));
									$E1EventFee = ltrim(substr($line,28,10));
									$E1EventNumber = ltrim(substr($line,38,3));
									$E1EventSeedTime1 = ltrim(substr($line,42,8));
									$E1EventSeedTime1Course = substr($line,50,1);
									$E1EventSeedTime2 = ltrim(substr($line,51,8));
									$E1EventSeedTime2Course = substr($line,59,1);
									if($E1EventSeedTime1=='0'){
										$E1EventSeedTime = $E1EventSeedTime2;
									} else {
										$E1EventSeedTime = $E1EventSeedTime1;
									}
									if(strpos($E1EventSeedTime,'.')){
										$E1SeedHundredths = substr($E1EventSeedTime,strpos($E1EventSeedTime,'.') + 1);
										$E1EventSeedTime = substr($E1EventSeedTime,0,strpos($E1EventSeedTime,'.'));
									} else {
										$E1SeedHundredths = "00";
									}
													
$temp.= "<p>Event Swimmer Gender: ".$E1EventSwimmerGender."</p>";
$temp.= "<p>Event Swimmer Event Id: ".$E1EventTeamId."</p>";
$temp.= "<p>Event Swimmer Last Name Abbr: ".$E1SwimmerLastNameAbbr."</p>";
$temp.= "<p>Event Gender: ".$E1EventGender."</p>";
$temp.= "<p>Event Gender Type: ".$E1EventGenderType."</p>";
$temp.= "<p>Event Distance: ".$E1EventDistance."</p>";
$temp.= "<p>Event Stroke: ".$E1EventStroke."</p>";
$temp.= "<p>Event Age Lower: ".$E1EventAgeLower."</p>";
$temp.= "<p>Event Age Upper: ".$E1EventAgeUpper."</p>";
$temp.= "<p>Event Fee: ".$E1EventFee."</p>";
$temp.= "<p>Event Number: ".$E1EventNumber."</p>";
$temp.= "<p>Event Seed Time 1: ".$E1EventSeedTime1."</p>";
$temp.= "<p>Event Seed Time 1 Course: ".$E1EventSeedTime1Course."</p>";
$temp.= "<p>Event Seed Time 2: ".$E1EventSeedTime2."</p>";
$temp.= "<p>Event Seed Time 2 Course: ".$E1EventSeedTime2Course."</p>";
$temp.= "<p>Event Seed Time: ".$E1EventSeedTime."</p>";
$temp.= "<p>Event Seed Hundredths: ".$E1SeedHundredths."</p>";
									$sql = "SELECT tblEvent.Id AS Id, tblEvent.Meet AS Meet, tblEvent.EventNo AS EventNo, tblEvent.Classification AS Classification, tblEvent.Gender AS Gender, ";
									$sql .= "tblEvent.Type AS Type, tblEvent.MinAge AS MinAge, tblEvent.MaxAge AS MaxAge, tblEvent.Distance AS Distance, tblStroke.Code AS Stroke, tblEvent.QT AS QT, ";
									$sql .= "tblEvent.Swim AS Swim  FROM tblEvent INNER JOIN tblStroke ON tblEvent.Stroke = tblStroke.Id ";
									$sql .= "WHERE tblEvent.Meet = ".$meet["Id"]." AND tblEvent.EventNo = ".$E1EventNumber;
$temp.=$sql; 
									$events=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
									if($events){
										if(mysqli_num_rows($events) > 0){
											if($event = mysqli_fetch_array($events)){
												$eventId = $event["Id"];
												if($event["Gender"] <> $E1EventGender){$errorstr .= "Gender not valid for event ".$event["Id"]." ".$event["Gender"]." <> ".$E1EventGender."<br/>";}
												if($event["Distance"] <> $E1EventDistance){$errorstr .= "Distance not valid for event ".$event["Id"]." ".$event["Distance"]." <> ".$E1EventDistance."<br/>";}
												if($event["Stroke"] <> $E1EventStroke){$errorstr .= "Stroke not valid for event ".$event["Id"]." ".$event["Stroke"]." <> ".$E1EventStroke."<br/>";}
												if($event["MinAge"] <> $E1EventAgeLower){$errorstr .= "Min Age not valid for event ".$event["Id"]." ".$event["MinAge"]." <> ".$E1EventAgeLower."<br/>";}
												if(($event["MaxAge"] <> $E1EventAgeUpper) AND !(($event["MaxAge"] == 0) AND ($E1EventAgeUpper == 109))){$errorstr .= "Max Age not valid for event ".$event["Id"]." ".$event["MaxAge"]." <> ".$E1EventAgeUpper."<br/>";}
												if($fileType == "Entries"){
													$sql = "SELECT Id FROM tblEntry WHERE Event = ".$eventId." AND Competitor = ".$MembershipNo;
$temp.=$sql; 
													$entries=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
													if($entries){
														if(mysqli_num_rows($entries) == 0){
															$sql = "INSERT INTO tblEntry (Event, Competitor, SeedTime, Hundredths, Course) VALUES (".$eventId.", ".$MembershipNo.", ".$E1EventSeedTime.", ".$E1SeedHundredths.",'".$E1EventSeedTime1Course."')";
$temp.=$sql; 
															$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
														}
													} else {
														$sql = "INSERT INTO tblEntry (Event, Competitor, SeedTime, Hundredths, Course) VALUES (".$eventId.", ".$MembershipNo.", ".$E1EventSeedTime.", ".$E1SeedHundredths.",'".$E1EventSeedTime1Course."')";
$temp.=$sql; 
														$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
													}
												} elseif($fileType == "Results") {
													$sql = "SELECT Id FROM tblEntry WHERE Event = ".$eventId." AND Competitor = ".$MembershipNo;
$temp.=$sql; 
													$entries=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
													if($entries){
														if(mysqli_num_rows($entries) > 0){
															if($entry = mysqli_fetch_array($entries)){
																$EntryId = $entry["Id"];
															} else {
																$errorstr .= "Entry does not exist: Event = ".$eventId." Competitor = ".$MembershipNo."<br/>";
															}
														} else {
															$errorstr .= "Entry does not exist: Event = ".$eventId." Competitor = ".$MembershipNo."<br/>";
															$sql = "INSERT INTO tblEntry (Event, Competitor, SeedTime, Hundredths, Course) VALUES (".$eventId.", ".$MembershipNo.", ".$E1EventSeedTime.", ".$E1SeedHundredths.",'".$E1EventSeedTime1Course."')";
$temp.=$sql; 
															$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
															$sql = "SELECT Id FROM tblEntry WHERE Event = ".$eventId." AND Competitor = ".$MembershipNo;
$temp.=$sql;
															$entries2=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
															if($entries2){
																if(mysqli_num_rows($entries2) > 0){
																	if($entry2 = mysqli_fetch_array($entries2)){
																		$EntryId = $entry2["Id"];
																	} else {
																		$errorstr .= "Could not create entry: Event = ".$eventId." Competitor = ".$MembershipNo."<br/>";
																	}
																} else {
																	$errorstr .= "Could not create entry: Event = ".$eventId." Competitor = ".$MembershipNo."<br/>";
																}
															} else {
																$errorstr .= "Could not create entry: Event = ".$eventId." Competitor = ".$MembershipNo."<br/>";
															}
														}
													} else {
														$errorstr .= "Entry does not exist: Event = ".$eventId." Competitor = ".$MembershipNo."<br/>";
														$sql = "INSERT INTO tblEntry (Event, Competitor, SeedTime, Hundredths, Course) VALUES (".$eventId.", ".$MembershipNo.", ".$E1EventSeedTime.", ".$E1SeedHundredths.",'".$E1EventSeedTime1Course."')";
$temp.=$sql; 
														$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
														$sql = "SELECT Id FROM tblEntry WHERE Event = ".$eventId." AND Competitor = ".$MembershipNo;
$temp.=$sql;
														$entries2=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
														if($entries2){
															if(mysqli_num_rows($entries2) > 0){
																if($entry2 = mysqli_fetch_array($entries2)){
																	$EntryId = $entry2["Id"];
																} else {
																	$errorstr .= "Could not create entry: Event = ".$eventId." Competitor = ".$MembershipNo."<br/>";
																}
															} else {
																$errorstr .= "Could not create entry: Event = ".$eventId." Competitor = ".$MembershipNo."<br/>";
															}
														} else {
															$errorstr .= "Could not create entry: Event = ".$eventId." Competitor = ".$MembershipNo."<br/>";
														}
													}
												}
											} else {
												$errorstr .= "Event does not exist: Meet = ".$meet["Id"]." AND EventNo = ".$E1EventNumber." Competitor = ".$MembershipNo."<br/>";
											}
										} else {
											$errorstr .= "Event does not exist: Meet = ".$meet["Id"]." AND EventNo = ".$E1EventNumber." Competitor = ".$MembershipNo."<br/>";
										}
									} else {
										$errorstr .= "Event does not exist: Meet = ".$meet["Id"]." AND EventNo = ".$E1EventNumber." Competitor = ".$MembershipNo."<br/>";
									}
								} else {
									$errorstr .= "Event not imported as no member specified<br/>";
								}
							} else {
								$errorstr .= "Event not imported as no meet specified<br/>";
							}
							break;
						case "E2": //Individual Event Results
							if($valid and $B1MeetName){
								if($MembershipNo <> 0){
									if($EntryId <> 0){
										$E2ResultType = substr($line,2,1);
										$E2ResultTime = ltrim(substr($line,3,8));
										if(strpos($E2ResultTime,'.')){
											$E2ResultHundredths = substr($E2ResultTime,strpos($E2ResultTime,'.') + 1);
											$E2ResultTime = substr($E2ResultTime,0,strpos($E2ResultTime,'.'));
										} else {
											$E2ResultHundredths = "00";
										}
										$E2ResultCourse = substr($line,11,1);
										$E2ResultTimeCode = rtrim(substr($line,12,3));
										$E2ResultHeat = ltrim(substr($line,20,3));
										$E2ResultLane = ltrim(substr($line,23,3));
										$E2ResultPlaceInHeat = ltrim(substr($line,26,3));
										$E2ResultOverallPlace = ltrim(substr($line,29,4));
										$E2ResultT1 = ltrim(substr($line,36,8));
										$E2ResultT2 = ltrim(substr($line,44,8));
										$E2ResultT3 = ltrim(substr($line,52,8));
										$E2ResultT4 = ltrim(substr($line,65,8));
										$E2ResultT5 = ltrim(substr($line,74,8));
										$E2ResultDay = strtotime(substr($line,102,2)."/".substr($line,104,2)."/".substr($line,106,4));
$temp.= "<p>Result Type: ".$E2ResultType."</p>";
$temp.= "<p>Result Time: ".$E2ResultTime.".".$E2ResultHundredths."</p>";
$temp.= "<p>Result Course: ".$E2ResultCourse."</p>";
$temp.= "<p>Result Time Code: ".$E2ResultTimeCode."</p>";
$temp.= "<p>Result Heat: ".$E2ResultHeat."</p>";
$temp.= "<p>Result Lane: ".$E2ResultLane."</p>";
$temp.= "<p>Result Place in Heat: ".$E2ResultPlaceInHeat."</p>";
$temp.= "<p>Result Overall Place: ".$E2ResultOverallPlace."</p>";
$temp.= "<p>Result T1: ".$E2ResultT1."</p>";
$temp.= "<p>Result T2: ".$E2ResultT2."</p>";
$temp.= "<p>Result T3: ".$E2ResultT3."</p>";
$temp.= "<p>Result T4: ".$E2ResultT4."</p>";
$temp.= "<p>Result T5: ".$E2ResultT5."</p>";
$temp.= "<p>Result Day: ".$E2ResultDay."</p>";
										if($fileType == "Results"){
											$sql = "SELECT Id FROM tblResult WHERE Entry = ".$EntryId;
$temp.=$sql; 
											$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
											if($result){
												if(mysqli_num_rows($result) == 0){
													$sql = "INSERT INTO tblResult (Entry, Type, Time, Hundredths, Course, TimeCode, Heat, Lane, Place, OverallPlace) VALUES (".$EntryId.", '".$E2ResultType."', SEC_TO_TIME(".$E2ResultTime."), ";
													$sql .= $E2ResultHundredths.", '".$E2ResultCourse."', '".$E2ResultTimeCode."', ".$E2ResultHeat.", ".$E2ResultLane.", ".$E2ResultPlaceInHeat.", ".$E2ResultOverallPlace.")";
$temp.=$sql; 
													$result2=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
												}
											} else {
												$sql = "INSERT INTO tblResult (Entry, Type, Time, Hundredths, Course, TimeCode, Heat, Lane, Place, OverallPlace) VALUES (".$EntryId.", '".$E2ResultType."', SEC_TO_TIME(".$E2ResultTime."), ";
												$sql .= $E2ResultHundredths.", '".$E2ResultCourse."', '".$E2ResultTimeCode."', ".$E2ResultHeat.", ".$E2ResultLane.", ".$E2ResultPlaceInHeat.", ".$E2ResultOverallPlace.")";
$temp.=$sql; 
												$result2=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
											}
										}
									} else {
										$errorstr .= "Result not imported as no Event specified<br/>";
									}
								} else {
									$errorstr .= "Event not imported as no member specified<br/>";
								}
							} else {
								$errorstr .= "Event not imported as no meet specified<br/>";
							}
							break;
						case "F1": //Relay Event Entry
							$F1RelayTeamId = rtrim(substr($line,2,5));
							$F1RelayTeam = substr($line,7,1);
							$F1RelayGender = substr($line,12,1);
							$F1RelayGender1 = substr($line,13,1);
							$F1RelayGender2 = substr($line,14,1);
							$F1RelayDistance = rtrim(substr($line,15,6));
							$F1RelayStroke = rtrim(substr($line,21,1));
							$F1RelayAgeLower = rtrim(substr($line,22,3));
							$F1RelayAgeUpper = rtrim(substr($line,25,3));
							$F1RelayFee = rtrim(substr($line,28,10));
							$F1RelayEventNumber = rtrim(substr($line,38,3));
							$F1RelaySeedTime1 = rtrim(substr($line,42,8));
							$F1RelaySeedTime1Course = rtrim(substr($line,50,1));
							$F1RelaySeedTime2 = rtrim(substr($line,51,8));
							$F1RelaySeedTime2Course = rtrim(substr($line,59,1));
							break;
						case "F2": //Relay Event Result
							$F2RelayResultType = substr($line,2,1);
							$F2RelayResultTime = rtrim(substr($line,3,8));
							$F2RelayResultCourse = rtrim(substr($line,11,1));
							$F2RelayResultTimeCode = rtrim(substr($line,12,3));
							$F2RelayResultHeat = rtrim(substr($line,20,3));
							$F2RelayResultLane = rtrim(substr($line,23,3));
							$F2RelayResultPlaceInHeat = rtrim(substr($line,26,3));
							$F2RelayResultOverallPlace = rtrim(substr($line,29,3));
							$F2RelayResultT1 = rtrim(substr($line,36,8));
							$F2RelayResultT2 = rtrim(substr($line,44,8));
							$F2RelayResultT3 = rtrim(substr($line,52,8));
							$F2RelayResultT4 = rtrim(substr($line,65,8));
							$F2RelayResultT5 = rtrim(substr($line,74,8));
							$F2RelayResultDay = strtotime(substr($line,102,2)."/".substr($line,104,2)."/".substr($line,106,4));
							break;
						case "F3": //Relay Swimmers
							$F3RelaySwimmer1Gender = substr($line,2,1);
							$F3RelaySwimmer1Id = rtrim(substr($line,3,5));
							$F3RelaySwimmer1Abbr = rtrim(substr($line,8,5));
							$F3RelaySwimmer1GenderType = substr($line,13,1);
							$F3RelaySwimmer1RelayLeg = substr($line,14,1);
							$F3RelaySwimmer2Gender = substr($line,15,1);
							$F3RelaySwimmer2Id = rtrim(substr($line,16,5));
							$F3RelaySwimmer2Abbr = rtrim(substr($line,21,5));
							$F3RelaySwimmer2GenderType = substr($line,26,1);
							$F3RelaySwimmer2RelayLeg = substr($line,27,1);
							$F3RelaySwimmer3Gender = substr($line,28,1);
							$F3RelaySwimmer3Id = rtrim(substr($line,29,5));
							$F3RelaySwimmer3Abbr = rtrim(substr($line,34,5));
							$F3RelaySwimmer3GenderType = substr($line,39,1);
							$F3RelaySwimmer3RelayLeg = substr($line,40,1);
							$F3RelaySwimmer4Gender = substr($line,41,1);
							$F3RelaySwimmer4Id = rtrim(substr($line,42,5));
							$F3RelaySwimmer4Abbr = rtrim(substr($line,47,5));
							$F3RelaySwimmer4GenderType = substr($line,52,1);
							$F3RelaySwimmer4RelayLeg = substr($line,53,1);
							$F3RelaySwimmer5Gender = substr($line,54,1);
							$F3RelaySwimmer5Id = rtrim(substr($line,55,5));
							$F3RelaySwimmer5bbr = rtrim(substr($line,60,5));
							$F3RelaySwimmer5GenderType = substr($line,65,1);
							$F3RelaySwimmer5RelayLeg = substr($line,66,1);
							$F3RelaySwimmer6Gender = substr($line,67,1);
							$F3RelaySwimmer6Id = rtrim(substr($line,68,5));
							$F3RelaySwimmer6Abbr = rtrim(substr($line,73,5));
							$F3RelaySwimmer6GenderType = substr($line,78,1);
							$F3RelaySwimmer6RelayLeg = substr($line,79,1);
							$F3RelaySwimmer7Gender = substr($line,80,1);
							$F3RelaySwimmer7Id = rtrim(substr($line,81,5));
							$F3RelaySwimmer7Abbr = rtrim(substr($line,86,5));
							$F3RelaySwimmer7GenderType = substr($line,91,1);
							$F3RelaySwimmer7RelayLeg = substr($line,92,1);
							$F3RelaySwimmer8Gender = substr($line,93,1);
							$F3RelaySwimmer8Id = rtrim(substr($line,94,5));
							$F3RelaySwimmer8Abbr = rtrim(substr($line,99,5));
							$F3RelaySwimmer8GenderType = substr($line,104,1);
							$F3RelaySwimmer8RelayLeg = substr($line,105,1);
							break;
						case "G1": //Split Times
							$G1Split1ResultType = substr($line,2,1);
							$G1Split1Length = rtrim(substr($line,3,2));
							$G1Split1Time = rtrim(substr($line,5,8));
							$G1Split2ResultType = substr($line,13,1);
							$G1Split2Length = rtrim(substr($line,14,2));
							$G1Split2Time = rtrim(substr($line,16,8));
							$G1Split3ResultType = substr($line,24,1);
							$G1Split3Length = rtrim(substr($line,25,2));
							$G1Split3Time = rtrim(substr($line,27,8));
							$G1Split4ResultType = substr($line,35,1);
							$G1Split4Length = rtrim(substr($line,36,2));
							$G1Split4Time = rtrim(substr($line,38,8));
							$G1Split5ResultType = substr($line,46,1);
							$G1Split5Length = rtrim(substr($line,47,2));
							$G1Split5Time = rtrim(substr($line,49,8));
							$G1Split6ResultType = substr($line,57,1);
							$G1Split6Length = rtrim(substr($line,58,2));
							$G1Split6Time = rtrim(substr($line,60,8));
							$G1Split7ResultType = substr($line,68,1);
							$G1Split7Length = rtrim(substr($line,69,2));
							$G1Split7Time = rtrim(substr($line,71,8));
							$G1Split8ResultType = substr($line,79,1);
							$G1Split8Length = rtrim(substr($line,80,2));
							$G1Split8Time = rtrim(substr($line,82,8));
							$G1Split9ResultType = substr($line,90,1);
							$G1Split9Length = rtrim(substr($line,91,2));
							$G1Split9Time = rtrim(substr($line,93,8));
							$G1Split10ResultType = substr($line,101,1);
							$G1Split10Length = rtrim(substr($line,102,2));
							$G1Split10Time = rtrim(substr($line,104,8));
							break;
					}
				}
			}
			fclose($file);
		}
		return $temp.$errorstr;
	}
	function hy3checksum($line){
		if(strlen($line)==128){
			$i = 0;
			$sum = 0;
			while($i<strlen($line)){
				$sum += ord(substr($line,$i,1)) * (1 + $i % 2);
				$i++;
			}
			$sum = (($sum - $sum % 21) / 21) + 205;
			$cs = str_pad($sum % 100,2,"0", STR_PAD_LEFT);
			$cs2 = substr($cs,1,1).substr($cs,0,1);
			return $cs2;
		} else { 
			return "00";
		}
	}
	function ExportHy3($mysqli,$meet,$exportFileName,$fileType){
		if (strlen($exportFileName) == 0){
			echo "Error: Zero filename size<br>";
		} else {
$temp.="<p>".$exportFileName."</p>";
			$fp = fopen('php://output', 'w');
			if($fp){
				header('Content-Type: text/csv');
				header('Content-Disposition: attachment; filename="'.$exportFileName.'"');
				header('Pragma: no-cache');
				header('Expires: 0');
				$line = "A1";
				if($fileType == "Entries"){
					$line .= "02Meet Entries             ";
				} elseif($fileType == "Results") {
					$line .= "07Results From MM to TM    ";
				}
				$line .= "Hy-Tek, Ltd    ";
				$line .= "Win-TM 5.0Jb  ";
				$line .= date("mdY H:i A");
				$line .= "Campbelltown ASC Inc.                                ";
				$line .= hy3checksum($line);
				fwrite($fp, $line."\n");
				
				$line = "B1";
				$line .= str_pad($meet["Name"],45);
				$line .= str_pad($meet["Location"],45);
				$line .= date("mdY",$meet["StartDate"]);
				$line .= date("mdY",$meet["EndDate"]);
				$line .= date("mdY",$meet["AgeUpDate"]);
				$line .= sprintf("%5s",$meet["Elevation"]);
				$line .= "       ";
				$line .= hy3checksum($line);
				fwrite($fp, $line."\n");
				
				$line = "B2";
				$line .= "                                                                                          ";
				if($fileType == "Entries"){
					$line .= "      ";
				} elseif($fileType == "Results") {
					$line .= "040101";
				}
				$line .= $meet["CourseCode"];
				$line .= "   0.00S                     ";
				$line .= hy3checksum($line);
				fwrite($fp, $line."\n");

				$line = "C1CMBT Campbelltown SC                                 Justin McEvoy                                                   AGE      ";
				$line .= hy3checksum($line);
				fwrite($fp, $line."\n");

				$line = "C2Anita Vorrias                 PO Box 509                    Campbelltown                  NS2560      AUSWAUST                09";
				fwrite($fp, $line."\n");

				$line = "C3                              0417268204                                                  cascracesecretary@gmail.com         54";
				fwrite($fp, $line."\n");

				$line = "C6Justin McEvoy                 Mr             0408 416 443                                                                     38";
				fwrite($fp, $line."\n");
				
				$sql = "SELECT tblCompetitor.MembershipNo AS MembershipNo, tblCompetitor.Gender AS Gender, tblCompetitor.TeamId AS TeamId,  ";
				$sql .= "tblCompetitor.LastName AS LastName, tblCompetitor.FirstName AS FirstName, tblCompetitor.Initial AS Initial, ";
				$sql .= "tblCompetitor.TMID AS TMID, tblCompetitor.BirthDate AS DOB, tblEvent.Gender AS EventGender, tblEvent.GenderType AS EventGenderType, tblEvent.MaxAge AS EventAgeUpper, ";
				$sql .= "tblEvent.Distance AS EventDistance, tblEvent.Stroke AS EventStroke, tblEvent.MinAge AS EventAgeLower, tblEvent.EventNo AS EventNo, ";
				$sql .= "tblEntry.SeedTime AS EntrySeedTime, tblEntry.Course AS EntrySeedTimeCourse, ";
				$sql .= "tblResult.Time AS ResultTime, tblResult.Hundredths AS ResultHundredths, tblResult.Course AS ResultCourse, tblResult.TimeCode AS ResultTimeCode, ";
				$sql .= "tblResult.Heat AS ResultHeat, tblResult.Lane AS ResultLane, tblResult.Place AS ResultPlaceInHeat, tblResult.OverallPlace AS ResultOverallPlace ";
				$sql .= "FROM ((tblCompetitor INNER JOIN tblEntry ON tblCompetitor.MembershipNo = tblEntry.Competitor) ";
				$sql .= "INNER JOIN tblEvent ON tblEvent.Id = tblEntry.Event) ";
				$sql .= "INNER JOIN tblResult ON tblEntry.Id = tblResult.Entry WHERE tblEvent.Meet = ".$meet["Id"]." ";
				$sql .= "ORDER BY tblCompetitor.MembershipNo, tblEvent.Id";
				$tblCompetitor=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
				$MembershipNo = 0;
				if(mysqli_num_rows($tblCompetitor) > 0){
					if ($Competitor = mysqli_fetch_array($tblCompetitor)){
						if($MembershipNo != $Competitor["MembershipNo"]){
							$MembershipNo = $Competitor["MembershipNo"];
							$line = "D1";
							$line .= $Competitor["Gender"];
							$line .= sprintf("%5s",$Competitor["TeamID"]);
							$line .= sprintf("%20s",$Competitor["LastName"]);
							$line .= sprintf("%20s",$Competitor["FirstName"]);
							$line .= "                    ";//nick name
							$line .= $Competitor["Initial"];
							$line .= sprintf("%14s",$Competitor["TMID"]);
							$line .= "     ";//Team ID
							$line .= sprintf("%m%d%Y",$Competitor["DOB"]);
							$line .= sprintf("%3s",floor($Meet["AgeUpDate"] - $Competitor["DOB"]));
							$line .= "                             ";
							$line .= hy3checksum($line);
							fwrite($fp, $line."\n");
						}

						if($fileType == "Entries"){
							$line = "E1";
							$line .= $Competitor["Gender"];
							$line .= sprintf("%5s",$Competitor["TeamID"]);
							$line .= sprintf("%5s",left($Competitor["LastName"],5));
							$line .= $Competitor["EventGender"];
							$line .= $Competitor["EventGenderType"];
							$line .= sprintf("%6s",$Competitor["EventDistance"]);
							$line .= $Competitor["EventStroke"];
							$line .= sprintf("%3s",$Competitor["EventAgeLower"]);
							$line .= sprintf("%3s",$Competitor["EventAgeUpper"]);
							$line .= "      1.50";
							$line .= sprintf("%3s",$Competitor["EventNo"]);
							$line .= "  ";
							$line .= "      0";
							$line .= sprintf("%1s",$Competitor["EntrySeedTimeCourse"]);
							$line .= " ";
							$line .= sprintf("%7s",$Competitor["EntrySeedTime"]);
							$line .= sprintf("%1s",$Competitor["EntrySeedTimeCourse"]);
							$line .= "        ";
							$line .= "        ";
							$line .= "   ";
							$line .= " ";
							$line .= " ";
							$line .= "               ";
							$line .= " ";
							$line .= "                     ";
							$line .= hy3checksum($line);
							fwrite($fp, $line."\n");
						} elseif($fileType == "Results") {
							$line = "E1";
							$line .= $Competitor["Gender"];
							$line .= sprintf("%5s",$Competitor["TeamID"]);
							$line .= sprintf("%5s",left($Competitor["LastName"],5));
							$line .= $Competitor["EventGender"];
							$line .= $Competitor["EventGenderType"];
							$line .= sprintf("%6s",$Competitor["EventDistance"]);
							$line .= $Competitor["EventStroke"];
							$line .= sprintf("%3s",$Competitor["EventAgeLower"]);
							$line .= sprintf("%3s",$Competitor["EventAgeUpper"]);
							$line .= "      1.50";
							$line .= sprintf("%3s",$Competitor["EventNo"]);
							$line .= "  ";
							$line .= sprintf("%7s",$Competitor["EntrySeedTime"]);
							$line .= sprintf("%1s",$Competitor["EntrySeedTimeCourse"]);
							$line .= " ";
							$line .= sprintf("%7s",$Competitor["EntrySeedTime"]);
							$line .= sprintf("%1s",$Competitor["EntrySeedTimeCourse"]);
							$line .= "    0.00";
							$line .= "    0.00";
							$line .= "   ";
							$line .= "N";
							$line .= "N";
							$line .= "               ";
							$line .= "N";
							$line .= "                     ";
							$line .= hy3checksum($line);
							fwrite($fp, $line."\n");
							
							$line = "E2";
							$line .= "F"; //Result Type F=Final P=Prelim
							$line .= sprintf("%8.2e",$Competitor["ResultTime"] + $Competitor["ResultHundredths"]/100);
							$line .= $Competitor["ResultCourse"];
							$line .= $Competitor["ResultTimeCode"];
							$line .= "     0";
							$line .= sprintf("%3s",$Competitor["ResultHeat"]);
							$line .= sprintf("%3s",$Competitor["ResultLane"]);
							$line .= sprintf("%3s",$Competitor["ResultPlaceInHeat"]);
							$line .= sprintf("%4s",$Competitor["ResultOverallPlace"]);
							$line .= "  0";
							$line .= "    0.00";
							$line .= "    0.00";
							$line .= "    0.00";
							$line .= "     ";
							$line .= "    0.00";
							$line .= " ";
							$line .= "    0.00";
							$line .= "                                        0     ";
							$line .= hy3checksum($line);
							fwrite($fp, $line."\n");
						}
					}
				}
				die;
			}
		}
		return $temp.$errorstr;
	}
	
	if ($_COOKIE["auth"] == "1"){
		$mysql_host = "mysql4.000webhost.com";
		$mysql_database = "a3498016_casc";
		$mysql_user = "a3498016_casc";
		$mysql_password = "IWant2Swim";
		$mysqli = mysqli_connect($mysql_host, $mysql_user, $mysql_password, $mysql_database);
		$temp="";
		if(isset($_POST["Add"])){
			if ($_FILES["addfile"]["error"] > 0){
				echo "Error: " . $_FILES["importedfile"]["error"] . "<br>";
			} else {
$temp.="<p>".$_FILES['addfile']['name']."</p>";
				$file = fopen($_FILES['addfile']['tmp_name'],"r");
				$valid = true;
				if(!feof($file)){
					$line = fgets($file);
					$firstLine = explode(";",$line);
					if(sizeof($firstLine) == 11){
						$meetName = $firstLine[0];
						$meetStartDate = $firstLine[1];
						$meetEndDate = $firstLine[2];
						$meetAgeUpDate = $firstLine[3];
						$meetCourseCode = $firstLine[4];
						$meetLocation = $firstLine[5];
						$meetElevation = str_pad($firstLine[6],4);
						$sql="INSERT INTO tblMeet (Name, StartDate, EndDate, AgeUpDate, CourseCode, Location, Elevation, Status) VALUES ('".$meetName."',STR_TO_DATE('".$meetStartDate."','%m/%d/%Y'),STR_TO_DATE('".$meetEndDate."','%m/%d/%Y'),STR_TO_DATE('".$meetAgeUpDate."','%m/%d/%Y'),'".$meetCourseCode."','".$meetLocation."','".$meetElevation."',1)";
$temp.=$sql; 
						$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
						//find the new meet (should be the largest Id)
						$sql = "SELECT Id FROM tblMeet WHERE Name = '".$meetName."' AND CourseCode = '".$meetCourseCode."' AND StartDate = STR_TO_DATE('".$meetStartDate."','%m/%d/%Y') ORDER BY Id DESC LIMIT 1";
$temp.=$sql; 
						$tblMeet=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
						if(mysqli_num_rows($tblMeet) > 0){
							if($info = mysqli_fetch_array($tblMeet)){
								$meetId = $info["Id"];
								$swimNo = 1;
								while(!feof($file)){
									$line = fgets($file);
									$thisLine = explode(";",$line);
									if(sizeof($thisLine) == 18){
										$eventNumber = $thisLine[0];
										$eventClassification = $thisLine[1];
										$eventGender = $thisLine[2];
										$eventType = $thisLine[3];
										$eventMinAge = $thisLine[4];
										$eventMaxAge = $thisLine[5];
										$eventDistance = $thisLine[6];
										$eventCode = $thisLine[7];
										$eventQualifyingTime = $thisLine[9];
										$eventFee = $thisLine[10];
										if(($swimNo == 1) and ($eventCode <> '1')){
											$swimNo = 2;
										}
										$sql="INSERT INTO tblEvent (Meet, EventNo, Classification, Gender, Type, MinAge, MaxAge, Distance, Stroke, QT, Swim) VALUES (".$meetId.",".$eventNumber.",'".$eventClassification."','".$eventGender."','".$eventType."',".$eventMinAge.",".$eventMaxAge.",".$eventDistance.",".$eventCode.",'".$eventQualifyingTime."',".$swimNo.")";
$temp.=$sql; 
										$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
									}
								}
							}
						}
					}
				}
			}
		}
		// Find out if one of the edit/delete/Display/UnDisplay/Close/Open/Export/Import/Archive buttons has been hit
		$sql="SELECT * FROM tblMeet WHERE status BETWEEN 1 AND 5";
$temp.=$sql; 
		$tblMeet=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		$Delete = 0;
		$Edit = 0;
		$Display = 0;
		$UnDisplay = 0;
		$Select = 0;
		$Close = 0;
		$Open = 0;
		$Export = 0;
		$Import = 0;
		$Archive = 0;
		if($tblMeet){
			if(mysqli_num_rows($tblMeet) > 0){
				while ($meet = mysqli_fetch_array($tblMeet)){
					// Delete Meets
					if(isset($_POST["Delete".$meet["Id"]])){
						$Delete = $meet["Id"];
						$sql="DELETE FROM tblEvent WHERE Meet = ".$Delete;
$temp.=$sql; 
						$result2=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
						$sql="DELETE FROM tblMeet WHERE Id = ".$Delete;
$temp.=$sql; 
						$result2=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
					}
					// Edit Meets
					if(isset($_POST["Edit".$meet["Id"]])){
						$Edit = $meet["Id"];
						$sql="SELECT Id, Meet FROM tblEvent WHERE Meet = ".$Edit." ORDER BY Id";
$temp.=$sql; 
						$events=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
						if($events){
							if(mysqli_num_rows($events) > 0){
								while ($event = mysqli_fetch_array($events)){
									$eventId = $event["Id"];
									if(isset($_POST["Modify".$eventId])){
										$sql = "UPDATE tblEvent SET ";
										$sql .= "EventNo = ".$_POST["EventNo".$eventId].", ";
										$sql .= "Distance = ".$_POST["Distance".$eventId].", ";
										$sql .= "Stroke = ".$_POST["Stroke".$eventId].", ";
										$sql .= "Swim = ".$_POST["Swim".$eventId]." ";
										$sql .= "WHERE Id = ".$eventId;
$temp.=$sql; 
										$result2 = mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
									}
								}
							}
						}
						if(isset($_POST["Done"])){
							$Edit = 0;
						}
					}
					//Display Meets
					if(isset($_POST["Display".$meet["Id"]])){
						$Display = $meet["Id"];
						$sql="UPDATE tblMeet SET Status=2 WHERE Id=".$meet["Id"];
$temp.=$sql; 
						$result2=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
					}
					//Un-Display Meets
					if(isset($_POST["UnDisplay".$meet["Id"]])){
						$UnDisplay = $meet["Id"];
						$sql="UPDATE tblMeet SET Status=1 WHERE Id=".$meet["Id"];
$temp.=$sql; 
						$result2=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
					}
					//Close Meets
					if(isset($_POST["Close".$meet["Id"]])){
						$Close = $meet["Id"];
						$sql="UPDATE tblMeet SET Status=3 WHERE Id=".$meet["Id"];
$temp.=$sql; 
						$result2=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
					}
					//Select Entries
					if(isset($_POST["Select".$meet["Id"]])){
						$Select = $meet["Id"];
						$sql="SELECT Id, Meet FROM tblEvent WHERE Meet = ".$Select." ORDER BY Id";
$temp.=$sql; 
						$events=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
						if($events){
							if(mysqli_num_rows($events) > 0){
								while ($event = mysqli_fetch_array($events)){
									$eventId = $event["Id"];
									$sql = "SELECT MembershipNo FROM tblCompetitor";
									$tblCompetitor=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
									if(mysqli_num_rows($tblCompetitor) > 0){
										while ($Competitor = mysqli_fetch_array($tblCompetitor)){
											$MembershipNo = $Competitor["MembershipNo"];
											if(isset($_POST["SelectEvent".$eventId."_".$MembershipNo])){
												$sql = "SELECT * FROM tblEntry WHERE Event = ".$eventId." AND Competitor = ".$MembershipNo;
$temp.=$sql; 
												$tblEntry=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
												if($tblEntry){
													if(mysqli_num_rows($tblEntry) > 0){
														if($entry = mysqli_fetch_array($tblEntry)){
															$sql = "DELETE FROM tblEntry WHERE Id = ".$entry["Id"];
$temp.=$sql; 
															$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
														} else {
															$errorstr .= "Could not select entry";
														}
													} else {
														$sql = "INSERT INTO tblEntry (Event, Competitor) VALUES (".$eventId.", ".$MembershipNo.")";
$temp.=$sql; 
														$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
													}
												} else {
													$sql = "INSERT INTO tblEntry (Event, Competitor) VALUES (".$eventId.", ".$MembershipNo.")";
$temp.=$sql; 
													$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
												}
											}
										}
									}
								}
							}
						}
						if(isset($_POST["Done"])){
							$Select = 0;
						}
					}
					//Open Meets
					if(isset($_POST["Open".$meet["Id"]])){
						$Open = $meet["Id"];
						$sql="UPDATE tblMeet SET Status=2 WHERE Id=".$meet["Id"];
$temp.=$sql; 
						$result2=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
					}
					//Export Meet Entries
					if(isset($_POST["Export".$meet["Id"]])){
						$temp .= ExportHy3($mysqli,$meet,$meet["Name"].".hy3","Entries");
						$sql="UPDATE tblMeet SET Status=4 WHERE Id=".$meet["Id"];
$temp.=$sql; 
						$result2=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
					}
					//Import Entries
					if(isset($_POST["Entry".$meet["Id"]])){
						$temp .= ImportHy3($mysqli,$meet,$_FILES["importEntry"],"Entries");
						$sql="UPDATE tblMeet SET Status=4 WHERE Id=".$meet["Id"];
						$result2=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
					}
					//Import Results
					if(isset($_POST["Import".$meet["Id"]])){
						$temp .= ImportHy3($mysqli,$meet,$_FILES["importedfile"],"Results");
						$sql="UPDATE tblMeet SET Status=5 WHERE Id=".$meet["Id"];
						$result2=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
					}
					// Archive Meet
					if(isset($_POST["Archive".$meet["Id"]])){
						$Archive = $meet["Id"];
						$sql="UPDATE tblMeet SET Status=6 WHERE Id=".$meet["Id"];
						$result2=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
					}
				}
			}
		}
		if (($Edit == 0)AND($Select == 0)) {
			$display_block = $temp.'<form method="post" enctype="multipart/form-data" action="listmeets.php">';
			$display_block .= '<table border="1"><thead><tr><th>Description</th><th>Start Date</th><th>End Date</th><th>Age Up Date</th><th>Course</th><th>Location</th><th>Status</th><th>Previous Step</th><th>Next Step</th></tr></thead><tbody>';
			$sql = "SELECT tblMeet.Id AS MeetId, tblMeet.Name AS MeetName, DATE_FORMAT(tblMeet.StartDate,'%d/%m/%Y') AS StartDate, DATE_FORMAT(tblMeet.EndDate,'%d/%m/%Y') AS EndDate, ";
			$sql .= "DATE_FORMAT(tblMeet.AgeUpDate,'%d/%m/%Y') AS AgeUpDate, tblMeet.CourseCode AS CourseCode, tblCourse.Course AS Course, ";
			$sql .= "tblMeet.Location AS Location, tblMeet.Status AS StatusId, tblStatus.Status AS Status ";
			$sql .= "FROM (tblMeet INNER JOIN tblCourse ON tblMeet.CourseCode = tblCourse.Id) INNER JOIN tblStatus ON tblMeet.Status = tblStatus.Id ";
			$sql .= "WHERE tblMeet.Status BETWEEN 1 AND 5";
			$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
			if($result){
				if(mysqli_num_rows($result) > 0){
					while ($info = mysqli_fetch_array($result)){
						$Course = $info['Course'];
						$Status = $info['Status'];
						$display_block .= '<tr><td>'.$info['MeetName'].'</td>';
						$display_block .= '<td>'.$info['StartDate'].'</td>';
						$display_block .= '<td>'.$info['EndDate'].'</td>';
						$display_block .= '<td>'.$info['AgeUpDate'].'</td>';
						$display_block .= '<td>'.$Course.'</td>';
						$display_block .= '<td>'.$info['Location'].'</td>';
						$display_block .= '<td>'.$Status.'</td>';
						switch($info['StatusId']){
							case "1": // Created
								$display_block .= '<td><input type="submit" name="Delete'.$info["MeetId"].'" value="Delete"/>';
								$display_block .= '<input type="submit" name="Edit'.$info["MeetId"].'" value="Edit"/></td>';
								$display_block .= '<td><input type="submit" name="Display'.$info["MeetId"].'" value="Display"/></td>';
								break;
							case "2": // Opened
								$display_block .= '<td><input type="submit" name="UnDisplay'.$info["MeetId"].'" value="UnDisplay"/></td>';
								$display_block .= '<td><input type="hidden" name="MAX_FILE_SIZE" value="100000" />';
								$display_block .= '<input name="importEntry" type="file" id="importEntry" /><br />';
								$display_block .= '<input type="submit" name="Entry'.$info["MeetId"].'" value="Import Entries"/><br/>';
								$display_block .= '<input type="submit" name="Select'.$info["MeetId"].'" value="Select Entries"/><br/>';
								$display_block .= '<input type="submit" name="Close'.$info["MeetId"].'" value="Close"/></td>';
								break;
							case "3": // Closed
								$display_block .= '<td><input type="submit" name="Open'.$info["MeetId"].'" value="Open"/></td>';
								$display_block .= '<td><input type="submit" name="Export'.$info["MeetId"].'" value="Export Events"/></td>';
								break;
							case "4": // Racing
								$display_block .= '<td><input type="submit" name="Open'.$info["MeetId"].'" value="Open"/></td>';
								$display_block .= '<td><input type="hidden" name="MAX_FILE_SIZE" value="100000" />';
								$display_block .= '<input name="importedfile" type="file" id="importedfile" /><br />';
								$display_block .= '<input type="submit" name="Import'.$info["MeetId"].'" value="Import Results"/></td>';
								break;
							case "5": // Results
								$display_block .= '<td><input type="submit" name="Open'.$info["MeetId"].'" value="Open"/></td>';
								$display_block .= '<td><input type="submit" name="Archive'.$info["MeetId"].'" value="Archive"/></td>';
								break;
							case "6": // Archive
								break;
						}
						$display_block .= '</tr>'; 
					}
				}
			}
			$display_block .= '<tr><td><input type="text" name="Name"/></td>';
			$display_block .= '<td><input type="text" name="StartDate"/></td>';
			$display_block .= '<td><input type="text" name="EndDate"/></td>';
			$display_block .= '<td><input type="text" name="AgeUpDate"/></td>';
			$sql="SELECT * FROM tblCourse";
			$tblCourse=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
			if($tblCourse){
				if(mysqli_num_rows($tblCourse) >= 1){
					$display_block .= '<td><select name="Course">';
					while ($Courses = mysqli_fetch_array($tblCourse)){
						$display_block .= '<option value='.$Courses['Id'].'>'.$Courses['Course'].'</option>';
					}
					$display_block .= '</select></td>';
				} else {
					$display_block .= '<td><input type="text" name="Course"/></td>';
				}
			} else {
				$display_block .= '<td><input type="text" name="Course"/></td>';
			}
			$display_block .= '<td><input type="text" name="Location"/></td>';
			$sql="SELECT * FROM tblStatus WHERE Status='Created'";
			$tblStatus=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
			if($tblStatus){
				if(mysqli_num_rows($tblStatus) >= 1){
					$Statuses = mysqli_fetch_array($tblStatus);
					$Status = $Statuses['Id'];
				} else {
					$Status = '1';
				}
			} else {
				$Course = '1';
			}
			$sql="SELECT * FROM tblTemplate";
			$tblTemplate=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
			if($tblTemplate){
				if(mysqli_num_rows($tblTemplate) >= 1){
					$display_block .= '<td colspan = "2"><select name="Template"">';
					$display_block .= '<option value="0">---Templates---</option>';
					while ($Templates = mysqli_fetch_array($tblTemplate)){
						$display_block .= '<option value="'.$Templates['Id'].'">'.$Templates['Name'].'</option>';
					}
					$display_block .= '</select></td>';
				} else {
					$display_block .= '<td><input type="text" name="Template" value=NULL/></td>';
				}
			} else {
				$display_block .= '<td><input type="text" name="Status" value=NULL/></td>';
			}			
			$display_block .= '<td><input type="submit" name="Create" value="Create New Meet" /></td></tr>';
			$display_block .= '<tr><td></td><td></td><td></td><td></td><td></td><td colspan = "3"><input name="addfile" type="file" id="addfile" /></td>';
			$display_block .= '<td><input type="submit" name="Add" value="Import .hyv file"/></td></tr>';
			$display_block .= '</tbody></table>';
			$display_block .= '</form>';
		} elseif ($Edit <> 0) {
			$display_block = $temp.'<form method="post" enctype="multipart/form-data" action="listmeets.php">';
			$display_block .= '<input type="hidden" name="Edit'.$Edit.'" value="Edit"/>';
			$display_block .= '<table border="1"><thead><tr><th>Event No</th><th>Distance</th><th>Stroke</th><th>Swim</th><th></th></tr></thead><tbody>';
			$sql="SELECT Id, Meet, EventNo, Classification, Gender, Type, MinAge, MaxAge, Distance, Stroke, QT, Swim FROM tblEvent WHERE Meet = ".$Edit." ORDER BY Id";
			$tblEvents=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
			if(mysqli_num_rows($tblEvents) >= 1){
				while ($Events = mysqli_fetch_array($tblEvents)){
					$EventId = $Events['Id'];
					$display_block .= '<tr>';
					$display_block .= '<td><input type="text" name="EventNo'.$EventId.'" value="'.$Events['EventNo'].'"/></</td>';
					$sql="SELECT * FROM tblDistance";
					$tblDistance=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
					if($tblDistance){
						if(mysqli_num_rows($tblDistance) >= 1){
							$display_block = $display_block.'<td><select name="Distance'.$EventId.'">';
							while ($Distances = mysqli_fetch_array($tblDistance)){
								if($Distances['distance'] == $Events['Distance']){
									$display_block = $display_block.'<option value='.$Distances['distance'].' selected>'.$Distances['distance'].'</option>';
								} else {
									$display_block = $display_block.'<option value='.$Distances['distance'].'>'.$Distances['distance'].'</option>';
								}
							}
							$display_block = $display_block.'</select></td>';
						} else {
							$display_block = $display_block.'<td><input type="text" name="Distance'.$EventId.'"/></td>';
						}
					} else {
						$display_block = $display_block.'<td><input type="text" name="Distance'.$EventId.'"/></td>';
					}
					$sql="SELECT * FROM tblStroke";
					$tblStroke=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
					if($tblStroke){
						if(mysqli_num_rows($tblStroke) >= 1){
							$display_block = $display_block.'<td><select name="Stroke'.$EventId.'">';
							while ($Strokes = mysqli_fetch_array($tblStroke)){
								if($Strokes['Id'] == $Events['Stroke']){
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
					$display_block .= '<td><input type="text" name="Swim'.$EventId.'" value="'.$Events['Swim'].'"/></</td>';
					$display_block .= '<td><input type="submit" name="Modify'.$EventId.'" value="Modify"/></</td>';
				$display_block .= '</tr>';
				}
			}
			$display_block .= '</table><p><input type="submit" name="Done" value="Done"/></</p></form>';
		} elseif ($Select <> 0) {
			$eventsql = "SELECT tblEvent.Id AS EventId, tblEvent.Distance AS Distance, tblStroke.Stroke AS Stroke ";
			$eventsql .= "FROM tblEvent INNER JOIN tblStroke ON tblEvent.Stroke = tblStroke.Id ";
			$eventsql .= "WHERE tblEvent.Meet = ".$Select." ORDER BY EventNo";
$temp.=$sql; 
			$events=mysqli_query($mysqli,$eventsql) or die(mysqli_error($mysqli));
			if($events){
				if(mysqli_num_rows($events) > 0){
					$display_block = $temp.'<form method="post" enctype="multipart/form-data" action="listmeets.php">';
					$display_block .= '<input type="hidden" name="Select'.$Select.'" value="Select"/>';
					$display_block .= '<table border="1"><thead><tr><th>Competitor</th>';
					while ($event = mysqli_fetch_array($events)){
						$display_block .= '<th>Event '.$event["EventNo"].'<br/>';
						$display_block .= $event["Stroke"].'<br/>';
						$display_block .= $event["Distance"].'m </th>';
					}
					$display_block .= '</tr></thead><tbody>';
					$sql = "SELECT * FROM tblCompetitor WHERE MemberType <> 'Non-Swimmer' ORDER BY LastName, FirstName";
					$tblCompetitor=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
					if($tblCompetitor){
						if(mysqli_num_rows($tblCompetitor) > 0){
							while ($Competitor = mysqli_fetch_array($tblCompetitor)){
								$MembershipNo = $Competitor["MembershipNo"];
								$display_block .= '<tr><td>'.$Competitor["FirstName"].' '.$Competitor["LastName"].'</td>';
								$sql="SELECT Id, EventNo, Meet FROM tblEvent WHERE Meet = ".$Select." ORDER BY EventNo";
$temp.=$sql; 
								$events=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
								if($events){
									if(mysqli_num_rows($events) > 0){
										while ($event = mysqli_fetch_array($events)){
											$sql = "SELECT * FROM tblEntry WHERE Event = ".$event['Id']." AND Competitor = ".$MembershipNo;
											$tblEntry=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
											if($tblEntry){
												if(mysqli_num_rows($tblEntry) > 0){
													$display_block .= '<td><input type="submit" name="SelectEvent'.$event['Id'].'_'.$MembershipNo.'" value="Unselect"/></td>';
												} else {
													$display_block .= '<td><input type="submit" name="SelectEvent'.$event['Id'].'_'.$MembershipNo.'" value="Select"/></td>';
												}
											} else {
												$display_block .= '<td><input type="submit" name="SelectEvent'.$event['Id'].'_'.$MembershipNo.'" value="Select"/></td>';
											}
										}
									}
								}
								$display_block .= '</tr>';
							}
						}
					}
					$display_block .= '</table><p><input type="submit" name="Done" value="Done"/></</p></form>';
				}
			}
		}
	} else {
		header("Location: loginform.html");
	}
?>
<html>
	<head>
		<title>Meets</title>
	</head>
	<body>
		<?php echo "$display_block"; ?>
	</body>
</html>