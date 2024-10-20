<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connect to the database
$host = 'localhost';
$dbname = 'ezride';
$username = 'root';
$password = '';

//$db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

// Create a connection to the database
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check if the connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

define("CONN",$conn);
//start the session
session_start();

//google maps API KEY
define("GOOGLE_MAPS_API_KEY","AIzaSyAvvMQkQyQYETGeVcSN3dWLaf2a7E64NxI");

$maps_api_key = "AIzaSyDB4tE_5d8sQVRR1x2KMTFbQbCpUYWXx8A";

?>