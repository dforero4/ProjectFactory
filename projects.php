<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php

define('IN_PHPBB', true);
// Specify the path to phpBB3 installation directory.
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
// The common.php file is required.
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);

$user->setup();
$conn = mysqli_connect('localhost', 'phpbb', 'password', 'phpbb_phpBB3');
	if ($conn->connect_error) {
		die("Connection failed: " . $conn.connect_error);
	}
?>
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
		<title>Project Factory - Get Your Project Done With Project Factory</title>
		<link href="../css/styles.css" rel="stylesheet" type="text/css" media="screen" />
	</head>
	<body>
			<div id="top">
				<a href="../index.php"><img src="../../images/ProjectFactory.png" width="670" height="140" alt=""/></a>
				<center><h4>This CPSC 4392 Capstone Project was created by Daniel Forero, Jeffery Wooldridge, and Mohammed Almashhed!</h4></center>
				<div id="logo">
	  				<a href="main.html"></a>
				</div>
				<div id="login_status">
					<p><?php 
						echo('Logged in as ' . $user->data['username_clean']);
					?></p>
				</div>
			</div>
			<div id="topnav">
				<ul>
				  <li><a href="../index.php">HOME</a></li>
				  <li><a href="projects.php">PROJECTS</a></li>
				  <li><a href="../create/create.php">CREATE</a></li>
				  <li><a href="../..">FORUM</a></li>
				</ul>
			</div>
			
			<FONT face="Arial ,Tahoma" size="4" color="#FFFFFF">
						<marquee bgcolor="#000000" behavior="alternate" direction="right">Welcome to Project Factory</marquee>
						</FONT>
						
			<div id="subbanner">
				<h3>Are you looking for a consolidated portal to bring your team together? If so, we have the perfect service for 							you...<br/>
					Welcome to Project Factory! We are here to provide an easy-to-use, online project management portal for you and your 						team!</h3>

			</div>
			<div id="content">
				<h1><?php 
					$selectHeader = "SELECT pf_field_of_study FROM phpbb_profile_fields_data WHERE user_id = " . $user->data['user_id'] . ";";
					$result = mysqli_query($conn, $selectHeader);
					if($result != false){
						$row = mysqli_fetch_array($result,MYSQLI_BOTH)
						?> Projects for <?php echo $row['pf_field_of_study']; 
					}?>
				</h1>
				
				<?php
						$selectProjs = "SELECT ProjName, NumStudents, MaxStudents, FieldOfStudy, Description FROM Project WHERE FieldOfStudy = 										(SELECT pf_field_of_study FROM phpbb_profile_fields_data WHERE user_id = " . $user->data['user_id'] . 										");";
						$result = mysqli_query($conn,$selectProjs);
						$rowCount = mysqli_num_rows($result);
						if($result == false || $rowCount == 0) {
							?><h2>No Projects Currently Available!</h2><?php
						}else{
							for($i = 1; $i <= $rowCount; $i++){
							
								$row = mysqli_fetch_array($result, MYSQLI_BOTH);
								$rowArray[$i] = $row;
								$userID = $user->data['user_id'];
								
								if(isset($_POST[$i])){
									joinProj($rowArray[$i], $userID);
								}
							}
						?>
				<form action="?action=joinProj" method="post">
				<fieldset>
				<table>
					<thead>
						<tr>
							<th>Project Name</th>
							<th>Current # of Students</th>
							<th>Max # of Students</th>
							<th>Field of Study</th>
							<th>Project Description</th>
						</tr>
					</thead>
					<tbody>		
					<?php
						$selectProjs = "SELECT ProjName, NumStudents, MaxStudents, FieldOfStudy, Description FROM Project WHERE FieldOfStudy = 										(SELECT pf_field_of_study FROM phpbb_profile_fields_data WHERE user_id = " . $user->data['user_id'] . 										");";
						$resultTable = mysqli_query($conn,$selectProjs);
						$rowCount = mysqli_num_rows($resultTable);
						//while($row = mysqli_fetch_array($result,MYSQLI_BOTH))
						for($i = 1; $i <= $rowCount; $i++)
    					{ 
    						$row = mysqli_fetch_array($resultTable, MYSQLI_BOTH);
    					?>
        					<tr>
        						<td><?php echo $row['ProjName']; ?></td>
            					<td><?php echo $row['NumStudents']; ?></td>
            					<td><?php echo $row['MaxStudents']; ?></td>
            					<td><?php echo $row['FieldOfStudy']; ?></td>
            					<td><?php echo $row['Description']; ?></td>
            					<td>
            						<?php
            						if($row['NumStudents'] == $row['MaxStudents']){
            							?><td>Project Closed!</td>
            						<?php
            						}else{
            						?>
            							<input type="submit" value="Join Project!" name="<?php echo $i ?>"/>
            						<?php
            						}?>
           						</td>
        					</tr>
  						<?php
   						}
					?>
					</tbody>
				</table>
				</fieldset>
				</form>
				<?php
				}
				?>
			</div>
			
			<?php
				function joinProj($row, $userID) {
					global $auth;
					
					// Increment the number of members in the specific project.
					$incrementGroupMems = "UPDATE Project SET NumStudents  = NumStudents + 1 WHERE ProjName = '" . $row['ProjName'] . "';";
					
					if(!mysqli_query($GLOBALS['conn'], $incrementGroupMems)) {
						die("Error in incrementing number of group members: " . mysqli_error($GLOBALS['conn']));
					}
					
					$findGroupID = "SELECT group_id from phpbb_groups where group_name = '" . $row['ProjName'] . " Project Group';";
					if(!mysqli_query($GLOBALS['conn'], $findGroupID)) {
						die("Error in getting proper Group ID: " . mysqli_error($GLOBALS['conn']));
					}
					$groupID = mysqli_query($GLOBALS['conn'], $findGroupID);				
					$id = mysqli_fetch_assoc($groupID);
					
					$joinGroupSQL = "INSERT INTO phpbb_user_group (group_id, user_id, user_pending) VALUES ('{$id['group_id']}','{$userID}',0);";
					
					if(!mysqli_query($GLOBALS['conn'], $joinGroupSQL)){
						die("Error in joining group: " . mysqli_error($GLOBALS['conn']));
					}
					
					$auth->acl_clear_prefetch();				

				}
			?>
				
			<div id="rightside">
				<h2>PROJECT LIST INFORMATION</h2>
				
				<p>Project Factory allows our users to apply for certain projects within their specific field of study. This page only displays 					available projects based on your field of study and vacant spots within the group. If you don't see a certain project 					listed, please contact the project's administrator for assistance.
				</p>
				
			</div>
	<div id="footer">
	  <p>&copy;Project Factory. All Rights Reserved | Greatest Website Ever.</p>
	</div>
	</body>

</html>
