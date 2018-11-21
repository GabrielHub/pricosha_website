<html lang="en">
	<head>
		<link rel="stylesheet" href="style.css">
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Page Title</title>
		<style>
			body {
			    font-family: Arial, Helvetica, sans-serif;
			}
		</style>
	</head>
	<body>

		<h1>Private Content Share</h1>
		<p>Project for Intro to Databases</p>
		<form id = "login" action = "login.php" method = "post">
			<div class = "container">
				<label for = "uname"><b>Email</b></label>
				<input type = "text" placeholder="Enter Username" name="uname" required>

				<label for="psw"><b>Password</b></label>
			    <input type="password" placeholder="Enter Password" name="psw" required>

			    <button type="submit" value="Login">Login</button>
			</div>

	</body>
</html>