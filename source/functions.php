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

/* old test code
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