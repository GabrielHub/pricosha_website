<?php
session_start();
$_SESSION['curr_fgname'] = $_GET['fgname'];

header("location: addfriend1.php");


//middle man php page to set a session variable of the currently selected friend group
?>