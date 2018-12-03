<?php
/*
	Tutorial used to create registration and login page from https://www.tutorialrepublic.com/php-tutorial/php-mysql-login-system.php
*/
//Registration

	//Connection
include("functions.php");
$connection = connect(); //Start Connection

//define and init var
$email = $password = $confirm_password = "";
$email_err = $password_err = $confirm_password_err = "";
$fname = $lname = "";
$fname_err = $lname_err = "";

//Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	//validate Email
	if (empty(trim($_POST["email"]))) {
		$email_err = "Please enter an email";
	}
	else {
		//Prepare Select email
		//$query = "SELECT email, password FROM person WHERE email='$email' and password='$pass'";
		$query = "SELECT email FROM person WHERE email = ?";

		if ($statement = $connection->prepare($query)) {
			//bind variables to prepared statement as parameters
			$statement->bind_param("s", $param_email);

			//Set parameters
			$param_email = trim($_POST["email"]);

			//Attempt execution of prepared statement
			if ($statement->execute()) {
				//Store result
				$statement->store_result();

				if ($statement->num_rows == 1) {
					$email_err = "This email already has an account!";
				}
				else {
					$email = trim($_POST["email"]);
				}
			}
			else {
				echo "Something went wrong, please try again later.";
			}
		}

		$statement->close();
	}

	//validate password
	if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";     
    } 
    else if (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } 
    else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";     
    } 
    else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    //validate first name
    if (empty(trim($_POST["fname"]))) {
        $fname_err = "Please enter a first name.";     
    } 
    else {
        $fname = trim($_POST["fname"]);
    }

    //validate first name
    if (empty(trim($_POST["lname"]))) {
        $lname_err = "Please enter a last name.";     
    } 
    else {
        $lname = trim($_POST["lname"]);
    }

    //Check input errors before db insertion
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($fname_err) && empty($lname_err)) {
    	//prepare insert statement
    	$query = "INSERT INTO Person VALUES (?, ?, ?, ?)";

    	if ($statement = $connection->prepare($query)) {
    		//bind variables to the prepared statement as parameters
    		$statement->bind_param("ssss", $param_email, $param_password, $param_fname, $param_lname);

    		//Set Parameters
    		$param_email = $email;
    		$param_password = password_hash($password, PASSWORD_DEFAULT); //create hash
    		$param_fname = $fname;
    		$param_lname = $lname;

    		//Attempt execution of prepared statement
    		if ($statement->execute()) {
    			// Redirect to login page
    			header("location: login.php");
    		}
    		else {
    			echo "Something went wrong. Please try again later.";
    		}
    	}
    	//Close statement
    	$statement->close();
    }
    $connection->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <h1>PriCoSha by Gabriel Ong</h1>
    <link rel="stylesheet" href="style.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>Sign Up</h1>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($fname_err)) ? 'has-error' : ''; ?>">
                <label>First Name</label>
                <input type="fname" name="fname" class="form-control" value="<?php echo $fname; ?>">
                <span class="help-block"><?php echo $fname_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($lname_err)) ? 'has-error' : ''; ?>">
                <label>Last Name</label>
                <input type="lname" name="lname" class="form-control" value="<?php echo $lname; ?>">
                <span class="help-block"><?php echo $lname_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>    
</body>
</html>