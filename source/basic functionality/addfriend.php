<?php //How to check if logged in
//init session
session_start();

//checking if logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

echo $_SESSION["fname"] . ' ' . $_SESSION["lname"];
?>

<html>
	<head>
		<link rel="stylesheet" href="style.css">
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
<body>
	<ul>
		<li><a class="active" href= "main.php">Homepage</a></li>
		<li><a href="friendgroup.php">Manage Groups</a></li>
		<li><a href="createfriendgroup.php">Create New Group</a></li>
	</ul>

	<h1>Add Friends</h1>

	<?php
	//start connection
	include("functions.php");
	$connection = connect();

	//set email
	$owner_email = $_SESSION["email"];

	//Prepare statement to check if there are no friendgroups
	if (empty(getOwnedFriendGroup($owner_email, $connection))) {
		echo "<p> You have no friendgroups </p>";
	}
	else {
		createFriendGroupTable($owner_email, $connection);
	}

	$connection->close();
	?> 

	<p>
		<br><br>
		<a href="logout.php" class="button" id="logout_button">Sign Out</a>
	</p>
</body>
</html>