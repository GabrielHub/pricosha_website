<?php
//init session
session_start();

//checking if logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

echo $_SESSION["fname"] . ' ' . $_SESSION["lname"];

include "functions.php";
$connection = connect();

$email_err = "";
$email = "";
echo $_SESSION['tag_item_id'];

//Form handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	//validate email
	if (empty(trim($_POST["email"]))) {
		$email_err = "Please enter an email";
	}
	else {//check if visible for user
		//query gets a row if thetagged email can view the selected content
		$query = "SELECT item_id FROM contentitem WHERE (is_pub = 1 AND item_id = ?) UNION SELECT item_id FROM contentitem WHERE item_id IN (SELECT item_id FROM share WHERE item_id = ? AND fg_name IN (SELECT fg_name FROM belong WHERE email = ?))";

		//prepare statement
		if ($statement = $connection->prepare($query)) {
			//bind variables to statement
			$err = $statement->bind_param("iis", $param_item, $param_item2, $param_emailtagged);

			if (false === $err) {
				die('bind_param() failed: ' . htmlspecialchars($statement->error));
			}
			

			//set parameters
			$param_item = $_SESSION['tag_item_id'];
			$param_item2 = $_SESSION['tag_item_id'];
			$param_emailtagged = trim($_POST['email']);

			//attempt execution of prepared statement
			if ($statement->execute()) {
				$err = $statement->store_result();

				if (false === $err) {
					die('store_result() failed: ' . htmlspecialchars($statement->error));
				}

				//error check for empty set
				if ($statement->num_rows == 1) {
					$email = trim($_POST['email']); //set email variable to tagged email
				}
				else {
					$email_err = "Either email not found, or this person does not have access to this post.";
				}
			}
			else {
				$email_err = "Could not check if content item is visible to the person you want to tag.";
			}
		}
		$statement->close();
	}

	//check input errors before db insertion
	if (empty($email_err)) {
		//prepare insert statement
		if ($statement = $connection->prepare("INSERT INTO tag VALUES (?, ?, ?, ?, ?)")) {
			//bind param variables to statement
			$statement->bind_param("ssiss", $param_tagged, $param_tagger, $param_id, $param_status, $param_time);

			//Set parameters
			$param_tagger = $_SESSION['email'];
			$param_id = $_SESSION['tag_item_id'];
			$param_time = date('Y-m-d G:i:s'); //Timestamp data for mysql

			//check if you tagged yourself
			if ($email == $_SESSION['email']) {
				//if tagging yourself, use your own email and set status to true
				$param_tagged = $_SESSION['email'];
				$param_status = "true";
			}
			else {
				//if tagging someone else
				$param_tagged = $email;
				$param_status = "false";
			}

			//attemp exeuction
			if ($statement->execute()) {
				//return statement!
				echo "<h2>Successfully tagged! Click on <b>Homepage</b> to return!</h2>";
			}
			else {
				echo "<h2>Could not tag , error executing SQL</h2>";
			}
		}
		$statement->close();
	}
	$connection->close();
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
		    <label>Email</label><br>
		    <input type="text" name="email" value="<?php echo $email; ?>">
		    <span class="help-block"><?php echo $email_err; ?></span>
		</div>  
		<div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
        </div>  
    </form>
</body>
</html>