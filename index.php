<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php

define('IN_PHPBB', true);
// Specify the path to you phpBB3 installation directory.
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
// The common.php file is required.
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);

$user->setup();
session_start();
?>

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
		<title>Project Factory - Get Your Project Done With Project Factory</title>
		<link href="css/styles.css" rel="stylesheet" type="text/css" media="screen" />
	</head>
	<body>
			<div id="top">
				<a href="index.php"><img src="../images/ProjectFactory.png" width="670" height="140" alt="Project Factory Logo"/></a>
				<center><h4>This CPSC 4392 Capstone Project was created by Daniel Forero, Jeffery Wooldridge, and Mohammed Almashhed!</h4></center>
			
				<div id="login_status">
					<p><?php 
						echo('Logged in as ' . $user->data['username_clean']);
					?></p>
				</div>
				
			</div>
			<div id="topnav">
				<ul>
				  <li><a href="index.php">HOME</a></li>
				  <li><a href="projects/projects.php">PROJECTS</a></li>
				  <li><a href="create/create.php">CREATE</a></li>
				  <li><a href="..">FORUM</a></li>
				</ul>
			</div>
			<div id="subbanner">
				<h3>Are you looking for a consolidated portal to bring your team together? If so, we have the perfect service for 							you...<br/>
					Welcome to Project Factory! We are here to provide an easy-to-use, online project management portal for you and your 						team!</h3>

			</div>
			<div id="homepage">
				<center>
					<h1>Welcome to Project Factory</h1>
					<img src="../image/homepage.jpeg"/>
				</center>
				
			</div>	
			<div id="footer">
				<p>&copy;Project Factory. All Rights Reserved | Greatest Website Ever.</p>
			</div>
	</body>
</html>

