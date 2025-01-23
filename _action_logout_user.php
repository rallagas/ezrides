<?php
include_once "_db.php";
include_once "_functions.php";

if(isset($_SESSION['user_id'])){
     setOnlineStatus($_SESSION['user_id'], 0);   
}

session_unset();  // Remove all session variables
session_destroy();  // Destroy the session

header("Content-Type: application/json");
echo json_encode(["message" => "Logout successful"]);
http_response_code(200);
?>