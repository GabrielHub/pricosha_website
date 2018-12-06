<?php
//php page that is linked from addfriend.php, gets fgname and provides a form and then list of friends you can add <- might be feature for later
//init session
session_start();

//checking if logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
//make sure a fg has been properly selected
if (!isset($_SESSION['curr_fgname'])) {
	echo "ERROR: could not retrieve chosen friend group, please try again";
}

echo $_SESSION["fname"] . ' ' . $_SESSION["lname"];

//Get variables from previous page
$fgname = $_SESSION['curr_fgname'];
//echo $fgname;
$owner_email = $_SESSION["email"];

//connect to server
include "functions.php";
$connection = connect();

$fname = $lname = "";
$fname_err = $lname_err = "";
$email = ""; //var to store future email of person to be added

//Form Data
if ($_SERVER["REQUEST_METHOD"] == "POST") {

	//if first name if empty
	if (empty(trim($_POST["fname"]))) {
			$fname_err = "Please enter a first name.";
		}
		else {
			$fname = trim($_POST["fname"]);
		}

	// Check if lname is empty
    if (empty(trim($_POST["lname"]))) {
        $lname_err = "Please enter a last name.";
    } 
    else {
        $lname = trim($_POST["lname"]);
    }


    //validate that person exists and isn't already in group, then store the email result for insertion
    if (empty($fname_err) && empty($lname_err)) {
    	$query = "SELECT email FROM person WHERE (fname = ? AND lname = ?) AND person.email NOT IN (SELECT email FROM belong WHERE fg_name = ?)";

    	//prepare statement to check if person exists and also isn't already in the group
    	if ($statement = $connection->prepare($query)) {
    		//bind var
    		$statement->bind_param("sss", $param_fname, $param_lname, $param_fgname);

    		//set parameters
    		$param_fname = $fname;
    		$param_lname = $lname;
    		$param_fgname = $fgname;



    		//attempt execution
    		if ($statement->execute()) {
    			//store result
    			$statement->store_result();
    			$statement->bind_result($email);
    			$statement->fetch();
    			
    			//if such a person exists
    			if ($statement->num_rows == 1) {
    				//bind result var
    				
    				//Now call function that adds someone to belong
					$err_result = addToFriendGroup($email, $owner_email, $fgname, $connection);
					$_SESSION['last_err'] = $err_result;
					header("location: addfriend2.php");

    			}
    			else {
    				//Person not found
    				$fname_err = "Either the person does not exist or is already in this Group";
    			}
    		}
    		else {
    			echo "Could not query for the submitted person";
    		}
    	}
    	$statement->close();

    }
    $connection->close();
}
?>

<!DOCTYPE html>
<html lang = "en">
<meta name = "viewport" content = "width=device-width, initial-scale=1.0, user-scalable=no">
	<head>
		<link rel="stylesheet" href="style.css">
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Add Friend (FORM)</title>
		<style>
			body {
			    font-family: Arial, Helvetica, sans-serif;
			}
		</style>
	</head>
	<body>

		<h1>Add Friend</h1>
		<p>Enter Friend's first and last name</p>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($fname_err)) ? 'has-error' : ''; ?>">
                <label>First Name</label><br>
                <input type="text" name="fname" class="form-control" value="<?php echo $fname; ?>">
                <span class="help-block"><?php echo $fname_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($lname_err)) ? 'has-error' : ''; ?>">
                <label>Last Name</label><br>
                <input type="text" name="lname" class="form-control">
                <span class="help-block"><?php echo $lname_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" style="width:22%" class="btn btn-primary" value="Submit">
            </div>
            <p>Return to home page <a href="addfriend.php">Back</a>.</p>
        </form>
	</body>
</html>