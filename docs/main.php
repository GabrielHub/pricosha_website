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
	<h1>MySpace 2.0</h1>
	<p>Current features:</p>
	<p>FriendGroups you Own:</p>
	<?php
	include("functions.php");
	$connection = connect();
	//Find friendgroups you own and display them as links to their content pages
	$friendgroups = getFriendGroup($_SESSION["email"], $connection);
	if ($friendgroups == 0) {
		echo "You own no friendgroups!";
	}
	for ($i = 0; $i < count($friendgroups); $i++) {
		echo $friendgroups[$i];
		echo "<br>";
	}

	$connection->close();
	?>

	<!--<p>Current features: Press the generate button to create accounts.</p>
	<p>Press delete to delete these accounts.</p>
	<button onclick = "window.location.href='insert.php'">Insert Initial Testing Accounts</button>
	<button onclick = "window.location.href='delete.php'">Delete Testing Accounts</button>-->
	<p>
        <a href="createfriendgroup.php" class="btn btn-danger">Create New Friendgroup</a>
    </p>
	<p>
        <a href="logout.php" class="btn btn-danger">Sign Out</a>
    </p>
</body>
</html>