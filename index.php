<html>
	<head>
		<link rel="stylesheet" href="style.css">
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
<body>
	<ul>
		<li><a href= "registration.php">Register</a></li>
		<li><a href="login.php">Login</a></li>
	</ul>

	<div class = "columns">
		<a href="https://github.com/GabrielHub/pricosha_website"><p> A Project By Gabriel Ong </p></a>
		<br>
		<p>You aren't logged in! Here are all the public posts from the last 24 hours</p> <br>

		<h2>Feed:</h2>
		<br>

		<?php
		include "functions.php";
		$connection = connect();

		//display shared content items and info about them
		$posts = getGuestContentItemData($connection); //store results in an array to use

		//if empty
		if (empty($posts)) {
			echo "There are no public posts in the last 24 hours!";
		} 
		else {
			//go through each visible post and display details, also add a tag button
			foreach ($posts as $v) {
				echo "<div class = 'column'><h2>" . $v['item_name'] . "</h2><p>" . $v['file_path'] . " </p> <h3>" . $v['item_id'] . ".  <b>" . $v['email_post'] . "</b> , " . $v['post_time'] . "</h3></div>";
			}
		}

		$connection->close();
		?>
	</div>
</body>
</html>