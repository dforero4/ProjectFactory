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
				  <li><a href="../projects/projects.php">PROJECTS</a></li>
				  <li><a href="create.php">CREATE</a></li>
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
	<center>
	<?php
	if(isset($_POST['submit']))
		createProj();
	else
	?>		
	<form action="?action=createProj" method="post">
  <fieldset class="class">
    <legend ><h1>Project Information:</h1></legend>
    <h3>Project Name:<br/>
    <input type="text" name="ProjName" placeholder="Enter Project Name" required/><br/>
    Maximum number of students allowed to join:<br/>
    <input type="text" name="MaxStudents" placeholder="Enter Max # of Students" required/><br/>
     Field of study:<br/>
    <input type="text" name="FieldOfStudy" placeholder="Enter Field of Study" required/><br/>
    Project Description:<br/>
    <input type="text" name="Description" placeholder="Enter Project Description" style="font-size:10pt;height:200px;width:500px;" required/><br/>
    <input type="submit" value="Submit" name="submit"/>
    </h3>
  </fieldset>
</form>
<?php
	function createProj()
	{
		global $auth, $user;
		$conn = new mysqli('localhost', 'phpbb', 'password', 'phpbb_phpBB3');
		if ($conn->connect_error) {
			die("Connection failed: " . $conn.connect_error);
		}
		
		 $query = "SELECT pf_user_prof FROM phpbb_profile_fields_data WHERE user_id = (SELECT user_id FROM phpbb_users WHERE username_clean = '"  . $user->data['username_clean'] . "');";
		 $profRow = mysqli_fetch_assoc(mysqli_query($conn, $query));
		//  if(!$retVal) {
		//  	die("Error: " . mysqli_error($conn));
		//  }
		 
		 $isProf = $profRow['pf_user_prof'];
		//  if($isProf == null) {
		// 	 die("BREAK");
		//  }
		
		//Allow project creation only if this account is a professor account.
		if($isProf == 1)
		{
			//Attempt to insert Project into project table.
			$sqlInsert = "INSERT INTO Project (ProjName,MaxStudents,FieldOfStudy,Description) VALUES ('". $_POST["ProjName"]."','". $_POST["MaxStudents"]."','".$_POST["FieldOfStudy"]."','".$_POST["Description"]."');";
			

			if (mysqli_query($conn, $sqlInsert)) {
    			echo "Project created successfully!";
			} 
			else {
    			die("Error: " . mysqli_error($conn));
			}

			//Create a forum for the project.
			$createForumSql = "INSERT INTO phpbb_forums (forum_name, left_id, right_id, forum_desc, forum_type) VALUES ('" . $_POST["ProjName"]."','2','2','" . $_POST["Description"]. "','1');";
			if (!mysqli_query($conn, $createForumSql)) {
				die("Error creating forum: " . mysqli_error($conn));
			}

			//Create project group.
			$createGroupSql = "INSERT INTO phpbb_groups (group_name, group_type, group_max_recipients) VALUES('" . $_POST["ProjName"] . " Project Group','0','" . $_POST["MaxStudents"] . "');";

			if(!mysqli_query($conn, $createGroupSql)) {
				die("Error creating group: " . mysqli_error($conn));
			}

			$curForumIdQuery = "SELECT forum_id from phpbb_forums where forum_name = '" . $_POST["ProjName"] . "';";
			
			//Get the forum id of the created group's forum.
			$forumRow = mysqli_fetch_assoc(mysqli_query($conn, $curForumIdQuery));

			if($forumRow['forum_id'] == 0)
			{
				die("Forum id is null");
			}
			
			//Set admin group permissions for this forum (allow admins to have full access to this forum).
			$forumAdminPermSql = "INSERT INTO phpbb_acl_groups VALUES('5', '{$forumRow['forum_id']}','0','14','0');";
			if(!mysqli_query($conn, $forumAdminPermSql)) {
				die("Error assigning admin permissions: " . mysqli_error($conn));
			}
			
			//Set forum group permissions for this forum (allow project group to view and post on forum).
			$curGroupIdQuery = "SELECT group_id from phpbb_groups where group_name = '" . $_POST["ProjName"] . " Project Group';";

			//Get the group id of the created group.
			$groupRow = mysqli_fetch_assoc(mysqli_query($conn, $curGroupIdQuery));

			if($groupRow['group_id'] == 0)
			{
				die("Group id is null");
			}
			
			$forumGroupPermSql = "INSERT INTO phpbb_acl_groups VALUES('{$groupRow['group_id']}','{$forumRow['forum_id']}','0','15','0');";

			if(!mysqli_query($conn, $forumGroupPermSql)) {
				die("Error assigning group permissions: " . mysqli_error($conn));
			}

			$auth->acl_clear_prefetch();
		}
		 else
		 {
		 	echo 'Only professor accounts may create projects!';
		 }
	}
?>

</center>


<div id="footer">
  <p>&copy;Project Factory. All Rights Reserved | Greatest Website Ever.</p>
	</div>
	</body>

</html>
