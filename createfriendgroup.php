<?php
//init session
session_start();

//checking if logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

//create connection
include "functions.php";
$connection = connect();

//define and init variables
$fgname = $description = "";
$fgname_err = $description_err = "";
$email = $_SESSION["email"];

//Display account name at top of page
echo $_SESSION["fname"] . ' ' . $_SESSION["lname"];

//Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	//check if name is empty
	if (empty(trim($_POST["fgname"]))) {
		$fgname_err = "Please enter a unique friend group name.";
	}
	else if (strlen(trim($_POST["fgname"])) > 20) {
        $fgname_err = "Name must be less than 20 characters!";
    }
	else {
		//validate unique name
		$query = "SELECT owner_email, fg_name FROM friendgroup WHERE owner_email=? AND fg_name=?";

		if ($statement = $connection->prepare($query)) {
			//bind var to prep statement
			$statement->bind_param("ss", $param_oemail, $param_fgname);

			//set parameters
			$param_oemail = $email;
			$param_fgname = trim($_POST["fgname"]);

			//execute
			if ($statement->execute()) {
				//store result
				$statement->store_result();

				if ($statement->num_rows == 1) {
					$fgname_err = "You already have a friendgroup with this name!";
				}
				else {
					$fgname = trim($_POST["fgname"]); //Set fg name
				}
			}
			else {
				echo "Something went wrong! ERROR";
			}
		}
		$statement->close();
	}

	//clean description
	if (empty(trim($_POST["description"]))) {
		$description_err = "Please enter a description!";
	}
	else if (strlen(trim($_POST["description"])) > 1000) {
        $description_err = "Description must be less than 1000 characters!";
    }
	else {
		$description = filterString($_POST["description"]); //set description, filter string is custom function
	}

	//Check errors before insertion
	if (empty($fgname_err) && empty($description_err)) {
		$query = "INSERT INTO friendgroup VALUES (?, ?, ?)";

		if ($statement = $connection->prepare($query)) {
			//Bind var to prepared statement
			$statement->bind_param("sss", $param_email, $param_fgname, $param_description);

			//Set param
			$param_email = $email;
			$param_fgname = $fgname;
			$param_description = $description;

			//Execute prep statement
			if ($statement->execute()) {
				//Update BelongTo table with owner
				$err_result = addToFriendGroup($email, $email, $fgname, $connection);

				//return to group page page
				header("location: friendgroup.php"); 
				//might change it to individual group page later
			}
			else {
				echo "Something went wrong, please try again later.";
			}
		}
		$statement->close();
	}
	$connection->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <h1>Create A New Friendgroup!</h1>
    <link rel="stylesheet" href="style.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
	<div class = "wrapper">
        <p>Choose a unique name for your friend group and write a description!</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($fgname_err)) ? 'has-error' : ''; ?>">
                <label>Friend Group Name</label>
                <input type="txt" name="fgname" class="form-control" value="<?php echo $fgname; ?>">
                <span class="help-block"><?php echo $fgname_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($description_err)) ? 'has-error' : ''; ?>">
                <label>Description</label><br>
                <input type="textbox" name="description" class="form-control" value="<?php echo $description; ?>">
                <span class="help-block"><?php echo $description_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Changed your mind? <a href="friendgroup.php">Return to your <b>Groups</b> here</a>.</p>
        </form>   
    </div>
</body>
</html>