<?php //How to check if logged in
//init session
session_start();

//checking if logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>
<html>
	<head>
		<link rel="stylesheet" href="style.css">
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
<body>
	<h1>MySpace 2.0</h1>
	<!--<p>Current features: Press the generate button to create accounts.</p>
	<p>Press delete to delete these accounts.</p>
	<button onclick = "window.location.href='insert.php'">Insert Initial Testing Accounts</button>
	<button onclick = "window.location.href='delete.php'">Delete Testing Accounts</button>-->
	<p>
        <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
    </p>
</body>
</html>