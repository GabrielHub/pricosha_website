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

	<h1>MySpace 2.0</h1>

	<div class = "columns">
		<p>Features:</p>
		<p>Click on <b>Manage Groups</b> for adding Friends and Group management!</p>
		<p>Click on <b>Post</b> to post something!</p> <br>
		<label>Recent Posts (last 24 hours):</label>
		<br>

		<?php
		//View shared content items and info about them


		?>
	</div>

	</div>
	<p>
		<br><br>
		<a href="logout.php" class="button" id="logout_button">Sign Out</a>
	</p>
</body>
</html>