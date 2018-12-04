<?php
function connect() {
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbName = "pricosha";

	//create connection
	$connection = new mysqli($servername, $username, $password, $dbName);

	//Check connection
	if ($connection->connect_error) {
		die("Connection failed: " . $connection->connect_error);
	}
	//echo "Connected Success";
	return $connection;
}

function filterString($str) {
	//sanitize string
	$str = filter_var(trim($str), FILTER_SANITIZE_STRING);
	if (!empty($str)) {
		return $str;
	}
	else {
		return false;
	}
}

//For getting all friendgroups
function getFriendGroup($email, $connection){
	$result = array();
	$fgnames; //Var to store temp results
	$oemails;

	//prepare statement
	if ($statement = $connection->prepare("SELECT owner_email, fg_name FROM belong WHERE email = ?")) {
		$statement->bind_param("s", $param_email);

		$param_email = $email;

		if ($statement->execute()) {
			$statement->store_result();
			$statement->bind_result($oemails, $fgnames);
			while ($statement->fetch()) {
				$result[] = array(
					"owner_email" => $oemails,
					"fg_name" => $fgnames,
				);
			}
		}
		else {
			echo "Couldn't fetch friendgroups";
		}
	}
	$statement->close();
	//Returns empty if there are no friend groups, else returns an array with names of friendgroups
	return $result;
}

//For getting friendgroups you own
function getOwnedFriendGroup($email, $connection) {
	$array = array();
	$emails; //Store variable
	//Prepare statement
	if ($statement = $connection->prepare("SELECT fg_name, description FROM friendgroup WHERE owner_email = ?")) {
		$statement->bind_param("s", $param_email);

		$param_email = $email;

		if ($statement->execute()) {
			$statement->store_result();
			$statement->bind_result($fgnames, $descriptions);
			while ($statement->fetch()) {
				$array[] = array(
					"fg_name" => $fgnames,
					"description" => $descriptions
				);
				//echo $fgnames, $descriptions;
			}
		}
		else {
			echo "Couldn't fetch friendgroups";
		}
	}
	$statement->close();

	//Returns empty if there are no friend groups, else returns an array with names of friendgroups
	return $array;
}

//function to post contentitems
function postContentItem($email, $post_time, $file_path, $item_name, $is_pub, $connection, $groups) {
	//prepare insert statement, for content item
	$query = "INSERT INTO contentitem (email_post, post_time, file_path, item_name, is_pub) VALUES (?, ?, ?, ?, ?)";

	if ($statement = $connection->prepare($query)) {
		//bind variables to the prepared statement as parameters
		$statement->bind_param("ssssi", $param_email, $param_time, $param_file, $param_item, $param_pub);

		//set parameters
		$param_email = $email;
		$param_time = $post_time;
		$param_file = $file_path;
		$param_item = $item_name;
		$param_pub = $is_pub;

		//attempt execution
		if ($statement->execute()) {
			if ($is_pub == 0) {
				$id = $statement->insert_id;
				share($email, $groups, $id, $connection);
			}
			return "POSTED:  $email $post_time $file_path $item_name $is_pub";
		}
		else {
			return "Failed to post items";
		}
	}
	$statement->close();
}

//function to share to friendgroups if public is 0
function share($owner_email, $groups, $id, $connection) {
	$query = "INSERT INTO share VALUES (?, ?, ?)";

	//prepare insert statement
	if ($statement = $connection->prepare($query)) {
		//bind variables to the prepared statement as param
		$statement->bind_param("ssi", $param_email, $param_fg, $param_id);

		//set parameters
		$param_email = $owner_email;
		$param_id = $id;

		//loop over every friendgroup they have selected
		foreach($groups as $v) {
			//bind fgname
			$param_fg = $v;

			//attempt execution
			if ($statement->execute()) {
				echo "Shared to  $v, ";
			}
			else {
				echo "Failed to share to  $v, ";
			}
		}
	}
	$statement->close();
}

//Adds a person to belong
function addToFriendGroup($email, $owner_email, $fgname, $connection) {
	$email_err = ""; //If person is already in the group
	$fgname_err = ""; //If you don't have a group with the name 
	$email_result = "";

	//Prepare statement to check for duplicate member in the group
	if ($statement = $connection->prepare("SELECT email FROM belong WHERE owner_email = ? AND fg_name = ?")) {
		//bind var to prep statement
		$statement->bind_param("ss", $param_oemail, $param_fgname);

		//set parameters
		$param_oemail = $owner_email;
		$param_fgname = $fgname;

		//execute
		if ($statement->execute()) {
			//store result
			$statement->store_result();
			$statement->bind_result($email_result);
    		$statement->fetch();

			if ($statement->num_rows == 1 && $email_result == $email) {
				$email_err = "This member is already in the group";
			}
		}
	}
	else {
		$statement->close();
		return "Could not check for duplicate member.";
	}
	$statement->close();

	//prepare statement to check that you actually have a friendgroup with this name
	if ($statement = $connection->prepare("SELECT fg_name FROM friendgroup WHERE owner_email = ? AND fg_name = ?")) {
		//bind var to prep statement
		$statement->bind_param("ss", $param_oemail, $param_fgname);

		//set parameters
		$param_oemail = $owner_email;
		$param_fgname = $fgname;

		//execute
		if ($statement->execute()) {
			//store result
			$statement->store_result();

			if ($statement->num_rows == 0) {
				$fgname_err = "You don't own a friend group named: $fgname , $owner_email";
			}
		}
		else {
			return "Could not execute fg existence check";
		}
	}
	else {
		return "Could not check if friendgroup exists.";
	}
	$statement->close();

	//check for errors
	if (empty($email_err) && empty($fgname_err)) {
		//prepare statement for inserting to Belong
		if ($statement = $connection->prepare("INSERT INTO belong VALUES (?, ?, ?)")) {
			//bind variables
			$statement->bind_param("sss", $param_email, $param_oemail, $param_fgname);

			//Set Parameters
			$param_email = $email;
			$param_oemail = $owner_email;
			$param_fgname = $fgname;

			//Execute
			if ($statement->execute()) {
				return "";
			}
			else {
				return "Could not insert into belong";
			}
		}
		$statement->close();
	}
	else {
		return "$email_err | $fgname_err"; //output errors if they occur
	}
}

