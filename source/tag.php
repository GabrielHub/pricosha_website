<?php
//init session
session_start();

//checking if logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

echo $_SESSION["fname"] . ' ' . $_SESSION["lname"];

$timestamp = date('Y-m-d G:i:s'); //Timestamp data for mysql
$email_err = "";
$email = "";

//Form handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	//validate email
	if (empty(trim($_POST["email"]))) {
		$email_err = "Please enter an email";
	}
	else {//check if visible for user
		//query gets a row if thetagged email can view the selected content
		$query = "SELECT item_id FROM contentitem WHERE (is_pub = 1 AND item_id = ?)
UNION
	SELECT item_id FROM ContentItem WHERE item_id IN (SELECT item_id FROM share WHERE fg_name IN (SELECT fg_name FROM belong WHERE email = ?) AND item_id = ?)";

		//prepare statement
		if ($statement = $connection->prepare($query)) {
			//bind variables to statement
			$statement->bind_param("isi", $param_item, $param_emailtagged, $param_item);

			//set parameters
			$param_item = $_SESSION['tag_item_id'];
			$param_emailtagged = trim($_POST['email']);

			//attempt execution of prepared statement
			if ($statement->execute()) {

			}
			else {
				echo "Could not check if content item is visible to the person you want to tag.";
			}
		}
		$statement->close();
	}
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
		<li><a href= "main.php">Homepage</a></li>
	</ul>
	<h1>Who would you like to tag? (Enter their email)</h1>
	<br>
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		<div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
		    <label>Email</label>
		    <input type="text" name="email" value="<?php echo $email; ?>">
		    <span class="help-block"><?php echo $email_err; ?></span>
		</div>    
    </form>
</body>
</html>