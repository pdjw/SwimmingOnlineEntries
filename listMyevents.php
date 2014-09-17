<?php
	if ($_COOKIE["auth"] == "1"){
		$mysql_host = "mysql4.000webhost.com";
		$mysql_database = "a3498016_casc";
		$mysql_user = "a3498016_casc";
		$mysql_password = "IWant2Swim";
		$mysqli = mysqli_connect($mysql_host, $mysql_user, $mysql_password, $mysql_database);
		$memberNo=$_COOKIE["MemberNo"];
$temp="";
		$sql="SELECT * FROM tblCompetitor WHERE MembershipNo = ".$memberNo;
$temp.=$sql."<br/>";
		$tblCompetitor = mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
		if(mysqli_num_rows($tblCompetitor) == 1){
			if($info = mysqli_fetch_array($tblCompetitor)){
				$FirstName=stripslashes($info['FirstName']);
				$LastName=stripslashes($info['LastName']);
				$SystemId=$info["SystemId"];
				$PrimaryId=$info["PrimaryId"];
				if($PrimaryId == 0){$PrimaryId = $SystemId;}
				// Find out if one of the submit buttons has been hit
				$sql="SELECT * FROM tblMeet WHERE status = 2 ORDER BY StartDate";//open
				$tblMeet=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
				$Submit = 0;
				if($tblMeet){
					if(mysqli_num_rows($tblMeet) > 0){
						while ($info = mysqli_fetch_array($tblMeet)){
							$MeetId = $info["Id"];
							$sql = "SELECT MembershipNo, FirstName, LastName FROM tblCompetitor WHERE (SystemId = ".$PrimaryId." OR PrimaryId = ".$PrimaryId.") AND MemberType <> 'Non-Swimmer'";
$temp.=$sql."<br/>";
							$tblCompetitorFamily=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
							if(mysqli_num_rows($tblCompetitorFamily) > 0){
								while ($CompetitorFamily = mysqli_fetch_array($tblCompetitorFamily)){
									if(isset($_POST["Submit".$MeetId])){
										$swim1 = $_POST["swim1".$MeetId."_".$CompetitorFamily["MembershipNo"]];
										$swim2 = $_POST["swim2".$MeetId."_".$CompetitorFamily["MembershipNo"]];
										$sql="SELECT tblEntry.Id AS EntryId, tblEntry.Event AS EventId, tblEvent.Swim AS SwimNo ";
										$sql = $sql."FROM tblEntry INNER JOIN tblEvent ON tblEvent.Id = tblEntry.Event ";
										$sql = $sql."WHERE tblEvent.Meet = ".$MeetId." AND tblEntry.Competitor = ".$CompetitorFamily["MembershipNo"];
$temp.=$sql."<br/>";
										$tblEntry=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
										if($tblEntry){
											if(mysqli_num_rows($tblEntry) > 0){
												while ($info = mysqli_fetch_array($tblEntry)){
													if($POST["EntryId".$info["EntryId"] <> $info["EntryId"]]){
														if($info["SwimNo"]=1){
															if($info["EventId"] <> $swim1){
																$sql="DELETE FROM tblEntry WHERE Id = ".$info["EntryId"];
$temp.=$sql."<br/>";
																$tblEntry2=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
															}
														}
														if($info["SwimNo"]=2){
															if($swim2<>""){
																if($info["EventId"] <> $swim2){
																	$sql="DELETE FROM tblEntry WHERE Id = ".$info["EntryId"];
$temp.=$sql."<br/>";
																$tblEntry2=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
															}
														}
													}
												}
											}
										}
									}
									if(($swim1<>"0")AND($swim1<>"")){  //if the first swim has not been selected, or a first swim had been selected previously
										$sql = "SELECT * FROM tblEntry WHERE Event = ".$swim1." AND Competitor = ".$CompetitorFamily["MembershipNo"];
$temp.=$sql."<br/>swim1".$MeetId."_".$CompetitorFamily["MembershipNo"]."<br/>";
											$tblEntry=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
											if(!$tblEntry){
												$sql = "INSERT INTO tblEntry (Event, Competitor) VALUES (".$swim1.",".$CompetitorFamily["MembershipNo"].")";
$temp.=$sql."<br/>";
												$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
											} else if(mysqli_num_rows($tblEntry) == 0){
												$sql = "INSERT INTO tblEntry (Event, Competitor) VALUES (".$swim1.",".$CompetitorFamily["MembershipNo"].")";
$temp.=$sql."<br/>";
												$result=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
											}
										}
										if(($swim2<>"0")AND($swim2<>"")){  //if the second swim has not been selected, or a second swim had been selected previously
											$sql = "SELECT * FROM tblEntry WHERE Event = ".$swim2." AND Competitor = ".$CompetitorFamily["MembershipNo"];
$temp.=$sql."<br/>";
											$tblEntry=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
											if(!$tblEntry){
												$sql = "INSERT INTO tblEntry (Event, Competitor) VALUES (".$swim2.",".$CompetitorFamily["MembershipNo"].")";
$temp.=$sql."<br/>";
												$result = mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
											} else if(mysqli_num_rows($tblEntry) == 0){
												$sql = "INSERT INTO tblEntry (Event, Competitor) VALUES (".$swim2.",".$CompetitorFamily["MembershipNo"].")";
$temp.=$sql."<br/>";
												$result = mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
											}
										}
									}
								}
							}
						}
					}
				}
				// Find out if one of the undo buttons has been hit
				$sql = "SELECT tblEntry.Id AS EntryId FROM (tblEntry INNER JOIN tblEvent ON tblEvent.Id = tblEntry.Event) INNER JOIN tblMeet on tblEvent.Meet = tblMeet.Id WHERE tblMeet.status = 2";
				$tblEntry=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
				$Undo = 0;
				if($tblEntry){
					if(mysqli_num_rows($tblEntry) > 0){
						while ($info = mysqli_fetch_array($tblEntry)){
							$EntryId = $info["EntryId"];
							if(isset($_POST["Undo".$EntryId])){
								$sql="DELETE FROM tblEntry WHERE Id = ".$info["EntryId"];
$temp.=$sql."<br/>";
								$tblEntry2=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
							}
						}
					}
				}
				$display_block = "<h1>Club night meets for ".$FirstName." ".$LastName."</h1>";
				$sql="SELECT * FROM tblMeet WHERE status = 2 ORDER BY StartDate";//open and not submitted
				$tblMeet=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
				$Edit = 0;
				if($tblMeet){
					if(mysqli_num_rows($tblMeet) > 0){
						$display_block .= "<h2>Upcoming Club Nights</h2>";
						$display_block .= "<form method='post' action='listMyevents.php'>";
						while ($info = mysqli_fetch_array($tblMeet)){
							$MeetId = $info["Id"];
							$MeetStartDate = $info["StartDate"];
							$MeetEndDate = $info["EndDate"];
							$MeetAgeUpDate = $info["AgeUpDate"];
							$MeetName = $info["Name"];
							$display_block .= "<table><tr><td>Meet Info</td><td>".$MeetName."</td>";
							$display_block .= "<td>Start Date: ".$MeetStartDate."</td><td>End Date: ".$MeetEndDate."</td><td>Age Up Date: ".$MeetAgeUpDate."</td></tr>";
							$sql = "SELECT MembershipNo, FirstName, LastName FROM tblCompetitor WHERE (SystemId = ".$PrimaryId." OR PrimaryId = ".$PrimaryId.") AND MemberType <> 'Non-Swimmer'";
							$tblCompetitorFamily=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
							if(mysqli_num_rows($tblCompetitorFamily) > 0){
								$display_block .= "<tr><td></td>";
								while ($CompetitorFamily = mysqli_fetch_array($tblCompetitorFamily)){
									$display_block .= "<td>".$CompetitorFamily["FirstName"]." ".$CompetitorFamily["LastName"]."</td>";
								}
								$display_block .= "</tr><tr><td>Swim 1</td>";
								$tblCompetitorFamily=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
								while ($CompetitorFamily = mysqli_fetch_array($tblCompetitorFamily)){
									$sql = "SELECT tblEvent.Id AS EventId, tblEvent.Distance AS Distance, tblStroke.Stroke AS Stroke, tblEntry.Id AS EntryId ";
									$sql .= "FROM (tblEvent INNER JOIN tblStroke ON tblEvent.Stroke = tblStroke.Id) INNER JOIN tblEntry ON tblEvent.Id = tblEntry.Event ";
									$sql .= "WHERE tblEntry.Competitor = ".$CompetitorFamily["MembershipNo"]." AND tblEvent.Meet = ".$MeetId." AND tblEvent.Swim = 1";
									$tblEntry=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
									if(mysqli_num_rows($tblEntry) == 0){
										$display_block .= "<td><select name='swim1".$MeetId."_".$CompetitorFamily["MembershipNo"]."'>";
										$display_block .= "<option value = 0>No Swim</option>";
										$sql="SELECT tblEvent.Id AS EventId, tblEvent.Distance AS Distance, tblStroke.Stroke AS Stroke ";
										$sql = $sql."FROM tblEvent INNER JOIN tblStroke ON tblEvent.Stroke = tblStroke.Id WHERE tblEvent.Meet = ".$MeetId." AND tblEvent.Swim = 1 ORDER BY EventId";
										$tblEvent=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
										if(mysqli_num_rows($tblEvent) > 0){
											while ($info = mysqli_fetch_array($tblEvent)){
												$EventId = $info["EventId"];
												$Distance = $info["Distance"];
												$Stroke = $info['Stroke'];
												$display_block .= "<option value = ".$EventId.">".$Distance."m ".$Stroke."</option>";
											}
										}
										$display_block .= "</select></td>";
									} else {
										if($info = mysqli_fetch_array($tblEntry)){
											$EntryId = $info["EntryId"];
											$EventId = $info["EventId"];
											$Distance = $info["Distance"];
											$Stroke = $info['Stroke'];
											$display_block .= "<td><input type='hidden' name='EntryId".$EntryId."' value=".$EntryId." />".$Distance."m ".$Stroke."<input type='submit' name='Undo".$EntryId."' value='Undo'/></td>";
										}
									}
								}
								$display_block .= "</tr><tr><td>Swim 2</td>";
								$sql = "SELECT MembershipNo, FirstName, LastName FROM tblCompetitor WHERE (SystemId = ".$PrimaryId." OR PrimaryId = ".$PrimaryId.") AND MemberType <> 'Non-Swimmer'";
								$tblCompetitorFamily=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
								while ($CompetitorFamily = mysqli_fetch_array($tblCompetitorFamily)){
									$sql = "SELECT tblEvent.Id AS EventId, tblEvent.Distance AS Distance, tblStroke.Stroke AS Stroke, tblEntry.Id AS EntryId ";
									$sql .= "FROM (tblEvent INNER JOIN tblStroke ON tblEvent.Stroke = tblStroke.Id) INNER JOIN tblEntry ON tblEvent.Id = tblEntry.Event ";
									$sql .= "WHERE tblEntry.Competitor = ".$CompetitorFamily["MembershipNo"]." AND tblEvent.Meet = ".$MeetId." AND tblEvent.Swim = 2";
									$tblEntry=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
									if(mysqli_num_rows($tblEntry) == 0){
										$display_block .= "<td><select name='swim2".$MeetId."_".$CompetitorFamily["MembershipNo"]."'>";
										$display_block .= "<option value = 0>No Swim</option>";
										$sql="SELECT tblEvent.Id AS EventId, tblEvent.Distance AS Distance, tblStroke.Stroke AS Stroke ";
										$sql = $sql."FROM tblEvent INNER JOIN tblStroke ON tblEvent.Stroke = tblStroke.Id WHERE tblEvent.Meet = ".$MeetId." AND tblEvent.Swim = 2 ORDER BY EventId";
										$tblEvent=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
										if(mysqli_num_rows($tblEvent) > 0){
											while ($info = mysqli_fetch_array($tblEvent)){
												$EventId = $info["EventId"];
												$Distance = $info["Distance"];
												$Stroke = $info['Stroke'];
												$sql="SELECT * FROM tblEntry WHERE Event = ".$EventId." AND Competitor = ".$CompetitorFamily["MembershipNo"];
												$tblEntry=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
												$display_block .= "<option value = ".$EventId.">".$Distance."m ".$Stroke."</option>";
											}
										}
										$display_block .= "</select></td>";
									} else {
										if($info = mysqli_fetch_array($tblEntry)){
											$EntryId = $info["EntryId"];
											$EventId = $info["EventId"];
											$Distance = $info["Distance"];
											$Stroke = $info['Stroke'];
											$display_block .= "<td><input type='hidden' name='EntryId".$EntryId."' value=".$EntryId." />".$Distance."m ".$Stroke."<input type='submit' name='Undo".$EntryId."' value='Undo'/></td>";
										}
									}
								}
								$display_block .= "</tr>";
							}
							$display_block .= '<tr><td></td><td><input type="submit" name="Submit'.$MeetId.'" value="Submit"/></td></tr></table>';
						}
						$display_block .= '</form>';
					}
				}
				$sql="SELECT * FROM tblMeet WHERE status = 3 OR status = 4 ORDER BY StartDate";//closed or racing
				$tblMeet=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
				$Edit = 0;
				if($tblMeet){
					if(mysqli_num_rows($tblMeet) > 0){
						$display_block .= "<h2>Entries Closed</h2>";
						$display_block .= "<table><tr><td>Start Date</td><td>Description</td><td>Swim</td>";
						$sql = "SELECT MembershipNo, FirstName, LastName FROM tblCompetitor WHERE (SystemId = ".$PrimaryId." OR PrimaryId = ".$PrimaryId.") AND MemberType <> 'Non-Swimmer'";
						$tblCompetitorFamily=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
						if(mysqli_num_rows($tblCompetitorFamily) > 0){
							while ($CompetitorFamily = mysqli_fetch_array($tblCompetitorFamily)){
								$display_block .= "<td>".$CompetitorFamily["FirstName"]." ".$CompetitorFamily["LastName"]."</td>";
							}
						}
						$display_block .= "</tr>";
						while ($meet = mysqli_fetch_array($tblMeet)){
							$MeetId = $meet["Id"];
							$display_block .= "<tr><td>".$meet["StartDate"]."</td>";
							$display_block .= "<td>".$meet["Name"]."</td>";
							$display_block .= "<td>Swim 1</td>";
							$sql = "SELECT MembershipNo, FirstName, LastName FROM tblCompetitor WHERE (SystemId = ".$PrimaryId." OR PrimaryId = ".$PrimaryId.") AND MemberType <> 'Non-Swimmer'";
							$tblCompetitorFamily=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
							if(mysqli_num_rows($tblCompetitorFamily) > 0){
								while ($CompetitorFamily = mysqli_fetch_array($tblCompetitorFamily)){
									$sql = "SELECT tblEntry.Id AS EntryId, tblEntry.Event AS EventId, tblEvent.Meet AS MeetId, tblEvent.EventNo as EventNo, ";
									$sql .= "tblEvent.Distance AS Distance, tblEvent.Stroke AS StrokeId, tblStroke.Stroke AS Stroke, tblEvent.Swim AS Swim, ";
									$sql .= "tblCompetitor.FirstName AS FirstName, tblCompetitor.LastName AS LastName ";
									$sql .= "FROM ((tblEntry INNER JOIN tblCompetitor ON tblEntry.Competitor = tblCompetitor.MembershipNo) ";
									$sql .= "INNER JOIN tblEvent ON tblEvent.Id = tblEntry.Event) INNER JOIN tblStroke ON tblEvent.Stroke = tblStroke.Id ";
									$sql .= "WHERE tblEntry.Competitor = ".$CompetitorFamily["MembershipNo"]." AND tblEvent.Meet = ".$MeetId." AND tblEvent.Swim = 1 ORDER BY tblEntry.Event";
									$tblEntry=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
									if(mysqli_num_rows($tblEntry) > 0){
										if($Entry = mysqli_fetch_array($tblEntry)){
											$EventId = $Entry["EventNo"];
											$Distance = $Entry["Distance"];
											$Stroke = $Entry["Stroke"];
											$display_block .= "<td>".$Distance."m ".$Stroke."</td>";
										} else {
											$display_block .= "<td>No Swim</td>";
										}
									} else {
										$display_block .= "<td>No Swim</td>";
									}
								}
							}
							$display_block .= "</tr><tr><td>".$meet["StartDate"]."</td>";
							$display_block .= "<td>".$meet["Name"]."</td>";
							$display_block .= "<td>Swim 2</td>";
							$sql = "SELECT MembershipNo, FirstName, LastName FROM tblCompetitor WHERE (SystemId = ".$PrimaryId." OR PrimaryId = ".$PrimaryId.") AND MemberType <> 'Non-Swimmer'";
							$tblCompetitorFamily=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
							if(mysqli_num_rows($tblCompetitorFamily) > 0){
								while ($CompetitorFamily = mysqli_fetch_array($tblCompetitorFamily)){
									$sql = "SELECT tblEntry.Id AS EntryId, tblEntry.Event AS EventId, tblEvent.Meet AS MeetId, tblEvent.EventNo as EventNo, ";
									$sql .= "tblEvent.Distance AS Distance, tblEvent.Stroke AS StrokeId, tblStroke.Stroke AS Stroke, tblEvent.Swim AS Swim, ";
									$sql .= "tblCompetitor.FirstName AS FirstName, tblCompetitor.LastName AS LastName ";
									$sql .= "FROM ((tblEntry INNER JOIN tblCompetitor ON tblEntry.Competitor = tblCompetitor.MembershipNo) ";
									$sql .= "INNER JOIN tblEvent ON tblEvent.Id = tblEntry.Event) INNER JOIN tblStroke ON tblEvent.Stroke = tblStroke.Id ";
									$sql .= "WHERE tblEntry.Competitor = ".$CompetitorFamily["MembershipNo"]." AND tblEvent.Meet = ".$MeetId." AND tblEvent.Swim = 2 ORDER BY tblEntry.Event";
									$tblEntry=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
									if(mysqli_num_rows($tblEntry) > 0){
										if($Entry = mysqli_fetch_array($tblEntry)){
											$EventId = $Entry["EventNo"];
											$Distance = $Entry["Distance"];
											$Stroke = $Entry["Stroke"];
											$display_block .= "<td>".$Distance."m ".$Stroke."</td>";
										} else {
											$display_block .= "<td>No Swim</td>";
										}
									} else {
										$display_block .= "<td>No Swim</td>";
									}
								}
							}
							$display_block .= "</tr>";
						}
						$display_block = $display_block."</table>";
					}
				}
							
				$sql="SELECT Id, DATE_FORMAT(StartDate,'%e %b %y') AS MeetDate FROM tblMeet WHERE status = 5 ORDER BY StartDate DESC";//results
				$tblMeet=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
				$Edit = 0;
				if($tblMeet){
					if(mysqli_num_rows($tblMeet) > 0){
						$display_block .= "<h2>Results Available</h2>";
						$display_block .= "<table border='1'><tr><td>Start&nbsp;Date</td><td>Swim</td>";
						$sql = "SELECT MembershipNo, FirstName, LastName FROM tblCompetitor WHERE (SystemId = ".$PrimaryId." OR PrimaryId = ".$PrimaryId.") AND MemberType <> 'Non-Swimmer'";
						$tblCompetitorFamily=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
						if(mysqli_num_rows($tblCompetitorFamily) > 0){
							while ($CompetitorFamily = mysqli_fetch_array($tblCompetitorFamily)){
								$display_block .= "<td colspan='2'>".$CompetitorFamily["FirstName"]." ".$CompetitorFamily["LastName"]."</td>";
							}
						}
						$display_block .= "</tr>";
						while ($meet = mysqli_fetch_array($tblMeet)){
							$MeetId = $meet["Id"];
							$display_block .= "<tr><td>".$meet["MeetDate"]."</td>";
							$display_block .= "<td>1st</td>";
							$sql = "SELECT MembershipNo, FirstName, LastName FROM tblCompetitor WHERE (SystemId = ".$PrimaryId." OR PrimaryId = ".$PrimaryId.") AND MemberType <> 'Non-Swimmer'";
							$tblCompetitorFamily=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
							if(mysqli_num_rows($tblCompetitorFamily) > 0){
								while ($CompetitorFamily = mysqli_fetch_array($tblCompetitorFamily)){
									$sql = "SELECT tblEntry.Id AS EntryId, tblEntry.Event AS EventId, TIME_TO_SEC(tblResult.Time) AS Result, tblResult.Hundredths AS Hundredths, tblEvent.Meet AS MeetId, ";
									$sql .= "tblEvent.EventNo as EventNo, tblEvent.Distance AS Distance, tblEvent.Stroke AS StrokeId, tblStroke.Stroke AS Stroke, tblEvent.Swim AS Swim, ";
									$sql .= "tblCompetitor.FirstName AS FirstName, tblCompetitor.LastName AS LastName ";
									$sql .= "FROM (((tblEntry INNER JOIN tblCompetitor ON tblEntry.Competitor = tblCompetitor.MembershipNo) ";
									$sql .= "INNER JOIN tblEvent ON tblEvent.Id = tblEntry.Event) INNER JOIN tblStroke ON tblEvent.Stroke = tblStroke.Id) ";
									$sql .= "INNER JOIN tblResult ON tblEntry.Id = tblResult.Entry ";
									$sql .= "WHERE tblEntry.Competitor = ".$CompetitorFamily["MembershipNo"]." AND tblEvent.Meet = ".$MeetId." AND tblEvent.Swim = 1 ORDER BY tblEntry.Event";
									$tblEntry=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
									if(mysqli_num_rows($tblEntry) > 0){
										if($Entry = mysqli_fetch_array($tblEntry)){
											$EventId = $Entry["EventNo"];
											$Distance = $Entry["Distance"];
											$Stroke = $Entry["Stroke"];
											$display_block .= "<td>".$Distance."m<br/>".$Stroke."</td>";
											$display_block .= "<td>".gmdate("i:s",$Entry["Result"]).sprintf(".%02d",$Entry["Hundredths"])."</td>";
										} else {
											$display_block .= "<td colspan = '2'>No Swim</td>";
										}
									} else {
										$display_block .= "<td colspan = '2'>No Swim</td>";
									}
								}
							} else {
								$display_block .= "<td colspan='4'>".$sql."</td>";
							}
							$display_block .= "</tr><tr><td>".$meet["MeetDate"]."</td>";
							$display_block .= "<td>2nd</td>";
							$sql = "SELECT MembershipNo, FirstName, LastName FROM tblCompetitor WHERE (SystemId = ".$PrimaryId." OR PrimaryId = ".$PrimaryId.") AND MemberType <> 'Non-Swimmer'";
							$tblCompetitorFamily=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
							if(mysqli_num_rows($tblCompetitorFamily) > 0){
								while ($CompetitorFamily = mysqli_fetch_array($tblCompetitorFamily)){
									$sql = "SELECT tblEntry.Id AS EntryId, tblEntry.Event AS EventId, TIME_TO_SEC(tblResult.Time) AS Result, tblResult.Hundredths AS Hundredths, tblEvent.Meet AS MeetId, ";
									$sql .= "tblEvent.EventNo as EventNo, tblEvent.Distance AS Distance, tblEvent.Stroke AS StrokeId, tblStroke.Stroke AS Stroke, tblEvent.Swim AS Swim, ";
									$sql .= "tblCompetitor.FirstName AS FirstName, tblCompetitor.LastName AS LastName ";
									$sql .= "FROM (((tblEntry INNER JOIN tblCompetitor ON tblEntry.Competitor = tblCompetitor.MembershipNo) ";
									$sql .= "INNER JOIN tblEvent ON tblEvent.Id = tblEntry.Event) INNER JOIN tblStroke ON tblEvent.Stroke = tblStroke.Id) ";
									$sql .= "INNER JOIN tblResult ON tblEntry.Id = tblResult.Entry ";
									$sql .= "WHERE tblEntry.Competitor = ".$CompetitorFamily["MembershipNo"]." AND tblEvent.Meet = ".$MeetId." AND tblEvent.Swim = 2 ORDER BY tblEntry.Event";
									$tblEntry=mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
									if(mysqli_num_rows($tblEntry) > 0){
										if($Entry = mysqli_fetch_array($tblEntry)){
											$EventId = $Entry["EventNo"];
											$Distance = $Entry["Distance"];
											$Stroke = $Entry["Stroke"];
											if($Stroke == "Individual Medley"){$Stroke = "Individual<br/>Medley";}
											$display_block .= "<td>".$Distance."m<br/>".$Stroke."</td>";
											$display_block .= "<td>".gmdate("i:s",$Entry["Result"]).sprintf(".%02d",$Entry["Hundredths"])."</td>";
										} else {
											$display_block .= "<td colspan = '2'>No Swim</td>";
										}
									} else {
										$display_block .= "<td colspan = '2'>No Swim</td>";
									}
								}
							}
							$display_block .= "</tr>";
						}
						$display_block .= "</table>";
					}
				}
			} else {
				$display_block = $temp."<p>Unknown swimmer</p>";
			}
		} else {
			$display_block = $temp."<p>Unknown swimmer</p>";
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
