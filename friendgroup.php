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
		<li><a href="addfriend.php">Add Friends</a></li>
	</ul>

	<h1>Friend Groups</h1>

	<div class = "columns">
		<h2>FriendGroups you Own:</h2>

		<?php
		include("functions.php");
		$connection = connect();

		//Find friendgroups you own and display them as links to their content pages
		$friendgroups = getOwnedFriendGroup($_SESSION["email"], $connection);
		if (empty($friendgroups)) {
			echo "You own no friend groups!";
		}

		//Loop through associative array, and print only the fgnames
		foreach($friendgroups as $v) {
			echo $v['fg_name'], "<br>"; 
		}

		echo "<br><h2>All the friend groups you are in: </h2> <br>";

		//Find friendgroups you are in
		$fgs = getFriendGroup($_SESSION["email"], $connection);

		if (empty($fgs)) {
			echo "You aren't in any friend groups!";
		}

		//Loop through associative array, and print
		foreach($fgs as $v) {
			$description = getDescription($v['owner_email'], $v['fg_name'], $connection);
			echo "<b>" . $v['fg_name'] . "</b>" . ", owned by: " . $v['owner_email'] . "<br><p>DESCRIPTION: " . $description . "</p><br><br>"; 
		}


		$connection->close();
		?>
		<br>

	</div>
	<p>
		<br><br>
		<a href="logout.php" class="button" id="logout_button">Sign Out</a>
	</p>
</body>
</html>