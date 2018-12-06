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
		<li style = "float:right"><a href="logout.php" class='btn btn-primary'>Sign Out</a></li>
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

	echo "<br><h1>Users:</h1><p>List of users</p><br>";

	//get an array of all users except yourself
	$users = getAllUsers($_SESSION['email'], $connection);

	echo "
	<table>
	<tr>
	<th>Email</th>
	<th>Name</th>
	</tr>";

	foreach ($users as $v) {
	echo "<tr>";
	echo "<td>" . $v['email'] . "</td>";
	echo "<td>" . $v['fname'] . " " . $v['lname'] . "</td>";
	echo "</tr>";
	}
	echo "</table>";


	$connection->close();
	?> 
	
</body>
</html>