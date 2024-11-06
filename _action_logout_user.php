<?php
session_start();
session_unset();  // Remove all session variables
session_destroy();  // Destroy the session

header("Content-Type: application/json");
echo json_encode(["message" => "Logout successful"]);
http_response_code(200);
?>