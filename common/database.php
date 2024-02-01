<?php 
$host = "localhost";
$username = "root";
$password = "";
$database = "restaurant";

$mysqli = mysqli_connect($host, $username, $password, $database);

if($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
}