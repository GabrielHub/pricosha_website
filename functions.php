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

//getting descriptions of friendgroups
function getDescription($owner_email, $fg_name, $connection) {
	$result = "";
	$description; //Var to store temp results

	//prepare statement
	if ($statement = $connection->prepare("SELECT description FROM friendgroup WHERE owner_email = ? AND fg_name = ?")) {
		//bind param
		$statement->bind_param("ss", $param_email, $param_fg);

		//set parameter values
		$param_email = $owner_email;
		$param_fg = $fg_name;

		if ($statement->execute()) {
			$statement->store_result();
			$statement->bind_result($description);
			if ($statement->fetch()) {
				$result = $description;
			}
		}
		else {
			$result = "Couldn't fetch descriptions";
		}
	}
	$statement->close();

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
	//check if file_path or item_name is too long
	if (strlen($file_path) > 100) {
		return "Text length must be under 100 characters!";
	}
	if (strlen($item_name) > 20) {
		return "Title length must be under 20 characters!";
	}
	//prepare insert statement, for content item
	$query = "INSERT INTO contentitem (email_post, post_time, file_path, item_name, is_pub) VALUES (?, ?, ?, ?, ?)";

	if ($statement = $connection->prepare($query)) {
		//bind variables to the prepared statement as parameters
		$statement->bind_param("ssssi", $param_email, $param_time, $param_file, $param_item, $param_pub);

		//set parameters
		$param_email = $email;
		$param_time = trim($post_time);
		$param_file = trim($file_path);
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
	echo "</tr>";
	}
	echo "</table>";
}

//retrive content items for a guest user
function getGuestContentItemData($connection) {
	//variables to bind to
	$item_id;
	$email_post;
	$post_time;
	$item_name;
	$file_path;

	//result array
	$result = array();

	//query gets only public content from the last 24 hours
	$query = "SELECT item_id, email_post, post_time, item_name, file_path FROM contentitem WHERE (is_pub = ? AND (post_time >= NOW() - INTERVAL 1 DAY)) ORDER BY post_time DESC";

	//prepare select statement, and retrieve all info about content
	if ($statement = $connection->prepare($query)) {
		//bind variables to prep
		$statement->bind_param("i", $param_public);

		//set public paramter. I want this to be a prepared statement, but public will always be 1 here
		$param_public = 1;

		//attempt execution 
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
			echo "could not execute query";
		}
	}
	$statement->close();

	return $result;
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
	$query = "SELECT item_id, email_post, post_time, item_name, file_path FROM contentitem WHERE is_pub = 1 UNION SELECT item_id, email_post, post_time, item_name, file_path FROM contentitem WHERE item_id IN (SELECT item_id FROM share WHERE fg_name IN (SELECT fg_name FROM belong WHERE email = ?)) ORDER BY post_time DESC";

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

//get readable name (string) from an email
function getName($email, $connection) {
	//variables to bind to
	$fname;
	$lname;

	$result;

	//query gets first name and last in person
	$query = "SELECT fname, lname FROM person WHERE email = ?";

	//prepare select statement
	if ($statement = $connection->prepare($query)) {
		//bind variables to prepared statement
		$statement->bind_param("s", $param_email);

		//set email
		$param_email = $email;

		//attempt execution
		if ($statement->execute()) {
			//store result
			$statement->store_result();
			$statement->bind_result($fname, $lname);
			if ($statement->fetch()) {
				$result = $fname . " " . $lname;
			} 
			else {
				return "Could not FETCH results from query";
			}
		}
		else {
			return "Could not retieve name from email";
		}
	}
	$statement->close();

	return $result;
}

//get person's email from first and last names
function getEmail($fname, $lname, $connection) {
	//variables to bind to
	$email;

	$result;

	//query to get email from first and last name
	$query = "SELECT email FROM person WHERE fname = ? AND lname = ?";

	//prepare statement
	if ($statement = $connection->prepare($query)) {
		//bind variables to prepare
		$statement->bind_param("ss", $param_fname, $param_lname);

		//set parameters
		$param_fname = $fname;
		$param_lname = $lname;

		//attempt an execution
		if ($statement->execute()) {
			//store result
			$statement->store_result();
			$statement->bind_result($email);
			if ($statement->fetch()) {
				$result = $email;
			}
			else {
				return "Could not fetch email from names";
			}
		}
		else {
			return "could not query for email from name";
		}
	}
	$statement->close();

	return $result;
}

function getPendingTags($email_tagged, $connection) {
	//variables to bind to
	$item_id;
	$tagtime;
	$email_tagger;

	//Result array
	$result = array();

	//query gets all tagged posts with status = false
	$query = "SELECT email_tagger, item_id, tagtime FROM tag WHERE email_tagged = ? AND status = 'false'";

	//prepare select statement, and retrieve info about tag
	if ($statement = $connection->prepare($query)) {
		//bind variables to prepared state
		$statement->bind_param("s", $param_email);

		//set tagged Email
		$param_email = $email_tagged;

		//attempt an execution ;(
		if ($statement->execute()) {
			//store result
			$statement->store_result();

			//only add to array if there are rows
			if ($statement->num_rows > 0) {
				//bind results
				$statement->bind_result($email_tagger, $item_id, $tagtime);

				//add results to an array
				while ($statement->fetch()) {
				$result[] = array(
					"email_tagger" => $email_tagger,
					"item_id" => $item_id,
					"tagtime" => $tagtime
					);
				}	
			}
		}
		else {
			echo "Could not fetch pending tags items.";
		}
	}
	$statement->close();

	return $result;
}

