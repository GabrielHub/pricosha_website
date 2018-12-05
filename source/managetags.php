<?php 
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
	</ul>

	<h1>Manage Tags</h1>

	<div class = "columns">
		<h2>Pending Tags</h2>
		<p>Someone has tagged you in a post!</p>
		<br>

		<?php
		include "functions.php";
		$connection = connect();

		//display shared content items and info about them
		$posts = getContentItemData($_SESSION['email'], $connection); //store results in an array to use

		//if empty
		if (empty($posts)) {
			echo "No posts yet!";
		} 
		else {
			//go through each visible post and display details, also add a tag button
			foreach ($posts as $v) {
				echo "<div class = 'column'><h2>" . $v['item_name'] . "</h2><p>" . $v['file_path'] . " </p> <h3>" . $v['item_id'] . ".  <b>" . $v['email_post'] . "</b> , " . $v['post_time'] . "</h3>
				</div>";
			}
		}

		$connection->close();
		?>
	</div>

	</div>
</body>
</html>