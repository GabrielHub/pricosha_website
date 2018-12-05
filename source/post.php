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
	</ul>
	<h1>Select Post Options</h1>
	<br>
	<form action="posttemp.php" method = "post">
		<?php
		include "functions.php";
		$connection = connect();
		$email = $_SESSION['email'];

		//session info for arrays
		$fgs = getFriendGroup($email, $connection);
		$_SESSION['fgs_array'] = $fgs;
		
		if (empty($fgs)) {
			echo "You aren't in any groups!";
		}
		else {
			//loop through array, and create checkboxes
			echo "<h2><label>Groups: </label></h2><br> <div class='columns'>";
			$i = 0;
			foreach($fgs as $v) {
				echo "<input type='checkbox' id='checkbox" . $i . "' name='options[]' value='" . $v['fg_name'] . "' checked='checked'><label for='checkbox" . $i . "'>" . $v['fg_name'] . "</label>";
				$i++;
			}
			echo "</div>";
		}

		$connection->close();
		?>
		<div>
                <label>Title of Post</label><br>
                <input type="text" name="item_name">
        </div>
        <div>
                <label>Text</label><br>
                <input type="text" name="file_path">
        </div>
        <!--<input type = 'checkbox' id='checkbox' name = 'public' value = 'public' checked='checked'><label for='public'>Public</label>-->

        <label class="container">Public Post
        	<input type="checkbox" id='checkbox' name = 'public' value = 'public' checked="checked">
 			<span class="checkmark"></span>
        </label>
        
		<input type="submit" value="Submit" />
	</form>

	</div>
	<p>
		<br><br>
		<a href="logout.php" class="button" id="logout_button">Sign Out</a>
	</p>
</body>
</html>