function createFriendGroupTable($owner_email, $connection) {
	//get associative array of fgnames and descriptions from groups you own
	$result = getOwnedFriendGroup($owner_email, $connection);

	echo "
	<table>
	<tr>
	<th>Name</th>
	<th>Description</th>
	<th>Add Friend</th>
	</tr>";

	foreach ($result as $v) {
	echo "<tr>";
	echo "<td>" . $v['fg_name'] . "</td>";
	echo "<td>" . $v['description'] . "</td>";
	echo "<td><a href=\"addfriendtempsession.php?fgname=" . $v['fg_name'] . "\" class=\"btn btn-primary\">ADD</a></td>";
	/*echo "<td><form enctype=\"multipart/formdata\" class= \"btn btn-primary \" action=\"addfriend1.php\" method=\"post\">
	<input type=\"hidden\" name=\"fgname\"
	value=\"" . $v['fg_name'] . "\">
	<input type=\"submit\" name=\"whatever\" value=\"ADD\" id=\"hyperlink-style-button\"/>
	</form></td>
	";*/
	echo "</tr>";
	}
	echo "</table>";
}

//retrieve all content items with permission to view and store in an associative array
function getContentItemData($email, $connection) {
	//variables to bind to
	$item_id;
	$email_post;
	$post_time;
	$item_name;
	$file_path;

	//Result array
	$result = array();

	//query gets all public and friend group posts, and orders them by time posted
	$query = "SELECT item_id, email_post, post_time, item_name, file_path FROM contentitem WHERE is_pub = 1 
UNION
SELECT item_id, email_post, post_time, item_name, file_path FROM ContentItem WHERE item_id IN (SELECT item_id FROM share WHERE fg_name IN (SELECT fg_name FROM belong WHERE email = ?)) ORDER BY post_time DESC";

	//prepare select statement, and retrieve all info about content. Make sure you belong to these fgs
	if ($statement = $connection->prepare($query)) {
		//bind variables to prepared state
		$statement->bind_param("s", $param_email);

		//set Email
		$param_email = $email;

		//attempt an execution :)
		if ($statement->execute()) {
			//store result
			$statement->store_result();

			//bind results
			$statement->bind_result($item_id, $email_post, $post_time, $item_name, $file_path);

			//add results to an array
			while ($statement->fetch()) {
			$result[] = array(
				"item_id" => $item_id,
				"email_post" => $email_post,
				"post_time" => $post_time,
				"item_name" => $item_name,
				"file_path" => $file_path
				);
			}	
		}
		else {
			echo "Could not fetch content items.";
		}
	}
	$statement->close();

	return $result;
}

/* old test code, learning basic prepared statements and insertions
function db_insert() {
	$connection = connect();
	//Query and check if these have been initialized already
	$query = "SELECT email FROM person WHERE email='AA@nyu.edu'";
	$result = $connection->query($query);
	if ($result->num_rows > 0) {
		$connection->close();
		return false;
	}
	else {
		$statement = $connection->prepare("INSERT INTO Person VALUES (?, ?, ?, ?)");
		$statement->bind_param("ssss", $email, $password, $fname, $lname);

		//test insertions
		$email = "AA@nyu.edu";
		$password = "password";
		$fname = "Albert";
		$lname = "Ainstein";
		$statement->execute();

		$email = "BB@nyu.edu";
		$password = "password";
		$fname = "Busta";
		$lname = "Bhymes";
		$statement->execute();

		$email = "CC@nyu.edu";
		$password = "password";
		$fname = "Calamari";
		$lname = "Calamity";
		$statement->execute();

		$email = "DD@nyu.edu";
		$password = "password";
		$fname = "Donald";
		$lname = "Dump";
		$statement->execute();

		$email = "EE@nyu.edu";
		$password = "password";
		$fname = "EastAsian";
		$lname = "Espionage";
		$statement->execute();

		$email = "FF@nyu.edu";
		$password = "password";
		$fname = "Forest";
		$lname = "Florist";
		$statement->execute();

		$email = "GG@nyu.edu";
		$password = "password";
		$fname = "Guppy";
		$lname = "Gupta";
		$statement->execute();

		$statement->close();
		$connection->close();
		return true;
	}
}

function db_delete() {
	$connection = connect();
	//Query and check if these have been initialized already
	$query = "SELECT email FROM person WHERE email='AA@nyu.edu'";
	$result = $connection->query($query);
	if ($result->num_rows <= 0) {
		$connection->close();
		return false;
	}
	else {
		$statement = $connection->prepare("DELETE FROM Person WHERE email = ?");
		$statement->bind_param("s", $email);

		//test insertions
		$email = "AA@nyu.edu";
		$statement->execute();

		$email = "BB@nyu.edu";
		$statement->execute();

		$email = "CC@nyu.edu";
		$statement->execute();

		$email = "DD@nyu.edu";
		$statement->execute();

		$email = "EE@nyu.edu";
		$statement->execute();

		$email = "FF@nyu.edu";
		$statement->execute();

		$email = "GG@nyu.edu";
		$statement->execute();

		$statement->close();
		$connection->close();
		return true;
	}
}*/
?>