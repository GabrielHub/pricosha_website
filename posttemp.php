<?php
session_start();
//checking if logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

include "functions.php";
$connection = connect();

$groups = array(); //keep track of which groups were checked
$ispublic = 0; //default public to false
$item_name = ""; //these are empty if there are no item name/file path posted
$file_path = "";
$timestamp = date('Y-m-d G:i:s'); //Timestamp data for mysql

//ERROR HANDLING v

//check groups
if (isset($_POST['options']) && is_array($_POST['options'])) {
	$groups = $_POST['options']; //if groups were selected, else groups is an empty array
}
else {
	echo "error with checkboxes";
}
//check if public
if (isset($_POST['public'])) {
	$ispublic = 1;
}

//check for empty file path and item name
if (isset($_POST['item_name'])) {
	$item_name = $_POST['item_name'];
}
if (isset($_POST['file_path'])) {
	$file_path = $_POST['file_path'];
}

//make sure groups are selected if not public
if (!isset($_POST['options']) && !isset($_POST['public'])) {
	$return = "You need to select groups if you want to make a private post.";
}
else {
	//Form handling
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		//function to add
		$return = postContentItem($_SESSION['email'], $timestamp, $file_path, $item_name, $ispublic, $connection, $groups);
	}
}




$connection->close();
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

	<<?php echo "<h1>" . $return . "</h1>"; ?>
	<br>
	<p>Made a mistake? Click <a href="post.php"><b>HERE</b></a> to go back</p>
</body>
</html>