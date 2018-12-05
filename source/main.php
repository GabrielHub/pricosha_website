<?php //How to check if logged in
//init session
session_start();

//checking if logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

echo $_SESSION["fname"] . ' ' . $_SESSION["lname"];

// NEXT STEP, ADD TAG button to EACH CONTENT ITEM BELOW
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
		<li><a href="managetag.php">Manage Tags</a></li>
		<li><a href="post.php">Post</a></li>
	</ul>

	<h1>Wall</h1>

	<div class = "columns">
		<p>Features:</p>
		<p>Click on <b>Manage Groups</b> for adding Friends other Group management!</p>
		<p>Click on <b>Post</b> to post something!</p> 
		<p>Click on <b>Manage Tags</b> to remove or accept or view tags!</p><br>
		<br>

		<p> Click on <b>Tag</b> on any post to tag someone! </p> <br>

		<h2>Feed:</h2>
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
				<p><a href='tag.php?id='" . $v['item_id'] . "' class='btn btn-primary'>TAG</a></p>
				</div>";
			}
		}

		$connection->close();
		?>
	</div>

	</div>
	<p>
		<br><br>
		<a href="logout.php" class="button" id="logout_button">Sign Out</a>
	</p>
</body>
</html>