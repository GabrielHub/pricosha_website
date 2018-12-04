<?php //How to check if logged in
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
		<li><a href= "main.php">Homepage</a></li>
		<li><a href="friendgroup.php">Manage Groups</a></li>
	</ul>
	<h1>Select Post Options</h1>
	<div id = "checkboxes">
		<input type = "checkbox" id = "public" name = "public" value = public checked="checked"><label for="public">Public</label>
	</div>
	<br>
	<form action="posttemp.php" method = "post">
		<?php
		include "functions.php";
		$connection = connect();
		$email = $_SESSION['email'];
		$fgs = getFriendGroup($email, $connection);
		$_SESSION['fgs_array'] = $fgs;
		

		if (empty($fgs)) {
			echo "You aren't in any groups!";
		}
		else {
			//loop through array, and create checkboxes
			echo "Groups: ";
			$i = 0;
			foreach($fgs as $v) {
				echo "<input type='checkbox' id='checkbox" . $i . "' name='options[]' value '" . $i . "' checked='checked'><label for='checkbox" . $i . "'>" . $v['fg_name'] . "</label>";
				$i++;
			}
		}

		$connection->close();
		?>
		<div class="form-group">
                <label>Item Name</label><br>
                <input type="text" name="item_name" class="form-control">
        </div>
        <div class="form-group">
                <label>File Path</label><br>
                <input type="text" name="file_path" class="form-control">
        </div>
		<input type="submit" name="option" value="Submit" />
	</form>

	</div>
	<p>
		<br><br>
		<a href="logout.php" class="button" id="logout_button">Sign Out</a>
	</p>
</body>
</html>