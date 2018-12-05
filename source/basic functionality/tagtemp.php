<?php
//init session
session_start();

//checking if logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

echo $_SESSION["fname"] . ' ' . $_SESSION["lname"];

//temporary tag session id
$_SESSION['tag_item_id'] = $_GET['id'];

header("location: tag.php");
?>