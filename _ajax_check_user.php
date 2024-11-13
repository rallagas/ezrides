<?php
include_once "_db.php";
include_once "_functions.php";

$response = ["exists" => false];

if (isset($_POST['username'])) {
    $username = $_POST['username'];

    if (usernameExists($username)) {
        $response["exists"] = true;
    }
}
if (isset($_POST['email'])) {
    $email = $_POST['email'];

    if (emailExists($email)) {
        $response["exists"] = true;
    }
}

echo json_encode($response);
?>
