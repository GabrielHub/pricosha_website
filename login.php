<?php
//init session
session_start();

include "functions.php";
$connection = connect();

$email = $password = "";
$email_err = $password_err = "";

//Form Data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	//If email is empty
	if (empty(trim($_POST["email"]))) {
		$email_err = "Please enter email.";
	}
	else {
		$email = trim($_POST["email"]);
	}

	// Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } 
    else {
        $password = trim($_POST["password"]);
    }

    //credential validation
    if(empty($username_err) && empty($password_err)) {
    	//prepare select statement, check for email before looking for password
    	$query = "SELECT email, password, fname, lname FROM person WHERE email=?";

    	if ($statement = $connection->prepare($query)) {
    		//bind variables to prepared statement as param
    		$statement->bind_param("s", $param_email);

    		//set email
    		$param_email = $email;

    		//execute prep statement
    		if ($statement->execute()) {
    			//store result
    			$statement->store_result();

    			//if email exists, check password
    			if ($statement->num_rows == 1) {
    				//bind result var
    				$statement->bind_result($email, $hashed_password, $fname, $lname);

    				if ($statement->fetch()) {
    					//if password is correct
    					if (password_verify($password, $hashed_password)) {
    						//start new session
    						session_start();

    						//store data as session var
    						$_SESSION["loggedin"] = true;
                        	$_SESSION["email"] = $email;
                        	$_SESSION["fname"] = $fname;
                        	$_SESSION["lname"] = $lname;

                        	//redirect user to main page
                        	header("location: main.php");
    					}
    					else {
    						//password was incorrect
    						$password_err = "Incorrect password";
    					}
    				}
    			}
    			else {
    				//error message for invalid email
    				$email_err = "No account found with that email";
    			}
    		}
    		else {
    			echo "Something went wrong. Please try again";
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
		<title>Login</title>
		<style>
			body {
			    font-family: Arial, Helvetica, sans-serif;
			}
		</style>
	</head>
	<body>

		<h1>Login</h1>
		<p>Project for Intro to Databases: Login Page</p>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" style="width: 20%" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href="registration.php">Sign up now</a>.</p>
            <p>Want to remain a guest? <a href="index.php">Back to homepage</a>.</p>
        </form>
	</body>
</html>