function manageTag($email_tagged, $email_tagger, $item_id, $response, $connection) {
	//check if you're updating the tag or deleting it
	if ($response === "accept") {
		//update tag to change status to true
		$query = "UPDATE tag SET status = 'true' WHERE email_tagged = ? AND email_tagger = ? AND item_id = ?";

		//prepare statement to update pending tag
		if ($statement = $connection->prepare($query)) {
			//bind variables to statement
			$statement->bind_param("ssi", $param_tagged, $param_tagger, $param_id);

			//set parameters
			$param_tagged = $email_tagged;
			$param_tagger = $email_tagger;
			$param_id = $item_id;

			//attempt execution of prep statement
			if ($statement->execute()) {
				echo "accepted and updated!";
				header("location: managetags.php");
			}
			else {
				echo "Could not update";
			}
		}
	}
	else if ($response === "decline") {
		//delete pending tag when it has been declined
		$query = "DELETE FROM tag WHERE email_tagged = ? AND email_tagger = ? AND item_id = ?";

		//prepare statement to delete
		if ($statement = $connection->prepare($query)) {
			//bind variables to statement
			$statement->bind_param("ssi", $param_tagged, $param_tagger, $param_id);

			//set parameters
			$param_tagged = $email_tagged;
			$param_tagger = $email_tagger;
			$param_id = $item_id;

			//attempt execution of prep statement
			if ($statement->execute()) {
				echo "deleted and updated!";
				header("location: managetags.php");
			}
			else {
				echo "Could not update";
			}
		}
	}
	else {
		echo "Response button broke boi.";
	}
}

function singleContentInfo($item_id, $connection) {
	//variables to bind to
	$is_pub;
	$email_post;
	$post_time;
	$item_name;
	$file_path;

	$result = array(); //store in array

	$query = "SELECT email_post, post_time, file_path, item_name, is_pub FROM contentitem WHERE item_id = ?";

	//prepare statement to get info from contentitem
	if ($statement = $connection->prepare($query)) {
		//bind var to statement
		$statement->bind_param("i", $param_id);

		//set id 
		$param_id = $item_id;

		//attempt an execution :)
		if ($statement->execute()) {
			//store result
			$statement->store_result();

			//bind results
			$statement->bind_result($email_post, $post_time, $file_path, $item_name, $is_pub);

			//add results to an array
			while ($statement->fetch()) {
			$result[] = array(
				"is_pub" => $is_pub,
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

function tagContentInfo($item_id, $connection) {
	//variables to bind to
	$email_tagged;
	$email_tagger;
	$tagtime;

	$result = array(); //store in array

	//get info from tag and add it to array
	$query = "SELECT email_tagged, email_tagger, tagtime FROM tag WHERE status = 'true' AND item_id = ?";

	//prepare statement to get info from contentitem
	if ($statement = $connection->prepare($query)) {
		//bind var to statement
		$statement->bind_param("i", $param_id);

		//set id 
		$param_id = $item_id;

		//attempt an execution :)
		if ($statement->execute()) {
			//store result
			$statement->store_result();

			//bind results
			$statement->bind_result($email_tagged, $email_tagger, $tagtime);

			//add results to an array
			while ($statement->fetch()) {
			$result[] = array(
				"email_tagged" => $email_tagged,
				"email_tagger" => $email_tagger,
				"tagtime" => $tagtime,
				);
			}	
		}
		else {
			echo "Could not fetch tag items.";
		}
	}
	else {
		echo "BIG ERROR";
	}
	$statement->close();

	return $result;

}

function getAllUsers($self_email, $connection) {
	//variables to bind to
	$email;
	$fname;
	$lname;

	//result array
	$result = array();

	//query gets only public content from the last 24 hours
	$query = "SELECT email, fname, lname FROM person WHERE email NOT IN (SELECT email FROM person WHERE email = ?) ORDER BY fname ASC";

	//prepare select statement, and retrieve all person info except for self
	if ($statement = $connection->prepare($query)) {
		//bind variables to prep
		$statement->bind_param("s", $param_email);

		//set email param
		$param_email = $self_email;

		//attempt execution 
		if ($statement->execute()) {
			//store result
			$statement->store_result();

			//bind results
			$statement->bind_result($email, $fname, $lname);

			//add results to an array
			while ($statement->fetch()) {
			$result[] = array(
				"email" => $email,
				"fname" => $fname,
				"lname" => $lname
				);
			}
		}
		else {
			echo "could not execute get people query";
		}
	}
	$statement->close();

	return $result;
}
?>