<?php
//init session
session_start();

//checking if logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

//create connection
include "functions.php";
$connection = connect();

//define and init variables
$fgname = $description = "";
$fgname_err = "";

$connection->close();
?>