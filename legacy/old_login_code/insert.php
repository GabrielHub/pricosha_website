<html>
<body>
	<?php
		include "functions.php";
		if (db_insert()) {
			echo "Accounts Inserted! Press back to return to page";
		}
		else {
			echo "Accounts already inserted. Try deleting.";
		}

	?>

</body>
</html>