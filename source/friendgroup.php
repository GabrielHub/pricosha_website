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
		<li><a href="createfriendgroup.php">Create New Group</a></li>
		<li><a href="friendgroupfunctions.php">View Group Posts</a></li>
	</ul>

	<h1>Friend Groups</h1>

	<div class = "columns">
		<p>FriendGroups you Own:</p>
		<?php
		include("functions.php");
		$connection = connect();
		//Find friendgroups you own and display them as links to their content pages
		$friendgroups = getOwnedFriendGroup($_SESSION["email"], $connection);
		if ($friendgroups == 0) {
			echo "You own no friendgroups!";
		}
		for ($i = 0; $i < count($friendgroups); $i++) {
			echo $friendgroups[$i];
			echo "<br>";
		}

		$connection->close();
		?>
	</div>
	<p>
		<br><br>
		<a href="logout.php" class="button" id="logout_button">Sign Out</a>
	</p>
</body>
</html>