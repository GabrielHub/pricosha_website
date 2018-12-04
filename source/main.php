<?php //How to check if logged in
//init session
session_start();

//checking if logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

/* for getting content items

SELECT item_id, email_post, post_time, item_name, file_path FROM ContentItem WHERE is_pub = 1 
UNION
SELECT item_id, email_post, post_time, item_name, file_path FROM ContentItem WHERE item_id IN (SELECT item_id FROM Share WHERE fg_name IN (SELECT fg_name FROM Belong WHERE email = %s))ORDER BY post_time DESC
*/

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
		<li><a href="post.php">Post</a></li>

	</ul>

	<h1>MySpace 2.0, but like Twitter</h1>

	<div class = "columns">
		<p>Features:</p>
		<p>Click on <b>Manage Groups</b> for adding Friends and Group management!</p>
		<p>Click on <b>Post</b> to post something!</p> <br>
		<h2>Feed:</h2>
		<br>

		<?php
		include "functions.php";
		$connection = connect();

		//display shared content items and info about them
		$posts = getContentItemData($_SESSION['email'], $connection); //store results in an array to use

		if (empty($posts)) {
			echo "No posts yet!";
		}
		else {
			foreach ($posts as $v) {
				echo "<div class = 'column'><h2>" . $v['item_name'] . "</h2><p>" . $v['file_path'] . " </p> <h3>" . $v['item_id'] . ".  <b>" . $v['email_post'] . "</b> , " . $v['post_time'] . "</h3></div>";
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