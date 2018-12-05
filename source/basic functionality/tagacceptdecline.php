<?php
//init session
session_start();

//checking if logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

include "functions.php";
$connection = connect();

manageTag($_SESSION['email'], $_GET['email_tagger'], $_GET['id'], $_GET['response'], $connection);

$connection->close();
?>