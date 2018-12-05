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
		<h2>Pending Tags: </h2>
		<p>Decline, Accept or ignore the pending tag.</p>
		<br>

		<?php
		include "functions.php";
		$connection = connect();

		//get an array of pending tags
		$pending = getPendingTags($_SESSION['email'], $connection); 

		//if empty
		if (empty($pending)) {
			echo "No pending tags!";
		} 
		else {
			//display each pending tag
			foreach ($pending as $v) {
				$tagger_name = getName($v['email_tagger'], $connection);
				echo "<div class = 'column'><h2>" . $tagger_name . " has tagged you</h2><p>in item " . $v['item_id'] . " </p> <h3>" . $v['tagtime'] . "</h3> <p><a href='tagacceptdecline.php?id=" . urlencode($v['item_id']) . "&email_tagger=" . urlencode($v['email_tagger']) . "&response=" . urlencode("accept") . "'class='btn btn-primary'>Accept</a>  <a href='tagacceptdecline.php?id=" . urlencode($v['item_id']) . "&email_tagger=" . urlencode($v['email_tagger']) . "&response=" . urlencode("decline") . "'class='btn btn-primary'>Decline</a></p></div>";
			}
		}

		$connection->close();
		?>
	</div>

	</div>
</body>
</html>