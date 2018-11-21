<html>
	<body>
		<?php
		include "functions.php";
		if (db_delete()) {
			echo "Accounts Deleted! Press back to return to page";
		}
		else {
			echo "No Accounts, Please Insert";
		}
		?>
	</body>
</html>