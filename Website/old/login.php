<html>
	<body>
		<?php
			$servername = "localhost";
			$username = "root";
			$password = "";

			//create connection
			$connection = new mysqli($servername, $username, $password, "pricosha");

			//Check connection
			if ($connection->connect_error) {
				die("Connection failed: " . $connection->connect_error);
			}
			//echo "Connected Success";

			$email = $_POST['uname'];
			$pass = $_POST['psw'];

			//get query
			$query = "SELECT email, password FROM person WHERE email='$email' and password='$pass'";

			//If check
			$result = $connection->query($query);

			if ($result->num_rows <= 0) {
				die("Error logging in, email or password does not match");
			}
			else {
				echo "Logged In!";
				echo "<script>window.location.href = 'main.php';</script>";
			}

			$connection->close();
		?>
	</body>
</html>