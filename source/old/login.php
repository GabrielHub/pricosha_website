<html>
	<body>
		<?php
			include "functions.php";
			$connection = connect();

			$email = trim($_POST['uname']);
			$pass = trim($_POST['psw']);

			//get query
			$query = "SELECT email, password FROM person WHERE email='$email' and password='$pass'";

			//If check
			$result = $connection->query($query);

			if ($result->num_rows <= 0) {
				die("Error logging in, email or password does not match");
			}
			else {
				saveEmail($email);
				echo "<script>window.location.href = 'main.php';</script>";
			}
			$connection->close();
		?>
	</body>
</html>