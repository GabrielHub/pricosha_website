<?php
session_start();
//checking if logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

include "functions.php";
$connection = connect();

//for Share inserts
$appendSQL = "";

foreach($_POST["options"] as $index => $option) {
  if ($appendSQL == "") $appendSQL = "AND Stato IN ("; //if it's the first detected option, add the IN clause to the string
  $appendSQL .= $option.",";
}

//trim the trailing comma and add the closing bracket of the IN clause instead
if ($appendSQL != "") 
{
  $appendSQL = rtrim($appendSQL, ","); 
  $appendSQL .= ")";
}

//

//Form handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	echo trim($_POST["item_name"]), trim($_POST["file_path"], $appendSQL);
}

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

	<h1>Posted!</h1>
	<br>
	<p>Made a mistake? Click <a href="post.php"><b>HERE</b></a> to go back</p>
</body>
</html>